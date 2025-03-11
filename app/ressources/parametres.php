<?php

// Carriage return
// $cr = "\r\n"; // version Windows
$cr = "\n";   // version Unix
//$cr = "\r";   // version Macintosh

// Est-on sur un site gratuit ?
// ==> on est sur genealogies.geneamania.net
// ==> présence d'un fichier site_gratuit.txt
$SiteGratuit = false;
if (strpos($_SERVER['SERVER_NAME'], '.geneamania.net') !== false) $SiteGratuit = true;
else {
    if (file_exists(__DIR__ . '/../../site_gratuit.txt')) $SiteGratuit = true;
}

// Est-on sur un site de type premium ==> présence d'un fichier premium.txt
$Premium = false;
if (file_exists(__DIR__ . '/../../premium.txt')) $Premium = true;

// Taille maxi d'une image à télécharger
$taille_maxi_image = 150 * 1024; // 150Ko

// Hauteur, largeur, poids pour la version standard
$taille_maxi_images['w'] = 800;
$taille_maxi_images['h'] = 600;
$taille_maxi_images['s'] = 150 * 1024;

// Pour la version Premium
if (($Premium) or (!$SiteGratuit)) {
    $taille_maxi_images['w'] *= 1.5;
    $taille_maxi_images['h'] *= 1.5;
    $taille_maxi_images['s'] *= 4;
}

// Définition du temps maxi d'exécution pour le net (certains scripts seulement)
$lim_temps = 60;
if ($Premium) $lim_temps *= 2;

// Paramétrage du répertoire de stockage de TinyMCE à des fins de mutualisation
$rep_Tiny = 'assets/js/tiny_mce/';

// Paramétrage du répertoire de stockage des sources
$rep_src = '';

// Chemins des images
$chemin_images_util = 'Images_Util/';
$chemin_images      = 'assets/img/';
$chemin_images_a_asc = $chemin_images . 'arbres_asc/';
$chemin_images_lettres = $chemin_images . 'lettres/';
$chemin_images_barres = $chemin_images . 'fonds_barre/';
$chemin_images_icones = $chemin_images;

// Chemin des Gedcom
$chemin_Gedcom = 'Gedcom/';

// Chemin des exports fichiers
$chemin_exports = 'exports/';

// Chemin des contributions
$chemin_contributions = 'contributions/';      // Attention, le chemin est aussi paramétré dans la page d'install

// Chemin des documents
$chemin_docs_HTM = 'documents/HTM/';
$chemin_docs_PDF = 'documents/PDF/';
$chemin_docs_IMG = 'documents/IMG/';
$chemin_docs_TXT = 'documents/TXT/';

// Chemin du site
// Ajout automatique de sous-répertoire en local
$ajout = '';
$self  = my_self();

if ($self[strlen($self) - 1] == '/') $self = substr($self, 0, strlen($self) - 1); // Au cas où l'utilsateur mettrait un / en dernière position on le supprime...
$dpos  = strrpos($self, '/');
if ($dpos !== false) {
    if ($dpos > 0) {
        $ajout = substr($self, 1, $dpos);
    }
}
$RepGenSiteLoc = $ajout;
// $RepGenSiteLoc = 'http://localhost/'.$ajout;	// Ne fonctionne pas avec les virtual host locaux TODO: lol so it's useless.
$RepGenSiteInt = '';

// Nombre de générations maximum à remonter pour la recherche de cousinage
// sur internet ou en local
$max_gen_int = 5;
$max_gen_loc = 10;

// Calcul du nombre maximum de générations à explorer pour limiter les problèmes de mémoire
$max_gen_AD_loc = 20;
if (($SiteGratuit) and (!$Premium)) $max_gen_AD_int = $max_gen_int * 2;
else $max_gen_AD_int = $max_gen_int * 3;

// Police pour les graphiques
$FontLoc = 'C:/Windows/Fonts/Arial.ttf';
$FontInt = './arial.ttf';

// Insertion ou non d'un enregistrement dans la table des compteurs
// pour les sites ne disposant pas de statistiques ; valeur : 0/1
$Insert_Compteur = 0;

