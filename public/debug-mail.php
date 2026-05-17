<?php
/**
 * DEBUG EMAIL - PPDB Yayasan Indonesia
 * Akses: https://demo-ppdbyayasan.arifsiddikm.biz.id/debug-mail.php?key=debugppdb2025
 * !! HAPUS FILE INI SETELAH SELESAI DEBUG !!
 */
define('DEBUG_PASS', 'debugppdb2025');
if (!isset($_GET['key']) || $_GET['key'] !== DEBUG_PASS) {
    http_response_code(403);
    die('<h2 style="font-family:sans-serif;color:red;">403 - Tambahkan ?key=debugppdb2025</h2>');
}

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

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
$pmOk     = file_exists($autoload);
if ($pmOk) require_once $autoload;

// =============================================
// PING SMTP
// =============================================
if ($action === 'ping') {
    $conn = @fsockopen("ssl://{$host}", $port, $errno, $errstr, 5);
    if ($conn) {
        $banner = fgets($conn, 512);
        fclose($conn);
        $result = '<div class="ok">✅ Koneksi SMTP berhasil! Banner: ' . htmlspecialchars($banner) . '</div>';
        $log[]  = "OK — {$banner}";
    } else {
        $result = '<div class="err">❌ SMTP {$host}:{$port} tidak bisa dikonek — Error #{$errno}: ' . htmlspecialchars($errstr) . '<br><strong>Hosting kemungkinan blokir outbound port ini.</strong></div>';
        $log[]  = "Gagal #{$errno}: {$errstr}";
    }
}

