<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class gym_booking extends Model
{
    use HasFactory;

    protected $table = "gym_booking";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_gym_booking',
        'id_gym',
        'id_member',
        'date_booking',
        'date_time',
    ];

    public function gym()
    {
        return $this->belongsTo(gym::class, 'id_gym');
    }

    public function member()
    {
        return $this->belongsTo(member::class, 'id_member');
    }

    public function gym_history()
    {
        return $this->hasOne(gym_history::class,  'id_gym_booking', 'id');
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
