
@extends('backend.layouts.master')

@section('title')
@lang('Liste des plannings des approvisionnements des boissons') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Liste des plannings des approvisionnements des boissons')</h4>
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
                    <h4 class="header-title float-left">Liste des plannings des approvisionnements des boissons</h4>
                    <p class="float-right mb-2">
                        @if (Auth::guard('admin')->user()->can('drink_purchase.create'))
                            <a class="btn btn-primary text-white" href="{{ route('admin.plan-purchase-drinks.create') }}">@lang('messages.new')</a>
                        @endif
                    </p>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('Date debut')</th>
                                    <th width="10%">@lang('Date Fin')</th>
                                    <th width="10%">Plan No</th>
                                    <th width="10%">@lang('Plan Signature')</th>
                                    <th width="10%">@lang('messages.status')</th>
                                    <th width="30%">@lang('messages.description')</th>
                                    <th width="10%">@lang('messages.created_by')</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($plans as $plan)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ $plan->start_date }}</td>
                                    <td>{{ $plan->end_date }}</td>
                                    <td><a href="{{ route('admin.plan-purchase-drinks.show',$plan->plan_no) }}">{{ $plan->plan_no }}</a></td>
                                    <td>{{ $plan->purchase_signature }}</td>
                                    @if($plan->status == 2)
                                    <td><span  class="badge badge-success">Validé</span></td>
                                    @elseif($plan->status == -1)
                                    <td><span class="badge badge-danger">Rejeté</span></td>
                                    @elseif($plan->status == 3)
                                    <td><span class="badge badge-success">confirmé</span></td>
                                    @elseif($plan->status == 4)
                                    <td><span class="badge badge-success">Approuvé</span></td>
                                    @elseif($plan->status == 5)
                                    <td><span class="badge badge-success">Commandé</span></td>
                                    @else
                                    <td><span class="badge badge-primary">Encours...</span></td>
                                    @endif
                                    <td>{{ $plan->description }}</td>
                                    <td>{{ $plan->created_by }}</td>
                                    <td>
                                        @if (Auth::guard('admin')->user()->can('drink_purchase.create'))
                                        <a href="{{ route('admin.plan-purchase-drinks.fiche_plan',$plan->plan_no) }}"><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_purchase.validate'))
                                        @if($plan->status == 1 || $plan->status == -1)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.plan-purchase-drinks.validate', $plan->plan_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('validate-form-{{ $plan->plan_no }}').submit();">
                                                Valider
                                            </a>

                                            <form id="validate-form-{{ $plan->plan_no }}" action="{{ route('admin.plan-purchase-drinks.validate', $plan->plan_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_purchase.confirm'))
                                        @if($purchase->status == 2)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.plan-purchase-drinks.confirm', $plan->plan_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('confirm-form-{{ $plan->plan_no }}').submit();">
                                                Confirmer
                                            </a>

                                            <form id="confirm-form-{{ $plan->plan_no }}" action="{{ route('admin.plan-purchase-drinks.confirm', $plan->plan_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_purchase.approuve'))
                                        @if($plan->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.plan-purchase-drinks.approuve', $plan->plan_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('approuve-form-{{ $plan->plan_no }}').submit();">
                                                Approuver
                                            </a>

                                            <form id="approuve-form-{{ $plan->plan_no }}" action="{{ route('admin.plan-purchase-drinks.approuve', $plan->plan_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_purchase.reject'))
                                            @if($plan->status == 1 || $plan->status == 2 || $plan->status == 3)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.plan-purchase-drinks.reject', $plan->plan_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reject-form-{{ $plan->plan_no }}').submit();">
                                                Rejeter
                                            </a>
                                            @endif
                                            <form id="reject-form-{{ $plan->plan_no }}" action="{{ route('admin.plan-purchase-drinks.reject', $plan->plan_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if (Auth::guard('admin')->user()->can('drink_purchase.reset'))
                                            @if($plan->status == -1 || $plan->status == 2 || $plan->status == 3 || $plan->status == 4)
                                            <a class="btn btn-primary text-white" href="{{ route('admin.plan-purchase-drinks.reset', $plan->plan_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('reset-form-{{ $plan->plan_no }}').submit();">
                                                Annuler
                                            </a>
                                            @endif
                                            <form id="reset-form-{{ $plan->plan_no }}" action="{{ route('admin.plan-purchase-drinks.reset', $plan->plan_no) }}" method="POST" style="display: none;">
                                                @method('PUT')
                                                @csrf
                                            </form>
                                        @endif
                                        @if($plan->status == 1)
                                        @if (Auth::guard('admin')->user()->can('drink_purchase.edit'))
                                            <a class="btn btn-success text-white" href="{{ route('admin.plan-purchase-drinks.edit', $plan->plan_no) }}">@lang('messages.edit')</a>
                                        @endif
                                        @endif

                                        @if (Auth::guard('admin')->user()->can('drink_purchase.delete'))
                                            <a class="btn btn-danger text-white" href="{{ route('admin.plan-purchase-drinks.destroy', $plan->plan_no) }}"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $plan->plan_no }}').submit();">
                                                @lang('messages.delete')
                                            </a>

                                            <form id="delete-form-{{ $plan->plan_no }}" action="{{ route('admin.plan-purchase-drinks.destroy', $plan->plan_no) }}" method="POST" style="display: none;">
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