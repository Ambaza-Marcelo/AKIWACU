
@extends('backend.layouts.master')

@section('title')
@lang('Modifier Responsable') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Modifier Responsable')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Modifier Responsable')</span></li>
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
                    <h4 class="header-title">Modifier Responsable</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.staff_members.update',$staff_member->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Date debut</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $staff_member->start_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Date debut</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $staff_member->end_date }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Nom</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $staff_member->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="position_id">Position<span class="text-danger"></span></label>
                                        <select class="form-control" name="position_id" id="position_id">
                                            <option disabled= "disabled" selected="selected">merci de choisir</option>
                                            @foreach($positions as $position)
                                            <option value="{{ $position->id }}" class="form-control">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="total_amount_authorized">Montant Total</label>
                                        <input type="number" class="form-control" id="total_amount_authorized" name="total_amount_authorized" value="{{ $staff_member->total_amount_authorized }}" min="0" required>
                                    </div>
                                </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection