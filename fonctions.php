<?php

// Valeurs par défaut pour les accès directs
if (!isset($_SESSION['estGestionnaire'])) $_SESSION['estGestionnaire'] = false;
if (!isset($_SESSION['estContributeur'])) $_SESSION['estContributeur'] = false;
if (!isset($_SESSION['estPrivilegie'])) $_SESSION['estPrivilegie'] = false;
if (!isset($_SESSION['estInvite'])) $_SESSION['estInvite'] = true;
if (!isset($_SESSION['estCnx'])) $_SESSION['estCnx'] = false;
if (!isset($_SESSION['niveau'])) $_SESSION['niveau'] = 'I';
if (!isset($est_privilegie)) $est_privilegie = false;

include_once('parametres.php');

$deb = '';
$suffixe_info = '_info.php';

$langue = 'FR';
$langue_min = 'fr';
$fic_lang = $deb . $rep_lang . '/lang_' . $langue . '.php';
if (file_exists($fic_lang)) {
    include($fic_lang);
    // echo 'fic trouvé<br>'.'LG_TIP : '.LG_TIP.'<br>'.'LG_html_file : '.$LG_html_file.'<br>';
}
$fic_lang_part = $deb . $rep_lang . '/lang_' . $langue . '_part.php';
if (file_exists($fic_lang_part)) include($fic_lang_part);

$is_windows = substr(php_uname(), 0, 7) == "Windows" ? true : false;

include_once($fic_icones);

$ListeMoisRev = array(
    "vendémiaire",     //1
    "brumaire",
    "frimaire",
    "nivôse",
    "pluviôse",
    "ventôse",
    "germinal",
    "floréal",
    "prairial",
    "messidor",
    "thermidor",
    "fructidor",
    "sanculottides"
);         //13
$Mois_Lib_rev_h = array(
    "vendémiaire",
    "brumaire",
    "frimaire",
    "nivôse",
    "pluviôse",
    "ventôse",
    "germinal",
    "floréal",
    "prairial",
    "messidor",
    "thermidor",
    "fructidor",
    "sanculottides"
);

$ListeAnneesRev = array('I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV');

// Mois révolutionnaires abrégés sous la forme de 3 lettres
$MoisRevAbr = "-BRUFLOFRIFRUGERMESNIVPLUPRATHEVNDVNTCOM";

// Sous forme de 4 lettres
$MoisRevAbr4 = 'VENDBRUMFRIMNIVOPLUVVENTGERMFLORPRAIMESSTHERFRUCSANC';

$Natures_Docs = array(
    "HTM" => $LG_html_file,
    "IMG" => $LG_image_file,
    "PDF" => $LG_pdf_file,
    "TXT" => $LG_text_file,
    "AUD" => $LG_audio_file,
    "VID" => $LG_video_file
);

// // Connexion à la base de données
// function xconnexion($plantage="oui") {
// global $db,$util,$mdp,$serveur,$ndb,$nutil,$nmdp,$nserveur;
// $db      = $ndb;
// $util    = $nutil;
// $mdp     = $nmdp;
// $serveur = $nserveur;
// $mysqli = new mysqli($serveur, $util, $mdp, $db);
// $linkid = true;
// if ($plantage == "oui") {
// if ($mysqli->connect_errno) {
// $linkid = false;
// echo "Echec de la connexion à MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
// }
// }
// return $linkid;
// }

// S'agit-il dune page d'information ?
function is_info()
{
    global $suffixe_info, $debug;
    if ($debug) var_dump($suffixe_info);
    if (strpos(my_self(), $suffixe_info) !== false)
        return true;
    else
        return false;
}

function nom_table($table)
{
    global $pref_tables;
    return $pref_tables . $table;
}

// Ecrit une ligne dans un fichier texte
function ecrire($fic, $texte)
{
    global $cr;
    fputs($fic, $texte . $cr);
}

// Redimensionnemnt d'une image pour l'affichage
// Conserve le rapport hauteur / largeur
function redimage2($img_src, &$hauteur, &$largeur)
{
    // Lit les dimensions de l'image
    //echo '<!-- '.$img_src.' -->';
    $size = GetImageSize($img_src);
    $src_w = $size[0];
    $src_h = $size[1];
    //echo '<!-- w :'.$src_w.' -->';
    //echo '<!-- h :'.$src_h.' -->';
    // Calcule le facteur de zoom mini
    $zoom_h = $hauteur / $src_h;
    $zoom_w = $largeur / $src_w;
    $zoom = min($zoom_h, $zoom_w);
    // Calcule les dimensions finales en fonction du facteur de zoom mini
    $hauteur = $zoom < 1 ? round($src_h * $zoom) : $src_h;
    $largeur = $zoom < 1 ? round($src_w * $zoom) : $src_w;
}

// Affiche une image redimensionnée sur laquelle on peut cliquer
// Affichage d'un message d'erreur si le fichier n'est pas trouvé
function Aff_Img_Redim_Lien($image, $largeur, $hauteur, $id = "idimg")
{
    global $chemin_images, $chemin_images_icones, $Icones;
    if (file_exists($image)) {
        redimage2($image, $hauteur, $largeur);
        $texte = 'Cliquez sur l\'image pour l\'agrandir ';
        echo '<a href="' . $image . '" target="_blank"><img id="' . $id . '" src="' . $image . '" ' .
            'border="0" alt="' . $texte . '" title="' . $texte . '" ' .
            'width="' . $largeur . '" height="' . $hauteur . '"/></a>';
    } else {
        echo '<img id="ImageAbs' . $id . '" src="' . $chemin_images_icones . $Icones['warning'] . '" alt="Image non trouv&eacute;e">' .
            '&nbsp;Image ' . $image . ' non trouv&eacute;e';
    }
}

function Ret_Romain($Annee)
{
    global $ListeAnneesRev;
    if ($Annee <= count($ListeAnneesRev)) {
        if ($Annee != 0) return $ListeAnneesRev[$Annee - 1];
        else return '?';
    } else {
        return "???";
    }
}

function Age_Mois($date_ref, $date_fin)
{
    $retour = '';
    if ((strlen($date_ref) == 10) and (strlen($date_fin) == 10)) {
        if (($date_ref[9] == 'L') and ($date_fin[9] == 'L')) {
            $date1 = intval(substr($date_ref, 0, 4)) * 12 + intval(substr($date_ref, 4, 2));
            $date2 = intval(substr($date_fin, 0, 4)) * 12 + intval(substr($date_fin, 4, 2));
            if (substr($date_fin, 6, 2) < substr($date_ref, 6, 2)) {
                --$date2;
            }
            $retour = intval($date2 - $date1);
        }
    }
    return $retour;
}

// Décompose un nombre de mois en années / mois
function Decompose_Mois($mois)
{
    if ($mois != '') {
        $an = floor($mois / 12);
        if ($an > 0) {
            $xan = $an . ' an' . pluriel($an);
        } else $xan = '';
        $m = round(fmod($mois, 12));
        if ($m > 0) {
            $xm = $m . ' mois';
            if ($an > 0) $xm = ' et ' . $xm;
        } else $xm = '';
        return $xan . $xm;
    } else return '';
}

function Age_Annees_Mois($date_ref, $date_fin)
{
    if ((strlen($date_ref) == 10) and (strlen($date_fin) == 10) and ($date_ref[9] == 'L') and ($date_fin[9] == 'L')) {
        $mois = Age_Mois($date_ref, $date_fin);
        $x = Decompose_Mois($mois);
        return $x;
    } else return '';
}

function lect_sql($sql)
{
    global $aff_req, $connexion, $nb_req_ex;
    if (!isset($nb_req_ex)) $nb_req_ex = 0;
    $nb_req_ex++;
    if ($aff_req) echo 'Requ&ecirc;te : ' . $sql . '<br>';
    $res = false;
    try {
        $res = $connexion->query($sql);
    } catch (PDOException $ex) {
        $err = $ex->getMessage();
        echo 'Requ&ecirc;te en erreur : ' . $sql . '<br>';
        echo $err . '<br>';
        if (strpos($err, 'exist') !== false) {
            echo 'Avez-vous bien suivi la procédure <a href="install.php">d\'installation</a>,&nbsp;<a href="lisezmoi.html">Cf. lisezmoi.html</a> ?';
        }
    }
    return $res;
}

function maj_sql($sql, $plantage = true)
{
    global $aff_req, $connexion, $enr_mod, $err;
    if ($aff_req) echo 'Requ&ecirc;te : ' . $sql . '<br>';
    try {
        $modif = $connexion->prepare($sql);
        $res = $modif->execute();
        $enr_mod = $modif->rowCount();
    } catch (PDOException $e) {
        $res = false;
        $err = $e->getMessage();
        echo 'Requ&ecirc;te en erreur : ' . $sql . '<br>';
        echo $err . '<br>';
        if ($plantage) die;
    }
    return $res;
}

/* Retourne le contenu d'un champ caractères avec interprétation */
function Champ_car(&$enreg, $champ)
{
    $enreg[$champ] = my_html($enreg[$champ]);
}

// Codage des champs de type caractère
function Champs_car($res, $enreg)
{
    $enreg2 = $enreg;

    foreach ($enreg as $key => $value)
        echo $key . ' ==> ' . $value . '<br>';

    while ($finfo = $res->fetch_field()) {
        $type = $finfo->type;
        $name = $finfo->name;
        if ($type == 'string')
            $enreg2[$name] = my_html($enreg2[$name]);
    }
    return $enreg2;
}

/* Retourne un libellé en fonction du sexe */
function Lib_sexe($libelle, $Sexe)
{
    switch ($Sexe) {
        case 'm':
            $LeLib = $libelle;
            break;
        case 'f':
            $LeLib = $libelle . "e";
            break;
        default:
            $LeLib = $libelle . "(e)";
            break;
    }
    return $LeLib;
}

function pluriel($nb)
{
    $plu = '';
    if ($nb > 1) $plu = 's';
    return $plu;
}

// Le nombre en paramètre demande-t-il le pluriel ?
function is_pluriel($nb)
{
    $ret = false;
    if ($nb > 1) $ret = true;
    return $ret;
}
// Retourne la précision d'une date, stockée en position 9
function Etent_Precision($LaDate)
{
    global $Affiche_Annee, $Environnement, $est_cnx, $LG_year, $LG_day;
    $ret = '';
    if (($Affiche_Annee == 'O') and ($Environnement == 'I') and (!$est_cnx)) {
        switch ($LaDate[9]) {
            case 'E':
                $ret = $LG_year['ca'];
                break;
            case 'L':
                $ret = $LG_year['on'];
                break;
            case 'A':
                $ret = $LG_year['bf'];
                break;
            case 'P':
                $ret = $LG_year['af'];
                break;
        }
    } else {
        switch ($LaDate[9]) {
            case 'E':
                $ret = $LG_day['ca'];
                break;
            case 'L':
                $ret = $LG_day['on'];
                break;
            case 'A':
                $ret = $LG_day['bf'];
                break;
            case 'P':
                $ret = $LG_day['af'];
                break;
        }
    }
    return $ret . ' ';
}

// Retourne une date pour un export CSV
function Retourne_Date_CSV($la_date)
{
    $ret = ';;';
    if ($la_date != '') {
        if (strlen($la_date) == 10) {
            switch ($la_date[9]) {
                case 'E':
                    $pre = 'ca';
                    break;
                case 'L':
                    $pre = 'le';
                    break;
                case 'A':
                    $pre = 'avant le';
                    break;
                case 'P':
                    $pre = 'après le';
                    break;
                default:
                    $pre = '';
                    break;
            }
            $ret = $pre . ';' . substr($la_date, 6, 2) . '/' . substr($la_date, 4, 2) . '/' . substr($la_date, 0, 4) . ';' . substr($la_date, 8, 1);
        }
    }
    return $ret;
}

/* Lit l'environnement en cours : Local ou Internet */
function Lit_Env()
{
    global $db, $linkid,
        $Image_Fond, $coul_fond_table,
        $Environnement, $Nom, $Version, $Adresse_Mail,
        $Image_Arbre_Asc, $Affiche_Mar_Arbre_Asc,
        $Affiche_Annee, $Comportement,
        $Chemin_Barre, $chemin_images_barres,
        $Lettre_B, $chemin_images_lettres, $Chemin_Lettre,
        $Degrade, $Image_Barre, $Modif_Site,
        $Coul_Lib, $Coul_Val, $Coul_Bord, $Coul_Paires, $Coul_Impaires,
        $Pivot_Masquage,
        $Image_Index, $font_pdf, $coul_pdf,
        $Base_Vide, $est_privilegie,
        $connexion, $def_enc, $bk;
    $Acces = 0;
    include('connexion_inc.php');
    if ($ndb != '') {
        $db      = $ndb;
        $util    = $nutil;
        $mdp     = $nmdp;
        $serveur = $nserveur;
        $aj_charset = '';
        if ($def_enc == 'UTF-8')
            $aj_charset = ';charset=utf8';
        try {
            // affiche_heure_precise('1 env');
            $connexion = new PDO("mysql:host=$serveur;dbname=$db$aj_charset", $util, $mdp);
            // affiche_heure_precise('2 env');
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //$pdo = new PDO('mysql:host=localhost;dbname=encoding_test;charset=utf8', 'user', 'pass');
            // or, before PHP 5.3.6:
            //$pdo = new PDO('mysql:host=localhost;dbname=encoding_test', 'user', 'pass',
            //        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            if ($res = lect_sql('select * from ' . nom_table('general'))) {
                if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
                    $Acces = 1;
                    $Lettre_B = $enreg['Lettre_B'];
                    if ($Lettre_B != '') {
                        $Chemin_Lettre = $chemin_images_lettres . $Lettre_B;
                        $Lettre_B = 'lettres/' . $Lettre_B;
                    } else {
                        $Lettre_B = '-';
                    }
                    $Image_Fond = 'fonds/' . $enreg['Image_Fond'];
                    $coul_fond_table = $enreg['Coul_Fond_Table'];
                    $Nom = $enreg['Nom'];
                    $Version = $enreg['Version'];
                    $Adresse_Mail = $enreg['Adresse_Mail'];
                    $Image_Arbre_Asc = $enreg['Image_Arbre_Asc'];
                    $Affiche_Mar_Arbre_Asc = $enreg['Affiche_Mar_Arbre_Asc'];
                    $Environnement = $enreg['Environnement'];
                    $Affiche_Annee = $enreg['Affiche_Annee'];
                    $Comportement = $enreg['Comportement'];
                    $Degrade = $enreg['Degrade'];
                    $Image_Barre = $enreg['Image_Barre'];
                    if ($Image_Barre != '') {
                        $Chemin_Barre = $chemin_images_barres . $Image_Barre;
                        $Image_Barre = 'fonds_barre/' . $Image_Barre;
                    }
                    $Modif_Site = $enreg['Date_Modification'];
                    $Coul_Lib = $enreg['Coul_Lib'];
                    $Coul_Val = $enreg['Coul_Val'];
                    $Coul_Bord = $enreg['Coul_Bord'];
                    $Coul_Paires = $enreg['Coul_Paires'];
                    $Coul_Impaires = $enreg['Coul_Impaires'];
                    $Pivot_Masquage = $enreg['Pivot_Masquage'];
                    if (isset($enreg['Image_Index'])) $Image_Index = $enreg['Image_Index'];
                    if (isset($enreg['Font_Pdf'])) $font_pdf = $enreg['Font_Pdf'];
                    if (isset($enreg['Coul_PDF'])) $coul_pdf = $enreg['Coul_PDF'];
                    if (isset($enreg['Base_Vide'])) $Base_Vide = $enreg['Base_Vide'];
                    if ($Environnement == 'L') {
                        $_SESSION['estGestionnaire'] = true;
                        $_SESSION['niveau'] = 'G';
                        $_SESSION['estPrivilegie'] = true;
                        $est_privilegie = true;
                    }
                }
            }
        } catch (PDOException $ex) {
            echo 'Echec de la connexion !' . $ex->getMessage();
            echo '<br><br>V&eacute;rifiez votre connexion via la page <a href="install.php">d\'installation</a>.<br><br>';
        }
    } else {
        echo 'Fichier de connexion non trouv&eacute;<br>';
    }
    return $Acces;
}

// Insère le bas de page
function Insere_Bas($compl_entete)
{
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>';
    echo '<td align="right">';
    if ($compl_entete != '') {
        echo $compl_entete;
    }
    $x = Affiche_Icones_Standard();
    echo '</tr>';
    echo '</table>' . "\n";
    // Fichiers de session des bots : pool!
    kill_sess_bot();
}

function Insere_Haut_texte($titre)
{
    echo '</head>' . "\n";
    echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td align="center"><b>' . StripSlashes($titre) . '</b></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
}

function Ligne_Body($aff_manuel = true)
{
    global $chemin_images, $Image_Fond, $Icones, $offset_info;
    $nom_manuel = 'Geneamania.pdf';
    if (is_info()) {
        $chemin = $offset_info . $chemin_images . $Image_Fond;
        $chemin_manuel = $offset_info . $nom_manuel;
    } else {
        $chemin = $chemin_images . $Image_Fond;
        $chemin_manuel = $nom_manuel;
    }
    if (($Image_Fond != 'fonds/-') and (file_exists($chemin))) {
        echo '<body background="' . $chemin . '">' . "\n";
        // echo '<body vlink="#0000ff" link="#0000ff" background="'.$chemin.'">'."\n";
    } else
        echo '<body>' . "\n";
    if ($aff_manuel) {
        echo Affiche_Icone_Lien('href="' . $chemin_manuel . '" target="_blank"', 'manuel', 'Manuel Généamania') . '<br>';
    }
}

