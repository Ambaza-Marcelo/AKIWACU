
@extends('backend.layouts.master')

@section('title')
@lang('Liste des Stagiaires') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Liste des Stagiaires')</h4>
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
                    <h4 class="header-title float-left">Liste des Stagiaires</h4>
                    <p class="float-right mb-2">
                            <a class="btn btn-primary text-white" href="{{ route('admin.hr-stagiaires.create') }}">@lang('messages.new')</a>
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Nom</th>
                                    <th width="10%">Prenom</th>
                                    <th width="10%">Telephone</th>
                                    <th width="10%">Diplome</th>
                                    <th width="10%">Departement</th>
                                    <th width="10%">Service</th>
                                    <th width="10%">Position</th>
                                    <th width="10%">E-mail</th>
                                    <th width="10%">Genre</th>
                                    <th width="10%">Date Naissance</th>
                                    <th width="10%">Groupe Sanguin</th>
                                    <th width="10%">CNI</th>
                                    <th width="10%">Province</th>
                                    <th width="10%">Commune</th>
                                    <th width="10%">Zone</th>
                                    <th width="10%">Quartier</th>
                                    <th width="10%">Nbre Enfants</th>
                                    <th width="10%">Nom du Père</th>
                                    <th width="10%">Nom du Mère</th>
                                    <th width="10%">Residence Actuelle</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($stagiaires as $stagiaire)
                               <tr>
                                    <td>{{ $loop->index+1 }} </td>
                                    <td><a href="{{ route('admin.hr-stagiaires.show',$stagiaire->id) }}">{{ $stagiaire->firstname }} </a></td>
                                    <td>{{ $stagiaire->lastname }}</td>
                                    <td>{{ $stagiaire->phone_no }}</td>
                                    <td>@if($stagiaire->grade_id){{ $stagiaire->grade->name }}@endif</td>
                                    <td>@if($stagiaire->departement_id){{ $stagiaire->departement->name }} @endif</td>
                                    <td>@if($stagiaire->service_id){{ $stagiaire->service->name }} @endif</td>
                                    <td>@if($stagiaire->fonction_id){{ $stagiaire->fonction->name }} @endif</td>
                                    <td>{{ $stagiaire->mail }}</td>
                                    <td>{{ $stagiaire->gender }}</td>
                                    <td>{{ $stagiaire->birthdate }}</td>
                                    <td>{{ $stagiaire->bloodgroup }}</td>
                                    <td>{{ $stagiaire->cni }}</td>
                                    <td>{{ $stagiaire->province }}</td>
                                    <td>{{ $stagiaire->commune }}</td>
                                    <td>{{ $stagiaire->zone }}</td>
                                    <td>{{ $stagiaire->quartier }}</td>
                                    <td>{{ $stagiaire->children_number }}</td>
                                    <td>{{ $stagiaire->fathername }}</td>
                                    <td>{{ $stagiaire->mothername }}</td>
                                    <td>{{ $stagiaire->quartier_residence_actuel }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('hr_stagiaire.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.hr-stagiaires.edit', $stagiaire->id) }}">Editer</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('hr_stagiaire.edit'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.hr-stagiaires.destroy', $stagiaire->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $stagiaire->id }}').submit();">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $stagiaire->id }}" action="{{ route('admin.hr-stagiaires.destroy', $stagiaire->id) }}" method="POST" style="display: none;">
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