<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name','email','password','role','foto_profil','whatsapp'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at'=>'datetime','password'=>'hashed'];

    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }
    public function isAdmin(): bool      { return in_array($this->role, ['admin','superadmin']); }
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto_profil)) {
            return asset('storage/' . $this->foto_profil);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1e40af&color=fff&size=128';
    }
}
