<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Les préférences</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2><a id="haut"/>Les pr&eacute;f&eacute;rences</h2>
<p><a href="#A1">Valeur num&eacute;rique</a> - <a href="#A2">R&eacute;pertoire</a> - <a href="#A3">Couleur</a> - <a href="#A4">Liste de choix</a></p>
<p>Les pr&eacute;f&eacute;rences trait&eacute;es ici s'appliquent &agrave; tous les arbres. Quand vous modifiez une des pr&eacute;f&eacute;rences, il suffit d'ouvrir un arbre pour qu'il tienne compte de ces valeurs.</p>
<p>Pour modifier les pr&eacute;f&eacute;rences, allez dans le <a href="fichier.php">menu Fichier</a> puis Pr&eacute;f&eacute;rences. G&eacute;n&eacute;Graphe ouvre une fen&ecirc;tre : </p>
<p><img src="images/preferences01.png" width="825" height="375" alt="" /> </p>
<p>Cette fen&ecirc;tre est compos&eacute;e de deux parties. &Agrave; gauche, un tableau avec les diff&eacute;rents param&egrave;tres et leur valeur. &Agrave; droite, vous avez un arbre qui montre l'effet des  valeurs que vous modifiez. Cet arbre est mis &agrave; jour &agrave; chaque fois que vous modifiez une valeur des pr&eacute;f&eacute;rences. </p>
<p><span class="souligne">Dimension du symbole d'une personne</span> : cette valeur donne la taille du symbole d'une personne. C'est un carr&eacute; pour un homme, un cercle pour une femme et un triangle pour une personne dont on ne conna&icirc;t pas le sexe.</p>
<p><span class="souligne">&Eacute;cart entre les personnes d'une union</span> : quand G&eacute;n&eacute;Graphe positionne deux personnes qui sont unies, il tient compte des noms des personnes et laisse un espace vide entre ces noms. Cette distance est modifiable ici.</p>
<p><span class="souligne">&Eacute;cart entre deux g&eacute;n&eacute;rations</span> : quand G&eacute;n&eacute;Graphe positionne des parents et des enfants, il laisse une distance entre les deux g&eacute;n&eacute;rations. Cette distance est renseign&eacute;e ici.</p>
<p><span class="souligne">Remarque au sujet des deux derni&egrave;res valeurs</span> : toute modification de ces valeurs sera utilis&eacute;e lors de l'ajout d'une personne &agrave; un arbre. Les modifications n'auront aucun effet sur les arbres d&eacute;j&agrave; dessin&eacute;s.</p>
<p><span class="souligne">Date de la derni&egrave;re mise &agrave; jour des pr&eacute;f&eacute;rences</span> : cette ligne vous indique quand vous avez fait la derni&egrave;re modification des pr&eacute;f&eacute;rences. Cette date est utilis&eacute;e pour la <a href="gestFichiers.php">gestion des fichiers</a>.</p>
<hr />
<h4><a id="A1"/>Valeur num&eacute;rique <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Pour modifier une valeur num&eacute;rique, il suffit de cliquer sur la valeur pour la modifier. G&eacute;n&eacute;Graphe fait des contr&ocirc;les sur les valeurs saisies pour qu'elles restent dans une fourchette correcte. Cette fourchette d&eacute;pend de chaque pr&eacute;f&eacute;rence. </p>
<hr />
<h4><a id="A2"/>R&eacute;pertoire <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Quand vous cliquez sur un nom de r&eacute;pertoire, une fen&ecirc;tre s'ouvre pour vous permettre de choisir le r&eacute;pertoire correspondant.</p>
<p><img src="images/preferences02.png" width="421" height="285" alt="" /></p>
<hr />
<h4><a id="A3"/>Couleur<a href="#haut"> <img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p><img src="images/preferences03.png" width="350" height="326" alt="" /></p>
<p>Quand vous cliquez sur une pr&eacute;f&eacute;rence qui est une couleur, une fen&ecirc;tre vous permet de choisir la couleur que vous voulez utiliser. </p>
<hr />
<h4><a id="A4"/>Liste de choix<a href="#haut"> <img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>En cliquant sur certaines zones, G&eacute;n&eacute;Graphe peut vous ouvrir une liste de choix (ici pour la g&eacute;n&eacute;ration des fiches individuelles). Vous pouvez choisir l'une des options qui vous sont propos&eacute;es.</p>
<p><img src="images/preferences04.png" alt="" width="518" height="83" class="imageBord1pt" /></p>
</div>
</div>
</body>
</html>
