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
                   <img src="img/eden_logo.png" width="200" height="65">
                </div>
                <div>
                    <div style="float: left;">
                          <small> &nbsp;&nbsp;{{$setting->commune}}-{{$setting->zone}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->rue}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->telephone1}}-{{$setting->telephone2}}</small><br>
                          <small>&nbsp;&nbsp;{{$setting->email}}</small>
                    </div>
                    <br>
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black; background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(50)->generate('eSIGNATURE : '.$store_signature.' www.edengardenresorts.bi')) !!} ">
                        </small><br>

                        <small>
                           &nbsp;&nbsp; DU : {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h2 style="text-align: center;text-decoration: underline;">RAPPORT DU STOCK DES BOISSONS (GRAND STOCK)</h2>
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
                                    <th width="10%">V. S. Initial</th>
                                    <th width="10%">Q. Entree/Reception</th>
                                    <th width="10%">V. Entree/Reception</th>
                                    <th width="10%">Q. Sortie</th>
                                    <th width="10%">V. Sortie</th>
                                    <th width="10%">Q. S. Final</th>
                                    <th width="10%">V. S. Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ $data->drink->name }} </td>
                                    <td>{{ $data->drink->code }} </td>
                                    <td>{{ $data->quantity_stock_initial }} </td>
                                    @php 
                                        $value_stock_initial = $data->quantity_stock_initial * $data->drink->cump;
                                    @endphp
                                    <td>{{ number_format($value_stock_initial,0,',',' ') }} </td>
                                    <td>@if($data->quantity_stockin){{ $data->quantity_stockin }} @elseif($data->quantity_reception) {{ $data->quantity_reception }} @endif </td>
                                    <td>@if($data->value_stockin){{ number_format($data->value_stockin,0,',',' ') }} @elseif($data->value_reception) {{ number_format($data->value_reception,0,',',' ') }} @endif </td>
                                    <td>@if($data->quantity_stockout){{ $data->quantity_stockout }} @elseif($data->quantity_transfer) {{ $data->quantity_transfer }} @endif </td>
                                    @php
                                        $value_stockout = $data->quantity_stockout * $data->drink->cump;

                                        $value_transfer = $data->quantity_transfer * $data->drink->cump;
                                    @endphp
                                    <td>@if($data->value_stockout){{ number_format($value_stockout,0,',',' ') }} @elseif($data->value_transfer) {{ number_format($value_transfer,0,',',' ') }} @endif </td>
                                    <td>{{ ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer) }} </td>
                                    @php
                                        $quantite_finale = ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer);

                                        $quantity_stock_initial = $data->quantity_stock_initial;
                                    @endphp

                                    <td>{{ number_format(($quantite_finale * $data->drink->cump),0,',',' ') }} </td>
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

