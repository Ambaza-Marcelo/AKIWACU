<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             text-align: center;
             width: 83px;
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
                           &nbsp;&nbsp; RAPPORT SIMPLIFIE
                        </small><br>
                        <small>
                           &nbsp;&nbsp;  DU {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} AU {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} 
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h2 style="text-align: center;text-decoration: underline;">RAPPORT SIMPLIFIE</h2>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>Specification</th>
                                    <th>C.U.M.P</th>
                                    <th>Entrée</th>
                                    <th style="background-color: rgb(150,150,150);">Val. Entrée</th>
                                    <th>Sortie</th>
                                    <th style="background-color: rgb(150,150,150);">Val. Sortie</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->article->name }}</td>
                                    <td>{{ $data->article->specification }}</td>
                                    <td>{{ number_format($data->article->unit_price,0,',',' ') }}</td>
                                    <td>{{ $data->quantity_in }}</td>
                                    @php $entreeValeurTot = $data->article->unit_price * $data->quantity_in
                                    @endphp
                                    <td style="background-color: rgb(150,150,150);">{{ number_format($entreeValeurTot,0,',',' ') }}</td>
                                    <td>{{ $data->quantity_out }}</td>
                                    @php $sortieValeurTot = $data->article->unit_price * $data->quantity_out
                                    @endphp
                                    <td style="background-color: rgb(150,150,150);">{{ number_format($sortieValeurTot,0,',',' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br>
                    <div style="display: flex;">
                        <div style="float: left center; margin-right: 0;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;signature
                            <div>
                                &nbsp;&nbsp;
                            </div>
                        </div>
                        <div style="float: right;margin-right: 15px;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;Gestionnaire et signature
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

