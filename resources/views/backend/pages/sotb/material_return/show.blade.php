
@extends('backend.layouts.master')

@section('title')
@lang('details des retours') - @lang('messages.admin_panel')
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
                    <h4 class="header-title float-left">Details sur bon de retour No : {{ $code }}</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">Return No</th>
                                    <th width="10%">Transfert No</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('messages.quantity_transfered')</th>
                                    <th width="10%">@lang('messages.unit_price')</th>
                                    <th width="10%">@lang('messages.quantity_returned')</th>
                                    <th width="10%">@lang('messages.unit')</th>
                                    <th width="10%">@lang('messages.total_value')</th>
                                    <th width="20%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($returns as $return)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $return->date }}</td>
                                    <td>{{ $return->return_no }}</td>
                                    <td>{{ $return->transfer_no }}</td>
                                    <td>{{ $return->material->name }}</td>
                                    <td>{{ $return->quantity_transfered }}</td>
                                    <td>{{ number_format($return->price,0,',',' ' ) }}</td>
                                    <td>{{ $return->quantity_returned }}</td>
                                    <td>{{ $return->unit }}</td>
                                    <td>{{ number_format($return->total_value_returned,0,',',' ' ) }}</td>
                                    <td>{{ $return->description }}</td>
                                    <td>{{ $return->created_by }}</td>
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