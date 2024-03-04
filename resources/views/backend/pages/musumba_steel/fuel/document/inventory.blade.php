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
                <div>
                   <img src="img/logo_musumba.jpg" width="700" height="85">
                </div>
                <div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$inventory_signature.' www.musumba_steel.bi')) !!} ">
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
                                    <th>Carburant</th>
                                    <th>Cuve Stockage</th>
                                    <th>Qté Actuelle</th>
                                    <th>V.Unitaire Actuelle</th>
                                    <th style="background-color: rgb(150,150,150);">Valeur Stock Actuelle</th>
                                    <th>Nouvelle Qté</th>
                                    <th>Nouvelle V.U</th>
                                    <th style="background-color: rgb(150,150,150);">Nouvelle V du stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->pump->fuel->name }}</td>
                                    <td>{{ $data->pump->name }}</td>
                                    <td>{{ $data->quantity }}</td>
                                    <td>{{ number_format($data->cost_price,0,',',' ' )}}</td>
                                    <td style="background-color: rgb(150,150,150);">{{ number_format($data->total_cost_value,0,',',' ' )}}</td>
                                    <td>{{ $data->new_quantity }}</td>
                                    <td>{{ number_format($data->new_cost_price,0,',',' ' )}}</td>
                                    <td style="background-color: rgb(150,150,150);">{{ number_format($data->new_total_cost_value,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                             <tfoot>
                                <tr>
                                    <th>Totaux</th>
                                    <th style="background-color: rgb(150,150,150);" colspan="4"></th>
                                    <th>{{number_format($totalValueActuelle,0,',',' ')}}</th>
                                    <th style="background-color: rgb(150,150,150);" colspan="2"></th>
                                    <th>{{ number_format($totalValueNew,0,',',' ') }}</th>
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

