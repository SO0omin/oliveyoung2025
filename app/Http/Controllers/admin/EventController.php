<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // 파일 삭제 및 관리를 위해 추가
use App\Models\Event;
use App\Models\EventImage;
use App\Models\Item;

class EventController extends Controller
{
    // 파일 저장 기본 폴더 이름
    protected $uploadFolder = 'event_uploads';

    // 현재 로그인된 관리자의 company_id와 type을 가져옵니다.
    private function getAdminFilterParams()
    {
        $company_id = session('company_id');
        $admin_type = session('type');
        
        // 'super_admin'이 아니며, company_id가 세션에 있을 때 필터링이 필요함
        $needs_company_filter = ($admin_type !== 'super_admin' && $company_id);
        
        return [
            'needs_company_filter' => $needs_company_filter,
            'company_id' => $company_id
        ];
    }

    // --------------------------------------------------------------------------------
    // 1. READ (목록)
    // --------------------------------------------------------------------------------
    public function index()
    {
        $data['tmp'] = $this->qstring();
        
        $text1 = request('text1', '');
        
        $data['list'] = $this->getlist($text1);
        $data['text1'] = $text1;

        return view('admin.event.index', $data);
    }
    
    public function getlist($text1)
    {
        $filter = $this->getAdminFilterParams();

        $query = Event::orderBy('id', 'desc');

        // 🔥 회사 필터링 적용 🔥
        $query->when($filter['needs_company_filter'], function($q) use ($filter) {
            $q->where('company_id', $filter['company_id']);
        });

        // text1 (검색어) 필터링
        if ($text1) {
            $query->where('title', 'like', '%' . $text1 . '%');
        }

        return $query->paginate(5)->appends(['text1' => $text1]);
    }

    // --------------------------------------------------------------------------------
    // 2. CREATE (생성)
    // --------------------------------------------------------------------------------
    public function create()
    {
        $data['tmp'] = $this->qstring();
        $filter = $this->getAdminFilterParams();
        
        // 🔥 관련 상품 선택 시에도 해당 회사 상품만 조회 🔥
        $data['items'] = Item::orderBy('name')
            ->when($filter['needs_company_filter'], function($q) use ($filter) {
                $q->where('company_id', $filter['company_id']);
            })
            ->get(); 
        
        return view('admin.event.create', $data);
    }

    public function store(Request $request)
    {
        $filter = $this->getAdminFilterParams();

        $request->validate([
            'title' => 'required|max:255',
            'pic_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'item_ids' => 'nullable|array',
            // super_admin이 만들 경우를 대비하여 company_id도 유효성 검사
            'company_id' => $filter['needs_company_filter'] ? 'nullable' : 'nullable|integer|exists:companies,id',
        ]);

        // 2. 메인 이미지 파일 업로드 및 경로 설정 (pic)
        $pic_name = $this->uploadFile($request, 'pic_file');
        
        // 3. 이벤트 기본 정보 저장
        $event = new Event;
        $event->title = $request->input('title');
        $event->pic = $pic_name;
        
        // 🔥 company_id 설정 🔥
        if ($filter['needs_company_filter']) {
            $event->company_id = $filter['company_id']; // 일반 관리자는 세션의 company_id 사용
        } else {
             // super_admin은 폼에서 받은 company_id를 사용하거나, 없으면 1번 회사로 지정
             $event->company_id = $request->input('company_id', 1);
        }
        $event->save();

        // 4. 관련 상품 연결 (event_items 테이블)
        $item_ids = $request->input('item_ids', []);
        $event->items()->attach($item_ids); 

        // 5. 추가 이미지 처리
        $this->saveAdditionalImages($request, $event);

        return redirect('admin/event' . $this->qstring());
    }

    // --------------------------------------------------------------------------------
    // 3. READ (상세 보기)
    // --------------------------------------------------------------------------------
    public function show(string $id)
    {
        $data['tmp'] = $this->qstring();
        
        $row = Event::with(['images' => function($query) {
                $query->orderBy('sort', 'asc');
            }, 'items'])
            ->findOrFail($id);
            
        // 🔥 권한 확인: 해당 회사의 이벤트인지 확인 🔥
        $this->checkCompanyAccess($row);

        $data['row'] = $row;
        return view('admin.event.show', $data);
    }

