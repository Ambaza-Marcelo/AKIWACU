<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="public/backend/assets/css/bootstrap.min.css'">
    <link rel="stylesheet" href="public/'backend/assets/css/font-awesome.min.css'">
    <meta charset="utf-8">
    <style type="text/css">
        table,thead,tr,th,td,tbody{
            border-collapse: collapse;
            border: 1px solid black;
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
            bottom: 250;
            right: 0;
            }
    </style>
<body>
<div>
    <div>
        <div>
            <div>
                <div>
                    <span style="float: left;">
                        <img src="img/eden_logo.png" width="200" height="85">
                    </span>
                    <span style="float: right;">
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('No : '.$booking_no.' www.edengardenresort.bi ,powered by https://ambazamarcellin.netlify.app')) !!} ">
                        </span>
                </div>
                <div><br><br><br><br><br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">FICHE DE RESERVATION {{ $booking_no }} DU {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
                    </div>
                    <div>
                        <strong style="text-decoration: underline;text-align: center;">A. Identification du Vendeur</strong>         
                    </div>
                    <div>
                          <small>NIF : {{$setting->nif}}</small><br>
                          <small>RC : {{$setting->rc}}</small><br>
                          <small>Centre Fiscal : {{ $setting->tp_fiscal_center }}</small><br>
                          <small>Secteur d'activité : {{ $setting->tp_activity_sector }}</small><br>
                          <small> Forme Juridique : {{ $setting->tp_legal_form }}</small><br>
                          <small> Adresse : {{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>Telephone : {{$setting->telephone1}}/{{$setting->telephone2}}</small><br>
                          <small>Assujetti a la TVA : |oui<input type="checkbox" @if($setting->vat_taxpayer == '1') checked="checked" @endif>|Non<input type="checkbox" @if($setting->vat_taxpayer == '0') checked="checked" @endif></small>
                    </div> 
                    <br>
                    <div>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: left;background-color: #027789;">B. Identification du Client</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <td>Nom et Prénom </td>
                                    <td>{{ $data->nom_referent }}</td>
                                </tr>
                                <tr>
                                    <td>E-mail</td>
                                    <td>{{ $data->mail_referent }}</td>
                                </tr>
                                <tr>
                                    <td>Téléphone  </td>
                                    <td>{{ $data->telephone_referent }}</td>
                                </tr>
                                <tr>
                                    <td>CNI/No Passeport</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Date et heure d’arrivée</td>
                                    <td>Du {{ \Carbon\Carbon::parse($data->date_debut)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td>Date et heure de départ</td>
                                    <td>au {{ \Carbon\Carbon::parse($data->date_fin)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </thead>
                            @if($data->salle_id)
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: left;background-color: #027789;">VOTRE DEMANDE DE MISE A DISPOSITION</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr style="text-align: left;">
                                    <td>TYPE D'EVENEMENT</td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul>
                                                    <li>ASSEMBLEE GENERALE <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                    <li>FORMATION <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                    <li>REUNION <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul>
                                                    <li>CONFERENCE OU SEMINAIRE <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                    <li>REUNION PUBLIQUE <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                    <li>RECEPTION <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            @endif
                            <thead>
                                <tr>
                                    <th>Désignation</th>
                                    <th>Quantité......................................Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>@if($data->salle_id){{ $data->salle->name }} @elseif($data->service_id){{ $data->service->name }} @elseif($data->swiming_pool_id) {{ $data->swimingPool->name }} @elseif($data->kidness_space_id) {{ $data->kidnessSpace->name }} @elseif($data->room_id) {{ $data->room->name }} @endif</td>
                                    <td>{{ $data->quantity }}........................................{{ number_format($data->selling_price,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td>Montant Total</td>
                                    <td>{{ number_format($totalAmount,0,',',' ') }}</td>
                                </tr>
                            </tbody>
                            @if($data->salle_id)
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: left;background-color: #027789;">BESOIN TECHNIQUE ?</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>TECHNIQUES</th>
                                    <th>SYSTEME D'INTERPRETATION</th>
                                </tr>
                            </thead>
                            <tbody style="text-align: left;">
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul>
                                                    <li>ECRAN <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                    <li>VIDEORETROPROJECTEUR <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                    <li>PETITE SONORISATION <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                            <div class="col-md-6">
                                                <ul>
                                                    <li>SYSTEME D'INTERPRETATION <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            @endif
                            @if($data->room_id)
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: left;background-color: #027789;">Mode de paiement ?</th>
                                </tr>
                            </thead>
                            <tbody style="text-align: left;">
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul>
                                                    <li>Cash sur place : BIF <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                    <li>Carte de crédit : visa <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                    <li>USD : <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                            <div class="col-md-6">
                                                <ul>
                                                    <li>EUR :  <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                </ul>
                                                <ul>
                                                    <li>MasterCard :  <span><img src="{{ asset('img/checkbox.gif')}}" width="15"></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            @endif
                        </table>
                    </div>
                    <div>
                        <h3 style="text-decoration: underline;">Condition d'annulation :</h3>
                        Toute annulation doit se faire 48h avant la date d’arrivée
                    </div>
                    @if($data->room_id)
                    <div>
                        <h3 style="text-decoration: underline;">Informations sur les tarifs :</h3>
                        <ol>
                            <li>Appartement Standard-trois chambres, salons, cuisine interne et stock : 100 USD (03 personnes)</li>
                            <li>Appartement Junior-une chambre : 50 USD (01 personnes).</li>
                            <li>Taux de change du jour (USD-BIF).</li>
                        </ol>
                    </div>
                    @endif
                </div>
                <br><br>
                <div>
                    &nbsp;&nbsp;{{ $description }}
                </div>
                <br>
                    <div style="display: flex;">
                        <div style="float: left;">
                            &nbsp;&nbsp;Nom et signature
                            <div>
                                &nbsp;&nbsp;
                            </div>
                        </div>

                        <div style="float: center;margin-left: 65%;">
                            &nbsp;&nbsp;Pour Eden Garden Resort
                            <div>
                            &nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
            </div>
        </div>
            <div class="marque">
                <img src="img/marque_eden.png" width="300" height="500">
            </div>
            <div class="watermark">
            <hr>
            COMPTE BCB N° 21300420016 USD;KCB N° 6690847012 USD;BCB N° 13120-21300420003-61 BIF;BBCI N° 6012151/001-000-108 BIF;BANCOBU N° 15597620101-13 BIF;ECOBANK N° 38125026983 BIF AU NOM DE EDEN GARDEN RESORT. 
            <h4>www.edengardenresorts.bi | info@edengardenresorts.bi | bookings@edengardenresorts.bi | +257 79 500 500</h4>                                               
            </div>
    </div>
</div>
</body>
</html>