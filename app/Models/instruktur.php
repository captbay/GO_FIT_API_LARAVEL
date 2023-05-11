<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class instruktur extends Model
{
    use HasFactory;

    protected $table = "instruktur";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_users',
        'no_instruktur',
        'name',
        'address',
        'number_phone',
        'born_date',
        'gender',
        'total_late',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    public function jadwal_umum()
    {
        return $this->hasMany(jadwal_umum::class,  'id_instruktur', 'id');
    }

    public function class_running()
    {
        return $this->hasMany(class_running::class,  'id_instruktur', 'id');
    }

    public function instruktur_presensi()
    {
        return $this->hasMany(instruktur_presensi::class,  'id_instruktur', 'id');
    }

    public function instruktur_izin()
    {
        return $this->hasMany(instruktur_izin::class,  'id_instruktur', 'id');
    }

    public function instruktur_izin_pengganti()
    {
        return $this->hasMany(instruktur_izin::class,  'id_instruktur_pengganti', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        if (!is_null($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute($value)
    {
        if (!is_null($this->attributes['updated_at'])) {
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }
}