@extends('layouts.default')

@section('main')
<div class="section center no-pad-bot" id="index-banner">
    <h1 class="header center yellow-text text-darken-2">
        <img src="{{ asset('img/suntzu-avatar.png') }}" alt=""
             class="circle responsive-img s2 z-depth-3">
        <br>
        {{ $title }}
    </h1>
    <br>

    <form method="get" action="{{ route('quotes.show') }}">
        <div class="row">
            <div class="col s6 offset-s3">
                <div class="input-field">
                    <label for="quote">Enter the quote</label>
                    <input autofocus name="quote" type="text" class="" value="{{ old('quote') }}">
                </div>
                <button class="btn waves-effect waves-light z-depth-2" type="submit">
                    Generate quote
                    <i class="material-icons right">send</i>
                </button>
            </div>
        </div>
    </form>
</div>
@stop
