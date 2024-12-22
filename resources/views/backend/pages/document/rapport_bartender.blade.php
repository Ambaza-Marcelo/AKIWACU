<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             width: 100px;
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
                        <h2 style="text-align: center;text-decoration: underline;">RAPPORT(BARTENDER) DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </h2>
                    </div>
                    <br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Code</th>
                                    <th width="10%">Libellé</th>
                                    <th width="10%">Quantité</th>
                                    <th width="10%">Prix Unitaire</th>
                                    <th width="20%">Montant Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->bartenderItem->code }}</td>
                                    <td>{{ $data->bartenderItem->name }}</td>
                                    <td>{{ $data->quantity }}</td>
                                    <td>{{ number_format($data->selling_price,0,',',' ') }}</td>
                                    <td>{{ number_format($data->total_selling_value,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total</th>
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

