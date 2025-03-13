<?php

//=====================================================================
// Fonction de recherche sur les villes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'reprise',
    'NomV',
    'Code_Postal',
    'Departement',
    'Statut_Fiche',
    'New_Window',
    'Sortie',
    'Horigine'
);
foreach ($tab_variables as $NomV_variables) {
    if (isset($_POST[$NomV_variables])) $$NomV_variables = $_POST[$NomV_variables];
    else $$NomV_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_Rechercher), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_Rechercher) $ok = 'OK';

// Gestion standard des pages
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Town_Search_Title'];     // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Suite sécurisation des variables postées
$reprise      = Secur_Variable_Post($reprise, 1, 'S'); // 1 seul caractère suffit
$NomV         = Secur_Variable_Post($NomV, 80, 'S');
$Code_Postal  = Secur_Variable_Post($Code_Postal, 10, 'S');
$Departement  = Secur_Variable_Post($Departement, 1, 'N');
$Statut_Fiche = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$Sortie       = Secur_Variable_Post($Sortie, 1, 'S');
$New_Window   = Secur_Variable_Post($New_Window, 1, 'S');


function Ajb_Zone_Req($NomRub, $Rub, $TypRub, &$LaReq, $Zone)
{
    global $memo_criteres, $separ;
    if ($Rub != '') {
        $C_Rub = $Rub;
        if ($NomRub == 'Zone_Mere')
            $C_Rub = lib_departement($Rub);
        $le_crit = $C_Rub;
        echo '   ' . $Zone . ' = ' . $le_crit . '<br>';
        $memo_criteres = $memo_criteres . $Zone . ' = ' . $C_Rub . $separ;
        if ($LaReq != '') $LaReq = $LaReq . ' and ';
        if ($TypRub == 'A') {
            // Recherche de type like ou = ?
            if (strpos($Rub, '*') === false) {
                $oper = '=';
            } else {
                $oper = ' like ';
                $Rub = str_replace('*', '%', $Rub);
            }
            $LaReq = $LaReq . ' upper(v.' . $NomRub . ')' . $oper;
            $LaReq = $LaReq . '"' . strtoupper($Rub) . '"';
        } else {
            $LaReq = $LaReq . ' v.' . $NomRub . '=' . $Rub;
        }
    }
}

$compl = Ajoute_Page_Info(650, 300);

if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);

if ($Sortie != 't') Insere_Haut($titre, $compl, 'Recherche_Ville', '');
else                Insere_Haut_texte('');

