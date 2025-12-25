@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">회사 상세</h3>

{{-- 상세 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;">번호</th>
                <td>{{ $row->id }}</td>
            </tr>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 이름</th>
                <td>{{ $row->name }}</td>
            </tr>
            <tr>
                <th style="width:20%;">로고</th>
                <td>
                    <b> 파일이름</b> : {{ $row->logo ?: '파일 없음' }}<br>
                    <div class="mt-2">
                        @if($row->logo)
                            <img src="{{ asset('/storage/logo/' . $row->logo) }}" 
                                 style="max-width: 200px; height: auto;" 
                                 class="img-fluid img-thumbnail">
                        @else
                            {{-- 로고가 없을 경우, Placeholder 표시 --}}
                            <div class="d-flex align-items-center justify-content-center border bg-light text-muted" 
                                 style="width: 200px; height: 150px; font-size: 0.9rem;">
                                로고 이미지 없음
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    {{-- 수정 버튼 --}}
    <a href="{{ route('company.edit', $row->id) }}{{ $tmp }}" class="btn btn-sm btn-primary" style="color:#fff;">
        <i class="fas fa-edit me-1"></i> 수정
    </a>

    {{-- 삭제 폼 --}}
    <form action="{{ route('company.destroy', $row->id) }}" method="POST" 
          onsubmit="return confirm('삭제할까요 ?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash me-1"></i> 삭제
        </button>
    </form>

    {{-- 이전 화면 버튼 --}}
    <button type="button" class="btn btn-sm btn-secondary" onClick="history.back();">
        <i class="fas fa-arrow-left me-1"></i> 이전화면
    </button>
</div>

@endsection