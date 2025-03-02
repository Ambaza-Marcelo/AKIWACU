<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             width: auto;
             text-align: center;
             font-size: 12px;
        }
        .signature{
            display: flex;
        }
    </style>
</head>

<body>
<div>
    <div>
        <div>
            <div>
               <div>
                   <img src="img/eden_logo.png" width="200" height="65">
                </div>
                <div>
                    <div style="float: left;">
                          <small> &nbsp;&nbsp;{{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->rue}}</small><br>
                          <small>&nbsp;&nbsp;NIF : {{$setting->nif}}-RC : {{$setting->rc}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small><br>
                    </div>
                    <br><br><br>
                    <br>
                    <div>
                        <h2 style="text-align: center;text-decoration: underline;">JOURNAL DE VENTES(BOISSON) DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </h2>
                    </div>
                    <br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <caption>LES CASH</caption>
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">No Commande</th>
                                    <th width="10%">No Facture</th>
                                    <th width="10%">Nom du Client</th>
                                    <th width="10%">Libellé</th>
                                    <th width="10%">Quantité</th>
                                    <th width="10%">PV HTVA</th>
                                    <th width="10%">TVA</th>
                                    <th width="20%">TTC</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y') }}</td>
                                    <td>@if($data->drink_order_no){{ $data->drink_order_no }} @endif</td>
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>@if($data->client_id){{ $data->client->customer_name }} @else {{ $data->customer_name }} @endif</td>
                                    <td>{{ $data->drink->name }}</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_price_nvat,3,',',' ') }}</td>
                                    <td>{{ number_format($data->vat,3,',',' ') }}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7">Total Cash</th>
                                    <th>{{ number_format($total_item_price_nvat,3,',',' ') }}</th>
                                    <th>{{ number_format($total_vat,3,',',' ') }}</th>
                                    <th>{{ number_format($total_amount,0,',',' ') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <caption>LES CREDITS</caption>
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">No Commande</th>
                                    <th width="10%">No Facture</th>
                                    <th width="10%">Nom du Client</th>
                                    <th width="10%">Libellé</th>
                                    <th width="10%">Quantité</th>
                                    <th width="10%">PV HTVA</th>
                                    <th width="10%">TVA</th>
                                    <th width="20%">TTC</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($credits as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y') }}</td>
                                    <td>{{ $data->drink_order_no }}</td>
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>@if($data->client_id){{ $data->client->customer_name }} @else {{ $data->customer_name }} @endif</td>
                                    <td>{{ $data->drink->name }}</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_price_nvat,3,',',' ') }}</td>
                                    <td>{{ number_format($data->vat,3,',',' ') }}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7">Total Credit</th>
                                    <th>{{ number_format($total_item_price_nvat_credit,3,',',' ') }}</th>
                                    <th>{{ number_format($total_vat_credit,3,',',' ') }}</th>
                                    <th>{{ number_format($total_amount_credit,0,',',' ') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
            </div>
            <div style="display: flex;">
                        <div style="float: left;">
                            &nbsp;&nbsp;Nom et signature
                            <div>
                                &nbsp;&nbsp;
                            </div>
                        </div>

                        <div style="float: center;margin-left: 65%;">
                            &nbsp;&nbsp;Nom et signature
                            <div>
                            &nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
        </div>
    </div>
</div>
</body>
</html>

