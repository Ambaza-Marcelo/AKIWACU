
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
                    <li><a href="{{ route('admin.barrist-requisitions.index') }}">@lang('messages.list')</a></li>
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
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_investment_data_re_sh9x.svg') }}" width="200">

                                    @lang('Cotisation')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg4">
                        <a href="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_product_teardown_re_m1pc.svg') }}" width="200">

                                    @lang('Impot')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            </div>
            <div class="row">
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg5">
                        <a href="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_project_complete_lwss.svg') }}" width="200">

                                    @lang('Prime')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg2">
                        <a href="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_visual_data_re_mxxo.svg') }}" width="200">

                                    @lang('Indemnite')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            </div>
            <div class="row">
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg7">
                        <a href="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_team_re_0bfe.svg') }}" width="200">

                                    @lang('Conge Paye')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg8">
                        <a href="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_in_no_time_-6-igu.svg') }}" width="200">

                                    @lang('Conge')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            </div>
            <div class="row">
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_by_the_road_re_vvs7.svg') }}" width="200">

                                    @lang('Absence')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg4">
                        <a href="">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_laravel_and_vue_-59-tp.svg') }}" width="200">

                                    @lang('Heure Supplementaire')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
