@extends('main')
@section('content')

<div class="container-fluid main-header-event">
    <div class="gojeong main-header-content">
        <h2>이벤트</h2>
        <span>풍성한 이벤트! 즐거운 올리브영</span>
    </div>
</div>

<div class="event-list gojeong mt-4">
    <div class="row">
        @foreach($list as $row)
        <div class="col-6 col-md-3 mb-4">
            <a href="{{ route('event.show', $row->id) }}" class="text-decoration-none text-dark">
                <img src="{{ asset('storage/event_uploads/' . $row->pic) }}" class="img-fluid mb-2" alt="{{ $row->title }}">
                <div class="event-item p-3 border rounded text-center">
                    {{ $row->title }}
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection