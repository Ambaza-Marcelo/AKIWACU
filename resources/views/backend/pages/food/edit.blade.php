
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
                    <li><a href="{{ route('admin.foods.index') }}">@lang('messages.list')</a></li>
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
                    
                    <form action="{{ route('admin.foods.update',$food->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('messages.item')<strong style="color: red;">*</strong></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $food->name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit">@lang('messages.unit')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="unit" id="unit">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="pieces" {{ $food->unit == 'pieces' ? 'selected' : '' }} class="form-control">Pieces</option>
                                        <option value="portions" {{ $food->unit == 'portions' ? 'selected' : '' }} class="form-control">Portions</option>
                                        <option value="kg" {{ $food->unit == 'kg' ? 'selected' : '' }} class="form-control">Kilogrammes</option>
                                        <option value="mg" {{ $food->unit == 'mg' ? 'selected' : '' }} class="form-control">Milligrammes</option>
                                        <option value="ml" {{ $food->unit == 'ml' ? 'selected' : '' }} class="form-control">MilliLitres</option>
                                        <option value="litres" {{ $food->unit == 'litres' ? 'selected' : '' }} class="form-control">Litres</option>
                                        <option value="paquets" {{ $food->unit == 'paquets' ? 'selected' : '' }} class="form-control">Paquets</option>
                                        <option value="botts" {{ $food->unit == 'botts' ? 'selected' : '' }} class="form-control">Botts</option>
                                        <option value="grammes" {{ $food->unit == 'grammes' ? 'selected' : '' }} class="form-control">Grammes</option>
                                        <option value="bidons" {{ $food->unit == 'bidons' ? 'selected' : '' }} class="form-control">Bidons</option>
                                        <option value="rouleau" {{ $food->unit == 'rouleau' ? 'selected' : '' }} class="form-control">Rouleau</option>
                                        <option value="bouteilles" {{ $food->unit == 'bouteilles' ? 'selected' : '' }} class="form-control">Bouteilles</option>
                                        <option value="sachets" {{ $food->unit == 'sachets' ? 'selected' : '' }} class="form-control">Sachets</option>
                                        <option value="boites" {{ $food->unit == 'boites' ? 'selected' : '' }} class="form-control">Boites</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">@lang('messages.quantity')</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $food->quantity }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="threshold_quantity">@lang('messages.threshold_quantity')</label>
                                    <input type="number" class="form-control" id="threshold_quantity" name="threshold_quantity" value="{{ $food->threshold_quantity }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_price">@lang('messages.purchase_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" value="{{ $food->purchase_price }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">@lang('messages.selling_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="selling_price" name="selling_price" value="{{ $food->selling_price }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fcategory_id">@lang('messages.category')</fabel>
                                    <select class="form-control" name="fcategory_id" id="dcategory_id">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $food->fcategory_id == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                                @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="store_type">@lang('Store Type')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="store_type" id="store_type">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" class="form-control">Food Extra Big Store</option>
                                        <option value="1" class="form-control">Food Big Store</option>
                                        <option value="2" class="form-control">Food Small Store</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="specification">@lang('messages.specification')</label>
                                    <input type="text" class="form-control" id="specification" name="specification" placeholder="Enter Specification">
                                </div>
                            </div>
                            <div class="col-md-6" id="dynamic_big_store">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" id="dynamic_small_store">
                                
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
                            "<label for='code_store'>Food Extra Big Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($food_extra_big_stores as $food_big_store)"+
                                "<option value='{{$food_big_store->code}}'>{{ $food_big_store->code}}&nbsp;{{ $food_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(extra_big_store);
        $("#dynamic_small_store").hide();
    }
    if ($(this).val() === '1'){

        var big_store = "<div class='form-group'>"+
                            "<label for='code_store'>Food Big Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($food_big_stores as $food_big_store)"+
                                "<option value='{{$food_big_store->code}}'>{{ $food_big_store->code}}&nbsp;{{ $food_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(big_store);
        $("#dynamic_small_store").hide();
    }
    if ($(this).val() === '2'){

        var small_store = "<div class='form-group'>"+
                            "<label for='code_store'>Food Small Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($food_small_stores as $food_small_store)"+
                                "<option value='{{$food_small_store->code}}'>{{ $food_small_store->code }}&nbsp;{{ $food_small_store->name}}</option>"+
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
