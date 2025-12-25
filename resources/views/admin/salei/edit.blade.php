@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">매입 수정</h3>

<form name="form1" method="POST" action="{{ route('salei.update', $row->id) }}{{ $tmp }}">
    @csrf
    @method('PATCH')

    {{-- 상세 테이블 --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <tbody>
                <tr>
                    <th style="width:20%;">날짜</th>
                    <td>
                        <div class="input-group input-group-sm date" id="writeday">
                            <input type="text" name="writeday" class="form-control" 
                                value="{{ $row->writeday }}">
                            <div class="input-group-text">
                                <span class="input-group-addon"> <i class="far fa-calendar-alt fa-lg"></i></span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><span class="text-danger">*</span> 제품명</th>
                    <td>
                        <select name="sel_items_id" class="form-select form-select-sm" onchange="select_item();">
                            <option value="">선택하세요.</option>
                            @foreach($list as $row1)
                                <? $t1 = "$row1->id^^$row1->price"; $t2 = "$row1->name($row1->price)"; ?>
                                <option value="{{ $t1 }}" @if($row->item_id == $row1->id) selected @endif>
                                    {{ $t2 }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="item_id" value="{{ $row->item_id }}">
                    </td>
                </tr>
                <tr>
                    <th><span class="text-danger">*</span> 단가</th>
                    <td>
                        <input type="text" name="price" class="form-control form-control-sm" 
                               value="{{ $row->price }}" onchange="cal_prices();">
                    </td>
                </tr>
                <tr>
                    <th>수량</th>
                    <td>
                        <input type="text" name="numi" class="form-control form-control-sm" 
                               value="{{ $row->numi }}" onchange="cal_prices();">
                    </td>
                </tr>
                <tr>
                    <th>금액</th>
                    <td>
                        <input type="text" name="prices" class="form-control form-control-sm" 
                               value="{{ $row->prices }}" readonly>
                    </td>
                </tr>
                <tr>
                    <th>비고</th>
                    <td>
                        <input type="text" name="bigo" class="form-control form-control-sm" 
                               value="{{ $row->bigo }}">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- 버튼 그룹 --}}
    <div class="d-flex justify-content-center gap-2 mt-3">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="fas fa-save me-1"></i>저장
        </button>

        <button type="button" class="btn btn-sm btn-secondary" onclick="history.back();">
            <i class="fas fa-arrow-left me-1"></i>이전화면
        </button>
    </div>
</form>

{{-- JS --}}
<script>
function select_item(){
    var str = form1.sel_items_id.value;
    if(str == ""){
        form1.item_id.value = "";
        form1.price.value = "";
        form1.prices.value = "";
    } else {
        str = str.split("^^");
        form1.item_id.value = str[0];
        form1.price.value = str[1];
        form1.prices.value = Number(str[1]) * Number(form1.numi.value);
    }
}

function cal_prices(){
    form1.prices.value = Number(form1.price.value) * Number(form1.numi.value);
    form1.bigo.focus();
}

$(function(){
    $('#writeday').datetimepicker({
        locale: 'ko',
        format: 'YYYY-MM-DD',
        defaultDate: moment(),
        // 위젯 위치 설정 객체 사용
        widgetPositioning: {
            // 'auto'가 기본값이며, 가장 많은 공간이 있는 곳에 표시
            vertical: 'bottom', 
            horizontal: 'auto'
        }
    });
    
});
</script>


@endsection