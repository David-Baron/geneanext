<?php

//=====================================================================
// 
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

// Initilisation des infirmations de connexion
function Init_infos_cnx()
{
    global $util_defaut;
    $_SESSION['estInvite'] = true;
    $_SESSION['estPrivilegie'] = false;
    $_SESSION['estContributeur'] = false;
    $_SESSION['estGestionnaire'] = false;
    $_SESSION['niveau'] = 'I';
    $_SESSION['nomUtilisateur'] = $util_defaut;
    $_SESSION['estCnx'] = false;
    $est_cnx = false;
}

/** @deprecated */
function cryptmail($addmail)
{
    $addmailcode = '';
    $longueur = strlen($addmail);
    for ($x = 0; $x < $longueur; $x++) {
        $ord = ord(substr($addmail, $x, 1));
        $addmailcode .= "&#$ord;";
    }
    return $addmailcode;
}

// Récupération des variables de l'affichage précédent
$tab_variables = array('NomU', 'motPasse', 'geneGraphe', 'ok', 'sortir');
foreach ($tab_variables as $nom_variables) {
    $$nom_variables = '';
    //	lecture des variables $_POST avec sécurité
    if (isset($_POST[$nom_variables])) $$nom_variables = trim(htmlspecialchars(addslashes($_POST[$nom_variables]), ENT_QUOTES, $def_enc));
}

/** @deprecated Not used anymore. */
// $is_windows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? true : false;

/** 
 * Demande de lancement de GénéGraphe
 * @deprecated GeneGraphe file cannot be moved, which causes security issues. It will no longer be distributed with the application. 
 */
/* if ($geneGraphe == 'exec') {
    $cmd = 'app/ressources/GeneGraphe.jar';
    if ($is_windows) {
        pclose(popen("start /B " . $cmd, "r"));
    } else {
        $cmd = getcwd() . "/" . $cmd;
        exec("DISPLAY=:0.0 " . $cmd . " > /dev/null &");
    }
} */

$max_tentatives = 5; // Nombre maximum de tentatives de connexions successives
$util_defaut = 'Anonyme'; // Code utilisateur par défaut

$_SESSION['sens'] = '>';

if (isset($_SESSION['pages'])) unset($_SESSION['pages']); // On vide l'empilement des pages
if (!isset($_SESSION['mem_pers'])) { // Mémorisation des fiches personnes visualisées ==> initialisation
    for ($nb = 0; $nb < 3; $nb++) {
        $_SESSION['mem_pers'][$nb] = 0;
        $_SESSION['mem_nom'][$nb] = '-';
        $_SESSION['mem_prenoms'][$nb] = '-';
    }
}

$x = Lit_Env();
$id_cnx = $x;

//$Environnement = 'I';
if ($Environnement == 'L') $RepGenSite = $RepGenSiteLoc;
else                       $RepGenSite = $RepGenSiteInt;

// Lit la version contenu dans le fichier de référence
$vers_fic = lit_fonc_fichier();


//	Contrôle des droits
// En local, on a tous les droits par défaut
if ($Environnement == 'L') {
    $_SESSION['niveau'] = 'G';
    $_SESSION['estInvite'] = true;
    $_SESSION['estPrivilegie'] = true;
    $_SESSION['estContributeur'] = true;
    $_SESSION['estGestionnaire'] = true;
    $_SESSION['idUtil'] = -1;
    $_SESSION['estCnx'] = true;
}

// De cujus par défaut
if (!isset($_SESSION['decujus'])) {
    $decujus = -1;
    $_SESSION['decujus'] = $decujus;
    $_SESSION['decujus_defaut'] = 'O';
}

controle_utilisateur('I');

if (!isset($_SESSION['estCnx'])) $_SESSION['estCnx'] = false;
$est_cnx = ($_SESSION['estCnx'] === true ? true : false);

// L'utilisateur se déconnecte, on ré-initalise les droits
if ($sortir == $lib_Deconnecter) Init_infos_cnx();

$self = my_self();

