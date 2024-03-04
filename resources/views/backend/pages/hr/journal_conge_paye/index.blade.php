
@extends('backend.layouts.master')

@section('title')
@lang('Le journal de congé payé') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">Le journal de congé payé <mark>{{ $conge_paye->session }}</mark></h4>
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
                    <h4 class="header-title float-left">L journal de congé payé <mark>{{ $conge_paye->session }}</mark></h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">N<sup>o</sup> Matricule</th>
                                    <th width="10%">Employé</th>
                                    <th width="10%">Service</th>
                                    <th width="10%">Fonction</th>
                                    <th width="10%">Nbre Jrs Congé Annuel</th>
                                    <th width="10%">Nbre Jrs Congé Sollicité</th>
                                    <th width="10%">Nbre Jrs Congé Pris</th>
                                    <th width="10%">Nbre Jrs Congé Restant</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($datas as $data)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td><a href="{{ route('admin.hr-employes.show',$data->matricule_no) }}">{{ $data->employe->matricule_no }}</a></td>
                                    <td>{{ $data->employe->firstname }}&nbsp;{{ $data->employe->lastname }}</td>
                                    <td>{{ $data->employe->service->name }}</td>
                                    <td>{{ $data->employe->fonction->name }}</td>
                                    <td>{{ $data->nbre_jours_conge_paye }}</td>
                                    <td>{{ $data->nbre_jours_conge_sollicite }}</td>
                                    <td>{{ $data->nbre_jours_conge_pris }}</td>
                                    <td>{{ $data->nbre_jours_conge_restant }}</td>
                                    <td>
                                       @if (Auth::guard('admin')->user()->can('hr_conge_paye.create'))
                                        <a href="" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('hr_conge_paye.edit'))
                                        &nbsp;<a href="{{ route('admin.hr-journal-conge-paye.edit',$data->id) }}" class="btn btn-success">Editer</a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('hr_conge_paye.delete'))
                                            <a class="btn btn-danger text-white" href=""
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $data->id }}').submit();">
                                                Supprimer
                                            </a>

                                            <form id="delete-form-{{ $data->id }}" action="" method="POST" style="display: none;">
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