// =============================================
// KIRIM VIA SMTP (PHPMailer)
// =============================================
if ($action === 'send_smtp' && $toEmail && $pmOk) {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host        = $host;
        $mail->SMTPAuth    = true;
        $mail->Username    = $user;
        $mail->Password    = $pass;
        $mail->SMTPSecure  = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port        = $port;
        $mail->CharSet     = 'UTF-8';
        $mail->Timeout     = 10;
        $mail->SMTPDebug   = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
        $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
        ob_start();
        $mail->Debugoutput = 'echo';
        $mail->setFrom($from, $fromName);
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = '[DEBUG SMTP] Test Email PPDB - ' . date('d/m/Y H:i:s');
        $mail->Body    = '<h2>✅ SMTP Berhasil!</h2><p>Waktu: ' . date('d/m/Y H:i:s') . '</p>';
        $mail->send();
        $log[] = ob_get_clean();
        $result = '<div class="ok">✅ Email via SMTP berhasil dikirim ke ' . htmlspecialchars($toEmail) . '! Cek inbox/spam.</div>';
    } catch (\Exception $e) {
        $log[] = ob_get_clean();
        $result = '<div class="err">❌ SMTP Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// =============================================
// KIRIM VIA PHP mail() NATIVE
// =============================================
if ($action === 'send_native' && $toEmail) {
    $subject = '[DEBUG NATIVE] Test Email PPDB - ' . date('d/m/Y H:i:s');
    $body    = "Test email dari PHP mail() native.\nWaktu: " . date('d/m/Y H:i:s') . "\nDari: {$from}";
    $headers = implode("\r\n", [
        "From: {$fromName} <{$from}>",
        "Reply-To: {$from}",
        "MIME-Version: 1.0",
        "Content-Type: text/plain; charset=UTF-8",
        "X-Mailer: PHP/" . PHP_VERSION,
    ]);

    $sent = @mail($toEmail, $subject, $body, $headers);
    if ($sent) {
        $result = '<div class="ok">✅ PHP mail() native berhasil! Email dikirim ke ' . htmlspecialchars($toEmail) . '. Cek inbox/spam dalam 1-2 menit.</div>';
        $log[]  = 'mail() return: true';
    } else {
        $lastErr = error_get_last();
        $result  = '<div class="err">❌ PHP mail() native gagal. ' . htmlspecialchars($lastErr['message'] ?? 'Unknown error') . '</div>';
        $log[]   = 'mail() return: false | ' . ($lastErr['message'] ?? '');
    }
}

// =============================================
// KIRIM VIA PHPMailer + mail() NATIVE
// =============================================
if ($action === 'send_phpmailer_native' && $toEmail && $pmOk) {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isMail(); // pakai PHP mail() native, bukan SMTP
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($from, $fromName);
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = '[DEBUG PHPMailer+Native] Test Email PPDB - ' . date('d/m/Y H:i:s');
        $mail->Body    = '<h2>✅ PHPMailer via mail() native Berhasil!</h2><p>Waktu: ' . date('d/m/Y H:i:s') . '</p>';
        $mail->AltBody = 'PHPMailer native berhasil. Waktu: ' . date('d/m/Y H:i:s');
        $mail->send();
        $result = '<div class="ok">✅ PHPMailer (mail native) berhasil dikirim ke ' . htmlspecialchars($toEmail) . '! Cek inbox/spam.</div>';
    } catch (\Exception $e) {
        $result = '<div class="err">❌ PHPMailer native Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        $log[]  = $e->getMessage();
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
.badge-err{background:#fee2e2;color:#991b1b}
label{font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px}
input[type=email]{width:100%;padding:8px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;margin-bottom:10px}
input[type=email]:focus{border-color:#3b82f6}
.btn{display:inline-block;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;border:none;margin-right:8px;margin-top:4px}
.btn-blue{background:#1e40af;color:#fff}
.btn-green{background:#059669;color:#fff}
.btn-gray{background:#e5e7eb;color:#374151}
.btn-orange{background:#d97706;color:#fff}
.ok{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;border-radius:8px;padding:12px;margin-bottom:12px}
.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:8px;padding:12px;margin-bottom:12px}
.warn{background:#fef3c7;border:1px solid #fde047;border-radius:8px;padding:10px 14px;font-size:13px;color:#92400e;margin-bottom:12px}
pre{background:#0f172a;color:#e2e8f0;padding:14px;border-radius:8px;font-size:11px;overflow-x:auto;white-space:pre-wrap;margin-top:10px;max-height:300px;overflow-y:auto}
.note{font-size:12px;color:#6b7280;margin-top:6px}
</style>
</head>
<body>
<div class="wrap">
  <h1>🔧 Debug Email — PPDB Yayasan</h1>
  <p class="sub">Tool debug email. <strong style="color:red">Hapus file ini setelah selesai!</strong></p>
  <div class="warn">⚠️ File ini sensitif. Key: <code>?key=debugppdb2025</code></div>

  <?php if ($result) echo $result; ?>
  <?php if ($log) echo '<pre>' . htmlspecialchars(implode("\n", $log)) . '</pre>'; ?>

  <!-- CONFIG -->
  <div class="card">
    <h2>📋 Konfigurasi .env (MAIL)</h2>
    <table>
      <tr><td>MAIL_HOST</td><td><?= htmlspecialchars($host) ?></td></tr>
      <tr><td>MAIL_PORT</td><td><?= $port ?></td></tr>
      <tr><td>MAIL_USERNAME</td><td><?= htmlspecialchars($user) ?></td></tr>
      <tr><td>MAIL_PASSWORD</td><td><?= $pass ? str_repeat('*', strlen($pass)) . ' (' . strlen($pass) . ' chars)' : '<span class="badge badge-err">KOSONG!</span>' ?></td></tr>
      <tr><td>MAIL_FROM_ADDRESS</td><td><?= htmlspecialchars($from) ?></td></tr>
      <tr><td>ADMIN_EMAIL</td><td><?= htmlspecialchars($env['ADMIN_EMAIL'] ?? '-') ?></td></tr>
    </table>
  </div>

  <!-- EXTENSIONS -->
  <div class="card">
    <h2>🧩 PHP Extensions</h2>
    <table>
      <?php foreach (['openssl','curl','mbstring','fileinfo','sockets'] as $ext): ?>
      <tr><td><?= $ext ?></td><td><span class="badge <?= extension_loaded($ext) ? 'badge-ok' : 'badge-err' ?>"><?= extension_loaded($ext) ? '✅ aktif' : '❌ tidak aktif' ?></span></td></tr>
      <?php endforeach; ?>
      <tr><td>PHP Version</td><td><?= PHP_VERSION ?></td></tr>
      <tr><td>PHPMailer</td><td><span class="badge <?= $pmOk ? 'badge-ok' : 'badge-err' ?>"><?= $pmOk ? '✅ OK' : '❌ tidak ditemukan' ?></span></td></tr>
      <tr><td>sendmail path</td><td><?= htmlspecialchars(ini_get('sendmail_path') ?: '(kosong)') ?></td></tr>
    </table>
  </div>

  <!-- TEST AREA -->
  <div class="card">
    <h2>🧪 Test Email</h2>
    <label>Kirim ke Email:</label>
    <form method="POST" style="display:inline">
      <input type="email" name="to_email" value="<?= htmlspecialchars($toEmail) ?>" placeholder="email@example.com" required>

      <!-- PING -->
      <button class="btn btn-gray" type="submit" name="action" value="ping">
        🔌 Ping SMTP <?= $host ?>:<?= $port ?>
      </button>

      <!-- SMTP -->
      <button class="btn btn-blue" type="submit" name="action" value="send_smtp">
        📤 Kirim via SMTP
      </button>

      <!-- PHP mail() native -->
      <button class="btn btn-green" type="submit" name="action" value="send_native">
        📨 Kirim via PHP mail() Native
      </button>

      <!-- PHPMailer + mail() native -->
      <button class="btn btn-orange" type="submit" name="action" value="send_phpmailer_native">
        📧 PHPMailer + mail() Native
      </button>
    </form>

    <p class="note">
      Kalau <strong>SMTP gagal</strong> (Network unreachable), coba <strong>"PHP mail() Native"</strong> atau <strong>"PHPMailer + mail() Native"</strong>.
      Yang berhasil = yang akan dipakai di MailService.
    </p>
  </div>

  <p style="text-align:center;color:#9ca3af;font-size:12px;margin-top:8px">
    ⚠️ Hapus <code>public/debug-mail.php</code> setelah selesai!
  </p>
</div>
</body>
</html>
