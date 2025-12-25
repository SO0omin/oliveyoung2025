<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Discount;

class DiscountController extends Controller
{

    private function getAdminFilterParams()
    {
        $company_id = session('company_id');
        $admin_type = session('type');
        
        // 'super_admin'이 아니며, company_id가 세션에 있을 때 필터링이 필요함
        $needs_company_filter = ($admin_type !== 'super_admin' && $company_id);
        
        return [
            'needs_company_filter' => $needs_company_filter,
            'company_id' => $company_id
        ];
    }

    public function index()
    {
        $data['tmp'] = $this->qstring();

        // text1의 기본값을 null로 변경 (쿼리에서 사용하지 않으므로)
        $text1 = request('text1', null); 
        $text2 = request('text2', 0);     // 상태 필터: 0=전체, 1=예정, 2=진행중, 3=종료
        $text3 = request('text3', 0);     // 제품 필터, 0 = 전체

        $data['text1'] = $text1;
        $data['text2'] = $text2; 
        $data['text3'] = $text3;
        $data['list_item'] = $this->getlist_item();
        $data['list'] = $this->getlist($text2, $text3); // text1은 전달되지만 쿼리에서 무시됨

        return view('admin.discount.index', $data);
    }
    
    public function getlist( $text2, $text3)
    {
        $filter = $this->getAdminFilterParams();
        
        $query = Discount::leftJoin('items', 'discounts.item_id', '=', 'items.id')
            ->select('discounts.*', 'items.name as items_name');
        
        $today = now()->toDateString(); // ★★★ 상태 필터의 기준 날짜 (오늘) ★★★

        // 1. 상태 필터 (text2: 0=전체, 1=예정, 2=진행중, 3=종료) 적용
        // text2가 0이 아니면, 무조건 $today를 기준으로 상태를 판단합니다.
        if ($text2 == 1) { // 1: 예정된 할인 (시작일이 오늘보다 미래)
            $query->whereRaw("DATE(discounts.start_at) > ?", [$today]);
        } elseif ($text2 == 2) { // 2: 현재 진행중 (오늘이 시작일과 종료일 사이)
            $query->whereRaw("DATE(discounts.start_at) <= ?", [$today])
                ->where(function($q) use ($today){
                    // 오늘이 종료일 "이전"까지만 진행중
                    $q->whereRaw("DATE(discounts.end_at) > ?", [$today]);
                });
        } elseif ($text2 == 3) { // 3: 종료된 할인 (종료일이 오늘보다 과거)
            $query->whereNotNull('discounts.end_at')
                ->whereRaw("DATE(discounts.end_at) < ?", [$today]);
        }
        // text2 == 0 (전체 상태)일 경우, 상태 필터를 적용하지 않음

        // 2. 날짜 필터 (text1) 로직이 제거됨. 이제 text1은 조회에 영향을 주지 않습니다.
        // 이전 코드
        // if ($text1) {
        //     $query->where(function($q) use ($text1) {
        //         // ... (날짜 필터링 로직) ...
        //     });
        // }

        // 3. 제품명 필터 (text3) 적용
        if ($text3 != 0) {
            $query->where('discounts.item_id', $text3);
        }

        // 🔥 정렬: 진행중 → 예정 → 종료 (항상 이 순서)
        $query->orderByRaw("
            CASE
                WHEN DATE(start_at) <= '$today' AND (end_at IS NULL OR DATE(end_at) >= '$today') THEN 1
                WHEN DATE(start_at) > '$today' THEN 2
                ELSE 3
            END ASC
        ");

        // 🔥 진행중이면 종료일 가까운 순서(선택사항)
        $query->orderByRaw("
            CASE
                WHEN DATE(start_at) <= '$today' AND (end_at IS NULL OR DATE(end_at) >= '$today') THEN end_at
                ELSE NULL
            END ASC
        ");

        $query->when($filter['needs_company_filter'], function($q) use ($filter) {
            $q->where('company_id', $filter['company_id']);
        });

        // 마지막 보조 정렬
        $query->orderBy('id', 'desc');

        // 페이지네이션
        return $query = $query->paginate(5)->appends([
            'text2' => $text2,
            'text3' => $text3
        ]);
    }


    public function getlist_item()
    {
        $company_id = session('company_id');

        $query = Item::orderBy('name');

        // company_id가 1(관리자)이 아니면 회사별로 필터
        if ($company_id != 1) {
            $query->where('company_id', $company_id);
        }

        return $query->get();
    }

    public function create()
    {
        $data['list'] = Item::orderBy('name')->get();
        $data['tmp'] = $this->qstring();

        return view('admin.discount.create', $data);
    }

    public function store(Request $request)
    {
        $row = new Discount;
        $this->save_row($request, $row);

        return redirect('admin/discount' . $this->qstring());
    }

    public function show(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Discount::leftJoin('items', 'discounts.item_id', '=', 'items.id')
            ->select('discounts.*', 'items.name as item_name')
            ->where('discounts.id', $id)
            ->first();

        return view('admin.discount.show', $data);
    }

    public function edit(string $id)
    {
        $data['list'] = Item::orderBy('name')->get();
        $data['tmp'] = $this->qstring();

        $data['row'] = Discount::leftJoin('items', 'discounts.item_id', '=', 'items.id')
            ->select('discounts.*', 'items.name as item_name','items.price as price')
            ->where('discounts.id', $id)
            ->first();

        return view('admin.discount.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $row = Discount::find($id);
        $this->save_row($request, $row);

        return redirect('admin/discount' . $this->qstring());
    }

    public function destroy(string $id)
    {
        Discount::find($id)->delete();

        return redirect('admin/discount' . $this->qstring());
    }

    public function save_row(Request $request, $row)
    {
        $request->validate([
            'item_id' => 'required'
        ], [
            'item_id.required' => '제품명은 필수입력입니다.',
            'writeday.date' => '날짜형식이 잘못되었습니다.'
        ]);

        $row->item_id = $request->input('item_id');
        $row->sale_price = $request->input('sale_price');
        $row->discount_percent = $request->input('discount_percent');
        $row->start_at = $request->input('start_at');
        $row->end_at = $request->input('end_at');
        $row->is_active = $request->input('is_active', 1);

        $row->save();
    }

    public function qstring()
    {
        $text1 = request('text1') ? request('text1') : "";
        $page  = request('page') ? request('page') : "1";

        return $text1 ? "?text1=$text1&page=$page" : "?page=$page";
    }
}