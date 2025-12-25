<!---------------------------------------------------------------------------------------------
	제목 : Django Tutorial (실습용 디자인 HTML)

	소속 : 인덕대학교 컴퓨터소프트웨어학과
	이름 : 교수 윤형태 (2025.01)
---------------------------------------------------------------------------------------------->
@extends('main')
@section('content')
<div class="gojeong">
    <div id="mini-category">
        <a class="cate-padding" href="{{ route('main.index') }}"><i class="fa fa-home me-1"></i></a>
        <span class="cate-little-padding">&nbsp;&nbsp;> &nbsp;&nbsp;</span>
        <div class="dropdown category-dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle">
                {{ $category->name }}
            </button>

            <ul class="dropdown-menu">
                @foreach($allCategories as $c)
                    <li>
                        <a class="dropdown-item 
                        {{ $category->id == $c->id ? 'active' : '' }}"
                        href="{{ route('category.sub', [$c->id, $c->subCategories->first()->id]) }}">
                            {{ $c->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <span class="cate-little-padding">&nbsp;&nbsp;> &nbsp;&nbsp;</span>
        <div class="dropdown sub-dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle">
                {{ $sub->name }}
            </button>
            <ul class="dropdown-menu">
                @foreach($category->subCategories as $s)
                    <li>
                        <a class="dropdown-item
                        {{ $sub->id == $s->id ? 'active' : '' }}"
                        href="{{ route('category.sub', [$category->id, $s->id]) }}">
                            {{ $s->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        @if(isset($detail))
            <span class="cate-little-padding">&nbsp;&nbsp;> &nbsp;&nbsp;</span>
            <div class="dropdown detail-dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle">
                    {{ $detail->name ?? '소분류' }}
                </button>
                <ul class="dropdown-menu">
                    @foreach($details as $d)
                        <li>
                            <a class="dropdown-item
                            {{ $detail && $detail->id == $d->id ? 'active' : '' }}"
                            href="{{ route('category.sub', [$category->id, $sub->id, $d->id]) }}">
                                {{ $d->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <hr>
    </div>
    <hr style="margin: 10px 0px;">
    @php $currentDetailId = $detail->id ?? null; @endphp
    <div id="category_list" class="cv-page">
        <h3 style="margin-bottom: 20px;">{{ $sub->name }}</h3>
        <table id="category_table">
            @foreach($details->chunk(5) as $index => $chunk)
                <tr>
                    @if($index == 0)
                        {{-- 첫 줄 첫 칸: 전체 버튼 --}}
                        <td>
                            <a href="{{ route('category.sub', [$category->id, $sub->id]) }}" class="my-btn {{ ($currentDetailId == null) ? 'active' : '' }} btn-sm btn-outline-primary">
                                전체
                            </a>
                        </td>
                    @else
                        {{-- 첫 칸: chunk 첫 요소 --}}
                        <td>
                            @php $firstDetail = $chunk->first(); @endphp
                            <a href="{{ route('category.sub', [$category->id, $sub->id, $firstDetail->id]) }}" class="my-btn btn-sm btn-outline-primary {{ ($currentDetailId == $firstDetail->id) ? 'active' : '' }}">
                                {{$chunk->shift()->name }}
                            </a>
                        </td>
                    @endif

                    {{-- 나머지 소분류 버튼 --}}
                    @foreach($chunk as $detailCategory)
                        <td>
                            <a href="{{ route('category.sub', [$category->id, $sub->id, $detailCategory->id]) }}" class="my-btn {{ ($currentDetailId == $detailCategory->id) ? 'active' : '' }} btn-sm btn-outline-primary">
                                {{ $detailCategory->name }}
                            </a>
                        </td>
                    @endforeach

                    {{-- 6칸 맞추기 위해 빈칸 추가 --}}
                    @for($i = ($index == 0 ? 1 : 1) + $chunk->count(); $i < 6; $i++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </table>
    </div>

    <div id="ctg_main-carousel-view" class="position-relative">

    @if($carousels->count() > 2)
        {{-- 2. 버튼 위치: 캐러셀 좌우 중앙에 배치하기 위해 div 바깥으로 이동 --}}
        <button id="btnPrev" class="carousel-control-prev-custom">〈</button>
        <button id="btnNext" class="carousel-control-next-custom">〉</button>
    @endif   
        <div class="mt-3 mb-3">
            <div id="carouselExampleIndicatorsCtg" class="carousel slide carousel-fade">
                <div class="carousel-inner">
                    
                    @foreach($carousels->chunk(2) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            
                            {{-- 🔥 이미지 간격을 위해 d-flex에 p-3 (패딩) 추가 🔥 --}}
                            <div class="d-flex p-3">
                                @foreach($chunk as $carousel)
                                    {{-- 🔥 개별 아이템 좌우 간격을 위해 px-2 (패딩) 추가 🔥 --}}
                                    <div class="carousel-individual-item px-2" style="width: 50%;"> 
                                        <div class="item-card-wrapper">
                                            <a href="{{ $carousel->event_id ? route('event.show', $carousel->event_id) : '#' }}">
                                                
                                                {{-- 🔥 각 이미지 전용 오버레이 텍스트 🔥 --}}
                                                <div class="item-text-overlay">
                                                    {{-- <span class="company-tag">{{ $carousel->event->company->name ?? 'EVENT' }}</span> --}}
                                                    <h3 class="item-title">{{ $carousel->title }}</h3>
                                                    <p class="item-content">{{ Str::limit($carousel->content, 40) }}</p>
                                                </div>

                                                <img src="{{ asset('storage/carousel_img/'.$carousel->pic) }}" 
                                                    class="d-block w-100" 
                                                    style="height: 450px; object-fit: cover; border-radius: 15px;" 
                                                    alt="{{ $carousel->title }}">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                                
                                {{-- 아이템이 1개뿐일 때 빈 공간 채우기 --}}
                                @if($chunk->count() < 2)
                                    <div style="width: 50%; padding-left: 0.5rem; padding-right: 0.5rem;"> 
                                        <div style="background: #f8f9fa; height: 450px; border-radius: 15px;"></div>
                                    </div>
                                @endif

                            </div>
                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const carouselEl = document.querySelector('#carouselExampleIndicatorsCtg');
            if (!carouselEl) return;

            const carousel = new bootstrap.Carousel(carouselEl, {
                interval: false,
                wrap: true
            });

            const carouselCurrentEl = document.getElementById('carouselCurrent');
            const btnPrev = document.getElementById("btnPrev");
            const btnNext = document.getElementById("btnNext");

            // 슬라이드가 넘어갔을 때 숫자 인디케이터만 업데이트
            carouselEl.addEventListener('slid.bs.carousel', (event) => {
                const items = carouselEl.querySelectorAll('.carousel-item');
                const currentIndex = Array.from(items).indexOf(event.relatedTarget) + 1;
                carouselCurrentEl.textContent = currentIndex;
            });

            if (btnPrev) btnPrev.addEventListener("click", () => carousel.prev());
            if (btnNext) btnNext.addEventListener("click", () => carousel.next());
        });
    </script>
    <div id="item-list">
        <div class="text-center" style="font-weight:200; font-size:23px;">@if(!isset($detail) || !isset($detail->id))
                {{ $sub->name }}
            @else
                {{ $detail->name }}
            @endif 카테고리에 <strong>{{count($items)}}</strong>개의 상품이 등록되어있습니다.</div>
        <hr class="thick-hr">
            <div class="sort-buttons mb-2">
                <a href="{{ route('category.sub', [$category->id, $sub->id, 'sort' => 'sales']) }}"
                class="btn btn-sm sort-btn {{ $sort == 'sales' ? 'active' : '' }}">판매순</a>
                |
                <a href="{{ route('category.sub', [$category->id, $sub->id, 'sort' => 'new']) }}"
                class="btn btn-sm sort-btn {{ $sort == 'new' ? 'active' : '' }}">신상품</a>
                |
                <a href="{{ route('category.sub', [$category->id, $sub->id, 'sort' => 'low_price']) }}"
                class="btn btn-sm sort-btn {{ $sort == 'low_price' ? 'active' : '' }}">낮은 가격순</a>
                |
                <a href="{{ route('category.sub', [$category->id, $sub->id, 'sort' => 'high_price']) }}"
                class="btn btn-sm sort-btn {{ $sort == 'high_price' ? 'active' : '' }}">높은 가격순</a>
            </div>
        <hr>
        <div id="cv-product">
            @include('partials.items', ['items' => $items])
        </div>
    </div>

</div>

@endsection 