<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class class_running extends Model
{
    use HasFactory;

    protected $table = "class_running";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_jadwal_umum',
        'id_instruktur',
        'start_class',
        'end_class',
        'capacity',
        'date',
        'day_name',
        'status',
    ];


    public function class_booking()
    {
        return $this->hasMany(class_booking::class, 'id_class_running', 'id');
    }

    public function jadwal_umum()
    {
        return $this->belongsTo(jadwal_umum::class, 'id_jadwal_umum');
    }

    public function instruktur()
    {
        return $this->belongsTo(instruktur::class, 'id_instruktur');
    }

    public function instruktur_izin()
    {
        return $this->hasOne(instruktur_izin::class,  'id_class_running', 'id');
    }

    public function instruktur_presensi()
    {
        return $this->hasOne(instruktur_presensi::class,  'id_class_running', 'id');
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
