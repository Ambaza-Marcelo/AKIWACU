
@extends('backend.layouts.master')

@section('title')
@lang('chauffeur vehicule') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('chauffeur vehicule')</h4>
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
                    <h4 class="header-title float-left">Liste des véhicules chauffeurs</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('fuel_driver_car.create'))
                            <a class="btn btn-success text-white" href="" title="Exporter en PDF"><i class="fa fa-file-pdf-o"></i>&nbsp;Exporter</a>
                            <a class="btn btn-primary text-white" href="{{ route('admin.driver_cars.create') }}">Nouveau</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Nom</th>
                                    <th width="10%">Prenom</th>
                                    <th width="10%">Téléphone</th>
                                    <th width="20%">Marque</th>
                                    <th width="20%">Couleur</th>
                                    <th width="20%">Immatriculation</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($fuelDriverCars as $fuelDriverCar)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $fuelDriverCar->driver->nom }}</td>
                                    <td>{{ $fuelDriverCar->driver->prenom }}</td>
                                    <td>{{ $fuelDriverCar->driver->telephone }}</td>
                                    <td>{{ $fuelDriverCar->car->marque }}</td>
                                    <td>{{ $fuelDriverCar->car->couleur }}</td>
                                    <td>{{ $fuelDriverCar->car->immatriculation }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('fuel_driver_car.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.driver_cars.edit', $fuelDriverCar->id) }}">Editer</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('fuel_driver_car.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.driver_cars.destroy', $fuelDriverCar->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $fuelDriverCar->id }}').submit();" title="si vous cliquez sur ce bouton,cette ligne sera supprimée définitivement">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $fuelDriverCar->id }}" action="{{ route('admin.driver_cars.destroy', $fuelDriverCar->id) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
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