
@extends('backend.layouts.master')

@section('title')
@lang('Details sur facture') - @lang('messages.admin_panel')
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('Details sur facture')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('messages.list')</span></li>
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
                    <h4 class="header-title">Details sur le Facture No {{ $facture->invoice_number }}</h4>
                    @include('backend.layouts.partials.messages')
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Date Facture</label>
                                <input type="text" value="{{ $facture->invoice_date}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label>Signature Electronique Facture</label>
                                <input type="text" value="{{ $facture->invoice_signature}}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="invoice_type">Type Facture</label>
                                <div class="form-group">
                                    @if($facture->invoice_type == 'FN')
                                    <label class="text">F. Normale
                                    <input type="checkbox" value="FN" checked="checked" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    @endif
                                    @if($facture->invoice_type == 'RC')
                                    <label class="text">R. Caution
                                    <input type="checkbox" checked="checked" value="RC" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    @endif
                                    @if($facture->invoice_type == 'RHF')
                                    <label class="text">Reduction HF
                                    <input type="checkbox" checked="checked" value="RHF" class="form-control" readonly>
                                    </label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="tp_type">Type Contribuable</label>
                                <div class="form-group">
                                    @if($facture->tp_type == '1')
                                    <label class="text">Personne Physique
                                    <input type="checkbox" name="tp_type" value="1" checked="checked" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    @endif
                                    @if($facture->tp_type == '2')
                                    <label class="text">Personne Morale
                                    <input type="checkbox" name="tp_type" value="2" class="form-control" readonly>
                                    </label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_name">Nom et Prenom</label>
                                <input type="text" value="{{ $facture->tp_name}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_TIN">NIF Contribuable</label>
                                <input type="text" value="{{ $facture->tp_TIN}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_trade_number">RC du Contribuable</label>
                                <input type="text" value="{{ $facture->tp_trade_number}}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_postal_number">Boite Postal</label>
                                <input type="text" value="{{ $facture->tp_postal_number}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_phone_number">Tel. du Contribuable</label>
                                <input type="text" value="{{ $facture->tp_phone_number}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_province">Province</label>
                                <input type="text" value="{{ $facture->tp_address_province}}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_address_commune">Commune</label>
                                <input type="text" value="{{ $facture->tp_address_commune}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_quartier">Quartier</label>
                                <input type="text" value="{{ $facture->tp_address_quartier}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_avenue">Avenue</label>
                                <input type="text" value="{{ $facture->tp_address_avenue}}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_address_rue">Rue</label>
                                <input type="text" value="{{ $facture->tp_address_rue}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_number">Numero</label>
                                <input type="text" value="{{ $facture->tp_address_number}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="ct_taxpayer">Assujetti à la taxe de conso.</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="ct_taxpayer" value="0" checked="checked" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="ct_taxpayer" value="1" class="form-control" readonly>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tl_taxpayer">Assujetti au PFL</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="tl_taxpayer" value="0" checked="checked" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="tl_taxpayer" value="1" class="form-control" readonly>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_fiscal_center">Centre Fiscale</label>
                                <div class="form-group">
                                    <label class="text">DGC
                                    <input type="checkbox" name="tp_fiscal_center" value="DGC" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">DMC
                                    <input type="checkbox" checked="checked" name="tp_fiscal_center" value="DMC" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">DPMC
                                    <input type="checkbox" name="tp_fiscal_center" value="DPMC" class="form-control" readonly>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_activity_sector">Secteur d'activité</label>
                                <input type="text" value="{{ $facture->tp_activity_sector}}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_legal_form">Forme Juridique</label>
                                <input type="text" value="{{ $facture->tp_legal_form}}" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="payment_type">Type de Paiement</label>
                                <div class="form-group">
                                    <label class="text">Espece
                                    <input type="checkbox" name="payment_type" value="1" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Banque
                                    <input type="checkbox" name="payment_type" value="2" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Credit
                                    <input type="checkbox" name="payment_type" value="3" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Autres
                                    <input type="checkbox" name="payment_type" value="4" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="invoice_currency">Type de Monaie</label>
                                <div class="form-group">
                                    <label class="text">BIF
                                    <input type="checkbox" name="tp_fiscal_center" value="BIF" checked="checked" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">USD
                                    <input type="checkbox" name="tp_fiscal_center" value="USD" class="form-control" readonly>
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">EUR
                                    <input type="checkbox" name="tp_fiscal_center" value="EUR" class="form-control" readonly>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="customer_name">Nom du client</label>
                                <input type="text" value="@if($facture->client_id) {{ $facture->client->customer_name }} @else {{ $facture->customer_name}} @endif" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="customer_TIN">NIF du client</label>
                                <input type="text" value="@if($facture->client_id) {{ $facture->client->customer_TIN }} @else {{ $facture->customer_TIN}} @endif" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="customer_address">Adresse du client</label>
                                <input type="text" value="@if($facture->client_id) {{ $facture->client->customer_address }} @else {{ $facture->customer_address}} @endif" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="vat_customer_payer">client est assujetti TVA?</label>
                                <div class="form-group">
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_customer_payer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_customer_payer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <br>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Article</th>
                                <th>Quantite</th>
                                <th>Prix Unitaire</th>
                                <th>TC</th>
                                <th>P HTVA</th>
                                <th>TVA</th>
                                <th>TVAC</th>
                                <th>PFL</th>
                                <th>Taxe Spec.</th>
                                <th>Over The Top</th>
                                <th>PVT</th>
                            </tr>
                            @foreach($factureDetails as $factureDetail)
                            <tr>  
                                <td><input type="text" value="{{ $factureDetail->article->name }}" class="form-control" readonly></td>  
                                <td><input type="text" value="{{ $factureDetail->item_quantity }}" class="form-control" readonly /></td>  
                                <td><input type="text" value="{{ $factureDetail->item_price }}" class="form-control" readonly /></td>
                                <td><input type="text" value="{{ $factureDetail->item_ct }}" class="form-control" readonly /></td>   
                                <td><input type="text" value="{{ $factureDetail->item_price_nvat }}" class="form-control" readonly /></td> 
                                <td><input type="text" value="{{ $factureDetail->vat }}" class="form-control" readonly /></td>
                                <td><input type="text" value="{{ $factureDetail->item_price_wvat }}" class="form-control" readonly /></td> 
                                <td><input type="text" value="{{ $factureDetail->item_tl }}" class="form-control" readonly /></td>
                                <td><input type="text" value="{{ $factureDetail->item_tsce_tax }}" class="form-control" readonly /></td>
                                <td><input type="text" value="{{ $factureDetail->item_ott_tax }}" class="form-control" readonly /></td>
                                <td><input type="text" value="{{ $factureDetail->item_total_amount }}" class="form-control" readonly /></td>
                            </tr> 
                            @endforeach
                        </table> 
                        <div class="col-md-2 pull-right">
                            <input type="text" class="form-control" value="{{ number_format($total_amount,0,',',' ')}}" readonly>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
