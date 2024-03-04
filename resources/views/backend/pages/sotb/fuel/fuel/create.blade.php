
@extends('backend.layouts.master')

@section('title')
@lang('Nouveau type carburant') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Nouveau type carburant')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-fuels.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Nouveau type carburant')</span></li>
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
                    <h4 class="header-title">Nouveau</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.sotb-fuels.store') }}" method="POST">
                        @csrf
                <table class="table table-bordered" id="dynamicTable">  
                            <tr class="bg-secondary">
                                <th>Designation</th>
                                <th>Prix Achat</th>
                                <th>Action</th>
                            </tr>
                            <tr>    
                                <td><input type="text" class="form-control" id="name" name="name" placeholder="Entrer Desigantion"></td>
                                <td><input type="number" class="form-control" id="purchase_price" name="purchase_price" placeholder="Entrer Prix Unitaire"></td> 
                                <td><button type="submit" class="btn btn-primary">Enregister</button></td>  
                            </tr>  
                        </table>      
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
