<?php

//=====================================================================
// Import ou lecture d'un fichier csv
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
    'diff_internet',
    'val_statut',
    'entete'
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
$acces = 'L'; // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Import CSV'; // Titre pour META
$x = Lit_Env();
$niv_requis = 'G'; // Page accessible au gestionnaire
require(__DIR__ . '/../app/ressources/gestion_pages.php');

if (($SiteGratuit) and (!$Premium)) Retour_Ar(); // Page interdite sur les gratuits non Premium

// Valeurs par défaut des codes département et région
$code_defaut_depart = 'xxx';
$code_defaut_region = 99999999999;

// Champs du formulaire
$radical_variable_champ   = 'var_champ_';
$radical_variable_csv     = 'var_csv_';

$z_base[] = LG_PERS_NAME;
$z_base[] = LG_PERS_FIRST_NAME;
$z_base[] = LG_SEXE;
$z_base[] = LG_PERS_BORN;
$z_base[] = $LG_ICSV_Pers_Ville_Naissance;
$z_base[] = LG_PERS_DEAD;
$z_base[] = $LG_ICSV_Pers_Ville_Deces;
$z_base[] = $LG_ICSV_Pers_Numero;
$z_base[] = $LG_ICSV_Pers_Surnom;
$c_zbase = count($z_base);

$champ_table[] = 'Nom';
$champ_table[] = 'Prenoms';
$champ_table[] = 'Sexe';
$champ_table[] = 'Ne_le';
$champ_table[] = 'Ville_Naissance';
$champ_table[] = 'Decede_Le';
$champ_table[] = 'Ville_Deces';
$champ_table[] = 'Numero';
$champ_table[] = 'Surnom';

// Traitement d'une ville
// Si elle n'existe pas, on la crée
function traite_ville($la_ville)
{
    global
        $db, $memo_id_ville, $memo_lib_ville,
        $arr_villes, $num_ville,
        $val_statut, $modif;

    if ($la_ville !== 0) {
        $la_ville = trim($la_ville);
        // Si la ville est différente de la précedente, on fait la recherche, sinon, on renvoie l'ident précédent  	
        if ($la_ville != $memo_lib_ville) {
            $memo_lib_ville = $la_ville;
            $x = false;
            if (isset($arr_villes)) $x = array_search($la_ville, $arr_villes);
            // La zone a été trouvée dans le tableau
            if ($x !== false) {
                $zone_geo = $arr_villes[$x];
            }
            // Sinon on la recherche en base et éventuellement on la crée et on attribue un nouvel identifiant
            else {
                $sZone = ajoute_sl($la_ville);
                $trouve = false;
                $sql = 'select Identifiant_zone from ' . nom_table('villes') . ' where Nom_Ville = \'' . $sZone . '\' limit 1';
                if ($res = lect_sql($sql)) {
                    if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                        $memo_id_ville = $enreg[0];
                        $trouve = true;
                    }
                }
                // Si la ville n'est pas trouvée, on la crée en base
                if (!$trouve) {
                    // La première fois, on va récupérer l'identifiant
                    if (!$num_ville) $num_ville = Nouvel_Identifiant('Identifiant_zone', 'villes');
                    else $num_ville++;
                    // Création de la ville en base
                    $req = 'insert into ' . nom_table('villes') . ' values(' . $num_ville . ',' . '\'' . $sZone . '\',null,current_timestamp,current_timestamp,\'' . $val_statut . '\',0,0,0)';
                    $res = maj_sql($req);

                    $modif = true;
                }
                $arr_villes[] = $num_ville;
                $memo_id_ville = $num_ville;
            }
            return $memo_id_ville;
        } else return $memo_id_ville;
    } else return 0;
}

if ($bt_OK) Ecrit_Entete_Page($titre, '', '');

$compl = Ajoute_Page_Info(600, 300);
Insere_Haut($titre, $compl, 'Import_CSV', '');
require(__DIR__ . '/../app/ressources/commun_import_csv.php');

