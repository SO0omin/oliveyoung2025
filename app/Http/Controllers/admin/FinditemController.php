<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Gubun;

class FinditemController extends Controller
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
        return view('admin.finditem.index', $data);
    }

    public function getlist($text1)
    {
        $company_id = session('company_id');
        $result = Item::leftJoin('detail_categories', 'items.detail_category_id', '=', 'detail_categories.id')
                ->leftJoin('sub_categories', 'detail_categories.sub_id', '=', 'sub_categories.id')
                ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
                ->select('items.*','categories.name as category_name')->
                where('items.name','like','%' . $text1 . '%')->
                when($company_id != 1, function($query) use ($company_id) { $query->where('company_id', $company_id);})->
                orderby('items.name','asc')->
                paginate(10)->appends(['text1'=>$text1]);
        return $result;
        // $sql = 'SELECT * FROM members ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }
    public function qstring(){
        $text1 = request('text1') ? request('text1') : "";
        $page = request('page') ? request('page') : "1";

        $tmp = $text1 ? "?text1=$text1&page=$page" : "?page=$page";

        return $tmp;
    }
}