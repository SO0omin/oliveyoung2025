@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">제품</h3>

{{-- 검색 스크립트 --}}
<script>
function find_text() {
    form1.action = "{{ route('item.index') }}";
    form1.submit();
}
</script>

{{-- 검색 및 버튼 --}}
<form name="form1" action="" class="mb-3">
    <div class="row align-items-center">
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text">이름</span>
                <input type="text" name="text1" value="{{ $text1 }}" placeholder="찾을 이름은?" class="form-control"
                    onkeydown="if(event.keyCode === 13) { find_text(); }">
                <button class="btn mycolor1" type="button" onclick="find_text();">검색</button>
            </div>
        </div>
        <div class="col-md-9 text-end">
            <a href="{{ route('item.create') }}{{ $tmp }}" class="btn btn-sm mycolor1">
                <i class="fas fa-plus me-1"></i>추가
            </a>
            <a href="{{ url('admin/item/jaego') }}{{ $tmp }}" class="btn btn-sm mycolor1">
                재고계산
            </a>
        </div>
    </div>
</form>

{{-- 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="table-secondary text-center">
            <tr>
                <th style="width:10%;">번호</th>
                <th style="width:20%;">분류</th>
                <th style="width:30%;">제품명</th>
                <th style="width:20%;">단가</th>
                <th style="width:20%;">재고</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $row)
            <tr>
                <td class="text-center">{{ $row->id }}</td>
                <td class="text-center">{{ $row->category_name }}</td>
                <td>
                    <a href="{{ route('item.show', $row->id) }}{{ $tmp }}" class="link-btn text-decoration-none mycolor4" 
                       title="{{ $row->name }}">
                        {{ $row->name }}
                    </a>
                </td>
                <td class="text-end">{{ number_format($row->price) }}</td>
                <td class="text-end">{{ number_format($row->jaego) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">등록된 데이터가 없습니다.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 페이지네이션 --}}
<div class="d-flex justify-content-center mt-3">
    {{ $list->links('mypagination') }}
</div>

@endsection