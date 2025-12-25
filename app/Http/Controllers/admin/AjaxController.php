<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\DetailCategory;

class AjaxController extends Controller
{
    // ... (index, getlist, qstring 메소드는 변화 없음) ...

    public function index()
    {
        $data['tmp'] = $this->qstring();
        // next_id는 현재 DetailCategory에만 사용되지만, 일단 유지합니다.
        $data['next_id'] = DB::select("SHOW TABLE STATUS LIKE 'detail_categories'")[0]->Auto_increment; 
        $data['text1'] = request('text1') ?? '';
        
        
        // 계층 구조 목록 가져오기
        $data['list'] = $this->getlist($data['text1']);

        return view('admin.ajax.index', $data);
    }

    public function getlist($text1)
    {
        $categories = Category::with(['subCategories.detailCategories'])
            ->when($text1, function($q) use ($text1) {
                // 검색어는 최상위 카테고리에만 적용합니다. (필요 시 하위 레벨 검색 로직 추가 가능)
                $q->where('name', 'like', "%$text1%"); 
            })
            ->orderBy('id')
            ->get();
        // 현재 뷰에서 pagination을 사용하지 않으므로 get()을 유지합니다.
        // return $categories->paginate(10); 

        return $categories;
    }

    public function qstring()
    {
        $text1 = request('text1') ?? '';
        $page = request('page') ?? '1';

        return $text1 ? "?text1=$text1&page=$page" : "?page=$page";
    }


    /**
     * 항목 추가 (POST /admin/ajax/{type})
     */
    public function store(Request $request, string $type)
    {
        // 1. 유효성 검사
        $this->validate_request($request);

        // 2. 모델 및 부모 ID 설정 정보를 가져옴
        $config = $this->get_model_config($type, $request); // 연관 배열 반환

        // 3. 데이터 저장
        $modelClass = $config['model'];
        $row = new $modelClass;
        $row->name = $request->input('name');
        
        // 부모 ID 설정 (sub나 detail 추가 시)
        if ($config['foreignKey'] && $config['parentId']) {
            $row->{$config['foreignKey']} = $config['parentId'];
        }

        $row->save();

        return response()->json($row, 201);
    }

    /**
     * 항목 수정 (PUT/PATCH /admin/ajax/{type}/{id})
     */
    public function update(Request $request, string $type, string $id)
    {
        // 1. 유효성 검사
        $this->validate_request($request);

        // 2. 모델 찾기
        $modelClass = $this->get_model_config($type)['model'];
        $row = $modelClass::find($id);

        if (!$row) {
            return response()->json(['message' => '항목을 찾을 수 없습니다.'], 404);
        }

        // 3. 데이터 업데이트 및 저장
        $row->name = $request->input('name');
        $row->save();

        return response()->json($row);
    }
    /**
    * 항목 삭제 (DELETE /admin/ajax/{type}/{id})
    * @param string $type (category, sub, detail)
    * @param string $id 삭제할 항목 ID
    */
    public function destroy(string $type, string $id)
    {
        // 1. 모델 찾기
        $config = $this->get_model_config($type);
        $model = $config['model']; // ✅ [0] 제거
        $row = $model::find($id);

        if (!$row) {
            return response()->json(['message' => '항목을 찾을 수 없습니다.'], 404);
        }

        // 2. 삭제 실행 (관계 설정에 따라 하위 항목도 같이 삭제될 수 있음)
        $row->delete();

        return response()->json(['message' => '삭제 완료']);
    }

    /**
     * 모델 이름 및 외래 키 설정 반환 (LIST 대신 연관 배열 사용으로 명확성 증대)
     */
    private function get_model_config(string $type, Request $request = null)
    {
        // 부모 ID는 POST 데이터에서 가져오며, 없을 경우 null
        $parentId = $request ? $request->input('parent_id') : null; 

        switch ($type) {
            case 'category':
                return [
                    'model' => Category::class,
                    'foreignKey' => null, // 최상위 카테고리는 외래 키 없음
                    'parentId' => null
                ];
            case 'sub':
                return [
                    'model' => SubCategory::class,
                    'foreignKey' => 'category_id', // SubCategory는 Category ID에 종속
                    'parentId' => $parentId
                ];
            case 'detail':
                // **주의**: DB 스키마에 따라 'sub_id' 또는 'sub_category_id' 사용
                return [
                    'model' => DetailCategory::class,
                    'foreignKey' => 'sub_id', // DetailCategory는 SubCategory ID에 종속
                    'parentId' => $parentId
                ];
            default:
                throw new \InvalidArgumentException("Invalid type provided: $type");
        }
    }
    
    /**
     * 공통 유효성 검사
     */
    private function validate_request(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50'
        ], [
            'name.required' => '이름은 필수입력입니다.',
            'name.max' => '50자 이내입니다.'
        ]);
    }
}