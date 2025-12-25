@extends('admin.main')
@section('content')

<h3 class="alert mt-3 ctg-admin" role="alert">이벤트 수정</h3>

{{-- 폼 제출 시 PUT/PATCH 메서드를 사용하기 위해 @method('PATCH')를 추가합니다. --}}
<form name="form1" method="post" action="{{ route('admin.event.update', $row->id) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="table">
    <table class="table table-bordered align-middle">
        <tbody>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 이벤트 제목</th>
                <td>
                    {{-- 기존 데이터를 value에 넣습니다. --}}
                    <input type="text" name="title" value="{{ old('title', $row->title) }}" 
                           class="form-control form-control-sm" required>
                    @error('title')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">현재 메인 이미지</th>
                <td>
                    @if($row->pic)
                        <img src="{{ asset('storage/event_uploads/'.$row->pic) }}" style="max-width: 150px; height: auto;" class="img-fluid rounded border mb-2">
                        <small class="d-block text-muted">새 파일을 선택하면 기존 이미지는 삭제됩니다.</small>
                    @else
                        (등록된 이미지 없음)
                    @endif
                    
                    <input type="file" name="pic_file" class="form-control form-control-sm mt-2" accept="image/*">
                    @error('pic_file')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>

            <tr>
                <th style="width:20%;">추가 이미지 관리</th>
                <td>
                    <p class="fw-bold mb-1">현재 이미지:</p>
                    <div id="current_images_container">
                        @forelse($row->images as $image)
                            {{-- 🚩 수정: 삭제 버튼과 wrapper ID 추가 --}}
                            <div id="image_wrapper_{{ $image->id }}" class="d-inline-block border p-1 me-2 mb-2 text-center">
                                <img src="{{ asset('storage/event_uploads/'.$image->img_path) }}" style="max-width: 100px; max-height: 150px;" class="img-fluid rounded">
                                
                                <button type="button" 
                                        class="btn btn-sm btn-danger mt-1" 
                                        onclick="deleteImage({{ $image->id }})">
                                    <i class="fas fa-times me-1"></i> 삭제
                                </button>
                            </div>
                        @empty
                            <p id="no_images_text" class="text-muted mb-0">등록된 추가 이미지가 없습니다.</p>
                        @endforelse
                    </div>
                    
                    <p class="fw-bold mt-3 mb-1">새 이미지 추가:</p>
                    <input type="file" name="additional_files[]" class="form-control form-control-sm" accept="image/*" multiple>
                    <!--<small class="text-muted">추가 이미지는 별도의 삭제/추가 로직이 컨트롤러에 필요합니다.</small>-->
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
                            {{-- 🚩 수정: $row->items 컬렉션을 반복하여 기존 연결 상품을 표시 --}}
                            @php $item_counter = 1; @endphp
                            @foreach ($row->items as $item)
                                <tr id="row_{{ $item->id }}">
                                    <td>{{ $item_counter++ }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="remove_item({{ $item->id }})">삭제</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div id="hidden_item_ids_container">
                        {{-- 🚩 수정: 기존 연결 상품의 ID를 Hidden Field로 생성 --}}
                        @foreach ($row->items as $item)
                            <input type="hidden" 
                                name="item_ids[]" 
                                value="{{ $item->id }}" 
                                id="hidden_item_id_{{ $item->id }}">
                        @endforeach
                    </div>

                    @error('item_ids')<span class="text-danger small mt-1 d-block">{{ $message }}</span>@enderror
                </td>
            </tr>
        </tbody>
    </table>
</div>
  <script>
    // 🚩 수정: 기존에 연결된 상품의 개수 다음 번호로 초기화합니다.
    let item_counter = {{ $row->items->count() }} + 1; 

    /**
     * 제품 선택 팝업 열기
     */
    function find_event_item() {
        window.open(
            "{{ route('admin.findeventitem.index') }}", 
            "find_event_item_popup",
            "width=600,height=600,resizable=yes,scrollbars=yes"
        );
    }

    /**
     * 팝업에서 선택된 상품을 폼에 추가
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
        
        // 주의: 행 번호(item_counter)는 간단하게 증가시키지만, 삭제 후 재정렬은 복잡하므로 단순화합니다.
        newRow.innerHTML = `
            <td>${item_counter++}</td> 
            <td>${itemName}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="remove_item(${itemId})">삭제</button>
            </td>
        `;

        // 3. Hidden Field 추가
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

        // 2. Hidden Field 제거
        const hidden = document.getElementById('hidden_item_id_' + itemId);
        if (hidden) hidden.remove();
    }
    /**
     * 특정 추가 이미지를 Ajax로 삭제하는 함수
     * @param {number} imageId - 삭제할 EventImage의 ID
     */
    function deleteImage(imageId) {
        if (!confirm('이 이미지를 정말 삭제하시겠습니까?')) {
            return;
        }

        // 🚨 CSRF 토큰 확보 (Layout 파일에 meta 태그가 있다고 가정)
        // 만약 없다면, @csrf 지시어로 hidden input에서 값을 가져와야 합니다.
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const requestUrl = `/~sale48/one/public/admin/event/image/${imageId}`; 
        console.log("요청 URL:", requestUrl);

        fetch(requestUrl, { 
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        
        .then(response => {
            if (!response.ok) {
                // 응답이 성공(200-299)이 아닌 경우 에러 처리
                throw new Error('이미지 삭제에 실패했습니다. (HTTP Code: ' + response.status + ')');
            }
            // JSON 응답을 파싱
            return response.json(); 
        })
        .then(data => {
            alert(data.message || '이미지가 성공적으로 삭제되었습니다.');
            
            // UI에서 해당 이미지 wrapper 제거
            const imageWrapper = document.getElementById(`image_wrapper_${imageId}`);
            if (imageWrapper) {
                imageWrapper.remove();
            }

            // 추가: 모든 이미지가 삭제된 경우 '등록된 이미지가 없습니다.' 텍스트 표시
            const container = document.getElementById('current_images_container');
            if (container.children.length === 0) {
                 container.innerHTML = '<p class="text-muted mb-0">등록된 추가 이미지가 없습니다.</p>';
            }
        })
        .catch(error => {
            alert('삭제 중 오류 발생: ' + error.message);
            console.error('Error:', error);
        });
    }
</script>
{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    <button type="submit" class="btn btn-sm btn-primary">
        <i class="fas fa-save me-1"></i> 수정 사항 저장
    </button>
    <button type="button" class="btn btn-sm btn-secondary" onclick="history.back();">
        <i class="fas fa-arrow-left me-1"></i> 이전화면
    </button>
</div>

</form>
@endsection