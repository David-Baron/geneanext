<?php
//=====================================================================
// Affichage des anniversaires de naissance, mariage et décès du mois en cours
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Anniversaires';              // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('ignorer', 'sel_mois');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ignorer = Secur_Variable_Post($ignorer, 1, 'S');
$ignorer_dec = false;
if ($ignorer == 'O') $ignorer_dec = true;

$Mois = Secur_Variable_Post($sel_mois, 1, 'N');
$cMois = date('m');
if ($Mois == 0) $Mois = $cMois;

// Renvoye d' ou de en fonction du mois
function du_mois($Mois)
{
    global $Mois_Lib;
    $lib_mois = $Mois_Lib[$Mois - 1];
    $article = 'de ';
    if ((substr($lib_mois, 0, 1) == 'a') or
        (substr($lib_mois, 0, 1) == 'o')
    ) {
        $article = 'd\'';
    }
    return $article . $lib_mois;
}

// Affichage des naissances et des décès
// La requête est du type : select Nom,Prenoms,Reference, Ne_le (...)
function aff_nai_dec($type, $req)
{
    global $db, $Mois, $Auj, $cMois, $chemin_images_util, $ignorer_dec, $LG_birth_many, $LG_death_many, $LG_Day_Birth, $LG_Day_Death;
    $affiche = true;
    if ($type == 'N') {
        $lib_titre = $LG_birth_many . ' ';
        $icone = 'anniv_nai';
        $lib_icone = $LG_Day_Birth;
    } else {
        $lib_titre = $LG_death_many . ' ';
        $icone = 'anniv_dec';
        $lib_icone = $LG_Day_Death;
    }
    $aff_icone = '&nbsp;&nbsp;' . Affiche_Icone($icone, $lib_icone);
    echo '<tr><th colspan="2">' . $lib_titre . du_mois($Mois) . '</th></tr>';
    $res = lect_sql($req);
    $num_lig = 0;
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $la_date = $row[3];
        if ($type == 'N') {
            if ($ignorer_dec) $affiche = determine_etat_vivant($la_date);
        }
        if ($affiche) {
            echo '<tr>';
            $JDate = substr($la_date, 6, 2);
            echo '<td>&nbsp;';
            echo Etend_date($la_date);
            if (($JDate == $Auj) and ($Mois == $cMois)) echo $aff_icone;
            echo '</td>' . "\n";
            $Ref_Pers = $row[2];
            echo '<td>&nbsp;<a ' . Ins_Ref_Pers($Ref_Pers) . '>' . my_html(UnPrenom($row[1]) . ' ' . $row[0]) . '</a>';

            // Affichage de l'image par défaut
            $image = Rech_Image_Defaut($Ref_Pers, 'P');
            if ($image != '') {
                $image = $chemin_images_util . $image;
                echo '<br />';
                Aff_Img_Redim_Lien($image, 100, 100, 'id' . $type . $Ref_Pers);
            }
            echo '</td></tr>' . "\n";
        }
    }
    $res->closeCursor();
}

$compl = Ajoute_Page_Info(600, 200);
$nb = $Mois - 1;
Insere_Haut('Anniversaires ' . du_mois($Mois), $compl, 'Anniversaires', '');

$xMois = zerofill2($Mois);
$Auj = date('d');

$n_personnes = nom_table('personnes');

echo '<form method="post" action="">' . "\n";
echo '<table width="80%" align= "center"><tr align= "center"><td class="rupt_table">';
echo $LG_Choose_Month . '&nbsp;:&nbsp;<select name="sel_mois" size="1">' . "\n";
echo '<option value="0">-- Mois --</option>' . "\n";
for ($nb = 0; $nb < 12; $nb++) {
    $nb2 = $nb + 1;
    echo '<option value="' . $nb2 . '">' . $Mois_Lib[$nb] . '</option>' . "\n";
}
echo '</select>' . "\n";
echo '</td>';

echo '<td  class="rupt_table"><label for="ignorer">' . $LG_Ignore . ' : ' . Etend_date($date_lim_vivant . 'GL') . ')</label> ';
echo '<input type="checkbox" id="ignorer" name="ignorer" value="O" ';
if ($ignorer == 'O') echo 'checked="checked"';
echo '/></td><td  class="rupt_table"><input type="submit" value="Afficher la liste"/></td>' . "\n";
echo '</tr></table>' . "\n";
echo '</form>' . "\n";

