
@extends('backend.layouts.master')

@section('title')
@lang('Commande Barrista') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Commande Barrista')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.barrist-orders.index',$table_id) }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Commande Barrista')</span></li>
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
                    <h4 class="header-title">Commande Barrista</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.barrist-orders.store') }}" method="post" id="dynamic_form">
                        @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3 mb-lg-0">
                            <div class="card">
                                <div class="seo-fact sbg3">
                                    <a href="">
                                        <div class="p-4 d-flex justify-content-between align-items-center">
                                            <div class="seofct-icon">
                                                <img src="{{ asset('img/undraw_special_event-001.svg') }}" width="200">
                                                {{ $table->name }}
                                    </div>
                                            <h2>
                                                @if($table->etat == '0')
                                                <span class="badge badge-success">libre</span>
                                                @elseif($table->etat == '1')
                                                <span class="badge badge-warning">Si tu n'es pas {{ $table->waiter_name }},cliquer </span><a href="{{ route('admin.dashboard') }}" class="btn btn-info">@lang('ICI')</a>
                                                @endif
                                            </h2>
                                        </div>
                                    </a>
                                </div>
                            </div><br>
                        </div>
                    </div>
                    <input type="hidden" name="table_id" value="{{ $table_id }}">
                    <div class="row">
                            <div class="col-md-12">
                                <label for="espace">Dans quelle place ?</label>
                                <div class="form-group">
                                    <label class="text">SIMPLE (0%)
                                    <input type="checkbox" name="espace" value="1" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">PLAGE (25%)
                                    <input type="checkbox" name="espace" value="2" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">VIP (25%)
                                    <input type="checkbox" name="espace" value="3" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">STAFF (P.A)
                                    <input type="checkbox" name="espace" value="4" class="form-control">
                                    </label>
                                </div>
                            </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date">@lang('messages.date')</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="table_no">@lang('Table No')</label>
                            <input type="text" class="form-control" placeholder="Saisir Table No" id="table_no" name="table_no" required>
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="employe_id">@lang('Serveur')</label>
                            <select class="form-control" name="employe_id" required>
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
                                <th>Ingredient</th>
                                <th>Action</th>
                            </tr>
                            <tr>  
                                <td><select class="form-control" name="barrist_item_id[]" id="barrist_item_id" required>
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" class="form-control">{{ $article->name }}/{{$article->code }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" required placeholder="Entrer quantite" class="form-control" /></td>
                                <td>
                                        @foreach ($ingredients as $ingredient)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="ingredient_id[]" id="{{ $ingredient->id }}" checked="checked" value="{{ $ingredient->id }}">
                                                <label class="form-check-label" for="{{ $ingredient->id }}">{{ $ingredient->name }}</label>
                                            </div>
                                        @endforeach

                                </td>
                                <td><button type="button" name="add" id="add" class="btn btn-success">@lang('messages.addmore')</button></td>     
                            </tr>
                        </table>
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description">
                                COMMANDE CLIENT DES BOISSONS
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
                         "<select class='form-control' name='barrist_item_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($articles as $article)"+
                                 "<option value='{{ $article->id }}'>{{ $article->name }}/{{ $article->code }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' required/>"+
                        "</td>"+
                        "<td>"+
                        "<td>"+
                         "<select class='form-control' name='ingredient_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($ingredients as $ingredient)"+
                                 "<option value='{{ $ingredient->id }}'>{{ $ingredient->name }}</option>"+
                             "@endforeach>"+
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

    //one checked box in checkbox group of espace

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('espace'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('espace'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

</script>
@endsection
