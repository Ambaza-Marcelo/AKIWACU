
@extends('backend.layouts.master')

@section('title')
@lang('messages.dashboard') - @lang('messages.admin_panel')
@endsection


@section('admin-content')
<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang('messages.dashboard')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{url('/404/muradutunge/ivyomwasavye-ntibishoboye-kuboneka')}}">@lang('messages.home')</a></li>
                    <li><span>@lang('messages.dashboard')</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

@if (Auth::guard('admin')->user()->can('food_order_client.view') || Auth::guard('admin')->user()->can('drink_order_client.view') || Auth::guard('admin')->user()->can('material_big_store.view') || Auth::guard('admin')->user()->can('drink_big_store.view') ||
Auth::guard('admin')->user()->can('drink_small_store.view') || 
Auth::guard('admin')->user()->can('food_big_store.view') || Auth::guard('admin')->user()->can('invoice_drink.view'))
<div class="main-content-inner">
  <div class="row">
    <div class="col-md-2" id="side-navbar">
    </div>

    <div class="col-lg-12"> 
        <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <canvas id="canvas" height="280" width="500"></canvas>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    </div>
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.barrist-orders.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_beer-006.svg') }}" width="200">

                                    @lang('Commande Barrist')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.bartender-orders.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_beer-006.svg') }}" width="200">

                                    @lang('Commande Bartender')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg5">
                        <a href="{{ route('admin.order_drinks.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_barista_at0v.svg') }}" width="200">

                                    @lang('Commande Boissons')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg6">
                        <a href="{{ route('admin.order_kitchens.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_eating_together-004.svg') }}" width="200">

                                    @lang('Commande Cuisine')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg4">
                        <!--
                        <a href="{{ route('admin.booking-kidness-space.index') }}">
                        -->
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_toy_car_-7-umw.svg') }}" width="100">

                                    @lang('Reservation Kidness Space')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg4">
                        <!--
                        <a href="{{ route('admin.booking-swiming-pool.index') }}">
                        -->
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/piscine1.jpg') }}" width="100">

                                    @lang('Reservation Piscine')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg4">
                        <a href="{{ route('admin.barrist-invoices.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_beer-006.svg') }}" width="200">

                                    @lang('Vente Barrist')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                <div class="seo-fact sbg8">
                        <a href="{{ route('ebms_api.invoices.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_special_event-001.svg') }}" width="200">

                                    @lang('Vente Boissons')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg4">
                        <a href="{{ route('admin.invoice-kitchens.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_breakfast-005.svg') }}" width="200">

                                    @lang('Vente Cuisine')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.bartender-invoices.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_beer-006.svg') }}" width="200">

                                    @lang('Vente Bartender')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg4">
                        <a href="{{ route('admin.booking-breakfast.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_special_event-001.svg') }}" width="100">

                                    @lang('RESERVATION BREAKFAST')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg4">
                        <a href="{{ route('admin.booking-invoices.choose') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_polaroid_re_481f.svg') }}" width="100">

                                    @lang('Vente Reservation')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.drink-requisitions.index') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_beer-006.svg') }}" width="100">

                                    @lang('Requisition Boissons')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
    </div><br>
    @if (Auth::guard('admin')->user()->can('drink_purchase.view') || Auth::guard('admin')->user()->can('food_purchase.view') || Auth::guard('admin')->user()->can('material_purchase.view'))
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.plan-purchase.choice') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_empty_cart_co35.svg') }}" width="100">
                                </div>
                                <h2>
                                    LES PLANNINGS D'APPROVISIONNEMENT 
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
        </div>
        @endif
    @if (Auth::guard('admin')->user()->can('invoice_booking.view'))
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.credit-invoices.list') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_special_event-001.svg') }}" width="100">
                                </div>
                                <h2>
                                    Credits 
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
            <!--
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.credit-payes.list') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_special_event-001.svg') }}" width="100">
                                </div>
                                <h2>
                                    Credits Payes 
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
        -->
            <div class="col-md-6">
                <div class="card">
                    <div class="seo-fact sbg3">
                        <a href="{{ route('admin.voir-chiffre-affaires') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_resume_folder_re_e0bi.svg') }}" width="100">
                                </div>
                                <h2>
                                    DOSSIER DES RAPPORTS
                                </h2>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
        </div>
        @endif
    @if (Auth::guard('admin')->user()->can('employe.edit'))
    <div class="row">
        <h2>TOP 5 DES SERVEURS QUI PROVOQUE L'ANNULATION DES FACTURES </h2>
        @foreach($employes as $employe)
            <div class="col-md-4">
                <div class="card">
                    <div class="seo-fact sbg3">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/undraw_raining_re_4b55.svg') }}" width="100">

                                    {{$loop->index + 1}}.@if($employe->employe_id){{ $employe->employe->name }} @endif</div>
                                <h2>
                                    {{ $employe->invoice_number }}
                                </h2>
                            </div>
                    </div>
                </div><br>
            </div>
        @endforeach
    </div>
    @endif
  <!-- ambaza marcellin -pink -->
</div>
@endif
@if (Auth::guard('admin')->user()->can('dashboard.view'))
<script type="text/javascript">
    var year = <?php echo $year; ?>;
    var drink_extra_big_store = <?php echo $drink_extra_big_store; ?>;
    var drink_big_store = <?php echo $drink_big_store; ?>;
    var drink_small_store = <?php echo $drink_small_store; ?>;
    var food_extra_big_store = <?php echo $food_extra_big_store; ?>;
    var food_big_store = <?php echo $food_big_store; ?>;
    var food_small_store = <?php echo ($food_small_store); ?>;
    var material_extra_big_store = <?php echo $material_extra_big_store; ?>;
    var material_big_store = <?php echo $material_big_store; ?>;
    var material_small_store = <?php echo $material_small_store; ?>;
    var barrist_store = <?php echo $barrist_store; ?>;

    var barChartData = {
        labels: year,
        datasets: [
        {
            label: 'GRAND STOCK(BOISSONS)',
            backgroundColor: "red",
            data: 0
        },
        {
            label: 'STOCK INTERMEDIAIRE(BOISSONS)',
            backgroundColor: "#077D92",
            data: drink_big_store
        },
        {
            label: 'PETIT STOCK(BOISSONS)',
            backgroundColor: "pink",
            data: drink_small_store
        },
        {
            label: 'GRAND STOCK(NOURRITURES)',
            backgroundColor: "red",
            data: food_extra_big_store
        },
        {
            label: 'STOCK INTERMEDIAIRE(NOURRITURES)',
            backgroundColor: "#077D92",
            data: 0
        },
        {
            label: 'PETIT STOCK(NOURRITURES',
            backgroundColor: "pink",
            data: 0
        },
        {
            label: 'GRAND STOCK(MATERIELS)',
            backgroundColor: "red",
            data: 0
        },
        {
            label: 'STOCK INTERMEDIAIRE(MATERIELS)',
            backgroundColor: "#077D92",
            data: material_big_store
        },
        {
            label: 'PETIT STOCK(MATERIELS)',
            backgroundColor: "pink",
            data: material_small_store
        },
        /*
        {
            label: 'Barrist Store',
            backgroundColor: "yellow",
            data: barrist_store
        }*/

        ]
    };

    window.onload = function() {
        var ctx = document.getElementById("canvas").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: '#c1c1c1',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                title: {
                    display: true,  
                    text: 'STATISTIQUE DES STOCKS'
                }
            }
        });
    };
</script>
@endif
@endsection