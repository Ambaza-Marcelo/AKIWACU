
@extends('backend.layouts.master')

@section('title')
@lang('Modifier le planning des approvisionnements des boissons') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier le planning des approvisionnements des boissons')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.plan-purchase-drinks.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier le planning des approvisionnements des boissons')</span></li>
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
                    <h4 class="header-title">Modifier le planning des approvisionnements des boissons</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.plan-purchase-drinks.update',$plan->plan_no) }}" method="post" id="dynamic_form">
                        @csrf
                        @method('PUT')
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                            <div class="form-group">
                                <label for="start_date">@lang('Date debut')</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $plan->start_date }}">
                            </div>
                        </div>
                        <div class="col-md-6" id="dynamicDiv">
                            <div class="form-group">
                                <label for="end_date">@lang('Date Fin')</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $plan->end_date }}">
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
                            @foreach($plans as $plan)
                            <tr class="">  
                                <td><select class="form-control" name="drink_id[]" id="drink_id" required>
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($drinks as $drink)
                                <option value="{{ $drink->id }}" {{ $plan->drink_id == $drink->id ? 'selected' : '' }} class="form-control">{{$drink->name}}/{{ $drink->code }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" class="form-control" min="0" value="{{ $plan->quantity }}" required /></td> 
                                <td><select class="form-control" name="unit[]" id="unit">
                                    <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="bouteilles" class="form-control" {{ $plan->unit == 'bouteilles' ? 'selected' : '' }}>Bouteilles</option>
                                        <option value="pcs" class="form-control" {{ $plan->unit == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                        <option value="cartons" class="form-control" {{ $plan->unit == 'cartons' ? 'selected' : '' }}>Cartons</option>
                                        <option value="millilitres" class="form-control" {{ $plan->unit == 'millilitres' ? 'selected' : '' }}>Millilitres</option>
                                </select></td>
                                <td><input type="number" name="purchase_price[]" class="form-control" min="0" value="{{ $plan->purchase_price }}" required /></td> 
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
                                {{ $plan->description }}
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
                          "<input type='number' name='purchase_price[]' placeholder='Enter purchase Price' class='form-control' min='0' required/>"+
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
