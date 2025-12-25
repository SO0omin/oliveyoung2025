@extends('main')
@section('content')

<div class="carousel-wrapper">
    <div class="menu-overlay">
        <ul class="menu-ul">
            @foreach($categories as $category)
            <li class="menu-li" data-subs='@json($category->subCategories)'>
                {{ $category->name }}
            </li>
            @endforeach
        </ul>
    </div>

    <div class="menu-overlay-add" id="subBox">
        <ul id="sub-list" class="sub-menu-ul"></ul>
    </div>
    
    {{-- 동적으로 내용을 업데이트할 빈 오버레이 영역 --}}
    <div class="carousel-title">
        <div class="carousel-overlay">
            <span class="company" id="overlayCompany"></span><br>
            - <br>
            <p class="title" id="overlayTitle"></p>
            <p class="content" id="overlayContent"></p>
        </div>
    </div>
    
	<div class="carousel-overlay-controls">
		<button id="btnPrev">〈</button>&nbsp;&nbsp;&nbsp;
		<div class="carousel-number-indicator">
			<span id="carouselCurrent">1</span> / <span id="carouselTotal"></span>
		</div>&nbsp;&nbsp;&nbsp;
		<button id="btnNext">〉</button>
		<button id="btnPause">❚❚</button>
	</div>
    
    <div class="container-fluid p-0">
        @include('partials.carousel', ['carousels' => $carousels])
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const carouselEl = document.querySelector('#carouselExampleIndicators');
        
        if (!carouselEl) {
            console.error("Bootstrap Carousel 요소를 찾을 수 없습니다. #carouselExampleIndicators ID를 확인해주세요.");
            return;
        }

        const carousel = new bootstrap.Carousel(carouselEl, {
            interval: 4000, 
            pause: false
        });

        const overlayTitle = document.getElementById('overlayTitle');
        const overlayContent = document.getElementById('overlayContent');
        const overlayCompany = document.getElementById('overlayCompany');
        const carouselItems = carouselEl.querySelectorAll('.carousel-item');
        const carouselTotalEl = document.getElementById('carouselTotal');
        const carouselCurrentEl = document.getElementById('carouselCurrent');

        // 슬라이드 정보를 오버레이에 업데이트하는 함수
        function updateOverlay(activeItem) {
            const data = activeItem.querySelector('.slide-data');
            if (data) {
                overlayTitle.textContent = data.dataset.title || '';
                overlayContent.textContent = data.dataset.content || '';
                overlayCompany.textContent = data.dataset.company || '';
                
                // 현재 슬라이드 번호 업데이트 (1부터 시작)
                const currentIndex = Array.from(carouselItems).indexOf(activeItem) + 1;
                carouselCurrentEl.textContent = currentIndex;
            }
        }

        // --- 수정된 부분: 이벤트 리스너를 'slid.bs.carousel' -> 'slide.bs.carousel' 변경 ---
        // 슬라이드 전환이 시작될 때 (애니메이션 시작 전) 오버레이 업데이트
        carouselEl.addEventListener('slide.bs.carousel', (event) => {
            // event.relatedTarget은 새로 활성화될 슬라이드 요소
            updateOverlay(event.relatedTarget);
        });

        // 초기 로드 시 첫 번째 슬라이드 정보로 오버레이 업데이트
        const initialActiveItem = carouselEl.querySelector('.carousel-item.active');
        if (initialActiveItem) {
            updateOverlay(initialActiveItem);
        }

        // Prev / Next 버튼 클릭 이벤트 리스너 (이미 즉각적으로 동작)
        document.getElementById("btnPrev").addEventListener("click", () => {
            carousel.prev();
        });
        document.getElementById("btnNext").addEventListener("click", () => {
            carousel.next();
        });

        // Pause / Play 버튼
        let paused = false;
        document.getElementById("btnPause").addEventListener("click", (e) => {
            if (!paused) {
                carousel.pause();
                e.target.textContent = "▶"; 
            } else {
                carousel.cycle(); 
                e.target.textContent = "❚❚"; 
            }
            paused = !paused;
        });

        // 총 슬라이드 수 표시
        carouselTotalEl.textContent = carouselItems.length;
    });

    $(document).ready(function(){

        $(".menu-li").hover(function(){
            const subs = $(this).data("subs");  // JSON 배열[{id:.., name:..}, ...]

            let html = "";

            subs.forEach(sub => {
                html += `
                    <li class="sub-menu-li">
                        <a href="/~sale48/one/public/categories/${sub.category_id}/${sub.id}">
                            ${sub.name}
                        </a>
                    </li>
                `;
            });

            $("#sub-list").html(html);  // HTML 삽입
            $("#subBox").show();
        });

        $("#subBox").mouseleave(function(){
            $(this).hide();
        });
    });

