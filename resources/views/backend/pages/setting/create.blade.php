
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
                            <div class="col-md-6">
                                <label for="tp_type">Type Contribuable</label>
                                <div class="form-group">
                                    <label class="text">Personne Physique
                                    <input type="checkbox" name="tp_type" value="1" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Société
                                    <input type="checkbox" checked="checked" name="tp_type" value="2" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="vat_taxpayer">Assujetti à la TVA</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_taxpayer" value="0" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_taxpayer" value="1" checked="checked" class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="ct_taxpayer">Assujetti à la taxe de conso.</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="ct_taxpayer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="ct_taxpayer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="tl_taxpayer">Assujetti au PF</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="tl_taxpayer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="tl_taxpayer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
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
                                    <label for="tp_fiscal_center">Centre Fiscale</label>
                                    <div class="form-group">
                                    <label class="text">DGC
                                        <input type="checkbox" name="tp_fiscal_center" value="DGC" class="form-control">
                                        </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label class="text">DMC
                                        <input type="checkbox" checked="checked" name="tp_fiscal_center" value="DMC" class="form-control">
                                        </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label class="text">DPMC
                                        <input type="checkbox" name="tp_fiscal_center" value="DPMC" class="form-control">
                                    </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="tp_activity_sector">Secteur Activite<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="tp_activity_sector" placeholder="Entrer Secteur Activite " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="tp_legal_form">Forme Juridique<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="tp_legal_form" placeholder="Entrer la forme Juridique" required minlength="2" maxlength="255">
                                    </div>
                                </div>                               
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="province">Province<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="province" placeholder="Entrer Province" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="commune">Commune<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="commune" placeholder="Entrer Commune " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="zone">Zone<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="zone" placeholder="Entrer Zone " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="quartier">Quartier<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="quartier" placeholder="Entrer Quartier" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="rue">Rue<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="rue" placeholder="Entrer Rue " required minlength="2" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="postal_number">Code Postal<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="postal_number" placeholder="Entrer code Postal" required minlength="2" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="telephone1">Telephone 1<span class="text-danger"></span></label>
                                        <input autofocus type="tel" class="form-control" name="telephone1" placeholder="Entrer telephone 1" required minlength="6" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="telephone2">Telephone 2<span class="text-danger"></span></label>
                                        <input autofocus type="tel" class="form-control" name="telephone2" placeholder="Entrer Telephone 2 " required minlength="6" maxlength="15">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="email">Email<span class="text-danger"></span></label>
                                        <input autofocus type="mail" class="form-control" name="email" placeholder="Entrer Email" required minlength="5" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="logo">Logo<span class="text-danger"></span></label>
                                        <input type="file" class="form-control" name="logo" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="developpeur">Developpeur<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="developpeur" placeholder="Entrer Developpeur">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="max_line">Nbre des lignes<span class="text-danger">*</span></label>
                                        <input autofocus type="number" min="1" class="form-control" name="max_line" placeholder="Entrer Nbre des lignes">
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
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
    //one checked box in checkbox group of tp_type

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('tp_type'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('tp_type'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of assujeti a la taxe de consommation

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('ct_taxpayer'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('ct_taxpayer'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of assujeti au PF

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('tl_taxpayer'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('tl_taxpayer'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of tp_fiscal_center

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('tp_fiscal_center'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('tp_fiscal_center'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of vat_taxpayer

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('vat_taxpayer'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('vat_taxpayer'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })


</script>
@endsection