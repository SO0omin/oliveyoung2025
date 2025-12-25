@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">매입 상세</h3>

{{-- 상세 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;">날짜</th>
                <td>{{ $row->writeday }}</td>
            </tr>
            <tr>
                <th><span class="text-danger">*</span> 제품명</th>
                <td>{{ $row->item_name }}</td>
            </tr>
            <tr>
                <th><span class="text-danger">*</span> 단가</th>
                <td>{{ number_format($row->price) }}</td>
            </tr>
            <tr>
                <th>수량</th>
                <td>{{ number_format($row->numi) }}</td>
            </tr>
            <tr>
                <th>금액</th>
                <td>{{ number_format($row->prices) }}</td>
            </tr>
            <tr>
                <th>비고</th>
                <td>{{ $row->bigo }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3">
    <a href="{{ route('salei.edit', $row->id) }}{{ $tmp }}" class="btn btn-sm btn-primary" style="color: #fff;">
        <i class="fas fa-edit me-1"></i>수정
    </a>

    <form action="{{ route('salei.destroy', $row->id) }}" method="POST" onsubmit="return confirm('삭제할까요 ?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash me-1"></i>삭제
        </button>
    </form>

    <button type="button" class="btn btn-sm btn-secondary" onclick="history.back();">
        <i class="fas fa-arrow-left me-1"></i>이전화면
    </button>
</div>

@endsection