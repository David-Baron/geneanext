<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Recherche de lien</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2>Recherche de lien</h2>
<p><a id="haut"/><a href="#A1">Particularit&eacute;s d'affichage </a></p>
<p>G&eacute;n&eacute;Graphe vous permet de rechercher s'il existe un lien entre deux personnes. Puisque vous allez ajouter plusieurs personnes &agrave; votre arbre, cette fonction n'est possible que quand votre arbre est vide. Pour faire la recherche de lien, allez dans le menu <a href="personnes.php">Personnes</a> et choisissez &laquo;&nbsp;Recherche lien&nbsp;&raquo;. Vous obtenez la fen&ecirc;tre suivante :</p>
<p><img src="images/recherche01.png" width="400" height="272" alt="" /> </p>
<p>Il vous faut choisir les deux personnes concern&eacute;es par la recherche. Cliquez sur un bouton <span class="bouton">Choisir</span> et s&eacute;lectionnez la personne que vous voulez. </p>
<p><img src="images/recherche02.png" width="400" height="272" alt="" /></p>
<p>Quand vous cliquez sur  <span class="bouton">Rechercher</span>, G&eacute;n&eacute;Graphe fait la recherche. Si un lien existe entre ces deux personnes, il s'affiche dans le bas de la fen&ecirc;tre.</p>
<p><img src="images/recherche03.png" width="400" height="272" alt="" /></p>
<p>Le bouton  <span class="bouton">Cr&eacute;er arbre</span> est alors utilisable. Quand vous cliquez dessus, les personnes se positionnent dans la page en cours.</p>
<p><img src="images/recherche04.png" width="261" height="182" class="imageBord1pt" alt="" /> </p>
<hr />
<h4><a id="A1"/>Particularités d'affichage <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Dans l'exemple ci-dessus, Marie STITU et Alain STITU sont fr&egrave;res et s&#339;urs. Dans le cas de demi-fr&egrave;res ou de demi-soeurs, le lien passe obligatoirement par le parent commun aux deux enfants :</p>
<p><img src="images/recherche05.png" width="233" height="150" class="imageBord1pt" alt="" /> </p>
</div>
</div>
</body>
</html>