function Insere_Haut($titre, $compl_entete, $page, $param)
{
    global $chemin_images, $Image_Fond, $Insert_Compteur, $Environnement, $connexion;
    echo '</head>' . "\n";
    Ligne_Body(false);
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td width="15%">';
    aff_menu('D', $_SESSION['niveau']);
    echo '</td>';
    // Version avec titre sur la même ligne que les icones
    echo '<td align="center">' . "\n";
    echo '<h1>' . StripSlashes($titre) . '</h1>' . "\n";
    echo '</td>' . "\n";
    echo '<td align="right">';
    if ($compl_entete != '') echo $compl_entete;
    Affiche_Icones_Standard();
    /* Version avec icones au dessus du titre
	echo "<td align=\"right\">";
	if ($compl_entete != '') echo $compl_entete;
	Affiche_Icones_Standard();
	echo "</td></tr>\n";
	echo "<tr><td ALIGN=\"CENTER\"><h1>".StripSlashes($titre)."</h1></td>\n"; */
    echo "  </tr>\n";
    echo " </table>\n";
    if ($page != "--") {
        $adr_ip = getenv("REMOTE_ADDR");
        $origin = AddSlashes(getenv("HTTP_REFERER"));
        if ($Insert_Compteur) {
            $entry = 'INSERT INTO ' . nom_table('compteurs') . ' (date_acc,page,origine,adresse,parametres) ' .
                "VALUES('" . date("Y-m-d H:i:s") . "','" . $page . "','" . $origin . "','" . $adr_ip . "','" . $param . "')";
            try {
                $res = $connexion->exec($entry);
            } catch (PDOException $ex) {
                echo 'Requ&ecirc;te en erreur : ' . $entry . '<br>';
                echo $ex->getMessage() . '<br>';
            }
        }
    }
}

function Affiche_Icones_Standard()
{
    global $chemin_images_icones, $Icones;
    echo '<a href="' . Get_Adr_Base_Ref() . 'index.php"><img src="' . $chemin_images_icones . $Icones['home'] . '" alt="Accueil" title="Accueil" /></a>' . "\n";
    echo "</td>\n";
}

//	Constitution du libellé du niveau des droits utilisateur
function libelleNiveau($niveau)
{
    switch ($niveau) {
        case 'I':
            $libelle = 'Invit&eacute;';
            break;
        case 'P':
            $libelle = 'Privil&eacute;gi&eacute;';
            break;
        case 'C':
            $libelle = 'Contributeur';
            break;
        case 'G':
            $libelle = 'Gestionnaire';
            break;
        default:
            $libelle = '';
    }
    return $libelle;
}

//	----- Contrôle du niveau de l'utilisateur
//			Il faut avoir le niveau requis pour accéder à ce script
//			Le paramètre niveauRequis doit contenir G (gestionnaire), C (contributeur), P (privilégié) ou I (invité)
function controle_utilisateur($niveauRequis)
{
    //	C"est le premier appel au contrôle => droits d'invité anonyme
    if (!isset($_SESSION['niveau'])) {
        $_SESSION['nomUtilisateur']  = 'Anonyme';
        $_SESSION['utilisateur']     = 'Anonyme';
        $_SESSION['motPasse']        = '';
        $_SESSION['niveau']          = 'I';
        $_SESSION['estInvite']       = true;
        $_SESSION['estPrivilegie']   = false;
        $_SESSION['estContributeur'] = false;
        $_SESSION['estGestionnaire'] = false;
    }
    //	vérification que les droits de l'utilisateur permettent d'accéder à la page demandée
    $num_niveau = 0;
    $val_ret = false;
    switch ($_SESSION['niveau']) {
        case 'I':
            $num_niveau = 1;
            break;
        case 'P':
            $num_niveau = 3;
            break;
        case 'C':
            $num_niveau = 5;
            break;
        case 'G':
            $num_niveau = 9;
            break;
    }
    switch ($niveauRequis) {
        case 'I':
            if ($num_niveau >= 1) $val_ret = true;
            break;
        case 'P':
            if ($num_niveau >= 3) $val_ret = true;
            break;
        case 'C':
            if ($num_niveau >= 5) $val_ret = true;
            break;
        case 'G':
            if ($num_niveau >= 9) $val_ret = true;
            break;
    }
    return $val_ret;
}

// Ecrit les balises meta de l'entête
function Ecrit_meta($titre, $cont, $mots, $index_follow = 'IF')
{
    global $HTTP_REFERER, $Horigine, $avec_js, $chemin_images, $chemin_images_icones, $Image_Barre, $Images,
        $Coul_Lib, $Coul_Val, $Coul_Bord, $Coul_Paires, $Coul_Impaires, $coul_fond_table, $Chemin_Barre, $chemin_images_barres, $Image_Fond, $def_enc;
    echo '<title>' . my_html($titre) . '</title>' . "\n";
    echo '<meta name="description" content="' . $cont . '">' . "\n";
    echo '<meta name="keywords" content="Généalogie, Genealogy, G&eacute;n&eacute;alogie, gratuit, logiciel, Geneamania, Généamania, G&eacute;n&eacute;amania';
    if ($mots != '') echo ', ' . $mots;
    echo '">' . "\n";
    echo '<meta name="owner" content="support@geneamania.net">' . "\n";
    echo '<meta name="author" content="Jean-Luc Servin">' . "\n";
    // echo '<meta http-equiv="content-LANGUAGE" content="French">'."\n"; transféré dans l'entête html
    echo '<meta http-equiv="content-TYPE" content="text/html; charset=' . $def_enc . '">' . "\n";
    // Balises index et follow pour restreindre les robots ==> NOINDEX, NOFOLLOW
    if ($index_follow != 'IF') {
        $p1 = '';
        $p2 = '';
        if ($index_follow[0] == 'N') $p1 = 'NO';
        if ($index_follow[1] == 'N') $p2 = 'NO';
        echo '<meta name="robots" content="' . $p1 . 'INDEX, ' . $p2 . 'FOLLOW">' . "\n";
    }
    echo '<meta name="REVISIT-AFTER" content="7 days">' . "\n";
    // echo '<link rel="stylesheet" href="divers_styles.css">'."\n";
    include('divers_styles.css');
    if (file_exists('divers_styles_part.css'))
        echo '<link rel="stylesheet" href="divers_styles_part.css">' . "\n";

    if (isset($_SERVER['HTTP_REFERER']))
        $HTTP_REFERER = $_SERVER['HTTP_REFERER'];
    // La première fois, Horigine n'est pas renseignée...
    if ($Horigine == '') $Horigine = $HTTP_REFERER;
    if ($Horigine == '') $Horigine = Get_Adr_Base_Ref() . 'index.php';

    // Cet indicateur permet de d'intégrer ou non le js
    // Lors de la mise à jour suite à une saisie, l'intégration du js peut provoquer une erreur Cannot modify header information sur la commande header...
    // Par défaut, on demande le javascript
    if (!isset($avec_js)) $avec_js = 1;
    // Pas de javascript sur les pages d'information
    if (is_info()) {
        //if (strpos(my_self(),$suffixe_info) !== false) {
        $avec_js = false;
    }
    if ($avec_js) include('monSSG.js');
    return 0;
}

/* Etend une date */
function Etend_date($LaDate, $forcage = false)
{
    global $Mois_Lib, $ListeMoisRev, $ListeAnneesRev, $Affiche_Annee, $Environnement, $est_cnx,
        $Premium, $Pivot_Masquage, $SiteGratuit, $langue, $LG_first;
    if (is_null($LaDate))
        $LaDate = '';
    $long_date = strlen($LaDate);
    if (($LaDate != '') and ($long_date == 10)) {
        $date_retour = $LaDate;
        $annee = substr($LaDate, 0, 4);
        // Date grégorienne classique e.g. 19330302GL
        if ($LaDate[8] == 'G') {
            $precision = Etent_Precision($LaDate);
            $annee = substr($LaDate, 0, 4);
            if (($Affiche_Annee == 'O') and ($Environnement == 'I') and (!$est_cnx)) {
                $date_retour = $precision . ' ' . $annee;
            } else {
                $LeJour = substr($LaDate, 6, 2);
                $LeMois = intval(substr($LaDate, 4, 2));
                if (($LaDate[9] == 'E') and ($LeJour == '01') and ($LeMois == '01')) {
                    $date_retour = $precision . ' ' . $annee;
                } else {
                    if ($LeJour == '01') $LeJour = $LG_first;
                    else {
                        if ($LeJour[0] == '0') $LeJour = substr($LeJour, 1, 1);
                    }
                    switch ($langue) {
                        case 'FR':
                            $date_retour = $precision . $LeJour . ' ' . $Mois_Lib[$LeMois - 1] . ' ' . $annee;
                            break;
                        case 'GB':
                            $date_retour = $precision . $LeJour . ' of ' . $Mois_Lib[$LeMois - 1] . ' of ' . $annee;
                            break;
                        default:
                            $date_retour = $precision . $LeJour . ' ' . $Mois_Lib[$LeMois - 1] . ' ' . $annee;
                            break;
                    }
                }
            }
        }
        // Date révolutionnaire classique e.g. 17950821RL
        // Il faut faire la conversion inverse...
        if ($LaDate[8] == 'R') {
            // Calcul du nombre de jours de la date
            $jd = gregoriantojd(substr($LaDate, 4, 2), substr($LaDate, 6, 2), substr($LaDate, 0, 4));
            // On passe en date révolutionnaire
            $resu = jdtofrench($jd);
            // On étend la date révolutionnaire
            $S1 = strpos($resu, '/');
            $S2 = strrpos($resu, '/');
            $LeMois = intval(substr($resu, 0, $S1));
            $LeJour = substr($resu, $S1 + 1, $S2 - $S1 - 1);
            if ($LeJour == '1') $LeJour = '1er';
            $LAnnee = substr($resu, $S2 + 1, 2);
            // On repasse en chiffres romains
            $LAnnee = Ret_Romain($LAnnee);
            if (($Affiche_Annee == 'O') and ($Environnement == 'I') and (!$est_cnx))
                $date_retour = Etent_Precision($LaDate) . " l'an " . $LAnnee;
            else {
                if ($LeMois != 0) $date_retour = Etent_Precision($LaDate) . $LeJour . ' ' . $ListeMoisRev[$LeMois - 1] . " de l'an " . $LAnnee;
                else $date_retour = Etent_Precision($LaDate) . $LeJour . ' ? ' . " de l'an " . $LAnnee;
            }
        }
        // Masquage des dates récentes en option
        if (!$forcage) {
            if ($Environnement == 'I') {
                if (!$est_cnx) {
                    if ((($SiteGratuit) and ($Premium)) or (!$SiteGratuit)) {
                        if (($LaDate[9] == 'L') or ($LaDate[9] == 'P')) {
                            if ($annee >= $Pivot_Masquage) $date_retour = '';
                        }
                    }
                }
            }
        }
        return $date_retour;
    } else {
        return $LaDate;
    }
}

// Fonction étendue d'affichage de date avec conversion des dates révolutionnares
function Etend_date_2($LaDate, $forcage = false)
{
    global $aff_rev, $chemin_images_icones, $Icones;
    if (is_null($LaDate))
        $LaDate = '';
    $LaDate2 = Etend_date($LaDate);
    $long_date = strlen($LaDate);
    if (($LaDate != '') and ($long_date == 10)) {
        if ($LaDate[8] == 'R') {
            $LaDate[8] = 'G';
            switch ($aff_rev) {
                case 'I':
                    $texte_image = Etend_date($LaDate);
                    $LaDate2 .= '&nbsp;<img src="' . $chemin_images_icones . $Icones['arrange']
                        . '" alt="' . $texte_image . '" title="' . $texte_image . '" border="0" />';
                    break;
                case 'P':
                    $LaDate2 .= ' (' . Etend_date($LaDate) . ')';
                    break;
                default:
                    break;
            }
        }
    }
    return $LaDate2;
}

function Etend_Date_Inv($LaDate)
{
    global $Mois_Lib;
    $s1 = strpos($LaDate, '/', 1);
    $s2 = strpos($LaDate, '/', $s1 + 1);
    $j  = substr($LaDate, $s1 + 1, $s2 - $s1 - 1);
    $mois = substr($LaDate, 0, $s1);
    $a  = substr($LaDate, $s2 + 1, 4);
    //echo "Dans date inv : ".$LaDate."==>".$s1."-".$s2."=".$j."-".$mois."-".$a."<br>";
    $retour = $j . " " . $Mois_Lib[intval($mois) - 1] . " " . $a;
    return $retour;
}

function Etend_Jour($Num_Jour)
{
    global $JourFr;
    return $JourFr[$Num_Jour];
}

//--------------------------------------------------------------------------
// Retourne le père et la mère : code retour 1 : trouvé, 0 : sinon
//--------------------------------------------------------------------------
function Get_Parents($enfant, &$Pere, &$Mere, &$Rang)
{
    $Pere = 0;
    $Mere = 0;
    $Rang = 0;
    $sql = 'select Pere, Mere, Rang from ' . nom_table('filiations') . ' where Enfant = ' . $enfant . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($parents = $res->fetch(PDO::FETCH_NUM)) {
            $Pere = $parents[0];
            $Mere = $parents[1];
            $Rang = $parents[2];
        }
        $res->closeCursor();
        unset($res);
    }
    if (($Pere != 0) or ($Mere != 0)) {
        return 1;
    } else
        return 0;
}

//--------------------------------------------------------------------------
// Teste si un nombre est pair
//--------------------------------------------------------------------------
function pair($var)
{
    return (($var & 1) == 0);
}

//--------------------------------------------------------------------------
// Teste si un nombre est impair
//--------------------------------------------------------------------------
function impair($var)
{
    return ($var % 2 == 1);
}

function Get_Adr_Base_Ref()
{
    global $Environnement, $RepGenSiteLoc, $RepGenSiteInt;
    if ($Environnement == 'I') return $RepGenSiteInt;
    else return $RepGenSiteLoc;
}

// Donne le chemin de la font en fonction de l'environnement
function Get_Font()
{
    global $Environnement, $FontLoc, $FontInt;
    if ($Environnement == 'I') return $FontInt;
    else return $FontLoc;
}

function Ins_Ref_Pers($Reference, $new_window = false)
{
    $target = '';
    if ($new_window) $target = ' target="_blank"';
    return 'href="' . Get_Adr_Base_Ref() . 'Fiche_Fam_Pers.php?Refer=' . $Reference . '"' . $target;
}

// Appelle l'édition d'une personne
function Ins_Edt_Pers($Reference, $new_window = false)
{
    $target = '';
    if ($new_window) $target = ' target="_blank"';
    return 'href="' . Get_Adr_Base_Ref() . 'Edition_Personne.php?Refer=' . $Reference . '"' . $target;
}

// Appelle l'édition d'une union
function Ins_Edt_Union($Reference, $Personne = 0, $us = 'n')
{
    return 'href="' . Get_Adr_Base_Ref() . 'Edition_Union.php?Reference=' . $Reference . '&amp;Personne=' . $Personne . '&amp;us=' . $us . '"';
}

// Appelle l'édition d'une filiation
function Ins_Edt_Filiation($Reference)
{
    return 'href="' . Get_Adr_Base_Ref() . 'Edition_Filiation.php?Refer=' . $Reference . '"';
}

// Appel de la page fiche couple de type texte
function Ins_Ref_Fam($Reference, $sortie = "H")
{
    if ($sortie == 'H') return 'href="' . Get_Adr_Base_Ref() . 'Fiche_Couple_txt.php?Reference=' . $Reference . '"';
    else return 'href="' . Get_Adr_Base_Ref() . 'Fiche_Couple_txt.php?Reference=' . $Reference . '&amp;pdf=O"';
}

// Appel de la page fiche individuelle de type texte
function Ins_Ref_Indiv($Reference, $sortie = "H")
{
    if ($sortie == 'H') return 'href="' . Get_Adr_Base_Ref() . 'Fiche_Indiv_txt.php?Reference=' . $Reference . '"';
    else return 'href="' . Get_Adr_Base_Ref() . 'Fiche_Indiv_txt.php?Reference=' . $Reference . '&amp;pdf=O"';
}

function Ins_Ref_Arbre($Reference)
{
    return 'href="' . Get_Adr_Base_Ref() . 'Arbre_Asc_Pers.php?Refer=' . $Reference . '"';
}

function Ins_Ref_Arbre_Desc($Reference)
{
    return 'href="' . Get_Adr_Base_Ref() . 'Arbre_Desc_Pers.php?Refer=' . $Reference . '"';
}

function Ins_Ref_Images($Reference, $Type_Ref)
{
    return 'href="' . Get_Adr_Base_Ref() . 'Liste_Images.php?Refer=' . $Reference . '&amp;Type_Ref=' . $Type_Ref . '"';
}

// Affiche l'icone vers la chronologie d'une personne
function Lien_Chrono_Pers($Reference)
{
    return Affiche_Icone_Lien('href="appelle_chronologie_personne.php?Refer=' . $Reference . '"', 'time_line', LG_FFAM_CHRONOLOGIE) . "\n";
}

// Référence des images pour un évènement
function Ins_Ref_ImagesE($Reference)
{
    return Ins_Ref_Images($Reference, 'E');
}

// Référence des images pour une personne
function Ins_Ref_ImagesP($Reference)
{
    return Ins_Ref_Images($Reference, 'P');
}

// Référence des images pour une ville
function Ins_Ref_ImagesV($Reference)
{
    return Ins_Ref_Images($Reference, "V");
}

// Référence des images pour une union
function Ins_Ref_ImagesU($Reference)
{
    return Ins_Ref_Images($Reference, "U");
}

function Presence_Images($Reference, $Type_Ref)
{
    $cond_sup = '';
    if ($_SESSION['niveau'] == 'I') $cond_sup = ' and Diff_Internet_Img = "O"';
    $sql = 'select ident_image from ' . nom_table('images') .
        ' where Reference = ' . $Reference .
        ' and Type_Ref = "' . $Type_Ref . '"' .
        $cond_sup .
        ' limit 1';
    $res = lect_sql($sql);
    if ($row = $res->fetch(PDO::FETCH_NUM)) return true;
    else return false;
}

// Présence d'images pour une personne
function Presence_ImagesP($Reference)
{
    return Presence_Images($Reference, 'P');
}

// Présence d'images pour une ville
function Presence_ImagesV($Reference)
{
    return Presence_Images($Reference, 'V');
}

// Présence d'images pour une union
function Presence_ImagesU($Reference)
{
    return Presence_Images($Reference, 'U');
}

