<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Gubun;

class PictureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $text1 = request('text1');
        $data['text1'] = $text1;
        $data['list'] = $this->getlist($text1); //배열 선언 + 값할당
        return view('admin.picture.index', $data);
    }

    public function getlist($text1)
    {
        $company_id = session('company_id');
        $result = Item::where('name','like','%' . $text1 . '%')->
            orderby('name','asc')->
            when($company_id != 1, function($query) use ($company_id) { $query->where('company_id', $company_id);})->
            paginate(8)->appends(['text1'=>$text1]);
        return $result;
        // $sql = 'SELECT * FROM members ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }
}