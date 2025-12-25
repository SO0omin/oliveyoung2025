<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'total_price',
        'status',
        'shipping_label', // 👈 이게 빠져있으면 create() 시 저장되지 않습니다.
        'shipping_name',
        'shipping_phone',
        'shipping_zipcode',
        'shipping_address1',
        'shipping_address2',
    ];

    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
}