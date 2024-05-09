
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
                    <li><a href="{{ route('admin.materials.index') }}">@lang('messages.list')</a></li>
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
                    
                    <form action="{{ route('admin.materials.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('messages.item')<strong style="color: red;">*</strong></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit">@lang('messages.unit')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="unit" id="unit">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="pcs" class="form-control">Pieces</option>
                                        <option value="paire" class="form-control">PAIRE</option>
                                        <option value="litres" class="form-control">Litres</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">@lang('messages.quantity')</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter Quantity" min="0">
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
                                    <select class="form-control" name="store_type" id="store_type">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" class="form-control">Material Extra Big Store</option>
                                        <option value="1" class="form-control">Material Big Store</option>
                                        <option value="2" class="form-control">Material Small Store</option>
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
                            "<label for='code_store'>Material Extra Big Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($material_extra_big_stores as $material_big_store)"+
                                "<option value='{{$material_big_store->code}}'>{{ $material_big_store->code}}&nbsp;{{ $material_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(extra_big_store);
        $("#dynamic_small_store").hide();
    } 
    if ($(this).val() === '1'){

        var big_store = "<div class='form-group'>"+
                            "<label for='code_store'>Material Big Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($material_big_stores as $material_big_store)"+
                                "<option value='{{$material_big_store->code}}'>{{ $material_big_store->code}}&nbsp;{{ $material_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(big_store);
        $("#dynamic_small_store").hide();
    }
    if ($(this).val() === '2'){

        var small_store = "<div class='form-group'>"+
                            "<label for='code_store'>Material Small Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='code_store' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($material_small_stores as $material_small_store)"+
                                "<option value='{{$material_small_store->code}}'>{{ $material_small_store->code }}&nbsp;{{ $material_small_store->name}}</option>"+
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
