
@extends('backend.layouts.master')

@section('title')
@lang('Liste des fournisseurs') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Liste des fournisseurs')</h4>
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
                    <h4 class="header-title float-left">@lang('Liste des fournisseurs')</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('supplier.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.suppliers.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Type Contribuable</th>
                                    <th width="20%">Assujetti à la TVA</th>
                                    <th width="20%">Nom Fournisseur</th>
                                    <th width="20%">NIF Fournisseur</th>
                                    <th width="20%">Adresse Fournisseur</th>
                                    <th width="20%">Mail</th>
                                    <th width="20%">Phone No</th>
                                    <th width="30%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($suppliers as $supplier)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>@if($supplier->tp_type == 1) Personne Physique @else Société @endif</td>
                                    <td>@if($supplier->vat_supplier_payer == 1) OUI @else NON @endif</td>
                                    <td>{{ $supplier->supplier_name }}</td>
                                    <td>{{ $supplier->supplier_TIN }}</td>
                                    <td>{{ $supplier->supplier_address }}</td>
                                    <td>{{ $supplier->mail }}</td>
                                    <td>{{ $supplier->telephone }}</td>

                                    <td>
                                        @if (Auth::guard('admin')->user()->can('supplier.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.suppliers.edit', $supplier->id) }}">@lang('messages.edit')</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('supplier.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.suppliers.destroy', $supplier->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $supplier->id }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $supplier->id }}" action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" style="display: none;">
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