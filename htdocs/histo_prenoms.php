<?php

//=====================================================================
// Prénoms au cours du temps en fonction de l'année de naissance
// ==> prénoms les plus portés pour les hommes et les femmes
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$tab_variables = array('annuler', 'Horigine', 'tri');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = LG_STAT_SURNAMES;      // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Verrouillage sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

$compl = Ajoute_Page_Info(600, 220);

function affiche($donnee)
{
    global $tot;
    if ($tot) echo '<b>' . $donnee . '</b>';
    else      echo $donnee;
}

function aff_res($res)
{
    global $tri, $def_enc;
    echo '<td>';
    $separ = '';
    if ((isset($res)) and ($res != '')) {
        if ($tri == 'A') asort($res);
        else arsort($res);
        foreach ($res as $key => $val) {
            //echo "$separ&nbsp;$key ($val)";
            $key = my_html($key);
            echo "$separ&nbsp;$key : $val";
            $separ = ',';
        }
    } else echo '&nbsp;';
    echo '</td>' . "\n";
}

function calcul_precedent()
{
    global $nb_F, $nb_H, $ref_annee_anc, $num_lig, $Pr_1_hommes, $Pr_1_femmes, $Pr_hommes, $Pr_femmes;
    if (pair($num_lig++)) $style = 'liste';
    else                  $style = 'liste2';
    echo '<tr class="' . $style . '">' . "\n";
    aff_res($Pr_1_hommes);
    aff_res($Pr_hommes);

    $fin_annee = $ref_annee_anc + 19;
    echo '<td align="center">' . $ref_annee_anc . '-' . $fin_annee . '</td>';

    aff_res($Pr_1_femmes);
    aff_res($Pr_femmes);

    echo '</tr>';
}

Insere_Haut(my_html($titre), $compl, 'Prenoms_Histo', '');

// Tri ascendant par défaut
if ($tri == '') $tri = 'A';

echo '<form action="' . my_self() . '" method="post">' . "\n";
echo '<table border="0" width="60%" align="center">' . "\n";
echo '<tr align="center">';

echo '<td class="rupt_table">' . my_html(LG_STAT_SURNAMES_SORT_FREQ) . LG_SEMIC . "\n";
echo '<input type="radio" name="tri" value="A"';
if ($tri == 'A') echo ' checked="checked"';
echo ' />' . my_html(LG_STAT_SURNAMES_ASC) . '&nbsp;';
echo '<input type="radio" name="tri" value="D"';
if ($tri == 'D') echo ' checked="checked"';
echo ' />' . my_html(LG_STAT_SURNAMES_DESC);
echo '</td>' . "\n";
echo '<td class="rupt_table"><input type="submit" value="' . my_html($LG_display_list) . '"/>';
echo '</td>' . "\n";
echo '</tr></table>';
echo '<input type="hidden" id="memo_etat" name="memo_etat"/>';
echo '</form>' . "\n";

$ref_annee_anc = 0;
$nb_F = 0;
$nb_H = 0;

// Récupération des personnes pour lesquelles la date de naissance est certaine
$sql = 'SELECT Sexe, Ne_le, Prenoms FROM ' . nom_table('personnes') .
    " WHERE Ne_le LIKE '_________L' and Sexe in ('m','f') ";
if (!$est_privilegie) {
    $sql = $sql . "and Diff_Internet = 'O' ";
}
$sql = $sql . 'order by Ne_Le';

$num_lig = 0;

$Pr_1_hommes = [];
$Pr_1_femmes = [];
$Pr_hommes = [];
$Pr_femmes = [];

if ($res = lect_sql($sql)) {

    echo '<table width="90%" border="0" class="classic" align="center" >' . "\n";
    echo '<tr>';
    echo '<th width="40%" colspan="2">' . my_html(LG_STAT_SURNAMES_MEN) . '</th>';
    echo '<th width="10%">' . $LG_birth . '</th>';
    echo '<th width="40%" colspan="2">' . my_html(LG_STAT_SURNAMES_WOMEN) . '</th>';
    echo '</tr>' . "\n";
    echo '<tr align="center">';
    echo '<td width="20%">' . my_html(LG_STAT_SURNAMES_FIRST) . '</td>';
    echo '<td width="20%">' . my_html(LG_STAT_SURNAMES_ALL) . '</td>';
    echo '<td>&nbsp;</td>';
    echo '<td width="20%">' . my_html(LG_STAT_SURNAMES_FIRST) . '</td>';
    echo '<td width="20%">' . my_html(LG_STAT_SURNAMES_ALL) . '</td>';
    echo '</tr>' . "\n";

    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $sexe = $row[0];
        $annee = intval(substr($row[1], 0, 4));
        $ref_annee = intval($annee / 20) * 20;
        $prenoms = explode(' ', $row[2]);
        $c_prenoms = count($prenoms);
        //echo $row[2].' / '.$annee.' / '.$sexe.' ref année '.$ref_annee.' ref année anc '.$ref_annee_anc.'<br>';

        if ($c_prenoms > 0) {
            // Traitements en rupture sur l'année de référence : affichage et init
            if (($ref_annee != $ref_annee_anc) and ($ref_annee_anc > 0)) {
                calcul_precedent();
                $nb_F = 0;
                $nb_H = 0;
                // unset($Pr_1_hommes);
                // unset($Pr_1_femmes);
                // unset($Pr_hommes);
                // unset($Pr_femmes);
                $Pr_1_hommes = [];
                $Pr_1_femmes = [];
                $Pr_hommes = [];
                $Pr_femmes = [];
            }
            $ref_annee_anc = $ref_annee;

            $p1 = $prenoms[0];
            if ($sexe == 'm') {
                if (!isset($Pr_1_hommes[$p1]))
                    $Pr_1_hommes[$p1] = 1;
                else
                    $Pr_1_hommes[$p1] += 1;
                for ($nb = 0; $nb < $c_prenoms; $nb++) {
                    if (!isset($Pr_hommes[$prenoms[$nb]])) $Pr_hommes[$prenoms[$nb]] = 1;
                    else $Pr_hommes[$prenoms[$nb]] += 1;
                }
            } else {
                if (!isset($Pr_1_femmes[$p1])) $Pr_1_femmes[$p1] = 1;
                else $Pr_1_femmes[$p1] += 1;
                for ($nb = 0; $nb < $c_prenoms; $nb++) {
                    if (!isset($Pr_femmes[$prenoms[$nb]])) $Pr_femmes[$prenoms[$nb]] = 1;
                    else $Pr_femmes[$prenoms[$nb]] += 1;
                }
            }
        }
    }
    if ($num_lig > 0) calcul_precedent();
    echo '</table>';
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '');

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>