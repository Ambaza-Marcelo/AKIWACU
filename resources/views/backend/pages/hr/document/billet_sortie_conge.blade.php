<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        table,tr,th,td{
             border: 1px solid black;
             width: 100%;
             border-collapse: collapse;
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
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('MATRICULE : '.$data->matricule_no.' www.edengardenresort.bi ')) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ date('d') }}/{{date('m')}}/{{ date('Y')}}
                        </small><br>
                    </div>
                    <br><br><br><br><br>
                    <h3 style="text-align: center;text-decoration: underline;">BILLET DE SORTIE</h3><br>
                    <div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Mot Clé</th>
                                    <th>Désignation</th>
            
                                </tr>
                            </thead>
                            <tbody>
                                @if($data->employe_id)
                               <tr>
                                    <td>Employé(e)</td>
                                    <td>{{ strtoupper($data->employe->firstname) }} &nbsp;{{ strtoupper($data->employe->lastname) }}</td>                              
                                </tr>
                                <tr>
                                    <td>Département</td>
                                    <td>{{ strtoupper($data->employe->departement->name) }}</td>                              
                                </tr>
                                <tr>
                                    <td>Service</td>
                                    <td>{{ strtoupper($data->employe->service->name) }}</td>                              
                                </tr>
                                <tr>
                                    <td>Fonction</td>
                                    <td>{{ strtoupper($data->employe->fonction->name) }}</td>                              
                                </tr>
                                @else
                                <tr>
                                    <td>Stagiaire</td>
                                    <td>{{ strtoupper($data->stagiaire->firstname) }} &nbsp;{{ strtoupper($data->stagiaire->lastname) }}</td>                              
                                </tr>
                                <tr>
                                    <td>Département</td>
                                    <td>{{ strtoupper($data->stagiaire->departement->name) }}</td>                              
                                </tr>
                                <tr>
                                    <td>Service</td>
                                    <td>{{ strtoupper($data->stagiaire->service->name) }}</td>                              
                                </tr>
                                <tr>
                                    <td>Fonction</td>
                                    <td>{{ strtoupper($data->stagiaire->fonction->name) }}</td>                              
                                </tr>
                                @endif
                                <tr>
                                    <td>Motif de Congé</td>
                                    <td>{{ strtoupper($data->typeConge->libelle) }}</td>                              
                                </tr>
                                <tr>
                                    <td>date et Heure de Sortie</td>
                                    <td>{{ \Carbon\Carbon::parse($data->date_heure_debut)->format('d/m/Y à H:i:s') }}</td>                              
                                </tr>
                                <tr>
                                    <td>date et Heure de retour prévue</td>
                                    <td>{{ \Carbon\Carbon::parse($data->date_heure_fin)->format('d/m/Y à H:i:s') }}</td>                              
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>Mr/Mme @if($data->employe_id){{ strtoupper($data->employe->firstname) }} &nbsp;{{ strtoupper($data->employe->lastname) }} @else {{ strtoupper($data->stagiaire->firstname) }} &nbsp;{{ strtoupper($data->stagiaire->lastname) }} @endif, vous avez l'autorisation de <strong>@if($data->nbre_heures_conge_pris < 24){{ number_format($data->nbre_heures_conge_pris,0)}}</strong> heure(s)@else {{ number_format(($data->nbre_heures_conge_pris / 24),0) }} jour(s) @endif de sortie.
                <br>
                <div>
                    Motif:..................................................................................................................................................................
                </div>
                <br>
                <div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Visa du Demandeur</th>
                                    <th>Visa du Supérieur hiérarchique</th>
                                    <th>Visa Chargé des RH</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>Date et Signature : <br>................................. </td>
                                    <td>Date et Signature :<br> .................................</td>
                                    <td>Date et Signature :<br> .................................</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Motif en cas du rejet : <br>.................................</td>
                                    <td>Motif en cas du rejet :<br> .................................</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <br>
                        <span>NB:</span>Ce billet est exigé à la sortie de tout(e) @if($data->employe_id) Employé(e) @else Stagiaire @endif.Il doit être classé par la sécurité et remis aux RH.
                    </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

