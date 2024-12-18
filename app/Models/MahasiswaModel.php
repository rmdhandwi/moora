<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class MahasiswaModel extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = 'tbl_mahasiswa';
    protected $primaryKey = 'mahasiswa_id';
    protected $keyType = 'string';
    // protected $guarded = [];

    // deteksi kolom pada tabel dinamis
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = Schema::getColumnListing($this->table);
    }

    protected static function boot()
    {
        parent::boot();

        // Event yang akan berjalan sebelum data pengguna dibuat
        static::creating(function ($mahasiswa) {
            $mahasiswa->mahasiswa_id = self::generateMahasiswaId();
        });
    }

    // Fungsi untuk membuat kode mahasiswa
    private static function generateMahasiswaId()
    {
        // Ambil kode mahasiswa terakhir yang ada
        $lastMahasiswa = self::orderBy('mahasiswa_id', 'desc')->first();

        // Jika belum ada kode mahasiswa, mulai dengan 'MHS0001'
        if (!$lastMahasiswa) {
            return 'MHS0001';
        }

        // Ambil angka dari kode mahasiswa terakhir dan increment
        $lastNumber = intval(substr($lastMahasiswa->mahasiswa_id, 3)); // Ubah dari 1 ke 3
        $newNumber = $lastNumber + 1;

        // Format kode mahasiswa dengan 4 digit, misal: 'MHS0001', 'MHS0002'
        return 'MHS' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function dosen()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_id', 'dosen_id');
    }

    public function angkatan()
    {
        return $this->belongsTo(AngkatanModel::class, 'angkatan_id', 'angkatan_id');
    }
}
