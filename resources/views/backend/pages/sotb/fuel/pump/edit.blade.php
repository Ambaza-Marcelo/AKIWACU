
@extends('backend.layouts.master')

@section('title')
@lang('modification pompe') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('modification pompe')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-fuel-pumps.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('modification pompe')</span></li>
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
                    <h4 class="header-title">modification pompe</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.sotb-fuel-pumps.update',$fuel_pump) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="name">Désignation</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{$fuel_pump->name}}">
                                </div>
                                <div class="form-group">
                                    <label for="emplacement">Emplacement</label>
                                    <input type="text" class="form-control" id="emplacement" name="emplacement" value="{{$fuel_pump->emplacement}}">
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Quantité</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{$fuel_pump->quantity}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="capacity">Capacité (en litres)</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" value="{{$fuel_pump->capacity}}">
                                </div>
                                <div class="form-group">
                                    <label for="fuel_id">Carburant</label>
                                    <select name="fuel_id" id="fuel_id" class="form-control">
                                        <option value="0">merci de choisir</option>
                                        @foreach($fuels as $fuel)
                                        <option value="{{ $fuel->id}}" {{$fuel_pump->fuel_id == $fuel->id  ? 'selected' : ''}}>{{$fuel->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="quantite_seuil">Quantité Seuil</label>
                                    <input type="number" class="form-control" id="quantite_seuil" name="quantite_seuil" value="{{$fuel_pump->quantite_seuil}}">
                                </div>
                            </div>
                        </div>                   
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->    
    </div>
</div>
@endsection
