@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">회사 목록</h3>

<script>
    function find_text()
    {
        // 폼 액션을 현재 라우트로 설정하고 제출
        form1.action="{{ route('company.index')}}";
        form1.submit();
    }
</script>

<form name="form1" action=""> {{-- 메소드 생략 시 GET --}}

<div class="row mb-3">
    {{-- 검색 영역 --}}
    <div class="col-6 col-md-4" align="left">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-building me-1"></i> 회사명</span>
            <input type="text" name="text1" value="{{ $text1 }}" placeholder="찾을 이름은?" class="form-control" 
                onKeydown="if (event.keyCode == 13) { find_text(); }"> 
            <button class="btn mycolor1" type="button" onClick="find_text();">
                <i class="fas fa-search"></i> 검색
            </button>
        </div>
    </div>
    
    {{-- 추가 버튼 영역 --}}
    <div class="col-6 col-md-8" align="right">
        <a href="{{ route('company.create') }}{{ $tmp }}" class="btn btn-sm mycolor1">
            <i class="fas fa-plus me-1"></i> 추가
        </a>
    </div>
</div>
</form>

{{-- 회사 목록 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="table-secondary text-center">
            <tr>
                <th style="width:20%;">번호</th>
                <th style="width:80%;">이름</th>
            </tr>
        </thead>
        <tbody>
            @foreach($list as $row)
                <tr>
                    <td class="text-center">{{ $row->id }}</td>
                    <td>
                        {{-- 클릭 시 상세 페이지로 이동 --}}
                        <a class="link-btn text-decoration-none" href="{{ route('company.show', $row->id)}}{{ $tmp }}">
                            {{ $row->name}}
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- 페이지네이션 --}}
<div class="d-flex justify-content-center mt-3">
    {{ $list -> links('mypagination') }}
</div>

@endsection