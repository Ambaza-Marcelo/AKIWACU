
@extends('backend.layouts.master')

@section('title')
@lang('Enregistrer un Stagiaire') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Enregistrer un Stagiaire')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Enregistrer un Stagiaire')</span></li>
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
                    <h4 class="header-title">Enregistrer un Stagiaire</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-stagiaires.store') }}" method="POST">
                        @csrf
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="firstname">Nom *<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="firstname" placeholder="Entrer Nom " required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="lastname">Prenom *<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="lastname" placeholder="Entrer Prenom" required>
                                    </div>
                                </div>
                        </div>
                            <div class="row">
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
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="academique_ou_professionnel">Type de Stage *<span class="text-danger"></span></label>
                                        <select id="academique_ou_professionnel" class="form-control" name="academique_ou_professionnel" required>
                                            <option disabled selected>Merci de choisir</option>
                                            <option value="1">Academique</option>
                                            <option value="2">Professionnel</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6" id="dynamicDiplome">

                                </div>
                                <div class="col-md-6" id="dynamicSommePrime">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6" id="dynamicEcole">

                                </div>
                                <div class="col-md-6" id="dynamicFiliere">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="phone_no">Telephone<span class="text-danger"></span></label>
                                        <input autofocus type="tel" class="form-control" name="phone_no" placeholder="Entrer Telephone ">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                    <label for="departement_id">Departement<span class="text-danger"></span></label>
                                        <select class="form-control" name="departement_id">
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
                                    <label for="service_id">Service<span class="text-danger"></span></label>
                                        <select class="form-control" name="service_id">
                                            <option disabled selected>Merci de choisir</option>
                                            @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                    <label for="position_id">Fonction<span class="text-danger"></span></label>
                                        <select class="form-control" name="position_id">
                                            <option disabled selected>Merci de choisir</option>
                                            @foreach($fonctions as $fonction)
                                            <option value="{{ $fonction->id }}">{{ $fonction->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="province">Province <span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="province" placeholder="Entrer Province ">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="commune">Commune<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="commune" placeholder="Entrer Commune">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="zone">Zone<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="zone" placeholder="Entrer Zone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="quartier">Quartier<span class="text-danger"></span></label>
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
                                    <label for="gender">Genre<span class="text-danger"></span></label>
                                        <select class="form-control" name="gender">
                                            <option disabled selected>Merci de choisir</option>
                                            <option value="1">Male</option>
                                            <option value="2">Femelle</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for=" birthdate">Date Naissance<span class="text-danger"></span></label>
                                        <input type="date" class="form-control" name=" birthdate">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="cni">CNI<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="cni" placeholder="Entrer Numero CNI">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="quartier_residence_actuel">Residence Actuel<span class="text-danger"></span></label>
                                        <input type="text" class="form-control" name="quartier_residence_actuel" placeholder="Entrer Residence Actuel">
                                    </div>
                                </div>
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
    
    $('#academique_ou_professionnel').change(function () { 
    if ($(this).val() === '2'){
        
        var somme_prime = "<div class='form-group'>"+
                            "<label for='somme_prime'>Somme Prime</label>"+
                            "<input type='number' class='form-control' id='somme_prime' min='50000' max='100000' name='somme_prime' value='50000'>"+
                        "</div>";
        var grade = "<div class='form-group'>"+
                            "<label for='grade_id'>Grade</label>"+
                            "<select name='grade_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($grades as $grade)"+
                                "<option value='{{$grade->id}}'>{{ $grade->name }}</option>"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamicDiplome").append(grade);
        $("#dynamicSommePrime").append(somme_prime);
        $("#dynamicEcole").hide();
        $("#dynamicFiliere").hide();
    }
    if ($(this).val() === '1'){
        
        var somme_prime = "<div class='form-group'>"+
                            "<label for='somme_prime'>Somme Prime</label>"+
                            "<input type='number' class='form-control' id='somme_prime' min='50000' max='100000' name='somme_prime' value='50000'>"+
                        "</div>";
        var ecole = "<div class='form-group'>"+
                            "<label for='ecole_id'>Ecole</label>"+
                            "<select name='ecole_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($ecoles as $ecole)"+
                                "<option value='{{$ecole->id}}'>{{ $ecole->nom }}</option>"+
                                "@endforeach"
                            +
                        "</div>";
        var filiere = "<div class='form-group'>"+
                            "<label for='filiere_id'>Filière</label>"+
                            "<select name='filiere_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($filieres as $filiere)"+
                                "<option value='{{$filiere->id}}'>{{ $filiere->nom }}</option>"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamicEcole").append(ecole);
        $("#dynamicFiliere").append(filiere);
        $("#dynamicDiplome").hide();
        $("#dynamicSommePrime").hide();
    }

    })
    .trigger( "change" );


</script>
@endsection