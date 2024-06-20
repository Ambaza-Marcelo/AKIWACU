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
                           Bon de Consommation
                        </h4>
                        <hr>
                          <small>{{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>{{$setting->rue}}</small><br>
                          <small>{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>{{$setting->email}}</small>
                    </div>
                    <hr>
                    <div>
                      <small>Responsable : {{ $consumption->staffMember->name }}</small><br>
                      <small>Position : {{ $consumption->staffMember->position->name }}</small>
                    </div>
                    <div>

                        <small>
                           Document No: {{ $consumption_no }}
                        </small><br>
                        <small>
                           Date : Le {{ \Carbon\Carbon::parse($consumption->created_at)->format('d/m/Y H:i:s') }}
                        </small><br>
                        <small>Date d'impression : Le {{ Carbon\Carbon::parse($date)->format('d/m/Y') }} à {{Carbon\Carbon::parse($date)->format('H:i:s') }}</small>
                        <br>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Quantité Consommé</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>@if($data->barrist_item_id){{ $data->barristItem->name }} @else {{ $data->foodItem->name }} @endif</td>
                                    <td>{{ $data->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div>
                          {{ $consumption->description }}
                        </div>
                    </div>
                </div>
                <br><br>
                <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(200)->generate('eSIGNATURE : '.$consumption_signature.' www.edengardenresorts.bi, Responsable : '.$consumption->staffMember->name)) !!} ">
                 </small>
            </div>
        </div>
    </div>
</div>
</body>
</html>

