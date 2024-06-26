
@extends('backend.layouts.master')

@section('title')
@lang('material supplier orders') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('material supplier orders')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.material-supplier-orders.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('material supplier orders')</span></li>
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
                    
                    <form action="{{ route('admin.material-supplier-orders.store') }}" method="POST">
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
                                <label for="purchase_no">Purchase Request No</label>
                                <input type="text" class="form-control" id="purchase_no" name="purchase_no" value="{{$purchase_no}}" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
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
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.unit')</th>
                                <th>@lang('messages.unit_price')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <select class="form-control" name="material_id[]" id="material_id">
                                <option value="{{ $data->material_id }}" class="form-control">{{ $data->material->name }}/{{ $data->material->code }}</option>
                                </select></td>  
                                <td><input type="number" name="quantity[]" value="{{ $data->quantity }}" class="form-control" min="{{ $data->quantity }}" max="{{ $data->quantity }}" /></td>  
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
                                <td><input type="number" name="purchase_price[]" value="{{$data->price}}" class="form-control" step="any" min="{{$data->price}}"/></td>
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
                                COMMANDE DES ARTICLES CHEZ LE FOURNISSEUR
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
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<select class='form-control' name='unit[]' id='unit'>"+
                                "<option disabled='disabled' selected='selected'>Merci de choisir</option>"+
                                "<option value='pieces' class='form-control'>Pieces</option>"+
                                "</select>"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='purchase_price[]' placeholder='Enter Unit price' class='form-control' step='any' min='0' />"+
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
