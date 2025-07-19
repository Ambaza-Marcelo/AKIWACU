
@extends('backend.layouts.master')

@section('title')
@lang('messages.stockout') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.stockout')</h4>
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
                    <h4 class="header-title float-left">@lang('messages.stockout')</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('material_stockout.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockouts.choose') }}">@lang('messages.new')</a>
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
                                    <th width="10%">Stockout No</th>
                                    <th width="10%">Stockout Signature</th>
                                    <th width="5%">@lang('messages.status')</th>
                                    <th width="10%">@lang('messages.asker')</th>
                                    <th width="10%">Store From</th>
                                    <th width="10%">Destination</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="20%">@lang('messages.description')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($stockouts as $stockout)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($stockout->date)->format('d/m/Y') }}</td>
                                    <td><a href="{{ route('admin.material-stockouts.show',$stockout->stockout_no)}}">{{ $stockout->stockout_no }}</a></td>
                                    <td>{{ $stockout->stockout_signature }}</td>
                                    <td>@if($stockout->status == 1)<img src="{{ asset('img/warning3.gif')}}" width="35">@elseif($stockout->status == 1)<span class="badge badge-info">Encours</span> @elseif($stockout->status == 2)<span class="badge badge-info">Validé</span> @elseif($stockout->status == 3)<span class="badge badge-info">Confirmé</span> @elseif($stockout->status == 4)<span class="badge badge-info">Approuvé</span> @elseif($stockout->status == -1)<span class="badge badge-danger">Rejeté</span>@endif</td>
                                    <td>{{ $stockout->asker }}</td>
                                    <td>@if($stockout->store_type == 'sm') PETIT STOCK @elseif($stockout->store_type == 'md') STOCK INTERMEDIAIRE @elseif($stockout->store_type == 'bg') GRAND STOCK @endif</td>
                                    <td>{{ $stockout->destination }}</td>
                                    <td>{{ $stockout->created_by }}</td>
                                    <td>{{ $stockout->description }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('material_stockout.validate'))
                                        @if($stockout->status == 1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockouts.validate', $stockout->stockout_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $stockout->stockout_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $stockout->stockout_no }}" action="{{ route('admin.material-stockouts.validate', $stockout->stockout_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockout.confirm'))
                                        @if($stockout->status == 2)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockouts.confirm', $stockout->stockout_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('confirm-form-{{ $stockout->stockout_no }}').submit();">
                                                Confirmer
                                            </a>

                                            <form id="confirm-form-{{ $stockout->stockout_no }}" action="{{ route('admin.material-stockouts.confirm', $stockout->stockout_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockout.approuve'))
                                        @if($stockout->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockouts.approuve', $stockout->stockout_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('approuve-form-{{ $stockout->stockout_no }}').submit();this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';">
                                                Approuver
                                            </a>

                                            <form id="approuve-form-{{ $stockout->stockout_no }}" action="{{ route('admin.material-stockouts.approuve', $stockout->stockout_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockout.reject'))
                                            @if($stockout->status == 1 || $stockout->status == 2 || $stockout->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockouts.reject', $stockout->stockout_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $stockout->stockout_no }}').submit();">
                                                Rejeter
                                            </a>
                                            @endif
                                            <form id="reject-form-{{ $stockout->stockout_no }}" action="{{ route('admin.material-stockouts.reject', $stockout->stockout_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockout.reset'))
                                            @if($stockout->status == -1 || $stockout->status == 2 || $stockout->status == 3 || $stockout->status == 4)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockouts.reset', $stockout->stockout_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $stockout->stockout_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $stockout->stockout_no }}" action="{{ route('admin.material-stockouts.reset', $stockout->stockout_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockout.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.material-stockouts.edit', $stockout->stockout_no) }}">@lang('messages.edit')</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('material_stockout.delete'))
                                            @if($stockout->status == -1 || $stockout->status == 1)
                                            <a class="btn btn-danger text-white" href="{{ route('admin.material-stockouts.destroy', $stockout->stockout_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $stockout->stockout_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $stockout->stockout_no }}" action="{{ route('admin.material-stockouts.destroy', $stockout->stockout_no) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockout.create'))
                                        
                                        <a href="{{ route('admin.material-stockouts.bonSortie',$stockout->stockout_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        
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

    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    window.onunload = function () {
        null
    };

     </script>
@endsection