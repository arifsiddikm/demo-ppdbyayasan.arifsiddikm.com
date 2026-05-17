<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    protected $table = 'metode_pembayarans';
    protected $fillable = ['nama_metode','tipe','nama_bank','no_rekening','atas_nama','instruksi','logo','is_active','urutan'];

    public function isOtomatis(): bool { return $this->tipe === 'otomatis'; }
    public function isBankTransfer(): bool { return $this->tipe === 'bank_transfer'; }
    public function isCash(): bool { return $this->tipe === 'cash'; }
}
