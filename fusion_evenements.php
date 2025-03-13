<?php
//=====================================================================
// Fusion d'évènements
// On peut fusionner les commentaires ayant des informations communes mais pas de commentaires
// Un seul commentaire est autorisé par évènement
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

function aff_nb_enr($lib1, $accord)
{
    global $simulation, $nb_enr, $premier_lib, $tab, $h_LG_EVT_MERGE_ACTION, $lg_evt_participation, $lg_evt_participations, $lg_evt_image, $lg_evt_images, $lg_evt_document, $lg_evt_documents, $lg_evt_done;
    if ($nb_enr > 0) {
        $lib1 = 'lg_evt_' . $lib1;
        // Si on a plus d'1 enregistrement, on va chercher le contenu de la variable de même nom mais avec un s
        $plu = is_pluriel($nb_enr);
        if ($plu) $lib1 = $lib1 . 's';
        if ($premier_lib) {
            echo $tab . '- ';
            $premier_lib = false;
        } else echo ', ';
        echo $nb_enr . ' ' . $$lib1 . ' ';
        if ($simulation) echo $h_LG_EVT_MERGE_ACTION . '<br />';
        else {
            // $lg_evt_done est 1 array avec 4 indice en fonction du sexe et du pluriel
            $indice = $accord;
            if ($plu) $accord = $accord . 's';
            echo my_html($lg_evt_done[$accord]) . '<br />';
        }
    }
}

$simulation = -1;

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok', 'simulation', 'Horigine');

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) {
        $$nom_variables = $_POST[$nom_variables];
        // echo $nom_variables.' : '.$_POST[$nom_variables].'<br />';
    } else $$nom_variables = '';
}

$lib_OK = LG_EVT_MERGE_OK;

// Sécurisation des variables postées - phase 1
$ok         = Secur_Variable_Post($ok, strlen($lib_OK), 'S');
$simulation = Secur_Variable_Post($simulation, 1, 'N');
$Horigine   = Secur_Variable_Post($Horigine, 100, 'S');

// var_dump($simulation);
// Dans le cas où la case n'est pas cochée, elle n'est pas reçue, donc on fait comme si on avait reçu faux
if ($simulation == -1) $simulation = false;

$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Event_Merging'];       // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 250);
Insere_Haut(my_html($titre), $compl, 'Fusion_Evenements', '');

// La simulation sera cochée par défaut
if ($ok != $lib_OK) $simulation = 1;

echo '<form method="post">';
echo '<table width="60%" align="center">';
echo '<tr align="center">';
echo '<td class="rupt_table"><label for="simulation">' . LG_EVT_MERGE_SIMULATE . '</label>&nbsp;:&nbsp;';
echo '<input type="checkbox"';
if ($simulation) echo ' checked="checked"';
echo ' id="simulation" name="simulation" value="1"/></td>';
echo '<td class="rupt_table"><input type="submit" name="ok" value="' . $lib_OK . '"/></td>';
echo '</tr></table>';
// echo '<input type="hidden" name="init" value="x" />'
echo '</form>';

echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Information" title="Information"> ' . my_html(LG_EVT_MERGE_TIP) . '<br />';

$Anc_Enreg = '';
$ident_premier = 0;
$premier = true;
$nb_fus = 0;
$nb_enr = 0;
$Type_Ref = 'E';
$tab = '&nbsp;&nbsp;&nbsp;';

$n_participe = nom_table('participe');
$n_images = nom_table('images');
$n_concerne_doc = nom_table('concerne_doc');
$n_evenements = nom_table('evenements');

$sql = 'SELECT Reference, Identifiant_zone, Identifiant_Niveau, Code_Type, Titre, Debut, Fin ' .
    ' FROM ' . nom_table('evenements') .
    ' ORDER BY Titre, Identifiant_zone, Identifiant_Niveau, Code_Type, Debut, Fin, Reference';
$res = lect_sql($sql);

$h_LG_EVT_MERGE_REF = my_html(LG_EVT_MERGE_REF);
$h_LG_EVT_MERGE_OTHER = my_html(LG_EVT_MERGE_OTHER);
$h_LG_EVT_MERGE_ACTION = my_html(LG_EVT_MERGE_ACTION);
$h_LG_EVT_MERGE_IS_COMMENT = my_html(LG_EVT_MERGE_IS_COMMENT);

