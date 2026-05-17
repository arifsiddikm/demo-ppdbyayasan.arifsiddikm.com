<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use App\Models\Sekolah;
use App\Models\MetodePembayaran;
use App\Services\MailService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['siswa','sekolah','jurusan','pembayarans'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('kode_regis','like',"%{$s}%")
                  ->orWhereHas('siswa', fn($q2) => $q2->where('nama_siswa','like',"%{$s}%")->orWhere('email','like',"%{$s}%"));
            });
        }
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('sekolah_id')) $query->where('sekolah_id', $request->sekolah_id);
        if ($request->filled('jalur'))      $query->where('jalur_pendaftaran', $request->jalur);

        $pendaftarans = $query->paginate(20)->withQueryString();
        $sekolahs     = Sekolah::where('is_active',1)->orderBy('urutan')->get();

        return view('admin.pendaftaran.index', compact('pendaftarans','sekolahs'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load(['siswa','sekolah','jurusan','waliSiswas','dokumens','pembayarans.metodePembayaran','userVerifikator','tahunAkademik']);
        $metodes = MetodePembayaran::where('is_active',1)->orderBy('urutan')->get();
        return view('admin.pendaftaran.show', compact('pendaftaran','metodes'));
    }

    public function konfirmasiDiterima(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->status !== 'diproses') {
            return back()->withErrors(['error' => 'Status tidak valid.']);
        }
        $pendaftaran->update([
            'status'             => 'diterima',
            'diverifikasi_oleh'  => Auth::id(),
            'tanggal_verifikasi' => now(),
            'catatan_admin'      => $request->catatan ?? null,
        ]);
        try { app(MailService::class)->sendBerkasDiterima($pendaftaran->load(['siswa','sekolah'])); }
        catch (\Exception $e) {}
        return back()->with('success', 'Berkas dikonfirmasi diterima. Email dikirim ke siswa.');
    }

    public function konfirmasiDitolak(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate(['catatan' => 'required|string|max:500']);
        $pendaftaran->update([
            'status'             => 'ditolak',
            'diverifikasi_oleh'  => Auth::id(),
            'tanggal_verifikasi' => now(),
            'catatan_admin'      => $request->catatan,
        ]);
        return back()->with('success', 'Pendaftaran ditolak.');
    }

    public function konfirmasiPembayaran(Request $request, Pembayaran $pembayaran)
    {
        $pembayaran->update([
            'status_pembayaran' => 'sukses',
            'verifikasi_oleh'   => Auth::id(),
            'verifikasi_tanggal'=> now(),
            'catatan_verifikasi'=> $request->catatan ?? null,
            'tanggal_pembayaran'=> $pembayaran->tanggal_pembayaran ?? now()->toDateString(),
        ]);

        $pendaftaran = $pembayaran->pendaftaran;
        if ($request->boolean('selesaikan_pendaftaran', true)) {
            $pendaftaran->update(['status' => 'lunas']);
            try {
                app(MailService::class)->sendPembayaranSelesai(
                    $pendaftaran->load(['siswa','sekolah','jurusan','waliSiswas','pembayarans.metodePembayaran','tahunAkademik'])
                );
            } catch (\Exception $e) {}
        }
        return back()->with('success', 'Pembayaran dikonfirmasi!' . ($request->boolean('selesaikan_pendaftaran',true) ? ' Email + formulir PDF dikirim ke siswa.' : ''));
    }

    public function uploadBuktiTf(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate(['proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240']);
        $pembayaran = $pendaftaran->pembayarans()->latest()->first();
        if (!$pembayaran) return back()->withErrors(['error' => 'Tidak ada data pembayaran.']);
        $path = $request->file('proof')->store('bukti_tf/'.$pendaftaran->kode_regis, 'public');
        $pembayaran->update(['proof_path' => $path]);
        return back()->with('success', 'Bukti transfer diupload.');
    }

    public function downloadPdf(Pendaftaran $pendaftaran)
    {
        $filename = 'formulir-ppdb-' . $pendaftaran->kode_regis . '.pdf';
        try {
            $pdf = app(PdfService::class)->generatePdf($pendaftaran);
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
        } catch (\Exception $e) {
            $html = app(PdfService::class)->generateResume($pendaftaran);
            return response($html)->header('Content-Type','text/html; charset=UTF-8');
        }
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        foreach ($pendaftaran->dokumens as $d) Storage::disk('public')->delete($d->file_path);
        foreach ($pendaftaran->pembayarans as $p) { if ($p->proof_path) Storage::disk('public')->delete($p->proof_path); }
        $pendaftaran->delete();
        return redirect()->route('admin.pendaftaran.index')->with('success','Data pendaftaran dihapus.');
    }

    // =============================================
    // EXPORT EXCEL
    // =============================================
    public function exportExcel(Request $request)
    {
        $query = Pendaftaran::with(['siswa','sekolah','jurusan','pembayarans'])->latest();
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('sekolah_id')) $query->where('sekolah_id', $request->sekolah_id);
        if ($request->filled('jalur'))      $query->where('jalur_pendaftaran', $request->jalur);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('kode_regis','like',"%{$s}%")
                  ->orWhereHas('siswa',fn($q2)=>$q2->where('nama_siswa','like',"%{$s}%"));
            });
        }
        $data = $query->get();

        $rows   = [];
        $rows[] = ['No','Kode Registrasi','Nama Siswa','NISN','Email','HP','Sekolah','Jurusan','Jalur','Status Daftar','Status Bayar','Nominal','Metode Bayar','Tgl Daftar'];

        foreach ($data as $i => $p) {
            $bayar = $p->pembayarans->sortByDesc('id')->first();
            $rows[] = [
                $i + 1,
                $p->kode_regis,
                $p->siswa?->nama_siswa ?? '',
                $p->siswa?->nisn ?? '',
                $p->siswa?->email ?? '',
                $p->siswa?->phone ?? '',
                $p->sekolah?->nama_sekolah ?? '',
                $p->jurusan?->nama_jurusan ?? '',
                ucfirst($p->jalur_pendaftaran),
                $p->label_status,
                $bayar ? $bayar->label_status : '',
                $bayar ? 'Rp '.number_format($bayar->nominal,0,',','.') : '',
                $bayar?->metodePembayaran?->nama_metode ?? '',
                $p->tanggal_submit?->format('d/m/Y') ?? '',
            ];
        }

        // Build CSV (widely compatible)
        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(';', array_map(fn($v) => '"'.str_replace('"','""',$v).'"', $row)) . "\r\n";
        }

        $filename = 'data-pendaftaran-' . now()->format('Ymd-His') . '.csv';
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Content-Encoding', 'UTF-8');
    }

    // =============================================
    // EXPORT PDF SUMMARY (print-friendly HTML)
    // =============================================
    public function exportPdf(Request $request)
    {
        $query = Pendaftaran::with(['siswa','sekolah','jurusan','pembayarans'])->latest();
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('sekolah_id')) $query->where('sekolah_id', $request->sekolah_id);
        if ($request->filled('jalur'))      $query->where('jalur_pendaftaran', $request->jalur);
        $data = $query->get();

        $total  = $data->count();
        $lunas  = $data->where('status','lunas')->count();
        $revenue= \App\Models\Pembayaran::where('status_pembayaran','sukses')->sum('nominal');
        $revenueFormatted = number_format($revenue, 0, ',', '.');
        $tgl    = now()->format('d F Y H:i');

        $rows = '';
        foreach ($data as $i => $p) {
            $bayar = $p->pembayarans->where('status_pembayaran','sukses')->first();
            $rows .= "<tr>
              <td style='padding:6px 10px;border:1px solid #e5e7eb;'>".($i+1)."</td>
              <td style='padding:6px 10px;border:1px solid #e5e7eb;font-family:monospace;font-weight:600;'>{$p->kode_regis}</td>
              <td style='padding:6px 10px;border:1px solid #e5e7eb;'>" . htmlspecialchars($p->siswa?->nama_siswa ?? '—') . "</td>
              <td style='padding:6px 10px;border:1px solid #e5e7eb;'>" . htmlspecialchars($p->sekolah?->singkatan ?? '—') . "</td>
              <td style='padding:6px 10px;border:1px solid #e5e7eb;'>" . ucfirst($p->jalur_pendaftaran) . "</td>
              <td style='padding:6px 10px;border:1px solid #e5e7eb;'><span style='background:" . ($p->status==='lunas'?'#d1fae5':($p->status==='diproses'?'#fef3c7':'#dbeafe')) . ";color:" . ($p->status==='lunas'?'#065f46':($p->status==='diproses'?'#92400e':'#1e40af')) . ";padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;'>{$p->label_status}</span></td>
              <td style='padding:6px 10px;border:1px solid #e5e7eb;'>" . ($bayar ? '<span style="color:#059669;font-weight:700;">Rp '.number_format($bayar->nominal,0,',','.').'</span>' : '—') . "</td>
              <td style='padding:6px 10px;border:1px solid #e5e7eb;font-size:11px;'>" . ($p->tanggal_submit?->format('d/m/Y') ?? '—') . "</td>
            </tr>";
        }

        $filterInfo = '';
        if ($request->filled('status'))     $filterInfo .= ' | Status: '.ucfirst($request->status);
        if ($request->filled('sekolah_id')) $filterInfo .= ' | Sekolah: '.(\App\Models\Sekolah::find($request->sekolah_id)?->nama_sekolah ?? '');
        if ($request->filled('jalur'))      $filterInfo .= ' | Jalur: '.ucfirst($request->jalur);

        $html = <<<HTML
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
<title>Laporan Data Pendaftaran PPDB</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}body{font-family:Arial,sans-serif;font-size:11px;color:#111}
.c{max-width:100%;padding:24px}.hdr{border-bottom:3px solid #1e40af;padding-bottom:12px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center}
.hdr h1{font-size:16px;font-weight:700;color:#1e40af}.hdr p{font-size:11px;color:#6b7280}
.stats{display:flex;gap:16px;margin-bottom:16px}.stat{background:#eff6ff;border-radius:8px;padding:10px 16px;flex:1;text-align:center}
.stat .n{font-size:20px;font-weight:800;color:#1e40af}.stat .l{font-size:11px;color:#6b7280}
table{width:100%;border-collapse:collapse;font-size:11px}
th{background:#1e40af;color:#fff;padding:7px 10px;text-align:left;font-weight:600}
tr:nth-child(even) td{background:#f9fafb}
.ftr{margin-top:14px;font-size:10px;color:#9ca3af;text-align:center}
@media print{.c{padding:12px}}
</style>
</head>
<body><div class="c">
<div class="hdr">
  <div><h1>Laporan Data Pendaftaran PPDB</h1><p>Yayasan Indonesia &mdash; Dicetak: {$tgl}{$filterInfo}</p></div>
  <div style="font-size:10px;color:#6b7280">Total: {$total} pendaftar</div>
</div>
<div class="stats">
  <div class="stat"><div class="n">{$total}</div><div class="l">Total Pendaftar</div></div>
  <div class="stat"><div class="n">{$lunas}</div><div class="l">Lunas</div></div>
  <div class="stat"><div class="n">Rp {$revenueFormatted}</div><div class="l">Total Revenue</div></div>
</div>
<table>
  <thead><tr><th>#</th><th>Kode</th><th>Nama Siswa</th><th>Sekolah</th><th>Jalur</th><th>Status</th><th>Pembayaran</th><th>Tgl Daftar</th></tr></thead>
  <tbody>{$rows}</tbody>
</table>
<div class="ftr">Laporan ini dicetak otomatis oleh sistem PPDB Yayasan Indonesia</div>
</div>
<script>window.onload=function(){window.print();}</script>
</body></html>
HTML;

        return response($html)->header('Content-Type','text/html; charset=UTF-8');
    }
}
