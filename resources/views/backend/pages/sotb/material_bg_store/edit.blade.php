
@extends('backend.layouts.master')

@section('title')
@lang('Edit material big store') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Edit material big store')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.sotb-material-bg-store.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Edit material big store')</span></li>
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
                    <h4 class="header-title">@lang('Edit material big store')</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.sotb-material-bg-store.update',$material_big_store->code) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('Designation')</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $material_big_store->name }}">
                                </div>
                                <div class="form-group">
                                    <label for="emplacement">@lang('Emplacement')</label>
                                    <input type="text" class="form-control" id="emplacement" name="emplacement" value="{{ $material_big_store->emplacement }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manager">@lang('Manager')</label>
                                    <input type="text" class="form-control" id="manager" name="manager" value="{{ $material_big_store->manager }}">
                                </div>
                            </div>
                        </div>
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
