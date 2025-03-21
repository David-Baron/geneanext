<?php

//=====================================================================
// Import ou lecture d'un fichier csv avec des évènements
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('G')) {
    header('Location: ' . $root . '/');
    exit();
}

$tab_variables = array(
    'ok',
    'annuler',
    'nom_du_fichier',
    'Horigine',
    'nom_du_fichier',
    'val_statut',
    'entete',
    'idNiveauF',
    'idZoneF',
    'type_evt'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées - phase 1
$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

require(__DIR__ . '/../app/ressources/fonctions_maj.php');

$titre = $LG_Menu_Title['Imp_CSV_Events'];
$x = Lit_Env();

require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Page interdite sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// $champ_table  : champ dans la table à charger
// $champ_lib    : libellé du champ
// $champ_classe : clase du champ : (C)aractère, (N)umérique, (D)ate
$champ_table[]  = 'Titre';
$champ_lib[]    = $LG_ICSV_Event_Title;
$champ_classe[] = 'C';
$champ_table[]  = 'Debut';
$champ_lib[]    = $LG_ICSV_Event_Beg;
$champ_classe[] = 'D';
$champ_table[]  = 'Fin';
$champ_lib[]    = $LG_ICSV_Event_End;
$champ_classe[] = 'D';

$n_events = nom_table('evenements');
$n_types_evenement = nom_table('types_evenement');

// Champs du formulaire
$radical_variable_champ   = 'var_champ_';
$radical_variable_csv     = 'var_csv_';

if ($bt_OK) Ecrit_Entete_Page($titre, '', '');

$compl = Ajoute_Page_Info(600, 300);
Insere_Haut($titre, $compl, 'Import_CSV_Evenements', '');

require(__DIR__ . '/../app/ressources/commun_import_csv.php');

//Demande de chargement
if ($ok == 'OK') {
    // Sécurisation des variables postées - phase 2
    $nom_du_fichier = Secur_Variable_Post($nom_du_fichier, 100, 'S');
    $val_statut     = Secur_Variable_Post($val_statut, 1, 'S');
    $entete         = Secur_Variable_Post($entete, 1, 'S');
    $idNiveauF      = Secur_Variable_Post($idNiveauF, 1, 'N');
    $idZoneF        = Secur_Variable_Post($idZoneF, 1, 'N');
    $type_evt       = Secur_Variable_Post($type_evt, 4, 'S');
    if ($idZoneF == -1) $idZoneF = 0;

    // Pas de limite de temps en local
    // Sur le net, limite fixée à la valeur paramétrée ; plus importante sur les sites Premium
    if ($Environnement == 'L') {
        set_time_limit(0);
    }
    if ($SiteGratuit) {
        set_time_limit($lim_temps);
    }

    echo $LG_Requested_File . ' : ' . $_FILES['nom_du_fichier']['name'] . '<br />';

    $status = '';
    switch ($val_statut) {
        case 'O':
            $status = LG_CHECKED_RECORD_SHORT;
            break;
        case 'N':
            $status = LG_NOCHECKED_RECORD_SHORT;
            break;
        case 'I':
            $status = LG_FROM_INTERNET;
            break;
    }
    echo $LG_Default_Status . ' : ' . $status . '<br />';

    //Restitution du type d'évènement
    $requete  = 'select Libelle_Type from ' . $n_types_evenement . " where Code_Type = '" . $type_evt . "' limit 1";
    $result = lect_sql($requete);
    $enreg = $result->fetch(PDO::FETCH_NUM);
    echo $LG_ICSV_Event_Type . ' : ' . my_html($enreg[0]) . '<br />';

    //Restitution du lieu
    echo $LG_ICSV_Event_Where . ' : ';
    if ($idNiveauF != 0) {
        echo LectZone($idZoneF, $idNiveauF) . '<br />';
    } else {
        echo $LG_ICSV_Event_Where_No . '<br />';
    }

    $erreur = false;
    $tmp_file = $_FILES['nom_du_fichier']['tmp_name'];
    $nom_du_fichier = $_FILES['nom_du_fichier']['name'];

    // Une demande de chargement a été faite
    if ($nom_du_fichier != '') {
        $erreur = ctrl_fichier_ko();
        if (!$erreur) {
            // Seuls sont autorisés les fichiers csv
            if (Extension_Fic($nom_du_fichier) != 'csv') {
                echo '<center><font color="red"><br><br><br><h2>' . LG_IMP_CSV_ERR_TYPE . '</h2></font></center>';
                $erreur = true;
            }
        }

        // On peut télécharger s'il n'y a pas d'erreur
        if (!$erreur) {
            $path = $chemin_exports . $nom_du_fichier;
            move_uploaded_file($tmp_file, $path);
            // Traitement du fichier
            if ($fp = fopen($path, 'r')) {
                $nb_enr_crees = 0;
                $nb_enr = 0;
                $modif = false;
                $num_ident = '';
                $deb_req = 'insert into ' . $n_events . ' (';
                $deb_req_suite = ',Date_Creation,Date_Modification,Statut_Fiche,Identifiant_zone,Identifiant_Niveau,Code_Type';
                $fin_req = ',current_timestamp,' .    // Date_Creation
                    'current_timestamp,' .    // Date_Modification
                    '"' . $val_statut . '",' .    // Statut_Fiche
                    '"' . $idZoneF . '",' .        // Identifiant_zone
                    '"' . $idNiveauF . '",' .        // Identifiant_Niveau
                    '"' . $type_evt . '"' .        // Code_Type
                    ')';

                //(Titre,Debut,FinTitre,Debut,Fin,Date_Creation,Date_Modification,Statut_Fiche,Identifiant_zone,Identifiant_Niveau,Code_Type) 
                //("Evt 1",19400101GL,19400211GL,current_timestamp,current_timestamp,"O","71","1","ADOP")
                insert_champs();
                fclose($fp);
                if ($modif) {
                    maj_date_site();
                    echo $nb_enr_crees . ' ' . LG_IMP_CSV_EVTS_CREATED . '<br />';
                }
            } else {
                echo LG_IMP_CSV_ERR_OPEN_FILE . '<br />';
            }
        }
    }
}

    if (($ok == '') && ($annuler == '')) {

        include(__DIR__ . '/../public/assets/js/Edition_Evenement.js');

        echo '<br />';
        echo '<form id="saisie" method="post" enctype="multipart/form-data">' . "\n";
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        echo '<input type="hidden" name="idZoneF" value="-1"/>' . "\n";
        echo '<table width="90%" class="table_form">' . "\n";
        echo '<tr><td class="label" width="35%">' . ucfirst($LG_csv_file_upload) . '</td><td class="value">';
        echo '<input type="file" name="nom_du_fichier" size="80"/></td>';
        echo '</tr>' . "\n";
        echo '<tr><td class="label" width="35%">' . $LG_Default_Status . '</td><td class="value">';
        echo '<input type="radio" id="val_statutO" name="val_statut" value="O" checked/>'
        . '<label for="val_statutO">' . LG_CHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="val_statutN" name="val_statut" value="N"/>'
        . '<label for="val_statutN">' . LG_NOCHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="val_statutI" name="val_statut" value="I"/>'
        . '<label for="val_statutI">' . LG_FROM_INTERNET . '</label> ';
        echo '</td></tr>';
        echo '<tr><td colspan="2"> </td></tr>';
        echo '<tr><td class="label" width="35%">' . ucfirst($LG_ICSV_Event_Where) . '</td><td class="value">';
        // Niveau de la zone géographique associée
        $name_radio = 'idNiveauF';
        echo '<input type="radio" name="' . $name_radio . '" id="' . $name_radio . '_0" value="0" checked="checked" onclick="cache_image_zone()"/><label for="' . $name_radio . '_0">'
            . LG_EVENT_NOPLACE . '</label> ' . "\n";

        $req = 'select * from ' . nom_table('niveaux_zones');
        $result = lect_sql($req);

        while ($enr_zone = $result->fetch(PDO::FETCH_ASSOC)) {
            $id_niveau = $enr_zone['Identifiant_Niveau'];
            $id = $name_radio . '_' . $id_niveau;
            echo '<input type="radio" name="idNiveauF" id="' . $id . '" value="' . $id_niveau . '" onclick="bascule_image(\'img_zone\')"/><label for="' . $id . '">' . $enr_zone['Libelle_Niveau'] . '</label> ' . "\n";
        }
        echo '<input type="text" readonly="readonly" name="zoneAff" value=""/>' . "\n";
        echo '<img id="img_zone" style="display:none; visibility:hidden;" src="' . $root . '/assets/img/' . $Icones['localisation'] . '"  alt="' . $LG_Place_Select . '" title="' . $LG_Place_Select . '"' .
            ' onclick="Appelle_Zone_Lect()"/>' . "\n";
        echo "</td></tr>\n";
        echo '<tr><td class="label" width="35%">' . ucfirst($LG_ICSV_Event_Type) . '</td><td class="value">';
        // Select avec les types existants
        $req = 'select Code_Type, Libelle_Type from ' . $n_types_evenement . ' order by Libelle_Type';
        $result = lect_sql($req);
        if ($result->rowCount() > 0) {
            echo '<select name="type_evt">' . "\n";
            echo '<option value="-">' . LG_IMP_CSV_LINKS_SEL_TYPE . '</option>' . "\n";
            while ($enrT = $result->fetch(PDO::FETCH_NUM)) {
                echo '<option value="' . $enrT[0] . '">' . my_html($enrT[1]) . '</option>' . "\n";
            }
            echo '</select>' . "\n";
        }
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="35%">' . $LG_csv_header . '</td><td class="value">';
        echo '<input type="radio" name="entete" id="entete_A" value="A" onclick="montre_div(\'corresp\');" checked="checked"/><label for="entete_A">' . LG_IMP_CSV_HEADER_NO . '</label> ';
        echo '<input type="radio" name="entete" id="entete_I" value="I" onclick="montre_div(\'corresp\');"/><label for="entete_I">' . LG_IMP_CSV_HEADER_YES_IGNORE . '</label> ';
        echo '<input type="radio" name="entete" id="entete_P" value="P" onclick="cache_div(\'corresp\');"/><label for="entete_P">' . LG_IMP_CSV_HEADER_YES_CONSIDER . '</label>';
        echo '</td></tr>';
        echo '<tr><td class="label" width="35%">' . LG_IMP_CSV_COLS_MATCH . '</td><td class="value">';
        echo '<div id="corresp">';
        echo '<table>';
        echo '<tr align="center">';
        echo '<td>' . LG_IMP_CSV_COLS_CSV . '</td>';
        echo '<td>' . LG_IMP_CSV_COLS_GEN . '</td></tr>';
        // Les 3 zones suivantes sont fixes
        echo '<tr>';
        aff_corr_csv2(0);
        echo '<td><input type="text" name="' . $radical_variable_champ . '0" readonly="readonly" value="' . $champ_lib[0] . '"/></td>';
        echo '</tr><tr>';
        aff_corr_csv2(1);
        echo '<td><input type="text" name="' . $radical_variable_champ . '1" readonly="readonly" value="' . $champ_lib[1] . '"/></td>';
        echo '</tr><tr>';
        aff_corr_csv2(2);
        echo '<td><input type="text" name="' . $radical_variable_champ . '2" readonly="readonly" value="' . $champ_lib[2] . '"/></td>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';
        echo '</td></tr>';
        echo '<tr><td colspan="2"> </td></tr>';
        bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');
        echo '<tr><td colspan="2"> </td></tr>';
        echo '</table>';
        echo '</form>';
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