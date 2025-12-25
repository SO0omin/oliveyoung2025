@extends('admin.main')
@section('content')
    <div class="container-fluid">
        <h2>캐러셀 등록</h2>
        <form method="POST" action="{{ route('carousels.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="title" class="form-label">제목</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">내용</label>
                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">1. 연결 링크 유형 선택</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input link-type-radio" type="radio" name="link_type" id="linkTypeMain" value="main" checked>
                        <label class="form-check-label" for="linkTypeMain">메인 페이지</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input link-type-radio" type="radio" name="link_type" id="linkTypeCategory" value="category">
                        <label class="form-check-label" for="linkTypeCategory">카테고리 페이지</label>
                    </div>
                </div>
            </div>

            {{-- ------------------------------------------------------------------------------------------------- --}}
            {{-- 2. 카테고리 선택 영역 (JavaScript로 토글) --}}
            <div id="categorySelectionArea" style="display: none;" class="mb-3 p-3 border rounded bg-light">
                
                <label for="category_id_select" class="form-label">대분류 선택:</label>
                <select name="category_id_select" id="category_id_select" class="form-control mb-3">
                    <option value="">-- 대분류를 선택하세요 --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                

                <label for="sub_category_id_select" class="form-label">중분류 페이지:</label>
                {{-- 1. 중분류 연결 방식 라디오 버튼 --}}
                <div class="form-check form-check-inline">
                    <input class="form-check-input sub-link-type-radio" type="radio" 
                        name="sub_select_type" id="subSelectAll" value="all">
                    <label class="form-check-label" for="subSelectAll">선택</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input sub-link-type-radio" type="radio" 
                        name="sub_select_type" id="subSelectSpecific" value="none" checked>
                    <label class="form-check-label" for="subSelectSpecific">선택 안함</label>
                </div>
            </div>
            {{-- ------------------------------------------------------------------------------------------------- --}}

            {{-- 3. 최종 URL을 저장할 숨겨진 필드 (컨트롤러에 전송) --}}
            <input type="hidden" name="link_url" id="final_link_url" value="{{ old('link_url', $row->link_url ?? '') }}">

            {{-- 4. 현재 조합된 URL을 보여주는 미리보기 (선택 사항) --}}
            <div class="alert alert-info mt-3" role="alert">
                **미리보기 URL:** <span id="urlPreview">메인 페이지</span>
            </div>

            <div class="mb-3">
                <label for="event_id" class="form-label">연결 이벤트</label>
                <select name="event_id" id="event_id" class="form-control @error('event_id') is-invalid @enderror">
                    <option value="">-- 이벤트 선택 안 함 --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
                    @endforeach
                </select>
                @error('event_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="mb-3">
                <label for="pic_file" class="form-label">이미지 파일 (필수)</label>
                <input type="file" name="pic_file" id="pic_file" class="form-control @error('pic_file') is-invalid @enderror">
                @error('pic_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">저장</button>
            <a href="{{ route('carousels.index') }}{{ $tmp }}" class="btn btn-secondary">목록</a>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 이 페이지는 등록 페이지이므로 $row->link_url 값은 없습니다.
            const categoriesData = @json($categories->keyBy('id')); 
            const finalLinkUrl = document.getElementById('final_link_url');
            const urlPreview = document.getElementById('urlPreview');
            const linkTypeRadios = document.querySelectorAll('.link-type-radio');
            const categorySelectionArea = document.getElementById('categorySelectionArea');
            const categorySelect = document.getElementById('category_id_select');
            const subLinkTypeRadios = document.querySelectorAll('.sub-link-type-radio');

            // ----------------------------------------------------------------
            // 1. 최종 URL 조합 및 업데이트 (라디오 버튼 기반)
            // ----------------------------------------------------------------
            function updateFinalUrl() {
                // 초기에는 'main'이 checked 되어 있으므로 이 로직은 항상 작동합니다.
                const selectedTypeRadio = document.querySelector('.link-type-radio:checked');
                if (!selectedTypeRadio) return; 

                const selectedType = selectedTypeRadio.value;
                let url = '';
                let previewText = '';

                if (selectedType === 'main') {
                    url = '/';
                    previewText = '메인 페이지';
                } else if (selectedType === 'category') {
                    const cId = categorySelect.value;
                    const subTypeRadio = document.querySelector('.sub-link-type-radio:checked');
                    const subType = subTypeRadio ? subTypeRadio.value : ''; // 'all' 또는 'none'
                    
                    if (cId) {
                        if (subType === 'all') {
                            // 💡 "선택" (all) = 대분류 전체 페이지 URL
                            url = `categories/${cId}/`; 
                            previewText = categoriesData[cId].name + ' (전체 하위 페이지)';
                        } else if (subType === 'none') {
                            // 💡 "선택 안함" (none) = 중분류 ID 없이 대분류만
                            url = `categories/${cId}`; 
                            previewText = categoriesData[cId].name + ' (대분류만)';
                        } else {
                            url = '';
                            previewText = '중분류 연결 방식을 선택해야 합니다.';
                        }
                    } else {
                        url = ''; 
                        previewText = '대분류를 선택해야 합니다.';
                    }
                }

                finalLinkUrl.value = url;
                urlPreview.textContent = url || previewText;
            }

            // ----------------------------------------------------------------
            // 2. 이벤트 리스너 연결
            // ----------------------------------------------------------------
            
            // 대분류 변경 시 URL 업데이트
            categorySelect.addEventListener('change', updateFinalUrl);
            
            // 중분류 라디오 버튼 변경 시 URL 업데이트
            subLinkTypeRadios.forEach(radio => {
                radio.addEventListener('change', updateFinalUrl);
            });

            // Link Type 변경 시 영역 토글 및 URL 업데이트 (중복 제거)
            linkTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'category') {
                        categorySelectionArea.style.display = 'block';
                    } else {
                        categorySelectionArea.style.display = 'none';
                    }
                    updateFinalUrl();
                });
            });

            // 💡 [삭제] 존재하지 않는 subCategorySelect 이벤트 리스너 제거
            // subCategorySelect.addEventListener('change', updateFinalUrl); 
            
            // ----------------------------------------------------------------
            // 3. 초기 실행: 기본값 설정 (수정 모드 로직 제거)
            // ----------------------------------------------------------------
            
            // 초기에는 'linkTypeMain'이 checked=true 상태이므로, 이 상태를 반영하여 URL을 설정합니다.
            // categorySelectionArea는 HTML에서 style="display: none;"으로 이미 숨겨져 있습니다.
            updateFinalUrl(); 

        });
    </script>
@endsection