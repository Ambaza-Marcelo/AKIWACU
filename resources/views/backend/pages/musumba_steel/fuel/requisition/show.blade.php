
@extends('backend.layouts.master')

@section('title')
@lang('demande de recquisition') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('demande de recquisition')</h4>
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
                    <h4 class="header-title float-left">Liste des recquisitions</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Date</th>
                                    <th width="20%">Requisition No</th>
                                    <th width="20%">Carburant</th>
                                    <th width="20%">Chauffeur</th>
                                    <th width="20%">Véhicule</th>
                                    <th width="20%">Quantité</th>
                                    <th width="50%">Description</th>
                                    <th width="20%">Auteur</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($requisitions as $fuel_requisition)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $fuel_requisition->date }}</td>
                                    <td>{{ $fuel_requisition->requisition_no }} </td>
                                    <td> {{ $fuel_requisition->fuel->name}}</td>
                                    <td> {{ $fuel_requisition->driver->firstname}}&nbsp;{{ $fuel_requisition->driver->lastname}}</td>
                                    <td> {{ $fuel_requisition->car->marque}}-{{ $fuel_requisition->car->immatriculation}}</td>
                                    <td> {{ $fuel_requisition->quantity_requisitioned }}</td>
                                    <td> {{ $fuel_requisition->description}}</td>
                                    <td> {{ $fuel_requisition->auteur}}</td>
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