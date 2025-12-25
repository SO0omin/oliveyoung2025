<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Item;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\DetailCategory;
use App\Models\Carousel;


class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['carousels'] = Carousel::with('event.company')
            ->where('link_url','/') // 메인용 캐로셀
            ->get();
        $data['categories'] = $this->categories_list();
        $dat['categoryGnb'] = $this->show_gnb_menu();
        $data['topItems'] = $this->topItems_list();  // 판매량이 좋은 상품
        return view('main.index', $data);
    }

    public function categories_list()
    {
        $result = Category::all();
        return $result;
    }
    
    public function show_gnb_menu()
    {
        $result = Category::rightjoin('sub_categories', 'categories.id', '=', 'sub_categories.category_id')
            ->select(
                'categories.id as category_id',
                'categories.name as category_name',
                'sub_categories.id as sub_id',
                'sub_categories.name as sub_name'
            )
            ->orderBy('categories.id', 'asc')
            ->get();
        return $result;
    }
    public function topItems_list()
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $result = Sale::where('io', 1)
            ->where('writeday', '>=', $threeMonthsAgo)
            ->with(['item.company', 'item.activeDiscount']) // Item과 Company, Discount 로딩
            ->get()
            ->groupBy('item_id')
            ->map(function($sales){
                $item = $sales->first()->item;
                $item->total_qty = $sales->sum('numo'); // 판매 수량 합계
                return $item;
            })
            ->sortByDesc('total_qty')
            ->take(10);

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
