<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class instruktur_izin extends Model
{
    use HasFactory;

    protected $table = "instruktur_izin";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_instruktur',
        'id_instruktur_pengganti',
        'id_class_running',
        'alasan',
        'is_confirm',
        'date'
    ];

    public function instruktur()
    {
        return $this->belongsTo(instruktur::class, 'id_instruktur');
    }

    public function instruktur_pengganti()
    {
        return $this->belongsTo(instruktur::class, 'id_instruktur_pengganti');
    }

    public function class_running()
    {
        return $this->belongsTo(class_running::class, 'id_class_running');
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