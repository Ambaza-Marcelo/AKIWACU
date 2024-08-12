<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>


    <h3>{{ $mailData['title'] }}</h3>
  
    <p>Salut,{{ $mailData['auteur']}} vient d'entrer {{ $mailData['totalQuantity'] }} articles equivaut a {{ $mailData['totalValue'] }} au MAGASIN EGR ({{ $mailData['description']}}),plus de details voir le document {{ $mailData['stockin_no'] }},Le Système AKIWACU ne cesse jamais de vous envoyer la notification en cas des operations pour le MAGASIN EGR.</p>

     <p>
         POWERED BY Informaticien-Programmeur <a target="blank" href="https://ambazamarcellin.netlify.app/">Ambaza Marcellin</a>
     </p>
    <p>Merci !</p>
</body>
</html>