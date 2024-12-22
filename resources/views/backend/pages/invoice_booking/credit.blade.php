
@extends('backend.layouts.master')

@section('title')
@lang('Validation Credit de facture') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('Validation Credit de facture')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('ebms_api.invoices.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('Validation Credit de facture')</span></li>
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
                    <h4 class="header-title">Validation Credit Facture : {{$facture->invoice_number}}</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="@if($facture->drink_order_no){{ route('admin.facture-boisson.valider-credit', $facture->invoice_number) }} @elseif($facture->food_order_no){{ route('admin.facture-cuisine.valider-credit', $facture->invoice_number) }} @elseif($facture->barrist_order_no){{ route('admin.facture-barrist.valider-credit', $facture->invoice_number) }} @elseif($facture->bartender_order_no){{ route('admin.facture-bartender.valider-credit', $facture->invoice_number) }} @endif" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-md-12">
                                <label for="employe_id">Serveur</label>
                                <select class="form-control" name="employe_id" id="employe_id">
                                <option disabled="disabled">Merci de choisir un Serveur</option>
                                <option value="{{$facture->employe_id}}" selected="selected">{{$facture->employe->name}}</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="invoice_number">Numero Facture</label>
                                <input type="text" value="{{ $facture->invoice_number }}" name="invoice_number" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_name">Nom et Prenom</label>
                                <input type="text" value="{{ $facture->tp_name }}" name="tp_name" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_TIN">NIF Contribuable</label>
                                <input type="text" value="{{ $facture->tp_TIN }}" name="tp_TIN" class="form-control" readonly>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_trade_number">RC du Contribuable</label>
                                <input type="text" value="{{ $facture->tp_trade_number }}" name="tp_trade_number" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_phone_number">Tel. du Contribuable</label>
                                <input type="text" value="{{ $facture->tp_phone_number }}" name="tp_phone_number" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_province">Province</label>
                                <input type="text" value="{{ $facture->tp_address_province }}" name="tp_address_province" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                            <div class="form-group">
                            <label for="client_id">@lang('Nom du Client')</label>
                            <select class="form-control" name="client_id">
                                <option disabled="disabled" selected="selected">Merci de choisir client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->customer_name }}/{{ $client->telephone }}</option>
                            @endforeach
                            </select>
                            </div>
                            </div>
                        </div>
                        <br>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Article</th>
                                <th>Quantite</th>
                                <th>Prix Unitaire</th>
                                <th>TC</th>
                                <th>PFL</th>
                                <th>Prix HTVA </th>
                                <th>Montant TVA </th>
                                <th>P.V TVAC </th>
                                <th>P.V total </th>
                               <!-- <th>Action</th> -->
                            </tr>
                            @foreach($datas as $data)
                            <tr>  
                                <td><input type="text" name="item_designation[]" value="@if($data->salle_id){{ $data->salle->name }} @elseif($data->service_id){{ $data->service->name }} @elseif($data->breakfast_id) BREAKFAST @elseif($data->swiming_pool_id) {{ $data->swimingPool->name }} @elseif($data->kidness_space_id) {{ $data->kidnessSpace->name }} @else{{ $data->table->name }} @endif" class="form-control" readonly /></td>  
                                <td><input type="text" step='any' min='0' name="item_quantity[]" value="{{ $data->item_quantity }}" class="form-control" readonly /></td>  
                                <td><input type="text" step='any' min='0' name="item_price[]" value="{{ $data->item_price }}" class="form-control" readonly /></td>
                                <td><input type="text" step='any' min='0' name="item_ct[]" value="{{ $data->item_ct }}" class="form-control" readonly/></td>   
                                <td><input type="text" step='any' min='0' name="item_tl[]" value="{{ $data->item_tl }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="item_price_nvat[]" value="{{ $data->item_price_nvat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="vat[]" value="{{ $data->vat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="item_price_wvat[]" value="{{ $data->item_price_wvat }}" class="form-control" readonly/></td>
                                <td><input type="text" step='any' min='0' name="Item_total_amount[]" value="{{ $data->item_total_amount }}" class="form-control" readonly/></td>
                                <!--<td><button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" title="Ajouter Plus" aria-hidden="false"></i></button></td> -->
                            </tr> 
                            @endforeach
                        </table> 
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">VALIDER AVEC CREDIT(N'OUBLIE PAS NOM DU CLIENT)</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
        
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
    var i = 0;
       
    $("#add").click(function(){
   
        ++i;

         var markup = "<tr>"+
                        "<td>"+
                          "<input type='text' name='item_designation' placeholder='Article' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' step='any' min='0' name='item_quantity' placeholder='Quantite' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' step='any' min='0' name='item_price' placeholder='Prix' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' step='any' min='0' name='item_ct' placeholder='TC' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<input type='number' step='any' min='0' name='item_tl' placeholder='PFL' class='form-control' />"+
                        "</td>"+
                        "<td>"+
                          "<button type='button' class='btn btn-danger remove-tr'><i class='fa fa-trash-o' title='Supprimer la ligne' aria-hidden='false'></i></button>"+
                        "</td>"+
                    "</tr>";
   
        $("#dynamicTable").append(markup);
    });
   
    $(document).on('click', '.remove-tr', function(){  
         $(this).parents('tr').remove();
    }); 

    //one checked box in checkbox group of invoice type

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('invoice_type'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('invoice_type'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of tp_type

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('tp_type'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('tp_type'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of assujeti a la taxe de consommation

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('ct_taxpayer'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('ct_taxpayer'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of tp_fiscal_center

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('tp_fiscal_center'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('tp_fiscal_center'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of payment_type

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('payment_type'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('payment_type'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of invoice_currency

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('invoice_currency'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('invoice_currency'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })

    //one checked box in checkbox group of cancelled_invoice

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('cancelled_invoice'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('cancelled_invoice'),(item)=>{
    item.checked=false;
    })
    e.target.checked=true;

    }
    })


</script>
@endsection