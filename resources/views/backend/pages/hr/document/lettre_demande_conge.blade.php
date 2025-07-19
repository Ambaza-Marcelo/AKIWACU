<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             text-align: center;
        }

    </style>
</head>
<body>
<div>
    <div>
        <div>
            <div>
                <table>
                    <tr>
                        <td><img src="img/eden_logo.png" width="180" height="85"></td>
                        <td><small> &nbsp;&nbsp;{{$setting->commune}}-{{$setting->zone}},{{$setting->rue}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small></td>
                    </tr>
                </table><br>
                <div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('MATRICULE : '.$data->matricule_no.' www.edengardenresort.bi ')) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Employé : {{ $data->employe->firstname }}&nbsp;{{ $data->employe->lastname }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Département : {{ $data->employe->departement->name }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Service : {{ $data->employe->service->name }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Fonction : {{ $data->employe->fonction->name }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date d'absence
                        </small><br>

                        <small>
                           &nbsp;&nbsp; Du : {{ \Carbon\Carbon::parse($take_conge_paye->date_heure_debut)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($take_conge_paye->date_heure_fin)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br><br><br><br><br><br><br><br>
                    <h3 style="text-align: center;">LETTRE DE DEMANDE ET APPROBATION DE CONGE</h3>
                    <div>
                        <h3><span>Objet : </span>Demande de Congé annuel</h3>
                        <div>
                          Motif: Au titre de l'année {{ $data->session }} j'aimerais prendre {{ $data->nbre_jours_conge_sollicite}} jours de congé payé. Je tiens par la présente à vous informer de mon souhait de prendre le congé tel que décrit ci-dessous:
                        </div>
                    </div><br>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Nombre de jours de congé annuel</th>
                                    <th>Nombre de jours de congé sollicité</th>
                                    <th>Nombre de jours de congé déjà pris</th>
                                    <th>Nombre de jours de congé restant</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>{{ $data->nbre_jours_conge_paye }}</td>
                                    <td>{{ $take_conge_paye->nbre_jours_conge_sollicite }}</td>
                                    <td>{{ $data->nbre_jours_conge_pris - $take_conge_paye->nbre_jours_conge_sollicite }}</td>
                                    <td>{{ $data->nbre_jours_conge_restant }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <div>
                Date de la demande et signature de l'Employé :
                </div>
                <br>
                <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Approbation du Supérieur hiérarchique(1er degré)</th>
                                    <th>Approbation du Supérieur hiérarchique(2eme degré)</th>
                                    <th>Approbation du Responsable de Gestion des ressources Hummaines</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>Nom et Signature : <br>{{ $take_conge_paye->valide_par }} </td>
                                    <td>Nom et Signature :<br> {{ $take_conge_paye->confirme_par }}</td>
                                    <td>Nom et Signature :<br> {{ $take_conge_paye->approuve_par }}</td>
                                </tr>
                                <tr>
                                    <td>Motif en cas du rejet :<br> .................................</td>
                                    <td>Motif en cas du rejet : <br>.................................</td>
                                    <td>Motif en cas du rejet :<br> .................................</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <br>
                        <span>NB:</span>prière informer le DG avant tout départ en congé d'un chef de cellule ou d'équipe et tous les cadres.
                    </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

