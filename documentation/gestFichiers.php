<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Document sans nom</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2>Gestion des fichiers </h2>
<p><a id="haut"/><a href="#A1">Modification du nom de l'arbre</a> - <a href="#A2">Modification du nom de fichier</a> - <a href="#A3">Suppression de l'arbre</a> - <a href="#A4">G&eacute;n&eacute;ration d'images</a> - <a href="#A5">G&eacute;n&eacute;ration de fichier PDF</a> - <a href="#A6">Appliquer</a></p>
<p>La gestion de fichiers permet de g&eacute;rer les fichiers g&eacute;n&eacute;r&eacute;s par G&eacute;n&eacute;Graphe. Ce choix n'est actif que quand vous n'avez rien dans l'arbre courant. Pour ce faire, le plus simple est de choisir l'option Nouveau du menu Fichier.</p>
<p>Quand vous choisissez l'option Gestion des fichiers du menu Fichier, une fen&ecirc;tre s'ouvre :</p>
<p> <img src="images/gestion01.png" width="576" height="524" alt="" /></p>
<p>Le tableau contient plusieurs colonnes :</p>
<ul>
  <li>le nom de l'arbre ;</li>
  <li>le nom de fichier ;</li>
  <li>une case &agrave; cocher pour supprimer l'arbre ;</li>
  <li>l'&eacute;tat des fichiers images ;</li>
  <li>une case &agrave; cocher pour g&eacute;n&eacute;rer les images ;</li>
  <li>l'&eacute;tat des fichiers PDF ;</li>
  <li>une case &agrave; cocher pour g&eacute;n&eacute;rer les fichiers PDF.</li>
</ul>
<p>Les deux cases &agrave; cocher marqu&eacute;es Tout permettent de s&eacute;lectionner ou d&eacute;s&eacute;lectionner la totalit&eacute; de la colonne correspondante. </p>
<hr />
<h4><a id="A1"/>Modification du nom de l'arbre <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Vous pouvez modifier le nom de l'arbre. Il appara&icirc;t alors encadr&eacute; en rouge pour indiquer que vous avez fait une modification.</p>
<p><img src="images/gestion02.png" alt="" width="302" height="126" class="imageBord1pt" /></p>
<hr />
<h4><a id="A2"/>Modification du nom de fichier <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Vous pouvez modifier le nom de fichier. Il appara&icirc;t alors encadr&eacute; en rouge pour indiquer que vous avez fait une modification.</p>
<p><img src="images/gestion03.png" width="313" height="121" class="imageBord1pt"  alt=""/> </p>
<p>Toute modification de nom de fichier aura pour cons&eacute;quence que les fichiers images et le fichier PDF seront eux aussi renomm&eacute;s.</p>
<hr />
<h4><a id="A3"/>Suppression de l'arbre <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Pour supprimer un arbre, validez la case &agrave; cocher dans la colonne Supp. Cela supprimera l'arbre ainsi que les fichiers images et le fichier PDF.</p>
<p><img src="images/gestion04.png" width="575" height="118" class="imageBord1pt" alt="" /> </p>
<p>Remarquez que les colonnes G&eacute;n&eacute;rer pour les images et les fichiers PDF ne sont plus accessibles. Si l'une d'elles &eacute;tait coch&eacute;e, elle ne l'est plus quand vous demandez la suppression de l'arbre. C'est normal, il est inutile de demander &agrave; g&eacute;n&eacute;rer des images ou des fichiers PDF si vous voulez les supprimer.</p>
<p>Quand vous cliquez sur <span class="bouton">Appliquer</span>, G&eacute;n&eacute;Graphe vous demande de confirmer la suppression de l'arbre.</p>
<hr />
<h4><a id="A4"/>G&eacute;n&eacute;ration d'images <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Vous disposez de deux colonnes pour vous aider dans la g&eacute;n&eacute;ration des images. </p>
<p>Celle qui est intitul&eacute;e &laquo;&nbsp;Etat image&nbsp;&raquo; est renseign&eacute;e par G&eacute;n&eacute;Graphe&agrave; partir de tous les renseignements dont il dispose. Elle peut contenir &laquo;&nbsp;A jour&nbsp;&raquo; ou &laquo;&nbsp;A g&eacute;n&eacute;rer&nbsp;&raquo;. Elle contient &laquo;&nbsp;A g&eacute;n&eacute;rer&nbsp;&raquo; si, apr&egrave;s avoir g&eacute;n&eacute;r&eacute; les fichiers,</p>
<ul>
  <li>vous avez enregistr&eacute; l'arbre  ;</li>
  <li>vous avez modifi&eacute; les param&egrave;tres de G&eacute;n&eacute;Graphe    ;</li>
  <li>vous avez chang&eacute; un mod&egrave;le d'&eacute;tiquette utilis&eacute; dans l'arbre ;</li>
  <li>vous avez chang&eacute; une des personnes de l'arbre dans G&eacute;n&eacute;amania ;</li>
  <li>vous avez modifi&eacute; une relation pour une des personnes utilis&eacute;es (union ou filiation) ;</li>
  <li>vous avez modifi&eacute; une des images utilis&eacute;es dans l'arbre.</li>
</ul>
<p>Si une ou plusieurs de ces conditions sont pr&eacute;sentes, la colonne &laquo;&nbsp;G&eacute;n&eacute;rer&nbsp;&raquo; est coch&eacute;e.</p>
<p>Vous pouvez toujours choisir de g&eacute;n&eacute;rer ou non les fichiers en cochant ou non la colonne &laquo;&nbsp;A g&eacute;n&eacute;rer&nbsp;&raquo;.</p>
<hr />
<h4><a id="A5"/>G&eacute;n&eacute;ration de fichier PDF <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Tout ce qui expliqu&eacute; pour les images est valable pour le fichier PDF.</p>
<hr />
<h4><a id="A6"/>Appliquer <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Quand vous  cliquez sur  Appliquer, les modifications demand&eacute;es sont faites. Par exemple, vous avez demand&eacute; les modifications suivantes :</p>
<p><img src="images/gestion05.png" width="574" height="131" class="imageBord1pt" alt="" /> </p>
<p>G&eacute;n&eacute;Graphe vous demande de confirmer la suppression de l'arbre.</p>
<p><img src="images/gestion06.png" width="347" height="94" alt="" /></p>
<p>Les modifications demand&eacute;es sont faites et vous obtenez la fen&ecirc;tre suivante :</p>
<p><img src="images/gestion07.png" width="587" height="331" class="imageBord1pt" alt="" /> </p>
<p>Le rectangle en bas de la fen&ecirc;tre pr&eacute;sente le suivi des modifications appliqu&eacute;es.</p>
<p>Vous pouvez faire une autre demande :</p>
<p><img src="images/gestion08.png" width="574" height="121" class="imageBord1pt" alt="" /> </p>
<p>Apr&egrave;s avoir appliqu&eacute; les modifications, vous obtenez la fen&ecirc;tre :</p>
<p><img src="images/gestion09.png" width="587" height="320" class="imageBord1pt" alt="" /> </p>
</div>
</div>
</body>
</html>
