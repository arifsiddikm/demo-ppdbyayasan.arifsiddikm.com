<?php
/**
 * DEBUG EMAIL - PPDB Yayasan Indonesia
 * Akses via: https://demo-ppdbyayasan.arifsiddikm.biz.id/debug-mail.php
 *
 * !! HAPUS FILE INI SETELAH SELESAI DEBUG !!
 */

// Keamanan minimal - ganti password ini sebelum upload
define('DEBUG_PASS', 'debugppdb2025');
if (!isset($_GET['key']) || $_GET['key'] !== DEBUG_PASS) {
    http_response_code(403);
    die('<h2 style="font-family:sans-serif;color:red;">403 - Akses ditolak. Tambahkan ?key=debugppdb2025 di URL</h2>');
}

// Load .env manual
$envPath = dirname(__DIR__) . '/.env';
$env = [];
if (file_exists($envPath)) {
    foreach (file($envPath) as $line) {
        $line = trim($line);
        if (!$line || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
}

$host     = $env['MAIL_HOST']         ?? 'smtp.hostinger.com';
$port     = (int)($env['MAIL_PORT']   ?? 465);
$user     = $env['MAIL_USERNAME']     ?? '';
$pass     = $env['MAIL_PASSWORD']     ?? '';
$from     = $env['MAIL_FROM_ADDRESS'] ?? $user;
$fromName = $env['MAIL_FROM_NAME']    ?? 'PPDB Yayasan Indonesia';
$toEmail  = $_POST['to_email']        ?? $env['ADMIN_EMAIL'] ?? '';
$action   = $_POST['action']          ?? '';

$result = '';
$log    = [];

// =============================================
// KIRIM TEST EMAIL
// =============================================
if ($action === 'send' && $toEmail) {
    // Cek apakah PHPMailer ada
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (!file_exists($autoload)) {
        $result = '<div class="err">❌ vendor/autoload.php tidak ditemukan. Jalankan <code>composer install</code> dulu.</div>';
    } else {
        require_once $autoload;

        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $user;
            $mail->Password   = $pass;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $port;
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 15;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];
            $mail->SMTPDebug  = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;

            // Tangkap output debug
            ob_start();
            $mail->Debugoutput = 'echo';

            $mail->setFrom($from, $fromName);
            $mail->addAddress($toEmail);
            $mail->isHTML(true);
            $mail->Subject = '[DEBUG] Test Email PPDB - ' . date('d/m/Y H:i:s');
            $mail->Body    = '<h2>Test Email Berhasil!</h2><p>Email ini dikirim dari debug tool PPDB.</p><p>Waktu: ' . date('d/m/Y H:i:s') . '</p>';
            $mail->AltBody = 'Test Email Berhasil! Waktu: ' . date('d/m/Y H:i:s');

            $mail->send();
            $debugOutput = ob_get_clean();
            $result = '<div class="ok">✅ <strong>Email berhasil dikirim ke ' . htmlspecialchars($toEmail) . '!</strong> Cek inbox/spam.</div>';
            $log[]  = $debugOutput;

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $debugOutput = ob_get_clean();
            $result = '<div class="err">❌ <strong>PHPMailer Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
            $log[]  = $debugOutput;
        } catch (\Exception $e) {
            $debugOutput = ob_get_clean();
            $result = '<div class="err">❌ <strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
            $log[]  = $debugOutput;
        }
    }
}

// =============================================
// CEK SOCKET KONEKSI SMTP
// =============================================
if ($action === 'ping') {
    $log[] = "Mencoba koneksi ke {$host}:{$port} ...";
    $conn  = @fsockopen("ssl://{$host}", $port, $errno, $errstr, 10);
    if ($conn) {
        $banner = fgets($conn, 512);
        fclose($conn);
        $result = '<div class="ok">✅ <strong>Koneksi SMTP berhasil!</strong> Banner: ' . htmlspecialchars($banner) . '</div>';
        $log[]  = "Koneksi OK. Banner: {$banner}";
    } else {
        $result = '<div class="err">❌ <strong>Koneksi SMTP gagal!</strong> Error #{$errno}: ' . htmlspecialchars($errstr) . '</div>';
        $log[]  = "Gagal: #{$errno} {$errstr}";
    }
}

