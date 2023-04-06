<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class gym extends Model
{
    use HasFactory;

    protected $table = "gym";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'capacity',
        'date',
        'start_gym',
        'end_gym',
    ];

    public function gym_booking()
    {
        return $this->hasMany(gym_booking::class,  'id_gym', 'id');
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
