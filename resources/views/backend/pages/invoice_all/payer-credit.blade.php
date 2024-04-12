
@extends('backend.layouts.master')

@section('title')
@lang('Validation Credit de facture') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Validation Credit de facture')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('ebms_api.invoices.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Validation Credit de facture')</span></li>
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
                    <h4 class="header-title">Validation Credit Facture : {{$facture->invoice_number}}</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.facture-credit.payer', $facture->invoice_number) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if($facture->employe_id)
                        <div class="col-md-12">
                                <label for="employe_id">Serveur</label>
                                <select class="form-control" name="employe_id" id="employe_id">
                                <option disabled="disabled">Merci de choisir un Serveur</option>
                                <option value="{{$facture->employe_id}}" selected="selected">{{$facture->employe->name}}</option>
                            </select>
                        </div>
                        @endif
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
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="client_id">@lang('Nom du Client')</label>
                                <select class="form-control" name="client_id">
                                <option disabled="disabled" selected="selected">Merci de choisir client</option>
                                @foreach ($clients as $client)
                                <option value="{{ $client->id }}" {{ $facture->client_id == $client->id ? 'selected' : '' }} class="form-control">{{ $client->customer_name }}/{{ $client->telephone }}</option>
                                @endforeach
                                </select>
                            </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="statut_paied">@lang('Type de Paiement')</label>
                                <select class="form-control" name="statut_paied">
                                <option disabled="disabled" selected="selected">Merci de choisir client</option>
                                <option value="1" class="form-control">CASH</option>
                                <option value="2" class="form-control">Banque</option>
                                <option value="3" class="form-control">Lumicash</option>
                                <option value="4" class="form-control">Autres</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            
                        </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Article</th>
                                <th>Quantite</th>
                                <th>Prix Unitaire</th>
                                <th>TC</th>
                                <th>PFL</th>
                                <th>Prix HTVA </th>
                                <th>Montant TVA </th>
                                <th>P.V TVAC </th>
                                <th>P.V total </th>
                               <!-- <th>Action</th> -->
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td><input type="text" name="item_designation[]" value="@if($data->drink_id) {{ $data->drink->name }} @elseif($data->food_item_id) {{ $data->foodItem->name }} @elseif($data->barrist_item_id) {{ $data->barristItem->name }} @elseif($data->bartender_id) {{ $data->bartenderItem->name }} @elseif($data->salle_id) {{ $data->salle->name }} @elseif($data->service_id) {{ $data->service->name }} @elseif($data->table_id) {{ $data->table->name }} @endif" class="form-control" readonly /></td>  
                                <td><input type="text" step='any' min='0' name="item_quantity[]" value="{{ $data->item_quantity }}" class="form-control" readonly /></td>  
                                <td><input type="text" step='any' min='0' name="item_price[]" value="{{ $data->item_price }}" class="form-control" readonly /></td>
                                <td><input type="text" step='any' min='0' name="item_ct[]" value="{{ $data->item_ct }}" class="form-control" readonly/></td>   
                                <td><input type="text" step='any' min='0' name="item_tl[]" value="{{ $data->item_tl }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="item_price_nvat[]" value="{{ $data->item_price_nvat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="vat[]" value="{{ $data->vat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="item_price_wvat[]" value="{{ $data->item_price_wvat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="Item_total_amount[]" value="{{ $data->item_total_amount }}" class="form-control" readonly/></td>
                                <!--<td><button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" title="Ajouter Plus" aria-hidden="false"></i></button></td> -->
                            </tr> 
                            @endforeach
                        </table> 
                        <div class="col-md-2 pull-right">
                            <input type="text" class="form-control" value="{{ number_format($total_amount,0,',',' ')}}" readonly>
                        </div>
                        @if (Auth::guard('admin')->user()->can('invoice_drink.view'))
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ route('admin.facture-brouillon.imprimer',$facture->invoice_number) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a> 
                        @endif
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">VALIDER CREDIT</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">


    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('statut_paied'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('statut_paied'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of cancelled_invoice

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('cancelled_invoice'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('cancelled_invoice'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })


</script>
@endsection