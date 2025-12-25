@extends('admin.main')
@section('content')

{{-- 페이지 제목 --}}
<h3 class="alert mt-3 ctg-admin" role="alert">회사 수정</h3>

<form id="form1" name="form1" method="post" action="{{ route('company.update', $row->id) }}{{ $tmp }}" enctype="multipart/form-data">
@csrf
@method('PATCH')

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;">번호</th>
                <td>{{ $row->id }}</td>
            </tr>
            <tr>
                <th style="width:20%;"><span class="text-danger">*</span> 이름</th>
                <td>
                    <div class="d-inline-flex">
                        <input type="text" name="name" size="20" maxlength="20" value="{{ $row->name }}" class="form-control form-control-sm">
                    </div>
                    @error('name')<span class="mt-1 d-block text-danger">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th style="width:20%;">로고</th>
                <td>
                    {{-- 1. 파일 업로드 인풋 --}}
                    <div class="d-inline-flex mb-2">
                        <input type="file" name="logo" class="form-control form-control-sm">
                    </div>
                    
                    {{-- 2. 기존 파일 정보 및 미리보기 --}}
                    <div>
                        <b> 파일 이름</b> : {{ $row->logo ?: '파일 없음' }}
                        <br>
                        <div class="mt-2">
                            @if($row->logo)
                                <img src="{{ asset('storage/logo/' . $row->logo) }}" 
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
                    </div>
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