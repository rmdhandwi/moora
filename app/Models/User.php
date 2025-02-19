<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = 'tbl_users';
    protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    // protected $guarded = [];

    // deteksi kolom pada tabel dinamis
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    protected $hidden_ = [
        'password',
    ];

    // Event model 'creating' untuk mengisi 'user_id'
    protected static function boot()
    {
        parent::boot();

        // Event yang akan berjalan sebelum data pengguna dibuat
        static::creating(function ($user) {
            $user->user_id = self::generateUserId();
        });
    }

    // Fungsi untuk membuat kode pengguna
    private static function generateUserId()
    {
        // Ambil kode pengguna terakhir yang ada
        $lastUser = self::orderBy('user_id', 'desc')->first();

        // Jika belum ada kode pengguna, mulai dengan 'U001'
        if (!$lastUser) {
            return 'U001';
        }

        // Ambil angka dari kode pengguna terakhir dan increment
        $lastNumber = intval(substr($lastUser->user_id, 1));
        $newNumber = $lastNumber + 1;

        // Format kode pengguna dengan 3 digit, misal: 'U002', 'U010'
        return 'U' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function dosen()
    {
        return $this->hasOne(DosenModel::class, 'user_id', 'user_id');
    }

}
