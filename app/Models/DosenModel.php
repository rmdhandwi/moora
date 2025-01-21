<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class DosenModel extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = 'tbl_dosen';
    protected $primaryKey = 'dosen_id';
    protected $keyType = 'string';
    // protected $guarded = [];

    // deteksi kolom pada tabel dinamis
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    // Event model 'creating' untuk mengisi 'dosen_id'
    protected static function boot()
    {
        parent::boot();

        // Event yang akan berjalan sebelum data pengguna dibuat
        static::creating(function ($user) {
            $user->dosen_id = self::generateDosenId();
        });
    }

    // Fungsi untuk membuat kode pengguna
    private static function generateDosenId()
    {
        // Ambil kode pengguna terakhir yang ada
        $lastUser = self::orderBy('dosen_id', 'desc')->first();

        // Jika belum ada kode pengguna, mulai dengan 'D001'
        if (!$lastUser) {
            return 'D001';
        }

        // Ambil angka dari kode pengguna terakhir dan increment
        $lastNumber = intval(substr($lastUser->dosen_id, 1));
        $newNumber = $lastNumber + 1;

        // Format kode pengguna dengan 3 digit, misal: 'D002', 'D010'
        return 'D' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'dosen_id', 'dosen_id');
    }

    public function angkatan()
    {
        return $this->hasOne(AngkatanModel::class, 'dosen_id', 'dosen_id');
    }

}
