
@extends('backend.layouts.master')

@section('title')
@lang('messages.inventory') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.inventory')</h4>
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
                    <h4 class="header-title float-left">Details de l'inventaire No: {{$code}}</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">@lang('messages.inventory_no')</th>
                                    <th width="10%">@lang('Inventory Signature')</th>
                                    <th width="10%">@lang('messages.title')</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('Item Code')</th>
                                    <th width="10%">@lang('messages.quantity')</th>
                                    <th width="10%">@lang('messages.unit')</th>
                                    <th width="10%">@lang('messages.purchase_price')</th>
                                    <th width="10%">@lang('messages.new_quantity')</th>
                                    <th width="10%">@lang('messages.new_purchase_price')</th>
                                    <th width="10%">@lang('messages.new_total_purchase_price')</th>
                                    <th width="10%">@lang('messages.new_total_selling_price')</th>
                                    <th width="10%">Relicat</th>
                                    <th width="10%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($inventories as $inventory)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $inventory->date }}</td>
                                    <td>{{ $inventory->inventory_no }}</td>
                                    <td>{{ $inventory->inventory_signature }}</td>
                                    <td>{{ $inventory->title }}</td>
                                    <td>{{ $inventory->food->name }}</td>
                                    <td>{{ $inventory->food->code }}</td>
                                    <td>{{ $inventory->quantity }}</td>
                                    <td>{{ $inventory->unit }}</td>
                                    <td>{{ number_format($inventory->purchase_price,0,',','.') }}</td>
                                    <td>{{ $inventory->new_quantity }}</td>
                                    <td>{{ $inventory->new_purchase_price }}</td>
                                    <td>{{ number_format($inventory->new_total_purchase_value,0,',','.') }}</td>
                                    <td>{{ number_format($inventory->new_total_selling_value,0,',','.') }}</td>
                                    <td>{{ $inventory->relicat }} </td>
                                    <td>{{ $inventory->description }}</td>
                                    <td>{{ $inventory->created_by }}</td>
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