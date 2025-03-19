<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Démarrage du logiciel</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2><a id="haut">Démarrage du logiciel</a></h2>
<p><a href="#A1">Principes généraux</a> - <a href="#A2">Barre de menus</a> - <a href="#A7">Menu Mode</a> - <a href="#A3">Palette de boutons</a> - <a href="#A4">Actions sur plusieurs objets</a> - <a href="#A5">Sélection  d'objets</a> - <a href="#A6">Raccourci clavier</a></p>
<p>Quand vous démarrez GénéGraphe, vous obtenez la fenêtre suivante :</p>
<p><img src="images/demarrage01.png" alt="" width="558" height="419" class="imageBord1pt" /></p>
<p>Les différentes zones sont :</p>
<ul>
  <li> 1 :  la barre de menus avec 5 choix : <a href="fichier.php">Fichier</a>, Mode, <a href="personnes.php">Personnes</a>, <a href="disposition.php">Disposition,</a> Zoom, <a href="selection.php">Sélection</a> et ? ;</li>
  <li> 2 :  une <a href="#A1">palette flottante</a> vous propose des boutons qui appellent les opérations les plus importantes du logiciel ;</li>
  <li>3 : la page dans laquelle vous allez dessiner votre arbre ;</li>
  <li>4 : cette partie n'est pas utilisable sauf quand vous augmentez la <a href="fichier.php#A5">taille</a> de votre document.</li>
