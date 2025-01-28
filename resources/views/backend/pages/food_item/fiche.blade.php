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
                   <img src="img/eden_logo.png" width="200" height="85">
                </div>
                <div>
                    <div>
                          <h4>
                           FICHE TECHNIQUE
                            </h4>
                    </div>
                    <div>
                        <table style="border: 1px solid black;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Article</th>
                                    <th>TVA</th>
                                    <th>PVT</th>
                                </tr>
                            </thead>
                            <tbody>
                               <tr>
                                    <td>1</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->vat }}%</td>  
                                    <td>{{ number_format($data->selling_price,0,',',' ') }}</td>          
                                </tr>
                            </tbody>
                        </table>
                        <ol>Ingredients :
                            @foreach($datas as $data)
                                <li>{{ $data->food->name }} : {{ $data->quantity }} ({{ $data->food->foodMeasurement->production_unit }})</li>
                            @endforeach
                        </ol>
                    </div>
                </div>
                <br>
                <small>
                    &nbsp;&nbsp; <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('www.edengardenresorts.bi, Powered by Ambaza Marcellin ')) !!} ">
                </small>
            </div>
        </div>
    </div>
</div>
</body>
</html>

