<?php
namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\MetodePembayaran;
use App\Models\Testimoni;
use App\Services\MailService;
use App\Services\MidtransService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    const NOMINAL = 200000;

    // =============================================
    // CEK STATUS & PEMBAYARAN
    // =============================================
    public function cekStatus(Request $request)
    {
        $pendaftaran = null;
        $error       = null;

        if ($request->filled('kode')) {
            $kode        = strtoupper(trim($request->kode));
            $pendaftaran = Pendaftaran::with([
                'siswa','sekolah','jurusan',
                'pembayarans.metodePembayaran',
                'waliSiswas','dokumens',
            ])->where('kode_regis', $kode)->first();

            if (!$pendaftaran) $error = 'Kode pendaftaran tidak ditemukan.';
        }

        $metodePembayaran = MetodePembayaran::where('is_active', 1)->orderBy('urutan')->get();

        return view('client.cek-status', compact('pendaftaran', 'error', 'metodePembayaran'));
    }

    // =============================================
    // SUBMIT PEMBAYARAN MANUAL
    // =============================================
    public function storePembayaran(Request $request)
    {
        $request->validate([
            'kode_regis'           => 'required|exists:pendaftarans,kode_regis',
            'metode_pembayaran_id' => 'required|exists:metode_pembayarans,id',
            'proof'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'tanggal_bayar'        => 'required|date',
        ]);

        $pendaftaran = Pendaftaran::with(['siswa','sekolah'])->where('kode_regis', $request->kode_regis)->firstOrFail();

        if (!$pendaftaran->canPay()) {
            return back()->withErrors(['error' => 'Status pendaftaran tidak memenuhi syarat untuk pembayaran.']);
        }

        if ($pendaftaran->pembayarans()->whereIn('status_pembayaran', ['sukses','menunggu_verifikasi'])->exists()) {
            return back()->withErrors(['error' => 'Sudah ada pembayaran yang diproses.']);
        }

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('bukti_tf/' . $pendaftaran->kode_regis, 'public');
        }

        Pembayaran::create([
            'pendaftaran_id'       => $pendaftaran->id,
            'metode_pembayaran_id' => $request->metode_pembayaran_id,
            'nominal'              => self::NOMINAL,
            'order_id'             => 'MNL-' . strtoupper(substr(md5(uniqid($pendaftaran->kode_regis)), 0, 10)),
            'status_pembayaran'    => 'menunggu_verifikasi',
            'proof_path'           => $proofPath,
            'tanggal_pembayaran'   => $request->tanggal_bayar,
        ]);

        $pendaftaran->update(['status' => 'menunggu_pembayaran']);

        try { app(MailService::class)->sendBuktiTfDiterima($pendaftaran); }
        catch (\Exception $e) { Log::error('Email TF: ' . $e->getMessage()); }

        return redirect()->route('cek.status', ['kode' => $pendaftaran->kode_regis])
            ->with('success', 'Bukti pembayaran berhasil dikirim! Menunggu konfirmasi admin.');
    }

    // =============================================
    // REQUEST SNAP TOKEN (AJAX)
    // =============================================
    public function requestSnapToken(Request $request)
    {
        $request->validate(['kode_regis' => 'required|exists:pendaftarans,kode_regis']);

        $pendaftaran = Pendaftaran::with(['siswa','pembayarans'])->where('kode_regis', $request->kode_regis)->firstOrFail();

        if (!$pendaftaran->canPay()) {
            return response()->json(['status' => false, 'message' => 'Status pendaftaran tidak memenuhi syarat pembayaran.'], 422);
        }

        if ($pendaftaran->pembayarans()->where('status_pembayaran','sukses')->exists()) {
            return response()->json(['status' => false, 'message' => 'Pendaftaran ini sudah lunas.'], 422);
        }

        $existing = $pendaftaran->pembayarans()
            ->where('status_pembayaran','pending')->whereNotNull('snap_token')->latest()->first();

        if ($existing) {
            return response()->json(['status' => true, 'snap_token' => $existing->snap_token, 'order_id' => $existing->order_id]);
        }

        $result = app(MidtransService::class)->requestSnapToken($pendaftaran);
        return response()->json($result, $result['status'] ? 200 : 500);
    }

    // =============================================
    // MIDTRANS CALLBACK
    // =============================================
    public function midtransCallback(Request $request)
    {
        $data = $request->all();
        Log::info('Midtrans callback', $data);
        $handled = app(MidtransService::class)->handleCallback($data);
        return response()->json(['handled' => $handled]);
    }

    // =============================================
    // PAYMENT SUCCESS (JS fallback)
    // =============================================
    public function handlePaymentSuccess(Request $request)
    {
        $request->validate(['order_id' => 'required|string']);

        $pembayaran = Pembayaran::where('order_id', $request->order_id)->first();
        if (!$pembayaran) return response()->json(['status' => false, 'message' => 'Order tidak ditemukan.'], 404);

        if (!$pembayaran->isSukses()) {
            $pembayaran->update(['status_pembayaran' => 'sukses', 'tanggal_pembayaran' => now()->toDateString()]);
            $pendaftaran = $pembayaran->pendaftaran;
            $pendaftaran->update(['status' => 'lunas']);
            try { app(MailService::class)->sendPembayaranSelesai($pendaftaran->load(['siswa','sekolah','jurusan','waliSiswas','pembayarans.metodePembayaran','tahunAkademik'])); }
            catch (\Exception $e) { Log::error('Email sukses: ' . $e->getMessage()); }
        }

        return response()->json([
            'status'   => true,
            'redirect' => route('cek.status', ['kode' => $pembayaran->pendaftaran->kode_regis])
        ]);
    }

    // =============================================
    // DOWNLOAD PDF RESUME
    // =============================================
    public function downloadPdf(Request $request)
    {
        $kode        = strtoupper(trim($request->kode ?? ''));
        $pendaftaran = Pendaftaran::where('kode_regis', $kode)->first();

        if (!$pendaftaran) abort(404, 'Kode pendaftaran tidak ditemukan.');
        if (!$pendaftaran->isLunas()) abort(403, 'Formulir hanya tersedia setelah pembayaran selesai.');

        $filename = 'formulir-ppdb-' . $kode . '.pdf';

        try {
            $pdfBytes = app(PdfService::class)->generatePdf($pendaftaran);
            return response($pdfBytes)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdfBytes));
        } catch (\Exception $e) {
            // Fallback to HTML print if Dompdf not installed
            \Illuminate\Support\Facades\Log::error('PDF generation failed: ' . $e->getMessage());
            $html = app(PdfService::class)->generateResume($pendaftaran);
            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        }
    }

    // =============================================
    // SUBMIT TESTIMONI (dari halaman cek status)
    // =============================================
    public function submitTestimoni(Request $request)
    {
        $request->validate([
            'kode_regis'    => 'required|exists:pendaftarans,kode_regis',
            'isi_testimoni' => 'required|string|min:20|max:500',
            'rating'        => 'required|integer|min:1|max:5',
        ]);

        $pendaftaran = Pendaftaran::with('siswa','sekolah')->where('kode_regis', $request->kode_regis)->firstOrFail();

        if (!$pendaftaran->isLunas()) {
            return back()->withErrors(['testimoni' => 'Testimoni hanya bisa dikirim setelah pendaftaran selesai.']);
        }

        // Cek jika sudah pernah kirim
        if (Testimoni::where('pendaftaran_id', $pendaftaran->id)->exists()) {
            return back()->with('info', 'Anda sudah pernah mengirim testimoni. Menunggu persetujuan admin.');
        }

        Testimoni::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nama'           => $pendaftaran->siswa?->nama_siswa ?? 'Anonim',
            'asal_sekolah'   => $pendaftaran->sekolah?->nama_sekolah,
            'tahun_masuk'    => date('Y'),
            'isi_testimoni'  => $request->isi_testimoni,
            'rating'         => $request->rating,
            'is_active'      => false, // pending approval
            'urutan'         => 99,
        ]);

        return back()->with('success_testimoni', 'Testimoni berhasil dikirim! Menunggu persetujuan admin.');
    }
}
