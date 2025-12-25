@extends('admin.main')
@section('content')
    <div class="container-fluid">
        <h2>캐러셀 상세 정보: {{ $row->title }}</h2>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ $row->title }}</h5>
                
                <table class="table table-bordered detail-table">
                    <tbody>
                        <tr>
                            <th style="width: 20%;">ID</th>
                            <td>{{ $row->id }}</td>
                        </tr>
                        <tr>
                            <th>제목</th>
                            <td>{{ $row->title }}</td>
                        </tr>
                        <tr>
                            <th>내용</th>
                            <td>{{ $row->content ?? '내용 없음' }}</td>
                        </tr>
                        <tr>
                            <th>링크 URL</th>
                            <td>{{ $row->link_url == "/" ? '메인 홈페이지' : $row->link_url }}</td>
                        </tr>
                        <tr>
                            <th>연결 이벤트</th>
                            <td>
                                @if($row->event)
                                    <a href="{{ route('event.show', $row->event->id) }}">{{ $row->event->title }} (ID: {{ $row->event->id }})</a>
                                @else
                                    연결된 이벤트 없음
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>등록일</th>
                            <td>{{ $row->created_at }}</td>
                        </tr>
                        <tr>
                            <th>수정일</th>
                            <td>{{ $row->updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <h4 class="mt-4">이미지 미리보기</h4>
                @if($row->pic)
                    {{-- public/storage/carousel_img 폴더에 저장된 이미지를 표시합니다. --}}
                    <img src="{{ asset('storage/carousel_img/' . $row->pic) }}" alt="{{ $row->title }}" class="img-fluid" style="max-width: 500px; height: auto;">
                @else
                    <p class="text-muted">등록된 이미지가 없습니다.</p>
                @endif
                
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('carousels.edit', $row->id) }}{{ $tmp }}" class="btn btn-warning">수정</a>
            <a href="{{ route('carousels.index') }}{{ $tmp }}" class="btn btn-secondary">목록</a>
            
            <form action="{{ route('carousels.destroy', $row->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('정말로 이 캐러셀을 삭제하시겠습니까?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">삭제</button>
            </form>
        </div>
    </div>
@endsection