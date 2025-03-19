<?php

//=====================================================================
// Constitution d'une liste de vérifications par commune pour visite aux archives
// Sortie possible :
//   - à l'écran avec les liens vers les personnes
//   - au format texte pour impression
//   - au format csv pour un import dans un tableur
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$tab_variables = array(
    'ok',
    'annuler',
    'reprise',
    'T_Donnees_N',
    'T_Donnees_M',
    'T_Donnees_D',
    'T_Fiches_O',
    'T_Fiches_N',
    'T_Fiches_I',
    'Ville',
    'Annee_Debut',
    'Annee_Fin',
    'Tri',
    'Sortie',
    'ut_suf',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok          = Secur_Variable_Post($ok, strlen($lib_Rechercher), 'S');
$annuler     = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine    = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_Rechercher) $ok = 'OK';

// Gestion standard des pages
$acces = 'L';                                        // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Archive_Preparation'];        // Titre pour META
$niv_requis = 'C';
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

$compl = '';

if ($_SESSION['estContributeur']) {

    if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);

    if ($Sortie == 't') {
        echo '</head>' . "\n";
        echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
        echo '<table cellpadding="0" width="100%">' . "\n";
        echo '<tr>' . "\n";
        echo '<td align="center"><b> </b></td>' . "\n";
        echo '</tr>' . "\n";
        echo '</table>' . "\n";
    } else {
        $compl = Ajoute_Page_Info(600, 250);
        Insere_Haut($titre, $compl, 'Recherche_Personne_Archive', '');
    }

    //Demande de recherche
    if ($bt_OK) {

        $reprise     = Secur_Variable_Post($reprise, 1, 'S'); // 1 seul caractère suffit
        $T_Donnees_N = Secur_Variable_Post($T_Donnees_N, 1, 'S');
        $T_Donnees_M = Secur_Variable_Post($T_Donnees_M, 1, 'S');
        $T_Donnees_D = Secur_Variable_Post($T_Donnees_D, 1, 'S');
        $T_Fiches_O  = Secur_Variable_Post($T_Fiches_O, 1, 'S');
        $T_Fiches_N  = Secur_Variable_Post($T_Fiches_N, 1, 'S');
        $T_Fiches_I  = Secur_Variable_Post($T_Fiches_I, 1, 'S');
        $Ville       = Secur_Variable_Post($Ville, 1, 'N');
        $Annee_Debut = Secur_Variable_Post($Annee_Debut, 4, 'S');
        $Annee_Fin   = Secur_Variable_Post($Annee_Fin, 4, 'S');
        $Tri         = Secur_Variable_Post($Tri, 1, 'S');
        $Sortie      = Secur_Variable_Post($Sortie, 1, 'S');
        $ut_suf      = Secur_Variable_Post($ut_suf, 2, 'S');

        $l_ville = lib_ville($Ville, 'N');
        echo $LG_Ch_Search_Town . ' : ' . $l_ville . '<br />';
        if ($Annee_Debut != '') echo $LG_Ch_Search_Beg . ' : ' . $Annee_Debut . '<br />';
        if ($Annee_Fin != '') echo $LG_Ch_Search_End . ' : ' . $Annee_Fin . '<br />';
        if ($Annee_Debut == '') $Annee_Debut = '0000';
        if ($Annee_Fin == '') $Annee_Fin = '9999';

        // Constitution de la requête d'extraction
        $n_pers = nom_table('personnes');
        $req = '';

        // Par défaut, si aucune case n'est cochée, toutes le sont...
        if (($T_Fiches_O != 'O') and ($T_Fiches_N != 'N') and ($T_Fiches_I != 'I')) {
            $T_Fiches_O = 'O';
            $T_Fiches_N = 'I';
            $T_Fiches_I = 'I';
        }
        // Vu que sur les sites gratuits non Premium, les mariages ne sont pas sélectionnés, on ne teste que naissances et décès
        if (($SiteGratuit) and (!$Premium)) {
            if (($T_Donnees_N != 'N') and ($T_Donnees_D != 'D')) {
                $T_Donnees_N = 'N';
                $T_Donnees_D = 'D';
            }
        } else {
            if (($T_Donnees_N != 'N') and ($T_Donnees_M != 'M') and ($T_Donnees_D != 'D')) {
                $T_Donnees_N = 'N';
                $T_Donnees_M = 'M';
                $T_Donnees_D = 'D';
            }
        }
        echo $LG_Ch_Search_Consider . ' : ';
        $aff_donnees = '';
        if ($T_Donnees_N == 'N') $aff_donnees .= $LG_Ch_Search_Birth;
        if ($T_Donnees_M == 'M') {
            if ($aff_donnees != '') $aff_donnees .= ', ';
            $aff_donnees .= $LG_Ch_Search_Wed;
        }
        if ($T_Donnees_D == 'D') {
            if ($aff_donnees != '') $aff_donnees .= ', ';
            $aff_donnees .= $LG_Ch_Search_Death;
        }
        echo $aff_donnees . '<br />';

        // Conditionnement du statut en fonction de la saisie
        // si aucun statut particulier n'est coché, tous les status sont pris, sinon aucune fiche ne serait ramenée...
        $cond_statut = '';
        if ($T_Fiches_O == 'O') $cond_statut = '"O"';
        if ($T_Fiches_N == 'N') {
            if ($cond_statut != '') $cond_statut .= ',';
            $cond_statut .= '"N"';
        }
        if ($T_Fiches_I == 'I') {
            if ($cond_statut != '') $cond_statut .= ',';
            $cond_statut .= '"I"';
        }
        $cond_statut = 'Statut_Fiche in(' . $cond_statut . ')';
        echo $LG_Ch_Search_Consider_Valid . ' : ';
        $aff_donnees = '';
        if ($T_Fiches_O == 'O') $aff_donnees .= $LG_Ch_Search_Valid;
        if ($T_Fiches_N == 'N') {
            if ($aff_donnees != '') $aff_donnees .= ', ';
            $aff_donnees .= $LG_Ch_Search_Non_Valid;
        }
        if ($T_Fiches_I == 'I') {
            if ($aff_donnees != '') $aff_donnees .= ', ';
            $aff_donnees .= $LG_Ch_Search_Internet;
        }
        echo $aff_donnees . '<br />';

        // Si tous les statuts sont demandés, on va optimiser la requête
        $all_statuts = false;
        if (($T_Fiches_O == 'O') and ($T_Fiches_N == 'N') and ($T_Fiches_I == 'I')) $all_statuts = true;
        // Idem sur les dates
        $all_dates = false;
        if (($Annee_Debut == '0000') and ($Annee_Fin == '9999')) $all_dates = true;

        if ($T_Donnees_N == 'N') {
            $req = 'select Reference, Nom, Prenoms, Ne_Le as Ladate, "N" from ' . $n_pers .
                ' where Ville_Naissance = ' . $Ville;
            if (!$all_dates) $req .= ' and substr(Ne_Le,1,4) >= \'' . $Annee_Debut . '\' and substr(Ne_Le,1,4) <= \'' . $Annee_Fin . '\'';
            if (!$all_statuts) $req .= ' and ' . $cond_statut;
        }
        if ($T_Donnees_M == 'M') {
            $n_unions = nom_table('unions');
            if ($req != '') $req .= ' union ';
            $req .= 'select p.Reference, Nom, Prenoms, Maries_Le as Ladate, "M" from ' . $n_pers . ' p, ' . $n_unions . ' u ' .
                ' where p.Reference = u.Conjoint_1 and Ville_Mariage = ' . $Ville;
            if (!$all_dates) $req .= ' and substr(Maries_Le,1,4) >= \'' . $Annee_Debut . '\' and substr(Maries_Le,1,4) <= \'' . $Annee_Fin . '\'';
            if (!$all_statuts) $req .= ' and u.' . $cond_statut;
            $req .= ' union ' .
                'select p.Reference, Nom, Prenoms, Maries_Le as Ladate, "M" from ' . $n_pers . ' p, ' . $n_unions . ' u ' .
                ' where p.Reference = u.Conjoint_2 and Ville_Mariage = ' . $Ville;
            if (!$all_dates) $req .= ' and substr(Maries_Le,1,4) >= \'' . $Annee_Debut . '\' and substr(Maries_Le,1,4) <= \'' . $Annee_Fin . '\'';
            if (!$all_statuts) $req .= ' and u.' . $cond_statut;
        }
        if ($T_Donnees_D == 'D') {
            if ($req != '') $req .= ' union ';
            $req .= 'select Reference, Nom, Prenoms, Decede_Le as Ladate, "D" from ' . $n_pers .
                ' where Ville_Deces = ' . $Ville;
            if (!$all_dates) $req .= ' and substr(Decede_Le,1,4) >= \'' . $Annee_Debut . '\' and substr(Decede_Le,1,4) <= \'' . $Annee_Fin . '\'';
            if (!$all_statuts) $req .= ' and ' . $cond_statut;
        }
        if ($Tri == 'D') $req .= ' order by LaDate, Nom, Prenoms';
        else $req .= ' order by Nom, Prenoms, LaDate';

        /*
		Exemple de requête avec les naissances, mariages et décès
		'select Reference, Nom, Prenoms, Ne_Le as Ladate, "N" from personnes where Ville_Naissance = 383 and Statut_Fiche in("N") 
		union 
		select p.Reference, Nom, Prenoms, Maries_Le as Ladate, "M" from personnes p, unions u  where p.Reference = u.Conjoint_1 and Ville_Mariage = 383 and u.Statut_Fiche in("N") 
		union 
		select p.Reference, Nom, Prenoms, Maries_Le as Ladate, "M" from personnes p, unions u  where p.Reference = u.Conjoint_2 and Ville_Mariage = 383 and u.Statut_Fiche in("N") 
		union 
		select Reference, Nom, Prenoms, Decede_Le as Ladate, "D" from personnes where Ville_Deces = 383 and Statut_Fiche in("N") 
		order by LaDate, Nom, Prenoms'
		*/

        $res = lect_sql($req);
        $nb_enr = $res->RowCount();
        $plu = pluriel($nb_enr);
        echo $nb_enr . ' ' . $LG_Ch_Search_Pers_1 . $plu . ' ' . $LG_Ch_Search_Pers_2 . $plu . '<br /><br />';

        if ($nb_enr > 0) {
            $champs = get_fields($req, true);
            $nb_champs = count($champs);
            if ($Sortie == 'c') {
                // Traiter le cas d'erreur sur l'ouverture du fichier
                // Ajout du suffixe ville si demandé
                $nom_fic = 'recherche_archives';
                if ($ut_suf == 'on') $nom_fic = $nom_fic . '_' . $l_ville;
                $nom_fic = construit_fic($chemin_exports, $nom_fic . '#', 'csv');
                $fp = fopen($nom_fic, 'w+');
                $ligne = '';
                for ($nb = 0; $nb < $nb_champs - 1; $nb++) {
                    $nom_champ = $champs[$nb];
                    if (strpos($nom_champ, 'Ladate') !== false) {
                        $ajout = '';
                        if ($T_Donnees_N == 'N') $ajout = 'Naissance';
                        if ($T_Donnees_M == 'M') {
                            if ($ajout != '') $ajout .= '/';
                            $ajout .= 'Mariage';
                        }
                        if ($T_Donnees_D == 'D') {
                            if ($ajout != '') $ajout .= '/';
                            $ajout .= 'Décès';
                        }
                        $c_date = $nb;
                        $nom_champ = 'Date;Precision;Calendrier;' . $ajout;
                    }
                    $ligne .= $nom_champ . ';';
                }
                fputs($fp, $ligne);
            } else {
                echo '<table>';
            }
        }

        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            if ($Sortie != 'c') echo '<tr><td>';
            switch ($Sortie) {
                case 'e':
                    echo '<a href="' . $root . '/fiche_fam_pers.php?Refer=' . $row[0] . '">' . my_html($row[2] . ' ' . $row[1]) . '</a>';
                    echo ' <a href="' . $root . '/edition_personne.php?Refer=' . $row[0] . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
                    break;
                case 't':
                    echo my_html($row[2] . ' ' . $row[1]);
                    break;
                case 'c':
                    $ligne = '';
                    for ($nb = 0; $nb < $nb_champs; $nb++) {
                        $contenu = $row[$nb];
                        // On retravaille le champ naissance / décès
                        if ($nb == $c_date) {
                            if (strlen($contenu) == 10) {
                                $contenu = substr($contenu, 6, 2) . '/' . substr($contenu, 4, 2) . '/' . substr($contenu, 0, 4) . '";"' .
                                    substr($contenu, 9, 1) . '";"' . substr($contenu, 8, 1);
                            } else $contenu = $contenu . '";"";"';
                        }
                        $ligne .= '"' . $contenu . '";';
                    }
                    //echo $ligne.'<br />';
                    fputs($fp, $ligne);
                    break;
            }
            if ($Sortie != 'c') {
                echo '</td>';
                echo '<td>';
                switch ($row[4]) {
                    case 'N':
                        echo  '&deg;';
                        break;
                    case 'M':
                        echo  'x';
                        break;
                    case 'D':
                        echo  '+';
                        break;
                }
                echo ' ' . Etend_date($row[3]);
                echo '</td></tr>' . "\n";
            }
        }
        if ($nb_enr > 0) {
            if ($Sortie != 'c') {
                echo '</table>' . "\n";
            } else {
                fclose($fp);
                echo '<br />' . $LG_csv_available_in . '  <a href="' . $nom_fic . '">' . $nom_fic . '</a><br />' . "\n";
            }
        }

        if ($Sortie != 't') {
            // Nouvelle recherche
            echo '<form id="nouvelle" method="post" action="' . my_self() . '">' . "\n";
            echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
            echo '<input type="hidden" name="reprise" value=""/>';
            echo '<input type="hidden" name="Ville" value="' . $Ville . '"/>';
            if ($Annee_Debut == '0000') $Annee_Debut = '';
            if ($Annee_Fin == '9999') $Annee_Fin = '';
            echo '<input type="hidden" name="Annee_Debut" value="' . $Annee_Debut . '"/>';
            echo '<input type="hidden" name="Annee_Fin" value="' . $Annee_Fin . '"/>';
            echo '<input type="hidden" name="T_Donnees_N" value="' . $T_Donnees_N . '"/>';
            echo '<input type="hidden" name="T_Donnees_M" value="' . $T_Donnees_M . '"/>';
            echo '<input type="hidden" name="T_Donnees_D" value="' . $T_Donnees_D . '"/>';
            echo '<input type="hidden" name="T_Fiches_O" value="' . $T_Fiches_O . '"/>';
            echo '<input type="hidden" name="T_Fiches_N" value="' . $T_Fiches_N . '"/>';
            echo '<input type="hidden" name="T_Fiches_I" value="' . $T_Fiches_I . '"/>';
            echo '<input type="hidden" name="Tri" value="' . $Tri . '"/>';
            echo '<input type="hidden" name="Sortie" value="' . $Sortie . '"/>';
            echo '<input type="hidden" name="ut_suf" value="' . $ut_suf . '"/>';
            echo '<br />';
            echo '<div class="buttons">';
            echo '<button type="submit" class="positive"><img src="' . $root . '/assets/img/' . $Icones['chercher'] . '" alt=""/>' . $lib_Nouv_Rech . '</button>';
            if ((!$SiteGratuit) or ($Premium)) {
                echo '<button type="submit" onclick="document.forms.nouvelle.reprise.value=\'reprise\'; "' .
                    ' class="positive"><img src="' . $root . '/assets/img/' . $Icones['chercher_plus'] . '" alt=""/>' . $lib_Nouv_Rech_Aff . '</button>';
            }
            echo '</div>';
            echo '</form>' . "\n";
        }
    }

    // Première entrée : affichage pour saisie
    if ((!$bt_OK) && (!$bt_An)) {

        $existe_ville = false;
        $sql = 'select Identifiant_zone, Nom_Ville from ' . nom_table('villes') .
            ' where Identifiant_Zone <> 0' .
            ' order by Nom_Ville';
        $res = lect_sql($sql);

        echo '<form id="saisie" method="post">' . "\n";
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        echo '<table width="80%" class="table_form">' . "\n";
        echo '<tr><td colspan="2"> </td></tr>';
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Ch_Search_Town) . '</td><td class="value">';
        echo '<select name="Ville">' . "\n";
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $ligne = $row[0];
            echo '<option value="' . $ligne . '"';
            if ($reprise) {
                if ($ligne == $Ville) echo ' selected';
            }
            echo '>' . my_html($row[1]);
            echo '</option>' . "\n";
            $existe_ville = true;
        }
        echo '</select>' . "\n";
        echo '</td></tr>' . "\n";

        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Ch_Search_Beg) . '</td><td class="value">';
        echo '<input type="text" size="4" name="Annee_Debut"';
        if ($reprise) echo ' value="' . $Annee_Debut . '"';
        echo '/></td></tr>' . "\n";

        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Ch_Search_End) . '</td><td class="value">';
        echo '<input type="text" size="4" name="Annee_Fin"';
        if ($reprise) echo ' value="' . $Annee_Fin . '"';
        echo '/>';
        echo  '<img src="' . $root . '/assets/img/' . $Icones['calendrier'] . '" alt="' . $LG_Ch_Search_Copy_Date . '" title="' . $LG_Ch_Search_Copy_Date . '" onclick="document.forms.saisie.Annee_Fin.value=document.forms.saisie.Annee_Debut.value;">';
        echo '</td></tr>' . "\n";

        // Type de données : Naissance, mariages, décès
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Ch_Search_Consider) . '</td><td class="value">';
        echo '<input type="checkbox" id="T_Donnees_N" name="T_Donnees_N" value="N"';
        if ($reprise) {
            if ($T_Donnees_N == 'N') echo ' checked';
        } else echo ' checked';
        echo '/> <label for="T_Donnees_N">' . $LG_Ch_Search_Birth . '</label> ';
        // Les hébergés non Premium ne peuvent avoir les mariages
        if ((!$SiteGratuit) or ($Premium)) {
            echo '<input type="checkbox" id="T_Donnees_M" name="T_Donnees_M" value="M"';
            if ($reprise) {
                if ($T_Donnees_M == 'M') echo ' checked';
            }
            echo '/> <label for="T_Donnees_M">' . $LG_Ch_Search_Wed . '</label> ';
        } else echo '<input type="hidden" name="T_Donnees_M" value="-"/>';
        echo '<input type="checkbox"  id="T_Donnees_D" name="T_Donnees_D" value="D"';
        if ($reprise) {
            if ($T_Donnees_D == 'D') echo ' checked';
        } else echo ' checked';
        echo '/> <label for="T_Donnees_D">' . $LG_Ch_Search_Death . '</label>';
        echo '</td></tr>' . "\n";

        // Type de fiche
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Ch_Search_Consider_Valid) . '</td><td class="value">';
        echo '<input type="checkbox" id="T_Fiches_O" name="T_Fiches_O" value="O"';
        if ($reprise) {
            if ($T_Fiches_O == 'O') echo ' checked';
        }
        echo '/> <label for="T_Fiches_O">' . $LG_Ch_Search_Valid . '</label> ';
        echo '<input type="checkbox" id="T_Fiches_N" name="T_Fiches_N" value="N"';
        if ($reprise) {
            if ($T_Fiches_N == 'N') echo ' checked';
        } else echo ' checked';
        echo '/> <label for="T_Fiches_N">' . $LG_Ch_Search_Non_Valid . '</label> ';
        echo '<input type="checkbox" id="T_Fiches_I" name="T_Fiches_I" value="I"';
        if ($reprise) {
            if ($T_Fiches_I == 'I') echo ' checked';
        }
        echo '/> <label for="T_Fiches_I">' . $LG_Ch_Search_Internet . '</label>';
        echo '</td></tr>' . "\n";

        // Tri des données en sortie
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Ch_Search_Sort) . '</td><td class="value">';
        echo '<input type="radio" id="Tri_D" name="Tri" value="D"';
        if ($reprise) {
            if ($Tri == 'D') echo ' checked';
        } else echo ' checked';
        echo '/> <label for="Tri_D">' . $LG_Ch_Search_Sort_Date . '</label>';
        echo '<input type="radio" id="Tri_P" name="Tri" value="P"';
        if ($reprise) {
            if ($Tri == 'P') echo ' checked';
        }
        echo '/> <label for="Tri_P">' . $LG_Ch_Search_Sort_Pers . '</label>';
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Ch_Output_Format) . '</td><td class="value">';
        echo '<input type="radio" id="Sortie_e" name="Sortie" value="e" ';
        if (($Sortie == '') or (($Sortie == 'e')))
            echo 'checked';
        echo '/> <label for="Sortie_e">' . $LG_Ch_Output_Screen . ' </label>';
        echo '<input type="radio" id="Sortie_t" name="Sortie" value="t"';
        if ($Sortie == 't') echo ' checked';
        echo '/> <label for="Sortie_t">' . $LG_Ch_Output_Text . ' </label>';
        echo '<input type="radio" id="Sortie_c" name="Sortie" value="c"';
        if ($Sortie == 'c') echo ' checked';
        echo '/> <label for="Sortie_c">' . $LG_Ch_Output_CSV . '</label>';
        echo ' ( <input type="checkbox" id="ut_suf" name="ut_suf" onclick="document.forms.saisie.Sortie[2].checked=true;"/> <label for="ut_suf">' . $LG_Ch_Search_Suffix . '</label> )' . "\n";
        echo '</td></tr>' . "\n";

        echo '<tr><td colspan="2"> </td></tr>';
        // Pas de bouton OK s'il n'y a pas de ville
        if (!$existe_ville) $lib_Rechercher = '';
        bt_ok_an_sup($lib_Rechercher, $lib_Annuler, '', '');
        echo '<tr><td colspan="2"> </td></tr>';
        echo '</table>' . "\n";
        echo '</form>' . "\n";
    }

    if ($Sortie != 't') {
        echo '<table cellpadding="0" width="100%">';
        echo '<tr>';
        echo '<td align="right">';
        echo $compl;
        echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
        echo "</td>";
        echo '</tr>';
        echo '</table>';
    }
} else {
    echo $LG_function_noavailable_profile . "\n";
}
?>
</body>

</html>