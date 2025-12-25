@extends('main')
@section('content')

<style>
    .profile-card {margin: 50px auto; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .form-label { font-weight: 600; color: #555; }
    .btn-update { background-color: var(--ov-green); border: none; padding: 12px; font-weight: bold; color: white; border-radius: 8px; }
    .btn-update:hover { opacity: 0.9; color: white; }
    .info-text { font-size: 0.85rem; color: #888; margin-top: 5px; }
/* 사이드바 스타일 */
    .active-menu { 
        background-color: #000 !important; 
        color: #fff !important; 
        border-radius: 8px !important;
    }
    .list-group-item:hover:not(.active-menu) {
        background-color: #f1f3f5;
        border-radius: 8px;
    }

    /* 주문 카드 스타일 */
    .order-card { 
        border-radius: 12px; 
        transition: transform 0.2s; 
    }
    .order-card:hover { 
        transform: translateY(-3px); 
    }
    .bg-success-light { background-color: #e6fcf5; }
    
    /* 버튼 스타일 */
    .btn-primary { background-color: #000; border: none; }
    .btn-primary:hover { background-color: #333; }
</style>

<div class="gojeong">
    <div class="row mt-3">
        {{-- 좌측 사이드바 메뉴 --}}
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm p-3">
                <h4 class="fw-bold mb-4 ps-2">마이페이지</h4>
                <div class="list-group list-group-flush">
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="fas fa-shopping-bag me-2"></i>주문내역
                    </a>
                    <a href="{{ route('cart.index') }}" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="fas fa-shopping-cart me-2"></i>장바구니
                    </a>
                    <a href="{{ route('my.profile.edit') }}" class="list-group-item list-group-item-action border-0 py-3 active-menu">
                        <i class="fas fa-user me-2"></i>정보수정
                    </a>
                </div>
            </div>
        </div>

        {{-- 우측 메인 컨텐츠 --}}
        <div class="col-lg-9">
        <div class="profile-card card p-4 p-md-5">
            <h3 class="fw-bold mb-4 text-center">개인정보 수정</h3>

            @if(session('success'))
                <div class="alert alert-success border-0" style="background-color: #f0f9f0; color: #2d5a2d;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('my.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">아이디</label>
                    <input type="text" class="form-control bg-light" value="{{ $customer->uid }}" readonly title="아이디는 변경할 수 없습니다.">
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">이름</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="tel" class="form-label">연락처</label>
                    <input type="text" name="tel" id="tel" class="form-control @error('tel') is-invalid @enderror" value="{{ old('tel', $customer->tel) }}" required>
                    <p class="info-text">- 없이 숫자만 입력해주세요.</p>
                    @error('tel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3">비밀번호 변경</h5>
                <div class="mb-3">
                    <label for="password" class="form-label">새 비밀번호</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="변경할 경우에만 입력">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">새 비밀번호 확인</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="비밀번호 재입력">
                </div>

                <button type="submit" class="btn btn-update w-100 mt-2 mycolor2">정보 수정하기</button>
            </form>
            
            <hr class="my-5">

            <div class="withdrawal-section p-3 border rounded" style="background-color: #fff5f5; border-color: #feb2b2 !important;">
                <h6 class="fw-bold text-danger mb-2"><i class="fas fa-exclamation-triangle me-2"></i>계정 삭제</h6>
                <p class="small text-muted mb-3">탈퇴 시 모든 주문 내역과 개인정보가 삭제되며 복구할 수 없습니다.</p>
                
                <form action="{{ route('my.profile.withdraw') }}" method="POST" onsubmit="return confirm('정말로 탈퇴하시겠습니까? 모든 정보가 영구 삭제됩니다.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">회원 탈퇴하기</button>
                </form>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted small">취소하고 돌아가기</a>
            </div>
        </div>
    </div>
</div>

@endsection