// Retourne le libellé d'une subdivision
function lib_subdivision($num_subdivision, $html = 'O')
{
    global $Z_Mere;
    $lib = '';
    $Z_Mere = 0;
    if ($num_subdivision != 0) {
        $sql = 'select Nom_Subdivision, Zone_Mere from ' . nom_table('subdivisions') . ' where identifiant_zone = ' . $num_subdivision . ' limit 1';
        if ($res = lect_sql($sql)) {
            if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                $lib = $enr[0];
                $Z_Mere = $enr[1];
            }
            $res->closeCursor();
        }
    }
    return $lib;
}

/* Retourne le libellé d'une ville */
function lib_ville($num_ville, $html = 'O')
{
    global $Z_Mere, $Lat_V, $Long_V, $memo_num_ville, $lib_req_ville;
    if (!isset($memo_num_ville)) $memo_num_ville = -1;
    //echo 'Mémoire 1 : '.number_format(memory_get_usage(), 0, ',', ' ') . "\n";
    if ($num_ville != $memo_num_ville) {
        $lib = '';
        $Z_Mere = 0;
        $Lat_V = 0;
        $Long_V = 0;
        if ($num_ville != 0) {
            $sql = 'select nom_ville, Zone_Mere, Latitude, Longitude from ' . nom_table('villes') . ' where identifiant_zone = ' . $num_ville . ' limit 1';
            if ($res = lect_sql($sql)) {
                if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                    // if ($html == 'O') $lib = my_html($enr[0]);
                    // else $lib = $enr[0];
                    $lib = $enr[0];
                    $Z_Mere = $enr[1];
                    $Lat_V = $enr[2];
                    $Long_V = $enr[3];
                }
                $res->closeCursor();
                unset($res);
            }
        }
        $memo_num_ville = $num_ville;
        $lib_req_ville = $lib;
        return $lib;
    } else return $lib_req_ville;
}

// Retourne le libellé d'un département
function lib_departement($num_depart, $html = 'O')
{
    global $Z_Mere;
    $lib = '';
    $Z_Mere = 0;
    if ($num_depart != 0) {
        $sql = 'select Nom_Depart_Min, Zone_Mere from ' . nom_table('departements') . ' where identifiant_zone = ' . $num_depart . ' limit 1';
        if ($res = lect_sql($sql)) {
            if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                $lib = $enr[0];
                $Z_Mere = $enr[1];
            }
            $res->closeCursor();
        }
    }
    return $lib;
}

// Retourne le libellé d'une région
function lib_region($num_region, $html = 'O')
{
    global $Z_Mere;
    $lib = '';
    $Z_Mere = 0;
    if ($num_region != 0) {
        $sql = 'select Nom_Region_Min, Zone_Mere from ' . nom_table('regions') . ' where identifiant_zone = ' . $num_region . ' limit 1';
        if ($res = lect_sql($sql)) {
            if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                $lib = $enr[0];
                $Z_Mere = $enr[1];
            }
            $res->closeCursor();
        }
    }
    return $lib;
}

// Retourne le libellé d'un pays
function lib_pays($num_pays, $html = 'O')
{
    global $Z_Mere;
    $lib = '';
    $Z_Mere = 0;
    if ($num_pays != 0) {
        $sql = 'select Nom_Pays from ' . nom_table('pays') . ' where identifiant_zone = ' . $num_pays . ' limit 1';
        if ($res = lect_sql($sql)) {
            if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                $lib = $enr[0];
            }
            $res->closeCursor();
        }
    }
    return $lib;
}

/* Retourne le premier prénom */
function UnPrenom($LesPrenoms)
{
    $pblanc = false;
    if ($LesPrenoms != '') $pblanc = strpos($LesPrenoms, ' ', 1);
    if ($pblanc === FALSE) return $LesPrenoms;        // Un seul prénom dans les prénoms transmis
    else return substr($LesPrenoms, 0, $pblanc);
}

// Affichage des données des fiches (personne, union, filiation)
// Validation, création, modification ; fs : affichage dans un fieldset
function Affiche_Fiche($enreg, $fs = 0)
{
    $Statut_Fiche = $enreg['Statut_Fiche'];
    if ($fs == 0) {
        echo '<table width="85%" border="1">' . "\n";
        echo '<tr>' . "\n";
        // Validation fiche
        echo '<td colspan="2">Statut fiche';
        bouton_radio('Statut_Fiche', 'O', LG_CHECKED_RECORD_SHORT, $Statut_Fiche == 'O' ? true : false);
        bouton_radio('Statut_Fiche', 'N', LG_NOCHECKED_RECORD_SHORT, $Statut_Fiche == 'N' ? true : false);
        bouton_radio('Statut_Fiche', 'I', LG_FROM_INTERNET, $Statut_Fiche == 'I' ? true : false);
        echo '<input type="hidden" name="AStatut_Fiche" value="' . $Statut_Fiche . '">';
        echo '</td>';
        echo '<td>Cr&eacute;ation : ' . DateTime_Fr($enreg['Date_Creation']) . '</td>' . "\n";
        echo '<td>Modification : ' . DateTime_Fr($enreg['Date_Modification']) . '</td>' . "\n";
        if ($Statut_Fiche == 'O') echo ' checked="checked"';
        echo '/>Valid&eacute;e&nbsp;' . "\n";
        echo '<input type="radio" name="Statut_Fiche" value="N"';
        if ($Statut_Fiche == 'N') echo ' checked="checked"';
        echo '</tr>' . "\n";
        echo '</table>' . "\n";
    } else {
        echo '<fieldset>' . "\n";
        aff_legend('Statut');
        bouton_radio('Statut_Fiche', 'O', LG_CHECKED_RECORD_SHORT, $Statut_Fiche == 'O' ? true : false);
        bouton_radio('Statut_Fiche', 'N', LG_NOCHECKED_RECORD_SHORT, $Statut_Fiche == 'N' ? true : false);
        bouton_radio('Statut_Fiche', 'I', LG_FROM_INTERNET, $Statut_Fiche == 'I' ? true : false);
        echo '<input type="hidden" name="AStatut_Fiche" value="' . $Statut_Fiche . '"/>';
        echo '</fieldset>' . "\n";
        echo '<fieldset>' . "\n";
        echo '<legend>Tra&ccedil;abilit&eacute;</legend>' . "\n";
        echo 'Cr&eacute;ation : ' . DateTime_Fr($enreg['Date_Creation']) . '<br>' . "\n";
        echo 'Modification : ' . DateTime_Fr($enreg['Date_Modification']) . "\n";
        echo '</fieldset>' . "\n";
    }
}

// Renvoie une datetime au format français
//2006-07-18 19:35:36
function DateTime_Fr($datetime)
{
    sscanf($datetime, "%4s-%2s-%2s %2s:%2s:%2s", $y, $mo, $d, $h, $mi, $s);
    if ($d != '') return $d . '-' . $mo . '-' . $y . ' ' . $h . ':' . $mi . ':' . $s;
    else          return '';
}

// Renvoye l'image par défaut si trouvée
function Rech_Image_Defaut($Reference, $Type_Ref)
{
    global $titre_img;
    $cond_sup = '';
    if ($_SESSION['niveau'] == 'I') $cond_sup = ' and Diff_Internet_Img = "O"';
    $Image = '';
    $sqlI = 'select nom, Titre  from ' . nom_table('images') .
        ' where Defaut = "O" and Type_Ref = "' . $Type_Ref . '" and Reference = ' . $Reference . $cond_sup . ' limit 1';
    $resI = lect_sql($sqlI);
    if ($enregI = $resI->fetch(PDO::FETCH_NUM)) {
        $Image = $enregI[0];
        $titre_img = $enregI[1];
    }
    $resI->closeCursor();
    return $Image;
}

// Récupère les commentaires pour un objet
// Types d'objets possibles :
// D : département
// E : évènement
// F : filiation
// I : image
// P : personne
// R : région
// U : union
// V : ville
// L : lien
function Rech_Commentaire($Reference, $Type_Ref)
{
    global $Commentaire, $Diffusion_Commentaire_Internet;
    $Result = false;
    $Commentaire = '';
    $Diffusion_Commentaire_Internet = 'N';
    $Reference++; // Sinon, si la référence vaut 0, la requête n'est pas appelée :-(
    if (($Reference != '') and ($Reference != -1)) {
        $sqlN = 'select Note, Diff_Internet_Note from ' . nom_table('commentaires') .
            ' where Reference_Objet = ' . --$Reference . ' and Type_Objet = \'' . $Type_Ref . '\' limit 1';
        if ($resN = lect_sql($sqlN)) {
            if ($comment = $resN->fetch(PDO::FETCH_NUM)) {
                $Commentaire = $comment[0];
                $Diffusion_Commentaire_Internet = $comment[1];
                if ($Commentaire != '') {
                    $Result = true;
                    $Commentaire = html_entity_decode($Commentaire);
                }
            }
        }
    }
    return $Result;
}

// Affiche une personne et ses parents
function Aff_Personne($enreg2, $Personne, $Decalage, $Texte, $sortie_pdf = false)
{
    global $chemin_images_util, $Commentaire, $Diffusion_Commentaire_Internet, $Pere, $Mere, $premier_lib_v,
        $SiteGratuit, $Premium, $LG_Sosa_Number, $LG_Data_noavailable_profile, $aff_note_old;
    if (!$sortie_pdf) $sortie = 'H';
    else $sortie = 'P';
    $image = '';
    $ref_pers = $enreg2['Reference'];

    // On ira chercher les commentaires sur ville si non pdf et non texte
    $modalite_ville_comment = false;
    if ((!$sortie_pdf) and ($Texte != 'T')) $modalite_ville_comment = true;

    if (($_SESSION['estPrivilegie']) or ($enreg2['Diff_Internet'] == 'O')) {
        if ($Texte != 'T') {
            // Recherche de l présence d'une image par défaut
            $image = Rech_Image_Defaut($ref_pers, 'P');
            if ($image != '') {
                echo '<table><tr><td align="center" valign="middle">' . "\n";
                $image = $chemin_images_util . $image;
                Aff_Img_Redim_Lien($image, 150, 150, 'img_' . $ref_pers);
                echo '</td><td>&nbsp;</td><td>' . "\n";
            }
        }
        if ($Decalage) $tab = '   ';
        else $tab = '';

        $sur = $enreg2['Surnom'];
        if (($Texte != 'T') and ($sur != '')) echo my_html(LG_PERS_SURNAME) . ' : ' . $sur . '<br>';
        // Affichage du commentaire associé à la personne
        if ($Texte != 'T') {
            $Existe_Commentaire = Rech_Commentaire($ref_pers, 'P');
            if (($Existe_Commentaire) and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
                if ($aff_note_old) Div_Note_Old('ajout' . $ref_pers, 'id_div_ajout' . $ref_pers, $Commentaire);
                else echo Div_Note($Commentaire);
            }
        }
        Aff_NS($Personne, $sortie);

        $on_screen = false;
        if ((!$sortie_pdf) and ($Texte != 'T'))
            $on_screen = true;
        $Sexe = $enreg2['Sexe'];
        HTML_ou_PDF($tab . lib_sexe_born($Sexe), $sortie);
        // HTML_ou_PDF($tab.Lib_sexe('N&eacute;',$Sexe),$sortie);
        $Date_Nai = $enreg2['Ne_le'];
        if ($on_screen)
            $E_Date_Nai = Etend_date_2($Date_Nai);
        else
            $E_Date_Nai = Etend_date($Date_Nai);
        HTML_ou_PDF(' ' . $E_Date_Nai, $sortie);
        $ville = $enreg2['Ville_Naissance'];
        if ($enreg2['Ville_Naissance'] <> 0) {
            HTML_ou_PDF(' ' . LG_AT . ' ' . lib_ville_new($ville, 'N', $modalite_ville_comment), $sortie);
            if (($Texte != 'T') and (($premier_lib_v))) {
                appelle_carte_osm();
                if ($modalite_ville_comment) {
                    if (($Commentaire != '') and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
                        echo Div_Note($Commentaire);
                    }
                }
            }
        }
        HTML_ou_PDF('<br>' . "\n", $sortie);
        if (($enreg2['Decede_Le'] <> '') or ($enreg2['Ville_Deces']) <> 0) {
            HTML_ou_PDF($tab . lib_sexe_dead($Sexe), $sortie);
            $Date_Dec = $enreg2['Decede_Le'];
            if ($on_screen)
                $E_Date_Dec = Etend_date_2($Date_Dec);
            else
                $E_Date_Dec = Etend_date($Date_Dec);
            HTML_ou_PDF(' ' . $E_Date_Dec, $sortie);
            $ville = $enreg2['Ville_Deces'];
            if ($ville <> 0) {
                HTML_ou_PDF(' ' . LG_AT . ' ' . lib_ville_new($ville, 'N', $modalite_ville_comment), $sortie);
                if (($Texte != 'T') and (($premier_lib_v))) {
                    appelle_carte_osm();
                    if ($modalite_ville_comment) {
                        if (($Commentaire != '') and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
                            echo Div_Note($Commentaire);
                        }
                    }
                }
            }
            if (($Date_Nai != '') and ($Date_Dec != '')) {
                $age = Age_Annees_Mois($Date_Nai, $Date_Dec);
                if ($age != '') {
                    HTML_ou_PDF(' (' . LG_PERS_OLD . ' : ' . $age . ')', $sortie);
                }
            }
            HTML_ou_PDF('<br>' . "\n", $sortie);
        }
        // Recherche des professions dans les évènements
        $profession = '';
        $sqlP = 'select Titre, p.Debut, p.Fin, e.Reference ' .
            'from ' . nom_table('evenements') . ' e, ' . nom_table('participe') . ' p ' .
            ' where e.Code_Type = \'OCCU\'' .
            ' and e.Reference =  p.Evenement' .
            ' and p.Personne = ' . $ref_pers .
            ' order by p.Debut';
        if ($resP = lect_sql($sqlP)) {
            while ($enregP = $resP->fetch(PDO::FETCH_NUM)) {
                // Recherche éventuelle du commentaire associé à la profession
                $cmt  = '';
                if (($modalite_ville_comment) and (!$sortie_pdf) and ((!$SiteGratuit) or ($Premium))) {
                    if (Rech_Commentaire($enregP[3], 'E')) {
                        if (($Commentaire != '') and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
                            $cmt = div_note($Commentaire);
                            //'&nbsp;<a href="#" class="info2">'.Affiche_Icone('note').'<span>'.$Commentaire.'</span></a>';
                        }
                    }
                }
                $intervalle = Etend_2_dates($enregP[1], $enregP[2]);
                if ($profession != '') $profession .= ', ';
                $profession .= $enregP[0];
                if ($intervalle != '') $profession .= ' (' . $intervalle . ')';
                $profession .= $cmt;
            }
            $resP->closeCursor();
            unset($resP);
        }

        HTML_ou_PDF($tab . LG_PERS_OCCU . ' : ' . $profession . '<br>' . "\n", $sortie);
        if ($enreg2['Numero'] <> '') {
            HTML_ou_PDF($tab . $LG_Sosa_Number . ' : ' . $enreg2['Numero'] . '<br>' . "\n", $sortie);
        }
        if (Get_Parents($Personne, $Pere, $Mere, $Rang)) {
            HTML_ou_PDF($tab, $sortie);
            if (($Pere != 0) or ($Mere != 0)) {
                switch ($Sexe) {
                    case 'm':
                        HTML_ou_PDF(ucfirst(LG_SON), $sortie);
                        break;
                    case 'f':
                        HTML_ou_PDF(ucfirst(LG_DAUGHTER), $sortie);
                        break;
                    default:
                        HTML_ou_PDF(ucfirst(LG_CHILD), $sortie);
                        break;
                }
            }
            $mys = my_self();
            if ($Pere != 0) {
                if (Get_Nom_Prenoms($Pere, $Nom, $Prenoms)) {
                    if ($Texte == 'T') HTML_ou_PDF(' de ' . $Prenoms . ' ' . $Nom, $sortie);
                    else echo ' de <a href="' . $mys . '?Refer=' . $Pere . '">' . $Prenoms . ' ' . $Nom . '</a>' . "\n";
                }
            }
            if ($Mere != 0) {
                if (Get_Nom_Prenoms($Mere, $Nom, $Prenoms)) {
                    if ($Pere != 0) HTML_ou_PDF(' et', $sortie);
                    if ($Texte == 'T') HTML_ou_PDF(' de ' . $Prenoms . ' ' . $Nom, $sortie);
                    else echo ' de <a href="' . $mys . '?Refer=' . $Mere . '">' . $Prenoms . ' ' . $Nom . '</a>' . "\n";
                }
            }
            HTML_ou_PDF('<br>', $sortie);

            //  Documents lies a la filiation
            if ($Texte != 'T') $x = Aff_Documents_Objet($Personne, 'F', 'O');
        }
        if (($Texte != 'T') and ($image != ''))
            echo '</td></tr></table>' . "\n";
    } else aff_erreur($LG_Data_noavailable_profile);
}

// Calcule la génération
function Calc_Gener($numero)
{
    $numero = intval($numero);
    if ($numero == 0) {
        return '';
    } else {
        $nb     = 1;
        $nb_gen = 1;
        // on calcule le nombre de départ pour une génération jusqu'à dépassement
        while ($nb <= $numero) {
            $nb_gen++;
            $nb *= 2;
        }
        // on redescend d'un cran pour calculer le côté
        --$nb_gen;
        $nb /= 2;
        $nb += ($nb / 2);
        $cote = '';
        if ($nb_gen > 1) {
            ($numero >= $nb) ? $cote = LG_GEN_MOTHER : $cote = LG_GEN_FATHER;
            return "( " . $nb_gen . LG_GEN_NEXT . $cote . " )";
        } else
            return '( ' . LG_GEN_FIRST . ' )';
    }
}

function bissextile($annee)
{
    if (($annee % 4) == 0) {
        if (($annee % 100) == 0) {
            if (($annee % 400) == 0) {
                return (1);
            } else {
                return (0);
            }
        } else {
            return (1);
        }
    } else {
        return (0);
    }
}

