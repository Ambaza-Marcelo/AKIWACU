
@extends('backend.layouts.master')

@section('title')
@lang('demander achat') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('demander achat')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.ms-fuel-purchases.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('demander achat')</span></li>
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
                    <h4 class="header-title">Demander l'achat</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.ms-fuel-purchases.store') }}" method="post" id="dynamic_form">
                        @csrf
                    <div class="row">
                        <div class="col-sm-6" id="dynamicDiv">
                            <input type="hidden" class="form-control" name="bon_no">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>
                        </div>

                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Carburant</th>
                                <th>Quantité</th>
                                <th>Action</th>
                            </tr>
                            <tr>  
                                <td><select class="form-control" name="fuel_id[]" id="fuel_id">
                                    <option selected disabled>Merci de Choisir</option>
                                    @foreach($fuels as $fuel)
                                    <option value="{{ $fuel->id }}" class="form-control">{{ $fuel->name }}</option>
                                    @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" placeholder="Entrer Quantité" class="form-control" step="any" min="0" /></td> 
                                <td><button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" aria-hidden="false"></i>&nbsp;Plus</button> </td>    
                            </tr>
                        </table>
                        <div class="col-lg-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description">
                                DEMANDE D'ACHAT DU CARBURANT
                            </textarea>
                        </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary" id="save">Enregistrer</button>
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
                         "<select class='form-control' name='fuel_id[]'"+
                            "<option value='0'>merci de choisir</option>"+
                             "@foreach($fuels as $fuel)"+
                                 "<option value='{{ $fuel->id }}'>{{ $fuel->nom }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Entrer Quantité' class='form-control' step='any' min='0'/>"+
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
