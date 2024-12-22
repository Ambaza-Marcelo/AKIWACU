
@extends('backend.layouts.master')

@section('title')
@lang('Créer un Ecole') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Créer un Ecole')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.hr-ecoles.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Créer un Ecole')</span></li>
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
                    <h4 class="header-title">Créer un Ecole</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-ecoles.store') }}" method="POST">
                        @csrf
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="nom">Nom Ecole<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="nom" placeholder="Entrer Nom Ecole" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="etat">Etat<span class="text-danger"></span></label>
                                        <select class="form-control" name="etat">
                                            <option selected disabled>merci de choisir</option>
                                            <option value="0">Privé</option>
                                            <option value="1">Publique</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="adresse">Adresse<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="adresse" placeholder="Entrer Adresse" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="description">Description<span class="text-danger"></span></label>
                                        <textarea name="description" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">@lang('messages.save')</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection