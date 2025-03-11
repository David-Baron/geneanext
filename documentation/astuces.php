<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Astuces</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2><a id="haut"/>Astuces</h2>
<p> <a href="#A1">Un seul parent</a> - <a href="#A2">Pr&eacute;nom usuel diff&eacute;rent du premier</a> - <a href="#A3">Acc&egrave;s rapide dans une liste</a> - <a href="#A4">Mod&egrave;les d'&eacute;tiquettes</a> - <a href="#A5">Difficult&eacute;s pour s&eacute;lectionner des objets</a> - <a href="#A6">Impression avec des marges</a> - <a href="#A7">Obtenir des images de grande dimension</a> </p>
<h4><a id="A1"/>Voir un seul des parents d'une personne</h4>
		
<table class="sansBord" summary="">
  <tr class="sansBord">
    <td class="sansBord"><img src="images/astuces01.jpg" width="149" height="244" class="imageBord1pt" alt="" /></td>
    <td class="sansBord"><p>G&eacute;n&eacute;Graphe montre les deux parents d'une personne quand on clique sur le bouton d'ajout des parents.</p>
      <p>Parfois, on veut n'avoir qu'un des parents. Pour cela, vous avez trois possibilit&eacute;s :</p>
      <ul>
        <li>vous placer l'enfant puis vous cliquez sur  le bouton <img src="images/personne.png" width="35" height="35" alt="" /> pour placer le parent ;</li>
        <li>vous placer l'enfant puis vous cliquez sur le bouton <img src="images/parents.png" alt="" height="35" width="35" />, il suffit d'effacer le parent que vous ne voulez pas voir ;</li>
        <li>vous placez le parent puis vous cliquez sur le bouton <img src="images/personne.png" width="35" height="35" alt="" />  pour placer l'enfant.</li>
    </ul></td>
  </tr>
</table>
<hr />
<h4><a id="A2"/>Pr&eacute;nom usuel diff&eacute;rent du premier  <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord"/></a></h4>
<p>Si le pr&eacute;nom usuel d'une personne n'est pas le premier, il faut modifier la fiche de la personne dans G&eacute;n&eacute;amania comme cela est expliqu&eacute; pour les <a href="modeleEtiq.php#A5">mod&egrave;les d'&eacute;tiquette de personne</a>. </p>
<hr />
<h4><a id="A3"/>Acc&egrave;s rapide dans une liste  <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Dans les listes (par exemple celle de <a href="ajoutPersonne.php">choix d'une personne</a>), vous pouvez aller tr&egrave;s rapidement &agrave; une ligne de la fa&ccedil;on suivante :</p>
<ul>
  <li> cliquez dans la liste ;</li>
  <li>saisissez un, deux ou trois caract&egrave;res du d&eacute;but du mot &agrave; atteindre.</li>
</ul>
<p>La liste va alors directement &agrave; la premi&egrave;re ligne qui correspond &agrave; votre saisie. </p>
<hr />
<h4><a id="A4"/>Mod&egrave;les d'&eacute;tiquettes libres <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Si vous voulez disposer d'&eacute;tiquettes libres identiques, vous pouvez en d&eacute;finir une dans un arbre que vous appelez par exemple &laquo;&nbsp;Mod&egrave;le&nbsp;&raquo;. Pour utiliser ce mod&egrave;le dans un autre arbre, utilisez la fonction <a href="importer.php">Importer</a>. </p>
<hr />
<h4><a id="A5"/>Difficult&eacute;s pour s&eacute;lectionner des objets <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Si plusieurs objets se chevauchent et que vous avez des difficult&eacute;s pour les s&eacute;lectionner, utilisez le menu de<a href="selection.php"> s&eacute;lection</a> pour qu'un clic &agrave; un endroit ne concerne que les objets que vous voulez s&eacute;lectionner. </p>
<hr />
<h4><a id="A6"/>Impression avec des marges <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Certaines imprimantes ne peuvent pas imprimer sur la totalit&eacute; de la page, elles laissent une marge autour de la page. Si vous voulez ne rien mettre dans cette marge alors que votre arbre occupe plusieurs pages, vous pouvez changer la dimension de la page, par exemple 200&nbsp;mm en largeur et 287&nbsp;mm en hauteur. Quand vous g&eacute;n&eacute;rez un fichier PDF et que vous l'imprimez, l'arbre sera limit&eacute; &agrave; la dimension de 200&nbsp;mm par 287&nbsp;mm. Les traits qui passent d'une page &agrave; l'autre resteront &agrave; l'int&eacute;rieur des marges. Il suffit de d&eacute;couper la marge qui n'est pas utilis&eacute;e par l'imprimante et vous pouvez assembler vos feuilles correctement. </p>
<hr />
<h4><a id="A7"/>Obtenir des images de grande dimension <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Si vous voulez obtenir des images de grande dimension, il suffit de changer les dimensions de la page pour mettre les valeurs que vous voulez obtenir. </p>
</div>
</div>
</body>
</html>
