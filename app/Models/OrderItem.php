<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'item_id', 'qty', 'price', 'sale_price'];

    // OrderItem은 하나의 Item에 속합니다.
    public function item()
    {
        // 💡 item_id가 Item 테이블의 id를 가리키고 있는지 확인
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
