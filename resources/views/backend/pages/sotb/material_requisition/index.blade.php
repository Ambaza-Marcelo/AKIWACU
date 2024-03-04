
@extends('backend.layouts.master')

@section('title')
@lang('Demande de Requisition des Articles') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Demande de Requisition des Articles')</h4>
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
                    <h4 class="header-title float-left">Demande de Requisition des Articles</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-requisitions.choose') }}">@lang('messages.new')</a>
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
                                    <th width="10%">Requisition No</th>
                                    <th width="10%">@lang('Requisition Signature')</th>
                                    <th width="10%">@lang('messages.status')</th>
                                    <th width="30%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($requisitions as $requisition)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $requisition->date }}</td>
                                    <td><a href="{{ route('admin.sotb-material-requisitions.show',$requisition->requisition_no) }}">{{ $requisition->requisition_no }}</a></td>
                                    <td>{{ $requisition->requisition_signature }}</td>
                                    @if($requisition->status == 2)
                                    <td><span  class="badge badge-success">Validé</span></td>
                                    @elseif($requisition->status == -1)
                                    <td><span class="badge badge-danger">Rejeté</span></td>
                                    @elseif($requisition->status == 3)
                                    <td><span class="badge badge-success">confirmé</span></td>
                                    @elseif($requisition->status == 4)
                                    <td><span class="badge badge-success">Approuvé</span></td>
                                    @elseif($requisition->status == 5)
                                    <td><span class="badge badge-success">Transferé</span></td>
                                    @else
                                    <td><span class="badge badge-primary">Encours...</span></td>
                                    @endif
                                    <td>{{ $requisition->description }}</td>
                                    <td>{{ $requisition->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.create'))
                                        @if($requisition->status == 2 || $requisition->status == 3 || $requisition->status == 4 || $requisition->status == 5)
                                        <a href="{{ route('admin.sotb-material-requisitions.generatepdf',$requisition->requisition_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.validate'))
                                        @if($requisition->status == 0 || $requisition->status == -1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-requisitions.validate', $requisition->requisition_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $requisition->requisition_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $requisition->requisition_no }}" action="{{ route('admin.sotb-material-requisitions.validate', $requisition->requisition_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.confirm'))
                                        @if($requisition->status == 2)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-requisitions.confirm', $requisition->requisition_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('confirm-form-{{ $requisition->requisition_no }}').submit();">
                                                Confirmer
                                            </a>

                                            <form id="confirm-form-{{ $requisition->requisition_no }}" action="{{ route('admin.sotb-material-requisitions.confirm', $requisition->requisition_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.approuve'))
                                        @if($requisition->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-requisitions.approuve', $requisition->requisition_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('approuve-form-{{ $requisition->requisition_no }}').submit();">
                                                Approuver
                                            </a>

                                            <form id="approuve-form-{{ $requisition->requisition_no }}" action="{{ route('admin.sotb-material-requisitions.approuve', $requisition->requisition_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.reject'))
                                            @if($requisition->status == 1 || $requisition->status == 2 || $requisition->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-requisitions.reject', $requisition->requisition_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $requisition->requisition_no }}').submit();">
                                                Rejeter
                                            </a>
                                            @endif
                                            <form id="reject-form-{{ $requisition->requisition_no }}" action="{{ route('admin.sotb-material-requisitions.reject', $requisition->requisition_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.reset'))
                                            @if($requisition->status == -1 || $requisition->status == 2 || $requisition->status == 3 || $requisition->status == 4)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.sotb-material-requisitions.reset', $requisition->requisition_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $requisition->requisition_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $requisition->requisition_no }}" action="{{ route('admin.sotb-material-requisitions.reset', $requisition->requisition_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.create'))
                                        @if($requisition->status == 4 && $requisition->type_store == 'md')
                                        <a href="{{ route('admin.sotb-material-transferts.create',$requisition->requisition_no)}}" class="btn btn-primary">Transferer vers petit stock</a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.create'))
                                        @if($requisition->status == 4 && $requisition->type_store == 'bg')
                                        <a href="{{ route('admin.sotb-material-transferts.createFromBig',$requisition->requisition_no)}}" class="btn btn-primary">Transferer vers Stock Intermediaire</a>
                                        @endif
                                        @endif
                                        @if($requisition->status == 1)
                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.sotb-material-requisitions.edit', $requisition->requisition_no) }}">@lang('messages.edit')</a>
                                        @endif
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('sotb_material_requisition.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.sotb-material-requisitions.destroy', $requisition->requisition_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $requisition->requisition_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $requisition->requisition_no }}" action="{{ route('admin.sotb-material-requisitions.destroy', $requisition->requisition_no) }}" method="POST" style="display: none;">
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