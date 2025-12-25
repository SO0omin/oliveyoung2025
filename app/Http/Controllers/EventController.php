<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       // event_images가 있는 이벤트만 가져오기
       $data['list'] = Event::doesntHave('carousels')
        // 필요한 정렬이나 페이지네이션을 추가합니다.
        ->orderBy('id', 'desc')
        ->get();

        return view('event.index', $data);
    }
    public function getlist($text1)
    {
        $result = Event::all();
        return $result;
        // $sql = 'SELECT * FROM customers ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with('images')->findOrFail($id); // images 함께 로드
        $data['row'] = $event;
        $data['items'] = $event->items; // Blade에서 items 사용 가능
        return view('event.show', $data);
    }
}