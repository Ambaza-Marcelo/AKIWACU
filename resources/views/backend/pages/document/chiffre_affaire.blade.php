<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             width: auto;
             font-size: 16px;
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
                   <img src="img/eden_logo.png" width="200" height="85">
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
                    @php
                      $date = Carbon\Carbon::now();
                    @endphp
                    <div>
                        <h5>Date de tirage : Le {{ Carbon\Carbon::parse($date)->format('d/m/Y') }} à {{Carbon\Carbon::parse($date)->format('H:i:s') }}</h5>
                        <h5 style="text-decoration: underline;">CA DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </h5>
                    </div>
                    <br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="10%">TOTAL C.A(TTC)</th>
                                    <th width="20%">PVHTVA</th>
                                    <th width="20%">TVA</th>
                                    <!--
                                    <th width="10%">TOTAL CASH</th>
                                    <th width="20%">TOTAL CREDIT</th>
                                    -->
                                    <th width="20%">TOTAL NOTE DE CREDIT</th>
                                </tr>
                            </thead>

                            <tbody>
                               <tr>
                                @php
                                    $chiffre_affaire=$ca-($note_credit*2);
                                    $pvhtva = $total_item_price_nvat-($note_credit_pvhtva*2);
                                    $tva = $total_vat-($note_credit_tva*2);
                                @endphp
                                   <td>{{ number_format($chiffre_affaire,0,',',' ') }}</td>
                                   <td>{{ number_format($pvhtva,0,',',' ') }}</td>
                                   <td>{{ number_format($tva,0,',',' ') }}</td>
                                   <!--
                                   <td>{{ number_format($cash,0,',',' ') }}</td>
                                   <td>{{ number_format($credit,0,',',' ') }}</td>
                                -->
                                   <td>{{ number_format($note_credit,0,',',' ') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

