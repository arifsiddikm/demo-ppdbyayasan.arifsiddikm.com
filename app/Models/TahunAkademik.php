<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademiks';
    protected $fillable = ['nama_tahun','tanggal_mulai_daftar','tanggal_tutup_daftar','is_active'];
    protected $casts = ['tanggal_mulai_daftar'=>'date','tanggal_tutup_daftar'=>'date','is_active'=>'boolean'];
}
