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

<body>
<div>
    <div>
        <div>
            <div>
                <div>
                   <img src="img/sotb_logo.jpg" width="85" height="85">
                </div>
                <div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$store_signature.' www.sotb.bi')) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp; ETAT DU STOCK
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ date('d') }}/{{date('m')}}/{{ date('Y')}}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h2 style="text-align: center;text-decoration: underline;">ETAT DU STOCK DES MATERIELS (PETIT STOCK)</h2>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Code</th>
                                    <th>Quantite</th>
                                    <th>Unité</th>
                                    <th>P.A</th>
                                    <th>Total P.A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->material->name }}</td>
                                    <td>{{ $data->material->code }}</td>
                                    <td>{{ $data->quantity }}</td>
                                    <td>{{ $data->unit }}</td>
                                    <td>{{ number_format($data->purchase_price,0,',',' ')}}</td>
                                    <td>{{ number_format($data->total_purchase_value,0,',',' ')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th style="background-color: rgb(150,150,150);" colspan="5"></th>
                                    <th>{{ number_format($totalPrixAchat,0,',',' ')}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br><br>
                    <div style="display: flex;">
                        <div style="float: left;">
                            &nbsp;&nbsp;Nom et signature
                            <div>
                                &nbsp;&nbsp;
                            </div>
                        </div>
                        <div style="float: right;">
                            &nbsp;&nbsp;Nom du Gestionnaire Stock et Signature
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

