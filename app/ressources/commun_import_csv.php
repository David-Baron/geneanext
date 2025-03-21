<?php
// Code commun générique pour l'import d'un fichier CSV

function insert_champs()
{
    global $champ_lib, $champ_table, $champ_classe, $entete, $num_ident, $fp, $deb_req, $deb_req_suite, $fin_req, $nb_enr, $nb_enr_crees, $radical_variable_champ, $radical_variable_csv, $modif;

    $c_zbase = count($champ_lib);
    for ($nb = 0; $nb < $c_zbase; $nb++)
        $num_csv[] = -1;

    // Balayage du fichier csv
    while (($arr = fgetcsv($fp, 1000, ';', '"')) !== false) {

        $nb_enr++;
        $req = '';
        $c_arr = count($arr);

        if ($nb_enr == 1) {

            $max_champs = 0;
            echo my_html(LG_IMP_CSV_REQ_FIELDS) . ' :<br />';
            $tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
            $max_champs = 0;

            $champ_lib_l = array_map('strtolower', $champ_lib);
            $o_A = ord('A');

            // Préparation de la requête par les champs saisis
            // et recherche de la colonne csv pour chaque champ demandé
            if ($entete != 'P') {
                for ($nb = 0; $nb < $c_zbase; $nb++) {
                    $nom_var = $radical_variable_champ;
                    $nom_var_def = $nom_var . $nb;
                    $$nom_var_def = strtolower(retourne_var_post($nom_var, $nb));
                    $x = array_search($$nom_var_def, $champ_lib_l);
                    if ($nb > 0) $deb_req = $deb_req . ',';
                    $deb_req = $deb_req . $champ_table[$x];
                    $num_csv[$x] = retourne_var_post($radical_variable_csv, $nb);
                    echo $tab . '"' . $$nom_var_def . '" ' . my_html(LG_IMP_CSV_IN_COL) . ' ' . chr($o_A + $num_csv[$x]) . '<br />';
                }
            }
            // Préparation de la requête par la correspondance des champs dans l'entête
            else {
                $champ_table_l = array_map('strtolower', $champ_table);
                $arr_l = array_map('strtolower', $arr);
                for ($nb = 0; $nb < $c_zbase; $nb++) {
                    // Recherche du libellé du champ
                    $cont = trim($arr_l[$nb]);
                    $x = array_search($cont, $champ_lib_l);
                    if ($x === false) $x = array_search($cont, $champ_table_l);
                    if ($x !== false) {
                        if ($nb > 0) $deb_req = $deb_req . ',';
                        $deb_req = $deb_req . $champ_table[$x];
                        $num_csv[$x] = $nb;
                        echo $tab . '"' . $champ_lib_l[$nb] . '" ' . my_html(LG_IMP_CSV_IN_COL) . ' ' . chr($o_A + $nb) . '<br />';
                    } else {
                        echo my_html(LG_IMP_CSV_ERR_MATCH_1 . $cont . LG_IMP_CSV_ERR_MATCH_2) . '<br />';
                    }
                }
            }
            // Ajout des constantes
            $deb_req = $deb_req . $deb_req_suite . ') values(';
        }

        if (($entete != 'P') or ($nb_enr > 1)) {
            if ($c_arr < $max_champs) {
                echo my_html(LG_IMP_CSV_ERROR_LINE) . ' ' . $nb_enr . ' : ' . $ligne . '<br />';
            } else {
                // Récupération des champs dans le fichier
                for ($nb = 0; $nb < $c_zbase; $nb++) {
                    if ($num_csv[$nb] > -1) {
                        $cont = rtrim($arr[$nb]);
                        if (($entete == 'A') or ($nb_enr > 1)) {
                            switch ($champ_classe[$nb]) {
                                case 'C':
                                    if ($cont != '') $cont = '"' . ajoute_sl($cont) . '"';
                                    break;
                                case 'L':
                                    if ($cont != '') $cont = '"' . ajoute_sl(strtolower($cont)) . '"';
                                    break;
                                case 'U':
                                    if ($cont != '') $cont = '"' . ajoute_sl(strtoupper($cont)) . '"';
                                    break;
                                case 'D':
                                    if ($cont != '') $cont = '"' . traite_date_csv($cont) . '"';
                                    break;
                            }
                            if ($cont == '') $cont = 'null';
                            if ($req != '') $req = $req . ',';
                            $req = $req . $cont;
                        }
                    }
                }
            }
            // Création de l'enregistrement	
            if (($entete == 'A') or ($nb_enr > 1)) {
                $z_ident = '';
                if ($num_ident != '') $z_ident = $num_ident . ',';
                $req = $deb_req . $z_ident . $req . $fin_req;
                $res = maj_sql($req);
                if ($res !== false) ++$nb_enr_crees;
                $modif = true;
                if ($num_ident != '') $num_ident++;
            }
        }
    }
}
