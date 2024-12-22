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
                   <img src="{{ asset('img/eden_logo.png')}}" width="200" height="65">
                </div>
                <div>
                    <div style="float: left;">
                          <small> &nbsp;&nbsp;{{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->rue}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small>
                    </div>
                    <br><br><br><br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">FICHE D'INFORMATION SUR LE STAGIAIRE</h3>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th colspan="8" style="text-align: left;background-color: pink;">IDENTIFICATION DE LE STAGIAIRE</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>NOM ET PRENOM</th>
                                    <th>NATIONALITE</th>
                                    <th>CNI</th>
                                    <th>RESIDENCE ACTUEL</th>
                                    <th>TELEPHONE</th>
                                    <th>ADRESSE ELECTRONIQUE</th>
                                    <th>GENRE</th>
                                    <th>ETAT CIVIL</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>{{ $data->firstname }} {{ $data->lastname }}</td>
                                    <td>{{ $data->pays }}</td>
                                    <td>{{ $data->cni }}</td>
                                    <td>{{ $data->quartier_residence_actuel }}</td>
                                    <td>{{ $data->phone_no }}</td>
                                    <td>{{ $data->mail }}</td>
                                    <td>{{ $data->gender }}</td>
                                    <td>{{ $data->etat_matrimonial }}</td>
                                </tr>
                            </tbody>
                            <thead>
                                <tr>
                                    <th colspan="7" style="text-align: left;background-color: pink;">STATUT DE LE STAGIAIRE</th>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th>DATE D'ARRIVEE</th>
                                    <th>DATE D'EMBAUCHE</th>
                                    <th>DEPARTEMENT</th>
                                    <th>SERVICE</th>
                                    <th>FONCTION</th>
                                    <th>GRADE</th>
                                    <th>FRAIS DE DEPLACEMENT</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>{{ $data->date_debut }}</td>
                                    <td>{{ $data->date_fin }}</td>
                                    <td>{{ $data->departement->name }}</td>
                                    <td>{{ $data->service->name }}</td>
                                    <td></td>
                                    <td>{{ $data->grade->name }}</td>
                                    <td>{{ $data->somme_prime }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="float: left;">
                        <a href=""><img src="{{ asset('img/ISSh.gif') }}" width="60" title="Télécharger d'abord le document et puis imprimer"></a> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

