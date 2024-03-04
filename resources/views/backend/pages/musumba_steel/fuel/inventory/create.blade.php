
@extends('backend.layouts.master')

@section('title')
@lang('Inventaire du stock carburant') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Inventaire du stock carburant')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.ms-fuel-inventories.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Inventaire du stock carburant')</span></li>
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
                    <h4 class="header-title">Inventory</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.ms-fuel-inventories.store') }}" method="POST">
                        @csrf
                        <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Titre</label>
                            <input type="text" class="form-control" id="title" name="title" value="INVENTAIRE DU STOCK DE CARBURANT DU {{ date('d') }} ,{{date('M')}} {{ date('Y')}}">
                        </div>
                        </div>
                    </div>

                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Type de carburant</th>
                                <th>Quantite</th>
                                <th>Prix</th>
                                <th>Nouvelle Quantité</th>
                                <th>Nouveau Prix</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>
                                <td><select id="driver-dropdown" class="form-control" name="pump_id[]" id="pump_id">
                                <option disabled="disabled">merci de choisir</option>
                                @foreach($pumps as $pump)
                                <option value="{{ $pump->id }}" selected="selected" class="form-control">{{ $pump->fuel->name }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="text" name="quantity[]" value="{{$data->quantity }}" class="form-control" readonly="readonly" /></td>  
                                <td><input type="text" name="cost_price[]" value="{{ $data->cost_price }}" class="form-control" readonly="readonly"/></td>
                                <td><input type="number" name="new_quantity[]" value="{{ $data->quantity }}" class="form-control" step="any" min="0"/></td> 
                                <td><input type="number" name="new_cost_price[]" value="{{ $data->cost_price }}" class="form-control" step="any" min="0"/></td>
                                <td><button type="button" class="btn btn-danger remove-tr"><i class="fa fa-trash-o" aria-hidden="false"></i>&nbsp;Supprimer</button></td>    
                            </tr> 
                            @endforeach 
                        </table> 
                        <button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" aria-hidden="false"></i>&nbsp;Plus</button>
                        <div class="col-lg-12">
                            <label for="description"> Description</label>
                            <textarea class="form-control" name="description" id="description">
                              INVENTAIRE DU STOCK DE CARBURANT DU {{ date('d') }} ,{{date('M')}} {{ date('Y')}}
                            </textarea>
                        </div>
                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary mt-4 pr-4 pl-4">Enregistrer</button>
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

         var markup = "<tr>"+
                        "<td>"+
                         "<select class='form-control' name='pump_id[]'"+
                            "<option>Merci de choisir</option>"+
                             "@foreach($pumps as $pump)"+
                                 "<option value='{{ $pump->id }}'>{{ $pump->fuel->name }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Entrer quantity' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<input type='text' name='cost_price[]' placeholder='Entrer prix' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='new_quantity[]' placeholder='nouvelle quantite' class='form-control' step='any' min='0'/>"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' name='new_cost_price[]' placeholder='nouveau prix' class='form-control' step='any' min='0'/>"+
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