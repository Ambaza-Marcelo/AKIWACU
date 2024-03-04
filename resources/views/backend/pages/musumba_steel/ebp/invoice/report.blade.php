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
                          <small> &nbsp;&nbsp;NTAHANGWA-NGAGARA</small><br>
                          <small>&nbsp;&nbsp;AVENUE DE L'AFRIQUE</small><br>
                          <small>&nbsp;&nbsp;NIF : 4000387391-RC : 02664</small><br>
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
                                    <th width="10%">Nom du Client</th>
                                    <th width="10%">NIF du Client</th>
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
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>@if($data->client_id){{ $data->client->customer_name }} @else {{ $data->customer_name }} @endif</td>
                                    <td>@if($data->client_id){{ $data->client->customer_TIN }} @else {{ $data->customer_TIN }} @endif</td>
                                    <td>{{ $data->article->name }}</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_price,0,',',' ') }}</td>
                                    <td>{{ number_format($data->vat,0,',',' ') }}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7">Total BIF</th>
                                    <th>{{ number_format($total_item_price_nvat,0,',',' ') }}</th>
                                    <th>{{ number_format($total_vat,0,',',' ') }}</th>
                                    <th>{{ number_format($total_amount_bif,0,',',' ') }}</th>
                                </tr>
                            </tfoot>
                            <tfoot>
                                <tr>
                                    <th colspan="9">Total USD</th>
                                    <th>{{ number_format($total_amount_usd,0,',',' ') }}</th>
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

