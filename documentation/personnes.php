<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Menu Personnes</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2>Menu Personnes</h2>
<p>Ce menu propose des actions identiques &agrave; celles propos&eacute;es par les boutons. Il s'agit de :</p>
<ul>
  <li><a href="ajoutPersonne.php">ajouter une personne</a> ;</li>
  <li><a href="ajoutParents.php">ajouter les parents</a> &agrave; une ou plusieurs personnes ;</li>
  <li><a href="ajoutConjoint.php">ajouter le ou les conjoints</a> &agrave; une ou plusieurs personnes ;</li>
  <li><a href="ajoutEnfant.php">ajouter le ou les enfants</a> &agrave; une ou plusieurs unions ;</li>
  <li><a href="ajoutFratrie.php">compl&eacute;ter la fratrie</a> d'une ou plusieurs personnes ;</li>
  <li><a href="ascendance.php">compl&eacute;ter l'ascendance</a> d'une ou plusieurs personnes ;</li>
  <li><a href="lien.php">rechercher le lien</a> entre deux personnes ;</li>
  <li>importer une recherche de cousinage de Gn&eacute;amania ; </li>
  <li><a href="importer.php">importer</a> un ou plusieurs &eacute;l&eacute;ments. </li>
</ul>
<p><img src="images/personnes02.png" width="120" height="230" class="imageBord1pt" alt="" /></p>
<h4>Import de cousinage </h4>
<p>Ce choix permet de dessiner un arbre &agrave; partir d'une recherche de parent&eacute; faite dans G&eacute;n&eacute;amania.</p>
<p>En premier lieu, il faut faire la recherche dans G&eacute;n&eacute;amania en demandant de sauver la demande, par exemple :</p>
<p><img src="images/personnes03.png" width="409" height="181" class="imageBord1pt" alt="" /> </p>
<p>Le fait de cocher l'option &laquo;&nbsp;Sauver demande&nbsp;&raquo; m&eacute;morise cette demande. Cela permet, dans G&eacute;n&eacute;Graphe, de r&eacute;cup&eacute;rer cette demande et de dessiner l'arbre correspondant. Il suffit de choisir la ligne &laquo;&nbsp;Cousinage&nbsp;&raquo; du menu &laquo;&nbsp;Personnes&nbsp;&raquo; pour que G&eacute;n&eacute;Graphe g&eacute;n&egrave;re l'arbre. </p>
<p>G&eacute;n&eacute;amania vous propose ce r&eacute;sultat de recherche</p>
<p><img src="images/personnes04.png" width="806" height="258" class="imageBord1pt" alt="" /></p>
<p>et   G&eacute;n&eacute;Graphe vous propose ceci</p>
<p><img src="images/personnes05.png" width="267" height="191" class="imageBord1pt" alt="" /></p>
</div>
</div>
</body>
</html>
