<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Exemples de modèles d'étiquettes de personne</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("include.html") ?>
<div id="contenu">
<h2><a name="haut" id="haut"></a>Exemples d'évènements dans les mod&egrave;les d'&eacute;tiquettes de personne</h2>
<p>Pour présenter ces modèles, j'ai ajouté quelques évènements.</p>
<p>J'ai d'abord créé un évènement - agent commercial - que j'ai lié à Jean TISSIPE :</p>
<p><img src="images/exemple13.png" alt="" width="384" height="225" class="imageBord1pt"/></p>
<p>J'ai créé un évènement - baptême - qui ne contient pas de date ni de lieu :</p>
<p><img src="images/exemple14.png" alt="" width="647" height="240" class="imageBord1pt" /></p>
<p>J'ai associé cet évènement à Jean TISSIPE de la façon suivante : </p>
<p><img src="images/exemple15.png" alt="" width="779" height="176" class="imageBord1pt" /></p>
<p>Pour afficher son métier, l'étiquette se définit ainsi :</p>
<p class="blocCode">&lt;prenoms&gt; &lt;nom&gt;<br />
  &lt;evenement&gt;&lt;typeEvt&gt; : &lt;titreEvt&gt; à &lt;villeEvt&gt; du &lt;debutEvt(&quot;&quot;)&gt; au &lt;finEvt(&quot;&quot;)&gt;</p>
<p>Le résultat est <img src="images/exemple16.png" alt="" width="391" height="42" class="texteAlignHautEtiquette" /></p>
<p>Si je veux afficher son baptême, je peux dupliquer la ligne pour obtenir :</p>
<p class="blocCode">&lt;prenoms&gt; &lt;nom&gt;<br />
  &lt;evenement&gt;&lt;typeEvt&gt; : &lt;titreEvt&gt; à &lt;villeEvt&gt; du &lt;debutEvt(&quot;&quot;)&gt; au &lt;finEvt(&quot;&quot;)&gt;<br />
  <span class="texteSurligne">&lt;evenement&gt;&lt;typeEvt&gt; : &lt;titreEvt&gt; à &lt;villeEvt&gt; du &lt;debutEvt(&quot;&quot;)&gt; au &lt;finEvt(&quot;&quot;)&gt;</span></p>
  <p>J'ai surligné en gris le texte qui a été ajouté par rapport à la précédente étiquette.</p>
  <p>L'étiquette affiche alors <img src="images/exemple17.png" alt="" width="392" height="61" class="texteAlignHautEtiquette" /></p>
<p>Ce résultat est normal car l'évèneent baptême n'a pas de date définie, seul le lien entre le baptême et la personne renseigne des dates.</p>
  <p>Il faut modifier l'étiquette et utiliser une condition :</p>
  <p class="blocCode">&lt;prenoms&gt; &lt;nom&gt;<br />
    &lt;evenement&gt;&lt;typeEvt&gt; : &lt;titreEvt&gt;<span class="texteSurligne">&lt;SI&gt;&lt;debutEvt(&quot;&quot;)&gt;&lt;ALORS&gt;</span> à &lt;villeEvt&gt; du &lt;debutEvt(&quot;&quot;)&gt; au &lt;finEvt(&quot;&quot;)&gt;<span class="texteSurligne">&lt;SINON&gt;&lt;SI&gt;&lt;debutPart(&quot;&quot;)&gt;&lt;ALORS&gt; à &lt;villePart&gt; du &lt;debutPart(&quot;&quot;)&gt; au &lt;finPart(&quot;&quot;)&gt;&lt;FINSI&gt;&lt;FINSI&gt;</span><br />
  &lt;evenement&gt;&lt;typeEvt&gt; : &lt;titreEvt&gt;<span class="texteSurligne">&lt;SI&gt;&lt;debutEvt(&quot;&quot;)&gt;&lt;ALORS&gt;</span> à &lt;villeEvt&gt; du &lt;debutEvt(&quot;&quot;)&gt; au &lt;finEvt(&quot;&quot;)&gt;<span class="texteSurligne">&lt;SINON&gt;&lt;SI&gt;&lt;debutPart(&quot;&quot;)&gt;&lt;ALORS&gt; à &lt;villePart&gt; du &lt;debutPart(&quot;&quot;)&gt; au &lt;finPart(&quot;&quot;)&gt;&lt;FINSI&gt;&lt;FINSI&gt;</span></p>
  <p>L'étiquette devient <img src="images/exemple18.png" alt="" width="391" height="61" class="texteAlignHautEtiquette" /></p>
  <p>Plaçons l'épouse de Jean TISSIPE dans l'arbre et affectons-lui ce modèle d'étiquette.</p>
  <p>L'étiquette affiche <img src="images/exemple19.png" alt="" width="207" height="61" class="texteAlignHautEtiquette" /> </p>
  <p>C'est normal parce que Marie STITU n'est liée à aucun évènement. Ce qui est affiché indique clairement qu'il y a un problème.</p>
  <p>Pour y remédier, utilisons encore une fois les conditions. Le modèle devient :</p>
  <p class="blocCode">&lt;prenoms&gt; &lt;nom&gt;<br />
    <span class="texteSurligne">&lt;SI&gt;</span>&lt;evenement&gt;<span class="texteSurligne">&lt;ALORS&gt;</span>&lt;typeEvt&gt; : &lt;titreEvt&gt;&lt;SI&gt;&lt;debutEvt(&quot;&quot;)&gt;&lt;ALORS&gt; à &lt;villeEvt&gt; du &lt;debutEvt(&quot;&quot;)&gt; au &lt;finEvt(&quot;&quot;)&gt;&lt;SINON&gt;&lt;SI&gt;&lt;debutPart(&quot;&quot;)&gt;&lt;ALORS&gt; à &lt;villePart&gt; du &lt;debutPart(&quot;&quot;)&gt; au &lt;finPart(&quot;&quot;)&gt;&lt;FINSI&gt;&lt;FINSI&gt;<span class="texteSurligne">&lt;FINSI&gt;</span><br />
  <span class="texteSurligne">&lt;SI&gt;</span>&lt;evenement&gt;<span class="texteSurligne">&lt;ALORS&gt;</span>&lt;typeEvt&gt; : &lt;titreEvt&gt;&lt;SI&gt;&lt;debutEvt(&quot;&quot;)&gt;&lt;ALORS&gt; à &lt;villeEvt&gt; du &lt;debutEvt(&quot;&quot;)&gt; au &lt;finEvt(&quot;&quot;)&gt;&lt;SINON&gt;&lt;SI&gt;&lt;debutPart(&quot;&quot;)&gt;&lt;ALORS&gt; à &lt;villePart&gt; du &lt;debutPart(&quot;&quot;)&gt; au &lt;finPart(&quot;&quot;)&gt;&lt;FINSI&gt;&lt;FINSI&gt;<span class="texteSurligne">&lt;FINSI&gt;</span></p>
  <p>L'étiquette affiche alors <img src="images/exemple20.png" alt="" width="80" height="42" class="texteAlignHautEtiquette" /></p>
  <p>L'arbre s'affiche ainsi <img src="images/exemple21.png" alt="" width="670" height="111" class="imageBordAlignHaut" /></p>
  <p>Il est possible de continuer à utiliser les conditions pour éviter d'afficher &quot;<span class="texteCode">du 06/06/1941 au</span>&quot; alors qu'il n'y a pas de date de fin.</p>
</div>
</div>
</body>
</html>
