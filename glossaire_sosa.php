<?php

//=====================================================================
// Explications sur la numérotation Sosa
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Sosa'];       // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

?>
<style type="text/css">
    .homme {
        background-color: #9999FF;
        border: 1px solid black;
    }

    .femme {
        background-color: #FF9999;
        border: 1px solid black;
    }

    .indet {
        background-color: #FFFFFF;
        border: 1px solid black;
    }
</style>
<?php

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Glossaire_Sosa', '');

?>
R&egrave;gles de base :
<ul>
    <li>La personne dont on part est le num&eacute;ro 1 (Cf. <a href="<?= $root; ?>/glossaire_gen.php#CUJUS">De cujus</a>).</li>
    <li>Pour obtenir le num&eacute;ro d'un p&egrave;re, on multiplie par 2 le num&eacute;ro de la personne.</li>
    <li>Pour obtenir le num&eacute;ro d'un m&egrave;re, on multiplie par 2 le num&eacute;ro de la personne et on ajoute 1.</li>
    <li>Seuls les ascendants directs font l'objet d'une num&eacute;rotation.</li>
</ul>
<br />
Il en ressort :
<ul>
    <li>Un num&eacute;ro pair correspond &agrave; un homme et un nombre impair &agrave; une femme (sauf pour le num&eacute;ro 1).</li>
    <li>Le num&eacute;ro d'une femme est &eacute;gal au num&eacute;ro de son mari + 1.</li>
    <li>Le premier num&eacute;ro d'une g&eacute;n&eacute;ration double &agrave; chaque fois : 1, 2, 4, 8, 16, 32...</li>
    <li>On peut calculer la g&eacute;n&eacute;ration d'appartenance &agrave; partir du num&eacute;ro ou plus exactement du premier num&eacute;ro de la g&eacute;n&eacute;ration. Exemple : 8 = 2 puissance 3 ; il s'agit donc de la 3&egrave;me g&eacute;n&eacute;ration.</li>
    <li>Le premier num&eacute;ro d'une g&eacute;n&eacute;ration correspond au nombre le nombre d'anc&ecirc;tres th&eacute;oriques pour cette g&eacute;n&eacute;ration.</li>
    <li>La num&eacute;rotation n'est valable que pour la personne de d&eacute;part ; elle varie pour ses ascendants et descendants.</li>
</ul>
<br />
Ce qui nous donne :
<br />
<table width="50%" align="center" border="0">
    <tr align="center">
        <td rowspan="4" valign="middle" class="indet">Personne de d&eacute;part<br />De cujus<br />Sosa : 1</td>
        <td rowspan="2" valign="middle" class="homme">P&egrave;re<br />Sosa : 2<br />(=1*2)</td>
        <td class="homme">Grand-p&egrave;re paternel<br />Sosa : 4<br />(=2*2)</td>
    </tr>
    <tr align="center">
        <td class="femme">Grand-m&egrave;re paternelle<br />Sosa : 5<br />(=2*2+1)</td>
    </tr>
    <tr align="center">
        <td rowspan="2" valign="middle" class="femme">M&egrave;re<br />Sosa : 3<br />(=1*2+1)</td>
        <td class="homme">Grand-p&egrave;re maternel<br />Sosa : 6<br />(=3*2)</td>
    </tr>
    <tr align="center">
        <td class="femme">Grand-m&egrave;re maternelle<br />Sosa : 7<br />(=3*2+1)</td>
    </tr>
</table>
<br /><a href="<?= $root; ?>/calc_so.php">Calculette Sosa</a><br />

<table cellpadding="0" width="100%">
    <tr>
    <td align="right">
    <?= $compl; ?>
    <a href="<?= $root; ?>/"><img src="<?= $root; ?>/assets/img/house.png" alt="Accueil" title="Accueil" /></a>
    </td>
    </tr>
    </table>
</body>

</html>