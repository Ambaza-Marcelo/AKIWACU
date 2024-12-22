
@extends('backend.layouts.master')

@section('title')
@lang('liste des boissons') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('liste des boissons de PDG MUSUMBA HOLDING')</h4>
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
                    <h4 class="header-title float-left">@lang('liste des boissons de PDG MUSUMBA HOLDING')</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('private_store_item.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.private-store-items.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('private_store_item.view'))
                            <a class="btn btn-success text-white" href="{{ route('admin.private-store-items.export-to-pdf') }}">@lang('Exporter en PDF')</a>
                        @endif
                        @if (Auth::guard('admin')->user()->can('private_store_item.view'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.private-store-items.export-to-excel') }}">@lang('Exporter en Excel')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('messages.code')</th>
                                    <th width="10%">@lang('messages.quantity')</th>
                                    <th width="10%">@lang('messages.unit')</th>
                                    <th width="10%">@lang('Min Store')</th>
                                    <th width="10%">@lang('TVA')</th>
                                    <th width="10%">@lang('messages.purchase_price')</th>
                                    <th width="10%">@lang('messages.selling_price')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($items as $item)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->code }}</td>
                                    @if($item->quantity <= $item->threshold_quantity)
                                    <td>{{ $item->quantity }}<img src="{{ asset('img/warning.gif')}}" width="30"></td>
                                    @else
                                    <td>{{ $item->quantity }}</td>
                                    @endif
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ $item->threshold_quantity }}</td>
                                    <td>{{ $item->vat }}%</td>
                                    <td>{{ number_format($item->purchase_price,0,',',' ') }}</td>
                                    <td>{{ number_format($item->selling_price,0,',',' ') }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('private_store_item.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.private-store-items.edit', $item->id) }}">@lang('messages.edit')</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('private_store_item.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.private-store-items.destroy', $item->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $item->id }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $item->id }}" action="{{ route('admin.private-store-items.destroy', $item->id) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                        @endif
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