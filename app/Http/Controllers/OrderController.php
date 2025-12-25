<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // 주문 내역
    public function index(Request $request)
    {
        // 세션에 'user_id'가 없으면 로그인 페이지로
        if (!$request->session()->has('id')) {
            return redirect()->route('login');
        }
        $customer_id = session('id'); // 로그인 사용자
        $orders = Order::with('items.item')->where('customer_id', $customer_id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        //dd($orders->first()->items->first()->item);

        return view('my.orders.index', compact('orders'));
    }

    // 주문 상세
    public function show(Request $request, $id)
    {
        // 세션에 'user_id'가 없으면 로그인 페이지로
        if (!$request->session()->has('id')) {
            return redirect()->route('login');
        }
        $order = Order::with('items.item')->findOrFail($id);
        return view('my.orders.show', compact('order'));
    }
}