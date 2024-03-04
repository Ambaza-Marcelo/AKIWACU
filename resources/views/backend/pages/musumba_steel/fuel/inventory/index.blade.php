
@extends('backend.layouts.master')

@section('title')
@lang('Inventaire du stock carburant') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Inventaire du stock carburant')</h4>
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
                    <h4 class="header-title float-left">Inventaire du stock carburant</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_inventory.create'))
                            <a class="btn btn-success text-white" href="" title="Exporter en PDF"><i class="fa fa-file-pdf-o"></i>&nbsp;Exporter</a>
                            <a class="btn btn-primary text-white" href="{{ route('admin.ms-fuel-inventories.create') }}">Nouveau</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Inventaire No</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Titre</th>
                                    <th width="10%">Auteur</th>
                                    <th width="10%">Etat</th>
                                    <th width="65%">Description</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($fuel_inventories as $fuel_inventory)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td><a href="{{ route('admin.ms-fuel-inventories.show', $fuel_inventory->inventory_no) }}">{{ $fuel_inventory->inventory_no }}</a></td>
                                    <td>{{ $fuel_inventory->date }}</td>
                                    <td>{{ $fuel_inventory->title }}</td>
                                    <td>{{ $fuel_inventory->created_by }}</td>
                                    @if($fuel_inventory->status == 0)
                                    <td><span class="badge badge-primary">Encours </span></td>
                                    @elseif($fuel_inventory->status == -1)
                                    <td><span class="badge badge-danger">Rejeté </span></td>
                                    @else
                                    <td><span class="badge badge-success">Validé </span></td>
                                    @endif
                                    <td>{{ $fuel_inventory->description }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('fuel_inventory.edit'))
                                        @if($fuel_inventory->etat != 2)
                                            <a class="btn btn-success text-white" href="{{ route('admin.ms-fuel-inventories.edit', $fuel_inventory->inventory_no) }}">Editer</a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_inventory.edit'))
                                        @if($fuel_inventory->etat != 2)
                                            <a class="btn btn-danger text-white" href="{{ route('admin.ms-fuel-inventories.destroy', $fuel_inventory->inventory_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $fuel_inventory->inventory_no }}').submit();">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $fuel_inventory->inventory_no }}" action="{{ route('admin.ms-fuel-inventories.destroy', $fuel_inventory->inventory_no) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_inventory.edit'))
                                        @if($fuel_inventory->etat != 2)
                                          <a class="btn btn-primary text-white" href="{{ route('admin.ms-fuel-inventories.validate', $fuel_inventory->inventory_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $fuel_inventory->inventory_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $fuel_inventory->inventory_no }}" action="{{ route('admin.ms-fuel-inventories.validate', $fuel_inventory->inventory_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_inventory.edit'))
                                        @if($fuel_inventory->etat != 2)
                                          <a class="btn btn-primary text-white" href="{{ route('admin.ms-fuel-inventories.reject', $fuel_inventory->inventory_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $fuel_inventory->inventory_no }}').submit();">
                                                Rejeter
                                            </a>

                                            <form id="reject-form-{{ $fuel_inventory->inventory_no }}" action="{{ route('admin.ms-fuel-inventories.reject', $fuel_inventory->inventory_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_inventory.edit'))
                                        @if($fuel_inventory->etat == 2 || $fuel_inventory->etat == 1)
                                          <a class="btn btn-primary text-white" href="{{ route('admin.ms-fuel-inventories.reset', $fuel_inventory->inventory_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $fuel_inventory->inventory_no }}').submit();">
                                                Annuler
                                            </a>

                                            <form id="reset-form-{{ $fuel_inventory->inventory_no }}" action="{{ route('admin.ms-fuel-inventories.reset', $fuel_inventory->inventory_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_inventory.create'))
                                        <a href="{{ route('admin.ms-fuel-inventories.generatePdf', $fuel_inventory->inventory_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
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