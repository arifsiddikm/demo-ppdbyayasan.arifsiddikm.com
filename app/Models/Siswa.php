<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    protected $table = 'siswas';
    protected $fillable = [
        'pendaftaran_id','nisn','nama_siswa','jk','phone','email',
        'agama','tempat_lahir','tanggal_lahir','alamat',
        'asal_sekolah','tahun_lulus','nomor_ijazah',
    ];
    protected $casts = ['tanggal_lahir' => 'date'];

    public function pendaftaran(): BelongsTo { return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id'); }

    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jk === 'laki_laki' ? 'Laki-Laki' : 'Perempuan';
    }
    public function getAgamaLabelAttribute(): string
    {
        return match($this->agama) {
            'islam'=>'Islam','protestan'=>'Kristen Protestan','katolik'=>'Katolik',
            'hindu'=>'Hindu','budha'=>'Budha','khonghucu'=>'Konghucu',
            default=>ucfirst($this->agama),
        };
    }
}
