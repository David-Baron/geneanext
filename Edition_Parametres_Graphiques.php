<?php
//=====================================================================
// Paramétrage graphique du site
//=====================================================================

session_start();

include('fonctions.php');

function Liste_Noms_Images($chemin)
{
    global $chemin_images, $ext_poss;
    $dir = $chemin;
    //$dir = $chemin_images.$chemin;
    // echo '$chemin demande : '.$chemin.'<br>';
    $dossier = opendir($dir);
    $premier = true;
    while ($fichier = readdir($dossier)) {
        $extension = strtolower(substr(strrchr($fichier, "."), 1));
        if ((is_file($dir . '/' . $fichier)) and (strpos($ext_poss, $extension) != false)) {
            if (!$premier) echo ',';
            else $premier = false;
            echo '"' . $fichier . '"';
        }
    }
}

function affiche_images($extension, $nb_cases, $cancel = false)
{
    global $chemin_images, $chemin_images_arbres, $Icones, $aff_infos, $chemin_images_icones, $LG_Graphics_First, $LG_Graphics_Stop, $LG_Graphics_Next, $LG_Graphics_Last, $w_fonds, $h_fonds, $w_barres, $h_barres, $w_lettres, $h_lettres, $w_arbres, $h_arbres, $Chemin_Lettre, $Chemin_Barre, $Image_Fond, $Lettre_B, $Image_Barre, $Image_Arbre_Asc;
    echo '<table cellpadding="1" width="100%" border="0">';
    echo '<tr>';
    for ($nb = 1; $nb <= $nb_cases; $nb++)
        echo '<td id="case_' . $nb . '_' . $extension . '" align="center"></td>';
    echo '<td valign="middle">';
    if ($cancel) {
        $texte_im = 'Pas de ' . $extension;
        $texte_im = rtrim($texte_im, 's');
        echo '&nbsp;&nbsp;<img id="im_cancel_' . $extension . '" src="' . $chemin_images_icones . $Icones['cancel'] . '" alt="' . $texte_im . '" title="' . $texte_im . '" onclick="efface(\'' . $extension . '\')"/>';
    }
    echo '</td>';
    $src = '';
    $texte_im = 'choix';
    switch ($extension) {
        case 'fonds':
            $larg = $w_fonds;
            $haut = $h_fonds;
            $src = $chemin_images . $Image_Fond;
            break;
        case 'barres':
            $larg = $w_barres;
            $haut = $h_barres;
            $src = $Chemin_Barre;
            break;
        case 'lettres':
            $larg = $w_lettres;
            $haut = $h_lettres;
            $src = $Chemin_Lettre;
            break;
        case 'arbres_asc':
            $larg = $w_arbres;
            $haut = $h_arbres;
            $src = $chemin_images . 'arbres_asc/' . $Image_Arbre_Asc;
            break;
    }
    // Si le nom de fichier est '-', on init la source
    if ($src[strlen($src) - 1] == '-') $src = '';
    echo '<td align="center" valign="middle">S&eacute;lection :<br>';
    echo '<img id="image_copie_' . $extension . '" width="' . $larg . '" height="' . $haut . '" src="' . $src . '" alt="' . $texte_im . '" title="' . $texte_im . '"/>';
    echo '&nbsp;<input type="' . $aff_infos . '" id="src_case_copie_' . $extension . '" name="src_case_copie_' . $extension . '" value="-"/>';
    echo '</td></tr>' . "\n";

    echo '<tr align="center"><td colspan="' . $nb_cases . '">';
    echo '<img id ="im_premier_' . $extension . '" src="' . $chemin_images_icones . $Icones['first']
        . '" alt="' . $LG_Graphics_First . '" title="' . $LG_Graphics_First . '" onclick="premier(\'' . $extension . '\')"/>&nbsp;';
    echo '<img id="im_precedent_' . $extension . '" src="' . $chemin_images_icones . $Icones['stop']
        . '" alt="' . $LG_Graphics_Stop . '" title="' . $LG_Graphics_Stop . '" onclick="recule(\'' . $extension . '\',nb_cases_' . $extension . ')"/>';
    echo '&nbsp;&nbsp;<input type="text" style="text-align:center;" readonly="readonly" name="page_' . $extension . '" id="page_' . $extension . '" size="3" />&nbsp;&nbsp;';
    echo '<img id="im_suivant_' . $extension . '" src="' . $chemin_images_icones . $Icones['next']
        . '" alt="' . $LG_Graphics_Next . '" title="' . $LG_Graphics_Next . '" onclick="avance(\'' . $extension . '\',nb_cases_' . $extension . ')"/>&nbsp;';
    echo '<img id="im_dernier_' . $extension . '" src="' . $chemin_images_icones . $Icones['last']
        . '" alt="' . $LG_Graphics_Last . '" title="' . $LG_Graphics_Last . '" onclick="dernier(\'' . $extension . '\',nb_cases_' . $extension . ',nb_images_' . $extension . ')"/>';
    echo '</td>' . "\n";
    echo '<td>&nbsp;</td></tr>' . "\n";

    echo '</table>' . "\n";
    echo '<input type="' . $aff_infos . '" id="indice' . '_' . $extension . '" value="1"/>';
    for ($nb = 1; $nb <= $nb_cases; $nb++)
        echo '<input type="' . $aff_infos . '"  id="src_case_' . $nb . '_' . $extension . '" value="-"/>';
}

