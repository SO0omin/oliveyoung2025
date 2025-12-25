<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Gubun;

class FindcompanyController extends Controller
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
        return view('admin.findcompany.index', $data);
    }

    public function getlist($text1)
    {
        $result = Company::where('name','like','%' . $text1 . '%')
                ->orderby('name','asc')->paginate(5)->appends(['text1' => $text1]);
        return $result;
        // $sql = 'SELECT * FROM companys ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }
    
    public function qstring(){
        $text1 = request('text1') ? request('text1') : "";
        $page = request('page') ? request('page') : "1";

        $tmp = $text1 ? "?text1=$text1&page=$page" : "?page=$page";

        return $tmp;
    }
}