
@extends('backend.layouts.master')

@section('title')
@lang('messages.drinks') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('messages.drinks')</h4>
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
                    <h4 class="header-title float-left">@lang('messages.drinks')</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('drink.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.drinks.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('messages.code')</th>
                                    <th width="10%">@lang('Code Store')</th>
                                    <th width="10%">@lang('messages.quantity')</th>
                                    <th width="10%">@lang('messages.unit')</th>
                                    <th width="10%">@lang('Min Store')</th>
                                    <th width="10%">@lang('messages.purchase_price')</th>
                                     <th width="10%">@lang('P. BRARUDI')</th>
                                    <th width="10%">@lang('Taux TVA')</th>
                                    <th width="10%">@lang('Taux marge')</th>
                                    <th width="10%">@lang('Taux majoration')</th>
                                    <th width="10%">@lang('Taux Reduction')</th>
                                    <th width="10%">@lang('messages.selling_price')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($drinks as $drink)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $drink->name }}</td>
                                    <td>{{ $drink->code }}</td>
                                    <td>{{ $drink->code_store }}</td>
                                    <td>{{ $drink->quantity_bottle }}</td>
                                    <td>{{ $drink->unit }}</td>
                                    <td>{{ $drink->threshold_quantity }}</td>
                                    <td>{{ number_format($drink->purchase_price,0,',',' ') }}</td>
                                    <td>{{ number_format($drink->brarudi_price,0,',',' ') }}</td>
                                    <td>{{ $drink->vat }}%</td>
                                    <td>{{ $drink->taux_marge }}%</td>
                                    <td>{{ $drink->taux_majoration }}%</td>
                                    <td>{{ $drink->taux_reduction }}%</td>
                                    <td>{{ number_format($drink->selling_price,0,',',' ') }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('drink.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.drinks.edit', $drink->id) }}">@lang('messages.edit')</a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('drink.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.drinks.destroy', $drink->id) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $drink->id }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $drink->id }}" action="{{ route('admin.drinks.destroy', $drink->id) }}" method="POST" style="display: none;">
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