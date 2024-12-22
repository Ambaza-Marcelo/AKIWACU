
@extends('backend.layouts.master')

@section('title')
@lang('messages.transfer') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.transfer')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.barrist-transfers.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.transfer')</span></li>
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
                <div class="card-body bg-success">
                    <h4 class="header-title">@lang('messages.new')</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.barrist-transfer-food.store') }}" method="POST">
                        @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date">@lang('messages.date')</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="requisition_no">Requisition No</label>
                                <input type="text" class="form-control" id="requisition_no" name="requisition_no" value="{{$requisition_no}}" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="origin_fstore_id">@lang('Stock Origine')</label>
                                <select class="form-control" name="origin_fstore_id" id="origin_fstore_id">
                                 <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($origin_stores as $origin_store)
                                    <option value="{{$origin_store->id}}">{{$origin_store->name}}</option>
                                @endforeach
                             </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">@lang('Stock Destination')</label>
                                <select class="form-control" name="" id="">
                                 <option disabled="disabled" selected="selected">BARRIST STORE</option>
                             </select>
                            </div>
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.unit')</th>
                                <th>@lang('messages.unit_price')</th>
                                <th>@lang('Transfering Quantity')</th>
                                <th>@lang('Transfering unit')</th>
                                <th>@lang('Transfering Price')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <select class="form-control" name="food_id[]" id="food_id">
                                <option value="{{ $data->food_id }}" class="form-control">{{ $data->food->name }}/{{ $data->food->code }}</option>
                                </select></td>  
                                <td><input type="number" name="quantity_requisitioned[]" value="{{ $data->quantity_requisitioned }}" class="form-control" step="any"  readonly /></td>  
                                <td><input type="text" name="unit[]" value="{{$data->unit}}" class="form-control"  readonly /></td>
                                <td><input type="number" name="price[]" value="{{$data->price}}" class="form-control" step="any" min="0" readonly /></td>
                                <td><input type="number" name="quantity_transfered[]" value="{{ $data->quantity_requisitioned }}" class="form-control" min="0" step="any" /></td>  
                                <td>
                                    <select class="form-control" name="unit[]" id="unit">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="pieces" {{ $data->unit == 'pieces' ? 'selected' : '' }} class="form-control">Pieces</option>
                                        <option value="portions" {{ $data->unit == 'portions' ? 'selected' : '' }} class="form-control">Portions</option>
                                        <option value="kg" {{ $data->unit == 'kg' ? 'selected' : '' }} class="form-control">Kilogrammes</option>
                                        <option value="mg" {{ $data->unit == 'mg' ? 'selected' : '' }} class="form-control">Milligrammes</option>
                                        <option value="litres" {{ $data->unit == 'litres' ? 'selected' : '' }} class="form-control">Litres</option>
                                        <option value="paquets" {{ $data->unit == 'paquets' ? 'selected' : '' }} class="form-control">Paquets</option>
                                        <option value="botts" {{ $data->unit == 'botts' ? 'selected' : '' }} class="form-control">Botts</option>
                                        <option value="grammes" {{ $data->unit == 'grammes' ? 'selected' : '' }} class="form-control">Grammes</option>
                                        <option value="bidons" {{ $data->unit == 'bidons' ? 'selected' : '' }} class="form-control">Bidons</option>
                                        <option value="rouleau" {{ $data->unit == 'rouleau' ? 'selected' : '' }} class="form-control">Rouleau</option>
                                        <option value="bouteilles" {{ $data->unit == 'bouteilles' ? 'selected' : '' }} class="form-control">Bouteilles</option>
                                        <option value="sachets" {{ $data->unit == 'sachets' ? 'selected' : '' }} class="form-control">Sachets</option>
                                        <option value="boites" {{ $data->unit == 'boites' ? 'selected' : '' }} class="form-control">Boites</option>
                                    </select>
                                </td>
                                <td><input type="hidden" name="price[]" value="{{$data->price}}" class="form-control" step="any" min="{{$data->price}}"/></td>
                                <td><button type='button' class='btn btn-danger remove-tr'>@lang('messages.delete')</button></td>  
                            </tr> 
                            @endforeach 
                        </table>
                        <!--
                        <button type="button" name="add" id="add" class="btn btn-primary">@lang('messages.addmore')</button> 
                    -->
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Desccription">
                                TRANSFERT DES ARTICLES
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
                         "<select class='form-control' name='food_id[]'"+
                            "<option value='0'>Merci de choisir</option>"+
                             "@foreach($foods as $food)"+
                                 "<option value='{{ $food->id }}'>{{ $food->name }}/{{ $food->code }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' min='0' step='any'/>"+
                        "</td>"+
                        "<td>"+
                          "<select class='form-control' name='unit[]' id='unit'>"+
                                "<option disabled='disabled' selected='selected'>Merci de choisir</option>"+
                                "<option value='pieces' class='form-control'>Pieces</option>"+
                                "<option value='portions' class='form-control'>Portions</option>"+
                                "<option value='kg' class='form-control'>Kilogrammes</option>"+
                                "<option value='mg' class='form-control'>Milligrames</option>"+
                                "<option value='litres' class='form-control'>Litres</option>"+
                                "<option value='paquets' class='form-control'>Paquets</option>"+
                                "<option value='botts' class='form-control'>Botts</option>"+
                                "<option value='grammes' class='form-control'>Grammes</option>"+
                                "<option value='bidons' class='form-control'>Bidons</option>"+
                                "<option value='rouleau' class='form-control'>Rouleau</option>"+
                                "<option value='bouteilles' class='form-control'>Bouteilles</option>"+
                                "<option value='sachets' class='form-control'>Sachets</option>"+
                                "<option value='boites' class='form-control'>Boites</option>"+
                                "</select>"+
                        "</td>"+
                        "<td>"+
                        "<input type='hidden' name='unit_price[]' placeholder='Enter Unit price' class='form-control' />"+
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

</script>
@endsection
