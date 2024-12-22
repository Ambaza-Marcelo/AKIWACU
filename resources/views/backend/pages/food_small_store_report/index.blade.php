
@extends('backend.layouts.master')

@section('title')
@lang('RAPPORT DU STOCK DE NOURRITURES (PETIT STOCK)') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('RAPPORT DU STOCK DE NOURRITURES (PETIT STOCK)')</h4>
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
                    <form action="{{ route('admin.food-small-store-report.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="search" title="Cliquer pour exporter en Excel" class="btn btn-primary">Exporter En Excel</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="start_date">@lang('Date Debut')</label>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date">@lang('Date Fin')</label>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="code_store">@lang('Stock')</label>
                                        <select class="form-control" name="code_store" id="code_store">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->code}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>
                        </p>
                    </form>
                    <form action="{{ route('admin.food-small-store-report.export-to-pdf')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-info" title="Cliquer pour exporter en PDF">Exporter En PDF</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="start_date">@lang('Date Debut')</label>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date">@lang('Date Fin')</label>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="code_store">@lang('Stock')</label>
                                        <select class="form-control" name="code_store" id="code_store">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->code}}">{{$store->code}}/{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    </div>
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
                                    <th width="10%">Stock Initial</th>
                                    <th width="10%">Unité de mesure</th>
                                    <th width="10%">C.U.M.P</th>
                                    <th width="10%">Entrée Transfert</th>
                                    <th width="10%">Entrée Autre</th>
                                    <th width="10%">Portionnage</th>
                                    <th width="10%">Quantité Sortie</th>
                                    <th width="10%">Stock Final</th>
                                    <th width="10%">Type Transaction</th>
                                    <th width="10%">No Document</th>
                                    <th width="10%">Auteur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $data->food->name }} </td>
                                    <td>{{ $data->food->code }} </td>
                                    <td>{{ $data->quantity_stock_initial_portion }} </td>
                                    <td>{{ $data->food->foodMeasurement->production_unit }}</td>
                                    <td>{{ $data->cump }}</td>
                                    <td>{{ $data->quantity_transfer }}</td>
                                    <td>{{ $data->quantity_stockin }}</td>
                                    <td>{{ $data->quantity_portion }}</td>
                                    <td>{{ $data->quantity_stockout }}</td>
                                    <td>{{ $data->quantity_stock_final_portion }}</td>
                                    <td>{{ $data->type_transaction }}</td>
                                    <td>{{ $data->document_no }}</td>
                                    <td>@if($data->created_portion_by){{ $data->created_portion_by }} @else {{ $data->created_by }} @endif</td>
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