<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'gubuns_id',
        'name',
        'price',
        'jaego',
        'pic'
    ];
}
