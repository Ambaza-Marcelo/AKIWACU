
@extends('backend.layouts.master')

@section('title')
@lang('Nouvel Accompagnement') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Nouvel Accompagnement')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.accompagnements.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Nouvel Accompagnement')</span></li>
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
                    <h4 class="header-title">Nouvel Accompagnement</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.accompagnements.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nom Accompagnement</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nom Accompagnement">
                        </div>
                        
                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary mt-4 pr-4 pl-4">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection