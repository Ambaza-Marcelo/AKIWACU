
@extends('backend.layouts.master')

@section('title')
@lang('Nouvelle membre d\'equipe') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Nouvelle membre d\'equipe')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.employes.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Nouvelle membre d\'equipe')</span></li>
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
                    <h4 class="header-title">Nouvelle membre d'equipe</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.employes.store') }}" method="POST"  enctype="multipart/form-data">
                        @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="name">Nom Complet<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="name" placeholder="Entrer Votre Nom Complet" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="position_id">Position<span class="text-danger"></span></label>
                                        <select class="form-control" name="position_id" id="position_id">
                                            <option disabled= "disabled" selected="selected">merci de choisir</option>
                                            @foreach($positions as $position)
                                            <option value="{{ $position->id }}" class="form-control">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="telephone">Numero Téléphone<span class="text-danger"></span></label>
                                        <input autofocus type="tel" class="form-control" name="telephone" placeholder="Saisir le Numero Telephone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="address_id">Adresse<span class="text-danger"></span></label>
                                        <select class="form-control" name="address_id" id="address_id">
                                            <option disabled= "disabled" selected="selected">merci de choisir</option>
                                            @foreach($addresses as $address)
                                            <option value="{{ $address->id }}" class="form-control">{{ $address->country_name }}/{{ $address->city }}/{{ $address->district }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                               <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="image">Image<span class="text-danger"></span></label>
                                        <input type="file" class="form-control" name="image">
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
@endsection