
@extends('backend.layouts.master')

@section('title')
@lang('Liste des salles de conferences et reunions') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Liste des salles de conferences et reunions')</h4>
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
                    <h4 class="header-title float-left">@lang('Liste des salles de conferences et reunions')</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('booking_salle.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.salles.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('messages.code')</th>
                                    <th width="10%">@lang('messages.quantity')</th>
                                    <th width="10%">@lang('Taux TVA')</th>
                                    <th width="10%">@lang('Taux marge')</th>
                                    <th width="10%">@lang('Taux majoration')</th>
                                    <th width="10%">@lang('Taux Reduction')</th>
                                    <th width="10%">@lang('messages.selling_price')</th>
                                    <th width="10%">@lang('ETAT')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($salles as $salle)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $salle->name }}</td>
                                    <td>{{ $salle->code }}</td>
                                    <td>{{ $salle->quantity }}</td>
                                    <td>{{ $salle->vat }}%</td>
                                    <td>{{ $salle->taux_marge }}%</td>
                                    <td>{{ $salle->taux_majoration }}%</td>
                                    <td>{{ $salle->taux_reduction }}%</td>
                                    <td>{{ number_format($salle->selling_price,0,',',' ') }}</td>
                                    @if($salle->status == 1)
                                    <td><span  class="badge badge-success">Reservée</span></td>
                                    @else
                                    <td><span class="badge badge-primary">Libre</span></td>
                                    @endif
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('booking_salle.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.salles.edit', $salle->id) }}">@lang('messages.edit')</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('booking_salle.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.salles.destroy', $salle->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $salle->id }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $salle->id }}" action="{{ route('admin.salles.destroy', $salle->id) }}" method="POST" style="display: none;">
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