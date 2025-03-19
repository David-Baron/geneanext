<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Évènements liés à la personne</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2><a id="haut"/>&Eacute;vènements liés à la personne</h2>
<p><a href="#A0">Choix des évènements</a> - <a href="#A1">Fonctionnement de la fenêtre </a>- <a href="#A2">Variables de l'étiquette</a> - <a href="#A3">Exemples</a></p>
<hr />
<h4><a id="A0"/>Choix des évènements <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord"/></a></h4>
<p>Comme une personne peut être liée à plusieurs évènements, il faut choisir ceux qui seront utilisés dans l'étiquette.</p>
<p>  La première chose à faire est de créer un modèle d'étiquette qui utilise des évènements. Puis il faut qu'une ou plusieurs personnes de l'arbre utilisent cette étiquette. Ensuite, il faut choisir les évènements à prendre en compte dans l'étiquette. Pour cela, il faut aller dans le menu Personnes et choisir la ligne Personnaliser :</p>
<p><img src="images/personnes02.png" width="123" height="248" class="imageBord1pt" alt="" /></p>
<p>Vous obtenez une fenêtre qui va vous permettre de choisir quel(s) évènement(s) utiliser dans l'étiquette.</p>
<table border="1" summary="">
  <tr>
    <td class="sansBord"><img src="images/personnaliser1.png" width="597" height="454" alt=""/></td>
    <td class="sansBord"><p>Repère 1 : ce menu permet de choisir la personne que vous voulez personnaliser.</p>
      <p>Repère 2 : affichage des évènements sélectionnés. Vous avez autant de lignes que d'évènements à afficher dans l'étiquette (d'après le modèle utilisé pour la personne).</p>
      <p>Repère 3 : choix d'un évènement lié à la personne.</p>
      <p>Repère 4 : affichage du détail de l'évènement choisi dans la liste repère 3.</p>
      <p>Repère 5 : boutons pour modifier l'évènement choisi dans la liste repère 2 ou pour supprimer l'évènement.</p>
<p>Repère 6 : bouton qui ferme la fenêtre.</p></td>
  </tr>
</table>
<h4><a id="A1"/>Fonctionnement de la fenêtre <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord"/></a></h4>
<p>Pour pouvoir travailler correctement, il faut créer une étiquette de personne qui utilise deux évènements. L'étiquette se présente donc comme ceci :</p>
<p><img src="images/evenement1.png" width="501" height="255" class="imageBord1pt"  alt=""/></p>
<p>Cette étiquette contient deux fois le mot &laquo;<span class="texteCode">&lt;evenement&gt;</span>&raquo;, elle fait donc appel à deux évènements. Créez un arbre contenant une personne au minimum. Il faut que cette personne soit liée à deux évènements. Affectez l'étiquette créée précédemment à la personne.</p>
<p>Nous allons maintenant personnaliser l'arbre, c'est-à-dire faire le lien entre les évènements nécessaires pour l'étiquette et les évènements à utiliser.</p>
<p>Nous utilisons  la fenêtre de personnalisation.</p>
<p>Vous sélectionnez une personne (repère 1), au repère 2 s'affiche une liste contenant autant de lignes que d'évènements requis par l'étiquette liée à la personne. Chaque ligne peut contenir :</p>
<ul>
  <li>le texte &laquo;<span class="codeTexte"> --- Pas d'évènement choisi --</span>- &raquo; si aucun évènement n'a été choisi pour cette ligne ;</li>
  <li>le titre de l'évènement choisi.</li>
</ul>
<p>Sélectionnez un évènement dans cette liste pour le modifier.</p>
<p>Vous choisissez dans la liste repère 3 l'évènement qui remplacera celui déjà choisi. Quand votre choix est fait, il suffit de cliquer sur  <span class="bouton">Modifier</span> (repère 5) pour le prendre en compte.</p>
<p>Si vous voulez supprimer un évènement dans la liste 2, il suffit de le sélectionner et de cliquer sur <span class="bouton">Supprimer</span> en 5.</p>
<p>Toutes vos modifications sont mémorisées et vous les retrouverez telles quelles dans l'arbre.</p>
<p>Par exemple, la fenêtre se présenta comme ceci :</p>
<p><img src="images/evenement2.png" width="584" height="438" alt="" /></p>
<p>Pour résumer ce qu'elle présente, on peut dire que l'étiquette utilisée pour la personne fait appel à deux évènements. Pour le premier, il a été choisi d'afficher sa profession (agent commercial) et pour le second, son baptême.</p>
<p>Pour finir, il suffit de cliquer sur <span class="bouton">Fermer</span> (repère 6).</p>
<hr />
<h4><a id="A2"/>Variables de l'étiquette <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Lors de la création d'un modèle d'étiquette,  vous disposez de variables pour afficher une partie d'en évènement.</p>
<p>Les variables disponibles sont :</p>
<table summary="">
  <tr>
    <th>Nom de la variable </th>
    <th>Fonction </th>
  </tr>
  <tr>
    <td class="texteCode">&lt;evenement&gt;</td>
    <td>Prise en compte d'un &eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;titreEvt&gt;</td>
    <td>Titre de l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;typeEvt&gt;</td>
    <td>Type de l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;debutEvt(&quot;&quot;)&gt;</td>
    <td>Date de d&eacute;but de l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;finEvt(&quot;&quot;)&gt;</td>
    <td>Date de fin de l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;villeEvt&gt;</td>
    <td>Ville de l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;deptEvt&gt;</td>
    <td>D&eacute;partement de l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;regionEvt&gt;</td>
    <td>R&eacute;gion de l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;paysEvt&gt;</td>
    <td>Pays de l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;role&gt;</td>
    <td>R&ocirc;le dans l'&eacute;v&egrave;nement</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;debutPart(&quot;&quot;)&gt;</td>
    <td>Date de d&eacute;but de participation</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;finPart(&quot;&quot;)&gt;</td>
    <td>Date de fin de participation</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;villePart&gt;</td>
    <td>Ville de participation</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;deptPart&gt;</td>
    <td>D&eacute;partement de participation</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;regionPart&gt;</td>
    <td>R&eacute;gion de participation</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;paysPart&gt;</td>
    <td>Pays de participation</td>
  </tr>
</table>
<p>Le mot <span class="texteCode">&lt;evenement&gt;</span> a un r&ocirc;le particulier. <br />
Comme une personne peut être liée à plusieurs évènements, il faut indiquer quand il faut changer d'évènement. C'est le rôle du mot <span class="codeTexte">&lt;evenement&gt;</span>. &Agrave; chaque fois que GénéGraphe trouve ce mot, il passe à l'évènement suivant qui figure dans la liste des évènements sélectionnés pour la personne.</p>
<p>Vous pouvez utiliser le  mot <span class="texteCode">&lt;evenement&gt; </span>dans une condition (<span class="texteCode">&lt;SI&gt;&lt;evenement&gt;&lt;ALORS&gt;&#8230;</span>), il aura deux fonctions :</p>
<ul>
  <li>il sert dans la condition, s'il n'y a pas d'évènement choisi pour la personne, la condition est fausse et tout ce qui est placé entre les mots <span class="texteCode">&lt;ALORS&gt;</span> et <span class="texteCode">&lt;SINON&gt;</span> ou <span class="texteCode">&lt;FINSI&gt;</span> ne sera pas affiché (pour plus d'explications sur les conditions, voir le <a href="modeleEtiq.php#A6">paragraphe</a> adéquat) ;</li>
  <li>il fera passer à l'évènement suivant.</li>
</ul>
<p>Pour compléter l'exemple commencé ci-dessus, voici une possibilité d'étiquette qui affiche les deux évènements sélectionnée :</p>
<p><img src="images/evenement3.png" width="800" height="436" alt="" /></p>
<hr />
<h4><a id="A3"/>Exemples <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Les exemples se suivent, chacun étant la reprise de l'exemple précédent avec une possibilité supplémentaire. le code qui est ajouté est surigné en gris.</p>
<table border="1" summary="">
  <tr>
    <th>Affichage à obtenir</th>
    <th>Code à utiliser</th>
  </tr>
  <tr>
    <td>Afficher un évènement</td>
    <td class="texteCode">&lt;evenement&gt;Le &lt;debutEvt(&quot;&quot;)&gt;,  &lt;titreEvt&gt; à &lt;villeEvt&gt;</td>
  </tr>
  <tr>
    <td>Afficher l'évènement uniquement s'il a été sélectionné pour la personne</td>
    <td class="texteCode">&lt;SI&gt;&lt;evenement&gt;&lt;ALORS&gt;Le &lt;debutEvt(&quot;&quot;)&gt;,  &lt;titreEvt&gt; à &lt;villeEvt&gt;&lt;FINSI&gt;</td></tr>
  <tr>
    <td>L'affichage de la ville ne se fait que si elle existe dans l'évènement.</td>
    <td class="texteCode">&lt;SI&gt;&lt;evenement&gt;&lt;ALORS&gt;Le &lt;debutEvt(&quot;&quot;)&gt;,  &lt;titreEvt&gt;<span class="texteSurligne">&lt;SI&gt;&lt;villeEvt&gt;&lt;ALORS&gt;</span> à &lt;villeEvt&gt;<span class="texteSurligne">&lt;FINSI&gt;</span>&lt;FINSI&gt;</td>
  </tr>
  <tr>
    <td>Ajout du rôle de la personne s'il est saisi</td>
    <td class="texteCode">&lt;SI&gt;&lt;evenement&gt;&lt;ALORS&gt;Le &lt;debutEvt(&quot;&quot;)&gt;,  &lt;titreEvt&gt;&lt;SI&gt;&lt;villeEvt&gt;&lt;ALORS&gt; à &lt;villeEvt&gt;&lt;FINSI&gt;<span class="texteSurligne">&lt;SI&gt;&lt;role&gt;&lt;ALORS&gt;,&lt;role&gt; &lt;FINSI&gt;</span>&lt;FINSI&gt;</td>
  </tr>
</table>
</div>
</div>
</body>
</html>
