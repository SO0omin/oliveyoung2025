<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Gubun;
use App\Models\Sale;

class BestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $text1 = request('text1');
        if(!$text1) $text1 = date("Y-m-d",strtotime("-1 month")); //오늘기준 1달전 날짜

        $text2 = request('text2');
        if(!$text2) $text2 = date("Y-m-d"); //텍스트가 null이면 오늘 날짜

        $data['text1'] = $text1;
        $data['text2'] = $text2;

        $data['list'] = $this->getlist($text1, $text2); //배열 선언 + 값할당

        $data['list_item'] = $this->getlist_item(); //콤보상자용 제품정보
        return view('admin.best.index', $data);
    }

    public function getlist($text1, $text2)
    {
        $company_id = session('company_id');
        $result = Sale::leftjoin('items','sales.item_id','=','items.id')
            ->select('items.name as items_name',DB::raw('count(sales.numo) as cnumo')) //cnumo는 매출건수!!
            ->wherebetween('sales.writeday',array($text1, $text2))
            ->when($company_id != 1, function($query) use ($company_id) { $query->where('company_id', $company_id);})
            ->where('sales.io','=', 1) //판매실적이기 때문에 매출
            ->orderby('cnumo','desc')
            ->groupby('items.name')
            ->paginate(5)
            ->appends(['text1'=>$text1,'text2'=>$text2]);
        return $result;
        // $sql = 'SELECT * FROM members ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }

    public function getlist_item(){
        $company_id = session('company_id');
        $result = Item::orderby('name')
                        ->when($company_id != 1, function($query) use ($company_id) { $query->where('company_id', $company_id);})
                        ->get();
        return $result;
    }
}
