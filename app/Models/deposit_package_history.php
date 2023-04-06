<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class deposit_package_history extends Model
{
    use HasFactory;

    protected $table = "deposit_package_history";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_deposit_package_history',
        'id_promo_class',
        'id_member',
        'id_pegawai',
        'date_time',
        'total_price',
        'package_amount',
        'expired_date',
    ];

    public function promo_class()
    {
        return $this->belongsTo(promo_class::class, 'id_promo_class');
    }

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
