<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        "customer_id",
        "label", 
        "name", 
        "phone",
        "zipcode", 
        "address1", 
        "address2"
    ];
}
