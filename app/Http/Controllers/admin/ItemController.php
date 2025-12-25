<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\DetailCategory;
use Intervention\Image\Laravel\Facades\Image;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $data['tmp'] = $this->qstring();

        $text1 = request('text1');
        $data['text1'] = $text1;
        $data['list'] = $this->getlist($text1); //배열 선언 + 값할당
        return view('admin.item.index', $data);
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
    
    public function create()
    {
        $data['list'] = $this->getlist_gubun();
        $data['next_id'] = DB::select("SHOW TABLE STATUS LIKE 'items'")[0]->Auto_increment;
        $data['tmp'] = $this->qstring();
        return view('admin.item.create', $data);
    }

    public function getlist_gubun(){
        $result = Category::orderby('name')->get();
        return $result;
    }

    public function store(Request $request)
    {
        // 1. 새 Item 모델 인스턴스 생성
        $row = new Item; 
        
        // 2. 데이터 저장 (여기서 $row->save()가 호출되어 ID가 할당됩니다)
        $this->save_row($request, $row);

        // 3. ✨ 새로 저장된 $row의 ID를 사용하여 item.show 라우트로 리디렉션 ✨
        // $row->id는 방금 생성된 제품의 고유 ID입니다.
        
        // 목록으로 돌아갈 때 필요한 쿼리 스트링($tmp)을 세션에 담아 show 페이지로 전달합니다.
        $tmp = $this->qstring(); 
        
        return redirect()->route('item.show', ['item' => $row->id])->with('tmp', $tmp);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['tmp'] = $this->qstring();

        $data['row'] = Item::leftJoin('detail_categories', 'items.detail_category_id', '=', 'detail_categories.id')
            ->leftJoin('sub_categories', 'detail_categories.sub_id', '=', 'sub_categories.id')
            ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->leftJoin('companies', 'items.company_id', '=', 'companies.id')
            ->select(
                'items.*',
                'detail_categories.name as detail_name',  // 소분류
                'sub_categories.name as sub_name',        // 중분류
                'categories.name as category_name',       // 대분류
                'companies.name as company_name'          // 회사명
            )
            ->where('items.id', $id)
            ->first();

        return view('admin.item.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // app/Http/Controllers/ItemController.php

    public function edit(string $id)
    {
        $data['list'] = $this->getlist_gubun(); // 기타 리스트
        $data['tmp'] = $this->qstring(); // 쿼리 스트링

        // 아이템 + companies + detailCategory.subCategory.category 관계 로딩
        $data['row'] = Item::with('detailCategory.subCategory.category')
                            ->leftJoin('companies', 'items.company_id', '=', 'companies.id')
                            ->select('items.*', 'companies.name as company_name')
                            ->where('items.id', $id)
                            ->first();

        // 모든 대분류 (필요함)
        $data['categories'] = Category::all();

        // --- 🚨 AJAX를 사용하므로 이 부분은 필요 없습니다! ---
        // $data['sub_categories'] = ...
        // $data['detail_categories'] = ...
        // ----------------------------------------------------

        return view('admin.item.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $row = Item::find($id); //자료 찾기
        $this->save_row($request,$row);

        $tmp = $this->qstring();
        return  redirect(url('admin/item' . $tmp));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::with('detailImages')->find($id); // 상세 이미지 관계를 함께 로드

        if ($item) {
            // 1. 대표 사진 삭제
            if ($item->pic) {
                Storage::disk('public')->delete('item_img/' . $item->pic);
                Storage::disk('public')->delete('item_img/thumb/' . $item->pic);
            }

            // 2. 연관된 모든 상세 이미지 파일과 DB 레코드 삭제
            foreach ($item->detailImages as $detailImage) {
                Storage::disk('public')->delete('item_detail_img/' . $detailImage->img_path);
                $detailImage->delete(); 
            }
            
            // 3. 제품 레코드 삭제
            $item->delete();
        }

        $tmp = $this->qstring();
        return  redirect(url('admin/item' . $tmp));
    }

    public function jaego(){
        DB::statement('drop table if exists temps;');
        DB::statement('create table temps (
            id int not null auto_increment,
            item_id int,
            jaego int default 0,
            primary key(id) );');
        DB::statement('update items set jaego = 0;');
        DB::statement('insert into temps (item_id, jaego)
            select item_id, sum(numi)-sum(numo)
            from sales
            group by item_id;');
        DB::statement('update items join temps
            on items.id = temps.item_id
            set items.jaego = temps.jaego;');

       return  redirect(url('admin/item'));
    }

    public function save_row(Request $request, $row){
        $request->validate([
            'company_id' => 'required',
            'detail_category_id' => 'required|numeric',
            'name' => 'required',
            'price' => 'required|numeric'
        ],
        [
            'company_id' => '회사명은 필수선택입니다.',
            'detail_category_id' => '구분명은 필수입력입니다.',
            'name.required' => '이름은 필수입력입니다.',
            'price.required' => '단가는 필수입력입니다.',
        ]);
        
        if(session('company_id')==1){
            $row->company_id = $request->input('company_id');
        } else {
            $row->company_id = session('company_id');
        }
        $row->detail_category_id = $request->input('detail_category_id');
        $row->name = $request->input('name');
        $row->price = $request->input('price');
        $row->jaego = $request->input('jaego');

        if($request->hasFile('pic')){
            $pic = $request->file('pic');
            $pic_name = $pic->getClientOriginalName(); //파일이름

            $img = Image::read($pic->getRealPath())
                ->resize(null,200,function($constraint) { $constraint->aspectRatio();})
                ->save('storage/item_img/thumb/'. $pic_name);

            $pic->storeAs('item_img', $pic_name, 'public'); //파일저장

            $row->pic = $pic_name; //$row->pic은 파일명
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