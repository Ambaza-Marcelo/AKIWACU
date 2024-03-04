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
                   <img src="img/eden_logo.png" width="200" height="65">
                </div>
                <div>
                        <small>
                           <strong style="text-decoration: underline;">Bon d'Expedition</strong>
                        </small><br>
                        @if($data->drink_id)
                        <small>
                           Order Number: {{ $data->drink_order_no }}
                        </small><br>
                        @elseif($data->food_item_id)
                        <small>
                           Order Number: {{ $data->food_order_no }}
                        </small><br>
                        @elseif($data->bartender_item_id)
                        <small>
                           Order Number: {{ $data->bartender_order_no }}
                        </small><br>
                        @elseif($data->salle_id || $data->service_id || $data->table_id)
                        <small>
                           Booking Number: {{ $data->booking_no }}
                        </small><br>
                        @else
                        <small>
                           Order Number: {{ $data->barrist_order_no }}
                        </small><br>
                        @endif
                        <small>
                           B.E Numero: {{ $invoice_number }}
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
                                    <!--
                                    <th>PV. U</th>
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
                                    <td>@if($data->drink_id){{ $data->drink->name }} @elseif($data->food_item_id){{ $data->foodItem->name }} @elseif($data->bartender_item_id){{ $data->bartenderItem->name }} @elseif($data->salle_id){{ $data->salle->name }} @elseif($data->service){{ $data->service->name }} @elseif($data->table_id){{ $data->table->name }} @else {{ $data->barristItem->name }} @endif</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <!--
                                    <td>{{ number_format($data->item_price,0,',',' ' )}}</td>
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
                           &nbsp;&nbsp;Total TTC : {{ number_format($item_total_amount,0,',',' ' )}}</strong>
                        </small>
                        </div>
                    </div>
                    <small>{{ $invoice_signature }} : ID</small><br>
                    @if($data->employe_id)
                    <small>Serveur(se) : {{ $data->employe->name }}</small>
                    @endif
                    <br>
                    <small>Caissier(e) : {{ $facture->auteur }}</small>
                    <br><br>
                    <small>Thank You For Visit</small>
                    <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('eSIGNATURE : '.$invoice_signature.' www.edengardenresorts.bi')) !!} ">
                    </small>
            </div>
        </div>
    </div>
</div>
</body>
</html>

