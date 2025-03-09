<?php
//=====================================================================
// Complétude des informations des personnes portant un nom
//=====================================================================

session_start();
include('fonctions.php');
$acces = 'L';                        // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Name_Is_Complete'];         // Titre pour META

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

$x = Lit_Env();
$niv_requis = 'P';                // Page accessible à partir du niveau privilégié
include('Gestion_Pages.php');

// Verrouillage de la gestion des documents sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup des variables passées dans l'URL :
$idNom = Recup_Variable('idNom', 'N');            // Famille, ville ou catégorie
$NomL  = Recup_Variable('Nom', 'S');                // Nom de famille, de ville ou de catégorie
$texte = Dem_Texte();                            // texte ou non
$Sortie = Recup_Variable('Sortie', 'C', 'ce');    // Sortie dans un fichier CSV ?

$titre .= $NomL;

$n_unions = nom_table('unions');

$lien = 'href="' . my_self() . '?texte=O' .
    '&amp;idNom=' . $idNom .
    '&amp;Nom=' . StripSlashes(str_replace(' ', '%20', $NomL));

$compl = Ajoute_Page_Info(700, 250) .
    Affiche_Icone_Lien($lien . '"', 'text', 'Format imprimable') . '&nbsp;' .
    Affiche_Icone_Lien('href="' . my_self() . '?idNom=' . $idNom . '&amp;Nom=' . $NomL . '&amp;Sortie=c"', 'exp_tab', 'Export CSV') . '&nbsp;';

if (! $texte) {
    Insere_Haut(my_html($titre), $compl, 'Completude_Nom', $NomL);
} else {
    Insere_Haut_texte(my_html($titre));
    echo '<br />';
}

// Constitution de la requête d'extraction
$sql = 'select Reference, Prenoms, Ne_le, Ville_Naissance, Decede_Le, Ville_Deces, Sexe ' .
    ' from ' . nom_table('personnes') .
    ' where Reference <> 0 ' .
    ' and Reference in (select idPers from ' . nom_table('noms_personnes') . ' where idNom = ' . $idNom . ') ';
if (!$est_privilegie) $sql = $sql . "and Diff_Internet = 'O' ";
$sql = $sql . 'order by Nom, Prenoms';

$num_lig = 0;

if (!$Sortie) $Sortie = 'e';
$CSV = ($Sortie == 'c') ? true : false;

