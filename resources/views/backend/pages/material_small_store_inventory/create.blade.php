
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
                    <li><a href="{{ route('admin.material-small-store-inventory.index') }}">@lang('messages.list')</a></li>
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
                    
                    <form action="{{ route('admin.material-small-store-inventory.store') }}" method="POST">
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
                                <input type="text" class="form-control" id="title" name="title" value="INVENTAIRE AU PETIT STOCK DES MATERIELS DU {{ date('d') }} ,{{date('M')}} {{ date('Y')}}">
                            </div>
                        </div>

                         <table class="table table-bordered" id="dynamicTable">  
                            <tr class="bg-secondary">
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.unit')</th>
                                <th>@lang('messages.purchase_price')</th>
                                <th>@lang('messages.new_quantity')</th>
                                <th>@lang('New unit')</th>
                                <th>@lang('messages.purchase_price')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr class="">  
                                <td> <select class="form-control" name="material_id[]" id="material_id">
                                <option value="{{ $data->material_id }}" selected="selected" class="form-control">{{ $data->material->name }}/{{ $data->material->code }}</option>
                                </select></td>  
                                <td><input type="text" name="quantity[]" value="{{$data->quantity }}" class="form-control" readonly="readonly" /></td>  
                                <td>
                                    <select class="form-control" name="unit[]" id="unit">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="pcs" {{ $data->unit == 'pcs' ? 'selected' : '' }} class="form-control">Pieces</option>
                                        <option value="paire" {{ $data->unit == 'paire' ? 'selected' : '' }} class="form-control">PAIRE</option>
                                        <option value="litres" {{ $data->unit == 'litres' ? 'selected' : '' }} class="form-control">Litres</option>
                                    </select>
                                </td>
                                <td><input type="text" name="purchase_price[]" value="{{ $data->purchase_price }}" class="form-control" readonly="readonly"/></td>  
                                <td><input type="number" name="new_quantity[]" value="{{ $data->quantity }}" class="form-control" step="any" min="0" /></td>
                                <td>
                                    <select class="form-control" name="new_unit[]" id="new_unit">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="pcs" {{ $data->unit == 'pcs' ? 'selected' : '' }} class="form-control">Pieces</option>
                                        <option value="paire" {{ $data->unit == 'paire' ? 'selected' : '' }} class="form-control">PAIRE</option>
                                        <option value="litres" {{ $data->unit == 'litres' ? 'selected' : '' }} class="form-control">Litres</option>
                                    </select>
                                </td>
                                <td><input type="number" name="new_purchase_price[]" value="{{ $data->purchase_price }}" class="form-control" step="any" min="0" /></td> 
                                <td><button type="button" class="btn btn-danger remove-tr"><i class="fa fa-trash-o" aria-hidden='false'></i>&nbsp;Supprimer</button></td>   
                            </tr> 
                            @endforeach 
                        </table> 
                        <button type="button" name="add" id="add" class="btn btn-primary">@lang('messages.addmore')</button>
                        <div class="col-lg-12">
                            <label for="description"> @lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description">
                              INVENTAIRE AU PETIT STOCK DES MATERIELS DU {{ date('d') }} ,{{date('M')}} {{ date('Y')}}
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
                         "<select class='form-control' name='material_id[]'"+
                            "<option value='0'>merci de choisir</option>"+
                            "@foreach($materials as $material)"+
                                 "<option value='{{$material->id}}'>{{$material->name}}</option>"+
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
                        "<input type='number' name='new_quantity[]' placeholder='new quantity' class='form-control' min='0' />"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='new_purchase_price[]' placeholder='new purchase price' class='form-control' min='0' />"+
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