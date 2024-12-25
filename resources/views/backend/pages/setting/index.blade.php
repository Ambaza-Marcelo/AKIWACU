
@extends('backend.layouts.master')

@section('title')
@lang('messages.setting') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.setting')</h4>
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
                    <h4 class="header-title float-left">Setting</h4>
                    <p class="float-right mb-2">
                            <a class="btn btn-primary text-white" href="{{ route('admin.settings.create') }}">@lang('messages.new')</a>
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="30%">logo</th>
                                    <th width="10%">Type Contribuable</th>
                                    <th width="10%">Assujetti à la TVA</th>
                                    <th width="10%">Assujetti au PF</th>
                                    <th width="10%">Nom</th>
                                    <th width="10%">NIF</th>
                                    <th width="10%">RC</th>
                                    <th width="10%">Centre Fiscal</th>
                                    <th width="10%">Secteur d'activite</th>
                                    <th width="10%">Forme Juridique</th>
                                    <th width="10%">Province</th>
                                    <th width="10%">Commune</th>
                                    <th width="10%">Zone</th>
                                    <th width="10%">Quartier</th>
                                    <th width="10%">Rue</th>
                                    <th width="10%">Code Postal</th>
                                    <th width="10%">Telephone 1</th>
                                    <th width="10%">Telephone 2</th>
                                    <th width="10%">Email</th>
                                    <th width="10%">Nbre de lignes</th>
                                    <th width="10%">Devloppeur</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($settings as $setting)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td> <img class="img-responsive" style="max-height: 50px;" src="{{ asset('storage/logo')}}/{{ $setting->logo }}" alt=""></td>
                                    <td>@if($setting->tp_type == 1) Personne Physique @else Société @endif</td>
                                    <td>@if($setting->vat_taxpayer == 1) OUI @else NON @endif</td>
                                    <td>@if($setting->tl_taxpayer == 1) OUI @else NON @endif</td>
                                    <td>{{ $setting->name }}</td>
                                    <td>{{ $setting->nif }}</td>
                                    <td>{{ $setting->rc }}</td>
                                    <td>{{ $setting->tp_fiscal_center }}</td>
                                    <td>{{ $setting->tp_activity_sector }}</td>
                                    <td>{{ $setting->tp_legal_form }}</td>
                                    <td>{{ $setting->province }}</td>
                                    <td>{{ $setting->commune }}</td>
                                    <td>{{ $setting->zone }}</td>
                                    <td>{{ $setting->quartier }}</td>
                                    <td>{{ $setting->rue }}</td>
                                    <td>{{ $setting->postal_number }}</td>
                                    <td>{{ $setting->telephone1 }}</td>
                                    <td>{{ $setting->telephone2 }}</td>
                                    <td>{{ $setting->email }}</td>
                                    <td>{{ $setting->max_line }}</td>
                                    <td>{{ $setting->developpeur }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('setting.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.settings.edit', $setting->id) }}">Edit</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('setting.edit'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.settings.destroy', $setting->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $setting->id }}').submit();">
                                                Delete
                                            </a>

                                            <form id="delete-form-{{ $setting->id }}" action="{{ route('admin.settings.destroy', $setting->id) }}" method="POST" style="display: none;">
                                                @method('DELETE')
                                                @csrf
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                               @endforeach
                            </tbody>
                        </table>
                    </div>
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