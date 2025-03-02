
@extends('backend.layouts.master')

@section('title')
@lang('Formulaire de reservation Kidness Space') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Formulaire de reservation Kidness Space')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.booking-kidness-space.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Formulaire de reservation Kidness Space')</span></li>
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
                    <h4 class="header-title">Formulaire de reservation Kidness Space</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.booking-kidness-space.store') }}" method="post" id="dynamic_form">
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
                                <option value="{{ $client->id }}" {{ $client->mail == 'clientcash@gmail.com' ? 'selected' : '' }}>{{ $client->customer_name }}/{{ $client->telephone }}</option>
                            @endforeach
                            </select>
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
                                <td><select class="form-control" name="kidness_space_id[]" id="kidness_space_id" required>
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" class="form-control">{{ $article->name }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" min="0" placeholder="Entrer quantite" required class="form-control" /></td>
                                <td><button type="button" name="add" id="add" class="btn btn-success">@lang('messages.addmore')</button></td>     
                            </tr>
                        </table>
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description">
                                RESERVATION DE KIDNESS SPACE
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
                         "<select class='form-control' name='kidness_space_id[]' required"+
                            "<option>merci de choisir</option>"+
                             "@foreach($articles as $article)"+
                                 "<option value='{{ $article->id }}'>{{ $article->name }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' min='0' required placeholder='Enter Quantity' class='form-control' />"+
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
