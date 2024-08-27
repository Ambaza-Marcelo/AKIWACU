<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             text-align: center;
             width: 120px;
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
                   <img src="img/eden_logo.png" width="200" height="100">
                </div>
                <div><br>
                           <strong style="text-decoration: underline;text-align: center;">FACTURE GLOBALE .......................................................................DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y')}} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y')}} </strong>
                    </div>
                    <div><br>
                        <strong style="text-decoration: underline;text-align: center;">A. Identification du Vendeur</strong>
                        
                    </div>
                    <div><br>
                          <small>NIF : {{$setting->nif}}</small><br>
                          <small>RC : {{$setting->rc}}</small><br>
                          <small>Centre Fiscal : DMC</small><br>
                          <small>Secteur d'activite : HOTELERIE</small><br>
                          <small> Forme Juridique : SPRL</small><br>
                          <small> Adresse : {{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>Telephone : {{$setting->telephone1}}/{{$setting->telephone2}}</small><br>
                          <small>Assujetti a la TVA : |oui<input type="checkbox" checked="checked">|Non<input type="checkbox"></small>
                    </div>  
                    <div><br>
                        <strong style="text-decoration: underline;text-align: center;">B. Identification du Client</strong>
                        
                    </div>             
                    <div><br>
                        <small>Nom et Prenom : @if($data->client_id){{ $data->client->customer_name }} @endif</small> <br>
                        <small>NIF : <!--@if($data->client_id){{ $data->client->customer_TIN }} @endif --></small> <br>
                        <small>Adresse :<!-- @if($data->client_id){{ $data->client->customer_address }} @endif / @if($data->client_id){{ $data->client->telephone }} @endif --></small> <br>
                        <small>Assujetti a la TVA : |oui<input type="checkbox">|Non<input type="checkbox"></small><br>
                    </div>
                    <div>
                        <strong style="text-decoration: underline;">CREDIT</strong><br>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: pink;">
                                    <th>No</th>
                                    <th>DESIGNATION</th>
                                    <th>T HTVA</th>
                                    <th>T TVA(10%)</th>
                                    <th>TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>1</td>
                                    <td>BOISSONS</td>
                                    <td>{{ number_format($item_total_nvat_drink,0,',',' ' )}}</td>
                                    <td>{{ number_format($total_vat_drink,0,',',' ' )}}</td>
                                    <td>{{ number_format($item_total_amount_drink,0,',',' ' )}}</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>CUISINE</td>
                                    <td>{{ number_format($item_total_nvat_kitchen,0,',',' ' )}}</td>
                                    <td>{{ number_format($total_vat_kitchen,0,',',' ' )}}</td>
                                    <td>{{ number_format($item_total_amount_kitchen,0,',',' ' )}}</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>BARRISTA</td>
                                    <td>{{ number_format($item_total_nvat_barrista,0,',',' ' )}}</td>
                                    <td>{{ number_format($total_vat_barrista,0,',',' ' )}}</td>
                                    <td>{{ number_format($item_total_amount_barrista,0,',',' ' )}}</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>BARTENDER</td>
                                    <td>{{ number_format($item_total_nvat_bartender,0,',',' ' )}}</td>
                                    <td>{{ number_format($total_vat_bartender,0,',',' ' )}}</td>
                                    <td>{{ number_format($item_total_amount_bartender,0,',',' ' )}}</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>SERVICE</td>
                                    <td>{{ number_format($item_total_nvat_service,0,',',' ' )}}</td>
                                    <td>{{ number_format($total_vat_service,0,',',' ' )}}</td>
                                    <td>{{ number_format($item_total_amount_service,0,',',' ' )}}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr style="background-color: pink;">
                                    <th colspan="2">TOTAL</th>
                                    <th>{{ number_format(($item_total_nvat_drink + $item_total_nvat_kitchen + $item_total_nvat_barrista + $item_total_nvat_bartender + $item_total_nvat_service),0,',',' ') }}</th>
                                    <th>{{ number_format(($total_vat_drink + $total_vat_kitchen + $total_vat_barrista + $total_vat_bartender + $total_vat_service),0,',',' ') }}</th>
                                    <th>{{ number_format(($item_total_amount_drink + $item_total_amount_kitchen + $item_total_amount_barrista + $item_total_amount_bartender + $item_total_amount_service),0,',',' ') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                        
                    <small>{{ $montant_total_global_en_lettre }}</small><br>
                    <small>Thank You For Visit</small>
                    <br><br><br>
                    <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('www.edengardenresorts.bi, '.number_format(($item_total_amount_drink + $item_total_amount_kitchen + $item_total_amount_barrista + $item_total_amount_bartender + $item_total_amount_service),0,',',' ').' ,powered by https://ambazamarcellin.netlify.app/')) !!} ">
                    </small>
            </div>
            <div class="watermark">
                <hr>
                        COMPTE CORILAC N° 19432;KCB N° 6690846997;BCB N° 13120-21300420003-61 ;BBCI N° 6012151/001-000-108;BANCOBU N° 15597620101-13;ECOBANK N° 38125026983 AU NOM DE EDEN GARDEN RESORT. 
                        <h4>www.edengardenresorts.bi | info@edengardenresorts.bi | bookings@edengardenresorts.bi | +257 79 500 500</h4>                                               
            </div>
        </div>
    </div>
</div>
</body>
</html>

