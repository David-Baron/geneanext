<?php
//=====================================================================
// Import d'une sauvegarde de la base éventuellement sur une base distante
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'init_base',
    'nom_du_fichier',
    'SelFic',
    'loc_base',
    // paramètres de localisation distante
    'base_int',
    'uti_int',
    'mdp_int',
    'site_int',
    'port_int',
    'aff_pres_ut',
    'memo',
    'Horigine',
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

$acces = 'M';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Import_Backup'];
$x = Lit_Env();
$niv_requis = 'G';                        // Page réservée au gestionnaire
require(__DIR__ . '/app/ressources/gestion_pages.php');

// $SiteGratuit = true;

// Traitement en rupture sur le nom de la table ==> uniquement pour les sites gratuits
function traite_rup_table($nom_table)
{
    global $nb_req_table, $chemin_images, $Icones, $SiteGratuit, $Premium, $Pivot_Masquage, $depuis_heberge, $Environnement, $debug;
    if ($debug) echo $nb_req_table . ' ' . my_html(LG_IMP_BACKUP_READ_LINES) . '<br>';
    // Traitement de cohérence : on vérifie que le nombre de lignes de la table correspond au nombre de lignes du fichier
    // Si ce n'est pas le cas, on considère qu'il y falsification du fichier
    // La vérification n'est faite que dans le cas du site gratuit
    if ($SiteGratuit) {
        $req = 'select count(*) from ' . $nom_table;
        $res = lect_sql($req);
        if ($row = $res->fetch(PDO::FETCH_NUM)) {
            if ($row[0] != $nb_req_table) {
                $image = $Icones['stop'];
                echo '<img src="' . $chemin_images . $image . '" BORDER=0 alt="' . $image . '" title="' . $image . '"> '
                    . my_html(LG_IMP_BACKUP_LINES_ERROR) . '<br>';
                // var_dump ($row[0]);
                // var_dump ($nb_req_table);
                // Vidage de la table
                $req = 'delete from ' . $nom_table;
                $res = maj_sql($req);
            }
        }
    }
    $nb_req_table = 0;
    // Si on vient de traiter la table general, on force l'indicateur Internet pour les sites gratuits et éventuellement l'année pivot de masquage des dates
    if (($SiteGratuit) and (strpos($nom_table, 'general') != 0)) {
        $req = 'update ' . $nom_table . ' set Environnement = "I"';
        $res = maj_sql($req);
        if (!$Premium) {
            $req = 'update ' . $nom_table . ' set Pivot_Masquage = 9999;';
            $res = maj_sql($req);
        }
    }
}

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$init_base      = Secur_Variable_Post($init_base, 2, 'S');
$nom_du_fichier = Secur_Variable_Post($nom_du_fichier, 80, 'S');
$SelFic         = Secur_Variable_Post($SelFic, 80, 'S');
$loc_base       = Secur_Variable_Post($loc_base, 1, 'S');
$base_int       = Secur_Variable_Post($base_int, 20, 'S');
$uti_int        = Secur_Variable_Post($uti_int, 20, 'S');
$mdp_int        = Secur_Variable_Post($mdp_int, 20, 'S');
$site_int       = Secur_Variable_Post($site_int, 50, 'S');
$port_int       = Secur_Variable_Post($port_int, 5, 'S');
$aff_pres_ut    = Secur_Variable_Post($aff_pres_ut, 1, 'S');
$memo           = Secur_Variable_Post($memo, 2, 'S');

if ($bt_OK) Ecrit_Entete_Page($titre, '', '');

$compl = Ajoute_Page_Info(600, 300);
Insere_Haut($titre, $compl, 'Import_Sauvegarde', '');

$nom_fic_cnx_dist = 'connexion_distante_inc.php';

if (!$SiteGratuit) $ext_poss = '/sql/txt/';
else               $ext_poss = '/txt/';

