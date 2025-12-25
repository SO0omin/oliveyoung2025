@extends('admin.main_nomenu')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">회사 선택</h3>

<script>
    function find_text()
    {
        // 폼 액션을 현재 라우트로 설정하고 제출
        form1.action="{{ route('findcompany.index')}}";
        form1.submit();
    }

    function send_company(id,name){
        // 팝업을 연 부모 창의 form1에 값 전달 후 팝업 닫기
        opener.form1.company_id.value = id;
        opener.form1.company_name.value = name;
        self.close();
    }
</script>

<form name="form1" action=""> {{-- 메소드 생략 시 GET --}}

{{-- 검색 UI 섹션 --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-user-tie me-1"></i> 회사명</span>
            <input type="text" name="text1" value="{{ $text1 }}" placeholder="찾을 회사 이름을 입력하세요" class="form-control" 
                onKeydown="if (event.keyCode == 13) { find_text(); }"> 
            <button class="btn mycolor1" type="button" onClick="find_text();">
                <i class="fas fa-search me-1"></i> 검색
            </button>
        </div>
    </div>
</div>

{{-- 회사 목록 테이블 --}}
<div class="table-responsive">
    <table class="table table-sm table-bordered table-hover align-middle mymargin5">
        <thead class="table-light">
            <tr>
                <th class="mycolor2 text-center" style="width:20%;">번호</th>
                <th class="mycolor2" style="width:80%;">이름</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $row)
                <tr>
                    <td class="text-center"><a href="javascript:send_company({{ $row->id }} ,'{{ $row->name }}');" 
                           class="text-decoration-none">{{ $row->id }}</a></td>
                    <td><a href="javascript:send_company({{ $row->id }} ,'{{ $row->name }}');" 
                           class="text-decoration-none">
                        {{-- 클릭 시 부모 창으로 데이터 전송 --}}
                            {{ $row->name }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center text-muted">검색 결과가 없습니다.</td>
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
@endsection