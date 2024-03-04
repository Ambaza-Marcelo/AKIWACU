
@extends('backend.layouts.master')

@section('title')
@lang('Entree du carburant') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Entree du carburant')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-fuel-stockins.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Entree du carburant')</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>

<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Entree du carburant</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.sotb-fuel-stockins.store') }}" method="POST">
                        @csrf

                         <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="date">date</label>
                                <input type="date" class="form-control" id="date" name="date">
                                </div>
                            </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label for="handingover">Remettant</label>
                                <input type="text" class="form-control" id="handingover" name="handingover" placeholder="Enter handingover">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="origin">Origin</label>
                                <input type="text" class="form-control" id="origin" name="origin" placeholder="Enter origin">
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_movement_type">@lang('Type Entrée')</label>
                                    <select class="form-control" name="item_movement_type" id="item_movement_type" required>
                                    <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="EN">Entrée Normale</option>
                                        <option value="ER">Entrée Retour Marchandises</option>
                                        <option value="EAJ">Entrée Ajustement</option>
                                        <option value="EAU">Entrée Autre</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pump_id">Cuve de Stockage</label>
                                    <select class="form-control" name="pump_id" id="pump_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($pumps as $pump)
                                <option value="{{ $pump->id }}" class="form-control">{{ $pump->name }}</option>
                                @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="receptionist">@lang('messages.receptionist')</label>
                                    <input type="text" class="form-control" id="receptionist" name="receptionist" placeholder="Enter receptionist">
                                </div>
                            </div>
                        </div>
                            <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Type de Carburant</th>
                                <th>Quantité</th>
                                <th>Prix d'Achat</th>
                                <th>Action</th>
                            </tr>
                            <tr>  
                                <td> <select id="driver-dropdown" class="form-control" name="fuel_id[]" id="fuel_id">
                                <option disabled="disabled">merci de choisir</option>
                                @foreach($fuels as $fuel)
                                <option value="{{ $fuel->id }}" selected="selected" class="form-control">{{ $fuel->name }}</option>
                                @endforeach
                                </select></td>
                                <td><input type="number" name="quantity[]" class="form-control"  step="any" min="0" placeholder="Entrer la Quantité" /></td> 
                                <td><input type="number" name="purchase_price[]" class="form-control"  step="any" min="0" placeholder="Entrer le prix d'achat" /></td> 
                                <td>     
                            </tr>
                        </table> 
                        <div class="col-lg-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Entrer description">
                                ENTREE DU CARBURANT AU CUVE DE STOCKAGE
                            </textarea>
                        </div>                   
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        
</div>
@endsection
