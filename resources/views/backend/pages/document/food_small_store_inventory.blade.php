<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             text-align: center;
             width: auto;
             font-size: 12px;
        }

    </style>

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
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small>
                    </div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$inventory_signature.' www.edengardenresorts.bi, inventory number : '.$inventory_no)) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Inventory Number: {{ $inventory_no }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">{{ $title }}</h3>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Code</th>
                                    <th>Unité</th>
                                    <th>Qté Théorique</th>
                                    <th>CUMP Théorique</th>
                                    <th>Qté Physique</th>
                                    <th>Nouveau CUMP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->food->name }}</td>
                                    <td>{{ $data->food->code }}</td>
                                    <td>{{ $data->food->foodMeasurement->production_unit }}</td>
                                    <td>{{ $data->quantity }}</td>
                                    <td>{{ number_format($data->purchase_price,0,',',' ' )}}</td>
                                    <td>{{ $data->new_quantity }}</td>
                                    <td>{{ number_format($data->new_purchase_price,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                             <tfoot>
                                <tr>
                                    <th>Totaux</th>
                                    <th style="background-color: rgb(150,150,150);" colspan="3"></th>
                                    <th>{{number_format($totalQuantiteTheorique,0,',',' ')}}</th>
                                    <th></th>
                                    <th style="background-color: rgb(150,150,150);">{{ number_format($totalQuantitePhysique,0,',',' ') }}</th>
                                    <th style="background-color: rgb(150,150,150);"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br><br>
                <div>
                    &nbsp;&nbsp;{{ $description }}
                </div>
                <br>
                <h4 style="text-decoration: underline;text-align: center;">Pour la commission de l'Inventaire :</h4>
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
                    <br><br><br><br>
                        <h4 style="text-decoration: underline;text-align: center;">Pour le Gestionnaire du stock :</h4>
                        <div style="float: center;margin-left: 35%;">
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

food