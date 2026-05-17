<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PengaturanWeb extends Model
{
    protected $table = 'pengaturan_webs';
    protected $fillable = [
        'nama_yayasan','singkatan_yayasan','alamat_yayasan','email_yayasan',
        'phone_yayasan','website_yayasan','logo_yayasan','favicon_yayasan',
        'deskripsi_yayasan','tagline','hero_bg_image','ppdb_aktif','pengumuman',
    ];
    protected $casts = ['ppdb_aktif' => 'boolean'];

    public static function getSetting(): self
    {
        return self::first() ?? new self([
            'nama_yayasan' => 'PPDB Yayasan Indonesia',
            'tagline'      => 'Membangun Generasi Unggul Bangsa',
        ]);
    }
}
