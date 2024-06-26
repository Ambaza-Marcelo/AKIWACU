
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
                    <div class="col-md-4 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="seo-fact sbg3">
                                <a href="{{ route('admin.plan-purchase-drinks.index') }}">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <img src="{{ asset('img/undraw_empty_cart_co35.svg') }}" width="200">

                                    @lang('PLANNING BOISSONS')</div>
                                    </div>
                                </a>
                            </div>
                        </div><br>
                    </div>
                    <div class="col-md-4 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="seo-fact sbg4">
                                <a href="{{ route('admin.plan-purchase-foods.index') }}">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <img src="{{ asset('img/undraw_empty_cart_co35.svg') }}" width="200">

                                    @lang('PLANNING NOURRITURES')</div>
                                <h2>
                                </h2>
                                    </div>
                                </a>
                            </div>
                        </div><br>
                    </div>
                    <div class="col-md-4 mb-3 mb-lg-0">
                        <div class="card">
                            <div class="seo-fact sbg4">
                                <a href="{{ route('admin.plan-purchase-materials.index') }}">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <img src="{{ asset('img/undraw_empty_cart_co35.svg') }}" width="200">

                                    @lang('PLANNING MATERIELS')</div>
                                <h2>
                                </h2>
                                    </div>
                                </a>
                            </div>
                        </div><br>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
