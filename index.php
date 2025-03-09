<?php

//=====================================================================
// 
//=====================================================================

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

session_start();
include('fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('NomU', 'motPasse', 'geneGraphe', 'ok', 'sortir');
foreach ($tab_variables as $nom_variables) {
    //	lecture des variables $_POST avec sécurité
    if (isset($_POST[$nom_variables])) $$nom_variables = trim(htmlspecialchars(addslashes($_POST[$nom_variables]), ENT_QUOTES, $def_enc));
    else $$nom_variables = '';
}

// Nombre maximum de tentatives de connexions successives
$max_tentatives = 5;

// Code utilisateur par défaut
$util_defaut = 'Anonyme';

$is_windows = (substr(php_uname(), 0, 7) == "Windows") ? true : false;

// Demande de lancement de GénéGraphe
if ($geneGraphe == 'exec') {
    $cmd = 'GeneGraphe.jar';
    if ($is_windows) {
        pclose(popen("start /B " . $cmd, "r"));
    } else {
        $cmd = getcwd() . "/" . $cmd;
        exec("DISPLAY=:0.0 " . $cmd . " > /dev/null &");
    }
}

$_SESSION['sens'] = '>';

echo '<!DOCTYPE html>';
// echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html lang="' . $langue_min . '">';
echo '<head>' . "\n";

// On vide l'empilement des pages
if (isset($_SESSION['pages'])) unset($_SESSION['pages']);

// Mémorisation des fiches personnes visualisées ==> initialisation
if (!isset($_SESSION['mem_pers'])) {
    for ($nb = 0; $nb < 3; $nb++) {
        $_SESSION['mem_pers'][$nb] = 0;
        $_SESSION['mem_nom'][$nb] = '-';
        $_SESSION['mem_prenoms'][$nb] = '-';
    }
}

// Possibilité d'afficher un message de maintenance sur présence d'un fichier
$maintenance = false;
if (file_exists('maintenance.php')) $maintenance = true;

// Possibilité de bloquer la connexion ==> verrouillage du site
$verrou = false;
if (file_exists('verrou.php')) $verrou = true;

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

$x = Lit_Env();
$id_cnx = $x;

if ($Environnement == 'L') $RepGenSite = $RepGenSiteLoc;
else                       $RepGenSite = $RepGenSiteInt;
//$Environnement = 'I';

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

controle_utilisateur('I');

if (!isset($_SESSION['estCnx'])) $_SESSION['estCnx'] = false;
$est_cnx = ($_SESSION['estCnx'] === true ? true : false);


// L'utilisateur se déconnecte, on ré-initalise les droits
if ($sortir == $lib_Deconnecter) Init_infos_cnx();

$self  = my_self();
//echo 'Self : '.$self.'<br>';

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

// Utilisateur par défaut si non loggué
if (!isset($_SESSION['nomUtilisateur'])) $_SESSION['nomUtilisateur'] = $util_defaut;

Ecrit_Meta($LG_index_title . ' ' . $Nom, $LG_index_desc . ' ' . $Nom, '');
echo '</head>' . "\n";

// Affichage de l'image de fond
Ligne_Body(false);

// Informations
echo '<!-- Environnement : ' . $Environnement . ' -->' . "\n";
echo '<!-- Préfixe : ';
if ($pref_tables != '') echo $pref_tables;
else                    echo 'aucun - none';
echo ' -->' . "\n";
if ($SiteGratuit) {
    echo '<!-- Site gratuit -->' . "\n";
    if ($Premium) echo '<!-- Premium -->' . "\n";
}

// echo '<p><img style="vertical-align:middle" src="'.$chemin_images.$Lettre_B.'" alt="" /> Texte &agrave; aligner</p>';
// echo '<p><img class="img-b" src="'.$chemin_images.$Lettre_B.'" alt="" /> Texte &agrave; aligner</p>';
echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";
echo '<tr align="center" v-align="middle"><td>' . "\n";

$lib = $LG_index_welcome;
// Affichage de la lettre B si paramétrée en base
if (!$id_cnx) {
    $Lettre_B = '-';
    $Nom = '?';
}
if (substr($Lettre_B, strlen($Lettre_B) - 1) != '-') {
    echo '<img class="img-b" src="' . $Chemin_Lettre . '" width="45" alt="B">';
    $lib = substr($lib, 1);
}
// On distingue car les syntaxes peuvent être différentes ; à gérer au cas par cas
if ($langue == 'FR') {
    echo '<font size="+3"><i>' . my_html($lib) . ' ' . my_html($Nom) . '</i></font>' . "\n";
} else
    echo '<font size="+3">' . my_html($lib) . ' <i>' . my_html($Nom) . '</i></font>' . "\n";

// Contrôle de la présence du nom du site
if ($_SESSION['estGestionnaire']) {
    if (($Nom == '???') or ($Nom == ''))
        echo '<br>' . Affiche_Icone('tip', $LG_tip) . ' &nbsp;<font color="red" size="+2">' . $LG_index_tip_no_param . '</font>' . "\n";
    echo '<br>' . "\n";
}

if ($maintenance) {
    echo '<br><br><br><font color="red" size="+2"><br>' . my_html($LG_index_tip_maintenance) . '...</font><br><br><br>';
}
echo '</td></tr>' . "\n";
echo '</table>' . "\n";
//echo '<br>'."\n";

//echo '<a href="test_images.php">test_images</a><<br>>';
//echo '<a href="Demarrage_Rapide.php">Demarrage_Rapide</a>';

// Menus...
if (($vers_fic == $Version) and (!$maintenance)  and (!$verrou)) {

    $Existe_Commentaire = false;
    $Presence_Commentaire = Rech_Commentaire(0, 'G');
    if (($Presence_Commentaire) and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
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

    if (!$deja_acces_anniv) {
        if (!$_SESSION['estPrivilegie']) $ajout = ' and Diff_Internet = \'O\'';
        $sql = 'SELECT count(*),\'A\' FROM ' . $n_personnes . ' where Ne_le like \'____' . $xMoisA . $xJourA . '_L\'' . $ajout .
            ' union ' .
            'SELECT count(*),\'D\' FROM ' . $n_personnes . ' where Ne_le like \'____' . $xMoisD . $xJourD . '_L\'' . $ajout;
        if ($res = lect_sql($sql)) {
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
    } else {
        $nbAuj = $_SESSION['AnnivA'];
        $nbDemain = $_SESSION['AnnivD'];
    }

    //echo '<br>';

    echo '<form method="post" action="">';
    echo '<div class="exemple" id="ex2">';
    echo '<ul class="nav">';
    echo '<li><a href="Liste_Pers.php?Type_Liste=P">' . $LG_index_menu_pers . '</a></li>';
    echo '<li><a href="Liste_NomFam.php">' . $LG_index_menu_names . '</a></li>';
    echo '<li><a href="Liste_Villes.php?Type_Liste=V">' . $LG_index_menu_towns . '</a></li>';
    echo '<li><a href="Stat_Base.php">' . $LG_Menu_Title['Statistics'] . '</a></li>';
    echo '<li>&nbsp;';
    aff_menu('D', $_SESSION['niveau'], false);
    echo '</li>';
    echo '</ul>';
    echo '</div>';
    echo '</form>' . "\n";

    $star = Affiche_Icone('etoile', $LG_star);

    //$Base_Vide = true;
    if (!$Base_Vide) {
        echo '<table width="60%" align="center" border="0">';
        //echo '<table width="80%" align="center" border="0" class="tab_bord_bas">';
        echo '<tr><td><fieldset style="width:90%;"><legend>' . $LG_index_quick_search . '&nbsp;' . Affiche_Icone('help', $LG_index_tip_search) . '</legend>';
        echo '<table align="center" border="0">';
        echo '<tr><td>';
        echo '<fieldset><legend>' . $LG_index_menu_pers . '</legend>';
        echo '<form method="post" action="Recherche_Personne.php" >' . "\n";
        echo '<table border="0">';
        echo '<tr><td>' . LG_PERS_NAME . '&nbsp;:</td><td><input type="text" size="30" name="NomP"/></td>';
        echo '<td rowspan="2" valign="middle"><input type="submit" name="ok" value="' . $lib_Rechercher . '" style="background:url(' . $chemin_images_icones . $Icones['chercher'] . ') no-repeat;padding-left:18px;" /></td></tr>';
        echo '<tr><td>' . LG_PERS_FIRST_NAME . '&nbsp;:</td><td><input type="text" size="30" name="Prenoms"/></td></tr>';
        echo '</table>';
        echo '<input type="hidden" name="Horigine" value="index.php">';
        echo '<input type="hidden" name="Sortie" value="e">';
        echo '<input type="hidden" name="Son" value="o">' . "\n";
        echo '</form>' . "\n";
        echo '</fieldset>';
        echo '</td>';
        echo '<td valign="middle">';
        echo '<fieldset><legend>' . $LG_index_menu_towns . '</legend>';
        echo '<form method="post" action="Recherche_Ville.php" >' . "\n";
        echo '<input type="text" size="30" name="NomV"/>' . "\n";
        echo '<input type="hidden" name="Horigine" value="index.php">';
        echo '<input type="hidden" name="Sortie" value="e">';
        echo '<input type="hidden" name="Code_Postal" value="">';
        echo '<input type="hidden" name="Departement" value="-1">';
        echo '<input type="submit" name="ok" value="' . $lib_Rechercher . '" style="background:url(' . $chemin_images_icones . $Icones['chercher'] . ') no-repeat;padding-left:18px;" />';
        echo '</form>' . "\n";
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
        echo '</fieldset></td></tr></table>';
    } else {
        // Affichage lien vers le noyau
        // Pour un gestionnaire
        if ($_SESSION['estGestionnaire']) {
            echo '<table width="60%" align="center" border="0"><tr align="center"><td>';
            echo '<br><a href="Noyau_Pers.php">' . $LG_Menu_Title['Decujus_And_Family'] . '</a><br><br>';
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
                echo '<td valign="middle">&nbsp;<br>' . $Commentaire . '<br>&nbsp;</td>';
            else
                echo '<td valign="middle">' . $Commentaire . '</td>';
        }
        if ($Existe_Image_Gen) {
            echo '<td width="50%" valign="middle" align="center">';
            Aff_Img_Redim_Lien($chemin_images_util . $Image_Index, 190, 190, 'image_gen');
            echo '</td>';
        }
        echo '</tr></table>' . "\n";
    }
    $date_mod = '';
    if ($Modif_Site != '0000-00-00 00:00:00') {
        $date_mod = my_html($LG_index_last_update) . ' ' . DateTime_Fr($Modif_Site);
    }

    // Affichage des actualités
    // On va chercher en base les actualités ; pour les sites gratuits non Premium, les actualités sont centralisées (préfixe spécial)
    $memo_pref = $pref_tables;
    if (($SiteGratuit) and (!$Premium)) $pref_tables = 'gra_sg_';
    $requete = 'SELECT Reference , Titre, Debut, Fin, Identifiant_zone ' . 'from ' . nom_table('evenements') .
        ' where Code_Type = "' . $TypeEv_actu . '" ORDER BY Reference desc limit 4';
    $pref_tables = $memo_pref;
    $result = lect_sql($requete);
    $nb_actus = $result->rowCount();
    $nb = 0;
    echo '<table width="95%" border="0" cellspacing="1" cellpadding="3" align="center" class="tab_bord_bas">' . "\n";
    echo '<tr>';
    echo '<td width="50%" class="tab_bord_bas"><font size="+1">' . my_html($LG_index_news) . '...</font></td>' . "\n";
    echo '<td width="50%" class="tab_bord_bas"><font size="+1">' . my_html($LG_index_links) . '...</font></td>' . "\n";
    echo '</tr>' . "\n";
    echo '<tr><td>' . "\n";
    echo '<div id="liste">';
    echo '<ul class="puces">';
    // if ($date_mod != '') {
    // echo '<li>'.$date_mod.'</li>';
    // }
    if ($nb_actus > 0) {
        while ($enreg = $result->fetch(PDO::FETCH_NUM)) {
            $nb++;
            if ($nb < 4) {
                $ref   = $enreg[0];
                $titre = $enreg[1];
                $debut = $enreg[2];
                $fin   = $enreg[3];
                //if ($nb > 1) echo '<br>';
                echo '<li>';
                if ($debut != '') {
                    $debut = Etend_2_dates($debut, $fin, true);
                    echo $debut . ' : ';
                }
                if ($enreg[4] != 0) echo my_html($enreg[1]) . '&nbsp;<a href="Fiche_Actualite.php?refPar=' . $ref . '">../..' . "</a></li>\n";
                else echo my_html($enreg[1]) . '</li>';
            }
        }
    }
    echo '</ul>';
    echo '</div>';
    echo '</td>';
    //echo '<td>&nbsp;</td>';
    echo '<td';
    if ($nbAuj or $nbDemain or ($date_mod != ''))
        echo ' rowspan="2"';
    echo '>';
    // Affichage des liens sur la page d'accueil
    if ((!$SiteGratuit) or ($Premium)) {
        $requete = 'SELECT URL, description from ' . nom_table('liens') .
            ' where Sur_Accueil = true ORDER BY Date_Modification desc limit 3';
        $result = lect_sql($requete);
        while ($enreg = $result->fetch(PDO::FETCH_NUM)) {
            echo '<a href="' . $enreg[0] . '">' . my_html($enreg[1]) . "</a><br>\n";
        }
        $result->closeCursor();
    }
    echo '<br><a href="https://forum.geneamania.net/" target="_blank">' . my_html($LG_index_forum) . '</a>' . "\n";
    echo '<br><br>' . $star . '&nbsp;<a href="https://genealogies.geneamania.net/demande_site.php" target="_blank">' . $LG_index_ask_site . '</a>&nbsp;' . $star;
    echo '<br><br><a href="https://genealogies.geneamania.net/" target="_blank"><b>GENEAMANIA</b></a>, ' . $LG_index_version . ' ' . $Version . "\n";
    if ($SiteGratuit) {
        echo '<br><br><a href="http://tech.geneamania.net/Telechargements/Guide_demarrage_rapide_site_heberge_Geneamania.pdf" target="_blank">' . my_html($LG_index_getting_started_hosted) . '</a>' . "\n";
        $lib = $LG_index_hosted_free;
        if ($Premium) $lib = $LG_index_hosted_premium;
        echo ', ' . my_html($lib);
    }
    if ($is_windows) {
        echo '<br><br><a href="Guide_demarrage_rapide_Geneamania_Windows.pdf" target="_blank">' . $LG_index_getting_started_Windows . '</a>' . "\n";
    }
    echo '</td>';
    echo '</tr>';
    // Affichage du nombre d'anniversaires de naissance pour le jour même et le lendemain
    // Va-t-on afficher des anniversaires et la date de modif ?
    if ($nbAuj or $nbDemain or ($date_mod != '')) {
        echo '<tr><td>';
        if ($nbAuj or $nbDemain) {
            echo Affiche_Icone('tip', $LG_tip) . '&nbsp;<a href="' . $RepGenSite . 'Anniversaires.php">' . my_html($LG_index_birthdays) . '</a>  : ';
            if ($nbAuj != 0) echo $nbAuj . ' ' . my_html($LG_index_today) . ' ';
            if (($nbAuj != 0) and ($nbDemain != 0)) echo my_html($LG_and) . ' ';
            if ($nbDemain != 0) echo $nbDemain . ' ' . my_html($LG_index_tomorrow) . ' ';
        }
        if ($date_mod != '') {
            if ($nbAuj or $nbDemain)
                echo '<br>';
            echo $date_mod;
        }
        echo '</td></tr>';
    }
    echo '</table>';
} else {
    if (($vers_fic != $Version) and ($id_cnx)) {
        echo '<br>';
        Affiche_Stop($LG_index_version_mismatched . ' (' . $vers_fic . ' vs. ' . $Version . '), ' . $LG_index_please_migrate);
        echo '<br><a href="install.php">' . my_html($LG_index_migrate_here) . '</a>'; // pour migrer votre base.';
    }
}

// De cujus par défaut
if (!isset($_SESSION['decujus'])) {
    $decujus = -1;
    $_SESSION['decujus'] = $decujus;
    $_SESSION['decujus_defaut'] = 'O';
}

if ($vers_fic == $Version) {
    echo '<table width="80%" align="center">' . "\n";
    echo '<tr>';
    echo '<td width="30%" align="center" valign="middle">' . "\n";
    echo Affiche_Icone('email', 'Mail') . '&nbsp;';
    echo '<a href="mailto:' . cryptmail($Adresse_Mail) . '">' . str_replace('@', '-AT-', $Adresse_Mail) . '</a>' . "\n";
    echo Affiche_Icone('email', 'Mail');
    echo '</td>' . "\n";

    echo '<td width="30%" align="center" valign="middle">' . "\n";

    // Affichage du formulaire de saisie de code utilisateur ; uniquement sur internet
    // if (true == true) {
    if ($Environnement == 'I') {
        echo '<form id="saisie" method="post" action="' . $self . '" >' . "\n";
        echo '<input type="hidden" name="motPasse" value=""/>' . "\n";
        echo '<table class="tab_bord_gauche_droite" align="center">';
        echo '<tr align="center"><td>' . my_html($LG_index_connexion) . '</td></tr>';
        if (! $verrou) {
            // On propose la connexion si on n'a pas de message d'erreur et si la personne n'est pas connectée
            if ($_SESSION['nomUtilisateur'] == $util_defaut) {
                echo '<tr><td><input type="text" name="NomU" value="utilisateur"
					onfocus="javascript:this.value=\'\';" /></td></tr>' . "\n";
                echo '<tr><td><input type="text" name="LeMot" id="LeMot" value="' . $LG_index_password . '"
					onfocus="javascript:if(this.getAttribute(\'type\') == \'text\') { this.value=\'\'; this.setAttribute(\'type\', \'password\');}" />';
                echo '&nbsp;' . Affiche_Icone_Clic('oeil', 'Toggle();', $LG_index_psw_show) . "\n";
                echo '</td></tr>' . "\n";
                //	Message d'erreur
                if ($mesErreur != '') echo '<p style="color: #FF0000;font-weight: bold;">' . $mesErreur . '</font><br>' . "\n";
                // On a droit à 5 échecs sinon on n'affiche plus le bouton OK ; lutte contre le bruteforce
                // $max_tentatives = 99999;
                if ($_SESSION['tentatives'] <= $max_tentatives) {
                    echo '<tr align="center"><td colspan="2">';
                    echo '<input type="submit" name="ok" value="' . $lib_Connecter . '" style="background:url(' . $chemin_images_icones . $Icones['connecter'] . ') no-repeat;padding-left:18px;"
					 onclick="return avantEnvoiIndex(this.form);" />';
                    if ($SiteGratuit)
                        echo '&nbsp;<a href="aide_mdp.php" target="_blank">' . my_html($LG_index_psw_forgoten) . '&nbsp;?</a>&nbsp;';
                    echo '</td></tr>';
                }
            }
            // L'utilisateur est connecté
            else {
                echo '<tr align="center"><td><i>' . $LG_index_connected_user . ' ' . $_SESSION['nomUtilisateur'] . ' ' . $LG_index_connected_level . ' ' . libelleNiveau($_SESSION['niveau']) . '</i></td></tr>';
                //echo '<tr align="center"><td><input type="submit" name="sortir" value="'.$val_sortir.'"/></td></tr>';
                echo '<tr align="center"><td>';
                echo '<input type="submit" name="sortir" value="' . $lib_Deconnecter . '" style="background:url(' . $chemin_images_icones . $Icones['deconnecter'] . ') no-repeat;padding-left:18px;" />';
                echo '</td></tr>';
            }
        }
        // Site verrouillé
        else {
            echo '<tr align="center"><td colspan="2"><font color="red" size="+2"><br>' . my_html($LG_index_contact_support) . '</font></td></tr>';
        }
        echo '</table>';
        echo '</form>' . "\n";
    }
    // En local, on offre la possibilité d'appeler GénéGraphe si celui-ci est présent sur le poste de travail
    else {
        if (file_exists('GeneGraphe.jar')) {
            echo '<form id="f1" action="' . $self . '" method="post">' . "\n";
            echo '<input type="hidden" name="geneGraphe" value="exec"/>' . "\n";
            $info = my_html($LG_index_info_genegraphe);
            echo '<img src="' . $chemin_images_icones . $Icones['GeneGraphe'] . '" alt="' . $info . '" title="' . $info . '"' .
                ' onclick="javascript:document.forms[\'f1\'].submit();"/>&nbsp;' . "\n";
            echo '&nbsp;' . Affiche_Icone_Lien('href="' . $RepGenSite . 'documentation/index.php" target="_blank"', 'help', $LG_index_doc_genegraphe);
            echo '</form>' . "\n";
        }
    }

    echo '</td>' . "\n";
    echo '<td align="center" valign="middle">' . my_html($LG_help) . ' G&eacute;n&eacute;amania&nbsp;' . "\n";
    echo Affiche_Icone_Lien('href="' . $RepGenSite . 'Aide_Geneamania.php"', 'help', $LG_help . ' Généamania');
    echo '</td></tr>' . "\n";

    if ($SiteGratuit)
        echo '<tr><td colspan="3" align="center"><i>' . my_html($LG_index_responsability) . '</i></td></tr>' . "\n";

    echo '</table>' . "\n";
}

// On fait un RAZ de la mémorisation des pages lorsque l'on est sur l'accueil
if (isset($_SESSION['pages'])) unset($_SESSION['pages']);
$_SESSION['pages'][] = $_SERVER['REQUEST_URI'];

/*
echo 'Pages mémo : '.count($_SESSION['pages']).'<br>';
for ($nb=0;$nb<count($_SESSION['pages']);$nb++) echo 'Page '.$nb.' : '.$_SESSION['pages'][$nb]."<br>\n";
*/

include 'jscripts/ctrlMotPasse.js';
?>
</body>

</html>