// Retourne l'année d'une date précise ;
// utilisé dans les arbres ascendants et descendants et dans les listes de personnes
function affiche_date($ladate)
{
    global $Environnement, $est_cnx, $Premium, $Pivot_Masquage, $SiteGratuit;
    $retour = '?';
    if (($ladate != '') and (strlen($ladate) == 10)) {
        $annee = substr($ladate, 0, 4);
        $preci = $ladate[9];
        switch ($preci) {
            case 'L':
                $retour = $annee;
                break;
            case 'E':
                $retour = '~' . $annee;
                break;
            case 'A':
                $annee = strval($annee) + 1;
                $retour = '/' . $annee;
                break;
            case 'P':
                $annee = strval($annee) - 1;
                $retour = $annee . '/';
                break;
        }
        // Masquage des dates récentes en option
        if ($Environnement == 'I') {
            if (!$est_cnx) {
                if (($preci == 'L') or ($preci == 'P')) {
                    if ($annee >= $Pivot_Masquage) $retour = '';
                }
            }
        }
    }
    return $retour;
}

// Retourne l'extension d'un fichier
function Extension_Fic($fichier)
{
    $elements = explode(".", $fichier);
    $extension = array_pop($elements);
    return strtolower($extension);
}

// Recup de la variable passée dans l'URL : texte ou non
function Dem_Texte()
{
    $texte = 0;
    if (isset($_GET['texte'])) {
        $texte = Recup_Variable('texte', 'C', 'O');
        if ($texte === 'O') $texte = 1;
    }
    return $texte;
}

// Récupère une variable passée dans l'URL avec contrôle du type
// N : type numérique
// C : type caractère avec liste de valeurs autorisées ; si valeur non autorisée, on force la première
// S : type chaine (string)
function Recup_Variable($nom_var, $type_var, $Autorises = "")
{
    $contenu = 0;
    if (isset($_GET[$nom_var])) {
        // get_magic_quotes_gpc renvoie toujours false depuis PHP 5.4, donc on simplifie
        // get_magic_quotes_gpc always returns false as of PHP 5.4, so let's forget it
        // if (!get_magic_quotes_gpc()) $contenu = addslashes($_GET[$nom_var]);
        // else $contenu = $_GET[$nom_var];
        $contenu = addslashes($_GET[$nom_var]);
    }
    if ($contenu) {
        if ($type_var == 'N') {
            if (!is_numeric($contenu)) $contenu = 0;
        }
        if ($type_var == 'C') {
            if (strlen($contenu) > 1) $contenu = substr($contenu, 0, 1);
            if (strpos($Autorises, $contenu) === false) $contenu = substr($Autorises, 0, 1);
        }
    }
    return $contenu;
}

// Sécurise une variable postée
function Secur_Variable_Post($contenu, $long, $type_var)
{
    if ($type_var == 'S') {
        if (strlen($contenu) > $long) $contenu = substr($contenu, 0, strval($long));
    }
    if ($type_var == 'N') {
        if (!is_numeric($contenu)) $contenu = 0;
    }
    return $contenu;
}

function Erreur_DeCujus()
{
    global $chemin_images_icones, $RepGenSite, $Icones;
    echo '<img src="' . $chemin_images_icones . $Icones['warning'] . '" alt="Avertissement">&nbsp;';
    echo 'De cujus non trouv&eacute;, veuillez attribuer le num&eacute;ro 1 &agrave; la personne de votre choix ;&nbsp;';
    echo 'pour ce faire, passez par la <a href="' . $RepGenSite . 'Liste_Pers.php?Type_Liste=P">liste par noms</a>.';
    return 1;
}
function Affiche_Warning($Message)
{
    global $chemin_images_icones, $Icones;
    echo '<img src="' . $chemin_images_icones . $Icones['warning'] . '" alt="Avertissement"/>&nbsp;';
    echo $Message . "<br>\n";
}

function Affiche_Stop($Message)
{
    global $chemin_images_icones, $Icones;
    echo '<br>' . '<img src="' . $chemin_images_icones . $Icones['stop'] . '" alt="Stop"/>&nbsp;';
    echo my_html($Message) . "<br>\n";
}

// Entete de paragraphe
function paragraphe($texte)
{
    global $def_enc;
    echo '<br>' . "\n";
    echo '<table width="100%" border="0" align="left" cellspacing="1" cellpadding="3">' . "\n";
    echo '<tr class="rupt_table">';
    echo '<td><b>' . my_html($texte) . '</b></td>';
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '<br><br>' . "\n";
    return 0;
}

//  Lecture de la ville, du departement, de la region et du pays ==> arborescence en fonction du niveau
function lectZone($idZone, $Niveau, $html = 'O')
{
    global $Z_Mere, $debug;
    if ($debug) {
        echo 'id : ' . $idZone . ', niveau : ' . $Niveau . '<br>';
    }
    $retour = '';
    // Lecture du nom de la subdivision et de la zone mère
    if ($Niveau >= 5) {
        $lib_subd = lib_subdivision($idZone, $html);
        if ($lib_subd == '') return $retour;
        $retour .= $lib_subd;
        $idZone = $Z_Mere;
        if ($idZone == 0) return $retour;
    }
    // Lecture du nom de la ville et de la zone mère
    if ($Niveau >= 4) {
        $lib_ville = lib_ville($idZone, $html);
        if ($lib_ville == '') return $retour;
        if ($retour != '') $retour .= ', ';
        $retour .= $lib_ville;
        $idZone = $Z_Mere;
        if ($idZone == 0) return $retour;
    }
    // Lecture du département et de la zone mère
    if ($Niveau >= 3) {
        $lib_depart = lib_departement($idZone, $html);
        if ($lib_depart == '') return $retour;
        if ($retour != '') $retour .= ', ';
        $retour .= $lib_depart;
        $idZone = $Z_Mere;
        if ($idZone == 0) return $retour;
    }
    // Lecture de la région et de la zone mère
    if ($Niveau >= 2) {
        $lib_region = lib_region($idZone, $html);
        if ($lib_region == '') return $retour;
        if ($retour != '') $retour .= ', ';
        $retour .= $lib_region;
        $idZone = $Z_Mere;
        if ($idZone == 0) return $retour;
    }
    // Lecture du pays
    if ($Niveau >= 1) {
        $lib_pays = lib_pays($idZone, $html);
        if ($lib_pays == '') return $retour;
        if ($retour != '') $retour .= ', ';
        $retour .= $lib_pays;
    }
    return $retour;
}


function Etend_2_dates($date1, $date2, $forcage = false)
{
    $texte = '';
    if ($date1 != $date2) {
        if ($date1 != '') $texte .= 'début : ' . Etend_date($date1, $forcage);
        if ($date2 != '') {
            if ($date1 != '') $texte .= ', ';
            $texte .= 'fin : ' . Etend_date($date2, $forcage);
        }
    } else {
        if ($date1 != '') $texte .= 'début/fin : ' . Etend_date($date1, $forcage);
    }
    return $texte;
}

// Affiche les évènements liés à une personne
// Paramètre : référence de la personne, modification autorisée du lien
function Aff_Evenements_Pers($numPers, $modif)
{
    global $Texte, $Commentaire, $Diffusion_Commentaire_Internet, $aff_note_old, $LG_Add_Existing_Event;
    $nom_div = 'id_div_eve';
    $anc_lib = '';
    $requete  = 'SELECT Libelle_Type, Titre, p.Debut AS dDebP , p.Fin AS dFinP , p.Evenement as refEve ,' .
        ' e.Identifiant_Zone as idZone , e.Identifiant_Niveau as Niveau, r.Code_Role ,' .
        ' p.Identifiant_zone as idZoneP, p. Identifiant_Niveau as NiveauP, ' .
        ' Libelle_Role AS libRole, e.Debut AS dDebE , e.Fin AS dFinE' .
        ' FROM ' . nom_table('evenements') . ' AS e ,' .
        nom_table('participe') . ' AS p ,' .
        nom_table('types_evenement') . ' AS t , ' .
        nom_table('roles') . ' AS r ' .
        ' WHERE personne = ' . $numPers;
    if ($modif == 'N') $requete .= ' AND (e.Code_Type != "OCCU" or (p.Debut != "" and p.Debut is not null) or (p.Fin != "" and p.Fin is not null))';
    $requete .= ' AND e.Code_Type = t.Code_Type AND p.Evenement = e.Reference AND p.Code_Role = r.Code_Role' .
        ' order by Libelle_Type, dDebE, dFinE';
    $res = lect_sql($requete);
    if ($res->rowCount()) {
        // En mode lecture, on ne montre que s'il existe des évènements
        if ($modif == 'N') {
            echo 'Evènements et faits pour la personne ' . "\n";
            $x = Oeil_Div('ajout_eve', 'Montrer les évènements', $nom_div);
        }

        $tab = '   ';
        while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
            $ref_evt = $enreg['refEve'];
            // On affiche le cadre en rupture sur le libellé
            $nouv_lib = $enreg['Libelle_Type'];
            if ($nouv_lib != $anc_lib) {
                // On ferme la table ouverte précédemment
                if ($anc_lib != '') echo '</table></fieldset>';
                echo '  <fieldset><legend>' . my_html($nouv_lib) . '</legend>' . "\n";
                $anc_lib = $nouv_lib;
                echo '<table width="95%" border="0">' . "\n";
            }
            echo '<tr>' . "\n";
            echo '<td width="90%"><a href="Fiche_Evenement.php?refPar=' . $ref_evt . '">' . my_html($enreg['Titre']) . '</a>';
            // Ajout des commentaires des évènements
            if ($Texte != 'T') {
                $Existe_Commentaire = Rech_Commentaire($ref_evt, 'E');
                if (($Existe_Commentaire) and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
                    if ($aff_note_old) Div_Note_Old('ajout' . $ref_evt, 'id_div_ajout' . $ref_evt, $Commentaire);
                    else echo Div_Note($Commentaire);
                }
            }
            echo '</td>' . "\n";
            if ($modif == 'O') {
                echo '<td align="center">' .
                    Affiche_Icone_Lien('href="Edition_Lier_Eve.php?refPar=' . $numPers . '&amp;refEvt=' .
                        $ref_evt . '&amp;refPers=' . $numPers . '&amp;refRolePar=' .
                        $enreg['Code_Role'] . '"', 'fiche_edition', 'Modification du lien') .
                    '</td>' . "\n";
            }
            echo '</tr><tr>' . "\n";
            if ($modif == 'O') echo '<td colspan="2">';
            else               echo '<td>';
            $idZone  = $enreg['idZone'];
            $dDebE   = $enreg['dDebE'];
            $dFinE   = $enreg['dFinE'];
            $idZoneP = $enreg['idZoneP'];
            $NiveauP = $enreg['NiveauP'];

            if (($idZone) or ($dDebE != '') or ($dFinE != '')) {
                if (($dDebE != '') or ($dFinE != '')) {
                    $plage = Etend_2_dates($enreg['dDebE'], $enreg['dFinE']);
                    echo $tab . 'Dates de l\'&eacute;v&egrave;nement : ' . $plage . "\n";
                }
                if ($idZone) {
                    if (($dDebE != '') or ($dFinE != '')) echo ' et lieu : ';
                    else                      echo 'Lieu : ';
                    $zone = LectZone($idZone, $enreg['Niveau']);
                    echo $zone . "\n";
                }
            }
            echo '</td></tr>';
            $libRole = my_html($enreg['libRole']);
            $Code_Role  = $enreg['Code_Role'];
            $dDebP  = $enreg['dDebP'];
            $dFinP  = $enreg['dFinP'];
            if (($idZoneP) or (($libRole != '') and ($Code_Role != '')) or ($dDebP != '') or ($dFinP != '')) {
                echo '<tr><td colspan="2">' . "\n";
                $ligne = false;
                if (($libRole != '') and ($Code_Role != '')) {
                    echo $tab . 'R&ocirc;le : ' . $libRole;
                    $ligne = true;
                }
                if (($dDebP != '') or ($dFinP != '')) {
                    if ($ligne) echo '<br>';
                    echo $tab . 'Dates de participation : ' . Etend_2_dates($enreg['dDebP'], $enreg['dFinP']);
                    $ligne = true;
                }
                if ($idZoneP) {
                    if ($ligne) echo '<br>';
                    echo $tab . 'Lieu : ' . LectZone($idZoneP, $NiveauP) . "\n";
                    $ligne = true;
                }
                echo '</td></tr>' . "\n";
            }
        }
        echo '</table></fieldset>' . "\n";
        $res->closeCursor();
        unset($res);
        if ($modif == 'N')
            fin_div_cache($nom_div);
    }
    if ($modif == 'O') {
        $lib = $LG_Add_Existing_Event;
        // $lib = 'Ajouter un évènement existant';
        echo '<br>' . $lib . '&nbsp;:&nbsp;' . Affiche_Icone_Lien('href="Edition_Lier_Eve.php?refPers=' . $numPers . '&amp;refEvt=-1"', 'ajout', $lib) . "\n";
    }
}

// Affiche les personnes liées à une personne
// Paramètre : référence de la personne, modification autorisée du lien
function Aff_Liens_Pers($numPers, $modif)
{
    global $chemin_images_icones, $Icones;
    $nom_div = 'id_div_liens';
    $requete = 'SELECT Personne_1, Personne_2, rp.Code_Role AS codeRole,Libelle_Role,Debut,Fin, Principale ' .
        ',Symetrie, Libelle_Inv_Role ' .
        'FROM ' . nom_table('relation_personnes') . ' AS rp,' . nom_table('roles') . ' AS r ' .
        'WHERE rp.Code_Role = r.Code_Role ' .
        'AND (rp.Personne_1 = ' . $numPers . ' OR rp.Personne_2 = ' . $numPers . ') ORDER by Debut';
    $res = lect_sql($requete);
    if ($res->rowCount()) {
        // En mode lecture, on ne montre que s'il existe des liens
        if ($modif == 'N') {
            echo 'Liens avec d\'autres personnes&nbsp;' . "\n";
            $x = Oeil_Div('ajout_liens', 'Montrer les liens', $nom_div);
        }
        while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
            $P1 = $enreg['Personne_1'];
            $P2 = $enreg['Personne_2'];
            $Symetrie = $enreg['Symetrie'];
            $Principale = $enreg['Principale'];
            $role = $enreg['Libelle_Role'];
            if ($P1 == $numPers) {
                $LaRef = $P2;
                if (($Symetrie == 'N') and ($Principale == 'N')) $role = $enreg['Libelle_Inv_Role'];
            } else {
                $LaRef = $P1;
                if (($Symetrie == 'N') and ($Principale == 'O')) $role = $enreg['Libelle_Inv_Role'];
            }
            //echo 'sym/princ : '.$Symetrie.'/'.$Principale.'<br>';

            if (Get_Nom_Prenoms($LaRef, $Nom, $Prenoms)) {
                echo '  <fieldset><legend>Avec ' . '<a ' . Ins_Ref_Pers($LaRef) . '>' . $Prenoms . '&nbsp;' . $Nom . '</a>' . '</legend>' . "\n";
                echo '<table width="85%" border="0">' . "\n";
                echo '<tr><td>R&ocirc;le : ' . $role . '</td>' . "\n";
                if ($modif == 'O') {
                    $lib = 'Modification du lien';
                    echo '<td rowspan="2" align="center" valign="middle"><a href="Edition_Lier_Pers.php?ref1=' . $enreg['Personne_1'] .
                        '&amp;ref2=' . $enreg['Personne_2'] . '&amp;orig=';
                    // De quelle personne vient-on ?
                    if ($numPers == $enreg['Personne_1']) echo '1';
                    else echo '2';
                    // Fin du lien
                    echo '&amp;role=' . $enreg['codeRole'] . '">' .
                        '<img src="' . $chemin_images_icones . $Icones['fiche_edition'] . '" border="0" alt="' . $lib . '" title="' . $lib . '"></a></td>' . "\n";
                }
                echo '</tr>' . "\n";
                $debut = $enreg['Debut'];
                $fin   = $enreg['Fin'];
                echo '<tr><td>Dates : ' . Etend_2_dates($debut, $fin) . '</td></tr>' . "\n";
                echo '</table>' . "\n";
                echo '</fieldset>' . "\n";
            }
        }
        $res->closeCursor();
        if ($modif == 'N') fin_div_cache($nom_div);
    }
    if ($modif == 'O') {
        $lib = 'Ajouter un lien vers une personne';
        echo '<br>' . $lib . '&nbsp;:&nbsp;' .
            Affiche_Icone_Lien('href="Edition_Lier_Pers.php?ref1=' . $numPers . '&amp;ref2=-1"', 'ajout', $lib) . "\n";
    }
}

