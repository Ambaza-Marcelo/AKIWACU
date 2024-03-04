
@extends('backend.layouts.master')

@section('title')
@lang('return') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.return')</h4>
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
                    <h4 class="header-title float-left">return List</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">Return No</th>
                                    <th width="10%">Return Signature</th>
                                    <th width="10%">Transfert No</th>
                                    <th width="10%">Stock Origine</th>
                                    <th width="10%">Stock Destination</th>
                                    <th width="5%">@lang('messages.status')</th>
                                    <th width="20%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($returns as $return)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $return->date }}</td>
                                    <td><a href="{{ route('admin.material-return.show',$return->return_no)}}">{{ $return->return_no }}</a></td>
                                    <td>{{ $return->transfer_signature }}</td>
                                    <td><a href="@if($return->transfer_no){{ route('admin.material-transfers.show',$return->transfer_no)}}@endif">{{ $return->transfer_no }}</a></td>
                                    <td>{{ $return->origin_store_id }}</td>
                                    <td>{{ $return->destination_store_id }}</td>
                                    <td>@if($return->status == 1)<img src="{{ asset('img/warning3.gif')}}" width="35">@elseif($return->status == 1)<span class="badge badge-info">Encours</span> @elseif($return->status == 2)<span class="badge badge-info">Validé</span> @elseif($return->status == 3)<span class="badge badge-info">Confirmé</span> @elseif($return->status == 4)<span class="badge badge-info">Approuvé</span>@endif</td>
                                    <td>{{ $return->description }}</td>
                                    <td>{{ $return->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('material_return.validate'))
                                        @if($return->status == 1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-return.validate', $return->return_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $return->return_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $return->return_no }}" action="{{ route('admin.material-return.validate', $return->return_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_return.confirm'))
                                        @if($return->status == 2)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-return.confirm', $return->return_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('confirm-form-{{ $return->return_no }}').submit();">
                                                Confirmer
                                            </a>

                                            <form id="confirm-form-{{ $return->return_no }}" action="{{ route('admin.material-return.confirm', $return->return_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_return.approuve'))
                                        @if($return->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-return.approuve', $return->return_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('approuve-form-{{ $return->return_no }}').submit();">
                                                Approuver
                                            </a>

                                            <form id="approuve-form-{{ $return->return_no }}" action="{{ route('admin.material-return.approuve', $return->return_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_return.reject'))
                                            @if($return->status == 1 || $return->status == 2 || $return->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-return.reject', $return->return_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $return->return_no }}').submit();">
                                                Rejeter
                                            </a>
                                            @endif
                                            <form id="reject-form-{{ $return->return_no }}" action="{{ route('admin.material-return.reject', $return->return_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_return.reset'))
                                            @if($return->status == -1 || $return->status == 2 || $return->status == 3 || $return->status == 4)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-return.reset', $return->return_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $return->return_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $return->return_no }}" action="{{ route('admin.material-return.reset', $return->return_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_return.create'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.material-return.create', $return->return_no) }}">@lang('Retourner')</a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_return.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.material-return.edit', $return->return_no) }}">@lang('messages.edit')</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('material_return.delete'))
                                            @if($return->status == -1 || $return->status == 1)
                                            <a class="btn btn-danger text-white" href="{{ route('admin.material-return.destroy', $return->return_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $return->return_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $return->return_no }}" action="{{ route('admin.material-return.destroy', $return->return_no) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_return.create'))
                                        
                                        <a href="{{ route('admin.material-return.bonRetour',$return->return_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        
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