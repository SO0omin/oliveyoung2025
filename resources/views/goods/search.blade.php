@extends('main')

@section('content')
<div class="gojeong mt-3">
    <h4 class="mt-3 mb-3">"{{ $q }}"에 대한 검색결과</h4>
    <hr>

    @if($items->isEmpty())
    <div id="non-search" class="mt-3 d-flex flex-column justify-content-center align-items-center text-center text-secondary">
        <i id="icon-s" class="fa-solid fa-circle-exclamation mb-3"></i>
        <h6 class="mb-1">검색 결과가 없습니다.</h6>
        <p class="mb-0">
            철자를 확인하거나<br>
            다른 키워드로 검색해보세요.
        </p>
    </div>
    @else
        @include('partials.items', [
            'items' => $items,
            'ranked' => false,
            'sale' => false
        ])
    @endif

</div>
@endsection