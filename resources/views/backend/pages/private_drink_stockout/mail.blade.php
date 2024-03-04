<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>


    <h3>{{ $mailData['title'] }}</h3>
  
    <p>Salut,{{ $mailData['auteur']}} vient de sortir {{ $mailData['totalQuantity'] }} articles equivaut a {{ $mailData['totalValue'] }} au stock prive de PDG ({{ $mailData['description']}}),plus de details voir le document {{ $mailData['stockout_no'] }},Le Système AKIWACU ne cesse jamais de vous envoyer la notification en cas des operations pour le stock de PDG.</p>

     <p>
         POWERED BY Informaticien-Programmeur <a target="blank" href="https://ambazamarcellin.netlify.app/">Ambaza Marcellin</a>
     </p>
    <p>Merci !</p>
</body>
</html>