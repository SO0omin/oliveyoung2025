<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailCategory extends Model
{
    protected $fillable = ['name', 'sub_id'];

    // 중분류 (SubCategory)
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_id', 'id');
    }
}