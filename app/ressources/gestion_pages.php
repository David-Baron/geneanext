<?php

// Gestion de l'empilage des pages + entete + javascript

// Entête de document des pages
function Ecrit_Entete_Page($titre, $contenu, $mots, $index_follow = 'IF')
{
    global $langue_min;
    echo '<!DOCTYPE html>';
    echo '<html lang="' . $langue_min . '">';
    echo '<head>';
    Ecrit_Meta($titre, $contenu, $mots, $index_follow);
}


// L'accès est-il le fait d'un robot ?
$is_bot =  (
    isset($_SERVER['HTTP_USER_AGENT'])
    && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
);

// Initialisations
if (!isset($titre)) $titre = '';
if (!isset($contenu)) $contenu = $titre;
if (!isset($mots)) $mots = '';
if (!isset($acces)) $acces = 'L';
if (!isset($_SESSION['sens'])) $_SESSION['sens'] = '>';
if (!isset($index_follow)) $index_follow = 'IF';                // Index, follow par défaut
if ($acces == 'L') $lect = true;
else $lect = false;
$lect = ($acces == 'L' ? true : false);
$maj  = ($acces == 'M' ? true : false);
$courante = $_SERVER['REQUEST_URI'];

if ($maj) {
    // En modification, on supprime la dernière personne de la page, qui permet d'optimiser certaines pages
    if (isset($_SESSION['dern_pers'])) unset($_SESSION['dern_pers']);
}

// Contrôles pour le cross sites
$self  = my_self();
if ($self[strlen($self) - 1] == '/') $self = substr($self, 0, strlen($self) - 1); // Au cas où l'utilsateur mettrait un / en dernière position on le supprime...
$deb_self = substr($self, 0, strrpos($self, '/'));
if (!isset($_SESSION['deb_site'])) $_SESSION['deb_site'] = '';

if (isset($Environnement)) {
    if ($Environnement == 'I') {
        // Si ce n'est pas bon
        if ($deb_self != $_SESSION['deb_site']) {
            // on fait un RAZ des informations de connexion
            $_SESSION['estInvite'] = false;
            $_SESSION['estPrivilegie'] = false;
            $_SESSION['estContributeur'] = false;
            $_SESSION['estGestionnaire'] = false;
            $_SESSION['estCnx'] = false;
            $_SESSION['niveau'] = 'I';
            // et des infos sur les personnes mémorisées
            if (isset($_SESSION['mem_pers'])) unset($_SESSION['mem_pers']);
        }
    }
    $_SESSION['deb_site'] = $deb_self;

    // Appel du contrôle générique
    if (!isset($niv_requis)) {
        // Par défaut : Invité
        $niv_requis = 'I';
        // Par défaut pour les pages en mise à jour : contributeur
        if ($maj) $niv_requis = 'C';
    }

    if (controle_utilisateur($niv_requis) == 0) {
        header('Location: Erreur_Profil.php');
        // RAZ boutons pour interdire les mises à jours dans certains contextes
        $ok = '';
        $supprimer = '';
        exit;
    }

    // Intégration des fonctions spécifiques de mise à jour
    if ($maj) require(__DIR__ . '/fonctions_maj.php');

    // Initialisation des boutons
    $bt_OK        = false;
    $bt_An        = false;
    $bt_Sup       = false;
    $bt_Sup_An    = false;
    $clic_boutons = false;

    // Quel bouton ?
    if ((isset($ok)) and ($ok == $lib_Okay)) $ok = 'OK';
    if ((isset($ok)) and ($ok == 'OK')) $bt_OK = true;
    if ((isset($annuler)) and ($annuler == $lib_Annuler)) $bt_An = true;
    if ((isset($supprimer)) and ($supprimer == $lib_Supprimer)) $bt_Sup = true;
    if ((isset($supprimer)) and ($supprimer == '-')) $bt_Sup_An = true;

    if (($bt_OK) or ($bt_An) or ($bt_Sup)) $clic_boutons = true;

    // Sur les pages avec mise à jour on ajoute la gestion de l'appel du javascript
    if ($maj) {
        // Pas de javascript en mise à jour !
        if ($clic_boutons) {
            $avec_js = false;
        }
        // Javascript sur mise à jour puis empilage de la page précédente
        else {
            $avec_js = true;
            Ecrit_Entete_Page($titre, $contenu, $mots, $index_follow);
            include(__DIR__ . '/../../assets/js/edition_geneamania.js');
        }
    }

    // Sur les pages de lecture, on écrit juste l'entête ; et même rien si on n'a pu accéder aux données
    if ($lect) {
        $enreg_sel = false;
        if (!$clic_boutons) {
            if (!isset($no_entete)) {
                if (isset($req_sel)) {
                    $res_sel = lect_sql($req_sel);
                    $enreg_sel = $res_sel->fetch(PDO::FETCH_ASSOC);
                } else $enreg_sel = true;
                if ($enreg_sel) {
                    Ecrit_Entete_Page($titre, $contenu, $mots, $index_follow);
                }
            }
        }
    }

    $memo_page = true;
    if (isset($not_memo))
        $memo_page = false;
    if ($memo_page) {
        $courante = $_SERVER['REQUEST_URI'];
        if (!isset($_SESSION['pages'])) $_SESSION['pages'][] = 'index.php';
        $ind_max = count($_SESSION['pages']) - 1;
        $precedente = $_SESSION['pages'][$ind_max];
        if ($courante != $precedente) $_SESSION['pages'][] = $courante;
    }

    if ($debug) {
        fputs($f_log, $dh . ' ===== après');
        fputs($f_log, ' Pages mémorisées');
        for ($nb = 0; $nb < count($_SESSION['pages']); $nb++) {
            fputs($f_log, '   ' . $_SESSION['pages'][$nb]);
        }
        fputs($f_log, ' Self ' . my_self());
        fputs($f_log, ' Page courante ' . $courante);
        fputs($f_log, ' Page précédente ' . $precedente);
        fclose($f_log);
    }
}
// Valeurs par défaut si non définies
if (!isset($_SESSION['estInvite'])) $_SESSION['estInvite'] = false;
if (!isset($_SESSION['estPrivilegie'])) $_SESSION['estPrivilegie'] = false;
if (!isset($_SESSION['estContributeur'])) $_SESSION['estContributeur'] = false;
if (!isset($_SESSION['estGestionnaire'])) $_SESSION['estGestionnaire'] = false;
if (!isset($_SESSION['estCnx'])) $_SESSION['estCnx'] = false;
// Positionnement des droits
$est_invite       = ($_SESSION['estInvite'] === true ? true : false);
$est_privilegie   = ($_SESSION['estPrivilegie'] === true ? true : false);
$est_contributeur = ($_SESSION['estContributeur'] === true ? true : false);
$est_gestionnaire = ($_SESSION['estGestionnaire'] === true ? true : false);
$est_cnx          = ($_SESSION['estCnx'] === true ? true : false);
