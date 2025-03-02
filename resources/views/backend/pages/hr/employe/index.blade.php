
@extends('backend.layouts.master')

@section('title')
@lang('Employés') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Employés')</h4>
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
                    <h4 class="header-title float-left">Employés</h4>
                    <p class="float-right mb-2">
                            <a class="btn btn-success text-white" href="{{ route('admin.hr-employes.create') }}"><i class="fa fa-plus-square" title="Ajouter" aria-hidden="false"></i></a>
                    </p>
                    <p class="float-right mb-2">
                            <a class="btn btn-success text-white" href="{{ route('admin.hr-employes.exportToExcel',$company_id) }}">Exporter en Excel</a>&nbsp;
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">No Matricule</th>
                                    <th width="10%">Nom</th>
                                    <th width="10%">Prenom</th>
                                    <th width="10%">Telephone</th>
                                    <th width="10%">Grade</th>
                                    <th width="10%">Departement</th>
                                    <th width="10%">Service</th>
                                    <th width="10%">Fonction</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($employes as $employe)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td> <a href="{{ route('admin.hr-employes.show',$employe->id) }}">{{ $employe->matricule_no }}/{{ \Carbon\Carbon::parse($employe->date_debut)->format('Y')}}</a> </td>
                                    <td>{{ $employe->firstname }}</td>
                                    <td>{{ $employe->lastname }}</td>
                                    <td>{{ $employe->phone_no }}</td>
                                    <td>{{ $employe->grade->name }}</td>
                                    <td>{{ $employe->departement->name }}</td>
                                    <td>{{ $employe->service->name }}</td>
                                    <td>{{ $employe->fonction->name }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('hr_employe.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.hr-employes.edit', $employe->id) }}">Editer</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('hr_employe.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.hr-employes.destroy', $employe->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $employe->id }}').submit();">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $employe->id }}" action="{{ route('admin.hr-employes.destroy', $employe->id) }}" method="POST" style="display: none;">
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