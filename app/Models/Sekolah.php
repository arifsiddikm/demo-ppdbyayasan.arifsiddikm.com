<?php
// app/Models/Sekolah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    protected $table = 'sekolahs';
    protected $fillable = [
        'nama_sekolah','singkatan','tingkatan','npsn','alamat','kota',
        'phone','email','website','deskripsi','logo','akreditasi',
        'kuota','tahun_berdiri','is_active','urutan',
    ];

    public function jurusans(): HasMany
    {
        return $this->hasMany(Jurusan::class, 'sekolah_id');
    }

    public function jurusan(): HasMany
    {
        return $this->hasMany(Jurusan::class, 'sekolah_id');
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'sekolah_id');
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/default-school.png');
    }
}
