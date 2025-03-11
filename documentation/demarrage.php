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
<h2><a  id="haut" />D&eacute;marrage du logiciel</h2>
<p><a href="#A1">Principes g&eacute;n&eacute;raux</a> - <a href="#A2">Barre de menus</a> - <a href="#A7">Menu Mode</a> - <a href="#A3">Palette de boutons</a> - <a href="#A4">Actions sur plusieurs objets</a> - <a href="#A5">S&eacute;lection  d'objets</a> - <a href="#A6">Raccourci clavier</a></p>
<p>Quand vous d&eacute;marrez G&eacute;n&eacute;Graphe, vous obtenez la fen&ecirc;tre suivante&nbsp;:</p>
<p><img src="images/demarrage01.png" alt="" width="558" height="419" class="imageBord1pt" /></p>
<p>Les diff&eacute;rentes zones sont :</p>
<ul>
  <li> 1 :  la barre de menus avec 5 choix&nbsp;: <a href="fichier.php">Fichier</a>, Mode, <a href="personnes.php">Personnes</a>, <a href="disposition.php">Disposition,</a> Zoom, <a href="selection.php">S&eacute;lection</a> et ? ;</li>
  <li> 2 :  une <a href="#A1">palette flottante</a> vous propose des boutons qui appellent les op&eacute;rations les plus importantes du logiciel ;</li>
  <li>3 : la page dans laquelle vous allez dessiner votre arbre ;</li>
  <li>4 : cette partie n'est pas utilisable sauf quand vous augmentez la <a href="fichier.php#A5">taille</a> de votre document.</li>
