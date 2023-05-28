<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class member extends Model
{
    use HasFactory;

    protected $table = "member";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_users',
        'no_member',
        'name',
        'address',
        'number_phone',
        'born_date',
        'gender',
        'jumlah_deposit_reguler',
        'expired_date_membership',
        'status_membership',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    public function deposit_reguler_history()
    {
        return $this->hasMany(deposit_reguler_history::class,  'id_member', 'id');
    }

    public function deposit_package_history()
    {
        return $this->hasMany(deposit_package_history::class,  'id_member', 'id');
    }

    public function aktivasi_history()
    {
        return $this->hasMany(deposit_package_history::class,  'id_member', 'id');
    }

    public function member_activity()
    {
        return $this->hasMany(member_activity::class,  'id_member', 'id');
    }

    public function gym_booking()
    {
        return $this->hasMany(gym_booking::class,  'id_member', 'id');
    }

    public function deposit_package()
    {
        return $this->hasMany(deposit_package::class,  'id_member', 'id');
    }

    public function class_booking()
    {
        return $this->hasMany(class_booking::class,  'id_member', 'id');
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
