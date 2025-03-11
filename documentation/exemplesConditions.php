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
<h2>Exemples de conditions dans les mod&egrave;les d'&eacute;tiquettes</h2>
<p>Les exemples développés ici concernent un modèle d'étiquette de personne mais ils peuvent aussi s'appliquer aux modèles d'étiquettes de famille.</p>
<table border="0" cellpadding="3" summary="">
  <tr>
    <th>Variable</th>
    <th>Action</th>
    <th>Situation</th>
    <th>Affichage de l'&eacute;tiquette</th>
    <th>Description du mod&egrave;le</th>
  </tr>
  <tr>
    <td rowspan="2" class="aligneMilieu"><span class="texteCode">&lt;e&gt;</span></td>
    <td rowspan="2" class="aligneMilieu">Accorder un mot au genre de la personne</td>
    <td>Homme</td>
    <td><img src="images/exemple02.jpg" width="97" height="38" class="imageBord1pt" alt="" /></td>
    <td rowspan="2" class="aligneMilieu"><img src="images/exemple01.png" width="159" height="37" class="imageBord1pt" alt="" /></td>
  </tr>
  <tr>
    <td>Femme</td>
    <td><img src="images/exemple03.jpg" width="103" height="38" class="imageBord1pt" alt="" /></td>
  </tr>
  <tr>
    <td rowspan="6" class="aligneMilieu"><span class="texteCode">&lt;SI&gt;</span></td>
    <td rowspan="2" class="aligneMilieu">Voir du texte si une variable est renseign&eacute;e</td>
    <td>La date de naissance est renseign&eacute;e</td>
    <td><img src="images/exemple05.jpg" width="138" height="38" class="imageBord1pt" alt="" /></td>
    <td rowspan="2" class="aligneMilieu"><img src="images/exemple04.png" width="420" height="37" class="imageBord1pt" alt="" /></td>
  </tr>
  <tr>
    <td>La date de naissance n'est pas renseign&eacute;e</td>
    <td><img src="images/exemple06.jpg" width="75" height="19" class="imageBord1pt" alt="" /></td>
  </tr>
  <tr>
    <td rowspan="2" class="aligneMilieu">Voir du texte si une variable n'est pas renseign&eacute;e</td>
    <td>La date de d&eacute;c&egrave;s est renseign&eacute;e</td>
    <td><img src="images/exemple09.jpg" width="72" height="19" class="imageBord1pt" alt="" /></td>
    <td rowspan="2" class="aligneMilieu"><img src="images/exemple07.png" width="386" height="36" class="imageBord1pt" alt="" /></td>
  </tr>
  <tr>
    <td>La date de d&eacute;c&egrave;s n'est pas renseign&eacute;e</td>
    <td><img src="images/exemple08.jpg" width="117" height="38" class="imageBord1pt" alt="" /></td>
  </tr>
  <tr>
    <td rowspan="2" class="aligneMilieu">Voir des textes diff&eacute;rents, suivant qu'une variable est renseign&eacute;e ou non</td>
    <td>La date de d&eacute;c&egrave;s est renseign&eacute;e</td>
    <td><img src="images/exemple11.jpg" width="142" height="38" class="imageBord1pt" alt="" /></td>
    <td rowspan="2" class="aligneMilieu"><img src="images/exemple10.png" width="562" height="40" class="imageBord1pt" alt="" /></td>
  </tr>
  <tr>
    <td>La date de d&eacute;c&egrave;s n'est pas renseign&eacute;e</td>
 <td><img src="images/exemple12.jpg" width="117" height="38" class="imageBord1pt" alt="" /></td>  
  </tr>
</table>
</div>
</div>
</body>
</html>
