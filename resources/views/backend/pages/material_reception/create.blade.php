
@extends('backend.layouts.master')

@section('title')
@lang('messages.reception') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.reception')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.material-receptions.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.reception')</span></li>
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
                    <h4 class="header-title">@lang('messages.new')</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.material-receptions.store') }}" method="POST">
                        @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">@lang('messages.date')</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="order_no">Order No</label>
                                <input type="text" class="form-control" id="order_no" name="order_no" value="{{$order_no}}" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                                <label for="invoice_currency">Type de Monaie</label>
                                <div class="form-group">
                                    <label class="text">BIF
                                    <input type="checkbox" name="invoice_currency" value="BIF" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">USD
                                    <input type="checkbox" name="invoice_currency" value="USD" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">EUR
                                    <input type="checkbox" name="invoice_currency" value="EUR" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label for="supplier_id">@lang('Fournisseur')</label>
                                <select class="form-control" name="supplier_id" id="supplier_id">
                                 <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                @endforeach
                             </select>
                            </div>
                        </div>
                            <div class="col-md-4">
                                <label for="vat_supplier_payer">Fournisseur est assujetti TVA?</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_supplier_payer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_supplier_payer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receptionist">@lang('messages.receptionist')</label>
                                <input type="text" class="form-control" id="receptionist" name="receptionist" placeholder="Enter receptionist" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="handingover">@lang('messages.handingover')</label>
                                <input type="text" class="form-control" id="handingover" name="handingover" placeholder="Enter handingover" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_no">@lang('messages.invoice_number')</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" placeholder="Enter invoice_no" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                                <div class="form-group">
                                    <label for="store_type">@lang('Type Stock')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="store_type" id="store_type">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="0" class="form-control">GRAND STOCK</option>
                                        <option value="1" class="form-control">STOCK INTERMEDIAIRE</option>
                                    </select>
                                </div>
                        </div>
                        <div class="col-md-6" id="dynamic_big_store">
                                
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('Ordered quantity')</th>
                                <th>@lang('messages.unit')</th>
                                <th>@lang('messages.unit_price')</th>
                                <th>@lang('Reception Quantity')</th>
                                <th>@lang('unit')</th>
                                <th>@lang('Purchase Price')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <select class="form-control" name="material_id[]" id="material_id">
                                <option value="{{ $data->material_id }}" class="form-control">{{ $data->material->name }}/{{ $data->material->code }}</option>
                                </select></td>  
                                <td><input type="number" name="quantity_ordered[]" value="{{ $data->quantity }}" step="any" class="form-control"  readonly /></td>  
                                <td><input type="text" name="unit[]" value="{{$data->unit}}" class="form-control"  readonly /></td>
                                <td><input type="number" name="" value="{{$data->purchase_price}}" class="form-control" step="any" min="0" readonly /></td>
                                <td><input type="number" name="quantity_received[]" value="{{ $data->quantity }}" step="any" class="form-control" min="0" /></td> 
                                <td>
                                    <select class="form-control" name="unit[]" id="unit">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="pcs" {{ $data->unit == 'pcs' ? 'selected' : '' }} class="form-control">Pieces</option>
                                        <option value="boites" {{ $data->unit == 'boites' ? 'selected' : '' }} class="form-control">Boites</option>
                                        <option value="kg" {{ $data->unit == 'kg' ? 'selected' : '' }} class="form-control">KG</option>
                                        <option value="paire" {{ $data->unit == 'paire' ? 'selected' : '' }} class="form-control">PAIRE</option>
                                        <option value="litres" {{ $data->unit == 'litres' ? 'selected' : '' }} class="form-control">Litres</option>
                                    </select>
                                </td>
                                <td><input type="number" name="purchase_price[]" value="{{$data->purchase_price}}" class="form-control" step="any" min="0"/></td>
                                <td>
                                <button type='button' class='btn btn-danger remove-tr'>@lang('messages.delete')</button></td>  
                            </tr> 
                            @endforeach 
                        </table>
                        <!--
                        <button type="button" name="add" id="add" class="btn btn-primary">@lang('messages.addmore')</button> 
                    -->
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Desccription">
                                MOI {{ Auth::guard('admin')->user()->name }},J'ACCUSE LA RECEPTION DE CES ARTICLES
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
                         "<select class='form-control' name='material_id[]'"+
                            "<option value='0'>Merci de choisir</option>"+
                             "@foreach($materials as $material)"+
                                 "<option value='{{ $material->id }}'>{{ $material->name }}/{{ $material->code }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' step='any' min='0'/>"+
                        "</td>"+
                        "<td>"+
                          "<select class='form-control' name='unit[]' id='unit'>"+
                                "<option disabled='disabled' selected='selected'>Merci de choisir</option>"+
                                "<option value='pieces' class='form-control'>Pieces</option>"+
                                "</select>"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='unit_price[]' placeholder='Enter Unit price' class='form-control' step='any' min='0' />"+
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


    //one checked box in checkbox group of invoice_currency

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('invoice_currency'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('invoice_currency'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of vat_supplier_payer

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('vat_supplier_payer'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('vat_supplier_payer'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })


    $('#store_type').change(function () { 
    if ($(this).val() === '0'){

        var extra_big_store = "<div class='form-group'>"+
                            "<label for='destination_extra_store_id'>GRAND STOCK<strong style='color: red;'>*</strong></label>"+
                            "<select name='destination_extra_store_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($destination_big_stores as $destination_big_store)"+
                                "<option value='{{$destination_big_store->id}}'>{{ $destination_big_store->code}}&nbsp;{{ $destination_big_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(extra_big_store);
    }
    if ($(this).val() === '1'){

        var big_store = "<div class='form-group'>"+
                            "<label for='destination_store_id'>STOCK INTERMEDIAIRE<strong style='color: red;'>*</strong></label>"+
                            "<select name='destination_store_id' class='form-control'>"+
                                "<option selected disabled>merci de choisir</option>"+
                                "@foreach($destination_stores as $destination_store)"+
                                "<option value='{{$destination_store->id}}'>{{ $destination_store->code}}&nbsp;{{ $destination_store->name}}"+
                                "@endforeach"
                            +
                        "</div>";
        
        $("#dynamic_big_store").append(big_store);
    }

    })
    .trigger( "change" );

</script>
@endsection
