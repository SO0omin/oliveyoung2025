<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    protected $fillable = [
        'event_id',
        'img_path',
        'sort', // 🚨 이 필드가 반드시 있어야 합니다. 🚨
    ];
}
