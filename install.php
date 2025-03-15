<?php

require(__DIR__ . '/app/ressources/fonctions.php');

$cnx = false;
$msg_cnx_ok = 'Connexion OK';
$lib_maj_param = 'Mettre à jour les paramètres';
$lib_tst_param = 'Tester les paramètres';
$lib_bt_sources = 'Mettre à jour les sources';

function db_connect($host, $dbname, $user, $pswd)
{
    global $msg, $connexion, $msg_cnx_ok;
    $cnx = false;
    $msg = '';
    $aj_charset = ';charset=utf8';
    try {
        $connexion = new PDO("mysql:host=$host;dbname=$dbname$aj_charset", $user, $pswd);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $cnx = true;
    } catch (PDOException $ex) {
        $msg = 'Echec de la connexion à la base de donnnées !' . $ex->getMessage();
    }
    return $cnx;
}

function req_maj_vers($numvers)
{
    global $req;
    $req[] = 'UPDATE ' . nom_table('general') . ' SET version=\'' . $numvers . '\'';
    return $numvers;
}

// Création d'un répertoire
function cre_rep($nom_rep)
{
    if (!file_exists($nom_rep)) {
        mkdir($nom_rep);
        if (substr(php_uname(), 0, 7) != 'Windows') chmod($nom_rep, 0755);
        // Création d'un fichier d'index
        copy('Images/index.html', $nom_rep . '/index.html');
    }
    if (!is_writable($nom_rep)) {
        echo 'Le répertoire ' . $nom_rep . 'n\'est pas accessible en écriture, veuillez corriger le problème.';
    }
}
function Traite_Commentaire($type_objet)
{
    global     $db,
        $ref_comment,
        $n_commentaires,
        $n_personnes,
        $n_unions,
        $n_evenements,
        $req;
    switch ($type_objet) {
        case 'P':
            $n_table = $n_personnes;
            break;
        case 'U':
            $n_table = $n_unions;
            break;
        case 'E':
            $n_table = $n_evenements;
            break;
    }
    $sql = 'SELECT Reference, Divers, Diff_Internet_Note  FROM ' . $n_table . ' WHERE Divers IS NOT NULL AND Divers <> \'\'';

    if ($res = lect_sql($sql)) {
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $ref_comment++;
            $note = $enreg[1];
            //echo 'Commentaire associé : '.$note.'<br>';
            $Reference = $enreg[0];

            // Transformation du BBCODE avec [url] en lien internet
            $note = preg_replace("!\[(url)\](.+)\[/(?:url)\]!Ui", "<a href=\"$2\">$2</a>", $note);

            if (strlen(addslashes($note)) > 255) {
                echo 'Attention, commentaire tronqué : ' . $note . '<br>';
            }

            // Insertion de l'enregistrement en table commentaires
            $req[] = 'INSERT INTO ' . $n_commentaires .
                ' (Reference_Objet,Type_Objet,Note,Diff_Internet_Note) values (' .
                $Reference . ',\'' . $type_objet . '\',\'' . addslashes($note) . '\',\'' . $enreg[2] . '\');';
        }
    }
}

// Ajout requête modification du numéro de version
function Ex_Req()
{
    global     $req, $LaVersion, $Num_Gen, $msg;
    $c_req = count($req);
    for ($nb = 0; $nb < $c_req; $nb++) {
        // echo $req[$nb].'<br>';
        $res = maj_sql($req[$nb], false);
    }
    $req = '';
}

function exec_req_vide($req)
{
    global $vide;
    $ret = false;
    $z1 = 0;
    if ($vide) {
        if ($res = lect_sql($req)) {
            if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                $z1 = $enreg[0];
            }
            $res->closeCursor();
        }
        if ($z1 > 0) {
            $ret = true;
            $vide = false;
        }
    }
    return $ret;
}

function from_to_vers($from, $to)
{
    global $Num_Gen;
    if ($Num_Gen == $from) {
        $Num_Gen = req_maj_vers($to);
    }
}
$msgIni = '';
$msgMod = '';
$msgMaj = '';
$msgEff = '';

// Lit la version contenu dans le fichier de référence
$LaVersion = lit_fonc_fichier();
// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'tester',
    'majparam',
    'Supprimer',
    'maj',
    'eff',
    'conf_eff',
    'majsource',
    'serveurs',
    'dbs',
    'utils',
    'mdps',
    'nom_mod',
    'nom_crea',
    'envir',
    'prefixe',
    'Horigine',
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) {
        $$nom_variables = $_POST[$nom_variables];
    } else {
        $$nom_variables = '';
    }
}

$erreur = false;
$msg = '';

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation de Généamania</title>
    <link rel="shortcut icon" href="<?= $root; ?>/assets/favicon.ico" type="image/x-icon">
    <style type="text/css">
        body {
            background-color: #e8fbe5;
            font-family: Verdana, sans-serif;
        }

        .form-style-6 {
            font: 95% Arial, Helvetica, sans-serif;
            max-width: 400px;
            margin: 10px auto;
            padding: 16px;
            background: #F7F7F7;
        }

        .form-style-6 h1 {
            background: #43D1AF;
            padding: 20px 0;
            font-size: 140%;
            font-weight: 300;
            text-align: center;
            color: #fff;
            margin: -16px -16px 16px -16px;
        }

        .form-style-6 input[type="text"],
        .form-style-6 input[type="date"],
        .form-style-6 input[type="datetime"],
        .form-style-6 input[type="email"],
        .form-style-6 input[type="number"],
        .form-style-6 input[type="search"],
        .form-style-6 input[type="time"],
        .form-style-6 input[type="url"],
        .form-style-6 textarea,
        .form-style-6 select {
            -webkit-transition: all 0.30s ease-in-out;
            -ms-transition: all 0.30s ease-in-out;
            -o-transition: all 0.30s ease-in-out;
            transition: all 0.30s ease-in-out;
            outline: none;
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            width: 100%;
            background: #fff;
            margin-bottom: 4%;
            border: 1px solid #ccc;
            padding: 3%;
            color: #555;
            font: 95% Arial, Helvetica, sans-serif;
        }

        .form-style-6 input[type="text"]:focus,
        .form-style-6 input[type="date"]:focus,
        .form-style-6 input[type="datetime"]:focus,
        .form-style-6 input[type="email"]:focus,
        .form-style-6 input[type="number"]:focus,
        .form-style-6 input[type="search"]:focus,
        .form-style-6 input[type="time"]:focus,
        .form-style-6 input[type="url"]:focus,
        .form-style-6 textarea:focus,
        .form-style-6 select:focus {
            box-shadow: 0 0 5px #43D1AF;
            padding: 3%;
            border: 1px solid #43D1AF;
        }

        .form-style-6 input[type="submit"],
        .form-style-6 input[type="button"] {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            width: 100%;
            padding: 3%;
            background: #43D1AF;
            border-bottom: 2px solid #30C29E;
            border-top-style: none;
            border-right-style: none;
            border-left-style: none;
            color: #fff;
        }

        .form-style-6 input[type="submit"]:hover,
        .form-style-6 input[type="button"]:hover {
            background: #2EBC99;
        }
    </style>

</head>

