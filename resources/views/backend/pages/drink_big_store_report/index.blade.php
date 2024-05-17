
@extends('backend.layouts.master')

@section('title')
@lang('RAPPORT DU STOCK DE BOISSONS (GRAND STOCK)') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('RAPPORT DU STOCK DE BOISSONS (GRAND STOCK)')</h4>
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
                    
                    <form action="{{ route('admin.drink-big-store-report.export-to-excel')}}" method="GET">
                        <table>
                            <tr>
                                <th>Date Debut</th>
                                <th>Date Fin</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </td>
                                <td>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </td>
                                <td>
                                    <select class="form-control" name="code_store" id="code_store">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->code}}">{{$store->code}}/{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" value="pdf" class="btn btn-success" title="Cliquer pour exporter en PDF">Exporter R. Stock Intermediaire Boissons</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <form action="{{ route('admin.drink-big-store-report.export-to-pdf')}}" method="GET">
                        <table>
                            <tr>
                                <th>Date Debut</th>
                                <th>Date Fin</th>
                                <th>Stock</th>
                                <th>Article</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </td>
                                <td>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </td>
                                <td>
                                    <select class="form-control" name="code_store" id="code_store">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->code}}">{{$store->code}}/{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="drink_id" id="drink_id">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($drinks as $drink)
                                            <option value="{{$drink->id}}">{{$drink->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" value="pdf" class="btn btn-info" title="Cliquer pour exporter en PDF">Exporter En PDF</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $data->drink->name }} </td>
                                    <td>{{ $data->drink->code }} </td>
                                    <td>{{ $data->quantity_stock_initial }} </td>
                                    @php 
                                        $value_stock_initial = $data->quantity_stock_initial * $data->drink->cump;
                                    @endphp
                                    <td>{{ number_format($value_stock_initial,0,',',' ') }} </td>
                                    <td>@if($data->quantity_stockin){{ $data->quantity_stockin }} @elseif($data->quantity_reception) {{ $data->quantity_reception }} @endif </td>
                                    <td>@if($data->value_stockin){{ number_format($data->value_stockin,0,',',' ') }} @elseif($data->value_reception) {{ number_format($data->value_reception,0,',',' ') }} @endif </td>
                                    <td>@if($data->quantity_stockout){{ $data->quantity_stockout }} @elseif($data->quantity_transfer) {{ $data->quantity_transfer }} @endif </td>
                                    @php
                                        $value_stockout = $data->quantity_stockout * $data->drink->cump;

                                        $value_transfer = $data->quantity_transfer * $data->drink->cump;
                                    @endphp
                                    <td>@if($data->value_stockout){{ number_format($value_stockout,0,',',' ') }} @elseif($data->value_transfer) {{ number_format($value_transfer,0,',',' ') }} @endif </td>
                                    <td>{{ ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer) }} </td>
                                    @php
                                        $quantite_finale = ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer);

                                        $quantity_stock_initial = $data->quantity_stock_initial;
                                    @endphp

                                    <td>{{ number_format(($quantite_finale * $data->drink->cump),0,',',' ') }} </td>
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