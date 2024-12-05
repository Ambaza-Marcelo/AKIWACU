@extends('errors.errors_layout')

@section('title')
404 - @lang('messages.404')
@endsection

@section('error-content')
    <h2>404</h2>
    <p>@lang('messages.404')</p>
    <a href="{{ route('admin.dashboard') }}">@lang('messages.back_to_dashboard')</a>
    <a href="{{ route('admin.login') }}">@lang('messages.login_again')!</a>
@endsection