while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
    $nb_enr++;
    // On concatène l'enregistrement pour le comparer au précédent
    $Nouv_Enreg = $enreg[1] . $enreg[2] . $enreg[3] . $enreg[4] . $enreg[5] . $enreg[6];
    //var_dump($Nouv_Enreg); echo '<br />';
    //echo '['.$enreg[0].']'.'nouv : '.$Nouv_Enreg.'<br />';
    $id_courant = $enreg[0];
    // S'il y a égalité, on regarde si on peut fusionner, ce qui sera le cas s'il n'y a pas de commentaires
    if ($Nouv_Enreg == $Anc_Enreg) {
        //echo 'Fusion ?<br />';
        $Existe_Commentaire = Rech_Commentaire($id_courant, $Type_Ref);
        if (! $Existe_Commentaire) {
            $premier_lib = true;

            if ($premier) {
                echo '<br />' . my_html(LG_EVT_MERGE_PROCESS . ' ' . $enreg[4]) . '<br />';
                echo $tab . '<a href="' . $root . '/fiche_evenement.php?refPar=' . $ident_premier . '">' . $h_LG_EVT_MERGE_REF . '</a><br />';
                $premier = false;
            }
            echo $tab . '<a href="' . $root . '/fiche_evenement.php?refPar=' . $id_courant . '">' . $h_LG_EVT_MERGE_OTHER . '</a><br />';


            // Etape 1 : on bascule les participations sur le commentaire le plus ancien
            if (!$simulation) {
                $sql_upd = 'UPDATE ' . $n_participe . ' SET Evenement = ' . $ident_premier . ' WHERE Evenement = ' . $id_courant;
                maj_sql($sql_upd);
                $nb_enr = $enr_mod;
            } else {
                $sql_upd = 'SELECT count(*) FROM ' . $n_participe . ' WHERE Evenement = ' . $id_courant;
                $res_U = lect_sql($sql_upd);
                $enr = $res_U->fetch(PDO::FETCH_NUM);
                $nb_enr = $enr[0];
            }
            aff_nb_enr('participation', 'f');
            // Etape 2 : on bascule les images sur le commentaire le plus ancien
            if (!$simulation) {
                $sql_U = 'UPDATE ' . $n_images . ' SET Reference = ' . $ident_premier . ' WHERE Reference = ' . $id_courant .
                    ' AND Type_Ref = \'' . $Type_Ref . '\'';
                maj_sql($sql_U);
                $nb_enr = $enr_mod;
            } else {
                $sql_U = 'SELECT count(*) FROM ' . $n_images . ' WHERE Reference = ' . $id_courant .
                    ' AND Type_Ref = \'' . $Type_Ref . '\'';
                $res_U = lect_sql($sql_U);
                $enr = $res_U->fetch(PDO::FETCH_NUM);
                $nb_enr = $enr[0];
            }
            aff_nb_enr('image', 'f');
            // Etape 3 : on bascule les documents sur le commentaire le plus ancien
            if (!$simulation) {
                $sql_U = 'UPDATE ' . $n_concerne_doc . ' SET Reference_Objet = ' . $ident_premier . ' WHERE Reference_Objet = ' . $id_courant .
                    ' AND Type_Objet = \'' . $Type_Ref . '\'';
                maj_sql($sql_U);
                $nb_enr = $enr_mod;
            } else {
                $sql_U = 'SELECT count(*) FROM ' . $n_concerne_doc . ' WHERE Reference_Objet = ' . $id_courant .
                    ' AND Type_Objet = \'' . $Type_Ref . '\'';
                $res_U = lect_sql($sql_U);
                $enr = $res_U->fetch(PDO::FETCH_NUM);
                $nb_enr = $enr[0];
            }
            aff_nb_enr('document', 'm');
            if ($nb_enr) echo '.<br />';
            echo "\n";
            $nb_fus++;
            // Etape 4 : on supprime l'évènement
            if (!$simulation) {
                $sql_U = 'DELETE FROM ' . $n_evenements . ' WHERE Reference = ' . $id_courant;
                maj_sql($sql_U);
            }
        } else {
            echo $h_LG_EVT_IS_COMMENT . ' (' . $id_courant . ') ' . $enreg[4] . '<br />';
        }
    }
    // Sinon, on mémorise l'identifiant du premier bloc potentiel
    else {
        $ident_premier = $id_courant;
        $premier = true;
    }
    // Mémorisation de l'enregistrement
    $Anc_Enreg = $Nouv_Enreg;
}
$plu = pluriel($nb_fus);
$plu = is_pluriel($nb_fus);
if (is_pluriel($nb_fus)) $msg = $lg_evt_nb_events;
else $msg = $lg_evt_nb_event;
echo '<br>' . $nb_fus . ' ' . my_html($msg) . '<br />';
$nb_fus++;

Insere_Bas($compl);
?>
</body>

</html>