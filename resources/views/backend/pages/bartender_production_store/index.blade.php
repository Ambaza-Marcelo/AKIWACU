
@extends('backend.layouts.master')

@section('title')
@lang('Bartender store') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Bartender store')</h4>
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
                    <h4 class="header-title float-left">@lang('Bartender store')</h4>
                    <form action="{{ route('admin.bartender-transformation.rapport')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-info">Exporter En PDF</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </p>
                    </form><br>
                    <p class="float-right mb-2">
                        <a class="btn btn-info text-white" href="" title="Exporter en pdf l'état du stock"><i class="fa fa-file-pdf-o"></i>&nbsp;PDF</a>
                    </p>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('bartender_item.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.bartender-transformation.create') }}">@lang('Saisie Production')</a>&nbsp;&nbsp;
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
                                    <th width="10%">@lang('messages.threshold_quantity')</th>
                                    <th width="10%">@lang('messages.unit_price')</th>
                                    <th width="10%">@lang('messages.total_value')</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($stocks as $stock)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $stock->bartenderItem->name }}</td>
                                    <td>{{ $stock->bartenderItem->code }}</td>
                                    @if($stock->quantity <= $stock->threshold_quantity)
                                    <td>{{ $stock->quantity }}<img src="{{ asset('img/warning.gif')}}" width="30"></td>
                                    @else
                                    <td>{{ $stock->quantity }}</td>
                                    @endif
                                    <td>{{ $stock->threshold_quantity }}</td>
                                    <td>{{ number_format($stock->selling_price,0,',',' ' ) }}</td>
                                    <td>{{ number_format(($stock->selling_price * $stock->quantity),0,',',' ' )}} </td>
                                    <td>
                                    @if (Auth::guard('admin')->user()->can('bartender_production_store.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.bartender-production-store.destroy', $stock->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $stock->id }}').submit();">
                                                Delete
                                            </a>

                                            <form id="delete-form-{{ $stock->id }}" action="{{ route('admin.bartender-production-store.destroy', $stock->id) }}" method="POST" style="display: none;">
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