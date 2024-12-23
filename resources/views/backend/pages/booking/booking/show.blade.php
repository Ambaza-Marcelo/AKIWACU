
@extends('backend.layouts.master')

@section('title')
@lang('detail pour la reservation') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('detail pour la reservation')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><span>@lang('messages.list')</span></li>
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
                    <h4 class="header-title float-left">Details sur la fiche de reservation : {{$booking_no}}</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <form action="{{ route('admin.booking-salles.store') }}" method="post" id="dynamic_form">
                        @csrf
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date">@lang('messages.date')</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $data->date }}">
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="booking_client_id">@lang('Nom du Client')</label>
                            <input type="text" name="" class="form-control" value="{{ $data->booking_client_id }}">
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" id="dynamicDiv">
                        <div class="form-group">
                            <label for="nom_referent">@lang('nom referent')</label>
                            <input type="text" class="form-control" id="nom_referent" name="nom_referent" value="{{ $data->telephone_referent }}">
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="telephone_referent">@lang('telephone referent')</label>
                            <input type="tel" class="form-control" value="{{ $data->telephone_referent }}" id="telephone_referent" name="telephone_referent">
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="courriel_referent">@lang('courriel referent')</label>
                            <input type="tel" class="form-control" value="{{ $data->courriel_referent }}" id="courriel_referent" name="courriel_referent">
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="type_evenement">@lang('Type Evenement')</label>
                            <select class="form-control" name="type_evenement">
                                <option disabled="disabled" selected="selected">Merci de choisir client</option>
                                <option value="1">CONFERENCES</option>
                                <option value="2">REUNIONS</option>
                                <option value="3">AUTRES</option>
                            </select>
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_personnes">@lang('Nombre de Personnes')</label>
                            <input type="number" class="form-control" value="{{ $data->nombre_personnes }}" id="nombre_personnes" name="nombre_personnes">
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date_debut">@lang('Date de debut')</label>
                            <input type="datetime-local" class="form-control" id="date_debut" name="date_debut" value="{{ $data->date_debut }}">
                        </div>
                        </div>
                        <div class="col-md-6" id="dynamicDiv">
                        <div class="form-group">
                            <label for="date_fin">@lang('Date de fin')</label>
                            <input type="datetime-local" class="form-control" id="date_fin" value="{{ $data->date_fin }}" name="date_fin">
                        </div>
                        </div>
                    </div>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.quantity')</th>
                                <th>Besoin Technique ?</th>
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td> <input type="text" class="form-control" name="" value="@if($data->salle_id){{ $data->salle->name }} @elseif($data->service_id) {{ $data->service->name }} @elseif($data->breakfast_id) {{ $data->breakFast->name }} @elseif($data->swiming_pool_id) {{ $data->swimingPool->name }} @elseif($data->kidness_space_id) {{ $data->kidnessSpace->name }} @elseif($booking->room_id) {{ $booking->room->name }} @else {{ $data->table->name }}  @endif"> </td>  
                                <td><input type="number" name="quantity[]" value="{{ $data->quantity }}" class="form-control" /></td>
                                <td>
                                    <ol>
                                        @foreach ($techniques as $technique)
                                                <li>{{ $technique->technique->name }}</li>
                                        @endforeach
                                    </ol>
                                </td>   
                            </tr>
                            @endforeach
                        </table>
                        <div class="col-lg-12">
                            <label for="description">@lang('messages.description')</label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description">
                                {{ $data->description }}
                            </textarea>
                        </div>
                    </form>
                    </div>
                </div>
                <div>
                        <a href="{{ route('admin.bookings.generatepdf',$booking_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                    </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
@endsection


@section('scripts')
     <!-- Start datatable js -->
     <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
     <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
     <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
     <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
     <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
     
     <script>
         /*================================
        datatable active
        ==================================*/
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                responsive: true
            });
        }

     </script>
@endsection