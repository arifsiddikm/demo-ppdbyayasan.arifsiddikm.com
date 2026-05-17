<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Pendaftaran;

class MailService
{
    protected function mailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();

        // FIX: Pakai env() langsung, bukan config('mail.mailers.smtp.*')
        // key struktur Laravel 13 berbeda, config tersebut tidak return value yang bener
        $mail->Host       = env('MAIL_HOST', 'smtp.hostinger.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USERNAME', 'noreply@arifsiddikm.com');
        $mail->Password   = env('MAIL_PASSWORD', '');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = (int) env('MAIL_PORT', 465);
        $mail->CharSet    = 'UTF-8';

        // FIX: Timeout 15 detik biar tidak hang
        $mail->Timeout       = 15;
        $mail->SMTPKeepAlive = false;

        // FIX: Disable SSL peer verification - shared hosting sering gagal verify cert
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom(
            env('MAIL_FROM_ADDRESS', 'noreply@arifsiddikm.com'),
            env('MAIL_FROM_NAME', 'PPDB Yayasan Indonesia')
        );

        return $mail;
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, ?string $pdfBytes = null, ?string $pdfFilename = null): bool
    {
        try {
            $mail = $this->mailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            if ($pdfBytes && $pdfFilename) {
                $mail->addStringAttachment($pdfBytes, $pdfFilename, PHPMailer::ENCODING_BASE64, 'application/pdf');
            }

            $mail->send();
            Log::info("Email sent to {$toEmail}: {$subject}");
            return true;

        } catch (Exception $e) {
            Log::error("Email PHPMailer error to {$toEmail}: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error("Email unexpected error to {$toEmail}: " . $e->getMessage());
            return false;
        }
    }

    public function sendPendaftaranBerhasil(Pendaftaran $p): void
    {
        $siswa = $p->siswa;
        $email = $siswa?->email;
        $nama  = $siswa?->nama_siswa ?? 'Calon Siswa';
        $kode  = $p->kode_regis;
        if (!$email) return;

        $urlCekStatus = url('/cek-status?kode=' . $kode);

        $html = $this->template('Pendaftaran Berhasil Dikirim', "
            <p>Halo <strong>{$nama}</strong>,</p>
            <p>Pendaftaran Anda ke <strong>{$p->sekolah?->nama_sekolah}</strong> telah berhasil dikirim dan sedang dalam proses verifikasi admin.</p>
            {$this->kodeBox($kode)}
            <p>Simpan kode ini untuk memantau status pendaftaran dan pembayaran Anda.</p>
            <a href='{$urlCekStatus}' style='{$this->btnStyle('#1e40af')}'>Cek Status Pendaftaran</a>
        ");
        $this->send($email, $nama, "Pendaftaran Berhasil - Kode: {$kode}", $html);

        $adminEmail = env('ADMIN_EMAIL');
        if ($adminEmail) {
            $urlAdmin    = url('/admin/pendaftaran/' . $p->id);
            $jurusanNama = $p->jurusan?->nama_jurusan ?? '-';
            $jalur       = ucfirst($p->jalur_pendaftaran);
            $sekolahNama = $p->sekolah?->nama_sekolah;

            $adminHtml = $this->template('Pendaftaran Baru Masuk', "
                <p>Ada pendaftaran baru yang perlu diverifikasi:</p>
                <ul style='background:#f9fafb;padding:16px 16px 16px 32px;border-radius:8px;line-height:2;'>
                  <li><strong>Kode:</strong> {$kode}</li>
                  <li><strong>Nama:</strong> {$nama}</li>
                  <li><strong>Sekolah:</strong> {$sekolahNama}</li>
                  <li><strong>Jurusan:</strong> {$jurusanNama}</li>
                  <li><strong>Jalur:</strong> {$jalur}</li>
                </ul>
                <a href='{$urlAdmin}' style='{$this->btnStyle('#1e40af')}'>Lihat di Admin Panel</a>
            ");
            $this->send($adminEmail, 'Admin PPDB', "Pendaftaran Baru: {$nama} ({$kode})", $adminHtml);
        }
    }

    public function sendBerkasDiterima(Pendaftaran $p): void
    {
        $email = $p->siswa?->email;
        $nama  = $p->siswa?->nama_siswa ?? 'Calon Siswa';
        $kode  = $p->kode_regis;
        if (!$email) return;

        $urlCekStatus = url('/cek-status?kode=' . $kode);
        $html = $this->template('Berkas Diterima — Segera Lakukan Pembayaran', "
            <p>Halo <strong>{$nama}</strong>,</p>
            <p>Kabar baik! Berkas pendaftaran Anda ke <strong>{$p->sekolah?->nama_sekolah}</strong> telah <strong>diterima dan diverifikasi</strong>.</p>
            <p>Silakan lakukan <strong>pembayaran uang pendaftaran</strong> untuk menyelesaikan proses PPDB Anda.</p>
            {$this->kodeBox($kode)}
            <a href='{$urlCekStatus}' style='{$this->btnStyle('#1e40af')}'>Cek Status &amp; Bayar Sekarang</a>
        ");
        $this->send($email, $nama, "Berkas Diterima — Segera Bayar ({$kode})", $html);
    }

    public function sendBuktiTfDiterima(Pendaftaran $p): void
    {
        $email = $p->siswa?->email;
        $nama  = $p->siswa?->nama_siswa ?? 'Calon Siswa';
        $kode  = $p->kode_regis;
        if (!$email) return;

        $urlCekStatus = url('/cek-status?kode=' . $kode);
        $this->send($email, $nama, "Bukti Transfer Diterima ({$kode})",
            $this->template('Bukti Transfer Diterima', "
                <p>Halo <strong>{$nama}</strong>,</p>
                <p>Bukti transfer Anda telah kami terima dan sedang dalam proses verifikasi oleh admin.</p>
                {$this->kodeBox($kode)}
                <a href='{$urlCekStatus}' style='{$this->btnStyle('#1e40af')}'>Pantau Status Pembayaran</a>
            ")
        );

        $adminEmail = env('ADMIN_EMAIL');
        if ($adminEmail) {
            $urlAdmin = url('/admin/pendaftaran/' . $p->id);
            $this->send($adminEmail, 'Admin PPDB', "Bukti Transfer Baru: {$nama} ({$kode})",
                $this->template('Bukti Transfer Perlu Diverifikasi', "
                    <p>Siswa <strong>{$nama}</strong> (kode: <strong>{$kode}</strong>) telah mengupload bukti transfer.</p>
                    <p>Silakan verifikasi pembayaran di admin panel.</p>
                    <a href='{$urlAdmin}' style='{$this->btnStyle('#1e40af')}'>Verifikasi Sekarang</a>
                ")
            );
        }
    }

    public function sendPembayaranSelesai(Pendaftaran $p): void
    {
        $email = $p->siswa?->email;
        $nama  = $p->siswa?->nama_siswa ?? 'Calon Siswa';
        $kode  = $p->kode_regis;
        if (!$email) return;

        $urlCekStatus = url('/cek-status?kode=' . $kode);
        $pdfBytes = null;
        $pdfName  = "formulir-pendaftaran-{$kode}.pdf";
        try {
            $pdfBytes = app(PdfService::class)->generatePdf($p);
        } catch (\Exception $ex) {
            Log::error('Dompdf error saat kirim email: ' . $ex->getMessage());
        }

        $html = $this->template('Pendaftaran &amp; Pembayaran Selesai', "
            <p>Halo <strong>{$nama}</strong>,</p>
            <p>Selamat! Pendaftaran dan pembayaran Anda ke <strong>{$p->sekolah?->nama_sekolah}</strong> telah <strong>selesai dan terkonfirmasi</strong>.</p>
            {$this->kodeBox($kode)}
            <p>Formulir pendaftaran terlampir di email ini. Cetak dan serahkan ke pihak sekolah pada saat pendaftaran ulang.</p>
            <a href='{$urlCekStatus}' style='{$this->btnStyle('#059669')}'>Unduh Formulir PDF</a>
        ");
        $this->send($email, $nama, "PPDB Selesai! Formulir Pendaftaran ({$kode})", $html, $pdfBytes, $pdfName);

        $adminEmail = env('ADMIN_EMAIL');
        if ($adminEmail) {
            $this->send($adminEmail, 'Admin PPDB', "Pembayaran Selesai: {$nama} ({$kode})",
                $this->template('Pembayaran Selesai', "
                    <p>Pembayaran siswa <strong>{$nama}</strong> dengan kode <strong>{$kode}</strong> telah selesai dan terkonfirmasi.</p>
                ")
            );
        }
    }

    // ======================================================
    // HELPERS
    // ======================================================

    private function kodeBox(string $kode): string
    {
        return "<div style='background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:16px;margin:16px 0;text-align:center;'>
          <p style='margin:0;font-size:12px;color:#6b7280;'>Kode Pendaftaran Anda:</p>
          <p style='margin:6px 0 0;font-size:22px;font-weight:800;color:#1e40af;letter-spacing:3px;'>{$kode}</p>
        </div>";
    }

    private function btnStyle(string $bg): string
    {
        return "display:inline-block;background:{$bg};color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;margin-top:12px;font-size:14px;";
    }

    private function template(string $title, string $content): string
    {
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
<title>{$title}</title></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
  <div style="max-width:600px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
    <div style="background:linear-gradient(135deg,#1e40af,#3b82f6);padding:28px 36px;text-align:center;">
      <div style="font-size:26px;font-weight:800;color:#fff;">PPDB Yayasan Indonesia</div>
      <div style="color:#bfdbfe;font-size:13px;margin-top:4px;">Penerimaan Peserta Didik Baru</div>
    </div>
    <div style="padding:28px 36px;color:#374151;line-height:1.7;font-size:14px;">
      <h2 style="margin:0 0 18px;font-size:18px;font-weight:700;color:#1e3a8a;">{$title}</h2>
      {$content}
    </div>
    <div style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 36px;text-align:center;color:#9ca3af;font-size:12px;">
      &copy; {$year} PPDB Yayasan Indonesia
    </div>
  </div>
</body></html>
HTML;
    }
}
