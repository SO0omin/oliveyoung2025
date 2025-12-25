<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Point;
use App\Models\Item;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Sale;



class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cartIds = explode(',', $request->cart_ids);

        $carts = Cart::with(['item', 'item.activeDiscount'])
                    ->whereIn('id', $cartIds)
                    ->get();

        $totalPrice = 0;
        foreach ($carts as $cart) {
            $price = $cart->item->activeDiscount
                ? $cart->item->activeDiscount->sale_price
                : $cart->item->price;

            $totalPrice += $price * $cart->qty;
        }

        //기본 배송지 가져오기
        $customerId = session('id');
        $addresses = Address::where('customer_id', $customerId)->get();
        
        //포인트 잔액 계산
        $totalEarned = Point::where('customer_id', $customerId)
                            ->where('type', 'earn')
                            ->sum('amount');
        $totalUsed = Point::where('customer_id', $customerId)
                        ->where('type', 'use')
                        ->sum('amount');
        $customerPoints = $totalEarned + $totalUsed; //사용된 값이 알아서 자체로 음수로 들어감

        $isFromCart = $request->has('cart_ids') && !empty($request->input('cart_ids'));

        //customerPoints 변수를 view로 전달
        return view('goods.checkout', compact('carts', 'totalPrice', 'addresses', 'isFromCart', 'customerPoints'));
    }
    public function buyNow(Request $request)
    {
        // 바로 구매: item_id + qty 전달
        $itemId = $request->item_id;
        $qty = $request->qty ?? 1;

        $item = Item::with('activeDiscount')->findOrFail($itemId);

        // 가상 Cart 객체 생성 (Buy Now는 실제 Cart 객체를 사용하지 않음)
        $carts = collect();
        $carts->push((object)[
            // id는 임시값이며, 실제 DB의 cart_id가 아님에 유의합니다.
            'id' => $item->id, 
            'item' => $item,
            'qty' => $qty,
        ]);

        // 총 가격 계산
        $totalPrice = 0;
        foreach ($carts as $cart) {
            $price = $cart->item->activeDiscount
                ? $cart->item->activeDiscount->sale_price
                : $cart->item->price;

            $totalPrice += $price * $cart->qty;
        }

        // 기본 배송지 가져오기
        $customerId = session('id');
        $addresses = Address::where('customer_id', $customerId)->get();

        // 💡 [추가] 현재 고객의 잔여 포인트 조회 로직
        $totalEarned = Point::where('customer_id', $customerId)
                            ->where('type', 'earn')
                            ->sum('amount');
        $totalUsed = Point::where('customer_id', $customerId)
                        ->where('type', 'use')
                        ->sum('amount');
        $customerPoints = $totalEarned - $totalUsed;


        $isFromCart = null;

        // 💡 [수정] customerPoints 변수를 view로 전달
        return view('goods.checkout', compact('carts', 'totalPrice', 'addresses', 'isFromCart', 'customerPoints'));
    }


    public function pay(Request $request)
    {
        // 트랜잭션을 사용하여 데이터 안전성 확보
        return \DB::transaction(function () use ($request) {
            $customerId = session('id');
            $pointsUsed = (int) $request->input('points_used', 0);
            $finalTotalPrice = $request->input('final_total_price');

            // 1. 배송지 정보 준비
            $addressId = $request->input('address_id');
            $address = $addressId ? Address::find($addressId) : null;

            // ⭐ DB에 주소가 있으면 그걸 쓰고, 없으면 화면에서 입력한 input 값을 직접 씁니다.
            $orderData = [
                'customer_id'       => $customerId,
                'total_price'       => $request->input('final_total_price'),
                'status'            => 'paid',
                'shipping_label'    => $address ? $address->label    : $request->input('label'),
                'shipping_name'     => $address ? $address->name     : $request->input('name'),
                'shipping_phone'    => $address ? $address->phone    : $request->input('phone'),
                'shipping_zipcode'  => $address ? $address->zipcode  : $request->input('zipcode'),
                'shipping_address1' => $address ? $address->address1 : $request->input('address1'),
                'shipping_address2' => $address ? $address->address2 : $request->input('address2'),
            ];

            // 1-2. 주문 생성
            $order = Order::create($orderData);

            // 2. 처리할 아이템 리스트 추출
            $itemsToProcess = [];

            if ($request->has('cart_ids')) {
                // 장바구니 결제인 경우
                $carts = Cart::with('item.activeDiscount')->whereIn('id', $request->cart_ids)->get();
                foreach ($carts as $cart) {
                    $itemsToProcess[] = [
                        'item' => $cart->item,
                        'qty' => $cart->qty,
                        'cart_obj' => $cart // 나중에 삭제하기 위함
                    ];
                }
            } elseif ($request->has('item_id')) {
                // 바로구매 결제인 경우
                $itemId = $request->input('item_id');
                // 💡 Blade에서 qty를 hidden으로 보냈는지 확인. 안 왔으면 1개로 간주
                $qty = $request->input('qty', 1); 

                $item = Item::with('activeDiscount')->findOrFail($itemId);
                $itemsToProcess[] = [
                    'item' => $item,
                    'qty' => $qty,
                    'cart_obj' => null
                ];
            }

            // 3. OrderItem 및 Sale 데이터 생성
            foreach ($itemsToProcess as $data) {
                $item = $data['item'];
                $qty = $data['qty'];
                $price = $item->price;
                $salePrice = $item->activeDiscount->sale_price ?? null;

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'price' => $price,
                    'sale_price' => $salePrice,
                ]);

                Sale::create([
                    'io' => 1,
                    'writeday' => now(),
                    'item_id' => $item->id,
                    'price' => $salePrice ?? $price,
                    'numo' => $qty,
                    'prices' => ($salePrice ?? $price) * $qty,
                    'bigo' => '주문번호 ' . $order->id,
                ]);

                // 장바구니 데이터면 삭제
                if ($data['cart_obj']) {
                    $data['cart_obj']->delete();
                }
            }

            // 4. 포인트 처리 (기존 로직 유지)
            if ($pointsUsed > 0) {
                Point::create([
                    'customer_id' => $customerId,
                    'amount' => $pointsUsed * -1, //음수로 들어감
                    'type' => 'use',
                ]);
            } else {
                $pointEarned = floor($finalTotalPrice * 0.01); 
                if ($pointEarned > 0) {
                    Point::create(['customer_id' => $customerId, 'amount' => $pointEarned, 'type' => 'earn']);
                }
            }

            return redirect()->route('orders.index')->with('success', '주문이 완료되었습니다!');
        });
    }
}