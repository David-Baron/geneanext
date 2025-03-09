<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formatage des dates</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2><a id="haut"></a>Formatage des dates </h2> 
<p><a href="#A0">Principes</a> - <a href="#A1">Variables</a> - <a href="#A2">Num&eacute;ro du jour</a> - <a href="#A3">Nom du jour</a> - <a href="#A4">Num&eacute;ro du mois</a> - <a href="#A5">Nom du mois</a> - <a href="#A6">Ann&eacute;e</a> - <a href="#A7">Pr&eacute;cision</a> - <a href="#A8">Conversion</a> - <a href="#A9">Exemples</a></p>
<p>Dans les mod&egrave;les d'&eacute;tiquettes pour les personnes ou les familles, certaines variables concernent des dates. Vous pouvez d&eacute;finir la fa&ccedil;on dont cette date s'affiche, c'est le <span class="gras"><strong>formatage</strong></span> de la date.</p>
<hr />
<h4><a id="A0"/>Principes <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Les variables concernant des dates admettent un param&egrave;tre qui est le format utilis&eacute; pour l'affichage de la date. Ce param&egrave;tre se place entre les parenth&egrave;ses qui suivent le nom de la variable. Comme le param&egrave;tre est du texte, il est plac&eacute; entre guillemets. Exemple : <span class="codeTexte">dateNais(&quot;&lt;j4&gt; le &lt;j1&gt; &lt;m4&gt; de l'ann&eacute;e &lt;a4&gt;&quot;)</span>.</p>
<p>Ce code dit qu'on utilise la variable <span class="codeTexte">dateNais</span> avec, comme param&egrave;tre, le texte <span class="texteCode">&lt;j4&gt; le &lt;j4&gt; &lt;m4&gt; de l'ann&eacute;e &lt;a4&gt;</span>.<br />
</p>
<p>Vous voyez que le param&egrave;tre de format contient du texte (<span class="codeTexte">de l'ann&eacute;e</span>), des espaces et des variables (<span class="texteCode">&lt;j4&gt;</span>,<span class="texteCode">&lt;j1&gt;</span> ,<span class="texteCode">&lt;m4&gt;</span> ou <span class="texteCode">&lt;a4&gt;</span>). Ces variables disent quelle partie de la date est utilis&eacute;e.</p>
<p>G&eacute;n&eacute;amania permet de saisir une date au format gr&eacute;gorien ou r&eacute;publicain. G&eacute;n&eacute;Graphe sait dans quel format a &eacute;t&eacute; saisie la date et respecte cette saisie.</p>
<p>Si vous ne voulez pas d&eacute;finir de format, l'utilisation de la variable date sans format fera afficher la date avec un format standard. Par exemple le code suivant <span class="texteCode">&lt;dateNais(&quot;&quot;)&gt;</span> affichera la date du genre <span class="codeTexte">01/01/2011</span>.</p>
<p>Il est possible de demander une conversion de date entre les calendriers r&eacute;publicain et gr&eacute;gorien. Voir le <a href="#A8">paragraphe</a> &agrave; ce sujet.</p>
<hr />
<h4><a id="A1"/>Variables <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Les variables permettent d'afficher une partie de la date suivant une certaine mise en forme. Elles sont :.</p>
<table border="1" summary="">
  <tr>
    <th scope="col">Variable</th>
    <th scope="col">Description</th>
  </tr>
  <tr>
    <td class="texteCode">&lt;j1&gt;</td>
    <td><a href="#A2">Num&eacute;ro du jour</a></td>
  </tr>
  <tr>
    <td class="texteCode">&lt;j2&gt;</td>
    <td><a href="#A2">Num&eacute;ro du jour</a> sur 2 chiffres</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;j3&gt;</td>
    <td><a href="#A3">Nom du jour</a> format court</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;j4&gt;</td>
    <td><a href="#A3">Nom du jour</a> complet</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;m1&gt;</td>
    <td><a href="#A4">Num&eacute;ro du mois</a></td>
  </tr>
  <tr>
    <td class="texteCode">&lt;m2&gt;</td>
    <td><a href="#A4">Num&eacute;ro du mois</a> sur 2 chiffres</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;m3&gt;</td>
    <td><a href="#A5">Nom du mois</a> format court</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;m4&gt;</td>
    <td><a href="#A5">Nom du mois</a> complet</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;a2&gt;</td>
    <td><a href="#A6">Ann&eacute;e</a> sur 2 chiffres</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;a4&gt;</td>
    <td><a href="#A6">Ann&eacute;e</a> compl&egrave;te</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;p1&gt;</td>
    <td><a href="#A7">Pr&eacute;cision</a> format court</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;p2&gt;</td>
    <td><a href="#A7">Pr&eacute;cision</a> format long</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;conversion&gt;</td>
    <td><a href="#A8">Conversion</a> vers/de r&eacute;publicain</td>
  </tr>
  <tr>
    <td class="texteCode">&lt;finConversion&gt;</td>
    <td>Fin de <a href="#A8">conversion</a> vers/de r&eacute;publicain</td>
  </tr>
</table>
<hr />
<h4><a id="A2"/>Num&eacute;ro du jour <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Format court : c'est le num&eacute;ro du jour sans mise en forme.<br />
  Format long : le num&eacute;ro du jour est format&eacute; sur 2 chiffres.<br /> 
  Exemples :<br />
</p>
<table border="1" summary="">
  <tr>
    <th scope="col">Date</th>
    <th scope="col">Format court</th>
    <th scope="col">Format long</th>
  </tr>
  <tr>
    <td>01/01/2011</td>
    <td>1</td>
    <td>01</td>
  </tr>
  <tr>
    <td>15/01/2011</td>
    <td>15</td>
    <td>15</td>
  </tr>
  <tr>
    <td>6 flor&eacute;al an V</td>
    <td>6</td>
    <td>06</td>
  </tr>
  <tr>
    <td>16 flor&eacute;al an V</td>
    <td>16</td>
    <td>16</td>
  </tr>
</table>
<hr />
<h4><a id="A3"/>Nom du jour <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Format court : c'est le nom du jour en abrégé.<br />
  Pour les dates du   calendrier grégorien, les noms sont : « lu », « ma », « me », « je », « ve », «   sa », « di ».<br />
  Pour les dates au format républicain, les noms sont : « pri. »,   « duo. », « tri. », « quar. », « quin. », « sex. », « sep. », « oc. », « no. »,   « dé. ». Les noms des jours du 13<sup>e</sup> mois républicain sont « j. vertu », « j.   génie », « j. travail », « j. opinion », « j. récom. », « j. révo. ».</p>
<p>Format long : le nom du jour est écrit en totalité.<br />
  Pour les dates du   calendrier grégorien, les noms sont : « lundi », « mardi », « mercredi », «   jeudi », « vendredi », « samedi », « dimanche ».<br />
  Pour les dates au format   républicain, les noms sont : « primidi », « duodi », « tridi », « quartidi », «   quintidi », « sextidi », « septidi », « octidi », « nonidi », « décadi ». Les noms   des jours du 13<sup>e</sup> mois républicain sont « jour de la vertu », « jour du génie »,   « jour du travail », « jour de l'opinion », « jour des récompenses », « jour de   la révolution ».</p>
<p> Exemples :<br />
</p>
<table border="1" summary="">
  <tr>
    <th scope="col">Date</th>
    <th scope="col">Format court</th>
    <th scope="col">Format long</th>
  </tr>
  <tr>
    <td>01/01/2011</td>
    <td>sa</td>
    <td>samedi</td>
  </tr>
  <tr>
    <td>6 flor&eacute;al an V</td>
    <td>sex.</td>
    <td>sextidi</td>
  </tr>
</table>
<hr />
<h4><a id="A4"/>Num&eacute;ro du mois <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Format court : c'est le num&eacute;ro du mois sans mise en forme.<br />
Format long : le num&eacute;ro du mois est format&eacute; sur 2 chiffres.<br />
Exemples :<br />
</p>
<table border="1" summary="">
  <tr>
    <th scope="col">Date</th>
    <th scope="col">Format court</th>
    <th scope="col">Format long</th>
  </tr>
  <tr>
    <td>01/01/2011</td>
    <td>1</td>
    <td>01</td>
  </tr>
  <tr>
    <td>15/11/2011</td>
    <td>11</td>
    <td>11</td>
  </tr>
  <tr>
    <td>6 flor&eacute;al an V</td>
    <td>8</td>
    <td>08</td>
  </tr>
  <tr>
    <td>1er thermidor an VIII</td>
    <td>11</td>
    <td>11</td>
  </tr>
</table>
<p>Attention, le calendrier r&eacute;publicain comprend 13 mois et le 13<sup>e</sup> mois a 5 ou 6 jours, suivant que l'année est bisextile ou non.</p>
<hr />
<h4><a id="A5"/>Nom du mois <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Format court : c'est le nom du mois en version courte.<br />
  Dans le calendrier   grégorien, les mois sont : « jan. », « févr. », « mars », « avr. », « mai », «   juin », « juil. », « août », « sept. », « oct. », « nov. », « déc. ».<br />
  Dans le   calendrier républicain, les mois sont : « ven », « bru », « fri », « niv », «   plu », « ven », « ger », « flo », « pra », « mes », « the », « fru », « san   ».</p>
<p>Format long : utilisation du nom du mois complet. <br />
  Dans le calendrier   grégorien, il s'agit de « janvier », « février », « mars », « avril », « mai »,   « juin », « juillet », « août », « septembre », « octobre », « novembre », «   décembre ».<br />
  Dans le calendrier républicain, ce sont : « vendémiaire », «   brumaire », « frimaire », « nivôse », « pluviôse », « ventôse », « germinal », «   floréal », « prairial », « messidor », « thermidor », « fructidor », «   sanculottides ». </p>
<p> Exemples :<br />
</p>
<table border="1" summary="">
  <tr>
    <th scope="col">Date</th>
    <th scope="col">Format court</th>
    <th scope="col">Format long</th>
  </tr>
  <tr>
    <td>01/01/2011</td>
    <td>jan.</td>
    <td>janvier</td>
  </tr>
  <tr>
    <td>6 flor&eacute;al an V</td>
    <td>flo</td>
    <td>flor&eacute;al</td>
  </tr>
</table>
<p>Attention, le calendrier r&eacute;publicain comprend 13 mois.</p>
<hr />
<h4><a id="A6"/>Ann&eacute;e <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Format court : pour le calendrier gr&eacute;gorien, on utilise les 2 derniers chiffres de l'ann&eacute;e. Pour le calendrier r&eacute;publicain, on utilise une forme courte (voir exemple).</p>
<p>Format long : pour le calendrier gr&eacute;gorien, on utilise  l'ann&eacute;e en totalit&eacute;. Pour le calendrier r&eacute;publicain, on utilise une forme longue (voir exemple).</p>
<p> Exemples :<br />
</p>
<table border="1" summary="">
  <tr>
    <th scope="col">Date</th>
    <th scope="col">Format court</th>
    <th scope="col">Format long</th>
  </tr>
  <tr>
    <td>01/01/2011</td>
    <td>11</td>
    <td>2011</td>
  </tr>
  <tr>
    <td>6 flor&eacute;al an V</td>
    <td>an V</td>
    <td>de l'an V</td>
  </tr>
</table>
<hr />
<h4><a id="A7"/>Pr&eacute;cision <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>C'est la pr&eacute;cision qui est indiqu&eacute;e quand on saisit une date dans G&eacute;n&eacute;amania.</p>
<p>Exemples :<br />
</p>
<table border="1" summary="">
  <tr>
    <th scope="col">Pr&eacute;cision</th>
    <th scope="col">Format court</th>
    <th scope="col">Format long</th>
  </tr>
  <tr>
    <td>Le</td>
    <td>le</td>
    <td>le</td>
  </tr>
  <tr>
    <td>Environ</td>
    <td>ca</td>
    <td>environ</td>
  </tr>
  <tr>
    <td>Avant</td>
    <td>av.</td>
    <td>avant le</td>
  </tr>
  <tr>
    <td>Apr&egrave;s</td>
    <td>ap.</td>
    <td>apr&egrave;s le</td>
  </tr>
</table>
<p>Pour les précisions &laquo;&nbsp;avant&nbsp;&raquo; et après&nbsp;&raquo;, le format long met l'article &laquo;&nbsp;le&nbsp;&raquo; si la date est supposée exacte (le jour et le mois sont différents de 1). Sinon, la date est supposée approximative et l'article n'est pas utilisé.<br />
</p>
<hr />
<h4><a id="A8"/>Conversion <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<p>Si la date saisie dans G&eacute;n&eacute;amania est au format r&eacute;publicain, la conversion affichera sa correspondance dans le calendrier gr&eacute;gorien.<br />
Si la date saisie est au format gr&eacute;gorien et qu'elle correspond &agrave; la p&eacute;riode d'utilisation du calendrier r&eacute;publicain qui va du 22&nbsp;septembre&nbsp;1792 (1<sup>er&nbsp;</sup>vend&eacute;miaire&nbsp;an&nbsp;I) au 30&nbsp;septembre&nbsp;1805 (8&nbsp;vend&eacute;miaire&nbsp;an&nbsp;XIV), la conversion affichera la  correspondance dans le calendrier r&eacute;publicain.<br />
Si la  date ne correspond pas aux cas &eacute;nonc&eacute;s ci-dessus, tout ce qui est mis entre les codes <span class="texteAlignHautEtiquette">&lt;conversion&gt;</span> et  <span class="texteCode">&lt;finConversion&gt;</span> ne sera pas pris en compte.</p>
<p>Le code  <span class="texteCode">&lt;finConversion&gt;</span> n'est pas obligatoire. La conversion se fait jusqu'à la fin du format de la date. Ce code existe si vous voulez arrêter la conversion pour reprendre l'utilisation du calendrier par défaut (exemple fourni avec les <a href="exemplesConditions.php">exemples de modèles d'étiquettes</a>).</p>
<hr />
<h4><a id="A9"/>Exemples <a href="#haut"><img src="images/debut.gif" alt="retour" width="16" height="16" class="imageSansBord" /></a></h4>
<table border="1" summary="">
  <tr>
    <th scope="col">Date</th>
    <th scope="col">Format</th>
    <th scope="col">Affichage</th>
  </tr>
  <tr>
    <td>01/01/2011</td>
    <td class="texteCode">&lt;j1&gt;/&lt;m1&gt;/&lt;a4&gt;</td>
    <td>01/01/2011</td>
  </tr>
  <tr>
    <td>01/01/2011</td>
    <td class="texteCode">&lt;p1&gt; &lt;j4&gt; &lt;j1&gt; &lt;m4&gt; &lt;a4&gt;</td>
    <td>le samedi 1 janvier 2011</td>
  </tr>
  <tr>
    <td>01/01/2011</td>
    <td class="texteCode">&lt;j1&gt;/&lt;m1&gt;/&lt;a4&gt;&lt;conversion&gt;&lt;j1&gt;/&lt;m1&gt;/&lt;a4&gt;&lt;finConversion&gt;</td>
    <td>01/01/2011</td>
  </tr>
  <tr>
    <td>10 vend&eacute;miaire an V</td>
    <td><span class="texteCode">&lt;j1&gt;/&lt;m1&gt;/&lt;a2&gt;</span></td>
    <td>1/1/an V</td>
  </tr>
  <tr>
    <td>10 vend&eacute;miaire 'an V</td>
    <td><span class="texteCode">&lt;conversion&gt;&lt;j1&gt;/&lt;m1&gt;/&lt;a2&gt;&lt;j1&gt;/&lt;m1&gt;/&lt;a2&gt;&lt;finConversion&gt;</span></td>
    <td>22/9/96</td>
  </tr>
  <tr>
    <td>22 septembre 1796</td>
    <td class="texteCode">&lt;j1&gt; &lt;m4&gt; &lt;a4&gt;&lt;conversion&gt; (&lt;j1&gt; &lt;m4&gt; &lt;a4&gt;)&lt;finConversion&gt;</td>
    <td>22 septembre 1796 (1 vend&eacute;miaire de l'an V)</td>
  </tr>
  <tr>
    <td>6 sanculottides an III</td>
    <td class="texteCode">&lt;j1&gt; &lt;m4&gt; &lt;a4&gt;&lt;conversion&gt; (&lt;j1&gt; &lt;m4&gt; &lt;a4&gt;)&lt;finConversion&gt;</td>
    <td>6 sanculottides de l'an III (22 septembre 1795)</td>
  </tr>
</table>
<p>D'autres exemples sont donnés dans les <a href="exemplesConditions.php">exemples de modèles d'étiquettes</a>.</p>
</div>
</div>
</body>
</html>
