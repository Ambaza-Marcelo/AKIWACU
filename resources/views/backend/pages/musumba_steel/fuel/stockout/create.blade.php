
@extends('backend.layouts.master')

@section('title')
@lang('sortie du carburant') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('sortie du carburant')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.ms-fuel-stockouts.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('sortie du carburant')</span></li>
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
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">sortie du carburant</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.ms-fuel-stockouts.store') }}" method="POST">
                        @csrf

                         <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="date">date</label>
                                <input type="date" class="form-control" id="date" name="date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fuel_id">Type Carburant</label>
                                    <select class="form-control" name="fuel_id" id="fuel_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                <option value="{{ $requisition->fuel_id }}" class="form-control" selected="selected">{{ $requisition->fuel->name }}</option>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="requisition_no">Requisition No</label>
                                    <select class="form-control" name="requisition_no" id="requisition_no">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                <option value="{{ $requisition->requisition_no }}" class="form-control" selected="selected">{{ $requisition->requisition_no }}</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pump_id">Cuve de Stockage</label>
                                    <select class="form-control" name="pump_id" id="pump_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($pumps as $pump)
                                <option value="{{ $pump->id }}" {{ $pump->fuel_id == $requisition->fuel_id ? 'selected' : '' }} class="form-control">{{ $pump->name }}</option>
                                @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                            <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Véhicule</th>
                                <th>Chauffeur</th>
                                <th>Quantité</th>
                                <th>Action</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <select id="driver-dropdown" class="form-control" name="car_id[]" id="car_id">
                                <option disabled="disabled">merci de choisir</option>
                                <option value="{{ $data->car_id }}" selected="selected" class="form-control">{{ $data->car->marque }}-{{ $data->car->immatriculation }}</option>
                                </select></td>
                                <td> <select id="driver-dropdown" class="form-control" name="driver_id[]" id="driver_id">
                                <option disabled="disabled">merci de choisir</option>
                                <option value="{{ $data->driver_id }}" selected="selected" class="form-control">{{ $data->driver->firstname }}&nbsp;{{ $data->driver->lastname }}</option>
                                </select></td>
                                <td><input type="number" name="quantity[]" value="{{ $data->quantity_requisitioned }}" class="form-control"  step="any" min="0" /></td> 
                                <td>
                                <button type='button' class='btn btn-danger remove-tr'>@lang('messages.delete')</button></td>       
                            </tr>
                            @endforeach  
                        </table> 
                        <div class="col-lg-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Entrer description">
                                SORTIE DU CARBURANT PAR ORDRE
                            </textarea>
                        </div>                   
                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary mt-4 pr-4 pl-4">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
   
    $(document).on('click', '.remove-tr', function(){  
         $(this).parents('tr').remove();
    }); 

</script>
@endsection
