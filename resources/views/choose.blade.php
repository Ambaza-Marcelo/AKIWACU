<!DOCTYPE html>
<html>
<head>
    <title>MENU EDEN GARDEN RESORT</title>
<link rel="shortcut icon" type="image/png" href="{{ asset('backend/assets/images/icon/favicon.ico') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/metisMenu.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/slicknav.min.css') }}">
<!-- amchart css -->
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<!-- others css -->
<link rel="stylesheet" href="{{ asset('backend/assets/css/typography.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/default-css.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/responsive.css') }}">
<!-- modernizr css -->
<script src="{{ asset('backend/assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>
</head>
<body>
    <div class="row">
        <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg1">
                        <a href="{{ route('menu-boissons') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/eden_logo1.png') }}" width="200">

                                    @lang('MENU BOISSONS')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div>
        </div>
        <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg1">
                        <a href="{{ route('menu-cuisine') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/eden_logo1.png') }}" width="200">

                                    @lang('MENU CUISINE')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
    </div><br>
    <div class="row">
            <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg1">
                        <a href="{{ route('menu-barrista') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/eden_logo1.png') }}" width="200">

                                    @lang('MENU BARRISTA')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div>
        </div>
        <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg1">
                        <a href="{{ route('menu-eden') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/eden_logo1.png') }}" width="200">

                                    @lang('MENU EDEN GARDEN')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
    </div><br>
    <div class="row">
        <div class="col-md-6 mb-3 mb-lg-0">
                <div class="card">
                    <div class="seo-fact sbg1">
                        <a href="{{ route('salle-conferences') }}">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon">
                                    <img src="{{ asset('img/eden_logo1.png') }}" width="200">

                                    @lang('SALLES DE CONFERENCES')</div>
                                <h2>
                                </h2>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
    </div>
</body>
</html>
        