    // --------------------------------------------------------------------------------
    // 4. UPDATE (수정)
    // --------------------------------------------------------------------------------
    public function edit(string $id)
    {
        $data['tmp'] = $this->qstring();
        $filter = $this->getAdminFilterParams();

        $row = Event::with(['images', 'items'])->findOrFail($id);
        
        // 🔥 권한 확인 🔥
        $this->checkCompanyAccess($row);

        $data['row'] = $row;
        
        // 🔥 모든 상품 목록 (관련 상품 선택 UI를 위해) 🔥
        $data['items'] = Item::orderBy('name')
             ->when($filter['needs_company_filter'], function($q) use ($filter) {
                $q->where('company_id', $filter['company_id']);
            })
            ->get(); 
        
        $data['related_item_ids'] = $data['row']->items->pluck('id')->toArray();

        return view('admin.event.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $event = Event::with('images')->findOrFail($id);
        
        // 🔥 권한 확인 🔥
        $this->checkCompanyAccess($event);

        $request->validate([
            'title' => 'required|max:255',
            'pic_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'item_ids' => 'nullable|array',
        ]);
        
        /// 2. 메인 이미지 파일 업로드 (새 파일이 있으면)
        if ($request->hasFile('pic_file')) {
            // 기존 파일 삭제
            $this->deleteFile($event->pic);
            $event->pic = $this->uploadFile($request, 'pic_file');
        }

        // 3. 이벤트 기본 정보 업데이트
        $event->title = $request->input('title');
        // company_id는 생성 시에만 설정하고 업데이트에서는 변경하지 않는 것이 일반적입니다.
        $event->save();
        
        // 4. 관련 상품 연결 업데이트 (sync)
        $item_ids = $request->input('item_ids', []);
        $event->items()->sync($item_ids);

        // 5. 추가 이미지 처리 (새 파일이 있다면 기존 이미지에 이어서 저장)
        $this->saveAdditionalImages($request, $event);
        
        return redirect('admin/event' . $this->qstring());
    }

    // --------------------------------------------------------------------------------
    // 5. DELETE (삭제)
    // --------------------------------------------------------------------------------
    public function destroy(string $id)
    {
        $event = Event::with(['images'])->findOrFail($id);
        
        // 🔥 권한 확인 🔥
        $this->checkCompanyAccess($event);
        
        // 1. 이미지 파일 삭제 (메인 이미지)
        $this->deleteFile($event->pic);

        // 2. 추가 이미지 파일 및 DB 레코드 삭제
        foreach ($event->images as $image) {
             $this->deleteFile($image->img_path);
        }
        
        // 3. DB 레코드 삭제
        $event->images()->delete(); // event_images 레코드 삭제
        $event->items()->detach();  // event_items 중간 테이블 연결 해제
        
        // 4. 이벤트 레코드 삭제
        $event->delete();

        return redirect('admin/event' . $this->qstring());
    }
    
    // --------------------------------------------------------------------------------
    // 6. 추가 이미지 개별 삭제 (Ajax)
    // --------------------------------------------------------------------------------
    public function destroyImage($id)
    {
        try {
            $image = EventImage::findOrFail($id);
            
            // 🔥 추가 권한 확인: 이 이미지가 속한 이벤트가 접근 권한이 있는지 확인 🔥
            $event = $image->event; 
            if ($event) {
                $this->checkCompanyAccess($event);
            }

            // 2. 실제 파일 삭제
            $this->deleteFile($image->img_path);

            // 3. DB Row 삭제
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => '이미지가 성공적으로 삭제되었습니다.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             return response()->json(['success' => false, 'message' => '삭제할 이미지를 찾을 수 없습니다.'], 404);
        } catch (\Exception $e) {
            \Log::error("이미지 삭제 오류: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '삭제 중 서버 오류가 발생했습니다. (권한 또는 파일 시스템 문제)',
            ], 500);
        }
    }
    
    // --------------------------------------------------------------------------------
    // 보조 메서드
    // --------------------------------------------------------------------------------

    // 파일 업로드 처리 (메인 이미지)
    protected function uploadFile(Request $request, $fieldName)
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            
            // 🚨 hashName()을 사용하여 고유한 파일명 생성 및 저장 (가장 안정적)
            $file_name = $file->hashName(); 
            // 저장 경로: storage/app/public/event_uploads/파일명
            $file->storeAs($this->uploadFolder, $file_name, 'public'); 
            
            return $file_name;
        }
        return null;
    }

    // 추가 이미지 저장
    protected function saveAdditionalImages(Request $request, $event)
    {
        if ($request->hasFile('additional_files')) {
            
            $max_sort = $event->images()->max('sort');
            $sort = ($max_sort ?? 0) + 1;
            
            foreach ($request->file('additional_files') as $file) {
                if ($file->isValid()) {
                    
                    $file_name = $file->hashName(); 
                    // 저장 경로: storage/app/public/event_uploads/파일명
                    $file->storeAs($this->uploadFolder, $file_name, 'public'); 

                    $event->images()->create([
                        'img_path' => $file_name,
                        'sort' => $sort++
                    ]);
                }
            }
        }
    }
    
    // 파일 삭제 헬퍼 (재사용성을 위해 경로를 $this->uploadFolder로 설정)
    protected function deleteFile($fileName)
    {
        if ($fileName) {
            // 저장된 경로를 기준으로 삭제 요청
            Storage::disk('public')->delete($this->uploadFolder . '/' . $fileName);
        }
    }

    // 🔥 권한 확인 헬퍼 함수 🔥
    protected function checkCompanyAccess(Event $event)
    {
        $filter = $this->getAdminFilterParams();

        if ($filter['needs_company_filter'] && $event->company_id !== $filter['company_id']) {
            // 다른 회사의 이벤트에 접근 시 403 Forbidden 반환
            abort(403, '접근 권한이 없는 이벤트입니다.'); 
        }
    }

    // 쿼리스트링 생성 (필터 유지)
    public function qstring()
    {
        $text1 = request('text1') ? request('text1') : "";
        $page  = request('page') ? request('page') : "1";

        $q = "?page=$page";
        if ($text1) $q .= "&text1=$text1";

        return $q;
    }
}