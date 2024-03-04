
@extends('backend.layouts.master')

@section('title')
@lang('modifier chauffeur') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('modifier chauffeur')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.ms-drivers.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('modifier chauffeur')</span></li>
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
                <div class="row">
                <div class="card-body">
                @include('backend.layouts.partials.messages')
                <form action="{{ route('admin.ms-drivers.update',$fuel_driver->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Nom</th>
                                <th>Prenom</th>
                                <th>Téléphone</th>
                                <th>E-mail</th>
                                <th>Action</th>
                            </tr>
                            <tr>    
                                <td><input type="text" class="form-control" id="firstname" name="firstname" value="{{$fuel_driver->firstname}}"></td>
                                <td><input type="text" class="form-control" id="lastname" name="lastname" value="{{$fuel_driver->lastname}}"></td>
                                <td><input type="text" class="form-control" id="telephone" name="telephone" value="{{$fuel_driver->telephone}}"></td> 
                                <td><input type="mail" class="form-control" id="email" name="email" value="{{$fuel_driver->email}}"></td>   
                                <td><button type="submit" class="btn btn-primary">Modifier</button></td>  
                            </tr>  
                        </table>      
                    </form>
                </div>
            </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
