
@extends('backend.layouts.master')

@section('title')
@lang('messages.supplier_edit') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.supplier_edit') - {{ $supplier->name }}</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.suppliers.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.supplier_edit')</span></li>
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
                    <h4 class="header-title">Edit Supplier</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Supplier Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter a Supplier Name">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="mail">Supplier mail</label>
                            <input type="mail" class="form-control" id="mail" name="mail" placeholder="Enter a Supplier mail">
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone_no">Supplier Phone No</label>
                            <input type="tel" class="form-control" id="phone_no" name="phone_no" placeholder="Enter a Supplier Phone No">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="address_id">Address</label>
                            <select class="form-control" name="address_id" id="address_id">
                                <option disabled="disabled" selected="selected">Select address</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}">{{ $address->country_name}}/{{$address->city}}/{{$address->district }}</option>
                            @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="vat_taxpayer">Supplier Vat Taxpayer?</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_taxpayer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_taxpayer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone_no">Supplier TIN</label>
                                    <input type="tel" class="form-control" id="phone_no" name="phone_no" placeholder="Enter a Supplier Phone No">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control" name="category" required="required" id="category">
                                        <option disabled="disabled" selected="selected">Thank You To Select</option>
                                        @if (Auth::guard('admin')->user()->can('food_purchase.create'))
                                        <option value="FOOD">FOOD</option>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_purchase.create'))
                                        <option value="MATERIAL">MATERIAL</option>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_purchase.create'))
                                        <option value="DRINK">DRINK</option>
                                        @endif
                                    </select>
                            </div>
                            </div>
                        </div>     
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Update</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
