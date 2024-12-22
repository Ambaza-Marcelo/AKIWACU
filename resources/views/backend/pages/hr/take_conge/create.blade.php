
@extends('backend.layouts.master')

@section('title')
@lang('Prendre un congé') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Prendre un congé')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Prendre congé')</span></li>
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
                    <h4 class="header-title">Prendre un congé</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-take-conges.store') }}" method="POST">
                        @csrf
                    <input type="hidden" name="company_id" value="{{ $company_id }}">
                    <div class="row">
                        <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="type_employe">Type Personnel<span class="text-danger"></span></label>
                                        <select id="type_employe" class="form-control" name="type_employe">
                                            <option disabled selected>Merci de choisir</option>
                                            <option value="1">Employé</option>
                                            <option value="2">Stagiaire</option>
                                        </select>
                                    </div>
                        </div>
                        <div class="col-md-6" id="dynamicStagiaire">
     
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" id="dynamicEmploye">

                        </div>
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="type_conge_id">@lang('Type Congé')</label>
                            <select class="form-control" name="type_conge_id">
                                <option disabled selected>Merci de choisir</option>
                                @foreach($type_conges as $type_conge)
                                <option value="{{ $type_conge->id }}">{{ $type_conge->libelle}}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_heure_debut">@lang('Date de départ')</label>
                                <input type="datetime-local" class="form-control" name="date_heure_debut" min="{{ date('Y') }}-{{date('m')}}/{{ date('d')}}T00:00">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_heure_fin">@lang('Date de retour')</label>
                                <input type="datetime-local" class="form-control" name="date_heure_fin">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="motif">@lang('Motif de Sortie')</label>
                                <textarea class="form-control" name="motif">
                                    AUTORISATION DE SORTIE
                                </textarea>
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
    <script type="text/javascript">
    
    $('#type_employe').change(function () { 
    if ($(this).val() === '1'){

        var employe = "<div class='form-group'>"+
                            "<label for='employe_id'>Employé</label>"+
                            "<select name='employe_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($employes as $employe)"+
                                "<option value='{{$employe->id}}'>{{ $employe->firstname}}&nbsp;{{ $employe->lastname}}</option>"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamicEmploye").append(employe);
        $("#dynamicStagiaire").hide();
    }
    if ($(this).val() === '2'){

        var stagiaire = "<div class='form-group'>"+
                            "<label for='stagiaire_id'>Stagiaire</label>"+
                            "<select name='stagiaire_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($stagiaires as $stagiaire)"+
                                "<option value='{{$stagiaire->id}}'>{{ $stagiaire->firstname }}&nbsp;{{ $stagiaire->lastname}}</option>"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamicStagiaire").append(stagiaire);
        $("#dynamicEmploye").hide();
    }

    })
    .trigger( "change" );


</script>
@endsection
