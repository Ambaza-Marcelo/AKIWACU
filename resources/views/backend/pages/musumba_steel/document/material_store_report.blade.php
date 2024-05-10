<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             text-align: center;
             font-size: 12px;
             width: auto;
        }

    </style>

<body>
<div>
    <div>
        <div>
            <div>
                <div>
                   <img src="img/logo_musumba.jpg" width="700" height="85">
                </div>
                <div>
                    <br>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('www.musumba_steel.bi')) !!} ">
                        </small><br>

                        <small>
                           &nbsp;&nbsp; DU : {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h2 style="text-align: center;text-decoration: underline;">RAPPORT DU STOCK DES MATERIELS DE BUREAU</h2>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">@lang('messages.date')</th>
                                    <th width="10%">@lang('messages.item')</th>
                                    <th width="10%">@lang('messages.code')</th>
                                    <th width="10%">Q. S. Initial</th>
                                    <th width="10%">Q. Entree/Reception</th>
                                    <th width="10%">Q. Sortie</th>
                                    <th width="10%">Q. S. Final</th>
                                    <th width="10%">Type Mouvement</th>
                                    <th width="10%">Document No</th>
                                    <th width="10%">Auteur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $data->material->name }} </td>
                                    <td>{{ $data->material->code }} </td>
                                    <td>{{ $data->quantity_stock_initial }} </td>
                                    <td>@if($data->quantity_stockin){{ $data->quantity_stockin }} @elseif($data->quantity_reception) {{ $data->quantity_reception }} @endif </td>
                                    <td>@if($data->quantity_stockout){{ $data->quantity_stockout }} @elseif($data->quantity_transfer) {{ $data->quantity_transfer }} @endif </td>
                                    <td>{{ ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer) }} </td>

                                    <td>{{ $data->type_transaction }} </td>
                                    <td>{{ $data->document_no }} </td>
                                    <td>{{ $data->created_by }} </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br>
                    <div style="display: flex;">
                        <div style="float: left center; margin-right: 0;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;Nom et prenom du Responsable
                            <div>
                                &nbsp;&nbsp;
                            </div>
                        </div>
                        <div style="float: right;margin-right: 15px;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;Nom et prenom du Responsable Hierarchique
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

