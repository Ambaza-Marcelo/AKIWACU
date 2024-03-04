
@extends('backend.layouts.master')

@section('title')
@lang('Modifier un Grade') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier un Grade')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.hr-grades.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Modifier un Grade')</span></li>
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
                    <h4 class="header-title">Modifier un Grade</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.hr-grades.update',$grade->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group has-feedback">
                                        <label for="name">Nom Grade<span class="text-danger"></span></label>
                                        <input autofocus type="text" class="form-control" name="name" value="{{ $grade->name }}" required minlength="2" maxlength="255">
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