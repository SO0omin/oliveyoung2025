@extends('admin.main')
@section('content')

<h3 class="alert mt-3 ctg-admin" role="alert">이벤트 관리</h3>

<form name="form1" method="get" action="{{ route('admin.event.index') }}">
    <div class="row">
        <div class="col-sm-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text">제목 검색</span>
                <input type="text" name="text1" value="{{ $text1 }}" class="form-control" placeholder="제목을 입력하세요">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search me-1"></i> 검색
                </button>
            </div>
        </div>
        <div class="col-sm-8 text-end">
            <a href="{{ route('admin.event.create') }}" class="btn btn-sm mycolor1">
                <i class="fas fa-plus me-1"></i> 이벤트 등록
            </a>
        </div>
    </div>
</form>

<div class="table-responsive mt-3">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th style="width:5%;">ID</th>
                <th style="width:10%;">메인 이미지</th>
                <th style="width:30%;">이벤트 제목</th>
                <th style="width:10%;">추가 이미지</th>
                <th style="width:10%;">관련 상품</th>
                <th style="width:15%;">등록일</th>
                <th style="width:10%;">작업</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>
                        @if($row->pic)
                            <img src="{{ asset('storage/event_uploads/'.$row->pic) }}" width="50" height="50" class="img-fluid rounded">
                        @else
                            (이미지 없음)
                        @endif
                    </td>
                    <td><a href="{{ route('admin.event.show', $row->id) }}{{ $tmp }}">{{ $row->title }}</a></td>
                    <td>{{ $row->images->count() }}개</td>
                    <td>{{ $row->items->count() }}개</td>
                    <td>{{ $row->created_at ? $row->created_at->format('Y-m-d') : '' }}</td>
                    <td>
                        <a href="{{ route('admin.event.edit', $row->id) }}{{ $tmp }}" class="btn btn-sm btn-outline-primary me-1">수정</a>
                        <form action="{{ route('admin.event.destroy', $row->id) }}{{ $tmp }}" method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">삭제</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    {{-- 총 7개의 열을 합칩니다. --}}
                    <td colspan="7" class="text-center">등록된 이벤트가 없습니다.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 페이지네이션 링크 --}}
<div class="d-flex justify-content-center">
    {{ $list->links('mypagination') }}
</div>

@endsection