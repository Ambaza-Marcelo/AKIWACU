@extends('errors.errors_layout')

@section('title')
    500 - @lang('messages.500')
@endsection

@section('error-content')
    <h2>500</h2>
    <p>@lang('messages.500')</p>
    <a href="{{ route('admin.dashboard') }}">@lang('messages.back_to_dashboard')</a>
    <a href="{{ route('admin.login') }}">@lang('messages.login_again')!</a>
@endsection