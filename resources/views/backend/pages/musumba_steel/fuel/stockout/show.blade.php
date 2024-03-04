
@extends('backend.layouts.master')

@section('title')
@lang('messages.stockout') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.stockout')</h4>
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
                    <h4 class="header-title float-left">Sortie</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Stockout No</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Requisition No</th>
                                    <th width="10%">Cuve de stockage</th>
                                    <th width="10%">Carburant</th>
                                    <th width="10%">Véhicule</th>
                                    <th width="10%">Chauffeur</th>
                                    <th width="10%">Quantité</th>
                                    <th width="10%">Prix Unitaire</th>
                                    <th width="10%">Valeur totale</th>
                                    <th width="10%">Description</th>
                                    <th width="20%">Auteur</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($fuel_stockouts as $fuel_stockout)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td><a href="@if($fuel_stockout->stockout_no){{ route('admin.sotb-fuel-stockouts.show',$fuel_stockout->stockout_no)}} @endif">{{ $fuel_stockout->stockout_no }} </a></td>
                                    <td>{{ $fuel_stockout->date }}</td>
                                    <td>{{ $fuel_stockout->requisition_no }}</a></td>
                                    <td>{{ $fuel_stockout->pump->name }}</td>
                                    <td>{{ $fuel_stockout->fuel->name }}</td>
                                    <td>{{ $fuel_stockout->car->marque }}-{{ $fuel_stockout->car->immatriculation }}</td>
                                    <td>{{ $fuel_stockout->driver->firstname }}&nbsp;{{ $fuel_stockout->driver->lastname }}</td>
                                    <td>{{ $fuel_stockout->quantity }}</td>
                                    <td>{{ $fuel_stockout->purchase_price }}</td>
                                    <td>{{ $fuel_stockout->total_purchase_value }}</td>
                                    <td>{{ $fuel_stockout->description }}</td>
                                    <td>{{ $fuel_stockout->created_by }}</td>
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