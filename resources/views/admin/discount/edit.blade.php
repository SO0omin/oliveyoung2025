@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">할인 이벤트 수정</h3>

<script>
    $(function(){
        // DateTimePicker 초기화
        $('#start_at-picker').datetimepicker({
            locale: 'ko',
            format: 'YYYY-MM-DD',
        });

        $('#end_at-picker').datetimepicker({
            locale: 'ko',
            format: 'YYYY-MM-DD',
        });
        
        // 초기 로드 시 할인가를 계산하여 정확성을 확보 (Hidden field로 전달된 경우 필요)
        calcSalePrice();
    });

    /**
     * 할인 퍼센트 입력 → 할인가 자동 계산
     */
    function calcSalePrice(){
        let price = Number(form1.price.value);
        let percent = Number(form1.discount_percent.value);

        // 원가가 0보다 크고 퍼센트가 0 이상일 때만 계산
        if(price > 0 && percent >= 0){
            // Math.floor를 사용하여 소수점 아래는 버림
            let sale = Math.floor(price * (1 - percent / 100));
            form1.sale_price.value = sale;
        } else {
            form1.sale_price.value = price; // 할인율이 유효하지 않으면 원가를 표시하거나 0 처리
        }
    }
</script>

<form name="form1" method="post" action="{{ route('discount.update', $row->id) }}{{ $tmp ?? '' }}" >
@csrf
@method('PATCH')

<div class="table">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 제품명</th>
                <td>
                    {{-- 제품 ID는 hidden으로 전달하고, 이름은 Readonly로 표시 --}}
                    <input type="hidden" name="item_id" value="{{ $row->item_id }}">
                    <input type="text" class="form-control form-control-sm" value="{{ $row->item_name }}" readonly>
                </td>
            </tr>

            <tr>
                <th style="width:20%;">원가</th>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="price" value="{{ $row->price }}" 
                               class="form-control form-control-sm" onchange="calcSalePrice();">
                        <span class="input-group-text">원</span>
                    </div>
                </td>
            </tr>

            <tr>
                <th style="width:20%;">할인 퍼센트 (%)</th>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="discount_percent" value="{{ round($row->discount_percent) }}"
                               class="form-control form-control-sm" onkeyup="calcSalePrice();">
                        <span class="input-group-text">%</span>
                    </div>
                </td>
            </tr>

            <tr>
                <th style="width:20%;">할인가</th>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" name="sale_price" value="{{ $row->sale_price }}" 
                               class="form-control form-control-sm bg-light fw-bold text-danger" readonly>
                        <span class="input-group-text">원</span>
                    </div>
                </td>
            </tr>

            <tr>
                <th style="width:20%;">시작일</th>
                <td>
                    <div class="input-group input-group-sm date" id="start_at-picker">
                        <input type="text" name="start_at" value="{{ \Carbon\Carbon::parse($row->start_at)->format('Y-m-d') }}" 
                               class="form-control form-control-sm">
                        <div class="input-group-text">
                            <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                        </div>
                    </div>
                    @error('start_at')<span class="text-danger small">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">종료일</th>
                <td>
                    <div class="input-group input-group-sm date" id="end_at-picker">
                        <input type="text" name="end_at" value="{{ $row->end_at ? \Carbon\Carbon::parse($row->end_at)->format('Y-m-d') : '' }}" 
                               class="form-control form-control-sm" placeholder="선택 시 종료일 지정">
                        <div class="input-group-text">
                            <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                        </div>
                    </div>
                    @error('end_at')<span class="text-danger small">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">진행 여부</th>
                <td>
                    <select name="is_active" class="form-select form-select-sm">
                        <option value="1" {{ $row->is_active == 1 ? 'selected' : '' }}>진행중</option>
                        <option value="0" {{ $row->is_active == 0 ? 'selected' : '' }}>종료</option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    <button type="submit" class="btn btn-sm btn-primary">
        <i class="fas fa-save me-1"></i> 저장
    </button>
    <button type="button" class="btn btn-sm btn-secondary" onclick="history.back();">
        <i class="fas fa-arrow-left me-1"></i> 이전화면
    </button>
</div>

</form>
@endsection