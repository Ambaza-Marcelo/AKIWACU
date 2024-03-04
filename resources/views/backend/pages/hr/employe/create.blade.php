
@extends('backend.layouts.master')

@section('title')
@lang('Enregistrer un Employé') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Enregistrer un Employé')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Enregistrer un Employé')</span></li>
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
                    <h4 class="header-title">Enregistrer un Employé</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-employes.store') }}" method="POST">
                        @csrf
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="firstname">Nom *<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="firstname" placeholder="Entrer Nom " required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="lastname">Prenom *<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="lastname" placeholder="Entrer Prenom" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="company_id">Entreprise *<span class="text-danger"></span></label>
                                        <select class="form-control" name="company_id" required id="company_id">
                                            <option disabled selected>Merci de choisir</option>
                                            @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="type_contrat">Type Contrat<span class="text-danger"></span></label>
                                        <select id="type_contrat" class="form-control" name="type_contrat" required>
                                            <option disabled selected>Merci de choisir</option>
                                            <option value="CDI">Indéterminé</option>
                                            <option value="CDD">Déterminé</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="date_debut">Date d'Entrée *<span class="text-danger"></span></label>
                                        <input autofocus type="date" class="form-control" name="date_debut" placeholder="Entrer Date Entrée" required>
                                    </div>
                                </div>
                                <div class="col-md-4" id="dynamicTypeContrat">
                                    <div class="form-group has-feedback">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="somme_salaire_base">Salaire de Base *<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="somme_salaire_base" placeholder="Entrer Somme Salaire Base" required min="0" minlength="3" maxlength="10">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="prime_fonction">Prime de fonction *<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="prime_fonction" placeholder="Entrer prime de fonction" required min="0" minlength="3" maxlength="10">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="banque_id">Banque *<span class="text-danger"></span></label>
                                        <select class="form-control" name="banque_id" required id="banque_id">
                                            <option disabled selected>Merci de choisir</option>
                                            @foreach($banques as $banque)
                                            <option value="{{ $banque->id }}">{{ $banque->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="numero_compte">Numero de Compte<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="numero_compte" placeholder="Entrer Numéro de compte">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="phone_no">Téléphone<span class="text-danger"></span></label>
                                        <input autofocus type="tel" class="form-control" name="phone_no" placeholder="Entrer Téléphone ">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="grade_id">Grade *<span class="text-danger"></span></label>
                                        <select class="form-control" name="grade_id" required>
                                            <option disabled selected>Merci de choisir</option>
                                            @foreach($grades as $grade)
                                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                    <label for="departement_id">Departement *<span class="text-danger"></span></label>
                                        <select class="form-control" name="departement_id" required>
                                            <option disabled selected>Merci de choisir</option>
                                            @foreach($departements as $departement)
                                            <option value="{{ $departement->id }}">{{ $departement->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                    <label for="service_id">Service *<span class="text-danger"></span></label>
                                        <select class="form-control" name="service_id" required>
                                            <option disabled selected>Merci de choisir</option>
                                            @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                    <label for="fonction_id">Fonction *<span class="text-danger"></span></label>
                                        <select class="form-control" name="fonction_id" required>
                                            <option disabled selected>Merci de choisir</option>
                                            @foreach($fonctions as $fonction)
                                            <option value="{{ $fonction->id }}">{{ $fonction->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="pays">Pays(Lieu Naissance) *<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="pays" placeholder="Entrer Le Pays " required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="province">Province(Lieu Naissance) *<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="province" placeholder="Entrer Province " required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="commune">Commune(Lieu Naissance)<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="commune" placeholder="Entrer Commune">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="zone">Zone(Lieu Naissance)<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="zone" placeholder="Entrer Zone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="quartier">Quartier(Lieu Naissance)<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="quartier" placeholder="Entrer Quartier">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="mail">E-mail<span class="text-danger"></span></label>
                                        <input autofocus type="mail" class="form-control" name="mail" placeholder="Entrer E-mail">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                    <label for="gender">Genre *<span class="text-danger"></span></label>
                                        <select class="form-control" name="gender" required>
                                            <option disabled selected>Merci de choisir</option>
                                            <option value="1">Masculin</option>
                                            <option value="2">Feminin</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for=" birthdate">Date Naissance *<span class="text-danger"></span></label>
                                        <input type="date" class="form-control" name=" birthdate" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="cni">CNI *<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="cni" placeholder="Entrer Numero CNI" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="bloodgroup">Groupe Sanguin<span class="text-danger"></span></label>
                                        <select class="form-control" name="bloodgroup">
                                            <option disabled selected>Merci de choisir</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="quartier_residence_actuel">Residence Actuel *<span class="text-danger"></span></label>
                                        <input type="text" class="form-control" name="quartier_residence_actuel" placeholder="Entrer Residence Actuel" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="statut_matrimonial">Etat Civil<span class="text-danger"></span></label>
                                        <select id="statut_matrimonial" class="form-control" name="statut_matrimonial" required>
                                            <option disabled selected>Merci de choisir</option>
                                            <option value="1">Marié(e)</option>
                                            <option value="2">Divorcé(e)</option>
                                            <option value="3">veuf(ve)</option>
                                            <option value="4">Célibtaire</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="image">Image<span class="text-danger"></span></label>
                                        <input autofocus type="file" class="form-control" name="image">
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row" id="dynamicSelect">

                            </div>
                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary mt-4 pr-4 pl-4">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
    
    $('#statut_matrimonial').change(function () { 
    if ($(this).val() === '1'){
        
        var wife_name = "<div class='col-md-6'>"+
                        "<div class='form-group'>"+
                            "<label for='wife_or_husband'>Marié(e)</label>"+
                            "<input type='text' class='form-control' id='wife_or_husband' name='wife_or_husband' placeholder='Nom Marié(e)'>"+
                            "</div>"+
                        "</div>";
        var ofchild = "<div class='col-md-6'>"+
                        "<div class='form-group'>"+
                            "<label for='children_number'>Nombre Enfants</label>"+
                            "<input type='number' class='form-control' id='children_number' name='children_number' placeholder='Nombre Enfants'>"+
                            "</div>"+
                        "</div>";
        
        $("#dynamicSelect").append(wife_name,ofchild);
    }

    if ($(this).val() === '2'){
        var wife_name = "<div class='col-md-6'>"+
                        "<div class='form-group'>"+
                            "<label for='wife_or_husband'>Marié(e)</label>"+
                            "<input type='text' class='form-control' id='wife_or_husband' name='wife_or_husband' placeholder='Nom Marié(e)'>"+
                            "</div>"+
                        "</div>";
        var ofchild = "<div class='col-md-6'>"+
                        "<div class='form-group'>"+
                            "<label for='children_number'>Nombre Enfants</label>"+
                            "<input type='number' class='form-control' id='children_number' name='children_number' placeholder='Nombre Enfants'>"+
                            "</div>"+
                        "</div>";
        
        $("#dynamicSelect").append(wife_name,ofchild);
    }

    if ($(this).val() === '3'){
        var ofchild = "<div class='form-group'>"+
                            "<label for='children_number'>Nombre Enfants</label>"+
                            "<input type='number' class='form-control' id='children_number' name='children_number' placeholder='Nombre Enfants'>"+
                        "</div>";
        
        $("#dynamicSelect").append(wife_name,ofchild);
    }

    })
    .trigger( "change" );

    $('#type_contrat').change(function () { 
    if ($(this).val() === 'CDD'){
        var type = "<div class='form-group'>"+
                            "<label for='date_fin'>Date de Fin *</label>"+
                            "<input type='date' class='form-control' name='date_fin' required"+
                        "</div>";
        
        $("#dynamicTypeContrat").append(type);
    }

    })
    .trigger( "change" );


</script>
@endsection