
@extends('backend.layouts.master')

@section('title')
@lang('Demande Achat des Articles') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Demande Achat des Articles')</h4>
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
                    <h4 class="header-title float-left">Demande Achat des Articles</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('material_requisition.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.material-purchases.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">Purchase No</th>
                                    <th width="10%">@lang('Purchase Signature')</th>
                                    <th width="10%">@lang('messages.status')</th>
                                    <th width="30%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($purchases as $purchase)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $purchase->date }}</td>
                                    <td><a href="{{ route('admin.material-purchases.show',$purchase->purchase_no) }}">{{ $purchase->purchase_no }}</a></td>
                                    <td>{{ $purchase->purchase_signature }}</td>
                                    @if($purchase->status == 2)
                                    <td><span  class="badge badge-success">Validé</span></td>
                                    @elseif($purchase->status == -1)
                                    <td><span class="badge badge-danger">Rejeté</span></td>
                                    @elseif($purchase->status == 3)
                                    <td><span class="badge badge-success">confirmé</span></td>
                                    @elseif($purchase->status == 4)
                                    <td><span class="badge badge-success">Approuvé</span></td>
                                    @elseif($purchase->status == 5)
                                    <td><span class="badge badge-success">Commandé</span></td>
                                    @elseif($purchase->status == 6)
                                    <td><span class="badge badge-success">Receptionné</span></td>
                                    @else
                                    <td><span class="badge badge-primary">Encours...</span></td>
                                    @endif
                                    <td>{{ $purchase->description }}</td>
                                    <td>{{ $purchase->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('material_purchase.create'))
                                        @if($purchase->status == 2 && $purchase->status == 3 || $purchase->status == 4 || $purchase->status == 5)
                                        <a href="{{ route('admin.material-purchases.materialPurchase',$purchase->purchase_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_purchase.validate'))
                                        @if($purchase->status == 1 || $purchase->status == -1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-purchases.validate', $purchase->purchase_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $purchase->purchase_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $purchase->purchase_no }}" action="{{ route('admin.material-purchases.validate', $purchase->purchase_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_purchase.confirm'))
                                        @if($purchase->status == 2)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-purchases.confirm', $purchase->purchase_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('confirm-form-{{ $purchase->purchase_no }}').submit();">
                                                Confirmer
                                            </a>

                                            <form id="confirm-form-{{ $purchase->purchase_no }}" action="{{ route('admin.material-purchases.confirm', $purchase->purchase_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_purchase.approuve'))
                                        @if($purchase->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-purchases.approuve', $purchase->purchase_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('approuve-form-{{ $purchase->purchase_no }}').submit();">
                                                Approuver
                                            </a>

                                            <form id="approuve-form-{{ $purchase->purchase_no }}" action="{{ route('admin.material-purchases.approuve', $purchase->purchase_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_purchase.reject'))
                                            @if($purchase->status == 1 || $purchase->status == 2 || $purchase->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-purchases.reject', $purchase->purchase_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $purchase->purchase_no }}').submit();">
                                                Rejeter
                                            </a>
                                            @endif
                                            <form id="reject-form-{{ $purchase->purchase_no }}" action="{{ route('admin.material-purchases.reject', $purchase->purchase_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_purchase.reset'))
                                            @if($purchase->status == -1 || $purchase->status == 2 || $purchase->status == 3 || $purchase->status == 4)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-purchases.reset', $purchase->purchase_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $purchase->purchase_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $purchase->purchase_no }}" action="{{ route('admin.material-purchases.reset', $purchase->purchase_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_purchase.create'))
                                        @if($purchase->status == 4)
                                        <a href="{{ route('admin.material-supplier-orders.create',$purchase->purchase_no)}}" class="btn btn-primary">Commander</a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_purchase.create'))
                                        @if($purchase->status == 4)
                                        <a href="{{ route('admin.material-reception-without-order.create',$purchase->purchase_no)}}" class="btn btn-success">Receptionner Sans Bon de Commande</a>
                                        @endif
                                        @endif
                                        @if($purchase->status == 1)
                                        @if (Auth::guard('admin')->user()->can('material_purchase.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.material-purchases.edit', $purchase->purchase_no) }}">@lang('messages.edit')</a>
                                        @endif
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('material_purchase.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.material-purchases.destroy', $purchase->purchase_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $purchase->purchase_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $purchase->purchase_no }}" action="{{ route('admin.material-purchases.destroy', $purchase->purchase_no) }}" method="POST" style="display: none;">
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