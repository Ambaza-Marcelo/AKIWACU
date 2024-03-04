
@extends('backend.layouts.master')

@section('title')
@lang('Chiffre d\'affaires') - @lang('messages.admin_panel')
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('Chiffre d\'affaires')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('Chiffre d\'affaires')</span></li>
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
    <br>
                    <form action="{{ route('admin.exporter-en-excel-chiffre-affaire')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-primary">Chiffre Affaire En Excel</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </p>
                    </form><br>
                    <form action="{{ route('admin.exporter-en-excel-credit')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-primary">Credit En Excel</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </p>
                    </form><br>
                    <form action="{{ route('admin.exporter-en-excel-cash')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-primary">Cash En Excel</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </p>
                    </form><br>
                    <form action="{{ route('admin.exporter-facture-encours')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-primary">Exporter Facture Encours</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </p>
                    </form><br>
                    <form action="{{ route('admin.exporter-facture-annule')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-primary">Exporter Facture Annule</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </p>
                    </form><br>
                    <form action="{{ route('admin.exporter-chiffre-affaire')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-info">Exporter En PDF</button>
                        </p>
                        <p class="float-right mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </p>
                    </form><br>
    <br>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                CASH PV HTVA
                                <h2>
                                    {{ number_format($total_item_price_nvat,3,',',' ')}}
                                </h2>
                            </div>
                    </div>
                </div><br>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                CASH TVA
                                <h2>
                                    {{ number_format($total_vat,3,',',' ')}} 
                                </h2>
                            </div>
                    </div>
                </div><br>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                CASH TTC
                                <h2>
                                    {{ number_format($item_total_amount,3,',',' ')}}
                                </h2>
                            </div>
                    </div>
                </div><br>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                CREDIT PV HTVA
                                <h2>
                                    {{ number_format($total_item_price_nvat_credit,3,',',' ')}}
                                </h2>
                            </div>
                    </div>
                </div><br>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                CREDIT TVA
                                <h2>
                                    {{ number_format($total_vat_credit,3,',',' ')}} 
                                </h2>
                            </div>
                    </div>
                </div><br>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                CREDIT TTC
                                <h2>
                                    {{ number_format($item_total_amount_credit,3,',',' ')}}
                                </h2>
                            </div>
                    </div>
                </div><br>
            </div>
        </div>
        <h3>TOP 10 DES PRODUITS LES PLUS VENDUS</h3>
        <div class="row">
            @foreach($datas as $data)
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                               {{ $loop->index +1 }}. @if($data->drink_id){{ $data->drink->name }} @elseif($data->food_item_id){{ $data->foodItem->name }} @elseif($data->bartender_item_id){{ $data->bartenderItem->name }} @elseif($data->barrist_item_id) {{ $data->barristItem->name }} @elseif($data->salle_id) {{ $data->salle->name }} @elseif($data->service_id) {{ $data->service->name }} @endif
                                <h2>
                                    {{ number_format($data->item_total_amount,0,',',' ')}}
                                </h2>
                            </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
</div>
@endsection