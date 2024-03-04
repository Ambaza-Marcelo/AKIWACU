
@extends('backend.layouts.master')

@section('title')
@lang('Index') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Index')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-fuel-index-pumps.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Nouveau Index')</span></li>
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
                    <h4 class="header-title">Nouveau Index</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.sotb-fuel-index-pumps.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" class="form-control" id="date" name="date">
                                </div>
                                <div class="form-group">
                                    <label for="start_index">Index de Départ</label>
                                    <input type="number" class="form-control" id="start_index" name="start_index"  step="any" min="0" placeholder="Entrer Index de Départ">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="end_index">Index de Fin</label>
                                <input type="number" class="form-control" id="end_index" name="end_index"  step="any" min="0" placeholder="Enter Index de Fin">
                                </div>
                            </div>
                        </div>                   
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Enregister</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
