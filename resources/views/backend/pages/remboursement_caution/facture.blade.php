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
                   <img src="img/eden_logo.png" width="200" height="85">
                </div>
                <div>
                        <small>
                           <strong style="text-decoration: underline;">Remboursement Caution {{ $invoice_number }} du {{ \Carbon\Carbon::parse($data->invoice_date)->format('d/m/Y H:i:s') }}</strong>
                        </small><br>
                    </div>
                    <div>
                          <small>NIF : {{$setting->nif}}</small><br>
                          <small>RC : {{$setting->rc}}</small><br>
                          <small>Centre Fiscal : {{ $setting->tp_fiscal_center }}</small><br>
                          <small>Secteur d'activite : {{ $setting->tp_activity_sector }}</small><br>
                          <small> Forme Juridique : {{ $setting->tp_legal_form }}</small><br>
                          <small> Adresse : {{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>Telephone : {{$setting->telephone1}}/{{$setting->telephone2}}</small><br>
                          <small>Assujetti a la TVA : |oui<input type="checkbox" @if($setting->vat_taxpayer == '1') checked="checked" @endif>|Non<input type="checkbox" @if($setting->vat_taxpayer == '0') checked="checked" @endif></small>
                          <hr> 
                    </div>               
                    <div>
                        <small>Nom et Prenom :@if($data->client_id){{ $data->client->customer_name }} @else {{ $data->customer_name }} @endif</small> <br>
                        <small>NIF : @if($data->client_id){{ $data->client->customer_TIN }} @else {{ $data->customer_TIN }} @endif</small> <br>
                        <small>Adresse : @if($data->client_id){{ $data->client->customer_address }} @else {{ $data->customer_address }} @endif / @if($data->client_id){{ $data->client->telephone }} @else {{ $data->telephone }} @endif</small> <br>
                        <small>Assujetti a la TVA : |oui<input type="checkbox" @if($data->client->vat_customer_payer == '1') checked="checked" @endif>|Non<input type="checkbox" @if($data->client->vat_customer_payer == '0') checked="checked" @endif></small><br>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>DESIGNATION</th>
                                    <th>Qtes</th>
                                    <th>PV. U</th>
                                    <th>Montant Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>@if($data->drink_id){{ $data->drink->name }} @elseif($data->food_item_id){{ $data->foodItem->name }} @elseif($data->bartender_item_id){{ $data->bartenderItem->name }} @elseif($data->salle_id){{ $data->salle->name }} @elseif($data->service_id){{ $data->service->name }}  @elseif($data->breakfast_id) {{ $data->breakFast->name }} @elseif($data->swiming_pool_id) {{ $data->swimingPool->name }} @elseif($data->kidness_space_id) {{ $data->kidnessSpace->name }} @else {{ $data->barristItem->name }} @endif</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_price,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        <div style="float: right;border: 1px solid black;">
                        <small><strong>
                           &nbsp;&nbsp;Montant total à déduire : -{{ number_format($item_total_amount,0,',',' ' )}}</strong>
                        </small>
                        </div>
                    </div>
                    <small>{{ $invoice_signature }} : ID</small><br>
                    @if($data->employe_id)
                    <small>Serveur(se) : {{ $data->employe->name }}</small>
                    @endif
                    <br>
                    <small>Caissier(e) : {{ $facture->auteur }}</small>
                    <br><br>
                    <small>Thank You For Visit</small>
                    <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('eSIGNATURE : '.$invoice_signature.' www.edengardenresorts.bi')) !!} ">
                    </small>
            </div>
        </div>
    </div>
</div>
</body>
</html>

