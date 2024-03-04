
@extends('backend.layouts.master')

@section('title')
@lang('RAPPORT DU STOCK DE MATERIELS (GRAND STOCK)') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('RAPPORT DU STOCK DE MATERIELS DE BUREAU')</h4>
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
                    <form action="{{ route('admin.ms-material-store-report.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="search" title="Cliquer pour exporter en Excel" class="btn btn-primary">Exporter En Excel</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="start_date">@lang('Date Debut')</label>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date">@lang('Date Fin')</label>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </div>
                            </div>
                        </p>
                    </form>
                    <form action="{{ route('admin.ms-material-store-report.export-to-pdf')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-info" title="Cliquer pour exporter en PDF">Exporter En PDF</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="start_date">@lang('Date Debut')</label>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date">@lang('Date Fin')</label>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
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
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('messages.code')</th>
                                    <th width="10%">Q. S. Initial</th>
                                    <th width="10%">V. S. Initial</th>
                                    <th width="10%">Q. Entree/Reception</th>
                                    <th width="10%">V. Entree/Reception</th>
                                    <th width="10%">Q. Sortie</th>
                                    <th width="10%">V. Sortie</th>
                                    <th width="10%">Q. S. Final</th>
                                    <th width="10%">V. S. Final</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $data->material->name }} </td>
                                    <td>{{ $data->material->code }} </td>
                                    <td>{{ $data->quantity_stock_initial }} </td>
                                    <td>{{ $data->value_stock_initial }} </td>
                                    <td>@if($data->quantity_stockin){{ $data->quantity_stockin }} @elseif($data->quantity_reception) {{ $data->quantity_reception }} @endif </td>
                                    <td>@if($data->value_stockin){{ $data->value_stockin }} @elseif($data->value_reception) {{ $data->value_reception }} @endif </td>
                                    <td>@if($data->quantity_stockout){{ $data->quantity_stockout }} @elseif($data->quantity_transfer) {{ $data->quantity_transfer }} @endif </td>
                                    <td>@if($data->value_stockout){{ $data->value_stockout }} @elseif($data->value_transfer) {{ $data->value_transfer }} @endif </td>
                                    <td>{{ $data->quantity_stock_final }} </td>
                                    <td>{{ $data->value_stock_final }} </td>
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