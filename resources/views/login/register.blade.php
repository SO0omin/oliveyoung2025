@extends('main')

@section('content')
<div class="container mt-5" style="max-width:400px; margin-bottom: 30px;">
    <div style="display:flex; flex-direction:column; align-items:center;">
        <h4 style="margin-bottom:30px;">회원가입</h4>

        <div class="bg-light p-3 rounded" style="width:100%; max-width:400px;">
            <div class="text-center mt-2 mb-3">
                <div id="kakao-login-btn">
                    <img src="{{ asset('storage/logo/kakao_login_medium_wide.png')}}" width="350px">
                </div>
            </div>

            <script src="https://developers.kakao.com/sdk/js/kakao.js"></script>
            <script>
                if (!Kakao.isInitialized()) {
                    Kakao.init('{{ env("KAKAO_JS_KEY") }}');
                }
                document.getElementById('kakao-login-btn').addEventListener('click', function() {
                    Kakao.Auth.authorize({
                        redirectUri: 'http://gamejigix.induk.ac.kr/~sale48/one/public/kakao/callback'
                    });
                });
            </script>

            <form id="register_form" method="POST" action="{{ route('register') }}">
                @csrf

                {{-- 아이디 --}}
                <div class="mb-3 w-100">
                    <label for="uid" class="form-label">아이디</label>
                    <div class="d-flex flex-column" style="gap: 10px;">
                        <input type="text" id="uid" name="uid" class="form-control" 
                            value="{{ request('uid') }}">
                        <button type="button" id="check_uid" class="btn mycolor2" style="color:#eee">중복체크</button>
                    </div>
                    <small id="uid_check" class="text-danger"></small>
                </div>

                {{-- 비밀번호 --}}
                <div class="mb-3 w-100">
                    <label for="pwd" class="form-label">비밀번호</label>
                    <input type="password" id="pwd" name="pwd" class="form-control">
                </div>

                {{-- 비밀번호 확인 --}}
                <div class="mb-3 w-100">
                    <label for="pwd_confirm" class="form-label">비밀번호 확인</label>
                    <input type="password" id="pwd_confirm" name="pwd_confirmation" class="form-control">
                    <small id="pwd_match" class="text-danger"></small>
                </div>

                {{-- 이름 --}}
                <div class="mb-3 w-100">
                    <label for="name" class="form-label">이름</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ request('name') }}">
                    <small id="name_check" class="text-danger"></small>
                </div>

                {{-- 전화번호 --}}
                <div class="mb-3">
                    <label class="form-label">전화번호</label>
                    <div class="d-flex gap-2">
                        <input type="text" name="tel1" class="form-control" placeholder="010" maxlength="3">
                        <input type="text" name="tel2" class="form-control" placeholder="1234" maxlength="4">
                        <input type="text" name="tel3" class="form-control" placeholder="5678" maxlength="4">
                    </div>
                    <small id="tel_check" class="text-danger"></small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" id="btn_register" class="btn mycolor2" style="color:#eee">회원가입</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const uidInput = document.getElementById('uid');
    const checkBtn = document.getElementById('check_uid');
    const pwdInput = document.getElementById('pwd');
    const pwdConfirmInput = document.getElementById('pwd_confirm');
    const nameInput = document.getElementById('name');
    const btnRegister = document.getElementById('btn_register');

    const uidMsg = document.getElementById('uid_check');
    const pwdMsg = document.getElementById('pwd_match');
    const nameMsg = document.getElementById('name_check'); 
    const telMsg = document.getElementById('tel_check');

    let isUidChecked = false; // 중복체크 확인 변수

    // 아이디 입력 시 중복체크 상태 리셋
    uidInput.addEventListener('input', () => {
        isUidChecked = false;
        uidMsg.textContent = '';
    });

    // 전화번호 숫자만 입력 제한
    document.querySelectorAll('input[name^="tel"]').forEach(input => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            telMsg.textContent = '';
        });
    });

    // 아이디 중복체크
    if(checkBtn) {
        checkBtn.addEventListener('click', () => {
            const uid = uidInput.value.trim();
            if(!uid){
                uidMsg.textContent = '아이디를 입력하세요.';
                return;
            }
            fetch("{{ route('register.check_uid') }}?uid=" + encodeURIComponent(uid))
            .then(res => res.json())
            .then(data => {
                if(data.exists){
                    uidMsg.textContent = '이미 사용 중인 아이디입니다.';
                    uidMsg.className = 'text-danger';
                    isUidChecked = false;
                } else {
                    uidMsg.textContent = '사용 가능한 아이디입니다.';
                    uidMsg.className = 'text-success';
                    isUidChecked = true;
                }
            });
        });
    }

    // 회원가입 버튼 클릭 (최종 검사)
    btnRegister.addEventListener('click', (e) => {
        e.preventDefault();

        // 1. 초기화
        [uidMsg, pwdMsg, nameMsg, telMsg].forEach(m => m.textContent = '');

        // 2. 필수 입력 검사
        let hasError = false;
        const isKakao = uidInput.hasAttribute('readonly');

        if (!uidInput.value.trim()) { uidMsg.textContent = '아이디를 입력하세요.'; hasError = true; }
        else if (!isKakao && !isUidChecked) { alert('아이디 중복체크를 해주세요.'); hasError = true; }

        if (!pwdInput.value) { pwdMsg.textContent = '비밀번호를 입력하세요.'; hasError = true; }
        if (pwdInput.value !== pwdConfirmInput.value) { pwdMsg.textContent = '비밀번호가 일치하지 않습니다.'; hasError = true; }
        if (!nameInput.value.trim()) { nameMsg.textContent = '이름을 입력하세요.'; hasError = true; }

        const t1 = document.querySelector('input[name="tel1"]').value;
        const t2 = document.querySelector('input[name="tel2"]').value;
        const t3 = document.querySelector('input[name="tel3"]').value;
        if (!t1 || !t2 || !t3) { telMsg.textContent = '전화번호를 모두 입력하세요.'; hasError = true; }

        if (hasError) return; // 에러 있으면 서버 안 보냄

        // 3. 서버 전송
        const formData = new FormData(document.getElementById('register_form'));
        fetch("{{ route('register') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                alert('가입과 로그인이 완료되었습니다!');
                window.location.href = "{{ route('main.index') }}";
            } else if(data.errors) {
                // 서버 유효성 에러 처리
                if(data.errors.uid) uidMsg.textContent = data.errors.uid[0];
                if(data.errors.pwd) pwdMsg.textContent = data.errors.pwd[0];
                alert('입력 정보를 다시 확인해주세요.');
            }
        });
    });
});
</script>
@endsection