
@extends('backend.layouts.master')

@section('title')
@lang('transfer') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.transfer')</h4>
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
                    <h4 class="header-title float-left">transfer List</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">Transfer No</th>
                                    <th width="10%">Transfer Signature</th>
                                    <th width="10%">Requisition No</th>
                                    <th width="10%">Stock Origine</th>
                                    <th width="10%">Stock Destination</th>
                                    <th width="5%">@lang('messages.status')</th>
                                    <th width="20%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($transfers as $transfer)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $transfer->date }}</td>
                                    <td><a href="{{ route('admin.material-transfers.show',$transfer->transfer_no)}}">{{ $transfer->transfer_no }}</a></td>
                                    <td>{{ $transfer->transfer_signature }}</td>
                                    <td><a href="@if($transfer->requisition_no){{ route('admin.material-requisitions.show',$transfer->requisition_no)}}@endif">{{ $transfer->requisition_no }}</a></td>
                                    <td>@if($transfer->origin_store_id){{ $transfer->originStore->code }} @else GRAND STOCK @endif</td>
                                    <td>@if($transfer->destination_store_id){{ $transfer->destinationStore->code }} @else STOCK INTERMEDIAIRE @endif</td>
                                    <td>@if($transfer->status == 1)<img src="{{ asset('img/warning3.gif')}}" width="35">@elseif($transfer->status == 1)<span class="badge badge-info">Encours</span> @elseif($transfer->status == 2)<span class="badge badge-info">Validé</span> @elseif($transfer->status == 3)<span class="badge badge-info">Confirmé</span> @elseif($transfer->status == 4)<span class="badge badge-info">Approuvé</span>@endif</td>
                                    <td>{{ $transfer->description }}</td>
                                    <td>{{ $transfer->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('material_transfer.validate'))
                                        @if($transfer->status == 1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-transfers.validate', $transfer->transfer_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $transfer->transfer_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $transfer->transfer_no }}" action="{{ route('admin.material-transfers.validate', $transfer->transfer_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_transfer.confirm'))
                                        @if($transfer->status == 2)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-transfers.confirm', $transfer->transfer_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('confirm-form-{{ $transfer->transfer_no }}').submit();">
                                                Confirmer
                                            </a>

                                            <form id="confirm-form-{{ $transfer->transfer_no }}" action="{{ route('admin.material-transfers.confirm', $transfer->transfer_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_transfer.approuve'))
                                        @if($transfer->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-transfers.approuve', $transfer->transfer_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('approuve-form-{{ $transfer->transfer_no }}').submit();this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';">
                                                Approuver
                                            </a>

                                            <form id="approuve-form-{{ $transfer->transfer_no }}" action="{{ route('admin.material-transfers.approuve', $transfer->transfer_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_transfer.reject'))
                                            @if($transfer->status == 1 || $transfer->status == 2 || $transfer->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-transfers.reject', $transfer->transfer_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $transfer->transfer_no }}').submit();">
                                                Rejeter
                                            </a>
                                            @endif
                                            <form id="reject-form-{{ $transfer->transfer_no }}" action="{{ route('admin.material-transfers.reject', $transfer->transfer_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_transfer.reset'))
                                            @if($transfer->status == -1 || $transfer->status == 2 || $transfer->status == 3 || $transfer->status == 4)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-transfers.reset', $transfer->transfer_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $transfer->transfer_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $transfer->transfer_no }}" action="{{ route('admin.material-transfers.reset', $transfer->transfer_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_return.create'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.material-return.create', $transfer->transfer_no) }}">@lang('Retourner')</a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_transfer.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.material-transfers.edit', $transfer->transfer_no) }}">@lang('messages.edit')</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('material_transfer.delete'))
                                            @if($transfer->status == -1 || $transfer->status == 1)
                                            <a class="btn btn-danger text-white" href="{{ route('admin.material-transfers.destroy', $transfer->transfer_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $transfer->transfer_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $transfer->transfer_no }}" action="{{ route('admin.material-transfers.destroy', $transfer->transfer_no) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_transfer.create'))
                                        
                                        <a href="{{ route('admin.material-transfers.bonTransfert',$transfer->transfer_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        
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