<?php
namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    const NOMINAL = 200000;

    public function requestSnapToken(Pendaftaran $pendaftaran): array
    {
        $siswa    = $pendaftaran->siswa;
        $kode     = $pendaftaran->kode_regis;
        $prefix   = config('midtrans.order_prefix', 'INV');
        $orderId  = $prefix . strtoupper(substr(md5(uniqid($kode, true)), 0, 12));
        $nama     = $siswa?->nama_siswa ?? $kode;
        $email    = $siswa?->email ?? 'noemail@ppdb.id';
        $phone    = $siswa?->phone ?? '0';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => config('midtrans.riplabs_snaptoken_url'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'key'        => config('midtrans.riplabs_key'),
                'order_id'   => $orderId,
                'total_harga'=> (string) self::NOMINAL,
                'nama'       => $nama,
                'email'      => $email,
                'notelp'     => $phone,
                'namaproduk' => 'Uang Pendaftaran PPDB - ' . $kode,
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        Log::info('Riplabs snaptoken request', [
            'order_id'  => $orderId,
            'http_code' => $httpCode,
            'curl_err'  => $curlError ?: null,
            'raw'       => $response,
        ]);

        if ($curlError || !$response || $httpCode !== 200) {
            return ['status' => false, 'message' => 'Gagal terhubung ke server pembayaran. Error: ' . ($curlError ?: "HTTP {$httpCode}")];
        }

        $data      = json_decode($response, true);
        $snapToken = $data['snaptoken'] ?? $data['snap_token'] ?? null;
        $isSuccess = !empty($snapToken) && ($data['status'] ?? false) === true;

        if (!$isSuccess || !$snapToken) {
            return ['status' => false, 'message' => $data['message'] ?? 'Gagal mendapatkan token pembayaran.'];
        }

        // Simpan ke DB
        Pembayaran::where('pendaftaran_id', $pendaftaran->id)
            ->where('status_pembayaran', 'pending')
            ->update(['status_pembayaran' => 'kadaluarsa']);

        $pembayaran = Pembayaran::create([
            'pendaftaran_id'       => $pendaftaran->id,
            'metode_pembayaran_id' => \App\Models\MetodePembayaran::where('tipe','otomatis')->first()?->id,
            'nominal'              => self::NOMINAL,
            'order_id'             => $orderId,
            'snap_token'           => $snapToken,
            'status_pembayaran'    => 'pending',
        ]);

        return ['status' => true, 'snap_token' => $snapToken, 'order_id' => $orderId, 'pembayaran_id' => $pembayaran->id];
    }

    public function handleCallback(array $data): bool
    {
        $callbackKey = config('midtrans.callback_key');
        if (!empty($callbackKey) && ($data['key'] ?? '') !== $callbackKey) {
            Log::warning('Midtrans callback key mismatch');
            return false;
        }

        $orderId          = $data['order_id'] ?? null;
        $transactionStatus= $data['transaction_status'] ?? null;
        $fraudStatus      = $data['fraud_status'] ?? null;

        if (!$orderId) return false;

        $pembayaran = Pembayaran::where('order_id', $orderId)->first();
        if (!$pembayaran) { Log::warning("Callback: order_id {$orderId} not found"); return false; }

        $statusMap = [
            'capture'   => ($fraudStatus === 'accept') ? 'sukses' : 'menunggu_verifikasi',
            'settlement'=> 'sukses',
            'pending'   => 'pending',
            'deny'      => 'gagal',
            'expire'    => 'kadaluarsa',
            'cancel'    => 'gagal',
        ];

        $newStatus = $statusMap[$transactionStatus] ?? null;
        if (!$newStatus) return false;

        $pembayaran->update(['status_pembayaran' => $newStatus, 'tanggal_pembayaran' => now()->toDateString()]);

        if ($newStatus === 'sukses') {
            $pendaftaran = $pembayaran->pendaftaran;
            $pendaftaran->update(['status' => 'lunas']);
            app(MailService::class)->sendPembayaranSelesai($pendaftaran);
        }

        Log::info("Callback handled: {$orderId} → {$newStatus}");
        return true;
    }
}