// Affiche une ligne pour choisir les couleurs
function ligne_couleurs($libelle, $nom, $anc_val)
{
    global $chemin_images_icones, $Icones, $LG_Graphics_Init_Color;
    echo '<tr><td>' . my_html($libelle) . ' :</td>' . "\n";
    echo '<td><input readonly="readonly" type="text" id="Anc_' . $nom . '" name="Anc_' . $nom . '" size="7" maxlength="7" value="' . $anc_val . '" style="background-color:' . $anc_val . '"/></td>' . "\n";
    echo '<td><input class="color" type="text" id="Nouv_' . $nom . '" name="Nouv_' . $nom . '" size="7" maxlength="6" value="' . $anc_val . '" style="background-color:' . $anc_val . '"/></td>' . "\n";
    echo '<td valign="middle">'
        . '&nbsp;<img id="im_dernier_' . $nom . '" src="' . $chemin_images_icones . $Icones['conversion']
        . '" alt="' . $LG_Graphics_Init_Color . '" title="' . $LG_Graphics_Init_Color
        . '" onclick="remet_code_coul(\'' . $nom . '\');"/>';
    echo '</td></tr>' . "\n";
}

function aff_degrade($couleur, $val_degrade, $lib_couleur)
{
    global $Degrade;
    echo '<tr>' . "\n";
    echo '<td width="15%"><input type="radio" id ="SelDegrade_' . $val_degrade . '" name="SelDegrade" value="' . $val_degrade . '"';
    if ($Degrade == $val_degrade) echo ' checked="checked"';
    echo '/><label for="SelDegrade_' . $val_degrade . '">' . $lib_couleur . '</label></td>';
    $nb_cols = count($couleur);
    for ($r = 0; $r < $nb_cols; $r++) {
        $rvb = strtoupper($couleur[$r]);
        echo '<td bgcolor="' . $rvb . '">&nbsp;</td>' . "\n";
    }
    echo '<td>&nbsp;</td>';
    echo '</tr>' . "\n";
}

