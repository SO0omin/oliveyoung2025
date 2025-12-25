@extends('admin.main_nomenu')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">📦 이벤트 연결 제품 선택</h3>

<script>
    /**
     * 검색 버튼 또는 Enter 키 입력 시 실행되는 함수
     * 폼 액션을 현재 라우트로 설정하고 제출하여 검색 결과를 갱신합니다.
     */
    function find_text()
    {
        // FindEventItemController의 index 라우트로 설정
        form1.action="{{ route('admin.findeventitem.index')}}";
        form1.submit();
    }

    /**
     * 선택된 제품 정보를 부모 창으로 전달하고 팝업을 닫는 함수
     * (부모 창의 add_selected_item(itemId, itemName) 함수를 호출함)
     * @param {number} itemId - 선택된 제품의 ID
     * @param {string} itemName - 선택된 제품의 이름
     */
    function selectItem(itemId, itemName) {
        if (window.opener && window.opener.add_selected_item) {
            // 부모 창의 add_selected_item 함수 호출 (이벤트 컨트롤러의 JS 로직)
            window.opener.add_selected_item(itemId, itemName);
            self.close(); // 작업 완료 후 팝업 창을 닫습니다.
        } else {
            alert('부모 창을 찾을 수 없거나 데이터 전송 함수(add_selected_item)가 정의되지 않았습니다.');
        }
    }
</script>

<form name="form1" action=""> {{-- 메소드 생략 시 GET --}}

{{-- 검색 UI 섹션 --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-boxes me-1"></i> 제품명</span>
            <input type="text" name="text1" value="{{ $text1 }}" placeholder="찾을 제품 이름을 입력하세요" class="form-control" 
                onKeydown="if (event.keyCode == 13) { find_text(); }"> 
            
            {{-- FindCompany와 동일한 mycolor1 클래스 사용 --}}
            <button class="btn mycolor1" type="button" onClick="find_text();">
                <i class="fas fa-search me-1"></i> 검색
            </button>
        </div>
    </div>
</div>

{{-- 제품 목록 테이블 --}}
<div class="table-responsive">
    <table class="table table-sm table-bordered table-hover align-middle mymargin5">
        <thead class="table-light">
            <tr>
                {{-- FindCompany와 동일한 mycolor2 클래스 사용 --}}
                <th class="mycolor2 text-center" style="width:20%;">ID</th> 
                <th class="mycolor2" style="width:65%;">제품명</th>
                <th class="mycolor2 text-center" style="width:15%;">선택</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $row)
                <tr>
                    <td class="text-center">{{ $row->id }}</td>
                    <td>
                        {{-- 클릭 시 부모 창으로 데이터 전송 (send_company 대신 selectItem 사용) --}}
                        <a href="javascript:selectItem({{ $row->id }} ,'{{ $row->name }}');" 
                           class="text-decoration-none">
                            {{ $row->name }}
                        </a>
                    </td>
                    <td class="text-center">
                        <button type="button" 
                                class="btn btn-sm btn-success" 
                                onclick="selectItem({{ $row->id }}, '{{ $row->name }}')">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-3">검색 결과가 없습니다.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 페이지네이션 --}}
<div class="d-flex justify-content-center mt-3">
    {{ $list -> links('mypagination') }}
</div>

</form>

{{-- 팝업 닫기 버튼 추가 (옵션) --}}
<div class="d-flex justify-content-end mt-3 mb-3">
    <button type="button" class="btn btn-sm btn-secondary" onclick="self.close();">
        <i class="fas fa-times me-1"></i> 닫기
    </button>
</div>

@endsection