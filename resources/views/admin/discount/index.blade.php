@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">할인 이벤트 목록</h3>

<script>
function find_text() {
    // text2 값 추가
    //const text1 = document.querySelector('input[name="text1"]').value;
    const text2 = document.querySelector('select[name="text2"]').value; // text2 추가
    const text3 = document.querySelector('select[name="text3"]').value;
    
    // URL에 text2 파라미터 추가text1=${text1}&
    location.href = `?text2=${text2}&text3=${text3}`; 
}

$(function(){
    /*$('#text1').datetimepicker({
        locale: 'ko',
        format: 'YYYY-MM-DD',
        /* defaultDate: moment() // 기존 코드는 defaultDate가 있었으나, 전체 조회를 위해 제거할 수 있음
        @if(isset($text1) && $text1)
        defaultDate: moment("{{ $text1 }}")
        @endif
    });*/

    $('#text2').on('dp.change', function(e){
        find_text();
    });
});
</script>

<form name="form1" action="" method="get">
<div class="row mb-3 align-items-center">
    
    {{-- 필터 그룹 (d-flex로 묶고 gap-2로 간격 최소화) --}}
    <div class="col-12 col-md-9 d-flex flex-wrap align-items-center gap-2"> 
        
        <!-- 1. 날짜 검색 영역 (max-width: 200px)
        <div style="max-width: 200px;">
            {{-- 기존 코드 유지 --}}
            <div class="input-group input-group-sm date" id="text1">
                <span class="input-group-text">날짜</span>
                <input type="text" class="form-control" size="10" name="text1" value="{{ $text1 }}"
                onKeydown="if (event.keyCode == 13) { find_text(); }">
                <div class="input-group-text">
                    <span class="input-group-addon">
                        <i class="far fa-calendar-alt fa-lg"></i>
                    </span>
                </div>
            </div>
        </div>-->

        {{-- 2. 상태 필터링 영역 (max-width: 200px) --}}
        <div style="max-width: 200px;">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-filter me-1"></i> 상태</span>
                <select name="text2" class="form-select form-select-sm" onchange="find_text();">
                    <option value="0" {{ ($text2 ?? 0) == 0 ? 'selected' : '' }}>전체 상태</option>
                    <option value="1" {{ ($text2 ?? 0) == 1 ? 'selected' : '' }}>예정된 할인</option>
                    <option value="2" {{ ($text2 ?? 0) == 2 ? 'selected' : '' }}>현재 진행중</option>
                    <option value="3" {{ ($text2 ?? 0) == 3 ? 'selected' : '' }}>종료된 할인</option>
                </select>
            </div>
        </div>

        {{-- 3. 제품명 필터링 영역 (max-width: 200px로 통일) --}}
        <div style="max-width: 200px;">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-box me-1"></i> 제품명</span>
                <select name="text3" class="form-select form-select-sm" onchange="find_text();">
                    <option value="0" {{ $text3 == 0 ? 'selected' : '' }}>전체 제품</option>
                    @foreach($list_item as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $text3 ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div> 

    {{-- 4. 추가 버튼 영역 (오른쪽 정렬) --}}
    <div class="col-12 col-md-3 text-end mt-2 mt-md-0 ms-auto">
        <a href="{{ route('discount.create') }}{{ $tmp ?? '' }}" class="btn btn-sm mycolor1">
            <i class="fas fa-plus me-1"></i> 할인 추가
        </a>
    </div>
</div>
</form>

{{-- 할인 목록 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="table-secondary text-center">
            <tr>
                <th style="width:25%;">제품명</th>
                <th style="width:15%;">할인 가격</th>
                <th style="width:15%;">할인율</th>
                <th style="width:15%;">시작일</th>
                <th style="width:15%;">종료일</th>
                <th style="width:15%;">진행 상태</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $row)
                <tr>
                    <td>
                        {{-- 상세 페이지 링크 --}}
                        <a class="text-decoration-none ctg-admin" href="{{ route('discount.show', $row->id)}}{{ $tmp ?? '' }}">
                            {{ $row->items_name }}
                        </a>
                    </td>
                    <td class="text-end text-danger" style="font-size:15px;">{{ number_format($row->sale_price) }}원</td>
                    <td class="text-center">
                        @if($row->discount_percent)
                            <span class="badge bg-danger" style="font-size:15px;">{{ round($row->discount_percent) }}% 할인</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->start_at)->format('Y-m-d') }}</td>
                    <td class="text-center">
                        @if($row->end_at)
                            {{ \Carbon\Carbon::parse($row->end_at)->format('Y-m-d') }}
                        @else
                            <span class="text-muted">무기한</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($row->is_active == 1)
                            <span class="badge bg-success" style="font-size:15px;">진행중</span>
                        @elseif($row->is_active == 0)
                            <span class="badge bg-secondary" style="font-size:15px;">종료</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:15px;">미시작</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">등록된 데이터가 없습니다.</td>
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