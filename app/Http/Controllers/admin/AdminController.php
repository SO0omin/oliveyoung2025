<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        $data['tmp'] = $this->qstring();

        $text1 = request('text1');
        $data['text1'] = $text1;
        $data['list'] = $this->getlist($text1); //배열 선언 + 값할당
        return view('admin.admins.index', $data);
    }

    public function getlist($text1)
    {
        $company_id = session('company_id');
        $result = Admin::where('name','like','%' . $text1 . '%')->
                when($company_id != 1, function($query) use ($company_id) { $query->where('company_id', $company_id);})
                ->orderby('name','asc')->paginate(5)->appends(['text1' => $text1]);
        return $result;
        // $sql = 'SELECT * FROM admins ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }
    
    public function create()
    {
        $data['next_id'] = DB::select("SHOW TABLE STATUS LIKE 'admins'")[0]->Auto_increment;
        $data['tmp'] = $this->qstring();
        return view('admin.admins.create', $data);
    }

    public function checkId(Request $request){
        $uid = $request->query('uid');
        $exists = Admin::where('uid',$uid)->exists();

        return response()->json(['exists'=>$exists]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $row = new Admin; //새 행을 추가하는 것(물론 세이브를 해야 적용됨)
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        return redirect(url('admin/admins' . $tmp));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Admin::find($id);
        return view('admin.admins.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Admin::find($id);
        return view('admin.admins.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $row = Admin::find($id); //자료 찾기
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        return redirect(url('admin/admins' . $tmp));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Admin::find($id)->delete();

        $tmp = $this->qstring();
        return redirect(url('admin/admins' . $tmp));
    }

    public function save_row(Request $request, $row){
        $request->validate([
            'uid' => 'required|max:20',
            'name' => 'required|max:20'
        ],
        [
            'uid.required' => '아이디는 필수입력입니다.',
            'name.required' => '이름은 필수입력입니다.',
            'uid.max' => '20자 이내입니다.',
            'name.max' => '20자 이내입니다.'
        ]);

        $tel1 = $request->input('tel1'); //$request->tel1과 기능적으로 동일
        $tel2 = $request->input('tel2');
        $tel3 = $request->input('tel3');
        $tel = sprintf("%-3s%-4s%-4s",$tel1,$tel2,$tel3);

        $row->uid = $request->input('uid');
        if ($request->filled('pwd')) {
            $row->pwd = Hash::make($request->input('pwd'));
        }
        $row->name = $request->input('name');
        $row->tel = $tel;
        $row->type = $request->input('type');
        $row->company_id = session('company_id');

        $row->save();
    }
    
    public function qstring(){
        $text1 = request('text1') ? request('text1') : "";
        $page = request('page') ? request('page') : "1";

        $tmp = $text1 ? "?text1=$text1&page=$page" : "?page=$page";

        return $tmp;
    }
}
