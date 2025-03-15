<?php

//=====================================================================
// Import ou lecture d'un fichier Gedcom
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

function suppression($lib, $n_table, $genre, $where, $affichage = true)
{
    global $enr_mod;
    if ($affichage) echo '  - ';
    $req = 'delete from ' . $n_table;
    if ($where != '') $req .= ' where ' . $where;
    $res = maj_sql($req);
    if ($affichage) {
        $lib_sup = '';
        if ($genre == 'm') {
            if ($enr_mod > 1) $lib_sup = LG_IMP_GED_DEL_MANY_M;
            else $lib_sup = LG_IMP_GED_DEL_1_M;
        } else {
            if ($enr_mod > 1) $lib_sup = LG_IMP_GED_DEL_MANY_F;
            else $lib_sup = LG_IMP_GED_DEL_1_F;
        }
        echo $enr_mod . ' ' . $lib . ' ' . my_html($lib_sup) . '<br />' . "\n";
    }
}

// Fonction de recctification UTF8
require(__DIR__ . '/app/ressources/rectif_utf8_commun.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'nom_du_fichier',
    'fic_utf8',
    'maj_oui',
    'diff_internet',
    'diff_internet_note',
    'diff_internet_img',
    'val_statut',
    'init_base',
    'reprise_date',
    'lieux',
    'Horigine',
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées - phase 1
$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Import GEDCOM';              // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
$niv_requis = 'G';                        // Page accessible au gestionnaire
require(__DIR__ . '/app/ressources/gestion_pages.php');          // Appel de la gestion standard des pages

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Sécurisation des variables postées - phase 2
$nom_du_fichier     = Secur_Variable_Post($nom_du_fichier, 100, 'S');
$fic_utf8           = Secur_Variable_Post($fic_utf8, 2, 'S');
$maj_oui            = Secur_Variable_Post($maj_oui, 2, 'S');
$diff_internet      = Secur_Variable_Post($diff_internet, 2, 'S');
$diff_internet_note = Secur_Variable_Post($diff_internet_note, 2, 'S');
$diff_internet_img = Secur_Variable_Post($diff_internet_img, 2, 'S');
$val_statut         = Secur_Variable_Post($val_statut, 1, 'S');
$init_base          = Secur_Variable_Post($init_base, 2, 'S');
$reprise_date       = Secur_Variable_Post($reprise_date, 2, 'S');
$lieux              = Secur_Variable_Post($lieux, 150, 'S');

// Table de correspondance Ansel / Ansi
$c_ansi  = 'éèêëóòôöáàâäúùûüíìîïýÿçñÉÈÊËÓÒÔÖÁÀÂÄÚÙÛÜÍÌÎÏÝYÇÑ';
$c_ansel = 'âeáeãeèeâoáoãoèoâaáaãaèaâuáuãuèuâiáiãièiâyèyðc~nâEáEãEèEâOáOãOèOâAáAãAèAâUáUãUèUâIáIãIèIâYèYðC~N';

// Valeurs par défaut des codes département et région
$code_defaut_depart = 'xxx';
$code_defaut_region = 99999;

function init_format_lieux()
{
    global     $lieux, $format_lieux, $nb_format_lieux, $p_ville, $p_code_postal, $p_depart, $p_region, $p_pays;

    // Prise en charge du format des lieux
    if ($lieux != '') $format_lieux = explode(',', $lieux);
    else $format_lieux[] = 'ville';
    $nb_format_lieux = count($format_lieux) - 1;

    // Format pris en charge au moment de l'insert de la ville
    // Ville, code postal, département, région, pays
    $p_ville = -1;
    $p_code_postal = -1;
    $p_depart = -1;
    $p_region = -1;
    $p_pays = -1;
    for ($nb = 0; $nb < $nb_format_lieux; ++$nb) {
        $zone = $format_lieux[$nb];
        if ($zone == 'ville') $p_ville = $nb;
        if ($zone == 'code postal') $p_code_postal = $nb;
        if ($zone == 'département') $p_depart = $nb;
        if ($zone == 'région') $p_region = $nb;
        if ($zone == 'pays') $p_pays = $nb;
    }
}

// Conversion de l'Ansel vers l'Ansi
function Ansel_ANSI($Entree)
{
    global $c_ansi, $c_ansel;
    $Sortie = '';
    $long = strlen($Entree);
    for ($nb = 0; $nb < $long; $nb++) {
        $posi = strpos($c_ansel, substr($Entree, $nb, 2));
        // Extrait non trouvé dans les correspondances ANSEL
        if ($posi === false) {
            $Sortie .= $Entree[$nb];
        } else {
            // C'est de l'ANSEL si la position est paire
            if (pair($posi)) {
                $Sortie .= $c_ansi[$posi / 2];
                $nb++;
            } else
                $Sortie .= $Entree[$nb];
        }
    }
    return $Sortie;
}


// La ligne est de type <niveau> <précision>DATE <type de calendrier> <date>
// Type de calendrier :
//  @#DGREGORIAN@  <DATE_GREG>
//  @#DJULIAN@     <DATE_JULN>
//  @#DHEBREW@     <DATE_HEBR>
//  @#DFRENCH R@   <DATE_FREN>
function traite_date($str)
{
    global $arr, $Mois_Abr, $ListeAnneesRev, $MoisRevAbr4;

    //Mois non trouvé sur la date
    //2 DATE @#DFRENCH R@ 04 FRIM 9 , merci de prendre contact avec l'auteur en lui donnant la date en erreur.
    //2 DATE @#DFRENCH R@ 4 VENT an IX
    //$MoisRevAbr4 = 'VENDBRUMFRIMNIVOPLUVVENTGERMFLORPRAIMESSTHERFRUCSANC';
    //$str = substr($str,6);

    // Initialisation des variables
    $Annee = '';
    $Mois = '';
    $Jour = '';
    $type_cal = '';
    $Precision = '';

    // Détermination de la précision ; si présente, c'est la 3ème zone de la ligne
    $c_arr = count($arr);
    if ($c_arr > 2) {
        switch (trim($arr[2])) {
            case 'ABT':
            case 'CAL':
            case 'EST':
                $Precision = 'E';
                break;
            case 'BEF':
                $Precision = 'A';
                break;
            case 'AFT':
                $Precision = 'P';
                break;
            case 'BET':
                $Precision = 'P';
                $p1 = strpos($str, 'AND ');
                $str = substr($str, 0, $p1 - 1);
                $arr = explode(' ', $str);
                $c_arr = count($arr);
                break;
            default:
                $Precision = 'L';
        }
    }
    if ($Precision == 'L') $deb_date = 2;
    else                   $deb_date = 3;

    // Le type de calendrier est-il renseigné ?
    $p1 = strpos($str, '@#');
    if ($p1 != 0) {
        $p2 = strpos($str, ' ', $p1);
        // Seuls le grégorien et le français sont pris en charge (le julien est considéré comme étant le grégorien...)
        $type_date = substr($str, $p1, $p2 - $p1);
        switch ($type_date) {
            case '@#DGREGORIAN@':
            case '@#DJULIAN@':
                $type_cal = 'G';
                $deb_date++;
                break;
            case '@#DFRENCH':
                $type_cal = 'R';
                $deb_date += 2;
                break;
            default:
                $type_cal = 'X';
                $deb_date++;
        }
    }

    // Détermination du premier champ date
    $ch_date = $deb_date;
    $nb_zones_date = $c_arr - $ch_date;
    // Cas particulier des dates révolutionnaires, suppression de la zone an
    if ($type_cal == 'R') {
        if (strtoupper($arr[$c_arr - 2]) == 'AN') {
            $arr[$c_arr - 2] = $arr[$c_arr - 1];
            $nb_zones_date--;
        }
    }
    //echo 'Date : '.$str.',';
    //echo ' ** type_cal '.$type_cal.', précision '.$Precision.', ch_date '.$ch_date.', nb_zones_date '.$nb_zones_date.'<br>';
    // 3 champs => jour mois année
    if ($nb_zones_date == 3) {
        $Jour = $arr[$ch_date];
        $Jour = sprintf('%02s', $Jour);
        $Mois = trim($arr[1 + $ch_date]);
        $Annee = $arr[2 + $ch_date];
    }
    // 2 champs => mois année
    else if ($nb_zones_date == 2) {
        if ($Precision == 'L') $Precision = 'E';
        $Jour = '01';
        $Mois = trim($arr[$ch_date]);
        $Annee = $arr[1 + $ch_date];
    }
    // 1 champ  => année
    else if ($nb_zones_date == 1) {
        if ($Precision == 'L') $Precision = 'E';
        $Jour = '01';
        // Mois par défaut : vendémiaire si révolutionnaire, 01 pour janvier sinon
        if ($type_cal == 'R') $Mois = substr($MoisRevAbr4, 0, 4);
        else                  $Mois = '01';
        $Annee = $arr[$ch_date];
    }

    // Attention si type calendrier incorrect ou calendrier rév ******************
    // Attention si calendrier rév ******************
    if ($type_cal != 'R') {
        $Mois  = str_pad(array_search(strtoupper($Mois), $Mois_Abr) + 1, 2, '0', STR_PAD_LEFT);
        $Annee = str_pad(trim($Annee), 4, '0', STR_PAD_LEFT);
    } else {
        // Les mois complémentaires correspondent aux sansculotides
        if ($Mois == 'COMP') $Mois = 'SANC';
        $rech = strpos($MoisRevAbr4, $Mois);
        // Alim mois en fonction du retour de la recherche ; si KO : 0000
        if ($rech === false) {
            echo 'Mois KO : ' . $Mois . ' jour : ' . $Jour . '<br>';
            $Mois = 0;
            echo 'Mois non trouvé sur la date ' . $str . ', merci de prendre contact avec l\'auteur en lui donnant la date en erreur.<br>';
        } else
            $Mois = ($rech / 4) + 1;
        // Si l'année est alpha, on va chercher l'équivalent en numérique
        $Annee = trim($Annee);
        if (!is_numeric($Annee)) {
            $rech = array_search($Annee, $ListeAnneesRev);
            // Alim année en fonction du retour de la recherche ; si KO : 0000
            if ($rech === false) $Annee = 0;
            else                 $Annee = $rech + 1;
        }
        //echo "Jour : $Jour, mois : $Mois, année : $Annee<br>";
        $nombre = frenchtojd($Mois, $Jour, $Annee);
        $LaDate = jdtogregorian($nombre);
        $s1 = strpos($LaDate, '/', 1);
        $s2 = strpos($LaDate, '/', $s1 + 1);
        $Jour  = str_pad(substr($LaDate, $s1 + 1, $s2 - $s1 - 1), 2, '0', STR_PAD_LEFT);
        $Mois = str_pad(substr($LaDate, 0, $s1), 2, '0', STR_PAD_LEFT);
        $Annee  = substr($LaDate, $s2 + 1, 4);
    }

    // Attention si type calendrier incorrect ou calendrier rév ******************
    if ($type_cal == '') $type_cal = 'G';
    $ret = $Annee . $Mois . $Jour . $type_cal . $Precision;

    // Traitement résiduel des dates incertaines : exemple 2 DATE (ABT 1784) donne 1784)0101GE ==> il faut supprimer les parenthèses
    $ret = str_replace(')', '', $ret);
    $ret = str_replace('(', '', $ret);

    if ($ret == '000001G') $ret = '';
    if ($ret == '000001XL') $ret = '';
    // RAZ des dates qui ne sont pas sur 10 en sortie, donc à priori non correctement traitées
    if (strlen($ret) != 10) {
        $ret = '';
    } else {
        // Rattrapage des dates approximatives
        if (strpos($str, '(') !== false) {
            $ret[9] = 'E';
        }
    }

    // echo 'Trait date : '.$str.', retour : '.$ret.'</br>';
    return $ret;
}

// Transforme une date Gedcom en format DateTime
function traite_date_dt($arr)
{
    // 0000-00-00 00:00:00
    global $Mois_Abr;
    $Jour   = sprintf('%02s', $arr[2]);
    $Mois  = sprintf('%02s', array_search(strtoupper($arr[3]), $Mois_Abr) + 1);
    $Annee = sprintf('%04s', trim($arr[4]));
    return '\'' . $Annee . '-' . $Mois . '-' . $Jour . ' 00:00:00\'';
}

function rech_zone($tableau_lib, $tableau_ref, $posi, $table, $nom_zone, $niveau)
{
    global
        $Codage_ANSEL, $lieu_arr, $Init, $db, $sZone,
        $lib_villes, $lib_departements, $lib_regions, $lib_pays,
        $ref_villes, $ref_departements, $ref_regions, $ref_pays, $memo_lib_ville, $memo_ville, $existe_villes, $debug;

    $la_zone = trim($lieu_arr[$posi]);

    if ($debug) {
        $lieu_arr_str = implode($lieu_arr);
        echo 'lieu_arr : ' . $lieu_arr_str . '<br />';
        echo 'posi : ' . $posi . ', ';
        echo 'Niveau : ' . $niveau . '/' . $la_zone . '<br />';
    }

    $zone_geo = -1;

    if ($niveau == 4) {
        //echo $la_zone.'/'.$memo_lib_ville.'<br />';
        if ($la_zone == $memo_lib_ville) {
            $zone_geo = $memo_ville;
            //echo 'pas de recherche...<br />';
        }
        $memo_lib_ville = $la_zone;
    }

    if ($zone_geo == -1) {
        if ($Codage_ANSEL) $la_zone = Ansel_ANSI($la_zone);

        // Si la zone a déjà été analysée, on a son identifiant
        $x = false;
        if (isset($tableau_lib)) $x = array_search($la_zone, $tableau_lib);
        //if ($debug) for ($nb=0;nb<count($tableau_lib);$nb++) echo $nb.':'.$tableau_lib[$nb].'<br />';
        // La zone a été trouvée dans le tableau
        if ($x !== false) {
            $zone_geo = $tableau_ref[$x];
            if ($debug) echo 'Zone trouvée dans tableau : ' . $la_zone . ', ' . $x . '/' . $zone_geo . '<br>';
        }
        // Sinon on la recherche en base et éventuellement on la crée et on attribue un nouvel identifiant
        else {
            switch ($niveau) {
                case 4:
                    $lib_villes[]       = $la_zone;
                    break;
                case 3:
                    $lib_departements[] = $la_zone;
                    break;
                case 2:
                    $lib_regions[]      = $la_zone;
                    break;
                case 1:
                    $lib_pays[]         = $la_zone;
                    break;
                    //case 1 : $lib_pays[]         = $la_zone;  $ref_pays[] = 0 ; break;
            }
            $sZone = ajoute_sl_rt($la_zone);
            $trouve = 0;
            // Recherche en base de la zone ; la recherche n'est nécessaire que si la base n'a pas été ré-initialisée
            if ((!$Init) or ($niveau != 4)) {
                $doit_rech = true;
                // Si on n'est pas en init mais qu'il n'y a pas de villes, inutile de chercher en base
                if (($niveau == 4) and (!$Init) and (!$existe_villes))
                    $doit_rech = false;
                if ($doit_rech) {
                    $sql = 'select Identifiant_zone from ' . $table . ' where ' . $nom_zone . ' = \'' . $sZone . '\' limit 1';
                    if ($debug) echo 'rech <br />';
                    if ($res = lect_sql($sql)) {
                        if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                            $zone_geo = $enreg[0];
                            switch ($niveau) {
                                case 4:
                                    $ref_villes[]       = $zone_geo;
                                    break;
                                case 3:
                                    $ref_departements[] = $zone_geo;
                                    break;
                                case 2:
                                    $ref_regions[]      = $zone_geo;
                                    break;
                                case 1:
                                    $ref_pays[]         = $zone_geo;
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }
    if ($niveau == 4) $memo_ville = $zone_geo;
    if ($debug) echo 'val retour = ' . $zone_geo . '<br />';
    return $zone_geo;
}

// La ligne est de type <niveau> PLAC <lieu>
// Le lieu est constitué d'une liste de valeur séparées par des ,
function traite_lieu($str)
{
    global
        $root,
        $debug, $maj_oui,
        $nb_enr,
        $ref_villes, $ref_departements, $ref_regions, $ref_pays,
        $id_ville, $id_depart, $id_region,
        $db, $Ins_Statut_Z, $Maj,
        $Init, $sZone,
        $nb_format_lieux, $p_ville, $p_code_postal, $p_depart, $p_region, $p_pays,
        $n_villes, $n_departements, $n_regions, $n_pays,
        $lib_villes, $lib_departements, $lib_regions, $lib_pays,
        $lieu_arr,
        $code_defaut_depart, $code_defaut_region;

    $zone_geo = 0;
    $zone_geo_ville  = 0;
    $zone_geo_depart = 0;
    $zone_geo_region = 0;
    $zone_geo_pays   = 0;
    // Top de création des zones
    $creation_depart = false;
    $creation_region = false;
    // On ne peut traiter que si la ville fait partie du format, vu que l'on ne peut rattacher à autre chose
    if ($maj_oui == 'on') {
        if ($p_ville != -1) {

            if ($debug) echo 'Lieu à traiter : ' . $str . '<br>';
            $ex_lieu = substr($str, 6);
            if ($debug) echo 'Lieu à traiter, extr : ' . $ex_lieu . '<br>';
            $lieu_arr = explode(',', $ex_lieu);
            $c_lieux = count($lieu_arr);

            // Incohérence du nombre de zones par rapport à l'attendu
            if ($c_lieux != $nb_format_lieux) {
                echo '<img src="' . $root . '/assets/img/error.png" alt="Avertissement"/>La ligne (' . $nb_enr . ') ' . $str . ' ne comporte pas le nombre de zones prévues pour les zones géographiques<br>';
            }

            $ident_base_ville = 0;
            $ident_base_depart = 0;
            $ident_base_region = 0;
            $ident_base_pays = 0;
            $zone_ville = '';
            $zone_depart = '';
            $zone_region = '';
            $zone_pays = '';
            $code_postal = 'null';

            $lg_cp = 0;
            $err_cp = false;

            if ($c_lieux > 0) {
                // La ville existe-t-elle en base ou a-t-elle déjà été analysée ? Si non, retour = -1
                $ident_base_ville = rech_zone($lib_villes, $ref_villes, $p_ville, $n_villes, 'Nom_Ville', 4);
                $zone_geo_ville = $ident_base_ville;
                //echo 'ident_base_ville : '.$ident_base_ville.'<br>';

                if (($Maj) and ($ident_base_ville == -1)) {
                    // On ne peut déterminer le code postal que si l'on a correspondance sur le nombre de zones géographiques
                    if (!($p_code_postal == -1) and ($p_code_postal < $c_lieux) and ($c_lieux == $nb_format_lieux)) {
                        $code_postal = trim($lieu_arr[$p_code_postal]);
                    }
                    if ($code_postal == '') {
                        $code_postal = 'null';
                    }
                    if ($code_postal != 'null') {
                        $lg_cp = strlen($code_postal);
                    }
                    // Contrôle de cohérence du code postal pour la France
                    if (($p_pays != -1) and ($p_pays < $c_lieux)) {
                        // var_dump($p_pays);
                        // var_dump($c_lieux);
                        if (strtoupper(trim($lieu_arr[$p_pays])) == 'FRANCE') {
                            if ($code_postal != 'null') {
                                if ((!is_numeric($code_postal)) or ($lg_cp <> 5)) {
                                    echo '<img src="' . $root . '/assets/img/error.png" alt="Avertissement"/>Attention, code postal incohérent (' . $code_postal . ') sur la ligne (' . $nb_enr . ') ' . $str . '<br>';
                                    $code_postal = 'null';
                                    $err_cp = true;
                                }
                            }
                        }
                    }
                }
                // Nb : le addslashes est dans la recherche
                $zone_ville = $sZone;
                // Si la ville doit être créée, doit-on créer les zones de niveau supérieur ?
                if (!$err_cp) {
                    if (($ident_base_ville == -1) and ($c_lieux == $nb_format_lieux)) {
                        if (($p_depart != -1) and ($ident_base_ville == -1)) {
                            // On va chercher le département
                            $ident_base_depart = rech_zone($lib_departements, $ref_departements, $p_depart, $n_departements, 'Nom_Depart_Min', 3);
                            //echo 'ident_base_depart : '.$ident_base_depart.'<br>';
                            $zone_depart = $sZone;
                            // On va chercher la région
                            if (($p_region != -1) and ($ident_base_depart == -1)) {
                                $ident_base_region = rech_zone($lib_regions, $ref_regions, $p_region, $n_regions, 'Nom_Region_Min', 2);
                                //echo 'ident_base_region : '.$ident_base_region.'<br>';
                                $zone_region = $sZone;
                                // On va chercher le pays
                                if (($p_pays != -1) and ($ident_base_region == -1)) {
                                    $ident_base_pays = rech_zone($lib_pays, $ref_pays, $p_pays, $n_pays, 'Nom_Pays', 1);
                                    //if ($ident_base_pays == -1) $ident_base_pays = 0;
                                    //echo 'ident_base_pays : '.$ident_base_pays.'<br>';
                                    $zone_pays = $sZone;
                                }
                            }
                        }
                    }
                }

                // Création de la région
                if (($Maj) and (! $err_cp) and ($ident_base_region == -1)) {
                    $ref_regions[] = ++$id_region;
                    $req = 'insert into ' . $n_regions . ' values(' .
                        $id_region . ',' . $code_defaut_region . ',' . '\'' . $zone_pays . '\',current_timestamp,current_timestamp' . $Ins_Statut_Z . $ident_base_pays . ')';;
                    $res = maj_sql($req);
                    $ident_base_region = $id_region;
                }

                // Création du département
                if (($Maj) and (! $err_cp) and ($ident_base_depart == -1)) {
                    $ref_departements[] = ++$id_depart;
                    $req = 'insert into ' . $n_departements . ' values(' .
                        $id_depart . ',\'' . $code_defaut_depart . '\',' . '\'' . $zone_depart . '\',current_timestamp,current_timestamp' . $Ins_Statut_Z . $ident_base_region . ')';
                    $res = maj_sql($req);
                    $ident_base_depart = $id_depart;
                }

                // Création de la ville en base
                if (($Maj) and ($ident_base_ville == -1)) {
                    $ref_villes[] = ++$id_ville;
                    $zone_geo_ville = $id_ville;
                    $memo_ville = $id_ville;
                    if ($code_postal != 'null') {
                        if ($lg_cp > 10) {
                            echo '<img src="' . $root . '/assets/img/error.png" alt="Avertissement"/>Attention, code postal tronqué (' . $code_postal . ') sur la ligne (' . $nb_enr . ') ' . $str . '<br>';
                            $code_postal = substr($code_postal, 0, 10);
                        }
                    }
                    if ($code_postal != 'null') $code_postal = '\'' . $code_postal . '\'';
                    // Création de la ville en base
                    $req = 'insert into ' . $n_villes .
                        ' values(' . $id_ville . ',' . '\'' . $zone_ville . '\',' . $code_postal . ',current_timestamp,current_timestamp' . $Ins_Statut_Z . $ident_base_depart . ',null,null)';
                    $res = maj_sql($req);
                }
            }
        }
    }
    // Lecture seule
    else {
        $zone_geo_ville = $ex_lieu = substr($str, 6);
    }
    return $zone_geo_ville;
}

if ($bt_OK) Ecrit_Entete_Page($titre, '', '');

$compl = Ajoute_Page_Info(600, 300);
Insere_Haut($titre, $compl, 'Import_Gedcom', '');

//Demande de chargement
if ($ok == 'OK') {

    require(__DIR__ . '/app/ressources/fonctions_maj.php');

    $temps = time();
    $jour = date('j', $temps);  //format numerique : 1->31
    $annee = date('Y', $temps); //format numerique : 4 chiffres
    $mois = date('m', $temps);
    $heure = date('H', $temps);
    $minutes = date('i', $temps);
    $sec = date('s', $temps);
    $debut = $jour . '/' . $mois . '/' . $annee . ' &agrave; ' . $heure . 'h' . $minutes . ':' . $sec;

    // Pas de limite de temps en local
    // Sur le net, limite fixée à la valeur paramétrée ; plus importante sur les sites Premium
    if ($Environnement == 'L') {
        set_time_limit(0);
    }
    if ($SiteGratuit) {
        set_time_limit($lim_temps);
    }

    $Statut_Fiche = $val_statut;
    $erreur = 'x';
    $msg = '';
    $Codage_ANSEL = 0;
    $msg .= ' Fichier demandé : ' . $_FILES['nom_du_fichier']['name'];
    echo $msg . '<br />';
    if ($fic_utf8 == 'on')
        echo $LG_Ch_UTF8 . '<br />';

    $erreur = false;

    $tmp_file = $_FILES['nom_du_fichier']['tmp_name'];
    $nom_du_fichier = $_FILES['nom_du_fichier']['name'];

    // Une demande de chargement a été faite
    if ($nom_du_fichier != '') {

        $erreur = ctrl_fichier_ko();

        if (!$erreur) {
            // Seuls sont autorisés les fichiers ged
            if (Extension_Fic($nom_du_fichier) != 'ged') {
                echo '<center><font color="red"><br><br><br><h2>' . LG_IMP_GED_ERR_TYPE . '</h2></font></center>';
            }
        }

        // On peut télécharger s'il n'y a pas d'erreur
        if (!$erreur) {

            // Optimisation : calcul des noms de tables
            $n_filiations     = nom_table('filiations');
            $n_unions         = nom_table('unions');
            $n_participe      = nom_table('participe');
            $n_personnes      = nom_table('personnes');
            $n_evenements     = nom_table('evenements');
            $n_villes         = nom_table('villes');
            $n_departements   = nom_table('departements');
            $n_regions        = nom_table('regions');
            $n_pays           = nom_table('pays');
            $n_commentaires   = nom_table('commentaires');
            $n_noms           = nom_table('noms_famille');
            $n_liens_noms     = nom_table('noms_personnes');
            $n_images         = nom_table('images');
            $n_sources        = nom_table('sources');
            $n_conc_source    = nom_table('concerne_source');
            $n_depots         = nom_table('depots');
            $n_concerne_objet = nom_table('concerne_objet');

            $path = $chemin_Gedcom . $nom_du_fichier;
            move_uploaded_file($tmp_file, $path);

            $pec_obje = false;
            // Indicateur de présence d'un objet image sur une personne
            $ind_obj_img =  false;

            // Traitement du fichier
            if ($fp = fopen($path, 'r')) {

                // Prise en charge du format des lieux
                init_format_lieux();

                $Cre_Noms = false;

                // Demande de mise à jour ou non ?
                $Maj = ($maj_oui == 'on') ? true : false;

                // Demande d'initialisation de la base ou non ?
                $Init = ($init_base == 'on') ? true : false;

                // Demande de reprise des dates ou non
                $Rep_date = ($reprise_date == 'on') ? true : false;

                if ($Maj) echo 'Demande de mise &agrave; jour des données<br />';
                else      echo 'Demande de lecture des données<br />';

                if (($Maj) and ($Init)) {

                    echo 'Demande de suppression des données de la base :<br />';
                    suppression('filiation', $n_filiations, 'f', '');
                    suppression('union', $n_unions, 'f', '');
                    suppression('image', $n_images, 'f', 'Type_Ref  in ("P","V","U")');
                    suppression('participation', $n_participe, 'f', '', false);
                    suppression('personne', $n_personnes, 'f', 'reference > 0');
                    suppression('nom', $n_noms, 'm', '');
                    suppression('lien nom', $n_liens_noms, 'm', '', false);
                    suppression('év&egrave;nement', $n_evenements, 'm', 'Code_Type <> "AC3U"');
                    suppression('ville', $n_villes, 'f', 'identifiant_zone > 0');
                    suppression('commentaire', $n_commentaires, 'm', '(Type_Objet <> "G" and Type_Objet <> "E")' .
                        ' or (Type_Objet = "E" and Reference_Objet not in (select Reference from ' . $n_evenements . ' where Code_Type = "AC3U"))');
                    suppression('dép&ocirc;t', $n_depots, 'm', 'Ident > 0');
                    suppression('source', $n_sources, 'f', '');
                    suppression('lien source', $n_conc_source, 'm', '', false);
                    suppression('lien doc', nom_table('concerne_doc'), 'm', 'Type_Objet  in ("P","V","U")', false);
                    suppression('lien objet', $n_concerne_objet, 'm', 'Type_Objet  in ("P","V","U")', false);

                    // RàZ des variables de session
                    if (isset($_SESSION['decujus']))
                        unset($_SESSION['decujus']);
                    if (isset($_SESSION['mem_pers']))
                        unset($_SESSION['mem_pers']);
                }
                if ($Maj) {

                    // Si l'init n'a pas été demandé, on vérifie s'il y a des villes ==> pour optimiser recherche ultérieure
                    if (!$Init) {
                        $existe_villes = false;
                        $num_ville = 0;
                        $sql = 'select Identifiant_zone from ' . nom_table('villes') . ' where Identifiant_zone <> 0 limit 1';
                        if ($res = lect_sql($sql)) {
                            if ($ville = $res->fetch(PDO::FETCH_NUM)) {
                                $num_ville = $ville[0];
                            }
                            $res->closeCursor();
                        }
                        if ($num_ville != 0) $existe_villes = true;
                        //if ($existe_villes) echo 'existe ville<br /> '; else echo 'pas de villes en base<br />';
                    }

                    // Optimisation
                    $deb_ins_evt = 'INSERT INTO ' . $n_evenements .
                        ' (Identifiant_zone, Identifiant_Niveau, Code_Type,Titre, Debut,Fin,Date_Creation,Date_Modification,Statut_Fiche)' .
                        ' values (';

                    $num_ev = -1;
                    $z1 = '';
                    // Recherche des types Gedcom pour les personnes et les unions
                    if (isset($types_evenements)) unset($types_evenements);
                    if (isset($titres_evenements)) unset($titres_evenements);
                    $req = 'SELECT Code_Type, Libelle_Type from ' . nom_table('types_evenement') .
                        ' where Type_Gedcom = "O" and (Objet_Cible in ("P","U"))';
                    $types_evenements = '';
                    if ($resEv = lect_sql($req)) {
                        while ($enrEv = $resEv->fetch(PDO::FETCH_NUM)) {
                            $titre = $enrEv[0];
                            if (strlen($titre) == 3) $titre .= ' ';
                            $types_evenements .= $titre . '/';
                            $titres_evenements[] = $enrEv[1];
                        }
                        $resEv->closeCursor();
                    }
                    /*echo 'Liste Ged '.$evt_Ged.'<br>';
            for ($nb_ev = 0;$nb_ev < count($types_evenements); $nb_ev++) {
              echo $types_evenements[$nb_ev].'/'.$titres_evenements[$nb_ev].'<br>';
            }*/
                    // Rappel des options demandées
                    echo 'Visibilité Internet ';
                    if ($diff_internet != 'on') echo 'non ';
                    echo 'autorisée par défaut<br />';
                    echo 'Visibilité Internet des notes ';
                    if ($diff_internet_note != 'on') echo 'non ';
                    echo 'Visibilité Internet des images ';
                    if ($diff_internet_img != 'on') echo 'non ';
                    echo 'autorisée par défaut<br />';
                    echo 'Statut par défaut des fiches : ';
                    switch ($Statut_Fiche) {
                        case 'O':
                            echo 'validé';
                            break;
                        case 'N':
                            echo 'non validé';
                            break;
                        case 'I':
                            echo 'source internet';
                            break;
                    }
                    echo '<br />';
                    echo 'Reprise des dates de modification du fichier : ';
                    if ($Rep_date) echo 'oui';
                    else           echo 'non';
                }
                echo '<hr/>';
                // Recherche des premiers identifiants que l'on pourra attribuer
                if ($Maj) {
                    $ref_ind_base = Nouvel_Identifiant('Reference', 'personnes') - 1;
                    $id_ville = Nouvel_Identifiant('Identifiant_zone', 'villes') - 1;
                    $id_depart = Nouvel_Identifiant('Identifiant_zone', 'departements') - 1;
                    $id_region = Nouvel_Identifiant('Identifiant_zone', 'regions') - 1;
                    $id_image = Nouvel_Identifiant('ident_image', 'images') - 1;
                } else $ref_ind_base = 0;        // Pour supprimer un warning en log
                $section = '';
                $individus_ref[0] = 'x';
                $individus_ref_base[0] = 'x';
                //$lib_villes = '';
                $ref_ville = '';
                $nb_unions = 0;
                $nb_filiations = 0;
                $nb_enr = 0;

                // Arboresence des niveaux
                for ($nb = 0; $nb <= 99; $nb++) $arbo[$nb] = '';

                // Valeurs par défaut
                $Ins_Statut = ',\'' . $Statut_Fiche . '\')';
                $Ins_Statut_Z = ',\'' . $Statut_Fiche . '\',';
                $Ins_StatutP = ',\'' . $Statut_Fiche . '\',null,0';
                if ($diff_internet_note == 'on') $vdiff_internet_note = "O";
                else                             $vdiff_internet_note = "N";
                if ($diff_internet == 'on') $vdiff_internet = "'O',";
                else                        $vdiff_internet = "'N',";
                if ($Rep_date) $date_mod = "0000-00-00 00:00:00'";
                else           $date_mod = 'current_timestamp';
                if ($diff_internet_img == 'on') $vdiff_internet_img = "'o'";
                else                            $vdiff_internet_img = "'n'";

                $s_INDI = false;
                $s_FAM  = false;
                $fic_ansi = false;
                // Balayage du fichier
                while ($str = fgets($fp, 255)) {
                    // Conversion d'ANSI en UTF-8
                    if (($fic_ansi) and ($def_enc == 'UTF-8')) {
                        $str = iconv("ISO-8859-1", "UTF-8", $str);
                        // var_dump($str);			
                    }
                    $nb_enr++;

                    // Suppression des blancs initiaux
                    $str = trim($str);

                    // Détermination du niveau de la ligne
                    $p_blanc = strpos($str, ' ');
                    $niveau = intval(substr($str, 0, $p_blanc));

                    if ($debug) echo $nb_enr . ' :' . $str . '<br />';

                    // Traitement en rupture sur niveau 0
                    if (($niveau == 0) and ($nb_enr > 1)) {

                        // Création d'un dépôt
                        if ($s_REPO) {
                            if ($Codage_ANSEL) {
                                $D_Nom = Ansel_ANSI($D_Nom);
                            }
                            if ($Maj) {
                                $req = 'insert into ' . $n_depots . ' values(0,' .
                                    '\'' . ajoute_sl_rt($D_Nom) . '\',' .
                                    '\'' . $D_Ident_Depot_Tempo . '\'' .
                                    ');';
                                $res = maj_sql($req);
                            }
                        }

                        // Création d'une source
                        if ($s_SOUR) {
                            if ($Codage_ANSEL) {
                                $S_Titre = Ansel_ANSI($S_Titre);
                                $S_Auteur = Ansel_ANSI($S_Auteur);
                            }
                            if ($Maj) {
                                $req = 'insert into ' . $n_sources . ' values(0,' .
                                    '\'' . ajoute_sl_rt($S_Titre) . '\',' .
                                    '\'' . ajoute_sl_rt($S_Auteur) . '\',' .
                                    '\'' . ajoute_sl_rt($S_Classement) . '\',' .
                                    '0,' .        // Ident_Depot ==> on rattache par défaut au dépôt générique
                                    '\'' . ajoute_sl_rt($S_Ident_Depot_Tempo) . '\',' .
                                    '\'' . ajoute_sl_rt($S_Cote) . '\',' .
                                    'null,' .    // Adresse_Web
                                    'null,' .    // Fiabilite_Source
                                    '\'' . $S_Ident_Source_Tempo . '\'' .
                                    ');';
                                $res = maj_sql($req);
                            }
                        }

                        // Mise à jour ou affichage d'un individu
                        if ($s_INDI) {
                            if ($Codage_ANSEL) {
                                $nom = Ansel_ANSI($nom);
                                $prenoms = Ansel_ANSI($prenoms);
                                //$metier = Ansel_ANSI($metier);
                                $comment = Ansel_ANSI($comment);
                            }
                            // Demande de lecture uniquement
                            if (! $Maj) {
                                echo '<font color="blue">Données personne : ' .
                                    ' référence : ' . $ref_ind .
                                    ' nom : ' . $nom .
                                    ', prénoms : ' . $prenoms .
                                    ', sexe : ' . $sexe .
                                    ', date de naissance : ' . Etend_date($date_naissance) .
                                    ', date de déc&egrave;s : ' . Etend_date($date_deces) .
                                    //' date de bapt&ecirc;me'.$date_bapteme.
                                    ', lieu de naissance : ' . $lieu_naissance .
                                    ', lieu de déc&egrave;s : ' . $lieu_deces .
                                    //' métier : '.$metier.
                                    ', commentaire : ' . $comment .
                                    '</font><br />';
                            }
                            // Demande de mise à jour
                            else {
                                if ($nom == '') $nom = '?';
                                $req = 'insert into ' . $n_personnes . ' values(' . $ref_ind_base . ',' .
                                    '\'' . ajoute_sl_rt($nom) . '\',' .
                                    '\'' . ajoute_sl_rt($prenoms) . '\',' .
                                    '\'' . $sexe . '\',' .
                                    '\'\',' .
                                    '\'' . $date_naissance . '\',' .
                                    '\'' . $date_deces . '\',';
                                $req .=
                                    strval($lieu_naissance) . ',' .
                                    strval($lieu_deces) . ',';
                                $req .= $vdiff_internet . $date_mod . ',' . $date_mod . $Ins_StatutP;
                                if ($surnom == '') $surnom = 'NULL';
                                else $surnom = '\'' . ajoute_sl_rt($surnom) . '\'';
                                $req .= ',' . $surnom . ');';
                                $res = maj_sql($req);

                                // Insertion d'un commentaire
                                if ($comment != '') {
                                    insere_commentaire($ref_ind_base, 'P', $comment, $vdiff_internet_note, 'o');
                                    $res = maj_sql($req_comment);
                                }

                                // Traitement des évènements de la personne
                                if (isset($code_ev)) {
                                    $c_code_ev = count($code_ev);
                                    for ($nb_ev = 0; $nb_ev < $c_code_ev; $nb_ev++) {
                                        $la_date = $date_ev[$nb_ev];
                                        if ($la_date == '') $la_date = 'null';
                                        else                $la_date = '\'' . $la_date . '\'';
                                        //echo 'code : '.$code_ev[$nb_ev].', date : '.$la_date.', lieu : '.$lieu_ev[$nb_ev].'<br />';
                                        // Le titre est celui du type de l'évènement si vide
                                        $titre = $titre_ev[$nb_ev];
                                        if ($Codage_ANSEL) $titre = Ansel_ANSI($titre);
                                        if ($titre == '') {
                                            $le_code = $code_ev[$nb_ev];
                                            if (strlen($le_code) == 3) $le_code .= ' ';
                                            $x = strpos($types_evenements, $le_code);
                                            if ($x !== false) {
                                                $titre = $titres_evenements[$x / 5];
                                            }
                                        }
                                        if ($titre == '') $titre = 'null';
                                        else              $titre = '\'' . ajoute_sl_rt($titre) . '\'';

                                        $le_lieu_evt = $lieu_ev[$nb_ev];
                                        $le_niveau_evt = 0;
                                        // ON n'alimente le niveau géographique que si le lieu est renseigné ; ville...
                                        if ($le_lieu_evt != 0) $le_niveau_evt = 4;
                                        $req_ev = $deb_ins_evt .
                                            $le_lieu_evt . ',' . $le_niveau_evt . ',' .
                                            '\'' . $code_ev[$nb_ev] . '\',' .
                                            $titre . ',' .
                                            $la_date . ',' .
                                            'null, ' .
                                            $date_mod . ',' . $date_mod . $Ins_Statut;
                                        $res = maj_sql($req_ev);
                                        $req_ev = 'INSERT INTO ' . $n_participe .
                                            ' values (LAST_INSERT_ID(),' . $ref_ind_base . ',\'\',null,null,\'N\',0,0,\'n\')';
                                        $res = maj_sql($req_ev);
                                    }
                                    unset($code_ev);
                                    unset($titre_ev);
                                    unset($date_ev);
                                    unset($lieu_ev);
                                }
                                // Création des sources pour la personne
                                if (isset($sources)) {
                                    $nb_sources = count($sources);
                                    for ($nb = 0; $nb < $nb_sources; $nb++) {
                                        $req = 'INSERT INTO ' . $n_conc_source . ' VALUES(0,0,' . $ref_ind_base . ',\'' . $S_Type_Objet . '\',\'' . ajoute_sl_rt($sources[$nb]) . '\');';
                                        $res = maj_sql($req);
                                    }
                                    unset($sources);
                                }
                                if (isset($req_img)) {
                                    $c_img = count($req_img);
                                    for ($nb_im = 0; $nb_im < $c_img; $nb_im++) {
                                        $sql_im = 'insert into ' . $n_images . ' values(' . ++$id_image . ',' . $ref_ind_base . ',' . $req_img[$nb_im] . ")";
                                        $res = maj_sql($sql_im);
                                    }
                                    unset($req_img);
                                }
                            }
                        }
                        // Traitement d'une union
                        if ($s_FAM) {
                            // Demande de lecture uniquement
                            if (! $Maj) {
                                echo '<font color="green">Données mariage : ' .
                                    ' mari : ' . $mari .
                                    ', femme : ' . $femme .
                                    ', date de mariage : ' . Etend_date($date_mariage) .
                                    ', lieu de mariage : ' . $lieu_mariage .
                                    ', date de contrat de mariage : ' . Etend_date($date_K) .
                                    ', lieu de contrat de mariage : ' . $lieu_K .
                                    '</font><br />';
                            }
                            $num_mari  = strval($mari);
                            $num_femme = strval($femme);
                            // Création des filiations pour les unions
                            if (isset($enfants)) {
                                $c_enf = count($enfants);
                                for ($nb_enf = 0; $nb_enf < $c_enf; $nb_enf++) {
                                    if ($Maj) {
                                        $req = 'insert into ' . $n_filiations . ' values(' .
                                            $enfants[$nb_enf] . ',' .
                                            $num_mari . ',' .
                                            $num_femme .
                                            ',0,' . $date_mod . ',' . $date_mod .
                                            $Ins_Statut;
                                        $res = maj_sql($req, false);    // Sans plantage
                                    }
                                    $nb_filiations++;
                                }
                            }
                            // Demande de mise à jour
                            $ok_union = true;
                            if ($Maj) {
                                if (($num_mari != 0) and ($num_femme != 0)) {
                                    if ($Codage_ANSEL) {
                                        $comment = Ansel_ANSI($comment);
                                    }
                                    $req = 'insert into ' . $n_unions . ' values(0,' .
                                        $num_mari . ',' .
                                        $num_femme . ',' .
                                        '\'' . $date_mariage . '\',' .
                                        strval($lieu_mariage) . ',' .
                                        '\'' . $date_K . '\',' .
                                        '\'\',' .
                                        strval($lieu_K) . ',';
                                    $req .= $date_mod . ',' . $date_mod . $Ins_Statut;
                                    $res = maj_sql($req);
                                    $num_union = $connexion->lastInsertId();

                                    // Insertion d'un commentaire
                                    if ($comment != '') {
                                        insere_commentaire($num_union, 'U', $comment, $vdiff_internet_note, 'o');
                                        $res = maj_sql($req_comment);
                                    }
                                    /////////////////////////////
                                    // Traitement des évènements de l'union
                                    if (isset($code_ev)) {
                                        $c_code_ev = count($code_ev);
                                        for ($nb_ev = 0; $nb_ev < $c_code_ev; $nb_ev++) {
                                            $la_date = $date_ev[$nb_ev];
                                            if ($la_date == '') $la_date = 'null';
                                            else                $la_date = '\'' . $la_date . '\'';
                                            //echo 'code : '.$code_ev[$nb_ev].', date : '.$la_date.', lieu : '.$lieu_ev[$nb_ev].'<br />';
                                            // Le titre est celui du type de l'évènement si vide
                                            $titre = $titre_ev[$nb_ev];
                                            if ($Codage_ANSEL) $titre = Ansel_ANSI($titre);
                                            if ($titre == '') {
                                                $le_code = $code_ev[$nb_ev];
                                                if (strlen($le_code) == 3) $le_code .= ' ';
                                                $x = strpos($types_evenements, $le_code);
                                                if ($x !== false) {
                                                    $titre = $titres_evenements[$x / 5];
                                                }
                                            }
                                            if ($titre == '') $titre = 'null';
                                            else              $titre = '\'' . ajoute_sl_rt($titre) . '\'';

                                            $le_lieu_evt = $lieu_ev[$nb_ev];
                                            $le_niveau_evt = 0;
                                            // ON n'alimente le niveau géographique que si le lieu est renseigné ; ville...
                                            if ($le_lieu_evt != 0) $le_niveau_evt = 4;
                                            $req_ev = $deb_ins_evt .
                                                $le_lieu_evt . ',' . $le_niveau_evt . ',' .
                                                '\'' . $code_ev[$nb_ev] . '\',' .
                                                $titre . ',' .
                                                $la_date . ',' .
                                                'null, ' .
                                                $date_mod . ',' . $date_mod . $Ins_Statut;
                                            $res = maj_sql($req_ev);
                                            //Evenement
                                            //Reference_Objet
                                            //Type_Objet
                                            $req_ev = 'INSERT INTO ' . $n_concerne_objet .
                                                ' values (LAST_INSERT_ID(),' . $num_union . ',\'U\')';
                                            $res = maj_sql($req_ev);
                                        }
                                        unset($code_ev);
                                        unset($titre_ev);
                                        unset($date_ev);
                                        unset($lieu_ev);
                                    }
                                    /////////////////////////////
                                } else $ok_union = false;
                            }
                            if ($ok_union) $nb_unions++;
                        }
                        $msg = '';
                    }

                    $zone2 = rtrim(substr($str, $p_blanc + 1, 4));
                    //echo 'zone 2 : >'.$zone2.'<<br />';

                    // Type de ligne ?
                    $l_NOTE = false;
                    $l_CONT = false;
                    $l_CONC = false;
                    $l_PLAC = false;
                    $l_DATE = false;
                    $n_var = 'l_' . $zone2;
                    $$n_var = true;

                    // Pour les dates on supprime les doubles blancs
                    // Cas des jours < 10 sur 2 caractères dont 1 blanc initial (vu dans Elie 3.4 par exemple)
                    if ($l_DATE) $str = str_replace('  ', ' 0', $str);

                    $z1 = strtoupper($zone2);
                    //echo 'str : '.$str.'<br />';
                    if ((! $l_NOTE) and (! $l_CONT) and (! $l_CONC)) {
                        $arr = explode(' ', $str);
                    }

                    switch ($niveau) {
                        case 0:
                            $n0 = $zone2;
                            break;
                        case 1:
                            $n1 = $zone2;
                            break;
                        case 2:
                            $n2 = $zone2;
                            break;
                        case 3:
                            $n3 = $zone2;
                            break;
                    }

                    $arbo[$niveau] = $zone2;

                    // Enregistrements de niveau 0 possibles
                    if ($niveau == 0) {
                        if (count($arr) == 3) $zone2 = trim($arr[2]);
                        // enregistrement « personne » (INDI = individual)
                        // enregistrement « famille » (FAM = family)
                        // enregistrement « note » (NOTE = note)
                        // enregistrement « source » (SOUR = source)
                        // enregistrement « dépôt d'archives » (REPO = repository)
                        // enregistrement « objet multimédia » (OBJE = object)
                        // enregistremet  « entête » (HEAD)
                        $s_INDI = false;
                        $s_FAM  = false;
                        $s_NOTE = false;
                        $s_SOUR = false;
                        $s_REPO = false;
                        $s_OBJE = false;
                        $s_HEAD = false;

                        switch ($zone2) {
                            case 'INDI':
                                $s_INDI = true;
                                break;
                            case 'FAM':
                                $s_FAM  = true;
                                break;
                            case 'NOTE':
                                $s_NOTE = true;
                                break;
                            case 'SOUR':
                                $s_SOUR = true;
                                break;
                            case 'REPO':
                                $s_REPO = true;
                                break;
                            case 'OBJE':
                                $s_OBJE = true;
                                break;
                            case 'HEAD':
                                $s_HEAD = true;
                                break;
                        }

                        //echo 'Zone 2 : '.$zone2.'<br />';
                    }

                    // Traitement de la section HEAD ==> affichage
                    if (($s_HEAD) and ($niveau > 0)) {
                        //$sous_section
                        $sous_section = $z1;
                        // Sous-section de niveau 1
                        // Données à afficher : SOUR, DATE, GEDC, CHAR
                        if ($niveau == 1) {
                            $msg = '';
                            $ss_SOUR = 0;
                            $ss_DATE = 0;
                            $ss_GEDC = 0;
                            $ss_PLAC = 0;
                            if ($sous_section == 'SOUR') {
                                $ss_SOUR = 1;
                                $msg = '+Source du fichier : ' . my_html(trim($arr[2]));
                            }
                            if ($sous_section == 'DATE') {
                                $ss_DATE = 1;
                                $msg = 'Création du fichier : ';
                                $c_arr_date = count($arr);
                                if ($c_arr_date == 5) {
                                    $msg .= trim($arr[2]) . ' ';
                                    $x = array_search(strtoupper(trim($arr[3])), $Mois_Abr);
                                    if ($x !== FALSE) $msg .= $Mois_Lib[$x] . ' ';
                                    else    $msg .= 'mois inconnu ';
                                    $msg .= trim($arr[4]) . ' ';
                                    echo $msg . '<br>';
                                } else {
                                    if ($c_arr_date > 2) {
                                        for ($nb = 2; $nb <= $c_arr_date; $nb++) {
                                            $msg .= trim($arr[$nb]) . ' ';
                                        }
                                        echo $msg . ' - 2<br>';
                                    }
                                }
                            }
                            if ($sous_section == 'GEDC') {
                                $ss_GEDC = 1;
                                $msg = '';
                            }
                            // Codage des caractères
                            if ($sous_section == 'CHAR') {
                                $z2 = trim($arr[2]);
                                $msg = '+' . $LG_Ch_Encoding . ' : ' . $z2;
                                if ($z2 == 'ANSEL') $Codage_ANSEL = 1;
                                if ($z2 == 'UTF-8') $fic_utf8 = 'on';
                                if ($z2 == 'ANSI') $fic_ansi = true;
                            }
                            // Sous-section pour le format des lieux
                            if ($sous_section == 'PLAC') {
                                $ss_PLAC = 1;
                            }
                        }
                        // Sous-section de niveau 2
                        // Récupération des informations complémentaires
                        if (($niveau == 2) and (count($arr)) > 2) {
                            $z2 = trim($arr[2]);
                            if ($ss_SOUR == 1) {
                                if ($sous_section == 'VERS') $msg = '+Version de la source : ' . $z2;
                                else $msg = '';
                            }
                            if ($ss_DATE == 1) {
                                if ($sous_section == 'TIME') $msg = '+' . $msg . ' &agrave; ' . $z2;
                                else $msg = '';
                            }
                            if ($ss_GEDC == 1) {
                                if ($sous_section == 'VERS') $msg = '+' . $msg . 'GEDCOM version ' . $z2;
                                else $msg = '';
                            }
                            // Définition du format des lieux
                            if ($ss_PLAC) {
                                if ($sous_section == 'FORM') {
                                    $zones = substr($str, 7);
                                    if ($Codage_ANSEL) $zones = Ansel_ANSI($zones);
                                    $niveaux = explode(',', $zones);
                                    $lieux = '';
                                    $count = count($niveaux);
                                    // Balayage de l'arborescence présente dans le fichier
                                    for ($nb = 0; $nb < $count; $nb++) {
                                        $zone = strtolower(trim($niveaux[$nb]));
                                        switch ($zone) {
                                            case ('town'):
                                                $zone = 'ville';
                                                break;
                                            case ('area code'):
                                            case ('code lieu'):
                                                $zone = 'code postal';
                                                break;
                                            case ('county'):
                                                $zone = 'département';
                                                break;
                                            case ('region'):
                                                $zone = 'région';
                                                break;
                                            case ('country'):
                                                $zone = 'pays';
                                                break;
                                            case ('subdivision'):
                                                $zone = 'sous-division';
                                                break;
                                        }
                                        $lieux .= $zone . ',';
                                    }
                                    init_format_lieux();
                                }
                            }
                        }
                    }
                    if ((! $s_HEAD) and ($niveau == 0)) {
                        // On est devant une ligne de type <niveau> @référence@ <section>
                        if (count($arr) == 3) $section = trim($arr[2]);
                        // Init des zones pour un individu
                        if ($s_INDI) {
                            $ref_ind = $arr[1];
                            $individus_ref[] = $ref_ind;
                            $ref_ind_base++;
                            $individus_ref_base[] = $ref_ind_base;
                            $nom = '';
                            $prenoms = '';
                            $sex = '';
                            $date_naissance = '';
                            $date_bapteme = '';
                            $date_deces = '';
                            $lieu_naissance = 0;
                            $lieu_deces = 0;
                            $surnom = '';
                            $metier = '';
                            $comment = '';
                            $num_ev = -1;
                            $S_Type_Objet = 'P';
                            if (isset($sources)) unset($sources);
                        }
                        // Init des zones pour une famille
                        if ($s_FAM) {
                            $mari  = 0;
                            $femme = 0;
                            $date_mariage = '';
                            $lieu_mariage = 0;
                            $date_K = '';
                            $lieu_K = 0;
                            $comment = '';
                            $num_ev = -1;
                            unset($enfants);
                        }
                        // Init des zones pour une source
                        if ($s_SOUR) {
                            $S_Titre = '';
                            $S_Auteur = '';
                            $S_Classement = '';
                            $S_Ident_Depot_Tempo = '';
                            $S_Cote = '';
                            $S_Ident_Source_Tempo = $arr[1];
                        }
                        // Init des zones pour un dépôt
                        if ($s_REPO) {
                            $D_Nom = '';
                            $D_Ident_Depot_Tempo = $arr[1];
                        }
                    }
                    if (!$s_HEAD) {
                        // Traitement de la section INDI
                        //0 @I1@ INDI
                        if ($s_INDI) {
                            if ($niveau == 1) {
                                $sous_section = $z1;
                                $obj_ind = false;
                                $ssGed = false;
                                switch ($sous_section) {
                                    case 'NAME': // prénoms ou /nom/ ou prénoms/nom/ ou /nom/prénoms
                                        // le nom est entouré de //
                                        $pos2 = 0;
                                        $pos1 = strpos($str, '/', 6);
                                        if ($pos1) {
                                            $pos2 = strpos($str, '/', $pos1 + 1);
                                            if ($pos2 > 0) $nom = trim(substr($str, $pos1 + 1, $pos2 - $pos1 - 1));
                                            // /nom/prénoms ou /nom/ seul
                                            if ($pos1 == 7) $prenoms = trim(substr($str, $pos2 + 1));
                                            // prénoms/nom/
                                            else            $prenoms = trim(substr($str, 7, $pos1 - 7));
                                        }
                                        // prénoms tous seuls
                                        else
                                            $prenoms = trim(substr($str, 7));
                                        if ($nom == '')     $nom = '?';
                                        if ($prenoms == '') $prenoms = '?';
                                        break;
                                    case 'SEX':
                                        if (count($arr) > 2) $sexe = strtolower(trim($arr[2]));
                                        break;
                                    case 'NICK':
                                        $surnom = trim(substr($str, 7));
                                        break;
                                    case 'SOUR':
                                        $sources[] = trim(substr($str, 7));
                                        break;
                                    // Pour optimisation, rien à faire
                                    case 'BIRT':
                                    case 'CHAN':
                                    case 'NOTE':
                                    case 'FAMS':
                                    case 'FAMC':
                                    case 'DEAT':
                                        break;
                                    default:
                                        if ($Maj) {
                                            // S'agit-il d'un évènement Gedcom reconnu ?
                                            $x = strpos($types_evenements, $sous_section);
                                            if ($x !== false) {
                                                ++$num_ev;
                                                $code_ev[] = $sous_section;
                                                $titre_ev[] = rtrim(substr($str, 7));
                                                $date_ev[] = '';
                                                $lieu_ev[] = 0;
                                                $ssGed = true;
                                                //echo '<b>sous section gedcom : '.$sous_section.'</b>, num_ev'.$num_ev.'<br />';
                                                if ($sous_section == 'OCCU') $metier = trim(substr($str, 7));
                                            }
                                        }
                                }
                            }
                            if ($niveau == 2) {
                                //echo 'sous-section : '.$sous_section.', z1 : '.$z1.'<br />';
                                switch ($sous_section) {
                                    case 'BIRT':
                                        if ($z1 == 'DATE') $date_naissance = traite_date($str);
                                        else {
                                            if ($z1 == 'PLAC') $lieu_naissance = traite_lieu($str);
                                        }
                                        break;
                                    case 'DEAT':
                                        if ($z1 == 'DATE') $date_deces = traite_date($str);
                                        else {
                                            if ($z1 == 'PLAC') $lieu_deces = traite_lieu($str);
                                        }
                                        break;
                                    //case 'BAPM' :
                                    //case 'CHR'  : if ($z1 == 'DATE') $date_bapteme = traite_date($str);
                                    //              break;
                                    case 'CHAN':
                                        if (($z1 == 'DATE') and ($Rep_date)) $date_mod = traite_date_dt($arr);
                                        break;
                                    default:
                                        if ($ssGed == 1) {
                                            //echo '<i>sous section gedcom : '.$sous_section.'</i>, num_ev'.$num_ev.'<br />';
                                            // Récupération des dates et lieux d'un évènement Gedcom
                                            if ($z1 == 'DATE') {
                                                $date_ev[$num_ev] = traite_date($str);
                                            }
                                            if ($z1 == 'PLAC') $lieu_ev[$num_ev] = traite_lieu($str);
                                        }
                                        break;
                                }
                            }
                            // Traitement particulier des notes sur les personnes
                            // Niveau 1 : directement sous la personne
                            // Niveau 2 : on prend en compte si rattaché à la naissance ou le décès
                            //echo 'sous-section : '.$sous_section.', niveau : '.$niveau.'/'.$zone2.'<br />';
                            if ($zone2 == 'NOTE') {
                                $pec_note = false;
                                if ($niveau == 1) {
                                    $pec_note = true;
                                    if ($comment != '') $comment .= '<br />';
                                    $comment .= 'Note sur la personne :<br />' . rtrim(substr($str, 7));
                                }
                                if ($niveau == 2) {
                                    $n1 = $niveau - 1;
                                    //echo 'arbo - 1 : '.$arbo[$n1].'<br />';
                                    if ($arbo[$n1] == 'BIRT') {
                                        $pec_note = true;
                                        if ($comment != '') $comment .= '<br />';
                                        $comment .= 'Note sur la naissance : <br />' . rtrim(substr($str, 7));
                                    }
                                    if ($arbo[$n1] == 'DEAT') {
                                        $pec_note = true;
                                        if ($comment != '') $comment .= '<br />';
                                        $comment .= 'Note sur le déc&egrave;s :<br />' . rtrim(substr($str, 7));
                                    }
                                }
                            }
                            // On ne prend en compte les continuations que sur les notes prises en charge
                            if (($zone2 == 'CONT') and ($pec_note)) {
                                $comment .= '<br />' . rtrim(substr($str, 7));
                            }
                            if (($zone2 == 'CONC') and ($pec_note)) {
                                $comment .= rtrim(substr($str, 7));
                            }

                            // Prise en compte des images sur les personnes
                            // Au niveau 2 : directement sur la personne
                            // Au niveau 3 : sur la naissance ou le décès
                            // Détection de la présence d'une image
                            if (($z1 == 'FORM') and ($z1_prec == 'OBJE')) {
                                $pec_obje = false;
                                if ($niveau == 3) {
                                    if (($sous_section == 'BIRT') or ($sous_section == 'DEAT')) $pec_obje = true;
                                } else if ($niveau == 2) {
                                    $pec_obje = true;
                                }
                            }

                            if ($pec_obje) {
                                $tp = strtoupper(rtrim(substr($str, 7)));
                                if (($tp == 'JPEG') or ($tp == 'JPG')) {
                                    $ind_obj_img = true;
                                    $title_image = '';
                                }
                            }

                            // Traitement des images sur les personnes
                            //3 TITL Le titre de l'image
                            //3 FILE rep\nom_image.jpg
                            if (($ind_obj_img) and ($z1 == 'TITL')) {
                                $title_image = ajoute_sl_rt(substr($str, 7, 80));
                            }
                            if (($ind_obj_img) and ($z1 == 'FILE')) {
                                // Détermination du nom de l'image
                                $pos_sl = strrpos($str, '/');
                                if ($pos_sl === false) $pos_sl = strrpos($str, '\\');
                                if ($pos_sl === false) $pos_sl == -1;
                                $nom_img = ajoute_sl_rt(substr($str, $pos_sl + 1));
                                $req_img[] = "'P','" . $nom_img . "','N','" . $title_image . "'," . $vdiff_internet_img;
                                $ind_obj_img = false;
                            }
                        }

                        if ($s_FAM) {
                            if ($niveau == 1) {
                                $sous_section = $z1;
                                $ssGed = false;
                                if (($sous_section == 'HUSB') or
                                    ($sous_section == 'WIFE') or
                                    ($sous_section == 'CHIL')
                                ) $x = array_search(trim($arr[2]), $individus_ref);
                                switch ($sous_section) {
                                    case 'HUSB':
                                        if ($x) $mari = $individus_ref_base[$x];
                                        break;
                                    case 'WIFE':
                                        if ($x) $femme = $individus_ref_base[$x];
                                        break;
                                    case 'CHIL':
                                        if ($x) $enfants[] = $individus_ref_base[$x];
                                        break;
                                    case 'CHAN':
                                        if (($z1 == 'DATE') and ($Rep_date)) $date_mod = traite_date_dt($arr);
                                        break;
                                    case 'NOTE':
                                        break;
                                    // Récup des évènements Gedcom
                                    default:
                                        if ($Maj) {
                                            // S'agit-il d'un évènement Gedcom reconnu ?
                                            $x = strpos($types_evenements, $sous_section);
                                            if ($x !== false) {
                                                ++$num_ev;
                                                $code_ev[] = $sous_section;
                                                $titre_ev[] = rtrim(substr($str, 7));
                                                $date_ev[] = '';
                                                $lieu_ev[] = 0;
                                                $ssGed = true;
                                            }
                                        }
                                }
                            }
                            if ($niveau == 2) {
                                switch ($sous_section) {
                                    case 'MARR':
                                        if ($z1 == 'DATE') $date_mariage = traite_date($str);
                                        if ($z1 == 'PLAC') $lieu_mariage = traite_lieu($str);
                                        break;
                                    case 'MARC':
                                        if ($z1 == 'DATE') $date_K = traite_date($str);
                                        if ($z1 == 'PLAC') $lieu_K = traite_lieu($str);
                                        break;
                                    default:
                                        if ($ssGed) {
                                            if ($z1 == 'DATE') {
                                                //echo 'Appel traitement date sur section mariage </br>';
                                                $date_ev[$num_ev] = traite_date($str);
                                            }
                                            if ($z1 == 'PLAC') $lieu_ev[$num_ev] = traite_lieu($str);
                                        }
                                        break;
                                }
                            }
                            switch ($zone2) {
                                case 'NOTE':
                                    $comment .= rtrim(substr($str, 7));
                                    break;
                                case 'CONT':
                                    $comment .= '<br />' . rtrim(substr($str, 7));
                                    break;
                                case 'CONC':
                                    $comment .= rtrim(substr($str, 7));
                                    break;
                            }
                        }

                        // Traitement de la section SOUR (source)
                        //0 @<XREF:SOUR>@ SOUR
                        if ($s_SOUR) {
                            if ($niveau == 1) {
                                $sous_section = $z1;
                                $z2 = rtrim(substr($str, 7));
                                switch ($sous_section) {
                                    case 'TITL':
                                        $S_Titre = $z2;
                                        break;
                                    case 'AUTH':
                                        $S_Auteur = $z2;
                                        break;
                                    case 'ABBR':
                                        $S_Classement = $z2;
                                        break;
                                    case 'REPO':
                                        $S_Ident_Depot_Tempo = $z2;
                                        break;
                                }
                            }
                            if ($niveau == 2) {
                                $sous_section = $z1;
                                $z2 = rtrim(substr($str, 7));
                                switch ($sous_section) {
                                    case 'CALN':
                                        $S_Cote = $z2;
                                        break;
                                }
                            }
                        }

                        // Traitement de la section REPO (dépôt)
                        //0 @<XREF:REPO>@ REPO
                        if ($s_REPO) {
                            if ($niveau == 1) {
                                $sous_section = $z1;
                                $z2 = rtrim(substr($str, 7));
                                switch ($sous_section) {
                                    case 'NAME':
                                        $D_Nom = $z2;
                                        break;
                                }
                            }
                        }

                        //echo '('.$niveau.' : '.$section.'/'.$sous_section.')'.$tab.$str.'<br />'."\n";
                    }
                    if (substr($msg, 0, 1) == '+') {
                        echo substr($msg, 1) . '<br />' . "\n";
                    }

                    // Mémo de la balise précédente
                    $z1_prec = $z1;
                }
            } else {
                echo 'Fichier impossible &agrave; ouvrir<br />';
            }
            fclose($fp);
            /*
        for ($nb_ind = 1;$nb_ind < count($individus_ref);$nb_ind++) {
          echo $individus_ref[$nb_ind];
          if ($maj_oui == 'on') echo ' ==> '.$individus_ref_base[$nb_ind];
          echo '<br />'."\n";
        }
        for ($nb_villes = 0;$nb_villes < count($lib_villes);$nb_villes++) {
          echo $lib_villes[$nb_villes];
          if ($maj_oui == 'on') echo ' ==> '.$ref_villes[$nb_villes];
          echo '<br />'."\n";
        }
        */

            // Création des noms de famille ; normalement cela a été fait en amont sauf s'il n'y a pas de sections familles
            // + divers
            if ($Maj) {
                Creation_Noms_Commun();

                // Rattachement des sources aux dépôts
                $req = 'UPDATE ' . $n_sources . ' s SET Ident_Depot = ' .
                    '(SELECT Ident FROM ' . $n_depots . ' d ' .
                    'WHERE d.Ident_Depot_Tempo = s.Ident_Depot_Tempo) ' .
                    'WHERE s.Ident_Depot_Tempo <> \'\' ' .
                    'AND s.Ident_Depot =0;';
                $res = maj_sql($req);

                // Rattachement des liens de sources aux sources
                $req = 'UPDATE ' . $n_conc_source . ' l SET Id_Source = ' .
                    '(SELECT Ident FROM ' . $n_sources . ' s ' .
                    'WHERE s.Ident_Source_Tempo = l.Id_Source_Tempo) ' .
                    'WHERE l.Id_Source_Tempo <> \'\' ' .
                    'AND l.Id_Source =0;';
                $res = maj_sql($req);

                $req = 'UPDATE ' . $n_sources . ' s SET Ident_Source_Tempo = NULL WHERE s.Ident_Depot_Tempo IS NOT NULL';
                $res = maj_sql($req);

                $req = 'update ' . nom_table('general') . ' set date_modification = current_timestamp';
                $res = maj_sql($req);
            }

            // Rectification UTF-8
            if ($Maj) {
                if ($fic_utf8 == 'on') {
                    if ($def_enc != 'UTF-8') rectif_UTF8();
                }
            }

            echo '<hr/>';
            $temps = time();
            $jour = date('j', $temps);  //format numerique : 1->31
            $annee = date('Y', $temps); //format numerique : 4 chiffres
            $mois = date('m', $temps);
            $heure = date('H', $temps);
            $minutes = date('i', $temps);
            $sec = date('s', $temps);
            $fin = $jour . '/' . $mois . '/' . $annee . ' &agrave; ' . $heure . 'h' . $minutes . ':' . $sec;

            if ($maj_oui == 'on') $action = 'créées';
            else                  $action = 'lues';
            if ($maj_oui == 'on') $action_m = 'créés';
            else                  $action_m = 'lus';
            $nb_pers = count($individus_ref) - 1; // 1ère pos à x pour trouver le poste 0
            $tab = '  ';
            if ($Codage_ANSEL)
                echo 'Décodage des caract&egrave;res ANSEL vers ANSI<br />';
            echo LG_IMP_GED_RESUME . ' :<br />';
            echo $tab . 'Début : ' . $debut . '<br />';
            echo $tab . 'Fin : ' . $fin . '<br />';
            echo $tab . $nb_pers . ' personnes ' . $action . '<br />';
            if (isset($nb_cre_noms)) {
                if ($nb_cre_noms > 0) echo $tab . $idNom . ' noms ' . $action_m . '<br />';
            }
            echo $tab . $nb_unions . ' unions ' . $action . '<br />';
            echo $tab . $nb_filiations . ' filiations ' . $action . '<br />';
            if (($maj_oui == 'on') and (isset($ref_villes)))
                echo $tab . count($ref_villes) . ' villes ' . $action . '<br />';
            echo $tab . 'Format des lieux pris en charge : ' . my_html($lieux) . '<br />';

            if ($maj_oui == 'on') {
                maj_date_site(true);
                $ic_conseil = '<img src="' . $chemin_images_icones . $Icones['tip'] . '" alt="' . LG_TIP . '"/> ';
                echo $ic_conseil . LG_IMP_GED_REMIND_SOSA_1
                    . '<a href="' . $root . '/liste_pers.php?Type_Liste=P">'
                    . LG_IMP_GED_REMIND_SOSA_2 . '</a>. ' . LG_IMP_GED_REMIND_SOSA_3
                    . '<a href="' . $root . '/verif_sosa.php">'
                    . $LG_Menu_Title['Check_Sosa'] . '</a>,<br>';
                echo $ic_conseil . LG_IMP_GED_REMIND_EVT
                    . '<a href="' . $root . '/fusion_evenements.php">' . $LG_Menu_Title['Event_Merging'] . '</a>.<br />';
                echo $ic_conseil . LG_IMP_GED_REMIND_INTERNET
                    . '<a href="' . $root . '/verif_internet.php">' . $LG_Menu_Title['Internet_Cheking'] . '</a>.<br />';
            }
        }
    }

    // Init des zones de requête
    $req = '';
}

if ($_SESSION['estGestionnaire']) {
    if (($ok == '') && ($annuler == '')) {
        echo '<br />';
        echo '<form id="saisie" method="post" enctype="multipart/form-data">';
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        echo '<table width="90%" class="table_form">';
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_FILE) . '</td><td class="value">';
        echo '<input type="file" name="nom_du_fichier" size="80"></td></tr>';
        if ($def_enc != 'UTF-8') {
            echo '<tr><td class="label" width="35%">' . ucfirst($LG_Ch_UTF8) . '</td><td class="value">';
            echo '<input type="checkbox" name="fic_utf8"></td></tr>';
        }

        echo '<tr><td colspan="2"> </td></tr>';
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_INSERT) . '</td><td class="value">';
        echo '<input type="checkbox" name="maj_oui"></td></tr>';
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_RESET) . '</td><td class="value">';
        echo '<input type="checkbox" name="init_base"></td></tr>';

        // Sur site gratuit non  Premium, on diffuse par défaut sans possibilité de modifier l'indicteur ==> respect de la charte
        $readonly = false;
        if (($SiteGratuit) and (!$Premium)) $readonly = true;

        echo '<tr><td colspan="2"> </td></tr>';
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_DEFAULT_VISIBILITY) . '</td><td class="value">';
        if ($readonly) {
            echo 'Option indisponible sur les sites gratuits non  Premium';
            echo '<input type="hidden" name="diff_internet" value="on"/>';
        } else {
            echo '<input type="checkbox" name="diff_internet" checked>';
        }
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_DEFAULT_VISIBILITY_COMMENTS) . '</td><td class="value">';
        echo '<input type="checkbox" name="diff_internet_note" checked></td></tr>' . "\n";
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_IMAGE_DEFAULT_VISIBILITY) . '</td><td class="value">';
        echo '<input type="checkbox" name="diff_internet_img" checked></td></tr>' . "\n";
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_DEFAULT_STATUS) . '</td><td class="value">';
        bouton_radio('val_statut', 'O', LG_CHECKED_RECORD_SHORT, true);
        bouton_radio('val_statut', 'N', LG_NOCHECKED_RECORD_SHORT);
        bouton_radio('val_statut', 'I', LG_FROM_INTERNET);
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_IMPORT_DATES) . '</td><td class="value">';
        echo '<input type="checkbox" name="reprise_date"/></td></tr>' . "\n";

        echo '<tr><td colspan="2"> </td></tr>';
        echo '<tr><td class="label" width="35%">' . ucfirst(LG_IMP_GED_PLACES) . '</td><td class="value">';
        echo '<select id="sel_lieux" size="1" onchange="lieux.value += sel_lieux.options[sel_lieux.selectedIndex].text + \',\';" >';
        echo '<option value="0">--</option>';
        /*<OPTION value="1">town</option>
		<OPTION value="2">area code</option>
		<OPTION value="3">county</option>
		<OPTION value="4">region</option>
		<OPTION value="5">country</option>
		<OPTION value="6">subdivision</option>*/
        echo '<option value="1">ville</option>';
        echo '<option value="2">code postal</option>';
        echo '<option value="3">département</option>';
        echo '<option value="4">région</option>';
        echo '<option value="5">pays</option>';
        echo '<option value="6">sous-division</option>';
        echo '</select>' . "\n";
        echo '<input readonly="readonly" id="lieux" name ="lieux" size="50"/> ';
        echo  '<img src="' . $root . '/assets/img/' . $Icones['efface'] . '" alt="Efface le format des lieux" title="Efface le format des lieux" onclick="document.getElementById(\'lieux\').value = \'\';">';
        echo '</td></tr>' . "\n";
        echo '<tr><td colspan="2"> </td></tr>';
        bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');
        echo '<tr><td colspan="2"> </td></tr>';
        echo '</table>';
        echo '</form>';
    }
} else {
    echo '<center><font color="red"><br><br><br><h2>' . $LG_function_noavailable_profile . '</h2></font></center>';
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