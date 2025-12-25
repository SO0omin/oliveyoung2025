<!---------------------------------------------------------------------------------------------
	제목 : Django Tutorial (실습용 디자인 HTML)

	소속 : 인덕대학교 컴퓨터소프트웨어학과
	이름 : 교수 윤형태 (2025.01)
---------------------------------------------------------------------------------------------->
@extends('main')
@section('content')
<div class="gojeong">
	<div id="total_detail">
		{{-- 왼쪽: 상품 디테일 --}}
		<div id="product-detail">
			<br>
			<p>
				<a href="{{ route('category.index', $item->detailCategory->subCategory->category->id) }}">{{ $item->detailCategory->subCategory->category->name ?? '' }}</a> >
				<a href="{{ route('category.sub', [$item->detailCategory->subCategory->category->id,$item->detailCategory->subCategory->id]) }}">{{ $item->detailCategory->subCategory->name ?? '' }}</a> >
				<a href="{{ route('category.sub', [$item->detailCategory->subCategory->category->id,$item->detailCategory->subCategory->id, $item->detailCategory->id]) }}">{{ $item->detailCategory->name ?? '' }}</a>
			</p>
			{{-- 대표 이미지 --}}
			<div id="product-image">
				<img src="{{ asset('storage/item_img/' . $item->pic) }}" 
					alt="{{ $item->name }}">
			</div>
		
			<hr>
			<div id="product-info">
				<h5> 상품설명 </h5>
				
			</div>
			<hr>
			{{-- 상세 정보
			<div id="product-info">
				<h1>{{ $item->name }}</h1>
				<p>가격: {{ number_format($item->price) }}원</p>
			</div>
			 --}}

			{{-- 상세 이미지 영역 --}}
			<div id="product-detail-images-wrapper">
				<div id="product-detail-images" class="collapsed">
					@if(isset($item->detailImages))
						@foreach($item->detailImages as $img)
							<img class="detail-img"
								src="{{ asset('storage/item_detail_img/' . $img->img_path) }}">
						@endforeach
					@endif
				</div>

					{{-- 흐림 오버레이 --}}
				<div id="fade-overlay"></div>
				<button id="detail-more-btn">상품설명 더보기</button>
			</div>
			<!-- 상품설명 더보기 버튼 아래에 관련 상품 출력 -->
			<div id="related-products" class="mt-5">
				<h4 class="mb-3">같은 카테고리의 인기 상품</h4>
				<div class="related-products-list">
					{{-- take(7)로 7개 제한, scroll 옵션 추가 --}}
					@include('partials.items', [
						'items' => $relatedItems->take(7), 
						'sale' => true,
						'scroll' => true  {{-- 가로 스크롤 모드 활성화 --}}
					])
				</div>
			</div>

		</div>
		<script>
		document.addEventListener("DOMContentLoaded", function () {
			const container = document.getElementById("product-detail-images");
			const btn = document.getElementById("detail-more-btn");

			btn.addEventListener("click", function () {
				container.classList.toggle("expanded");
				container.classList.toggle("collapsed");

				if (container.classList.contains("expanded")) {
					btn.textContent = "상품설명 접기";
				} else {
					btn.textContent = "상품설명 더보기";
				}
			});
		});
		</script>

		{{-- 오른쪽: 구매 파트 --}}
		<div id="purchase-panel">
			<div class="purchase-box">
				<p>{{ $item->company->name ?? '정보 없음' }} ></p>
				<h4>{{ $item->name }}

				</h4>
				<p>
					@if($item->activeDiscount)
						<p class="card-text mb-1">
							<del class="text-muted small">{{ number_format($item->price) }}원</del><br>
							<span class="text-danger" style="font-weight: 600;">
								{{ number_format($item->activeDiscount->discount_percent) }}%
							</span>
							<span style="font-weight: 600;">
								{{ number_format($item->activeDiscount->sale_price) }}원
							</span>
						</p>
					@else
						<p class="card-text mb-1">{{ number_format($item->price) }}원</p>
					@endif
				</p>
				<div class="mb-3">
					<label for="quantity">수량:</label>
					<input type="number" id="quantity" value="1" min="1" class="form-control">
				</div>
				@php
					$isLoggedIn = session('uid') ? true : false;
				@endphp

				<!-- 장바구니 버튼 -->
				<button class="btn" onclick="@if(!$isLoggedIn) window.location='{{ route('login') }}'; @else addToCart({{ $item->id }}); @endif">
					장바구니
				</button>
				<!-- 숨겨진 form -->
				<form id="buyNowForm" method="GET" action="{{ route('checkout.buyNow') }}">
					<input type="hidden" name="item_id" id="buy_item_id">
					<input type="hidden" name="qty" id="buy_qty">
				</form>
				
				<!-- 바로 구매 버튼 -->
				<button class="btn" onclick="@if(!$isLoggedIn) window.location='{{ route('login') }}'; @else buyNow({{ $item->id }}); @endif">바로 구매</button>


				<script>
				function addToCart(itemId) {
					const qty = parseInt(document.getElementById('quantity').value) || 1; // 입력된 수량 가져오기
					$.ajax({
						url: "{{ route('cart.add') }}",
						method: 'POST',
						data: {
							_token: "{{ csrf_token() }}",
							item_id: itemId,
							qty: qty // 입력값 반영
						},
						success: function(res) {
							if(res.success){
								var cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
								cartModal.show();
							}
						},
						error: function(xhr){
							if(xhr.status === 401){
								alert('로그인이 필요합니다.');
								location.href = "{{ route('login') }}";
							}
						}
					});
				}
				function buyNow(itemId){
					const qty = parseInt(document.getElementById('quantity').value) || 1;
					document.getElementById('buy_item_id').value = itemId;
					document.getElementById('buy_qty').value = qty;
					document.getElementById('buyNowForm').submit();
				}
				</script>
			</div>
		</div>
	</div>
</div>

<!-- 모달 (장바구니 성공) -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">장바구니</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
		</div>
		<div class="modal-body">
			상품이 장바구니에 추가되었습니다.
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">계속 쇼핑하기</button>
			<a href="{{ route('cart.index') }}" class="btn btn-primary">장바구니로 이동</a>
		</div>
		</div>
	</div>
</div>

@endsection