@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">회사 추가</h3>

<form id="form1" name="form1" method="post" action="{{ route('company.store') }}{{ $tmp }}" enctype="multipart/form-data">
@csrf

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;">번호</th>
                <td>{{ $next_id }}</td>
            </tr>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 이름</th>
                <td>
                    <div class="d-inline-flex">
                        <input type="text" name="name" size="20" maxlength="20" value="{{ old('name') }}" class="form-control form-control-sm">
                    </div>
                    @error('name')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th style="width:20%;">로고</th>
                <td>
                    <div class="d-inline-flex">
                        <input type="file" name="logo" class="form-control form-control-sm">
                    </div>
                    {{-- 로고는 필수가 아니므로 @error는 생략 --}}
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- 버튼 그룹 --}}
<div class="d-flex justify-content-center gap-2 mt-3 mb-3">
    <button type="submit" class="btn btn-sm btn-primary">
        <i class="fas fa-save me-1"></i> 저장
    </button>
    <button type="button" class="btn btn-sm btn-secondary" onClick="history.back();">
        <i class="fas fa-arrow-left me-1"></i> 이전화면
    </button>
</div>

</form>
@endsection