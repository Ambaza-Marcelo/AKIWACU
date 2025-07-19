
@extends('backend.layouts.master')

@section('title')
@lang('Fiche des paies') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Fiche des paies')</h4>
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
<div class="row">
    <div class="col-md-12">
        <div class="main-content-inner">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title float-left">Journal de paies</h4>
                    @if (Auth::guard('admin')->user()->can('hr_journal_paie.create'))
                    @if($journal_paie_encours <= 0)
                    <p class="float-right mb-2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="button" title="Nouveau journal" class="btn btn-success" data-toggle="modal" data-target="#journalPaieModal" data-whatever="@mdo"><i class="fa fa-plus-square" aria-hidden="false"></i></button>
                    </p>
                    @endif
                    @endif
                    @if (Auth::guard('admin')->user()->can('hr_paiement.create'))
                    @if($journal_paie_encours > 0)
                    <p class="float-right mb-2">
                            <a class="btn btn-success text-white" title="Nouvelle Fiche de Paie" href="{{ route('admin.hr-paiement.createByCompany') }}"><i class="fa fa-plus-square" aria-hidden="false"></i></a>
                    </p>
                    @endif
                    @endif
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Code</th>
                                    <th width="20%">Titre</th>
                                    <th width="20%">Date Debut</th>
                                    <th width="20%">Date de Clôture</th>
                                    <th width="20%">Etat</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($journal_paies as $journal_paie)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td> <a href="{{ route('admin.hr-journal-paie.select-by-company',$journal_paie->code) }}">{{ $journal_paie->code }}</a> </td>
                                    <td>{{ $journal_paie->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($journal_paie->date_debut)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($journal_paie->date_fin)->format('d/m/Y') }}</td>
                                    <td>@if($journal_paie->etat == 0) <span class="badge badge-primary">Encours...</span> @elseif($journal_paie->etat == 1) <span class="badge badge-success">Déjà clôturé</span> @endif</td>
                                    <td>
                                    @if($journal_paie->etat != 1)
                                        @if (Auth::guard('admin')->user()->can('hr_journal_paie.cloturer'))
                                        <a class="btn btn-success text-white" href="{{ route('admin.hr-journal-paies.cloturer', $journal_paie->code) }}" title="clôturer le journal de paie" 
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $journal_paie->code }}').submit();">
                                                clôturer
                                            </a>&nbsp;&nbsp;

                                        <form id="validate-form-{{ $journal_paie->code }}" action="{{ route('admin.hr-journal-paies.cloturer', $journal_paie->code) }}" method="POST" style="display: none;">
                                        @method('PUT')
                                        @csrf
                                        </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('hr_journal_paie.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.hr-journal-paies.edit', $journal_paie->code) }}"><i class="fa fa-edit" title="Modifier" aria-hidden="false"></i></a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('hr_journal_paie.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.hr-journal-paies.destroy', $journal_paie->code) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $journal_paie->code }}').submit();">
                                                <i class="fa fa-trash" title="Supprimer" aria-hidden="false"></i>
                                            </a>

                                            <form id="delete-form-{{ $journal_paie->code }}" action="{{ route('admin.hr-journal-paies.destroy', $journal_paie->code) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                        @endif
                                    @endif
                                        <form action="{{ route('admin.hr-journal-paies.export-to-excel') }}" method="GET">
                                        <button type="submit" title="Cliquer pour exporter en Excel" class="btn btn-primary">Exporter En Excel</button>
                                        <input type="hidden" name="code" class="form-control" value="{{ $journal_paie->code }}">

                                        </form>
                                    </td>
                                </tr>
                               @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
        </div>
    </div>
</div>
@include('backend.pages.hr.journal_paie.create')

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