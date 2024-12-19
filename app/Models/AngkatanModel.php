<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class AngkatanModel extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = 'tbl_angkatan';
    protected $primaryKey = 'angkatan_id';
    protected $keyType = 'string';
    // protected $guarded = [];

    // deteksi kolom pada tabel dinamis
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    // Event model 'creating' untuk mengisi 'angkatan_id'
    protected static function boot()
    {
        parent::boot();

        // Event yang akan berjalan sebelum data pengguna dibuat
        static::creating(function ($user) {
            $user->angkatan_id = self::generateAngkatanId();
        });
    }

    // Fungsi untuk membuat kode pengguna
    private static function generateAngkatanId()
    {
        // Ambil kode pengguna terakhir yang ada
        $lastUser = self::orderBy('angkatan_id', 'desc')->first();

        // Jika belum ada kode pengguna, mulai dengan 'A001'
        if (!$lastUser) {
            return 'A001';
        }

        // Ambil angka dari kode pengguna terakhir dan increment
        $lastNumber = intval(substr($lastUser->angkatan_id, 1));
        $newNumber = $lastNumber + 1;

        // Format kode pengguna dengan 3 digit, misal: 'D002', 'D010'
        return 'A' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'angkatan_id', 'angkatan_id');
    }

    public function dosen()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_id', 'dosen_id');
    }
}
