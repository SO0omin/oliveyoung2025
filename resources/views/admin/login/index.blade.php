<!doctype html>
<html lang="kr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>올리브영 관리자 로그인</title>

<link rel="icon" type="image/png" href="{{ url('static/images/favicon.png') }}">
<link   href="{{ asset('static/my/css/bootstrap.min.css')}}" rel="stylesheet">
<link   href="{{ asset('static/my/css/my.css')}}" rel="stylesheet">
<script src="{{ asset('static/my/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{ asset('static/my/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('static/my/js/moment-with-locales.min.js')}}"></script>
<script src="{{ asset('static/my/js/bootstrap5-datetimepicker.min.js') }}"></script>
<link href="{{ asset('static/my/css/bootstrap5-datetimepicker.min.css')}}" rel="stylesheet">
<link href="{{ asset('static/my/css/all.min.css')}}" rel="stylesheet">
<link href="{{ asset('static/my/css/myNew0.css')}}" rel="stylesheet">
<link href="{{ asset('static/my/css/myNew1.css')}}" rel="stylesheet">
<style>
    body {
        background: #f7f9fb;
        font-family: 'GothicA1', sans-serif;
    }

    .login-wrapper {
        max-width: 420px;
        margin: 90px auto;
        animation: fadeIn 0.5s ease-out;
    }

    .login-card {
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 8px 26px rgba(0,0,0,0.08);
    }

    .login-header {
        background: #8cc63f;
        color: white;
        text-align: center;
        padding: 22px 10px;
        font-size: 1.4rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .login-body {
        padding: 15px 25px;
        background: #fcfcfc;
    }

    .form-group {
        margin-bottom: 10px;
    }

    label {
        font-weight: 600;
        font-size: 0.95rem;
        color: #454545;
        margin-bottom: 6px; /* 라벨 아래 간격 */
        display: block;
    }

    .form-control {
        height: 48px;
        border-radius: 10px;
        border-color: #dcdcdc;
        margin-top: 0; /* 인풋 위쪽 간격 제거 */
    }

    .form-control:focus {
        border-color: #8cc63f;
        box-shadow: 0 0 0 0.2rem rgba(140,198,63,0.25);
    }

    .login-button {
        width: 100%;
        height: 50px;
        border-radius: 10px;
        background: #8cc63f;
        color: white;
        font-size: 1.05rem;
        font-weight: 600;
        border: none;
        transition: 0.2s;
    }

    .login-button:hover {
        background: #7bb034;
    }

    .test-info {
        color: #666;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
</head>

<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            관리자 로그인
        </div>

        <div class="login-body">
            <form id="form_login" method="post" action="{{ route('admin.login') }}">
                @csrf

                @if(session('error'))
                    <div class="alert alert-danger text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- 아이디 입력 -->
                <div class="form-group">
                    <label for="uid">아이디</label>
                    <input type="text" id="uid" name="uid" class="form-control" style="height: 40px; width:100%; padding:0 5px;">
                </div>

                <!-- 암호 입력 -->
                <div class="form-group">
                    <label for="pwd">암호</label>
                    <input type="password" id="pwd" name="pwd" class="form-control" style="height: 40px; width:100%; padding:0 5px;">
                </div>
            </form>
        </div>

        <div class="p-3 text-center">
            <button type="button" class="login-button"
                onclick="document.getElementById('form_login').submit();">
                로그인
            </button>
        </div>
    </div>

    <div class="text-center mt-3 test-info">
        💡 <b>테스트용 계정</b><br>
        <b>슈퍼 관리자:</b> admin / 1234<br>
        <b>회사 관리자:</b> companyAdmin / 1234<br>
        <b>회사 스탭:</b> companyStaff / 1234<br>
    </div>
</div>

</body>
</html>