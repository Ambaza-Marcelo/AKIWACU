
@extends('backend.layouts.master')

@section('title')
@lang('messages.edit') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.edit')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.barrist-items.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.edit')</span></li>
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
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body bg-success">
                    <h4 class="header-title">@lang('messages.edit')</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.barrist-items.update',$barrist_item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('messages.item')<strong style="color: red;">*</strong></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $barrist_item->name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit">@lang('messages.unit')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="unit" id="unit">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="bouteilles" {{ $barrist_item->unit == 'bouteilles' ? 'selected' : '' }} class="form-control">Bouteilles</option>
                                        <option value="pcs" {{ $barrist_item->unit == 'pcs' ? 'selected' : '' }} class="form-control">Pcs</option>
                                        <option value="cartons" {{ $barrist_item->unit == 'cartons' ? 'selected' : '' }} class="form-control">Cartons</option>
                                        <option value="millilitres" {{ $barrist_item->unit == 'millilitres' ? 'selected' : '' }} class="form-control">Millilitres</option>
                                        <option value="kg" {{ $barrist_item->unit == 'kg' ? 'selected' : '' }} class="form-control">Kilogrammes</option>
                                        <option value="mg" {{ $barrist_item->unit == 'mg' ? 'selected' : '' }} class="form-control">Milligrammes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">@lang('messages.quantity')</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $barrist_item->quantity }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="threshold_quantity">@lang('messages.threshold_quantity')</label>
                                    <input type="number" class="form-control" id="threshold_quantity" name="threshold_quantity" value="{{ $barrist_item->threshold_quantity }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_price">@lang('messages.purchase_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" value="{{ $barrist_item->purchase_price }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">@lang('messages.selling_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="selling_price" name="selling_price" value="{{ $barrist_item->selling_price }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vat">@lang('Taux TVA')</label>
                                    <select class="form-control" name="vat" id="vat" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $barrist_item->vat == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="10" {{ $barrist_item->vat == 0 ? 'selected' : '' }} class="form-control">10%</option>
                                        <option value="18" {{ $barrist_item->vat == 0 ? 'selected' : '' }} class="form-control">18%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_majoration">@lang('Taux Majoration')</label>
                                    <select class="form-control" name="taux_majoration" id="taux_majoration" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $barrist_item->taux_majoration == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="25" {{ $barrist_item->taux_majoration == 25 ? 'selected' : '' }} class="form-control">25%</option>
                                        <option value="50" {{ $barrist_item->taux_majoration == 50 ? 'selected' : '' }} class="form-control">50%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_reduction">@lang('Taux Reduction')</label>
                                    <select class="form-control" name="taux_reduction" id="taux_reduction" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $barrist_item->taux_reduction == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="10" {{ $barrist_item->taux_reduction == 10 ? 'selected' : '' }} class="form-control">10%</option>
                                        <option value="25" {{ $barrist_item->taux_reduction == 25 ? 'selected' : '' }} class="form-control">25%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_marge">@lang('Taux Marge')</label>
                                    <select class="form-control" name="taux_marge" id="taux_marge" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $barrist_item->taux_marge == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="30" {{ $barrist_item->taux_marge == 30 ? 'selected' : '' }} class="form-control">30%</option>
                                        <option value="50" {{ $barrist_item->taux_marge == 50 ? 'selected' : '' }} class="form-control">50%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">@lang('messages.update')</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
