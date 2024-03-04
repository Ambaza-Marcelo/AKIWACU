
@extends('backend.layouts.master')

@section('title')
@lang('details des receptions') - @lang('messages.admin_panel')
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
                    <h4 class="header-title float-left">Details sur bon de reception No : {{ $code }}</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">Reception No</th>
                                    <th width="10%">Order No</th>
                                    <th width="10%">Supplier</th>
                                    <th width="10%">Vat S. Taxepayer?</th>
                                    <th width="10%">Invoice No</th>
                                    <th width="10%">Invoice Currency</th>
                                    <th width="10%">Handingover</th>
                                    <th width="10%">Receptionist</th>
                                    <th width="10%">Stock Destination</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('messages.quantity_ordered')</th>
                                    <th width="10%">@lang('messages.unit_price')</th>
                                    <th width="10%">@lang('messages.quantity_received')</th>
                                    <th width="10%">@lang('messages.selling_price')</th>
                                    <th width="10%">@lang('messages.unit')</th>
                                    <th width="10%">@lang('messages.total_value')</th>
                                    <th width="20%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($receptions as $reception)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $reception->date }}</td>
                                    <td>{{ $reception->reception_no }}</td>
                                    <td>{{ $reception->order_no }}</td>
                                    <td>{{ $reception->supplier_id }}</td>
                                    <td>{{ $reception->vat_supplier_payer }}</td>
                                    <td>{{ $reception->invoice_no }}</td>
                                    <td>{{ $reception->invoice_currency }}</td>
                                    <td>{{ $reception->handingover }}</td>
                                    <td>{{ $reception->receptionist }}</td>
                                    <td>{{ $reception->destination_store_id }}</td>
                                    <td>{{ $reception->drink->name }}</td>
                                    <td>{{ $reception->quantity_ordered }}</td>
                                    <td>{{ number_format($reception->purchase_price,0,',',' ' ) }}</td>
                                    <td>{{ $reception->quantity_received }}</td>
                                    <td>{{ number_format($reception->selling_price,0,',',' ' ) }}</td>
                                    <td>{{ $reception->unit }}</td>
                                    <td>{{ number_format($reception->total_amount_received,0,',',' ' ) }}</td>
                                    <td>{{ $reception->description }}</td>
                                    <td>{{ $reception->created_by }}</td>
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