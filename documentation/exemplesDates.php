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
<h2>Exemples de mise en forme de date dans un mod&egrave;le d'&eacute;tiquette</h2>
<p>Les exemples développés ici concernent un modèle d'étiquette de personne mais ils peuvent aussi s'appliquer aux modèles d'étiquettes de famille.</p>
<p>Pour expliquer le formatage des dates, j'ai créé eux personnes qu isont :</p>
<p><img src="images/exemple22.png" alt="" width="487" height="206" class="imageBord1pt" /> <img src="images/exemple25.png" alt="" width="513" height="208" class="imageBord1pt" /></p>
<p>Affichons les dates de naissance et de décès avec l'affichage par défaut des dates. Le modèle est :</p>
<p class="blocCode">&lt;prenoms&gt; &lt;nom&gt;<br />
  Naissance : &lt;dateNais(&quot;&quot;)&gt;<br />
  Décès : &lt;dateDeces(&quot;&quot;)&gt;</p>
<p>L'arbre est <img src="images/exemple23.png" alt="" width="474" height="223" class="imageBordAlignHaut" /></p>
<p>Personnalisons un peu ces dates. Pour la date de naissance, indiquons :</p>
<ul>
  <li>la précision ;</li>
  <li>le numéro du jour sur 2 chiffres ;</li>
  <li> le nom complet du mois ;</li>
  <li>l'année au format long.</li>
  </ul>
<p>Pour la date de décès, utilisons les formats courts.</p>
<p class="blocCode">&lt;prenoms&gt; &lt;nom&gt;<br />
  Naissance : &lt;dateNais(&quot;<span class="texteSurligne">&lt;p2&gt; &lt;j4&gt; &lt;j2&gt; &lt;m4&gt; &lt;a4&gt;</span>&quot;)&gt;<br />
  Décès : &lt;dateDeces(&quot;<span class="texteSurligne">&lt;j3&gt; &lt;j1&gt; &lt;m3&gt; &lt;a2&gt;</span>&quot;)&gt;</p>
<p>L'arbre devient <img src="images/exemple24.png" alt="" width="539" height="224" class="imageBordAlignHaut" /></p>
<p>Puisu'il y a des dates dans le calendrier républicain, utilisons la possibilité de convertir ces dates pour les afficher au format grégorien. L'étiquette est :</p>
<p class="blocCode">&lt;prenoms&gt; &lt;nom&gt;<br />
  Naissance : &lt;dateNais(&quot;&lt;p2&gt; &lt;j4&gt; &lt;j2&gt; &lt;m4&gt; &lt;a4&gt;<span class="texteSurligne">&lt;conversion&gt; (&lt;j4&gt; &lt;j2&gt; &lt;m4&gt; &lt;a4&gt;</span>)&quot;)&gt;<br />
  Décès : &lt;dateDeces(&quot;&lt;j3&gt; &lt;j1&gt; &lt;m3&gt; &lt;a2&gt;<span class="texteSurligne">&lt;conversion&gt; (&lt;j4&gt; &lt;j2&gt; &lt;m4&gt; &lt;a4&gt;)</span>&quot;)&gt;</p>
<p>L'arbre montre <img src="images/exemple26.png" alt="" width="646" height="299" class="imageBordAlignHaut" /></p>
<p>L'exemple suivant illustre l'utilisation du mot <span class="texteCode">&lt;finConversion&gt;</span> :</p>
<p class="blocCode">&lt;prenoms&gt; &lt;nom&gt;<br />
  Naissance : &lt;dateNais(&quot;&lt;j2&gt; &lt;m4&gt;&lt;conversion&gt; (&lt;j2&gt; &lt;m4&gt;)&lt;finConversion&gt; &lt;a4&gt; &lt;conversion&gt;(&lt;a4&gt;) &lt;finConversion&gt;&quot;)&gt;</p>
<p>L'arbre affiche <img src="images/exemple27.png" alt="" width="517" height="265" class="imageBordAlignHaut" /></p>

</div>
</div>
</body>
</html>
