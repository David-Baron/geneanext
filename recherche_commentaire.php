<?php
//=====================================================================
// Recherche dans les commentaires, quel que soit l'objet associé
// Sortie possible :
//   - à l'écran avec les liens vers les personnes
//   - au format texte pour impression
//   - au format csv pour un import dans un tableur
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'Divers',
    'Sortie',
    'Restriction',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_Rechercher), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_Rechercher) $ok = 'OK';

// Gestion standard des pages
$acces = 'L';                                    // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Search_Comment'];        // Titre pour META
$niv_requis = 'C';
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Divers   = Secur_Variable_Post($Divers, 65535, 'S');
$Sortie   = Secur_Variable_Post($Sortie, 1, 'S');

$compl = '';

if ($est_gestionnaire) {

    if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);

    if ($Sortie == 't') {
        Insere_Haut_texte('');
    } else {
        $compl = Ajoute_Page_Info(600, 310);
        Insere_Haut($titre, $compl, 'Recherche_Commentaire', '');
    }

    //Demande de recherche
    if ($bt_OK) {

        require(__DIR__ . '/app/ressources/commun_rech_com_util_docs.php');

        if ($Sortie == 'c') {
            $nom_fic = $chemin_exports . 'recherche_commentaires.csv';
            $fp = ouvre_fic($nom_fic, 'w+');
        }

        if ($Restriction != '-')
            echo LG_SCH_COMMENT_RESTRICTION . '&nbsp;:&nbsp;' . lib_pfu($Restriction) . '<br />';
        echo LG_SCH_COMMENT . '&nbsp;:<br />';
        $Divers = str_replace('<p>', '', $Divers);
        $Divers = str_replace('</p>', '', $Divers);

        echo html_entity_decode($Divers, ENT_QUOTES, $def_enc) . '<br />';

        $sql = 'select * from ' . nom_table('commentaires') .
            ' where upper(Note) like \'%' . trim(strtoupper(html_entity_decode($Divers, ENT_QUOTES, $def_enc))) . '%\'';
        if ($Restriction != '-')
            $sql .= ' and Type_Objet = "' . $Restriction . '"';
        $sql .= ' order by Type_Objet';

        $res = lect_sql($sql);
        $nb = $res->RowCount();
        //$plu = pluriel($nb);
        //echo $nb.'&nbsp;'.$LG_SCH_COMMENT_FOUND_1.$plu.'&nbsp;'.$LG_SCH_COMMENT_FOUND_2.$plu.'<br /><br />';
        echo $nb . '&nbsp;' . LG_SCH_COMMENT_FOUND_1 . '&nbsp;' . LG_SCH_COMMENT_FOUND_2 . '<br /><br />';
        //$num_fields = $res->field_count;

        $echo_modif = Affiche_Icone('fiche_edition', $LG_modify) . '</a>';
        $num_lig = 0;
        $base_ref = Get_Adr_Base_Ref();

        $w_on = my_html(LG_SCH_COMMENT_ON);
        $w_of = my_html(LG_SCH_COMMENT_OF);
        $w_and = my_html(LG_SCH_COMMENT_AND);

        while ($row = $res->fetch(PDO::FETCH_NUM)) {

            if ($num_lig == 0) echo '<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" >';

            $Objet_Cible = $row[2];
            $Ref_Objet = $row[1];
            $cible = lib_pfu($Objet_Cible, true);

            acces_donnees($Objet_Cible, $Ref_Objet);

            if ($Sortie == 'e') {
                if (pair($num_lig++)) $style = 'liste';
                else                $style = 'liste2';
                echo '<tr class="' . $style . '"><td width="30%">';
            }
            if ($Sortie == 't') echo '<tr><td>';

            switch ($Sortie) {
                case 'e':
                    affiche_donnees($Objet_Cible, $Ref_Objet, 'C');
                    break;
                case 't':
                case 'c':
                    switch ($Objet_Cible) {
                        case 'P':
                            $tmp = $w_on . $Prenoms . ' ' . $Nom;
                            break;
                        case 'U':
                            $tmp = $w_on . $cible . $w_of . $Prenoms_M . ' ' . $Nom_M . $w_and . $Prenoms_F . ' ' . $Nom_F;
                            break;
                        case 'F':
                            $tmp = $w_on . $cible . $w_of . $Prenoms . ' ' . $Nom;
                            break;
                        case 'E':
                            $tmp = $w_on . $cible . $w_of . $Titre;
                            break;
                        case 'V':
                            $tmp = $w_on . $cible . $w_of . $Nom_Zone;
                            break;
                        case 's':
                            $tmp = $w_on . $cible . $w_of . $Nom_Zone;
                            break;
                        case 'D':
                            $tmp = $w_on . $cible . $w_of . $Nom_Zone;
                            break;
                        case 'R':
                            $tmp = $w_on . $cible . $w_of . $Nom_Zone;
                            break;
                        case 'I':
                            $tmp = $w_on . $cible . ' ' . $Titre;
                            break;
                        case 'O':
                            $tmp = $w_on . $cible . ' ' . $Titre;
                            break;
                        case 'L':
                            $tmp = $w_on . $cible . ' ' . $Titre;
                            break;
                        case 'S':
                            $tmp = $w_on . $cible . ' ' . $Titre;
                            break;
                        default:
                            $tmp = '';
                            break;
                    }
                    if ($Sortie == 't') echo $tmp;
                    else ecrire($fp, '"' . $tmp . '";"' . $row[3] . '";');
                    $num_lig = 1;
                    break;
            }
            if ($Sortie != 'c') {
                echo '</td>';
                echo '<td>' . $row[3] . '</td>';
                echo '</tr>' . "\n";
            }
        }
        if ($num_lig != 0) {
            if ($Sortie == 'c') {
                fclose($fp);
                echo '<br />' . $LG_csv_available_in . '&nbsp; <a href="' . $nom_fic . '">' . $nom_fic . '</a><br />' . "\n";
            } else {
                echo '</table>' . "\n";
            }
        }
        // Nouvelle recherche
        if ($Sortie != 't') {
            echo '<form id="nouvelle" method="post" action="' . my_self() . '">' . "\n";
            aff_origine();
            echo '<br />';
            echo '<div class="buttons">';
            echo '<button type="submit" class="positive">' .
                '<img src="' . $chemin_images_icones . $Icones['chercher'] . '" alt=""/>Nouvelle recherche</button>';
            echo '</div>';
            echo '</form>' . "\n";
        }
    }

    // Première entrée : affichage pour saisie
    if ((!$bt_OK) && (!$bt_An)) {

        echo '<form id="saisie" method="post" action="' . my_self() . '">' . "\n";
        aff_origine();

        $larg_titre = 30;
        echo '<table width="80%" class="table_form">' . "\n";
        ligne_vide_tab_form(1);

        col_titre_tab(LG_SCH_COMMENT_CONTAINING, $larg_titre);
        echo '<td class="value"><textarea cols="50" rows="4" name="Divers"></textarea></td>' . "\n";
        echo '</tr>' . "\n";

        colonne_titre_tab(LG_SCH_COMMENT_RESTRICTION);
        echo '<select name="Restriction" size="1">';
        echo '<option value="-">' . LG_SCH_COMMENT_NO_RESTRICTION . '</option>' . "\n";
        $liste_objets = 'PUFEVsDROLSI';
        $l_liste_objets = strlen($liste_objets);
        for ($nb = 0; $nb < $l_liste_objets; $nb++) {
            $car = $liste_objets[$nb];
            echo '<option value="' . $car . '">' . ucfirst(lib_pfu($car)) . '</option>' . "\n";
        }
        echo '</select>' . "\n";
        echo '</td></tr>' . "\n";

        colonne_titre_tab($LG_Ch_Output_Format);
        affiche_sortie(true);
        echo '</td></tr>' . "\n";

        ligne_vide_tab_form(1);
        bt_ok_an_sup($lib_Rechercher, $lib_Annuler, '', '');
        ligne_vide_tab_form(1);

        echo '</table>' . "\n";
        echo '</form>' . "\n";

        include(__DIR__ . '/assets/js/Insert_Tiny.js');
    }

    if ($Sortie != 't') Insere_Bas($compl);
} else echo my_html($LG_function_noavailable_profile) . "\n";
?>
</body>

</html>