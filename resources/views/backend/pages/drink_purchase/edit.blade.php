
@extends('backend.layouts.master')

@section('title')
@lang('Modifier la demande d\'achat') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier la demande d\'achat')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.drink-purchases.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier la demande d\'achat')</span></li>
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
                    <h4 class="header-title">Modifier la demande d'achat</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.drink-purchases.update',$data->purchase_no) }}" method="post" id="dynamic_form">
                        @csrf
                        @method('PUT')
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                            <div class="form-group">
                                <label for="date">@lang('Date debut')</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ $data->date }}">
                            </div>
                        </div>
                    </div>

                         <table class="table table-bordered" id="dynamicTable">  
                            <tr class="">
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.unit')</th>
                                <th>@lang('P.A')</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr class="">  
                                <td><select class="form-control" name="drink_id[]" id="drink_id" required>
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($drinks as $drink)
                                <option value="{{ $drink->id }}" {{ $data->drink_id == $drink->id ? 'selected' : '' }} class="form-control">{{$drink->name}}/{{ $drink->code }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" class="form-control" min="0" value="{{ $data->quantity }}" required /></td> 
                                <td><select class="form-control" name="unit[]" id="unit">
                                    <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="bouteilles" class="form-control" {{ $data->unit == 'bouteilles' ? 'selected' : '' }}>Bouteilles</option>
                                        <option value="pcs" class="form-control" {{ $data->unit == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                        <option value="cartons" class="form-control" {{ $data->unit == 'cartons' ? 'selected' : '' }}>Cartons</option>
                                        <option value="millilitres" class="form-control" {{ $data->unit == 'millilitres' ? 'selected' : '' }}>Millilitres</option>
                                </select></td>
                                <td><input type="number" name="price[]" class="form-control" min="0" value="{{ $data->price }}" step="any" required /></td> 
                                <td><button type='button' class='btn btn-danger remove-tr'>@lang('messages.delete')</button></td>     
                            </tr>
                            @endforeach
                        </table>
                        <div>
                            <button type="button" name="add" id="add" class="btn btn-success">@lang('messages.addmore')</button>
                        </div>
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description">
                                {{ $data->description }}
                            </textarea>
                        </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary" id="update">@lang('Modifier')</button>
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
                         "<select class='form-control' name='drink_id[]' required"+
                            "<option>merci de choisir</option>"+
                             "@foreach($drinks as $drink)"+
                                 "<option value='{{ $drink->id }}'>{{ $drink->name }}/{{ $drink->code }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' min='0' required/>"+
                        "</td>"+
                        "<td>"+
                          "<select class='form-control' name='unit[]' id='unit' required>"+
                                "<option disabled='disabled' selected='selected'>Merci de choisir</option>"+
                                "<option value='bouteilles' class='form-control'>Bouteilles</option>"+
                                "<option value='pcs' class='form-control'>Pieces</option>"+
                                "<option value='millilitres' class='form-control'>Millilitres</option>"+
                                "<option value='cartons' class='form-control'>Cartons</option>"+
                            "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='price[]' placeholder='Enter purchase Price' class='form-control' step='any' min='0' required/>"+
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
