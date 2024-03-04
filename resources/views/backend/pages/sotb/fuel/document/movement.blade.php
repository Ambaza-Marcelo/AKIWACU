<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             text-align: center;
        }

    </style>

<body>
<div>
    <div>
        <div>
            <div>
                <div>
                   <!-- <img src="{{ asset('img.logopiece.jpg')}}"> -->
                  <!-- <h1>MUSUMBA STEEL</h1> -->
                   <img src="img/logo_musumba.jpg" width="1024" height="45">
                </div>
                <div>
                    <div style="float: left;">
                          <small> &nbsp;&nbsp;{{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->rue}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small>
                    </div>
                    <br>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; Mouvement du carburant
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h2 style="text-align: center;text-decoration: underline;">MOUVEMENT STOCK DES CARBURANTS</h2>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Pompe</th>
                                    <th>Carburant</th>
                                    <th>Plaque</th>
                                    <th>Marque</th>
                                    <th>Chauffeur</th>
                                    <!--
                                    <th style="background-color: rgb(150,150,150);">Stock Initial</th> -->
                                    <th>C.U.M.P</th>
                                    <th>Entrée</th>
                                    <th style="background-color: rgb(150,150,150);">Val. Tot. Entrée</th>
                                    <th>Sortie</th>
                                    <th style="background-color: rgb(150,150,150);">val. Tot. Sortie</th>
                                    <th>Q. S. Finale</th>
                                    <th style="background-color: rgb(150,150,150);">val. S. Finale</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $data->fuelPump->designation }}</td>
                                    <td>{{ $data->fuelPump->fuel->nom }}</td>
                                    <td>@if($data->driver_car_id){{ $data->driverCar->car->immatriculation }}@endif</td>
                                    <td>@if($data->driver_car_id){{ $data->driverCar->car->marque }}@endif</td>
                                    <td>@if($data->driver_car_id){{ $data->driverCar->driver->nom }}&nbsp;{{ $data->driverCar->driver->prenom }}@endif</td>
                                    <!--
                                    <td style="background-color: rgb(150,150,150);">{{ $data->quantity_stock_init }}</td> -->
                                    <td>{{ number_format($data->fuelPump->fuel->prix_unitaire,0,',',' ') }}</td>
                                    <td>{{ $data->quantity_in }}</td>
                                    @php $entreeValeurTot = $data->fuelPump->fuel->prix_unitaire * $data->quantity_in
                                    @endphp
                                    <td style="background-color: rgb(150,150,150);">{{ number_format($entreeValeurTot,0,',',' ') }}</td>
                                    <td>{{ $data->quantity_out }}</td>
                                    @php $sortieValeurTot = $data->fuelPump->fuel->prix_unitaire * $data->quantity_out
                                    @endphp
                                    <td style="background-color: rgb(150,150,150);">{{ number_format($sortieValeurTot,0,',',' ') }}</td>
                                    @php $stockTotal = $data->quantite_stock_initiale + $data->quantity_in;
                                    $stockFinal = $stockTotal - $data->quantity_out;
                                    @endphp
                                    <td>{{ $stockFinal }}</td>
                                    <td style="background-color: rgb(150,150,150);">{{ number_format(($data->fuelPump->fuel->prix_unitaire * $stockFinal),0,',',' ') }}</td>
                                </tr>
                                @endforeach 
                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br>
                    <div style="display: flex;">
                        <div style="float: left center; margin-right: 0;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;Chef S. Généraux et signature
                            <div>
                                &nbsp;&nbsp;HACIMANA CALLIXTE
                            </div>
                        </div>
                        <div style="float: right;margin-right: 15px;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;Gestionnaire et signature
                            <div>
                                &nbsp;&nbsp;NDAYIZEYE CLAUDINE
                            </div>
                        </div>
                    </div>
 
            </div>
        </div>
    </div>
</div>
</body>
</html>