// Reconnexion pour base internet avec les paramètres saisis
if (($ok == 'OK') and ($loc_base == 'I')) {
    // Fermeture connexion courante
    $connexion = null;
    $db      = $base_int;
    $util    = $uti_int;
    $mdp     = $mdp_int;
    $serveur = $site_int . ':' . $port_int;
    //echo 'db : '.$db.'<br>';
    //echo 'util : '.$util.'<br>';
    //echo 'mdp : '.$mdp.'<br>';
    //echo 'serv : '.$serveur.'<br>';
    // if ($def_enc == 'UTF-8') 
    // $aj_charset = ';charset=utf8';

    try {
        $connexion = new PDO("mysql:host=$serveur;dbname=$db", $util, $mdp);
        // $connexion = new PDO("mysql:host=$serveur;dbname=$db$aj_charset", $util, $mdp);
        // $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($memo == 'O') {
            echo my_html(LG_IMP_BACKUP_INTERNET_SAVE_REQUEST);
            $fp1 = fopen($nom_fic_cnx_dist, 'wb');
            if (! $fp1) die(LG_IMP_BACKUP_FILE_ERROR . $nom_fic_cnx_dist);
            else {
                //ecriture des paramêtres saisis
                ecrire($fp1, '<?php');
                ecrire($fp1, '//--- Paramètres de connexion distants ---');
                ecrire($fp1, '$ddb      = \'' . $base_int . '\';');
                ecrire($fp1, '$dutil    = \'' . $uti_int . '\';');
                ecrire($fp1, '$dmdp     = \'' . $mdp_int . '\';');
                ecrire($fp1, '$dserveur = \'' . $site_int . '\';');
                ecrire($fp1, '$dport    = \'' . $port_int . '\';');
                ecrire($fp1, '//----------------- fin ------------------');
                ecrire($fp1, '?>');
                if ($gz) gzclose($fp1);
                else fclose($fp1);
            }
        }
    } catch (PDOException $ex) {
        echo 'Echec de la connexion !' . $ex->getMessage();
        $bt_OK = false;
    }

    // Forçage fin pour test de connexion sans chargement
    //$ok = 'KO';
}