// Pour palier aux soucis de session entre des sous-sites d'un même site, on contrôle que l'on est bien sur le même sous-site
if ($Environnement == 'I') {
    if ($self[strlen($self) - 1] == '/') $self = substr($self, 0, strlen($self) - 1); // Au cas où l'utilsateur mettrait un / en dernière position on le supprime...
    $deb_self = substr($self, 0, strrpos($self, '/'));
    if (!isset($_SESSION['deb_site'])) $_SESSION['deb_site'] = '';
    // Si ce n'est pas bon, on fait un RAZ des informations de connexion
    if ($deb_self != $_SESSION['deb_site']) {
        Init_infos_cnx();
        if (isset($_SESSION['mem_pers'])) unset($_SESSION['mem_pers']);
        if (isset($_SESSION['laDateJ'])) {
            unset($_SESSION['laDateM']);
            unset($_SESSION['laDateJ']);
            unset($_SESSION['AnnivA']);
            unset($_SESSION['AnnivD']);
        }
    }
    $_SESSION['deb_site'] = $deb_self;
}

// On sauvegarde le nombre de tentatives successives échouées
if (!isset($_SESSION['tentatives'])) $_SESSION['tentatives'] = 0;


//	Changement d'utilisateur
$mesErreur = '';
if ($NomU != '') {

    // On bascule en mode invité par défaut
    Init_infos_cnx();
    $niveauNouveau = '';

    // $motPasseSha = hash('sha256', $salt1 . $NomU . $salt2 . $motPasse . $salt3);
    $motPasseSha = hash('sha256', ';$€°d' . $NomU . '#\'_^' . $motPasse . '@")[&ù');
    // echo $NomU.' / '.$motPasse.' / '.$motPasseSha;

    $sql = 'SELECT niveau , nom, idUtil FROM ' . nom_table('utilisateurs') .
        ' WHERE codeUtil = \'' . $NomU . '\' AND motPasseUtil = \'' . $motPasseSha . '\' limit 1';
    if ($res = lect_sql($sql)) {
        if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $niveauNouveau = $enreg[0];
            // Mémorisation de la connexion
            $req = 'insert into ' . nom_table('connexions') . " values(" . $enreg[2] . ",current_timestamp,'" . getenv("REMOTE_ADDR") . "')";
            $res = maj_sql($req);
        }
    }

    //
    if ($niveauNouveau != '') {
        // Ré-init du compteur de tentaives échouées
        $_SESSION['tentatives'] = 0;
        $_SESSION['niveau'] = $niveauNouveau;
        $_SESSION['nomUtilisateur'] = $enreg[1];
        $_SESSION['idUtil'] = $enreg[2];
        $_SESSION['estCnx'] = true;

        switch ($niveauNouveau) {
            case 'G':
                $_SESSION['estInvite'] = true;
                $_SESSION['estPrivilegie'] = true;
                $_SESSION['estContributeur'] = true;
                $_SESSION['estGestionnaire'] = true;
                break;
            case 'C':
                $_SESSION['estInvite'] = true;
                $_SESSION['estPrivilegie'] = true;
                $_SESSION['estContributeur'] = true;
                break;
            case 'P':
                $_SESSION['estInvite'] = true;
                $_SESSION['estPrivilegie'] = true;
                break;
            case 'I':
                $_SESSION['estInvite'] = true;
                break;
        }
    } else {
        $mesErreur = $LG_index_connexion_error;
        $_SESSION['tentatives']++;
    }
}

$head_title = ($Nom !== '???') ? $Nom : 'Site genealogique';
$title = ($Nom !== '???') ? $LG_index_welcome . ' ' . $Nom : 'Site genealogique';
// Utilisateur par défaut si non loggué
if (!isset($_SESSION['nomUtilisateur'])) $_SESSION['nomUtilisateur'] = $util_defaut;

echo '<!DOCTYPE html>';
echo '<html lang="' . $langue_min . '">';
echo '<head>';
Ecrit_Meta($head_title, $LG_index_desc . ' ' . $Nom, '');
echo '</head>';
echo '<body>';
echo '<table width="100%" cellspacing="0" cellpadding="0">';
echo '<tr align="center" v-align="middle"><td>';
echo '<font size="+3">' . $title . '</i></font>';

// Contrôle de la présence du nom du site
if ($_SESSION['estGestionnaire']) {
    if (!$x)
        echo '<br><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . $LG_tip . '" title="' . $LG_tip . '"> <font color="red" size="+2">' . $LG_index_tip_no_param . '</font>';
    echo '<br>';
}

/** @deprecated */
/* $maintenance = false; // Possibilité d'afficher un message de maintenance sur présence d'un fichier
if (file_exists(__DIR__ . '/../maintenance')){ $maintenance = true;}

if ($maintenance) {
    echo '<br><br><br><font color="red" size="+2"><br>' . $LG_index_tip_maintenance . '...</font><br><br><br>';
}

$verrou = false; // Possibilité de bloquer la connexion ==> verrouillage du site
if (file_exists(__DIR__ . '/verrou')) $verrou = true;
if ($maintenance) {
    echo '<br><br><br><font color="red" size="+2"><br>' . $LG_index_tip_maintenance . '...</font><br><br><br>';
} */
echo '</td></tr>';
echo '</table>';

