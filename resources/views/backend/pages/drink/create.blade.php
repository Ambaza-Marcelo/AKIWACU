
@extends('backend.layouts.master')

@section('title')
@lang('messages.article_create') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.article_create')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.drinks.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.article_create')</span></li>
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
                    <h4 class="header-title">@lang('messages.article_create')</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.drinks.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('messages.item')<strong style="color: red;">*</strong></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="drink_measurement_id">@lang('messages.unit')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="drink_measurement_id" id="drink_measurement_id" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($drink_measurements as $drink_measurement)
                                        <option value="{{ $drink_measurement->id }}" class="form-control">{{ $drink_measurement->purchase_unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity_bottle">@lang('messages.quantity')</label>
                                    <input type="number" class="form-control" id="quantity_bottle" name="quantity_bottle" placeholder="Enter Quantity" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="threshold_quantity">@lang('messages.threshold_quantity')</label>
                                    <input type="number" class="form-control" id="threshold_quantity" name="threshold_quantity" placeholder="Enter threshold quantity" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_price">@lang('messages.purchase_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" placeholder="Enter purchase Price" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">@lang('messages.selling_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="selling_price" name="selling_price" placeholder="Enter selling price" min="0">
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
                                        <option value="{{ $category->id }}">{{$category->name}}</option>
                                @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="store_type">@lang('Store Type')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="store_type" id="store_type" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" class="form-control">Drink Big Store</option>
                                        <option value="1" class="form-control">Drink Medium Store</option>
                                        <option value="2" class="form-control">Drink Small Store</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="brarudi_price">@lang('Prix BRARUDI')</label>
                                    <input type="number" class="form-control" id="brarudi_price" name="brarudi_price" placeholder="Entrer le prix Brarudi" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vat">@lang('Taux TVA')</label>
                                    <select class="form-control" name="vat" id="vat" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" class="form-control">0%</option>
                                        <option value="10" class="form-control">10%</option>
                                        <option value="18" class="form-control">18%</option>
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
                                        <option value="0" class="form-control">0%</option>
                                        <option value="25" class="form-control">25%</option>
                                        <option value="50" class="form-control">50%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_reduction">@lang('Taux Reduction')</label>
                                    <select class="form-control" name="taux_reduction" id="taux_reduction" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" class="form-control">0%</option>
                                        <option value="10" class="form-control">10%</option>
                                        <option value="25" class="form-control">25%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_marge">@lang('Taux Marge')</label>
                                    <select class="form-control" name="taux_marge" id="taux_marge" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" class="form-control">0%</option>
                                        <option value="30" class="form-control">30%</option>
                                        <option value="50" class="form-control">50%</option>
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
                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary mt-4 pr-4 pl-4">@lang('messages.save')</button>
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
        $("#dynamic_small_store").hide();
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
        $("#dynamic_small_store").hide();
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
        $("#dynamic_big_store").hide();
    }

    })
    .trigger( "change" );


</script>
@endsection
