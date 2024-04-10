
@extends('backend.layouts.master')

@section('title')
@lang('Formulaire de reservation Breakfast') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Formulaire de reservation Breakfast')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.booking-break-fast.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Formulaire de reservation Breakfast')</span></li>
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
                    <h4 class="header-title">Formulaire de reservation Breakfast</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.booking-break-fast.store') }}" method="post" id="dynamic_form">
                        @csrf
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date">@lang('messages.date')</label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="booking_client_id">@lang('Nom du Client')</label>
                            <select class="form-control" name="booking_client_id">
                                <option disabled="disabled" selected="selected">Merci de choisir client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->customer_name }}/{{ $client->telephone }}</option>
                            @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" id="dynamicDiv">
                        <div class="form-group">
                            <label for="nom_referent">@lang('nom referent')</label>
                            <input type="text" class="form-control" id="nom_referent" name="nom_referent" placeholder="Saisir le nom referent">
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="telephone_referent">@lang('telephone referent')</label>
                            <input type="tel" class="form-control" placeholder="Saisir telephone referent" id="telephone_referent" name="telephone_referent">
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="courriel_referent">@lang('courriel referent')</label>
                            <input type="tel" class="form-control" placeholder="Saisir courriel referent" id="courriel_referent" name="courriel_referent">
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="type_evenement">@lang('Type Evenement')</label>
                            <select class="form-control" name="type_evenement">
                                <option disabled="disabled" selected="selected">Merci de choisir client</option>
                                <option value="1">CONFERENCES</option>
                                <option value="2">REUNIONS</option>
                                <option value="3">AUTRES</option>
                            </select>
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_personnes">@lang('Nombre de Personnes')</label>
                            <input type="number" class="form-control" placeholder="Saisir nombre de personnes" id="nombre_personnes" min="0" name="nombre_personnes">
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date_debut">@lang('Date de debut')</label>
                            <input type="datetime-local" class="form-control" id="date_debut" name="date_debut">
                        </div>
                        </div>
                        <div class="col-md-6" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date_fin">@lang('Date de fin')</label>
                            <input type="datetime-local" class="form-control" id="date_fin" name="date_fin">
                        </div>
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>Action</th>
                            </tr>
                            <tr>  
                                <td><select class="form-control" name="break_fast_id[]" id="break_fast_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" class="form-control">{{ $article->name }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" min="0" placeholder="Entrer quantite" class="form-control" /></td>
                                <td><button type="button" name="add" id="add" class="btn btn-success">@lang('messages.addmore')</button></td>     
                            </tr>
                        </table>
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description">
                                RESERVATION DE SERVICE
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
                         "<select class='form-control' name='break_fast_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($articles as $article)"+
                                 "<option value='{{ $article->id }}'>{{ $article->name }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' min='0' placeholder='Enter Quantity' class='form-control' />"+
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
