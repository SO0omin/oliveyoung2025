<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 'detail_category_id', 'company_id', // 필요한 컬럼들
    ];

    // 소분류 (DetailCategory)
    public function detailCategory()
    {
        return $this->belongsTo(DetailCategory::class, 'detail_category_id', 'id');
    }
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_items');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function detailImages()
    {
        return $this->hasMany(ItemDetailImage::class, 'item_id');
    }
    public function discounts()
    {
        return $this->hasMany(Discount::class, 'item_id', 'id');
    }
    // 현재 진행중인 할인만 가져오는 편리한 함수
    public function activeDiscount()
    {
        return $this->hasOne(Discount::class, 'item_id', 'id')
                    ->where('is_active', 1)
                    ->where(function($q){
                        $q->whereNull('end_at')->orWhere('end_at', '>=', now());
                    });
    }
}
