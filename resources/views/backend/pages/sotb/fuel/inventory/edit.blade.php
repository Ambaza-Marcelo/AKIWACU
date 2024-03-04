
@extends('backend.layouts.master')

@section('title')
@lang('Modifier inventaire du stock carburant') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier inventaire du stock carburant')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.fuel_inventories.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier inventaire du stock carburant')</span></li>
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
                <div class="card-body bg-warning">
                    <h4 class="header-title">Modifier inventaire du stock carburant</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.fuel_inventories.update',$fuel_inventory->inventory_no) }}" method="POST">
                        @method('put')
                        @csrf
                        <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $fuel_inventory->date}}">
                        </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="title">Titre</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $fuel_inventory->title}}">
                        </div>
                    </div>

                         <table class="table table-bordered" id="dynamicTable">  
                            <tr class="bg-secondary">
                                <th>Cuve/Carburant</th>
                                <th>Quantite</th>
                                <th>CUMP</th>
                                <th>Nouveau Quantité</th>
                                <th>Jauge</th>
                                <th>Nouveau Prix</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr class="bg-warning">  
                                <td> <select class="form-control" name="fuel_pump_id[]" id="fuel_pump_id">
                                <option value="{{ $data->id }}" selected="selected" class="form-control">{{ $data->fuelPump->designation }}/{{ $data->fuelPump->fuel->nom }}</option>
                                </select></td>  
                                <td><input type="text" name="quantite[]" value="{{$data->quantite }}" class="form-control" readonly="readonly" /></td>  
                                <td><input type="text" name="prix_unitaire[]" value="{{ $data->fuelPump->prix_unitaire }}" class="form-control" readonly="readonly"/></td>
                                <td><input type="number" name="nouvelle_quantite[]" value="{{ $data->nouvelle_quantite }}" class="form-control" step="any" min="0"/></td>
                                <td> <select class="form-control" name="jauge_id[]" id="jauge_id">
                                <option selected="selected" disabled="disabled">merci de choisir</option>
                                @foreach($fuel_jauges as $fuel_jauge)
                                <option value="{{ $fuel_jauge->id }}" class="form-control">{{ $fuel_jauge->quantite }}/{{ $fuel_jauge->valeur }}</option>
                                @endforeach
                                </select></td> 
                                <td><input type="number" name="nouveau_prix[]" value="{{ $data->fuelPump->prix_unitaire }}" class="form-control" step="any" min="0"/></td>
                                <td><button type="button" class="btn btn-danger remove-tr"><i class="fa fa-trash-o" aria-hidden="false"></i>&nbsp;Supprimer</button></td>    
                            </tr> 
                            @endforeach 
                        </table> 
                        <button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" aria-hidden="false"></i>&nbsp;Plus</button>
                        <div class="col-lg-12">
                            <label for="description"> Description</label>
                            <textarea class="form-control" name="description" id="description">
                              {{ $fuel_inventory->description}}
                            </textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Modifier</button>
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

         var markup = "<tr class='bg-warning'>"+
                      "<td>"+
                         "<select class='form-control' name='fuel_pump_id[]'"+
                            "<option>Merci de choisir</option>"+
                            "@foreach($fuel_pumps as $fuel_pump)"+
                                 "<option value='{{$fuel_pump->id}}'>{{$fuel_pump->designation}}</option>"+
                            "@endforeach"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantite[]' placeholder='Entrer quantite' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<input type='text' name='prix_unitaire[]' placeholder='Entrer prix' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='nouvelle_quantite[]' placeholder='nouvelle quantite' class='form-control' step='any' min='0'/>"+
                        "</td>"+
                        "<td>"+
                         "<select class='form-control' name='jauge_id[]'"+
                            "<option>Merci de choisir</option>"+
                            "@foreach($fuel_jauges as $fuel_jauge)"+
                                 "<option value='{{$fuel_jauge->id}}'>{{$fuel_jauge->quantite}}/{{$fuel_jauge->valeur}}</option>"+
                            "@endforeach"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='nouveau_prix[]' placeholder='nouveau prix' class='form-control' step='any' min='0'/>"+
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