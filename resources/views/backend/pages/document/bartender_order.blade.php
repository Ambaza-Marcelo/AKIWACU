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

        @page 
        {
            size: auto;   /* auto is the current printer page size */
            margin: 0mm;  /* this affects the margin in the printer settings */
        }

        body 
        {
            background-color:#FFFFFF; 
            margin: 0px;  /* the margin on the content before printing */
       }

    </style>

<body>
<div>
    <div>
        <div>
            <div>
                <div>
                   <img src="{{ asset('img/eden_logo.png')}}" width="200" height="85">
                </div>
                <div>
                      @if($data->flag != 1)
                        <small>
                           <strong style="font-weight: bold;font-style: italic;">Original</strong>
                        </small><br>
                        @else
                        <small>
                           <strong style="font-weight: bold;font-style: italic;">Copie</strong>
                        </small><br>
                        @endif
                    <div>
                           <h4>Bon Commande(Bartender)</h4>
                          <small>{{$setting->commune}}-{{$setting->zone}}</small><br>
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
                                    <td>{{ $data->bartenderItem->name }}</td>
                                    <td>{{ $data->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <small>Nom du Serveur(se) : {{ $data->employe->name }}</small>
                </div>
                <div>
                    <small>Table : {{ $data->table->name }}</small>
                </div><br>
                <div>
                 {{ $data->description }}
                </div>
                <br><br>
                @if($data->flag != 1)
                 <a href="javascript:window.print();"><small>Thank You For Work</small></a>
                 @elseif(Auth::guard('admin')->user()->can('drink_order_client.reject'))
                 <a href="javascript:window.print();"><small>Thank You For Work</small></a>
                 @else
                 <a href="#"><small>Thank You For Work</small></a>
                 @endif

                    <small>
                           &nbsp;&nbsp;
                           {!! QrCode::size(100)->backgroundColor(255,255,255)->generate('ID : '.$order_signature.' www.edengardenresorts.bi, Designed by AMBAZA Marcellin' ) !!}
                    </small>
                  <!--
                  <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('eSIGNATURE : '.$order_signature.' www.ambazamarcellin.netlify.com, Order Number : '.$order_no)) !!} ">
                 </small>
               -->
            </div>
        </div>
    </div>
</div>
</body>
</html>

