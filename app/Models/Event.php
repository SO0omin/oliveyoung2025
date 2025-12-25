<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;

class Event extends Model
{
    // Event.php
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    
    public function images()
    {
        return $this->hasMany(EventImage::class)->orderBy('sort'); // 정렬 적용
    }
    public function items()
    {
        return $this->belongsToMany(Item::class, 'event_items', 'event_id', 'item_id');
    }
    public function carousels()
    {
        // Event 모델에 'event_id' 컬럼이 있다고 가정합니다.
        return $this->hasMany(Carousel::class, 'event_id');
    }
}
