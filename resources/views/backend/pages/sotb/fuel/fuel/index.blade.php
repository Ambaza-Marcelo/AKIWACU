
@extends('backend.layouts.master')

@section('title')
@lang('Carburant') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Carburant')</h4>
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
                    <h4 class="header-title float-left">Carburant</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('sotb_fuel.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.sotb-fuels.create') }}">Nouveau</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Designation</th>
                                    <th width="20%">Prix Achat</th>
                                    <th width="20%">Date de creation</th>
                                    <th width="20%">Auteur</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($fuels as $fuel)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $fuel->name }}</td>
                                    <td>{{ $fuel->purchase_price }} </td>
                                    <td> {{ $fuel->created_at}}</td>
                                    <td> {{ $fuel->auteur}}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('sotb_fuel.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.sotb-fuels.edit',$fuel->id)}}">Editer</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('sotb_fuel.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.sotb-fuels.destroy',$fuel->id)}}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $fuel->id }}').submit();" title="si vous cliquez sur ce bouton,cette ligne sera supprimée définitivement">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $fuel->id }}" action="{{ route('admin.sotb-fuels.destroy',$fuel->id)}}" method="POST" style="display: none;">
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