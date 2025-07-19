
@extends('backend.layouts.master')

@section('title')
@lang('Piscines') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Piscines')</h4>
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
                    <h4 class="header-title float-left">Piscines</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('booking.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.booking-swiming-pool.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Booking No</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="30%">@lang('Designation')</th>
                                    <th width="30%">@lang('Quantites')</th>
                                    <th width="30%">@lang('P.V')</th>
                                    <th width="30%">@lang('Date Debut')</th>
                                    <th width="30%">@lang('Date Fin')</th>
                                    <th width="10%">@lang('messages.status')</th>
                                    <th width="30%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($bookings as $booking)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td><a href="{{ route('admin.bookings.show',$booking->booking_no) }}">{{ $booking->booking_no }}</a></td>
                                    <td>{{ Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</td>
                                    <td>@if($booking->swiming_pool_id) {{ $booking->swimingPool->name }} @endif</td>
                                    <td>{{ $booking->quantity }}</td>
                                    <td>{{ number_format($booking->total_amount_selling,0,',',' ') }}</td>
                                    <td>{{ $booking->date_debut }}</td>
                                    <td>{{ $booking->date_fin }}</td>
                                    @if($booking->status == 1)
                                    <td><span  class="badge badge-success">Validée</span></td>
                                    @elseif($booking->status == -1)
                                    <td><span class="badge badge-danger">Rejetée</span></td>
                                    @elseif($booking->status == 2)
                                    <td><span class="badge badge-warning">Facturée(Encours)</span></td>
                                    @elseif($booking->status == 3)
                                    <td><span class="badge badge-success">Facturée</span></td>
                                    @else
                                    <td><span class="alert alert-primary">Encours...</span></td>
                                    @endif
                                    <td>{{ $booking->description }}</td>
                                    <td>{{ $booking->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('booking.create'))
                                        @if($booking->status == 0 || $booking->status == 1)
                                        <a href="{{ route('admin.bookings.generatepdf',$booking->booking_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('booking.validate'))
                                        @if($booking->status == 0 || $booking->status == -1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.bookings.validate', $booking->booking_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $booking->booking_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $booking->booking_no }}" action="{{ route('admin.bookings.validate', $booking->booking_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('booking.reject'))
                                            <a class="btn btn-primary text-white" href="{{ route('admin.bookings.reject', $booking->booking_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $booking->booking_no }}').submit();">
                                                Rejeter
                                            </a>
                                            <form id="reject-form-{{ $booking->booking_no }}" action="{{ route('admin.bookings.reject', $booking->booking_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('booking.reset'))
                                            @if($booking->status == -1 || $booking->status == 1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.bookings.reset', $booking->booking_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $booking->booking_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $booking->booking_no }}" action="{{ route('admin.bookings.reset', $booking->booking_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if($booking->status == 0)
                                        @if (Auth::guard('admin')->user()->can('booking.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.bookings.edit', $booking->booking_no) }}">@lang('messages.edit')</a>
                                        @endif
                                        @endif
                                        @if($booking->status == 1)
                                        @if (Auth::guard('admin')->user()->can('invoice_swiming_pool.create'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.booking-invoices.create', $booking->booking_no) }}">@lang('Facturation')</a>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('booking.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.bookings.destroy', $booking->booking_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $booking->booking_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $booking->booking_no }}" action="{{ route('admin.bookings.destroy', $booking->booking_no) }}" method="POST" style="display: none;">
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

    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    window.onunload = function () {
        null
    };

     </script>
@endsection