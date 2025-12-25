<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Category;
use App\Models\Sale;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------
        // 1. 날짜 처리: 입력값 없으면 기본값 한 달 전 ~ 오늘
        // -------------------------
        $text1 = request('text1');
        $text2 = request('text2');

        if($text1 === null || $text1 === '') $text1 = date("Y-m-d", strtotime("-1 month"));
        if($text2 === null || $text2 === '') $text2 = date("Y-m-d");

        $year = $request->input('year', date('Y'));

        // -------------------------
        // 2. 종류별 분포도
        // -------------------------
        $chartData = Sale::with('item')
            ->select('item_id', DB::raw('SUM(numo) as total'))
            ->whereBetween('writeday', [$text1, $text2])
            ->groupBy('item_id')
            ->get();

        $str_label = $chartData->map(fn($sale) => "'".$sale->item->name."'")->implode(',');
        $str_data  = $chartData->pluck('total')->implode(',');

        // -------------------------
        // 3. BEST 제품
        // -------------------------
        $list_best = Sale::with('item')
            ->select('item_id', DB::raw('SUM(numo) as cnumo'))
            ->whereBetween('writeday', [$text1, $text2])
            ->groupBy('item_id')
            ->orderByDesc('cnumo')
            ->limit(10)
            ->get();

        // -------------------------
        // 4. 월별 제품별 매출현황
        // -------------------------
        $list_crosstab = Sale::with('item')
            ->select('item_id',
                DB::raw("SUM(CASE WHEN MONTH(writeday)=1 THEN numo ELSE 0 END) as s1"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=2 THEN numo ELSE 0 END) as s2"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=3 THEN numo ELSE 0 END) as s3"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=4 THEN numo ELSE 0 END) as s4"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=5 THEN numo ELSE 0 END) as s5"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=6 THEN numo ELSE 0 END) as s6"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=7 THEN numo ELSE 0 END) as s7"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=8 THEN numo ELSE 0 END) as s8"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=9 THEN numo ELSE 0 END) as s9"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=10 THEN numo ELSE 0 END) as s10"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=11 THEN numo ELSE 0 END) as s11"),
                DB::raw("SUM(CASE WHEN MONTH(writeday)=12 THEN numo ELSE 0 END) as s12")
            )
            ->whereYear('writeday', $year)
            ->groupBy('item_id')
            ->get();

        // -------------------------
        // 5. 제품 리스트 (필터용)
        // -------------------------
        $list_item = $this->getlist_item();

        return view('admin.chart.index', compact(
            'text1','text2','year','str_label','str_data',
            'list_best','list_crosstab','list_item'
        ));
    }

    public function getlist($text1, $text2)
    {
        $company_id = session('company_id');
        return Sale::leftJoin('items', 'sales.item_id', '=', 'items.id')
            ->leftJoin('detail_categories', 'items.detail_category_id', '=', 'detail_categories.id')
            ->leftJoin('sub_categories', 'detail_categories.sub_id', '=', 'sub_categories.id')
            ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                DB::raw('COUNT(sales.numo) as cnumo')
            )
            ->whereBetween('sales.writeday', [$text1, $text2])
            ->when($company_id != 1, fn($query) => $query->where('company_id', $company_id))
            ->where('sales.io', 1)
            ->groupBy('categories.name')
            ->orderByDesc('cnumo')
            ->limit(14)
            ->paginate(14)
            ->appends(['text1' => $text1, 'text2' => $text2]);
    }

    public function getlist_item()
    {
        $company_id = session('company_id');
        return Item::orderby('name')
            ->when($company_id != 1, fn($query) => $query->where('company_id', $company_id))
            ->get();
    }
}