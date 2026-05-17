<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliSiswa extends Model
{
    protected $table = 'wali_siswas';
    protected $fillable = [
        'pendaftaran_id','nama_wali','hubungan','jenis_wali',
        'pekerjaan','notelp_wali','email','nik','alamat',
    ];
    public function pendaftaran(): BelongsTo { return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id'); }
    public function getHubunganLabelAttribute(): string
    {
        return match($this->hubungan) {
            'orang_tua'=>ucfirst($this->jenis_wali ?? 'Orang Tua'),
            'saudara_kandung'=>'Saudara Kandung',
            'saudara_keluarga'=>'Saudara Keluarga',
            'wali'=>'Wali',
            default=>ucfirst($this->hubungan),
        };
    }
}
