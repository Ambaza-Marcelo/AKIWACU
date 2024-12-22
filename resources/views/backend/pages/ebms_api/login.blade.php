
@extends('backend.layouts.master')

@section('title')
@lang('Connexion') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Connexion')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('ebms_api.invoices.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Connexion')</span></li>
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
                    <h4 class="header-title">Connexion</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('ebms_api.login') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <label>Username</label>
                                <input type="text" name="username" placeholder="Enter username" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Enter password" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection