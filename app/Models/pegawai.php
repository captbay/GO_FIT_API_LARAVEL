<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class pegawai extends Model
{
    use HasFactory;

    protected $table = "pegawai";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_users',
        'no_pegawai',
        'role',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    public function deposit_reguler_history()
    {
        return $this->hasMany(deposit_reguler_history::class,  'id_pegawai', 'id');
    }

    public function deposit_package_history()
    {
        return $this->hasMany(deposit_package_history::class,  'id_pegawai', 'id');
    }

    public function aktivasi_history()
    {
        return $this->hasMany(aktivasi_history::class,  'id_pegawai', 'id');
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
