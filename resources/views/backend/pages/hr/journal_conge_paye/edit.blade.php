
@extends('backend.layouts.master')

@section('title')
@lang('Modifier les jours des conges payes') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier les jours des conges payes')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.hr-ecoles.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier les jours des conges payes')</span></li>
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
                    <h4 class="header-title">Modifier les jours des conges payes</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-journal-conge-paye.update',$journal_conge_paye->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employe_id">@lang('Employé')</label>
                                        <input autofocus type="text" class="form-control" name="employe_id" value="{{ $journal_conge_paye->employe->firstname }} {{ $journal_conge_paye->employe->lastname }}" >
                                </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="nom">Session<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="session" value="{{ $journal_conge_paye->session }}" required min="2022">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="nom">Nbre Jrs Congé Payé<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="nbre_jours_conge_paye" value="{{ $journal_conge_paye->nbre_jours_conge_paye }}" required min="2022">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="nom">Nbre Jrs Congé Pris<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="nbre_jours_conge_pris" value="{{ $journal_conge_paye->nbre_jours_conge_pris }}" required min="2022">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="nom">Nbre Jrs Congé Restant<span class="text-danger"></span></label>
                                        <input autofocus type="number" class="form-control" name="nbre_jours_conge_restant" value="{{ $journal_conge_paye->nbre_jours_conge_restant }}" required min="2022">
                                    </div>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">@lang('messages.update')</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection