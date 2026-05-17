<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    protected $table = 'testimonis';
    protected $fillable = ['nama','asal_sekolah','tahun_masuk','isi_testimoni','rating','foto','is_active','urutan','pendaftaran_id'];
    protected $casts    = ['is_active' => 'boolean'];

    public function scopeAktif($q)   { return $q->where('is_active', true); }
    public function scopePending($q) { return $q->where('is_active', false)->whereNotNull('pendaftaran_id'); }
}
