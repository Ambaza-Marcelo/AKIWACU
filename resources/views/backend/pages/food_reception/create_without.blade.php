
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
                    <li><a href="{{ route('admin.food-receptions.index') }}">@lang('messages.list')</a></li>
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
                    
                    <form action="{{ route('admin.food-reception-without-order.store') }}" method="POST">
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
                                <label for="purchase_no">Purchase Request No</label>
                                <input type="text" class="form-control" id="purchase_no" name="purchase_no" value="{{$purchase_no}}" readonly="readonly">
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
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_no">@lang('messages.invoice_number')</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" placeholder="Enter invoice number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="destination_store_id">@lang('Stock Destination')</label>
                                <select class="form-control" name="destination_store_id" id="destination_store_id">
                                 <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($destination_stores as $destination_store)
                                    <option value="{{$destination_store->id}}">{{$destination_store->name}}</option>
                                @endforeach
                             </select>
                            </div>
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
                                <th>@lang('Selling Price')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <select class="form-control" name="food_id[]" id="food_id">
                                <option value="{{ $data->food_id }}" class="form-control">{{ $data->food->name }}/{{ $data->food->code }}</option>
                                </select></td>  
                                <td><input type="number" name="quantity_ordered[]" value="{{ $data->quantity }}" class="form-control"  readonly step="any"/></td>  
                                <td><input type="text" name="unit[]" value="{{$data->unit}}" class="form-control"  readonly /></td>
                                <td><input type="number" value="{{$data->price}}" class="form-control" step="any" min="0" readonly /></td>
                                <td><input type="number" name="quantity_received[]" value="{{ $data->quantity }}" class="form-control" step="any" min="0" /></td> 
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
                                <td><input type="number" name="purchase_price[]" value="{{$data->price}}" class="form-control" step="any" min="0"/></td>
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
                         "<select class='form-control' name='food_id[]'"+
                            "<option value='0'>Merci de choisir</option>"+
                             "@foreach($foods as $food)"+
                                 "<option value='{{ $food->id }}'>{{ $food->name }}/{{ $food->code }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<select class='form-control' name='unit[]' id='unit'>"+
                                "<option disabled='disabled' selected='selected'>Merci de choisir</option>"+
                                "<option value='pieces' class='form-control'>Pieces</option>"+
                                "</select>"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='unit_price[]' placeholder='Enter Unit price' class='form-control' />"+
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

</script>
@endsection
