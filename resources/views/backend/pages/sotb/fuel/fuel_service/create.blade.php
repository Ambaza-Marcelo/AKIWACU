
@extends('backend.layouts.master')

@section('title')
@lang('messages.service') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.service')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.services.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.service')</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Create New Service</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.services.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Service Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter a Service Name">
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Save Service</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection