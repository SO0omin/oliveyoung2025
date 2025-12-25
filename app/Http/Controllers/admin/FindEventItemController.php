<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Item; // Item 모델 사용 가정

class FindEventItemController extends Controller
{
    /**
     * 제품 검색 및 목록 팝업 화면을 출력합니다.
     */
    public function index(Request $request)
    {
        // 검색어 (text1) 파라미터 획득
        $text1 = $request->input('text1', '');
        
        $data['list'] = $this->getlist($text1);
        $data['text1'] = $text1;

        // 팝업 창이므로 레이아웃이 없는 전용 뷰를 사용하거나, 매우 간소화된 뷰를 사용합니다.
        return view('admin.findeventitem.index', $data);
    }
    
    /**
     * 제품 목록을 검색어에 따라 필터링하여 가져옵니다.
     */
    private function getlist($text1)
    {
        $query = Item::orderBy('name', 'asc');

        // text1 (검색어) 필터링: 제품 이름(name) 기준으로 검색
        if ($text1) {
            $query->where('name', 'like', '%' . $text1 . '%');
        }

        // 팝업이므로 페이지네이션을 적용하여 성능을 확보합니다.
        return $query->paginate(10)->appends(['text1' => $text1]);
    }
}