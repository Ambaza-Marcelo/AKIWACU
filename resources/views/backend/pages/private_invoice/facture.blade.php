<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             text-align: center;
             width: auto;
        }

    </style>

<body>
<div>
    <div>
        <div>
                <div>
                        <small>
                           <strong style="text-decoration: underline;">BON D'EXPEDITION No : {{ $invoice_number }} du {{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i:s') }}</strong>
                        </small><br>
                    </div>
                    <h3>A.IDENTIFICATION DU VENDEUR</h3>  
                    <div>
                          <small>Nom du Vendeur : MAGASIN EGR</small><br>
                          <small> Adresse : {{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>Assujetti a la TVA : |oui<input type="checkbox" checked="checked">|Non<input type="checkbox"></small>
                          <hr>
                    </div>
                    <h3>B.IDENTIFICATION DU CLIENT</h3>               
                    <div>
                        <small>Nom du Client : {{ $data->customer_name }}</small> <br>
                        <small>NIF : </small> <br>
                        <small>Adresse : </small> <br>
                        <small>Assujetti a la TVA : |oui<input type="checkbox">|Non<input type="checkbox"></small><br>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>DESIGNATION</th>
                                    <th>Qtes</th>
                                    <th>PV. U</th>
                                    <th>PV. T</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->privateStoreItem->name }}</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_price,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        <div style="float: right;border: 1px solid black;">
                            <!--
                        <small>
                           &nbsp;&nbsp;PVT HTVA : {{ number_format($item_total_amount,0,',',' ' )}}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;TVA : {{ number_format($totalVat,3,',',' ' )}}
                        </small><br>
                    -->
                        <small><strong>
                           &nbsp;&nbsp;Montant Total : {{ number_format($item_total_amount,0,',',' ' )}}</strong>
                        </small>
                        </div>
                    </div>
                    <!--
                    <small>{{ $invoice_signature }} : ID</small><br>
                  -->
                    <br><br><br><br><br>
                    <small>Thank You For Visit</small>
                    <br><br><br><br><br><br>
                    <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('COMPTE CORILAC N° 3003-01788603-84 AU NOM DE UWIRAGIYE F.')) !!} ">
                    </small>
            </div>
        </div>
    </div>
</div>
</body>
</html>

