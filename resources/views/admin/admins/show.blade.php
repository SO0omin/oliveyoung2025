@extends('admin.main')
@section('content')

{{-- PHP 로직을 Blade @php 블록으로 이동 및 스타일 반영 --}}
<?php
    // 전화번호 포맷팅
    $tel = $row->tel;
    if (strlen($row->tel) == 11) {
        $tel = substr($row->tel, 0, 3) . "-" . substr($row->tel, 3, 4) . "-" . substr($row->tel, 7, 4);
    } elseif (strlen($row->tel) == 10) {
        $tel = substr($row->tel, 0, 3) . "-" . substr($row->tel, 3, 3) . "-" . substr($row->tel, 6, 4);
    }

    // 등급 이름 설정
    $type = '회사 직원';
    if($row->type == 'super_admin'){
        $type='관리자';
    } else if ($row->type == 'company_admin'){
        $type='회사 관리자';
    }
?>

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">사용자 상세</h3>

{{-- 상세 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                {{-- [최종 수정] mycolor2 클래스 완전히 제거 --}}
                <th style="width:20%;">번호</th>
                <td>{{ $row->id }}</td>
            </tr>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 이름</th>
                <td>{{ $row->name }}</td>
            </tr>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 아이디</th>
                <td>{{ $row->uid }}</td>
            </tr>
            <tr>
                <th style="width:20%;">전화</th>
                <td>{{ $tel }}</td>
            </tr>
            <tr>
                <th style="width:20%;">등급</th>
                <td>{{ $type }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    {{-- 수정 버튼: btn-primary --}}
    <a href="{{ route('admins.edit', $row->id) }}{{ $tmp }}" class="btn btn-sm btn-primary" style="color:#fff;">
        <i class="fas fa-edit me-1"></i>수정
    </a>

    {{-- 삭제 폼: btn-danger --}}
    <form action="{{ route('admins.destroy', $row->id) }}" method="POST" 
          onsubmit="return confirm('삭제할까요 ?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash me-1"></i>삭제
        </button>
    </form>

    {{-- 이전 화면 버튼: btn-secondary --}}
    <button type="button" class="btn btn-sm btn-secondary" onClick="history.back();">
        <i class="fas fa-arrow-left me-1"></i>이전화면
    </button>
</div>

@endsection