// Balayage
if ($res = lect_sql($sql)) {
    if ($res->RowCount() > 0) {

        // Sortie CSV
        if ($CSV) {
            $nom_fic = construit_fic($chemin_exports, 'completude_' . $NomL . '#', 'csv');
            $fp = ouvre_fic($nom_fic, 'w+');
            // Ecriture de la ligne d'entête
            $ligne = '';
            $ligne .= LG_PERS_REF . ';';
            $ligne .= LG_PERS_PERSONS . ';';
            $ligne .= LG_PERS_BORN . ';';
            $ligne .= LG_PERS_BORN_AT . ';';
            $ligne .= LG_PERS_DEAD . ';';
            $ligne .= LG_PERS_DEAD_AT . ';';
            $ligne .= LG_FATHER . ';';
            $ligne .= LG_MOTHER . ';';
            $ligne .= LG_PERS_IS_COUPLE . ';';
            $ligne .= LG_PERS_UNION_DATE . ';';
            $ligne .= LG_PERS_UNION_AT . ';';
            ecrire($fp, $ligne);
            $img_vert   = 'O';
            $img_orange = 'P';
            $img_rouge  = 'N';
        }
        // Sortie écran
        else {
            // Optimisation : préparation echo des images
            $echo_modif = Affiche_Icone('fiche_edition', $LG_modify) . '</a>';
            $echo_verif = Affiche_Icone('fiche_controle', LG_PERS_CONTROL) . '</a>&nbsp;';

            $img_vert   = '<img src="' . $chemin_images_icones . $Icones['drapeau_vert'] . '" border="0" alt="Drapeau vert"/>';
            $img_orange = '<img src="' . $chemin_images_icones . $Icones['drapeau_orange'] . '" border="0" alt="Drapeau orange"/>';
            $img_rouge  = '<img src="' . $chemin_images_icones . $Icones['drapeau_rouge'] . '" border="0" alt="Drapeau rouge"/>';

            $bord = '0';
            if (!$texte) {
                $bord = '0';
                echo '<br />';
            }
            echo '<table border="' . $bord . '" width="80%" align="center" ';
            if ($texte) echo 'class="tableau_imp"';
            echo '>' . "\n";
            echo '<thead><tr>';
            $style_td = '';
            if (!$texte) $style_td = ' class="rupt_table"';
            echo '<th' . $style_td . '>' . my_html(LG_PERS_PERS) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html(LG_PERS_BORN) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html($LG_at) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html(LG_PERS_DEAD) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html($LG_at) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html(LG_FATHER) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html(LG_MOTHER) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html(LG_PERS_IS_COUPLE) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html($LG_day['on']) . '</th>' . "\n";
            echo '<th' . $style_td . '>' . my_html($LG_at) . '</th>' . "\n";
            echo '</tr></thead>' . "\n";
        }

        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $Ref = $row['Reference'];
            $Sexe = $row['Sexe'];

            if (!$CSV) {
                if (!$texte) {
                    if (pair($num_lig++)) $style = 'class="liste"';
                    else                  $style = 'class="liste2"';
                } else {
                    if (pair($num_lig++)) $style = '';
                    else                  $style = 'class="even"';
                }
                echo '<tr ' . $style . '>' . "\n";
            } else {
                $ligne = '';
            }

            // Prénom de la personne
            if (!$CSV) {
                echo '<td>';
                if (!$texte) {
                    echo '&nbsp;<a ' . Ins_Ref_Pers($Ref) . '>' . my_html($row['Prenoms']) . '</a>' . "\n";
                    echo '&nbsp;<a ' . Ins_Edt_Pers($Ref) . '>' . $echo_modif;
                    echo '&nbsp;<a href="Verif_Personne.php?Refer=' . $Ref . '">' . $echo_verif;
                } else echo my_html($row['Prenoms']) . "\n";
                echo '</td>';
            } else {
                $ligne .= $Ref . ';' . $row['Prenoms'] . ';';
            }

            // Date de naissance
            $resu = '';
            $dateN = $row['Ne_le'];
            if (($dateN == '') or (strlen($dateN) != 10)) $resu = $img_rouge;
            else {
                if ($dateN[9] == 'L') $resu = $img_vert;
                else $resu = $img_orange;
            }
            if (!$CSV) echo '<td align="center">' . $resu . '</td>';
            else $ligne .= $resu . ';';

            // Lieu de naissance
            $resu = '';
            if ($row['Ville_Naissance'] == 0) $resu = $img_rouge;
            else $resu = $img_vert;
            if (!$CSV) echo '<td align="center">' . $resu . '</td>';
            else $ligne .= $resu . ';';

            // Date de décès
            $resu = '';
            $dateD = $row['Decede_Le'];
            $lgDate = strlen($dateD);
            $vivant = determine_etat_vivant($dateN, $dateD);
            if ($lgDate == 10) {
                if ($dateD[9] == 'L') $resu = $img_vert;
                else $resu = $img_orange;
            } else {
                if ($lgDate == 0) {
                    if (!$vivant) $resu = $img_rouge;
                    else $resu = $img_vert;
                } else $resu = $img_rouge;
            }
            if (!$CSV) echo '<td align="center">' . $resu . '</td>';
            else $ligne .= $resu . ';';

            // Lieu de décès
            $resu = '';
            if ((!$vivant) and ($row['Ville_Deces'] == 0)) $resu = $img_rouge;
            else $resu = $img_vert;
            if (!$CSV) echo '<td align="center">' . $resu . '</td>';
            else $ligne .= $resu . ';';

            // Présence des parents
            $x = Get_Parents($Ref, $Pere, $Mere, $Rang);
            $resu = '';
            if ($Pere) $resu = $img_vert;
            else $resu = $img_rouge;
            if (!$CSV) echo '<td align="center">' . $resu . '</td>';
            else $ligne .= $resu . ';';

            $resu = '';
            if ($Mere) $resu = $img_vert;
            else $resu = $img_rouge;
            if (!$CSV) echo '<td align="center">' . $resu . '</td>';
            else $ligne .= $resu . ';';

            // On suppose qu'une union ne peut pas intervenir avant 15 ans ; on ne contrôle donc pas dans ce cas là
            $trop_jeune = false;
            if (($dateN != '') and ($dateD != '')) {
                $age = Age_Mois($dateN, $dateD);
                if ($age <= (15 * 12)) $trop_jeune = true;
            }

            if (!$trop_jeune) {
                // Conjoint / Union
                $sql = 'select Maries_Le, Ville_Mariage from ' . $n_unions . ' where ';
                switch ($Sexe) {
                    case 'm':
                        $sql = $sql . 'Conjoint_1 = ' . $Ref;
                        break;
                    case 'f':
                        $sql = $sql . 'Conjoint_2 = ' . $Ref;
                        break;
                    default:
                        $sql = $sql . 'Conjoint_1 = ' . $Ref . ' or Conjoint_2 =' . $Ref;
                        break;
                }
                $sql .= ' limit 1';
                $union = false;
                if ($resU = lect_sql($sql)) {
                    if ($enrU = $resU->fetch(PDO::FETCH_NUM)) {
                        $union = true;
                    }
                }
                $resu = '';
                if (!$union) $resu = $img_rouge;
                else $resu = $img_vert;
                if (!$CSV) echo '<td align="center">' . $resu . '</td>';
                else $ligne .= $resu . ';';

                if ($union) {
                    // Date de l'union
                    $resu = '';
                    $date = $enrU[0];
                    if (($date == '') or (strlen($date) != 10)) $resu = $img_rouge;
                    else {
                        if ($date[9] == 'L') $resu = $img_vert;
                        else $resu = $img_orange;
                    }
                    if (!$CSV) echo '<td align="center">' . $resu . '</td>';
                    else $ligne .= $resu . ';';
                    // Lieu de l'union
                    $resu = '';
                    if ($enrU[1] == 0) $resu = $img_rouge;
                    else $resu = $img_vert;
                    if (!$CSV) echo '<td align="center">' . $resu . '</td>';
                    else $ligne .= $resu . ';';
                } else {
                    if (!$CSV) echo '<td align="center">' . $img_rouge . '</td><td align="center">' . $img_rouge . '</td>' . "\n";
                    else $ligne .= $img_rouge . ';' . $img_rouge . ';';
                }
            } else {
                if (!$CSV) echo '<td align="center">-</td><td align="center">-</td><td align="center">-</td>' . "\n";
                else $ligne .= ';;;';
            }

            if (!$CSV) echo '</tr>' . "\n";
            else ecrire($fp, $ligne);
        }
        if (!$CSV) {
            echo '</table>' . "\n";
            //echo '<br />'.Affiche_Icone('tip',$LG_tip).' L&eacute;gende :&nbsp;';
            echo '<br />' . $img_vert . ' : ' . my_html(LG_PERS_COMPLETE_GREEN)
                . ' ; ' . $img_orange . ' : ' . my_html(LG_PERS_COMPLETE_ORANGE)
                . ' ; ' . $img_rouge . ' : ' . my_html(LG_PERS_COMPLETE_RED)
                . " \n";
        } else {
            fclose($fp);
            echo '<br /><br />' . my_html($LG_csv_available_in) . ' <a href="' . $nom_fic . '" target="_blank">' . $nom_fic . '</a><br />' . "\n";
        }
    }
}

if (! $texte) {

    // Formulaire pour le bouton retour
    Bouton_Retour($lib_Retour, '?' . Query_Str());

    Insere_Bas($compl);
}
?>
</body>

</html>