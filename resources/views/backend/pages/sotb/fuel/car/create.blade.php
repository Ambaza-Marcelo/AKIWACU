
@extends('backend.layouts.master')

@section('title')
@lang('Véhicules') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Véhicules')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-cars.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.address_create')</span></li>
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
                    <h4 class="header-title">Nouveau vehicule</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.sotb-cars.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="marque">Marque *</label>
                                <input type="text" class="form-control" id="marque" name="marque" placeholder="Entrer le marque">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="couleur">Couleur</label>
                                    <input type="text" class="form-control" id="couleur" name="couleur" placeholder="Entrer la couleur">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Type Vehicule</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">merci de choisir</option>
                                        <option value="1">POIDS LOURDS</option>
                                        <option value="2">POIDS LEGERS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="etat">Etat</label>
                                    <select name="etat" id="etat" class="form-control">
                                        <option value="">merci de choisir</option>
                                        <option value="1">En bon état</option>
                                        <option value="0">En panne</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="chassis_no">Chassis No</label>
                                <input type="text" class="form-control" id="chassis_no" name="chassis_no" placeholder="Entrer le No Chassis">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="immatriculation">Plaque *</label>
                                <input type="text" class="form-control" id="immatriculation" name="immatriculation" placeholder="Entrer le plaque">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fuel_id">Carburant *</label>
                                    <select name="fuel_id" id="fuel_id" class="form-control">
                                        <option>merci de choisir</option>
                                        @foreach($fuels as $fuel)
                                        <option value="{{ $fuel->id}}">{{ $fuel->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>                   
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Enregister</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
