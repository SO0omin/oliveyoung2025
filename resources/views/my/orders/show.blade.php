@extends('main')
@section('content')

<div class="gojeong py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- 상단 타이틀 및 상태 --}}
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
                <div>
                    <h2 class="fw-bold mb-1">주문 상세 내역</h2>
                    <p class="text-muted mb-0">주문 일자: {{ $order->created_at->format('Y-m-d H:i') }} | 주문 번호: <span class="fw-bold text-dark">#{{ $order->id }}</span></p>
                </div>
                <div>
                    <span class="badge rounded-pill bg-primary px-3 py-2 fs-6">
                        @if($order->status == 'paid') 결제완료 @else {{ $order->status }} @endif
                    </span>
                </div>
            </div>

            <div class="row">
                {{-- 1. 상품 리스트 --}}
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">주문 상품</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4" style="width: 50%">상품정보</th>
                                            <th class="text-center">수량</th>
                                            <th class="text-end pe-4">합계 금액</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        {{-- 상품 이미지가 있다면 여기에 추가 --}}
                                                        <div>
                                                            <div class="fw-bold">{{ $item->item->name }}</div>
                                                            <small class="text-muted">단가: {{ number_format($item->sale_price ?? $item->price) }}원</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $item->qty }}</td>
                                                <td class="text-end pe-4">
                                                    @if($item->sale_price)
                                                        <div class="text-decoration-line-through text-muted small">{{ number_format($item->price * $item->qty) }}원</div>
                                                        <div class="fw-bold text-danger">{{ number_format($item->sale_price * $item->qty) }}원</div>
                                                    @else
                                                        <div class="fw-bold">{{ number_format($item->price * $item->qty) }}원</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. 우측 요약 및 배송지 정보 --}}
                <div class="col-md-4">
                    {{-- 배송지 정보 --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">배송지 정보</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="badge bg-secondary mb-2">{{ $order->shipping_label }}</span>
                                <div class="fw-bold fs-5">{{ $order->shipping_name }}</div>
                                <div class="text-muted small">{{ $order->shipping_phone }}</div>
                            </div>
                            <div class="border-top pt-2 mt-2">
                                <div class="text-muted small">({{ $order->shipping_zipcode }})</div>
                                <div class="small">{{ $order->shipping_address1 }} {{ $order->shipping_address2 }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- 결제 요약 --}}
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4">결제 금액 정보</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>주문 총액</span>
                                <span>{{ number_format($order->total_price) }}원</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>배송비</span>
                                <span class="text-success">무료</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">최종 결제 금액</span>
                                <span class="h4 fw-bold text-primary mb-0">{{ number_format($order->total_price) }}원</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-dark btn-lg fs-6">주문 목록으로</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .container { font-family: 'Pretendard', sans-serif; }
    .card { border-radius: 12px; }
    .table thead th { font-weight: 600; font-size: 0.85rem; color: #6c757d; border-bottom: none; }
    .badge { font-weight: 500; }
    .text-decoration-line-through { color: #adb5bd !important; }
</style>

@endsection