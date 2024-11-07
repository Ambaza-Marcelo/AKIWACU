
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
                    <li><a href="{{ route('admin.food-transfers.index') }}">@lang('messages.list')</a></li>
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
                    
                    <form action="{{ route('admin.food-transfers.storePortion',$transfer_no) }}" method="POST">
                        @csrf
                        @method('PUT')
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date">@lang('messages.date')</label>
                                <input type="date" class="form-control" id="date" name="date_portion">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="transfer_no">Transfert No</label>
                                <input type="text" class="form-control" id="transfer_no" name="transfer_no" value="{{$transfer_no}}" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="origin_store_id">@lang('Stock Origine')</label>
                                <select class="form-control" name="origin_store_id" id="origin_store_id">
                                 <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($origin_stores as $origin_store)
                                    <option value="{{$origin_store->id}}" {{ $data->origin_store_id == $origin_store->id ? 'selected' : '' }}>{{$origin_store->name}}</option>
                                @endforeach
                             </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="destination_store_id">@lang('Stock Destination')</label>
                                <select class="form-control" name="destination_store_id" id="destination_store_id">
                                 <option disabled="disabled" selected="selected">Merci de choisir</option>
                                @foreach($destination_stores as $destination_store)
                                    <option value="{{$destination_store->id}}" {{ $data->destination_store_id == $destination_store->id ? 'selected' : '' }}>{{$destination_store->name}}</option>
                                @endforeach
                             </select>
                            </div>
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.unit_price')</th>
                                <th>@lang('portioned Quantity')</th>
                                <th>@lang('portioned Price')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <select class="form-control" name="food_id[]" id="food_id">
                                <option value="{{ $data->food_id }}" class="form-control">{{ $data->food->name }}/{{ $data->food->foodMeasurement->production_unit }}</option>
                                </select></td>  
                                <td><input type="number" name="quantity_transfered[]" value="{{ $data->quantity_transfered }}" class="form-control" step="any"  readonly /></td>  
                                <td><input type="number" name="price[]" value="{{$data->price}}" class="form-control" step="any" min="0" readonly /></td>
                                <td><input type="number" name="quantity_portion[]" value="{{ $data->quantity_transfered * $data->food->foodMeasurement->equivalent }}" class="form-control" min="1" step="any" /></td>  
                                <td><input type="number" name="price[]" value="{{$data->price}}" class="form-control" step="any" min="0"/></td>
                                <td><button type='button' class='btn btn-danger remove-tr'>@lang('messages.delete')</button></td>  
                            </tr> 
                            @endforeach 
                        </table>
                        <!--
                        <button type="button" name="add" id="add" class="btn btn-primary">@lang('messages.addmore')</button> 
                    -->
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description_portion" id="description" placeholder="Enter Desccription">
                                PORTIONNAGE
                            </textarea>
                        </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
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

</script>
@endsection
