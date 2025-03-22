<?php

//=====================================================================
// Vérification de la numérotation Sosa
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('C')) {
    header('Location: ' . $root . '/');
    exit();
}

$tab_variables = array(
    'ok',
    'annuler',
    'modif',
    'Horigine',
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_Rectifier), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_Rectifier) $ok = 'OK';

$acces = 'M';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Check_Sosa'];    // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

$echo_modif = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '">';
$n_pers = nom_table('personnes');

// Accède à une personne et l'affiche
function Accede_Personne($Reference)
{
    global $root, $db, $Personne, $Sosa, $echo_modif, $num_lig, $def_enc, $n_pers;
    $Sql = 'select Reference, Nom, Prenoms, Numero from ' . $n_pers . ' where Reference = ' . $Reference . ' limit 1';
    $Res = lect_sql($Sql);
    if ($Personne = $Res->fetch(PDO::FETCH_NUM)) {
        //echo 'Sosa lu : '.$Personne[3].', attendu : '.$Sosa.'<br />';
        if ($Personne[3] != $Sosa) {
            $Ref = $Personne[0];
            if (pair($num_lig++)) $style = 'liste';
            else                $style = 'liste2';
            echo '<tr class="' . $style . '">';
            echo '<td align="center" width="10%"><input type="checkbox" name="modif[]" value="' . $Ref . '_' . $Sosa . '"/></td>';
            echo '<td width="20%">' . $Personne[3] . '</td>';
            echo '<td width="20%">' . $Sosa . '</td>';
            echo '<td width="50%">';
            echo '<a href="' . $root . '/fiche_fam_pers?Refer=' . $Ref . '">' . my_html($Personne[2] . ' ' . $Personne[1]) . '</a>';
            echo ' <a href="' . $root . '/edition_personne?Refer=' . $Ref . '">' . $echo_modif;
            //echo 'mem : '.memory_get_usage();
            echo '</td></tr>' . "\n";
        }
    }
    $Res->closeCursor();
}

$compl = Ajoute_Page_Info(600, 200);

$Ind_Ref = 0;

if ($bt_OK) {
    Ecrit_Entete_Page($titre, $contenu, $mots);
    include(__DIR__ . '/assets/js/monSSG.js');
}

Insere_Haut(my_html($titre), $compl, 'Verif_Sosa', '');

// Demande de rectification
if ($bt_OK) {
    $dem_mod = 0;
    $nb_mod  = 0;
    $deb_upd = 'update ' . nom_table('personnes') . ' set numero = ';
    if (isset($_POST['modif'])) {
        $tab_modif = $_POST['modif'];
        foreach ($tab_modif as $lamodif) {
            $p    = strpos($lamodif, '_');
            $ref  = substr($lamodif, 0, $p);
            $ref = Secur_Variable_Post($ref, 1, 'N');
            $theo = substr($lamodif, $p + 1);
            $theo = Secur_Variable_Post($theo, 1, 'N');
            if ($ref and $theo) {
                $req = $deb_upd . $theo . ' where reference = ' . $ref . ';';
                $dem_mod++;
                $Res = maj_sql($req);
                $nb_mod += $enr_mod;
            }
        }
        echo '<br />' . $dem_mod . ' ' . my_html(LG_CHK_SOSA_RESULT_1 . ' ; ' . $nb_mod . ' ' . LG_CHK_SOSA_RESULT_2) . '<br />';
    }
}

// Initialisations
$Num_Pere = 0;
$Num_Mere = 0;
$Sosa = 1;
$num_lig = 0;

// Récupération de la référence de la personne '1'
$Sql = 'select Reference from ' . nom_table('personnes') . ' where Numero = \'1\'';
$Res = lect_sql($Sql);