// Affiche les évènements liés à un objet
// Paramètre : référence de l'objet, type d'objet, modification autorisée du lien
function Aff_Evenements_Objet($RefObjet, $TypeObjet, $modif)
{
    global $Environnement, $chemin_images_icones, $Icones, $LG_Add_Existing_Event;
    $nom_div = 'id_div_eve_obj_' . $TypeObjet . $RefObjet;
    $Lib_Type = lib_pfu($TypeObjet, true);
    $requete  = 'SELECT Libelle_Type, Titre, e.Debut AS dDebE , e.Fin AS dFinE , c.Evenement as refEve ,' .
        ' e.Identifiant_Zone as idZone , e.Identifiant_Niveau as Niveau ' .
        ' FROM ' . nom_table('evenements') . ' AS e ,' .
        nom_table('concerne_objet') . ' AS c ,' .
        nom_table('types_evenement') . ' AS t ' .
        ' WHERE Reference_Objet = ' . $RefObjet . ' and Type_Objet = \'' . $TypeObjet . '\'' .
        ' AND e.Code_Type = t.Code_Type AND c.Evenement = e.Reference' .
        ' order by Libelle_Type, dDebE, dFinE';
    $res = lect_sql($requete);

    if ($res->rowCount()) {
        // En mode lecture, on ne montre que s'il existe des évènements
        if ($modif == 'N') {
            echo 'Ev&egrave;nements et faits pour ' . $Lib_Type . '&nbsp;' . "\n";
            $x = Oeil_Div('ajout_eve_obj', 'Montrer les évènements', $nom_div);
        }
        $anc_lib = '';
        while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
            $nouv_lib = $enreg['Libelle_Type'];
            if ($nouv_lib != $anc_lib) {
                // On ferme la table ouverte précédemment
                if ($anc_lib != '') echo '</table></fieldset>';
                echo '  <fieldset><legend>' . $nouv_lib . '</legend>' . "\n";
                $anc_lib = $nouv_lib;
                echo '<table width="95%" border="0">' . "\n";
            }

            echo '<tr>' . "\n";
            echo '<td>&nbsp;<a href="Fiche_Evenement.php?refPar=' . $enreg['refEve'] . '">' . $enreg['Titre'] . '</a></td>' . "\n";
            if ($modif == 'O') {
                echo '<td align="center"><a href="Edition_Lier_Objet.php?refEvt=' . $enreg['refEve'] .
                    '&amp;refObjet=' . $RefObjet .
                    '&amp;TypeObjet=' . $TypeObjet . '">' .
                    '<img src="' . $chemin_images_icones . $Icones['fiche_edition'] . '" border="0" alt="Modification lien"/></a></td>' . "\n";
            }
            echo '</tr><tr>' . "\n";
            if ($modif == 'O') echo '<td colspan="2">';
            else               echo '<td>';
            $idZone = $enreg['idZone'];
            $dDebE  = $enreg['dDebE'];
            $dFinE  = $enreg['dFinE'];
            if (($idZone) or ($dDebE != '') or ($dFinE != '')) {
                if (($dDebE != '') or ($dFinE != '')) {
                    $plage = Etend_2_dates($enreg['dDebE'], $enreg['dFinE']);
                    echo 'Dates de l\'&eacute;v&egrave;nement : ' . $plage . "\n";
                }
                if ($idZone) {
                    if (($dDebE != '') or ($dFinE != '')) echo ' et lieux : ';
                    else                      echo 'Lieux : ';
                    $zone = LectZone($idZone, $enreg['Niveau']);
                    echo $zone . "\n";
                }
            }
            echo '</td></tr>' . "\n";
        }
        echo '</table></fieldset>' . "\n";
        $res->closeCursor();
        if ($modif == 'N')
            fin_div_cache($nom_div);
    }
    // Sur la modification on montre toujours l'entête de div et on peut ajouter un évènement
    if ($modif == 'O') {
        echo my_html($LG_Add_Existing_Event) . ' : ' .
            '<a href="Edition_Lier_Objet.php?refEvt=-1' .
            '&amp;refObjet=' . $RefObjet .
            '&amp;TypeObjet=' . $TypeObjet . '">' .
            '<img src="' . $chemin_images_icones . $Icones['ajout'] . '" border="0" alt="' . $LG_Add_Existing_Event . '"/></a>' . "\n";
    }
}

//	Affichage des documents liés à un objet
//	Paramètres : $refObjet : identifiant de l'objet
//		$typeObjet : type de l'objet
//		$masquer : masquer la balise div à l'affichage (valeurs O ou N)
function Aff_Documents_Objet($refObjet, $typeObjet, $masquer)
{
    global $Environnement, $LG_update_link, $LG_see_document, $Natures_Docs;
    $nom_div = 'id_div_doc_obj_' . $typeObjet . '_' . $refObjet;
    $req_doc = 'SELECT Titre,d.id_document,nature_document,Nom_Fichier FROM ' . nom_table('documents') . ' d, ' . nom_table('concerne_doc') . ' c' .
        ' WHERE d.id_document = c.id_document AND reference_objet = ' . $refObjet;
    if (!$_SESSION['estPrivilegie']) $req_doc = $req_doc . ' AND Diff_Internet = "O"';
    $req_doc = $req_doc . ' AND type_objet = "' . $typeObjet . '" order by Nature_Document,titre';
    $res_doc = lect_sql($req_doc);
    // Affichage
    if ($res_doc->rowCount()) {
        echo 'Documents li&eacute;s &agrave;&nbsp;' . lib_pfu($typeObjet, true) . '&nbsp;' . "\n";
        $x = Oeil_Div('ajout_doc_obj' . $refObjet, 'Montrer les documents', $nom_div);
        $natureAncien = '';
        $nbRupt = 0;
        while ($enr_doc = $res_doc->fetch(PDO::FETCH_NUM)) {
            $natureCourante = $enr_doc[2];
            if ($natureCourante != $natureAncien) {
                if ($nbRupt > 0) {
                    echo '</table></fieldset>' . "\n";
                }
                $nbRupt++;
                echo '  <fieldset><legend>' . $Natures_Docs[$natureCourante] . '</legend>' . "\n";
                echo '<table width="95%" border="0">' . "\n";
            }
            echo '<tr><td>' . '<a href="Fiche_Document.php?Reference=' . $enr_doc[1] . '">' . $enr_doc[0] . '</a>' . "\n";
            $chemin_docu = get_chemin_docu($natureCourante);
            $le_type = Get_Type_Mime($natureCourante);
            if ($_SESSION['estGestionnaire']) {
                echo '&nbsp;&nbsp;' . Affiche_Icone_Lien('href="Edition_Lier_Doc.php?refObjet=' . $refObjet .
                    '&amp;typeObjet=' . $typeObjet . '&amp;refDoc=' . $enr_doc[1] . '"', 'fiche_edition', $LG_update_link) .
                    '&nbsp;&nbsp;';
            }
            echo Affiche_Icone_Lien('href="' . $chemin_docu . $enr_doc[3] . '" type="' . $le_type . '"', 'oeil', $LG_see_document, 'n') .
                '</td></tr>' . "\n";
            $natureAncien = $natureCourante;
        }
        echo '</table></fieldset>' . "\n";
        if ($masquer == 'O')
            fin_div_cache($nom_div);
        else
            echo '</div>' . "\n";
    }
}

//	Affichage des sources liés à un objet
//	Paramètres : $refObjet : identifiant de l'objet
//		$typeObjet : type de l'objet
//		$masquer : masquer la balise div à l'affichage (valeurs O ou N)
function Aff_Sources_Objet($refObjet, $typeObjet, $masquer)
{
    global $Environnement;
    if ($_SESSION['estContributeur']) {
        $nom_div = 'id_div_src_obj_' . $typeObjet . '_' . $refObjet;
        $req_src = 'SELECT s.Titre, s.Ident FROM ' . nom_table('sources') . ' s, ' . nom_table('concerne_source') . ' c' .
            ' WHERE s.Ident = c.Id_Source AND reference_objet = ' . $refObjet .
            ' AND type_objet = "' . $typeObjet . '" order by titre';
        $res_src = lect_sql($req_src);
        // Affichage
        if ($res_src->rowCount()) {
            echo 'Sources li&eacute;es &agrave;&nbsp;' . lib_pfu($typeObjet, true) . '&nbsp;' . "\n";
            $x = Oeil_Div('ajout_src_obj' . $refObjet, 'Montrer les sources', $nom_div);
            $premier = true;
            while ($enr_src = $res_src->fetch(PDO::FETCH_NUM)) {
                if ($premier) {
                    echo '<table width="95%" border="0">' . "\n";
                    $premier = false;
                }
                echo '<tr><td>' . '<a href="Fiche_Source.php?ident=' . $enr_src[1] . '">' . my_html($enr_src[0]) . '</a>' . "\n";
                echo '&nbsp;&nbsp;' . Affiche_Icone_Lien('href="Edition_Lier_Source.php?refObjet=' . $refObjet .
                    '&amp;typeObjet=' . $typeObjet . '&amp;refSrc=' . $enr_src[1] . '"', 'fiche_edition', 'Modification de la liaison');
            }
            echo '</table></fieldset>' . "\n";
            if ($masquer == 'O')
                fin_div_cache($nom_div);
            else
                echo '</div>' . "\n";
        }
    }
}

// Positionnement d'une fin de div avec masquage
function fin_div_cache($nom_div)
{
    echo '</div>' . "\n";
    echo '<script type="text/javascript">' . "\n";
    echo '<!--' . "\n";
    echo 'cache_div(\'' . $nom_div . '\');' . "\n";
    echo '//-->' . "\n";
    echo '</script>' . "\n";
}

function Note_Div($nom_img, $alt_img, $div)
{
    Image_Div('note', $nom_img, $alt_img, $div);
}

function Oeil_Div($nom_img, $alt_img, $div)
{
    Image_Div('oeil', $nom_img, $alt_img, $div);
}

// Affiche une image avec son comportement associé et le début d'un div
// Paramètre : nom de l'image, alt sur image, nom du div
function Image_Div($image, $nom_img, $alt_img, $div)
{
    global $chemin_images_icones, $Icones;
    echo '<img id="' . $nom_img . '" src="' . $chemin_images_icones . $Icones[$image] . '" alt="' . my_html($alt_img) . '" ' . Survole_Clic_Div($div) . '/>' . "\n";
    echo '<div id="' . $div . '">' . "\n";
}

function oeil_div_simple($image, $nom_img, $alt_img, $div)
{
    global $chemin_images_icones, $Icones;
    echo '&nbsp;&nbsp;<img id="' . $image . '" src="' . $chemin_images_icones . $Icones['oeil'] . '" alt="' . $alt_img . '" title="' . $alt_img . '" ' . Survole_Clic_Div($div) . '/>' . "\n";
}

//--------------------------------------------------------------------------
// Retourne le nom et le prénom d'une personne : code retour 1 : trouvé, 0 : sinon
//--------------------------------------------------------------------------
function Get_Nom_Prenoms($Pers, &$Nom, &$Prenoms)
{
    global $Diff_Internet_P;
    $Nom = '';
    $Prenoms = '';
    $Diff_Internet_P = 'N';
    $sql = 'select Nom, Prenoms, Diff_Internet from ' . nom_table('personnes') . ' where Reference  = ' . $Pers . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $Nom     = $enreg[0];
            $Prenoms = $enreg[1];
            $Diff_Internet_P = $enreg[2];
        }
        $res->closeCursor();
    }
    if (($Nom != '') or ($Prenoms != '')) return 1;
    else return 0;
}

//--------------------------------------------------------------------------
// Retourne le nom correspondant à un id : code retour 1 : trouvé, 0 : sinon
//--------------------------------------------------------------------------
function Get_Nom($idNom, &$Nom)
{
    $Nom = '';
    $sql = 'select nomFamille from ' . nom_table('noms_famille') . ' where idNomFam = ' . $idNom . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $Nom = my_html($enreg[0]);
        }
    }
    $res->closeCursor();
    if ($Nom != '') return 1;
    else return 0;
}

// Affiche les notes pour une fiche
function Aff_Comment_Fiche($divers, $diff)
{
    global $est_privilegie, $def_enc;
    if (($divers != '') and (($est_privilegie) or ($diff == 'O'))) {
        echo '<fieldset><legend>Note</legend>' . html_entity_decode(my_html($divers), ENT_QUOTES, $def_enc) . '</fieldset><br>' . "\n";
    }
}

// Affiche la balise Img pour une icone
function Affiche_Icone($nom_image, $texte_image = '')
{
    return Affiche_Icone_Clic($nom_image, '', $texte_image);
}

function Affiche_Icone_Clic($nom_image, $Action_Clic, $texte_image = '')
{
    global $chemin_images_icones, $Icones, $offset_info, $id_image;
    $texte_image = my_html($texte_image);
    $the_id = ' ';
    if ((isset($id_image)) and ($id_image != '')) {
        $the_id = 'id="' . $id_image . '" ';
    }
    $id_image = '';
    $nom_icone = $Icones[$nom_image];
    if (is_info()) {
        $chemin = $offset_info . $chemin_images_icones . $nom_icone;
    } else {
        $chemin = $chemin_images_icones . $nom_icone;
    }
    $oc = '';
    if ($Action_Clic != '') $oc = 'onclick="' . $Action_Clic . ';"';
    return '<img ' . $the_id . 'src="' . $chemin . '" alt="' . $texte_image . '" title="' . $texte_image . '" ' . $oc . '>';
}

function Lien_Icone_Brut($lien, $nom_image, $id_image, $Action_Clic, $texte_image = '')
{
    global $chemin_images_icones, $Icones;
    return '<a ' . $lien . '>'
        . '<img id="' . $id_image . '" '
        . 'src="' . $chemin_images_icones . $Icones[$nom_image] . '" '
        . 'alt="' . $texte_image . '" '
        . 'title="' . $texte_image . '" '
        . 'border="0" '
        . 'onclick="' . $Action_Clic . '"/>'
        . '</a>';
}

// Affiche icone d'appel des textes et pdf ; lien en nofollow...
function Affiche_Icone_Lien_TXT_PDF($lien, $texte_image, $le_type)
{
    global $chemin_images_icones, $Icones;
    $texte_image = my_html($texte_image);
    switch ($le_type) {
        case 'T':
            $image = 'text';
            break;
        case 'P':
            $image = 'PDF';
            break;
    }
    return '<a ' . $lien . ' rel="nofollow"><img src="' . $chemin_images_icones . $Icones[$image] . '" alt="' . $texte_image . '" title="' . $texte_image . '" /></a>';
}

// Affiche la balise Img pour une icone avec le lien
function Affiche_Icone_Lien($lien, $nom_image, $texte_image, $target = '')
{
    if ($target == 'n') $lien .= ' target="_blank"';
    return '<a ' . $lien . '>' . Affiche_Icone($nom_image, $texte_image) . '</a>';
}

function Img_Zone_Oblig($nom_image)
{
    global $chemin_images_icones, $Icones;
    $texte = 'Zone obligatoire';
    echo '<img id="' . $nom_image . '" src="' . $chemin_images_icones . $Icones['obligatoire'] . '" alt="' . $texte . '" title="' . $texte . '"/>';
}

function Affiche_Calendrier($nom_image, $fonc_click)
{
    global $chemin_images_icones, $Icones;
    $texte = 'Calendrier';
    echo '<img id="' . $nom_image . '" src="' . $chemin_images_icones . $Icones['calendrier'] . '" alt="' . $texte . '" title="' . $texte . '" onclick="' . $fonc_click . '"/>' . "\n";
}

// Affiche l'icone d'information si la page d'information existe et le lien vers la page
function Ajoute_Page_Info($largeur, $hauteur)
{
    global $chemin_images_icones, $Icones, $rep_Infos;
    // Constitution du nom de la page info
    $nom_script = $_SERVER['SCRIPT_NAME'];
    if ($nom_script[0] == '/') $nom_script = substr($nom_script, 1);
    $nom_script = Retire_sr($nom_script);
    $l_p  = strrpos($nom_script, '.');
    $texte = 'Aide sur la page';
    $nom_script = substr($nom_script, 0, $l_p);
    return '<a href=\'javascript:PopupCentrer("appel_info.php?aide=' . $nom_script . '",' . $largeur . ',' . $hauteur . ',"menubar=no,scrollbars=yes,statusbar=no")\'>' .
        '<img src="' . $chemin_images_icones . $Icones['information'] . '" alt="' . $texte . '" title="' . $texte . '" /></a>&nbsp;';
}

function Retire_sr($nom_script)
{
    $position = strrpos($nom_script, '/');
    if ($position) $nom_script = substr($nom_script, $position + 1, strlen($nom_script));
    return $nom_script;
}

// Retour arrière vers la page précédente
// S'il n'y en a pas, retour vers l'index
function Retour_Ar()
{
    global $fp, $cr, $debug;
    if (isset($_SESSION['pages'])) {
        $xx = array_pop($_SESSION['pages']);
        $dest = $_SESSION['pages'][count($_SESSION['pages']) - 1];
        if ($debug) {
            $mys = my_self();
            $dh = date("d/m/Y H:i:s");
            $f_log = open_log();
            ecrire($f_log, '');
            ecrire($f_log, $dh . ' Retour_Ar');
            ecrire($f_log, ' Self ' . $mys . ', dest : ' . $dest);
            fclose($f_log);
        }
        header('Location: ' . $dest);
    }
}

// Liste de personnes dans un select
function Liste_Pers($Ensemble, $Nom_Sel, $Ref_Sel = 0)
{
    // Liste des rubriques : Reference, Nom, Prenoms, Ne_le, Decede_Le
    echo '<select name="' . $Nom_Sel . '">' . "\n";
    while ($row = $Ensemble->fetch(PDO::FETCH_NUM)) {
        $Ref = $row[0];
        echo '<option value="' . $Ref . '"';
        if (($Ref_Sel != 0) and ($Ref == $Ref_Sel)) echo ' selected="selected" ';
        echo '>' . my_html($row[1] . ' ' . $row[2]) .
            ' (' . affiche_date($row[3]) . '-' . affiche_date($row[4]) . ')' . '</option>' .
            "\n";
    }
    echo '</select>';
}

// Affiche la liste des villes dans un select
// Paramètres : $nom_select : nom du select
//              $premier : première fois que l'on appelle le select dans la page
//              $dernier : dernière fois que l'on appelle le select dans la page
//              $cle_sel : clé à sélectionner
function aff_liste_villes($nom_select, $premier, $dernier, $cle_sel)
{
    global $res_lv;
    //if ($premier) echo 'Premier ';else echo 'Pas premier ';
    //if ($dernier) echo 'Dernier ';else echo 'Pas dernier ';
    echo '<select name="' . $nom_select . '" id="' . $nom_select . '">' . "\n";
    $sql = 'select Identifiant_zone, Nom_Ville from ' . nom_table('villes') . ' order by Nom_Ville';
    if ($premier) {
        $res_lv = lect_sql($sql);
    } else {
        $res_lv->closeCursor();
        $res_lv = lect_sql($sql);
    }
    while ($row = $res_lv->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($cle_sel == $row[0]) echo ' selected="selected" ';
        echo '>' . my_html($row[1]) . '</option>' . "\n";
    }
    echo "</select>\n";
    if ($dernier) $res_lv->closeCursor();
}

// Retourne les années de naissance et de décès entre parenthèses si l'une des 2 est servie
function aff_annees_pers($Ne, $Decede)
{
    $Dates = '';
    $Ne     = affiche_date($Ne);
    $Decede = affiche_date($Decede);
    if (($Ne != '?') or ($Decede != '?')) {
        $Dates = '&nbsp;(' . $Ne . '-' . $Decede . ')';
    }
    return $Dates;
}

