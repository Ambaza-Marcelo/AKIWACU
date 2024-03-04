
@extends('backend.layouts.master')

@section('title')
@lang('Prendre un congé payé') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Prendre un congé payé')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Prendre un congé payé')</span></li>
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
                    <h4 class="header-title">Prendre un congé payé</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-take-conge-payes.store') }}" method="POST">
                        @csrf
                    <input type="hidden" name="company_id" value="{{ $company_id }}">
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="employe_id">@lang('Employé')</label>
                            <select class="form-control" name="employe_id" id="employe_id-dropdown">
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
                                <label for="date_heure_debut">@lang('Date début')</label>
                                <input type="date" class="form-control" name="date_heure_debut" min="{{ date('Y') }}-{{date('m')}}/{{ date('d')}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_heure_fin">@lang('Date fin')</label>
                                <input type="date" class="form-control" name="date_heure_fin">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" id="nbre_jours_conge_paye">

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="nbre_jours_conge_sollicite">@lang('Nbre Jrs Congé Sollicité')</label>
                                <input type="number" class="form-control" name="nbre_jours_conge_sollicite" min="2" max="20">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group" id="nbre_jours_conge_pris">

                            </div>
                        </div> 
                        <div class="col-sm-6">
                           <div class="form-group" id="nbre_jours_conge_restant">
                                
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
                $("#nbre_jours_conge_restant").html('');
                $.ajax({
                    url: "{{route('admin.hr-take-conge-payes.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#nbre_jours_conge_restant').html('<label>Nbre Jrs Congé Restant</label>');
                        $.each(result.data, function (key, value) {
                            $("#nbre_jours_conge_restant").append('<input type="number" name="nbre_jours_conge_restant" class="form-control" min="2" readonly value="' + value
                                .nbre_jours_conge_restant + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#nbre_jours_conge_paye").html('');
                $.ajax({
                    url: "{{route('admin.hr-take-conge-payes.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#nbre_jours_conge_paye').html('<label>Nbre Jrs Congé Annuel</label>');
                        $.each(result.data, function (key, value) {
                            $("#nbre_jours_conge_paye").append('<input type="number" class="form-control" min="0" readonly value="' + value
                                .nbre_jours_conge_paye + '">');
                        });
                    }
                });
            });

    $('#employe_id-dropdown').on('change', function () {
                var idEmploye = this.value;
                $("#nbre_jours_conge_pris").html('');
                $.ajax({
                    url: "{{route('admin.hr-take-conge-payes.fetch')}}",
                    type: "POST",
                    data: {
                        employe_id: idEmploye,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#nbre_jours_conge_pris').html('<label>Nbre Jrs Congé Pris</label>');
                        $.each(result.data, function (key, value) {
                            $("#nbre_jours_conge_pris").append('<input type="number" class="form-control" min="0" readonly value="' + value
                                .nbre_jours_conge_pris + '">');
                        });
                    }
                });
            });

</script>
@endsection
