
@extends('backend.layouts.master')

@section('title')
@lang('Demande de Requisition des Articles') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Demande de Requisition des Articles')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.material-requisitions.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Demande de Requisition des Articles')</span></li>
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
                    <h4 class="header-title">Demande de Requisition des Articles</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.material-requisitions.store') }}" method="post" id="dynamic_form">
                        @csrf
                    <input type="hidden" name="type_store" value="bg">
                    <div class="row">
                        <div class="col-sm-6" id="dynamicDiv">
                            <div class="form-group">
                                <label for="date">@lang('messages.date')</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                        </div>
                    </div>

                         <table class="table table-bordered" id="dynamicTable">  
                            <tr class="">
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.unit')</th>
                                <th>Action</th>
                            </tr>
                            <tr class="">  
                                <td><select class="form-control" name="material_id[]" id="material_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($materials as $material)
                                <option value="{{ $material->id }}" class="form-control">{{$material->name}}/{{ $material->code }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity_requisitioned[]" placeholder="Enter quantity" class="form-control" min="0" /></td> 
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
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description">
                                DEMANDE DE REQUISITION DES ARTICLES
                            </textarea>
                        </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary" id="save">@lang('messages.save')</button>
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
                                 "<option value='{{ $material->id }}'>{{ $material->name }}/{{ $material->code }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity_requisitioned[]' placeholder='Enter Quantity' class='form-control' min='0'/>"+
                        "</td>"+
                        "<td>"+
                          "<select class='form-control' name='unit[]' id='unit'>"+
                                "<option disabled='disabled' selected='selected'>Merci de choisir</option>"+
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


    var path = "{{ route('admin.drinks.autocomplete') }}";
  
    $( "#search" ).autocomplete({
        source: function( request, response ) {
          $.ajax({
            url: path,
            type: 'GET',
            dataType: "json",
            data: {
               search: request.term
            },
            success: function( data ) {
               response( data );
            }
          });
        },
        select: function (event, ui) {
           $('#search').val(ui.item.label);
           console.log(ui.item); 
           return false;
        }
      });

</script>
@endsection
