
@extends('backend.layouts.master')

@section('title')
@lang('Thank You To Choose') - @lang('messages.admin_panel')
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('Thank You To Choose')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-material-stockins.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Thank You To Choose')</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">@lang('Thank You To Choose')</h4>
                    @include('backend.layouts.partials.messages')
            <div class="row">
                @foreach($stores as $store)
                <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.sotb-material-stockins.createFromBig',$store->code) }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_coming_home_re_ausc.svg') }}" width="65">

                                    {{ $store->name }}</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            @endforeach
            </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
