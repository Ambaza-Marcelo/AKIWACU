<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style type="text/css">
        tr,th,td{
             border: 1px solid black;
             width: 100%;
             text-align: center;
        }
        .signature{
            display: flex;
        }
    </style>
</head>

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
                    <div style="float: right; border-top-right-radius: 10px solid black;border-top-left-radius: 10px solid black;border-bottom-right-radius: 10px solid black;border-bottom-left-radius: 10px solid black;background-color: rgb(150,150,150);width: 242px;padding: 20px;">
                        <small>
                           &nbsp;&nbsp; Date : Le {{ date('d') }}/{{date('m')}}/{{ date('Y')}}
                        </small>
                    </div>
                    <br><br><br><br><br>
                    <br><br><br>
                    <div>
                        <h2 style="text-align: center;text-decoration: underline;">REFERENCE DE L'INVENTAIRE</h2>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Code</th>
                                    <th>Article</th>
                                    <th>Specification</th>
                                    <th>Qté Actuelle</th>
                                    <th>Prix Unitaire</th>
                                    <th>Nouvelle Qté</th>
                                    <th>Nouveau Prix Unit.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                               <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $data->article->code }}</td>
                                    <td>{{ $data->article->name }}</td>
                                    <td>{{ $data->article->specification }}</td>
                                    <td>{{ $data->quantity }}</td>
                                    <td>{{ number_format($data->unit_price,0,',','.' )}}fbu</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br>
                <div>
                    &nbsp;&nbsp;
                </div>
                <br>
                    <div style="display: flex;">
                        <div style="float: left center; margin-right: 0;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;signature
                            <div>
                                &nbsp;&nbsp;
                                
                            </div>
                        </div>

                        <div style="float: right center; margin-left: 250px;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;signature
                            <div>&nbsp;&nbsp;
                               
                            </div>
                        </div>
                        <div style="float: right;width: 242px;padding-bottom: 40px;">
                            &nbsp;&nbsp;Gestionnaire et Signature
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

