@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">제품 사진 목록 (갤러리)</h3>

<script>
    function find_text()
    {
        form1.action="{{ route('picture.index')}}";
        form1.submit();
    }
    
    // 모달을 열 때 이미지 소스를 설정하는 함수
    function setModalImage(pname, originalPath) {
        document.getElementById('zoomModalLabel').innerText = pname;
        document.getElementsByName('picname')[0].src = originalPath;
    }
</script>

<form name="form1" action=""> {{-- 메소드 생략 시 GET --}}

<div class="row mb-3">
    {{-- 검색 영역 --}}
    <div class="col-12 col-md-4" align="left">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-box me-1"></i> 제품명</span>
            <input type="text" name="text1" value="{{ $text1 }}" placeholder="찾을 이름은?" class="form-control" 
                onKeydown="if (event.keyCode == 13) { find_text(); }"> 
            <button class="btn mycolor1" type="button" onClick="find_text();">
                <i class="fas fa-search"></i> 검색
            </button>
        </div>
    </div>
    <div class="col-12 col-md-8" align="right">
        {{-- 이 페이지는 목록만 보여주므로 추가 버튼은 없습니다. --}}
    </div>
</div>
</form>

{{-- 이미지 갤러리 (카드 스타일 - 이름 잘림 방지) --}}
{{-- row-cols-* 클래스로 화면 크기별 컬럼 수를 지정했습니다. g-3은 그리드 간격입니다. --}}
<div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 mb-3">
    @foreach($list as $row)
    
    @php
        $iname = $row->pic ?: "";
        $pname = $row->name;
        // 썸네일 경로와 원본 경로 설정
        $thumbPath = $iname ? asset('storage/item_img/thumb/' . $iname) : '';
        $originalPath = $iname ? asset('storage/item_img/' . $iname) : '';
    @endphp

    <div class="col">
        {{-- 카드 컨테이너 --}}
        <div class="card border-0 rounded-0 bg-transparent shadow-none"> {{-- 외곽선(border) 제거, 모서리 둥글게 제거 --}}
            <a href="javascript:void(0);" 
            onclick="setModalImage('{{ $pname }}', '{{ $originalPath }}')"
            data-bs-toggle="modal" data-bs-target="#zoomModal"
            class="text-decoration-none text-dark d-flex flex-column">

                {{-- 카드 이미지 (썸네일) --}}
                @if($iname)
                    <img src="{{ $thumbPath }}"
                        class="mx-auto d-block" {{-- 중앙 정렬 --}}
                        style="height: 150px; width: 180px; border-radius: 0;"> {{-- 둥글게 제거 --}}
                @else
                    {{-- 이미지가 없을 경우 플레이스홀더 --}}
                    <div class="d-flex align-items-center justify-content-center bg-light text-muted mx-auto"
                        style="height: 150px; width: 180px; flex-shrink: 0; border-radius: 0;">
                        이미지 없음
                    </div>
                @endif

                <div class="p-2 text-center text-truncate-2">
                    <strong class="mb-0" style="font-weight: 500; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word;">
                        {{ $pname }}
                    </strong>
                </div>
            </a>
        </div>
    </div>
    @endforeach
</div>

{{-- 페이지네이션 --}}
<div class="d-flex justify-content-center mt-3">
    {{ $list -> links('mypagination') }}
</div>


{{-- 이미지 확대 모달 --}}
<div class="modal fade" id="zoomModal" tabindex="-1" aria-labelledby="zoomModalLabel"
aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header table-secondary">
                <h5 class="modal-title" id="zoomModalLabel">상품명</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="#" name="picname" class="img-fluid img-thumbnail" style="cursor:pointer"
                data-bs-dismiss="modal">
            </div>
        </div>
    </div>
</div>
@endsection