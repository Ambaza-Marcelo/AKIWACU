<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tbody,thead,table,tr,th,td{
             border: 1px solid black;
             text-align: center;
             width: 100%;
        }

    </style>
</head>
<body>
<div class="container">
    <div>
        <div>
            <div>
                <div>
                   <img src="img/eden_logo.png" width="200" height="85">
                </div>
                <div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 10px;">
                        <small>
                            {{ \Carbon\Carbon::parse($data->date_debut)->format('M,Y') }}
                        </small>
                    </div>
                    <br><br>
                    <br>
                    <div>
                        <table style="border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;background-color: #027789;">BULLETIN DE PAIE DU {{ \Carbon\Carbon::parse($data->date_debut)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($data->date_fin)->format('d/m/Y') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>NOM ET PRENOM</th>
                                    <td>@if($data->employe_id){{ $data->employe->firstname }} {{ $data->employe->lastname }} @endif</td>
                                    <th>MATRICULE</th>
                                    <td>@if($data->employe_id){{ $data->employe->matricule_no }} @endif</td>
                                </tr>
                                <tr>
                                    <th>NUMERO DE COMPTE</th>
                                    <td>@if($data->employe_id){{ $data->employe->numero_compte }} @endif</td>
                                    <th>CIN</th>
                                    <td>@if($data->employe_id){{ $data->employe->cni }} @endif</td>
                                </tr>
                                <tr>
                                    <th>FONCTION</th>
                                    <td>@if($data->employe_id){{ $data->employe->fonction->name }} @endif</td>
                                    <th>ANCIENNETE</th>
                                    @php
                                        $time1 = strftime(strtotime($data->employe->date_debut));
                                        $time2 = strftime(strtotime(\Carbon\Carbon::now()));


                                        $diff = $time2 - $time1;
 
                                        $jours = ($diff / 3600)/24; 
                                    @endphp
                                    <td>@if($jours <= 30)<strong>{{ number_format($jours,0)}}</strong> jour(s) @elseif($jours >= 365.25)<strong>{{ number_format($jours/365.25,0)}} an(s)</strong> @else {{ number_format(($jours / 30),0) }} mois @endif</td>
                                </tr>
                                <tr>
                                    <th>GENRE</th>
                                    <td>@if($data->employe->gender === '1') MASCULIN @else FEMININ @endif</td>
                                    <th>Nbre de personnes a charges</th>
                                    <td>@if($data->employe->statut_matrimonial === '1') {{ $data->employe->children_number + 1 }} @elseif($data->employe->statut_matrimonial === '2') {{ $data->employe->children_number }} @elseif($data->employe->statut_matrimonial === '3') {{ $data->employe->children_number }} @else 0 @endif</td>
                                </tr>
                                <tr>
                                    <th>DATE D'EMBAUCHE</th>
                                    <td>@if($data->employe_id){{ \Carbon\Carbon::parse($data->employe->date_debut)->format('d/m/Y') }} @endif</td>
                                    <th>DATE DE FIN</th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table><br>
                        <table style="border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left;background-color: #027789;">
                                    <th>RUBRIQUES</th>
                                    <th>BASE</th>
                                    <th>TAUX(PART EMPLOYE)</th>
                                    <th>MONTANT(PART EMPLOYE)</th>
                                    <th>MONTANT(PART EMPLOYEUR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SALAIRE DE BASE</td>
                                    <td>@if($data->employe_id){{ number_format($data->somme_salaire_base,0,',',' ') }} @endif</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>ALLOCATIONS FAMILIALES</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ number_format($data->allocation_familiale,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>INDEMNITE DE DEPLACEMENT</td>
                                    <td>{{ number_format($data->somme_salaire_base,0,',',' ') }}</td>
                                    <td>15%</td>
                                    <td>{{ number_format($data->indemnite_deplacement,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>INDEMNITE DE LOGEMENT</td>
                                    <td>{{ number_format($data->somme_salaire_base,0,',',' ') }}</td>
                                    <td>60%</td>
                                    <td>{{ number_format($data->indemnite_logement,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>PRIME DE FONCTION</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ number_format($data->prime_fonction,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr style="background-color: #027789;">
                                    <td>SALAIRE BRUT</td>
                                    <td>{{ number_format($data->somme_salaire_base,0,',',' ') }}</td>
                                    <td></td>
                                    @php
                                        $salaire_brut = ($data->somme_salaire_base + $data->indemnite_deplacement + $data->indemnite_logement + $data->prime_fonction + $data->allocation_familiale);
                                    @endphp
                                    <td>{{ number_format($salaire_brut,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>INSS</td>
                                    <td>@if($salaire_brut < 450000){{ number_format($salaire_brut,0,',',' ') }} @else 450 000 @endif</td>
                                    <td>4%</td>
                                    <td>{{ number_format($data->somme_cotisation_inss,0,',',' ') }}</td>
                                    <td>{{ number_format(floor($data->inss_employeur),0,',',' ') }}</td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>ASSURANCE MALADIE</td>
                                    <td>@if($salaire_brut < 250000){{ number_format($salaire_brut,0,',',' ') }} @else 250 000 @endif</td>
                                    <td></td>
                                    <td>{{ number_format($data->assurance_maladie_employe,0,',',' ') }}</td>
                                    <td>{{ number_format($data->assurance_maladie_employeur,0,',',' ') }}</td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>SOINS MEDICAUX</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ number_format($data->soins_medicaux,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    @php

                                        if($salaire_brut < 250000){
                                            $assurance_maladie = 0;
                                        }else{
                                           $assurance_maladie = 6000; 
                                        }

                                        $salaire_brut = ($data->somme_salaire_base + $data->indemnite_deplacement + $data->indemnite_logement + $data->prime_fonction + $data->allocation_familiale);


                                        $base_imposable = $salaire_brut - $data->indemnite_deplacement - $data->indemnite_logement - $data->somme_cotisation_inss - $data->assurance_maladie_employe; 

                                        if ($base_imposable >= 0 && $base_imposable <= 150000) {
                                        $somme_impot = 0;
                                        }elseif ($base_imposable > 150000  && $base_imposable <= 300000) {
                                            $somme_impot = (($base_imposable - 150000) * 20)/100;
                                        }elseif ($base_imposable > 300000) {
                                        $somme_impot = 30000 + (($base_imposable - 300000) * 30)/100;    
                                        }
                                    @endphp
                                    <td>IMPOT SUR REVENU</td>
                                    <td>{{ number_format($base_imposable,0,',',' ') }}</td>
                                    <td>@if($base_imposable >= 0 && $base_imposable <= 150000) 0% @elseif($base_imposable >= 150000 && $base_imposable <= 300000) 20% @else 30% @endif</td>
                                    <td>{{ number_format($somme_impot,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>RETENUE PRET</td>
                                    <td>{{ number_format($salaire_brut,0,',',' ') }}</td>
                                    <td></td>
                                    <td>{{ number_format($data->retenue_pret,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>AUTRES RETENUES</td>
                                    <td>{{ number_format($salaire_brut,0,',',' ') }}</td>
                                    <td></td>
                                    <td>{{ number_format($data->autre_retenue,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr style="background-color: #027789;">
                                    <td>TOTAL DES DEDUCTIONS</td>
                                    <td>{{ number_format($salaire_brut,0,',',' ') }}</td>
                                    <td></td>
                                    @php
                                            $total_deductions = $data->somme_cotisation_inss + $data->assurance_maladie_employe + $data->somme_impot + $data->retenue_pret + $data->soins_medicaux + $data->autre_retenue;
                                    @endphp
                                    <td>{{ number_format($total_deductions,0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td>SALAIRE NET</td>
                                    <td>{{ number_format($salaire_brut,0,',',' ') }}</td>
                                    <td></td>
                                    <td>{{ number_format(($salaire_brut - $total_deductions),0,',',' ') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <table style="border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>REMUNERATION BRUTE</th>
                                    <th>TOTAL DEDUCTIONS</th>
                                    <th>NET A PAYER</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>MENSUEL</td>
                                    <td>{{ number_format($salaire_brut,0,',',' ') }}</td>
                                    <td>{{ number_format($total_deductions,0,',',' ') }}</td>
                                    <td>{{ number_format(($salaire_brut - $total_deductions),0,',',' ') }}</td>
                                </tr>
                            </tbody>
                        </table><br>
                        <table style="border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>RESPONSABLE RH ET SIGNATURE</th>
                                    <th>EMPLOYE ET SIGNATURE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>.......................................</td>
                                    <td>@if($data->employe_id){{ $data->employe->firstname }} {{ $data->employe->lastname }}......................... @endif</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

