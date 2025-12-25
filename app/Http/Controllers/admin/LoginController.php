<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Company;

class LoginController extends Controller
{
    public function showLoginForm(){
        return view('admin.login.index'); // index.blade.php
    }

    public function login(){
        $uid = request('uid');
        $pwd = request('pwd');

        $row = Admin::where('uid', $uid)->first();

       if ($row && Hash::check($pwd, $row->pwd)) {
            $company_name = null;
            if ($row->company_id) {
                $company = Company::find($row->company_id);
                $company_name = $company ? $company->name : null;
            }
            // 로그인 성공
            session([
                'admin_uid' => $row->uid,
                'admin_name' => $row->name,
                'type' => $row->type, // 등급
                'company_id' => $row->company_id,
                'company_name' => $company_name
            ]);
            return redirect()->route('admin.main');
        } else {
            // 로그인 실패
            return back()->with('error', '로그인 실패');
        }
    }
    
    public function showRegisterForm(){
        return view('admin.login.register');
    }

    public function RegisterForm(){

        $row = new Admin; //새 행을 추가하는 것(물론 세이브를 해야 적용됨)
        $this->save_row($request,$row);

        return redirect('admin.admins');

    }

    public function save_row(Request $request, $row){
        $request->validate([
            'uid' => 'required|max:20',
            'pwd' => 'required|max:20',
            'name' => 'required|max:20'
        ],
        [
            'uid.required' => '아이디는 필수입력입니다.',
            'pwd.required' => '비밀번호는 필수입력입니다.',
            'name.required' => '이름은 필수입력입니다.',
            'uid.max' => '20자 이내입니다.',
            'pwd.max' => '20자 이내입니다.',
            'name.max' => '20자 이내입니다.'
        ]);

        $tel1 = $request->input('tel1'); //$request->tel1과 기능적으로 동일
        $tel2 = $request->input('tel2');
        $tel3 = $request->input('tel3');
        $tel = sprintf("%-3s%-4s%-4s",$tel1,$tel2,$tel3);

        $row->uid = $request->input('uid');
        $row->pwd = $request->input('pwd');
        $row->name = $request->input('name');
        $row->tel = $tel;
        $row->rank = $request->input('rank');

        $row->save();
    }

    public function logout(){
        session()->forget('admin_uid');
        session()->forget('type');
        session()->forget('company_id');
        session()->forget('company_name');

        return redirect()->route('admin.login');
    }
}
