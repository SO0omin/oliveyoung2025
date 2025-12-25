@extends('main')
@section('content')

<style>
    :root { --ov-green: rgb(174, 240, 219); --ov-pink: #f05a5e; --ov-bg: #f9f9f9; }
    .membership-box { background: var(--ov-bg); border-radius: 12px; padding: 20px; border: 1px solid #eee; }
    .item-image { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
    .qty-controls { display: flex; align-items: center; border: 1px solid #ddd; width: fit-content; border-radius: 4px; overflow: hidden; }
    .qty-controls button { background: #fff; border: none; padding: 2px 8px; }
    .qty-controls span { padding: 0 10px; border-left: 1px solid #ddd; border-right: 1px solid #ddd; font-weight: bold; }
    .order-btn { border-radius: 30px; padding: 12px 30px; font-weight: bold; }
</style>

<div class="container-fluid main-header-cart">
    <div class="gojeong d-flex justify-content-between align-items-end">
        <h2>장바구니</h2>
        <p class="mb-0"><strong>01.장바구니</strong> > 02.주문/결제 > 03.주문완료</p>
    </div>
</div>

<div class="gojeong mt-4">
    <div class="membership-box d-flex justify-content-between align-items-center">
        <span><strong>{{ session('name') }}</strong>님의 등급: <b style="color:var(--ov-green)">{{ session('grade') }}</b></span>
        <span class="fw-bold">포인트: <span style="color:var(--ov-pink)">{{ number_format($balance) }}P</span></span>
    </div>

    <table class="table align-middle text-center mt-4">
        <thead class="table-light">
            <tr>
                <th width="5%"><input type="checkbox" id="check-all" checked></th>
                <th width="50%">상품정보</th>
                <th width="15%">가격</th>
                <th width="15%">수량</th>
                <th width="15%">선택</th>
            </tr>
        </thead>
        <tbody>
            @foreach($carts as $cart)
            <tr>
                <td><input type="checkbox" class="cart-checkbox" data-cart-id="{{ $cart->id }}" checked></td>
                <td class="text-start">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('/storage/item_img/' . ($cart->item->pic ?? 'no-image.png')) }}" class="item-image">
                        <div>
                            <p class="mb-1 fw-bold">{{ $cart->item->name }}</p>
                            @if($cart->item->activeDiscount)<span class="badge bg-danger">SALE</span>@endif
                        </div>
                    </div>
                </td>
                <td id="price-{{ $cart->id }}">
                    @if($cart->item->activeDiscount)
                        <div class="text-muted small"><del>{{ number_format($cart->item->price * $cart->qty) }}원</del></div>
                        <div class="fw-bold text-danger">{{ number_format($cart->item->activeDiscount->sale_price * $cart->qty) }}원</div>
                    @else
                        <div class="fw-bold">{{ number_format($cart->item->price * $cart->qty) }}원</div>
                    @endif
                </td>
                <td>
                    <div class="qty-controls mx-auto">
                        <button onclick="updateCartQty({{ $cart->id }}, 'minus')">-</button>
                        <span id="qty-{{ $cart->id }}">{{ $cart->qty }}</span>
                        <button onclick="updateCartQty({{ $cart->id }}, 'plus')">+</button>
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-dark mb-1 w-75 buy-now-btn" data-cart-id="{{ $cart->id }}">바로구매</button>
                    <form action="{{ route('cart.destroy',$cart->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-light border w-75">삭제</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center gap-2 my-5">
        <button id="selected-order" class="btn btn-outline-dark order-btn">선택주문</button>
        <button id="all-order" class="btn btn-dark order-btn" style="background:var(--ov-green); border:none;">전체주문</button>
    </div>
</div>

<form id="checkout-form" action="{{ route('checkout') }}" method="GET">
    <input type="hidden" name="cart_ids" id="cart_ids">
</form>

<script>
    // 전체 선택
    $('#check-all').on('click', function() { $('.cart-checkbox').prop('checked', this.checked); });

    // 수량 변경 AJAX
    function updateCartQty(cartId, type) {
        $.post('{{ route("cart.updateQty") }}', { _token: '{{ csrf_token() }}', cart_id: cartId, type: type }, function(res) {
            if(res.success) {
                location.reload(); // 디자인상 깔끔하게 새로고침 처리
            }
        });
    }

    // 주문 버튼들
    $('.buy-now-btn').click(function() { $('#cart_ids').val($(this).data('cart-id')); $('#checkout-form').submit(); });
    $('#all-order').click(function() { 
        let ids = []; $('.cart-checkbox').each(function() { ids.push($(this).data('cart-id')); });
        $('#cart_ids').val(ids.join(',')); $('#checkout-form').submit(); 
    });
    $('#selected-order').click(function() {
        let ids = []; $('.cart-checkbox:checked').each(function() { ids.push($(this).data('cart-id')); });
        if(ids.length == 0) return alert('상품을 선택하세요.');
        $('#cart_ids').val(ids.join(',')); $('#checkout-form').submit();
    });
</script>
@endsection