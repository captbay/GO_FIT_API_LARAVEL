<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class class_booking extends Model
{
    use HasFactory;

    protected $table = "class_booking";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_class_running',
        'id_member',
        'date_time',
    ];

    public function class_running()
    {
        return $this->belongsTo(class_running::class, 'id_class_running');
    }

    public function member()
    {
        return $this->belongsTo(member::class, 'id_member');
    }

    public function class_history()
    {
        return $this->hasOne(class_history::class, 'id_class_booking', 'id');
    }

    public function class_package_history()
    {
        return $this->hasOne(class_package_history::class, 'id_class_booking', 'id');
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