</ul>
<p>Si vous modifiez le nombre de pages en largeur et/ou en hauteur (vois <a href="fichier.php#A5">param&egrave;tres de l'arbre</a>), les pages sont mat&eacute;rialis&eacute;es par des traits pointill&eacute;s bleus. </p>
<hr />
<h4><a id="A1"></a>Principes g&eacute;n&eacute;raux <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Dans les menus, certaines options apparaissent en gris clair. C'est quand la situation ne permet pas de les utiliser. Par exemple, vous ne pouvez pas demander &agrave; enregistrer un arbre si l'arbre courant est vide.</p>
<p>Dans la palette de boutons, certains sont en gris clair. C'est aussi parce qu'ils ne peuvent pas &ecirc;tre utilis&eacute;s actuellement. Par exemple, vous ne pouvez pas demander &agrave; ajouter les parents d'une personne si aucune personne n'est s&eacute;lectionn&eacute;e.</p>
<hr />
<h4><a id="A2"></a>Barre de menus <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Le menu <a href="fichier.php">Fichier</a> permet les op&eacute;rations courantes de gestion des arbres&nbsp;:</p>
<ul>
  <li>ouvrir&nbsp;: permet de reprendre un arbre qui a &eacute;t&eacute; enregistr&eacute; auparavant&nbsp;;</li>
  <li>enregistrer&nbsp;: sauvegarde l'arbre courant dans la base de donn&eacute;es&nbsp;;</li>
  <li> nouveau &nbsp;: ferme l'arbre courant, vous retrouvez une page vierge, identique &agrave; celle obtenue au d&eacute;marrage du logiciel&nbsp;;</li>
  <li> pr&eacute;f&eacute;rences&nbsp;: acc&egrave;s aux param&egrave;tres g&eacute;n&eacute;raux du logiciel (vois chapitre sp&eacute;cifique)&nbsp;;</li>
  <li> arbre&nbsp;: permet de modifier les donn&eacute;es propres &agrave; l'arbre courant&nbsp;;</li>
  <li> g&eacute;n&eacute;rer PDF&nbsp;: g&eacute;n&egrave;re le fichier PDF contenant l'arbre courant&nbsp;;</li>
  <li> g&eacute;n&eacute;rer images&nbsp;: g&eacute;n&egrave;re la ou les images correspondant &agrave; l'arbre courant ;</li>
  <li> mod&egrave;les d'&eacute;tiquettes : gestion des &eacute;tiquettes des personnes (affichage des renseignements les concernant).  </li>
</ul>

   <hr />
    <h4><a id="A7"></a>Menu Mode <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord"  alt="" /></a></h4>
    <p>Le menu Mode d&eacute;termine le mode de travail de G&eacute;n&eacute;Graphe.<br />
      Vous pouvez choisir d'afficher la <a href="chronologie.php">chronologie</a> ou l'arbre g&eacute;n&eacute;alogique.<br />
      L'acc&egrave;s &agrave; la chronologie n'est possible que quand vous avez au moins une personne pr&eacute;sente dans l'arbre. </p>
    <hr />
<h4><a id="A3"></a>Palette de boutons <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Il y a 11 boutons qui sont&nbsp;:
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
    <td>compl&eacute;ter la <a href="ajoutFratrie.php">fratrie</a></td>
  </tr>
  <tr>
    <td><img src="images/ascendance.png" width="35" height="35" alt="" /></td>
    <td>compl&eacute;ter l<a href="ascendance.php">'ascendance</a></td>
  </tr>
  <tr>
    <td><img src="images/photo.png" width="34" height="34" alt="" /></td>
    <td>ajout de <a href="photos.php">photos</a></td>
  </tr>
  <tr>
    <td><img src="images/suppression.png" width="35" height="35" alt="" /></td>
    <td> supprimer la <a href="supObjet.php">s&eacute;lection</a></td>
  </tr>
  <tr>
    <td><img src="images/etiquette.png" width="35" height="35" alt="" /></td>
    <td>ajouter une <a href="etiquette.php">&eacute;tiquette</a></td>
  </tr>
  <tr>
    <td><img src="images/editEtiq.png" width="35" height="35" alt="" /></td>
    <td><a href="presentEtiq.php">mise en forme</a> d'une &eacute;tiquette</td>
  </tr>
  <tr>
    <td><img src="images/texteActif.png" width="35" height="35" alt="" /> <img src="images/texteRepos.png" width="35" height="35" alt="" /></td>
    <td>changer le <a href="etiquette.php#A2">mode de travail</a> de G&eacute;n&eacute;Graphe</td>
  </tr>
</table>
<hr />
<h4><a id="A4"></a>Actions sur plusieurs objets <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Notez que beaucoup de fonctionnalit&eacute;s s'appliquent quand une ou plusieurs personnes sont s&eacute;lectionn&eacute;es ou quand une ou plusieurs unions sont s&eacute;lectionn&eacute;es.</p>
<hr />
<h4><a id="A5"></a>S&eacute;lection d'objets <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Pour s&eacute;lectionner un objet, il suffit de cliquer dessus.</p>
<p> Pour s&eacute;lectionner plusieurs objets, vous disposez de deux possibilit&eacute;s.</p>
<p><span class="souligne">S&eacute;lection globale</span> : placez votre curseur en dehors de tout objet, appuyez sur le bouton de votre souris et d&eacute;placez l&agrave;. Le curseur change de forme et un rectangle en pointill&eacute;s se dessine &agrave; l'&eacute;cran. Tous les objets contenus dans ce rectangle seront s&eacute;lectionn&eacute;s quand vous rel&acirc;cherez le bouton de la souris.</p>
<p><img src="images/demarrage03.jpg" alt="" width="411" height="246" class="imageBord1pt" /></p>
<p>Ici, le rectangle de s&eacute;lection contient les personnes Alain STITU et Marina VOILE. De plus, comme ces personnes sont unies, leur union sera aussi s&eacute;lectionn&eacute;e. Comme le rectangle de s&eacute;lection englobe aussi le trait vertical qui relie les parents aux enfants, il est s&eacute;lectionn&eacute;. Voici l'&eacute;cran quand on rel&acirc;che le bouton de la souris :</p>
<p><img src="images/demarrage04.jpg" width="413" height="246" class="imageBord1pt" alt="" /> </p>
<p><span class="souligne">S&eacute;lection individuelle</span> : s&eacute;lectionnez un objet, appuyez sur la touche Shift et cliquez sur le ou les objets que vous voulez ajouter &agrave; la s&eacute;lection.</p>
<p>Quand un objet est s&eacute;lectionn&eacute;, il est entour&eacute; d'un trait bleu. Exemple :</p>
<p><img src="images/demarrage02.jpg" width="446" height="225" class="imageBord1pt" alt="" /></p>
<p>Sur cet arbre, les personnes s&eacute;lectionn&eacute;es sont Bernard STITU, Donna TEUR, Alain STITU et Paula RIS&Eacute;E. Le couple Alain STITU-Paula RIS&Eacute;E est aussi s&eacute;lectionn&eacute;. </p>
<hr />
<h4><a id="A6"></a>Raccourci clavier  <a href="#haut"><img src="images/debut.gif" width="16" height="16" class="imageSansBord" alt="" /></a></h4>
<p>Certains menus proposent des raccourcis clavier pour mettre en oeuvre la commande correspondante. Par exemple, le menu &laquo; Disposition &raquo; propose le raccourci <img src="images/toucheAlt.jpg" width="35" height="29" alt="" /> + <img src="images/toucheH.jpg" width="23" height="29" alt="" /> pour faire un alignement en haut et <img src="images/toucheAlt.jpg" width="35" height="29" alt="" /> + <img src="images/toucheM.jpg" width="22" height="29" alt="" /> pour faire un alignement au milieu. </p>
<p><img src="images/principe01.jpg" width="231" height="114" class="imageBord1pt" alt="" /></p>
</div>
</div>
</body>
</html>