// Fichier d'export GenWeb
$nom_fic_GenWeb = 'export_GenWeb#.txt';

// Affichage des requêtes ==> mode debug
$aff_req = false;
$debug = false;

// Préfixe des tables (stocké dans un fichier à part pour les sites gratuits)
$pref_tables = '';

// Fichier des icones
$fic_icones = 'Icones.php';

// Couleur de fond de zone saisissable obligatoire
$style_z_oblig = ' style="background-color:#FEFFBC" ';

// Nombre de postes à mémoriser pour les personnes modifiées / créées
$postes_memo_pers = 3;

// Nombre de noms à afficher dans la liste des noms les plus portés
$nb_noms = 15;
if ($SiteGratuit and !$Premium) $nb_noms = 10;

// Nombre de noms à afficher dans la liste des noms les plus portés
$nb_prof = 15;
if ($SiteGratuit and !$Premium) $nb_prof = 10;

// Nombre de dernières personnes modifiées à afficher
$nb_pers_mod = 15;
if ($SiteGratuit and !$Premium) $nb_pers_mod = 10;

// Séparateur
$separ = '|||';

// Localisation par défaut pour la date
date_default_timezone_set("Europe/Paris");

// Date limite de personne vivante
// Calcul de la date du jour - 130 ans (on suppose que 130 ans est la durée de vie maximum d'un être humain)
$annees_maxi_vivant = 130;
$A = date('Y') - $annees_maxi_vivant;
$M = date('m');
$J = date('d');
$xA = str_pad($A, 4, '0', STR_PAD_LEFT);
$xM = str_pad($M, 2, '0', STR_PAD_LEFT);
$xJ = str_pad($J, 2, '0', STR_PAD_LEFT);
$date_lim_vivant = $xA . $xM . $xJ;

// Limite de la diffusabilité exprimée en années
$Lim_Diffu = 100;

$Lim_Diffu_Dec = $Lim_Diffu + $annees_maxi_vivant;

// Type d'évènement des actualités
$TypeEv_actu = 'AC3U';

$FromTo_Mail = 'support@geneamania.net';
$FromTo_Texte = 'Support Geneamania';

// Encodage par défaut pour htmlentities
$def_enc = 'UTF-8'; // UTF-8|ISO-8859-1

// Polices intallées par défaut pour les pdf
$list_font_pdf = array('Courier', 'Helvetica', 'Arial', 'Times');
$list_font_pdf_plus = array();
# $coul_pdf = '#4F95B0';

