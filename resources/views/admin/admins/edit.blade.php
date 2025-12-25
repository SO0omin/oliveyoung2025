@extends('admin.main')
@section('content')

<?php
    // 전화번호 분리 (PHP 로직을 상단에 깔끔하게 배치)
    $tel1 = trim(substr($row->tel, 0, 3));
    $tel2 = trim(substr($row->tel, 3, 4));
    $tel3 = trim(substr($row->tel, 7, 4));
?>

<h3 class="alert mt-3 ctg-admin" role="alert">사용자 정보 수정</h3>

<form id="form_admin" name="form1" method="post" action="{{ route('admins.update', $row->id) }}{{ $tmp }}">
@csrf
@method('PATCH')

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <tbody>
            <tr>
                <th style="width:20%;" class="table-light">번호</th>
                <td>{{ $row->id }}</td>
            </tr>
            <tr>
                <th class="table-light"><span class="text-danger">*</span> 이름</th>
                <td>
                    <input type="text" name="name" value="{{ old('name', $row->name) }}" class="form-control form-control-sm w-auto" required>
                    @error('name')<span class="mt-1 d-block text-danger small">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th class="table-light"><span class="text-danger">*</span> 아이디</th>
                <td>
                    <div class="input-group input-group-sm w-auto">
                        <input type="text" id="uid" name="uid" value="{{ old('uid', $row->uid) }}" class="form-control" maxlength="20" required>
                        <button type="button" id="check_uid" class="btn mycolor2 text-white">중복체크</button>
                    </div>
                    <span id="check-result" class="mt-1 d-block small text-success">현재 아이디를 유지합니다.</span>
                    @error('uid')<span class="text-danger small">{{ $message }}</span>@enderror
                </td>
            </tr>
            <tr>
                <th class="table-light">비밀번호 설정</th>
                <td>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="changePwdCheck">
                        <label class="form-check-label" for="changePwdCheck">비밀번호를 변경하겠습니다.</label>
                    </div>
                </td>
            </tr>
            <tr class="pwdRows" style="display:none;">
                <th class="table-light"><span class="text-danger">*</span> 새 비밀번호</th>
                <td>
                    <input type="password" id="pwd" name="pwd" placeholder="새 비밀번호" class="form-control form-control-sm w-auto">
                </td>
            </tr>
            <tr class="pwdRows" style="display:none;">
                <th class="table-light"><span class="text-danger">*</span> 비밀번호 확인</th>
                <td>
                    <input type="password" id="pwdConfirm" name="pwd_confirm" placeholder="한 번 더 입력" class="form-control form-control-sm w-auto">
                    <span id="pwdError" class="text-danger mt-1 d-block small" style="display:none;"></span>
                </td>
            </tr>
            <tr>
                <th class="table-light">전화번호</th>
                <td>
                    <div class="d-inline-flex gap-1 align-items-center">
                        <input type="text" name="tel1" size="3" maxlength="3" value="{{ old('tel1', $tel1) }}" class="form-control form-control-sm text-center"> -
                        <input type="text" name="tel2" size="4" maxlength="4" value="{{ old('tel2', $tel2) }}" class="form-control form-control-sm text-center"> -
                        <input type="text" name="tel3" size="4" maxlength="4" value="{{ old('tel3', $tel3) }}" class="form-control form-control-sm text-center">
                    </div>
                </td>
            </tr>
            <tr>
                <th class="table-light">등급</th>
                <td>
                    <div class="d-inline-flex gap-3">
                        @php $currentType = old('type', $row->type); @endphp
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_super" value="super_admin" {{ $currentType == 'super_admin' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_super">관리자</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_company" value="company_admin" {{ $currentType == 'company_admin' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_company">회사 관리자</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_staff" value="staff" {{ $currentType == 'staff' ? 'checked' : '' }}>
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
        <i class="fas fa-save me-1"></i> 수정 저장
    </button>
    <button type="button" class="btn btn-sm btn-secondary px-4" onClick="history.back();">
        <i class="fas fa-arrow-left me-1"></i> 이전화면
    </button>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 원본 데이터 저장
    const originalUid = "{{ $row->uid }}";
    let isUidChecked = true; // 초기값은 내 아이디이므로 true

    const form = document.getElementById('form_admin');
    const uidInput = document.getElementById('uid');
    const checkBtn = document.getElementById('check_uid');
    const uidMsg = document.getElementById('check-result');
    
    const pwdCheck = document.getElementById('changePwdCheck');
    const pwdRows = document.querySelectorAll('.pwdRows');
    const pwdInput = document.getElementById('pwd');
    const pwdConfirmInput = document.getElementById('pwdConfirm');
    const pwdError = document.getElementById('pwdError');

    // 1. 아이디 중복 체크 로직
    uidInput.addEventListener('input', () => {
        if (uidInput.value.trim() === originalUid) {
            isUidChecked = true;
            uidMsg.textContent = '현재 아이디를 유지합니다.';
            uidMsg.className = 'mt-1 d-block small text-success';
        } else {
            isUidChecked = false;
            uidMsg.textContent = '아이디 중복 체크가 필요합니다.';
            uidMsg.className = 'mt-1 d-block small text-warning';
        }
    });

    checkBtn.addEventListener('click', () => {
        const uid = uidInput.value.trim();
        if (uid === originalUid) {
            alert('현재 사용 중인 본인의 아이디입니다.');
            return;
        }
        if (!uid) { alert('아이디를 입력해주세요.'); return; }

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
            });
    });

    // 2. 비밀번호 영역 토글 로직
    pwdCheck.addEventListener('change', function() {
        const isVisible = this.checked;
        pwdRows.forEach(row => row.style.display = isVisible ? 'table-row' : 'none');
        if (!isVisible) {
            pwdInput.value = '';
            pwdConfirmInput.value = '';
            pwdError.style.display = 'none';
        }
    });

    // 3. 폼 전송 시 최종 유효성 검사
    form.addEventListener('submit', (e) => {
        // 아이디 체크 확인
        if (!isUidChecked) {
            e.preventDefault();
            alert('아이디 중복 체크를 완료해주세요.');
            uidInput.focus();
            return;
        }

        // 비밀번호 변경 체크 시 일치 확인
        if (pwdCheck.checked) {
            if (!pwdInput.value || !pwdConfirmInput.value) {
                e.preventDefault();
                alert('변경할 비밀번호를 입력해주세요.');
                pwdInput.focus();
                return;
            }
            if (pwdInput.value !== pwdConfirmInput.value) {
                e.preventDefault();
                pwdError.textContent = '새 비밀번호가 일치하지 않습니다.';
                pwdError.style.display = 'block';
                pwdConfirmInput.focus();
                return;
            }
        }
    });
});
</script>

@endsection