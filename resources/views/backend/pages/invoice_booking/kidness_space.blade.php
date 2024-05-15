
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
                    <h4 class="header-title float-left">Liste des factures(Kidness Space)</h4>
                <form action="{{ route('admin.facture-rapport.reservation')}}" method="GET">
                        <table>
                            <tr>
                                <th>Date Debut</th>
                                <th>Date Fin</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </td>
                                <td>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </td>
                                <td>
                                    <select class="form-control" name="kidness_space_id" id="kidness_space_id">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                            <option value="KIDNESS">KIDNESS SPACE</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" value="pdf" class="btn btn-info" title="Cliquer pour exporter en PDF">Exporter En PDF</button>
                                </td>
                            </tr>
                        </table>
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
                                    <th width="10%">Le numéro RC </th>
                                    <!--
                                    <th width="10%">Telephone</th>
                                    <th width="10%">Province</th>
                                    <th width="10%">Commune</th>
                                    <th width="10%">Quartier</th>
                                    <th width="10%">Avenue</th>
                                    <th width="10%">Rue</th>
                                -->
                                    <th width="30%">Nom du client</th>
                                    <th width="10%">Designation</th>
                                    <th width="10%">Quantite</th>
                                    <th width="10%">P.V</th>
                                    <th width="10%">TTC</th>
                                    <th width="30%">Auteur</th>
                                    <th width="10%">Description</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($factures as $facture)
                               <tr>
                                    <td>{{ $loop->index+1}}</td>
                                    <td><a href="{{ route('admin.booking-invoices.show',$facture->invoice_number) }}">{{ $facture->invoice_number }}</a>&nbsp;@if($facture->etat == 0)<span class="badge badge-warning">Encours...</span>@elseif($facture->etat === '1')<span class="badge badge-success">Validée</span>@elseif($facture->etat ==2)<span class="badge badge-success">Envoyée</span>@elseif($facture->etat === '01')<span class="badge badge-info" title="@if($facture->client_id){{ $facture->client->customer_name }} @elseif($facture->booking_client_id) {{ $facture->bookingClient->customer_name }} @else {{ $facture->customer_name }} @endif">validé(crédit)</span>@else<span class="badge badge-danger" title="{{ $facture->cn_motif }}">Annulée</span>@endif</td>
                                    <td>{{ \Carbon\Carbon::parse($facture->invoice_date)->format('d/m/Y') }}</td>
                                    <td>{{ $facture->tp_trade_number }}</td>
                                    <!--
                                    <td>{{ $facture->tp_phone_number }}</td>
                                    <td>{{ $facture->tp_address_province }}</td>
                                    <td>{{ $facture->tp_address_commune }}</td>
                                    <td>{{ $facture->tp_address_quartier }}</td>
                                    <td>{{ $facture->tp_address_avenue }}</td>
                                    <td>{{ $facture->tp_address_rue }}</td>
                                -->
                                    <td>@if($facture->booking_client_id){{ $facture->bookingClient->customer_name }} @elseif($facture->client_id){{ $facture->client->customer_name }} @else {{ $facture->customer_name }} @endif</td>
                                    <td><span class="badge badge-success">{{ $facture->kidnessSpace->name }}</span></td>
                                    <td>{{ $facture->item_quantity }}</td>
                                    <td>{{ number_format($facture->item_price,0,',',' ') }}</td>
                                    <td>{{ number_format($facture->item_total_amount ,0,',',' ')}}</td>
                                    <td>{{ $facture->auteur }}</td>
                                    <td>{{ $facture->description }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('invoice_drink.create'))
                                        @if($facture->statut != '1')
                                        <a href="{{ route('admin.facture.imprimer',$facture->invoice_number) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @endif
                                        
                                        @if (Auth::guard('admin')->user()->can('invoice_drink.validate'))
                                        @if($facture->etat == 0)
                                         <a class="btn btn-primary text-white" href="{{ route('admin.facture-booking.validate', $facture->invoice_number) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $facture->invoice_number }}').submit();this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $facture->invoice_number }}" action="{{ route('admin.facture-booking.validate', $facture->invoice_number) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('invoice_drink.validate'))
                                        @if($facture->etat == 0)
                                         <a href="{{ route('admin.voir-facture.credit', $facture->invoice_number) }}" class="btn btn-info">Valider avec Credit</a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('invoice_drink.reset'))
                                        @if($facture->etat == 0)
                                         <a href="{{ route('admin.voir-facture.reset', $facture->invoice_number) }}" class="btn btn-success">Annuler</a>
                                        @endif
                                        @endif 
                                        @if (Auth::guard('admin')->user()->can('invoice_drink.delete'))
                                        @if($facture->etat == 0)
                                         <a class="btn btn-danger text-white" href="{{ route('ebms_api.destroy',$facture->invoice_number) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $facture->invoice_number }}').submit();">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $facture->invoice_number }}" action="{{ route('ebms_api.destroy',$facture->invoice_number) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
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