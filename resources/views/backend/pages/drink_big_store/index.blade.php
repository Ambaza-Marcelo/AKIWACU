
@extends('backend.layouts.master')

@section('title')
@lang('Drink Big Store') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Drink Big Store')</h4>
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
                    <h4 class="header-title float-left">@lang('Drink Big Store')</h4>
                    <form action="{{ route('admin.virtual-drink-big-store.exportToExcel')}}" method="GET">
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
                                        @foreach($drink_big_stores as $store)
                                            <option value="{{$store->code}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>
                        </p>
                    </form> 
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('drink_big_store.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.drink-big-store.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('Designation')</th>
                                    <th width="10%">@lang('messages.code')</th>
                                    <th width="10%">@lang('eSignature')</th>
                                    <th width="10%">@lang('Emplacement')</th>
                                    <th width="10%">@lang('Store Manager')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($drink_big_stores as $drink_big_store)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $drink_big_store->name }}</td>
                                    <td><a href="{{ route('admin.drink-big-store.show',$drink_big_store->code) }}"> {{ $drink_big_store->code }} </a></td>
                                    <td>{{ $drink_big_store->store_signature }}</td>
                                    <td>{{ $drink_big_store->emplacement }}</td>
                                    <td>{{ $drink_big_store->manager }}</td>
                                    <td>
                                        <a href="{{ route('admin.drink-big-store.storeStatus',$drink_big_store->code) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        <a href="{{ route('admin.drink-big-store.exportToExcel',$drink_big_store->code) }}" class="btn btn-primary">Excel</a>
                                        @if (Auth::guard('admin')->user()->can('drink_big_store.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.drink-big-store.edit', $drink_big_store->code) }}">@lang('messages.edit')</a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_big_inventory.create'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.drink-big-store-inventory.create', $drink_big_store->code) }}">@lang('messages.inventory')</a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_big_store.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.drink-big-store.destroy', $drink_big_store->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $drink_big_store->id }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $drink_big_store->id }}" action="{{ route('admin.drink-big-store.destroy', $drink_big_store->id) }}" method="POST" style="display: none;">
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