@extends('main')
@section('content')

<style>
    /* 전체 배경 및 폰트 */
    body { background-color: #f8f9fa; font-family: 'Pretendard', sans-serif; }
    .gojeong { max-width: 1100px; margin: 0 auto; display: flex; gap: 30px; padding-top: 50px; }

    /* 사이드바 스타일 */
    .sidebar-card { width: 250px; flex-shrink: 0; background: white; border-radius: 15px; overflow: hidden; }
    .list-group-item { border: none; padding: 15px 20px; font-weight: 500; color: #555; transition: 0.2s; }
    .list-group-item:hover { background-color: #f1f3f5; color: #97a11d; }
    .list-group-item.active { background-color: #97a11d !important; color: white !important; }

    /* 대시보드 본문 */
    .dashboard-content { flex-grow: 1; }
    
    /* 요약 카드 (주문현황) */
    .summary-box { 
        display: grid; grid-template-columns: repeat(4, 1fr); 
        background: white; border-radius: 15px; padding: 25px; 
        text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 30px;
    }
    .summary-item h5 { font-size: 0.9rem; color: #888; margin-bottom: 10px; }
    .summary-item p { font-size: 1.5rem; font-weight: 800; color: #333; margin: 0; }
    .summary-item.highlight p { color: #97a11d; }

    /* 최근 주문 내역 섹션 */
    .section-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .section-title h4 { font-weight: 700; margin: 0; }
    .section-title a { font-size: 0.85rem; color: #888; text-decoration: none; }

    .order-item-card { 
        background: white; border-radius: 12px; padding: 20px; 
        display: flex; align-items: center; gap: 20px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.02); margin-bottom: 15px;
    }
    .order-img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
    .order-info { flex-grow: 1; }
    .order-status { font-size: 0.85rem; font-weight: 700; color: #97a11d; margin-bottom: 5px; }
    .order-name { font-weight: 600; margin-bottom: 3px; }
    .order-date { font-size: 0.8rem; color: #999; }
</style>

<div class="gojeong mb-5">
    {{-- 좌측 사이드바 --}}
    <div class="sidebar-card shadow-sm">
        <div class="p-4">
            <h4 class="fw-bold mb-1">{{ session('name') }}님</h4>
            <p class="text-muted small mb-0">등급: <span style="color:#97a11d">{{ session('grade') }}</span></p>
        </div>
        <div class="list-group list-group-flush">
            <a href="#" class="list-group-item active">
                <i class="fas fa-home me-2"></i>대시보드
            </a>
            <a href="{{ route('orders.index') }}" class="list-group-item">
                <i class="fas fa-shopping-bag me-2"></i>주문내역
            </a>
            <a href="{{ route('cart.index') }}" class="list-group-item">
                <i class="fas fa-shopping-cart me-2"></i>장바구니
            </a>
            <a href="{{ route('my.profile.edit') }}" class="list-group-item">
                <i class="fas fa-user-edit me-2"></i>정보수정
            </a>
            <a href="#" class="list-group-item text-danger">
                <i class="fas fa-sign-out-alt me-2"></i>로그아웃
            </a>
        </div>
    </div>

    {{-- 우측 본문 --}}
    <div class="dashboard-content">
        <div class="summary-box">
            <div class="summary-item">
                <h5>입금/결제</h5>
                <p>1</p>
            </div>
            <div class="summary-item">
                <h5>배송중</h5>
                <p>0</p>
            </div>
            <div class="summary-item">
                <h5>배송완료</h5>
                <p>12</p>
            </div>
            <div class="summary-item highlight">
                <h5>포인트</h5>
                <p>{{ number_format(session('point') ?? 0) }}P</p>
            </div>
        </div>

        <div class="section-title">
            <h4>최근 주문 내역</h4>
            <a href="{{ route('orders.index') }}">전체보기 <i class="fas fa-chevron-right"></i></a>
        </div>

        @forelse($orders->take(3) as $order)
            @php $firstItem = $order->items->first(); @endphp
            <div class="order-item-card">
                <img src="{{ asset('storage/item_img/' . ($firstItem->item->pic ?? '')) }}" class="order-img">
                <div class="order-info">
                    <div class="order-status">결제완료</div>
                    <div class="order-name">
                        {{ $firstItem->item->name ?? '삭제된 상품' }} 
                        @if($order->items->count() > 1) 외 {{ $order->items->count() - 1 }}건 @endif
                    </div>
                    <div class="order-date">{{ $order->created_at->format('Y.m.d') }} | 주문번호 {{ $order->id }}</div>
                </div>
                <div>
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">상세보기</a>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm p-5 text-center text-muted">
                <i class="fas fa-box-open fa-3x mb-3"></i>
                <p>최근 주문한 내역이 없습니다.</p>
            </div>
        @endforelse

        <div class="card border-0 mt-4 overflow-hidden" style="border-radius:15px; background: linear-gradient(90deg, #97a11d, #b4c12d); color:white;">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1">리뷰 쓰고 포인트 받으세요!</h5>
                    <p class="mb-0 opacity-75">구매 확정된 상품의 리뷰를 작성하면 최대 500P 적립</p>
                </div>
                <i class="fas fa-pen-nib fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

@endsection