//Demande de chargement
if ($bt_OK) {

    // Récupération de la liste des tables pour vérification ultérieure
    if ($SiteGratuit) {
        $liste_tables = [];
        $sql = 'show tables from `' . $db . '` like \'' . $pref_tables . '%\'';
        $result = lect_sql($sql);
        if (!$result) {
            aff_erreur(LG_IMP_BACKUP_TABLE_ERROR);
            exit;
        } else {
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $liste_tables[] = $row[0];
            }
        }
        // Affichage de la liste des tables présentes
        //for ($nb = 0; $nb < count($liste_tables); $nb++) echo $liste_tables[$nb].', ';
    }

    // Pas de limite de temps en local
    // Sur le net, limite fixée à la valeur paramétrée ; plus importante sur les sites Premium
    if ($Environnement == 'L') {
        set_time_limit(0);
    }
    if ($SiteGratuit) {
        set_time_limit($lim_temps);
    }

    echo my_html(LG_IMP_BACKUP_FILE) . LG_SEMIC;
    $sel_init = false;
    if ($_FILES['nom_du_fichier']['name'] != '') {
        $n_fic = $_FILES['nom_du_fichier']['name'];
        echo $n_fic;
        if ($n_fic == 'Export_Initialisation.sql')
            $sel_init = true;
    } else
        echo $SelFic;
    echo '<br>';
    $erreur = '';
    $path = '';

    // Une demande de chargement a été faite ; cette demande est prioritaire sur une sélection de fichier éventuelle
    if ($_FILES['nom_du_fichier']['name'] != '') {
        $nom_du_fichier = $_FILES['nom_du_fichier']['name'];
        // Contrôle de l'extension du fichier demandé

        $ext = Extension_Fic($nom_du_fichier);
        // Fichier txt seul autorisé sur les sites gratuits
        if ($SiteGratuit) {
            if (strpos($ext_poss, $ext) == false) $erreur = LG_IMP_BACKUP_FILE_ERROR_TXT;
        }
        // Fichier txt ou sql seuls autorisés en environnement autre que sites gratuits
        else {
            if (strpos($ext_poss, $ext) == false) $erreur = LG_IMP_BACKUP_FILE_ERROR_TXT_SQL;
        }
        // Erreur d'extension
        if ($erreur != '') {
            Affiche_Stop($erreur);
        }
        // Sinon on peut télécharger (après contrôle)
        else {
            $erreur = false;
            $tmp_file = $_FILES['nom_du_fichier']['tmp_name'];
            $nom_du_fichier = $_FILES['nom_du_fichier']['name'];

            $erreur = ctrl_fichier_ko();

            // On peut télécharger s'il n'y a pas d'erreur
            if (!$erreur) {
                $path = $chemin_exports . '_tmp' . $_FILES['nom_du_fichier']['name'];
                move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $path);
                $mode = 'r';
            }
        }
    }
    // L'utilisateur a sélectionné un fichier ?
    else {
        if ($SelFic != '') {
            $path = $chemin_exports . $SelFic;
            $mode = 'r';
            $ext = Extension_Fic($SelFic);
        } else {
            Affiche_Stop(LG_IMP_BACKUP_NO_FILE);
        }
    }
    if ($path != '') {
        if ($fp = fopen($path, $mode)) {

            // Ré-init de la base ; on détruit toutes les tables, sauf la table general
            if ((!$SiteGratuit) and ($init_base == 'on')) {
                echo my_html(LG_IMP_BACKUP_RESET) . '<br>';
                $sql = 'show tables from ' . $db . ' like \'' . $pref_tables . '%\'';

                $result = lect_sql($sql);
                if ($result) {
                    while ($row = $result->fetch(PDO::FETCH_NUM)) {
                        $table_name = $row[0];
                        // On ne shoot pas la table general
                        if (strcmp(nom_table('general'), $table_name) != 0) {
                            maj_sql('drop table ' . $table_name);
                        }
                    }
                }
            }

            //Affiche toutes les erreurs sauf les notices
            error_reporting(E_ALL & ~E_NOTICE);

            $nb_req_lues = 0;
            $nb_req_table = 0;  // Nombre de requêtes par table pour les sites gratuits ==> contrôle de cohérence
            $nb_req_ok = 0;

            $depuis_heberge = false;
            $num_ligne = 0;
            $pref_fic = '';
            $ch_drop_sh_I = '';
            $ch_create_sh_I = '';
            $ch_insert_sh_I = '';
            $ch_drop_sh_O = '';
            $ch_create_sh_O = '';
            $ch_insert_sh_O = '';
            $table = '';
            $req = '';

            // Affichage du préfixe de rechargement
            if ($pref_tables == '') $lib_pref = LG_IMP_BACKUP_NO_PREFIX;
            else                    $lib_pref = $pref_tables;
            echo my_html(LG_IMP_BACKUP_PREFIX . ' : ' . $lib_pref) . '<br><br>' . "\n";

            $trt_table = true;

            while (!feof($fp)) {
                $ligne = trim(fgets($fp));
                $num_ligne++;
                $lligne = strlen($ligne);
                if ($debug) echo $num_ligne . ' : ' . $ligne . '<br>';

                // Sur la 1ère, il ne faut pas oublier que l'on a potentiellement les 3 caractères du BOM au début ; on les enlève
                if ($num_ligne == 1) {
                    if ($lligne > 3) {
                        // if ($debug) {
                        // var_dump(ord(substr($ligne,0,1)));
                        // var_dump(ord(substr($ligne,1,1)));
                        // var_dump(ord(substr($ligne,2,1)));
                        // }
                        if ((ord(substr($ligne, 0, 1)) == 0xEF) and
                            (ord(substr($ligne, 1, 1)) == 0xBB) and
                            (ord(substr($ligne, 2, 1)) == 0xBF)
                        ) {
                            if ($debug) echo 'Suppression du BOM<br>';
                            $ligne = substr($ligne, 3, $lligne - 3);
                        }
                    }
                }

                // La sauvegarde est-elle issue d'un site hébergé ?
                if ($num_ligne == 2) {
                    if ((strpos($ligne, 'site hébergé') !== false) or
                        (strpos($ligne, 'site heberge') !== false)
                    )
                        $depuis_heberge = true;
                }
                // Récupération du préfixe présent dans le fichier, ligne 4 ou 5, c'est selon...
                if (($num_ligne == 4) or ($num_ligne == 5)) {
                    if ($lligne > 10) {
                        if (strpos($ligne, 'prefixe') !== false) {
                            $pref_fic = substr($ligne, 10);
                            if ($debug) echo '$pref_fic : ' . $pref_fic . '<br>';
                            if (($depuis_heberge) or ($pref_fic != $pref_tables)) {
                                $ch_drop_sh_I = 'DROP TABLE IF EXISTS `' . $pref_fic;
                                $ch_create_sh_I = 'CREATE TABLE `' . $pref_fic;
                                $ch_insert_sh_I = 'INSERT INTO ' . $pref_fic;
                                $ch_drop_sh_O = 'DROP TABLE IF EXISTS `' . $pref_tables;
                                $ch_create_sh_O = 'CREATE TABLE `' . $pref_tables;
                                $ch_insert_sh_O = 'INSERT INTO ' . $pref_tables;
                            }
                        }
                    }
                }

                // Pour les fichiers SQL, une requête peut être sur plusieurs lignes
                if ($ext == 'sql') {
                    $car1 = '#';
                    $dercar = '#';
                    if ($lligne > 0) {
                        $car1   = substr($ligne, 0, 1);
                        $dercar = substr($ligne, $lligne - 1, 1);
                        // La ligne 1 est forcément une ligne de commentaire qui ne sera pas exploitée
                        if ($num_ligne == 1)
                            $car1 = '#';
                    }
                    if ($car1 != '#') {
                        if ($trt_table) {
                            if (($depuis_heberge) or ($pref_fic != $pref_tables)) {
                                $ligne = str_replace($ch_drop_sh_I, $ch_drop_sh_O, $ligne);
                                $ligne = str_replace($ch_create_sh_I, $ch_create_sh_O, $ligne);
                                $ligne = str_replace($ch_insert_sh_I, $ch_insert_sh_O, $ligne);
                            }
                            $req = $req . ' ' . $ligne;
                            if ($dercar == ';') {
                                $nb_req_lues++;
                                //echo $req.'<br>';
                                if ($res = maj_sql($req, false)) $nb_req_ok++;
                                else {
                                    if ($debug) {
                                        echo 'Req KO : ' . $req . '<br>';
                                    }
                                }
                                $req = '';
                            }
                        }
                    }
                    // Récupération du nom de la table pour savoir s'il s'agit de la table 'utilisateurs' que l'on doit conserver ou non
                    else {
                        if ($aff_pres_ut == 'N') {
                            $trt_table = true;
                        } else {
                            if (strpos($ligne, 'Traitement de la table') !== false) {
                                if (strpos($ligne, 'utilisateurs') !== false) {
                                    $trt_table = false;
                                    echo my_html(LG_IMP_BACKUP_KEEP_USERS2) . '<br><br>';
                                } else
                                    $trt_table = true;
                            }
                        }
                    }
                }
                // Pour les fichiers txt, une requête est sur une seule ligne et doit être reconstruite
                if ($ext == 'txt') {
                    // var_dump(ord(substr($ligne,0,1)));
                    // var_dump(ord(substr($ligne,1,1)));
                    // var_dump(ord(substr($ligne,2,1)));
                    // var_dump(substr($ligne,3,1));
                    $car1 = '#';
                    $dercar = '#';
                    if ($lligne > 0) {
                        $car1   = substr($ligne, 0, 1);
                        $dercar = substr($ligne, $lligne - 1, 1);
                    }
                    if ($debug)
                        var_dump($car1);
                    // Traitement de la ligne en fonction du premier caractère
                    // # : ligne de commentaire
                    // [ : ligne donnant un nom de table
                    // autre : valeurs à insérer

                    if (($SiteGratuit) and ($car1 == '#')) {
                        // Comparaison de la version locale avec la version courante
                        $lig_vers = false;
                        if (strpos($ligne, 'version Génémania ') !== false) $lig_vers = true;
                        if (strpos($ligne, 'version Genemania ') !== false) $lig_vers = true;
                        if ($lig_vers !== false) {
                            if (substr($ligne, 20) != $Version) {
                                aff_erreur(LG_IMP_BACKUP_ERR_VERS);
                                aff_erreur(LG_IMP_BACKUP_LOCAL_VERS . ' : ' . substr($ligne, 20));
                                aff_erreur(LG_IMP_BACKUP_CUR_VERS . ' : ' . $Version);
                                exit;
                            }
                        }
                        // Récupération du préfixe local
                        $lig_pref = false;
                        if (strpos($ligne, 'préfixe ') !== false) $lig_pref = true;
                        if (strpos($ligne, 'prefixe ') !== false) $lig_pref = true;
                        if ($lig_pref !== false) {
                            $pref_local = '';
                            if (strlen($ligne) > 11) {
                                $pref_local = substr($ligne, 11);
                                $lg_pref_local = strlen($pref_local);
                            }
                        }
                    }

                    // Récupération du nom de la table à traiter
                    if ($car1 == '[') {
                        // Traitement de cohérence + nb requêtes par table, indispensable pour les sites gratuits
                        if ($table != '') {
                            if ($debug) echo "traite_rup_table($pref_tables.$table)<br>";
                            traite_rup_table($pref_tables . $table);
                        }
                        $pos = strrpos($ligne, ']');
                        $table = substr($ligne, 1, $pos - 1);
                        // Suppression du préfixe éventuel de la table locale
                        if (($SiteGratuit) and ($pref_local != '')) $table = substr($table, $lg_pref_local + 1);
                        if (($table != 'general') or ($aff_pres_ut = "O")) {
                            echo my_html(LG_IMP_BACKUP_TABLE_IN_PROGRESS) . ' ' . $table . ', <br>';
                            $req = 'delete from ' . $pref_tables . $table . ';';
                            $res = maj_sql($req);
                        }
                    }
                    if (($car1 != '#') and ($car1 != '[')) {
                        if (($table != 'utilisateurs') or ($trt_table)) {
                            $req = 'insert into ' . $pref_tables . $table . ' values (' . $ligne . ');';
                            $nb_req_lues++;
                            $nb_req_table++;
                            //echo "table : $table, nb_req_table : $nb_req_table<br>";
                            if ($res = maj_sql($req)) {
                                $nb_req_ok++;
                                $req = '';
                            }
                        }
                    }
                }
            }
            // Ttraitement de cohérence + nb requêtes par table pour les fichiers txt
            if ($ext == 'txt') {
                if ($table != '')
                    traite_rup_table($pref_tables . $table);
                echo '<br>';
            }
        }
        fclose($fp);
        if (!$SiteGratuit) $mot = LG_IMP_BACKUP_REQ;
        else               $mot = LG_IMP_BACKUP_LINES;
        echo $nb_req_lues . ' ' . my_html($mot . ' ' . LG_IMP_BACKUP_ITEM_READ) . ' ' . $nom_du_fichier . '<br>';
        echo $nb_req_ok . ' ' . my_html(LG_IMP_BACKUP_ITEM_OK) . ' <br><br>';

        // Traitement de cohérence : on vérifie qu'aucune table n'a été créée
        // Pour cela, on compare les tables actuelles avec les tables avant import
        if ($SiteGratuit) {
            $sql = 'show tables from ' . $db . ' like \'' . $pref_tables . '%\'';
            $result = lect_sql($sql);
            if (!$result) {
                aff_erreur(LG_IMP_BACKUP_TABLE_ERROR);
                exit;
            } else {
                while ($row = $result->fetch(PDO::FETCH_NUM)) {
                    // Table non trouvée dans la liste initiale
                    if (array_search($row[0], $liste_tables) === false) {
                        $image = $Icones['stop'];
                        echo '<img src="' . $chemin_images . $image . '" BORDER=0 alt="' . $image . '" title="' . $image . '"> '
                            . $row[0] . LG_SEMIC . '<br>';
                        // Vidage de la table
                        $req = 'drop ' . $row[0];
                        $res = lect_sql($req);
                    }
                }
            }
            // Affichage de la liste des tables présentes
            //for ($nb = 0; $nb < count($liste_tables); $nb++) echo $liste_tables[$nb].', ';
        }
        // On force l'indicateur Local pour un import depuis un site hébergé
        if (($depuis_heberge) and ($Environnement == 'L')) {
            $req = 'update ' . nom_table('general') . ' set Environnement = "L"';
            $res = maj_sql($req);
        }
        //Export_Initialisation.sql
        if ($nb_req_ok > 0) {
            if ($sel_init) maj_date_site(false);
            else maj_date_site(true);
        }
    }
}

