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
                          <hr>
                          <small style="text-align: left; text-decoration: underline;">RAPPORT DES ACTIVITES DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </small><br>
                    </div>
                    <br><br><br>
                    <br><br><br>
                    <br><br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Nom et Prénom</th>
                                    <th width="10%">Nombre de commandes</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>@if($data->employe_id){{ $data->employe->name }}@endif</td>
                                    <td>{{ ($data->drink_order_no + $data->food_order_no + $data->bartender_order_no + $data->barrist_order_no) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
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

