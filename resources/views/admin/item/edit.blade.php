@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">제품 수정</h3>

<form name="form1" method="post" action="{{ route('item.update', $row->id) }}{{ $tmp }}" enctype="multipart/form-data">
@csrf
@method('PATCH')

{{-- 상세 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;">번호</th>
                <td>{{ $row->id }}</td>
            </tr>
            @if(session('company_id')==1)
            <tr>
                <th>회사명</th>
                <td>
                    {{ $row->company_name }}
                    <input type="hidden" name="company_id" value="{{ $row->company_id }}">
                </td>
            </tr>
            @else
            <tr>
                <th>회사명</th>
                <td>
                    {{ $row->company_name }}
                    <input type="hidden" name="company_id" value="{{ $row->company_id }}">
                </td>
            </tr>
            @endif
            <tr>
                <th>대분류</th>
                <td>
                    <select id="category" class="form-control form-control-sm">
                        <option value="">대분류 선택</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" 
                                {{-- 현재 상품의 대분류 ID를 선택 상태로 만듭니다. --}}
                                @if(optional(optional($row->detailCategory)->subCategory)->category->id == $cat->id) selected @endif>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>중분류</th>
                <td>
                    <select id="sub_category" class="form-control form-control-sm">
                        <option value="">중분류 선택</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>소분류</th>
                <td>
                    <select id="detail_category" name="detail_category_id" class="form-control form-control-sm">
                        <option value="">소분류 선택</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><span class="text-danger">*</span> 제품명</th>
                <td>
                    <input type="text" name="name" value="{{ $row->name }}" class="form-control form-control-sm">
                    @error('name')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th><span class="text-danger">*</span> 단가</th>
                <td>
                    <input type="text" name="price" value="{{ $row->price }}" class="form-control form-control-sm">
                    @error('price')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th>재고</th>
                <td>
                    <input type="text" name="jaego" value="{{ $row->jaego }}" class="form-control form-control-sm">
                </td>
            </tr>
            <tr>
                <th>사진</th>
                <td>
                    <div class="mb-2">
                        <input type="file" name="pic" class="form-control form-control-sm">
                    </div>
                    
                    <div class="my-2">
                        <b> 파일이름</b> : {{ $row->pic }}
                    </div>
                    
                    @if($row->pic)
                        <img src="{{ asset('/storage/item_img/' . $row->pic)}}" width="200" 
                        class="img-fluid img-thumbnail mymargin5">
                    @else
                        <img src=" " width="200" height="150"
                        class="img-fluid img-thumbnail mymargin5">
                    @endif
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

{{-- JS: 카테고리 동적 로딩 스크립트 --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    
    // 현재 선택된 값 (수정 모드 시 초기값)
    const currentCategoryId = $('#category').val();
    const currentSubId = '{{ optional(optional($row->detailCategory)->subCategory)->id ?? "" }}';
    const currentDetailId = '{{ optional($row->detailCategory)->id ?? "" }}';
    
    /**
     * @function loadCategories
     * AJAX 요청을 통해 하위 카테고리 목록을 로드하고 드롭다운을 업데이트합니다.
     */
    function loadCategories(type, parentId, $targetSelect, currentId) {
        if (!parentId) {
            $targetSelect.html('<option value="">' + (type === 'sub' ? '중분류 선택' : '소분류 선택') + '</option>');
            return;
        }

        let url = '';
        
        // 🚨 URL() 헬퍼를 사용하여 서브디렉토리 경로 (~sale48/one/public) 문제를 해결합니다.
        if (type === 'sub') {
            // 예: http://.../public/admin/get-subcategories/1
            url = '{{ url('admin/get-subcategories') }}' + '/' + parentId;
        } else {
            url = '{{ url('admin/get-detailcategories') }}' + '/' + parentId;
        }

        $.ajax({
            url: url, // 전체 경로가 포함된 URL
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let options = '<option value="">' + (type === 'sub' ? '중분류 선택' : '소분류 선택') + '</option>';
                
                $.each(data, function(key, value) {
                    // 현재 ID와 일치하면 selected 속성 추가
                    let selected = (value.id == currentId) ? 'selected' : '';
                    options += '<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>';
                });
                
                $targetSelect.html(options);
            },
            error: function(xhr) {
                console.error("카테고리 로드 실패:", xhr.responseText);
                $targetSelect.html('<option value="">로딩 오류</option>');
            }
        });
    }

    // --- 이벤트 리스너 설정: 사용자가 값을 변경했을 때 ---

    // 1. 대분류 변경 시 중분류 로드
    $('#category').on('change', function() {
        const categoryId = $(this).val();
        loadCategories('sub', categoryId, $('#sub_category'), '');
        $('#detail_category').html('<option value="">소분류 선택</option>');
    });

    // 2. 중분류 변경 시 소분류 로드
    $('#sub_category').on('change', function() {
        const subcategoryId = $(this).val();
        loadCategories('detail', subcategoryId, $('#detail_category'), '');
    });
    
    // --- 초기 로드 처리: 페이지 로드 시 기존 선택값 로드 ---
    
    // 대분류가 선택되어 있고 중분류 ID가 있으면 중분류 로드
    if (currentCategoryId && currentSubId) {
        loadCategories('sub', currentCategoryId, $('#sub_category'), currentSubId);
    }
    
    // 중분류 ID와 소분류 ID가 있으면 소분류 로드
    // 단, 중분류를 로드하기 위해 대분류의 값이 필요하므로, 이 순서대로 실행됩니다.
    if (currentSubId && currentDetailId) {
        // 중분류가 로드된 후 소분류를 로드해야 하므로,
        // 중분류 로드가 완료된 후 소분류를 로드하는 방식으로 변경될 수 있으나,
        // 현재 로직은 간단히 초기값으로 모든 것을 로드하려고 시도합니다.
        // **(참고: AJAX 비동기 문제로 인해 초기 로드 시 `currentSubId`를 이용한 소분류 로드만으로는 불충분할 수 있습니다. 
        //  실제 운영 환경에서는 중분류 로드 성공 콜백 함수 내에서 소분류 로드를 호출하는 것이 더 안전합니다.)**
        loadCategories('detail', currentSubId, $('#detail_category'), currentDetailId);
    }
});
</script>

@endsection