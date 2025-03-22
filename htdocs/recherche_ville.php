<?php

//=====================================================================
// Fonction de recherche sur les villes
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

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
require(__DIR__ . '/../app/ressources/gestion_pages.php');

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

if ($Sortie != 't') {
    Insere_Haut($titre, $compl, 'Recherche_Ville', '');
} else {
    echo '</head>' . "\n";
    echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td align="center"><b></b></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
}

//Demande de recherche
if ($bt_OK) {

    $erreur = 0;

    if ($Sortie == 'c') {
        // Traiter le cas d'erreur sur l'ouverture du fichier
        $NomV_fic = $chemin_exports . 'recherche_villes.csv';
        $fp = fopen($NomV_fic, 'w+');
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
            fputs($fp, $ligne);
        }

        $target = '';
        if ($New_Window) $target = ' target="_blank"';
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $ref = $row[0];
            switch ($Sortie) {
                case 'e':
                    echo '<a href="' . $root . '/fiche_ville?Ident=' . $ref . '"' . $target . '>' . my_html($row[1]) . '</a>';
                    $Lat_V = $row[2];
                    $Long_V = $row[3];
                    if (($Lat_V != 0) or ($Long_V != 0)) {
                        echo '<a href="http://www.openstreetmap.org/?lat=' . $Lat_V . '&amp;lon=' . $Long_V . '&amp;mlat=' . $Lat_V . '&amp;mlon=' . $Long_V . '&amp;zoom=10" target="_blank"><img src="' . $root . '/assets/img/' . $Icones['map_go'] . '" alt="' . $LG_Show_On_Map . '" title="' . $LG_Show_On_Map . '"></a>';
                    }
                    if (IS_GRANTED('G')) {
                        echo ' <a href="' . $root . '/edition_ville?Ident=' . $ref . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
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
                    fputs($fp, $ligne);
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
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        echo '<input type="hidden" name="reprise" value=""/>';
        echo '<input type="hidden" name="NomV" value="' . $NomV . '"/>';
        echo '<input type="hidden" name="Code_Postal" value="' . $Code_Postal . '"/>';
        echo '<input type="hidden" name="Departement" value="' . $Departement . '"/>';
        echo '<input type="hidden" name="Statut_Fiche" value="' . $Statut_Fiche . '"/>';
        echo '<input type="hidden" name="New_Window" value="' . $New_Window . '"/>';
        echo '<br>';
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

    $sql = 'select Identifiant_zone, Nom_Depart_Min from ' . nom_table('departements') . ' order by Nom_Depart_Min';
    $res = lect_sql($sql);

    echo '<br>';
    echo '<form id="saisie" method="post">' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo '<table width="90%" class="table_form">' . "\n";
    echo '<tr><td class="label" width="20%"> ' . ucfirst(LG_TOWN_SCH_NAME) . ' </td>';
    echo '<td class="value"><input type="text" size="80" name="NomV"';
    if ($reprise) echo ' value="' . $NomV . '"';
    echo '/>';
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%"> ' . ucfirst(LG_TOWN_SCH_ZIP) . ' </td>';
    echo '<td class="value"><input type="text" name="Code_Postal"';
    if ($reprise) echo ' value="' . $Code_Postal . '"';
    echo '/>';
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%"> ' . ucfirst(LG_COUNTY) . ' </td>';
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

    if (IS_GRANTED('G')) {
        echo '<tr><td class="label" width="20%"> ' . ucfirst(LG_TOWN_SCH_STATUS) . ' </td>';
        echo '<td class="value">' . "\n";
        echo '<input type="radio" id="Statut_Fiche_o" name="Statut_Fiche" value="O"';
        if ($reprise) {
            if ($Statut_Fiche == 'O') echo ' checked';
        }
        echo '/><label for="Statut_Fiche_o">' . LG_CHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="Statut_Fiche_n" name="Statut_Fiche" value="N"';
        if ($reprise) {
            if ($Statut_Fiche == 'N') echo ' checked';
        }
        echo '/><label for="Statut_Fiche_n">' . LG_NOCHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="Statut_Fiche_i" name="Statut_Fiche" value="I"';
        if ($reprise) {
            if ($Statut_Fiche == 'I') echo ' checked';
        }
        echo '/><label for="Statut_Fiche_i">' . LG_FROM_INTERNET . '</label>';
        echo '</td></tr>' . "\n";
    }

    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    echo '<tr><td class="label" width="20%"> ' . ucfirst($LG_Ch_Output_Format) . ' </td>';
    echo '<td class="value">';
    echo '<input type="radio" id="Sortie_e" name="Sortie" value="e" checked/><label for="Sortie_e">' . $LG_Ch_Output_Screen . '</label> ';
    echo '<input type="radio" id="Sortie_t" name="Sortie" value="t"/><label for="Sortie_t">' . $LG_Ch_Output_Text . '</label> ';
    if (IS_GRANTED('P')) echo '<input id="Sortie_c" type="radio" name="Sortie" value="c"/><label for="Sortie_c">' . $LG_Ch_Output_CSV . '</label>';
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%"> ' . ucfirst(LG_TOWN_NEW_TAB) . ' </td>';
    echo '<td class="value">';
    echo '<input type="checkbox" name="New_Window"';
    if ($reprise) {
        if ($New_Window == 'O') echo ' checked';
    }
    echo ' value="O"/>';
    echo '</td></tr>' . "\n";

    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    bt_ok_an_sup($lib_Rechercher, $lib_Annuler, '', '');

    echo '</table>' . "\n";
    echo '</form>';
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
?>
</body>

</html>