if ($_SESSION['estGestionnaire']) {

    // Première entrée : affichage pour saisie
    if (($ok == '') && ($annuler == '')) {
        // Recherche des paramètres de connexion distante
        $ddb      = '';
        $dutil    = '';
        $dmdp     = '';
        $dserveur = '';
        $dport    = '3306';
        $larg_titre = '25';

        if (file_exists($nom_fic_cnx_dist)) include($nom_fic_cnx_dist);
        // Affichage du formulaire
        echo '<form id="saisie" method="post" enctype="multipart/form-data">';
        aff_origine();
       
        echo '<table width="80%" class="table_form">';

        // La ré-initialisation de la base n'est pas prévue pour les sites gratuits
        if (!$SiteGratuit) {
            ligne_vide_tab_form(1);
            col_titre_tab(LG_IMP_BACKUP_RESET, $larg_titre);
            echo '<td class="value"><input type="checkbox" name="init_base"/>';
            echo ' <img src="' . $root . '/assets/img/' . $Icones['warning'] . '" alt="Attention" title="Attention"> ' . my_html(LG_IMP_BACKUP_RESET_TIP);
            echo '</td>';
            echo '</tr>';
        }

        ligne_vide_tab_form(1);
        col_titre_tab(LG_IMP_BACKUP_FILE, $larg_titre);
        echo '<td class="value"><input type="file" name="nom_du_fichier" size="80"/>';
        $dir = $chemin_exports;
        // Extensions autorisées
        $dossier = opendir($dir);
        $col = 0;
        $max_col = 5;
        $nb = 0;
        while ($fichier = readdir($dossier)) {
            // var_dump($fichier);
            $extension = strtolower(substr(strrchr($fichier, "."), 1));
            if ((is_file($dir . $fichier)) and (strpos($ext_poss, $extension) != false)) {
                // if ((is_file($dir.'/'.$fichier)) and (strpos($ext_poss,$extension) != false)) {
                $sel = false;
                if ($SiteGratuit) {
                    if (strpos($fichier, 'Export_SiteGratuit') === 0) $sel = true;
                } else {
                    if ((strpos($fichier, 'Export_SiteGratuit') === 0) or
                        (strpos($fichier, 'Export_Sauvegarde') === 0)
                    ) $sel = true;
                }
                if ($sel) {
                    if ($nb == 0) {
                        echo '<br>' . my_html(LG_IMP_BACKUP_FILE_SELECT);
                        $nom_div = 'lediv';
                        $x = Oeil_Div('ajout', my_html(LG_IMP_BACKUP_FILE_SHOW), $nom_div);
                        echo '<table width="90%" border="0">' . "\n";
                    }
                    $nb++;
                    $col++;
                    if ($col == 1) echo '<tr align="center" valign="middle">' . "\n";
                    $fic_svg = $dir . $fichier;
                    // $fic_svg = $dir.'/'.$fichier;
                    $date_fic = date("d/m/Y H:i:s", filemtime($fic_svg));
                    if (file_exists($fic_svg)) {
                        echo '<td>';
                        echo '<br><input type="radio" name="SelFic" value="' . $fichier . '"/>';
                        echo '<br>' . $fichier . '<br>' . $date_fic . "\n";
                    }
                    echo '</td>' . "\n";
                    if ($col == $max_col) {
                        echo '</tr>' . "\n";
                        $col = 0;
                    }
                }
            }
        }
        if (($col) and ($col < $max_col)) {
            $col = $max_col - $col;
            echo '<td colspan="' . $col . '"> </td>' . "\n";;
            echo '</tr>' . "\n";
        }
        if ($nb) {
            echo '</table>' . "\n";
            fin_div_cache($nom_div);
        }
        echo '</td>';
        echo '</tr>' . "\n";

        // Les options de destination ne sont pas disponibles pour les sites gratuits
        if (!$SiteGratuit) {
            ligne_vide_tab_form(1);
            col_titre_tab(LG_IMP_BACKUP_TARGET, $larg_titre);
            echo '<td class="value">';
            echo '<input type="radio" name="loc_base" value="L" checked="checked" onclick="cache_div(\'p_int\');"/>' . my_html(LG_IMP_BACKUP_TARGET_LOCAL);
            echo '<input type="radio" name="loc_base" value="I" onclick="montre_div(\'p_int\');"/>' . my_html(LG_IMP_BACKUP_TARGET_INTERNET) . "\n";

            echo ' <div id="p_int">' . "\n";
            echo '<fieldset>' . "\n";
            aff_legend(LG_IMP_BACKUP_INTERNET_PARAMS);
            echo '<table>' . "\n";
            echo '<tr><td>Base :</td><td><input type="text" name="base_int" value="' . $ddb . '"/></td></tr>' . "\n";
            echo '<tr><td>' . my_html(LG_IMP_BACKUP_INTERNET_PARAMS_DB) . LG_SEMIC . '</td><td><input type="text" name="uti_int" value="' . $dutil . '"/></td></tr>' . "\n";
            echo '<tr><td>' . my_html(LG_IMP_BACKUP_INTERNET_PARAMS_PSW) . LG_SEMIC . '</td><td><input type="password" name="mdp_int" value="' . $dmdp . '"/></td></tr>' . "\n";
            echo '<tr><td>' . my_html(LG_IMP_BACKUP_INTERNET_PARAMS_SITE) . LG_SEMIC . '</td><td><input type="text" name="site_int" value="' . $dserveur . '"/> ';
            echo my_html(LG_IMP_BACKUP_INTERNET_PARAMS_PORT) . LG_SEMIC . '<input type="text" name="port_int" value="' . $dport . '"/></td></tr>' . "\n";
            echo '<tr><td colspan="2"><input type="checkbox" name="memo" value="O"/>' . my_html(LG_IMP_BACKUP_INTERNET_PARAMS_SAVE) . '</td></tr>' . "\n";
            echo '</table>' . "\n";
            echo '</fieldset>' . "\n";
            echo '</div>' . "\n";
            echo '</td></tr>' . "\n";
        }

        $aff_pres_ut = false;
        // Préservation de la liste des utilisateurs sur Internet
        //if ('I' == 'I') {
        if ($Environnement == 'I') {
            $aff_pres_ut = true;
            ligne_vide_tab_form(1);
            col_titre_tab(LG_IMP_BACKUP_KEEP_USERS, $larg_titre);
            echo '<td class="value">';
            echo '<input type="radio" name="aff_pres_ut" value="O" checked="checked" />' . my_html(ucfirst($LG_Yes));
            echo '<input type="radio" name="aff_pres_ut" value="N" />' . my_html(ucfirst($LG_No)) . "\n";
            echo '</td></tr>' . "\n";
        }


        ligne_vide_tab_form(1);
        bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');
        ligne_vide_tab_form(1);

        echo '</table>';

        // Au cas où on n'a pas affiché le bouton de préservation de la liste des utilisateurs...
        if (!$aff_pres_ut)
            echo '<input type="hidden" name="aff_pres_ut" value="N"/>';

        echo '</form>';

        // Masquage du div de connexion
        echo '<script type="text/javascript">' . "\n";
        echo '<!--' . "\n";
        echo '  cache_div(\'p_int\');' . "\n";
        echo '//-->' . "\n";
        echo '</script>' . "\n";
    }
} else echo my_html($LG_function_noavailable_profile);
Insere_Bas($compl);
?>
</body>

</html>