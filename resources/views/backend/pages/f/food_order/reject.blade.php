
@extends('backend.layouts.master')

@section('title')
@lang('Rejeter la Commande Cuisine') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Rejeter la Commande Cuisine')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.f-food-orders.index',$data->table_id) }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Rejeter la Commande Cuisine')</span></li>
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
                    <h4 class="header-title">Rejeter la Commande Cuisine</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.f-food-orders.reject', $data->order_no) }}" method="post" id="dynamic_form">
                        @csrf
                    @method('PUT')
                    <input type="hidden" name="table_id" value="{{ $data->table_id }}">
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
                        <div class="col-md-6" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date">@lang('messages.date')</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $data->date }}" readonly>
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="employe_id">@lang('Serveur')</label>
                            <select class="form-control" name="employe_id" required>
                                <option disabled="disabled" selected="selected">Merci de choisir Serveur</option>
                            @foreach ($employes as $employe)
                                <option value="{{ $employe->id }}" disabled {{ $data->employe_id == $employe->id ? 'selected' : '' }}>{{ $employe->name }}/{{ $employe->telephone }}</option>
                            @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td><select class="form-control" name="food_item_id[]" id="food_item_id" required>
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" disabled class="form-control" {{ $data->food_item_id == $article->id ? 'selected' : '' }}>{{ $article->name }}/{{$article->code }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" name="quantity[]" value="{{ $data->quantity }}" class="form-control" readonly /></td>     
                            </tr>
                            @endforeach
                        </table>
                        <div class="col-lg-12">
                            <label for="rej_motif">@lang('messages.description')</label>
                            <textarea class="form-control" name="rej_motif" id="rej_motif" placeholder="Enter Description">
                                J'annule cette commande parceque
                            </textarea>
                        </div>
                        <div style="margin-top: 15px;margin-left: 15px;">
                            <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary" id="save">@lang('Rejeter')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
</div>
<script type="text/javascript">
    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    window.onunload = function () {
        null
    };
</script>
@endsection
