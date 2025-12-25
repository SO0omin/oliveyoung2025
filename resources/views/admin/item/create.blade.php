@extends('admin.main')
@section('content')
<script>
// 제품 등록 페이지에서는 이 함수들이 사용되지 않지만, 원본 코드에 있으므로 유지합니다.
function select_company(){
    var str;
    str = form1.sel_items_id.value;
    if( str == ""){
        form1.items_id.value = "";
        form1.price.value = "";
        form1.prices.value = "";
    } else {
        str = str.split("^^");
        form1.items_id.value = str[0];
        form1.price.value = str[1];
        // form1.prices.value = Number(str[1])*Number(form1.numo.value); // numo가 없으므로 수정 필요
    }
}
function find_company(){
    window.open("{{ route('findcompany.index') }}","",
    "resizable=yes,scrollbars=yes,width=500,height=600");
}
</script>

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">제품 등록</h3>

<form name="form1" method="post" action="{{ route('item.store') }}{{ $tmp }}" enctype="multipart/form-data">
@csrf

{{-- 상세 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;">번호</th>
                <td>{{ $next_id }}</td>
            </tr>
            @if(session('company_id')==1)
            <tr>
                <th><span class="text-danger">*</span> 회사명</th>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="hidden" name="company_id" value="{{ old('company_id') }}">
                        <input type="text" name="company_name" value="{{ old('company_name') }}" class="form-control form-control-sm" readonly>
                        <button type="button" onclick="find_company();" class="btn find-item-btn">회사찾기</button>
                    </div>
                    @error('company_id')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
                </td>
            </tr>
            @else
            <tr>
                <th>회사명</th>
                <td>{{ session('company_name')}}
                    <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                </td>
            </tr>
            @endif
            <tr>
                <th>대분류</th>
                <td>
                    <select id="category" name="category_id" class="form-select form-select-sm">
                        <option value="">대분류 선택</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>중분류</th>
                <td>
                    <select id="sub_category" name="sub_category_id" class="form-select form-select-sm">
                        <option value="">중분류 선택</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>소분류</th>
                <td>
                    <select id="detail_category" name="detail_category_id" class="form-select form-select-sm">
                        <option value="">소분류 선택</option>
                    </select>
                </td>
            </tr>
            {{-- 카테고리 로딩 스크립트는 테이블 바깥으로 이동해도 무방하나, 원본 위치를 최대한 유지했습니다. --}}
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {

                    const oldCategoryId = "{{ old('category_id') }}";
                    const oldSubId      = "{{ old('sub_category_id') }}";
                    const oldDetailId   = "{{ old('detail_category_id') }}";

                    function loadCategories(type, parentId, $targetSelect, selectedId, callback = null) {
                        if (!parentId) {
                            let defaultText = (type === 'sub' ? '중분류 선택' : '소분류 선택');
                            $targetSelect.html('<option value="">' + defaultText + '</option>');
                            if (callback) callback();
                            return;
                        }

                        let url = (type === 'sub')
                            ? "{{ url('admin/get-subcategories') }}/" + parentId
                            : "{{ url('admin/get-detailcategories') }}/" + parentId;

                        $.ajax({
                            url: url,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                let defaultText = (type === 'sub' ? '중분류 선택' : '소분류 선택');
                                let options = '<option value="">' + defaultText + '</option>';

                                $.each(data, function(key, value) {
                                    let sel = (value.id == selectedId) ? 'selected' : '';
                                    options += `<option value="${value.id}" ${sel}>${value.name}</option>`;
                                });

                                $targetSelect.html(options);

                                if (callback) callback();  // AJAX 끝난 후 실행
                            },
                             error: function(xhr) {
                                console.error("카테고리 로드 실패:", xhr.responseText);
                                $targetSelect.html('<option value="">로딩 오류</option>');
                                if (callback) callback();
                            }
                        });
                    }

                    // ▶ 1) Category old() 있으면 중분류 자동 로딩
                    if (oldCategoryId) {
                        // Note: category select box already set by blade's `old('category_id')` check
                        loadCategories('sub', oldCategoryId, $('#sub_category'), oldSubId, function() {
                            
                            // ▶ 2) Sub old() 있으면 소분류 자동 로딩
                            if (oldSubId) {
                                loadCategories('detail', oldSubId, $('#detail_category'), oldDetailId);
                            }

                        });
                    }

                    // ▶ 사용자 선택 이벤트
                    $('#category').on('change', function() {
                        loadCategories('sub', $(this).val(), $('#sub_category'), '');
                        $('#detail_category').html('<option value="">소분류 선택</option>');
                    });

                    $('#sub_category').on('change', function() {
                        loadCategories('detail', $(this).val(), $('#detail_category'), '');
                    });

                });
            </script>
            <tr>
                <th><span class="text-danger">*</span> 제품명</th>
                <td>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm">
                    @error('name')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th><span class="text-danger">*</span> 단가</th>
                <td>
                    <input type="text" name="price" value="{{ old('price') }}" class="form-control form-control-sm">
                    @error('price')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th>재고</th>
                <td>
                    <input type="text" name="jaego" value="{{ old('jaego') }}" class="form-control form-control-sm">
                </td>
            </tr>
            <tr>
                <th>사진</th>
                <td>
                    <input type="file" name="pic" class="form-control form-control-sm">
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
@endsection