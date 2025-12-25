<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Carousel;
use App\Models\Event; // 이벤트를 선택하기 위해 사용

class CarouselsController extends Controller
{
    // --------------------------------------------------------------------------------
    // 1. READ (목록)
    // --------------------------------------------------------------------------------
    public function index(Request $request)
    {
        $text1 = $request->input('text1'); // 검색어 (제목 등)

        $query = Carousel::orderBy('id', 'desc');

        if ($text1) {
            $query->where('title', 'like', '%' . $text1 . '%');
        }
        $list = $query->paginate(10)->appends(['text1' => $text1]);

        return view('admin.carousels.index', [
            'list' => $list,
            'text1' => $text1,
            'tmp' => $this->qstring(['page' => 1]) // 페이지네이션용 쿼리스트링
        ]);
    }

    // --------------------------------------------------------------------------------
    // 2. CREATE (생성)
    // --------------------------------------------------------------------------------
    public function create()
    {
        // 캐러셀에 연결할 수 있는 이벤트 목록 (캐러셀에 연결되지 않은 이벤트만 표시)
        $events = Event::doesntHave('carousels')->orderBy('title')->get();
        
        return view('admin.carousels.create', [
            'events' => $events,
            'tmp' => $this->qstring()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable|string',
            // 💡 [수정] url 규칙 대신 'nullable|string'을 사용하여 슬래시(/)를 허용
            'link_url' => 'nullable|string', 
            
            'event_id' => 'nullable|exists:events,id',
            'pic_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $carousel = new Carousel;
        $this->saveCarouselData($request, $carousel);

        return redirect()->route('carousels.index')
                         ->with('success', '새 캐러셀이 성공적으로 등록되었습니다.');
    }

    // --------------------------------------------------------------------------------
    // 3. READ (상세 보기)
    // --------------------------------------------------------------------------------
    public function show(string $id)
    {
        $row = Carousel::with('event')->findOrFail($id);
        
        return view('admin.carousels.show', [
            'row' => $row,
            'tmp' => $this->qstring()
        ]);
    }

    // --------------------------------------------------------------------------------
    // 4. UPDATE (수정)
    // --------------------------------------------------------------------------------
    public function edit(string $id)
    {
        $row = Carousel::findOrFail($id);
        
        // 현재 캐러셀과 연결되지 않았거나, 현재 캐러셀이 연결된 이벤트 목록만 가져옴
        $events = Event::whereDoesntHave('carousels')
                       ->orWhere('id', $row->event_id)
                       ->orderBy('title')
                       ->get();

        return view('admin.carousels.edit', [
            'row' => $row,
            'events' => $events,
            'tmp' => $this->qstring()
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable|string',
            // 💡 [수정] url 규칙 대신 'nullable|string'을 사용하여 슬래시(/)를 허용
            'link_url' => 'nullable|string',
            
            'event_id' => 'nullable|exists:events,id',
            'pic_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $carousel = Carousel::findOrFail($id);
        $this->saveCarouselData($request, $carousel, true); // true는 수정 모드

        return redirect()->route('carousels.index')
                     ->with('success', '캐러셀이 성공적으로 수정되었습니다.');
    }

    // --------------------------------------------------------------------------------
    // 5. DELETE (삭제)
    // --------------------------------------------------------------------------------
    public function destroy(string $id)
    {
        $carousel = Carousel::findOrFail($id);
        
        // 1. 실제 파일 삭제
        if ($carousel->pic) {
            Storage::disk('public')->delete('carousel_img/' . $carousel->pic);
        }

        // 2. DB 레코드 삭제
        $carousel->delete();

        return redirect()->route('carousels.index')
                         ->with('success', '캐러셀이 삭제되었습니다.');
    }
    
    // --------------------------------------------------------------------------------
    // 보조 메서드
    // --------------------------------------------------------------------------------

    // 데이터 저장/업데이트 공통 로직
    protected function saveCarouselData(Request $request, Carousel $carousel, $isUpdate = false)
    {
        $carousel->title = $request->input('title');
        $carousel->content = $request->input('content');
        $carousel->link_url = $request->input('link_url');
        $carousel->event_id = $request->input('event_id'); // null 또는 ID

        // 파일 처리
        if ($request->hasFile('pic_file')) {
            // 1. 기존 파일 삭제 (수정 모드일 때만)
            if ($isUpdate && $carousel->pic) {
                Storage::disk('public')->delete('carousel_img/' . $carousel->pic);
            }
            
            // 2. 새 파일 업로드
            $file = $request->file('pic_file');
            $file_name = $file->hashName();
            $file->storeAs('carousel_img', $file_name, 'public');
            
            $carousel->pic = $file_name;
        }

        $carousel->save();
    }
    
    // 쿼리스트링 생성 (필터 및 페이지 유지)
    protected function qstring(array $extra = [])
    {
        $text1 = request('text1') ? request('text1') : "";
        $page  = request('page') ? request('page') : "1";

        $q = "?page=$page";
        if ($text1) $q .= "&text1=$text1";
        
        foreach ($extra as $key => $value) {
            // 이미 존재하는 쿼리스트링은 덮어쓰지 않습니다.
            if (!request()->has($key)) {
                $q .= "&$key=$value";
            }
        }

        return $q;
    }
}