</ul>
<p>Si vous modifiez le nombre de pages en largeur et/ou en hauteur (vois <a href="fichier.php#A5">paramètres de l'arbre</a>), les pages sont matérialisées par des traits pointillés bleus. </p>
<hr />
<h4><a id="A1"></a>Principes généraux <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Dans les menus, certaines options apparaissent en gris clair. C'est quand la situation ne permet pas de les utiliser. Par exemple, vous ne pouvez pas demander à enregistrer un arbre si l'arbre courant est vide.</p>
<p>Dans la palette de boutons, certains sont en gris clair. C'est aussi parce qu'ils ne peuvent pas être utilisés actuellement. Par exemple, vous ne pouvez pas demander à ajouter les parents d'une personne si aucune personne n'est sélectionnée.</p>
<hr />
<h4><a id="A2"></a>Barre de menus <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Le menu <a href="fichier.php">Fichier</a> permet les opérations courantes de gestion des arbres :</p>
<ul>
  <li>ouvrir : permet de reprendre un arbre qui a été enregistré auparavant ;</li>
  <li>enregistrer : sauvegarde l'arbre courant dans la base de données ;</li>
  <li> nouveau  : ferme l'arbre courant, vous retrouvez une page vierge, identique à celle obtenue au démarrage du logiciel ;</li>
  <li> préférences : accès aux paramètres généraux du logiciel (vois chapitre spécifique) ;</li>
  <li> arbre : permet de modifier les données propres à l'arbre courant ;</li>
  <li> générer PDF : génère le fichier PDF contenant l'arbre courant ;</li>
  <li> générer images : génère la ou les images correspondant à l'arbre courant ;</li>
  <li> modèles d'étiquettes : gestion des étiquettes des personnes (affichage des renseignements les concernant).  </li>
</ul>

   <hr />
    <h4><a id="A7"></a>Menu Mode <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord"  alt="" /></a></h4>
    <p>Le menu Mode détermine le mode de travail de GénéGraphe.<br />
      Vous pouvez choisir d'afficher la <a href="chronologie.php">chronologie</a> ou l'arbre généalogique.<br />
      L'accès à la chronologie n'est possible que quand vous avez au moins une personne présente dans l'arbre. </p>
    <hr />
<h4><a id="A3"></a>Palette de boutons <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Il y a 11 boutons qui sont :
</p>
<table border="0" cellpadding="1" summary="">
  <tr>
    <td><img src="images/personne.png" width="35" height="35" alt="" /></td>
    <td>ajout d'une <a href="ajoutPersonne.php">personne</a></td>
  </tr>
  <tr>
    <td><img src="images/parents.png" width="35" alt="" height="35" /></td>
    <td> ajout des <a href="ajoutParents.php">parents</a></td>
  </tr>
  <tr>
    <td><img src="images/conjoints.png" width="35" height="35" alt="" /></td>
    <td>ajout du ou des <a href="ajoutConjoint.php">conjoints</a></td>
  </tr>
  <tr>
    <td><img src="images/enfants.png" width="35" height="35" alt="" /></td>
    <td>ajout du ou des <a href="ajoutEnfant.php">enfants</a></td>
  </tr>
  <tr>
    <td><img src="images/fratrie.png" width="35" height="35" alt="" /></td>
    <td>compléter la <a href="ajoutFratrie.php">fratrie</a></td>
  </tr>
  <tr>
    <td><img src="images/ascendance.png" width="35" height="35" alt="" /></td>
    <td>compléter l<a href="ascendance.php">'ascendance</a></td>
  </tr>
  <tr>
    <td><img src="images/photo.png" width="34" height="34" alt="" /></td>
    <td>ajout de <a href="photos.php">photos</a></td>
  </tr>
  <tr>
    <td><img src="images/suppression.png" width="35" height="35" alt="" /></td>
    <td> supprimer la <a href="supObjet.php">sélection</a></td>
  </tr>
  <tr>
    <td><img src="images/etiquette.png" width="35" height="35" alt="" /></td>
    <td>ajouter une <a href="etiquette.php">étiquette</a></td>
  </tr>
  <tr>
    <td><img src="images/editEtiq.png" width="35" height="35" alt="" /></td>
    <td><a href="presentEtiq.php">mise en forme</a> d'une étiquette</td>
  </tr>
  <tr>
    <td><img src="images/texteActif.png" width="35" height="35" alt="" /> <img src="images/texteRepos.png" width="35" height="35" alt="" /></td>
    <td>changer le <a href="etiquette.php#A2">mode de travail</a> de GénéGraphe</td>
  </tr>
</table>
<hr />
<h4><a id="A4"></a>Actions sur plusieurs objets <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Notez que beaucoup de fonctionnalités s'appliquent quand une ou plusieurs personnes sont sélectionnées ou quand une ou plusieurs unions sont sélectionnées.</p>
<hr />
<h4><a id="A5"></a>Sélection d'objets <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Pour sélectionner un objet, il suffit de cliquer dessus.</p>
<p> Pour sélectionner plusieurs objets, vous disposez de deux possibilités.</p>
<p><span class="souligne">Sélection globale</span> : placez votre curseur en dehors de tout objet, appuyez sur le bouton de votre souris et déplacez là. Le curseur change de forme et un rectangle en pointillés se dessine à l'écran. Tous les objets contenus dans ce rectangle seront sélectionnés quand vous rel&acirc;cherez le bouton de la souris.</p>
<p><img src="images/demarrage03.jpg" alt="" width="411" height="246" class="imageBord1pt" /></p>
<p>Ici, le rectangle de sélection contient les personnes Alain STITU et Marina VOILE. De plus, comme ces personnes sont unies, leur union sera aussi sélectionnée. Comme le rectangle de sélection englobe aussi le trait vertical qui relie les parents aux enfants, il est sélectionné. Voici l'écran quand on rel&acirc;che le bouton de la souris :</p>
<p><img src="images/demarrage04.jpg" width="413" height="246" class="imageBord1pt" alt="" /> </p>
<p><span class="souligne">Sélection individuelle</span> : sélectionnez un objet, appuyez sur la touche Shift et cliquez sur le ou les objets que vous voulez ajouter à la sélection.</p>
<p>Quand un objet est sélectionné, il est entouré d'un trait bleu. Exemple :</p>
<p><img src="images/demarrage02.jpg" width="446" height="225" class="imageBord1pt" alt="" /></p>
<p>Sur cet arbre, les personnes sélectionnées sont Bernard STITU, Donna TEUR, Alain STITU et Paula RIS&Eacute;E. Le couple Alain STITU-Paula RIS&Eacute;E est aussi sélectionné. </p>
<hr />
<h4><a id="A6"></a>Raccourci clavier  <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Certains menus proposent des raccourcis clavier pour mettre en oeuvre la commande correspondante. Par exemple, le menu &laquo; Disposition &raquo; propose le raccourci <img src="images/toucheAlt.jpg" width="35" height="29" alt="" /> + <img src="images/toucheH.jpg" width="23" height="29" alt="" /> pour faire un alignement en haut et <img src="images/toucheAlt.jpg" width="35" height="29" alt="" /> + <img src="images/toucheM.jpg" width="22" height="29" alt="" /> pour faire un alignement au milieu. </p>
<p><img src="images/principe01.jpg" width="231" height="114" class="imageBord1pt" alt="" /></p>
</div>
</div>
</body>
</html>
