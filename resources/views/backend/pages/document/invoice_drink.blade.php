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
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$invoice_signature.' www.edengardenresorts.bi, Invoice Number : '.$invoice_number)) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Order Number: {{ $order_no }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Invoice Number: {{ $invoice_number }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">FACTURE</h3>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Code</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Prix Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->drink->name }}</td>
                                    <td>{{ $data->drink->code }}</td>
                                    <td>{{ $data->item_quantity }}</td>
                                    <td>{{ number_format($data->item_price,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->item_total_amount,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th style="background-color: rgb(150,150,150);" colspan="4"></th>
                                    <th>{{ number_format($totalValue,0,',',' ') }}</th>
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

