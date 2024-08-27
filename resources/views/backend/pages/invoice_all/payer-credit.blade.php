
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
                    <li><a href="{{ route('admin.credit-invoices.list') }}">@lang('messages.list')</a></li>
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
                    <h4 class="header-title">Validation paiement Facture</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.facture-credit.payer',$facture->invoice_number) }}" method="POST">
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
                            <div class="col-sm-5">
                                <label for="tp_name">Nom et Prenom</label>
                                <input type="text" value="{{ $facture->tp_name }}" name="tp_name" class="form-control" readonly>
                            </div>
                            <div class="col-sm-5">
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
                                <label for="">Province</label>
                                <input type="text" value="{{ $facture->tp_address_province }}" name="" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="etat_recouvrement">@lang('Type de Paiement')</label>
                                <select class="form-control" name="etat_recouvrement" id="etat_recouvrement" required>
                                <option disabled="disabled" selected="selected">Merci de choisir client</option>
                                <option value="1" class="form-control">Paiement partiel</option>
                                <option value="2" class="form-control">Paiement Total</option>
                                </select>
                            </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="statut_paied">@lang('Mode de Paiement')</label>
                                <select class="form-control" name="statut_paied" id="statut_paied" required>
                                <option disabled="disabled" selected="selected">Merci de choisir client</option>
                                <option value="1" class="form-control">CASH</option>
                                <option value="2" class="form-control">Banque</option>
                                <option value="3" class="form-control">Lumicash</option>
                                <option value="4" class="form-control">Autres</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="row" id="mode_paiement">
                            
                        </div>
                        <div class="row" id="type_paiement">
                            <div class="col-md-4">
                                <label for="reste_credit">Reste Credit</label>
                                <input type="number" name="reste_credit" min="0"value="{{ $reste_credit }}" class="form-control" readonly required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_recouvrement">Date de Recouvrement</label>
                                <input type="date" name="date_recouvrement" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nom_recouvrement">Nom Charge de recouvrement</label>
                                <input type="text" name="nom_recouvrement" value="{{ Auth::guard('admin')->user()->name }}" class="form-control" required>
                            </div>
                        </div>
                        <br>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Date facture</th>
                                <th>No Facture</th>
                                <th>Article</th>
                                <th>Quantite</th>
                                <th>Prix Unitaire</th>
                                <th>Prix HTVA </th>
                                <th>Montant TVA </th>
                                <th>P.V TVAC </th>
                                <th>P.V total </th>
                               <!-- <th>Action</th> -->
                            </tr>
                            @foreach($datas as $data)
                            <tr>
                                <td><input type="text" value="{{ \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y') }}" class="form-control" readonly/></td>   
                                <td><input type="text" value="{{ $data->invoice_number }}" class="form-control" readonly/></td>  
                                <td><input type="text" name="item_designation[]" value="@if($data->drink_id) {{ $data->drink->name }} @elseif($data->food_item_id) {{ $data->foodItem->name }} @elseif($data->barrist_item_id) {{ $data->barristItem->name }} @elseif($data->bartender_id) {{ $data->bartenderItem->name }} @elseif($data->salle_id) {{ $data->salle->name }} @elseif($data->service_id) {{ $data->service->name }} @elseif($data->table_id) {{ $data->table->name }} @endif" class="form-control" readonly /></td>  
                                <td><input type="text" step='any' min='0' name="item_quantity[]" value="{{ $data->item_quantity }}" class="form-control" readonly /></td>  
                                <td><input type="text" step='any' min='0' name="item_price[]" value="{{ $data->item_price }}" class="form-control" readonly /></td>
                                <td><input type="text" step='any' min='0' name="item_price_nvat[]" value="{{ $data->item_price_nvat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="vat[]" value="{{ $data->vat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="item_price_wvat[]" value="{{ $data->item_price_wvat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="Item_total_amount[]" value="{{ $data->item_total_amount }}" class="form-control" readonly/></td>
                                <!--<td><button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" title="Ajouter Plus" aria-hidden="false"></i></button></td> -->
                            </tr> 
                            @endforeach
                        </table> 
                        <div>
                            <label for="note_recouvrement"></label>
                            <textarea name="note_recouvrement" id="note_recouvrement" class="form-control" required>
                                PAIEMENT
                            </textarea>
                        </div>
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


    $('#statut_paied').change(function () { 
    if ($(this).val() === '2'){

        var banque = "<div class='col-md-6'>"+
                            "<label for='bank_name'>merci de choisir<strong style='color: red;'>*</strong></label>"+
                            "<select name='bank_name' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "<option value='BCB'>BCB</option>"+
                                "<option value='BANCOBU'>BANCOBU</option>"+
                                "<option value='BBCI'>BBCI</option>"+
                                "<option value='CRDB'>CRDB</option>"+
                                "<option value='COOPEC'>COOPEC</option>"+
                                "<option value='CORILAC'>CORILAC</option>"+
                                "<option value='ECOBANK'>ECOBANK</option>"+
                                "<option value='FINBANK'>FINBANK</option>"+
                                "<option value='KCB'>KCB</option>"+
                                "<option value='INTERBANK'>INTERBANK</option>"+
                            +
                        "</div>";
        var cheque_no = "<div class='col-md-6'>"+
                            "<label for='cheque_no'>No Cheque<strong style='color: red;'>*</strong></label>"+
                                "<input type='text' name='cheque_no' placeholder='Saisir no cheque ou no bordereau' class='form-control'/>"+
                        "</div>";
        
        $("#mode_paiement").append([banque,cheque_no]);
    }

    })
    .trigger( "change" );

    $('#etat_recouvrement').change(function () { 
    if ($(this).val() === '2'){

        var montant_total_credit = "<div class='col-md-4'>"+
                            "<label for='montant_total_credit'>Montant Total a payer<strong style='color: red;'>*</strong></label>"+
                                "<input type='number' name='montant_total_credit' value='{{ $total_amount }}' class='form-control' readonly/>"+
                        "</div>";
        var montant_recouvre = "<div class='col-md-4'>"+
                            "<label for='montant_recouvre'>Montant paye<strong style='color: red;'>*</strong></label>"+
                                "<input type='number' name='montant_recouvre' value='{{ $total_amount }}' class='form-control' min='0' max='{{ $total_amount }}' required/>"+
                        "</div>";
        
        $("#type_paiement").append([montant_total_credit,montant_recouvre]);
    }

    if ($(this).val() === '1'){

        var montant_total_credit = "<div class='col-md-4'>"+
                            "<label for='montant_total_credit'>Montant Total a payer<strong style='color: red;'>*</strong></label>"+
                                "<input type='number' name='montant_total_credit' value='{{ $total_amount }}' class='form-control' readonly/>"+
                        "</div>";
        var montant_recouvre = "<div class='col-md-4'>"+
                            "<label for='montant_recouvre'>Montant paye<strong style='color: red;'>*</strong></label>"+
                                "<input type='number' name='montant_recouvre' value='{{ $reste_credit }}' min='0' max='{{ $reste_credit }}' class='form-control' required/>"+
                        "</div>";
        
        $("#type_paiement").append([montant_total_credit,montant_recouvre]);
    }

    })
    .trigger( "change" );

</script>
@endsection