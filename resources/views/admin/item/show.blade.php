@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">제품 상세</h3>

{{-- 1. 제품 기본 정보 --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-light">
        <i class="fas fa-box me-1"></i> 제품 기본 정보
    </div>
    <div class="card-body p-3">
        <table class="table table-bordered table-hover table-striped align-middle mb-0">
            <tbody>
                <tr>
                    <th style="width:20%;">번호</th>
                    <td>{{ $row->id }}</td>
                </tr>
                @if(session('company_id')==1)
                <tr>
                    <th>회사명</th>
                    <td>{{ $row->company_name }}</td>
                </tr>
                @endif
                <tr>
                    <th>대분류</th>
                    <td>{{ $row->category_name }}</td>
                </tr>
                <tr>
                    <th>중분류</th>
                    <td>{{ $row->sub_name }}</td>
                </tr>
                <tr>
                    <th>소분류</th>
                    <td>{{ $row->detail_name }}</td>
                </tr>
                <tr>
                    <th><span class="text-danger">*</span> 제품명</th>
                    <td>{{ $row->name }}</td>
                </tr>
                <tr>
                    <th><span class="text-danger">*</span> 단가</th>
                    <td>{{ number_format($row->price) }}</td>
                </tr>
                <tr>
                    <th>재고</th>
                    <td>{{ number_format($row->jaego) }}</td>
                </tr>
                <tr>
                    <th>대표 사진</th>
                    <td>
                        <b>파일명:</b> {{ $row->pic }}<br>
                        @if($row->pic)
                            <img src="{{ asset('/storage/item_img/' . $row->pic) }}" width="200"
                                 class="img-fluid img-thumbnail my-2 shadow-sm">
                        @else
                            <img src=" " width="200" height="150" class="img-fluid img-thumbnail my-2 shadow-sm">
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- 2. 상세 이미지 관리 --}}
<h4 class="alert mycolor1 mt-4" role="alert">제품 상세 이미지</h4>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-light">
        <i class="fas fa-camera me-1"></i> 상세 이미지 목록 (총 {{ count($row->detailImages) }}개)
    </div>
    <div class="card-body">
        @if($row->detailImages->isEmpty())
            <p class="text-center text-muted">등록된 상세 이미지가 없습니다.</p>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                @foreach($row->detailImages as $detailImage)
                    <div class="col">
                        <div class="card h-100 shadow-sm hover-shadow" style="transition: 0.3s;">
                            <img src="{{ asset('/storage/item_detail_img/' . $detailImage->img_path) }}" 
                                 class="card-img-top" alt="상세 이미지" style="height: 200px; object-fit: cover; cursor: pointer;"
                                 onclick="window.open(this.src)">
                            <div class="card-body p-2">
                                <p class="card-text small text-muted text-truncate mb-1">파일명: {{ $detailImage->img_path }}</p>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-sm btn-info text-white" 
                                            onclick="alert('상세 이미지 수정 기능 구현 필요: ID {{ $detailImage->id }}');">
                                        <i class="fas fa-wrench me-1"></i>수정
                                    </button>
                                    <form action="{{ route('item_detail_image.destroy', $detailImage->id) }}" method="POST" 
                                          onsubmit="return confirm('이 상세 이미지를 삭제하시겠습니까?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times me-1"></i>삭제
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- 3. 신규 상세 이미지 업로드 (수정됨) --}}
<div class="card mb-5 shadow-sm">
    <div class="card-header mycolor1 text-white">
        <i class="fas fa-plus me-1"></i> 새로운 상세 이미지 추가
    </div>
    <div class="card-body">
        <form action="{{ route('item_detail_image.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="item_id" value="{{ $row->id }}">
            <div class="input-group">
                {{-- 🚨 핵심 수정: name="img_path[]"로 변경하고 multiple 속성 추가 --}}
                <input type="file" name="img_path[]" class="form-control form-control-sm" multiple required>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-upload me-1"></i> 이미지 업로드
                </button>
            </div>
            {{-- 에러 처리 시에도 배열 형태로 와야 하므로 name을 img_path.* 로 변경 (컨트롤러에서) --}}
            @error('img_path.*')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
            @error('img_path')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
        </form>
    </div>
</div>

{{-- 4. 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    <a href="{{ route('item.edit', $row->id) }}{{ $tmp }}" class="btn btn-sm btn-primary text-white">
        <i class="fas fa-edit me-1"></i>수정
    </a>

    <form action="{{ route('item.destroy', $row->id) }}" method="POST" 
          onsubmit="return confirm('제품을 완전히 삭제할까요? (상세 이미지도 함께 삭제됩니다.)');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash me-1"></i>제품 삭제
        </button>
    </form>

    <button type="button" class="btn btn-sm btn-secondary" onclick="history.back();">
        <i class="fas fa-arrow-left me-1"></i>이전화면
    </button>
</div>

@endsection