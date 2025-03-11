<?php

//=====================================================================
// Affichage de l'historique de l'âge au décès pour les femmes et les hommes en fonction de l'année de naissance
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'L';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Histo_Death'];        // Titre pour META
$x = Lit_Env();
$index_follow = 'ON';        // NOFOLLOW demandé pour les moteurs
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 220);

function affiche($donnee)
{
    global $tot;
    if ($tot) echo '<b>' . $donnee . '</b>';
    else      echo $donnee;
}

function calcul_precedent()
{
    global $root, $nb_F, $nb_H, $moy_F, $moy_H, $ref_annee_anc, $num_lig, $deb_barre_h, $deb_barre_f;
    if ($nb_F != 0) $moy_F = $moy_F / $nb_F;
    if ($nb_H != 0) $moy_H = $moy_H / $nb_H;
    echo '<tr>' . "\n";
    $larg = intval($moy_H / 5);
    $age = Decompose_Mois($moy_H);
    echo '<td>';
    affiche($age);
    echo '</td>';
    echo '<td align="right">';
    echo $deb_barre_h . $larg . '" alt="Pourcent" title="' . $nb_H . ' ' . LG_CH_HISTO_AGE_PERS . '"/>';
    echo '</td>';
    echo '<td align="center">';
    if ($ref_annee_anc != 'ensemble') {
        $fin_annee = $ref_annee_anc + 19;
        echo '<a href="' . $root . '/histo_ages_deces.php?Debut=' . $ref_annee_anc . '&amp;Fin=' . $fin_annee . '" title="' . LG_CH_HISTO_REPARTITION . '">' . $ref_annee_anc . '-' . $fin_annee . '</a>';
    } else echo '<b>' . ucfirst($ref_annee_anc) . '</b>';
    echo '</td>';
    $larg = intval($moy_F / 5);
    $age = Decompose_Mois($moy_F);
    echo '<td>';
    echo $deb_barre_f . $larg . '" alt="Pourcent" title="' . $nb_F . ' ' . LG_CH_HISTO_AGE_PERS . '"/>';
    echo '</td>';
    echo '<td>';
    affiche($age);
    echo '</td>';
    echo '</tr>' . "\n";
}

Insere_Haut($titre, $compl, 'Pyramide_Ages_Histo', '');

$ref_annee_anc = 0;
$nb_F_Tot      = 0;
$nb_H_Tot      = 0;
$moy_F_Tot     = 0;
$moy_H_Tot     = 0;

// Zones pour les doyen(ne)s
$max_mois_h = 0;
$max_mois_f = 0;
$ref_max_h = 0;
$ref_max_f = 0;

// Récupération des personnes pour lesquelles on peut calculer un âge
$sql = 'SELECT Sexe, Ne_le, Decede_Le, Reference FROM ' . nom_table('personnes') .
    " WHERE Ne_le LIKE '_________L'" .
    " AND Decede_Le LIKE '_________L' ";
if (!$est_privilegie) {
    $sql = $sql . "and Diff_Internet = 'O' ";
}
$sql = $sql . 'order by Ne_Le';

$num_lig = 0;

$height = 15;
$deb_barre_h = '<img src="' . $barre_homme . '" height="' . $height . '" width="';
$deb_barre_f = '<img src="' . $barre_femme . '" height="' . $height . '" width="';


$larg_sexe = '40%';
echo '<br />';
echo '<table width="90%" border="0" class="classic" align="center" >' . "\n";
echo '<tr>';
echo '<th width="' . $larg_sexe . '" colspan="2">' . LG_CH_HISTO_AGE_MEN . '</th>';
echo '<th>' . LG_CH_HISTO_AGE_WED . '</th>';
echo '<th width="' . $larg_sexe . '" colspan="2">' . LG_CH_HISTO_AGE_WOMEN . '</th>';
echo '</tr>' . "\n";

$tot = false;
// Stockage des cumuls d'âges des hommes et des femmes dans des tableaux
if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $ref = $row[3];
        $sexe = $row[0];
        $mois = Age_Mois($row[1], $row[2]);
        $annee = intval(substr($row[1], 0, 4));
        $ref_annee = intval($annee / 20) * 20;
        // Traitements en rupture sur l'année de référence : affichage et init
        if ($ref_annee != $ref_annee_anc) {
            if ($ref_annee_anc > 0) {
                $nb_F_Tot  += $nb_F;
                $nb_H_Tot  += $nb_H;
                $moy_F_Tot += $moy_F;
                $moy_H_Tot += $moy_H;
                $x = calcul_precedent();
            }
            $nb_F = 0;
            $nb_H = 0;
            $moy_F = 0;
            $moy_H = 0;
            $ref_annee_anc = $ref_annee;
        }
        // On ne retient pas les enfants de moins d'1 an
        if ($mois > 11) {
            if ($sexe == 'f') {
                $moy_F += $mois;
                $nb_F++;
                if ($mois > $max_mois_f) {
                    $max_mois_f = $mois;
                    $ref_max_f = $ref;
                }
            }
            if ($sexe == 'm') {
                $moy_H += $mois;
                $nb_H++;
                if ($mois > $max_mois_h) {
                    $max_mois_h = $mois;
                    $ref_max_h = $ref;
                }
            }
        }
    }
    if (isset($ref_annee)) {
        $ref_annee_anc = $ref_annee;
        $nb_F_Tot  += $nb_F;
        $nb_H_Tot  += $nb_H;
        $moy_F_Tot += $moy_F;
        $moy_H_Tot += $moy_H;
        $x = calcul_precedent();
        // Affichage des totaux
        $tot = true;
        $nb_F  = $nb_F_Tot;
        $nb_H  = $nb_H_Tot;
        $moy_F = $moy_F_Tot;
        $moy_H = $moy_H_Tot;
        $ref_annee_anc = 'ensemble';
        $x = calcul_precedent();
    }
}

echo '</table>';

$Nom = '';
$Prenoms = '';
if (Get_Nom_Prenoms($ref_max_h, $Nom, $Prenoms)) {
    echo '<br />' . LG_CH_HISTO_AGE_OLDEST_M . ' : <a ' . Ins_Ref_Pers($ref_max_h) . '>' . $Prenoms . '&nbsp;' . $Nom . '</a> (' . Decompose_Mois($max_mois_h) . ')';
}
if (Get_Nom_Prenoms($ref_max_f, $Nom, $Prenoms)) {
    echo '<br />' . LG_CH_HISTO_AGE_OLDEST_W . ' : <a ' . Ins_Ref_Pers($ref_max_f) . '>' . $Prenoms . '&nbsp;' . $Nom . '</a> (' . Decompose_Mois($max_mois_f) . ')';
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour);

Insere_Bas($compl);
?>
</body>

</html>