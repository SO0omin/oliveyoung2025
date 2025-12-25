<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Discount;
use App\Models\Category;
use App\Models\Carousel;
use App\Models\SubCategory;
use App\Models\DetailCategory;

class GoodsController extends Controller
{
    public function categoryIndex($category_id)
    {
        // 1. 카테고리 정보 조회
        $category = Category::findOrFail($category_id);

        // 2. 해당 카테고리의 모든 중분류, 소분류 ID 조회 (기존 로직 유지)
        $subs = SubCategory::where('category_id', $category_id)->get();
        $subIds = $subs->pluck('id');
        $details = DetailCategory::whereIn('sub_id', $subIds)->get();

        // 3. 해당 소분류 상품 20개 조회 (기존 로직 유지)
        $items = Item::whereIn('detail_category_id', $details->pluck('id'))
                    ->limit(20)
                    ->get();
        
        // 4. 🔥 캐러셀 데이터 조회 및 필터링 🔥
        $targetLink = 'categories/' . $category_id;
        
        // 💡 [수정] link_url이 'categories/{category_id}' 이거나 'categories/{category_id}/' 인 것을 찾습니다.
        $carousels = Carousel::where(function($query) use ($targetLink) {
                                $query->where('link_url', $targetLink);       // categories/1 (정확히 일치)
                            })
                            ->orderBy('id', 'asc') // 표시 순서에 따라 정렬
                            ->get();

        // 5. 뷰로 데이터 전달
        return view('goods.category_list', compact('category', 'subs', 'items', 'carousels'));
    }

    // 2. 중분류 + 소분류 + 상품 목록
    public function subCategoryView(Request $request,$category_id, $sub_id, $detail_id = null)
    {
        $allCategories = Category::all();
        $category = Category::findOrFail($category_id);
        $sub = SubCategory::where('id', $sub_id)
                        ->where('category_id', $category_id)
                        ->firstOrFail();
        $details = DetailCategory::where('sub_id', $sub_id)->get();

        $sort = $request->query('sort', 'sales');

        if ($sort == 'sales') {
            $threeMonthsAgo = Carbon::now()->subMonths(3);
            $detailIds = $detail_id ? [$detail_id] : $details->pluck('id')->toArray();

            // 1) 먼저 판매량 기준 정렬된 item_id 리스트 뽑기
            $salesItems = Sale::from('sales as s')
                ->where('s.io', 1)
                ->where('s.writeday', '>=', $threeMonthsAgo)
                ->leftJoin('items as i', 's.item_id', '=', 'i.id')
                ->whereIn('i.detail_category_id', $detailIds)
                ->select(
                    'i.id',
                    DB::raw('SUM(s.numo) as total_qty')
                )
                ->groupBy('i.id')
                ->orderByDesc('total_qty')
                ->get();

            // 판매된 상품 ID 리스트
            $itemIds = $salesItems->pluck('id')->toArray();

            // 2) 실제 Item 모델을 company와 함께 로드
            $items = Item::with(['company','activeDiscount'])
                ->whereIn('id', $itemIds)
                ->get()
                // 3) 판매량 순서대로 다시 정렬
                ->sortByDesc(function ($item) use ($salesItems) {
                    return $salesItems->firstWhere('id', $item->id)->total_qty ?? 0;
                })
                ->values();   // index 재정렬

            if ($items->isEmpty()) {
                $items = Item::whereIn('detail_category_id', $detailIds)
                            ->with('company')
                            ->orderBy('created_at', 'desc')
                            ->get();
            }
        } else {
            $itemsQuery = $detail_id ? Item::where('detail_category_id', $detail_id)
                                    : Item::whereIn('detail_category_id', $details->pluck('id'));

            switch ($sort) {
                case 'new':
                    $itemsQuery = $itemsQuery->orderBy('created_at', 'desc');
                    break;
                case 'low_price':
                    $itemsQuery = $itemsQuery->orderBy('price', 'asc');
                    break;
                case 'high_price':
                    $itemsQuery = $itemsQuery->orderBy('price', 'desc');
                    break;
            }
            $items = $itemsQuery->get();
        }
        $detail = null;
        if ($detail_id) {
            $detail = DetailCategory::find($detail_id);
        }

        // 4. 🔥 캐러셀 데이터 조회 및 필터링 🔥
        // 💡 [수정] 캐러셀 link_url은 'categories/{category_id}/' 형식으로 저장되어 있으므로, 
        // 중분류 ID에 관계없이 해당 대분류의 캐러셀을 가져옵니다.
        $targetLink = 'categories/' . $category_id . '/';
        
        // 'link_url'이 'categories/1/'와 정확히 일치하는 캐러셀을 가져옵니다.
        $carousels = Carousel::where('link_url', $targetLink)
                            // 필요한 경우, 'event_id'가 NULL이거나 해당 이벤트가 활성 상태인지 확인할 수 있습니다.
                            ->orderBy('id', 'asc') // 표시 순서에 따라 정렬
                            ->get();

        return view('goods.category_view', compact('allCategories','category', 'sub', 'details', 'items', 'sort', 'detail','carousels'));
    }

