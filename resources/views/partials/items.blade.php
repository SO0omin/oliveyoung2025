<?php
    $containerClass = !empty($scroll) ? 'd-flex flex-nowrap overflow-auto pb-3 custom-scrollbar' : 'row row-cols-' . ($cols ?? 4) . ' g-3 gx-0 mb-3';
    $itemClass = !empty($scroll) ? 'flex-shrink-0' : 'col';
    $itemStyle = !empty($scroll) ? 'width: 200px; margin-right: 15px;' : '';
?>
<div class="{{ $containerClass }}">
    @foreach($items as $item)
        <div class="{{ $itemClass }} position-relative" style="{{ $itemStyle }}">

            {{-- 순위 스티커 --}}
            @if(!empty($ranked))
                <div class="rank-badge">
                    {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                </div>
            @endif

            <a href="{{ route('goods.detail', $item->id) }}" class="text-decoration-none text-dark">
                <div class="card h-100 rounded-0 border-0 shadow-none">
                    <img src="{{ asset('/storage/item_img/' . $item->pic ) }}"
                        class="card-img-top rounded-0"
                        style="height: 200px; object-fit: cover;"> {{-- 이미지 비율 유지 --}}
                    
                    <div class="card-body p-2 text-center">
                        <p class="mb-1 text-truncate" style="font-size: 0.9rem; font-weight: 500; color: gray">
                            {{ $item->company->name ?? '브랜드 없음' }}
                        </p>

                        <strong class="card-title text-truncate d-block" style="font-size: 1rem;">
                            {{ $item->name }}
                        </strong>
                        
                        @if($item->activeDiscount)
                            <p class="card-text mb-1">
                                <del class="text-muted small">{{ number_format($item->price) }}원</del><br>
                                <span class="text-danger" style="font-weight: 600;">
                                    {{ number_format($item->activeDiscount->sale_price) }}원
                                </span>
                            </p>
                        @else
                            <p class="card-text mb-1">{{ number_format($item->price) }}원</p>
                        @endif
                    </div>
                </div>
            </a>
        </div>

        {{-- 스크롤 모드가 아닐 때만 구분선 표시 --}}
        @if(empty($scroll) && $loop->iteration % 4 === 0 && $loop->iteration < $items->count())
            <div class="col-12 my-2">
                <hr class="full-hr">
            </div>
        @endif
    @endforeach
</div>