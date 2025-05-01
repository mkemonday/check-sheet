<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckMethod extends Model
{
    protected $fillable = ['name', 'created_by', 'updated_by'];

    public function checkItems()
    {
        return $this->hasMany(CheckItem::class, 'method_id');
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