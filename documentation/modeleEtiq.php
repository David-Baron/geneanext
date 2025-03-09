<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Modèle d'étiquette de personne</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
  <h2><a id="haut" />Mod&egrave;le d'&eacute;tiquette de personne </h2>
  <p><a href="#A0">Cr&eacute;er un mod&egrave;le</a> - <a href="#A1">Barre de boutons</a> - <a href="#A2">Choix du type</a> - <a href="#A3"> Mettre en forme</a> - <a href="#A4">Zone de saisie</a> - <a href="#A5">Les familles de variables</a> - <a href="#A6">Les donn&eacute;es variables</a> - <a href="#A7">Conditions</a> - <a href="#A8">Personne d'exemple</a> - <a href="#A9">Exemple</a> - <a href="#A10">Supprimer un mod&egrave;le</a> - <a href="#A11">Mod&egrave;le par d&eacute;faut</a> - <a href="#A12">Appliquer un mod&egrave;le</a> - <a href="exemplesConditions.php">Exemples d'utilisation des conditions</a> - <a href="exemplesEvts.php">Exemples d'utilisation des évènements</a> - <a href="exemplesDates.php">Exemples de formatage des dates</a></p>
  <p>Vous pouvez d&eacute;finir des mod&egrave;les pour les &eacute;tiquettes des personnes et pour les &eacute;tiquettes des familles. </p>
  <hr />
  <h4><a id="A0"/>
  Cr&eacute;er un mod&egrave;le
  <a href="#haut"> <img src="images/debut.gif"  alt="" width="16" height="16" class="imageSansBord" /></a>
  </h4>
  <p>Pour cr&eacute;er un mod&egrave;le de personne, il faut que l'arbre en cours d'&eacute;dition contienne au moins une personne. Pour cr&eacute;er un mod&egrave;le de famille, il faut que l'arbre en cours d'&eacute;dition contienne au moins une famille. Cela permet, lors de la d&eacute;finition d'un mod&egrave;le, de voir en temps r&eacute;el un exemple d'&eacute;tiquette appliqu&eacute; &agrave; une des personnes ou &agrave; une des familles de l'arbre courant.</p>
  <p>Pour cr&eacute;er un mod&egrave;le, il faut aller dans le <a href="fichier.php">menu Fichier</a> et choisir Mod&egrave;les d'&eacute;tiquettes. G&eacute;n&eacute;Graphe ouvre une fen&ecirc;tre pour cr&eacute;er et modifier vos mod&egrave;les. </p>
  <p><img src="images/modeleEtiq01.png" width="768" height="429" alt="" /></p>
  <p>Cet &eacute;cran comprend 8 zones qui sont :</p>
  <ul>
    <li>une <a href="#A1">barre de boutons</a> pour g&eacute;rer les mod&egrave;les (ouvrir, enregistrer, renommer&hellip;) - rep&egrave;re 1 ;</li>
    <li>deux boutons radio pour choisir le <a href="#A2">type du mod&egrave;le</a> (rep&egrave;re 2) ; </li>
    <li>une barre de boutons pour <a href="#A3">mettre en forme le texte</a> (rep&egrave;re 3) ;</li>
    <li>une zone o&ugrave; vous <a href="#A4">saisissez le mod&egrave;le</a> (rep&egrave;re 4) ;</li>
    <li>une liste d&eacute;roulante pour choisir la <a href="#A5">famille de variables</a> que vous voulez utiliser (rep&egrave;re 5) ;</li>
    <li>une liste d&eacute;roulante pour mettre en place<a href="#A6"> les donn&eacute;es variables</a> (rep&egrave;re 6) ;</li>
    <li>une liste d&eacute;roulante pour <a href="#A8">choisir la personne ou la famille</a> qui sert d'exemple de l'&eacute;tiquette (rep&egrave;re 7) ;</li>
    <li>une &eacute;tiquette d'exemple dont les donn&eacute;es d&eacute;pendent de la personne ou de la famille choisie (rep&egrave;re 8).</li>
  </ul>
  <hr />
  <h4><a id="A1"/>Barre de boutons (rep&egrave;re 1) <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Cette barre contient 7 boutons dont le r&ocirc;le est :</p>
  <table border="0" cellpadding="5" summary="">
    <tr>
      <td><img src="images/nouveau.png" width="16" height="16" alt="" /></td>
      <td>Nouveau mod&egrave;le - ce bouton ferme (et &eacute;ventuellement enregistre) le mod&egrave;le courant et permet de d&eacute;finir un nouveau mod&egrave;le. </td>
    </tr>
    <tr>
      <td><img src="images/ouvrir.png" width="16" height="16" alt="" /></td>
      <td>Ouvrir mod&egrave;le - ce bouton ouvre un mod&egrave;le existant (et &eacute;ventuellement enregistre le mod&egrave;le courant) pour le modifier. </td>
    </tr>
    <tr>
      <td><img src="images/enregistrer02.png" width="16" height="16" alt="" /></td>
      <td>Enregistrer - permet d'enregistrer le mod&egrave;le courant.</td>
    </tr>
    <tr>
      <td><img src="images/enregistrerSous.png" width="16" height="16" alt="" /></td>
      <td>Enregistre sous - permet d'enregistrer le mod&egrave;le courant sous un nouveau nom. </td>
    </tr>
    <tr>
      <td><img src="images/renommer.gif" width="16" height="16" alt="" /></td>
      <td>Renommer - ce bouton permet de changer le nom et la description du mod&egrave;le courant. Il faut sauvegarder le mod&egrave;le pour que ces modifications soient enregistr&eacute;es. </td>
    </tr>
    <tr>
      <td><img src="images/supprimer.gif" width="16" height="16" alt="" /></td>
      <td>Supprimer - ce bouton supprime le mod&egrave;le courant. Ce bouton n'est accessible que si aucun arbre n'utilise le mod&egrave;le. </td>
    </tr>
    <tr>
      <td><img src="images/modeleDefaut.gif" width="16" height="16" alt="" /></td>
      <td>Mod&egrave;le par d&eacute;faut - permet de d&eacute;finir le <a href="#A11">mod&egrave;le par d&eacute;faut</a> utilis&eacute; lorsque vous ajoutez une personne &agrave; un arbre. </td>
    </tr>
  </table>
  <p>Ces boutons sont accessibles ou pas. Cela d&eacute;pend de l'&eacute;tat du mod&egrave;le courant. Par exemple, quand le mod&egrave;le est vide, il n'est pas possible de l'enregistrer.</p>
  <hr />
  <h4><a id="A2"/>Choix du type de mod&egrave;le  (rep&egrave;re 2) <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Quand vous avez un mod&egrave;le vierge, vous pouvez choisir le type de mod&egrave;le que vous souhaitez cr&eacute;er. Tant que vous ne sauvegardez pas le mod&egrave;le, vous pouvez changer le type. Si vous le faites, tout ce que vous avez d&eacute;j&agrave; saisi sera effac&eacute;.</p>
  <p>Quand vous changez le type de mod&egrave;le, la liste des familles de variables (rep&egrave;re 6) change de contenu. </p>
  <hr />
  <h4><a id="A3"/>Mettre en forme le texte (rep&egrave;re 3) <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Ces boutons sont habituels dans un traitement de texte. </p>
  <table summary="">
    <tr>
      <td class="aligneDroite"><img src="images/choixPolice.jpg" width="150" height="20" alt="" /></td>
      <td>choix de la police de caract&egrave;res </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/choixCorps.jpg" width="50" height="20" alt="" /></td>
      <td>choix du corps (taille) des caract&egrave;res </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/aligneG.jpg" width="16" height="16" alt="" /></td>
      <td>alignement &agrave; gauche </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/aligneC.jpg" width="16" height="16" alt="" /></td>
      <td>alignement au centre </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/aligneD.jpg" width="16" height="16" alt="" /></td>
      <td>alignement &agrave; droite </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/couleurTexte.png" width="16" height="16" alt="" /></td>
      <td>couleur du texte </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/gras.png" width="16" height="16" alt="" /></td>
      <td>mise en gras </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/italic.png" width="16" height="16" alt="" /></td>
      <td>mise en italique </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/souligne.png" width="16" height="16" alt="" /></td>
      <td>soulignement du texte </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/couper.gif" width="16" height="16" alt="" /></td>
      <td>coupe le texte s&eacute;lectionn&eacute; dans le presse-papier </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/copier.gif" width="16" height="16" alt="" /></td>
      <td>copie le texte s&eacute;lectionn&eacute; dans le presse-papier </td>
    </tr>
    <tr>
      <td class="aligneDroite"><img src="images/coller.gif" width="16" height="16" alt="" /></td>
      <td>colle le contenu du presse-papier &agrave; l'emplacement du curseur </td>
    </tr>
  </table>
  <hr />
  <h4><a id="A4"/>Zone de saisie de l'&eacute;tiquette (rep&egrave;re 4) <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Dans cette zone, vous saisissez le texte de l'&eacute;tiquette.<br />
    Vous pouvez :</p>
  <ul>
    <li>mettre du texte constant (il sera le m&ecirc;me pour toutes les personnes), c'est tout ce que vous saisissez directement ;</li>
    <li>utiliser des <a href="#A6">variables</a> ;</li>
    <li>afficher du texte (constant ou variable) en fonction de certaines <a href="#A7">conditions</a>. </li>
  </ul>
  <hr />
  <h4><a id="A5"/>Les familles de variables (rep&egrave;re 5) <a href="#haut"></a> <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord"  /></a></h4>
  <p>Cette liste d&eacute;roulante vous permet de choisir la famille de variables qui s'affichent dans la liste (rep&egrave;re 6).</p>
  <p> Si vous avez choisi 
    un type d'&eacute;tiquette de personne, cette liste vous propose les variables de :</p>
  <ul>
    <li><a href="#B1">Renseignements de la personne</a> ;</li>
    <li><a href="#A7">Conditions</a>  ;</li>
    <li>Ev&egrave;vements de la personne (<a href="evtPers.php">voir la page concern&eacute;</a>e) ;</li>
    <li>Formatage des dates (<a href="formatDate.php">voir la page concern&eacute;e</a>).</li>
  </ul>
  <p>Si vous avez choisi 
    un type d'&eacute;tiquette de famille, cette liste vous propose les variables de :<br />
  </p>
  <ul>
    <li><a href="#B2">Renseignements sur l'union</a> ;</li>
    <li><a href="#A7">Conditions</a> ;</li>
    <li>Formatage des dates (<a href="formatDate.php">voir la page concern&eacute;e</a>).</li>
  </ul>
  <hr />
  <h4><a id="A6"/>Les donn&eacute;es variables (rep&egrave;re 6) <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Les donn&eacute;es variables sont les valeurs qui d&eacute;pendent de chaque personne ou de chaque famille.</p>
  <h5> <a id="B1"/>Renseignements de la personne</h5>
  <p>Les donn&eacute;es  concernant les renseignements de la personne sont :</p>
  <table summary="">
    <tr>
      <th>Nom de la variable </th>
      <th>Fonction </th>
    </tr>
    <tr>
      <td class="texteCode">&lt;nom&gt;</td>
      <td>Nom de la personne</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;prenomUsuel&gt;</td>
      <td>Pr&eacute;nom usuel de la personne : si le pr&eacute;nom usuel de la personne n'est pas le premier, il faut modifier la fiche de la personne dans G&eacute;n&eacute;amania pour le placer entre les caract&egrave;res &laquo; &lt; &raquo; et &laquo; &gt; &raquo;. Quand G&eacute;n&eacute;Graphe trouve un pr&eacute;nom entre ces caract&egrave;res, c'est ce pr&eacute;nom qui sera consid&eacute;r&eacute; comme pr&eacute;nom usuel sinon c'est le premier pr&eacute;nom qui sera pris en compte. </td>
    </tr>
    <tr>
      <td class="texteCode">&lt;prenoms&gt;</td>
      <td>Tous les pr&eacute;noms de la personne</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;dateNais(&quot;&quot;)&gt;</td>
      <td>Date de naissance</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;villeNais&gt;</td>
      <td>Ville de naissance</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;dateDeces(&quot;&quot;)&gt;</td>
      <td>Date de d&eacute;c&egrave;s</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;villeDeces&gt;</td>
      <td>Ville de d&eacute;c&egrave;s</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;e&gt;</td>
      <td>Accord masculin/f&eacute;minin : pour que certains mots (par exemple &laquo;&nbsp;N&eacute;&nbsp;&raquo; ou &laquo;&nbsp;D&eacute;c&eacute;d&eacute;&nbsp;&raquo;) s'accordent au genre de la personne, il faut mettre la variable &lt;e&gt; l&agrave; o&ugrave; vous voulez que l'accord se fasse (par exemple &laquo;&nbsp;<span class="texteCode">N&eacute;&lt;e&gt;</span>&nbsp;&raquo; ou &laquo;&nbsp;<span class="texteCode">D&eacute;c&eacute;d&eacute;&lt;e&gt;</span>&nbsp;&raquo;). Cette variable <span class="texteCode">&lt;e&gt;</span> s'applique &agrave; n'importe quel texte. (<a href="exemplesConditions.php">Voir des exemples</a>) </td>
    </tr>
    <tr>
      <td class="texteCode">&lt;deptNais&gt;</td>
      <td>D&eacute;partement de naissance</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;regionNais&gt;</td>
      <td>R&eacute;gion de naissance</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;paysNais&gt;</td>
      <td>Pays de naissance</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;deptDeces&gt;</td>
      <td>D&eacute;partement de d&eacute;c&egrave;s</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;regionDeces&gt;</td>
      <td>R&eacute;gion de d&eacute;c&egrave;s</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;paysDeces&gt;</td>
      <td>Pays de d&eacute;c&egrave;s</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;ageDeces&gt;</td>
      <td>&Acirc;ge au d&eacute;c&egrave;s</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;autresNoms&gt;</td>
      <td>Liste du ou des autres noms de la personne </td>
    </tr>
  </table>
  <p>Pour les conditions, voir les <a href="#A7">explications</a> sur les conditions et v<a href="exemplesConditions.php">oir des exemples</a>.</p>
  <h5>&Eacute;vènements liés à la personne</h5>