// Affichage des boutons radio pour les propositions de graphisme
function affiche_radio_prop($dominante)
{
    global     $gra_coul_lib, $gra_coul_val, $gra_coul_bord, $gra_barre, $gra_ch_barre, $gra_fond, $gra_lettre, $gra_description;
    echo '<input type="radio" id ="choix_' . $dominante . '" name="choix" value="' . $dominante . '" onclick="
		change_barre(\'' . $gra_barre[$dominante] . '\');
		change_lettre(\'' . $gra_lettre[$dominante] . '\');
		change_fond(\'' . $gra_fond[$dominante] . '\');
		change_couleur(\'case_form_lib\',\'' . $gra_coul_lib[$dominante] . '\');
		change_couleur(\'case_form_val\',\'' . $gra_coul_val[$dominante] . '\');
		change_couleur(\'liste_ligne1_1\',\'' . $gra_coul_lib[$dominante] . '\');
		change_couleur(\'liste_ligne2_1\',\'' . $gra_coul_val[$dominante] . '\');
		change_couleur(\'Nouv_Paires\',\'' . $gra_coul_lib[$dominante] . '\');
		change_couleur(\'Nouv_Lib\',\'' . $gra_coul_lib[$dominante] . '\');
		change_couleur(\'Nouv_Bord\',\'' . $gra_coul_bord[$dominante] . '\');
		change_couleur(\'Nouv_Bord_Onglets\',\'' . $gra_coul_bord[$dominante] . '\');
		change_couleur(\'liste_ligne1_2\',\'' . $gra_coul_lib[$dominante] . '\');
		change_couleur(\'liste_ligne2_2\',\'' . $gra_coul_val[$dominante] . '\');
		change_couleur(\'Nouv_Impaires\',\'' . $gra_coul_val[$dominante] . '\');
		change_couleur(\'Nouv_Val\',\'' . $gra_coul_val[$dominante] . '\');
		document.getElementById(\'Sel_fonds\').value = get_filename(\'' . $gra_fond[$dominante] . '\');
		document.getElementById(\'Sel_lettres\').value = get_filename(\'' . $gra_lettre[$dominante] . '\');
		document.getElementById(\'Sel_barres\').value = get_filename(\'' . $gra_barre[$dominante] . '\');
		copie_code_coul(\'Nouv_Paires\',\'' . $gra_coul_lib[$dominante] . '\');
		copie_code_coul(\'Nouv_Lib\',\'' . $gra_coul_lib[$dominante] . '\');
		copie_code_coul(\'Nouv_Bord\',\'' . $gra_coul_bord[$dominante] . '\');
		copie_code_coul(\'Nouv_Bord_Onglets\',\'' . $gra_coul_bord[$dominante] . '\');
		copie_code_coul(\'Nouv_Impaires\',\'' . $gra_coul_val[$dominante] . '\');
		copie_code_coul(\'Nouv_Val\',\'' . $gra_coul_val[$dominante] . '\');
	"/><label for="choix_' . $dominante . '">' . $gra_description[$dominante] . '</label>&nbsp;';
}

// Affiche une personne example
function affiche_pers_exemple($no_pers, $num_sosa, $style, $id_tr)
{
    global     $Ex_Born, $Ex_Dead, $Ex_Name;
    echo '<tr id="liste_ligne' . $id_tr . '" style="background-color:' . $style . ';">';
    echo '<td width="12%">' . $num_sosa . '</td>';
    $id = $no_pers - 1;
    echo '<td width="48%">' . $Ex_Name[$id] . '</td>';
    echo '<td width="20%">' . etend_date($Ex_Born[$id]) . '</td>';
    echo '<td width="20%">' . etend_date($Ex_Dead[$id]) . '</td>';
    echo '</tr>';
}

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'Sel_lettres',
    'ASel_lettres',
    'Sel_fonds',
    'ASel_fonds',
    'Sel_barres',
    'ASel_barres',
    'Anc_Bord_Onglets',
    'Nouv_Bord_Onglets',
    'Anc_Lib',
    'Nouv_Lib',
    'Anc_Val',
    'Nouv_Val',
    'Anc_Bord',
    'Nouv_Bord',
    'Anc_Paires',
    'Nouv_Paires',
    'Anc_Impaires',
    'Nouv_Impaires',
    'SelDegrade',
    'ADegrade',
    'SelImageBarre',
    'AImageBarre',
    'nom_du_fichier',
    'Sel_arbres_asc',
    'ASel_arbres_asc',
    'Affiche_Mar_Arbre',
    'AAffiche_Mar_Arbre',

);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour
$niv_requis = 'G';
$titre = $LG_Menu_Title['Design'];
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) {
    Retour_Ar();
    exit;
}

$Sel_lettres       = Secur_Variable_Post($Sel_lettres, 80, 'S');
$ASel_lettres      = Secur_Variable_Post($ASel_lettres, 80, 'S');
$Sel_fonds         = Secur_Variable_Post($Sel_fonds, 80, 'S');
$ASel_fonds        = Secur_Variable_Post($ASel_fonds, 80, 'S');
$Sel_barres        = Secur_Variable_Post($Sel_barres, 80, 'S');
$ASel_barres       = Secur_Variable_Post($ASel_barres, 80, 'S');
$Anc_Bord_Onglets  = Secur_Variable_Post($Anc_Bord_Onglets, 7, 'S');
$Nouv_Bord_Onglets = Secur_Variable_Post($Nouv_Bord_Onglets, 7, 'S');
$Anc_Lib           = Secur_Variable_Post($Anc_Lib, 7, 'S');
$Nouv_Lib          = Secur_Variable_Post($Nouv_Lib, 7, 'S');
$Anc_Val           = Secur_Variable_Post($Anc_Val, 7, 'S');
$Nouv_Val          = Secur_Variable_Post($Nouv_Val, 7, 'S');
$Anc_Bord          = Secur_Variable_Post($Anc_Bord, 7, 'S');
$Nouv_Bord         = Secur_Variable_Post($Nouv_Bord, 7, 'S');
$Anc_Paires        = Secur_Variable_Post($Anc_Paires, 7, 'S');
$Nouv_Paires       = Secur_Variable_Post($Nouv_Paires, 7, 'S');
$Anc_Impaires      = Secur_Variable_Post($Anc_Impaires, 7, 'S');
$Nouv_Impaires     = Secur_Variable_Post($Nouv_Impaires, 7, 'S');
$SelDegrade        = Secur_Variable_Post($SelDegrade, 1, 'S');
$AADegrade         = Secur_Variable_Post($ADegrade, 1, 'S');
$SelImageBarre     = Secur_Variable_Post($SelImageBarre, 80, 'S');
$AImageBarre       = Secur_Variable_Post($AImageBarre, 80, 'S');
$Affiche_Mar_Arbre  = Secur_Variable_Post($Affiche_Mar_Arbre, 1, 'S');
$AAffiche_Mar_Arbre = Secur_Variable_Post($AAffiche_Mar_Arbre, 1, 'S');
$Sel_arbres_asc    = Secur_Variable_Post($Sel_arbres_asc, 300, 'S'); // Plus long que la mémo car comporte le chemn
$ASel_arbres_asc   = Secur_Variable_Post($ASel_arbres_asc, 80, 'S');

//Demande de mise à jour
if ($bt_OK) {

    $erreur = '';

    // Une demande de chargement a été faite
    if ($_FILES['nom_du_fichier']['name'] != '') {

        // Contrôle de l'image à télécharger
        $erreur = Controle_Charg_Image();

        // Erreur constatée sur le chargement
        if ($erreur != '') {
            Insere_Haut($titre, '', 'Edition_Parametres_Graphiques', '');
            Affiche_Stop(html_entity_decode($erreur));
            Insere_Bas('');
        }
        // Sinon on peut télécharger
        else {
            // téléchargement du fichier après contrôle
            if (!ctrl_fichier_ko()) {
                move_uploaded_file(
                    $_FILES['nom_du_fichier']['tmp_name'],
                    $chemin_images . 'fonds/' . $_FILES['nom_du_fichier']['name']
                );
                // la zone nom_du_fichier n'est pas alimentée...
                $nom_du_fichier = $_FILES['nom_du_fichier']['name'];
                // On chmod le fichier si on n'est pas sous Windows
                if (!$is_windows) chmod($chemin_images . 'fonds/' . $nom_du_fichier, 0644);
                $Sel_fonds = $nom_du_fichier;
            } else $erreur = '-'; // ==> pas de maj en base en cas d'erreur
        }
    }

    if ($erreur == '') {
        // Init des zones de requête
        $req = '';
        if ($Sel_lettres == '') $Sel_lettres = '-';
        if ($Sel_fonds == '') $Sel_fonds = '-';
        // On remet les # qui ont pu être supprimés par jscolor
        if (substr($Nouv_Bord_Onglets, 0, 1) != '#') $Nouv_Bord_Onglets = '#' . $Nouv_Bord_Onglets;
        if (substr($Nouv_Lib, 0, 1) != '#') $Nouv_Lib = '#' . $Nouv_Lib;
        if (substr($Nouv_Val, 0, 1) != '#') $Nouv_Val = '#' . $Nouv_Val;
        if (substr($Nouv_Bord, 0, 1) != '#') $Nouv_Bord = '#' . $Nouv_Bord;
        if (substr($Nouv_Paires, 0, 1) != '#') $Nouv_Paires = '#' . $Nouv_Paires;
        if (substr($Nouv_Impaires, 0, 1) != '#') $Nouv_Impaires = '#' . $Nouv_Impaires;

        Aj_Zone_Req('Lettre_B', $Sel_lettres, $ASel_lettres, 'A', $req);
        Aj_Zone_Req('Image_Fond', $Sel_fonds, $ASel_fonds, 'A', $req);
        Aj_Zone_Req('Image_Barre', $Sel_barres, $ASel_barres, 'A', $req);
        Aj_Zone_Req('Coul_Fond_Table', $Nouv_Bord_Onglets, $Anc_Bord_Onglets, 'A', $req);
        Aj_Zone_Req('Coul_Lib', $Nouv_Lib, $Anc_Lib, 'A', $req);
        Aj_Zone_Req('Coul_Val', $Nouv_Val, $Anc_Val, 'A', $req);
        Aj_Zone_Req('Coul_Bord', $Nouv_Bord, $Anc_Bord, 'A', $req);
        Aj_Zone_Req('Coul_Paires', $Nouv_Paires, $Anc_Paires, 'A', $req);
        Aj_Zone_Req('Coul_Impaires', $Nouv_Impaires, $Anc_Impaires, 'A', $req);
        Aj_Zone_Req('Degrade', $SelDegrade, $ADegrade, 'A', $req);
        Aj_Zone_Req('Image_Barre', $Sel_barres, $AImageBarre, 'A', $req);
        //if ($src_case_copie_arbres_asc <> '') $src_case_copie_arbres_asc = basename($src_case_copie_arbres_asc);
        Aj_Zone_Req('Image_Arbre_Asc', $Sel_arbres_asc, $ASel_arbres_asc, 'A', $req);
        Aj_Zone_Req('Affiche_Mar_Arbre_Asc', $Affiche_Mar_Arbre, $AAffiche_Mar_Arbre, 'A', $req);

        // Modification
        if ($req != '') {
            $req = 'update ' . nom_table('general') . ' set ' . $req;
            $res = maj_sql($req);
        }
        // Retour arrière
        Retour_Ar();
        exit;
    }
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut($titre, $compl, 'Edition_Parametres_Graphiques', '');

    $aff_infos = ($debug) ? 'text' : 'hidden';

    $all = true;
    include_once('Degrades_inc.php');

    $Ex_Born = array('19020312GL', '18990307GL', '19050416GL', '19070616GL');
    $Ex_Dead = array('19730518GL', '19710512GL', '19791123GL', '19790520GL');
    $Ex_Name = array('DUPOND Prosper Joseph Antoine', 'DURAND Ambroisine Augustine', 'MARTIN Maurice Théodule François', 'DULAC Solange Eugénie');

    $nb_cases_fonds = 4;
    $nb_cases_barres = 4;
    $nb_cases_lettres = 5;
    $nb_cases_arbres_asc = 3;

    $w_fonds   = 100;
    $h_fonds   = 100;
    $w_barres  = 150;
    $h_barres  = 30;
    $w_lettres = 60;
    $h_lettres = 75;
    $w_arbres = 144;
    $h_arbres = 180;

    echo '<br>' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";

    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'gra_pre_def\', this)" id="tab_gra_pre_def">' . $LG_Graphics_Pred . '</a></li>' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'gra_dem_images\', this)" id="tab_gra_dem_images">' . $LG_Graphics_Req_Img . '</a></li>' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'gra_dem_couleurs\', this)" id="tab_gra_dem_couleurs">' . $LG_Graphics_Req_Cols . '</a></li>' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'arbres_asc\', this)" id="tab_arbres_asc">' . $LG_Graphics_Tree . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<form id="saisie" method="post" action="' . my_self() . '" enctype="multipart/form-data" >' . "\n";

    echo '<div class="tab-panes">' . "\n";

    echo '<div id="gra_pre_def">' . "\n";
    // Graphisme
    $dominante = 'marron';

    echo '<table width="85%" class="table_form">' . "\n";
    echo '<tr align="center">';
    echo '<td width="30%">';
    echo $LG_Graphics_BG . '<br>';
    echo '<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" align="center">';
    echo '<tr align="center">';
    echo '<td id="fond" style="background-color:white; background-image:url(\'' . $chemin_images . $Image_Fond . '\'); background-repeat:repeat;">';
    echo '<img id="lettre" src="' . $Chemin_Lettre . '" alt="B" title="B" />' . $LG_Graphics_Welcome . ' ...';
    echo '</td></tr>';
    echo '</table>' . "\n";
    echo '</td>';
    echo '<td width="33%">';
    echo $LG_Graphics_Form . '<br>';
    echo '<table width="100%">';
    echo '<tr>';
    echo '<td id="case_form_lib" width="40%" style="background-color:' . $Coul_Lib . ';">' . $LG_Graphics_BG_Label . '</td>';
    echo '<td id="case_form_val" style="background-color:' . $Coul_Val . ';">' . $LG_Graphics_BG_Value . '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</td>';
    echo '<td width="37%">';
    echo $LG_Graphics_Bar_List . '<br>';
    echo '<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" align="center">';
    echo '<tr align="center">';
    $posi = strrpos($Image_Barre, '/');
    $ch_barre = $chemin_images_barres . substr($Image_Barre, $posi + 1);
    echo '<td id="barre" style="background-image:url(\'' . $ch_barre . '\'); background-repeat:repeat-x;">';
    echo $LG_Graphics_3Gen . '&nbsp;&nbsp;' . Affiche_Icone('oeil', $LG_show_noshow);
    echo '</td></tr></table>';
    // Affichage des personnes exemples
    echo '<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" align="center">';
    $x = affiche_pers_exemple(1, '4', $Coul_Impaires, '1_1');
    $x = affiche_pers_exemple(2, '5', $Coul_Paires,   '2_1');
    $x = affiche_pers_exemple(3, '6', $Coul_Impaires, '1_2');
    $x = affiche_pers_exemple(4, '7', $Coul_Paires,   '2_2');
    echo '</table>';
    echo '</td>';
    echo '</tr>';
    echo '</table>' . "\n";

    foreach ($dominantes as $dominante) {
        affiche_radio_prop($dominante);
    }
    echo '</div>' . "\n";

    echo '<div id="gra_dem_images">' . "\n";
    echo '<br>Fonds de page' . "\n";
    affiche_images('fonds', $nb_cases_fonds, true);
    // Possibilité de charger une image de fond personnelle
    if ((!$SiteGratuit) or ($SiteGratuit and $Premium)) {
        echo '<br>Fond personnalis&eacute; : <input type="file" name="nom_du_fichier" id="nom_du_fichier" onchange="readURL(this,\'image_copie_fonds\');"/>' . "\n";
    }

    echo '<hr />Lettres' . "\n";
    affiche_images('lettres', $nb_cases_lettres, true);
    echo '<hr />Barres' . "\n";
    affiche_images('barres', $nb_cases_barres);
    echo '</div>' . "\n";

    echo '<div id="gra_dem_couleurs">' . "\n";
    // Couleurs
    echo '<table>' . "\n";
    echo '<tr align="center"><td>&nbsp;</td><td>' . $LG_Graphics_Color_Current . '</td><td>' . $LG_Graphics_Color_New . '</td><td>&nbsp;</td></tr>' . "\n";
    ligne_couleurs($LG_Graphics_Table_Border, 'Bord_Onglets', $coul_fond_table);
    echo '<tr><td colspan="4">&nbsp;</td></tr>' . "\n";
    echo '<tr><td colspan="4">' . $LG_Graphics_Form_Without_Tab . '</td></tr>' . "\n";
    ligne_couleurs($LG_Graphics_Borders, 'Bord', $Coul_Bord);
    ligne_couleurs($LG_Graphics_BG_Label, 'Lib', $Coul_Lib);
    ligne_couleurs($LG_Graphics_BG_Value, 'Val', $Coul_Val);

    echo '<tr><td colspan="4">&nbsp;</td></tr>' . "\n";
    echo '<tr><td colspan="4">' . $LG_Graphics_Lists . '</td></tr>' . "\n";
    ligne_couleurs($LG_Graphics_Odd, 'Paires', $Coul_Paires);
    ligne_couleurs($LG_Graphics_Even, 'Impaires', $Coul_Impaires);
    echo '</table>' . "\n";

    // Couleur des dégradés
    echo '<br>D&eacute;grad&eacute;' . "\n";
    echo '<table border="0" width="95%">' . "\n";
    aff_degrade($Rouge, 'R', 'Rouge');
    aff_degrade($Vert, 'V', 'Vert');
    aff_degrade($Bleu, 'B', 'Bleu');
    aff_degrade($Jaune, 'J', 'Jaune');
    aff_degrade($Marron, 'M', 'Marron');
    aff_degrade($Violet, 'v', 'Violet');
    aff_degrade($Gris, 'G', 'Gris');
    aff_degrade($Orange, 'O', 'Orange');
    aff_degrade($Rose, 'r', 'Rose');
    aff_degrade($Lavande, 'L', 'Lavande');
    echo '</table>' . "\n";
    echo '</div>' . "\n";

    echo '<div id="arbres_asc">' . "\n";
    echo '<hr />Fond de page' . "\n";
    affiche_images('arbres_asc', $nb_cases_arbres_asc);
    echo my_html($LG_Graphics_Show_Year) . '&nbsp;' . "\n";
    echo '<input type="checkbox" name="Affiche_Mar_Arbre" value="O"';
    if ($Affiche_Mar_Arbre_Asc == 'O') echo 'checked="checked"';
    echo "/>\n";

    echo '</div>' . "\n";

    echo '</div>' . "\n";        //echo '<!--  fin panes -->';

    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);

    if ($debug) echo 'Zones réceptrices des saisies : ';
    // Zones réceptrices des saisies
    echo '<input type="' . $aff_infos . '" name="Sel_fonds" id="Sel_fonds" value="' . $Image_Fond . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="Sel_lettres" id="Sel_lettres" value="' . $Lettre_B . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="Sel_barres" id="Sel_barres" value="' . $Image_Barre . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="Sel_arbres_asc" id="Sel_arbres_asc" value="' . $Image_Arbre_Asc . '"/>' . "\n";
    // Mémoristion des valeurs initiales
    if ($debug) echo '<br>Zones initiales : ';
    echo '<input type="' . $aff_infos . '" name="ASel_fonds" value="' . $Image_Fond . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="ASel_lettres" value="' . $Lettre_B . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="ASel_barres" id="ASel_barres" value="' . $Image_Barre . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="ASel_arbres_asc" id="ASel_arbres_asc" value="' . $Image_Arbre_Asc . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="ACouleur" value="' . $coul_fond_table . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="ADegrade" value="' . $Degrade . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="AImageBarre" value="' . $Image_Barre . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="ASelFond_Arbre" value="' . $Image_Arbre_Asc . '"/>' . "\n";
    echo '<input type="' . $aff_infos . '" name="AAffiche_Mar_Arbre" value="' . $Affiche_Mar_Arbre_Asc . '"/>' . "\n";

    echo '</form>';

    echo '</div>' . "\n";        //echo '<!-- fin tab-container -->';

    Insere_Bas($compl);

    include('gest_onglets.js');
    include('jscripts/Edition_Parametres_Graphiques.js');
    //include('jscripts/jscolor.js');
    echo '<script type="text/javascript" src="jscripts/jscolor.js"></script>';
}

$ext_poss = '/gif/jpg/png/';

?>

<script type="text/javascript">
    <!--
    <?php
    echo 'var im_stop = "' . $chemin_images_icones . $Icones['stop'] . '";' . "\n";
    echo 'var im_next = "' . $chemin_images_icones . $Icones['next'] . '";' . "\n";
    echo 'var im_prev = "' . $chemin_images_icones . $Icones['previous'] . '";' . "\n";
    echo 'var im_clear = "' . $chemin_images . $Images['clear'] . '";' . "\n";

    echo 'var nb_cases_fonds = ' . $nb_cases_fonds . ';' . "\n";
    echo 'var chemin_images_fonds = "' . $chemin_images . 'fonds/";' . "\n";

    echo 'var nb_cases_lettres = ' . $nb_cases_lettres . ';' . "\n";
    echo 'var chemin_images_lettres = "' . $chemin_images_lettres . '";' . "\n";

    echo 'var nb_cases_barres = ' . $nb_cases_barres . ';' . "\n";
    echo 'var chemin_images_barres = "' . $chemin_images_barres . '";' . "\n";

    echo 'var chemin_images_arbres = "' . $chemin_images_a_asc . '";' . "\n";
    echo 'var nb_cases_arbres_asc = ' . $nb_cases_arbres_asc . ';' . "\n";

    echo 'var w_fonds = ' . $w_fonds . '; var h_fonds = ' . $h_fonds . ';';
    echo 'var w_barres = ' . $w_barres . '; var h_barres = ' . $h_barres . ';';
    echo 'var w_lettres = ' . $w_lettres . '; var h_lettres = ' . $h_lettres . ';' . "\n";
    echo 'var w_arbres_asc = ' . $w_arbres . '; var h_arbres_asc = ' . $h_arbres . ';' . "\n";

    ?>

    var nb_cases = 4;
    var pas = nb_cases;
    var chemin_images = '';
    var lextension = '';

    // Liste des fonds d'écran possibles
    var images_fonds = [<?php Liste_Noms_Images($chemin_images . 'fonds'); ?>];
    var nb_images_fonds = images_fonds.length;

    // Liste des barres possibles
    var images_barres = [<?php Liste_Noms_Images($chemin_images_barres); ?>];
    var nb_images_barres = images_barres.length;

    // Liste des lettres possibles
    var images_lettres = [<?php Liste_Noms_Images($chemin_images_lettres); ?>];
    var nb_images_lettres = images_lettres.length;

    // Liste des arbres ascendants
    var images_arbres_asc = [<?php Liste_Noms_Images($chemin_images_a_asc); ?>];
    var nb_images_arbres_asc = images_arbres_asc.length;

    // Affiche une preview de l'image téléchargée sur changement du bouton d'upload
    function readURL2(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image_copie_fonds').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function efface(extension) {
        document.getElementById("image_copie_" + extension).src = im_clear;
        document.getElementById("src_case_copie_" + extension).value = '-';
        document.getElementById("Sel_" + extension).value = '-';
    }

    function copie(obj) {
        var source = obj.src;
        //console.log(obj);
        var lid = obj.id;
        var composants = lid.split('_');
        if (composants.length == 3) lextension = composants[2];
        else lextension = composants[2] + '_' + composants[3];
        document.getElementById("image_copie_" + lextension).src = source;
        document.getElementById("src_case_copie_" + lextension).value = source;
        document.getElementById("Sel_" + lextension).value = get_filename(source);
    }

    function affiche(la_case) {
        var indice = document.getElementById("indice").value;
        var cont_demande = '<img src="' + <?php echo $chemin_images; ?> + images[indice] + '" width="100" height="100" border="1" onclick="copie(this);"/>';
        document.getElementById(la_case).innerHTML = cont_demande;
    }

    function affiche_cases(indice, extension) {
        var la_case = '';
        var src_la_case = '';
        if (extension == 'fonds') {
            chemin_images = chemin_images_fonds;
            images = images_fonds;
            nb_images = nb_images_fonds;
            w_image = w_fonds;
            h_image = h_fonds;
            nb_cases = nb_cases_fonds;
        }
        if (extension == 'lettres') {
            chemin_images = chemin_images_lettres;
            images = images_lettres;
            nb_images = nb_images_lettres;
            w_image = w_lettres;
            h_image = h_lettres;
            nb_cases = nb_cases_lettres;
        }
        if (extension == 'barres') {
            chemin_images = chemin_images_barres;
            images = images_barres;
            nb_images = nb_images_barres;
            w_image = w_barres;
            h_image = h_barres;
            nb_cases = nb_cases_barres;
        }
        if (extension == 'arbres_asc') {
            chemin_images = chemin_images_arbres;
            images = images_arbres_asc;
            nb_images = nb_images_arbres_asc;
            w_image = w_arbres_asc;
            h_image = h_arbres_asc;
            nb_cases = nb_cases_arbres_asc;
        }
        for (nb = 1; nb <= nb_cases; nb++) {
            indice_im = indice + nb;
            la_case = "case_" + nb + '_' + extension;
            src_la_case = "src_" + la_case;
            //if (extension == 'barres') window.alert(indice_im+'/'+nb_images+' pour '+nb_cases+' cases ');
            if (indice_im <= nb_images + 1) {
                document.getElementById(la_case).innerHTML =
                    '<img id="img_' + nb + '_' + extension + '" src="' + chemin_images + images[indice_im - 2] + '" alt="' + images[indice_im - 2] + '" title="' + images[indice_im - 2] + '" width="' + w_image + '" height="' + h_image + '" onclick="copie(this);"/>';
                document.getElementById(src_la_case).value = images[indice_im - 2];
            } else {
                //window.alert('Dépassement');
                document.getElementById(la_case).innerHTML = '&nbsp';
                document.getElementById(src_la_case).value = '';
            }
        }
        la_case = "page_" + extension;
        //indice_im = indice / nb_cases;
        //window.alert(la_case);
        document.getElementById(la_case).value = Math.floor(indice / nb_cases) + 1;
    }

    function premier(extension) {
        indice = 1;
        document.getElementById("im_precedent_" + extension).src = im_stop;
        document.getElementById("indice_" + extension).value = indice;
        affiche_cases(1, extension);
        document.getElementById("im_suivant_" + extension).src = im_next;
    }

    function dernier(extension, nb_cases, nb_images) {
        indice = Math.floor(nb_images / nb_cases) * nb_cases;
        if (indice == nb_images)
            indice = indice - nb_cases;
        indice++;
        //indice = indice + 1;
        //window.alert(indice+'/'+nb_images);
        document.getElementById("im_suivant_" + extension).src = im_stop;
        document.getElementById("indice_" + extension).value = indice;
        affiche_cases(indice, extension);
        document.getElementById("im_precedent_" + extension).src = im_prev;
    }

    function recule(extension, nb_cases) {
        var zone_indice = document.getElementById("indice_" + extension);
        var indice = Math.floor(zone_indice.value);
        if (indice > 1) {
            indice = indice - Math.floor(nb_cases);
            if (indice == 1) {
                document.getElementById("im_precedent_" + extension).src = im_stop;
            }
            zone_indice.value = indice;
            affiche_cases(indice, extension);
        }
        if (indice < nb_images - 1) {
            document.getElementById("im_suivant_" + extension).src = im_next;
        }
    }

    function avance(extension, nb_cases) {
        var zone_indice = document.getElementById("indice_" + extension);
        var indice = Math.floor(zone_indice.value);
        if (indice == 1) {
            document.getElementById("im_precedent_" + extension).src = im_prev;
        }
        if (indice < nb_images - 1) {
            indice = indice + Math.floor(nb_cases);
            if (indice + Math.floor(nb_cases) > nb_images) {
                document.getElementById("im_suivant_" + extension).src = im_stop;
            }
            zone_indice.value = indice;
            affiche_cases(indice, extension);
        }
    }

    affiche_cases(1, 'fonds');
    affiche_cases(1, 'lettres');
    affiche_cases(1, 'barres');
    affiche_cases(1, 'arbres_asc');
    setupPanes("container1", "tab_gra_pre_def", 50);

    //
    -->
</script>
</body>

</html>