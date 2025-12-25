<!---------------------------------------------------------------------------------------------
	제목 : Django Tutorial (실습용 디자인 HTML)

	소속 : 인덕대학교 컴퓨터소프트웨어학과
	이름 : 교수 윤형태 (2025.01)
---------------------------------------------------------------------------------------------->
<!doctype html>
<html lang="kr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>올리브영</title>
  <!-- 파비콘 추가 -->
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
</head>
<body>

<div class="container">

    @yield("content")

</div>

</body>
</html>

