<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pendaftaran extends Model
{
    protected $table = 'pendaftarans';
    protected $fillable = [
        'kode_regis','tahun_akademik_id','sekolah_id','jurusan_id',
        'jalur_pendaftaran','ket_jalur_pendaftaran','status','catatan_admin',
        'diverifikasi_oleh','tanggal_submit','tanggal_verifikasi','dibuat_oleh',
    ];
    protected $casts = [
        'tanggal_submit'     => 'datetime',
        'tanggal_verifikasi' => 'datetime',
    ];

    public function tahunAkademik(): BelongsTo   { return $this->belongsTo(TahunAkademik::class,'tahun_akademik_id'); }
    public function sekolah(): BelongsTo          { return $this->belongsTo(Sekolah::class,'sekolah_id'); }
    public function jurusan(): BelongsTo          { return $this->belongsTo(Jurusan::class,'jurusan_id'); }
    public function siswa(): HasOne               { return $this->hasOne(Siswa::class,'pendaftaran_id'); }
    public function waliSiswas(): HasMany         { return $this->hasMany(WaliSiswa::class,'pendaftaran_id'); }
    public function dokumens(): HasMany           { return $this->hasMany(Dokumen::class,'pendaftaran_id'); }
    public function pembayarans(): HasMany        { return $this->hasMany(Pembayaran::class,'pendaftaran_id'); }
    public function userVerifikator(): BelongsTo  { return $this->belongsTo(User::class,'diverifikasi_oleh'); }
    public function document(): HasMany           { return $this->hasMany(Dokumen::class,'pendaftaran_id'); }

    public function getLabelStatusAttribute(): string
    {
        return match($this->status) {
            'diproses'            => 'Sedang Diproses',
            'diterima'            => 'Berkas Diterima',
            'ditolak'             => 'Berkas Ditolak',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'lunas'               => 'Lunas / Selesai',
            default               => ucfirst($this->status),
        };
    }

    public function getBadgeColorAttribute(): string
    {
        return match($this->status) {
            'diproses'            => 'bg-yellow-100 text-yellow-800',
            'diterima'            => 'bg-blue-100 text-blue-800',
            'ditolak'             => 'bg-red-100 text-red-800',
            'menunggu_pembayaran' => 'bg-purple-100 text-purple-800',
            'lunas'               => 'bg-green-100 text-green-800',
            default               => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Bisa bayar jika status diterima/menunggu_pembayaran
     * dan belum ada pembayaran sukses
     */
    public function canPay(): bool
    {
        if (!in_array($this->status, ['diterima','menunggu_pembayaran'])) return false;
        return !$this->pembayarans()->where('status_pembayaran','sukses')->exists();
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas'
            || $this->pembayarans()->where('status_pembayaran','sukses')->exists();
    }

    public function getNamaSiswaAttribute(): string    { return $this->siswa?->nama_siswa ?? ''; }
    public function getPembayaranAktifAttribute(): ?\App\Models\Pembayaran {
        return $this->pembayarans()->whereIn('status_pembayaran',['sukses','menunggu_verifikasi','pending'])->latest()->first();
    }
}
