@extends('main')
@section('content')

<div class="container-fluid main-header-rank">
    <div class="gojeong main-header-content">
        <h2>랭킹</h2>
        <span>오늘의 랭킹!요즘 가장 핫한 상품</span>
    </div>
</div>

<div class="gojeong rank-page">
    <div id="category_list">
        <table id="category_table">
            {{-- 첫 줄: 전체 + 5개 --}}
            <tr>
                <td>
                    <a href="{{ route('goods.rank') }}" 
                    class="my-btn btn-sm btn-outline-primary {{ request('category_id') ? '' : 'active' }}">
                        전체
                    </a>
                </td>
                @foreach($categories->take(5) as $category)
                    <td>
                        <a href="{{ route('goods.rank', ['category_id' => $category->id]) }}" 
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
                            <a href="{{ route('goods.rank', ['category_id' => $category->id]) }}" 
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
    <div id="rank">
    @include('partials.items', ['items' => $items,'ranked' => true,'sale' => true])
    </div>
</div>
@endsection