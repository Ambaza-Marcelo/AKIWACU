
@extends('backend.layouts.master')

@section('title')
@lang('Commande des Boissons (BARRIST)') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Commande des Boissons (BARRIST)')</h4>
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
                    <h4 class="header-title float-left">Commande des Boissons (BARRIST)</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Order No</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="30%">@lang('Table No')</th>
                                    <th width="30%">@lang('Serveur')</th>
                                    <th width="10%">@lang('messages.status')</th>
                                    <th width="30%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($orders as $order)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td><a href="{{ route('admin.barrist-orders.show',$order->order_no) }}">{{ $order->order_no }}</a></td>
                                    <td>{{ Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</td>
                                    <td>@if($order->table_id){{ $order->table->name }} @endif</td>
                                    <td>{{ $order->employe->name }}</td>
                                    @if($order->status == 1)
                                    <td><span  class="badge badge-success">Validée</span></td>
                                    @elseif($order->status == -1)
                                    <td><span class="badge badge-danger">Rejetée</span></td>
                                    @elseif($order->status == 2)
                                    <td><span class="badge badge-warning">Facturé(Encours)</span></td>
                                    @elseif($order->status == 3)
                                    <td><span class="badge badge-success">Facturé</span></td>
                                    @else
                                    <td><span class="badge badge-primary">Encours...</span></td>
                                    @endif
                                    <td>{{ $order->description }}</td>
                                    <td>{{ $order->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('drink_order_client.create'))
                                        @if($order->status == 1)
                                        <a href="{{ route('admin.barrist-orders.generatepdf',$order->order_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_order_client.validate'))
                                        @if($order->status == 0 || $order->status == -1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.barrist-orders.validate', $order->order_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $order->order_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $order->order_no }}" action="{{ route('admin.barrist-orders.validate', $order->order_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_order_client.reject'))
                                            <a class="btn btn-primary text-white" href="{{ route('admin.barrist-orders.reject', $order->order_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $order->order_no }}').submit();">
                                                Rejeter
                                            </a>
                                            <form id="reject-form-{{ $order->order_no }}" action="{{ route('admin.barrist-orders.reject', $order->order_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_order_client.reset'))
                                            @if($order->status == -1 || $order->status == 1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.barrist-orders.reset', $order->order_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $order->order_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $order->order_no }}" action="{{ route('admin.barrist-orders.reset', $order->order_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if($order->status == 0)
                                        @if (Auth::guard('admin')->user()->can('drink_order_client.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.barrist-orders.edit', $order->id) }}">@lang('messages.edit')</a>
                                        @endif
                                        @endif
                                        @if($order->status == 1)
                                        @if (Auth::guard('admin')->user()->can('invoice_drink.create'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.barrist-invoices.create', $order->order_no) }}">@lang('Facturation')</a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_order_client.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.barrist-orders.destroy', $order->order_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $order->order_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $order->order_no }}" action="{{ route('admin.barrist-orders.destroy', $order->order_no) }}" method="POST" style="display: none;">
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