</script>

{{-- 이하 Top 10 스크립트는 동일 --}}
<br>
<!--  요즘 뜨는 상품 -->
<div class="gojeong mt-5">
	<div style="display: flex; justify-content: space-between; align-items: center; align-items: center;padding: 10px 0;">
		<h4 class="mb-4">요즘 주목 받는 상품</h4>
		<div class="mt-3">
			<button class="btn btn-sm btn-primary page-btn" data-page="0">1</button>
			<button class="btn btn-sm btn-primary page-btn" data-page="1">2</button>
		</div>
	</div>
    <div class="card-viewport" style="overflow: hidden;;">
        <div class="card-track d-flex" style="transition: transform 0.5s ease;">
            @foreach($topItems as $item)
                    <div class="card text-center flex-shrink-0" style="width: 20%; padding: 0 0.5rem; border: none; box-shadow: none;">
                        <a href="{{ route('goods.detail', $item->id) }}">
                        <img src="{{ asset('/storage/item_img/' . $item->pic) }}" 
                            class="card-img-top" 
                            style="height: 150px; max-width: 170px; border-radius: 0; display: block; margin: 0 auto;">
                        <div class="card-body">
                            {{-- 브랜드 이름 --}}
                            <p class="mb-1">{{ $item->company->name ?? '브랜드 없음' }}</p>

                            <h5 class="card-title" 
                                style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                {{ $item->name }}
                            </h5>
                            {{-- 가격 표시 --}}
                            @if($item->activeDiscount)
                                <p class="card-text mb-1">
                                    <del>{{ number_format($item->price) }}원</del>
                                    <span class="text-danger" style="font-weight: 500;">{{number_format($item->activeDiscount->sale_price) }}원</span>
                                </p>
                                <span class="badge bg-danger">세일</span>
                            @else
                                <p class="card-text mb-1">{{ number_format($item->price) }}원</p>
                            @endif
                        </div>
                        </a>
                    </div>
            @endforeach
        </div>
    </div>
</div>
<script>
    const track = document.querySelector('.card-track');
    const cards = document.querySelectorAll('.card-track .card');
    const btns = document.querySelectorAll('.page-btn');

    const cardsPerPage = 5;
    const totalCards = cards.length;

    if (totalCards === 0) {
        console.log("표시할 카드가 없습니다.");
    } else {
        function slideToPage(page) {
            // 이 부분의 카드 너비 계산 로직은 cards[0]을 사용하도록 수정하는게 더 안전합니다.
            const firstCard = cards[0];
            const style = getComputedStyle(firstCard);
            const cardWidth = firstCard.offsetWidth + parseFloat(style.marginLeft) + parseFloat(style.marginRight);

            const remainingCards = totalCards - page * cardsPerPage;
            const visibleCards = remainingCards < cardsPerPage ? remainingCards : cardsPerPage;

            let moveX = -(page * cardsPerPage * cardWidth);

            if (remainingCards < cardsPerPage) {
                moveX = -(totalCards - visibleCards) * cardWidth;
            }

            track.style.transform = `translateX(${moveX}px)`;
        }

        btns.forEach(btn => {
            btn.addEventListener('click', function() {
                const page = parseInt(this.dataset.page);
                slideToPage(page);
            });
        });

        window.addEventListener('resize', () => {
            slideToPage(0); 
        });
    }
</script>
<br>
<!-- 위클리 베스트(이벤트 글로 넣어야됨) -->
<div class="gojeong mt-5">
    <div class="">
        <h4 class="text-center"> Weekly Special</h4>
    </div>
    <div class="d-flex justify-content-between"> 
        {{-- 1. 왼쪽 스페셜 영역 --}}
        <div id="left-special" class="special-item-container">
            <img src="{{asset('/storage/crsl_img/left.png')}}" alt="Left Special Item" class="img-fluid special-img">
            
            {{-- 이미지 위에 올라갈 텍스트 --}}
            <div class="special-overlay">
                <p class="special-title">올해의 베스트셀러<br>대체불가 메디힐</p>
                <p class="special-price">어워즈 수상기념 한정기획!</p>
            </div>
        </div> 
        
        {{-- 2. 오른쪽 스페셜 영역 --}}
        <div id="right-special" class="special-item-container">
            <img src="{{asset('/storage/crsl_img/right.png')}}" alt="Right Special Item" class="img-fluid special-img">
            
            {{-- 이미지 위에 올라갈 텍스트 --}}
            <div class="special-overlay">
                <p class="special-title">퓌 인기템.zip</p>
                <p class="special-price">푸딩팟to스파글로잉</p>
            </div>
        </div> 
    </div>
</div>
<!-- 작은 배너 광고 -->
<br>
<!-- 신상품 -->
<br>

@endsection
