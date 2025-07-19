
@extends('backend.layouts.master')

@section('title')
@lang('messages.stockout_create') - @lang('messages.admin_panel')
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('messages.stockout_create')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.drink-stockouts.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.stockout_create')</span></li>
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
                    <h4 class="header-title">Nouvelle Sortie</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.drink-stockouts.store') }}" method="POST">
                        @csrf
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                            <div class="form-group">
                             <label for="date">@lang('messages.date')</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asker">Demandeur</label>
                                <input type="text" class="form-control" id="asker" name="asker" placeholder="Enter asker">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="store_type">@lang('Store Type')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="store_type" id="store_type">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @if (Auth::guard('admin')->user()->can('drink_extra_big_inventory.view'))
                                        <option value="0" class="form-control">Drink Extra Big Store</option>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_big_inventory.view'))
                                        <option value="1" class="form-control">Drink Big Store</option>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_small_inventory.view'))
                                        <option value="2" class="form-control">Drink Small Store</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label for="destination">Destination</label>
                                <input type="text" class="form-control" id="destination" name="destination" placeholder="Enter destination">
                            </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_movement_type">@lang('Type de Sortie')</label>
                                    <select class="form-control" name="item_movement_type" id="item_movement_type" required>
                                    <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="SN">Sortie Normale</option>
                                        @if (Auth::guard('admin')->user()->can('drink_stockout.edit'))
                                        <option value="SP">Sortie Perte</option>
                                        <option value="SV">Sortie Vol</option>
                                        <option value="SD">Sortie Désuétude</option>
                                        <option value="SC">Sortie Casse</option>
                                        <option value="SAJ">Sortie Ajustement</option>
                                        @endif
                                        <option value="ST">Sortie Transfert</option>
                                        <option value="SAU">Sortie Autre</option>
                                </select>
                                </div>
                            </div>
                    </div>
                        <div class="row">
                            <div class="col-md-6" id="dynamic_big_store">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" id="dynamic_small_store">
                                
                            </div>
                        </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>Action</th>
                            </tr>
                            <tr>  
                                <td> <select class="form-control" name="drink_id[]" id="drink_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                            @foreach ($drinks as $drink)
                                <option value="{{ $drink->id }}" class="form-control">{{$drink->name}}/{{ number_format($drink->cump,0,',',' ') }}/{{ $drink->drinkMeasurement->purchase_unit }}</option>
                            @endforeach
                            </select></td>  
                                <td><input type="number" name="quantity[]" placeholder="Enter quantity" class="form-control" /></td>    
                                <td><button type="button" name="add" id="add" class="btn btn-success">@lang('messages.addmore')</button></td>  
                            </tr>  
                        </table> 
                        <div class="col-lg-12">
                            <label for="description"> @lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Entrer description">
                                SORTIE DES ARTICLES
                            </textarea>
                        </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary">@lang('messages.save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
    var i = 0;
       
    $("#add").click(function(){
   
        ++i;

         var markup = "<tr>"+
                      "<td>"+
                         "<select class='form-control' name='drink_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($drinks as $drink)"+
                                 "<option value='{{ $drink->id }}'>{{$drink->name}}/{{ number_format($drink->cump,0,',',' ') }}/{{ $drink->drinkMeasurement->purchase_unit }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<button type='button' class='btn btn-danger remove-tr'>@lang('messages.delete')</button>"+
                        "</td>"+
                    "</tr>";
   
        $("#dynamicTable").append(markup);
    });
   
    $(document).on('click', '.remove-tr', function(){  
         $(this).parents('tr').remove();
    }); 


    $('#store_type').change(function () { 
    if ($(this).val() === '0'){

        var extra_big_store = "<div class='form-group'>"+
                            "<label for='origin_extra_store_id'>Drink Extra Big Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='origin_extra_store_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($drink_extra_big_stores as $drink_big_store)"+
                                "<option value='{{$drink_big_store->id}}'>{{ $drink_big_store->code}}&nbsp;{{ $drink_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(extra_big_store);
        $("#dynamic_small_store").hide();
    }
    if ($(this).val() === '1'){

        var big_store = "<div class='form-group'>"+
                            "<label for='origin_bg_store_id'>Drink Big Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='origin_bg_store_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($drink_big_stores as $drink_big_store)"+
                                "<option value='{{$drink_big_store->id}}'>{{ $drink_big_store->code}}&nbsp;{{ $drink_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(big_store);
        $("#dynamic_small_store").hide();
    }
    if ($(this).val() === '2'){

        var small_store = "<div class='form-group'>"+
                            "<label for='origin_sm_store_id'>Drink Small Store<strong style='color: red;'>*</strong></label>"+
                            "<select name='origin_sm_store_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($drink_small_stores as $drink_small_store)"+
                                "<option value='{{$drink_small_store->id}}'>{{ $drink_small_store->code }}&nbsp;{{ $drink_small_store->name}}</option>"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_small_store").append(small_store);
        $("#dynamic_big_store").hide();
    }

    })
    .trigger( "change" );

    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    window.onunload = function () {
        null
    };

</script>
@endsection
