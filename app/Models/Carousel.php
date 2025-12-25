<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class Carousel extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'link_url', 'pic'];
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id'); // 캐로셀에 event_id가 있다면
    }
}