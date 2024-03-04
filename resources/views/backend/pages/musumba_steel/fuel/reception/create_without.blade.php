
@extends('backend.layouts.master')

@section('title')
@lang('messages.reception') - @lang('messages.admin_panel')
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('messages.reception')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.ms-fuel-receptions.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.reception')</span></li>
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
                    <h4 class="header-title">@lang('messages.new')</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.ms-fuel-reception-without-order.store') }}" method="POST">
                        @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">@lang('messages.date')</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="purchase_no">Purchase No</label>
                                <input type="text" class="form-control" id="purchase_no" name="purchase_no" value="{{$purchase_no}}" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                                <label for="invoice_currency">Type de Monaie</label>
                                <div class="form-group">
                                    <label class="text">BIF
                                    <input type="checkbox" name="invoice_currency" value="BIF" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">USD
                                    <input type="checkbox" name="invoice_currency" value="USD" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">EUR
                                    <input type="checkbox" name="invoice_currency" value="EUR" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label for="supplier_id">@lang('Fournisseur')</label>
                                <select class="form-control" name="supplier_id" id="supplier_id">
                                 <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                @endforeach
                             </select>
                            </div>
                        </div>
                            <div class="col-md-4">
                                <label for="vat_supplier_payer">Fournisseur est assujetti TVA?</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_supplier_payer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_supplier_payer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receptionist">@lang('messages.receptionist')</label>
                                <input type="text" class="form-control" id="receptionist" name="receptionist" placeholder="Enter receptionist" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="handingover">@lang('messages.handingover')</label>
                                <input type="text" class="form-control" id="handingover" name="handingover" placeholder="Enter handingover" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_no">@lang('messages.invoice_number')</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" placeholder="Enter invoice_no">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pump_id">Cuve de Stockage</label>
                                <select class="form-control" name="pump_id" id="pump_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($pumps as $pump)
                                <option value="{{ $pump->id }}" class="form-control">{{ $pump->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Type de Carburant</th>
                                <th>Quantité Commandée</th>
                                <th>Quantité Receptionnée</th>
                                <th>Prix d'Achat</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td><select id="driver-dropdown" class="form-control" name="fuel_id[]" id="fuel_id">
                                <option disabled="disabled">merci de choisir</option>
                                @foreach($fuels as $fuel)
                                <option value="{{ $fuel->id }}" selected="selected" class="form-control">{{ $fuel->name }}</option>
                                @endforeach
                                </select></td>
                                <td><input type="number" name="quantity_ordered[]" class="form-control"  step="any" min="0" value="{{ $data->quantity }}" /></td>
                                <td><input type="number" name="quantity_received[]" class="form-control"  step="any" min="0" value="{{ $data->quantity }}" /></td> 
                                <td><input type="number" name="purchase_price[]" class="form-control"  step="any" min="0" value="{{ $data->price }}" /></td> 
                                <td>     
                            </tr>
                            @endforeach
                        </table> 
                        <div class="col-lg-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Entrer description">
                                ENTREE DU CARBURANT AU CUVE DE STOCKAGE
                            </textarea>
                        </div>  
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary">@lang('messages.save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript"> 


    //one checked box in checkbox group of invoice_currency

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('invoice_currency'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('invoice_currency'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of vat_supplier_payer

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('vat_supplier_payer'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('vat_supplier_payer'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

</script>
@endsection
