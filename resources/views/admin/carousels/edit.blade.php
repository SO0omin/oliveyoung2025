@extends('admin.main')

@section('content')
    <div class="container-fluid">
        <h2>캐러셀 수정: {{ $row->title }}</h2>
        <form method="POST" action="{{ route('carousels.update', $row->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- 제목, 내용, 링크 URL 등 나머지 필드 유지 --}}
            <div class="mb-3">
                <label for="title" class="form-label">제목</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $row->title) }}">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">내용</label>
                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror">{{ old('content', $row->content) }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- ============================================================================== --}}
            {{-- 🔥🔥🔥 링크 URL 선택기 시작 (기존 입력 필드 대체) 🔥🔥🔥 --}}
            {{-- 기존 $row->link_url 값을 JavaScript에서 처리하기 위해 hidden 필드에 초기화 --}}
            <input type="hidden" name="link_url" id="final_link_url" value="{{ old('link_url', $row->link_url ?? '') }}">
            
            <div class="mb-3">
                <label class="form-label fw-bold">1. 연결 링크 유형 선택</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input link-type-radio" type="radio" name="link_type" id="linkTypeMain" value="main">
                        <label class="form-check-label" for="linkTypeMain">메인 페이지</label>
                    </div>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input link-type-radio" type="radio" name="link_type" id="linkTypeCategory" value="category">
                        <label class="form-check-label" for="linkTypeCategory">카테고리 페이지</label>
                    </div>
                </div>
            </div>
            
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

            <div class="alert alert-info mt-3" role="alert">
                **현재 조합 URL:** <span id="urlPreview"></span>
            </div>
            
            {{-- 🔥🔥🔥 링크 URL 선택기 끝 🔥🔥🔥 --}}
            {{-- ============================================================================== --}}


            {{-- 이벤트 ID 선택 부분 --}}
            <div class="mb-3">
                <label for="event_id" class="form-label">연결 이벤트</label>
                <select name="event_id" id="event_id" class="form-control @error('event_id') is-invalid @enderror">
                    <option value="">-- 이벤트 선택 안 함 --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id', $row->event_id) == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
                    @endforeach
                </select>
                @error('event_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- 이미지 파일 수정 부분 --}}
            <div class="mb-3">
                <label class="form-label">현재 이미지</label><br>
                @if($row->pic)
                    <img src="{{ asset('storage/carousel_img/' . $row->pic) }}" style="max-width: 200px;" class="mb-2">
                @else
                    <p>이미지 없음</p>
                @endif
                <input type="file" name="pic_file" id="pic_file" class="form-control @error('pic_file') is-invalid @enderror">
                <small class="form-text text-muted">새 파일을 선택하면 기존 파일이 대체됩니다.</small>
                @error('pic_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">수정 저장</button>
            <a href="{{ route('carousels.index') }}{{ $tmp }}" class="btn btn-secondary">목록</a>
        </form>
    </div>

    {{-- ============================================================================== --}}
    {{-- 🔥🔥🔥 JavaScript 로직 (edit 버전 초기화 기능 추가) 🔥🔥🔥 --}}
    {{-- ============================================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ... (기존 변수 정의 유지) ...
            const categoriesData = @json($categories->keyBy('id')); 
            const finalLinkUrl = document.getElementById('final_link_url');
            const urlPreview = document.getElementById('urlPreview');
            const linkTypeRadios = document.querySelectorAll('.link-type-radio');
            const categorySelectionArea = document.getElementById('categorySelectionArea');
            const categorySelect = document.getElementById('category_id_select');
            const subLinkTypeRadios = document.querySelectorAll('.sub-link-type-radio');
            
            // 💡 [제거] HTML에 존재하지 않는 subCategorySelect 변수는 제거합니다.
            // const subCategorySelect = document.getElementById('sub_category_id_select'); 

            // ----------------------------------------------------------------
            // 1. 최종 URL 조합 및 업데이트 (라디오 버튼 기반)
            // ----------------------------------------------------------------
            function updateFinalUrl() {
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
                            // 💡 "선택 안함" (none) = 특정 중분류를 선택하지 않은 경우 (현재 UI가 중분류 선택을 지원하지 않음)
                            url = `categories/${cId}`; 
                            previewText = categoriesData[cId].name + ' (특정 중분류 페이지는 UI가 없어 생성 불가)';
                            // URL 구조에 따라 'categories/{cId}'로만 남기도록 합니다.
                        } else {
                            url = '';
                            previewText = '중분류 연결 방식을 선택해야 합니다.';
                        }
                    } else {
                        url = ''; 
                        previewText = '대분류를 선택해야 합니다.';
                    }
                }
                
                // 💡 [수정] URL이 비어있으면 previewText를 표시하도록 합니다.
                finalLinkUrl.value = url;
                urlPreview.textContent = url || previewText;
            }

            // ----------------------------------------------------------------
            // 2. 이벤트 연결 (중복 제거 및 정리)
            // ----------------------------------------------------------------
            
            // 대분류 변경 시 URL 업데이트
            categorySelect.addEventListener('change', updateFinalUrl);
            
            // 중분류 라디오 버튼 변경 시 URL 업데이트
            subLinkTypeRadios.forEach(radio => {
                radio.addEventListener('change', updateFinalUrl);
            });

            // Link Type 변경 시 영역 토글 및 URL 업데이트
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

            // 💡 [삭제] 중복된 Link Type 리스너와 존재하지 않는 subCategorySelect 리스너 제거
            // ----------------------------------------------------------------
            // 4. 초기화 (수정 모드 처리)
            // ----------------------------------------------------------------
            const initialUrl = finalLinkUrl.value;

            if (initialUrl) {
                if (initialUrl === '/') {
                    // 1. 메인 페이지 연결인 경우
                    document.getElementById('linkTypeMain').checked = true;
                    categorySelectionArea.style.display = 'none';

                } else if (initialUrl.startsWith('categories/')) {
                    // 2. 카테고리 페이지 연결인 경우
                    document.getElementById('linkTypeCategory').checked = true;
                    categorySelectionArea.style.display = 'block';

                    // URL 분석
                    const parts = initialUrl.split('/').filter(p => p !== ''); 
                    const initialCId = parts[1]; // 대분류 ID

                    categorySelect.value = initialCId;
                    
                    // 💡 [핵심 수정]: URL의 끝이 슬래시(/)로 끝나는지에 따라 중분류 라디오 버튼을 결정합니다.
                    if (initialUrl.endsWith('/')) {
                        // 예: categories/1/ -> "선택" (all)
                        document.getElementById('subSelectAll').checked = true;
                    } else {
                        // 예: categories/1 -> "선택 안함" (none)
                        document.getElementById('subSelectSpecific').checked = true;
                    }

                } else {
                    // 3. 기타 알 수 없는 URL인 경우 (메인으로 설정)
                    document.getElementById('linkTypeMain').checked = true;
                    categorySelectionArea.style.display = 'none';
                }
            } else {
                // URL 값이 아예 없는 경우 (기본값: 메인)
                document.getElementById('linkTypeMain').checked = true;
                categorySelectionArea.style.display = 'none';
            }

            // 💡 [핵심] UI 초기화가 끝난 후 최종 URL을 미리보기에 반영
            updateFinalUrl();

        });
    </script>
@endsection