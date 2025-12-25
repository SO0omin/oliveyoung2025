<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Point;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_id = session('id');

        // Eloquent 관계 사용: Cart -> Item -> ActiveDiscount
        $data['carts'] = Cart::with(['item.activeDiscount'])
            ->where('customer_id', $customer_id)
            ->orderBy('id', 'asc')
            ->get();
            
        $data['balance'] = Point::balance($customer_id);
        return view('cart.index', $data);
    }

    public function updateQty(Request $request)
    {
        $cart = Cart::where('customer_id', session('id'))
                    ->where('id', $request->cart_id)
                    ->firstOrFail();

        if($request->type === 'plus') {
            $cart->qty += 1;
        } else {
            $cart->qty = max($cart->qty - 1, 1);
        }

        $cart->save();

        return response()->json([
            'success' => true,
            'qty' => $cart->qty,
            'item_price' => $cart->item->price,
            'sale_price' => $cart->item->activeDiscount ? $cart->item->activeDiscount->sale_price : null
        ]);
    }
    
    public function addToCart(Request $request)
    {
        $customer_id = session('id');

        if (!$customer_id) {
            return response()->json([
                'success' => false,
                'message' => '로그인이 필요합니다.'
            ], 401);
        }

        $item_id = $request->input('item_id');
        $qty = $request->input('qty', 1); // 기본 수량 1

        // 이미 장바구니에 있으면 수량만 증가
        $cart = Cart::where('customer_id', $customer_id)
                    ->where('item_id', $item_id)
                    ->first();

        if ($cart) {
            $cart->qty += $qty;
            $cart->save();
        } else {
            $cart = new Cart;
            $cart->customer_id = $customer_id;
            $cart->item_id = $item_id;
            $cart->qty = $qty;
            $cart->save();
        }

        return response()->json([
            'success' => true,
            'message' => '장바구니에 추가되었습니다.',
            'cart_id' => $cart->id,
            'qty' => $cart->qty
        ]);
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
        Cart::find($id)->delete();
        return redirect()->route('cart.index'); // route 이름 기준
    }
}
