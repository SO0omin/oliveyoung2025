@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">매입장</h3>

{{-- 날짜 검색 스크립트 --}}
<script>
function find_text() {
    form1.action = "{{ route('salei.index') }}";
    form1.submit();
}

$(function(){
        $('#text1').datetimepicker({
            locale: 'ko',
            format: 'YYYY-MM-DD',
            defaultDate: moment()
        });

        $('#text1').on('dp.change', function(e){
            find_text();
        });
    });
</script>

{{-- 검색 및 추가 버튼 --}}
<form name="form1" action="">
    <div class="row mb-3 align-items-center">
        <div class="col-md-3">
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
        </div>
        <div class="col-md-9 text-end">
            <a href="{{ route('salei.create') }}{{ $tmp }}" class="btn btn-sm mycolor1">
                <i class="fas fa-plus me-1"></i>추가
            </a>
        </div>
    </div>
</form>

{{-- 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="table-secondary text-center">
            <tr>
                <th style="width:15%;">날짜</th>
                <th style="width:30%;">제품명</th>
                <th style="width:10%;">단가</th>
                <th style="width:10%;">수령</th>
                <th style="width:15%;">금액</th>
                <th style="width:20%;">비고</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $row)
                <tr>
                    <td class="text-center">{{ $row->writeday }}</td>
                    <td>
                        <a href="{{ route('salei.show', $row->id) }}{{ $tmp }}" 
                           class="link-btn text-decoration-none" 
                           title="{{ $row->item_name }}">
                            {{ $row->item_name }}
                        </a>
                    </td>
                    <td class="text-end">{{ number_format($row->price) }}</td>
                    <td class="text-end">{{ number_format($row->numi) }}</td>
                    <td class="text-end">{{ number_format($row->prices) }}</td>
                    <td>{{ $row->bigo }}</td>
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