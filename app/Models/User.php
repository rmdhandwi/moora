<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $table = 'users';
    protected $primarykey = 'id';
    protected $fillable = [
        'id','kode_pengguna','nama_pengguna','kata_sandi','role','status'
    ];

    protected $hidden_= [
        'kata_sandi',
    ];

   // Event model 'creating' untuk mengisi 'kode_pengguna'
    protected static function boot()
    {
        parent::boot();

        // Event yang akan berjalan sebelum data pengguna dibuat
        static::creating(function ($user) {
            $user->kode_pengguna = self::generateKodePengguna();
        });
    }

    // Fungsi untuk membuat kode pengguna
    private static function generateKodePengguna()
    {
        // Ambil kode pengguna terakhir yang ada
        $lastUser = self::orderBy('kode_pengguna', 'desc')->first();

        // Jika belum ada kode pengguna, mulai dengan 'U001'
        if (!$lastUser) {
            return 'U001';
        }

        // Ambil angka dari kode pengguna terakhir dan increment
        $lastNumber = intval(substr($lastUser->kode_pengguna, 1));
        $newNumber = $lastNumber + 1;

        // Format kode pengguna dengan 3 digit, misal: 'U002', 'U010'
        return 'U' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
