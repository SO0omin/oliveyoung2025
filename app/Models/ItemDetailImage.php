<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemDetailImage extends Model
{
    protected $table = 'item_detail_images';
    
    // 이 필드들을 Mass Assignment로부터 보호하지 않습니다.
    protected $fillable = ['item_id', 'img_path'];
    
    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }
}
