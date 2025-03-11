<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Fiche individuelle</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2><a id="haut" />Fiche individuelle </h2>
<p><a href="#A1">Contenu</a> - <a href="#A2">Param&eacute;trage</a> - <a href="#A3">Nom du fichier</a> </p>
<h4><a id="A1"/>Contenu</h4>
<p>Une fiche individuelle contient toutes les informations de la personne :</p>
<ul>
  <li>son &eacute;tat civil ;</li>
  <li>sa ou ses professions ;</li>
  <li>le ou les &eacute;v&egrave;nements auxquels elle est li&eacute;e ;</li>
  <li>les photos associ&eacute;es ; </li>
  <li>les parents avec leur photo par d&eacute;faut ;</li>
  <li>la ou les unions avec :
<ul><li>la photo du conjoint,</li>
  <li>les enfants</li>
 </ul></li>
<li>les notes de la personne ;</li>
<li>les documents li&eacute;s &agrave; la personne, &agrave; sa filiation et &agrave; son ou ses unions. </li>
</ul>
<p>Les fiches individuelles sont g&eacute;n&eacute;r&eacute;es en m&ecirc;me temps que l'arbre au <a href="generationPDF.php">format PDF</a>. <br />
</p>
<hr />
<h4><a id="A2"/> Param&eacute;trage <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Dans les <a href="preferences.php">pr&eacute;f&eacute;rences</a>, vous pouvez choisir de g&eacute;n&eacute;rer ou non les fiches individuelles ainsi que l'emplacement o&ugrave; sont g&eacute;n&eacute;r&eacute;s ces fichiers.</p>
<p><img src="images/preferences04.png" width="518" height="83" class="imageBord1pt" alt="" /></p>
<p>Si vous choisissez &laquo; Aucun &raquo;, aucune fiche ne sera g&eacute;n&eacute;r&eacute;e. </p>
<p>Pour l'emplacement des fichiers, vous avez deux choix :</p>
<ul>
  <li>vous pouvez choisir l'option &laquo; Par arbre &raquo; pour g&eacute;n&eacute;rer toutes les fiches individuelles d'un arbre dans un dossier propre &agrave; l'arbre. Ce dossier porte le nom de l'arbre ;</li>
  <li>vous pouvez choisir l'option &laquo; Commun &raquo; pour g&eacute;n&eacute;rer toutes les fiches dans un seul dossier appel&eacute; &laquo; Fiches &raquo;.</li>
</ul>
<p>Dans l'illustration ci-dessous, vous voyez :</p>
<ul>
  <li>le dossier &laquo; A 01 &raquo; qui contient les fiches quand vous demandez de g&eacute;n&eacute;rer les fiches par arbre ;</li>
  <li>le dossier &laquo; Fiches&raquo; qui contient les fiches quand vous demandez de g&eacute;n&eacute;rer les fiches dans un seul dossier.</li>
</ul>
<p><img src="images/fiche01.png" width="285" height="152" class="imageBord1pt" alt="" /></p>
<hr />
<h4><a id="A3"/>Nom du fichier <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Le nom du fichier PDF g&eacute;n&eacute;r&eacute; est compos&eacute; du nom et du pr&eacute;nom de la personne, de l'ann&eacute;e de naissance et de d&eacute;c&egrave;s (si une ann&eacute;e n'est pas connue, elle est remplac&eacute;e par un &laquo; X &raquo;) et de son identifiant dans la base. Cet identifiant est un num&eacute;ro unique qui est attribu&eacute; &agrave; la personne lors de sa cr&eacute;ation. Le fait de l'indiquer ici permet de retrouver des personnes dont un des renseignements a chang&eacute;.</p>
</div>
</div>
</body>
</html>