// Affiche la liste des personnes dans un select
// Paramètres : $nom_select : nom du select
//              $premier : première fois que l'on appelle le select dans la page
//              $dernier : dernière fois que l'on appelle le select dans la page
//              $cle_sel : clé à sélectionner
//              $crit : critere de sélection
//              $order : critère de tri
//              $oblig : zone obligatoire ?
//              $oc : action complémentaire sur select exemple onchange="..."
function aff_liste_pers($nom_select, $premier, $dernier, $cle_sel, $crit, $order, $oblig, $oc = '')
{
    global $res, $_SESSION;
    if (!$oblig) $style_z_oblig = '';
    echo '<select name="' . $nom_select . '" class="oblig" ' . $oc . '>' . "\n";
    if ($premier) {
        $sql = 'select Reference, Nom, Prenoms, Ne_Le, Decede_Le from ' . nom_table('personnes');
        // clause where
        $crit_sel = '';
        if ($crit != '') $crit_sel = $crit;
        if (!$_SESSION['estPrivilegie']) {
            if ($crit_sel != '') $crit_sel .= ' and ';
            $crit_sel .= ' Diff_Internet = \'O\' ';
        }
        // clause where
        if ($crit_sel != '') $sql .= ' where ' . $crit_sel;
        // clause order by
        if ($order != '') $sql .= ' order by ' . $order;
        $res = lect_sql($sql);
    } else {
        $res->data_seek(0);
    }
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($cle_sel == $row[0]) echo ' selected="selected" ';
        echo '>' . my_html($row[1] . ' ' . $row[2]) . aff_annees_pers($row[3], $row[4]) . '</option>' . "\n";
    }
    echo '</select>&nbsp;';
    if ($dernier) $res->closeCursor();
}

function ne_dec_approx(&$naissance, &$deces)
{
    global $annees_maxi_vivant;
    if (is_null($naissance))
        $naissance = '';
    if (is_null($deces))
        $deces = '';
    $l_naissance = strlen($naissance);
    $l_deces = strlen($deces);
    if (($l_naissance == 10) and ($naissance[9] <> 'L')) $naissance = '';
    if ($l_naissance <> 10) $naissance = '';
    if (($l_deces == 10) and ($deces[9] <> 'L')) $deces = '';
    if ($l_deces <> 10) $deces = '';
    // Si la date de décès n'est pas servie, on fait naissance + 130 ans
    if (($deces == '') and ($naissance <> '')) {
        $tmp_annee = str_pad(intval(substr($naissance, 0, 4)) + $annees_maxi_vivant, 4, '0', STR_PAD_LEFT);
        $deces = $tmp_annee . substr($naissance, 4);
    }
    // Si la date de naissance n'est pas servie, on fait décès - 130 ans
    if (($naissance == '') and ($deces <> '')) {
        $tmp_annee = str_pad(intval(substr($deces, 0, 4)) - $annees_maxi_vivant, 4, '0', STR_PAD_LEFT);
        $naissance = $tmp_annee . substr($deces, 4);
    }
}

// Affiche les personnes liées à un évènement
function aff_lien_pers($refPar, $modif = 'N')
{
    global $chemin_images_icones, $Icones;
    //  ===== Recherche de liens avec des personnes
    $requete = 'SELECT Reference, Debut, Fin, Nom, Prenoms, r.Code_Role, Libelle_Role as libRole, Diff_Internet ' .
        ' FROM ' . nom_table('participe') . ' AS pa , ' . nom_table('personnes') . ' AS pe , ' .
        nom_table('roles') . ' AS r ' .
        " WHERE Evenement = $refPar AND pa.Personne = pe.Reference AND pa.Code_Role = r.Code_Role";
    $result = lect_sql($requete);
    if ($result->rowCount() > 0) {
        $icone_mod = '<img src="' . $chemin_images_icones . $Icones['fiche_edition'] . '" border="0" alt="Modification lien"/>';
        echo '<br>' . "\n";
        if ($modif == 'N') echo '<fieldset><legend>Lien avec des personnes</legend>' . "\n";
        while ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
            echo '<br>' . "\n";
            if (($_SESSION['estPrivilegie']) || ($enreg['Diff_Internet'] != 'N')) {
                echo '<a href="Fiche_Fam_Pers.php?Refer=' . $enreg['Reference'] . '"' . ">" .
                    my_html($enreg['Prenoms'] . ' ' . $enreg['Nom']) .
                    '</a>';
                $role = $enreg['libRole'];
                if (($role != '') and ($enreg['Code_Role'] != ''))
                    echo ', r&ocirc;le : ' . my_html($role);
                $deb = $enreg['Debut'];
                $fin = $enreg['Fin'];
                if (($deb != '') or ($fin != '')) {
                    if (($deb != '') and ($fin != '')) echo ', dates';
                    else  echo ', date';
                    echo ' de participation : ' . Etend_2_dates($deb, $fin);
                }
                // En mode modification, on va mettre un lien pour modifier la liaison
                if ($modif == 'O') {
                    echo '&nbsp;<a href="Edition_Lier_Eve.php?typeLienPar=P&amp;refPers=' . $enreg['Reference'] .
                        '&amp;refEvt=' . $refPar .
                        '&amp;refRolePar=' . $enreg['Code_Role'] . '">' .
                        $icone_mod . '</a>';
                }
            } else echo 'Donn&eacute;es non disponibles pour votre profil';
            echo "\n";
        }
        if ($modif == 'N') echo '</fieldset>' . "\n";
    }
}

// Affiche les filiations liées à un évènement
function aff_lien_filiations($refPar, $modif = 'N')
{
    global $chemin_images_icones, $Icones;
    $requete = 'SELECT ev.Reference,fi.Enfant,fi.Pere,fi.Mere,Reference_objet,' .
        'enf.Reference AS eRef,enf.Sexe AS eSexe,enf.Prenoms AS ePrenoms,enf.Nom AS eNom,enf.Diff_Internet AS eDiff,' .
        'pere.Reference AS pRef,pere.Prenoms AS pPrenoms,pere.Nom AS pNom,pere.Diff_Internet AS pDiff,' .
        'mere.Reference AS mRef,mere.Prenoms AS mPrenoms,mere.Nom AS mNom,mere.Diff_Internet AS mDiff,type_Objet,Debut,Fin' .
        ' FROM ' . nom_table('evenements') . ' AS ev,'
        . nom_table('concerne_objet') . ' AS co,'
        . nom_table('filiations') . ' AS fi,'
        . nom_table('personnes') . ' AS enf,'
        . nom_table('personnes') . ' AS pere,'
        . nom_table('personnes') . ' AS mere ' .
        ' WHERE ev.Reference = ' . $refPar . ' AND Evenement = ev.Reference AND fi.Enfant = Reference_objet ' .
        ' AND fi.Enfant=enf.Reference AND fi.Pere = pere.Reference AND fi.Mere = mere.Reference ' .
        ' AND Type_Objet="F"';
    $result = lect_sql($requete);
    if ($result->rowCount() > 0) {
        echo '<br>' . "\n";
        if ($modif == 'N') echo '<fieldset><legend>Lien avec des filiations</legend>' . "\n";
        while ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
            switch ($enreg['eSexe']) {
                case 'm':
                    $texte = LG_SON_OF;
                    break;
                case 'f':
                    $texte = LG_DAUGHTER_OF;
                    break;
                default:
                    $texte = LG_CHILD_OF;
            }
            if (($_SESSION['estPrivilegie']) || ($enreg['eDiff'] != 'N')) {
                echo '<br><a ' . Ins_Ref_Pers($enreg['eRef']) . '>' . my_html($enreg['ePrenoms'] . ' ' . $enreg['eNom']) . '</a>';
            }

            //
            echo '<br>&nbsp;&nbsp;' . $texte;
            if (($_SESSION['estPrivilegie']) || ($enreg['pDiff'] != 'N')) {
                echo '<a ' . Ins_Ref_Pers($enreg['pRef']) . '>' . my_html($enreg['pPrenoms'] . ' ' . $enreg['pNom']) . '</a>';
            }
            echo ' et de ';
            if (($_SESSION['estPrivilegie']) || ($enreg['mDiff'] != 'N')) {
                echo '<a ' . Ins_Ref_Pers($enreg['mRef']) . '>' . my_html($enreg['mPrenoms'] . ' ' . $enreg['mNom']) . '</a>';
            }
            // En mode modification, on va mettre un lien pour modifier la liaison (utile uniquement pour la suppression)
            if ($modif == 'O') {
                echo '&nbsp;<a href="Edition_Lier_Objet.php?refEvt=' . $refPar .
                    '&amp;refObjet=' . $enreg['eRef'] .
                    '&amp;TypeObjet=F' .
                    '">' .
                    '<img src="' . $chemin_images_icones . $Icones['fiche_edition'] . '" border="0" alt="Modification lien"/></a>';
            }
        }
        if ($modif == 'N') echo '</fieldset>' . "\n";
    }

    if ($modif == 'O') {
        echo '<br><br>Ajouter une filiation : ' .
            '<a href="Edition_Lier_Objet.php?refEvt=' . $refPar .
            '&amp;refObjet=-1' .
            '&amp;TypeObjet=F' .
            '">' .
            '<img src="' . $chemin_images_icones . $Icones['ajout'] . '" border="0" alt="Ajouter une filiation"/></a>' . "\n";
    }
}

// Affiche les unions liées à un évènement
function aff_lien_unions($refPar, $modif = 'N')
{
    global $chemin_images_icones, $Icones;
    $requete = 'SELECT ev.Reference,un.Reference as uRef,un.Conjoint_1,un.Conjoint_2,Reference_objet,' .
        'pere.Reference AS pRef,pere.Prenoms AS pPrenoms,pere.Nom AS pNom,pere.Diff_Internet AS pDiff,' .
        'mere.Reference AS mRef,mere.Prenoms AS mPrenoms,mere.Nom AS mNom,mere.Diff_Internet AS mDiff,type_Objet,Debut,Fin' .
        ' FROM ' . nom_table('evenements') . ' AS ev,' .
        nom_table('concerne_objet') . ' AS co,' . nom_table('unions') . ' AS un,' .
        nom_table('personnes') . ' AS pere,' .
        nom_table('personnes') . ' AS mere ' .
        ' WHERE ev.Reference = ' . $refPar . ' AND Evenement = ev.Reference AND un.Reference = Reference_objet ' .
        ' AND un.Conjoint_1 = pere.Reference AND un.Conjoint_2 = mere.Reference ' .
        ' AND Type_Objet="U"';
    $result = lect_sql($requete);
    if ($result->rowCount() > 0) {
        echo '<br>' . "\n";
        if ($modif == 'N') echo '<fieldset><legend>Lien avec des unions</legend>' . "\n";
        while ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
            echo '<br>' . "\n";
            if (($_SESSION['estPrivilegie']) || ($enreg['pDiff'] != 'N')) {
                echo '<a ' . Ins_Ref_Pers($enreg['pRef']) . '>' . my_html($enreg['pPrenoms'] . ' ' . $enreg['pNom']) . '</a>';
            }
            echo ' et ';
            if (($_SESSION['estPrivilegie']) || ($enreg['mDiff'] != 'N')) {
                echo '<a ' . Ins_Ref_Pers($enreg['mRef']) . '>' . my_html($enreg['mPrenoms'] . ' ' . $enreg['mNom']) . '</a>';
            }
            // En mode modification, on va mettre un lien pour modifier la liaison (utile uniquement pour la suppression)
            if ($modif == 'O') {
                echo '&nbsp;<a href="Edition_Lier_Objet.php?refEvt=' . $refPar .
                    '&amp;refObjet=' . $enreg['uRef'] .
                    '&amp;TypeObjet=U' .
                    '">' .
                    '<img src="' . $chemin_images_icones . $Icones['fiche_edition'] . '" border="0" alt="Modification lien"/></a>';
            }
        }
        if ($modif == 'N') echo '</fieldset>' . "\n";
    }
    if ($modif == 'O') {
        echo '<br><br>Ajouter une union : ' .
            '<a href="Edition_Lier_Objet.php?refEvt=' . $refPar .
            '&amp;refObjet=-1' .
            '&amp;TypeObjet=U' .
            '">' .
            '<img src="' . $chemin_images_icones . $Icones['ajout'] . '" border="0" alt="Ajouter une union"/></a>' . "\n";
    }
}

// Affiche un message d'erreur
function aff_erreur($message)
{
    echo '<center><font color="red"><br><br><br><h2>' . my_html($message) . '</h2></font></center>';
}

// Fonction de recherche du decujus
// Pour le moment, recherche en base ; à terme, recherche dans variable de session pour autoriser la vue personnalisée
function get_decujus()
{
    global $_SESSION;
    $decujus = 0;
    if ((!isset($_SESSION['decujus'])) or ($_SESSION['decujus'] == -1)) {
        $sql = 'select Reference from ' . nom_table('personnes') . ' where Numero = \'1\' limit 1';
        if ($Res = lect_sql($sql)) {
            if ($pers = $Res->fetch(PDO::FETCH_NUM)) {
                $decujus = $pers[0];
                $_SESSION['decujus'] = $decujus;
            }
            $Res->closeCursor();
        }
    } else {
        $decujus = $_SESSION['decujus'];
    }
    return $decujus;
}

// Affiche une entrée de sous-menu
function sous_menu($url, $libelle, $niveau)
{
    global $LG_Menu_Title;
    $sep = '^^^';
    return '1' . $sep . $url . $sep . $LG_Menu_Title[$libelle] . $sep . $niveau . $sep;
}

