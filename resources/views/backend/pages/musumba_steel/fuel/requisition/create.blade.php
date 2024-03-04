
@extends('backend.layouts.master')

@section('title')
@lang('demande de recquisition') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('demande de recquisition')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.ms-fuel-requisitions.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.address_create')</span></li>
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
                    <h4 class="header-title">Nouveau demande de recquisition</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.ms-fuel-requisitions.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="date">date</label>
                                <input type="date" class="form-control" id="date" name="date" placeholder="Enter le date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fuel_id">Type Carburant</label>
                                    <select class="form-control" name="fuel_id" id="fuel_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach ($fuels as $fuel)
                                <option value="{{ $fuel->id }}" class="form-control">{{ $fuel->name }}</option>
                                @endforeach 
                                </select>
                                </div>
                            </div>
                            <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Véhicule</th>
                                <th>Chauffeur</th>
                                <th>Quantité</th>
                                <th>Action</th>
                            </tr>
                            <tr>  
                                <td> <select id="driver-dropdown" class="form-control" name="car_id[]" id="car_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach ($cars as $car)
                                <option value="{{ $car->id }}" class="form-control">{{ $car->marque }}-{{ $car->immatriculation }}</option>
                                @endforeach 
                                </select></td>
                                <td> <select id="driver-dropdown" class="form-control" name="driver_id[]" id="driver_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}" class="form-control">{{ $driver->firstname }}&nbsp;{{ $driver->lastname }}</option>
                                @endforeach 
                                </select></td>
                                <td><input type="number" name="quantity_requisitioned[]" placeholder="Entrer quantité" class="form-control"  step="any" min="0" /></td>      
                                <td><button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" aria-hidden="false"></i>&nbsp;Plus</button></td>  
                            </tr>  
                        </table> 
                        <div class="col-lg-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Entrer description">
                                DEMANDE DE REQUISITION DU CARBURANT
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script type="text/javascript">
    var i = 0;
       
    $("#add").click(function(){
   
        ++i;

         var markup = "<tr>"+
                      "<td>"+
                         "<select class='form-control' name='car_id[]'"+
                            "<option>Merci de choisir</option>"+
                             "@foreach($cars as $car)"+
                                 "<option value='{{ $car->id }}'>{{ $car->marque }}-{{ $car->immatriculation }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                         "<select class='form-control' name='driver_id[]'"+
                            "<option>Merci de choisir</option>"+
                             "@foreach($drivers as $driver)"+
                                 "<option value='{{ $driver->id }}'>{{ $driver->firstname }}&nbsp;{{ $driver->lastname }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity_requisitioned[]' placeholder='Entrer Quantité' class='form-control' step='any' min='0'/>"+
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
