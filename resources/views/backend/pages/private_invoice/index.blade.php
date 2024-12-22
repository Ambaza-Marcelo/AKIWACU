
@extends('backend.layouts.master')

@section('title')
@lang('bons expeditions') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('bons expeditions')</h4>
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
                    <h4 class="header-title float-left">Liste des bons expeditions magasins EGR</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('private_drink_stockout.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.private-factures.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                <form action="{{ route('admin.private-factures.export-to-excel') }}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">Exporter En Excel</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control">
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
                                    <th width="20%">Expedition no</th>
                                    <th width="10%">Date de transfert</th>
                                    <th width="30%">Nom du client</th>
                                    <th width="10%">NIF du client</th>
                                    <th width="10%">Adresse du client</th>
                                    <th width="30%">Auteur</th>
                                    <th width="10%">Signature Expedition </th>
                                    <th width="10%">Date Signature Expedition</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($factures as $facture)
                               <tr>
                                    <td>{{ $loop->index+1}}</td>
                                    <td><a href="{{ route('admin.private-factures.show',$facture->invoice_number) }}">{{ $facture->invoice_number }}</a>&nbsp;@if($facture->etat == 0)<span class="badge badge-warning">Encours...</span>@elseif($facture->etat === '1')<span class="badge badge-success">Validée</span>@elseif($facture->etat ==2)<span class="badge badge-success">Envoyée</span>@elseif($facture->etat === '01')<span class="badge badge-info" title="{{ $facture->customer_name }}">validé(crédit)</span>@else<span class="badge badge-danger" title="{{ $facture->cn_motif }}">Annulée</span>@endif</td>
                                    <td>{{ \Carbon\Carbon::parse($facture->invoice_date)->format('d/m/Y') }}</td>
                                    <td>{{ $facture->customer_name }} </td>
                                    <td>{{ $facture->customer_TIN }}</td>
                                    <td>{{ $facture->customer_address }}</td>
                                    <td>{{ $facture->auteur }}</td>
                                    <td>{{ $facture->invoice_signature }}</td>
                                    <td>{{ $facture->invoice_signature_date }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('private_sales.create'))
                                        @if($facture->statut != '1')
                                        <a href="{{ route('admin.private-factures.generatepdf',$facture->invoice_number) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @endif
                                        
                                        @if (Auth::guard('admin')->user()->can('private_sales.validate'))
                                        @if($facture->etat == 0)
                                         <a class="btn btn-primary text-white" href="{{ route('admin.private-factures.validate-cash', $facture->invoice_number) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $facture->invoice_number }}').submit();this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $facture->invoice_number }}" action="{{ route('admin.private-factures.validate-cash', $facture->invoice_number) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('private_sales.validate'))
                                        @if($facture->etat == 0)
                                         <a href="{{ route('admin.private-factures.voir-facture-a-credit', $facture->invoice_number) }}" class="btn btn-info">Valider avec Credit</a>
                                        @endif
                                        @endif
                                        <!--
                                        @if (Auth::guard('admin')->user()->can('private_sales.validate'))
                                        @if($facture->etat == 01)
                                         <a href="{{ route('admin.private-factures.voir-facture-a-recouvrer', $facture->invoice_number) }}" class="btn btn-info">Recouvrement</a>
                                        @endif
                                        @endif
                                        -->
                                        @if (Auth::guard('admin')->user()->can('private_sales.reset'))
                                        @if($facture->etat == 0)
                                         <a href="#" class="btn btn-success">Annuler</a>
                                        @endif
                                        @endif 
                                        @if (Auth::guard('admin')->user()->can('private_sales.delete'))
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