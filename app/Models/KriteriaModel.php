<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class KriteriaModel extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = 'tbl_kriteria';
    protected $primaryKey = 'kriteria_id';
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
        static::creating(function ($user) {
            $user->kriteria_id = self::generateKriteriaId();
        });
    }

    // Fungsi untuk membuat kode kriteria
    private static function generateKriteriaId()
    {
        // Ambil kode kriteria terakhir yang ada
        $lastKriteria = self::orderBy('kriteria_id', 'desc')->first();

        // Jika belum ada kode kriteria, mulai dengan 'K01'
        if (!$lastKriteria) {
            return 'K01';
        }

        // Ambil angka dari kode kriteria terakhir dan increment
        $lastNumber = intval(substr($lastKriteria->kriteria_id, 1));
        $newNumber = $lastNumber + 1;

        // Format kode kriteria dengan 2 digit, misal: 'K02', 'K10'
        return 'K' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

   
}
