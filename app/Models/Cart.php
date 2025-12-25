<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'customer_id',
        'item_id',
        'qty'
    ];
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
