
@extends('backend.layouts.master')

@section('title')
@lang('messages.stockout_create') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.stockout_create')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-material-stockouts.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.stockout_create')</span></li>
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
                    <h4 class="header-title">Nouvelle Sortie</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.sotb-material-stockouts.storeFromBig') }}" method="POST">
                        @csrf
                        <input type="hidden" name="store_type" value="bg">
                        <input type="hidden" name="code_store" value="{{ $code }}">
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                            <div class="form-group">
                             <label for="date">@lang('messages.date')</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asker">Demandeur</label>
                                <input type="text" class="form-control" id="asker" name="asker" placeholder="Entrer le demandeur">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="item_movement_type">@lang('Type de Sortie')</label>
                                    <select class="form-control" name="item_movement_type" id="item_movement_type" required>
                                    <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        <option value="SN">Sortie Normale</option>
                                        <option value="SP">Sortie Perte</option>
                                        <option value="SV">Sortie Vol</option>
                                        <option value="SD">Sortie Désuétude</option>
                                        <option value="SC">Sortie Casse</option>
                                        <option value="SAJ">Sortie Ajustement</option>
                                        <option value="ST">Sortie Transfert</option>
                                        <option value="SAU">Sortie Autre</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="origin_bg_store_id">@lang('Stock Origine')<strong style="color: red;">*</strong></label>
                                    <select class="form-control" name="origin_bg_store_id" id="origin_bg_store_id" required>
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($material_origin_stores as $material_origin_store)
                                        <option value="{{ $material_origin_store->id }}" {{ $material_origin_store->code == $code ? 'selected' : '' }}>{{ $material_origin_store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label for="destination">Destination</label>
                                <input type="text" class="form-control" id="destination" name="destination" placeholder="Entrer destination">
                            </div>
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.unit')</th>
                                <th>Action</th>
                            </tr>
                            <tr>  
                                <td> <select class="form-control" name="material_id[]" id="material_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                            @foreach ($materials as $material)
                                <option value="{{ $material->material_id }}" class="form-control">{{ $material->material->name }}</option>
                            @endforeach
                            </select></td>  
                                <td><input type="number" name="quantity[]" placeholder="Enter quantity" class="form-control" /></td>  
                                <td><select class="form-control" name="unit[]" id="unit">
                                    <option disabled="disabled" selected="selected">Merci de choisir</option>
                                    <option value="pcs" class="form-control">Pieces</option>
                                        <option value="paire" class="form-control">PAIRE</option>
                                        <option value="litres" class="form-control">Litres</option>
                                </select></td>   
                                <td><button type="button" name="add" id="add" class="btn btn-success">@lang('messages.addmore')</button></td>  
                            </tr>  
                        </table> 
                        <div class="col-lg-12">
                            <label for="description"> @lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Entrer description">
                                SORTIE DES ARTICLES
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
                         "<select class='form-control' name='material_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($materials as $material)"+
                                 "<option value='{{ $material->material_id }}'>{{ $material->material->name }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<select class='form-control' name='unit[]' id='unit'>"+
                                "<option disabled='disabled' selected='selected'>merci de choisir</option>"+
                                "<option value='pcs' class='form-control'>Pieces</option>"+
                                "<option value='paire' class='form-control'>Paire</option>"+
                                "<option value='litres' class='form-control'>Litres</option>"+
                                "</select>"+
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
