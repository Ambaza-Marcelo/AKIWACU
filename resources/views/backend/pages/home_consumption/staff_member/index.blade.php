
@extends('backend.layouts.master')

@section('title')
@lang('Liste des responsables des services') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Liste des responsables des services')</h4>
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
                    <h4 class="header-title float-left">Liste des responsables des services</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('employe.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.staff_members.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Date Debut</th>
                                    <th width="15%">Date Fin</th>
                                    <th width="15%">Nom</th>
                                    <th width="15%">Position</th>
                                    <th width="15%">Montant Total a Consommer</th>
                                    <th width="15%">Montant Total Consomme</th>
                                    <th width="15%">Montant Total restant</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($staff_members as $staff_member)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $staff_member->start_date }}</td>
                                    <td>{{ $staff_member->end_date }}</td>
                                    <td>{{ $staff_member->name }}</td>
                                    <td>{{ $staff_member->position->name }}</td>
                                    <td>{{ $staff_member->total_amount_authorized }}</td>
                                    <td>{{ $staff_member->total_amount_consumed }}</td>
                                    <td>{{ $staff_member->total_amount_remaining }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('employe.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.staff_members.edit', $staff_member->id) }}">Edit</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('employe.edit'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.staff_members.destroy', $staff_member->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $staff_member->id }}').submit();">
                                                Delete
                                            </a>

                                            <form id="delete-form-{{ $staff_member->id }}" action="{{ route('admin.staff_members.destroy', $staff_member->id) }}" method="POST" style="display: none;">
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