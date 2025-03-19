<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Document sans nom</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.table1
{
	width: 90px;
	height: 100px;	
	border: 1px;
	padding: 1px;
	margin: 0px;
	border-color: #000000;
}
</style>
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2><a id="haut"/>G&eacute;n&eacute;ration de fichier PDF </h2>
<p><a href="#A1">Contenu du fichier</a> - <a href="#A2">Liens entre les fichiers</a> - <a href="#A3">Utilisation du fichier PDF</a></p>
<p>La g&eacute;n&eacute;ration de fichier PDF vous permet d'obtenir votre arbre dans un fichier PDF. Vois pouvez alors donner ce fichier &agrave; qui vous voulez, car il est consultable sans avoir besoin du logiciel G&eacute;n&eacute;amania ni G&eacute;n&eacute;Graphe. En m&ecirc;me temps, vous pouvez g&eacute;n&eacute;rer des <a href="fiche.php">fiches individuelles</a>, suivant le param&eacute;trage que vous aurez fait. Vous pouvez aussi donner ces fichiers &agrave; qui vous voulez.</p>
<h4><a id="A1"/>Contenu du fichier</h4>
<p>Le fichier PDF contient &eacute;ventuellement plusieurs pages. Si votre arbre contient plusieurs pages, chacune d'elles sera g&eacute;n&eacute;r&eacute;e ind&eacute;pendamment dans le fichier PDF. Les pages sont g&eacute;n&eacute;r&eacute;es en commen&ccedil;ant par la premi&egrave;re ligne puis par la ou les suivantes. Voici l'ordre des pages dans le fichier PDF d'un arbre qui contient 3 pages en largeur et 2 pages en hauteur. </p>
<table class="table1" summary="">
  <tr>
    <td class="centrage">1</td>
    <td class="centrage">2</td>
    <td class="centrage">3</td>
  </tr>
  <tr>
    <td class="centrage">4</td>
    <td class="centrage">5</td>
    <td class="centrage">6</td>
  </tr>
</table>
<hr />
<h4><a id="A2"/>Liens entre les fichiers <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Quand une personne se trouve dans plusieurs arbres, G&eacute;n&eacute;Graphe ajoute dans chaque arbre des ic&ocirc;nes <img src="images/lien.png" width="23" height="16" alt="" /> pour montrer les liens qui existent entre eux.</p>
<p><img src="images/lienPdf01.png" width="320" height="85" class="imageBord1pt" alt="" /></p>
<p>Dans cet exemple, Aude VAISSELLE est pr&eacute;sente dans deux autres arbres alors que Li AISON n'est repr&eacute;sent&eacute;e dans aucun autre arbre.</p>
<p>Ces ic&ocirc;nes figureront dans le fichier PDF g&eacute;n&eacute;r&eacute; et permettront de passer d'un fichier &agrave; l'autre. </p>
<hr />
<h4><a id="A3"/>Utilisation du fichier PDF <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Quand vous affichez le fichier PDF g&eacute;n&eacute;r&eacute;, vous avez les m&ecirc;mes ic&ocirc;nes que dans G&eacute;n&eacute;Graphe. Quand le curseur se place sur l'ic&ocirc;ne en forme de parchemin, Acrobat Reader vous affiche le nom du fichier PDF o&ugrave; est d&eacute;finie cette personne. </p>
<p><img src="images/lienPdf02.png" width="255" height="82" class="imageBord1pt" alt="" /></p>
<p>Quand vous cliquez sur la fl&egrave;che verte, le curseur change de forme (il devient une main) et, en cliquant, vous ouvrez le fichier PDF.</p>
<p><img src="images/lienPdf03.png" width="323" height="70" class="imageBord1pt" alt="" /></p>
</div>
</div>
</body>
</html>
