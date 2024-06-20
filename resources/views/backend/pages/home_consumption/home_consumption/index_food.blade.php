
@extends('backend.layouts.master')

@section('title')
@lang('Liste des consommations maisons') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Liste des consommations maisons '){{ $staff_member->name }}</h4>
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
                    <h4 class="header-title float-left">Liste des consommations maisons {{ $staff_member->name }}</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('food_order_client.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.home-consumption-food.create',$staff_member_id) }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Consommation No</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="30%">@lang('Responsable')</th>
                                    <th width="10%">@lang('messages.status')</th>
                                    <th width="30%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($consumptions as $consumption)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td><a href="{{ route('admin.home-consumption.show',$consumption->consumption_no) }}">{{ $consumption->consumption_no }}</a></td>
                                    <td>{{ Carbon\Carbon::parse($consumption->date)->format('d/m/Y') }}</td>
                                    <td>{{ $consumption->staffmember->name }}</td>
                                    <td>@if($consumption->status == -1)<span class="badge badge-danger">Rejetée</span>@endif</td>
                                    <td>@if($consumption->status == -1) {{ $consumption->rej_motif }} @else {{ $consumption->description }} @endif</td>
                                    <td>{{ $consumption->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('food_order_client.create'))
                                        <a href="{{ route('admin.home-consumption.generatepdf',$consumption->consumption_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('food_order_client.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.home-consumption.destroy', $consumption->consumption_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $consumption->consumption_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $consumption->consumption_no }}" action="{{ route('admin.home-consumption.destroy', $consumption->consumption_no) }}" method="POST" style="display: none;">
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