echo '<table width="100%" border="0">' . "\n";

// Anniversaires de naissance
echo '<tr valign="top"><td>';
echo '<table width="100%" border="0" class="classic" cellspacing="1" align="center" >' . "\n";
$sql = 'SELECT Nom, Prenoms, Reference, Ne_le FROM ' . $n_personnes . " where Ne_le like '____" . $xMois . "___L'";
// L'utilisateur a demandé à ignorer les personnes avec
if ($ignorer_dec) $sql .= ' and Decede_Le = \'\' ';
if (!$est_privilegie) $sql = $sql . " and Diff_Internet = 'O' ";
$sql = $sql . " order by substr(Ne_le,7,2)";
// Affichage du résultat
aff_nai_dec('N', $sql);
echo '</table>' . "\n";
echo '</td>';

// Anniversaires de mariage
echo "<td>";
echo '<table width="100%" border="0" class="classic" cellspacing="1" align="center" >' . "\n";
$sql = 'SELECT m.Nom as NomM, m.Prenoms as PrenomsM, m.Reference as RefM,'
    . ' f.Nom as NomF, f.Prenoms as PrenomsF, f.Reference as RefF, Maries_Le, u.Reference as RefU,'
    . ' f.Ne_le as Ne_F, m.Ne_le as Ne_M'
    . ' FROM ' . $n_personnes . ' m, ' . $n_personnes . ' f, ' . nom_table('unions') . " u where Maries_Le like '____" . $xMois . "___L' "
    . " and m.Reference = u.Conjoint_1 and f.Reference = u.Conjoint_2 ";
if ($ignorer_dec) $sql .= ' and m.Decede_Le = \'\'  and f.Decede_Le = \'\' ';
if (!$est_privilegie) $sql = $sql . " and m.Diff_Internet = 'O' and f.Diff_Internet = 'O' ";
$sql = $sql . " order by substr(Maries_Le,7,2)";
echo '<tr><th colspan="2">' . $LG_wedding_many . ' du mois ' . du_mois($Mois) . '</th></tr>';
$res = lect_sql($sql);
$num_lig = 0;
$aff_icone = '&nbsp;&nbsp;' . Affiche_Icone('anniv_mar', 'Anniversaire de mariage');
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    $affiche = true;
    if ($ignorer_dec) {
        $affiche = determine_etat_vivant($row['Ne_F']);
        if ($affiche) $affiche = determine_etat_vivant($row['Ne_M']);
    }
    if ($affiche) {
        echo '<tr>';
        $mar = $row['Maries_Le'];
        $JDate = substr($mar, 6, 2);
        echo '<td>&nbsp;';
        echo Etend_date($mar);
        if (($JDate == $Auj) and ($Mois == $cMois)) echo $aff_icone;
        echo '</td>' . "\n";
        echo '<td>&nbsp;<a ' . Ins_Ref_Pers($row['RefM']) . '>'
            . my_html(UnPrenom($row['PrenomsM'])) . '&nbsp;'
            . my_html($row['NomM']) . '</a>';
        echo '<br />&nbsp;<a ' . Ins_Ref_Pers($row['RefF']) . '>'
            . my_html(UnPrenom($row['PrenomsF'])) . '&nbsp;'
            . my_html($row['NomF']) . '</a>';

        // Affichage de l'image par défaut
        $Ref_U = $row['RefU'];
        $image = Rech_Image_Defaut($Ref_U, 'U');
        if ($image != '') {
            $image = $chemin_images_util . $image;
            echo '<br />';
            Aff_Img_Redim_Lien($image, 100, 100, 'id' . $Ref_U);
        }

        echo '</td></tr>' . "\n";
    }
}
$res->closeCursor();
echo '</table>' . "\n";
echo "</td>";

// Anniversaires de décès
echo "<td>";
echo '<table width="100%" border="0" class="classic" cellspacing="1" align="center" >' . "\n";
$sql = "SELECT Nom, Prenoms, Reference, Decede_Le FROM " . nom_table('personnes') . " where Decede_Le like '____" . $xMois . "___L'";
if (!$est_privilegie) $sql = $sql . " and Diff_Internet = 'O' ";
$sql = $sql . " order by substr(Decede_Le,7,2)";
// Affichage du résultat
aff_nai_dec('D', $sql);
echo '</table>' . "\n";
echo "</td>";

echo "</tr>";

echo "</table>";

Insere_Bas($compl);
?>
</body>

</html>