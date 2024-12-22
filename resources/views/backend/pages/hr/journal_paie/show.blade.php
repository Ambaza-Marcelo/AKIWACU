
@extends('backend.layouts.master')

@section('title')
@lang('Les Details sur le Journal de paie') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">Les détails sur le Journal de paie du {{ \Carbon\Carbon::parse($data->date_debut)->format('d, M Y') }} au {{ \Carbon\Carbon::parse($data->date_fin)->format('d, M Y') }}</h4>
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
                    <h4 class="header-title float-left">Les Details sur le Journal de paie du {{ \Carbon\Carbon::parse($data->date_debut)->format('d, M Y') }} au {{ \Carbon\Carbon::parse($data->date_fin)->format('d, M Y') }}</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Mois/Année</th>
                                    <th width="10%">Employé</th>
                                    <th width="10%">Matricule</th>
                                    <th width="10%">Salaire de base</th>
                                    <th width="10%">Allocation Familiale</th>
                                    <th width="10%">Indemnité de logement</th>
                                    <th width="10%">Indemnité de déplacement</th>
                                    <th width="10%">Prime de fonction</th>
                                    <th width="10%">Rémuneration brute</th>
                                    <th width="10%">INSS Pension</th>
                                    <th width="10%">INSS Risque</th>
                                    <th width="10%">INSS Employé</th>
                                    <th width="10%">INSS Employeur</th>
                                    <th width="10%">Assurance Maladie Employé</th>
                                    <th width="10%">Assurance Maladie Employeur</th>
                                    <th width="10%">IRE Retenu</th>
                                    <th width="10%">Retenu Pret</th>
                                    <th width="10%">Soins medicaux</th>
                                    <th width="10%">Autres retenues</th>
                                    <th width="10%">Total Retenue</th>
                                    <th width="10%">Salaire Net</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($journal_paies as $journal_paie)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($journal_paie->created_at)->format('m/Y') }}</td>
                                    <td>{{ $journal_paie->employe->firstname }}&nbsp;{{ $journal_paie->employe->lastname }}</td>
                                    <td>{{ $journal_paie->employe->matricule_no }}</td>
                                    <td>{{ number_format($journal_paie->somme_salaire_base,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->allocation_familiale,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->indemnite_logement,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->indemnite_deplacement,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->prime_fonction,0,',',' ') }}</td>
                                    @php
                                        $remuneration_brute = $journal_paie->somme_salaire_base + $journal_paie->allocation_familiale + $journal_paie->indemnite_logement + $journal_paie->indemnite_deplacement + $journal_paie->prime_fonction;
                                    @endphp
                                    <td>{{ number_format($remuneration_brute,0,',',' ') }}</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ number_format($journal_paie->somme_cotisation_inss,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->inss_employeur,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->assurance_maladie_employe,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->assurance_maladie_employeur,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->somme_impot,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->retenue_pret,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->soins_medicaux,0,',',' ') }}</td>
                                    <td>{{ number_format($journal_paie->autre_retenue,0,',',' ') }}</td>
                                    @php
                                        $total_deductions = $journal_paie->somme_cotisation_inss + $journal_paie->assurance_maladie_employe + $journal_paie->somme_impot + $journal_paie->retenue_pret + $journal_paie->soins_medicaux + $journal_paie->soins_medicaux + $journal_paie->autre_retenue;
                                    @endphp
                                    <td>{{ number_format($total_deductions,0,',',' ') }}</td>
                                    <td>{{ number_format(($remuneration_brute - $total_deductions),0,',',' ') }}</td>
                                    <td></td>
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