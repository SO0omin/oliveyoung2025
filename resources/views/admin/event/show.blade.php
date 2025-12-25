@extends('admin.main')
@section('content')

<h3 class="alert mt-3 ctg-admin" role="alert">이벤트 상세 정보</h3>

<div class="table">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;">ID</th>
                <td>{{ $row->id }}</td>
            </tr>
            <tr>
                <th>이벤트 제목</th>
                <td>{{ $row->title }}</td>
            </tr>
            <tr>
                <th>등록일</th>
                <td>{{ $row->created_at ? $row->created_at->format('Y-m-d H:i') : '' }}</td>
            </tr>
            <tr>
                <th>메인 이미지</th>
                <td>
                    @if($row->pic)
                        <img src="{{ asset('storage/event_uploads/'.$row->pic) }}" style="max-width: 200px; height: auto;" class="img-fluid rounded border">
                    @else
                        (이미지 없음)
                    @endif
                </td>
            </tr>
            <tr>
                <th>추가 이미지</th>
                <td>
                    @forelse($row->images as $image)
                        <img src="{{ asset('storage/event_uploads/'.$image->img_path) }}" style="max-width: 150px; max-height: 150px; margin-right: 10px;" class="img-fluid rounded border">
                    @empty
                        <p class="text-muted mb-0">등록된 추가 이미지가 없습니다.</p>
                    @endforelse
                </td>
            </tr>
            <tr>
                <th>관련 상품</th>
                <td>
                    @forelse($row->items as $item)
                        <span class="badge bg-secondary me-2">{{ $item->name }}</span>
                    @empty
                        <p class="text-muted mb-0">연결된 상품이 없습니다.</p>
                    @endforelse
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    <a href="{{ route('admin.event.edit', $row->id) }}{{ $tmp }}" class="btn btn-sm btn-primary" style="color:#fff;">
        <i class="fas fa-edit me-1"></i> 수정
    </a>
    <button type="button" class="btn btn-sm btn-secondary" onclick="history.back();">
        <i class="fas fa-arrow-left me-1"></i> 목록으로
    </button>
</div>

@endsection