function aff_menu($type_menu, $droits, $formu = true)
{
    global $RepGenSite, $Version, $adr_rech_gratuits, $gestionnaire, $SiteGratuit, $Premium, $chemin_images_icones, $Icones, $Base_Vide, $def_enc, $LG_Menu_Title;

    /* 4 niveaux d'autorisation
	Invité       : I
	Privilégié   : P
	Contributeur : C
	Gestionnaire : G
	*/

    $menu[] = '0^^^ ^^^Accès rapide^^^C^^^';
    $menu[] = sous_menu('Edition_Personne.php?Refer=-1', 'Person_Add', 'C');
    $menu[] = sous_menu('Edition_Ville.php?Ident=-1', 'Town_Add', 'C');
    $menu[] = sous_menu('Edition_Evenement.php?refPar=-1', 'Event_Add', 'C');
    $menu[] = '1^^^Edition_NomFam.php?idNom=-1^^^Ajouter un nom de famille ^^^C^^^';
    if ($droits == 'G') {
        $menu[] = '1^^^Edition_Parametres_Graphiques.php^^^Graphisme du site^^^G^^^';
        if ($Base_Vide)
            $menu[] = '1^^^Noyau_Pers.php^^^' . $LG_Menu_Title['Decujus_And_Family'] . '^^^G^^^';
    }

    if (!$Base_Vide) {
        $menu[] = '0^^^ ^^^Listes des personnes^^^I^^^';
        $menu[] = '1^^^Liste_Pers.php?Type_Liste=P^^^Par nom^^^I^^^';
        $menu[] = '1^^^Liste_Pers_Gen.php^^^Par génération^^^I^^^';
        $menu[] = '1^^^Liste_Pers.php?Type_Liste=N^^^Par ville de naissance^^^I^^^';
        $menu[] = '1^^^Liste_Pers.php?Type_Liste=M^^^Par ville de mariage^^^I^^^';
        $menu[] = '1^^^Liste_Pers.php?Type_Liste=K^^^Par ville de contrat de mariage^^^I^^^';
        $menu[] = '1^^^Liste_Pers.php?Type_Liste=D^^^Par ville de décès^^^I^^^';
        $menu[] = '1^^^Liste_Pers.php?Type_Liste=C^^^Par catégorie^^^C^^^';
        $menu[] = '1^^^Liste_Patro.php^^^Liste patronymique^^^I^^^';
        $menu[] = sous_menu('Liste_Eclair.php', 'County_List', 'I');
        $menu[] = sous_menu('Liste_Nom_Vivants.php', 'Living_Pers', 'I');
        $menu[] = '1^^^Liste_NomFam.php^^^Liste des noms de famille^^^I^^^';
    }
    $menu[] = '0^^^ ^^^Listes des zones géographiques^^^I^^^';
    $menu[] = '1^^^Liste_Villes.php?Type_Liste=S^^^Subdivisions^^^I^^^';
    $menu[] = '1^^^Liste_Villes.php?Type_Liste=V^^^Villes^^^I^^^';
    $menu[] = '1^^^Liste_Villes.php?Type_Liste=D^^^Départements^^^I^^^';
    $menu[] = '1^^^Liste_Villes.php?Type_Liste=R^^^Régions^^^I^^^';
    $menu[] = '1^^^Liste_Villes.php?Type_Liste=P^^^Pays^^^I^^^';

    $menu[] = '0^^^ ^^^Recherche^^^I^^^';
    $menu[] = '1^^^Recherche_Personne.php^^^De personnes^^^I^^^';
    $menu[] = '1^^^Recherche_Personne_CP.php^^^De personnes par les conjoints ou parents^^^P^^^';
    if ((!$SiteGratuit) or ($Premium)) {
        $menu[] = '1^^^Liste_Referentiel.php?Type_Liste=Q^^^Liste des requêtes sur les personnes^^^P^^^';
    }
    $menu[] = '1^^^' . $adr_rech_gratuits . '^^^Recherche sur les sites gratuits^^^I^^^';
    $menu[] = sous_menu('Recherche_Cousinage.php', 'Search_Related', 'I');
    $menu[] = '1^^^Recherche_Personne_Archive.php^^^Aux archives^^^C^^^';
    $menu[] = sous_menu('Recherche_Ville.php', 'Town_Search', 'I');
    $menu[] = sous_menu('Recherche_Commentaire.php', 'Search_Comment', 'C');

    if ((!$SiteGratuit) or ($Premium)) {
        $menu[] = '1^^^Recherche_Document.php^^^Dans les documents^^^C^^^';
    }
    $menu[] = '0^^^ ^^^Gestion des contributions^^^C^^^';
    $menu[] = sous_menu('Liste_Contributions.php', 'Contribs_List', 'C');

    $menu[] = '0^^^ ^^^Gestion des catégories^^^P^^^';
    $menu[] = '1^^^Liste_Referentiel.php?Type_Liste=C^^^Liste des catégories^^^P^^^';

    $menu[] = '0^^^ ^^^Gestion des évènements et des relations^^^P^^^';
    $menu[] = '1^^^Liste_Referentiel.php?Type_Liste=R^^^Liste des rôles^^^C^^^';
    $menu[] = sous_menu('Liste_Referentiel.php?Type_Liste=T', 'Event_Type_List', 'C');
    $menu[] = sous_menu('Liste_Evenements.php', 'Event_List', 'P');
    $menu[] = sous_menu('Liste_Evenements.php?actu=o', 'News_List', 'P');
    $menu[] = sous_menu('Liste_Evenements.php?prof=o', 'Jobs_List', 'P');
    $menu[] = sous_menu('Fusion_Evenements.php', 'Event_Merging', 'C');


    // La gestion des sources et documents n'est pas autorisée sur les sites gratuits non Premium
    if ((!$SiteGratuit) or ($Premium)) {
        $menu[] = '0^^^ ^^^Gestion des dépôts et des sources^^^C^^^';
        $menu[] = '1^^^Liste_Referentiel.php?Type_Liste=O^^^Liste des dépôts de sources^^^C^^^';
        $menu[] = sous_menu('Liste_Sources.php', 'Source_List', 'C');
        $menu[] = '0^^^ ^^^Documents^^^I^^^';
        $menu[] = '1^^^Liste_Referentiel.php?Type_Liste=D^^^Liste des types de documents^^^C^^^';
        $menu[] = sous_menu('Liste_Documents.php', 'Documents_List', 'I');
        $menu[] = sous_menu('Galerie_Images.php', 'Galery', 'I');
        if ((!$SiteGratuit) or ($Premium))
            $menu[] = sous_menu('Liste_Docs_Branche.php', 'Galery_Branch', 'I');
        $menu[] = sous_menu('Create_Multiple_Docs.php', 'Document_Multiple_Add', 'C');
    }

    $menu[] = '0^^^ ^^^Imports - exports^^^G^^^';
    $menu[] = '1^^^Export.php^^^Export de la base^^^G^^^';
    $menu[] = '1^^^exp_GenWeb.php^^^Export GenWeb^^^G^^^';
    $menu[] = sous_menu('exp_Gedcom.php', 'Exp_Ged', 'G');
    $menu[] = sous_menu('exp_Gedcom.php?leger=o', 'Exp_Ged_Light', 'G');
    $menu[] = sous_menu('Export_Pour_Deces.php', 'Export_Death', 'G');
    $menu[] = '1^^^Import_Gedcom.php^^^Import Gedcom^^^G^^^';
    $menu[] = sous_menu('Import_Sauvegarde.php', 'Import_Backup', 'G');
    if ((!$SiteGratuit) or ($Premium)) {
        $menu[] = '1^^^Import_CSV.php^^^Import CSV (tableur)^^^G^^^';
        $menu[] = sous_menu('Import_CSV_Liens.php', 'Imp_CSV_Links', 'G');
        $menu[] = sous_menu('Import_CSV_Evenements.php', 'Imp_CSV_Events', 'G');
        $menu[] = sous_menu('Import_CSV_Villes.php', 'Imp_CSV_Towns', 'G');
    }
    $menu[] = sous_menu('Import_Docs.php', 'Import_Docs', 'G');

    $menu[] = '0^^^ ^^^Vérifications^^^C^^^';
    $menu[] = sous_menu('Verif_Sosa.php', 'Check_Sosa', 'C');
    $menu[] = sous_menu('Verif_Internet.php', 'Internet_Cheking', 'C');
    $menu[] = sous_menu('Verif_Internet_Absente.php', 'Internet_Hidding_Cheking', 'C');
    $menu[] = sous_menu('Pers_Isolees.php', 'Non_Linked_Pers', 'C');
    $menu[] = sous_menu('Verif_Homonymes.php', 'Namesake_Cheking', 'C');
    if ((!$SiteGratuit) or ($Premium)) {
        $menu[] = sous_menu('Controle_Personnes.php', 'Check_Persons', 'C');
    }

    $menu[] = '0^^^ ^^^Vue personnalisée^^^I^^^';
    $menu[] = sous_menu('Vue_Personnalisee.php', 'Custom_View', 'I');

    $menu[] = '0^^^ ^^^Utilitaires^^^I^^^';
    $menu[] = '1^^^Calendriers.php^^^Les calendriers^^^I^^^';
    $menu[] = sous_menu('Calc_So.php', 'Calc_Sosa', 'I');
    $menu[] = '1^^^Conv_Romain.php^^^Convertisseur de nombres romains^^^I^^^';
    $menu[] = sous_menu('Init_Sosa.php', 'Delete_Sosa', 'G');
    if (!$SiteGratuit) $menu[] = sous_menu('Init_Noms.php', 'Init_Names', 'G');
    if ($def_enc != 'UTF-8')
        $menu[] = '1^^^Rectif_Utf8.php^^^' . $LG_Menu_Title['Rect_Utf'] . '^^^G^^^';
    if ((!$SiteGratuit) or ($Premium)) {
        $menu[] = sous_menu('Calcul_Distance.php', 'Calculate_Distance', 'I');
        $menu[] = sous_menu('Liste_Noms_Non_Ut.php', 'Name_Not_Used', 'C');
    }
    $menu[] = sous_menu('Vide_Base.php', 'Reset_DB', 'G');
    if (!$SiteGratuit) $menu[] = sous_menu('Infos_Tech.php', 'Tech_Info', 'G');

    $menu[] = '0^^^ ^^^Informations^^^I^^^';
    $menu[] = sous_menu('Premiers_Pas_Genealogie.php', 'Start', 'I');
    $menu[] = sous_menu('Glossaire_Gen.php', 'Glossary', 'I');
    $menu[] = sous_menu('Stat_Base.php', 'Statistics', 'I');
    $menu[] = sous_menu('Liste_Liens.php', 'Links', 'I');
    $menu[] = '1^^^Anniversaires.php^^^Anniversaires^^^I^^^';

    $menu[] = '0^^^ ^^^Gestion du site^^^G^^^';
    $menu[] = sous_menu('Edition_Parametres_Site.php', 'Site_parameters', 'G');
    $menu[] = sous_menu('Edition_Parametres_Graphiques.php', 'Design', 'G');
    $menu[] = sous_menu('Liste_Utilisateurs.php', 'Users_List', 'G');
    $menu[] = sous_menu('Liste_Connexions.php', 'Connections', 'G');
    if (!$SiteGratuit) {
        $menu[] = '1^^^https://tech.geneamania.net/Verif_Version.php?Version=' . $Version . '^^^Vérification de la version de Généamania^^^G^^^';
        $menu[] = sous_menu('Admin_Tables.php', 'Tables_Admin', 'G');
        $menu[] = '1^^^https://genealogies.geneamania.net/Gratuits_Premiums.php^^^Différences gratuit / Premium^^^G^^^';
    }

    $num_div = 0;
    $num_puce = 0;

    if ($type_menu == 'D') {
        if ($formu) echo '<form method="post" action="">';
        echo '<select name="example" size="1" onchange="document.location = this.options[this.selectedIndex].value;">' . "\n";
        echo '<option value="' . Get_Adr_Base_Ref() . 'index.php">Menu rapide...</option>' . "\n";
        if ($formu)
            echo '<option value="' . Get_Adr_Base_Ref() . 'index.php">Accueil</option>' . "\n";
    }
    $deb_opt  = 0;
    $count = count($menu);
    for ($nb = 0; $nb < $count; $nb++) {
        $elements = explode('^^^', $menu[$nb]);
        $rep = '';
        // On affiche les lignes publiques
        // ou les autres si on a les droits
        if (($elements[3] == 'I') or ($droits == $elements[3]) or ($droits == 'G')) {
            if ($elements[0] == 0) {
                if ($type_menu == 'D') {
                    if ($deb_opt) echo '</optgroup>' . "\n";
                    echo '<optgroup label="' . my_html($elements[2]) . '">' . "\n";
                } else {
                    if ($deb_opt) echo '</div>' . "\n";
                    echo '<br>' . my_html($elements[2]) . '&nbsp;' . "\n";
                    ++$num_div;
                    Image_Div('menu_open', 'ajout' . $num_div, 'Flèche', 'id_div' . $num_div);
                }
                $deb_opt = 1;
            } else {
                if ($type_menu == 'D') {
                    echo '<option value="';
                    echo $rep . $elements[1] . '">' . my_html($elements[2]) . '</option>' . "\n";
                } else {
                    echo '&nbsp;&nbsp;&nbsp;<img id="puce' . ++$num_puce . '" src="' . $chemin_images_icones . $Icones['menu_option'] . '" alt="Puce"/>' . "\n";
                    echo '<a href="' . $rep . $rep . $elements[1] . '">' . my_html($elements[2]) . '</a><br>' . "\n";
                }
            }
        }
    }
    if ($type_menu == 'D') {
        if ($deb_opt) echo '</optgroup>' . "\n";
        echo '</select>' . "\n";
        if ($formu) echo '</form>' . "\n";
    } else {
        if ($deb_opt) echo '</div>' . "\n";

        // Masquage des div créés
        echo '<script type="text/javascript">' . "\n";
        echo '<!--' . "\n";
        for ($x = 1; $x <= $num_div; $x++) {
            echo 'cache_div(\'id_div' . $x . '\');' . "\n";
        }
        echo '//-->' . "\n";
        echo '</script>' . "\n";
    }
}

// Libellé personne / filiation / union ...
function lib_pfu($TypeObjet, $dem_article = false)
{
    global $art_indet;
    $txt = '';
    switch ($TypeObjet) {
        case 'P':
            $txt = 'personne';
            $article = 'la ';
            $art_indet = 'une';
            break;
        case 'U':
            $txt = 'union';
            $article = 'l\'';
            $art_indet = 'une';
            break;
        case 'F':
            $txt = 'filiation';
            $article = 'la ';
            $art_indet = 'une';
            break;
        case 'E':
            $txt = 'évènement';
            $article = 'l\'';
            $art_indet = 'un';
            break;
        case 'V':
            $txt = 'ville';
            $article = 'la ';
            $art_indet = 'une';
            break;
        case 'D':
            $txt = 'département';
            $article = 'la ';
            $art_indet = 'un';
            break;
        case 'R':
            $txt = 'région';
            $article = 'la ';
            $art_indet = 'une';
            break;
        case 'I':
            $txt = 'image';
            $article = 'l\'';
            $art_indet = 'une';
            break;
        case 'O':
            $txt = 'nom';
            $article = 'le ';
            $art_indet = 'un';
            break;
        case 'L':
            $txt = 'lien';
            $article = 'le ';
            $art_indet = 'un';
            break;
        case 'S':
            $txt = 'source';
            $article = 'la ';
            $art_indet = 'une';
            break;
        case 's':
            $txt = 'subdivision';
            $article = 'la ';
            $art_indet = 'une';
            break;
        default:
            $txt = 'autre';
            $article = 'l\'';
            $art_indet = 'un';
    }
    if ($dem_article) $txt = $article . $txt;
    return $txt;
}

// Colonne de titre dans un tableau
function col_titre_tab($lib, $larg)
{
    echo '<tr><td class="label" width="' . $larg . '%">&nbsp;' . my_html(ucfirst($lib)) . '&nbsp;</td>';
}
function col_titre_tab_noClass($lib, $larg)
{
    echo '<tr><td width="' . $larg . '%">&nbsp;' . my_html($lib) . '&nbsp;</td>';
}
function colonne_titre_tab($lib)
{
    global $larg_titre;
    echo col_titre_tab($lib, $larg_titre) . '<td class="value">';
}

function lit_fonc_fichier()
{
    $nom_fic = 'version.txt';
    if (file_exists($nom_fic)) {
        $fic = fopen($nom_fic, 'r');
        if ($fic) {
            $vers_fic = trim(fgets($fic));
            fclose($fic);
        }
    }
    return $vers_fic;
}

// Affichage des noms secondaires (princ = 'N') pour la personne
function Aff_NS($Personne, $sortie = 'H')
{
    $req_ns = 'SELECT b.nomFamille, a.comment FROM ' . nom_table('noms_personnes') . ' a, ' . nom_table('noms_famille') . ' b' .
        ' where b.idNomFam = idNom' .
        ' and a.idPers = ' . $Personne .
        ' and a.princ = \'N\'' .
        ' order by b.nomFamille';
    $res_ns = lect_sql($req_ns);
    if ($res_ns->rowCount()) {
        HTML_ou_PDF('Noms secondaires :<br>' . "\n", $sortie);
        while ($enr_ns = $res_ns->fetch(PDO::FETCH_NUM)) {
            HTML_ou_PDF('  ' . $enr_ns[0], $sortie);
            if ($enr_ns[1] != '') HTML_ou_PDF(' (' . $enr_ns[1] . ')', $sortie);
            HTML_ou_PDF('<br>' . "\n", $sortie);
        }
    }
}

// Mémorisation de la personne en cours de consultation ou de modification
// Si la personne est déjà mémorisée, on ne fait rien
function memo_pers($Refer, $Nom, $Prenoms)
{
    if ($Refer != -1) {
        $est_memo = false;
        if (isset($_SESSION['mem_pers'])) {
            // Détection mémorisation antérieure
            for ($nb = 0; $nb < 3; $nb++) {
                if ($_SESSION['mem_pers'][$nb] == $Refer) $est_memo = true;
            }
            // La personne n'est pas mémorisée, on la mémorise
            if (!$est_memo) {
                // On décale les mémorisation
                for ($nb = 3; $nb > 0; $nb--) {
                    $_SESSION['mem_pers'][$nb]    = $_SESSION['mem_pers'][$nb - 1];
                    $_SESSION['mem_nom'][$nb]     = $_SESSION['mem_nom'][$nb - 1];
                    $_SESSION['mem_prenoms'][$nb] = $_SESSION['mem_prenoms'][$nb - 1];
                }
                // On mémorise les infos courantes
                $_SESSION['mem_pers'][0]    = $Refer;
                $_SESSION['mem_nom'][0]     = $Nom;
                $_SESSION['mem_prenoms'][0] = $Prenoms;
            }
        }
    }
}

// Affiche des lignes vides dans un formaulaire de saisie
function ligne_vide_tab_form($nb_lig)
{
    for ($nb = 1; $nb <= $nb_lig; $nb++) {
        echo '<tr><td colspan="2">&nbsp;</td></tr>' . "\n";
    }
}

// Affichage conditionné des boutons ok, annuler, supprimer
function bt_ok_an_sup($lib_ok, $lib_an, $lib_sup, $lib_conf, $dans_table = true, $suppl = false)
{
    global $chemin_images_icones, $Icones, $lib_Retour, $lib_Annuler, $lib_Rechercher, $hidden;

    if ($dans_table) echo '<tr><td colspan="2" align="center">';
    // Lors d'un appel supplémentaire, il ne faut pas re-créer les champs cachés
    if (!$suppl) {
        echo '<input type="' . $hidden . '" name="cache" id="cache" value=""/>' . "\n";
        echo '<input type="' . $hidden . '" name="ok" id="ok" value=""/>' . "\n";
        echo '<input type="' . $hidden . '" name="annuler" id="annuler" value=""/>' . "\n";
        echo '<input type="' . $hidden . '" name="supprimer" id="supprimer" value=""/>' . "\n";
    }

    $id_but = 'boutons';
    if ($suppl) $id_but .= 'b';
    echo '<div id="' . $id_but . '">' . "\n";
    echo '<br>';
    echo '<table border="0" cellpadding="0" cellspacing="0">' . "\n";
    echo '<tr><td>&nbsp;';
    echo '<div class="buttons">';
    if ($lib_ok != '') {
        if ($lib_ok == $lib_Rechercher) $Icone = 'chercher';
        else $Icone = 'fiche_validee';
        echo '<button type="submit" class="positive" id="bouton_ok" ' .
            'onclick="document.forms.saisie.cache.value=\'ok\';document.forms.saisie.ok.value=\'' . addslashes($lib_ok) . '\';"> ' .
            '<img src="' . $chemin_images_icones . $Icones[$Icone] . '" alt=""/>' . $lib_ok . '</button>';
    }
    if ($lib_an != '') {
        if ($lib_an == $lib_Retour) $Icone = 'previous';
        else $Icone = 'cancel';
        echo '<button type="submit" ' .
            'onclick="document.forms.saisie.cache.value=\'an\';document.forms.saisie.annuler.value=\'' . $lib_Annuler . '\';"> ' .
            '<img src="' . $chemin_images_icones . $Icones[$Icone] . '" alt=""/>' . $lib_an . '</button>';
    }
    if ($lib_sup != '')
        echo '<button type="submit" class="negative" ' .
            'onclick="confirmer(\'' . addslashes($lib_conf) . '\',this);"> ' .
            '<img src="' . $chemin_images_icones . $Icones['supprimer'] . '" alt=""/>' . $lib_sup . '</button>';
    echo '</div>';

    echo '</td></tr>';
    echo '</table>' . "\n";
    echo '</div>' . "\n";

    if ($dans_table) echo '</td></tr>' . "\n";
}

// Renvoye un nombre paddé à gauche avec des zéros (2 car) ==> jour, mois
function zerofill2($zInitiale)
{
    return sprintf('%02s', $zInitiale);
}

// Renvoye un nombre paddé à gauche avec des zéros  (4 car) ==> année
function zerofill4($zInitiale)
{
    return sprintf('%04s', $zInitiale);
}

// Récupération du microtime pour profilage de script
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

// Affichage de l'heure
function aff_heure()
{
    $temps = time();
    $jour = date('j', $temps);  //format numerique : 1->31
    $annee = date('Y', $temps); //format numerique : 4 chiffres
    $mois = date('m', $temps);
    $heure = date('H', $temps);
    $minutes = date('i', $temps);
    $secondes = date('s', $temps);
    $date = $jour . '/' . $mois . '/' . $annee . ' à ' . $heure . ':' . $minutes . ':' . $secondes . ' sec';
    echo $date . '<br>';
}

function aff_origine()
{
    global $Horigine;
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
}