// Menus...
/* if (($vers_fic == $Version) && (!$maintenance) && (!$verrou)) { .... } */
$Existe_Commentaire = false;
$Presence_Commentaire = Rech_Commentaire(0, 'G');
if (($Presence_Commentaire) && (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
    $anniv_comment = true;
    $Existe_Commentaire = true;
}

// Récupération des anniversaires de naissance du jour et du lendemain
$nbAuj    = 0;
$nbDemain = 0;
// Date du jour
$LaDate = date('Ymd');
$xAnnee = substr($LaDate, 0, 4);
$xMoisA = substr($LaDate, 4, 2);
$xJourA = substr($LaDate, 6, 2);
// On ne refera les accès aux anniversaires que :
// - s'ils n'ont pas été faits
// - ou s'ils ont été faits sur un autre jour
$deja_acces_anniv = true;
if (!isset($_SESSION['laDateJ'])) {
    $_SESSION['laDateM'] = $xMoisA;
    $_SESSION['laDateJ'] = $xJourA;
    $deja_acces_anniv = false;
} else {
    if ($_SESSION['laDateJ'] != $xJourA) {
        $_SESSION['laDateM'] = $xMoisA;
        $_SESSION['laDateJ'] = $xJourA;
        $deja_acces_anniv = false;
    }
}
// Date du lendemain
$mkDemain = mktime(00, 00, 00, intval($xMoisA), intval($xJourA) + 1, intval($xAnnee));
$Demain = date('Ymd', $mkDemain);
$xMoisD  = substr($Demain, 4, 2);
$xJourD  = substr($Demain, 6, 2);
$n_personnes = nom_table('personnes');
$ajout = ' ';
$nbAuj = $_SESSION['AnnivA'];
$nbDemain = $_SESSION['AnnivD'];
if (!$deja_acces_anniv) {
    if (!$_SESSION['estPrivilegie']) $ajout = ' and Diff_Internet = \'O\'';
    $sql2 = 'SELECT count(*),\'A\' FROM ' . $n_personnes . ' WHERE Ne_le like \'____' . $xMoisA . $xJourA . '_L\'' . $ajout .
        ' union ' .
        'SELECT count(*),\'D\' FROM ' . $n_personnes . ' WHERE Ne_le like \'____' . $xMoisD . $xJourD . '_L\'' . $ajout;
    if ($res = lect_sql($sql2)) {
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $nb    = $row[0];
            $quand = $row[1];
            if ($quand == 'A')
                $nbAuj = $nb;
            else
                $nbDemain = $nb;
        }
        $_SESSION['AnnivA'] = $nbAuj;
        $_SESSION['AnnivD'] = $nbDemain;
    }
}

echo '<form method="post">';
echo '<div class="exemple" id="ex2">';
echo '<ul class="nav">';
echo '<li><a href="' . $root . '/liste_pers?Type_Liste=P">' . $LG_index_menu_pers . '</a></li>';
echo '<li><a href="' . $root . '/liste_nomfam">' . $LG_index_menu_names . '</a></li>';
echo '<li><a href="' . $root . '/liste_villes?Type_Liste=V">' . $LG_index_menu_towns . '</a></li>';
echo '<li><a href="' . $root . '/stat_base">' . $LG_Menu_Title['Statistics'] . '</a></li>';
echo '<li> ';
aff_menu('D', $_SESSION['niveau'], false);
echo '</li>';
echo '</ul>';
echo '</div>';
echo '</form>';

