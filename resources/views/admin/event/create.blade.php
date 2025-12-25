@extends('admin.main')
@section('content')

<h3 class="alert mt-3 ctg-admin" role="alert">이벤트 등록</h3>

<form name="form1" method="post" action="{{ route('admin.event.store') }}" enctype="multipart/form-data">
@csrf

<div class="table">
    <table class="table table-bordered align-middle">
        <tbody>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 이벤트 제목</th>
                <td>
                    <input type="text" name="title" value="{{ old('title') }}" 
                           class="form-control form-control-sm" required>
                    @error('title')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 메인 이미지</th>
                <td>
                    <input type="file" name="pic_file" class="form-control form-control-sm" accept="image/*" required>
                    <small class="text-muted">대표 이벤트 이미지 (필수)</small>
                    @error('pic_file')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">추가 이미지</th>
                <td>
                    {{-- name="additional_files[]"로 다중 파일 업로드를 받습니다. --}}
                    <input type="file" name="additional_files[]" class="form-control form-control-sm" accept="image/*" multiple>
                    <small class="text-muted">이벤트 상세 페이지에 들어갈 이미지들 (선택)</small>
                    @error('additional_files')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">관련 상품</th>
                <td>
                    <button type="button" class="btn btn-sm btn-info mb-2" onclick="find_event_item()">
                        <i class="fas fa-search me-1"></i> 제품 추가/찾기
                    </button>

                    <table id="selected_items_table" class="table table-sm table-bordered mt-2">
                        <thead>
                            <tr>
                                <th style="width:10%;">#</th>
                                <th>제품명</th>
                                <th style="width:10%;">삭제</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (old('item_ids', []) as $itemId)
                                {{-- 이전에 선택했던 상품이 있다면 여기에 표시 --}}
                            @endforeach
                        </tbody>
                    </table>

                    <div id="hidden_item_ids_container">
                        </div>

                    @error('item_ids')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script>
    let item_counter = 1; // 테이블 행 번호를 위한 카운터

    /**
     * 제품 선택 팝업 열기 (기존 할인 이벤트와 유사)
     */
    function find_event_item() {
        // 이 팝업 URL은 별도의 검색/선택 기능을 구현해야 합니다.
        window.open(
            "{{ route('admin.findeventitem.index') }}", 
            "find_event_item_popup",
            "width=600,height=600,resizable=yes,scrollbars=yes"
        );
    }

    /**
     * 팝업 창에서 선택된 상품 정보를 받아와 메인 폼에 추가하는 함수
     * (팝업 창에서 이 함수를 호출해야 합니다. 예: window.opener.add_selected_item(itemId, itemName);)
     */
    function add_selected_item(itemId, itemName) {
        const tableBody = document.querySelector('#selected_items_table tbody');
        const hiddenContainer = document.getElementById('hidden_item_ids_container');

        // 1. 이미 추가된 상품인지 확인
        if (document.getElementById('hidden_item_id_' + itemId)) {
            alert('이미 추가된 제품입니다.');
            return;
        }

        // 2. 테이블 행(UI) 추가
        const newRow = tableBody.insertRow();
        newRow.id = 'row_' + itemId;
        
        newRow.innerHTML = `
            <td>${item_counter++}</td>
            <td>${itemName}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="remove_item(${itemId})">삭제</button>
            </td>
        `;

        // 3. Hidden Field 추가 (서버로 전송될 실제 데이터)
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'item_ids[]';
        hiddenInput.value = itemId;
        hiddenInput.id = 'hidden_item_id_' + itemId;
        hiddenContainer.appendChild(hiddenInput);
    }

    /**
     * 선택된 상품 제거 함수
     */
    function remove_item(itemId) {
        // 1. 테이블 행(UI) 제거
        const row = document.getElementById('row_' + itemId);
        if (row) row.remove();

        // 2. Hidden Field 제거 (서버 전송 데이터)
        const hidden = document.getElementById('hidden_item_id_' + itemId);
        if (hidden) hidden.remove();
        
        // *주의: 행 번호(item_counter) 재정렬 로직은 복잡하므로 생략합니다.
    }
</script>

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