<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokumen extends Model
{
    protected $table = 'dokumens';
    protected $fillable = ['pendaftaran_id','jenis_dokumen','file_path','original_name'];
    public function pendaftaran(): BelongsTo { return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id'); }
    public function getUrlAttribute(): string { return asset('storage/' . $this->file_path); }
    public function getLabelAttribute(): string
    {
        return match($this->jenis_dokumen) {
            'pas_foto'=>'Pas Foto','kk'=>'Kartu Keluarga','akta'=>'Akta Kelahiran',
            'ijazah'=>'Ijazah','skhun'=>'SKHUN','stl'=>'Surat Tanda Lulus',
            'lampiran_jalur'=>'Lampiran Jalur Pendaftaran',default=>ucfirst($this->jenis_dokumen),
        };
    }
}
