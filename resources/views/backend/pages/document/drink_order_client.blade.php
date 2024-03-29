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
                   <img src="img/eden_logo.png" width="200" height="65">
                </div>
                <div>
                    <div>
                        <h4>
                           Bon Commande(Boisson)
                        </h4>
                          <small> {{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>{{$setting->rue}}</small><br>
                          <small>{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>{{$setting->email}}</small>
                    </div>
                    <div>
                        <small>
                           Order Number: {{ $order_no }}
                        </small><br>
                        <small>
                          Table: 
                        </small><br>
                        <small>
                            Date : Le {{ \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s') }}
                        </small>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Quantité Commandé</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->drink->name }}</td>
                                    <td>{{ $data->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <small>Serveur(se) : {{ $data->employe->name }}</small>
                </div><br><br>
                <small>
                        &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('eSIGNATURE : '.$order_signature.' www.edengardenresorts.bi, Order Number : '.$order_no)) !!} ">
                </small>
            </div>
        </div>
    </div>
</div>
</body>
</html>

