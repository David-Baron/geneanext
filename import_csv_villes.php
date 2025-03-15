<?php

//=====================================================================
// Import ou lecture d'un fichier csv avec des villes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'nom_du_fichier',
    'Horigine',
    'nom_du_fichier',
    'val_statut',
    'entete',
    'departement',
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées - phase 1
$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

require(__DIR__ . '/app/ressources/fonctions_maj.php');
$acces = 'L';
$titre = $LG_Menu_Title['Imp_CSV_Towns'];
$x = Lit_Env();
$niv_requis = 'G';
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Page interdite sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

/*
Identifiant_zone
Nom_Ville 	
Code_Postal 	
Date_Creation 	
Date_Modification 	
Statut_Fiche 	
Zone_Mere 	
Latitude 	
Longitude
Identifiant_zone
*/

// $champ_table  : champ dans la table à charger
// $champ_lib    : libellé du champ
// $champ_classe : clase du champ : (C)aractère, (N)umérique, (D)ate
$champ_table[]  = 'Nom_Ville';
$champ_lib[]    = LG_ICSV_TOWN_NAME;
$champ_classe[] = 'C';
$champ_table[]  = 'Code_Postal';
$champ_lib[]    = LG_ICSV_TOWN_ZIP_CODE;
$champ_classe[] = 'C';
$champ_table[]  = 'Latitude';
$champ_lib[]    = LG_ICSV_TOWN_ZIP_LATITUDE;
$champ_classe[] = 'C';
$champ_table[]  = 'Longitude';
$champ_lib[]    = LG_ICSV_TOWN_ZIP_LONGITUDE;
$champ_classe[] = 'C';

$n_villes = nom_table('villes');
$n_departements = nom_table('departements');

// Champs du formulaire
$radical_variable_champ   = 'var_champ_';
$radical_variable_csv     = 'var_csv_';

if ($bt_OK) Ecrit_Entete_Page($titre, '', '');

$compl = Ajoute_Page_Info(600, 300);
Insere_Haut($titre, $compl, 'Import_CSV_Villes', '');

require(__DIR__ . '/app/ressources/commun_import_csv.php');

//Demande de chargement
if ($ok == 'OK') {
    // Sécurisation des variables postées - phase 2
    $nom_du_fichier = Secur_Variable_Post($nom_du_fichier, 100, 'S');
    $val_statut     = Secur_Variable_Post($val_statut, 1, 'S');
    $entete         = Secur_Variable_Post($entete, 1, 'S');
    $departement    = Secur_Variable_Post($departement, 1, 'N');

    // Pas de limite de temps en local
    // Sur le net, limite fixée à la valeur paramétrée ; plus importante sur les sites Premium
    if ($Environnement == 'L') {
        set_time_limit(0);
    }
    if ($SiteGratuit) {
        set_time_limit($lim_temps);
    }

    echo '<br />' . $LG_Requested_File . ' : ' . $_FILES['nom_du_fichier']['name'] . '<br />';

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
    echo $LG_Default_Status . ' : ' . my_html($status) . '<br />';

    //Restitution du département
    $requete  = 'select Nom_Depart_Min from ' . $n_departements . " where Identifiant_zone = '" . $departement . "' limit 1";
    $result = lect_sql($requete);
    $enreg = $result->fetch(PDO::FETCH_NUM);
    echo LG_COUNTY . ' : ' . my_html($enreg[0]) . '<br />';

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
            // ini_set('auto_detect_line_endings',TRUE);
            $mode = 'r';

            if ($fp = fopen($path, $mode)) {

                $nb_enr_crees = 0;

                $nb_enr = 0;
                $modif = false;

                $num_ident = Nouvel_Identifiant('Identifiant_zone', 'villes');
                $deb_req = 'insert into ' . $n_villes . ' (Identifiant_zone,';
                $deb_req_suite = ',Date_Creation,Date_Modification,Statut_Fiche,Zone_Mere';
                $fin_req = ',current_timestamp,' .    // Date_Creation
                    'current_timestamp,' .    // Date_Modification
                    '"' . $val_statut . '",' .    // Statut_Fiche
                    $departement .            // Zone_Mere
                    ')';
                insert_champs();
                fclose($fp);

                if ($modif) {
                    maj_date_site(true);
                    // $plu = pluriel($nb_enr_crees);
                    echo $nb_enr_crees . ' ' . LG_IMP_CSV_TOWNS_CREATED . '<br />';
                }
            } else {
                echo LG_IMP_CSV_ERR_OPEN_FILE . '<br />';
            }
        }
    }
}

if ($est_gestionnaire) {
    // Première entrée : affichage pour saisie
    if (($ok == '') && ($annuler == '')) {

        echo '<br />';
        echo '<form id="saisie" method="post" enctype="multipart/form-data">' . "\n";
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        echo '<table width="90%" class="table_form">' . "\n";
        echo '<tr><td class="label" width="35%">' . ucfirst($LG_csv_file_upload) . '</td><td class="value">';
        echo '<input type="file" name="nom_du_fichier" size="80"/></td>';
        echo '</tr>' . "\n";
        echo '<tr><td class="label" width="35%">' . $LG_Default_Status . '</td><td class="value">';
        bouton_radio('val_statut', 'O', LG_CHECKED_RECORD_SHORT, true);
        bouton_radio('val_statut', 'N', LG_NOCHECKED_RECORD_SHORT);
        bouton_radio('val_statut', 'I', LG_FROM_INTERNET);
        echo '</td></tr>';

        echo '<tr><td colspan="2"> </td></tr>';
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_COUNTY) . '</td><td class="value">';
        // Département de la ville
        $req = 'select Identifiant_zone, Nom_Depart_Min from ' . $n_departements . ' order by Nom_Depart_Min';
        $result = lect_sql($req);
        if ($result->rowCount() > 0) {
            echo '<select name="departement">' . "\n";
            echo '<option value="0">' . my_html(LG_ICSV_TOWN_SELECT_DEPARTEMENT) . '...</option>' . "\n";
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
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_ICSV_TOWN_COL_MATCHING) . '</td><td class="value">';
        echo '<div id="corresp">';
        echo '<table>';
        echo '<tr align="center">';
        echo '<td>' . LG_ICSV_TOWN_COL_CALC . '</td>';
        echo '<td>' . LG_ICSV_TOWN_COL_GEN_FIElD . '</td></tr>';
        echo '<tr>';
        aff_corr_csv(0);
        echo '<td><input type="text" name="' . $radical_variable_champ . '0" id="' . $radical_variable_champ . '0" readonly="readonly" value="' . $champ_lib[0] . '"/></td>';
        echo '</tr>' . "\n";
        $c_zbase = count($champ_lib);
        for ($nb = 1; $nb < $c_zbase; $nb++) {
            echo '<tr>';
            aff_corr_csv($nb);
            echo '<td><select name="' . $radical_variable_champ . $nb . '" id="' . $radical_variable_champ . $nb . '">' . "\n";
            echo '<option value="-1">' . LG_ICSV_TOWN_COL_SEL_FIELD . '</option>' . "\n";
            for ($nb2 = 1; $nb2 < $c_zbase; $nb2++) echo '<option value="' . $champ_lib[$nb2] . '">' . $champ_lib[$nb2] . '</option>';
            echo '</select></td>' . "\n";
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '</td></tr>' . "\n";
        echo '<tr><td colspan="2"> </td></tr>';
        bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');
        echo '<tr><td colspan="2"> </td></tr>';
        echo '</table>';
        echo '</form>';
    }
} else {
    echo $LG_function_noavailable_profile;
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