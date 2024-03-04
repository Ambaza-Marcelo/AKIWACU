
@extends('backend.layouts.master')

@section('title')
@lang('reception') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.reception')</h4>
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
                    <h4 class="header-title float-left">reception List</h4>
                    <p class="float-right mb-2">
                        <a class="btn btn-success text-white" href="" title="Exportet en Excel"><i class="fa fa-file-excel-o"></i>&nbsp;Export To Excel</a>
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Reception No</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Facture No</th>
                                    <th width="10%">Carburant</th>
                                    <th width="10%">Quantite</th>
                                    <th width="10%">Prix Unitaire</th>
                                    <th width="10%">Valeur totale</th>
                                    <th width="10%">Quantite restante</th>
                                    <th width="10%">valeur restante</th>
                                    <th width="10%">Commande No</th>
                                    <th width="10%">Fournisseur</th>
                                    <th width="10%">Receptionniste</th>
                                    <th width="10%">Auteur</th>
                                    <th width="20%">Description</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($receptions as $reception)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td><a href="{{ route('admin.sotb-fuel-receptions.show',$reception->invoice_no)}}">{{ $reception->order_no }}</a></td>
                                    <td>{{ $reception->date }}</td>
                                    <td>{{ $reception->invoice_no }}</td>
                                    <td>{{ $reception->fuel->name }}</td>
                                    <td>{{ $reception->quantity_received }}</td>
                                    <td>{{ $reception->purchase_price }}</td>
                                    <td>{{ $reception->total_amount_received }}</td>
                                    <td>{{ $reception->quantity_remaining }}</td>
                                    <td>{{ $reception->total_amount_remaining }}</td>
                                    <td><a href="@if($reception->order_no){{ route('admin.sotb-fuel-supplier-orders.show',$reception->order_no)}}@endif">{{ $reception->order_no }}</a></td>
                                    <td>{{ $reception->supplier->name }}/{{ $reception->supplier->phone_no }}</td>
                                    <td>{{ $reception->receptionnist }}</td>
                                    <td>{{ $reception->auteur }}</td>
                                    <td>{{ $reception->description }}</td>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
     
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