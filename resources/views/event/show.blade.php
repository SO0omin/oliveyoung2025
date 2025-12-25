@extends('main')
@section('content')

<div class="event-content gojeong mb-3">
    <h3>{{ $row->title }}</h3>
    {{-- 상세 이미지들 --}}
    @if($row->images && $row->images->count() > 0)
        <div class="event-images d-flex flex-column align-items-center">
            @foreach($row->images as $img)
                {{-- 💡 [수정] ms-auto 제거 --}}
                <img src="{{ asset('storage/event_uploads/' . $img->img_path) }}" alt="상세 이미지" class="img-fluid">
            @endforeach
        </div>
    @else
        <div class="event-images d-flex flex-column align-items-center">
            {{-- 💡 [수정] ms-auto 제거 --}}
            <img src="{{ asset('storage/event_uploads/' . $row->pic) }}" alt="상세 이미지" class="img-fluid">
        </div>
    @endif
</div>
<div class="item-list gojeong">
    <hr class="thick-hr mb-3">
    @include('partials.items', ['items' => $items])
</div>

@endsection