
@extends('backend.layouts.master')

@section('title')
@lang('Consommation maison Cuisine') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Consommation maison Cuisine')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.home-consumption-food.index',$staff_member_id) }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Consommation maison Cuisine')</span></li>
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
                    
                    <form action="{{ route('admin.home-consumption.store') }}" method="post" id="dynamic_form">
                        @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3 mb-lg-0">
                            <div class="card">
                                <div class="seo-fact sbg3">
                                    <a href="">
                                        <div class="p-4 d-flex justify-content-between align-items-center">
                                            <div class="seofct-icon">
                                                <img src="{{ asset('img/undraw_special_event-001.svg') }}" width="150">
                                                {{ $staff_member->name }}-{{ $staff_member->position->name }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div><br>
                        </div>
                    </div>
                    <input type="hidden" name="staff_member_id" value="{{ $staff_member_id }}">
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date">@lang('messages.date')</label>
                            <input type="date" class="form-control" id="date" name="date" required>
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
                                <td><select class="form-control" name="food_item_id[]" id="food_item_id" required>
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" class="form-control">{{ $article->name }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" placeholder="Entrer quantite" min="0" class="form-control" required /></td>
                                <td><button type="button" name="add" id="add" class="btn btn-success">@lang('messages.addmore')</button></td>     
                            </tr>
                        </table>
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description">
                                CONSOMMATION MAISON
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
                         "<select class='form-control' name='food_item_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($articles as $article)"+
                                 "<option value='{{ $article->id }}'>{{ $article->name }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' name='quantity[]' placeholder='Enter Quantity' class='form-control' required min='0' required/>"+
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
