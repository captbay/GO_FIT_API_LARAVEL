<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class deposit_reguler_history extends Model
{
    use HasFactory;

    protected $table = "deposit_reguler_history";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_deposit_reguler_history',
        'id_promo_cash',
        'id_member',
        'id_pegawai',
        'date_time',
        'topup_amount',
        'bonus',
        'sisa',
        'total',
    ];

    public function promo_cash()
    {
        return $this->belongsTo(promo_cash::class, 'id_promo_cash');
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
