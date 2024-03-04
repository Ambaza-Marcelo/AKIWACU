
@extends('backend.layouts.master')

@section('title')
@lang('messages.personnalized_report') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.personnalized_report')</h4>
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
                    <h4 class="header-title float-left">personnalized report</h4>
                    <div class="clearfix">
                        <form action="{{ route('admin.report.stock.personalized')}}" method="GET">
                        @csrf
                        <table class="table table-bordered" id="dynamicTable">  
                            <tr>    
                                <td><input type="date" name="start_date"></td>
                                <td><input type="date" name="end_date"></td>    
                                <td><button type="submit"><span class="">Search</span></button></td>  
                            </tr>  
                        </table>      
                    </form>
                    </div>
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
                               @foreach($reports as $report)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $report->day }}</td>
                                   
                                    <td><a href="@if($report->bon_entree){{ route('admin.stockins.show',$report->bon_entree)}} @endif">{{ $report->bon_entree }} </a></td>
                                    <td><a href="@if($report->bon_sortie){{ route('admin.stockouts.show',$report->bon_sortie)}} @endif">{{ $report->bon_sortie }}</a></td>
                                    <td>{{ $report->created_by }} </td>
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