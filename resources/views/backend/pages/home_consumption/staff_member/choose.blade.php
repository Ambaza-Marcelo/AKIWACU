
@extends('backend.layouts.master')

@section('title')
@lang('Thank You To Choose') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Thank You To Choose')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Thank You To Choose')</span></li>
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
                    <h4 class="header-title">@lang('Thank You To Choose')</h4>
                    @include('backend.layouts.partials.messages')
                    <div class="row">
                        @foreach($staff_members as $staff_member)
                        <div class="col-md-12 mb-3 mb-lg-0">
                            <div class="card">
                                <div class="seo-fact sbg3">
                                    @if($staff_member->total_amount_authorized > $staff_member->total_amount_consumed)
                                    <a href="{{ route('admin.staff_members.choose-type-consumption',$staff_member->id) }}">
                                        <div class="p-4 d-flex justify-content-between align-items-center">
                                            <div class="seofct-icon">
                                                @if($staff_member->id == 1)
                                                <img src="{{ asset('img/undraw_female_avatar_efig.svg') }}" width="100">
                                                @else
                                                <img src="{{ asset('img/undraw_male_avatar_g98d.svg') }}" width="100">
                                                @endif
                                                {{ $staff_member->name }}
                                            </div>
                                            @if (Auth::guard('admin')->user()->can('drink_reception.view') && Auth::guard('admin')->user()->can('drink_order_client.view'))
                                            <h4>
                                                @if($staff_member->etat == '0')
                                                <span class="badge badge-success">Montant Authorisé : {{ number_format($staff_member->total_amount_authorized,0,',',' ')}} - Montant Consommé : {{ $staff_member->total_amount_consumed}}</span>
                                                @elseif($staff_member->etat == '1')
                                                <span class="badge badge-danger">Montant Consommé : {{ number_format($staff_member->total_amount_consumed,0,',',' ') }}</span>
                                                @endif
                                            </h4>
                                            @endif
                                        </div>
                                    </a>

                                    @else
                                    <a href="#">
                                        <div class="p-4 d-flex justify-content-between align-items-center">
                                            <div class="seofct-icon">
                                                <img src="{{ asset('img/undraw_special_event-001.svg') }}" width="100">
                                                {{ $staff_member->name }}
                                            </div>
                                            <h4>
                                                @if($staff_member->etat == '0')
                                                <span class="badge badge-success">Montant Authorisé : {{ number_format($staff_member->total_amount_authorized,0,',',' ')}} - Montant Consommé : {{ $staff_member->total_amount_consumed}}</span>
                                                @elseif($staff_member->etat == '1')
                                                <span class="badge badge-danger">Montant Consommé : {{ number_format($staff_member->total_amount_consumed,0,',',' ') }}</span>
                                                @endif
                                            </h4>
                                        </div>
                                    </a>
                                    @endif
                                </div>
                            </div><br>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection
