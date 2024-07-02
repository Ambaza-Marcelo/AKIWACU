
@extends('backend.layouts.master')

@section('title')
@lang('facture magasin eden') - @lang('messages.admin_panel')
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
                <h4 class="page-title pull-left">@lang('facture magasin eden')</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">@lang('messages.dashboard')</a></li>
                    <li><a href="{{ route('admin.private-factures.index') }}">@lang('messages.list')</a></li>
                    <li><span>@lang('facture magasin eden')</span></li>
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
                    <h4 class="header-title">Nouveau Facture</h4>
                    @include('backend.layouts.partials.messages')
                    
                    <form action="{{ route('admin.private-factures.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label for="invoice_date">Date Facture</label>
                                <input type="date" placeholder="Date Facture" name="invoice_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="invoice_type">Type Facture</label>
                                <div class="form-group">
                                    <label class="text">F. Normale
                                    <input type="checkbox" name="invoice_type" value="FN" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">R. Caution
                                    <input type="checkbox" name="invoice_type" value="RC" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Reduction HF
                                    <input type="checkbox" name="invoice_type" value="RHF" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_type">Type Contribuable</label>
                                <div class="form-group">
                                    <label class="text">Personne Physique
                                    <input type="checkbox" name="tp_type" value="1" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Société
                                    <input type="checkbox" checked="checked" name="tp_type" value="2" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="vat_taxpayer">Assujetti à la TVA</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="vat_taxpayer" value="0" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="vat_taxpayer" value="1" checked="checked" class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
                        @if($setting)
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_name">Nom et Prenom</label>
                                <input type="text" value="{{ $setting->name }}" name="tp_name" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_TIN">NIF Contribuable</label>
                                <input type="text" value="{{ $setting->nif }}" name="tp_TIN" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_trade_number">RC du Contribuable</label>
                                <input type="text" value="{{ $setting->rc }}" name="tp_trade_number" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_postal_number">Boite Postal</label>
                                <input type="text" value="0000" name="tp_postal_number" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_phone_number">Tel. du Contribuable</label>
                                <input type="text" value="{{ $setting->telephone1 }}" name="tp_phone_number" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_province">Province</label>
                                <input type="text" value="BUJUMBURA" name="tp_address_province" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_address_commune">Commune</label>
                                <input type="text" value="{{ $setting->commune }}" name="tp_address_commune" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_quartier">Quartier</label>
                                <input type="text" value="{{ $setting->quartier }}" name="tp_address_quartier" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_avenue">Avenue</label>
                                <input type="text" value="{{ $setting->rue }}" name="tp_address_avenue" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_address_rue">Rue</label>
                                <input type="text" value="{{ $setting->rue }}" name="tp_address_rue" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_address_number">Numero</label>
                                <input type="text" value="00" name="tp_address_number" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="ct_taxpayer">Assujetti à la taxe de conso.</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="ct_taxpayer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="ct_taxpayer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tl_taxpayer">Assujetti au PFL</label>
                                <div class="form-group">
                                    <label class="text">Non Assujetti
                                    <input type="checkbox" name="tl_taxpayer" value="0" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Assujetti
                                    <input type="checkbox" name="tl_taxpayer" value="1" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_fiscal_center">Centre Fiscale</label>
                                <div class="form-group">
                                    <label class="text">DGC
                                    <input type="checkbox" name="tp_fiscal_center" value="DGC" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">DMC
                                    <input type="checkbox" checked="checked" name="tp_fiscal_center" value="DMC" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">DPMC
                                    <input type="checkbox" name="tp_fiscal_center" value="DPMC" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="tp_activity_sector">Secteur d'activité</label>
                                <input type="text" name="tp_activity_sector" class="form-control" value="HOTELERIE">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="tp_legal_form">Forme Juridique</label>
                                <input type="text" value="SA" name="tp_legal_form" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label for="payment_type">Type de Paiement</label>
                                <div class="form-group">
                                    <label class="text">Espece
                                    <input type="checkbox" name="payment_type" value="1" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Banque
                                    <input type="checkbox" name="payment_type" value="2" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Credit
                                    <input type="checkbox" name="payment_type" value="3" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">Autres
                                    <input type="checkbox" name="payment_type" value="4" class="form-control">
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="invoice_currency">Type de Monaie</label>
                                <div class="form-group">
                                    <label class="text">BIF
                                    <input type="checkbox" name="invoice_currency" value="BIF" checked="checked" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">USD
                                    <input type="checkbox" name="invoice_currency" value="USD" class="form-control">
                                    </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label class="text">EUR
                                    <input type="checkbox" name="invoice_currency" value="EUR" class="form-control">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="customer_name">@lang('Nom du Client')</label>
                                <input type="text" name="customer_name" class="form-control" value="CLIENT CASH">
                            </div>
                            </div>
                        </div>
                        <br>
                         <table class="table table-bordered" id="dynamicTable">  
                            <tr>
                                <th>Article</th>
                                <th>Quantite</th>
                                <th>Action</th>
                            </tr>
                            <tr>  
                                <td><select class="form-control" name="private_store_item_id[]" id="private_store_item_id" required>
                                <option disabled="disabled" selected="selected">merci de choisir</option>
                                @foreach($articles as $article)
                                <option value="{{ $article->id }}" class="form-control">{{ $article->name }}/{{$article->code }}</option>
                                @endforeach
                                </select></td>  
                                <td><input type="number" step='any' min='0'  name="item_quantity[]" placeholder="Quantite" class="form-control" /></td>  
                                <td>
                                    <button type="button" name="add" id="add" class="btn btn-success"><i class="fa fa-plus-square" title="Ajouter Plus" aria-hidden="false"></i></button>
                                </td> 
                            </tr> 
                        </table> 

                        <button type="submit" onclick="this.style.visibility='hidden';" ondblclick="this.style.visibility='hidden';" class="btn btn-primary">Enregistrer</button>
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
                          "<select class='form-control' name='private_store_item_id[]'"+
                            "<option>merci de choisir</option>"+
                             "@foreach($articles as $article)"+
                                 "<option value='{{ $article->id }}'>{{ $article->name }}</option>"+
                             "@endforeach>"+
                          "</select>"+
                        "</td>"+
                        "<td>"+
                        "<input type='number' step='any' min='0' name='item_quantity[]' placeholder='Quantite' class='form-control' />"+
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

    //one checked box in checkbox group of cancelled_invoice vat_taxpayer

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

    //one checked box in checkbox group of vat_taxpayer

    var group_=(el,callback)=>{
        el.forEach((checkbox)=>{
        callback(checkbox)
        })
    }

    group_(document.getElementsByName('vat_taxpayer'),(item)=>{
    item.onclick=(e)=>{
    group_(document.getElementsByName('vat_taxpayer'),(item)=>{
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


</script>
@endsection