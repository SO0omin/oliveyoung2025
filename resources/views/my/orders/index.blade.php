@extends('main')
@section('content')

<div class="gojeong py-5">
    <div class="row">
        {{-- 좌측 사이드바 메뉴 --}}
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm p-3">
                <h4 class="fw-bold mb-4 ps-2">마이페이지</h4>
                <div class="list-group list-group-flush">
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action border-0 py-3 active-menu">
                        <i class="fas fa-shopping-bag me-2"></i>주문내역
                    </a>
                    <a href="{{ route('cart.index') }}" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="fas fa-shopping-cart me-2"></i>장바구니
                    </a>
                    <a href="{{ route('my.profile.edit') }}" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="fas fa-user me-2"></i>정보수정
                    </a>
                </div>
            </div>
        </div>

        {{-- 우측 메인 컨텐츠 --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">주문 내역</h3>
                <span class="text-muted small">총 <strong>{{ $orders->count() }}</strong>건의 주문이 있습니다.</span>
            </div>

            @if($orders->isEmpty())
                <div class="card border-0 shadow-sm py-5 text-center">
                    <div class="card-body">
                        <i class="fas fa-box-open fa-3x text-light mb-3"></i>
                        <p class="text-muted">주문 내역이 없습니다.</p>
                        <a href="{{ route('goods.discount') }}" class="btn btn-primary px-4">쇼핑하러 가기</a>
                    </div>
                </div>
            @else
                @foreach($orders as $order)
                    <div class="card border-0 shadow-sm mb-4 order-card">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                            <div>
                                <span class="text-muted small me-2">{{ $order->created_at->format('Y.m.d') }}</span>
                                <span class="fw-bold text-dark">주문번호 #{{ $order->id }}</span>
                            </div>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary px-3">
                                상세 보기 <i class="fas fa-chevron-right ms-1 small"></i>
                            </a>
                        </div>
                        <div class="card-body py-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center">
                                        {{-- 첫 번째 상품명 외 건수 처리 --}}
                                        <div class="ms-1">
                                            <h5 class="fw-bold mb-1">
                                                @php $count = $order->items->count(); @endphp

                                                {{ $order->items->first()?->item?->name ?? '상품 정보 없음' }}
                                                @if($count > 1)
                                                    외 {{ $count - 1 }}건
                                                @endif
                                            </h5>
                                            <div class="h5 text-primary fw-bold mb-0">
                                                {{ number_format($order->total_price) }}원
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <span class="badge rounded-pill px-4 py-2 
                                        @if($order->status == 'paid') bg-success-light text-success 
                                        @else bg-light text-dark @endif">
                                        {{ $order->status == 'paid' ? '결제완료' : $order->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
    /* 전체 배경색 및 폰트 */
    body { background-color: #f8f9fa; font-family: 'Pretendard', sans-serif; }
    
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

@endsection