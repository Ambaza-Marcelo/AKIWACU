
@extends('backend.layouts.master')

@section('title')
@lang('Nouvelle pompe') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Nouvelle pompe')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-fuel-pumps.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Nouvelle pompe')</span></li>
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
                    <h4 class="header-title">Nouvelle pompe</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.sotb-fuel-pumps.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="name">Désignation</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Entrer le désignation">
                                </div>
                                <div class="form-group">
                                    <label for="emplacement">Emplacement</label>
                                    <input type="text" class="form-control" id="emplacement" name="emplacement" placeholder="Entrer emplacement">
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Quantité</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Entrer Quantité">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="capacity">Capacité (en litres)</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" placeholder="Entrer la capacité">
                                </div>
                                <div class="form-group">
                                    <label for="fuel_id">Carburant</label>
                                    <select name="fuel_id" id="fuel_id" class="form-control">
                                        <option value="0">merci de choisir</option>
                                        @foreach($fuels as $fuel)
                                        <option value="{{ $fuel->id}}">{{$fuel->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="quantite_seuil">Quantité Seuil</label>
                                    <input type="number" class="form-control" id="quantite_seuil" name="quantite_seuil" placeholder="Entrer Quantité Seuil">
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
