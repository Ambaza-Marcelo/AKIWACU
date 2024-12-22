
@extends('backend.layouts.master')

@section('title')
@lang('Modifier Membre Equipe') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier Membre Equipe')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.employes.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier Membre Equipe')</span></li>
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
                <div class="card-body bg-success">
                    <h4 class="header-title">Modifier Membre Equipe</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.employes.update',$employe->id) }}" method="POST"  enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="name">Nom Complet<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="name" value="{{ $employe->name }}" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="position_id">Position<span class="text-danger"></span></label>
                                        <select class="form-control" name="position_id" id="position_id">
                                            <option disabled= "disabled" selected="selected">merci de choisir</option>
                                            @foreach($positions as $position)
                                            <option value="{{ $position->id }}" {{$employe->position_id == $position->id  ? 'selected' : ''}} ">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="telephone">Numero Téléphone<span class="text-danger"></span></label>
                                        <input autofocus type="tel" class="form-control" name="telephone" value="{{ $employe->telephone }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="address_id">Adresse<span class="text-danger"></span></label>
                                        <select class="form-control" name="address_id" id="address_id">
                                            <option disabled= "disabled" selected="selected">merci de choisir</option>
                                            @foreach($addresses as $address)
                                            <option value="{{ $address->id }}" {{$employe->address_id == $address->id  ? 'selected' : ''}} ">{{ $address->country_name }}/{{ $address->city }}/{{ $address->district }}</option>
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
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection