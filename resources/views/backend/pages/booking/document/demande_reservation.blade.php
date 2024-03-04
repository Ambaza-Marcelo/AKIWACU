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
                    <div style="float: left;">
                          <small> &nbsp;&nbsp;{{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->rue}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small>
                    </div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$booking_signature.' www.edengardenresort.bi ')) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Reservation No: {{ $booking_no }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">FICHE DE RESERVATION DE SALLE </h3>
                    </div>
                    <div>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: left;background-color: pink;">LES DONNEES DE L'ASSOCIATION ET DE SES RESPONSABLES</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <td>NOM DE L'ASSOCIATION</td>
                                    <td>{{ $data->booking_client_id }}</td>
                                </tr>
                                <tr>
                                    <th colspan="2">COORDONNEES DE LA PERSONNE A CHARGE DE RESERVATION</th>
                                </tr>
                                <tr>
                                    <td>NOM ET PRENOM</td>
                                    <td>{{ $data->nom_referent }}</td>
                                </tr>
                                <tr>
                                    <td>ADRESSE ELECTRONIQUE</td>
                                    <td>{{ $data->mail_referent }}</td>
                                </tr>
                                <tr>
                                    <td>TELEPHONE</td>
                                    <td>{{ $data->telephone_referent }}</td>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: left;background-color: pink;">VOTRE DEMANDE DE MISE A DISPOSITION</th>
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
                                <tr style="text-align: left;">
                                    <td>DATE-HEURE DEBUT</td>
                                    <td>Le {{ \Carbon\Carbon::parse($data->date_debut)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr style="text-align: left;">
                                    <td>DATE-HEURE FIN</td>
                                    <td>Le {{ \Carbon\Carbon::parse($data->date_fin)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>DESIGNATION</th>
                                    <th>QUANTITE.....PRIX</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>@if($data->salle_id){{ $data->salle->name }} @elseif($data->service_id){{ $data->service->name }} @else{{ $data->salle->name }} @endif</td>
                                    <td>{{ $data->quantity }}........................................{{ number_format($data->selling_price,2,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: left;background-color: pink;">BESOIN TECHNIQUE ?</th>
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
                        </table>
                    </div>
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
                            &nbsp;&nbsp;Nom et signature
                            <div>
                            &nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

