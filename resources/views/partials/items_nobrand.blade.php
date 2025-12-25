<div class="row row-cols-{{ $cols ?? 4 }} g-3">
    @foreach($items as $item)
        <div class="col">
            <div class="card h-100 rounded-0 border-0">
                {{-- 🔥 순위 스티커 / ranked=true일 때만 표시 --}}
                @if(!empty($ranked))
                    <div class="rank-badge">
                        {{ $loop->iteration }}
                    </div>
                @endif

                <img src="{{ asset('/storage/item_img/' . $item->pic ) }}" 
                     class="card-img-top rounded-0" 
                     alt="{{ $item->name }}" 
                     height="160">

                <div class="card-body p-2">
                    <h6 class="card-title"
                        style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $item->name }}
                    </h6>

                    @if($item->activeDiscount)
                        <p class="card-text mb-1">
                            <del style='font-size: 15px;'>{{ number_format($item->price) }}원</del><br>
                            <strong class="text-danger"  style='font-size: 20px;'>
                                {{ number_format($item->activeDiscount->sale_price) }}원
                            </strong>
                        </p>
                        @if(!empty($sale))
                        <span class="badge bg-danger">세일</span>
                        @endif
                    @else
                        <p class="card-text mb-1">{{ number_format($item->price) }}원</p>
                    @endif

                    <a href="{{ route('goods.detail', $item->id) }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
    @endforeach
</div>