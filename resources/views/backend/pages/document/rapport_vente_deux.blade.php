<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             width: auto;
             text-align: center;
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
                   <img src="img/eden_logo.png" width="150" height="65">
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
                        <h2 style="text-align: center;text-decoration: underline;">RAPPORT DE VENTES DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </h2>
                    </div>
                    <br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">No Facture</th>
                                    <th width="10%">Nom du Client</th>
                                    <th width="10%">No Commande</th>
                                    <th width="10%">Quantité</th>
                                    <th width="20%">Montant Total Net HT</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y') }}</td>
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>@if($data->client_id){{ $data->client->customer_name }} @else {{ $data->customer_name }} @endif</td>
                                    <td>@if($data->drink_order_no){{ $data->drink_order_no }}<span class="badge badge-info">boisson</span> @elseif($data->food_order_no){{ $data->food_order_no }}<span class="badge badge-info">nourriture</span> @elseif($data->barrist_order_no){{ $data->barrist_order_no }}<span class="badge badge-info">barrist</span> @elseif($data->bartender_order_no){{ $data->bartender_order_no }}<span class="badge badge-info">bartender</span> @else {{ $data->booking_no }} @endif</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">No Facture</th>
                                    <th width="10%">Nom du Client</th>
                                    <th width="10%">No Commande</th>
                                    <th width="10%">Quantité</th>
                                    <th width="20%">Montant Total Net HT</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($credits as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y') }}</td>
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>{{ $data->customer_name }}</td>
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>@if($data->drink_order_no){{ $data->drink_order_no }}<span class="badge badge-info">boisson</span> @elseif($data->food_order_no){{ $data->food_order_no }}<span class="badge badge-info">nourriture</span> @elseif($data->barrist_order_no){{ $data->barrist_order_no }}<span class="badge badge-info">barrist</span> @elseif($data->bartender_order_no){{ $data->bartender_order_no }}<span class="badge badge-info">bartender</span> @else {{ $data->booking_no }} @endif</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>
</body>
</html>