if ($Personne = $Res->fetch(PDO::FETCH_ASSOC)) {

    echo '<form id="saisie" method="post" action="' . my_self() . '">' . "\n";

    bt_ok_an_sup($lib_Rectifier, $lib_Annuler, '', '', false);

    echo '<br /><br />' . my_html(LG_CHK_SOSA_NON_MATCHING) . '<br /><br />';

    echo '<table width="95%" border="0" class="classic" align="center">' . "\n";
    echo '<tr>';
    echo '<th width="10%"><input type="checkbox" id="selTous" name="selTous" value="on" onclick="checkUncheckAll(this);"/>&nbsp;'
        . '<label for="selTous">' . $LG_All . '</label>'
        . '</th>';
    echo '<th width="20%">' . LG_CHK_SOSA_NUMBER . '</th>';
    echo '<th width="20%">' . LG_CHK_SOSA_CALC_NUMBER . '</th>';
    echo '<th width="50%">' . LG_CHK_SOSA_PERSON . '</th>';
    echo '</tr>' . "\n";

    // 2 paires d'indices min / max
    // C : sur la génération courante
    // P : sur la génération précédente
    $MinC = 1;
    $MaxC = 1;

    $Ind_Cour = 1;
    $Sosa = 1;

    $Pers[1] = $Personne['Reference'];

    do {
        $trouve = false;
        $MinP = $MinC;
        $MaxP = $MaxC;
        $MinC = $Ind_Cour + 1;
        //echo '<br />----$MinP : '.$MinP. ', $MaxP:  '.$MaxP.'<br />';
        for ($nb = $MinP; $nb <= $MaxP; $nb++) {
            //echo 'nb : '.$nb.', ';
            //echo '$MinP : '.$MinP.', ';
            //echo '$MaxP : '.$MaxP.'<br />';
            $PersC = $Pers[$nb];
            $Pere_GP = 0;
            $Mere_GP = 0;
            $Rang_GP = 0;
            // Recherche des parents si la personne est définie
            $c1 = substr($PersC, 0, 1);
            //echo 'Perc : '.$PersC.', ';
            //echo 'c1 : '.$c1.'<br />';
            if ($c1 != '^') {
                //echo 'Rech parents de '.$PersC.'<br />';
                $x = Get_Parents($PersC, $Pere_GP, $Mere_GP, $Rang_GP);
                //echo 'parents : '.$Pere_GP.', '.$Mere_GP.'<br />';
                ++$Ind_Cour;
                ++$Sosa;
                if ($Pere_GP) {
                    $Pers[$Ind_Cour] = $Pere_GP;
                    $trouve = true;
                    Accede_Personne($Pere_GP);
                } else $Pers[$Ind_Cour] = '^1';
                ++$Ind_Cour;
                ++$Sosa;
                if ($Mere_GP) {
                    $Pers[$Ind_Cour] = $Mere_GP;
                    $trouve = true;
                    Accede_Personne($Mere_GP);
                } else $Pers[$Ind_Cour] = '^1';
            } else {
                // Combien de postes concaténés ?
                $nb_concat = substr($PersC, 1);
                //echo 'nombre : '.$nb_concat.', ';
                $Sosa += $nb_concat * 2;
                // Principe : dans $Pers, on a ^ et un nombre si la personne est inconnue ; le nombre représente le nombre de personnes inconnues
                // A la première passe, on a ^1 ==> 1 personne inconnue ; les parents le sont aussi ==> ^2, les grands parents aussi ==> ^4
                // Dans cet exemple, 3 postes en remplacent 1 + 2 + 4, donc 7 ; intéressant car un poste prend beaucoup de place en PHP
                $Pers[++$Ind_Cour] = '^' . $nb_concat * 2;
                //echo 'Resultat concat : '.$Pers[$Ind_Cour].'<br />';
            }
        }
        $MaxC = $Ind_Cour;
    } while ($trouve);

    echo '</table>';

    echo '<br />';
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";

    bt_ok_an_sup($lib_Rectifier, $lib_Annuler, '', '', false, true);

    echo '</form>';

    echo '<br /><a href="' . $root . '/init_sosa">' . my_html($LG_Menu_Title['Delete_Sosa']) . '</a>';
} else {
    echo '<img src="' . $root . '/assets/img/error.png" alt="Avertissement"> ';
    echo 'De cujus non trouvé, veuillez attribuer le numéro 1 &agrave; la personne de votre choix ; ';
    echo 'pour ce faire, passez par la <a href="' . $root . '/liste_pers?Type_Liste=P">liste par noms</a>.';
}

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