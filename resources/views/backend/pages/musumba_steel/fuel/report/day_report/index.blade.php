
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
                    <h4 class="header-title float-left">Rapport du carburant</h4>
                    <form action="{{ route('admin.report.fuels.monthReportToPdf')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" class="btn btn-info">PDF</button>
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
                                    <th width="10%">Bon Entree</th>
                                    <th width="10%">Bon Sortie</th>
                                    <th width="10%">Auteur</th> 
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($reportDays as $reportDay)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $reportDay->day }}/{{ $reportDay->month }}/{{ $reportDay->year }}</td>
                                    <td>{{ $reportDay->fuelPump->designation }}</td>
                                    <td>{{ $reportDay->fuelPump->fuel->nom }}</td>
                                    <td>{{ $reportDay->quantite_stock_initiale }} </td>
                                    <td>{{ $reportDay->quantite_e }}</td>
                                    <td>{{ $reportDay->stock_totale }} </td>
                                    <td>{{ $reportDay->quantite_s }}</td>
                                    <td>@if($reportDay->driver_car_id){{ $reportDay->driverCar->car->immatriculation }}@endif</td>
                                    <td>@if($reportDay->driver_car_id){{ $reportDay->driverCar->driver->nom }}&nbsp;{{ $reportDay->driverCar->driver->prenom }}@endif</td>
                                    <td><a href="@if($reportDay->bon_entree){{ route('admin.fuel_stockins.show',$reportDay->bon_entree)}} @endif">{{ $reportDay->bon_entree }}</a></td>
                                    <td><a href="@if($reportDay->bon_sortie){{ route('admin.fuel_stockouts.show',$reportDay->bon_sortie)}} @endif">{{ $reportDay->bon_sortie }}</a></td>
                                    <td>{{ $reportDay->auteur }}</td>
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