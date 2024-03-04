
@extends('backend.layouts.master')

@section('title')
@lang('messages.inventory') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.inventory')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.drink-big-store-inventory.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.inventory')</span></li>
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
                    <h4 class="header-title">@lang('messages.inventory')</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.drink-small-store-inventory.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="code_store" value="{{ $code }}">
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="hidden" class="form-control" name="bon_no">
                            <div class="form-group">
                                <label for="date">@lang('messages.date')</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                            </div>
                            <div class="col-sm-6">
                            <div class="form-group">
                                <label for="title">@lang('messages.title')</label>
                                <input type="text" class="form-control" id="title" name="title" value="INVENTAIRE AU PETIT STOCK DES BOISSONS DU {{ date('d') }} ,{{date('M')}} {{ date('Y')}}">
                            </div>
                        </div>

                         <table class="table table-bordered" id="dynamicTable">  
                            <tr class="bg-secondary">
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.purchase_price')</th>
                                <th>@lang('messages.selling_price')</th>
                                <th>@lang('messages.new_quantity')</th>
                                <th>@lang('messages.purchase_price')</th>
                                <th>@lang('messages.selling_price')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr class="">
                                <input type="hidden" name="unit[]" value="{{ $data->unit }}"> 
                                <input type="hidden" name="new_unit[]" value="{{ $data->unit }}"> 
                                <input type="hidden" name="selling_price_ml[]" value="0"> 
                                <td> <select class="form-control" name="drink_id[]" id="drink_id">
                                <option value="{{ $data->drink_id }}" selected="selected" class="form-control">{{ $data->drink->name }}/{{ $data->drink->code }}</option>
                                </select></td>  
                                <td><input type="text" name="quantity[]" value="{{$data->quantity_bottle }}" class="form-control" readonly="readonly" /></td>  
                                <input type="hidden" name="quantity_ml[]" value="0" class="form-control" readonly="readonly" />
                                <td><input type="text" name="purchase_price[]" value="{{ $data->purchase_price }}" class="form-control" readonly="readonly"/></td>  
                                <td><input type="text" name="selling_price[]" value="{{ $data->selling_price }}" class="form-control" readonly="readonly"/></td> 
                                <td><input type="number" name="new_quantity[]" value="{{ $data->quantity_bottle }}" class="form-control" step="any" min="0" /></td>
                                <input type="hidden" name="new_quantity_ml[]" value="{{ $data->quantity_ml }}" class="form-control" step="any" min="0" />
                                <input type="hidden" name="new_selling_price_ml[]" value="{{ $data->selling_price_ml }}" class="form-control" step="any" min="0" />
                                <td><input type="number" name="new_purchase_price[]" value="{{ $data->purchase_price }}" class="form-control" step="any" min="0" /></td> 
                                <td><input type="number" name="new_selling_price[]" value="{{ $data->selling_price }}" class="form-control" step="any" min="0" /></td>
                                <td><button type="button" class="btn btn-danger remove-tr"><i class="fa fa-trash-o" aria-hidden='false'></i>&nbsp;Supprimer</button></td>   
                            </tr> 
                            @endforeach 
                        </table> 
                        <button type="button" name="add" id="add" class="btn btn-primary">@lang('messages.addmore')</button>
                        <div class="col-lg-12">
                            <label for="description"> @lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description">
                              INVENTAIRE AU PETIT STOCK DES BOISSONS DU {{ date('d') }} ,{{date('M')}} {{ date('Y')}}
                            </textarea>
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
    var i = 0;
       
    $("#add").click(function(){
   
        ++i;

         var markup = "<tr class=''>"+
                      "<td>"+
                         "<select class='form-control' name='drink_id[]'"+
                            "<option value='0'>merci de choisir</option>"+
                            "@foreach($drinks as $drink)"+
                                 "<option value='{{$drink->id}}'>{{$drink->name}}</option>"+
                            "@endforeach"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Quantity' class='form-control' min='0' />"+
                        "</td>"+
                        "<td>"+
                          "<select class='form-control' name='unit[]'"+
                            "<option value='0'>Select unit</option>"+
                                 "<option value=''>unit</option>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='text' name='purchase_price[]' class='form-control' min='0' />"+
                        "</td>"+
                        "<td>"+
                            "<input type='text' name='selling_price[]' class='form-control' min='0' />"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='new_quantity[]' placeholder='new quantity' class='form-control' min='0' />"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='new_purchase_price[]' placeholder='new purchase price' class='form-control' min='0' />"+
                        "</td>"+
                        "<input type='number' name='new_selling_price[]' placeholder='new selling price' class='form-control' min='0' />"+
                        "</td>"+
                        "<td>"+
                          "<button type='button' class='btn btn-danger remove-tr'><i class='fa fa-trash-o' aria-hidden='false'></i>&nbsp;Supprimer</button>"+
                        "</td>"+
                    "</tr>";
   
        $("#dynamicTable").append(markup);
    });
   
    $(document).on('click', '.remove-tr', function(){  
         $(this).parents('tr').remove();
    }); 


</script>
@endsection