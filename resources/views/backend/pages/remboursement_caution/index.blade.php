
@extends('backend.layouts.master')

@section('title')
@lang('liste des remboursements cautions') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('liste des remboursements cautions')</h4>
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
                    <h4 class="header-title float-left">liste des remboursements cautions</h4>
                <form action="" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-info">Exporter En PDF</button>
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
                                    <th width="20%">Le numéro de la facture</th>
                                    <th width="10%">Date de facturation</th>
                                    <th width="10%">Serveur</th>
                                    <th width="30%">Nom du client</th>
                                    <th width="10%">Reference fa. annulee</th>
                                    <th width="10%">Motif Annulation</th>
                                    <th width="10%">Statut</th>
                                    <th width="30%">Auteur</th>
                                    <th width="10%">Signature Facture </th>
                                    <th width="10%">Date Signature Facture</th>
                                    <th></th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($factures as $facture)
                               <tr>
                                    <td>{{ $loop->index+1}}</td>
                                    <td><a href="{{ route('admin.remboursement-caution.show',$facture->invoice_number) }}">{{ $facture->invoice_number }}</a>&nbsp;@if($facture->etat === '01')<span class="badge badge-info">crédit</span>@elseif($facture->etat === '1')<span class="badge badge-info">cash</span>@endif</td>
                                    <td>{{ \Carbon\Carbon::parse($facture->invoice_date)->format('d/m/Y H:i:s') }}</td>
                                    <td>@if($facture->employe_id){{ $facture->employe->name }}@endif</td>
                                    <td>@if($facture->client_id){{ $facture->client->customer_name }} @else {{ $facture->customer_name }} @endif</td>
                                    <td>{{ $facture->cancelled_invoice_ref }}</td>
                                    <td>{{ $facture->cn_motif }}</td>
                                    <td>@if($facture->statut == 2)<span class="badge badge-info">Validée</span>@else <span class="badge badge-warning">Encours...</span>@endif</td>
                                    <td>{{ $facture->auteur }}</td>
                                    <td>{{ $facture->invoice_signature }}</td>
                                    <td>{{ $facture->invoice_signature_date }}</td>
                                    <td>
                                        @if($facture->electronic_signature)
                                        {!! QrCode::size(300)->backgroundColor(255,255,255)->generate('electronic signature: '.$facture->electronic_signature.' www.edengardenresorts.bi, Designed by www.ambazamarcellin.netlify.app' ) !!}
                                        @endif
                                    </td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('remboursement_caution.view'))
                                        <a href="{{ route('admin.remboursement-caution.facture',$facture->invoice_number) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('remboursement_caution.validate'))
                                        @if($facture->statut == 1)
                                         <a class="btn btn-primary text-white" href="@if(!empty($facture->drink_order_no)){{ route('admin.boissons-remboursement-caution.valider', $facture->invoice_number) }} @elseif(!empty($facture->food_order_no)){{ route('admin.nourritures-remboursement-caution.valider', $facture->invoice_number) }} @elseif(!empty($facture->bartender_order_no)){{ route('admin.bartender-remboursement-caution.valider', $facture->invoice_number) }} @elseif(!empty($facture->barrist_order_no)){{ route('admin.barrista-remboursement-caution.valider', $facture->invoice_number) }} @else{{ route('admin.booking-remboursement-caution.valider', $facture->invoice_number) }} @endif"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $facture->invoice_number }}').submit();this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $facture->invoice_number }}" action="@if(!empty($facture->drink_order_no)){{ route('admin.boissons-remboursement-caution.valider', $facture->invoice_number) }} @elseif(!empty($facture->food_order_no)){{ route('admin.nourritures-remboursement-caution.valider', $facture->invoice_number) }} @elseif(!empty($facture->bartender_order_no)){{ route('admin.bartender-remboursement-caution.valider', $facture->invoice_number) }} @elseif(!empty($facture->barrist_order_no)){{ route('admin.barrista-remboursement-caution.valider', $facture->invoice_number) }} @elseif(!empty($facture->booking_no)){{ route('admin.booking-remboursement-caution.valider', $facture->invoice_number) }} @endif" method="POST" style="display: none;">
                                                @method('PUT')
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

    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    window.onunload = function () {
        null
    };

     </script>
@endsection