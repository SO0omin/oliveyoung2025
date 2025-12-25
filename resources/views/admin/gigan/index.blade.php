@extends('admin.main')
@section('content')

<h3 class="alert mt-3 ctg-admin" role="alert">기간별 매출입 현황</h3>

<script>
    function find_text() {
        form1.action="{{ route('gigan.index') }}";
        form1.submit();
    }

    $(function(){
        $('#text1').datetimepicker({
            locale: 'ko',
            format: 'YYYY-MM-DD'
        });
        $('#text2').datetimepicker({
            locale: 'ko',
            format: 'YYYY-MM-DD'
        });

        $('#text1').on('dp.change', function(e){
            find_text();
        });
        $('#text2').on('dp.change', function(e){
            find_text();
        });
    });

    function make_excel(){
        form1.action = "{{ url('admin/gigan/excel') }}";
        form1.submit();
    }
</script>

<form name="form1" action="">
    <div class="d-flex align-items-center mb-3 justify-content-between">
        <!-- 왼쪽: 날짜1 - 날짜2 -->
        <div class="d-flex align-items-center">
            <div class="input-group input-group-sm date me-2" id="text1" style="min-width: 180px;">
                <span class="input-group-text">날짜</span>
                <input type="text" class="form-control" size="10" name="text1" value="{{ $text1 }}"
                onKeydown="if (event.keyCode == 13) { find_text(); }">
                <div class="input-group-text">
                    <span class="input-group-addon">
                        <i class="far fa-calendar-alt fa-lg"></i>
                    </span>
                </div>
            </div>
            <span class="me-2">-</span>
            <div class="input-group input-group-sm date" id="text2" style="min-width: 150px;">
                <input type="text" class="form-control" size="10" name="text2" value="{{ $text2 }}"
                onKeydown="if (event.keyCode == 13) { find_text(); }">
                <div class="input-group-text">
                    <span class="input-group-addon">
                        <i class="far fa-calendar-alt fa-lg"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- 오른쪽: 제품명 + EXCEL -->
        <div class="d-flex align-items-center">
            <div class="input-group input-group-sm me-2" style="max-width:400px;">
                <span class="input-group-text">제품명</span>
                <select name="text3" class="form-select form-select-sm" onchange="find_text();">
                    <option value="0" selected>전체</option>
                    @foreach($list_item as $row)
                        <option value="{{ $row->id }}" @if($row->id==$text3) selected @endif>
                            {{ $row->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <input type="button" value="EXCEL" class="btn btn-sm mycolor1"
                onclick="if(confirm('엑셀파일로 저장할까요?')) make_excel();">
        </div>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-sm table-bordered table-hover table-striped align-middle">
        <thead class="table-secondary text-center">
            <tr>
                <th style="width:15%;">날짜</th>
                <th style="width:25%;">제품명</th>
                <th style="width:10%;">단가</th>
                <th style="width:10%;">매입수령</th>
                <th style="width:10%;">매출수령</th>
                <th style="width:15%;">금액</th>
                <th style="width:15%;">비고</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $row)
                <tr>
                    <td class="text-center">{{ $row->writeday }}</td>
                    <td>
                        @if($row->io == 0)
                            <a href="{{ route('salei.show', $row->id) }}" class="link-btn text-decoration-none">
                                {{ $row->item_name }}
                            </a>
                        @else
                            <a href="{{ route('saleo.show', $row->id) }}" class="link-btn text-decoration-none">
                                {{ $row->item_name }}
                            </a>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($row->price) }}</td>
                    <td class="text-end">{{ number_format($row->numi) }}</td>
                    <td class="text-end">{{ number_format($row->numo) }}</td>
                    <td class="text-end">{{ number_format($row->prices) }}</td>
                    <td>{{ $row->bigo }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">등록된 데이터가 없습니다.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $list->links('mypagination') }}
</div>

@endsection