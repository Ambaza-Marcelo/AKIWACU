
@extends('backend.layouts.master')

@section('title')
@lang('Modifier du client') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier du client')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.musumba-steel-clients.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier du client')</span></li>
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
                    <h4 class="header-title">Modifier du Client</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.musumba-steel-clients.update',$client->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name">Nom du Client</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $client->customer_name }}">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="telephone">Telephone du Client</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" value="{{ $client->telephone }}">
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_TIN">NIF du Client</label>
                            <input type="text" class="form-control" id="customer_TIN" name="customer_TIN" value="{{ $client->customer_TIN }}">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_address">Adresse du Client</label>
                            <input type="text" class="form-control" id="customer_address" name="customer_address" value="{{ $client->customer_address }}">
                        </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="vat_customer_payer">Client est assujetti a la TVA?</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_customer_payer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_customer_payer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">@lang('messages.update') </button>
                    </form>
                </div>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">

    //one checked box in checkbox group of vat_supplier_payer

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('vat_customer_payer'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('vat_customer_payer'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

</script>
@endsection
