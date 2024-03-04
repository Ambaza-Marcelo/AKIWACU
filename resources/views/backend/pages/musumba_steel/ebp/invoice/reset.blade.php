
@extends('backend.layouts.master')

@section('title')
@lang('Annulation de facture') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Annulation de facture')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.musumba-steel-facture.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Annulation de facture')</span></li>
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
                    <h4 class="header-title">Annulation Facture : {{$facture->invoice_number}}</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.musumba-steel-facture.reset', $facture->invoice_number) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="invoice_number">Numero Facture</label>
                                <input type="text" value="{{ $facture->invoice_number }}" name="invoice_number" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_name">Nom et Prenom</label>
                                <input type="text" value="{{ $facture->tp_name }}" name="tp_name" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_TIN">NIF Contribuable</label>
                                <input type="text" value="{{ $facture->tp_TIN }}" name="tp_TIN" class="form-control" readonly>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_trade_number">RC du Contribuable</label>
                                <input type="text" value="{{ $facture->tp_trade_number }}" name="tp_trade_number" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_phone_number">Tel. du Contribuable</label>
                                <input type="text" value="{{ $facture->tp_phone_number }}" name="tp_phone_number" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_province">Province</label>
                                <input type="text" value="{{ $facture->tp_address_province }}" name="tp_address_province" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="customer_name">Nom du client</label>
                                <input type="text" value="{{ $facture->customer_name }}" name="customer_name" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="customer_TIN">NIF du client</label>
                                <input type="text" value="{{ $facture->customer_TIN }}" name="customer_TIN" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="customer_address">Adresse du client</label>
                                <input type="text" value="{{ $facture->customer_address }}" name="customer_address" class="form-control" readonly>
                            </div>
                        </div>
                        <br>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Article</th>
                                <th>Quantite</th>
                                <th>Prix Unitaire</th>
                                <th>TC</th>
                                <th>P HTVA </th>
                                <th>TVA </th>
                                <th>TVAC </th>
                                <th>PFL</th>
                                <th>PVT </th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td><input type="text" name="item_designation[]" value="@if($data->article_id) {{ $data->article->name }} @endif" class="form-control" readonly /></td>  
                                <td><input type="text" step='any' min='0' name="item_quantity[]" value="{{ $data->item_quantity }}" class="form-control" readonly /></td>  
                                <td><input type="text" step='any' min='0' name="item_price[]" value="{{ $data->item_price }}" class="form-control" readonly /></td>
                                <td><input type="text" step='any' min='0' name="item_ct[]" value="{{ $data->item_ct }}" class="form-control" readonly/></td>   
                                <td><input type="text" step='any' min='0' name="item_price_nvat[]" value="{{ $data->item_price_nvat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="vat[]" value="{{ $data->vat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="item_price_wvat[]" value="{{ $data->item_price_wvat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="item_tl[]" value="{{ $data->item_tl }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="Item_total_amount[]" value="{{ $data->item_total_amount }}" class="form-control" readonly/></td>
                            </tr> 
                            @endforeach
                        </table> 
                        <div class="col-md-12">
                          <div class="form-group">
                              <textarea name="cn_motif" class="form-control">
                            MOI {{ Auth::guard('admin')->user()->name }},J'ANNULE CETTE FACTURE ({{$facture->invoice_number}}) PARCEQUE...
                              </textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Annuler</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection