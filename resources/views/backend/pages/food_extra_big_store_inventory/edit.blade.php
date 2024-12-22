
@extends('backend.layouts.master')

@section('title')
@lang('messages.edit') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.edit')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.inventories.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('messages.edit')</span></li>
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
                <div class="card-body bg-warning">
                    <h4 class="header-title">Modifier le bon d'inventaire No : {{ $inventory_no }}</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.inventories.update',$inventory->inventory_no) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row">
                        <div class="col-sm-6">
                            <input type="hidden" class="form-control" name="bon_no">
                        <div class="form-group">
                            <label for="date">@lang('messages.date')</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $inventory->date }}">
                        </div>
                        </div>
                        <div class="col-sm-6">
                        <div class="form-group">
                            <label for="title">@lang('messages.title')</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{$inventory->title}}">
                        </div>
                    </div>

                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>@lang('messages.unit')</th>
                                <th>CUMP</th>
                                <th>@lang('messages.category')</th>
                                <th>@lang('messages.new_quantity')</th>
                                <th>@lang('messages.new_price')</th>
                                <th>Action</th>>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <select class="form-control" name="article_id[]" id="article_id">
                                <option value="{{ $data->article_id }}" selected="selected" class="form-control">{{ $data->article->name }}/{{ $data->article->specification }}</option>
                                </select></td>  
                                <td><input type="text" name="quantity[]" value="{{$data->quantity }}" class="form-control" readonly="readonly" /></td>  
                                <td><input type="text" name="unit[]" value="{{$data->unit }}" class="form-control" readonly="readonly" /></td>
                                <td><input type="text" name="unit_price[]" value="{{ $data->article->unit_price }}" class="form-control" readonly="readonly"/></td>
                                <td><input type="text" name="category[]" value="{{ $data->article->category->name }}" class="form-control" readonly="readonly"/></td>  
                                <td><input type="number" name="new_quantity[]" value="{{ $data->new_quantity }}" class="form-control" /></td>
                                <td><input type="number" name="new_price[]" value="{{ $data->article->unit_price }}" class="form-control" step="any" min="0" /></td> 
                                <td><button type="button" class="btn btn-danger remove-tr"><i class="fa fa-trash-o" aria-hidden='false'></i>&nbsp;Supprimer</button></td>     
                            </tr> 
                            @endforeach 
                        </table> 
                        <div class="col-lg-12">
                            <label for="description"> @lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description">
                              {{ $inventory->description }}
                            </textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">@lang('messages.update')</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection