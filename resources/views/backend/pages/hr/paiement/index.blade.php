
@extends('backend.layouts.master')

@section('title')
@lang('Liste de paies pour un mois dernier') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">Liste de paies pour un mois dernier</h4>
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
                    <h4 class="header-title float-left">Liste de paies pour un mois dernier</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Mois/Année</th>
                                    <th width="10%">No Matricule</th>
                                    <th width="10%">Employé</th>
                                    <th width="10%">Fonction</th>
                                    <th width="10%">Somme S.Base</th>
                                    <th width="10%">Salaire Brut IMposable</th>
                                    <th width="10%">Salaire Brut Non IMposable</th>
                                    <th width="10%">Salaire Net IMposable</th>
                                    <th width="10%">Salaire Net Non IMposable</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($paiements as $paiement)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($paiement->created_at)->format('m/Y') }}
                                        @if($paiement->etat == 0) <span class="badge badge-primary">Encours...</span> @elseif($paiement->etat == 1) <span class="badge badge-success">Déjà clôturé</span> @endif
                                    </td>
                                    <td>@if($paiement->employe_id)<a href="{{ route('admin.hr-paiements.show',$paiement->id) }}"> {{ $paiement->employe->matricule_no }}  @endif</a></td>
                                    <td>@if($paiement->employe_id){{ $paiement->employe->firstname }}&nbsp;{{ $paiement->employe->lastname }} @endif</td>
                                    <td>@if($paiement->employe_id){{ $paiement->employe->fonction->name }} @endif</td>
                                    <td>{{ number_format($paiement->somme_salaire_base,0,',',' ') }}</td>
                                    <td>{{ number_format($paiement->somme_salaire_brut_imposable,0,',',' ') }}</td>
                                    <td>{{ number_format($paiement->somme_salaire_brut_non_imposable,0,',',' ') }}</td>
                                    <td>{{ number_format($paiement->somme_salaire_net_imposable,0,',',' ') }}</td>
                                    <td>{{ number_format($paiement->somme_salaire_net_non_imposable,0,',',' ') }}</td>
                                    <td>
                                       @if (Auth::guard('admin')->user()->can('hr_employe.create'))
                                        <a href="" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @if($paiement->etat != 1)
                                        @if($paiement->employe_id)<a href="{{ route('admin.hr-paiements.edit',[$paiement->id,$paiement->company_id]) }}" class="btn btn-success"> Modifier  @endif</a>
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