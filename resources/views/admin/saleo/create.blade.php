@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">매출 등록</h3>

<form name="form1" method="POST" action="{{ route('saleo.store') }}{{ $tmp }}">
    @csrf

    {{-- 테이블 --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <tbody>
                {{-- 날짜 --}}
                <tr>
                    <th style="width:20%;"><span class="text-danger">*</span> 날짜</th>
                    <td>
                        <div class="input-group input-group-sm date" id="writeday">
                            <input type="text" name="writeday" class="form-control form-control-sm" 
                               value="{{ old('writeday') }}">
                            <div class="input-group-text">
                                <span class="input-group-addon"> <i class="far fa-calendar-alt fa-lg"></i></span>
                            </div>
                        </div>
                        @error('writeday')<span class="text-danger d-block mt-1">{{ $message }}</span>@enderror
                    </td>
                </tr>

                {{-- 제품명 --}}
                <tr>
                    <th><span class="text-danger">*</span> 제품명</th>
                    <td>
                       <div class="input-group input-group-sm">
                        <input type="hidden" name="item_id" value="{{ old('item_id') }}">
                        <input type="text" name="item_name" value="{{ old('item_name') }}"
                            class="form-control" readonly>

                        <button type="button" class="btn find-item-btn"
                                onclick="find_item();">
                            제품찾기
                        </button>
                    </div>
                        @error('item_id')<span class="text-danger d-block mt-1">{{ $message }}</span>@enderror
                    </td>
                </tr>

                {{-- 단가 --}}
                <tr>
                    <th>단가</th>
                    <td>
                        <input type="text" name="price" class="form-control form-control-sm"
                               value="{{ old('price') }}" onchange="cal_prices();">
                    </td>
                </tr>

                {{-- 수량 --}}
                <tr>
                    <th>수량</th>
                    <td>
                        <input type="text" name="numo" class="form-control form-control-sm"
                               value="{{ old('numo') }}" onchange="cal_prices();">
                    </td>
                </tr>

                {{-- 금액 --}}
                <tr>
                    <th>금액</th>
                    <td>
                        <input type="text" name="prices" class="form-control form-control-sm"
                               value="{{ old('prices') }}" readonly>
                    </td>
                </tr>

                {{-- 비고 --}}
                <tr>
                    <th>비고</th>
                    <td>
                        <input type="text" name="bigo" class="form-control form-control-sm"
                               value="{{ old('bigo') }}">
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
}

function find_item(){
    window.open("{{ route('finditem.index') }}","",
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