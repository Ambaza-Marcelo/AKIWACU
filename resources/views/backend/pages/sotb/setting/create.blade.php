
@extends('backend.layouts.master')

@section('title')
@lang('messages.setting_create') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.setting_create')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.settings.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.setting_create')</span></li>
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
                    <h4 class="header-title">Create setting</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.settings.store') }}" method="POST"  enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="name">Nom Entreprise<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="name" placeholder="Entrer Nom " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="nif">NIF<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="nif" placeholder="Entrer NIF" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="rc">Registre Commerce<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="rc" placeholder="Entrer RC" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="commune">Commune<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="commune" placeholder="Entrer Commune " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="zone">Zone<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="zone" placeholder="Entrer Zone" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="quartier">Quartier<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="quartier" placeholder="Entrer Quartier" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="avenue">Avenue<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="avenue" placeholder="Entrer Avenue " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="rue">Rue<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="rue" placeholder="Entrer Rue" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="telephone1">Telephone 1<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="telephone1" placeholder="Entrer telephone 1" required minlength="6" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="telephone2">Telephone 2<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="telephone2" placeholder="Entrer Telephone 2 " required minlength="6" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="email">Email<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="email" placeholder="Entrer Email" required minlength="5" maxlength="255">
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
                                        <input autofocus type="text" class="form-control" name="developpeur" placeholder="Entrer Developpeur">
                                    </div>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
<script type="text/javascript">
    var token = $('input[name = _token]').val();
    function backup() {
        $.ajax({
            type: "POST",
            url: 'admin.system.dbBackup',
            data: {
                _token: token,
            },
            success: function (result) {
              alert("ok")

            },
            error: function (errors) {

               alert("error");
            }
        });
    }
</script>
@endsection