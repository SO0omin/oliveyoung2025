@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">매출 수정</h3>

<form name="form1" method="POST" action="{{ route('saleo.update', $row->id) }}{{ $tmp }}">
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
                        <div class="input-group input-group-sm">

                            {{-- hidden: items_id --}}
                            <input type="hidden" name="items_id" value="{{ $row->items_id }}">

                            {{-- 보여줄 제품명 --}}
                            <input type="text" name="item_name"
                                   class="form-control form-control-sm"
                                   value="{{ $row->item_name }}" readonly>

                            {{-- 제품찾기 버튼 --}}
                            <button type="button" class="btn find-item-btn"
                                    onclick="find_item();">제품찾기</button>
                        </div>

                        @error('items_id')
                            <span class="mt-1 d-block text-danger">{{ $message }}</span>
                        @enderror
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
                        <input type="text" name="numo" class="form-control form-control-sm"
                               value="{{ $row->numo }}" onchange="cal_prices();">
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
function cal_prices(){
    form1.prices.value = Number(form1.price.value) * Number(form1.numo.value);
    form1.bigo.focus();
}

// 제품 찾기 팝업
function find_item(){
    window.open("{{ route('finditem.index') }}", "",
        "resizable=yes,scrollbars=yes,width=500,height=600");
}

$(function(){
    $('#writeday').datetimepicker({
        locale: 'ko',
        format: 'YYYY-MM-DD',
        defaultDate: moment()
    });
});
</script>

@endsection