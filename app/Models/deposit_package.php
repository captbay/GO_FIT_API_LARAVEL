<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class deposit_package extends Model
{
    use HasFactory;

    protected $table = "deposit_package";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_class_detail',
        'id_member',
        'package_amount',
        'expired_date',
    ];

    public function class_detail()
    {
        return $this->belongsTo(class_detail::class, 'id_class_detail');
    }

    public function member()
    {
        return $this->belongsTo(member::class, 'id_member');
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