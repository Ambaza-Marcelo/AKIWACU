
@extends('backend.layouts.master')

@section('title')
@lang('messages.report_month') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.report_month')</h4>
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
                    <h4 class="header-title float-left">Monthly report</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Date</th>
                                    <!--
                                    <th width="10%">Article Code</th>
                                    <th width="10%">Q.S.I</th>
                                    <th width="10%">V.S.I</th>
                                    <th width="10%">Q.Stockin</th>
                                    <th width="10%">V.Stockin</th>
                                    <th width="10%">Stock Total</th>
                                    <th width="10%">Q. Stockout</th>
                                -->
                                    <th width="10%">Bon Entree</th>
                                    <th width="10%">Bon Sortie</th>
                                    <th width="10%">Auteur</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($reportMonths as $reportMonth)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $reportMonth->month }}/{{ $reportMonth->year }}</td>
                                   
                                    <td><a href="@if($reportMonth->bon_entree){{ route('admin.stockins.show',$reportMonth->bon_entree)}} @endif">{{ $reportMonth->bon_entree }} </a></td>
                                    <td><a href="@if($reportMonth->bon_sortie){{ route('admin.stockouts.show',$reportMonth->bon_sortie)}} @endif">{{ $reportMonth->bon_sortie }}</a></td>
                                    <td>{{ $reportMonth->created_by }} </td>
                                    <td></td>
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