?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Debug Email - PPDB</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f1f5f9;padding:20px;font-size:14px}
.wrap{max-width:760px;margin:0 auto}
h1{font-size:22px;font-weight:800;color:#1e40af;margin-bottom:4px}
.sub{color:#6b7280;font-size:13px;margin-bottom:20px}
.card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;margin-bottom:16px}
.card h2{font-size:15px;font-weight:700;color:#374151;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #f1f5f9}
table{width:100%;border-collapse:collapse;font-size:13px}
td{padding:7px 10px;border-bottom:1px solid #f1f5f9}
td:first-child{font-weight:600;color:#374151;width:35%;background:#f9fafb}
td:last-child{font-family:monospace;color:#1e40af;word-break:break-all}
.badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700}
.badge-ok{background:#d1fae5;color:#065f46}
.badge-warn{background:#fef3c7;color:#92400e}
.badge-err{background:#fee2e2;color:#991b1b}
label{font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:4px}
input[type=email]{width:100%;padding:8px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none}
input[type=email]:focus{border-color:#3b82f6}
.btn{display:inline-block;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;border:none;margin-right:8px;margin-top:8px}
.btn-blue{background:#1e40af;color:#fff}
.btn-gray{background:#e5e7eb;color:#374151}
.ok{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;border-radius:8px;padding:12px;margin-bottom:12px;font-size:13px}
.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:8px;padding:12px;margin-bottom:12px;font-size:13px}
pre{background:#0f172a;color:#e2e8f0;padding:14px;border-radius:8px;font-size:11px;overflow-x:auto;white-space:pre-wrap;margin-top:10px;max-height:300px;overflow-y:auto}
.warn{background:#fef3c7;border:1px solid #fde047;border-radius:8px;padding:10px 14px;font-size:13px;color:#92400e;margin-bottom:12px}
</style>
</head>
<body>
<div class="wrap">
  <h1>🔧 Debug Email — PPDB Yayasan</h1>
  <p class="sub">Tool debug koneksi SMTP PHPMailer. <strong>Hapus file ini setelah selesai!</strong></p>

  <div class="warn">⚠️ File ini mengandung info sensitif. Akses hanya dengan key: <code>?key=debugppdb2025</code></div>

  <?php if ($result) echo $result; ?>

  <!-- INFO ENV -->
  <div class="card">
    <h2>📋 Konfigurasi .env (MAIL)</h2>
    <table>
      <tr><td>MAIL_HOST</td><td><?= htmlspecialchars($host) ?></td></tr>
      <tr><td>MAIL_PORT</td><td><?= $port ?></td></tr>
      <tr><td>MAIL_USERNAME</td><td><?= htmlspecialchars($user) ?></td></tr>
      <tr><td>MAIL_PASSWORD</td><td><?= $pass ? str_repeat('*', strlen($pass)) . ' (' . strlen($pass) . ' chars)' : '<span class="badge badge-err">KOSONG!</span>' ?></td></tr>
      <tr><td>MAIL_FROM_ADDRESS</td><td><?= htmlspecialchars($from) ?></td></tr>
      <tr><td>MAIL_FROM_NAME</td><td><?= htmlspecialchars($fromName) ?></td></tr>
      <tr><td>ADMIN_EMAIL</td><td><?= htmlspecialchars($env['ADMIN_EMAIL'] ?? '-') ?></td></tr>
    </table>
  </div>

  <!-- CEK PHP EXTENSION -->
  <div class="card">
    <h2>🧩 PHP Extensions</h2>
    <table>
      <?php
      $exts = ['openssl','curl','mbstring','fileinfo','sockets','filter'];
      foreach ($exts as $ext) {
          $ok = extension_loaded($ext);
          echo "<tr><td>{$ext}</td><td><span class='badge " . ($ok ? 'badge-ok' : 'badge-err') . "'>" . ($ok ? '✅ aktif' : '❌ tidak aktif') . "</span></td></tr>";
      }
      ?>
      <tr><td>PHP Version</td><td><span class="badge badge-ok"><?= PHP_VERSION ?></span></td></tr>
      <tr><td>PHPMailer</td><td>
        <?php
        $pmPath = dirname(__DIR__) . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
        echo file_exists($pmPath)
            ? '<span class="badge badge-ok">✅ terinstall</span>'
            : '<span class="badge badge-err">❌ tidak ditemukan - jalankan composer install</span>';
        ?>
      </td></tr>
    </table>
  </div>

  <!-- CEK KONEKSI SMTP -->
  <div class="card">
    <h2>🔌 Test Koneksi SMTP</h2>
    <form method="POST">
      <input type="hidden" name="action" value="ping">
      <button class="btn btn-gray" type="submit">Ping <?= htmlspecialchars($host) ?>:<?= $port ?></button>
    </form>
    <?php if ($action === 'ping' && $log) echo '<pre>' . htmlspecialchars(implode("\n", $log)) . '</pre>'; ?>
  </div>

  <!-- KIRIM TEST EMAIL -->
  <div class="card">
    <h2>📧 Kirim Test Email</h2>
    <form method="POST">
      <input type="hidden" name="action" value="send">
      <label>Kirim ke Email:</label>
      <input type="email" name="to_email" value="<?= htmlspecialchars($toEmail) ?>" placeholder="contoh@gmail.com" required>
      <button class="btn btn-blue" type="submit" style="margin-top:10px">Kirim Test Email</button>
    </form>
    <?php if ($action === 'send' && $log) echo '<pre>' . htmlspecialchars(implode("\n", $log)) . '</pre>'; ?>
  </div>

  <p style="text-align:center;color:#9ca3af;font-size:12px;margin-top:8px">
    ⚠️ Hapus <code>public/debug-mail.php</code> setelah selesai debug!
  </p>
</div>
</body>
</html>
