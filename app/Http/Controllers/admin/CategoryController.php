<?php

namespace App\Http\Controllers\admin;

// app/Http/Controllers/CategoryController.php

use App\Http\Controllers\Controller;
use App\Models\SubCategory; // 모델 이름 확인 필요
use App\Models\DetailCategory; // 모델 이름 확인 필요
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // 대분류 ID에 해당하는 중분류 목록을 JSON으로 반환
    public function getSubcategories($categoryId)
    {
        // 'category_id' 컬럼 이름이 정확한지 확인하세요.
        $subCategories = SubCategory::where('category_id', $categoryId)
                                   ->get(['id', 'name']);
        return response()->json($subCategories);
    }

    // 중분류 ID에 해당하는 소분류 목록을 JSON으로 반환
    public function getDetailcategories($subcategoryId)
    {
        // 'sub_category_id' 컬럼 이름이 정확한지 확인하세요. (혹은 'sub_id' 등)
        $detailCategories = DetailCategory::where('sub_id', $subcategoryId)
                                         ->get(['id', 'name']);
        return response()->json($detailCategories);
    }
}