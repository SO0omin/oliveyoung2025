<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;

class MyController extends Controller
{
    public function index(Request $request)
    {
        // 세션에 'id'가 없으면 로그인 페이지로
        if (!$request->session()->has('id')) {
            return redirect()->route('login');
        }

        return view('my.orders.index');
    }
    public function edit()
    {
        $customerId = session('id');
        $customer = Customer::findOrFail($customerId);
        return view('my.profile.edit', compact('customer'));
    }

    public function update(Request $request)
    {
        $customerId = session('id');
        $customer = Customer::findOrFail($customerId);

        $request->validate([
            'name' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'password' => 'nullable|min:4|confirmed', // 비밀번호 확인(password_confirmation) 필드 필요
        ]);

        $customer->name = $request->name;
        $customer->tel = $request->tel;

        // 비밀번호를 입력한 경우에만 해시화하여 변경
        if ($request->filled('password')) {
            $customer->pwd = \Hash::make($request->password);
        }

        $customer->save();

        // 세션 정보 갱신 (이름 변경 시 상단바 반영용)
        session(['name' => $customer->name]);

        return back()->with('success', '개인정보가 수정되었습니다.');
    }
    public function withdraw()
    {
        $customerId = session('id'); // 현재 세션의 ID 가져오기
        
        if (!$customerId) {
            return redirect()->route('login')->with('error', '로그인이 필요합니다.');
        }

        // 1. DB에서 고객 정보 삭제
        $customer = Customer::findOrFail($customerId);
        $customer->delete();

        // 2. 세션 정보 전체 삭제 (로그아웃 처리)
        Session::flush();

        // 3. 메인 페이지로 이동하며 메시지 전달
        return redirect()->route('main')->with('success', '회원 탈퇴가 완료되었습니다. 그동안 이용해주셔서 감사합니다.');
    }
}