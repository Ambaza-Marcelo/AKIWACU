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
                   <img src="{{ asset('img/eden_logo.png')}}" width="200" height="85">
                </div>
                <div>
                        @if($data->statut != 1)
                        <small>
                           <strong style="font-weight: bold;font-style: italic;">Original</strong>
                        </small><br>
                        @else
                        <small>
                           <strong style="font-weight: bold;font-style: italic;">Copie</strong>
                        </small><br>
                        @endif
                        <small>
                           <strong style="text-decoration: underline;">FACTURE</strong>
                        </small><br>
                        @if($data->drink_order_no)
                        <small>
                           Order Number: {{ $data->drink_order_no }}
                        </small><br>
                        @elseif($data->food_order_no)
                        <small>
                           Order Number: {{ $data->food_order_no }}
                        </small><br>
                        @elseif($data->bartender_order_no)
                        <small>
                           Order Number: {{ $data->bartender_order_no }}
                        </small><br>
                        @elseif($data->booking_no || $data->booking_no)
                        <small>
                           Booking Number: {{ $data->booking_no }}
                        </small><br>
                        @else
                        <small>
                           Order Number: {{ $data->barrist_order_no }}
                        </small><br>
                        @endif
                        <small>
                           No. : {{ $invoice_number }}
                        </small><br>
                        <small>
                           Date : Le {{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i:s') }}
                        </small>
                        <hr>
                    </div>
                    <div>
                          <small>NIF : {{$setting->nif}}</small><br>
                          <small>RC : {{$setting->rc}}</small><br>
                          <small>Centre Fiscal : DMC</small><br>
                          <small>Secteur d'activite : HOTELERIE</small><br>
                          <small> Forme Juridique : SPRL</small><br>
                          <small> Adresse : {{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>Telephone : {{$setting->telephone1}}/{{$setting->telephone2}}</small><br>
                          <small>Assujetti a la TVA : |oui<input type="checkbox" checked="checked">|Non<input type="checkbox"></small>
                          <hr> 
                    </div>               
                    <div>
                        <small>Nom et Prenom :@if($data->client_id){{ $data->client->customer_name }} @elseif($data->booking_client_id) {{ $data->bookingClient->customer_name }} @else {{ $data->customer_name }} @endif</small> <br>
                        <small>NIF : @if($data->client_id){{ $data->client->customer_TIN }} @elseif($data->booking_client_id) {{ $data->bookingClient->customer_TIN }} @else {{ $data->customer_TIN }} @endif</small> <br>
                        <small>Adresse : @if($data->client_id){{ $data->client->customer_address }} @elseif($data->booking_client_id) {{ $data->bookingClient->customer_address }} @else {{ $data->customer_address }} @endif / @if($data->client_id){{ $data->client->telephone }} @elseif($data->booking_client_id) {{ $data->bookingClient->telephone }} @else {{ $data->telephone }} @endif</small> <br>
                        <small>Assujetti a la TVA : |oui<input type="checkbox">|Non<input type="checkbox"></small><br>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>DESIGNATION</th>
                                    <th>Qtes</th>
                                    <th>PV. U</th>
                                    <!--
                                    <th>TC</th>
                                    <th>PFL</th>
                                    <th>PRIX HTVA</th>
                                    <th>MONTANT TVA</th>
                                    <th>PV. U</th>
                                -->
                                    <th>TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>@if($data->drink_id){{ $data->drink->name }} @elseif($data->food_item_id){{ $data->foodItem->name }} @elseif($data->bartender_item_id){{ $data->bartenderItem->name }} @elseif($data->salle_id){{ $data->salle->name }} @elseif($data->service){{ $data->service->name }}  @elseif($data->breakfast_id) {{ $data->breakFast->name }} @elseif($data->swiming_pool_id) {{ $data->swimingPool->name }} @elseif($data->kidness_space_id) {{ $data->kidnessSpace->name }} @else {{ $data->barristItem->name }} @endif</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    
                                    <td>{{ number_format($data->item_price,0,',',' ' )}}</td>
                                    <!--
                                    <td>{{ number_format($data->item_ct,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->item_tl,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->item_price_nvat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->vat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->item_price_wvat,0,',',' ' )}}</td>
                                -->
                                    <td>{{ number_format($data->item_total_amount,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        <div style="float: right;border: 1px solid black;">
                        <small>
                           &nbsp;&nbsp;TOTAL : {{ number_format($totalValue,3,',',' ' )}}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;TVA : {{ number_format($totalVat,3,',',' ' )}}
                        </small><br>
                        <small><strong>
                           &nbsp;&nbsp;Total à payer : {{ number_format($item_total_amount,0,',',' ' )}}</strong>
                        </small>
                        </div>
                    </div><br><br><br><br>
                    <small>{{ $invoice_signature }} : ID</small><br>
                    @if($data->employe_id)
                    <small>Serveur(se) : {{ $data->employe->name }}</small>
                    @endif
                    <br>
                    <small>Caissier(e) : {{ $facture->auteur }}</small>
                    <br><br>
                    
                    <a href="javascript:window.print();"><small>Thank You For Visit</small></a>
                    <small>
                           &nbsp;&nbsp;
                           {!! QrCode::size(100)->backgroundColor(255,255,255)->generate('ID : '.$invoice_signature.' www.edengardenresorts.bi, Designed by AMBAZA Marcellin' ) !!}
                    </small>
                  <!--
                  <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('eSIGNATURE : '.$invoice_signature.' www.ambazamarcellin.netlify.com')) !!} ">
                 </small>
               -->
            </div>
        </div>
    </div>
</div>
</body>
</html>

