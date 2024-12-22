
@extends('backend.layouts.master')

@section('title')
@lang('Nouvelle Fiche de Paie') - @lang('messages.admin_panel')
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('Nouvelle Fiche de Paie')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Nouvelle Fiche de Paie')</span></li>
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
                    <h4 class="header-title">Nouvelle Fiche de Paie</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-paiements.store') }}" method="POST">
                        @csrf
                    <input type="hidden" name="company_id" value="{{ $company_id }}">
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="employe_id">@lang('Employé')</label>
                            <select class="form-control" name="employe_id" id="employe_id-dropdown" required>
                                <option disabled selected>Merci de choisir</option>
                                @foreach($employes as $employe)
                                <option value="{{ $employe->id }}">{{ $employe->firstname}}&nbsp;{{ $employe->lastname}}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_debut">@lang('DU :')</label>
                                <input type="date" class="form-control" name="date_debut" value="{{ $journal_paie->date_debut }}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_fin">@lang('AU :')</label>
                                <input type="date" class="form-control" name="date_fin" value="{{ $journal_paie->date_fin }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group" id="matricule_no">

                            </div>
                        </div> 
                        <div class="col-md-6">
                           <div class="form-group" id="cni">

                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group" id="somme_salaire_base">

                            </div>
                        </div> 
                        <div class="col-md-6">
                           <div class="form-group" id="numero_compte">

                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                           <div class="form-group" id="indemnite_deplacement">

                            </div>
                        </div> 
                        <div class="col-md-4">
                           <div class="form-group" id="indemnite_logement">

                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group" id="prime_fonction">

                            </div>
                        </div>  
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                           <div class="form-group" id="code_departement">

                            </div>
                        </div> 
                        <div class="col-md-4">
                           <div class="form-group" id="code_service">

                            </div>
                        </div> 
                        <div class="col-md-4">
                           <div class="form-group" id="code_banque">

                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="retenue_pret">@lang('Retenue Prêt')</label>
                                <input type="number" class="form-control" name="retenue_pret" placeholder="Saisir Retenue Prêt" min="0" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="autre_retenue">@lang('Autres Retenues')</label>
                                <input type="number" class="form-control" placeholder="Saisir Autre Retenue " name="autre_retenue" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="soins_medicaux">@lang('Soins Medicaux')</label>
                                <input type="number" class="form-control" name="soins_medicaux" placeholder="Saisir Soins Medicaux" min="0" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="allocation_familiale">@lang('Allocations Familiales')</label>
                                <input type="number" class="form-control" placeholder="Saisir Allocations Familiales " name="allocation_familiale" min="0" required>
                            </div>
                        </div>
                    </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary">@lang('messages.save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#matricule_no").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#matricule_no').html('<label>Matricule No</label>');
                        $.each(result.data, function (key, value) {
                            $("#matricule_no").append('<input type="text" name="matricule_no" class="form-control" readonly value="' + value
                                .matricule_no + '">');
                        });
                    }
                });
            });
    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#cni").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#cni').html('<label>CNI</label>');
                        $.each(result.data, function (key, value) {
                            $("#cni").append('<input type="text" name="cni" class="form-control" readonly value="' + value
                                .cni + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#code_departement").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#code_departement').html('<label>Departement</label>');
                        $.each(result.data, function (key, value) {
                            $("#code_departement").append('<input type="text" name="code_departement" class="form-control" readonly value="' + value
                                .code_departement + '">');
                        });
                    }
                });
            });
    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#code_service").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#code_service').html('<label>Code Service</label>');
                        $.each(result.data, function (key, value) {
                            $("#code_service").append('<input type="text" name="code_service" class="form-control" readonly value="' + value
                                .code_service + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#code_banque").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#code_banque').html('<label>Code Banque</label>');
                        $.each(result.data, function (key, value) {
                            $("#code_banque").append('<input type="text" name="code_banque" class="form-control" readonly value="' + value
                                .code_banque + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#somme_salaire_base").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#somme_salaire_base').html('<label>Salaire Base</label>');
                        $.each(result.data, function (key, value) {
                            $("#somme_salaire_base").append('<input type="number" class="form-control" name="somme_salaire_base" min="0" readonly value="' + value
                                .somme_salaire_base + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#numero_compte").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#numero_compte').html('<label>Numero Compte</label>');
                        $.each(result.data, function (key, value) {
                            $("#numero_compte").append('<input type="text" name="numero_compte" class="form-control" min="0" readonly value="' + value
                                .numero_compte + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#indemnite_deplacement").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#indemnite_deplacement').html('<label>Indemnite de deplacement</label>');
                        $.each(result.data, function (key, value) {
                            $("#indemnite_deplacement").append('<input type="number" name="indemnite_deplacement" class="form-control" min="0" readonly value="' + value
                                .indemnite_deplacement + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#indemnite_logement").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#indemnite_logement').html('<label>Indemnite de logement</label>');
                        $.each(result.data, function (key, value) {
                            $("#indemnite_logement").append('<input type="number" name="indemnite_logement" class="form-control" min="0" readonly value="' + value
                                .indemnite_logement + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#prime_fonction").html('');
                $.ajax({
                    url: "{{route('admin.hr-paiements.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#prime_fonction').html('<label>Prime de fonction</label>');
                        $.each(result.data, function (key, value) {
                            $("#prime_fonction").append('<input type="number" name="prime_fonction" class="form-control" min="0" readonly value="' + value
                                .prime_fonction + '">');
                        });
                    }
                });
            });

</script>
@endsection
