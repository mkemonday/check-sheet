<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCheckPhoto extends Model
{
    protected $fillable = ['file_path'];

    public function dailyCheck()
    {
        return $this->belongsTo(DailyCheck::class);
    }

}
