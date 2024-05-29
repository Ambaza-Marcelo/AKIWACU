<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        table,tr,th,td {
            border-collapse: collapse;border: 1px solid black;
        }
    </style>
</head>

<body>
<div>
    <div>
        <div>
            <div>
               <div>
                   <img src="img/eden_logo.png" width="170" height="80">
                </div>
                <div>
                    <div style="float: left;">
                          <small> &nbsp;&nbsp;{{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->rue}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small>
                    </div>
                    <br>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black;background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; Journal de Paie
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Code : {{ $journal_paie->code }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date Debut : DU {{ \Carbon\Carbon::parse($journal_paie->date_debut)->format('d/m/y') }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date Fin : AU {{ \Carbon\Carbon::parse($journal_paie->date_fin)->format('d/m/y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br>
                    <h3 style="text-align: center;text-decoration: underline;">{{ strtoupper($journal_paie->title) }}</h3>
                    <div>
                        <table style="">
                            <thead>
                                <th>No</th>
                                <th>No Matricule</th>
                                <th>Employé</th>
                                <!--
                                <th>Nombre de personnes à charge</th>
                                <th>Nombre de jours ouvrables</th>
                                <th>Nombre de jours préstés</th>
                            -->
                                <th>Salaire Base</th>
                                <th>Somme Indemnité</th>
                                <th>Somme Prime</th>
                                <th>Salaire Brut</th>
                                <th>Somme Cotisation</th>
                                <th>Somme Impôt(IRE)</th>
                                <th>Avances sur Salaire</th>
                                <th>Autres retenues</th>
                                <th>Total des Retenues</th>
                                <th>Salaire Net</th>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->contrat->employe->matricule_no }}</td>
                                    <td>{{ $data->contrat->employe->firstname }}&nbsp;{{ $data->contrat->employe->lastname }}</td>
                                    <!--
                                    <td>{{ $data->nbre_personnes_a_charge }}</td>
                                    <td>{{ $data->nbre_jours_ouvrables }}</td>
                                    <td>{{ $data->nbre_jours_prestes }}</td>
                                -->
                                    <td>{{ number_format($data->somme_salaire_base,0,',',' ') }}</td>
                                    <td>{{ number_format($data->somme_indemnite,0,',',' ') }}</td>
                                    <td>{{ number_format($data->somme_prime,0,',',' ') }}</td>
                                    <td>@if($data->somme_salaire_brut > 0){{ number_format($data->somme_salaire_brut,0,',',' ') }} @else {{ number_format(($data->somme_salaire_base + $data->somme_indemnite + $data->somme_prime),0,',',' ') }} @endif </td>
                                    <td>{{ number_format($data->somme_cotisation,0,',',' ') }}</td>
                                    <td>{{ number_format($data->somme_impot,0,',',' ') }}</td>
                                    <td>{{ number_format($data->avance_sur_salaire,0,',',' ') }}</td>
                                    <td>{{ number_format($data->autre_retenue,0,',',' ') }}</td>
                                    <td>{{ number_format(($data->avance_sur_salaire + $data->somme_cotisation + $data->somme_impot + $data->autre_retenue),0,',',' ') }}</td>
                                    <td>@if($data->somme_salaire_net > 0){{ number_format($data->somme_salaire_net,0,',',' ') }} @else {{ number_format((($data->somme_salaire_base + $data->somme_indemnite + $data->somme_prime)-($data->avance_sur_salaire + $data->somme_cotisation + $data->somme_impot + $data->autre_retenue)),0,',',' ') }}  @endif</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                    <div style="display: flex;">
                        <div style="float: left;">
                            &nbsp;&nbsp;Chef de R.H
                            <div>
                                &nbsp;&nbsp;SETUKURU Emmanuel
                            </div>
                        </div>
                        <div style="float: right;">
                            &nbsp;&nbsp;Directeur Général
                            <div>
                                &nbsp;&nbsp;NGENDAKUMANA Donatien
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

