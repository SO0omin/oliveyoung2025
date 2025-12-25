@extends('main')
@section('content')

<div class="container-fluid main-header-discount">
    <div class="gojeong main-header-content">
        <h2>세일</h2>
        <span>핫 세일!이건 꼭 사야돼!</span>
    </div>
</div>

<div class="gojeong discount-page">
    <div id="category_list">
        <table id="category_table">
            {{-- 첫 줄: 전체 + 5개 --}}
            <tr>
                <td>
                    <a href="{{ route('goods.discount') }}" 
                    class="my-btn btn-sm btn-outline-primary {{ request('category_id') ? '' : 'active' }}">
                        전체
                    </a>
                </td>
                @foreach($categories->take(5) as $category)
                    <td>
                        <a href="{{ route('goods.discount', ['category_id' => $category->id]) }}" 
                        class="my-btn btn-sm btn-outline-primary {{ (request('category_id') == $category->id) ? 'active' : '' }}">
                            {{ $category->name }}
                        </a>
                    </td>
                @endforeach
                @for($i = $categories->take(5)->count() + 1; $i < 6; $i++)
                    <td></td>
                @endfor
            </tr>

            {{-- 나머지 줄: 6개씩 --}}
            @foreach($categories->skip(5)->chunk(6) as $chunk)
                <tr>
                    @foreach($chunk as $category)
                        <td>
                            <a href="{{ route('goods.discount', ['category_id' => $category->id]) }}" 
                            class="my-btn btn-sm btn-outline-primary {{ (request('category_id') == $category->id) ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        </td>
                    @endforeach
                    @for($i = $chunk->count(); $i < 6; $i++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </table>
    </div>
    <div id="discount">
        <div class="text-center mt-3" style="font-weight:200; font-size:23px;">
        전체 <strong style="color:rgb(253, 158, 63);">{{count($items)}}</strong>개의 상품이 등록되어있습니다.
        </div>
        <hr class="thick-hr">

            {{-- 정렬 버튼 --}}
            <div class="sort-buttons mb-2">
                <a href="{{ route('goods.discount', ['category_id' => request('category_id'), 'sort' => 'sales']) }}"
                class="btn btn-sm sort-btn {{ $sort == 'sales' ? 'active' : '' }}">판매순</a>
                |
                <a href="{{ route('goods.discount', ['category_id' => request('category_id'), 'sort' => 'new']) }}"
                class="btn btn-sm sort-btn {{ $sort == 'new' ? 'active' : '' }}">신상품</a>
                |
                <a href="{{ route('goods.discount', ['category_id' => request('category_id'), 'sort' => 'low_price']) }}"
                class="btn btn-sm sort-btn {{ $sort == 'low_price' ? 'active' : '' }}">낮은 가격순</a>
                |
                <a href="{{ route('goods.discount', ['category_id' => request('category_id'), 'sort' => 'high_price']) }}"
                class="btn btn-sm sort-btn {{ $sort == 'high_price' ? 'active' : '' }}">높은 가격순</a>
            </div>
        <hr>
        <div id="discount">
        @include('partials.items', ['items' => $items,'sale' => true])
        </div>
    </div>
</div>
@endsection