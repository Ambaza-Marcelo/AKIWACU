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
          font-size: 12px;
        }

        .watermark {
            opacity: 0.5;
            color: BLACK;
            position: absolute;
            bottom: 0;
            right: 0;
            }
        .marque {
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
                <div>
                   <img src="img/eden_logo.png" width="200" height="85">
                </div>
                <div>
                    <div style="float: left;">
                          <small> &nbsp;&nbsp;{{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->rue}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small>
                    </div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$reception_signature.' www.edengardenresorts.bi ')) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Reception No: {{ $code }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;@if($data->order_no)Commande No: {{ $data->order_no }} @endif
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Fournisseur: {{ $data->supplier->supplier_name }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Monaie: {{ $invoice_currency }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Facture No: @if($data->invoice_no) {{ $data->invoice_no }} @endif
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br><br><br>
                    <br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">FICHE DE RECEPTION DES ARTICLES</h3>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Code</th>
                                    <th>Quantité commandée</th>
                                    <th>Quantité reçu</th>
                                    <th>Unité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Prix HTVA</th>
                                    <th>TVA</th>
                                    <th>Taux TVA</th>
                                    <th>Prix TVAC</th>
                                    <th>Prix Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->food->name }}</td>
                                    <td>{{ $data->food->code }}</td>
                                    <td>{{ $data->quantity_ordered }}</td>
                                    <td>{{ $data->quantity_received }}</td>
                                    <td>{{ $data->food->foodMeasurement->purchase_unit }}</td>
                                    <td>{{ number_format($data->purchase_price,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->price_nvat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->vat,0,',',' ' )}}</td>
                                    <td>{{ $data->vat_rate }}%</td>
                                    <td>{{ number_format($data->price_wvat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->total_amount_received,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th style="background-color: rgb(150,150,150);" colspan="9"></th>
                                    <th>{{ number_format($price_wvat,0,',',' ') }}</th>
                                    <th>{{ number_format($totalValue,0,',',' ') }}</th>
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
                @if($price_wvat >= 1000000 || $totalValue >= 1000000)
                <h4 style="text-decoration: underline;text-align: center;">Pour la commission de Reception :</h4>
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
            @else
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
            @endif
            </div>
            <div class="marque">
                <img src="img/marque_eden.png" width="100" height="300">
            </div>
            <div class="watermark">
                <hr>
                        COMPTE CORILAC N° 19432;KCB N° 6690846997;BCB N° 13120-21300420003-61 ;BBCI N° 6012151/001-000-108;BANCOBU N° 15597620101-13;ECOBANK N° 38125026983;FINBANK N° 10162510011 AU NOM DE EDEN GARDEN RESORT. 
                        <h4>www.edengardenresorts.bi | info@edengardenresorts.bi | bookings@edengardenresorts.bi | +257 79 500 500</h4>                                               
            </div>
        </div>
    </div>
</div>
</body>
</html>

