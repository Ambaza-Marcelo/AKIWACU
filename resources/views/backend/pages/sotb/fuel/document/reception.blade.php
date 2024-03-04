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
                   <img src="img/sotb_logo.jpg" width="85" height="85">
                </div>
                <div>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$reception_signature.' www.sotb.bi ')) !!} ">
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Reception No: {{ $code }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;@if($data->order_no) Order No: {{ $data->order_no }} @else Purchase No : {{ $data->purchase_no }} @endif
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Monaie: {{ $invoice_currency }}
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Facture No: @if($data->invoice_no) {{ $data->invoice_no }} @endif
                        </small><br>
                        <small>
                           &nbsp;&nbsp;Supplier: @if($data->supplier_id) {{ $data->supplier->name }} @endif
                        </small><br>
                        <small>
                           &nbsp;&nbsp; Date : Le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br><br><br>
                    <div>
                        <h3 style="text-align: center;text-decoration: underline;">FICHE DE RECEPTION DES ARTICLES</h3>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Code</th>
                                    <th>Quantité cmdée</th>
                                    <th>Quantité reçu</th>
                                    <th>Unité</th>
                                    <th>PU</th>
                                    <th>P HTVA</th>
                                    <th>TVA</th>
                                    <th>TVAC</th>
                                    <th>TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->material->name }}</td>
                                    <td>{{ $data->material->code }}</td>
                                    <td>{{ $data->quantity_ordered }}</td>
                                    <td>{{ $data->quantity_received }}</td>
                                    <td>{{ $data->unit }}</td>
                                    <td>{{ number_format($data->purchase_price,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->price_nvat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->vat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->price_wvat,0,',',' ' )}}</td>
                                    <td>{{ number_format($data->total_amount_purchase,0,',',' ' )}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th style="background-color: rgb(150,150,150);" colspan="8"></th>
                                    <th>{{ number_format($total_wvat,0,',',' ') }}</th>
                                    <th>{{ number_format($totalValue,0,',',' ') }}</th>
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
                @if($total_wvat >= 1000000 || $totalValue >= 1000000)
                <h4 style="text-decoration: underline;text-align: center;">Pour la commission de Reception :</h4>
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
                    <br><br><br><br>
                        <h4 style="text-decoration: underline;text-align: center;">Pour le Gestionnaire du stock :</h4>
                        <div style="float: center;margin-left: 35%;">
                            &nbsp;&nbsp;Nom et signature
                            <div>
                            &nbsp;&nbsp;
                            </div>
                        </div>
            @else
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
            @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>

