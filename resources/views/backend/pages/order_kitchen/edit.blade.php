
@extends('backend.layouts.master')

@section('title')
@lang('Modifier Commande Cuisine') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier Commande Cuisine')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.order_kitchens.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier Commande Cuisine')</span></li>
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
                    <h4 class="header-title">Modifier Commande Cuisine</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.order_kitchens.update',$data->order_no) }}" method="post" id="dynamic_form">
                        @csrf
                        @method('PUT')
                    <div class="row">
                        <div class="col-md-4" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date">@lang('messages.date')</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $data->date }}">
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="table_no">@lang('Table No')</label>
                            <input type="text" class="form-control" value="{{ $data->table_no }}" id="table_no" name="table_no">
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="employe_id">@lang('Serveur')</label>
                            <select class="form-control" name="employe_id">
                                <option disabled="disabled" selected="selected">Merci de choisir Serveur</option>
                            @foreach ($employes as $employe)
                                <option value="{{ $employe->id }}" {{ $data->employe_id == $employe->id ? 'selected' : '' }}>{{ $employe->name }}/{{ $employe->telephone }}</option>
                            @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>Accompagnements</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td><select class="form-control" name="food_item_id[]" id="food_item_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" {{ $data->food_item_id == $article->id ? 'selected' : '' }} @if (Auth::guard('admin')->user()->can('food_order_client.delete')) @else disabled @endif class="form-control">{{ $article->name }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" value="{{ $data->quantity }}" @if (Auth::guard('admin')->user()->can('food_order_client.delete')) @else readonly @endif class="form-control" /></td>
                                <td>

                                        @foreach ($accompagnements as $accompagnement)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="accompagnement_id[]" id="{{ $accompagnement->id }}" value="{{ $accompagnement->id }}" {{ $data->accompagnement_id == $accompagnement->id ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ $accompagnement->id }}">{{ $accompagnement->name }}</label>
                                            </div>
                                        @endforeach

                                </td>
                                <td>@if(Auth::guard('admin')->user()->can('food_order_client.delete'))
                                    <button type='button' class='btn btn-danger remove-tr'><i class='fa fa-trash-o' title='Supprimer la ligne' aria-hidden='false'></i></button>
                                    @endif</td>     
                            </tr>
                            @endforeach
                        </table>
                        <button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" title="Ajouter Plus" aria-hidden="false"></i></button>
                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary">Modifier</button>
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
                         "<select class='form-control' name='food_item_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($articles as $article)"+
                                 "<option value='{{ $article->id }}'>{{ $article->name }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                                    "@foreach ($accompagnements as $accompagnement)"+
                                            "<div class='form-check'>"+
                                               "<input type='checkbox' class='form-check-input' name='accompagnement_id[]' id='{{ $accompagnement->id }}' value='{{ $accompagnement->id }}'>"+
                                                "<label class='form-check-label' for='{{ $accompagnement->id }}'>{{ $accompagnement->name }}</label>"+
                                            "</div>"+
                                    "@endforeach"+
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
