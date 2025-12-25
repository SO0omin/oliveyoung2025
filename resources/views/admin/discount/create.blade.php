@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">할인 이벤트 등록</h3>

<script>
    /**
     * 제품 선택 팝업 열기
     */
    function find_item() {
        window.open(
            "{{ route('finditem.index') }}",
            "finditem_popup",
            "width=500,height=600,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,status=no"
        );
    }

    /**
     * 할인 퍼센트 입력 시 할인가 자동 계산 및 업데이트
     */
    function update_discount() {
        // prices 필드 (원가)에서 값을 가져옵니다.
        let price = Number(form1.prices.value); 
        let discount_percent = Number(form1.discount_percent.value);

        if (price > 0 && discount_percent > 0) {
            // 할인가 = 원가 * (1 - 할인율/100)
            let sale = Math.round(price * (1 - (discount_percent / 100)));
            form1.sale_price.value = sale;
        } else {
            // 퍼센트가 0이거나 유효하지 않으면 원가 그대로 (또는 0 처리)
            form1.sale_price.value = price > 0 ? price : 0; 
        }
    }
    
    /**
     * 시작일/종료일 기준으로 '진행 여부' 상태를 실시간 업데이트
     * status: 1=진행중, 2=미시작(예정), 0=종료
     */
    function update_status() {
        let start_at = document.form1.start_at.value;
        let end_at   = document.form1.end_at.value;
        let today    = moment().format("YYYY-MM-DD"); // 오늘 날짜 (YYYY-MM-DD)

        // 날짜 필드가 모두 비어있으면 함수 종료
        if (!start_at) { 
            // 시작일이 없으면 미지정 상태이므로, 강제 종료 방지
            document.getElementById('is_active_view').value = 1; // 기본값 유지 또는 적절한 상태 설정
            document.getElementById('is_active').value = 1;
            return;
        }

        let status = 1; // 기본값: 진행중

        // 1. 시작일(start_at)이 오늘보다 미래라면 -> 미시작(예정)
        if (moment(start_at).isAfter(today)) {
            status = 2; // 미시작 (2)
        } 
        // 2. 종료일(end_at)이 있고, 종료일이 오늘보다 과거라면 -> 종료
        else if (end_at && moment(end_at).isBefore(today)) {
            status = 0; // 종료 (0)
        } 
        // 3. 그 외의 경우 (시작일이 오늘이거나 과거이고, 종료일이 오늘이거나 미래인 경우) -> 진행중
        else {
             status = 1; // 진행중 (1)
        }

        // 뷰와 히든 필드에 상태 값 업데이트
        document.getElementById('is_active_view').value = status;
        document.getElementById('is_active').value = status;
    }


    $(function(){
        // DateTimePicker 초기화 및 이벤트 연결 (start_at-picker, end_at-picker)
        $('#start_at-picker, #end_at-picker').datetimepicker({
            locale: 'ko',
            format: 'YYYY-MM-DD',
            defaultDate: moment(),
            widgetPositioning: { vertical: 'bottom' } // 달력 위치 강제
        });
        
        // ★★★ 날짜가 변경될 때마다 update_status 함수 호출 ★★★
        // dp.change는 DateTimePicker의 날짜 선택이 완료되었을 때 발생
        $('#start_at-picker, #end_at-picker').on('dp.change', update_status); 

        // 사용자가 직접 입력했을 때도 상태 업데이트 (keyup 대신 change/blur 권장)
        $('input[name="start_at"], input[name="end_at"]').on("change", update_status); 

        // 페이지 로드 시 초기 상태 한번 업데이트
        update_status();
    });
</script>

<form name="form1" method="post" action="{{ route('discount.store') }}{{ $tmp ?? '' }}">
@csrf

<div class="table">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 제품명</th>
                <td>
                    <div class="input-group input-group-sm">
                        {{-- hidden 필드: DB에 저장될 ID와 원가 --}}
                        <input type="hidden" name="item_id" value="{{ old('item_id') }}">
                        <input type="hidden" name="prices" value="{{ old('prices') }}">
                        
                        {{-- item_name: 팝업에서 선택된 제품 이름 표시 --}}
                        <input type="text" name="item_name" class="form-control form-control-sm bg-white" 
                               value="{{ old('item_name') }}" readonly placeholder="제품을 선택하세요">
                        
                        <button type="button" class="btn btn-secondary" onclick="find_item();">
                            <i class="fas fa-search me-1"></i> 제품찾기
                        </button>
                    </div>
                    @error('item_id')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">원가</th>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" name="price" value="{{ old('price') }}" 
                               class="form-control form-control-sm bg-light" readonly>
                        <span class="input-group-text">원</span>
                    </div>
                </td>
            </tr>

            <tr>
                <th style="width:20%;">할인 퍼센트 (%)</th>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="discount_percent" value="{{ old('discount_percent') }}" 
                               class="form-control form-control-sm"
                               onkeyup="update_discount();">
                        <span class="input-group-text">%</span>
                    </div>
                    @error('discount_percent')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">할인가</th>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" name="sale_price" value="{{ old('sale_price') }}" 
                               class="form-control form-control-sm bg-light fw-bold text-danger" readonly>
                        <span class="input-group-text">원</span>
                    </div>
                </td>
            </tr>

           <tr>
                <th style="width:20%;">시작일</th>
                <td>
                    {{-- ID 변경 및 input-group-addon 제거 --}}
                    <div class="input-group input-group-sm date" id="start_at-picker">
                        <input type="text" name="start_at" value="{{ old('start_at') }}" 
                               class="form-control form-control-sm">
                        <div class="input-group-text">
                            <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                        </div>
                    </div>
                    @error('start_at')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">종료일</th>
                <td>
                    {{-- ID 변경 및 input-group-addon 제거 --}}
                    <div class="input-group input-group-sm date" id="end_at-picker">
                        <input type="text" name="end_at" value="{{ old('end_at') }}" 
                               class="form-control form-control-sm">
                        <div class="input-group-text">
                            <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                        </div>
                    </div>
                    @error('end_at')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th style="width:20%;">진행 여부</th>
                <td>
                    <select name="is_active_view" id="is_active_view" class="form-select form-select-sm" disabled>
                        <option value="2">미시작</option>
                        <option value="1">진행중</option>
                        <option value="0">종료</option>
                    </select>

                    <input type="hidden" name="is_active" id="is_active" value="{{ old('is_active', 1) }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    <button type="submit" class="btn btn-sm btn-success">
        <i class="fas fa-save me-1"></i> 저장
    </button>
    <button type="button" class="btn btn-sm btn-secondary" onclick="history.back();">
        <i class="fas fa-arrow-left me-1"></i> 이전화면
    </button>
</div>

</form>
@endsection