
@extends('backend.layouts.master')

@section('title')
@lang('Modifier la fiche de paie') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier la fiche de paie')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Modifier la fiche de paie')</span></li>
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
                    <h4 class="header-title">Modifier la fiche de paie</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-paiements.update',$data->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                    <input type="hidden" name="company_id" value="{{ $company_id }}">
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="employe_id">@lang('Employé')</label>
                            <select class="form-control" name="employe_id" id="employe_id-dropdown" required>
                                <option disabled selected>Merci de choisir</option>
                                @foreach($employes as $employe)
                                <option value="{{ $employe->id }}" {{ $data->employe_id == $employe->id ? 'selected' : '' }}>{{ $employe->firstname}}&nbsp;{{ $employe->lastname}}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_debut">@lang('DU :')</label>
                                <input type="datetime-local" class="form-control" name="date_debut" value="{{ $data->date_debut }}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_fin">@lang('AU :')</label>
                                <input type="datetime-local" class="form-control" name="date_fin" value="{{ $data->date_fin }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group" id="matricule_no">
                                <label for="matricule_no">@lang('No Matricule')</label>
                                <input type="text" class="form-control" name="matricule_no" value="{{ $data->employe->matricule_no }}" readonly> 
                            </div>
                        </div> 
                        <div class="col-md-6">
                           <div class="form-group" id="cni">
                                <label for="cni">@lang('CNI')</label>
                                <input type="text" class="form-control" name="cni" value="{{ $data->employe->cni }}" readonly> 
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group" id="code_departement">
                                <label for="code_departement">@lang('Departement')</label>
                                <input type="text" class="form-control" name="code_departement" value="{{ $data->employe->departement->name }}" readonly> 
                            </div>
                        </div> 
                        <div class="col-md-6">
                           <div class="form-group" id="">
                                <label for="">@lang('Fonction')</label>
                                <input type="text" class="form-control" name="" value="{{ $data->employe->fonction->name }}" readonly> 
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group" id="code_banque">
                                <label for="code_banque">@lang('Banque')</label>
                                <input type="text" class="form-control" name="code_banque" value="{{ $data->employe->banque->name }}" readonly>
                            </div>
                        </div> 
                        <div class="col-md-6">
                           <div class="form-group">
                                <label for="numero_compte">@lang('Numero de Compte')</label>
                                <input type="text" class="form-control" name="numero_compte" value="{{ $data->numero_compte }}" readonly>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                                <label for="somme_salaire_base">@lang('Salaire de base')</label>
                                <input type="number" class="form-control" name="somme_salaire_base" value="{{ $data->somme_salaire_base }}" step="any" min="0" required> 
                            </div>
                        </div> 
                        <div class="col-md-6">
                           <div class="form-group">
                                <label for="prime_fonction">@lang('Prime de fonction')</label>
                                <input type="number" class="form-control" name="prime_fonction" value="{{ $data->prime_fonction }}" step="any" min="0" required>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="retenue_pret">@lang('Retenue Prêt')</label>
                                <input type="number" class="form-control" name="retenue_pret" value="{{ $data->retenue_pret }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="autre_retenue">@lang('Autres Retenues')</label>
                                <input type="number" class="form-control" value="{{ $data->autre_retenue }}" name="autre_retenue" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="soins_medicaux">@lang('Soins Medicaux')</label>
                                <input type="number" class="form-control" name="soins_medicaux" value="{{ $data->soins_medicaux }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="allocation_familiale">@lang('Allocations Familiales')</label>
                                <input type="number" class="form-control" value="{{ $data->allocation_familiale }}" name="allocation_familiale" min="0" required>
                            </div>
                        </div>
                    </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
</div>
@endsection