if (!$Base_Vide) {
    echo '<table width="60%" align="center">';
    //echo '<table width="80%" align="center" class="tab_bord_bas">';
    echo '<tr><td><fieldset style="width:90%;"><legend>' . $LG_index_quick_search . ' <img src="' . $root . '/assets/img/' . $Icones['help'] . '" alt="' . $LG_index_tip_search . '" title="' . $LG_index_tip_search . '"></legend>';
    echo '<table align="center">';
    echo '<tr><td>';
    echo '<fieldset><legend>' . $LG_index_menu_pers . '</legend>';
    echo '<form method="post" action="' . $root . '/Recherche_Personne" >';
    echo '<table>';
    echo '<tr><td>' . LG_PERS_NAME . ' :</td><td><input type="text" size="30" name="NomP"/></td>';
    echo '<td rowspan="2" valign="middle"><input type="submit" name="ok" value="' . $lib_Rechercher . '" style="background:url(' . $root . '/assets/img/' . $Icones['chercher'] . ') no-repeat;padding-left:18px;" /></td></tr>';
    echo '<tr><td>' . LG_PERS_FIRST_NAME . ' :</td><td><input type="text" size="30" name="Prenoms"/></td></tr>';
    echo '</table>';
    echo '<input type="hidden" name="Sortie" value="e">';
    echo '<input type="hidden" name="Son" value="o">';
    echo '</form>';
    echo '</fieldset>';
    echo '</td>';
    echo '<td valign="middle">';
    echo '<fieldset><legend>' . $LG_index_menu_towns . '</legend>';
    echo '<form method="post" action="' . $root . '/Recherche_Ville" >';
    echo '<input type="text" size="30" name="NomV"/>';
    echo '<input type="hidden" name="Sortie" value="e">';
    echo '<input type="hidden" name="Code_Postal" value="">';
    echo '<input type="hidden" name="Departement" value="-1">';
    echo '<input type="submit" name="ok" value="' . $lib_Rechercher . '" style="background:url(' . $root . '/assets/img/' . $Icones['chercher'] . ') no-repeat;padding-left:18px;" />';
    echo '</form>';
    echo '</fieldset>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</fieldset></td></tr></table>';
} else {
    // Affichage lien vers le noyau
    // Pour un gestionnaire
    if ($_SESSION['estGestionnaire']) {
        echo '<table width="60%" align="center"><tr align="center"><td>';
        echo '<br><a href="' . $root . '/noyau_pers">' . $LG_Menu_Title['Decujus_And_Family'] . '</a><br><br>';
        echo '</td></tr></table>';
    }
}

// Affichage du commentaire et de l'image
$Existe_Image_Gen = ($Image_Index != '') ? true : false;
if (!file_exists($chemin_images_util . $Image_Index)) $Existe_Image_Gen = false;
if ($Existe_Commentaire or $Existe_Image_Gen) {
    if ($Existe_Commentaire and $Existe_Image_Gen)
        $largeur = '80%';
    else
        $largeur = '50%';
    echo '<table width="' . $largeur . '" align="center"><tr>';
    if ($Existe_Commentaire) {
        if (!$Existe_Image_Gen)
            echo '<td valign="middle"> <br>' . $Commentaire . '<br> </td>';
        else
            echo '<td valign="middle">' . $Commentaire . '</td>';
    }
    if ($Existe_Image_Gen) {
        echo '<td width="50%" valign="middle" align="center">';
        Aff_Img_Redim_Lien($chemin_images_util . $Image_Index, 190, 190, 'image_gen');
        echo '</td>';
    }
    echo '</tr></table>';
}
$date_mod = '';
if ($Modif_Site != '0000-00-00 00:00:00') {
    $date_mod = $LG_index_last_update . ' ' . DateTime_Fr($Modif_Site);
}



echo '<table width="95%" cellspacing="1" cellpadding="3" align="center" class="tab_bord_bas">';
/** @todo Not the right place */
/* echo '<tr>';
echo '<td width="50%" class="tab_bord_bas"><font size="+1">' . $LG_index_news . '...</font></td>';
echo '<td width="50%" class="tab_bord_bas"><font size="+1">' . $LG_index_links . '...</font></td>';
echo '</tr>';
// Affichage des actualités
if (($SiteGratuit) && (!$Premium)) $pref_tables = 'gra_sg_'; // @todo not used?
$sql3 = 'SELECT Reference , Titre, Debut, Fin, Identifiant_zone FROM ' . nom_table('evenements') .
    ' WHERE Code_Type = "' . $TypeEv_actu . '" ORDER BY Reference DESC LIMIT 4';
$stmt3 = lect_sql($sql3);
$newscast = $stmt3->fetchAll(\PDO::FETCH_ASSOC);
echo '<tr><td>';
echo '<div id="liste">';
echo '<ul class="puces">';
foreach ($newscast as $news) {
    echo '<li>';
    if ($news['Debut'] != '') {
        $debut = Etend_2_dates($news['Debut'], $news['Fin'], true);
        echo $debut . ' : ';
    }
    if ($news['Identifiant_zone'] != 0) {
        echo my_html($news['Titre']) . ' <a href="' . $root . '/fiche_actualite?refPar=' . $news['Reference'] . '">../..' . "</a>";
    } else {
        echo my_html($news['Titre']);
    }
    echo '</li>';
}
echo '</ul>';
echo '</div>';
echo '</td>';
echo '<td';
if ($nbAuj || $nbDemain || ($date_mod != '')) echo ' rowspan="2"';
echo '>';
// Affichage des liens sur la page d'accueil
if ((!$SiteGratuit) or ($Premium)) {
    $sql4 = 'SELECT * FROM ' . nom_table('liens') . ' WHERE Sur_Accueil=true ORDER BY Date_Modification DESC LIMIT 3';
    $stmt4 = lect_sql($sql4);
    $links = $stmt4->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($links as $link) {
        echo '<a href="' . $link['URL'] . '">' . my_html($link['description']) . "</a><br>\n";
    }
} */

