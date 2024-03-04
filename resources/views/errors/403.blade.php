@extends('errors.errors_layout')

@section('title')
    403 - @lang('messages.403')
@endsection

@section('error-content')
    <h2>403</h2>
    <p>@lang('messages.403')</p>
    <hr>
    <p class="mt-2">
        {{ $exception->getMessage() }}
    </p>
    <a href="{{ route('admin.dashboard') }}">@lang('messages.back_to_dashboard')</a>
    <a href="{{ route('admin.login') }}">@lang('messages.login_again')!</a>
@endsection