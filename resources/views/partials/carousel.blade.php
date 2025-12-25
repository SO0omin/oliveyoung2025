<div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($carousels as $carousel)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <a href="{{ $carousel->event_id ? route('event.show', $carousel->event_id) : '#' }}">
                    <div class="slide-data"
                        data-title="{{ $carousel->title }}"
                        data-content="{{ $carousel->content }}"
                        data-company="{{ $carousel->event->company->name ?? '' }}">
                    </div>

                    <img src="{{ asset('storage/carousel_img/'.$carousel->pic) }}" class="d-block w-100" alt="{{ $carousel->title }}">
                </a>
            </div>
        @endforeach
    </div>
</div>