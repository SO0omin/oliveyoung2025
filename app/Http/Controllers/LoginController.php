<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class LoginController extends Controller
{
    // 로그인 폼
    public function showLoginForm(){
        return view('login.index'); // index.blade.php
    }

    // 로그인 처리
    public function login(Request $request)
    {
        $request->validate([
            'uid' => 'required',
            'pwd' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'g-recaptcha-response.required' => '캡챠 인증을 해주세요.',
            'captcha' => '캡챠 인증에 실패했습니다.',
        ]);

        $uid = $request->input('uid');
        $pwd = $request->input('pwd');

        $row = Customer::where('uid', $uid)->first();

        if ($row && Hash::check($pwd, $row->pwd)) {
            session([
                'id' => $row->id,
                'uid' => $row->uid,
                'name' => $row->name,
                'grade' => $row->grade
            ]);
            return redirect()->route('main.index');
        } else {
            return back()->with('error', '로그인 실패');
        }
    }

    // 회원가입 폼
    public function showRegisterForm(){
        return view('login.register');
    }

    public function register(Request $request){
        // 유효성 검사
        $validator = \Validator::make($request->all(), [
            'uid' => 'required|max:20|unique:customers,uid',
            'pwd' => 'required|max:20|confirmed', // pwd_confirmation 필요
            'name' => 'required|max:20',
            'tel1'=> 'required|digits:3',
            'tel2'=> 'required|digits:4',
            'tel3'=> 'required|digits:4',
        ], [
            'uid.required' => '아이디는 필수입력입니다.',
            'pwd.required' => '비밀번호는 필수입력입니다.',
            'name.required' => '이름은 필수입력입니다.',
            'tel1.required' => '전화번호는 필수입력입니다.',
            'tel2.required' => '전화번호는 필수입력입니다.',
            'tel3.required' => '전화번호는 필수입력입니다.',
            'uid.max' => '20자 이내입니다.',
            'pwd.max' => '20자 이내입니다.',
            'name.max' => '20자 이내입니다.'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            $tel = $request->input('tel1') . $request->input('tel2') . $request->input('tel3'); // 하이픈 없이 11자리

            // 2. DB에 사용자 저장
            $customer = new Customer();
            $customer->uid = $request->input('uid');
            $customer->pwd = Hash::make($request->input('pwd'));
            $customer->name = $request->input('name');
            $customer->tel = $tel;
            $customer->grade = 'newbie';
            $customer->save();

            // 🚀 [핵심 추가] 가입 성공 즉시 세션 생성 (자동 로그인)
            session([
                'id' => $customer->id,
                'uid' => $customer->uid,
                'name' => $customer->name,
                'grade' => $customer->grade,
            ]);

            // JSON 응답 시 redirect 경로를 메인으로 전달
            return response()->json([
                'success' => true,
                'redirect_url' => route('main.index') 
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }   
    public function checkUid(Request $request)
    {
        $uid = $request->query('uid');
        $exists = Customer::where('uid', $uid)->exists();

        return response()->json(['exists' => $exists]);
    }

    // 로그아웃
    public function logout(){
        session()->forget(['id', 'uid', 'name', 'grade']);
        return redirect()->route('main.index');
    }
}