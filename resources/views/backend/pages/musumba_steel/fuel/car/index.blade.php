
@extends('backend.layouts.master')

@section('title')
@lang('cars') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('cars')</h4>
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
                    <h4 class="header-title float-left">Liste des véhicules</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('musumba_steel_car.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.ms-cars.create') }}">Nouveau</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Marque</th>
                                    <th width="20%">Couleur</th>
                                    <th width="20%">Immatriculation</th>
                                    <th width="20%">Chassis No</th>
                                    <th width="20%">Etat</th>
                                    <th width="20%">Carburant</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($fuel_cars as $fuel_car)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $fuel_car->marque }}</td>
                                    <td>{{ $fuel_car->couleur }} </td>
                                    <td> {{ $fuel_car->immatriculation}}</td>
                                    <td> {{ $fuel_car->chassis_no}}</td>
                                    @if($fuel_car->etat == 0)
                                    <td><span class="badge badge-danger">En panne</span>
                                    @else
                                    <td><span class="badge badge-primary">En bon etat</span></td>
                                    @endif
                                    <td> {{ $fuel_car->fuel->name}}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('musumba_steel_car.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.ms-cars.edit',$fuel_car->id)}}">Editer</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('musumba_steel_car.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.ms-cars.destroy',$fuel_car->id)}}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $fuel_car->id }}').submit();"  title="si vous cliquez sur ce bouton,cette ligne sera supprimée définitivement">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $fuel_car->id }}" action="{{ route('admin.ms-cars.destroy',$fuel_car->id)}}" method="POST" style="display: none;">
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