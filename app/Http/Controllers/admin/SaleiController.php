<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Gubun;
use App\Models\Sale;

class SaleiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['tmp'] = $this->qstring();

        $text1 = request('text1');
        if(!$text1) $text1 = date("Y-m-d"); //텍스트가 null이면 오늘 날짜

        $data['text1'] = $text1;
        $data['list'] = $this->getlist($text1); //배열 선언 + 값할당
        return view('admin.salei.index', $data);
    }

    public function getlist($text1)
    {
        $company_id = session('company_id');
        $result = Sale::leftjoin('items','sales.item_id','=','items.id')->
                select('sales.*','items.name as item_name')->
                where('sales.io','=',0)->
                when($company_id != 1, function($query) use ($company_id) { $query->where('company_id', $company_id);})->
                where('sales.writeday','=',$text1)->
                orderby('sales.id','desc')->
                paginate(5)->appends(['text1'=>$text1]);
        return $result;
        // $sql = 'SELECT * FROM members ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }
    
    public function create()
    {
        $data['list'] = $this->getlist_item();

        $data['tmp'] = $this->qstring();
        return view('admin.salei.create', $data);
    }

    public function getlist_item(){
        $result = Item::orderby('name')->get();
        return $result;
    }

    public function store(Request $request)
    {
        
        $row = new Sale; //새 행을 추가하는 것(물론 세이브를 해야 적용됨)
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        //$text1 = request('text1') ? request('text1') : "";
        //$page = request('page') ? request('page') : "1";

        //return redirect()->route('admin.salei.index', ['text1' => $text1, 'page' => $page]);
        return redirect(url('admin/salei' . $tmp));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Sale::leftjoin('items','sales.item_id','=','items.id')->
                select('sales.*','items.name as item_name')->
                where('sales.id','=' , $id )->first();
        //first()를 작성하지 않으면 Eloquent 모델 객체(살행된 결과)가 아니라 Query Builder 객체(쿼리 준비 상태)가 반환
        return view('admin.salei.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['list'] = $this->getlist_item();
        $data['tmp'] = $this->qstring();

        $data['row'] = Sale::find($id); 
        // ::find(id넣기) : id기준으로 딱 1개만 찾아옴
        // ->first(); : 쿼리빌더로 조건을 만든 다음에 맨 앞의 1개만 가져옴(+ 다 가져오는 것은 get();)
        return view('admin.salei.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $row = Sale::find($id); //자료 찾기
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        return redirect(url('admin/salei' . $tmp));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Sale::find($id)->delete();

        $tmp = $this->qstring();
        return redirect()->route('admin.salei.index') . $tmp;
    }

    public function save_row(Request $request, $row){
        $request->validate([
            'writeday' => 'required|date',
            'item_id' => 'required'
        ],
        [
            'writeday.required' => '날짜는 필수입력입니다.',
            'item_id.required' => '제품명은 필수입력입니다.',
            'writeday.date' => '날짜형식이 잘못되었습니다.'
        ]);
        
        $row->io = 0;
        $row->writeday = $request->input('writeday');
        $row->item_id = $request->input('item_id');
        $row->price = $request->input('price');
        $row->numi = $request->input('numi');
        $row->numo = 0;
        $row->prices = $request->input('prices');
        $row->bigo = $request->input('bigo');

        $row->save();
    }
    
    public function qstring(){
        $text1 = request('text1') ? request('text1') : "";
        $page = request('page') ? request('page') : "1";

        $tmp = $text1 ? "?text1=$text1&page=$page" : "?page=$page";

        return $tmp;
    }
}
