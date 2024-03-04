
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
                    <h4 class="header-title float-left">Mouvement du carburant</h4>
                    <!--
                        <p class="float-right mb-2">
                            <a href="{{ route('admin.fuelMovement.export')}}" title="Cliquer pour exporter en Excel" class="btn btn-info">Exporter</a>
                        </p>
                    -->
                    <form action="{{ route('admin.fuelMovement.export')}}" method="GET">
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
                    <form action="{{ route('admin.fuels.movementFuelToPdf')}}" method="GET">
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
                               @foreach($fuelMovements as $fuelMovement)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $fuelMovement->created_at }}</td>
                                    <td>{{ $fuelMovement->fuelPump->designation }}</td>
                                    <td>{{ $fuelMovement->fuelPump->fuel->nom }}</td>
                                    <td>{{ $fuelMovement->quantite_stock_initiale }}</td>
                                    <td>{{ $fuelMovement->quantite_entree }}</td>
                                    <td>{{ $fuelMovement->stock_totale }}</td>
                                    <td>{{ $fuelMovement->quantite_sortie }}</td>
                                    <td>@if($fuelMovement->driver_car_id){{ $fuelMovement->driverCar->car->immatriculation }}@endif</td>
                                    <td>@if($fuelMovement->driver_car_id){{ $fuelMovement->driverCar->driver->nom }}&nbsp;{{ $fuelMovement->driverCar->driver->prenom }}@endif</td>
                                    <td><a href="@if($fuelMovement->bon_entree){{ route('admin.fuel_stockins.show',$fuelMovement->bon_entree)}} @endif">{{ $fuelMovement->bon_entree }}</a></td>
                                    <td><a href="@if($fuelMovement->bon_sortie){{ route('admin.fuel_stockouts.show',$fuelMovement->bon_sortie)}} @endif">{{ $fuelMovement->bon_sortie }}</a></td>
                                    <td>{{ $fuelMovement->auteur }}</td>
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


<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title float-left">Les Vehicules et Leurs Consommations par Ordre Croissant,Il faut Scanner Le QrCode Pour Voir La Consommation de chaque Vehicule</h4>
                    <div class="row">
                    @foreach($cars as $car)
                    <div class="col-md-6 mt-5 mb-3">
                    <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><img src="{{ asset('img/vehicule.svg') }}" width="100"> {{$car->driverCar->car->immatriculation}}({{$car->driverCar->driver->prenom}})</div>
                                <h2>{!! QrCode::size(100)->backgroundColor(255,255,255)->generate("Ce véhicule a consomé ".$car->qtite.' litres, Designed by ICT MUSUMBA STEEL' ) !!}
                                </h2>
                            </div>
                    </div>
                    </div>
                    </div>
                    @endforeach
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