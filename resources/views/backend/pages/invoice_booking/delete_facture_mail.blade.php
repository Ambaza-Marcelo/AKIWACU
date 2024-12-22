<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>


    <h3>{{ $mailData['title'] }}</h3>
  
    <p>Salut,{{ $mailData['auteur']}} vient de supprimer le facture No {{ $mailData['invoice_number']}},Le Systeme AMBAZAPP ne cesse jamais vous envoyer la notification en cas des operations douteuses.</p>

     <p>
         DESIGNED BY AIT 
     </p>
    <p>Merci !</p>
</body>
</html>