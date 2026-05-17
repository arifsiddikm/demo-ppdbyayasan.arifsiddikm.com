<?php
namespace App\Services;

use App\Models\Pendaftaran;

class PdfService
{
    /**
     * Generate real PDF binary using Dompdf.
     * Make sure to install: composer require dompdf/dompdf
     *
     * Returns the raw PDF bytes (string).
     */
    public function generatePdf(Pendaftaran $p): string
    {
        $html = $this->buildHtml($p);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Return raw HTML string (fallback / browser print).
     */
    public function generateResume(Pendaftaran $p): string
    {
        return $this->buildHtml($p);
    }

    // ---------------------------------------------------------------
    // HTML builder — ALL nullable values pre-computed before string
    // ---------------------------------------------------------------
    private function buildHtml(Pendaftaran $p): string
    {
        $p->load(['siswa','sekolah','jurusan','waliSiswas','dokumens','pembayarans.metodePembayaran','tahunAkademik']);

        $siswa   = $p->siswa;
        $sekolah = $p->sekolah;
        $walis   = $p->waliSiswas;
        $bayar   = $p->pembayarans->where('status_pembayaran','sukses')->first();

        $kode         = $p->kode_regis;
        $tahunAjar    = $p->tahunAkademik ? $p->tahunAkademik->nama_tahun : '—';
        $tglCetak     = now()->format('d F Y');
        $jalurLabel   = ucfirst($p->jalur_pendaftaran);
        $statusDaftar = $p->label_status;
        $tglSubmit    = $p->tanggal_submit ? $p->tanggal_submit->format('d/m/Y H:i') : '—';

        $skNama       = $sekolah ? $sekolah->nama_sekolah : '—';
        $skTingkatan  = $sekolah ? $sekolah->tingkatan : '';
        $skAlamat     = $sekolah ? ($sekolah->alamat ?? '') . ', ' . ($sekolah->kota ?? '') : '—';
        $skPhone      = $sekolah ? ($sekolah->phone ?? '—') : '—';
        $skEmail      = $sekolah ? ($sekolah->email ?? '—') : '—';
        $skAkreditasi = $sekolah ? ($sekolah->akreditasi ?? '—') : '—';
        $skNpsn       = $sekolah ? ($sekolah->npsn ?? '—') : '—';
        $jurusanNama  = $p->jurusan ? $p->jurusan->nama_jurusan : '—';

        $siswaNama   = $siswa ? $siswa->nama_siswa : '—';
        $siswaNisn   = $siswa ? ($siswa->nisn ?? '—') : '—';
        $siswaJkLbl  = $siswa ? $siswa->jenis_kelamin_label : '—';
        $tglLahir    = $siswa && $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '—';
        $tplLahir    = $siswa ? ($siswa->tempat_lahir ?? '—') : '—';
        $siswaTtl    = $tplLahir . ', ' . $tglLahir;
        $siswaAgama  = $siswa ? $siswa->agama_label : '—';
        $siswaAlamat = $siswa ? ($siswa->alamat ?? '—') : '—';
        $siswaPhone  = $siswa ? ($siswa->phone ?? '—') : '—';
        $siswaEmail  = $siswa ? ($siswa->email ?? '—') : '—';
        $siswaAsal   = $siswa ? ($siswa->asal_sekolah ?? '—') : '—';
        $siswaThnLls = $siswa ? ($siswa->tahun_lulus ?? '—') : '—';
        $siswaIjazah = $siswa ? ($siswa->nomor_ijazah ?? '—') : '—';

        $statusBayar  = $bayar ? 'LUNAS' : 'Belum Lunas';
        $nominalBayar = $bayar ? $bayar->nominal_formatted : '—';
        $metodeBayar  = ($bayar && $bayar->metodePembayaran) ? $bayar->metodePembayaran->nama_metode : '—';
        $tglBayar     = ($bayar && $bayar->tanggal_pembayaran) ? $bayar->tanggal_pembayaran->format('d/m/Y') : '—';

        $waliRows = '';
        foreach ($walis as $w) {
            $wNama = htmlspecialchars($w->nama_wali ?? '—');
            $wSts  = ucfirst($w->jenis_wali ?? '—');
            $wPkj  = htmlspecialchars($w->pekerjaan ?? '—');
            $wHp   = htmlspecialchars($w->notelp_wali ?? '—');
            $waliRows .= "<tr>
              <td style='padding:5px 9px;border:1px solid #e5e7eb;'>{$wNama}</td>
              <td style='padding:5px 9px;border:1px solid #e5e7eb;'>{$wSts}</td>
              <td style='padding:5px 9px;border:1px solid #e5e7eb;'>{$wPkj}</td>
              <td style='padding:5px 9px;border:1px solid #e5e7eb;'>{$wHp}</td>
            </tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Formulir Pendaftaran PPDB - {$kode}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111827; background: #fff; }
.page { padding: 28px; }
.hdr { border-bottom: 3px solid #1e40af; padding-bottom: 12px; margin-bottom: 16px; }
.hdr-inner { display: flex; align-items: center; }
.logo { width: 48px; height: 48px; background: #1e40af; border-radius: 8px; text-align: center; line-height: 48px; color: #fff; font-size: 16px; font-weight: 800; float: left; margin-right: 12px; }
.hdr h1 { font-size: 15px; font-weight: 700; color: #1e40af; }
.hdr p { font-size: 10px; color: #6b7280; margin-top: 2px; }
.kode-box { background: #eff6ff; border: 2px solid #1e40af; border-radius: 6px; padding: 9px 16px; text-align: center; margin-bottom: 14px; }
.kode-lbl { font-size: 9px; color: #6b7280; }
.kode-val { font-size: 20px; font-weight: 800; color: #1e40af; letter-spacing: 3px; }
.kode-sub { font-size: 9px; color: #6b7280; margin-top: 2px; }
.sec { margin-bottom: 13px; }
.sec-title { font-size: 11px; font-weight: 700; color: #1e40af; background: #eff6ff; padding: 4px 10px; border-left: 4px solid #1e40af; margin-bottom: 7px; }
table { width: 100%; border-collapse: collapse; font-size: 10px; }
th { background: #f1f5f9; padding: 5px 9px; text-align: left; border: 1px solid #e5e7eb; font-weight: 700; color: #374151; }
.rl { width: 32%; font-weight: 600; color: #374151; padding: 5px 9px; border: 1px solid #e5e7eb; background: #f9fafb; vertical-align: top; }
.rv { padding: 5px 9px; border: 1px solid #e5e7eb; vertical-align: top; }
.sk-card { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 9px 12px; margin-bottom: 7px; }
.sk-name { font-size: 13px; font-weight: 700; color: #1e40af; }
.sk-info { font-size: 10px; color: #374151; margin-top: 3px; }
.badge-lunas { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 10px; font-weight: 700; font-size: 10px; }
.next { background: #fefce8; border: 1px solid #fde047; border-radius: 6px; padding: 9px 12px; margin-top: 12px; }
.next h4 { font-size: 11px; font-weight: 700; color: #854d0e; margin-bottom: 5px; }
.next ol { padding-left: 14px; font-size: 10px; color: #92400e; line-height: 1.9; }
.footer { margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
.footer-note { font-size: 9px; color: #9ca3af; float: left; width: 70%; }
.ttd { text-align: center; float: right; width: 28%; }
.ttd-line { border-bottom: 1px solid #111; height: 44px; margin-bottom: 4px; }
.ttd-label { font-size: 10px; }
.clearfix::after { content: ''; display: table; clear: both; }
</style>
</head>
<body>
<div class="page">

  <!-- Header -->
  <div class="hdr">
    <div class="hdr-inner">
      <div class="logo">YI</div>
      <div>
        <h1>FORMULIR PENDAFTARAN PPDB</h1>
        <p>Yayasan Indonesia &mdash; Tahun Ajaran {$tahunAjar}</p>
        <p>Dicetak: {$tglCetak}</p>
      </div>
    </div>
  </div>

  <!-- Kode Box -->
  <div class="kode-box">
    <div class="kode-lbl">KODE PENDAFTARAN &mdash; TUNJUKKAN KE PIHAK SEKOLAH</div>
    <div class="kode-val">{$kode}</div>
    <div class="kode-sub">Simpan kode ini untuk keperluan administrasi sekolah</div>
  </div>

  <!-- Sekolah -->
  <div class="sec">
    <div class="sec-title">Informasi Sekolah Tujuan</div>
    <div class="sk-card">
      <div class="sk-name">{$skNama} ({$skTingkatan})</div>
      <div class="sk-info"><strong>Alamat:</strong> {$skAlamat}</div>
      <div class="sk-info"><strong>Telepon:</strong> {$skPhone} &nbsp;&nbsp; <strong>Email:</strong> {$skEmail}</div>
      <div class="sk-info"><strong>Akreditasi:</strong> {$skAkreditasi} &nbsp;&nbsp; <strong>NPSN:</strong> {$skNpsn}</div>
    </div>
    <table>
      <tr><td class="rl">Jurusan</td><td class="rv">{$jurusanNama}</td></tr>
      <tr><td class="rl">Jalur Pendaftaran</td><td class="rv">{$jalurLabel}</td></tr>
      <tr><td class="rl">Status Pendaftaran</td><td class="rv">{$statusDaftar}</td></tr>
      <tr><td class="rl">Tgl Submit</td><td class="rv">{$tglSubmit}</td></tr>
    </table>
  </div>

  <!-- Siswa -->
  <div class="sec">
    <div class="sec-title">Data Diri Siswa</div>
    <table>
      <tr><td class="rl">Nama Lengkap</td><td class="rv"><strong>{$siswaNama}</strong></td></tr>
      <tr><td class="rl">NISN</td><td class="rv">{$siswaNisn}</td></tr>
      <tr><td class="rl">Jenis Kelamin</td><td class="rv">{$siswaJkLbl}</td></tr>
      <tr><td class="rl">Tempat, Tgl Lahir</td><td class="rv">{$siswaTtl}</td></tr>
      <tr><td class="rl">Agama</td><td class="rv">{$siswaAgama}</td></tr>
      <tr><td class="rl">Alamat</td><td class="rv">{$siswaAlamat}</td></tr>
      <tr><td class="rl">No. HP</td><td class="rv">{$siswaPhone}</td></tr>
      <tr><td class="rl">Email</td><td class="rv">{$siswaEmail}</td></tr>
      <tr><td class="rl">Asal Sekolah</td><td class="rv">{$siswaAsal}</td></tr>
      <tr><td class="rl">Tahun Lulus</td><td class="rv">{$siswaThnLls}</td></tr>
      <tr><td class="rl">No. Ijazah</td><td class="rv">{$siswaIjazah}</td></tr>
    </table>
  </div>

  <!-- Wali -->
  <div class="sec">
    <div class="sec-title">Data Orang Tua / Wali</div>
    <table>
      <tr><th>Nama</th><th>Status</th><th>Pekerjaan</th><th>No. HP</th></tr>
      {$waliRows}
    </table>
  </div>

  <!-- Pembayaran -->
  <div class="sec">
    <div class="sec-title">Informasi Pembayaran</div>
    <table>
      <tr><td class="rl">Status</td><td class="rv"><span class="badge-lunas">{$statusBayar}</span></td></tr>
      <tr><td class="rl">Nominal</td><td class="rv">{$nominalBayar}</td></tr>
      <tr><td class="rl">Metode Pembayaran</td><td class="rv">{$metodeBayar}</td></tr>
      <tr><td class="rl">Tanggal Bayar</td><td class="rv">{$tglBayar}</td></tr>
    </table>
  </div>

  <!-- Langkah selanjutnya -->
  <div class="next">
    <h4>Langkah Selanjutnya &mdash; Serahkan Dokumen ke Sekolah</h4>
    <ol>
      <li>Cetak formulir ini dan bawa ke <strong>{$skNama}</strong></li>
      <li>Alamat: <strong>{$skAlamat}</strong></li>
      <li>Siapkan dokumen asli: Ijazah/STL, KK, Akta Kelahiran, Pas Foto 3x4 (4 lembar)</li>
      <li>Tunjukkan kode pendaftaran <strong>{$kode}</strong> kepada petugas penerimaan</li>
      <li>Info lebih lanjut: Telp <strong>{$skPhone}</strong> / Email <strong>{$skEmail}</strong></li>
    </ol>
  </div>

  <!-- Footer -->
  <div class="footer clearfix">
    <div class="footer-note">
      Dokumen dicetak otomatis oleh sistem PPDB Yayasan Indonesia.<br>
      Dokumen ini sah jika kode pendaftaran valid di sistem.
    </div>
    <div class="ttd">
      <div class="ttd-line"></div>
      <div class="ttd-label">Tanda Tangan Orang Tua/Wali</div>
    </div>
  </div>

</div>
</body>
</html>
HTML;
    }
}
