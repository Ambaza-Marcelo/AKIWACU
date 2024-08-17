
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
                    <li><a href="#">@lang('messages.list')</a></li>
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
                        @if (Auth::guard('admin')->user()->can('invoice_swiming_pool.view'))
                        <div class="col-md-4 mb-3 mb-lg-0">
                            <div class="card">
                            <div class="seo-fact sbg3">
                                <a href="{{ route('admin.booking-swiming-pool-invoices.index') }}">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <img src="{{ asset('img/piscine1.jpg') }}" width="100">

                                    @lang('PISCINE')</div>
                                    </div>
                                </a>
                            </div>
                            </div><br>
                        </div>
                        @endif
                        @if (Auth::guard('admin')->user()->can('invoice_booking.view'))
                        <div class="col-md-4 mb-3 mb-lg-0">
                            <div class="card">
                            <div class="seo-fact sbg3">
                                <a href="{{ route('admin.booking-salle-invoices.index') }}">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <img src="{{ asset('img/salle.jpg') }}" width="100">

                                    @lang('SALLE')</div>
                                    </div>
                                </a>
                            </div>
                            </div><br>
                        </div>
                        @endif
                        @if (Auth::guard('admin')->user()->can('invoice_kidness_space.view'))
                        <div class="col-md-4 mb-3 mb-lg-0">
                            <div class="card">
                            <div class="seo-fact sbg3">
                                <a href="{{ route('admin.booking-kidness-space-invoices.index') }}">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <img src="{{ asset('img/undraw_toy_car_-7-umw.svg') }}" width="100">

                                    @lang('KIDNESS SPACE')</div>
                                    </div>
                                </a>
                            </div>
                            </div><br>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (Auth::guard('admin')->user()->can('invoice_booking.view'))
                        <div class="col-md-4 mb-3 mb-lg-0">
                            <div class="card">
                            <div class="seo-fact sbg3">
                                <a href="{{ route('admin.booking-service-invoices.index') }}">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <img src="{{ asset('img/piscine2.jpg') }}" width="100">

                                    @lang('SERVICE')</div>
                                    </div>
                                </a>
                            </div>
                            </div><br>
                        </div>
                        @endif
                        @if (Auth::guard('admin')->user()->can('invoice_breakfast.view'))
                        <div class="col-md-4 mb-3 mb-lg-0">
                            <div class="card">
                            <div class="seo-fact sbg3">
                                <a href="{{ route('admin.booking-breakfast-invoices.index') }}">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <img src="{{ asset('img/undraw_special_event-001.svg') }}" width="100">

                                    @lang('BREAKFAST')</div>
                                    </div>
                                </a>
                            </div>
                            </div><br>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
