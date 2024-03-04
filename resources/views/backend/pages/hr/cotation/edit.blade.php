
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
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="name">Nom Entreprise<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="name" value="{{$setting->name}} " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="nif">NIF<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="nif" value="{{$setting->nif}} " required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="rc">Registre Commerce<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="rc" value="{{$setting->rc}} " required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="commune">Commune<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="commune" value="{{$setting->commune}} " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="zone">Zone<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="zone" value="{{$setting->zone}} " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="quartier">Quartier<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="quartier" value="{{$setting->quartier}} " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="avenue">Avenue<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="avenue" value=" " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="rue">Rue<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="rue" value="{{$setting->rue}} " required minlength="2" maxlength="255">
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
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="logo">Logo<span class="text-danger"></span></label>
                                        <input type="file" class="form-control" name="logo" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="developpeur">Developpeur<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="developpeur" value="{{$setting->developpeur}} ">
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
