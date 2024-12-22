
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
                    <li><a href="{{ route('admin.drinks.index') }}">@lang('messages.list')</a></li>
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
                <div class="card-body">
                    <h4 class="header-title">@lang('messages.edit')</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.drinks.update',$drink->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('messages.item')<strong style="color: red;">*</strong></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $drink->name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="drink_measurement_id">@lang('messages.unit')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="drink_measurement_id" id="drink_measurement_id">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($drink_measurements as $drink_measurement)
                                        <option value="{{ $drink_measurement->id }}" {{ $drink->drink_measurement_id == $drink_measurement->id ? 'selected' : '' }} class="form-control">{{ $drink_measurement->purchase_unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity_bottle">@lang('messages.quantity')</label>
                                    <input type="number" class="form-control" id="quantity_bottle" name="quantity_bottle" value="{{ $drink->quantity_bottle }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="threshold_quantity">@lang('messages.threshold_quantity')</label>
                                    <input type="number" class="form-control" id="threshold_quantity" name="threshold_quantity" value="{{ $drink->threshold_quantity }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_price">@lang('messages.purchase_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" value="{{ $drink->cump }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">@lang('messages.selling_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="selling_price" name="selling_price" value="{{ $drink->selling_price }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dcategory_id">@lang('messages.category')</label>
                                    <select class="form-control" name="dcategory_id" id="dcategory_id">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $drink->dcategory_id == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                                @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="store_type">@lang('Store Type')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="store_type" id="store_type">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $drink->store_type == '0' ? 'selected' : '' }} class="form-control">Drink Big Store</option>
                                        <option value="1" {{ $drink->store_type == '1' ? 'selected' : '' }} class="form-control">Drink Medium Store</option>
                                        <option value="2" {{ $drink->store_type == '2' ? 'selected' : '' }} class="form-control">Drink Small Store</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="brarudi_price">@lang('Prix BRARUDI')</label>
                                    <input type="number" class="form-control" id="brarudi_price" name="brarudi_price" value="{{ $drink->brarudi_price }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vat">@lang('Taux TVA')</label>
                                    <select class="form-control" name="vat" id="vat" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $drink->vat == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="10" {{ $drink->vat == 10 ? 'selected' : '' }} class="form-control">10%</option>
                                        <option value="18" {{ $drink->vat == 18 ? 'selected' : '' }} class="form-control">18%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="dynamic_big_store">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_majoration">@lang('Taux Majoration')</label>
                                    <select class="form-control" name="taux_majoration" id="taux_majoration" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $drink->taux_majoration == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="25" {{ $drink->taux_majoration == 25 ? 'selected' : '' }} class="form-control">25%</option>
                                        <option value="50" {{ $drink->taux_majoration == 50 ? 'selected' : '' }} class="form-control">50%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_reduction">@lang('Taux Reduction')</label>
                                    <select class="form-control" name="taux_reduction" id="taux_reduction" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $drink->taux_reduction == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="10" {{ $drink->taux_reduction == 10 ? 'selected' : '' }} class="form-control">10%</option>
                                        <option value="25" {{ $drink->taux_reduction == 25 ? 'selected' : '' }} class="form-control">25%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_marge">@lang('Taux Marge')</label>
                                    <select class="form-control" name="taux_marge" id="taux_marge" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $drink->taux_marge == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="30" {{ $drink->taux_marge == 30 ? 'selected' : '' }} class="form-control">30%</option>
                                        <option value="50" {{ $drink->taux_marge == 50 ? 'selected' : '' }} class="form-control">50%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" id="dynamic_extra_big_store">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" id="dynamic_small_store">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_ct">@lang('Taxe de Consommation')</label>
                                    <input type="number" class="form-control" id="item_ct" name="item_ct" placeholder="Entrer la Taxe de Consommation" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_tl">@lang('Prelevement Forfaitaire Liberatoire')</label>
                                    <input type="number" class="form-control" id="item_tl" name="item_tl" placeholder="Entrer le Prelevement Forfaitaire Liberatoire" min="0">
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
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
    
    $('#store_type').change(function () { 
    if ($(this).val() === '0'){

        var extra_big_store = "<div class='form-group'>"+
                            "<label for='code_store'>Drink Big Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($drink_extra_big_stores as $drink_extra_big_store)"+
                                "<option value='{{$drink_extra_big_store->code}}'>{{ $drink_extra_big_store->code}}&nbsp;{{ $drink_extra_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(extra_big_store);
    }
    if ($(this).val() === '1'){

        var big_store = "<div class='form-group'>"+
                            "<label for='code_store'>Drink Medium Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($drink_big_stores as $drink_big_store)"+
                                "<option value='{{$drink_big_store->code}}'>{{ $drink_big_store->code}}&nbsp;{{ $drink_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(big_store);
    }
    if ($(this).val() === '2'){

        var small_store = "<div class='form-group'>"+
                            "<label for='code_store'>Drink Small Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($drink_small_stores as $drink_small_store)"+
                                "<option value='{{$drink_small_store->code}}'>{{ $drink_small_store->code }}&nbsp;{{ $drink_small_store->name}}</option>"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_small_store").append(small_store);
    }

    })
    .trigger( "change" );


</script>
@endsection
