<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayarans';
    protected $fillable = [
        'pendaftaran_id','metode_pembayaran_id','nominal','order_id','snap_token',
        'status_pembayaran','proof_path','tanggal_pembayaran',
        'verifikasi_oleh','verifikasi_tanggal','catatan_verifikasi',
    ];
    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'verifikasi_tanggal' => 'datetime',
        'nominal'            => 'integer',
    ];

    public function pendaftaran(): BelongsTo       { return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id'); }
    public function metodePembayaran(): BelongsTo  { return $this->belongsTo(MetodePembayaran::class, 'metode_pembayaran_id'); }
    public function verifikator(): BelongsTo        { return $this->belongsTo(User::class, 'verifikasi_oleh'); }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'pending'             => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi Admin',
            'sukses'              => 'Pembayaran Lunas ✓',
            'gagal'               => 'Pembayaran Gagal',
            'kadaluarsa'          => 'Kadaluarsa',
            default               => ucfirst($this->status_pembayaran),
        };
    }
    public function getBadgeColorAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'pending'             => 'bg-yellow-100 text-yellow-800',
            'menunggu_verifikasi' => 'bg-blue-100 text-blue-800',
            'sukses'              => 'bg-green-100 text-green-800',
            'gagal'               => 'bg-red-100 text-red-800',
            'kadaluarsa'          => 'bg-gray-100 text-gray-800',
            default               => 'bg-gray-100 text-gray-800',
        };
    }
    public function getNominalFormattedAttribute(): string { return 'Rp ' . number_format($this->nominal, 0, ',', '.'); }
    public function isPending(): bool              { return $this->status_pembayaran === 'pending'; }
    public function isMenungguVerifikasi(): bool   { return $this->status_pembayaran === 'menunggu_verifikasi'; }
    public function isSukses(): bool               { return $this->status_pembayaran === 'sukses'; }
}