<body vlink="#0000ff" link="#0000ff">
    <table cellpadding="0" width="100%">
        <tr>
            <td align="center">
                <h1>Installation de Généamania v<?= $LaVersion; ?></h1>
            </td>
        </tr>
    </table>

    <?php // L'utilsateur a cliqué sur Tester les paramètres
    if ($tester == $lib_tst_param) {
        // Essai de connexion à la base avec les paramètres saisis
        echo '<br>Test de connexion avec les valeurs saisies&nbsp;;&nbsp;';
        $cnx = db_connect($serveurs, $dbs, $utils, $mdps);
        if (!$cnx) {
            $erreur = true;
            echo 'les paramètres saisis ne sont pas corrects.<br>';
        } else {
            echo 'les paramètres saisis sont corrects, vous pouvez les enregistrer...<br><br>';
        }
    }

    // L'utilisateur a cliqué sur Mettre à jour les paramètres
    if ($majparam == $lib_maj_param) {
        $nom_fic = 'connexion_inc.php';
        $fp1 = fopen($nom_fic, "wb");
        if (! $fp1) die("impossible de créer $nom_fic.");
        else {
            //ecriture des paramêtres saisis
            fputs($fp1, "<?php");
            fputs($fp1, "//--- Paramètres de connexion ---");
            fputs($fp1, "\$ndb      = \"" . $dbs . "\";");
            fputs($fp1, "\$nutil    = \"" . $utils . "\";");
            fputs($fp1, "\$nmdp     = \"" . $mdps . "\";");
            fputs($fp1, "\$nserveur = \"" . $serveurs . "\";");
            fputs($fp1, "//------------- fin -------------");
            fputs($fp1, "?>");
            fclose($fp1);
            $msgMaj = 'OKMaj';
        }
    }

    // L'utilsateur a cliqué sur Initialisation
    if ($maj == 'Initialisation') {
        include_once(__DIR__ . '/connexion_inc.php');
        $cnx = db_connect($nserveur, $ndb, $nutil, $nmdp);
        if ($cnx) {
            // Ecriture du fichier de préfixe en cas de changement de préfixe
            if ($prefixe != $pref_tables) {
                $nom_fic = 'param_part.php';
                // $nom_fic = $dirPathTarget.'param_part.php';
                $fic = fopen($nom_fic, 'w');
                if (! $fic) die("Impossible de créer $nom_fic. !");
                else {
                    fwrite($fic, '<?php' . $cr);
                    fwrite($fic, '$pref_tables = \'' . $prefixe . '\';' . $cr);
                    fwrite($fic, '?>' . $cr);
                    fclose($fic);
                }
                $pref_tables = $prefixe;
            }

            //Affiche toutes les erreurs sauf les notices
            error_reporting(E_ALL & ~E_NOTICE);
            // Ouverture du fichier et exécution des ordres
            $fic = fopen($chemin_exports . 'Export_Initialisation.sql', 'r');
            $lig_tot = '';
            $num_ligne = 0;

            while (!feof($fic)) {
                $ligne = trim(fgets($fic));
                $num_ligne++;
                //echo $ligne.'<br>';
                if (strlen($ligne) > 0)
                    $car1 = $ligne[0];
                else
                    $car1 = '';
                // La ligne 1 est forcément une ligne de commentaire qui ne sera pas exploitée
                if ($num_ligne == 1)
                    $car1 = '#';

                if (($car1 != '#') and ($ligne != '')) {
                    $lig_tot .= $ligne . " ";
                    if ($ligne[strlen($ligne) - 1] == ';') {
                        if ($lig_tot == '') {
                            $lig_tot = $ligne;
                        }
                        // Détection du nom de la table
                        //DROP TABLE IF EXISTS `arbreparam`;
                        if (strpos($lig_tot, 'DROP TABLE IF EXISTS ') === 0) {
                            $lig_tot = substr($lig_tot, 0, 22) . $pref_tables . substr($lig_tot, 22);
                        }
                        //CREATE TABLE `arbreparam` (
                        if (strpos($lig_tot, 'CREATE TABLE ') === 0) {
                            $lig_tot = substr($lig_tot, 0, 14) . $pref_tables . substr($lig_tot, 14);
                        }
                        //INSERT INTO arbreparam values ('repertoire','polices','c:/windows/fonts','Répertoire contenant les polices du système','4','1','101');
                        if (strpos($lig_tot, 'INSERT INTO ') === 0) {
                            $lig_tot = substr($lig_tot, 0, 12) . $pref_tables . substr($lig_tot, 12);
                        }
                        $res = maj_sql($lig_tot);
                        $msg .= $err;
                        $lig_tot = '';
                    }
                }
            }
            fclose($fic);
            // L'utilisateur a demandé l'initialisation en mode internet
            if ($envir == 'I') {
                $sql = 'update ' . nom_table('general') . ' set Environnement =\'I\';';
                $res = maj_sql($sql);
            }
            if ($msg == '') $msg = 'OKIni';
            $msgIni = $msg;
        }
    } // Fin du bloc d'initialisation

    // L'utilisateur a cliqué sur Mise à jour de la base
    if ($maj == 'Migration') {

        $msg = '';

        $n_commentaires = nom_table('commentaires');
        $n_personnes    = nom_table('personnes');
        $n_unions       = nom_table('unions');
        $n_evenements   = nom_table('evenements');
        $n_images       = nom_table('images');

        include_once(__DIR__ . '/connexion_inc.php');
        $cnx = db_connect($nserveur, $ndb, $nutil, $nmdp);
        if ($msg == $msg_cnx_ok) $msg = '';

        if ($cnx) {
            if ($res = lect_sql('select Version from ' . nom_table('general'))) {
                if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
                    $Num_Gen = $enreg['Version'];
                }
            }

            if ($Num_Gen == $LaVersion) $msg = 'La base est déjà en version ' . $LaVersion;
            if ($Num_Gen == '1.3') {
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' ADD `Diff_Internet_Note` CHAR( 1 ) DEFAULT \'O\' AFTER `Divers` ;';
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' ADD `Divers` VARCHAR( 200 ) AFTER `Ville_Notaire` ;';
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' ADD `Diff_Internet_Note` CHAR( 1 ) DEFAULT \'O\' AFTER `Divers` ;';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Lettre_B` VARCHAR( 80 );';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Image_Fond` VARCHAR( 80 );';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Coul_Fond_Table` CHAR( 7 );';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Adresse_Mail` VARCHAR( 80 ) ;';
                $req[] = 'update ' . nom_table('general') . ' set version=\'' . $LaVersion . '\';';
                $Num_Gen = '1.4';
                for ($nb = 0; $nb < count($req); $nb++) {
                    //echo $req[$nb].'<br>';
                    $res = maj_sql($req[$nb]);
                    $msg .= $err;
                }
                unset($req);
            }

            if ($Num_Gen == '1.4') {
                $req[] = 'ALTER TABLE ' . nom_table('images') . ' ADD `Defaut` CHAR( 1 ) DEFAULT \'N\' NOT NULL ;';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' MODIFY Nom VARCHAR( 50 );';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' MODIFY Prenoms VARCHAR( 50 );';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' MODIFY Profession VARCHAR( 50 );';
                $req[] = 'update ' . nom_table('general') . ' set version=\'' . $LaVersion . '\';';
                $Num_Gen == '1.5';
                for ($nb = 0; $nb < count($req); $nb++) {
                    //echo $req[$nb].'<br>';
                    $res = maj_sql($req[$nb]);
                    $msg .= $err;
                }
                unset($req);
            }

            if ($Num_Gen == '1.5') {
                // Modification de la table General
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Image_Arbre_Asc` VARCHAR( 80 ) ;';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Affiche_Mar_Arbre_Asc` CHAR( 1 ) ;';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Affiche_Annee` CHAR(1) DEFAULT \'N\' NOT NULL;';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Comportement` CHAR(1) DEFAULT \'C\' NOT NULL';
                // Alimentation des nouvelles zones
                $req[] = 'update ' . nom_table('general') . ' set Image_Arbre_Asc=\'arbre_asc_hor_carre.png\',' .
                    'Affiche_Mar_Arbre_Asc=\'O\',' .
                    'Affiche_Annee=\'N\',' .
                    'Comportement=\'C\';';
                // Création de la table concerne_objet
                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('concerne_objet') . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . nom_table('concerne_objet') . ' ('
                    . '  `Evenement` int(11) NOT NULL default \'0\','
                    . '  `Reference_Objet` int(11) NOT NULL default \'0\','
                    . '  `Type_Objet` char(3) default NULL,'
                    . '  PRIMARY KEY  (`Evenement`,`Reference_Objet`)'
                    . ') ENGINE=MyISAM;';
                // Création de la table evenements
                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('evenements') . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . nom_table('evenements') . ' ('
                    . '  `Reference` int(11) NOT NULL auto_increment,'
                    . '  `Identifiant_zone` int(11) default \'0\','
                    . '  `Identifiant_Niveau` int(11) default NULL,'
                    . '  `Code_Type` varchar(4) default NULL,'
                    . '  `Titre` varchar(50) NOT NULL default \'\','
                    . '  `Debut` varchar(10) default NULL,'
                    . '  `Fin` varchar(10) default NULL,'
                    . '  `Divers` varchar(200) default NULL,'
                    . '  `Diff_Internet_Note` char(1) default \'O\','
                    . '  `Date_Creation` datetime default \'0000-00-00 00:00:00\','
                    . '  `Date_Modification` datetime default \'0000-00-00 00:00:00\','
                    . '  `Statut_Fiche` char(1) default NULL,'
                    . '  PRIMARY KEY  (`Reference`)'
                    . ') ENGINE=MyISAM ;';
                // Création de la table participe
                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('participe') . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . nom_table('participe') . ' ('
                    . '  `Evenement` int(11) NOT NULL default \'0\','
                    . '  `Personne` int(11) NOT NULL default \'0\','
                    . '  `Code_Role` char(4) NOT NULL default \'\','
                    . '  `Debut` char(10) default NULL,'
                    . '  `Fin` char(10) default NULL,'
                    . '  `Pers_Principal` char(1) NOT NULL default \'\','
                    . '  PRIMARY KEY  (`Evenement`,`Personne`,`Code_Role`)'
                    . ') ENGINE=MyISAM ;';
                // Création de la table relation_personnes
                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('relation_personnes') . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . nom_table('relation_personnes') . ' ('
                    . '  `Personne_1` int(11) NOT NULL default \'0\','
                    . '  `Personne_2` int(11) NOT NULL default \'0\','
                    . '  `Code_Role` char(4) NOT NULL default \'\','
                    . '  `Debut` char(10) default NULL,'
                    . '  `Fin` char(10) default NULL,'
                    . '  PRIMARY KEY  (`Personne_1`,`Code_Role`,`Personne_2`)'
                    . ') ENGINE=MyISAM ;';
                // Création de la table roles
                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('roles') . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . nom_table('roles') . ' ('
                    . '  `Code_Role` varchar(4) NOT NULL default \'\','
                    . '  `Libelle_Role` varchar(50) default NULL,'
                    . '  PRIMARY KEY  (`Code_Role`)'
                    . ') ENGINE=MyISAM ;';
                // Création de la table types_evenement
                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('types_evenement') . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . nom_table('types_evenement') . ' ('
                    . '  `Code_Type` varchar(4) NOT NULL default \'\','
                    . '  `Libelle_Type` varchar(50) default NULL,'
                    . '  `Code_Modifiable` char(1) default NULL,'
                    . '  `Objet_Cible` char(1) NOT NULL default \'\','
                    . '  `Unicite` char(1) NOT NULL default \'\','
                    . '  `Type_Gedcom` char(1) NOT NULL default \'\','
                    . '  PRIMARY KEY  (`Code_Type`)'
                    . ') ENGINE=MyISAM ;';
                // Contenu de la table `types_evenement`
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'TITL\', \'Titre de noblesse ou honorifique\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'NATI\', \'Nationalité\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'ANUL\', \'Nullité du mariage\', \'N\', \'U\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'RESI\', \'Domicile\', \'N\', \'U\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'DIV\', \'Divorce\', \'N\', \'U\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'MARB\', \'Publication des bans\', \'N\', \'U\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'MARL\', \'Autorisation légale de mariage\', \'N\', \'U\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'MARS\', \'Convention ou contrat avant mariage\', \'N\', \'U\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'ENGA\', \'Fiançailles\', \'N\', \'U\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'EVEN\', \'Evènement\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'BURI\', \'Sépulture\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'CREM\', \'Crémation\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'RETI\', \'Retraite\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'PROB\', \'Validation d\'\'un testament\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'WILL\', \'Testament\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'GRAD\', \'Diplôme ou certificat\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'CENS\', \'Recensement de population\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'NATU\', \'Naturalisation\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'IMMI\', \'Immigration\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'EMIG\', \'Emigration\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'CHRA\', \'Baptême adulte non mormon\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'ORDN\', \'Ordination religieuse\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'BLES\', \'Bénédiction religieuse\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'BASM\', \'Bas mitzah\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'BARM\', \'Bar mitzvah\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'CONF\', \'Confirmation (religieuse)\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'FCOM\', \'Première communion\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'BAPM\', \'Baptême non mormon\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'CHR\', \'Baptême religieux\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'ADOP\', \'Adoption\', \'N\', \'F\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'CAST\', \'Rang ou statut\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'DSCR\', \'Description physique\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'EDUC\', \'Niveau d\'\'instruction\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'OCCU\', \'Profession\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'RELI\', \'Religion\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'PROP\', \'Bien ou possession\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'IDNO\', \'Identification externe\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'SSN\', \'Numéro de sécurité sociale\', \'N\', \'P\', \'U\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'FACT\', \'Fait ou caractéristique\', \'N\', \'P\', \'M\', \'O\');';
                $req[] = 'INSERT INTO ' . nom_table('types_evenement') .
                    ' VALUES (\'DIVF\', \'Dossier de divorce d\'\'une personne\', \'N\', \'U\', \'M\', \'O\');';
                // Contenu de la table `roles` : valeur défaut
                $req[] = 'INSERT INTO ' . nom_table('roles') .
                    ' VALUES (\'\', \'\');';
                // Passage des dates de baptême en évènement
                $ref_evt = 0;
                $sql = 'SELECT Reference, B_Le FROM ' . nom_table('personnes') . ' where B_Le is not null and B_LE <> \'\'';
                if ($res = lect_sql($sql)) {
                    //echo $res->rowCount().' personnes à traiter.<br>'
                    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                        $req[] = 'INSERT INTO ' . nom_table('evenements') .
                            ' (Code_Type,Titre,Debut,Fin,Date_Creation,Date_Modification,Statut_Fiche)' .
                            ' values (' .
                            '\'BAPM\', \'Baptême\',\'' . $enreg[1] . '\',null, ' .
                            'current_timestamp, current_timestamp,\'N\');';
                        $req[] = 'INSERT INTO ' . nom_table('participe') .
                            ' values (' .
                            ++$ref_evt . ',' . $enreg[0] . ',\'\',\'' . $enreg[1] . '\',null,\'O\');';
                    }
                }
                // Suppression de la colonne date de baptême
                $req[] = 'alter table ' . nom_table('personnes') . ' drop B_Le;';
                // Passage des professions en évènement
                $sql = 'SELECT Reference, Profession  FROM ' . nom_table('personnes') . ' where Profession is not null and Profession  <> \'\'';
                if ($res = lect_sql($sql)) {
                    //echo $res->rowCount().' personnes à traiter.<br>'
                    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                        $req[] = 'INSERT INTO ' . nom_table('evenements') .
                            ' (Code_Type,Titre,Debut,Fin,Date_Creation,Date_Modification,Statut_Fiche)' .
                            ' values (' .
                            '\'OCCU\', \'' . addslashes($enreg[1]) . '\',null,null, ' .
                            'current_timestamp, current_timestamp,\'N\');';
                        $req[] = 'INSERT INTO ' . nom_table('participe') .
                            ' values (' .
                            ++$ref_evt . ',' . $enreg[0] . ',\'\',null,null,\'O\');';
                    }
                }
                // Suppression de la colonne date de baptême
                $req[] = 'alter table ' . nom_table('personnes') . ' drop Profession;';
                // Modification du numéro de version
                $req[] = 'update ' . nom_table('general') . ' set version=\'' . $LaVersion . '\';';
                $Num_Gen == '2.0';
                for ($nb = 0; $nb < count($req); $nb++) {
                    $res = maj_sql($req[$nb]);
                    $msg .= $err;
                }
                unset($req);
            }

            if ($Num_Gen == '2.0') {
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Degrade` CHAR( 1 ) DEFAULT \'R\' NOT NULL ;';
                $req[] = 'update ' . nom_table('general') . ' set version=\'' . $LaVersion . '\';';
                $Num_Gen = req_maj_vers('2.1');
                for ($nb = 0; $nb < count($req); $nb++) {
                    //echo $req[$nb].'<br>';
                    $res = maj_sql($req[$nb]);
                    $msg .= $err;
                }
                unset($req);
            }

            if ($Num_Gen == '2.1') {
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Image_Barre` VARCHAR( 80 ) DEFAULT \'bar_off_bleu.gif\' NOT NULL ;';
                $Num_Gen = req_maj_vers('2.2');
            }

            if ($Num_Gen == '2.2') {
                // Création de la table commentaires
                $req[] = 'DROP TABLE IF EXISTS ' . $n_commentaires . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . $n_commentaires . ' ('
                    . '  `Commentaire` int(11) NOT NULL auto_increment,'
                    . '  `Reference_Objet` int(11) NOT NULL default \'0\','
                    . '  `Type_Objet` char(3) default NULL,'
                    . '  `Note` varchar(255) default NULL,'
                    . '  `Diff_Internet_Note` char(1) default \'O\','
                    . '  PRIMARY KEY  (`Commentaire`,`Reference_Objet`)'
                    . ') ENGINE=MyISAM;';

                // Déplacement des commentaires dans les tables origine
                // gestion des commantaires sur la table personnes
                $x = Traite_Commentaire('P');
                // gestion des commantaires sur la table unions
                $x = Traite_Commentaire('U');

                // Suppression des colonnes dans les tables de départ
                $req[] = 'alter table ' . $n_personnes . ' drop Divers;';
                $req[] = 'alter table ' . $n_personnes . ' drop Diff_Internet_Note;';
                $req[] = 'alter table ' . $n_unions . ' drop Divers;';
                $req[] = 'alter table ' . $n_unions . ' drop Diff_Internet_Note;';

                // Modification du libéllé sur le rôle par défaut
                $req[] = 'update ' . nom_table('roles') . ' set Libelle_Role = \'<Défaut>\' where Code_Role = \'\'';

                // Ajoute de la zone géographique dans la table participe
                $req[] = 'ALTER TABLE ' . nom_table('participe') . ' ADD `Identifiant_zone` int(11) default \'0\' NOT NULL ;';
                $req[] = 'ALTER TABLE ' . nom_table('participe') . ' ADD `Identifiant_Niveau` int(11) default \'0\' NOT NULL ;';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('contributions') . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . nom_table('contributions') . ' ('
                    . '  `Contribution` int(11) NOT NULL auto_increment,'
                    . '  `Reference_Personne` int(11) NOT NULL ,'
                    . '  `Mail` varchar(80) null,'
                    . '  `Statut` char(1) not null default \'N\','
                    . '  `Adresse_IP` varchar(20) not null,'
                    . '  `Date_Creation` datetime not null,'
                    . '  `Date_Modification` datetime not null,'
                    . '  PRIMARY KEY  (`Contribution`)'
                    . ') ENGINE=MyISAM;';
                $Num_Gen = req_maj_vers('2.3');
            }
            if ($Num_Gen == '2.3') {
                // Le nom de la version passe à 20 maxi
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' CHANGE `Version` `Version` VARCHAR( 20 );';

                // On passe le commentaire de 250 caractères au format TEXT ==> plus de 65 000
                $req[] = 'ALTER TABLE ' . nom_table('commentaires') . ' CHANGE `Note` `Note` TEXT';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('utilisateurs') . ';';
                $req[] = 'CREATE TABLE IF NOT EXISTS ' . nom_table('utilisateurs') . ' ('
                    . '`idUtil` INT( 11 ) NOT NULL AUTO_INCREMENT ,'
                    . '`nom` VARCHAR( 40 ) NOT NULL ,'
                    . '`codeUtil` VARCHAR( 35 ) NOT NULL ,'
                    . '`motPasseUtil` CHAR( 64 ) NOT NULL ,'
                    . '`niveau` CHAR( 1 ) DEFAULT \'I\' NOT NULL ,'
                    . 'PRIMARY KEY ( `idUtil` )'
                    . ') ENGINE=MyISAM ;';
                $req[] = 'INSERT INTO ' . nom_table('utilisateurs') . ' VALUES (\'\', \'Invité\',' .
                    '\'invité\',       \'\', \'I\');';
                // gestionnaire/gestionnaire
                $req[] = 'INSERT INTO ' . nom_table('utilisateurs') . ' VALUES (\'\', \'Gestionnaire\',' .
                    '\'gestionnaire\', \'cb5a837679b389074f2cbd407574f31ef5b98ca6d9f4fe3bb8101f62e43b5379\', \'G\');';
                $msg .= '<font color="green">Gestionnaire de la base : gestionnaire/gestionnaire</font> <br>';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('arbre') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('arbre') . ' ('
                    . '`idArbre` int(11) NOT NULL auto_increment,'
                    . '`nomFichier` varchar(20) NOT NULL ,'
                    . '`descArbre` varchar(200) NOT NULL ,'
                    . '`largeurPage` int(11) NOT NULL default \'0\','
                    . '`hauteurPage` int(11) NOT NULL default \'0\','
                    . '`nbPagesHor` int(11) NOT NULL default \'0\','
                    . '`nbPagesVer` int(11) NOT NULL default \'0\','
                    . '`lienPDF` char(1) NOT NULL default \'N\','
                    . '`dateCre` datetime NOT NULL default \'0000-00-00 00:00:00\','
                    . '`dateMod` datetime NOT NULL default \'0000-00-00 00:00:00\','
                    . 'PRIMARY KEY  (`idArbre`)'
                    . ') ENGINE=MyISAM ;';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('arbreetiquette') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('arbreetiquette') . ' ('
                    . '`idArbre` int(11) NOT NULL default \'0\','
                    . '`idEtiquette` int(11) NOT NULL default \'0\','
                    . '`texte` text NOT NULL,'
                    . '`nomEtiq` varchar(100) NOT NULL,'
                    . '`margeHaute` int(11) NOT NULL default \'0\','
                    . '`margeBasse` int(11) NOT NULL default \'0\','
                    . '`margeDroite` int(11) NOT NULL default \'0\','
                    . '`margeGauche` int(11) NOT NULL default \'0\','
                    . '`positionX` int(11) NOT NULL default \'0\','
                    . '`positionY` int(11) NOT NULL default \'0\','
                    . '`largeur` int(11) NOT NULL default \'0\','
                    . '`hauteur` int(11) NOT NULL default \'0\','
                    . '`couleurFond` varchar(12) NOT NULL,'
                    . '`couleurBord` varchar(12) NOT NULL,'
                    . '`largeurBordure` int(11) NOT NULL default \'0\','
                    . '`cadreForme` int(11) NOT NULL default \'0\','
                    . '`cadreLargeur` int(11) NOT NULL default \'0\','
                    . '`cadreHauteur` int(11) NOT NULL default \'0\','
                    . 'PRIMARY KEY  (`idArbre`,`idEtiquette`)'
                    . ') ENGINE=MyISAM ;';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('arbremodeleetiq') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('arbremodeleetiq') . ' ('
                    . '`idModele` int(11) NOT NULL auto_increment,'
                    . '`typeModele` char(1) NOT NULL,'
                    . '`nomModele` varchar(50) NOT NULL,'
                    . '`descModele` text,'
                    . '`modeleDefaut` char(1) NOT NULL,'
                    . '`dateCre` datetime NOT NULL default \'0000-00-00 00:00:00\','
                    . '`dateMod` datetime NOT NULL default \'0000-00-00 00:00:00\','
                    . 'PRIMARY KEY  (`idModele`)'
                    . ') ENGINE=MyISAM  ;';

                $req[] = 'INSERT INTO ' . nom_table('arbremodeleetiq')
                    . ' values (\'1\',\'P\',\'Modèle par défaut\',\'<html>'
                    . '<head></head>'
                    . '<body>'
                    . '<p align="center" style="margin-top: 0">'
                    . '<b><font size="4" color="#000000" face="Georgia">&lt;prenomUsuel&gt; &lt;nom&gt; </font></b>'
                    . '</p>'
                    . '<p align="center" style="margin-top: 0">'
                    . '<font size="4" color="#000000" face="Georgia">&lt;SI&gt;&lt;dateNais&gt;&lt;ALORS&gt; +'
                    . '&lt;dateNais&gt; &lt;villeNais&gt;&lt;FINSI&gt; </font>'
                    . '</p>'
                    . '<p align="center" style="margin-top: 0">'
                    . '<font size="4" color="#000000" face="Georgia">&lt;SI&gt;&lt;dateDeces&gt;&lt;ALORS&gt; +'
                    . '&lt;dateDeces&gt; &lt;villeDeces&gt;&lt;FINSI&gt;</font>'
                    . '</p>'
                    . '</body>'
                    . '</html>\','
                    . '\'D\',current_timestamp,current_timestamp);';

                $req[] = 'INSERT INTO ' . nom_table('arbremodeleetiq')
                    . ' values (\'2\',\'U\',\'Modèle par défaut\',\'<html>'
                    . '<head></head>'
                    . '<body>'
                    . '<p style=\"margin-top: 0\">'
                    . '<font size=\"3\" face=\"Georgia\">&lt;dateMariage&gt;&lt;SI&gt;&lt;villeMariage&gt;&lt;ALORS&gt;'
                    . '</font>    </p>'
                    . '<p style=\"margin-top: 0\">'
                    . '<font size=\"3\" face=\"Georgia\">(&lt;villeMariage&gt;)&lt;FINSI&gt; </font>'
                    . '</p>'
                    . '</body>'
                    . '</html>\','
                    . '\'D\',current_timestamp,current_timestamp);';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('arbreparam') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('arbreparam') . ' ('
                    . '`ident1` varchar(10) NOT NULL,'
                    . '`ident2` varchar(15) NOT NULL,'
                    . '`valeur` varchar(50) NOT NULL,'
                    . '`description` varchar(200) NOT NULL,'
                    . '`type` tinyint(4) NOT NULL default \'0\','
                    . '`limites` varchar(30) default NULL,'
                    . '`ordre` tinyint(4) NOT NULL default \'0\','
                    . 'PRIMARY KEY  (`ident1`,`ident2`)'
                    . ') ENGINE=MyISAM ;';

                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'repertoire\', \'polices\', \'c:/windows/fonts\', \'Répertoire contenant les polices du système\', 4, \'1\', 101);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'parametres\', \'titre\', \'\', \'Renseignements généraux\', 0, \'\', 120);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'dimension\', \'personne\', \'14\', \'Dimension du symbole d\'\'une personne\', 1, \'10,25\', 21);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'dimension\', \'ecartUnion\', \'20\', \'Ecart entre deux personnes unies\', 1, \'10,50\', 22);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'dimension\', \'ecartGener\', \'110\', \'Distance entre deux générations\', 1, \'50,200\', 23);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'parametres\', \'dateModif\', current_timestamp, \'Date de la dernière modification des préférences\', 5, \'\', 127);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'repertoire\', \'genPdf\', \'fichiers/pdf\', \'Répertoire pour générer les fichiers PDF\', 4, \'2\', 102);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'repertoire\', \'genImg\', \'fichiers/images\', \'Répertoire pour générer les images\', 4, \'2\', 103);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'dimension\', \'titre\', \'\', \'Dimensions\', 0, \'\', 20);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'parametres\', \'version\', \'1\', \'Numéro de la version\', 5, \'\', 126);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'repertoire\', \'titre\', \'\', \'Répertoires\', 0, \'\', 100);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'homme\', \'coulFond\', \'255,255,255\', \'Couleur de fond\', 3, \'\', 61);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'homme\', \'titre\', \'\', \'Symbole d\'\'un homme\', 0, \'\', 60);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'femme\', \'coulFond\', \'255,255,255\', \'Couleur de fond\', 3, \'\', 71);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'femme\', \'titre\', \'\', \'Symbole d\'\'une femme\', 0, \'\', 70);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'personne\', \'titre\', \'\', \'Symbole d\'\'une personne (sexe inconnu)\', 0, \'\', 80);';
                $req[] = 'INSERT INTO ' . nom_table('arbreparam') . ' VALUES (\'personne\', \'coulFond\', \'255,255,255\', \'Couleur de fond\', 3, \'\', 81);';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('arbrepers') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('arbrepers') . ' ('
                    . '`idArbre` int(11) NOT NULL default \'0\','
                    . '`reference` int(11) NOT NULL default \'0\','
                    . '`posX` int(5) NOT NULL default \'0\','
                    . '`posY` int(5) NOT NULL default \'0\','
                    . '`ecartEtiqX` int(5) NOT NULL default \'0\','
                    . '`ecartEtiqY` int(5) NOT NULL default \'0\','
                    . '`ecartLienX` int(5) NOT NULL default \'0\','
                    . '`ecartLienY` int(5) NOT NULL default \'0\','
                    . '`idModele` int(11) NOT NULL default \'0\','
                    . 'PRIMARY KEY  (`idArbre`,`reference`)'
                    . ') ENGINE=MyISAM ;';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('arbrephotos') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('arbrephotos') . ' ('
                    . '`idArbre` int(11) NOT NULL default \'0\','
                    . '`numImage` int(11) NOT NULL default \'0\','
                    . '`reference` int(11) NOT NULL default \'0\','
                    . '`nomFichier` varchar(200) character set utf8 collate utf8_unicode_ci NOT NULL,'
                    . '`ratio` float(7,6) NOT NULL default \'0.000000\','
                    . '`posX` int(11) NOT NULL default \'0\','
                    . '`posY` int(11) NOT NULL default \'0\','
                    . 'PRIMARY KEY  (`idArbre`,`numImage`)'
                    . ') ENGINE=MyISAM ;';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('arbreunion') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('arbreunion') . ' ('
                    . '`idArbre` int(11) NOT NULL default \'0\','
                    . '`refParent1` int(11) NOT NULL default \'0\','
                    . '`refParent2` int(11) NOT NULL default \'0\','
                    . '`typeLienParents` tinyint(4) NOT NULL default \'0\','
                    . '`ecartPtDebParents` int(11) NOT NULL default \'0\','
                    . '`ecartPtFinParents` int(11) NOT NULL default \'0\','
                    . '`ratio1Parents` float(9,6) NOT NULL default \'0.000000\','
                    . '`ratio2Parents` float(9,6) NOT NULL default \'0.000000\','
                    . '`ratio3Parents` float(9,6) NOT NULL default \'0.000000\','
                    . '`typeLienFam` tinyint(4) NOT NULL default \'0\','
                    . '`ratio1Fam` float(9,6) NOT NULL default \'0.000000\','
                    . '`ratio2Fam` float(9,6) NOT NULL default \'0.000000\','
                    . '`ratio3Fam` float(9,6) NOT NULL default \'0.000000\','
                    . '`idModele` int(11) NOT NULL default \'0\','
                    . 'PRIMARY KEY  (`idArbre`,`refParent1`,`refParent2`)'
                    . ') ENGINE=MyISAM  ;';

                $Num_Gen = req_maj_vers('3.0');
            }

            if ($Num_Gen == '3.0') {
                $Num_Gen = req_maj_vers('3.0.1');
            }

            if ($Num_Gen == '3.0.1') {
                $Num_Gen = req_maj_vers('3.0.2');
            }

            if ($Num_Gen == '3.0.2') {
                $req[] = 'ALTER TABLE ' . nom_table('roles') . ' ADD `Symetrie` CHAR( 1 ) DEFAULT \'O\' NOT NULL';
                $req[] = 'ALTER TABLE ' . nom_table('roles') . ' ADD `Libelle_Inv_Role` VARCHAR( 50 )';
                $req[] = 'ALTER TABLE ' . nom_table('relation_personnes') . ' ADD `Principale` CHAR( 1 ) DEFAULT \'O\' NOT NULL';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Date_Modification` datetime DEFAULT \'0000-00-00 00:00:00\' NOT NULL';
                $req[] = 'ALTER TABLE ' . $n_images . ' ADD `Titre` VARCHAR( 80 ) NOT NULL';

                // Déplacement des commentaires dans les tables origine ; cela avit été oublié sur la table evenements
                $x = Traite_Commentaire('E');
                // Suppression des colonnes dans les tables de départ
                $req[] = 'alter table ' . $n_evenements . ' drop Divers';
                $req[] = 'alter table ' . $n_evenements . ' drop Diff_Internet_Note';

                // La zone description de la table images est transformée en commentaires et on ajoute un titre
                $sql = 'SELECT ident_image, description from ' . $n_images;
                if ($res = lect_sql($sql)) {
                    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                        $image = $enreg[0];
                        $note = $enreg[1];
                        $titre = substr($note, 0, 80);
                        // Insertion de l'enregistrement en table commentaires
                        $req[] = 'INSERT INTO ' . $n_commentaires .
                            ' (Reference_Objet,Type_Objet,Note,Diff_Internet_Note) values (' .
                            $image . ',\'I\',\'' . addslashes($note) . '\',\'O\');';
                        // reprise du commentaire dans le titre
                        $req[] = 'update ' . $n_images . ' set Titre = \'' . addslashes($titre) . '\' where ident_image = ' . $image;
                    }
                }
                $req[] = 'alter table ' . $n_images . ' drop description;';

                $req[] = 'ALTER TABLE ' . nom_table('general') . ' CHANGE `Nom` `Nom` VARCHAR( 80 ) NOT NULL DEFAULT \'???\'';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' CHANGE `Adresse_Mail` `Adresse_Mail` VARCHAR( 80 ) NOT NULL DEFAULT \'support@geneamania.net\'';

                // Insertion du lien vers le nom principal
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' ADD `idNomFam` int(11)';

                // Traitement des noms de famille pour la phonétique
                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('noms_famille') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('noms_famille') . ' ('
                    . '`idNomFam` int(11) NOT NULL auto_increment,'
                    . ' `nomFamille` varchar(50) character set utf8 collate utf8_swedish_ci default NULL,'
                    . '`codePhonetique` varchar(50) character set utf8 collate utf8_bin default NULL,'
                    . 'PRIMARY KEY  (`idNomFam`)'
                    . ') ENGINE=MyISAM ';

                // Création d'une table des liens personnes / noms
                $req[] = 'CREATE TABLE ' . nom_table('noms_personnes') . ' ('
                    . '`idPers` int(11) NOT NULL default \'0\','
                    . '`idNom` int(11) NOT NULL default \'0\','
                    . '`princ` char(1) not null default \'N\','
                    . '`comment` varchar(50),'
                    . 'PRIMARY KEY  (`idPers`,`idNom`)'
                    . ') ENGINE=MyISAM ';


                require(__DIR__ . '/app/Phonetique.php');
                $codePho = new Phonetique();

                $Anom = '';
                $idNom = 0;
                $sql = 'SELECT UPPER(Nom), Reference FROM ' . nom_table('personnes') . ' order by UPPER(Nom)';
                if ($res = lect_sql($sql)) {
                    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                        $nom = $enreg[0];
                        $refPers = $enreg[1];
                        // Traitements en rupture sur le nom
                        if ($nom != $Anom) {
                            $Anom = $nom;
                            //    Calcul d'un code phonétique
                            $code = $codePho->calculer($nom);
                            $idNom++;
                            // Création de l'enregistrement dans la table des noms de famille
                            $req[] = 'insert into ' . nom_table('noms_famille') . ' values(' . $idNom . ',\'' . addslashes($nom) . '\',\'' . $code . '\')';
                        }
                        // Modification de la table des personnes
                        $req[] = 'update ' . nom_table('personnes') . ' set idNomFam=' . $idNom . ' where Reference=' . $refPers;
                        // Création de l'enregistrement dans la table des liens personnes / noms
                        $req[] = 'insert into ' . nom_table('noms_personnes') . ' values(' . $refPers . ',' . $idNom . ',\'O\',null)';
                    }
                    $res->CloseCursor();
                }
                $Num_Gen = req_maj_vers('4.00 beta 1');
            }

            if ($Num_Gen == '4.00 beta 1') {
                $Num_Gen = req_maj_vers('4.00 beta 2');
            }

            if ($Num_Gen == '4.00 beta 2') {
                $req[] = 'insert into ' . nom_table('types_evenement') . ' values("AC3U", "Actualités","N","A","M","N");';
                $req[] = 'ALTER TABLE ' . nom_table('evenements') . ' CHANGE `Titre` `Titre` VARCHAR( 80 ) NOT NULL DEFAULT \'-\'';
                $req[] = 'insert into ' . nom_table('evenements') . ' values(null,0,0,"AC3U","mise en ligne du site personnel sur Multimania","20030113GL","20030113GL",current_timestamp,current_timestamp,"V")';
                $req[] = 'insert into ' . nom_table('evenements') . ' values(null,0,0,"AC3U","première diffusion du logiciel sous le nom de monSSG","20060228GL","20060228GL",current_timestamp,current_timestamp,"V")';
                $req[] = 'insert into ' . nom_table('evenements') . ' values(null,0,0,"AC3U","monSSG devient Généamania","20070518GL","20070518GL",current_timestamp,current_timestamp,"V")';
                $Num_Gen = req_maj_vers('4.00 beta 3');
            }

            if ($Num_Gen == '4.00 beta 3') {
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' CHANGE `Nom_Ville` `Nom_Ville` VARCHAR( 80 ) NOT NULL DEFAULT \'-\'';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' ADD INDEX ( `idNomFam` ) ';
                $Num_Gen = req_maj_vers('4.00 RC 1');
            }

            if ($Num_Gen == '4.00 RC 1') {
                $Num_Gen = req_maj_vers('4.00 RC 2');
            }
            if ($Num_Gen == '4.00 RC 2') {
                $Num_Gen = req_maj_vers('4.00 RC 3');
            }
            if ($Num_Gen == '4.00 RC 3') {
                $Num_Gen = req_maj_vers('4.00');
            }
            if ($Num_Gen == '4.00') {
                $Num_Gen = req_maj_vers('4.0.1');
            }

            if ($Num_Gen == '4.0.1') {
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' ADD `Zone_Mere` INT';
                $req[] = 'UPDATE ' . nom_table('villes') . ' a SET Zone_Mere = ( SELECT Zone_Mere FROM ' . nom_table('zones_geographiques') . ' WHERE Identifiant_Zone = a.Identifiant_Zone AND Identifiant_Niveau =4 )';

                $req[] = 'ALTER TABLE ' . nom_table('departements') . ' ADD `Zone_Mere` INT';
                $req[] = 'UPDATE ' . nom_table('departements') . ' a SET Zone_Mere = ( SELECT Zone_Mere FROM ' . nom_table('zones_geographiques') . ' WHERE Identifiant_Zone = a.Identifiant_Zone AND Identifiant_Niveau =3 )';

                $req[] = 'ALTER TABLE ' . nom_table('regions') . ' ADD `Zone_Mere` INT';
                $req[] = 'UPDATE ' . nom_table('regions') . ' a SET Zone_Mere = ( SELECT Zone_Mere FROM ' . nom_table('zones_geographiques') . ' WHERE Identifiant_Zone = a.Identifiant_Zone AND Identifiant_Niveau =2 )';

                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('zones_geographiques');

                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Coul_Lib` varchar(7) DEFAULT \'#B8A165\'';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Coul_Val` varchar(7) DEFAULT \'#B1A980\'';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Coul_Bord` varchar(7) DEFAULT \'#49453B\'';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Coul_Paires` varchar(7) DEFAULT \'#B3A17E\'';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Coul_Impaires` varchar(7) DEFAULT \'#C2BA98\'';

                $Num_Gen = req_maj_vers('4.1 alpha 1');
            }
            if ($Num_Gen == '4.1 alpha 1') {
                $req[] = 'INSERT INTO ' . nom_table('regions') . ' values (0,\'\',\'\',current_timestamp,current_timestamp,\'V\',0)';
                $req[] = 'INSERT INTO ' . nom_table('pays') . ' values (0,\'\',\'\',0,\'\',current_timestamp,current_timestamp,\'V\')';

                $Num_Gen = req_maj_vers('4.1 alpha 2');
            }

            if ($Num_Gen == '4.1 alpha 2') {
                $req[] = 'UPDATE ' . nom_table('villes') . ' SET Zone_Mere = 0 WHERE Zone_Mere is null';
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' CHANGE `Zone_Mere` `Zone_Mere` INT( 11 ) NOT NULL DEFAULT \'0\'';
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' ADD INDEX ( `Zone_Mere` )';
                $req[] = 'UPDATE ' . nom_table('departements') . ' SET Zone_Mere = 0 WHERE Zone_Mere is null';
                $req[] = 'ALTER TABLE ' . nom_table('departements') . ' CHANGE `Zone_Mere` `Zone_Mere` INT( 11 ) NOT NULL DEFAULT \'0\'';
                $req[] = 'UPDATE ' . nom_table('regions') . ' SET Zone_Mere = 0 WHERE Zone_Mere is null';
                $req[] = 'ALTER TABLE ' . nom_table('regions') . ' CHANGE `Zone_Mere` `Zone_Mere` INT( 11 ) NOT NULL DEFAULT \'0\'';

                // Création de la table des types de documents
                $req[] = 'CREATE TABLE ' . nom_table('types_doc') . ' ('
                    . ' `Id_Type_Document` int(11) NOT NULL auto_increment,'
                    . ' `Libelle_Type` varchar(80) NOT NULL default \'\','
                    . ' PRIMARY KEY  (`Id_Type_Document`)'
                    . ') ENGINE=MyISAM';

                // Création de la table des documents
                $req[] = 'CREATE TABLE ' . nom_table('documents') . ' ('
                    . ' `Id_Document` int(11) NOT NULL auto_increment,'
                    . ' `Nature_Document` char(3) NOT NULL default \'\','
                    . ' `Titre` varchar(80) NOT NULL default \'\','
                    . ' `Nom_Fichier` varchar(160) NOT NULL default  \'\','
                    . ' `Diff_Internet` char(1) NOT NULL default \'N\','
                    . ' `Date_Creation` datetime NOT NULL default \'0000-00-00 00:00:00\','
                    . ' `Date_Modification` datetime NOT NULL default \'0000-00-00 00:00:00\','
                    . ' `Id_Type_Document` int(11) NOT NULL default \'0\','
                    . ' PRIMARY KEY  (`Id_Document`)'
                    . ') ENGINE=MyISAM';

                // Création de la table de lien entre les documents et les objets
                $req[] = 'CREATE TABLE ' . nom_table('concerne_doc') . ' ('
                    . '`Id_Document` int(11) NOT NULL default \'0\','
                    . '`Reference_Objet` int(11) NOT NULL default \'0\','
                    . '`Type_Objet` char(1) NOT NULL default \'\','
                    . '`Defaut` char(1) NOT NULL default \'N\','
                    . 'PRIMARY KEY  (`Id_Document`,`Reference_Objet`,`Type_Objet`)'
                    . ') ENGINE=MyISAM';

                $Num_Gen = req_maj_vers('4.1 beta 1');
            }

            if ($Num_Gen == '4.1 beta 1') {
                $req[] = 'UPDATE ' . nom_table('types_evenement') . ' SET Objet_Cible = \'-\' WHERE Code_Type = \'AC3U\'';
                $Num_Gen = req_maj_vers('4.1 beta 2');
            }

            if ($Num_Gen == '4.1 beta 2') {
                $Num_Gen = req_maj_vers('4.1 beta 3');
            }

            if ($Num_Gen == '4.1 beta 3') {
                $Num_Gen = req_maj_vers('4.1 RC 1');
            }

            if ($Num_Gen == '4.1 RC 1') {
                $Num_Gen = req_maj_vers('4.1 RC 2');
            }

            if ($Num_Gen == '4.1 RC 2') {
                $Num_Gen = req_maj_vers('4.1 RC 3');
            }

            if ($Num_Gen == '4.1 RC 3') {
                $Num_Gen = req_maj_vers('4.1');
            }

            if ($Num_Gen == '4.1') {
                // Création d'un index sur la table des personnes et ajout de la catégorie
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' ADD INDEX `numero` ( `Numero` ) ';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' ADD `Categorie` INT( 2 ) NOT NULL ';

                // Création de la table des catégories
                $req[] = 'CREATE TABLE ' . nom_table('categories') . ' ('
                    . '`Identifiant` INT NOT NULL AUTO_INCREMENT ,'
                    . '`Image` VARCHAR( 20 ) NOT NULL ,'
                    . '`Titre` VARCHAR( 80 ) NOT NULL ,'
                    . '`Ordre_Tri` INT NOT NULL ,'
                    . 'PRIMARY KEY ( `Identifiant` )'
                    . ') ENGINE = MYISAM ';
                // Création des catégories
                $req[] = 'insert into ' . nom_table('categories') . ' values(1,\'bleu\',\'Catégorie bleue\',1)';
                $req[] = 'insert into ' . nom_table('categories') . ' values(2,\'vert\',\'Catégorie verte\',2)';
                $req[] = 'insert into ' . nom_table('categories') . ' values(3,\'orange\',\'Catégorie orange\',3)';
                $req[] = 'insert into ' . nom_table('categories') . ' values(4,\'rose\',\'Catégorie rose\',4)';
                $req[] = 'insert into ' . nom_table('categories') . ' values(5,\'violet\',\'Catégorie violette\',5)';
                $req[] = 'insert into ' . nom_table('categories') . ' values(6,\'rouge\',\'Catégorie rouge\',6)';
                $req[] = 'insert into ' . nom_table('categories') . ' values(7,\'jaune\',\'Catégorie jaune\',7)';

                $Num_Gen = req_maj_vers('4.2 alpha 1');
            }

            if ($Num_Gen == '4.2 alpha 1') {
                $Num_Gen = req_maj_vers('4.2 beta 1');
            }

            if ($Num_Gen == '4.2 beta 1') {
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' ADD `Latitude` FLOAT';
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' ADD `Longitude` FLOAT';
                $req[] = 'ALTER TABLE ' . nom_table('images') . ' ADD `Diff_Internet_Img` ENUM(\'o\',\'n\') NOT NULL DEFAULT \'o\'';
                $Num_Gen = req_maj_vers('4.2 beta 2');
            }

            if ($Num_Gen == '4.2 beta 2') {
                $Num_Gen = req_maj_vers('4.2 RC 1');
            }

            if ($Num_Gen == '4.2 RC 1') {
                $Num_Gen = req_maj_vers('4.2 RC 2');
            }

            if ($Num_Gen == '4.2 RC 2') {
                $Num_Gen = req_maj_vers('4.2');
            }

            if ($Num_Gen == '4.2') {
                $Num_Gen = req_maj_vers('4.2.1');
            }

            if ($Num_Gen == '4.2.1') {
                $Num_Gen = req_maj_vers('4.2.2');
            }

            if ($Num_Gen == '4.2.2') {
                // Création de la table des requêtes sur les personnes
                $req[] = 'CREATE TABLE ' . nom_table('requetes') . ' ('
                    . '`Reference` int(11) NOT NULL AUTO_INCREMENT,'
                    . '`Titre` varchar(80) NOT NULL,'
                    . '`Criteres` varchar(512) NOT NULL,'
                    . '`Code_SQL` varchar(512) NOT NULL,'
                    . 'PRIMARY KEY (`Reference`)'
                    . ') ENGINE=MyISAM ';

                $Num_Gen = req_maj_vers('4.3 beta 1');
            }

            if ($Num_Gen == '4.3 beta 1') {
                $Num_Gen = req_maj_vers('4.3 beta 2');
            }

            if ($Num_Gen == '4.3 beta 2') {
                $Num_Gen = req_maj_vers('4.3 beta 3');
            }

            if ($Num_Gen == '4.3 beta 3') {
                $Num_Gen = req_maj_vers('4.3 RC 1');
            }

            if ($Num_Gen == '4.3 RC 1') {
                $Num_Gen = req_maj_vers('4.3 RC 2');
            }

            if ($Num_Gen == '4.3 RC 2') {
                $Num_Gen = req_maj_vers('4.3');
            }

            if ($Num_Gen == '4.3') {
                $req[] = 'UPDATE ' . $n_personnes . ' SET Diff_Internet = \'N\' WHERE Diff_Internet = \'\'';
                $Num_Gen = req_maj_vers('4.4 beta 1');
            }

            if ($Num_Gen == '4.4 beta 1') {
                $Num_Gen = req_maj_vers('4.4 beta 2');
            }

            if ($Num_Gen == '4.4 beta 2') {
                $Num_Gen = req_maj_vers('4.4 RC 1');
            }

            if ($Num_Gen == '4.4 RC 1') {
                $Num_Gen = req_maj_vers('4.4 RC 2');
            }

            if ($Num_Gen == '4.4 RC 2') {
                $Num_Gen = req_maj_vers('4.4 RC 3');
            }

            if ($Num_Gen == '4.4 RC 3') {
                $Num_Gen = req_maj_vers('4.4 RC 4');
            }

            if ($Num_Gen == '4.4 RC 4') {
                $req[] = 'ALTER TABLE ' . nom_table('participe') . ' ADD `Dans_Etiquette_GeneGraphe` ENUM(\'o\',\'n\') NOT NULL DEFAULT \'n\'';
                $Num_Gen = req_maj_vers('5.0 RC 1');
            }

            if ($Num_Gen == '5.0 RC 1') {
                $Num_Gen = req_maj_vers('5.0');
            }

            if ($Num_Gen == '5.0') {
                $Num_Gen = req_maj_vers('5.0.1');
            }

            if ($Num_Gen == '5.0.1') {
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' ADD `Surnom` VARCHAR( 50 ) NULL';
                $Num_Gen = req_maj_vers('6.0 alpha 1');
            }

            if ($Num_Gen == '6.0 alpha 1') {
                $req[] = 'ALTER TABLE ' . nom_table('utilisateurs') . ' ADD `Adresse` VARCHAR( 80 ) NULL';
                $req[] = 'CREATE TABLE ' . nom_table('depots') . ' ('
                    . '`Ident` INT NOT NULL AUTO_INCREMENT ,'
                    . '`Nom` VARCHAR( 100 ) NOT NULL ,'
                    . 'PRIMARY KEY (`Ident`)'
                    . ') ENGINE=MyISAM ';
                $Num_Gen = req_maj_vers('6.0 alpha 2');
            }

            if ($Num_Gen == '6.0 alpha 2') {
                $req[] = 'CREATE TABLE ' . nom_table('sources') . ' ('
                    . '`Ident` INT NOT NULL AUTO_INCREMENT ,'
                    . '`Titre` VARCHAR( 100 ) NOT NULL ,'
                    . '`Auteur` VARCHAR( 100 ) NULL ,'
                    . '`Classement` VARCHAR( 100 ) NULL ,'
                    . '`Ident_Depot` INT NOT NULL ,'
                    . '`Ident_Depot_Tempo` VARCHAR( 100 ) NULL ,'
                    . '`Cote` VARCHAR( 100 ) NULL ,'
                    . '`Adresse_Web` VARCHAR( 100 ) NULL,'
                    . '`Ident_Source_Tempo` VARCHAR( 100 ) NULL,'
                    . ' PRIMARY KEY (`Ident`)'
                    . ') ENGINE=MyISAM ';
                $req[] = 'SET SESSION sql_mode=\'NO_AUTO_VALUE_ON_ZERO\';';
                $req[] = 'INSERT INTO ' . nom_table('depots') . ' (`Ident`, `Nom`) VALUES (\'0\', \'Dépôt générique\');';
                $req[] = 'SET SESSION sql_mode=\'\';';
                $req[] = 'ALTER TABLE ' . nom_table('depots') . ' ADD `Ident_Depot_Tempo` VARCHAR( 100 ) NULL';
                $req[] = 'CREATE TABLE ' . nom_table('concerne_source') . ' ('
                    . '`Ident` INT NOT NULL AUTO_INCREMENT ,'
                    . '`Id_Source` int(11) NOT NULL DEFAULT \'0\','
                    . '`Reference_Objet` int(11) NOT NULL ,'
                    . '`Type_Objet` char(1) NOT NULL ,'
                    . '`Id_Source_Tempo` VARCHAR( 100 ) NULL ,'
                    . ' PRIMARY KEY (`Ident`),'
                    . ' KEY `Id_Source` (`Id_Source`),'
                    . ' KEY `Reference_Objet` (`Reference_Objet`)'
                    . ') ENGINE=MyISAM ';
                $Num_Gen = req_maj_vers('6.0 beta 1');
            }

            if ($Num_Gen == '6.0 beta 1') {
                $req[] = 'UPDATE ' . nom_table('roles') . ' SET Libelle_Role = \'-- Défaut --\' WHERE Code_Role = \'\' AND Libelle_Role= \'<Défaut>\'';
                $Num_Gen = req_maj_vers('6.0 beta 2');
            }

            if ($Num_Gen == '6.0 beta 2') {
                $Num_Gen = req_maj_vers('6.0 beta 3');
            }

            if ($Num_Gen == '6.0 beta 3') {
                $req[] = 'ALTER TABLE ' . nom_table('liens') . ' CHANGE `description` `description` VARCHAR( 255 )';
                $req[] = 'ALTER TABLE ' . nom_table('liens') . ' CHANGE `URL` `URL` VARCHAR( 255 )';

                $Num_Gen = req_maj_vers('6.0 RC 1');
            }

            if ($Num_Gen == '6.0 RC 1') {
                $Num_Gen = req_maj_vers('6.0 RC 2');
            }

            if ($Num_Gen == '6.0 RC 2') {
                $Num_Gen = req_maj_vers('6.0 RC 3');
            }

            if ($Num_Gen == '6.0 RC 3') {
                $Num_Gen = req_maj_vers('6.0');
            }

            if ($Num_Gen == '6.0') {
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Pivot_Masquage` SMALLINT DEFAULT 9999';

                $Num_Gen = req_maj_vers('6.1 beta 1');
            }

            if ($Num_Gen == '6.1 beta 1') {
                $Num_Gen = req_maj_vers('6.1 beta 2');
            }

            if ($Num_Gen == '6.1 beta 2') {
                $Num_Gen = req_maj_vers('6.1 RC 1');
            }

            if ($Num_Gen == '6.1 RC 1') {
                $Num_Gen = req_maj_vers('6.1 RC 2');
            }

            if ($Num_Gen == '6.1 RC 2') {
                $Num_Gen = req_maj_vers('6.1');
            }

            if ($Num_Gen == '6.1') {
                $Num_Gen = req_maj_vers('6.1.1');
            }

            if ($Num_Gen == '6.1.1') {
                $Num_Gen = req_maj_vers('6.2 alpha 1');
            }

            if ($Num_Gen == '6.2 alpha 1') {
                $Num_Gen = req_maj_vers('6.2 beta 1');
            }

            if ($Num_Gen == '6.2 beta 1') {
                $Num_Gen = req_maj_vers('6.2 RC 1');
            }

            if ($Num_Gen == '6.2 RC 1') {
                $Num_Gen = req_maj_vers('6.2');
            }

            if ($Num_Gen == '6.2') {
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Image_Index` VARCHAR( 80 );';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Font_Pdf` VARCHAR( 80 ) NOT NULL DEFAULT \'Arial\'';
                $Num_Gen = req_maj_vers('6.3 beta 1');
            }

            if ($Num_Gen == '6.3 beta 1') {
                $Num_Gen = req_maj_vers('6.3 beta 2');
            }

            if ($Num_Gen == '6.3 beta 2') {
                $Num_Gen = req_maj_vers('6.3 RC 1');
            }

            if ($Num_Gen == '6.3 RC 1') {
                $Num_Gen = req_maj_vers('6.3');
            }

            if ($Num_Gen == '6.3') {
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Coul_PDF` CHAR( 7 ) NOT NULL DEFAULT \'#000000\';';
                $Num_Gen = req_maj_vers('6.4 beta 1');
            }

            if ($Num_Gen == '6.4 beta 1') {
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' CHANGE `Ref_Mari` `Conjoint_1` INT( 11 ) NOT NULL DEFAULT \'0\'';
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' CHANGE `Ref_Femme` `Conjoint_2` INT( 11 ) NOT NULL DEFAULT \'0\'';
                $Num_Gen = req_maj_vers('6.4 beta 2');
            }

            if ($Num_Gen == '6.4 beta 2') {
                $Num_Gen = req_maj_vers('6.4 beta 3');
            }

            if ($Num_Gen == '6.4 beta 3') {
                $Num_Gen = req_maj_vers('6.4 RC 1');
            }

            if ($Num_Gen == '6.4 RC 1') {
                $Num_Gen = req_maj_vers('6.4 RC 2');
            }

            if ($Num_Gen == '6.4 RC 2') {
                $Num_Gen = req_maj_vers('6.4');
            }

            if ($Num_Gen == '6.4') {
                $Num_Gen = req_maj_vers('6.5 beta 1');
            }

            if ($Num_Gen == '6.5 beta 1') {
                $Num_Gen = req_maj_vers('6.5 beta 2');
            }

            if ($Num_Gen == '6.5 beta 2') {
                $Num_Gen = req_maj_vers('6.5 beta 3');
            }

            if ($Num_Gen == '6.5 beta 3') {
                $Num_Gen = req_maj_vers('6.5 RC 1');
            }

            if ($Num_Gen == '6.5 RC 1') {
                $Num_Gen = req_maj_vers('6.5 RC 2');
            }

            if ($Num_Gen == '6.5 RC 2') {
                $Num_Gen = req_maj_vers('6.5 RC 3');
            }

            if ($Num_Gen == '6.5 RC 3') {
                $Num_Gen = req_maj_vers('6.5');
            }

            if ($Num_Gen == '6.5') {
                $Num_Gen = req_maj_vers('6.6 beta 1');
            }

            if ($Num_Gen == '6.6 beta 1') {
                $Num_Gen = req_maj_vers('6.6 beta 2');
            }

            if ($Num_Gen == '6.6 beta 2') {
                $Num_Gen = req_maj_vers('6.6 RC 1');
            }

            if ($Num_Gen == '6.6 RC 1') {
                $Num_Gen = req_maj_vers('6.6 RC 2');
            }


            if ($Num_Gen == '6.6 RC 2') {
                $Num_Gen = req_maj_vers('6.6');
            }

            if ($Num_Gen == '6.6') {
                $Num_Gen = req_maj_vers('6.6.1');
            }

            if ($Num_Gen == '6.6.1') {
                $req[] = 'DROP TABLE IF EXISTS ' . nom_table('connexions') . ';';
                $req[] = 'CREATE TABLE ' . nom_table('connexions') . ' ('
                    . '`idUtil` int(11) NOT NULL ,'
                    . '`dateCnx` datetime NOT NULL default \'0000-00-00 00:00:00\','
                    . '`Adresse_IP` varchar(20) NOT NULL ,'
                    . 'PRIMARY KEY  (`idUtil`,`dateCnx`)'
                    . ') ENGINE=MyISAM  ;';
                $req[] = 'ALTER TABLE ' . nom_table('liens') . ' ADD `Sur_Accueil` BOOLEAN NOT NULL DEFAULT FALSE';
                $Num_Gen = req_maj_vers('6.7 beta 1');
            }

            if ($Num_Gen == '6.7 beta 1') {
                $Num_Gen = req_maj_vers('6.7 beta 2');
            }

            if ($Num_Gen == '6.7 beta 2') {
                $Num_Gen = req_maj_vers('6.7 beta 3');
            }

            if ($Num_Gen == '6.7 beta 3') {
                $Num_Gen = req_maj_vers('6.7 RC 1');
            }

            if ($Num_Gen == '6.7 RC 1') {
                $Num_Gen = req_maj_vers('6.7 RC 2');
            }

            if ($Num_Gen == '6.7 RC 2') {
                $Num_Gen = req_maj_vers('6.7');
            }

            if ($Num_Gen == '6.7') {
                $Num_Gen = req_maj_vers('6.8 beta 1');
            }

            if ($Num_Gen == '6.8 beta 1') {
                $Num_Gen = req_maj_vers('6.8 beta 2');
            }

            if ($Num_Gen == '6.8 beta 2') {
                $Num_Gen = req_maj_vers('6.8 beta 3');
            }

            if ($Num_Gen == '6.8 beta 3') {
                $Num_Gen = req_maj_vers('6.8 RC 1');
            }

            if ($Num_Gen == '6.8 RC 1') {
                $Num_Gen = req_maj_vers('6.8 RC 2');
            }

            if ($Num_Gen == '6.8 RC 2') {
                $Num_Gen = req_maj_vers('6.8 RC 3');
            }

            if ($Num_Gen == '6.8 RC 3') {
                $Num_Gen = req_maj_vers('6.8');
            }

            if ($Num_Gen == '6.8') {
                $Num_Gen = req_maj_vers('6.9 beta 1');
            }

            if ($Num_Gen == '6.9 beta 1') {
                $Num_Gen = req_maj_vers('6.9 beta 2');
            }

            if ($Num_Gen == '6.9 beta 2') {
                $Num_Gen = req_maj_vers('6.9 RC 1');
            }

            if ($Num_Gen == '6.9 RC 1') {
                $Num_Gen = req_maj_vers('6.9 RC 2');
            }

            if ($Num_Gen == '6.9 RC 2') {
                $Num_Gen = req_maj_vers('6.9');
            }

            if ($Num_Gen == '6.9') {
                // Vérification de la présence d'un dépôt (à priori générique) ; si absent, à recréer
                $n_depots = nom_table('depots');
                $ident = -1;
                if ($res = lect_sql('select Ident from ' . $n_depots . ' limit 1')) {
                    if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                        $ident = $enr[0];
                    }
                    $res->closeCursor();
                }
                if ($ident == -1) {
                    $req[] = 'INSERT INTO ' . $n_depots . ' (Ident, `Nom`) VALUES (0, "Dépôt générique");';
                }
                // Ajout de l'indicateur de base vide
                $vide = true;
                $ut = exec_req_vide('select Reference from ' . nom_table('personnes') . ' where Reference <> 0 limit 1');
                $ut = exec_req_vide('select Identifiant_zone from ' . nom_table('villes') . ' where Identifiant_zone <> 0 limit 1');
                $valeur = 'false';
                if ($vide) $valeur = 'true';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ADD `Base_Vide` BOOLEAN NOT NULL DEFAULT ' . $valeur;
                $req[] = 'ALTER TABLE ' . nom_table('sources') . ' ADD Fiabilite_Source CHAR(1) NULL AFTER Adresse_Web';
                $req[] = 'ALTER TABLE ' . $n_personnes . ' CHANGE Categorie Categorie INT(2) NOT NULL DEFAULT "0"';
                $Num_Gen = req_maj_vers('2019.09 beta 1');
            }

            if ($Num_Gen == '2019.09 beta 1') {
                $Num_Gen = req_maj_vers('2019.09 beta 2');
            }

            if ($Num_Gen == '2019.09 beta 2') {
                $req[] = 'update ' . nom_table('types_evenement') . ' set Objet_Cible = "P" where Code_Type = "RESI"';
                $Num_Gen = req_maj_vers('2019.09 beta 3');
            }

            if ($Num_Gen == '2019.09 beta 3') {
                $Num_Gen = req_maj_vers('2019.09 beta 4');
            }

            if ($Num_Gen == '2019.09 beta 4') {
                $Num_Gen = req_maj_vers('2019.09 RC 1');
            }

            if ($Num_Gen == '2019.09 RC 1') {
                $Num_Gen = req_maj_vers('2019.09 RC 2');
            }

            if ($Num_Gen == '2019.09 RC 2') {
                $Num_Gen = req_maj_vers('2019.09');
            }

            if ($Num_Gen == '2019.09') {
                $req[] = "CREATE TABLE " . nom_table('subdivisions') . " ("
                    . "`Identifiant_zone` int(11) NOT NULL DEFAULT '0',"
                    . "`Nom_Subdivision` varchar(80) NOT NULL DEFAULT '-',"
                    . "`Date_Creation` datetime DEFAULT '0000-00-00 00:00:00',"
                    . "`Date_Modification` datetime DEFAULT '0000-00-00 00:00:00',"
                    . "`Statut_Fiche` char(1) DEFAULT NULL,"
                    . "`Zone_Mere` int(11) NOT NULL DEFAULT '0',"
                    . "`Latitude` float DEFAULT NULL,"
                    . "`Longitude` float DEFAULT NULL,"
                    . " PRIMARY KEY (`Identifiant_zone`),"
                    . " KEY (`Zone_Mere`)"
                    . ") ENGINE=MyISAM;";
                $req[] = "INSERT INTO " . nom_table('niveaux_zones')
                    . " (`Identifiant_Niveau`, `Libelle_Niveau`) VALUES (5, 'Subdivision');";
                $req[] = "ALTER TABLE " . nom_table('liens') . " ADD `Diff_Internet` BOOLEAN NOT NULL DEFAULT FALSE";
                $Num_Gen = req_maj_vers('2021.06 alpha 1');
            }


            if ($Num_Gen == '2021.06') {
                $Num_Gen = req_maj_vers('2021.06 alpha 1');
            }

            if ($Num_Gen == '2021.06 alpha 1') {
                $Num_Gen = req_maj_vers('2021.06 beta 1');
            }

            if ($Num_Gen == '2021.06 beta 1') {
                $Num_Gen = req_maj_vers('2021.06 beta 2');
            }

            if ($Num_Gen == '2021.06 beta 2') {
                $Num_Gen = req_maj_vers('2021.06 RC 1');
            }

            if ($Num_Gen == '2021.06 RC 1') {
                $Num_Gen = req_maj_vers('2021.06');
            }

            if ($Num_Gen == '2021.06') {
                $Num_Gen = req_maj_vers('2022.02 beta 1');
            }

            if ($Num_Gen == '2022.02 beta 1') {
                $req[] = 'ALTER DATABASE ' . $ndb . ' CHARACTER SET utf8mb4;';
                $req[] = 'ALTER TABLE ' . nom_table('arbre') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('arbre') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('arbreetiquette') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('arbreetiquette') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('arbremodeleetiq') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('arbremodeleetiq') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('arbreparam') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('arbreparam') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('arbrepers') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('arbrepers') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('arbrephotos') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('arbrephotos') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('arbreunion') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('arbreunion') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('categories') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('categories') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('commentaires') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('commentaires') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('compteurs') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('compteurs') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('concerne_doc') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('concerne_doc') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('concerne_objet') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('concerne_objet') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('concerne_source') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('concerne_source') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('connexions') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('connexions') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('contributions') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('contributions') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('departements') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('departements') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('depots') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('depots') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('documents') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('documents') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('evenements') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('evenements') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('filiations') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('filiations') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('images') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('images') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('liens') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('liens') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('liste_diffusion') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('liste_diffusion') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('niveaux_zones') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('niveaux_zones') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('noms_famille') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('noms_famille') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('noms_personnes') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('noms_personnes') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('participe') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('participe') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('pays') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('pays') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('regions') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('regions') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('relation_personnes') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('relation_personnes') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('requetes') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('requetes') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('roles') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('roles') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('sources') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('sources') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('subdivisions') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('subdivisions') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('types_doc') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('types_doc') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('types_evenement') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('types_evenement') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('utilisateurs') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('utilisateurs') . ' ENGINE InnoDB;';
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' ENGINE InnoDB;';
                $Num_Gen = req_maj_vers('2022.02 beta 2');
            }

            // Rectifications
            $req[] = "update " . nom_table('evenements') . " set Identifiant_zone = 0 where Identifiant_zone is null";
            $req[] = "update " . nom_table('unions') . " set Ville_Notaire = 0 where Ville_Notaire is null";
            $req[] = "update " . nom_table('villes') . " set latitude = 0 where latitude is null";
            $req[] = "update " . nom_table('villes') . " set longitude = 0 where longitude is null";

            // 2022.02
            $req[] = "update " . nom_table('types_evenement') . " set Objet_Cible = 'P' where Code_Type = 'RESI'";
            $req[] = "update " . nom_table('departements') . " set Statut_Fiche = 'O' where Identifiant_zone between 271 and 370 and Date_Creation = '0000-00-00 00:00:00'";
            // Remise de l'utilisateur gestionnaire/gestionnaire
            $req[] = "update " . nom_table('utilisateurs') . " set motPasseUtil = '63e86b1e912220bdf2cafb57f5ad38673c104fa002f6d1139c3a00c459c048ed' "
                . "where codeUtil = 'gestionnaire'";

            if ($Num_Gen == '2022.02 beta 2') {
                $req[] = "ALTER TABLE " . nom_table('personnes') . " DROP INDEX `Reference`";
                $req[] = "ALTER TABLE " . nom_table('personnes') . " ADD PRIMARY KEY(`Reference`)";
                $Num_Gen = req_maj_vers('2022.02 RC 1');
            }

            if ($Num_Gen == '2022.02 RC 1') {
                $Num_Gen = req_maj_vers('2022.02');
            }

            if ($Num_Gen == '2022.02') {
                $req[] = "ALTER TABLE " . nom_table('unions') . " CHANGE `Ville_Notaire` `Ville_Notaire` INT(11) NULL DEFAULT '0'";
                $Num_Gen = req_maj_vers('2023.03 beta 1');
            }

            if ($Num_Gen == '2023.03 beta 1') {
                $Num_Gen = req_maj_vers('2023.03 RC 1');
            }

            if ($Num_Gen == '2023.03 RC 1') {
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' CHANGE `Notaire_K` `Notaire_K` VARCHAR(80)';
                $Num_Gen = req_maj_vers('2023.03 RC 2');
            }

            if ($Num_Gen == '2023.03 RC 2') {
                $Num_Gen = req_maj_vers('2023.03 RC 3');
            }


            if ($Num_Gen == '2023.03 RC 3') {
                $Num_Gen = req_maj_vers('2023.03');
            }

            if ($Num_Gen == '2023.03') {
                $Num_Gen = req_maj_vers('2023.12 beta 1');
            }

            if ($Num_Gen == '2023.12 beta 1') {
                // Rétablissement de la clé 1aire sur les villes
                $req[] = "ALTER TABLE " . nom_table('villes') . " DROP INDEX `Reference`";
                $req[] = "ALTER TABLE " . nom_table('villes') . " ADD PRIMARY KEY(`Identifiant_zone`)";
                $Num_Gen = req_maj_vers('2023.12 beta 2');
            }

            if ($Num_Gen == '2023.12 beta 2') {
                $Num_Gen = req_maj_vers('2023.12 RC 1');
            }

            if ($Num_Gen == '2023.12 RC 1') {
                $Num_Gen = req_maj_vers('2023.12 RC 2');
            }

            if ($Num_Gen == '2023.12 RC 2') {
                // Harmonisation des dates par défaut et pour régler le problème de mode MySQL NO_ZERO_DATE qui générait des plantage à l'init
                $req[] = 'ALTER TABLE ' . nom_table('arbre') . ' CHANGE `dateCre` `dateCre` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('arbre') . ' CHANGE `dateMod` `dateMod` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('arbremodeleetiq') . ' CHANGE `dateCre` `dateCre` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('arbremodeleetiq') . ' CHANGE `dateMod` `dateMod` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('compteurs') . ' CHANGE `date_acc` `date_acc` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('connexions') . ' CHANGE `dateCnx` `dateCnx` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('contributions') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('contributions') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('departements') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('departements') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('documents') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('documents') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('evenements') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('evenements') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('filiations') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('filiations') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('general') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('liens') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('liens') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('liste_diffusion') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('liste_diffusion') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('pays') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('pays') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('personnes') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('regions') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('regions') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('subdivisions') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('subdivisions') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('unions') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' CHANGE `Date_Creation` `Date_Creation` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $req[] = 'ALTER TABLE ' . nom_table('villes') . ' CHANGE `Date_Modification` `Date_Modification` DATETIME DEFAULT CURRENT_TIMESTAMP';
                $Num_Gen = req_maj_vers('2023.12 RC 3');
            }

            if ($Num_Gen == '2023.12 RC 3') {
                $Num_Gen = req_maj_vers('2023.12');
            }

            if ($Num_Gen == '2023.12') {
                $Num_Gen = req_maj_vers('2024.08 alpha 1');
            }

            from_to_vers('2024.08 alpha 1', '2024.08 alpha 2');
            from_to_vers('2024.08 alpha 2', '2024.08 beta 1');
            from_to_vers('2024.08 beta 1', '2024.08 beta 2');
            from_to_vers('2024.08 beta 2', '2024.08 RC 1');
            from_to_vers('2024.08 RC 1', '2024.08');

            if ($Num_Gen == '2024.08') {
                $req[] = 'ALTER TABLE ' . nom_table('sources') . ' CHANGE `Ident_Source_Tempo` `Ident_Source_Tempo` VARCHAR(200)';
                $Num_Gen = req_maj_vers('2025.03 beta 1');
            }

            // Lancement des requêtes
            Ex_Req();

            // Création du répertoire de stockage des contributions si inexistant
            cre_rep('contributions');

            // Création du répertoire de stockage des exports
            cre_rep('exports');

            // Création des répertoires pour GénéGraphe
            cre_rep('fichiers');
            cre_rep('fichiers/images');
            cre_rep('fichiers/pdf');

            // Création des répertoires pour la gestion des documents
            cre_rep('documents');
            cre_rep('documents/HTM');
            cre_rep('documents/IMG');
            cre_rep('documents/PDF');
            cre_rep('documents/TXT');

            // création des répertoires de langues
            cre_rep('languages');
        }

        if ($msg == '') $msg = 'OKMod';
        $msgMod = $msg;
    }
    include_once(__DIR__ . '/connexion_inc.php');
    if (!$cnx) {
        // Les paramètres de connexion sont-ils OK ?
        $cnx = db_connect($nserveur, $ndb, $nutil, $nmdp);
        if ($cnx) {
            echo 'La connexion est OK.<br>';
        } else {
            echo '<center><font color="red"><br><br><br><h2>Echec de la connexion ou de l\'accès à la base de données avec les paramètres enregistrés</h2></font></center>';
        }
        echo '<br>';
    }

    // La seule chose que l'on puisse faire si la connexion est KO c'est modifier le fichier de connexion
    if (!$cnx) {
        // Si rien n'a été saisi, on prend les valeurs du fichier de connexion
        if (($serveurs == '') and
            ($dbs == '') and
            ($utils == '') and
            ($mdps == '')
        ) {
            $serveurs = $nserveur;
            $dbs      = $ndb;
            $utils    = $nutil;
            $mdps     = $nmdp;
        }
        echo '<b>Constitution du fichier de connexion à la base de données :<br></b>' . "\n";

        echo '<form id="saisie" method="post" ENCTYPE="multipart/form-data" action="' . my_self() . '">' . "\n";

        echo '<div class="form-style-6">';
        echo '<h1>Saisissez vos paramètres de connexion</h1>';

        echo 'Nom du serveur (défaut : "localhost")<br>';
        echo '<input type="text" name="serveurs" value="' . $serveurs . '"/>';

        echo 'Nom de la base de données (défaut : "geneamania")<br>';
        echo '<input type="text" name="dbs" value="' . $dbs . '"/>';

        echo 'Code utilisateur (défaut : "root")<br>';
        echo '<input type="text" name="utils" value="' . $utils . '"/>';

        echo 'Mot de passe (défaut : "root", ou vide si Wampserver ")<br>';
        echo '<input type="text" name="mdps" value="' . $mdps . '"/>';

        echo '<input type="submit" name="tester" value="' . $lib_tst_param . '" /><br><br>';
        echo '<input type="submit" name="majparam" value="' . $lib_maj_param . '" />';
        echo '</form>';
        echo '</div>';

        echo "</form>";

        if ($msgMaj == "OKMaj") echo '<br><font color="green">Création du fichier des paramètres de connexion OK</font><br>';

        if (($msg != "") and ($msg != "OKMod") and ($msg != "OKIni") and ($msg != "OKMaj")) {
            if ($erreur) {
                echo "<br><font color=\"red\">" . $msg . "</font><br>";
            } else {
                echo "<br><font color=\"green\">" . $msg . "</font><br>";
            }
        }
    } else {

        // Récupération des derniers sources
        echo '<fieldset><legend>Récupération de la dernière version de référence du logiciel</legend>';
        if ($is_windows)
            echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Information" title="Information">' .
                'Si vous utilisez le lanceur Windows, vous pouvez mettre à jour  Généamania en 1 clic à partir de l\'onglet "Versions" du lanceur.<br>Sinon,&nbsp;';
        echo '<a href="' . $root . '/recup_sources.php">Cliquez ici</a>';
        echo '</fieldset>';

        // Recherche de la version éventuelle locale de Généamania
        $Version = '';
        if ($res = lect_sql('select Version from ' . nom_table('general'))) {
            if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
                $Version = $enreg['Version'];
            }
        }

        if ((!isset($envir)) or ($envir == '')) $envir = 'L';
        $chLoc = ($envir == 'L') ? 'checked="checked"' : '';
        $chInt = ($envir == 'I') ? 'checked="checked"' : '';

        echo '<br>';
        echo '<fieldset><legend>Initialisation de la base de données</legend>';
        echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Information" title="Information"> Uniquement pour une première installation de Généamania :<br>' . "\n";
        echo '<form id="form_modI" method="post">' . "\n";
        echo '<table width="25%"><tr align="center"><td>' . "\n";
        echo '  <fieldset>' . "\n";
        echo '    <legend>Environnement</legend>' . "\n";
        echo '      <input type="radio" name="envir" value="L"' . $chLoc . '/>Local&nbsp;&nbsp;&nbsp;' . "\n";
        echo '      <input type="radio" name="envir" value="I"' . $chInt . '/>Internet<br>' . "\n";
        echo '  </fieldset>' . "\n";
        echo '</td></tr>';
        echo '<tr><td>&nbsp;</td></tr>';
        echo '<tr><td>Préfixe des tables : <input type="text" name="prefixe" value="' . $pref_tables . '"/></td></tr>' . "\n";
        echo '</table>' . "\n";
        echo '<br><input type="submit" name="maj" value="Initialisation"/>' . "\n";
        echo '</form>' . "\n";
        if ($msgIni != '') {
            if ($msgIni == "OKIni") {
                echo '<br><font color="green">Initialisation de la base effectuée en environnement ';
                if ($envir == 'I') echo 'internet';
                else               echo 'local';
                echo '</font><br>';
            } else echo '<br><font color="red">' . $msgIni . '</font><br>';
        }
        echo '</fieldset>';

        echo '<br>';
        echo '<fieldset><legend>Migration de la base de données</legend>';
        if ($Version != '') {
            if ($Version != $LaVersion) {
                echo 'Version ' . $Version . ' vers ' . $LaVersion . ' : <br></b>' . "\n";
                echo '<i>NB : un <a href="' . $root . '/export.php">export</a> complet de la base est conseillé avant de demander la migration.</i>' . "\n";
                echo '<form id="form_modM" method="post" action="' . my_self() . '">' . "\n";
                echo '<input type="submit" name="maj" value="Migration"/>' . "\n";
                echo "</form>";
            } else echo 'Pas de migration nécessaire.' . "\n";
        }
        if ($msgMod != '') {
            if ($msgMod == 'OKMod') echo '<br><font color="green">Migration de la base effectuée</font><br>';
            else                    echo '<br><font color="red">' . $msgMod . '</font><br>';
        }
        echo '</fieldset>';

        echo '<br>';
        echo '<fieldset><legend>Liens</legend>';
        echo '<a href="https://forum.geneamania.net/" target="_blank">Forum Généamania</a><br>' . "\n";
        if ($Version != '') {
            echo '<a href="' . $root . '/">Accueil de votre généalogie</a>';
        }
        echo '</fieldset>';

        // Message d'avertissement en  environnement internet
        if ($envir == 'I') {
            echo '<br><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Information" title="Information"><b> Sur internet, pensez à supprimer la page install.php une fois que vous avez fini de l\'utiliser.</b><br>';
        }
    }

    ?>
</body>

</html>