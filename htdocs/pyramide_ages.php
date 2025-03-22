<?php

//=====================================================================
// Affichage de l'âge (pyramide) au décès pour les femmes et les hommes
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille éventuellement les boutons pour avoir un comportement standard
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Death_Age'];    // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 160);
Insere_Haut($titre, $compl, 'Pyramide_Ages', '');

$largeur_max = 350;
$num_lig = 0;

$larg_sexe = '45%';
$height = 12;
$deb_img_h = '<img src="' . $barre_homme . '" height="' . $height . '" width="';
$deb_img_f = '<img src="' . $barre_femme . '" height="' . $height . '" width="';

// Initialisation des compteurs
for ($a = 0; $a <= 100; $a++) {
    $nb_F[$a] = 0;
    $nb_H[$a] = 0;
}
$tot_F = 0;
$tot_H = 0;
$mois_F = 0;
$mois_H = 0;

// Initialisations des zones doyens
$doyen_F     = -1;
$doyen_H     = -1;
$ndoyen_F    = '';
$ddoyen_F    = '';
$npdoyen_F   = '';
$ref_doyen_F = -1;
$ref_doyen_H = -1;
$ndoyen_H    = '';
$ddoyen_H    = '';
$npdoyen_H   = '';

// Récupération des personnes pour lesquelles on peut calculer un âge
$sql = 'SELECT Sexe, Ne_le, Decede_Le, Reference, Nom, Prenoms FROM ' . nom_table('personnes') .
    " WHERE Ne_le LIKE '_________L'" .
    " AND Decede_Le LIKE '_________L'";
if (!IS_GRANTED('P')) $sql = $sql . " and Diff_Internet = 'O' ";

// Stockage des cumuls d'âges des hommes et des femmes dans des tableaux
if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $mois = Age_Mois($row[1], $row[2]);

        if ($mois > 0) {
            $ans = floor($mois / 12);
        } else {
            $ans = 0;
        }

        // On rassemble tous les centenaires
        if ($ans > 100) {
            $ans = 100;
        }

        $sexe = $row['0'];
        if ($sexe == 'f') {
            ++$tot_F;
            ++$nb_F[$ans];
            $mois_F += $mois;
            if ($mois > $doyen_F) {
                $doyen_F     = $mois;
                $ref_doyen_F = $row[3];
                $ndoyen_F    = $row[1];
                $ddoyen_F    = $row[2];
                $npdoyen_F   = UnPrenom($row[5]) . ' ' . $row[4];
            }
        }
        if ($sexe == 'm') {
            ++$tot_H;
            ++$nb_H[$ans];
            $mois_H += $mois;
            if ($mois > $doyen_H) {
                $doyen_H     = $mois;
                $ref_doyen_H = $row[3];
                $ndoyen_H    = $row[1];
                $ddoyen_H    = $row[2];
                $npdoyen_H   = UnPrenom($row[5]) . ' ' . $row[4];
            }
        }
    }
}

// Affichage des compteurs
// Représentativité maximum ?
// Et dispaching dans les blocs
$max = 0;
$rep_max = 0;
$bloc = 5;
for ($a = 0; $a <= 100; $a++) {
    if ($a >= $bloc) {
        $ref_annee = intval($a / $bloc) * $bloc;
        $ref_annee_m = $ref_annee + $bloc - 1;
    } else {
        if ($a == 0) {
            $ref_annee = 0;
            $ref_annee_m = 0;
        } else {
            $ref_annee = 1;
            $ref_annee_m = $bloc - 1;
        }
    }
    if (!isset($hommes[$ref_annee])) $hommes[$ref_annee] = 0;
    if (!isset($femmes[$ref_annee])) $femmes[$ref_annee] = 0;
    $hommes[$ref_annee] += $nb_H[$a];
    $femmes[$ref_annee] += $nb_F[$a];
    $mins[$ref_annee] = $ref_annee;
    $maxs[$ref_annee] = $ref_annee_m;

    if ($nb_F[$a] > $max) {
        $max = $nb_F[$a];
        $rep_max = $nb_F[$a];
    }
    if ($nb_H[$a] > $max) {
        $max = $nb_H[$a];
        $rep_max = $nb_H[$a];
    }
}

echo '<br />';
echo '<table width="90%" border="0" class="classic" align="center" >' . "\n";
echo '<tr>';
echo '<th width="' . $larg_sexe . '" colspan="2">' . LG_CH_HISTO_AGE_MEN . '</th>';
echo '<th>' . LG_CH_HISTO_AGE . '</th>';
echo '<th width="' . $larg_sexe . '" colspan="2">' . LG_CH_HISTO_AGE_WOMEN . '</th>';
echo '</tr>' . "\n";
$mins = array_reverse($mins, true);
foreach ($mins as $key => $value) {
    echo '<tr>';
    $nb = $hommes[$key];
    echo '<td>' . $nb . '</td>';
    echo '<td align="right">';
    $larg = larg_barre($nb);
    echo $deb_img_h . $larg . '" alt="Pourcent" title="' . $nb . ' ' . LG_CH_HISTO_AGE_PERS . '"/>';
    echo '</td>';
    echo '<td align="center">';
    if ($key == 0) {
        echo '0';
    } else {
        if ($key == 100) echo '100 ' . $LG_and . ' +';
        else echo $mins[$key] . ' - ' . $maxs[$key];
    }
    echo '</td>';
    $nb = $femmes[$key];
    echo '<td align="left">';
    $larg = larg_barre($nb);
    echo $deb_img_f . $larg . '" alt="Pourcent" title="' . $nb . ' ' . LG_CH_HISTO_AGE_PERS . '"/>';
    echo '</td>';
    echo '<td>' . $nb . '</td>';
    echo '</tr>';
}
if (($tot_H != 0) or ($tot_F != 0)) {
    echo '<tr><td colspan="5">&nbsp;</td></tr>';
    echo '<tr><td colspan="2" align="right">';
    if ($tot_H != 0) {
        echo Decompose_Mois(intval($mois_H / $tot_H)) . '&nbsp;';
    } else echo '&nbsp;';
    echo '</td>';
    echo '<td align="center">' . LG_CH_HISTO_AVERAGE_AGE . '</td>';
    echo '<td colspan="2" align="left">';
    if ($tot_F != 0) {
        echo Decompose_Mois(intval($mois_F / $tot_F));
    } else echo '&nbsp;';
    echo '</td>';
    echo '</tr>' . "\n";
}

echo '</table>';

echo '<br />';
if ($ndoyen_H != '') {
    echo '<br />' . LG_CH_HISTO_AGE_OLDEST_M . ' : <a href="' . $root . '/fiche_fam_pers?Refer=' . $ref_doyen_H . '">' . $npdoyen_H . '</a> : ' . Age_Annees_Mois($ndoyen_H, $ddoyen_H);
}
if ($ndoyen_F != '') {
    echo '<br />' . LG_CH_HISTO_AGE_OLDEST_W . ' : <a href="' . $root . '/fiche_fam_pers?Refer=' . $ref_doyen_F . '">' . $npdoyen_F . '</a> : ' . Age_Annees_Mois($ndoyen_F, $ddoyen_F);
}

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';

function larg_barre($nb)
{
    global $largeur_max, $max;
    $larg = 0;
    if ($nb > 0)
        $larg = intval($largeur_max / $max * $nb);
    return $larg;
}
?>
</body>

</html>