// Proposition pour les graphismes pré-définis
$dominantes = array('bleu', 'rouge', 'marron', 'vert', 'violet', 'gris');
$ch_barre = $chemin_images . 'fonds_barre/';
$ch_fond = $chemin_images . 'fonds/';
$ch_lettre = $chemin_images . 'lettres/';
// Base bleue
$dominante = 'bleu';
$gra_description[$dominante] = 'Bleu, fond blanc';
$gra_coul_bord[$dominante] = '#6699FF';
$gra_coul_lib[$dominante] = '#94B8FF';
$gra_coul_val[$dominante] = '#B2CCFF';
$gra_barre[$dominante] = $ch_barre . 'bar_off_bleu.gif';
$gra_fond[$dominante] = '';
$gra_lettre[$dominante] = $ch_lettre . 'B_bleu.gif';
// Avec $coul_lib_bleu = '#ABC8E2';, on codera ${'coul_lib_'.$dominante} pour accéder au contenu ; plus compliqué que les arrays...
// Base rouge et calque
$dominante = 'rouge';
$gra_description[$dominante] = 'Rouge, fond calque';
$gra_coul_bord[$dominante] = '#CC0000';
$gra_coul_lib[$dominante] = '#FFB6C1';    //#D63333
$gra_coul_val[$dominante] = '#FF8B8B';    //#E06666
$gra_barre[$dominante] = $ch_barre . 'bar_off_rouge.gif';
$gra_fond[$dominante] = $ch_fond . 'calque.jpg';
$gra_lettre[$dominante] = $ch_lettre . 'B_rouge.gif';
// Base marron et parchemin
$dominante = 'marron';
$gra_description[$dominante] = 'Marron, fond parchemin';
$gra_coul_bord[$dominante] = '#A6915B';
$gra_coul_lib[$dominante] = '#B8A165';
$gra_coul_val[$dominante] = '#F6E497';
$gra_barre[$dominante] = $ch_barre . 'bar_off_bistre.gif';
$gra_fond[$dominante] = $ch_fond . 'parchemin2.jpg';
$gra_lettre[$dominante] = $ch_lettre . 'LettreB41.jpg';
// Base vert et cuir
$dominante = 'vert';
$gra_description[$dominante] = 'Vert, fond cuir';
$gra_coul_bord[$dominante] = '#2EB82E';
$gra_coul_lib[$dominante] = '#47D147';
$gra_coul_val[$dominante] = '#70DB70';
$gra_barre[$dominante] = $ch_barre . 'bar_off_vert_fonce.gif';
$gra_fond[$dominante] = $ch_fond . 'cuir_vert.jpg';
$gra_lettre[$dominante] = $ch_lettre . 'B01.gif';
// Base violet et fond coton
$dominante = 'violet';
$gra_description[$dominante] = 'Violet, fond cotton';
$gra_coul_bord[$dominante] = '#D119A3';
$gra_coul_lib[$dominante] = '#DA70D6';
$gra_coul_val[$dominante] = '#EFAEDF';  //EE82EE';
$gra_barre[$dominante] = $ch_barre . 'bar_off_violet.gif';
$gra_fond[$dominante] = $ch_fond . 'coton.jpg';
$gra_lettre[$dominante] = $ch_lettre . 'B_pastel.gif';
// Base grise et pierre
$dominante = 'gris';
$gra_description[$dominante] = 'Gris, fond pierre';
$gra_coul_bord[$dominante] = '#FFFFFF';
$gra_coul_lib[$dominante] = '#CCCCCC';
$gra_coul_val[$dominante] = '#F0F0F0';
$gra_barre[$dominante] = $ch_barre . 'bar_off_gris.gif';
$gra_fond[$dominante] = $ch_fond . 'pierre02.jpg';
$gra_lettre[$dominante] = $ch_lettre . 'B_gris.gif';

//Couleur de fond des calculatrices
$fond_calc = '#DCDCDC';

// Paramétrage de l'affichage des dates révolutionnaires
// (I)cone, (P)arenthèse, (N)on
$aff_rev = 'I';

$adr_rech_gratuits = 'https://genealogies.geneamania.net/recherche_heberges.php';
$adr_rech_gratuits_ville = 'https://genealogies.geneamania.net/recherche_heberges_ville.php';
// $adr_rech_gratuits ='http://www.geneamania.net/gratuits_recherche_nom.php';
$adr_rech_ville_ref = 'https://genealogies.geneamania.net/recherche_ville_geneamania.php';

// Affichage des notes "à l'ancienne mode"
$aff_note_old = false;

$barre_homme = $chemin_images . 'bb3.jpg';
$barre_femme = $chemin_images . 'br2.jpg';

$rupt_Fiche_Indiv = '';

// Répertoire des pages d'information
//$rep_Infos = 'pages_info/';
//$offset_info = '../';

// Répertoire des langues
$rep_lang = 'languages'; // TODO: not a language rep but tranlations rep lol

// Date de naissance par défaut pour l'export de la recherche des décès
$death_def_min_year = 1970 - $annees_maxi_vivant;
$url_matchid = 'https://deces.matchid.io';
$url_matchid_link = $url_matchid . '/link';
$url_matchid_sch = $url_matchid . '/deces/api/v1/search';

// Paramétrages particuliers
if (file_exists(__DIR__ . '/../../param_part.php')) include(__DIR__ . '/../../param_part.php');
if ($SiteGratuit) {
    if (file_exists(__DIR__ . '/../../param_gratuit.php')) include(__DIR__ . '/../../param_gratuit.php');
}

if ($debug) $f_log = open_log();

$hidden = (!$debug ? 'hidden' : 'text');

if (!isset($mod_nom_fic)) $mod_nom_fic = '';
if (!isset($n_sv_img_gd)) $n_sv_img_gd = '';
