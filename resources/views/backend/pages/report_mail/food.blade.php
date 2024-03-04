<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>


    <h3>{{ $mailData['title'] }}</h3>
    <h2>TOTAL DES VENTES CASHS ET CREDITS (CUISINE)</h2>
  
    <p>Salut, DU {{ $mailData['start_date']}} AU {{ $mailData['end_date']}} on a vendu {{ $mailData['total_amount']}} CASH et {{ $mailData['total_amount_credit']}} CREDITS,Le Système edenSoft ne cesse jamais de vous envoyer le rapport des ventes.</p>

     <p>
         POWERED BY Informaticien-Programmeur <a target="blank" href="https://ambazamarcellin.netlify.app/">Ambaza Marcellin</a>
     </p>
    <p>Merci !</p>
</body>
</html>