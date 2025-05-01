<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCheck extends Model
{
    protected $fillable = [
        'check_item_id',
        'check_date',
        'status',
        'checked_by',
        'confirmed_by',
        'verified_by',
        'remarks',
        'checked_at',
        'confirmed_at',
        'verified_at',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function checkItem()
    {
        return $this->belongsTo(CheckItem::class, 'check_item_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function photos()
    {
        return $this->hasMany(DailyCheckPhoto::class);
    }
}
