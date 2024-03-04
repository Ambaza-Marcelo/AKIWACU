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
        body{
          font-size: 14px;
        }

        .watermark {
            opacity: 0.5;
            color: BLACK;
            position: absolute;
            bottom: 0;
            right: 0;
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
                           <strong style="text-decoration: underline;">FACTURE GLOBALE DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </strong>
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
                        <small>Nom et Prenom :@if($data->client_id){{ $data->client->customer_name }} @endif</small> <br>
                        <small>NIF : @if($data->client_id){{ $data->client->customer_TIN }} @endif</small> <br>
                        <small>Adresse : @if($data->client_id){{ $data->client->customer_address }} @endif / @if($data->client_id){{ $data->client->telephone }} @endif</small> <br>
                        <small>Assujetti a la TVA : |oui<input type="checkbox">|Non<input type="checkbox"></small><br>
                    </div>
                    <div>
                      <strong style="text-decoration: underline;">CASH</strong><br>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date Facture</th>
                                    <th>Facture No</th>
                                    <th>DESIGNATION</th>
                                    <th>Qtes</th>
                                    <th>P.U</th>
                                    <th>P.HTVA</th>
                                    <th>TVA</th>
                                    <th>TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y') }}</td>
                                    <td>{{ $data->invoice_number }}</td>
                                    <td>@if($data->drink_id){{ $data->drink->name }} @elseif($data->food_item_id){{ $data->foodItem->name }} @elseif($data->bartender_item_id){{ $data->bartenderItem->name }} @elseif($data->salle_id){{ $data->salle->name }} @elseif($data->service){{ $data->service->name }} @elseif($data->table_id){{ $data->table->name }} @else {{ $data->barristItem->name }} @endif</td>
                                    <td>{{ $data->item_quantity }}</td>

                                    <td>{{ number_format($data->item_price,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->item_price_nvat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->vat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7">Total Cash</th>
                                    <th>{{ number_format($total_vat,0,',',' ') }}</th>
                                    <th>{{ number_format($item_total_amount,0,',',' ') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                        <strong style="text-decoration: underline;">CREDIT</strong><br>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date Facture</th>
                                    <th>Facture No</th>
                                    <th>DESIGNATION</th>
                                    <th>Qtes</th>
                                    <th>P.U</th>
                                    <th>P.HTVA</th>
                                    <th>TVA</th>
                                    <th>TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($credits as $credit)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($credit->invoice_date)->format('d/m/Y') }}</td>
                                    <td>{{ $credit->invoice_number }}</td>
                                    <td>@if($credit->drink_id){{ $credit->drink->name }} @elseif($credit->food_item_id){{ $credit->foodItem->name }} @elseif($credit->bartender_item_id){{ $credit->bartenderItem->name }} @elseif($credit->salle_id){{ $credit->salle->name }} @elseif($credit->service){{ $credit->service->name }} @elseif($credit->table_id){{ $credit->table->name }} @else {{ $credit->barristItem->name }} @endif</td>
                                    <td>{{ $credit->item_quantity }}</td>

                                    <td>{{ number_format($credit->item_price,0,',',' ' )}}</td>
                                    <td>{{ number_format($credit->item_price_nvat,0,',',' ' )}}</td>
                                    <td>{{ number_format($credit->vat,0,',',' ' )}}</td>
                                    <td>{{ number_format($credit->item_total_amount,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7">Total Credit</th>
                                    <th>{{ number_format($total_vat_credit,0,',',' ') }}</th>
                                    <th>{{ number_format($item_total_amount_credit,0,',',' ') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    <small>Thank You For Visit</small>
                    <br><br><br>
                    <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('www.edengardenresorts.bi, '.number_format($item_total_amount_credit,0,',',' ').' ,powered by https://ambazamarcellin.netlify.app/')) !!} ">
                    </small>
            </div>
            <div class="watermark">
                        COMPTE CORILAC N° 19432;KCB N° 6690846997;BCB N° 13120-21300420003-61 ;BBCI N° 6012151/001-000-108;BANCOBU N° 15597620101-12 AU NOM DE EDEN GARDEN RESORT. 
                        <h4>www.edengardenresorts.bi | info@edengardenresorts.bi | bookings@edengardenresorts.bi | +257 79 500 500</h4>                                               
            </div>
        </div>
    </div>
</div>
</body>
</html>