/** @todo Not the right place */
/* echo '<br><a href="https://forum.geneamania.net/" target="_blank">' . $LG_index_forum . '</a>';
echo '<br><br><img src="' . $root . '/assets/img/' . $Icones['etoile'] . '" alt="' . $LG_star . '" title="' . $LG_star . '"> <a href="https://genealogies.geneamania.net/demande_site.php" target="_blank">' . $LG_index_ask_site . '</a>';
echo '<br><br><a href="https://genealogies.geneamania.net/" target="_blank"><b>GENEAMANIA</b></a>, ' . $LG_index_version . ' ' . $Version;
if ($SiteGratuit) {
    echo '<br><br><a href="http://tech.geneamania.net/Telechargements/Guide_demarrage_rapide_site_heberge_Geneamania.pdf" target="_blank">' . $LG_index_getting_started_hosted . '</a>';
    $lib = ($Premium) ? $LG_index_hosted_premium : $LG_index_hosted_free;
    echo ', ' . $lib;
} */

/** @deprecated Because of windows only. If you want, open it directly from File Explorer.. */
/* if ($is_windows) {
        echo '<br><br><a href="' . $root . '/documentation/Guide_demarrage_rapide_Geneamania_Windows.pdf" target="_blank">' . $LG_index_getting_started_Windows . '</a>';
    } */
echo '</td>';
echo '</tr>';
// Affichage du nombre d'anniversaires de naissance pour le jour même et le lendemain
// Va-t-on afficher des anniversaires et la date de modif ?
if ($nbAuj or $nbDemain or ($date_mod != '')) {
    echo '<tr><td>';
    if ($nbAuj or $nbDemain) {
        echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . $LG_tip . '" title="' . $LG_tip . '"> <a href="' . $root . '/anniversaires">' . $LG_index_birthdays . '</a>  : ';
        if ($nbAuj != 0) echo $nbAuj . ' ' . $LG_index_today . ' ';
        if (($nbAuj != 0) and ($nbDemain != 0)) echo $LG_and . ' ';
        if ($nbDemain != 0) echo $nbDemain . ' ' . $LG_index_tomorrow . ' ';
    }
    if ($date_mod != '') {
        if ($nbAuj or $nbDemain)
            echo '<br>';
        echo $date_mod;
    }
    echo '</td></tr>';
}
echo '</table>';

/** @todo Not the right place to do that */
/* else {
    if (($vers_fic != $Version) and ($x)) {
        echo '<br>';
        echo '<br><img src="' . $root . '/assets/img/stop.png" alt="Stop"/>' . $LG_index_version_mismatched . ' (' . $vers_fic . ' vs. ' . $Version . '), ' . $LG_index_please_migrate . '<br>';
        echo '<br><a href="' . $root . '/install">' . $LG_index_migrate_here . '</a>'; // pour migrer votre base.';
    }
        if ($vers_fic == $Version) { ..... }
} */


echo '<table width="80%" align="center">';
echo '<tr>';
echo '<td width="30%" align="center" valign="middle">';
/** @todo Not the right place to do that */
/* echo '<img src="' . $root . '/assets/img/' . $Icones['email'] . '" alt="Mail" title="Mail">';
echo ' <a href="mailto:' . cryptmail($Adresse_Mail) . '">' . str_replace('@', '-AT-', $Adresse_Mail) . '</a>'; */
echo '</td>';

echo '<td width="30%" align="center" valign="middle">';

