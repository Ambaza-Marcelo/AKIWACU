
@extends('backend.layouts.master')

@section('title')
@lang('messages.address_create') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.address_create')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.addresses.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.address_create')</span></li>
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
                    <h4 class="header-title">Create New Address</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.addresses.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Country Name</label>
                            <input type="text" class="form-control" id="name" name="country_name" placeholder="Enter a Country Name">
                        </div>
                        <div class="form-group">
                            <label for="name">City</label>
                            <input type="text" class="form-control" id="name" name="city" placeholder="Enter a City">
                        </div>
                        <div class="form-group">
                            <label for="name">District</label>
                            <input type="text" class="form-control" id="name" name="district" placeholder="Enter a  District">
                        </div>
                       
                        
                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary mt-4 pr-4 pl-4">Save Address</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
