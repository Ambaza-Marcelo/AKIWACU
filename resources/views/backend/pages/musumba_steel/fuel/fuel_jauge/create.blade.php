
@extends('backend.layouts.master')

@section('title')
@lang('Nouvelle valeur jauge') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Nouvelle valeur jauge')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.ms-fuel_jauges.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Nouvelle valeur jauge')</span></li>
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
                    
                    <form action="{{ route('admin.ms-fuel_jauges.store') }}" method="POST">
                        @csrf
                <table class="table table-bordered" id="dynamicTable">  
                            <tr class="bg-secondary">
                                <th>Quantité</th>
                                <th>Valeur</th>
                                <th>Action</th>
                            </tr>
                            <tr class="bg-warning">    
                                <td><input type="text" class="form-control" id="quantite" name="quantite" placeholder="Entrer Quantité"></td>
                                <td><input type="text" class="form-control" id="valeur" name="valeur" placeholder="Entrer valeur"></td> 
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
