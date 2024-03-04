
@extends('backend.layouts.master')

@section('title')
@lang('Les index') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Les index')</h4>
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
                    <h4 class="header-title float-left">Liste des index</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('sotb_index_pump.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.sotb-fuel-index-pumps.create') }}">Nouveau</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Date</th>
                                    <th width="20%">Index de Départ</th>
                                    <th width="20%">Index de Fin</th>
                                    <th width="20%">Quantité Sortie</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($fuel_index_pumps as $fuel_index_pump)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($fuel_index_pump->date)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $fuel_index_pump->start_index }} </td>
                                    <td> {{ $fuel_index_pump->end_index}}</td>
                                    <td> {{ $fuel_index_pump->final_index}}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('sotb_index_pump.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.sotb-fuel-index-pumps.edit',$fuel_index_pump->id)}}">Editer</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('sotb_index_pump.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.sotb-fuel-index-pumps.destroy',$fuel_index_pump->id)}}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $fuel_index_pump->id }}').submit();" title="si vous cliquez sur ce bouton,cette ligne sera supprimée définitivement">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-" action="{{ route('admin.sotb-fuel-index-pumps.destroy',$fuel_index_pump->id)}}" method="POST" style="display: none;">
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