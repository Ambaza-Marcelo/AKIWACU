
@extends('backend.layouts.master')

@section('title')
@lang('factures') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('factures')</h4>
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
                    <h4 class="header-title float-left">Liste des factures</h4>
                    <form action="{{ route('ebms_api.getInvoice') }}" method="POST">
                    <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="token" class="form-control" placeholder="Saisir le Jeton">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="invoice_signature" class="form-control" placeholder="Saisir le signature du facture">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-info">VERIFICATION FACTURE</button>
                                </div>
                            </div>
                    </p>
                </form><br>
                <form action="{{ route('ebms_api.checkTIN') }}" method="POST">
                    <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="token" class="form-control" placeholder="Saisir le Jeton">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="tp_TIN" class="form-control" placeholder="Saisir le NIF">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success">VERIFICATION NIF</button>
                                </div>
                            </div>
                    </p>
                </form><br>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Le numéro de la facture</th>
                                    <th width="10%">Date de facturation</th>
                                    <th width="10%">Serveur</th>
                                    <th width="10%">Le numéro RC </th>
                                    
                                    <th width="10%">Telephone</th>
                                    <th width="10%">Province</th>
                                    <th width="10%">Commune</th>
                                    <th width="10%">Quartier</th>
                                    <th width="10%">Avenue</th>
                                    <th width="10%">Rue</th>
                                    <th width="10%">Nom du client</th>
                                    <th width="10%">NIF du client</th>
                                    <th width="10%">Adresse du client</th>
                                    <th width="10%">Signature Facture </th>
                                    <th width="10%">Date Signature Facture</th>
                                    <th width="30%">Motif</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($factures as $facture)
                               <tr>
                                    <td>{{ $loop->index+1}}</td>
                                    <td><a href="@if($facture->drink_order_no){{ route('admin.facture.show',$facture->invoice_number) }} @elseif($facture->food_order_no) {{ route('admin.invoice-kitchens.show',$facture->invoice_number) }} @elseif($facture->bartender_order_no) {{ route('admin.bartender-invoices.show',$facture->invoice_number) }} @elseif($facture->barrist_order_no) {{ route('admin.barrist-invoices.show',$facture->invoice_number) }} @else {{ route('admin.booking-invoices.show',$facture->invoice_number) }} @endif">{{ $facture->invoice_number }}</a>&nbsp;@if($facture->etat == 0)<span class="badge badge-warning">Encours...</span>@elseif($facture->etat === '1')<span class="badge badge-success">Validée(@if($facture->cancelled_invoice == 1)<span class="badge badge-warning">note de crédit</span>@endif)</span>@elseif($facture->etat === '01')<span class="badge badge-info" title="@if($facture->client_id){{ $facture->client->customer_name }} @elseif($facture->booking_client_id) {{ $facture->bookingClient->customer_name }} @else {{ $facture->customer_name }} @endif">validé(crédit;@if($facture->cancelled_invoice == 1)<span class="badge badge-warning">note de crédit</span>@endif)</span>@else<span class="badge badge-danger" title="{{ $facture->cn_motif }}">Annulée</span>@endif</td>
                                    <td>{{ \Carbon\Carbon::parse($facture->invoice_date)->format('d/m/Y H:i:s') }}</td>
                                    <td>@if($facture->employe_id){{ $facture->employe->name }}@endif</td>
                                    <td>{{ $facture->tp_trade_number }}</td>
                                    <td>{{ $facture->tp_phone_number }}</td>
                                    <td>{{ $facture->tp_address_province }}</td>
                                    <td>{{ $facture->tp_address_commune }}</td>
                                    <td>{{ $facture->tp_address_quartier }}</td>
                                    <td>{{ $facture->tp_address_avenue }}</td>
                                    <td>{{ $facture->tp_address_rue }}</td>
                                    <td>@if($facture->client_id){{ $facture->client->customer_name }} @else {{ $facture->customer_name }} @endif</td>
                                    <td>@if($facture->client_id){{ $facture->client->customer_TIN }} @endif</td>
                                    <td>@if($facture->client_id){{ $facture->client->customer_address }} @endif</td>
                                    <td>{{ $facture->invoice_signature }}</td>
                                    <td>{{ $facture->invoice_signature_date }}</td>
                                    <td>@if($facture->cancelled_invoice == 1 || $facture->etat == -1)<span class="badge badge-danger">{{ $facture->cn_motif }}</span> ;Référence Facture : <span class="badge badge-warning">{{ $facture->invoice_ref }}</span> @endif</td>
                                    <td>
                                                                             
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection


@section('scripts')
     <!-- Start datatable js -->
     <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
     <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
     <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
     <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
     <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
     
     <script>
         /*================================
        datatable active
        ==================================*/
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                responsive: true
            });
        }

     </script>
@endsection