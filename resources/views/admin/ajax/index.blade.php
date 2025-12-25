@extends('admin.main')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* ... (기존 스타일 유지) ... */
    .accordion-toggle {
        transition: background-color 0.3s ease;
    }
    .accordion-toggle:hover {
        background-color: #f8f9fc;
    }
    .accordion-toggle i.fa-caret-right {
        transition: transform 0.3s ease;
    }
    .accordion-toggle[aria-expanded="true"] i.fa-caret-right {
        transform: rotate(90deg);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <strong class="mt-3 ctg-admin" style="font-size: 30px">카테고리 관리</strong>
    <button class="btn mycolor1" data-bs-toggle="modal" data-bs-target="#editModal" 
            onclick="prepareModal(0, 'category', '카테고리')"><i class="fas fa-plus"></i> 카테고리 추가</button>
</div>

<ul class="nav nav-tabs mb-3" id="categoryTabs" role="tablist">
    @foreach($list as $index => $cat)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $index === 0 ? 'active' : '' }} ctg-admin" 
                id="tab-{{ $cat->id }}" 
                data-bs-toggle="tab" 
                data-bs-target="#content-{{ $cat->id }}" 
                type="button" role="tab">{{ $cat->name }}</button>
    </li>
    @endforeach
</ul>

<div class="tab-content" id="categoryTabContent">
    @foreach($list as $index => $cat)
    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="content-{{ $cat->id }}" role="tabpanel">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            {{-- 1. 제목 (왼쪽 끝) --}}
            <strong class="mt-3 text-secondary" style="font-size: 20px">📦 {{ $cat->name }} 하위 카테고리</strong>
            
            {{-- 2. 버튼 그룹 (오른쪽 끝으로 묶음) --}}
            <div>
                {{-- 삭제 버튼 --}}
                <button class="btn btn-sm btn-danger" 
                        onclick="deleteCategory({{ $cat->id }}, 'category')">
                        {{ $cat->name }} 카테고리 삭제
                </button>
                {{-- 추가 버튼 --}}
                <button class="btn mycolor1 btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                        onclick="prepareModal(0, 'sub', '{{ $cat->name }}의 서브 카테고리', {{ $cat->id }})">
                    <i class="fas fa-plus"></i> 서브 카테고리 추가
                </button>
            </div>
        </div>
        
        <table class="table table-bordered table-hover">
            <thead class="table-secondary">
                <tr>
                    <th width="40%">서브/디테일 항목명</th>
                    <th width="40%">종류</th>
                    <th width="20%">액션</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cat->subCategories as $sub)
                @php
                    // JavaScript 함수에 안전하게 문자열을 전달하기 위해 작은따옴표를 이스케이프합니다.
                    $subNameSafe = addslashes($sub->name); 
                @endphp
                
                {{-- 1. 서브 카테고리 헤더 행 (클릭 가능) --}}
                <tr 
                    data-bs-toggle="collapse" 
                    data-bs-target="#detailCollapse-{{ $sub->id }}" 
                    aria-expanded="false" 
                    aria-controls="detailCollapse-{{ $sub->id }}"
                    style="cursor: pointer;"
                    class="accordion-toggle table-light">
                    <td>
                        <i class="fas fa-caret-right me-2"></i> <strong>{{ $sub->name }}</strong>
                    </td>
                    <td>서브 카테고리</td>
                    <td>
                        {{-- 서브 카테고리 수정 버튼 --}}
                        <button class="btn btn-sm btn-info" 
                                data-bs-toggle="modal" data-bs-target="#editModal"
                                onclick="event.stopPropagation(); prepareModal({{ $sub->id }}, 'sub', '{{ $subNameSafe }}', '{{ $subNameSafe }}')">수정</button>
                        <button class="btn btn-sm btn-danger" 
                                onclick="event.stopPropagation(); deleteCategory({{ $sub->id }}, 'sub')">삭제</button>
                        <button class="btn mycolor1 btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                onclick="event.stopPropagation(); prepareModal(0, 'detail', '{{ $sub->name }}의 디테일', {{ $sub->id }})">
                                <i class="fas fa-plus"></i> 디테일 추가 </button>
                    </td>

                </tr>

                {{-- 2. 디테일 카테고리를 담을 숨겨진 행 --}}
                <tr>
                    <td colspan="3" class="p-0">
                        <div id="detailCollapse-{{ $sub->id }}" class="collapse">
                            
                            @if($sub->detailCategories->isNotEmpty())
                            <table class="table table-bordered table-hover m-0">
                                <tbody>
                                    @foreach($sub->detailCategories as $detail)
                                    <tr>
                                        <td width="40%" style="padding-left: 30px;">{{ $detail->name }}</td>
                                        <td width="40%">디테일 카테고리</td>
                                        <td width="20%">
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" 
                                                    onclick="prepareModal({{ $detail->id }}, 'detail', '디테일 항목', '{{ $detail->name }}')">수정</button>
                                            
                                            <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); deleteCategory({{ $detail->id }}, 'detail')">삭제</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <p class="text-muted p-3 m-0 text-center border-top">등록된 디테일 카테고리가 없습니다.</p>
                            @endif
                        </div>
                    </td>
                </tr>

                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">항목 수정/추가</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="modalForm" method="POST">
                @csrf
                @method('PUT') 
                <input type="hidden" name="id" id="modalId">
                <input type="hidden" name="type" id="modalType">
                <input type="hidden" name="parent_id" id="modalParentId"> 
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">새 항목 이름</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                    <button type="submit" class="btn mycolor1" id="saveButton">저장</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    // 1. 모달 준비 및 데이터 주입 함수 (수정/추가 공용)
    function prepareModal(id, type, labelName, currentNameOrParentId = null) {

        const BASE_URL = '{{ url('/admin/ajax') }}';
        // DOM 요소 캐싱
        const modalTitle = document.getElementById('editModalLabel');
        const form = document.getElementById('modalForm');
        const nameInput = document.getElementById('categoryName');
        const saveButton = document.getElementById('saveButton');
        
        // Hidden 필드 설정 (ID와 타입은 필수)
        document.getElementById('modalId').value = id;
        document.getElementById('modalType').value = type;
        
        // _method 필드와 Parent ID 초기화
        form.querySelector('input[name="_method"]').value = 'POST'; // 기본값 설정
        document.getElementById('modalParentId').value = ''; 
        nameInput.value = '';

        // --- 로직 분기 ---

        if (id > 0) {
            // **수정 모드**
            modalTitle.textContent = `${labelName} 항목 수정`;
            nameInput.value = currentNameOrParentId; // currentNameOrParentId === 현재 항목 이름
            
            form.action = `${BASE_URL}/${type}/${id}`;
            form.querySelector('input[name="_method"]').value = 'PUT';
            saveButton.textContent = '수정';
        } 
        else {
            // **추가 모드**
            modalTitle.textContent = `${labelName} 항목 추가`;
            
            // currentNameOrParentId === 부모 ID
            document.getElementById('modalParentId').value = currentNameOrParentId; 
            
            form.action = `${BASE_URL}/${type}`; 
            saveButton.textContent = '추가';
        }
    }

    // 2. 폼 제출 처리 (수정/추가) - 유지 (jQuery AJAX 사용)

    $('#modalForm').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');

        console.log('AJAX URL:', url);
        console.log('AJAX DATA:', form.serialize());

        $.ajax({
            url: url,
            type: 'POST', 
            data: form.serialize(),
            success: function(res) {
                alert('처리 완료');
                // 모달 닫기
                const modalElement = document.getElementById('editModal');
                const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                modal.hide();
                location.reload(); 
            },
            error: function(xhr) {
                // ... (오류 처리 로직 유지) ...
                let errorMessage = '처리 실패';
                try {
                    const errorJson = JSON.parse(xhr.responseText);
                    if (errorJson.errors) {
                        errorMessage += ": " + Object.values(errorJson.errors).flat().join(', ');
                    } else if (errorJson.message) {
                        errorMessage += ": " + errorJson.message;
                    }
                } catch (e) {
                    errorMessage += " (서버 오류)";
                }
                alert(errorMessage);
            }
        });
    });

    // 3. 삭제 함수 (수정됨)
    function deleteCategory(id, type) {
        // 💡 [추가] BASE_URL을 이 함수 내에서 다시 정의해야 합니다.
        const BASE_URL = '{{ url('/admin/ajax') }}';
        
        if (!confirm("정말 삭제하시겠습니까?")) return;
        $.ajax({
            // BASE_URL을 사용하여 정확한 경로를 생성합니다.
            url: `${BASE_URL}/${type}/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                alert('삭제 완료');
                location.reload(); 
            },
            error: function(xhr) {
                // console.error('삭제 오류:', xhr); // 디버깅용
                alert('삭제 실패: 서버 오류 또는 라우트 문제');
            }
        });
}
</script>

@endsection