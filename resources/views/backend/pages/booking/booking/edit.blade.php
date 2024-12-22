
@extends('backend.layouts.master')

@section('title')
@lang('Commande Cuisine') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Commande Cuisine')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.order_kitchens.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Commande Cuisine')</span></li>
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
                    <h4 class="header-title">Commande Cuisine</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.order_kitchens.store') }}" method="post" id="dynamic_form">
                        @csrf
                    <div class="row">
                        <div class="col-md-4" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date">@lang('messages.date')</label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="table_no">@lang('Table No')</label>
                            <input type="text" class="form-control" placeholder="Saisir Table No" id="table_no" name="table_no">
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="employe_id">@lang('Serveur')</label>
                            <select class="form-control" name="employe_id">
                                <option disabled="disabled" selected="selected">Merci de choisir Serveur</option>
                            @foreach ($employes as $employe)
                                <option value="{{ $employe->id }}">{{ $employe->name }}/{{ $employe->telephone }}</option>
                            @endforeach
                            </select>
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
                                <td><select class="form-control" name="article_id[]" id="article_id">
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" class="form-control">{{ $article->name }}/{{$article->specification }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" placeholder="Entrer quantite" class="form-control" /></td>
                                <td><button type="button" name="add" id="add" class="btn btn-success">@lang('messages.addmore')</button></td>     
                            </tr>
                        </table>
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description">
                                
                            </textarea>
                        </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" class="btn btn-primary" id="save">@lang('messages.save')</button>
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
                         "<select class='form-control' name='article_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($articles as $article)"+
                                 "<option value='{{ $article->id }}'>{{ $article->name }}/{{ $article->specification }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' />"+
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
