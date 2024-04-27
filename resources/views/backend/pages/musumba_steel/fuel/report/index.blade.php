
@extends('backend.layouts.master')

@section('title')
@lang('Rapport du carburant') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Rapport du carburant')</h4>
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
                    <div>
                        <h4 class="header-title float-left">Mouvement du carburant</h4>
                    </div>
                <p>
                    <form action="{{ route('admin.ms-fuel-report.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" class="btn btn-success" title="Exporter en Excel">Exporter En Excel</button>
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
                    </form>
                </p>
                <p>
                    <form action="{{ route('admin.ms-fuel-report.export-to-pdf')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" class="btn btn-info">Exporter En PDF</button>
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
                    </form>
                </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Cuve</th>
                                    <th width="10%">Carburant</th>
                                    <th width="10%">Q. Stock Initial</th>
                                    <th width="10%">Quantite Entree</th>
                                    <th width="10%">Stock Total</th>
                                    <th width="10%">Quantite Sortie</th>
                                    <th width="10%">Plaque</th>
                                    <th width="10%">Chauffeur</th>
                                    <th width="10%">Stock Final</th>
                                    <th width="10%">Auteur</th> 
                                    <th>Description</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($fuelMovements as $fuelMovement)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($fuelMovement->date)->format('d/m/Y') }}</td>
                                    <td>{{ $fuelMovement->pump->name }}</td>
                                    <td>{{ $fuelMovement->pump->fuel->name }}</td>
                                    <td>{{ $fuelMovement->quantity_stock_initial }}</td>
                                    <td>@if($fuelMovement->quantity_inventory){{ $fuelMovement->quantity_inventory }} @elseif($fuelMovement->quantity_stockin) {{ $fuelMovement->quantity_stockin }} @else - @endif</td>
                                    <td>@if($fuelMovement->quantity_inventory){{ $fuelMovement->quantity_inventory }} @elseif($fuelMovement->quantity_stockin) {{ $fuelMovement->quantity_stock_initial + $fuelMovement->quantity_stockin }} @else - @endif</td>
                                    <td>{{ $fuelMovement->quantity_stockout }}</td>
                                    <td>@if($fuelMovement->car_id){{ $fuelMovement->car->immatriculation }}@endif</td>
                                    <td>@if($fuelMovement->driver_id){{ $fuelMovement->driver->firstname }}&nbsp;{{ $fuelMovement->driver->lastname }}@endif</td>
                                    <td>@if($fuelMovement->quantity_inventory){{ $fuelMovement->quantity_inventory }} @elseif($fuelMovement->quantity_stockout) {{ ($fuelMovement->quantity_stock_initial + $fuelMovement->quantity_stockin) - ($fuelMovement->quantity_stockout) }}@else - @endif</td>
                                    <td>{{ $fuelMovement->created_by }}</td>
                                    <td>{{ $fuelMovement->description }}</td>
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