<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwal_umum extends Model
{
    use HasFactory;

    protected $table = "jadwal_umum";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_instruktur',
        'id_class_detail',
        'start_class',
        'end_class',
        'capacity',
        // 'date',
        'day_name',
        // 'status',
    ];

    public function instruktur()
    {
        return $this->belongsTo(instruktur::class, 'id_instruktur');
    }

    public function class_detail()
    {
        return $this->belongsTo(class_detail::class, 'id_class_detail');
    }

    public function class_running()
    {
        return $this->hasOne(class_running::class, 'id_jadwal_umum', 'id');
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
