<!---------------------------------------------------------------------------------------------
	제목 : Django Tutorial (실습용 디자인 HTML)

	소속 : 인덕대학교 컴퓨터소프트웨어학과
	이름 : 교수 윤형태 (2025.01)
---------------------------------------------------------------------------------------------->
@extends('main')
@section('content')
<div class="ctg gojeong">
	<div id="ctg_nav">
		<h2>{{ $category->name }}</h2>
		<hr>
		@foreach($subs as $sub)
			<a href="{{ route('category.sub', [$category->id, $sub->id]) }}" class="a-ctg">
				{{ $sub->name }}
			</a>
		@endforeach
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
        const carouselItems = carouselEl.querySelectorAll('.carousel-item');
        const carouselTotalEl = document.getElementById('carouselTotal');
        const carouselCurrentEl = document.getElementById('carouselCurrent');

        // 슬라이드 정보를 오버레이에 업데이트하는 함수
        function updateOverlay(activeItem) {
            const data = activeItem.querySelector('.slide-data');
            if (data) {
                overlayTitle.textContent = data.dataset.title || '';
                overlayContent.textContent = data.dataset.content || '';

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
	<div id="ctg_main" class="mb-3">
        <div id="ctg_main-carousel">
            {{-- 동적으로 내용을 업데이트할 빈 오버레이 영역 --}}
            <div class="carousel-title-ctg">
                <div class="carousel-overlay-ctg">
                    <p class="title" id="overlayTitle"></p>
                    <p class="content" id="overlayContent"></p>
                </div>
            </div>
            <div class="carousel-overlay-controls-ctg">
                <button id="btnPrev">〈</button>&nbsp;&nbsp;&nbsp;
                <div class="carousel-number-indicator-ctg">
                    <span id="carouselCurrent" style="font-size: 13px;">1</span><span style="font-size: 13px;"> / </span><span id="carouselTotal" style="font-size: 13px;"></span>
                </div>&nbsp;&nbsp;&nbsp;
                <button id="btnNext">〉</button>
                <button id="btnPause">❚❚</button>
            </div>
            <div class="mb-3">
                @include('partials.carousel', ['carousels' => $carousels])
            </div>
        </div>
		<!--<div id="">
			작은 광고
		</div>-->
		<div id="ctg_best">
			<h5>{{ $category->name }}의 베스트만 모아봤어요</h5>
			@include('partials.items_nobrand', ['items' => $items->take(5), 'cols' => 5,'ranked' => true])
			<a href="{{ route('goods.rank', ['category_id' => $category->id]) }}"> 베스트상품 더보기</a>
		</div>
		<hr>
		<div id="ctg_list">
			<h5>{{ $category->name }}에서 많이 본 상품이에요</h5>
			@include('partials.items_nobrand', ['items' => $items,'sale' => true])
		</div>
	</div>
</div>
@endsection