//Demande de chargement
if ($ok == 'OK') {
    // Sécurisation des variables postées - phase 2
    $nom_du_fichier = Secur_Variable_Post($nom_du_fichier, 100, 'S');
    $diff_internet  = Secur_Variable_Post($diff_internet, 2, 'S');
    $val_statut     = Secur_Variable_Post($val_statut, 1, 'S');
    $entete         = Secur_Variable_Post($entete, 1, 'S');

    $num_ville = 0;

    $max_champs = 0;
    for ($num_ligne = 0; $num_ligne < $c_zbase; $num_ligne++) {
        $nom_var = $radical_variable_champ;
        $nom_var_def = $nom_var . $num_ligne;
        $$nom_var_def = retourne_var_post($nom_var, $num_ligne);

        if ($$nom_var_def != -1) {
            // Recherche du libellé des champs
            $cont = $$nom_var_def;
            $x = array_search($cont, $z_base);
            if ($x !== false) {
                $champ_pers[$max_champs++] = $champ_table[$x];
            } else {
                echo 'Erreur sur recherche libellé champ<br>';
            }
        }
    }

    // Transformation des colonnes CSV en numéros
    $o_A = ord('A');
    for ($num_ligne = 0; $num_ligne < $c_zbase; $num_ligne++) {
        $nom_var = $radical_variable_csv;
        $nom_var_def = $nom_var . $num_ligne;
        $$nom_var_def = retourne_var_post($nom_var, $num_ligne);
        $cont = $$nom_var_def;
        if ($cont != -1) {
            $num_csv[$num_ligne] = ord(substr($$nom_var_def, 0, 1)) - $o_A;
        }
    }

    // Pas de limite de temps en local
    // Sur le net, limite fixée à la valeur paramétrée ; plus importante sur les sites Premium
    if ($Environnement == 'L') {
        set_time_limit(0);
    }
    if ($SiteGratuit) {
        set_time_limit($lim_temps);
    }

    $msg = ' Fichier demandé : ' . $_FILES['nom_du_fichier']['name'];
    echo $msg . '<br>';

    echo 'Visibilité Internet ';
    if ($diff_internet != 'on') echo 'non ';
    echo 'autorisée par défaut<br>';
    echo 'Statut par défaut des fiches : ';
    switch ($val_statut) {
        case 'O':
            echo LG_CHECKED_RECORD_SHORT;
            break;
        case 'N':
            echo LG_NOCHECKED_RECORD_SHORT;
            break;
        case 'I':
            echo LG_FROM_INTERNET;
            break;
    }
    echo '<br>';

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
            }
        }

        if (!$erreur) { // On peut télécharger s'il n'y a pas d'erreur
            $vdiff_internet = 'N';
            if ($diff_internet == 'on') $vdiff_internet = 'O';

            move_uploaded_file($tmp_file, $chemin_exports . $nom_du_fichier);

            // Traitement du fichier
            if ($fp = fopen($chemin_exports . $nom_du_fichier, 'r')) {
                $Cre_Noms = false;
                $memo_id_ville = 0;
                $memo_lib_ville = '';
                $nb_pers = 0;
                $nb_enr = 0;
                $modif = false;
                $num_pers = Nouvel_Identifiant('Reference ', 'personnes');

                // Balayage du fichier
                while (($arr = fgetcsv($fp, 1000, ';', '"')) !== false) {
                    $nb_enr++;
                    $c_arr = count($arr);
                    if ($nb_enr == 1) {
                        $max_champs = 0;
                        echo LG_IMP_CSV_REQ_FIELDS . ' :<br>';
                        $tab = '    ';

                        // Récupération de la correspondance des champs dans l'entête
                        if ($entete == 'P') {
                            $z_base_l = array_map('strtolower', $z_base);
                            $champ_table_l = array_map('strtolower', $champ_table);
                            $arr_l = array_map('strtolower', $arr);
                            for ($nb = 0; $nb < $c_arr; $nb++) {
                                // Recherche du libellé du champ
                                $cont = trim($arr_l[$nb]);
                                $x = array_search($cont, $z_base_l);
                                if ($x === false) $x = array_search($cont, $champ_table_l);
                                if ($x !== false) {
                                    $champ_pers[$max_champs] = $champ_table[$x];
                                    if ($debug) {
                                        echo '$champ_table[$x] : ' . $champ_table[$x] . '<br>';
                                        echo '$champ_pers[$max_champs] : ' . $champ_pers[$max_champs] . '<br>';
                                    }
                                    $num_csv[$max_champs] = $nb;
                                    echo $tab . '"' . $cont . '" ' . LG_IMP_CSV_IN_COL . ' ' . chr($o_A + $nb) . '<br>';
                                    $max_champs++;
                                } else {
                                    echo LG_IMP_CSV_ERR_MATCH_1 . my_html($cont) . LG_IMP_CSV_ERR_MATCH_2 . '<br>';
                                }
                            }
                        } else {
                            for ($num_ligne = 0; $num_ligne < $c_zbase; $num_ligne++) {
                                $nom_var = $radical_variable_champ;
                                $nom_var_def = $nom_var . $num_ligne;
                                $$nom_var_def = retourne_var_post($nom_var, $num_ligne);

                                if ($$nom_var_def != -1) {
                                    // Recherche du libellé des champs
                                    $cont = $$nom_var_def;
                                    $x = array_search($cont, $z_base);
                                    if ($x !== false) {
                                        $champ_pers[$max_champs++] = $champ_table[$x];
                                    } else {
                                        echo LG_IMP_CSV_COL_MATCH_ERROR . '<br>';
                                    }
                                }
                            }

                            // Transformation des colonnes CSV en numéros
                            $o_A = ord('A');
                            for ($num_ligne = 0; $num_ligne < $c_zbase; $num_ligne++) {
                                $nom_var = $radical_variable_csv;
                                $nom_var_def = $nom_var . $num_ligne;
                                $$nom_var_def = retourne_var_post($nom_var, $num_ligne);
                                $cont = $$nom_var_def;
                                if ($cont != -1) {
                                    $num_csv[$num_ligne] = ord(substr($$nom_var_def, 0, 1)) - $o_A;
                                }
                            }
                            for ($nb = 0; $nb < $max_champs; $nb++) {
                                $nom_champ = $radical_variable_champ . $nb;
                                //echo 'Champ saisi : '.$$nom_champ.', ';
                                $nom_champ_dem = $$nom_champ;
                                //echo 'champ dans Généamania : '.$champ_pers[$nb].', ';
                                $nom_champ = $radical_variable_csv . $nb;
                                //echo 'colonne saisie : '.$$nom_champ.', ';
                                $nom_col_dem = $$nom_champ;
                                $num_col = $num_csv[$nb] + 1;
                                //echo 'numéro de la colonne : '.$num_col.'<br>';
                                echo $tab . '"' . $nom_champ_dem . '" ' . my_html(LG_IMP_CSV_IN_COL) . ' ' . $nom_col_dem . '<br>';
                            }
                        }


                        // Recherche des correspondances champs / colonnes
                        for ($nb = 0; $nb < $c_zbase; $nb++) {
                            //echo '======='.$champ_table[$nb].' ';
                            $x = array_search($champ_table[$nb], $champ_pers);
                            // On constitue une variable de nom 'col'+nom du champ dans la table des personnes
                            // cette variable contient le numéro de colonne dans le CSV ; -1 si pas de colonne
                            $nom_var = 'col_' . $champ_table[$nb];
                            $$nom_var = -1;
                            if ($x !== false) {
                                $$nom_var = $num_csv[$x];
                                $num_col = $num_csv[$x] + 1;
                                //echo $champ_table[$x]. ' > '.$num_col;
                            }
                            //echo '<br>';
                        }
                    }

                    //for ($nb2=0; $nb2<$c_arr; $nb2++) echo $arr[$nb2].'/';
                    //echo '<br>';

                    if (($entete == 'A') or ($nb_enr > 1)) {

                        // Init des variables à stocker;
                        //$Reference         = 0;
                        $Nom               = '?';
                        $Prenoms           = '?';
                        $Sexe              = 'null';
                        $Numero            = '';
                        $Ne_le             = '';
                        $Decede_Le         = '';
                        $Ville_Naissance   = 0;
                        $Ville_Deces       = 0;
                        $Diff_Internet     = $vdiff_internet;
                        $Date_Creation     = 'current_timestamp';
                        $Date_Modification = 'current_timestamp';
                        $Statut_Fiche      = $val_statut;
                        $idNomFam          = 'null';
                        $Categorie         = 0;
                        $Surnom            = 'null';

                        if ($c_arr < $max_champs) {
                            $ligne = implode(';', $arr);
                            echo LG_IMP_CSV_ERROR_LINE . ' ' . $nb_enr . ' : ' . $ligne . '<br>';
                            // echo '$c_arr : '.$c_arr.'<br>';
                            // echo '$max_champs : '.$max_champs.'<br>';
                            // var_dump($arr); echo '<br>';
                        } else {

                            // Récupération des champs
                            for ($nb = 0; $nb < $c_zbase; $nb++) {
                                $nomvar = $champ_table[$nb];
                                //echo 'Variable : '.$nomvar.' ; ';
                                $colvar = 'col_' . $champ_table[$nb];
                                //echo 'Colonne dans  : '.$colvar.' ; ';
                                //echo 'Num colonne : '.$$colvar.' ; ';
                                if ($$colvar != -1) {
                                    $$nomvar = $arr[$$colvar];
                                    //echo 'Contenu dans ligne : '.$arr[$$colvar].' ; ';
                                    //echo 'Contenu variable : '.$$nomvar;
                                }
                                //echo '<br>';
                            }

                            // Traitement des villes
                            $Ville_Naissance = traite_ville($Ville_Naissance);
                            $Ville_Deces = traite_ville($Ville_Deces);

                            // Traitement des dates
                            $Ne_le = traite_date_csv($Ne_le);
                            $Decede_Le = traite_date_csv($Decede_Le);
                        }

                        if ($Sexe != 'null') $Sexe = "'" . strtolower($Sexe) . "'";
                        if ($Surnom != 'null') $Surnom = "'" . $Surnom . "'";

                        $req = 'insert into ' . nom_table('personnes') . ' values(' . $num_pers . ',' .
                            '\'' . ajoute_sl($Nom) . '\',' .
                            '\'' . ajoute_sl($Prenoms) . '\',' .
                            $Sexe . ',' .
                            '\'' . $Numero . '\',' .
                            '\'' . $Ne_le . '\',' .
                            '\'' . $Decede_Le . '\',' .
                            strval($Ville_Naissance) . ',' .
                            strval($Ville_Deces) . ',' .
                            '\'' . $Diff_Internet . '\',' .
                            $Date_Creation . ',' .
                            $Date_Modification . ',' .
                            '\'' . $Statut_Fiche . '\',' .
                            strval($idNomFam) . ',' .
                            strval($Categorie) . ',' .
                            $Surnom . ');';
                        $res = maj_sql($req);
                        if ($res !== false) ++$nb_pers;
                        $modif = true;
                        $num_pers++;
                    }
                }

                fclose($fp);

                if ($modif) {
                    Creation_Noms_Commun();
                    maj_date_site(true);
                    echo $nb_pers . ' ' . LG_IMP_CSV_PERS_CREATED . '<br>';
                }
            } else {
                echo LG_IMP_CSV_ERR_OPEN_FILE . '<br>';
            }
        }
    }
}

    if (($ok == '') && ($annuler == '')) {
        echo '<br>';
        echo '<form id="saisie" method="post" enctype="multipart/form-data">' . "\n";
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        echo '<table width="90%" class="table_form">' . "\n";
        echo '<tr><td class="label" width="35%">' . ucfirst($LG_csv_file_upload) . '</td><td class="value">';
        echo '<input type="file" name="nom_du_fichier" size="80"/></td>';
        echo '</tr>' . "\n";

        // Sur site gratuit non  Premium, on diffuse par défaut sans possibilité de modifier l'indicteur ==> respect de la charte
        $readonly = false;
        if (($SiteGratuit) and (!$Premium)) $readonly = true;

        echo '<tr><td colspan="2"> </td></tr>';
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_CSV_DEFAULT_SHOW) . '</td><td class="value">';
        if ($readonly) {
            echo LG_IMP_CSV_NO_PREMIUM;
            echo '<input type="hidden" name="diff_internet" value="on">';
        } else {
            echo '<input type="checkbox" name="diff_internet" checked>';
        }
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="35%">' . $LG_Default_Status . '</td><td class="value">';
        echo '<input type="radio" id="val_statutO" name="val_statut" value="O" checked/>'
        . '<label for="val_statutO">' . LG_CHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="val_statutN" name="val_statut" value="N"/>'
        . '<label for="val_statutN">' . LG_NOCHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="val_statutI" name="val_statut" value="I"/>'
        . '<label for="val_statutI">' . LG_FROM_INTERNET . '</label> ';
        echo '</td></tr>';
        echo '<tr><td class="label" width="35%">' . $LG_csv_header . '</td><td class="value">';
        echo '<input type="radio" name="entete" id="entete_A" value="A" onclick="montre_div(\'corresp\');" checked/><label for="entete_A">' . LG_IMP_CSV_HEADER_NO . '</label> ';
        echo '<input type="radio" name="entete" id="entete_I" value="I" onclick="montre_div(\'corresp\');"/><label for="entete_I">' . LG_IMP_CSV_HEADER_YES_IGNORE . '</label> ';
        echo '<input type="radio" name="entete" id="entete_P" value="P" onclick="cache_div(\'corresp\');"/><label for="entete_P">' . LG_IMP_CSV_HEADER_YES_CONSIDER . '</label>';
        echo '</td></tr>';
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_CSV_COLS_MATCH) . '</td><td class="value">';
        echo '<div id="corresp">';
        echo '<table>';
        echo '<tr align="center">';
        echo '<td>' . LG_IMP_CSV_COLS_CSV . '</td>';
        echo '<td>' . LG_IMP_CSV_COLS_GEN . '</td></tr>';
        echo '<tr>';
        aff_corr_csv(0);
        echo '<td><input type="text" name="' . $radical_variable_champ . '0" id="' . $radical_variable_champ . '0" readonly="readonly" value="' . $z_base[0] . '"/></td>' . "\n";
        echo '</tr><tr>' . "\n";
        aff_corr_csv(1);
        echo '<td><input type="text" name="' . $radical_variable_champ . '1" id="' . $radical_variable_champ . '1" readonly="readonly" value="' . $z_base[1] . '"/></td>' . "\n";
        echo '</tr>';
        for ($nb = 2; $nb < $c_zbase; $nb++) {
            echo '<tr>';
            aff_corr_csv($nb);
            echo '<td><select name="' . $radical_variable_champ . $nb . '" id="' . $radical_variable_champ . $nb . '">' . "\n";
            echo '<option value="-1">' . LG_IMP_CSV_COL_GEN . '</option>' . "\n";
            for ($nb2 = 2; $nb2 < $c_zbase; $nb2++) echo '<option value="' . $z_base[$nb2] . '">' . $z_base[$nb2] . '</option>';
            echo '</select></td>' . "\n";
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        /*
		echo '<table>';
		echo '<tr><td>';
		echo Affiche_Icone('tip','Conseil').' Ne sélectionnez les correspondances que si elles sont absentes du fichier en entrée';
		echo '</td></tr>';
		echo '</table>';
		*/
        echo '</td></tr>' . "\n";
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