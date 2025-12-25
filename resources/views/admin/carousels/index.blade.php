@extends('admin.main')
@section('content')
    <div class="container-fluid">
        <h2>캐러셀 목록</h2>
         
        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded shadow-sm" style="font-size:15px;">
    
            <div class="flex-shrink-0">
                <a href="{{ route('carousels.create') }}{{ $tmp }}" class="btn mycolor1">
                    <i class="bi bi-plus-circle me-2"></i> 새 캐러셀 등록
                </a>
            </div>

            <div class="flex-grow-1 d-flex justify-content-end">
                <form action="{{ route('carousels.index') }}" method="GET" class="d-flex align-items-center flex-nowrap">
                    
                    <label for="search-title" class="me-2 text-muted fw-bold visually-hidden">제목 검색:</label>
                    
                    <input 
                        type="text" 
                        name="text1" 
                        id="search-title"
                        value="{{ $text1 }}" 
                        placeholder="검색어를 입력하세요" 
                        class="form-control me-2" 
                        style="max-width: 250px; min-width: 150px;"
                    >
                    
                    <button type="submit" class="btn btn-outline-secondary flex-shrink-0">
                        <i class="bi bi-search"></i> 검색
                    </button>
                    
                    {{-- 검색어가 있을 경우 초기화 버튼 추가 --}}
                    @if($text1)
                        <a href="{{ route('carousels.index', ['page' => 1]) }}" class="btn btn-outline-danger ms-2 flex-shrink-0">
                            <i class="bi bi-x-circle"></i> 초기화
                        </a>
                    @endif
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>이미지</th>
                    <th>제목</th>
                    <th>연결 이벤트</th>
                    <th>링크 URL</th>
                    <th>액션</th>
                </tr>
            </thead>
            <tbody>
                @foreach($list as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>
                        @if($row->pic)
                            {{-- public/storage/carousel_img 폴더에 저장되어 있다고 가정 --}}
                            <img src="{{ asset('storage/carousel_img/' . $row->pic) }}" alt="{{ $row->title }}" style="width: 100px;">
                        @else
                            이미지 없음
                        @endif
                    </td>
                    <td><a href="{{ route('carousels.show', $row->id) }}{{ $tmp }}">{{ $row->title }}</a></td>
                    <td>{{ $row->event->title ?? '없음' }}</td>
                    <td>{{ $row->link_url == "/" ? '메인 홈페이지' : $row->link_url }}</td>
                    <td>
                        <a href="{{ route('carousels.edit', $row->id) }}{{ $tmp }}" class="btn btn-sm btn-success">수정</a>
                        <form action="{{ route('carousels.destroy', $row->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">삭제</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $list->links('mypagination') }}
    </div>
@endsection