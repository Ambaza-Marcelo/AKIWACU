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
                   <img src="img/logo_musumba.jpg" width="1024" height="45">
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
                        <h2 style="text-align: center;text-decoration: underline;">LISTE DES FACTURES ENVOYES A L'OBR DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </h2>
                    </div>
                    <br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">No Facture</th>
                                    <!--<th width="10%">Signature Facture </th> -->
                                    <th width="10%">Nom du Client</th>
                                    <th width="10%">NIF du Client</th>
                                    <th width="10%">Libellé</th>
                                    <th width="10%">Quantité</th>
                                    <th width="10%">PV Net HT</th>
                                    <th width="20%">Montant Total Net HT</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y') }}</td>
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>{{ $data->customer_name }}</td>
                                    <td>{{ $data->customer_TIN }}</td>
                                    <td>{{ $data->article->name }}</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_price,0,',',' ') }}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="8">Total</th>
                                    <th>{{ number_format($total_amount,0,',',' ') }}</th>
                                </tr>
                            </tfoot>
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