    public function detail($id)
    {
        // 1. 현재 상품 정보 가져오기
    $item = Item::with([
        'detailCategory.subCategory.category', 
        'activeDiscount' 
    ])->findOrFail($id);

    // 2. 해당 카테고리의 신상품(최근 등록 순) 5개 가져오기
    $relatedItems = Item::where('detail_category_id', $item->detail_category_id)
        ->where('id', '<>', $id) // 현재 보고 있는 상품은 제외
        ->orderByDesc('created_at') // 💡 최신 등록일 순 정렬 (또는 id 사용 가능)
        ->limit(5)
        ->get();

    return view('goods.detail', compact('item', 'relatedItems'));

        return view('goods.detail', compact('item', 'relatedItems'));
}

    public function checkout(Request $request)
    {
        $customer_id = session('id');
        $cartIds = $request->cart_ids; // 체크된 장바구니 id 배열

        $carts = Cart::with('item.activeDiscount')->whereIn('id', $cartIds)->get();

        $total = 0;
        foreach($carts as $cart) {
            $total += $cart->item->activeDiscount ? $cart->item->activeDiscount->sale_price * $cart->qty
                                                : $cart->item->price * $cart->qty;
        }

        $order = Order::create([
            'customer_id' => $customer_id,
            'total_price' => $total,
            'status' => 'pending'
        ]);

        foreach($carts as $cart) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $cart->item->id,
                'qty' => $cart->qty,
                'price' => $cart->item->price,
                'sale_price' => $cart->item->activeDiscount ? $cart->item->activeDiscount->sale_price : null
            ]);
        }

        // 주문 후 장바구니 삭제
        Cart::whereIn('id', $cartIds)->delete();

        return redirect()->route('orders.show', $order->id);
    }

    public function rank($category_id = null)
    {
        // 모든 카테고리(상단 버튼용)
        $categories = Category::all();

        // sales와 items, detail_categories, sub_categories, categories를 조인해서 item별 판매 합계 구함
        $query = Sale::from('sales as s')
            ->where('s.io', 1)
            ->join('items as i', 's.item_id', '=', 'i.id')
            ->join('detail_categories as d', 'i.detail_category_id', '=', 'd.id')
            ->join('sub_categories as sc', 'd.sub_id', '=', 'sc.id')
            ->join('categories as c', 'sc.category_id', '=', 'c.id');

        if ($category_id) {
            $query->where('c.id', $category_id);
        }

        // item id와 총판매량을 뽑아서 정렬
        $result = $query->select('i.id as item_id', DB::raw('SUM(s.numo) as total_sold'))
                        ->groupBy('i.id')
                        ->orderByDesc('total_sold')
                        ->get();

        // 판매기록이 하나도 없으면(혹은 결과가 비어있으면) fallback으로 최근 등록 상품을 사용
        if ($result->isEmpty()) {

            $itemQuery = Item::with(['company', 'activeDiscount'])
                ->join('detail_categories as d', 'items.detail_category_id', '=', 'd.id')
                ->join('sub_categories as sc', 'd.sub_id', '=', 'sc.id')
                ->join('categories as c', 'sc.category_id', '=', 'c.id');

            // 🔥 카테고리 선택돼 있으면 해당 카테고리만
            if ($category_id) {
                $itemQuery->where('c.id', $category_id);
            }

            // 최근 등록 상품 50개
            $items = $itemQuery
                ->orderBy('items.created_at', 'desc')
                ->select('items.*')
                ->limit(50)
                ->get();

            $ranking = collect(); // 판매량 없음
        } else {
            // item id 순서대로 배열
            $itemIds = $result->pluck('item_id')->toArray();

            // Item들을 미리 로드 (company, activeDiscount 등 필요한 관계도 로드)
            $itemsById = Item::with(['company', 'activeDiscount'])
                        ->whereIn('id', $itemIds)
                        ->get()
                        ->keyBy('id');

            // itemIds 순서를 유지하는 컬렉션으로 재조합
            $items = collect($itemIds)
                        ->map(function($id) use ($itemsById) {
                            return $itemsById->get($id);
                        })
                        ->filter(); // 없는 아이템(삭제된 등)은 제거

            // ranking: item_id => total_sold (필요하면 뷰에서 사용)
            $ranking = $result->mapWithKeys(function($row) {
                return [$row->item_id => (int)$row->total_sold];
            });
        }

        // 뷰에서 $category->name 을 쓰고 있으므로 category 객체 셋팅 (없으면 '전체')
        $category = $category_id ? Category::find($category_id) : (object) ['name' => '전체'];

        // items는 이미 정렬된 Item 컬렉션 (->take(5) 등으로 blade에서 잘라서 사용)
        return view('goods.rank', compact('categories', 'items', 'ranking', 'category', 'category_id'));
    }

    public function discount(Request $request, $category_id = null)
    {
        // 카테고리 목록
        $categories = Category::all();

        // 정렬 파라미터 (기본값: sales)
        $sort = $request->query('sort', 'sales');

        // -------------------------
        // ① 기준 상품: 세일 상품만
        // -------------------------
        $discountItemsQuery = Item::with(['company', 'activeDiscount'])
            ->whereHas('activeDiscount')
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('detail_category_id', $category_id);   // ✅ FIX
            });

        // -------------------------
        // ② 정렬
        // -------------------------
        if ($sort === 'sales') {

            $threeMonthsAgo = Carbon::now()->subMonths(3);

            // 판매량 있는 상품 ID + 판매량 합계 가져오기
            $salesItems = Sale::from('sales as s')
                ->where('s.io', 1)
                ->where('s.writeday', '>=', $threeMonthsAgo)
                ->leftJoin('items as i', 's.item_id', '=', 'i.id')
                ->leftJoin('discounts as d', function ($join) {
                    $join->on('i.id', '=', 'd.item_id')
                        ->where('d.is_active', 1)
                        ->where(function ($q) {
                            $q->whereNull('d.end_at')
                            ->orWhere('d.end_at', '>=', now());
                        });
                })
                ->whereNotNull('d.id')   // 할인 상품만!
                ->when($category_id, fn($q) => $q->where('i.detail_category_id', $category_id))
                ->select(
                    'i.id',
                    DB::raw("SUM(s.numo) as total_qty")
                )
                ->groupBy('i.id')
                ->orderByDesc('total_qty')
                ->get();

            $itemIds = $salesItems->pluck('id')->toArray();

            // 1) 판매된 상품 먼저 정렬
            $items = Item::with(['company', 'activeDiscount'])
                ->whereIn('id', $itemIds)
                ->get()
                ->sortByDesc(function ($item) use ($salesItems) {
                    return $salesItems->firstWhere('id', $item->id)->total_qty ?? 0;
                })
                ->values();

            // 2) 판매 기록 없는 세일 상품도 추가(정렬은 아래쪽)
            $otherItems = $discountItemsQuery
                ->whereNotIn('id', $itemIds)
                ->orderBy('created_at', 'desc')
                ->get();

            // merge
            $items = $items->merge($otherItems);

        }else {

            switch ($sort) {
                case 'new':
                    $discountItemsQuery->orderBy('items.created_at', 'desc');
                    break;

                case 'low_price':
                    $discountItemsQuery->orderBy('d.sale_price', 'asc');
                    break;

                case 'high_price':
                    $discountItemsQuery->orderBy('d.sale_price', 'desc');
                    break;
            }

            // items.* + sale_price 같이 가져오기
            $items = $discountItemsQuery->select('items.*', 'd.sale_price')->get();
        }

        return view('goods.discount', [
            'categories' => $categories,
            'items' => $items,
            'sort' => $sort,
            'category' => $category_id ? Category::find($category_id) : null,
        ]);
    }
    public function search(Request $request)
    {
        $q = $request->query('q');

        // 검색어 없으면 빈 결과로 출력
        if (!$q) {
            return view('goods.search', [
                'items' => collect(),
                'q' => $q
            ]);
        }

        $items = Item::with('company')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")           // 제품명
                    ->orWhereHas('company', function ($q2) use ($q) {
                        $q2->where('name', 'like', "%{$q}%");    // 브랜드명
                    });
            })
            ->get();

        return view('goods.search', [
            'items' => $items,
            'q' => $q
        ]);
    }

}