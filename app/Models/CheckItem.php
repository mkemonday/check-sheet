<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckItem extends Model
{
    protected $fillable = ['name', 'area_id', 'method_id', 'created_by', 'updated_by', 'photo_paths'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function method()
    {
        return $this->belongsTo(CheckMethod::class, 'method_id');
    }

    public function dailyChecks()
    {
        return $this->hasMany(DailyCheck::class, 'check_item_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}