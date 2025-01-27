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
                   <img src="{{ asset('img/eden_logo.png')}}" width="200" height="85">
                </div>
                <div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; {!! QrCode::size(100)->backgroundColor(255,255,255)->generate('MATRICULE'.$data->matricule_no.', Designed by AMBAZA' ) !!}
                        </small><br>
                        <small>
                            Le {{ date('d') }}/{{date('m')}}/{{ date('Y')}}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">FICHE D'INFORMATION SUR L'EMPLOYE</h3>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th colspan="10" style="text-align: left;background-color: #027789;">IDENTIFICATION DE L'EMPLOYE</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>NOM ET PRENOM</th>
                                    <th>NATIONALITE</th>
                                    <th>CNI</th>
                                    <th>RESIDENCE ACTUEL</th>
                                    <th>TELEPHONE</th>
                                    <th>NUMERO MATRICULE</th>
                                    <th>ADRESSE ELECTRONIQUE</th>
                                    <th>GENRE</th>
                                    <th>ETAT CIVIL</th>
                                    <th>NBRE PERSONNES A CHARGE</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>{{ $data->firstname }} {{ $data->lastname }}</td>
                                    <td>{{ $data->pays }}</td>
                                    <td>{{ $data->cni }}</td>
                                    <td>{{ $data->quartier_residence_actuel }}</td>
                                    <td>{{ $data->phone_no }}</td>
                                    <td>{{ $data->matricule_no }}</td>
                                    <td>{{ $data->mail }}</td>
                                    <td>@if($data->gender == 1) Male @else Femelle @endif</td>
                                    <td>@if($data->statut_matrimonial == 1) Marié(e) @elseif($data->statut_matrimonial == 2 ) Divorcé(e) @elseif($data->statut_matrimonial == 3 ) Veuf(ve) @else Célibtaire @endif</td>
                                    <td>{{ $data->children_number }}</td>
                                </tr>
                            </tbody>
                            <thead>
                                <tr>
                                    <th colspan="6" style="text-align: left;background-color: #027789;">STATUT DE L'EMPLOYE</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>DATE D'ARRIVEE</th>
                                    <th>DATE DE FIN</th>
                                    <th>DEPARTEMENT</th>
                                    <th>SERVICE</th>
                                    <th>FONCTION</th>
                                    <th>GRADE</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>{{ Carbon\Carbon::parse($data->date_debut)->format('d/m/Y') }}</td>
                                    <td></td>
                                    <td>{{ $data->departement->name }}</td>
                                    <td>{{ $data->service->name }}</td>
                                    <td>{{ $data->fonction->name }}</td>
                                    <td>{{ $data->grade->name }}</td>
                                </tr>
                            </tbody>
                            <thead>
                                <tr>
                                    <th colspan="3" style="text-align: left;background-color: #027789;">REMUNERATION DE L'EMPLOYE</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>SALAIRE BASE</th>
                                    <th>INDEMNITE DE DEPLACEMENT</th>
                                    <th>INDEMNITE DE LOGEMENT</th>
                                    <th>PRIME DE FONCTION</th>
                                    <th>REMUNERATTION BRUTE</th>
                                    <th>BANQUE</th>
                                    <th>ADRESSE</th>
                                    <th>MONNAIE</th>
                                    <th>NUMERO DE COMPTE</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>{{ number_format($data->somme_salaire_base,0,',',' ') }}</td>
                                    <td>{{ number_format($data->indemnite_deplacement,0,',',' ') }}</td>
                                    <td>{{ number_format($data->indemnite_logement,0,',',' ') }}</td>
                                    <td>{{ number_format($data->prime_fonction,0,',',' ') }}</td>
                                    <td>{{ number_format(($data->somme_salaire_base + $data->indemnite_deplacement + $data->indemnite_logement + $data->prime_fonction),0,',',' ') }}</td>
                                    <td>@if($data->banque_id){{ $data->banque->name }} @endif</td>
                                    <td>@if($data->banque_id){{ $data->banque->address }} @endif</td>
                                    <td>@if($data->banque_id){{ $data->banque->currency }} @endif</td>
                                    <td>@if($data->banque_id){{ $data->numero_compte }} @endif</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="float: right;">
                        <a href=""><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

