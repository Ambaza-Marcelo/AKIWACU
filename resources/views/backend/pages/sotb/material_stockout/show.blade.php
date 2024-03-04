
@extends('backend.layouts.master')

@section('title')
@lang('details des sorties') - @lang('messages.admin_panel')
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
@endsection

@section('admin-content')
<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title float-left">Details des sorties sur bon de sortie No : {{ $code }}</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">Stockout No</th>
                                    <th width="10%">Asker</th>
                                    <th width="10%">Code Store</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('messages.quantity')</th>
                                    <th width="10%">@lang('messages.unit')</th>
                                    <th width="10%">Destination</th>
                                    <th width="10%">@lang('messages.purchase_price')</th>
                                    <th width="10%">@lang('messages.selling_price')</th>
                                    <th width="10%">@lang('messages.total_amount_selling')</th>
                                    <th width="20%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($stockouts as $stockout)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $stockout->date }}</td>
                                    <td>{{ $stockout->stockin_no }}</td>
                                    <td>{{ $stockout->asker }}</td>
                                    <td>{{ $stockout->origin_sm_store_id }}</td>
                                    <td>{{ $stockout->material->name }}</td>
                                    <td>{{ $stockout->quantity }}</td>
                                    <td>{{ $stockout->unit }}</td>
                                    <td>{{ $stockout->destination }}</td>
                                    <td>{{ number_format($stockout->purchase_price,0,',',' ' ) }}</td>
                                    <td>{{ number_format($stockout->selling_price,0,',',' ' ) }}</td>
                                    <td>{{ number_format($stockout->total_selling_value,0,',',' ' ) }}</td>
                                    <td>{{ $stockout->description }}</td>
                                    <td>{{ $stockout->created_by }}</td>
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