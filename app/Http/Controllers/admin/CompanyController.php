<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['tmp'] = $this->qstring();

        $text1 = request('text1');
        $data['text1'] = $text1;
        $data['list'] = $this->getlist($text1); //배열 선언 + 값할당
        return view('admin.company.index', $data);
    }

    public function getlist($text1)
    {
        $result = Company::where('name','like','%' . $text1 . '%')
                ->orderby('name','asc')->paginate(5)->appends(['text1' => $text1]);
        return $result;
        // $sql = 'SELECT * FROM companys ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }
    
    public function create()
    {
        $data['next_id'] = DB::select("SHOW TABLE STATUS LIKE 'companies'")[0]->Auto_increment;
        $data['tmp'] = $this->qstring();
        return view('admin.company.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $row = new Company; //새 행을 추가하는 것(물론 세이브를 해야 적용됨)
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        return redirect(url('admin/company' . $tmp));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Company::find($id);
        return view('admin.company.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Company::find($id);
        return view('admin.company.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $row = Company::find($id); //자료 찾기
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        return redirect(url('admin/company' . $tmp));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Company::find($id)->delete();

        $tmp = $this->qstring();
        return redirect(url('admin/company' . $tmp));
    }

    public function save_row(Request $request, $row){
        $request->validate([
            'name' => 'required'
        ],
        [
            'name.required' => '이름은 필수입력입니다.'
        ]);
        
        $row->name = $request->input('name');

        if($request->hasFile('logo')){
            $logo = $request->file('logo');
            $logo_name = $logo->getClientOriginalName(); //파일이름

            /*$img = Image::read($logo->getRealPath())
                ->resize(null,200,function($constraint) { $constraint->aspectRatio();})
                ->save('storage/logo/thumb/'. $logo_name); //thumb파일 필요없음*/

            $logo->storeAs('logo', $logo_name, 'public'); //파일저장

            $row->logo = $logo_name; //$row->pic은 파일명
        }

        $row->save();
    }
    
    public function qstring(){
        $text1 = request('text1') ? request('text1') : "";
        $page = request('page') ? request('page') : "1";

        $tmp = $text1 ? "?text1=$text1&page=$page" : "?page=$page";

        return $tmp;
    }
}
