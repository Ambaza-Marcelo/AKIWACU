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
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$transfer_signature.' www.edengardenresorts.bi, transfert number : '.$transfer_no.' , requisition number : '.$requisition_no)) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp;DU: {{ $data->originStore->code }} AU {{$data->destinationStore->code}}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Requisition No: {{ $requisition_no }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Transfert No: {{ $transfer_no }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">BON DE TRANSFERT DES ARTICLES</h3>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Code</th>
                                    <th>Quantite requisitionee</th>
                                    <th>Quantite Transferee</th>
                                    <th>Unité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Prix Total Requisitionnee</th>
                                    <th>Prix Total Transferee</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->food->name }}</td>
                                    <td>{{ $data->food->code }}</td>
                                    <td>{{ $data->quantity_requisitioned }}</td>
                                    <td>{{ $data->quantity_transfered }}</td>
                                    <td>{{ $data->unit }}</td>
                                    <td>{{ number_format($data->price,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->total_value_requisitioned,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->total_value_transfered,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th style="background-color: rgb(150,150,150);" colspan="6"></th>
                                    <th>{{ number_format($totalValueRequisitioned,0,',',' ') }}</th>
                                    <th>{{ number_format($totalValueTransfered,0,',',' ') }}</th>
                                </tr>
                            </tfoot>
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

