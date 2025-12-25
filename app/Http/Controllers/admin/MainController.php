<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Category;
use App\Models\Sale;

class MainController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------
        // 0. 등급(type) 및 Company ID 처리
        // -------------------------
        $company_id = session('company_id');
        $admin_type = session('type');
        
        // 'super_admin'이 아닐 때만 필터링 플래그 설정
        $needs_company_filter = ($admin_type !== 'super_admin'); 

        $text1 = request('text1', date("Y-m-d", strtotime("-1 month")));
        $text2 = request('text2', date("Y-m-d"));
        $year = $request->input('year', date('Y'));


        // -------------------------
        // 2, 3, 4. 쿼리 정의 (Chart, BEST, Crosstab)
        // 🔥 모든 쿼리에 $needs_company_filter 기반의 whereHas 필터가 적용되어 있음 🔥

        $chartData = Sale::with('item')
            ->select('item_id', DB::raw('SUM(numo) as total'))
            ->whereBetween('writeday', [$text1, $text2])
            ->when($needs_company_filter, fn($query) => 
                $query->whereHas('item', fn($q) => $q->where('company_id', $company_id))
            )
            ->groupBy('item_id')
            ->get();
        $str_label = $chartData->map(fn($sale) => "'".$sale->item->name."'")->implode(',');
        $str_data  = $chartData->pluck('total')->implode(',');

        $list_best = Sale::with('item')
            ->select('item_id', DB::raw('SUM(numo) as cnumo'))
            ->whereBetween('writeday', [$text1, $text2])
            ->when($needs_company_filter, fn($query) => 
                $query->whereHas('item', fn($q) => $q->where('company_id', $company_id))
            )
            ->groupBy('item_id')
            ->orderByDesc('cnumo')
            ->limit(10)
            ->get();
            
        $list_crosstab = Sale::with('item')
            ->select('item_id',
                DB::raw("SUM(CASE WHEN MONTH(writeday)=1 THEN numo ELSE 0 END) as s1"),
                // ... (2월~11월 SUM(CASE) 생략) ...
                DB::raw("SUM(CASE WHEN MONTH(writeday)=12 THEN numo ELSE 0 END) as s12")
            )
            ->whereYear('writeday', $year)
            ->when($needs_company_filter, fn($query) => 
                $query->whereHas('item', fn($q) => $q->where('company_id', $company_id))
            )
            ->groupBy('item_id')
            ->get();

        // -------------------------
        // 5. 제품 리스트 (필터용)
        // -------------------------
        $list_item = $this->getlist_item($admin_type, $company_id);

        // ===== 6. KPI 요약 데이터 계산 (TOTAL AMOUNT & COUNT) =====
        
        $baseQuery = Sale::whereBetween('writeday', [$text1, $text2])
            ->join('items', 'sales.item_id', '=', 'items.id'); 
            
            // JOIN 후 company_id 필터 적용 (super_admin 제외)
            if ($needs_company_filter) {
                $baseQuery->where('items.company_id', $company_id);
            }

        // 6-A. 총 매출 금액 계산 (SUM(numo * price))
        $total_sales_amount_result = (clone $baseQuery)
            ->select(DB::raw('SUM(sales.numo * items.price) as total_amount')) 
            ->first();

        // 6-B. 총 거래 건수 계산 (COUNT(id))
        $total_sales_count_result = (clone $baseQuery)
            ->select(DB::raw('COUNT(sales.id) as ccount')) 
            ->first();

        // 변수 정의 완료
        $total_sales_amount = $total_sales_amount_result ? $total_sales_amount_result->total_amount : 0;
        $total_sales_count = $total_sales_count_result ? $total_sales_count_result->ccount : 0;

        // -------------------------
        // 7. View 반환
        // -------------------------
        return view('admin.main.index', compact(
            'text1','text2','year','str_label','str_data',
            'list_best','list_crosstab','list_item',
            'total_sales_amount', 'total_sales_count'
        ));
    }

    // 헬퍼 함수: Item 리스트 조회
    public function getlist_item($admin_type, $company_id)
    {
        $needs_company_filter = ($admin_type !== 'super_admin'); 

        return Item::orderby('name')
            ->when($needs_company_filter, fn($query) => $query->where('company_id', $company_id))
            ->get();
    }
    
    // 헬퍼 함수: 카테고리별 매출 현황
    public function getlist($text1, $text2)
    {
        $company_id = session('company_id');
        $admin_type = session('type');
        $needs_company_filter = ($admin_type !== 'super_admin');

        return Sale::leftJoin('items', 'sales.item_id', '=', 'items.id')
            ->leftJoin('detail_categories', 'items.detail_category_id', '=', 'detail_categories.id')
            ->leftJoin('sub_categories', 'detail_categories.sub_id', '=', 'sub_categories.id')
            ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                DB::raw('COUNT(sales.numo) as cnumo')
            )
            ->whereBetween('sales.writeday', [$text1, $text2])
            // 필터 적용
            ->when($needs_company_filter, fn($query) => $query->where('items.company_id', $company_id))
            ->where('sales.io', 1)
            ->groupBy('categories.name')
            ->orderByDesc('cnumo')
            ->limit(14)
            ->paginate(14)
            ->appends(['text1' => $text1, 'text2' => $text2]);
    }

    // (사용되지 않는 private function applyCompanyFilter($query, $company_id)는 제거했습니다.)
}