//Demande de recherche
if ($bt_OK) {

    $erreur = 0;

    if ($Sortie == 'c') {
        // Traiter le cas d'erreur sur l'ouverture du fichier
        $NomV_fic = $chemin_exports . 'recherche_villes.csv';
        $fp = ouvre_fic($NomV_fic, 'w+');
    }
    //Init des zones de requête
    echo 'Critères demandés :<br>';
    $req = '';
    $memo_criteres = '';
    // Constitution de la requête d'extraction
    Ajb_Zone_Req('Nom_Ville', $NomV, 'A', $req, LG_TOWN_SCH_NAME);
    Ajb_Zone_Req('Code_Postal', $Code_Postal, 'A', $req, LG_TOWN_SCH_ZIP);
    if ($Departement > -1)
        Ajb_Zone_Req('Zone_Mere', $Departement, 'N', $req, LG_COUNTY);
    Ajb_Zone_Req('Statut_Fiche', $Statut_Fiche, 'A', $req, LG_TOWN_SCH_STATUS);

    // Exéution de la requête
    if ($req != '') {

        // Constitution de la partie champs à récupérer
        // Pour les sorties csv, on va récupérer tous les champs alors que sur les autres sorties, la référence, le nom et le prénom suffisent
        if ($Sortie == 'c') {
            $req2 = 'select v.Identifiant_zone, v.Nom_Ville, v.Code_Postal, d.Nom_Depart_Min, v.Date_Creation, v.Date_Modification, v.Statut_Fiche, ' .
                'v.Zone_Mere, v.Latitude, v.Longitude' .
                ' from ' . nom_table('villes') . ' v, ' .
                nom_table('departements') . ' d ' .
                'where v.Identifiant_zone <> 0 ' .
                ' and v.zone_mere = d.Identifiant_zone ';
        } else {
            $req2 = 'select Identifiant_zone, Nom_Ville, Latitude, Longitude from ' . nom_table('villes') . ' v where Identifiant_zone  <> 0';
        }

        $req = $req2 . ' and ' . $req . ' order by Nom_Ville';

        $res = lect_sql($req);
        $nb_lignes = $res->RowCount();
        // $plu = pluriel($nb_lignes);
        // echo $nb_lignes.' ville'.$plu.' trouvée'.$plu.'<br><br>';
        echo $nb_lignes . ' ' . my_html(LG_TOWN_FOUND) . '<br><br>';
        $champs = get_fields($req, true);
        $num_fields = count($champs);
        if ($Sortie == 'c') {
            $ligne = '';
            for ($nb = 0; $nb < $num_fields; $nb++) {
                $NomV_champ = $champs[$nb];
                $ligne .= $NomV_champ . ';';
            }
            ecrire($fp, $ligne);
        }

        $target = '';
        if ($New_Window) $target = ' target="_blank"';
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $ref = $row[0];
            switch ($Sortie) {
                case 'e':
                    echo '<a href="' . $root . '/fiche_ville.php?Ident=' . $ref . '"' . $target . '>' . my_html($row[1]) . '</a>';
                    $Lat_V = $row[2];
                    $Long_V = $row[3];
                    appelle_carte_osm();
                    if ($est_gestionnaire) {
                        echo ' <a href="' . $root . '/edition_ville.php?Ident=' . $ref . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
                    }
                    echo '<br>' . "\n";
                    break;
                case 't':
                    echo my_html($row[1]);
                    echo '<br>' . "\n";
                    break;
                case 'c':
                    $ligne = '';
                    for ($nb = 0; $nb < $num_fields; $nb++) {
                        $contenu = $row[$nb];
                        $ligne .= '"' . $contenu . '";';
                    }
                    ecrire($fp, $ligne);
                    break;
            }
        }
        if ($Sortie == 'c') {
            fclose($fp);
            echo '<br>' . my_html($LG_csv_available_in) . ' <a href="' . $NomV_fic . '">' . $NomV_fic . '</a><br>' . "\n";
        }
    }

    if ($Sortie != 't') {
        // Nouvelle recherche
        echo '<form id="nouvelle" method="post">';
        aff_origine();
        echo '<input type="hidden" name="reprise" value=""/>';
        echo '<input type="hidden" name="NomV" value="' . $NomV . '"/>';
        echo '<input type="hidden" name="Code_Postal" value="' . $Code_Postal . '"/>';
        echo '<input type="hidden" name="Departement" value="' . $Departement . '"/>';
        echo '<input type="hidden" name="Statut_Fiche" value="' . $Statut_Fiche . '"/>';
        echo '<input type="hidden" name="New_Window" value="' . $New_Window . '"/>';
        echo '<br>';
        echo '<div class="buttons">';
        echo '<button type="submit" class="positive"><img src="' . $chemin_images_icones . $Icones['chercher'] . '" alt=""/>' . $lib_Nouv_Rech . '</button>';
        if ((!$SiteGratuit) or ($Premium)) {
            echo '<button type="submit" onclick="document.forms.nouvelle.reprise.value=\'reprise\'; "' .
                ' class="positive"><img src="' . $chemin_images_icones . $Icones['chercher_plus'] . '" alt=""/>' . $lib_Nouv_Rech_Aff . '</button>';
        }
        echo '</div>';
        echo '</form>' . "\n";
    }
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    echo '<br>';

    $larg_titre = '20';
    $checked = ' checked="checked"';

    $sql = 'select Identifiant_zone, Nom_Depart_Min from ' . nom_table('departements') . ' order by Nom_Depart_Min';
    $res = lect_sql($sql);

    echo '<form id="saisie" method="post" action="' . my_self() . '">' . "\n";
    aff_origine();

    echo '<table width="90%" class="table_form">' . "\n";

    col_titre_tab(LG_TOWN_SCH_NAME, $larg_titre);
    echo '<td class="value"><input type="text" size="80" name="NomV"';
    if ($reprise) echo ' value="' . $NomV . '"';
    echo '/>';
    echo '</td></tr>' . "\n";

    col_titre_tab(LG_TOWN_SCH_ZIP, $larg_titre);
    echo '<td class="value"><input type="text" name="Code_Postal"';
    if ($reprise) echo ' value="' . $Code_Postal . '"';
    echo '/>';
    echo '</td></tr>' . "\n";

    col_titre_tab(LG_COUNTY, $larg_titre);
    echo '<td class="value"><select name="Departement">' . "\n";
    echo '<option value="-1"/>';
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($reprise) {
            if ($Departement == $row[0]) echo 'selected="selected"';
        }
        echo '>';
        if ($row[0] == 0)
            echo 'Non saisi';
        else
            echo my_html($row[1]) . "\n";
        echo '</option>';
    }
    echo '</select>' . "\n";
    echo '</td></tr>' . "\n";

    if ($est_gestionnaire) {
        col_titre_tab(LG_TOWN_SCH_STATUS, $larg_titre);
        echo '<td class="value">' . "\n";
        echo '<input type="radio" id="Statut_Fiche_o" name="Statut_Fiche" value="O"';
        if ($reprise) {
            if ($Statut_Fiche == 'O') echo $checked;
        }
        echo '/><label for="Statut_Fiche_o">' . LG_CHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="Statut_Fiche_n" name="Statut_Fiche" value="N"';
        if ($reprise) {
            if ($Statut_Fiche == 'N') echo $checked;
        }
        echo '/><label for="Statut_Fiche_n">' . LG_NOCHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="Statut_Fiche_i" name="Statut_Fiche" value="I"';
        if ($reprise) {
            if ($Statut_Fiche == 'I') echo $checked;
        }
        echo '/><label for="Statut_Fiche_i">' . LG_FROM_INTERNET . '</label>';
        echo '</td></tr>' . "\n";
    }

    ligne_vide_tab_form(1);

    col_titre_tab($LG_Ch_Output_Format, $larg_titre);
    echo '<td class="value">';
    affiche_sortie(true);
    echo '</td></tr>' . "\n";

    col_titre_tab(LG_TOWN_NEW_TAB, $larg_titre);
    echo '<td class="value">';
    echo '<input type="checkbox" name="New_Window"';
    if ($reprise) {
        if ($New_Window == 'O') echo $checked;
    }
    echo ' value="O"/>';
    echo '</td></tr>' . "\n";

    ligne_vide_tab_form(1);
    bt_ok_an_sup($lib_Rechercher, $lib_Annuler, '', '');

    echo '</table>' . "\n";
    echo '</form>';
}

if ($Sortie != 't') Insere_Bas($compl);
?>
</body>

</html>