<p>Pour les donn&eacute;es  concernant les &eacute;v&egrave;nements li&eacute;s &agrave; une personne, voir la <a href="evtPers.php">page qui y est consacr&eacute;e</a>.</p>
<h5>Formatage des dates</h5>
  <p>Pour le formatage des dates, voir la <a href="formatDate.php">page qui y est consacr&eacute;e</a>.<br />
  </p>
  <h5><a id="B2"/>Renseignements sur l'union</h5>
  <p>Les donn&eacute;es concernant l'union sont :</p>
  <table summary="">
    <tr>
      <th>Nom de la variable </th>
      <th>Fonction </th>
    </tr>
    <tr>
      <td class="texteCode">&lt;dateMariage(&quot;&quot;)&gt;</td>
      <td>Date du mariage </td>
    </tr>
    <tr>
      <td class="texteCode">&lt;dateActe(&quot;&quot;)&gt;</td>
      <td>Date de l'acte notarial du contrat de mariage </td>
    </tr>
    <tr>
      <td class="texteCode">&lt;nomNotaire&gt;</td>
      <td>Nom du notaire </td>
    </tr>
    <tr>
      <td class="texteCode">&lt;villeMariage&gt;</td>
      <td>Ville du mariage</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;deptMariage&gt;</td>
      <td>D&eacute;partement du mariage</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;regionMariage&gt;</td>
      <td>R&eacute;gion du mariage</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;paysMariage&gt;</td>
      <td>Pays du mariage</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;villeNotaire&gt;</td>
      <td>Ville du notaire </td>
    </tr>
    <tr>
      <td class="texteCode">&lt;deptNotaire&gt;</td>
      <td>D&eacute;partement du notaire</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;region&gt;</td>
      <td>R&eacute;gion du notaire</td>
    </tr>
    <tr>
      <td class="texteCode">&lt;paysActe&gt;</td>
      <td>Pays du notaire</td>
    </tr>
  </table>
  <h5>Formatage des dates</h5>
  <p>Pour le formatage des dates, voir la <a href="formatDate.php">page qui y est consacr&eacute;e</a>.</p>
  <p>Pour mettre en place une variable, vous avez deux possibilit&eacute;s :</p>
  <ul>
    <li>soit vous saisissez le code en totalit&eacute; (par exemple <span class="texteCode">&lt;dateNais(&quot;&quot;)&gt;</span>) ;</li>
    <li>soit :
      <ul>
        <li>vous placez le curseur &agrave; l'endroit o&ugrave; vous voulez ins&eacute;rer la variable,</li>
        <li>vous s&eacute;lectionnez le code dans la liste rep&egrave;re 4,</li>
        <li> vous cliquez sur <span class="bouton">&lt;&lt; Ajouter</span>.<br />
        </li>
      </ul>
    </li>
  </ul>
  <hr />
  <h4><a id="A7"/>Les conditions <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Pour les conditions, vous pouvez utiliser 4 mots particuli&egrave;res qui sont <span class="texteCode">&lt;SI&gt;</span>, <span class="texteCode">&lt;ALORS&gt;</span>, <span class="texteCode">&lt;SINON&gt;</span> et <span class="texteCode">&lt;FINSI&gt;</span>.</p>
  <table summary="">
    <tr>
      <td>Les conditions vous permettent de voir du texte si une variable est renseign&eacute;e. Par exemple, si votre &eacute;tiquette affiche la date de d&eacute;c&egrave;s et que la personne n'a pas de date de d&eacute;c&egrave;s, vous obtiendrez cette &eacute;tiquette. </td>
      <td><img src="images/modeleEtiq02.png" alt="" width="145" height="57" class="imageBord1pt"/></td>
    </tr>
    <tr>
      <td><p>Pour ne pas afficher la troisi&egrave;me ligne, il faut utiliser une condition. L'&eacute;tiquette doit contenir la formule qui peut se lire : si la date de d&eacute;c&egrave;s est renseign&eacute;e, alors il faut afficher le texte &laquo;&nbsp;D&eacute;c&eacute;d&eacute;&lt;e&gt; le &lt;dateDeces&gt; &agrave; &lt;villeDeces&gt;&nbsp;&raquo;</p></td>
      <td><p class="blocCode">&lt;prenomUsuel&gt; &lt;nom&gt;<br />
      Né&lt;e&gt; le &lt;datenais(&quot;&quot;)&gt; &lt;villeNais&gt;<br />
      &lt;SI&gt;&lt;dateDeces(&quot;&quot;)&gt;&lt;ALORS&gt;Décédé&lt;e&gt; le &lt;dateDeces(&quot;&quot;))&gt; à &lt;villeDeces&gt;&lt;FINSI&gt;
      <br />
      </p></td>
    </tr>
    <tr>
      <td><p>L'&eacute;tiquette affich&eacute;e contiendra alors :</p></td>
      <td><img src="images/modeleEtiq04.png"  alt="" width="145" height="38" class="imageBord1pt" /></td>
    </tr>
  </table>
  <p>Suivant les besoins, vous pouvez utiliser une syntaxe du genre :</p>
  <ul>
    <li><span class="texteCode">&lt;SI&gt;...&lt;ALORS&gt;...&lt;SINON&gt;...&lt;FINSI&gt;</span> (syntaxe 1) </li>
    <li><span class="texteCode">&lt;SI&gt;...&lt;ALORS&gt;...&lt;FINSI&gt;</span> (syntaxe 2)</li>
    <li><span class="texteCode">&lt;SI&gt;...&lt;SINON&gt;...&lt;FINSI&gt;</span> (syntaxe 3)</li>
  </ul>
  <p>Entre les mots <span class="texteCode">&lt;SI&gt;</span> et <span class="texteCode">&lt; ALORS&gt;</span>, il faut placer une variable. Cela permet de tenir compte de deux possibilit&eacute;s, soit elle est renseign&eacute;e, soit elle n'est pas renseign&eacute;e. </p>
  <p>Si la variable est renseign&eacute;e, G&eacute;n&eacute;Graphe prend en compte tout ce qui se trouve entre les mots <span class="texteCode">&lt;ALORS&gt;</span> et <span class="texteCode">&lt;SINON&gt;</span> ou <span class="texteCode">&lt;ALORS&gt;</span> et <span class="texteCode">&lt;FINSI&gt;</span> suivant que vous utilisiez la syntaxe 1 ou 2.</p>
  <p>Si la variable n'est pas renseign&eacute;e, G&eacute;n&eacute;Graphe prend en compte tout ce qui se trouve entre les mots <span class="texteCode">&lt;ALORS&gt;</span> et <span class="texteCode">&lt;FINSI&gt;</span> ou <span class="texteCode">&lt;SINON&gt;</span> et <span class="texteCode">&lt;FINSI&gt;</span> suivant que vous utilisiez la syntaxe 2 ou 3.</p>
  <p>Plusieurs <a href="exemplesConditions.php">exemples</a> vous sont propos&eacute;s. </p>
  <hr />
  <h4><a id="A8"/>Choix de la personne ou de la famille utilis&eacute;e pour l'exemple (rep&egrave;re 7) <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Cette liste vous propose de choisir, parmi les personnes ou les familles pr&eacute;sentes dans l'arbre courant, celle qui servira d'exemple. </p>
  <hr />
  <h4><a id="A9"/>Affichage de l'exemple (rep&egrave;re 8) <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Dans ce cadre, vous voyez imm&eacute;diatement toutes les modifications que vous apportez au mod&egrave;le. La dimension de l'exemple s'adapte au contenu.</p>
  <hr />
  <h4><a id="A10"/>Supprimer un mod&egrave;le <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Ce bouton (<img src="images/supprimer.gif" width="16" height="16" alt="" />) est accessible uniquement si l'&eacute;tiquette n'est utilis&eacute;e dans aucun arbre. Quand vous cliquez dessus, le mod&egrave;le est supprim&eacute;.</p>
  <hr />
  <h4><a id="A11"/>Mod&egrave;le par d&eacute;faut <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Le mod&egrave;le par d&eacute;faut est utilis&eacute; quand vous ajoutez une personne ou une famille &agrave; un arbre. Pour d&eacute;finir le mod&egrave;le &agrave; utiliser par d&eacute;faut, il faut cliquer sur le bouton <img src="images/modeleDefaut.gif" width="16" height="16" alt="" />. G&eacute;n&eacute;Graphe vous propose la liste des mod&egrave;les existants.</p>
  <p><img src="images/modeleEtiq05.png" width="285" height="281" alt="" /></p>
  <p>Le type de mod&egrave;le par d&eacute;faut (personne ou famille) d&eacute;pend du choix que vous faites (voir rep&egrave;re 2). </p>
  <p>Choisissez le mod&egrave;le &agrave; utiliser par d&eacute;faut en cliquant dans la colonne Choix et cliquez sur   <span class="bouton">Valider</span>. </p>
  <hr />
  <h4><a id="A12"/>Appliquer un mod&egrave;le <a href="#haut"><img src="images/debut.gif" alt="" width="16" height="16" class="imageSansBord" /></a></h4>
  <p>Pour utiliser un mod&egrave;le &agrave; une personne, il faut faire un clic droit <img src="images/sourisDroite.jpg" width="28" height="47" alt="" /> sur la personne. G&eacute;n&eacute;Graphe vous propose alors la liste des mod&egrave;les disponibles.</p>
  <p><img src="images/etiquette05.png" width="285" height="281" alt="" /></p>
  <p>Il sufit de s&eacute;lectionner le mod&egrave;le &agrave; utiliser en le s&eacute;lectionnant dans la colonne Choix. Quand vous cliquez sur   <span class="bouton">Valider</span>, G&eacute;n&eacute;Graphe utilise ce mod&egrave;le pour afficher les renseignements de la personne. </p>
  <p>Pour appliquer un mod&egrave;le &agrave; une famille, faites un clic droit sur l'&eacute;tiquette de la famille. Vous obtenez la m&ecirc;me fen&ecirc;tre que ci-dessus. L'application d'un mod&egrave;le respecte la m&ecirc;me d&eacute;marche. 	</p>
</div>
</div>
</body>
</html>