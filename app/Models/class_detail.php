<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class class_detail extends Model
{
    use HasFactory;

    protected $table = "class_detail";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
    ];

    public function deposit_package_history()
    {
        return $this->hasMany(deposit_package_history::class,  'id_class_detail', 'id');
    }

    public function class_running()
    {
        return $this->hasMany(class_running::class,  'id_class_detail', 'id');
    }

    public function deposit_package()
    {
        return $this->hasMany(deposit_package::class,  'id_class_detail', 'id');
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
