
@extends('backend.layouts.master')

@section('title')
@lang('messages.stockin') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.stockin')</h4>
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
                    <h4 class="header-title float-left">@lang('messages.stockin')</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('material_stockin.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockins.choose') }}">@lang('messages.new')</a>
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
                                    <th width="10%">Stockin No</th>
                                    <th width="10%">Stockin Signature</th>
                                    <th width="5%">@lang('messages.status')</th>
                                    <th width="10%">@lang('messages.receptionist')</th>
                                    <th width="10%">Remettant</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="20%">@lang('messages.description')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($stockins as $stockin)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($stockin->date)->format('d/m/Y') }}</td>
                                    <td><a href="{{ route('admin.material-stockins.show',$stockin->stockin_no)}}">{{ $stockin->stockin_no }}</a></td>
                                    <td>{{ $stockin->stockin_signature }}</td>
                                    <td>@if($stockin->status == 1)<img src="{{ asset('img/warning3.gif')}}" width="35">@elseif($stockin->status == 1)<span class="badge badge-info">Encours</span> @elseif($stockin->status == 2)<span class="badge badge-info">Validé</span> @elseif($stockin->status == 3)<span class="badge badge-info">Confirmé</span> @elseif($stockin->status == 4)<span class="badge badge-info">Approuvé</span> @else <span class="badge badge-danger">Rejeté</span> @endif</td>
                                    <td>{{ $stockin->receptionist }}</td>
                                    <td>{{ $stockin->handingover }}</td>
                                    <td>{{ $stockin->created_by }}</td>
                                    <td>{{ $stockin->description }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('material_stockin.validate'))
                                        @if($stockin->status == 1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockins.validate', $stockin->stockin_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $stockin->stockin_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $stockin->stockin_no }}" action="{{ route('admin.material-stockins.validate', $stockin->stockin_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockin.confirm'))
                                        @if($stockin->status == 2)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockins.confirm', $stockin->stockin_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('confirm-form-{{ $stockin->stockin_no }}').submit();">
                                                Confirmer
                                            </a>

                                            <form id="confirm-form-{{ $stockin->stockin_no }}" action="{{ route('admin.material-stockins.confirm', $stockin->stockin_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockin.approuve'))
                                        @if($stockin->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockins.approuve', $stockin->stockin_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('approuve-form-{{ $stockin->stockin_no }}').submit();this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';">
                                                Approuver
                                            </a>

                                            <form id="approuve-form-{{ $stockin->stockin_no }}" action="{{ route('admin.material-stockins.approuve', $stockin->stockin_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockin.reject'))
                                            @if($stockin->status == 1 || $stockin->status == 2 || $stockin->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockins.reject', $stockin->stockin_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $stockin->stockin_no }}').submit();">
                                                Rejeter
                                            </a>
                                            @endif
                                            <form id="reject-form-{{ $stockin->stockin_no }}" action="{{ route('admin.material-stockins.reject', $stockin->stockin_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockin.reset'))
                                            @if($stockin->status == -1 || $stockin->status == 2 || $stockin->status == 3 || $stockin->status == 4)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.material-stockins.reset', $stockin->stockin_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $stockin->stockin_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $stockin->stockin_no }}" action="{{ route('admin.material-stockins.reset', $stockin->stockin_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockin.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.material-stockins.edit', $stockin->stockin_no) }}">@lang('messages.edit')</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('material_stockin.delete'))
                                            @if($stockin->status == -1 || $stockin->status == 1)
                                            <a class="btn btn-danger text-white" href="{{ route('admin.material-stockins.destroy', $stockin->stockin_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $stockin->stockin_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $stockin->stockin_no }}" action="{{ route('admin.material-stockins.destroy', $stockin->stockin_no) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                            @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('material_stockin.create'))
                                        
                                        <a href="{{ route('admin.material-stockins.bonEntree',$stockin->stockin_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        
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