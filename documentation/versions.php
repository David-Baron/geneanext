<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Versions</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2>Versions</h2>
<h4>Version 4 (24 août 2011) </h4>
<ul>
  <li>Prise en compte des &eacute;v&egrave;nements li&eacute;s aux personnes dans les &eacute;tiquettes.</li>
  <li>Ajout de la possibilit&eacute; de d&eacute;finir un format d'affichage des dates.</li>
  <li>Révision de la documentation :
    <ul>
      <li>nouvelle présentation,</li>
      <li>corrections diverses,</li>
      <li>ajout des nouvelles fonctionnalités de GénéGraphe,</li>
      <li>réorganisation de certaines pages.</li>
    </ul>
  </li>
  </ul>
<hr />
<h4>Version 3 (1<sup>er</sup> novembre 2010) </h4>
<ul>
  <li>Ajout de la chronologie &agrave; l'arbre et ajout dans le fichier PDF de l'arbre.</li>
</ul>
<hr />
<h4>Version 2.3 (27 juillet 2010)</h4>
<ul>
  <li>Gestion de r&eacute;f&eacute;rences de personnes n&eacute;gatives.</li>
  <li>Affichage d'une barre de progression lors de la g&eacute;n&eacute;ration de fichier PDF.</li>
  <li>Prise en compte des documents PDF dans les fichiers PDF g&eacute;n&eacute;r&eacute;s.</li>
  <li>Possibilit&eacute; de ne pas g&eacute;rer les liens PDF dans chaque arbre.</li>
  <li>Correction d'une mauvaise gestion des formats de page dans la d&eacute;finition des arbres.</li>
</ul>
<hr />
<h4>Version 2.2 (diffusion &agrave; la demande) </h4>
<ul><li>Correction d'un message d'erreur en cas de suppression dans G&eacute;n&eacute;amania d'une image qui avait &eacute;t&eacute; utilis&eacute;e dans un arbre.</li>
  <li>Mise &agrave; niveau des fiches PDF pour tenir compte de l'&eacute;volution de G&eacute;n&eacute;amania. </li>
</ul>
<hr />
<h4>Version 2.1 (18 janvier 2010) </h4>
<ul>
  <li>Correction de la mauvaise gestion du num&eacute;ro de version qui bloquait le d&eacute;marrage du logiciel.</li>
</ul>
<hr />
<h4>Version 2.0 (novembre 2009) </h4>
<ul>
  <li>La fen&ecirc;tre &laquo;&nbsp;arbre&nbsp;&raquo; affiche la date de cr&eacute;ation et la date de derni&egrave;re modification</li>
  <li> Le menu &laquo;&nbsp;fichier&nbsp;&raquo; affiche les 10 derniers arbres modifi&eacute;s avec la possibilit&eacute; de les ouvrir</li>
  <li> Une apostrophe dans un nom de famille provoquait une erreur</li>
  <li>Le calcul de la dimension des &eacute;tiquettes de famille comportait une erreur quand elle ne contenait qu'une ligne</li>
  <li> Les fiches individuelles au format PDF ont la r&eacute;f&eacute;rence de la personne dans le nom du fichier</li>
  <li> La prise en compte de la r&eacute;organisation de la base : suppression des tables zones_geographiques et niveaux_zones</li>
  <li> La prise en compte des documents dans les fiches individuelles</li>
  <li> La cr&eacute;ation d'une &eacute;tiquette de texte se fait au point de coordonn&eacute;es (100,100) dans la fen&ecirc;tre affich&eacute;e au lieu du document</li>
  <li> La fen&ecirc;tre de gestion des mod&egrave;les d'&eacute;tiquettes contenait une erreur de fran&ccedil;ais, le bouton &laquo;&nbsp;Quitter&nbsp;&raquo; est mal orthographi&eacute; </li>
  <li>La mise &agrave; jour de la documentation</li>
</ul>
<hr />
<h4>Version 1.1 (juin 2009) </h4>
<ul>
  <li>Affichage du nom de l'arbre et de son code dans le titre de la fen&ecirc;tre</li>
  <li>Ajout des fiches individuelles au format PDF</li>
  <li>R&eacute;vision de la gestion des caract&egrave;res de fin de ligne dans les descriptifs HTML     pour faciliter l'exportation des bases</li>
  <li>R&eacute;vision de la documentation </li>
</ul>
<hr />
<h4>Version 1.0.4</h4>
<ul>
  <li>Corrig&eacute; l'ouverture  des fen&ecirc;tres de modification des liens de familles et de liens de personnes, car elles ne se positionnent pas au bon endroit</li>
  <li>Revu la gestion de la s&eacute;lection des liens de famille</li>
  <li>Corrig&eacute; la gestion des pages lors de l'ajout d'ascendance</li>
  <li>Correction du titre de la fen&ecirc;tre principale</li>
</ul>
<hr />
<h4>Version 1.0.3</h4>
<ul>
  <li>R&eacute;&eacute;criture d'une fonction utilis&eacute;e pour la recherche de liens entre deux personnes. Cela fait passer le temps de d&eacute;marrage de G&eacute;n&eacute;Graphe pour 51 872 personnes, de 37 minutes &agrave; 6 mn 23 s</li>
  <li>Dans la recherche de liens entre les personnes, suppression de la relation fratrie (gain de temps sur 51 872 personnes, on passe de 6 mn 23 s &agrave; 0,7 s de temps de traitement !) </li>
  <li>Optimisation de deux classes</li>
  <li>Modifi&eacute; la gestion de la base de donn&eacute;es</li>
</ul>
<hr />
<h4>Version 1.0.1</h4>
<ul>
  <li>Ajout de la fen&ecirc;tre &laquo;&nbsp;?&nbsp;&raquo; avec les renseignements sur la version, l'environnement&hellip; </li>
  <li>Dans les rubriques des &eacute;tiquettes de personnes, ajouter le num&eacute;ro SOSA</li>
  <li>Gestion de l'absence d'une photo d'une personne</li>
</ul>
<hr />
<h4>Version 1 (d&eacute;cembre 2008) </h4>
<p>Version initiale</p>
</div>
</div>
</body>
</html>
