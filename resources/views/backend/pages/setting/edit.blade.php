
@extends('backend.layouts.master')

@section('title')
@lang('messages.setting_edit') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.setting_edit')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.settings.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.setting_edit')</span></li>
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
                    <h4 class="header-title">Edit Setting</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.settings.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                            <div class="row">
                            <div class="col-md-6">
                                <label for="tp_type">Type Contribuable</label>
                                <div class="form-group">
                                    <label class="text">Personne Physique
                                    <input type="checkbox" name="tp_type" value="1" @if($setting->tp_type == '1') checked="checked" @endif class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Société
                                    <input type="checkbox" name="tp_type" value="2" @if($setting->tp_type == '2') checked="checked" @endif class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="vat_taxpayer">Assujetti à la TVA</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_taxpayer" value="0" @if($setting->vat_taxpayer == '0') checked="checked" @endif class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_taxpayer" value="1" @if($setting->vat_taxpayer == '1') checked="checked" @endif class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="tl_taxpayer">Assujetti au PFL</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="tl_taxpayer" value="0" @if($setting->tl_taxpayer == '0') checked="checked" @endif class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="tl_taxpayer" value="1" @if($setting->tl_taxpayer == '1') checked="checked" @endif class="form-control">
                                    </label>
                                </div>
                            </div>
                            <label for="ct_taxpayer">Assujetti à la taxe de conso.</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="ct_taxpayer" value="0" @if($setting->ct_taxpayer == '0') checked="checked" @endif  class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="ct_taxpayer" value="1" @if($setting->ct_taxpayer == '1') checked="checked" @endif class="form-control">
                                    </label>
                                </div>
                        </div>
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="name">Nom Entreprise<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="name" value="{{ $setting->name }}" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="nif">NIF<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="nif" value="{{ $setting->nif }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="rc">Registre Commerce<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="rc" value="{{ $setting->rc }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="tp_fiscal_center">Centre Fiscale</label>
                                    <div class="form-group">
                                    <label class="text">DGC
                                    <input type="checkbox" name="tp_fiscal_center" value="DGC" @if($setting->tp_fiscal_center == 'DGC') checked="checked" @endif class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">DMC
                                    <input type="checkbox" name="tp_fiscal_center" value="DMC" @if($setting->tp_fiscal_center == 'DMC') checked="checked" @endif class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">DPMC
                                    <input type="checkbox" name="tp_fiscal_center" value="DPMC" @if($setting->tp_fiscal_center == 'DPMC') checked="checked" @endif class="form-control">
                                    </label>
                                </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="tp_activity_sector">Secteur Activite<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="tp_activity_sector" value="{{ $setting->tp_activity_sector }} " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="tp_legal_form">Forme Juridique<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="tp_legal_form" value="{{ $setting->tp_legal_form }}" required minlength="2" maxlength="255">
                                    </div>
                                </div>                               
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="province">Province<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="province" value="{{ $setting->province }}" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="commune">Commune<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="commune" value="{{ $setting->commune }}" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="zone">Zone<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="zone" value="{{ $setting->zone }}" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="quartier">Quartier<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="quartier" value="{{ $setting->quartier }}" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="rue">Rue<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="rue" value="{{ $setting->rue }} " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="postal_number">Code Postal<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="postal_number" value="{{ $setting->postal_number }}" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="telephone1">Telephone 1<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="telephone1" value="{{$setting->telephone1}} " required minlength="6" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="telephone2">Telephone 2<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="telephone2" value="{{$setting->telephone2}} " " required minlength="6" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="email">Email<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="email" value="{{$setting->email}} " required minlength="5" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="logo">Logo<span class="text-danger"></span></label>
                                        <input type="file" class="form-control" name="logo">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="developpeur">Developpeur<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="developpeur" value="{{$setting->developpeur}} ">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="max_line">Nbre des lignes<span class="text-danger">*</span></label>
                                        <input autofocus type="number" min="1" class="form-control" name="max_line" value="{{$setting->max_line}}">
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
