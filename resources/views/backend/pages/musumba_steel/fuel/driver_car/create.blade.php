
@extends('backend.layouts.master')

@section('title')
@lang('Affectation du véhicule au chauffeur') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Affectation du véhicule au chauffeur')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.driver_cars.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Affectation du véhicule au chauffeur')</span></li>
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
                    <h4 class="header-title">Affectation du véhicule au chauffeur</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.driver_cars.store') }}" method="POST">
                        @csrf
                        <table class="table table-bordered" id="dynamicTable">  
                            <tr class="bg-secondary">
                                <th>Chauffeur</th>
                                <th>Véhicule</th>
                                <th>Action</th>
                            </tr>
                            <tr class="bg-warning">    
                                <td><select class="form-control" name="driver_id" id="driver_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{$driver->nom}} {{$driver->prenom}}</option>
                                @endforeach
                            </select></td> 
                            <td><select class="form-control" name="car_id" id="car_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($cars as $car)
                                <option value="{{ $car->id }}">{{$car->immatriculation}}</option>
                                @endforeach
                            </select></td>   
                                <td><button type="submit" class="btn btn-success">Enregistrer</button></td>  
                            </tr>  
                        </table> 
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
