
@extends('backend.layouts.master')

@section('title')
@lang('pompes') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('pompes')</h4>
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
                    <h4 class="header-title float-left">Liste des pompes</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_pump.create'))
                            <a class="btn btn-success text-white" href="" title="Exporter en PDF"><i class="fa fa-file-pdf-o"></i>&nbsp;Exporter</a>
                            <a class="btn btn-primary text-white" href="{{ route('admin.ms-fuel-pumps.create') }}">Nouveau</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">designation</th>
                                    <th width="10%">code</th>
                                    <th width="10%">emplacement</th>
                                    <th width="30%">capacite</th>
                                    <th width="10%">Carburant</th>
                                    <th width="10%">Quantité</th>
                                    <th width="10%">Prix Achat</th>
                                    <th width="30%">Valeur Totale</th>
                                    <th width="30%">Auteur</th>
                                    <th width="30%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($pumps as $pump)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $pump->name }}</td>
                                    <td>{{ $pump->code }}</td>
                                    <td>{{ $pump->emplacement }}</td>
                                    <td>{{ number_format($pump->capacity,2,',',' ') }}</td>
                                    <td>{{ $pump->fuel->name }}</td>
                                    <td>{{ number_format($pump->quantity,2,',',' ') }}</td>
                                    <td>{{ number_format($pump->fuel->purchase_price,2,',',' ') }}</td>
                                    <td>{{ number_format($pump->total_purchase_value,2,',',' ') }}</td>
                                    <td>{{ $pump->auteur }}</td>
                                    <td>

                                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_pump.delete'))
                                            <a class="btn btn-info text-white" href="{{ route('admin.ms-fuel-pumps.edit', $pump->id) }}">
                                                Editer
                                            </a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('musumba_steel_fuel_pump.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.ms-fuel-pumps.destroy', $pump->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $pump->id }}').submit();" title="si vous cliquez sur ce bouton,cette ligne sera supprimée définitivement">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $pump->id }}" action="{{ route('admin.ms-fuel-pumps.destroy', $pump->id) }}" method="POST" style="display: none;">
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