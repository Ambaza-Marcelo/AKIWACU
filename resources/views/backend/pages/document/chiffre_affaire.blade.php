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
        body{
          font-size: 14px;
        }

        .watermark {
            opacity: 0.5;
            color: BLACK;
            position: absolute;
            bottom: 0;
            right: 0;
            }

        .marque1 {
            opacity: 0.5;
            color: BLACK;
            position: absolute;
            top: 0;
            right: 0;
            }
        .marque2 {
            opacity: 0.5;
            color: BLACK;
            position: absolute;
            bottom: 0;
            right: 0;
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
                                    <th width="20%">PVT HTVA</th>
                                    <th width="20%">TVA</th>
                                    <th width="10%">TOTAL CASH</th>
                                    <th width="20%">TOTAL CREDIT</th>
                                </tr>
                            </thead>

                            <tbody>
                               <tr>
                                @php
                                    $chiffre_affaire=$ca;
                                    $pvhtva = $total_item_price_nvat;
                                    $tva = $total_vat;
                                @endphp
                                   <td>{{ number_format($chiffre_affaire,0,',',' ') }}</td>
                                   <td>{{ number_format($pvhtva,0,',',' ') }}</td>
                                   <td>{{ number_format($tva,0,',',' ') }}</td>
                                   <td>{{ number_format($cash,0,',',' ') }}</td>
                                   <td>{{ number_format($credit,0,',',' ') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="marque1">
                        <img src="img/marque_eden.png" width="200" height="550">
                    </div>
                    <div class="marque2">
                        <img src="img/marque_eden.png" width="200" height="550">
                    </div>
                    <div class="watermark">
                    <hr>
                        COMPTE CORILAC N° 19432;KCB N° 6690846997;BCB N° 13120-21300420003-61 ;BBCI N° 6012151/001-000-108;BANCOBU N° 15597620101-13;ECOBANK N° 38125026983;FINBANK N° 10162510011 AU NOM DE EDEN GARDEN RESORT. 
                        <h4>www.edengardenresorts.bi | info@edengardenresorts.bi | bookings@edengardenresorts.bi | +257 79 500 500</h4>                                               
            </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

