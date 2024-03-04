<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
</head>
<body>


    <h3>{{ $mailData['title'] }}</h3>
  
    <p>Salut,{{ $mailData['auteur']}} vient d'annuler la facture No {{ $mailData['invoice_number']}} au Système de facturation électronique edenSoft ({{ $mailData['cn_motif']}}),Le Système edenSoft ne cesse jamais de vous envoyer la notification en cas des operations douteuses.</p>

     <p>
         POWERED BY Informaticien-Programmeur <a target="blank" href="https://ambazamarcellin.netlify.app/">Ambaza Marcellin</a>
     </p>
    <p>Merci !</p>
</body>
</html>