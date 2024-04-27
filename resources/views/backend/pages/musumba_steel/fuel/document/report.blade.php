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
                   <img src="img/logo_musumba.jpg" width="1024" height="45">
                </div>
                <div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; Mouvement du carburant
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br>
                    <div>
                        <h2 style="text-align: center;text-decoration: underline;">MOUVEMENT STOCK DES CARBURANTS</h2>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Cuve</th>
                                    <th width="10%">Carburant</th>
                                    <th width="10%">Q. Stock Initial</th>
                                    <th width="10%">Quantite Entree</th>
                                    <th width="10%">Stock Total</th>
                                    <th width="10%">Quantite Sortie</th>
                                    <th width="10%">Plaque</th>
                                    <th width="10%">Chauffeur</th>
                                    <th width="10%">Stock Final</th>
                                    <th width="10%">Auteur</th> 
                                    <th>Type Mouv.</th>
                                    <th>Document No</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                                    <td>{{ $data->pump->name }}</td>
                                    <td>{{ $data->pump->fuel->name }}</td>
                                    <td>{{ $data->quantity_stock_initial }}</td>
                                    <td>@if($data->quantity_reception){{ $data->quantity_reception }} @elseif($data->quantity_stockin) {{ $data->quantity_stockin }} @endif</td>
                                    <td>@if($data->quantity_stockin){{ $data->quantity_stock_initial + $data->quantity_stockin }} @elseif($data->quantity_reception) {{ $data->quantity_stock_initial + $data->quantity_reception }} @endif</td>
                                    <td>{{ $data->quantity_stockout }}</td>
                                    <td>@if($data->car_id){{ $data->car->immatriculation }}@endif</td>
                                    <td>@if($data->driver_id){{ $data->driver->firstname }}&nbsp;{{ $data->driver->lastname }}@endif</td>
                                    <td>{{ ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout) }}</td>
                                    <td>{{ $data->created_by }}</td>
                                    <td>{{ $data->type_transaction }}</td>
                                    <td>{{ $data->document_no }}</td>
                                </tr>
                               @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br>
                    <div style="display: flex;">
                        <div style="float: left center; margin-right: 0;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;Gestionnaire et signature
                            <div>
                                &nbsp;&nbsp;
                            </div>
                        </div>
                        <div style="float: right;margin-right: 15px;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;Nom et signature
                            <div>
                                &nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
 
            </div>
        </div>
    </div>
</div>
</body>
</html>