// Retourne le chemin d'un document en fonction de son type
function get_chemin_docu($NatureDoc)
{
    global $chemin_docs_HTM, $chemin_docs_PDF, $chemin_docs_IMG, $chemin_docs_TXT;
    switch ($NatureDoc) {
        case 'HTM':
            $chemin_docu = $chemin_docs_HTM;
            break;
        case 'PDF':
            $chemin_docu = $chemin_docs_PDF;
            break;
        case 'IMG':
            $chemin_docu = $chemin_docs_IMG;
            break;
        case 'TXT':
            $chemin_docu = $chemin_docs_TXT;
            break;
    }
    return $chemin_docu;
}

// Retourne le type mime d'un document en fonction de son type
function Get_Type_Mime($NatureDoc)
{
    switch ($NatureDoc) {
        case 'HTM':
            $le_type = 'text/html';
            break;
        case 'PDF':
            $le_type = 'application/pdf';
            break;
        case 'IMG':
            $le_type = 'image';
            break;
        case 'TXT':
            $le_type = 'text';
            break;
    }
    return $le_type;
}

// Renvoye le bon ordre en fonction du comportement click / mouse over
function Survole_Clic_Div($id_div)
{
    global $Comportement;
    if ($Comportement == 'C') $evenement = 'onclick';
    else $evenement = 'onmouseover';
    return $evenement . '="inverse_div(\'' . $id_div . '\');"';
}

function HTML_ou_PDF($texte, $sortie, $ech = true)
{
    global $pdf, $def_enc;
    if ($sortie == 'P') $texte = chaine_pdf($texte);
    switch ($sortie) {
        //case 'H' : if ($ech) echo $texte; else return $texte; break;
        case 'H':
            echo $texte;
            break;
        // case 'P' : $texte = str_replace('<br>','<br>',$texte); $pdf->WriteHTML(html_entity_decode($texte, ENT_QUOTES, $def_enc )); break;
        case 'P':
            $texte = str_replace('<br>', '<br>', $texte);
            $pdf->WriteHTML($texte);
            break;
    }
}

// Détermine l'état d'une personne : présumée vivante ou non
function determine_etat_vivant($naissance, $deces = '')
{
    global $date_lim_vivant;

    // Initialisation de la variable de retour
    $vivant = true;

    if ($deces != '') {
        $vivant = false;
    } else {
        if (strlen($naissance) == 10) {
            $precision = $naissance[9];
            $calend    = $naissance[8];
            // Les personnes nées sous la révolution française sont décédées
            if ($calend == 'R')    $vivant = false;

            // Traitement des personnes nées en dehors de la révolution française
            if ($calend == 'G') {
                switch ($precision) {
                    case 'L':
                    case 'A':
                    case 'E':
                        if ($naissance < $date_lim_vivant) $vivant = false;
                }
            }
        }
    }
    return $vivant;
}

// Sauvegarde d'une image GD pour récupération éventuelle
function sauve_img_gd($image)
{
    global $Environnement, $chemin_images_util, $n_sv_img_gd;
    // Si environnement local, sauvegarde pour utilisation externe
    if (($Environnement == 'L') or ($n_sv_img_gd != '')) {
        if ($n_sv_img_gd == '') $n_sv_img_gd = '__sv_img_gd.png';
        @ImagePng($image, $chemin_images_util . $n_sv_img_gd);
    }
}

function Affiche_Icone_Lien_Bt($lien, $icone, $lib)
{
    global $chemin_images_icones, $Icones;
    $a = '<div class="buttons">';
    $a .= '<a ' . $lien . '"><img src="' . $chemin_images_icones . $Icones[$icone] . '" alt="' . $lib . '"/> ' . $lib . '</a>';
    $a .= '</div>' . "\n";
    return $a;
}

// Ajout d'un bouton retour
function Bouton_Retour($lib_Retour, $compl = '')
{
    echo '<form id="saisie" method="post" action="' . my_self() . $compl . '">' . "\n";
    bt_ok_an_sup('', $lib_Retour, '', '', false);
    echo '</form>';
}

// Positionne la couleur par défaut pour les PDF
function PDF_Set_Def_Color($PDF)
{
    global $coul_pdf, $SiteGratuit, $Premium;
    if ((!isset($coul_pdf)) or (($SiteGratuit) and (!$Premium))) {
        $coul_pdfr = 0;
        $coul_pdfv = 0;
        $coul_pdfb = 255;
    } else {
        $coul_pdfr = hexdec(substr($coul_pdf, 1, 2));
        $coul_pdfv = hexdec(substr($coul_pdf, 3, 2));
        $coul_pdfb = hexdec(substr($coul_pdf, 5, 2));
    }
    $PDF->SetTextColor($coul_pdfr, $coul_pdfv, $coul_pdfb);
}
function PDF_SetColor($PDF, $r, $v, $b)
{
    $PDF->SetTextColor($r, $v, $b);
}
function PDF_AddPolice($PDF)
{
    global $font_pdf, $list_font_pdf;
    // La police ne fait pas partie des polices par défaut, il faut l'installer
    if (!array_search($font_pdf, $list_font_pdf)) {
        $list_font_reg = array('LibreBaskerville', 'Quintessential', 'ZTMota', 'RocknRollOne');
        if (array_search($font_pdf, $list_font_reg))
            $nom_reg = $font_pdf . '-Regular.php';
        else
            $nom_reg = $font_pdf . '.php';
        $rep_font_pdf = 'font/';
        //echo $rep_font_pdf.$nom_reg.'<br >';
        if (file_exists($rep_font_pdf . $nom_reg)) {
            //echo 'add font'.$nom_reg.'<br >';
            $PDF->AddFont($font_pdf, '', $nom_reg);
            if (file_exists($rep_font_pdf . $font_pdf . '-Bold.php'))
                $PDF->AddFont($font_pdf, 'B', $font_pdf . '-Bold.php');
            else
                $PDF->AddFont($font_pdf, 'B', $nom_reg);
            if (file_exists($rep_font_pdf . $font_pdf . '-Italic.php'))
                $PDF->AddFont($font_pdf, 'I', $font_pdf . '-Italic.php');
            else
                $PDF->AddFont($font_pdf, 'I', $nom_reg);
        } else $font_pdf = $list_font_pdf[0];
    }
}

// Renvoye un Query String compatible W3C
function Query_Str()
{
    return str_replace('&', '&amp;', $_SERVER['QUERY_STRING']);
}

// Appel de my_html// Appel de htmlentities
function my_html($chaine)
{
    global $def_enc;
    return htmlentities($chaine, ENT_QUOTES, $def_enc);
}
function my_html_inv($chaine)
{
    global $def_enc;
    return html_entity_decode($chaine, ENT_QUOTES, $def_enc);
}

// Récupère la liste des champs d'une requête SQL
function get_fields($req, $enleve_descripteur)
{
    $res = '';
    $ureq = strtoupper($req);
    $p1 = strpos($ureq, 'SELECT ');
    $p2 = strpos($ureq, ' FROM ');
    if (($p1 !== false) and ($p2 !== false)) {
        $req = substr($req, 7, $p2 - 7);
        $res = explode(",", $req);
    }
    if ($enleve_descripteur) {
        $c_champs = count($res);
        for ($nb = 0; $nb < $c_champs; $nb++) {
            $nom_champ = $res[$nb];
            $ppoint = strpos($nom_champ, '.');
            if ($ppoint !== false)
                $res[$nb] = substr($nom_champ, $ppoint + 1);
        }
    }
    return $res;
}

function bouton_radio($nom, $valeur, $lib, $chk = false)
{
    if ($chk) $chk = 'checked="checked"';
    else $chk = '';
    echo '<input type="radio" id="' . $nom . $valeur . '" name="' . $nom . '" value="' . $valeur . '" ' . $chk . '/>'
        . '<label for="' . $nom . $valeur . '">' . $lib . '</label>&nbsp;';
}

function open_log()
{
    $f_log = ouvre_fic('log.txt', 'a+');
    // $f_log = fopen('log.txt', 'a+');
    return $f_log;
}

function affiche_var($nom)
{
    global $$nom;
    //echo ' variable $'.$nom.' = '.$$nom.'<br>';
    echo '$' . $nom . ' = ';
    var_dump($$nom);
    echo '<br>';
}

/* Retourne le libellé d'une ville */
/* P1 :numéro de la ville ; P2 : sortie HTML du libellé ; P3 : recherche du commentaire sur la ville */
function lib_ville_new($num_ville, $html = 'O', $rech_comment = false)
{
    global $Z_Mere, $Lat_V, $Long_V, $debug, $rech, $premier_lib_v, $SiteGratuit, $Premium, $Commentaire, $villes_ref, $villes_lib;
    $lib = '';
    $Z_Mere = 0;
    $Lat_V = 0;
    $Long_V = 0;
    $Commentaire = '';
    $premier_lib_v = true;
    // Si le libellé a déjà été demandé, on va chercher les infos en mémoire, sinon on accède à la base
    if ($debug) {
        echo ' <br>$num_ville 1 dans lib : ' . $num_ville . '<br>';
        affiche_var('villes_ref');
        affiche_var('villes_lib');
    }
    if (isset($villes_ref)) {
        $rech = array_search($num_ville, $villes_ref);
        if ($rech !== false) {
            $lib = $villes_lib[$rech];
            $premier_lib_v = false;
        }
    }

    if ($premier_lib_v) {
        if ($num_ville != 0) {
            $sql = 'select nom_ville, Zone_Mere, Latitude, Longitude from ' . nom_table('villes') . ' where identifiant_zone = ' . $num_ville . ' limit 1';
            if ($res = lect_sql($sql)) {
                if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                    if ($html == 'O') $lib = my_html($enr[0]);
                    else $lib = $enr[0];
                    $Z_Mere = $enr[1];
                    $Lat_V = $enr[2];
                    $Long_V = $enr[3];
                    $villes_ref[] = intval($num_ville);
                    $villes_lib[] = $lib;
                }
                $res->closeCursor();
                unset($res);
                if ((!$SiteGratuit) or ($Premium)) {
                    if ($rech_comment) {
                        $Existe_Commentaire = Rech_Commentaire($num_ville, 'V');
                    }
                }
            }
        }
        return $lib;
    } else return $lib;
}

// Affiche un div pour les notes
function Div_Note($texte)
{
    global $debug;
    //return '<a href="#" class="info2">'.Affiche_Icone('note').'<span>'.$texte.'</span></a>';	
    $strip_list = array('p', 'span');
    foreach ($strip_list as $tag) {
        $texte = preg_replace('/<\/?' . $tag . '(.|\s)*?>/', '', $texte);
    }
    return '&nbsp;<span class="help-tip"><span >' . $texte . '</span></span>&nbsp;';
}

// Affiche un div pour les notes ; "old style"
function Div_Note_Old($nom_image, $nom_div, $texte)
{
    global $def_enc, $LG_show_comment;
    Note_Div($nom_image, my_html($LG_show_comment), $nom_div);
    echo '<hr width="80%" align="left"/>';
    echo 'Note :<br/>';
    echo html_entity_decode($texte, ENT_QUOTES, $def_enc);
    echo '<hr width="80%" align="left"/>';
    echo '</div>' . "\n";
    echo '<script type="text/javascript">' . "\n";
    echo '<!--' . "\n";
    echo 'cache_div(\'' . $nom_div . '\');' . "\n";
    echo '//-->' . "\n";
    echo '</script>' . "\n";
}


function my_self()
{
    return my_html($_SERVER['PHP_SELF']);
}

// Recherche d'un divorce éventuel ; prend en entrée la référence de l'union
function get_divorce($Reference)
{
    global $lib_div;
    $retour = false;
    $lib_div = '';
    $sel_div = 'SELECT "1", Debut ' .
        'FROM ' . nom_table('concerne_objet') . ' co, ' . nom_table('evenements') . ' ev ' .
        'where ev.Reference = co.Evenement' .
        ' and ev.Code_Type = "DIV"' .
        ' and co.Reference_Objet = ' . $Reference .
        ' and co.Type_Objet = "U" ' .
        'limit 1';
    $res_div = lect_sql($sel_div);
    if ($enreg_div = $res_div->fetch(PDO::FETCH_NUM)) {
        if ($enreg_div[0] == '1') {
            $retour = true;
            $lib_div = ' (divorce ';
            $date_div = $enreg_div[1];
            if ($date_div != '') $lib_div .= Etend_date_2($date_div);
            $lib_div .= ')';
        }
    }
    return $retour;
}

/*
http://assemblysys.com/fr/calcul-de-distance-en-fonction-de-la-latitude-et-longitude-en-php/
Description : Calcul de la distance entre 2 points en fonction de leur latitude/longitude
Auteur : Rajesh Singh (2014)
Site web : AssemblySys.com
 
Si vous trouvez ce code utile, vous pouvez montrer votre
appréciation à Rajesh en lui offrant un café ;)
PayPal: rajesh.singh@assemblysys.com
 
Du moment que cette notice (y compris le nom et détails sur l'auteur) est
inclue et N'EST PAS ALTÉRÉE, ce code est distribué selon la GNU General
Public License version 3 : http://www.gnu.org/licenses/gpl-3.0.fr.html
*/
function distanceCalculation($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2)
{
    // Calcul de la distance en degrés
    $degrees = rad2deg(acos((sin(deg2rad($point1_lat)) * sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat)) * cos(deg2rad($point2_lat)) * cos(deg2rad($point1_long - $point2_long)))));

    // Conversion de la distance en degrés à l'unité choisie (kilomètres, milles ou milles nautiques)
    switch ($unit) {
        case 'km':
            $distance = $degrees * 111.13384; // 1 degré = 111,13384 km, sur base du diamètre moyen de la Terre (12735 km)
            break;
        case 'mi':
            $distance = $degrees * 69.05482; // 1 degré = 69,05482 milles, sur base du diamètre moyen de la Terre (7913,1 milles)
            break;
        case 'nmi':
            $distance =  $degrees * 59.97662; // 1 degré = 59.97662 milles nautiques, sur base du diamètre moyen de la Terre (6,876.3 milles nautiques)
    }
    return round($distance, $decimals);
}

// Ajoute le modificateur de nom de fichier
function construit_fic($chemin, $nom_fic, $ext = '')
{
    global $mod_nom_fic;
    $nom_fic = str_replace('#', $mod_nom_fic, $nom_fic);
    $nom_fic_out = $chemin . $nom_fic;
    if ($ext != '') $nom_fic_out .= '.' . $ext;
    return $nom_fic_out;
}

function aff_legend($lib)
{
    echo '<legend>' . my_html(ucfirst($lib)) . '</legend>' . "\n";
}

// Affiche le conseil OpenStreetmap
function aff_tip_carte()
{
    echo Affiche_Icone('tip', LG_TIP) . ' ' . my_html(LG_TIP_OPENSTREETMAP) . ' <a href="http://www.OpenStreetMap.com" target="_blank">OpenStreetMap</a></td></tr>' . "\n";
}

// Demande l'affichage d'une carte OpenStreetMap si les coordonnées sont renseignées
function appelle_carte_osm()
{
    global $Lat_V, $Long_V, $LG_Show_On_Map;
    if (($Lat_V != 0) or ($Long_V != 0)) {
        echo '&nbsp;' .
            Affiche_Icone_Lien(
                'href="http://www.openstreetmap.org/' . '?lat=' . $Lat_V . '&amp;lon=' . $Long_V . '&amp;mlat=' . $Lat_V . '&amp;mlon=' . $Long_V . '&amp;zoom=10" target="_blank"',
                'map_go',
                $LG_Show_On_Map
            );
    }
}

// Chaine pdf pour utf-8 ?
function chaine_pdf($chaine)
{
    global $def_enc;
    if ($def_enc == 'UTF-8')
        // $chaine = utf8_decode($chaine);
        // Compatibilité PHP 8.2, correction de Christian
        $chaine = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $chaine);
    return $chaine;
}

// Ouvre un fichier avec le BOM éventuel pour l'UTF-8
function ouvre_fic($nom_fic, $mode)
{
    global $def_enc;
    $fp = fopen($nom_fic, $mode);
    // Byte Order mark (BOM) for UTF-8
    if ($def_enc == 'UTF-8') fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
    return $fp;
}

// Affiche le label d'un bouton radio
function lb_radio($id_for, $lib)
{
    return '<label for="' . $id_for . '">' . $lib . '</label>';
}

// Affiche le choix de la sortie : écran, texte ou csv (facultatif)
function affiche_sortie($csv)
{
    global $LG_Ch_Output_Screen, $LG_Ch_Output_Text, $LG_Ch_Output_CSV, $est_privilegie;
    echo '<input type="radio" id="Sortie_e" name="Sortie" value="e" checked="checked"/><label for="Sortie_e">' . $LG_Ch_Output_Screen . '</label>&nbsp;';
    echo '<input type="radio" id="Sortie_t" name="Sortie" value="t"/><label for="Sortie_t">' . $LG_Ch_Output_Text . '</label>&nbsp;';
    if ($csv) {
        // L'export CSV n'est disponible qu'à partir du profil privilégié
        if ($est_privilegie) echo '<input id="Sortie_c" type="radio" name="Sortie" value="c"/><label for="Sortie_c">' . $LG_Ch_Output_CSV . '</label>';
    }
}

// Affiche le temps d'exécution d'une fonction
function affiche_temps($quoi)
{
    global $debut;
    $fin = microtime_float();
    $totaltime = ($fin - $debut);
    $exec_time = number_format($totaltime, 3);
    echo "Debut : " . date("H:i:s", $debut);
    echo "<br>Fin : " . date("H:i:s", $fin);
    echo "<br> $quoi exécuté en " . $exec_time . " sec<br>";
}
function affiche_heure_precise($quoi)
{
    $ladate = DateTime::createFromFormat('U.u', microtime(TRUE));
    echo $quoi . ' ' . $ladate->format('d-m-Y H:i:s.u') . '<br>';
}

// Corrige les null sur un enregistrement personne ; peuvent générer des plantages
function rectif_null_pers(&$enreg)
{
    if (is_null($enreg['Numero']))
        $enreg['Numero'] = '';
    if (is_null($enreg['Ne_le']))
        $enreg['Ne_le'] = '';
    if (is_null($enreg['Decede_Le']))
        $enreg['Decede_Le'] = '';
}


header('content-type: text/html; charset=' . $def_enc);
