<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(session()->get("rank") != 1 ) return redirect('/');
        $data['tmp'] = $this->qstring();

        $text1 = request('text1');
        $data['text1'] = $text1;
        $data['list'] = $this->getlist($text1); //배열 선언 + 값할당
        return view('customer.index', $data);
    }

    public function getlist($text1)
    {
        $result = Customer::where('name','like','%' . $text1 . '%')
                ->orderby('name','asc')->paginate(5)->appends(['text1' => $text1]);
        return $result;
        // $sql = 'SELECT * FROM customers ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }
    
    public function create()
    {
        $data['next_id'] = DB::select("SHOW TABLE STATUS LIKE 'customers'")[0]->Auto_increment;
        $data['tmp'] = $this->qstring();
        return view('customer.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $row = new Customer; //새 행을 추가하는 것(물론 세이브를 해야 적용됨)
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        return redirect('customer' . $tmp);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Customer::find($id);
        return view('customer.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Customer::find($id);
        return view('customer.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $row = Customer::find($id); //자료 찾기
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        return redirect('customer' . $tmp);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Customer::find($id)->delete();

        $tmp = $this->qstring();
        return redirect('customer' . $tmp);
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
        if ($request->filled('pwd')) {
            $row->pwd = Hash::make($request->input('pwd'));
        }
        $row->name = $request->input('name');
        $row->tel = $tel;
        $row->rank = $request->input('rank');

        $row->save();
    }
    
    public function qstring(){
        $text1 = request('text1') ? request('text1') : "";
        $page = request('page') ? request('page') : "1";

        $tmp = $text1 ? "?text1=$text1&page=$page" : "?page=$page";

        return $tmp;
    }
}
