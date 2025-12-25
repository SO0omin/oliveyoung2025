<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = ['name', 'category_id'];

    // 대분류 (Category)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // SubCategory.php
    public function detailCategories()
    {
        return $this->hasMany(DetailCategory::class, 'sub_id');
    }
}