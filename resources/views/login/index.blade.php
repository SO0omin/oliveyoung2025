<!---------------------------------------------------------------------------------------------
	제목 : Django Tutorial (실습용 디자인 HTML)

	소속 : 인덕대학교 컴퓨터소프트웨어학과
	이름 : 교수 윤형태 (2025.01)
---------------------------------------------------------------------------------------------->
@extends('main')
@section('content')
<div class="container mt-5" style="max-width:300px;">
    <div style="display:flex; flex-direction:column; align-items:center;">
        <h4 style="margin-bottom:30px;">로그인</h4>
        <div class="w-100">
            <form id="form_login" method="POST" action="{{ route('login') }}">
                @csrf
                @if(session('error'))
                    <div class="alert alert-danger mx-auto" style="width:100%; max-width:280px; text-align:center;">
                        {{ session('error') }}
                    </div>
                @endif
                <div style="display:flex; flex-direction:column; align-items:center; width:100%;">
                    <div class="input-group-vertical rounded-input" style="width:100%; max-width:280px;">
                        <input type="text" id="uid" name="uid" class="form-control" placeholder="아이디">
                        <input type="password" name="pwd" class="form-control" placeholder="비밀번호">
                    </div>
                </div><br>
                <div style="display:flex; flex-direction:column; align-items:center; width:100%;">
                    <div style="margin-top:20px; text-align:center;">
                        {!! NoCaptcha::display() !!}
                        @error('g-recaptcha-response')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
            {!! NoCaptcha::renderJs() !!}
        </div>
        <br>
        <div class="text-center mt-3 test-info">
            <b>테스트용 계정</b><br>
            <b>김민준:</b> customer1 / 1234<br>
        </div>
        <div class="form-check">
            <input type="checkbox" id="saveId"> 아이디 저장
        </div>
        <div class="text-center mt-2">
            <button type="submit" form="form_login" class="sender btn btn-sm mycolor2">로그인</button>
        </div>
        <div class="text-center mt-2 mb-3">
            <!-- 카카오톡 로그인 버튼 -->
             <div id="kakao-login-btn">
                    <img src="{{ asset('storage/logo/kakao_login_medium_wide.png')}}" width="350px">
            </div>
        </div>
    </div>
</div>
<!-- 카카오 JS SDK 로드 -->
<script src="https://developers.kakao.com/sdk/js/kakao.js"></script>
<script>
    // JS Key 초기화
    Kakao.init('{{ env("KAKAO_JS_KEY") }}');

    // 버튼 클릭 시 카카오 로그인 페이지로 이동
    document.getElementById('kakao-login-btn').addEventListener('click', function() {
        Kakao.Auth.authorize({
            redirectUri: '{{ route("kakao.callback") }}' // HTTP 환경에서도 테스트 가능
        });
    });

    
    document.addEventListener('DOMContentLoaded', () => {
    const uidInput = document.getElementById('uid');
    const saveIdCheckbox = document.getElementById('saveId');
    const form = document.getElementById('form_login');

    // 페이지 로드 시 아이디 불러오기
    if (localStorage.getItem('savedUid')) {
        uidInput.value = localStorage.getItem('savedUid');
        saveIdCheckbox.checked = true;
    }

    // 폼 제출 시 아이디 저장 처리
    form.addEventListener('submit', () => {
        if (saveIdCheckbox.checked) {
            localStorage.setItem('savedUid', uidInput.value);
        } else {
            localStorage.removeItem('savedUid');
        }
    });
});
</script>
@endsection