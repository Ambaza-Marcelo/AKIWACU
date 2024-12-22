
@extends('backend.layouts.master')

@section('title')
@lang('messages.address_edit') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.address_edit') - {{ $address->country_name }}</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.addresses.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.address_edit')</span></li>
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
                    <h4 class="header-title">Edit Address</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.addresses.update', $address->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <label for="name">Country Name</label>
                            <input type="text" class="form-control" id="name" name="country_name" value="{{ $address->country_name}}">
                        </div>
                        <div class="form-group">
                            <label for="name">City</label>
                            <input type="text" class="form-control" id="name" name="city" value="{{ $address->city}}">
                        </div>
                        <div class="form-group">
                            <label for="name">District</label>
                            <input type="text" class="form-control" id="name" name="district" value="{{ $address->district}}">
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Update Address</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
