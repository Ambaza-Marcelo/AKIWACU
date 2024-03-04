
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
                    <h4 class="header-title float-left">@lang('messages.inventory')</h4>
                    <p class="float-right mb-2">
                            <a href="" title="Cliquer pour telecharger la reference de l'inventaire" class="btn btn-info"><i class="fa fa-file-pdf-o"></i>&nbsp;Ref. Inventaire</a>
                        </p>
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
                                    <th width="10%">@lang('Code Store')</th>
                                    <th width="20%">@lang('messages.title')</th>
                                    <th width="10%">@lang('messages.status')</th>
                                    <th width="65%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($inventories as $inventory)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ Carbon\Carbon::parse($inventory->date)->format('d/m/Y') }}</td>
                                    <td><a href="{{ route('admin.sotb-material-md-store-inventory.show', $inventory->inventory_no) }}">{{ $inventory->inventory_no }}</a></td>
                                    <td>{{ $inventory->inventory_signature }}</td>
                                    <td>{{ $inventory->code_store }}</td>
                                    <td>{{ $inventory->title }}</td>
                                    @if($inventory->status == 0)
                                    <td class="badge badge-warning">Encours</td>
                                    @elseif($inventory->status == 1)
                                    <td class="badge badge-danger">Rejeté</td>
                                    @else
                                    <td class="badge badge-success">Validé</td>
                                    @endif
                                    <td>{{ $inventory->description }}</td>
                                    <td>{{ $inventory->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('sotb_material_md_inventory.edit'))
                                        @if($inventory->status != 2)
                                            <a class="btn btn-success text-white" href="{{ route('admin.sotb-material-md-store-inventory.edit', $inventory->inventory_no) }}">@lang('messages.edit')</a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_md_inventory.delete'))
                                        @if($inventory->status != 2)
                                            <a class="btn btn-danger text-white" href="{{ route('admin.sotb-material-md-store-inventory.destroy', $inventory->inventory_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $inventory->inventory_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $inventory->inventory_no }}" action="{{ route('admin.sotb-material-md-store-inventory.destroy', $inventory->inventory_no) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_md_inventory.validate'))
                                        @if($inventory->status != 2)
                                          <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-md-store-inventory.validate', $inventory->inventory_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $inventory->inventory_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $inventory->inventory_no }}" action="{{ route('admin.sotb-material-md-store-inventory.validate', $inventory->inventory_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_md_inventory.reject'))
                                        @if($inventory->status != 2)
                                          <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-md-store-inventory.reject', $inventory->inventory_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $inventory->inventory_no }}').submit();">
                                                Rejeter
                                            </a>

                                            <form id="reject-form-{{ $inventory->inventory_no }}" action="{{ route('admin.sotb-material-md-store-inventory.reject', $inventory->inventory_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_md_inventory.reset'))
                                        @if($inventory->status == 2 || $inventory->status == 1)
                                          <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-md-store-inventory.reset', $inventory->inventory_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $inventory->inventory_no }}').submit();">
                                                Annuler
                                            </a>

                                            <form id="reset-form-{{ $inventory->inventory_no }}" action="{{ route('admin.sotb-material-md-store-inventory.reset', $inventory->inventory_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_md_inventory.view'))
                                        <a href="{{ route('admin.sotb-material-md-store-inventory.generatePdf', $inventory->inventory_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
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

     <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
     
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