
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
                    <li><a href="{{ route('admin.food-items.index') }}">@lang('messages.list')</a></li>
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
                    
                    <form action="{{ route('admin.food-items.update',$food_item->code) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('messages.item')<strong style="color: red;">*</strong></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $food_item->name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit">@lang('messages.unit')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="unit" id="unit">
                                        <option disabled="disabled">Merci de choisir</option>
                                        <option value="pieces" selected="selected" class="form-control">Pieces</option>
                                        <option value="portions" class="form-control">Portions</option>
                                        <option value="kg" class="form-control">Kilogrammes</option>
                                        <option value="mg" class="form-control">Milligrames</option>
                                        <option value="litres" class="form-control">Litres</option>
                                        <option value="paquets" class="form-control">Paquets</option>
                                        <option value="botts" class="form-control">Botts</option>
                                        <option value="grammes" class="form-control">Grammes</option>
                                        <option value="bidons" class="form-control">Bidons</option>
                                        <option value="rouleau" class="form-control">Rouleau</option>
                                        <option value="bouteilles" class="form-control">Bouteilles</option>
                                        <option value="sachets" class="form-control">Sachets</option>
                                        <option value="boites" class="form-control">Boites</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_price">@lang('messages.purchase_price')<strong style="color: red;">*</strong></label>
                                    <input type="hidden" class="form-control" id="purchase_price" name="purchase_price" value="{{ $food_item->purchase_price }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selling_price">@lang('messages.selling_price')<strong style="color: red;">*</strong></label>
                                    <input type="number" class="form-control" id="selling_price" name="selling_price" value="{{ $food_item->selling_price }}" min="0" @if(Auth::guard('admin')->user()->can('invoice_drink.edit')) @else readonly @endif>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fcategory_id">@lang('messages.category')</label>
                                    <select class="form-control" name="fcategory_id" id="fcategory_id">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $food_item->fcategory_id == $category->id ? 'selected' : '' }}>{{$category->name}}</option>
                                @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="specification">@lang('messages.specification')</label>
                                    <input type="text" class="form-control" id="specification" name="specification" value="{{ $food_item->specification }}" @if(Auth::guard('admin')->user()->can('invoice_drink.edit')) @else readonly @endif>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vat">@lang('Taux TVA')</label>
                                    <select class="form-control" name="vat" id="vat" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $food_item->vat == '0' ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="10" {{ $food_item->vat == '10' ? 'selected' : '' }} class="form-control">10%</option>
                                        <option value="18" {{ $food_item->vat == '18' ? 'selected' : '' }} class="form-control">18%</option>
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
                                        <option value="0" {{ $food_item->taux_majoration == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="25" {{ $food_item->taux_majoration == 25 ? 'selected' : '' }} class="form-control">25%</option>
                                        <option value="50" {{ $food_item->taux_majoration == 50 ? 'selected' : '' }} class="form-control">50%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_reduction">@lang('Taux Reduction')</label>
                                    <select class="form-control" name="taux_reduction" id="taux_reduction" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $food_item->taux_reduction == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="10" {{ $food_item->taux_reduction == 10 ? 'selected' : '' }} class="form-control">10%</option>
                                        <option value="25" {{ $food_item->taux_reduction == 25 ? 'selected' : '' }} class="form-control">25%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_marge">@lang('Taux Marge')</label>
                                    <select class="form-control" name="taux_marge" id="taux_marge" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" {{ $food_item->taux_marge == 0 ? 'selected' : '' }} class="form-control">0%</option>
                                        <option value="30" {{ $food_item->taux_marge == 30 ? 'selected' : '' }} class="form-control">30%</option>
                                        <option value="50" {{ $food_item->taux_marge == 50 ? 'selected' : '' }} class="form-control">50%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <select class="form-control" name="food_id[]" id="food_id" required>
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach ($foods as $food)
                                <option value="{{ $food->id }}" {{ $data->food_id == $food->id ? 'selected' : '' }} class="form-control">{{ $food->name }}</option>
                                @endforeach
                                </select>
                                </td>  
                                <td>
                                    <input type='number' name='quantity[]' step='any' min='0' step='any' value='{{ $data->quantity }}' class='form-control' />
                                </td> 
                                <td><button type='button' class='btn btn-danger remove-tr'>@lang('messages.delete')</button></td>   
                            </tr> 
                            @endforeach 
                        </table>
                        <button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" title="Ajouter Plus" aria-hidden="false"></i></button>
                        <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
    var i = 0;
       
    $("#add").click(function(){
   
        ++i;

         var markup = "<tr>"+
                      "<td>"+
                         "<select class='form-control' name='food_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($foods as $food)"+
                                 "<option value='{{ $food->id }}'>{{ $food->name }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' step='any' min='0' step='any' placeholder='Enter Quantity' class='form-control' />"+
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

    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    window.onunload = function () {
        null
    };

</script>
@endsection