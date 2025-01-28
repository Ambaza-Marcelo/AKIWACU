
@extends('backend.layouts.master')

@section('title')
@lang('Modifier Unité de mesure') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier Unité de mesure')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.food-measurement.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier Unité')</span></li>
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
                    <h4 class="header-title">Modifier Unité de mesure</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.food-measurement.update',$food_measurement->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_unit">Unité d'achat</label>
                                    <input type="text" class="form-control" id="purchase_unit" name="purchase_unit" value="{{ $food_measurement->purchase_unit }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stockout_unit">Unité de sortie</label>
                                    <input type="text" class="form-control" id="stockout_unit" name="stockout_unit" value="{{ $food_measurement->stockout_unit }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="production_unit">Unité de production</label>
                                    <input type="text" class="form-control" id="production_unit" name="production_unit" value="{{ $food_measurement->production_unit }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="equivalent">Valeur équivalente</label>
                                    <input type="number" step="any" class="form-control" id="equivalent" name="equivalent" value="{{ $food_measurement->equivalent }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sub_equivalent">Valeur Sous-équivalente</label>
                                    <input type="number" step="any" class="form-control" id="sub_equivalent" name="sub_equivalent" value="{{ $food_measurement->sub_equivalent }}">
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