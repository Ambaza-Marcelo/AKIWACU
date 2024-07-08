
@extends('backend.layouts.master')

@section('title')
@lang('DOSSIER DES RAPPORTS') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('DOSSIER DES RAPPORTS')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('DOSSIER DES RAPPORTS')</span></li>
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
                <h2>RAPPORT CHIFFRE D'AFFAIRES</h2>
                    <form action="{{ route('admin.exporter-en-excel-chiffre-affaire')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-info">Chiffre Affaire En Excel</button>
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
                            <button type="submit" value="pdf" class="btn btn-success">Cash En Excel</button>
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
                            <button type="submit" value="pdf" class="btn btn-warning">Exporter Facture Encours</button>
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
                            <button type="submit" value="pdf" class="btn btn-danger">Exporter Facture Annule</button>
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
                            <button type="submit" value="pdf" class="btn btn-warning">Exporter Synthese C.A CREDIT/CASH</button>
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
                    <form action="{{ route('admin.exporter-en-excel-credits')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-warning">Exporter R. Recouvrement</button>
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
                    <h2>RAPPORT DES COMMANDES</h2>
                    <form action="{{ route('admin.food-orders.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">NOURRITURES</button>
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
                    <form action="{{ route('admin.drink-orders.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">BOISSONS</button>
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
                    <form action="{{ route('admin.barrist-orders.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">BARRISTA</button>
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
                    <form action="{{ route('admin.bartender-orders.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">BARTENDER</button>
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
                    <h2>RAPPORT DES STOCKS</h2>
                    <form action="{{ route('admin.drink-small-store-report.export-to-excel')}}" method="GET">
                        <table>
                            <tr>
                                <th>Date Debut</th>
                                <th>Date Fin</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </td>
                                <td>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </td>
                                <td>
                                    <select class="form-control" name="code_store" id="code_store">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($drinksmstores as $store)
                                            <option value="{{$store->code}}">{{$store->code}}/{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" value="pdf" class="btn btn-success" title="Cliquer pour exporter en PDF">Exporter R. Petit Stock Boissons</button>
                                </td>
                            </tr>
                        </table>
                    </form><br>
                    <form action="{{ route('admin.drink-big-store-report.export-to-excel')}}" method="GET">
                        <table>
                            <tr>
                                <th>Date Debut</th>
                                <th>Date Fin</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </td>
                                <td>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </td>
                                <td>
                                    <select class="form-control" name="code_store" id="code_store">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($drinkbgstores as $store)
                                            <option value="{{$store->code}}">{{$store->code}}/{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" value="pdf" class="btn btn-success" title="Cliquer pour exporter en PDF">Exporter R. Stock Intermediaire Boissons</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <form action="{{ route('admin.food-big-store-report.export-to-excel')}}" method="GET">
                        <table>
                            <tr>
                                <th>Date Debut</th>
                                <th>Date Fin</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </td>
                                <td>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </td>
                                <td>
                                    <select class="form-control" name="code_store" id="code_store">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($foodbgstores as $store)
                                            <option value="{{$store->code}}">{{$store->code}}/{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" value="pdf" class="btn btn-success" title="Cliquer pour exporter en PDF">Exporter R. Stock Nourritures</button>
                                </td>
                            </tr>
                        </table>
                    </form><br>
                    <form action="{{ route('admin.material-big-store-report.export-to-excel')}}" method="GET">
                        <table>
                            <tr>
                                <th>Date Debut</th>
                                <th>Date Fin</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" name="start_date" class="form-control" id="start_date">
                                </td>
                                <td>
                                    <input type="date" name="end_date" class="form-control" id="end_date">
                                </td>
                                <td>
                                    <select class="form-control" name="code_store" id="code_store">
                                        <option disabled="disabled" selected="selected">Merci de choisir</option>
                                        @foreach($materialbgstores as $store)
                                            <option value="{{$store->code}}">{{$store->code}}/{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" value="pdf" class="btn btn-success" title="Cliquer pour exporter en PDF">Exporter R. Stock Materiel</button>
                                </td>
                            </tr>
                        </table>
                    </form><br>
                    <h2>RAPPORT DES ACHATS</h2>
                    <form action="{{ route('admin.drink-receptions.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">Exporter R. Boissons</button>
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
                    <form action="{{ route('admin.food-receptions.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">Exporter R. Nourritures</button>
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
                    <form action="{{ route('admin.material-receptions.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">Exporter R. Materiels</button>
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
                    <h2>RAPPORT STOCK PDG</h2>
                    <form action="{{ route('admin.private-drink-stockouts.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">Exporter R. SORTIES</button>
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
                    <form action="{{ route('admin.private-drink-stockins.export-to-excel')}}" method="GET">
                        <p class="float-right mb-2">
                            <button type="submit" value="pdf" class="btn btn-success">Exporter R. ENTREES</button>
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

</div>
@endsection