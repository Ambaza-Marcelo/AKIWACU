
@extends('backend.layouts.master')

@section('title')
@lang('Modifier Réglage') - @lang('messages.admin_panel')
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('Modifier Reglage')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.hr-reglages.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier Réglage')</span></li>
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
                    <h4 class="header-title">Modifier Réglage</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-reglages.update',$reglage->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Taux Assurance Maladie <span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="taux_assurance_maladie" value="{{ $reglage->taux_assurance_maladie }} ">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Taux retraite <span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="taux_retraite" value="{{ $reglage->taux_retraite }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Retenue Prêt <span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="retenue_pret" value="{{ $reglage->retenue_pret }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Intérêt Credit Logement<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="interet_credit_logement" value="{{ $reglage->interet_credit_logement }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Plafond Impôt *<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="prafond_impot" value="{{ $reglage->prafond_impot }}" required min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Plafond Cotisation *<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="prafond_cotisation" value="{{ $reglage->prafond_cotisation }}" required min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Nbre Jrs Ouvrable *<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="nbre_jours_ouvrables" value="{{ $reglage->nbre_jours_ouvrables }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Nbre Jrs Fériés <span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="nbre_jours_feries" value="{{ $reglage->nbre_jours_feries }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Nbre Jrs Anticipation Congé *<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="jour_anticipation_conge" value="{{ $reglage->jour_anticipation_conge }}" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Nbre Jrs Congé Par mois *<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="jour_conge_par_mois" value="{{ $reglage->jour_conge_par_mois }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Min Jrs Congé Payé par Mois <span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="min_jour_conge_paye" value="{{ $reglage->min_jour_conge_paye }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="">Max Jrs Congé Payé Par Mois *<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="max_jour_conge_paye" value="{{ $reglage->max_jour_conge_paye }}" required min="0">
                                    </div>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection