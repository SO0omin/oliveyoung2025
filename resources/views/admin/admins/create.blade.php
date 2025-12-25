@extends('admin.main')
@section('content')

<h3 class="alert mt-3 ctg-admin" role="alert">사용자 추가</h3>

<form id="form_admin" name="form1" method="post" action="{{ route('admins.store') }}{{ $tmp }}">
@csrf

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;" class="table-light">번호</th>
                <td>{{ $next_id }}</td>
            </tr>
            <tr>
                <th class="table-light"><span class="text-danger">*</span> 이름</th>
                <td>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control form-control-sm w-auto" required>
                    @error('name')<span class="mt-1 d-block text-danger small">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th class="table-light"><span class="text-danger">*</span> 아이디</th>
                <td>
                    <div class="input-group input-group-sm w-auto">
                        <input type="text" id="uid" name="uid" value="{{ old('uid') }}" class="form-control" maxlength="20" required>
                        <button type="button" id="check_uid" class="btn mycolor2 text-white">중복체크</button>
                    </div>
                    <span id="check-result" class="mt-1 d-block small @error('uid') text-danger @enderror">
                        @error('uid') {{ $message }} @enderror
                    </span>
                </td>
            </tr>
            <tr>
                <th class="table-light"><span class="text-danger">*</span> 비밀번호</th>
                <td>
                    <input type="password" id="pwd" name="pwd" placeholder="비밀번호" class="form-control form-control-sm w-auto" required>
                </td>
            </tr>
            <tr>
                <th class="table-light"><span class="text-danger">*</span> 비밀번호 확인</th>
                <td>
                    <input type="password" id="pwdConfirm" name="pwd_confirm" placeholder="비밀번호 확인" class="form-control form-control-sm w-auto" required>
                    <span id="pwdError" class="text-danger mt-1 d-block small" style="display:none;"></span>
                </td>
            </tr>
            <tr>
                <th class="table-light">전화번호</th>
                <td>
                    <div class="d-inline-flex gap-1 align-items-center">
                        <input type="text" name="tel1" size="3" maxlength="3" value="{{ old('tel1') }}" class="form-control form-control-sm text-center"> -
                        <input type="text" name="tel2" size="4" maxlength="4" value="{{ old('tel2') }}" class="form-control form-control-sm text-center"> -
                        <input type="text" name="tel3" size="4" maxlength="4" value="{{ old('tel3') }}" class="form-control form-control-sm text-center">
                    </div>
                </td>
            </tr>
            <tr>
                <th class="table-light">등급</th>
                <td>
                    <div class="d-inline-flex gap-3">
                        @php
                            $userType = session('type');
                            $oldType = old('type');
                        @endphp

                        @if($userType == 'super_admin')
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_super" value="super_admin" {{ $oldType == 'super_admin' ? 'checked' : 'checked' }}>
                                <label class="form-check-label" for="type_super">관리자</label>
                            </div>
                        @endif

                        @if($userType == 'super_admin' || $userType == 'company_admin')
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_company" value="company_admin" {{ $oldType == 'company_admin' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_company">회사 관리자</label>
                            </div>
                        @endif

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_staff" value="staff" {{ ($oldType == 'staff' || !$oldType) ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_staff">스탭</label>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center gap-2 mt-3 mb-5">
    <button type="submit" class="btn btn-sm btn-primary px-4">
        <i class="fas fa-save me-1"></i> 저장
    </button>
    <button type="button" class="btn btn-sm btn-secondary px-4" onClick="history.back();">
        <i class="fas fa-arrow-left me-1"></i> 이전화면
    </button>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let isUidChecked = false;

    const form = document.getElementById('form_admin');
    const uidInput = document.getElementById('uid');
    const checkBtn = document.getElementById('check_uid');
    const uidMsg = document.getElementById('check-result');
    const pwdInput = document.getElementById('pwd');
    const pwdConfirmInput = document.getElementById('pwdConfirm');
    const pwdError = document.getElementById('pwdError');

    // 1. 아이디 입력 시 상태 리셋
    uidInput.addEventListener('input', () => {
        isUidChecked = false;
        uidMsg.textContent = '아이디 중복 체크가 필요합니다.';
        uidMsg.className = 'mt-1 d-block small text-warning';
    });

    // 2. 아이디 중복 체크 (Fetch API)
    checkBtn.addEventListener('click', function() {
        const uid = uidInput.value.trim();
        if (!uid) {
            alert('아이디를 입력해주세요.');
            uidInput.focus();
            return;
        }

        fetch("{{ route('admins.check_id') }}?uid=" + encodeURIComponent(uid))
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    uidMsg.textContent = '이미 사용 중인 아이디입니다.';
                    uidMsg.className = 'mt-1 d-block small text-danger';
                    isUidChecked = false;
                } else {
                    uidMsg.textContent = '사용 가능한 아이디입니다.';
                    uidMsg.className = 'mt-1 d-block small text-success';
                    isUidChecked = true;
                }
            })
            .catch(err => {
                console.error(err);
                alert('중복 체크 중 에러가 발생했습니다.');
            });
    });

    // 3. 폼 제출 시 통합 유효성 검사
    form.addEventListener('submit', (e) => {
        // 아이디 중복 체크 여부 확인
        if (!isUidChecked) {
            e.preventDefault();
            alert('아이디 중복 체크를 완료해주세요.');
            uidInput.focus();
            return false;
        }

        // 비밀번호 일치 확인
        if (pwdInput.value !== pwdConfirmInput.value) {
            e.preventDefault();
            pwdError.textContent = '비밀번호가 일치하지 않습니다.';
            pwdError.style.display = 'block';
            pwdConfirmInput.focus();
            return false;
        } else {
            pwdError.style.display = 'none';
        }
    });
});
</script>

@endsection