@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">할인 이벤트 상세</h3>

{{-- 상세 테이블 --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 제품명</th>
                <td>{{ $row->item_name }}</td>
            </tr>

            {{-- ------------------------- 할인 정보 ------------------------- --}}
            <tr>
                <th style="width:20%;">할인 가격</th>
                <td class="text-center text-danger" style="font-size:15px;">
                    {{ number_format($row->sale_price) ?? '-' }}원
                </td>
            </tr>
            <tr>
                <th style="width:20%;">할인율 (%)</th>
                <td>
                    @if ($row->discount_percent)
                        <span class="badge bg-danger" style="font-size:15px;">{{ round($row->discount_percent) }}% 할인</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th style="width:20%;">할인 시작일</th>
                <td>
                   {{ \Carbon\Carbon::parse($row->start_at)->format('Y-m-d') }}
                </td>
            </tr>
            <tr>
                <th style="width:20%;">할인 종료일</th>
                <td>
                    @if ($row->end_at)
                        {{ \Carbon\Carbon::parse($row->end_at)->format('Y-m-d') }}
                    @else
                        <span class="text-muted">무기한</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th style="width:20%;">진행 상태</th>
                <td>
                    @if($row->is_active == 1)
                            <span class="badge bg-success" style="font-size:15px;">진행중</span>
                    @elseif($row->is_active == 0)
                        <span class="badge bg-secondary" style="font-size:15px;">종료</span>
                    @else
                        <span class="badge bg-secondary" style="font-size:15px;">미시작</span>
                    @endif
                </td>
            </tr>
            {{-- ------------------------------------------------------------- --}}
        </tbody>
    </table>
</div>

{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    {{-- 수정 버튼 --}}
    <a href="{{ route('discount.edit', $row->id) }}{{ $tmp ?? '' }}" class="btn btn-sm btn-primary" style="color:#fff;">
        <i class="fas fa-edit me-1"></i> 수정
    </a>

    {{-- 삭제 폼 --}}
    <form action="{{ route('discount.destroy', $row->id) }}" method="POST" 
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