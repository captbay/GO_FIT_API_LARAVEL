<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class promo_cash extends Model
{
    use HasFactory;

    protected $table = "promo_cash";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'min_deposit_cash',
        'min_topup_cash',
        'bonus_cash',
    ];

    public function deposit_reguler_history()
    {
        return $this->hasMany(deposit_reguler_history::class, 'id_promo_cash', 'id');
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
