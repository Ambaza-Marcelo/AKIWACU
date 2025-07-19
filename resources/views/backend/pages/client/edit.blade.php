
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
                    <li><a href="{{ route('admin.clients.index') }}">@lang('messages.list')</a></li>
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
                    
                    <form action="{{ route('admin.clients.update',$client->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="tp_type">Type de contribuable</label>
                            <select name="tp_type" id="tp_type" class="form-control">
                                <option disabled selected>merci de choisir</option>
                                <option value="1">Personne Physique</option>
                                <option value="2">société locale</option>
                                <option value="3">société étrangère</option>
                            </select>
                        </div>
                        </div>
                        <div class="col-md-6" id="customer_TIN">
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="customer_name">

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
                            <label for="customer_address">Adresse du Client</label>
                            <input type="text" class="form-control" id="customer_address" name="customer_address" value="{{ $client->customer_address }}">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="mail">E-mail du Client</label>
                            <input type="text" class="form-control" id="mail" name="mail" value="{{ $client->mail }}">
                        </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="vat_customer_payer">Client est assujetti a la TVA?</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_customer_payer" value="0" @if($client->vat_customer_payer == '0') checked="checked" @endif class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_customer_payer" value="1" @if($client->vat_customer_payer == '1') checked="checked" @endif class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary mt-4 pr-4 pl-4">@lang('messages.update') </button>
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

    $('#tp_type').change(function () { 
    if ($(this).val() === '2'){

        var customer_TIN = "<div class='form-group'>"+
                            "<label for='customer_TIN'>NIF du Client<strong style='color: red;'>*</strong></label>"+
                                "<input type='text' class='form-control' name='customer_TIN' placeholder='Entrer le NIF du Client' required>"+
                        "</div>";
        
        $("#customer_TIN").append(customer_TIN);
        $("#customer_name").hidden();

    }

    if ($(this).val() === '1'){

        var customer_name = "<div class='form-group'>"+
                            "<label for='customer_name'>Nom du Client<strong style='color: red;'>*</strong></label>"+
                                "<input type='text' class='form-control' name='customer_name' placeholder='Entrer le NIF du Client' required>"+
                        "</div>";
        
        $("#customer_name").append(customer_name);
    }

    if ($(this).val() === '3'){

        var customer_name = "<div class='form-group'>"+
                            "<label for='customer_name'>Nom du Client<strong style='color: red;'>*</strong></label>"+
                                "<input type='text' class='form-control' name='customer_name' placeholder='Entrer le NIF du Client' required>"+
                        "</div>";
        
        $("#customer_name").append(customer_name);
    }

    })
    .trigger( "change" );

    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    window.onunload = function () {
        null
    };

</script>
@endsection
