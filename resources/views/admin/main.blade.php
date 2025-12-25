<!doctype html>
<html lang="kr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>올리브영</title>

  <link rel="icon" type="image/png" href="{{ url('static/images/favicon.png') }}">
  <link href="{{ asset('static/my/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{ asset('static/my/css/my.css')}}" rel="stylesheet">
  <script src="{{ asset('static/my/js/jquery-3.7.1.min.js')}}"></script>
  <script src="{{ asset('static/my/js/bootstrap.bundle.min.js') }}"></script>

  <script src="{{ asset('static/my/js/moment-with-locales.min.js')}}"></script>
  <script src="{{ asset('static/my/js/bootstrap5-datetimepicker.min.js') }}"></script>
  <link href="{{ asset('static/my/css/bootstrap5-datetimepicker.min.css')}}" rel="stylesheet">
  <link href="{{ asset('static/my/css/all.min.css')}}" rel="stylesheet">
  <link href="{{ asset('static/my/css/myNew0.css')}}" rel="stylesheet">
  <link href="{{ asset('static/my/css/myNew1.css')}}" rel="stylesheet">

  <!-- SB Admin 2 CSS -->
  <link href="{{ asset('static/admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

  <!-- FontAwesome -->
  <link href="{{ asset('static/admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

  <style>
    .navbar-toggler-icon {
        background-image: url('{{ asset('storage/logo/menu-black.png') }}');
    }
  </style>
</head>

<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="min-width:224px;">

        <!-- Sidebar Logo -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center"
           href="{{ route('admin.main') }}">
            <div class="sidebar-brand-icon">
                <img src="{{ asset('storage/logo/'.session('company_name').'.png') }}" width="180">
            </div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Nav Items (메뉴 그대로) -->
        <li class="my-nav-item nav-item"><a class="nav-link" href="{{ route('salei.index') }}">매입</a></li>
        <li class="my-nav-item nav-item"><a class="nav-link" href="{{ route('saleo.index') }}">매출</a></li>
        <li class="my-nav-item nav-item"><a class="nav-link" href="{{ route('gigan.index') }}">기간조회</a></li>


        <!-- 기초정보 -->
        <li class="my-nav-item nav-item">
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBase"
            aria-expanded="false" aria-controls="collapseBase">
                <span>기초정보</span>
            </a>
            <div id="collapseBase" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('item.index') }}">제품</a>
                    @if(session('type') == 'super_admin')
                        <div class="collapse-divider"></div>
                        <a class="collapse-item" href="{{ url('admin/admins') }}">사용자</a>
                        <a class="collapse-item" href="{{ route('company.index') }}">회사</a>
                        <a class="collapse-item" href="{{ route('ajax.index') }}">구분</a>
                    @elseif(session('type') == 'company_admin')
                        <div class="collapse-divider"></div>
                        <a class="collapse-item" href="{{ url('admin/admins') }}">사용자</a>
                    @endif
                </div>
            </div>
        </li>

        <!-- 기초정보 -->
        <li class="my-nav-item nav-item">
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePromotion"
            aria-expanded="false" aria-controls="collapsePromotion">
                <span>프로모션 관리</span>
            </a>
            <div id="collapsePromotion" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('admin.event.index') }}">이벤트</a>
                    <a class="collapse-item" href="{{ route('discount.index') }}">세일</a>
                    @if(session('type') == 'super_admin')
                        <div class="collapse-divider"></div>
                        <a class="collapse-item" href="{{ route('carousels.index') }}">캐로셀</a>
                    @endif
                </div>
            </div>
        </li>

        <li class="nav-item"><a class="nav-link" href="{{ route('picture.index') }}">사진</a></li>

    </ul>
    <!-- End Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar shadow">

                <div class="ms-auto">
                    {{-- 🔥 사용자 정보를 보여주고 모달을 띄우는 버튼으로 변경 🔥 --}}
                    <button type="button" class="btn mycolor4" data-bs-toggle="modal" data-bs-target="#adminInfoModal">
                        <i class="fa fa-user-circle" aria-hidden="true"></i> {{session('admin_name')}}
                    </button>
                    | 
                    <a href="{{ route('admin.logout')}}" class="text-decoration-none"> 로그아웃 </a>
                </div>
                <!--<button id="darkModeBtn"
                        class="btn btn-sm btn-warning"
                        style="position: fixed; bottom:20px; right:20px; z-index:1050;">
                    🌙 다크모드
                </button>-->
            </nav>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if(localStorage.getItem('darkMode') === 'true'){
                        document.body.classList.add('dark-mode');
                    }
                    const btn = document.getElementById('darkModeBtn');
                    btn.addEventListener('click', () => {
                        document.body.classList.toggle('dark-mode');
                        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
                    });
                });
            </script>

            <!-- Main Content -->
            <div class="container-fluid mt-3">
                @yield('content')
            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="adminInfoModal" tabindex="-1" aria-labelledby="adminInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="adminInfoModalLabel">관리자 정보</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fa fa-user-circle fa-4x text-primary" aria-hidden="true"></i>
                </div>
                
                <table class="table table-borderless table-sm">
                    <tbody>
                        <tr>
                            <th scope="row">이름</th>
                            <td><strong>{{ session('admin_name') }}</strong></td>
                        </tr>
                        <tr>
                            <th scope="row">아이디</th>
                            <td>{{ session('admin_uid') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">권한</th>
                            <td>@if(session('type') == 'super_admin')
                                최고 관리자
                                @elseif(session('type') == 'company_admin')
                                {{session('company_name')}} 관리자
                                @else
                                {{session('company_name')}} 직원
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="modal-footer justify-content-between">
                {{-- 로그아웃 버튼 (좌측 정렬) --}}
                <a href="{{ route('admin.logout')}}" class="btn">
                    <i class="fa fa-sign-out-alt"></i> 로그아웃
                </a>
                
                {{-- 닫기 버튼 (우측 정렬) --}}
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>