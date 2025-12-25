@extends('main')
@section('content')

<div class="container mt-5" style="max-width:300px; text-align:center;">
    <h4>CAPTCHA 테스트</h4>

    <form id="captcha_test_form" method="POST" action="{{ route('captcha.submit') }}">
        @csrf

        <div style="margin-top:20px;">
            {!! NoCaptcha::display() !!}
            @error('g-recaptcha-response')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">제출</button>
    </form>
</div>

{!! NoCaptcha::renderJs() !!}

@endsection