<!---------------------------------------------------------------------------------------------
	제목 : Laravel Framework 실무 (개별프로젝트 실습용 디자인 HTML)

	소속 : 인덕대학교 컴퓨터소프트웨어학과
	이름 : 교수 윤형태 (2023.10.30 - 2025.10.03)
---------------------------------------------------------------------------------------------->
<!doctype html>
<html lang="kr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=1200">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>올리브영 온라인몰</title>
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
<div class="wrapper">
<!-------------------------------------------------------------------------------------------->	
<!--  Header -->
<header class="gojeong">
	<div class="menu_list">
		<ul class="list-unstyled mb-0">
			@if(!session('uid'))
			<li><a href="{{ route('register') }}">회원가입</a></li><li>|</li>
			<li><a href="{{ route('login') }}">로그인</a></li><li>|</li>
			<li><a href="{{ route('login') }}">장바구니</a></li><li>|</li>
			@else
			<li><strong>{{session('grade')}} {{session('name')}}님</strong></li>
			<li><a href="{{ route('logout') }}">로그아웃</a></li><li>|</li>
			<li><a href="{{ route('orders.index') }}">마이페이지</a></li><li>|</li>
			<li><a href="{{ route('cart.index') }}">장바구니(수량)</a></li><li>|</li>
			@endif
			<li><a href="{{ route('admin.login') }}">관리자</a></li><li>|</li>
			<li><a href="{{ route('customer') }}">고객센터</a></li>
		</ul>
	</div>
	<div class="header_inner mt-3 mb-2">
		<div class="col fs-3">
			&nbsp;<a href="{{ url('/') }}"><img src="{{ asset('static/images/logo.png')}}" width="200"></a>
		</div>
		<!--  제품검색은 menu=0 으로 처리 -->
		<div class="col d-flex justify-content-end" style="padding-top:5px;">
			<form method="GET" action="{{ route('goods.search') }}">
			<div class="input-group" style="width: 300px;">
				<input type="text" name="q" class="form-control form-control-sm"
					placeholder="제품명 또는 브랜드명 검색" value="{{ request('q') }}">
				<button class="btn btn-sm btn-outline-secondary" style="font-size:13px;">🔍</button>
			</div>
		</form>
		</div>
	</div>
</header>
<div id="headerWrap">
	<!--  Gnb 메뉴 -->
	<div id="gnb" class="container-fluid row bg-light m-0 p-1 fs-6 border">
		<div class="col gojeong">
			<div class="d-flex">
				<ul class="nav me-auto">
					<li class="my-ctg-nav nav-item zoom_a"><button id="btnAllMenu" class="nav-link nemo">
						<img src="{{ asset('storage/logo/menu-black.png') }}" id="btnIcon" class="me-1" style="width:20px;">
						카테고리
					</button></li>
					<li class="nav-item zoom_a"><a class="my-nav nav-link nemo" href="{{ route('goods.rank') }}"> 랭킹 </a></li>
					<li class="nav-item zoom_a"><a class="my-nav nav-link nemo" href="{{ route('goods.discount') }}"> 세일 </a></li>
					<li class="nav-item zoom_a"><a class="my-nav nav-link nemo" href="{{ route('event.index') }}"> 이벤트 </a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- 펼치는 AllMenu (여기 위치가 정답) -->
    <div id="gnbAllMenu" class="gojeong gnb-menu" style="display:none;">
        <div class="gnb-menu-box">
            @foreach ($categories as $c)
                <div class="category"> 
                    <a href="{{ route('category.index', $c->id) }}">
						<strong>{{ $c->name }} ></strong>
					</a><br>
                    @foreach ($c->subCategories as $s)
                        <a href="{{ route('category.sub', [$c->id, $s->id]) }}">
							{{ $s->name }}
						</a><br>
                    @endforeach
                </div> 
            @endforeach
        </div>
    </div>
<script>
$(document).ready(function(){
    $("#btnAllMenu").click(function(){

        $(this).toggleClass("active");   // 버튼 색 토글
        $("#gnbAllMenu").toggle();       // 메뉴 열기/닫기

        let icon = $("#btnIcon");

        // 이미지 교체
        if($(this).hasClass("active")) {
            icon.attr("src", "{{asset('/storage/logo/menu-white.png')}}");  
        } else {
            icon.attr("src", "{{asset('/storage/logo/menu-black.png')}}");
        }
    });
});
</script>
</div>
<main>
<!-------------------------------------------------------------------------------------------->	
<!-- 시작 : 다른 웹페이지 삽입할 부분 -->
<!-------------------------------------------------------------------------------------------->	
@yield('content')
<!-------------------------------------------------------------------------------------------->	
<!-- 끝 : 다른 웹페이지 삽입할 부분 -->
<!-------------------------------------------------------------------------------------------->	
</main>
<!-- 화면 하단 (main_bottom) : 회사소개 -->
<hr class="m-0">
<footer class="container-fluid bg-light">
<div class="gojeong">
	<a href="{{ url('/') }}">
        <img src="{{ asset('static/images/logo.png') }}" style="height:25px;">
    </a>

    <div class="mt-3" style="font-size:12px; line-height:16px;">
        상호: 올리브영 | 대표 : 정수민 | 사업자 등록번호 : 123-12-123345<br>
        주소 : 21424 서울 노원구 초안산로 인덕대학교 | 전화 : 010-1111-2222 | Fax : 02-3333-4444<br>
        <br>
        Copyright © 2022 www.induk.ac.kr &nbsp; All Rights Reserved.
    </div>
</div>
</footer>
<br>

<!-------------------------------------------------------------------------------------------->	
</div>
</body>
</html>

<!-- Login Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">

			<div class="modal-header mycolor1">
				<h5 class="modal-title" id="exampleModalLabel">로그인</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body bg-light">
			
				<form name="form_login" method="post" action="login_check.html">
				
					<table class="table table-borderless my-2">
						<tr>
							<td width="30%" class="bg-light"><h6>아이디</h6></td>
							<td width="70%" class="bg-light"><input type="text" name="uid" class="form-control form-control-sm"></td>
						</tr>
						<tr>
							<td class="bg-light"><h6>암&nbsp;호</h6></td>
							<td class="bg-light"><input type="password" name="pwd" class="form-control  form-control-sm"></td>
						</tr>
					</table>
				
				</form>
				
			</div>

			<div class="modal-footer alert-secondary">
				<button type="button" class="btn btn-sm btn-secondary" 
					onclick="javascript:form_login.submit();">확인</button>
				<button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">닫기</button>
			</div>

		</div>
	</div>
</div>