// Affichage du formulaire de saisie de code utilisateur ; uniquement sur internet
if ($Environnement !== 'I') {
    echo '<form id="saisie" method="post">';
    echo '<input type="hidden" name="motPasse" value=""/>';
    echo '<table class="tab_bord_gauche_droite" align="center">';
    echo '<tr align="center"><td>' . $LG_index_connexion . '</td></tr>';
    /* if (! $verrou) { .... } */
    // On propose la connexion si on n'a pas de message d'erreur et si la personne n'est pas connectée
    if ($_SESSION['nomUtilisateur'] == $util_defaut) {
        echo '<tr><td><input type="text" name="NomU" value="utilisateur"
					onfocus="javascript:this.value=\'\';" /></td></tr>';
        echo '<tr><td><input type="text" name="LeMot" id="LeMot" value="' . $LG_index_password . '"
					onfocus="javascript:if(this.getAttribute(\'type\') == \'text\') { this.value=\'\'; this.setAttribute(\'type\', \'password\');}" />';
        echo ' <img src="' . $root . '/assets/img/' . $Icones['oeil'] . '" alt="' . $LG_index_psw_show . '" title="' . $LG_index_psw_show . '" onclick="Toggle();">';
        echo '</td></tr>';
        //	Message d'erreur
        if ($mesErreur != '') echo '<p style="color: #FF0000;font-weight: bold;">' . $mesErreur . '</font><br>';
        // On a droit à 5 échecs sinon on n'affiche plus le bouton OK ; lutte contre le bruteforce
        if ($_SESSION['tentatives'] <= $max_tentatives) {
            echo '<tr align="center"><td colspan="2">';
            echo '<input type="submit" name="ok" value="' . $lib_Connecter . '" style="background:url(' . $root . '/assets/img/' . $Icones['connecter'] . ') no-repeat;padding-left:18px;"
					 onclick="return avantEnvoiIndex(this.form);" />';
            if ($SiteGratuit)
                echo ' <a href="' . $root . '/aide_mdp" target="_blank">' . $LG_index_psw_forgoten . ' ?</a> ';
            echo '</td></tr>';
        }
    } else { // L'utilisateur est connecté
        echo '<tr align="center"><td><i>' . $LG_index_connected_user . ' ' . $_SESSION['nomUtilisateur'] . ' ' . $LG_index_connected_level . ' ' . libelleNiveau($_SESSION['niveau']) . '</i></td></tr>';
        echo '<tr align="center"><td>';
        echo '<input type="submit" name="sortir" value="' . $lib_Deconnecter . '" style="background:url(' . $root . '/assets/img/' . $Icones['deconnecter'] . ') no-repeat;padding-left:18px;" />';
        echo '</td></tr>';
    }
    /* } else { // Site verrouillé
        echo '<tr align="center"><td colspan="2"><font color="red" size="+2"><br>' . $LG_index_contact_support . '</font></td></tr>';
    } */
    echo '</table>';
    echo '</form>';
}

/** @deprecated GeneGraphe file cannot be moved, which causes security issues. It will no longer be distributed with the application. */
/* else { // En local, on offre la possibilité d'appeler GénéGraphe si celui-ci est présent sur le poste de travail
        if (file_exists(__DIR__ . '/app/ressources/GeneGraphe.jar')) {
            echo '<form id="f1" method="post">';
            echo '<input type="hidden" name="geneGraphe" value="exec"/>';
            echo '<img src="' . $root . '/assets/img/' . $Icones['GeneGraphe'] . '" alt="' . $LG_index_info_genegraphe . '" title="' . $LG_index_info_genegraphe . '"' .
                ' onclick="javascript:document.forms[\'f1\'].submit();"/> ';
            echo ' ' . Affiche_Icone_Lien('href="' . $root . '/documentation/index" target="_blank"', 'help', $LG_index_doc_genegraphe);
            echo '</form>';
        }
    } */

echo '</td>';
echo '<td align="center" valign="middle">';
/** @todo Not the right place to do that */
/* echo  $LG_help . ' Généamania ' . Affiche_Icone_Lien('href="' . $root . '/aide_geneamania"', 'help', $LG_help . ' Généamania'); */
echo '</td></tr>';

if ($SiteGratuit) {
    echo '<tr><td colspan="3" align="center"><i>' . $LG_index_responsability . '</i></td></tr>';
}

echo '</table>';

// On fait un RAZ de la mémorisation des pages lorsque l'on est sur l'accueil
if (isset($_SESSION['pages'])) unset($_SESSION['pages']);
$_SESSION['pages'][] = $_SERVER['REQUEST_URI'];

include(__DIR__ . '/../public/assets/js/ctrlMotPasse.js');
?>
</body>

</html>