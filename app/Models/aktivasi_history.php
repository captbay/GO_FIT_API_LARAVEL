<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class aktivasi_history extends Model
{
    use HasFactory;

    protected $table = "aktivasi_history";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_aktivasi_history',
        'id_member',
        'id_pegawai',
        'date_time',
        'expired_date',
    ];

    public function member()
    {
        return $this->belongsTo(member::class, 'id_member');
    }

    public function pegawai()
    {
        return $this->belongsTo(pegawai::class, 'id_pegawai');
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
