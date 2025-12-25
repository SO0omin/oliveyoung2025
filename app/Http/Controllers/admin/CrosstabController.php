<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Gubun;
use App\Models\Sale;

class CrosstabController extends Controller
{
    public function index()
    {
        // 1. 날짜 파라미터 처리 (기본값: 한 달 전 ~ 오늘)
        $text1 = request('text1', date('Y-m-d', strtotime('-1 month')));
        $text2 = request('text2', date('Y-m-d'));

        // 2. 조회
        $data['text1'] = $text1;
        $data['text2'] = $text2;
        $data['list'] = $this->getlist($text1, $text2); // 날짜 범위 전달

        // 3. 제품 리스트 (콤보박스)
        $data['list_item'] = $this->getlist_item();

        return view('admin.crosstab.index', $data);
    }

    public function getlist($text1, $text2)
    {
        $company_id = session('company_id');
        $result = Sale::leftjoin('items','sales.item_id','=','items.id')
            ->select(
                'items.name as items_name',
                DB::raw("SUM(CASE WHEN MONTH(writeday)=1 THEN numo ELSE 0 END) AS s1"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=2 THEN numo ELSE 0 END) AS s2"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=3 THEN numo ELSE 0 END) AS s3"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=4 THEN numo ELSE 0 END) AS s4"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=5 THEN numo ELSE 0 END) AS s5"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=6 THEN numo ELSE 0 END) AS s6"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=7 THEN numo ELSE 0 END) AS s7"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=8 THEN numo ELSE 0 END) AS s8"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=9 THEN numo ELSE 0 END) AS s9"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=10 THEN numo ELSE 0 END) AS s10"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=11 THEN numo ELSE 0 END) AS s11"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=12 THEN numo ELSE 0 END) AS s12")
            )
            ->whereBetween('sales.writeday', [$text1, $text2])
            ->when($company_id != 1, function($query) use ($company_id) {
                $query->where('company_id', $company_id);
            })
            ->where('sales.io','=',1)
            ->groupBy('items.name')
            ->orderBy('items.name')
            ->paginate(10)
            ->appends(['text1'=>$text1,'text2'=>$text2]);

        return $result;
    }
}
