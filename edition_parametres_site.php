<?php
//=====================================================================
// Gestion des paramètres généraux du site
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'NomS',
    'ANomS',
    'Adresse_MailS',
    'AAdresse_MailS',
    'Affiche_AnneeG',
    'AAffiche_AnneeG',
    'ComportementG',
    'AComportementG',
    'Font_PdfG',
    'AFont_PdfG',
    'Divers',
    'ADivers',
    'Diff_Note',
    'ADiff_Note',
    'Pivot_MasquageS',
    'APivot_MasquageS',
    'nom_du_fichier',
    'ANom_Image',
    'Garder_Image',
    'garder',
    'Anc_coul',
    'Nouv_coul',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// Gestion standard des pages
$acces = 'M';                                    // Type d'accès de la page : (M)ise à jour, (L)ecture
$niv_requis = 'G';
$titre = $LG_Menu_Title['Site_parameters'];    // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$NomS             = Secur_Variable_Post($NomS, 80, 'S');
$ANomS            = Secur_Variable_Post($ANomS, 80, 'S');
$Adresse_MailS    = Secur_Variable_Post($Adresse_MailS, 80, 'S');
$AAdresse_MailS   = Secur_Variable_Post($AAdresse_MailS, 80, 'S');
$Affiche_AnneeG   = Secur_Variable_Post($Affiche_AnneeG, 1, 'S');
$AAffiche_AnneeG  = Secur_Variable_Post($AAffiche_AnneeG, 1, 'S');
$ComportementG    = Secur_Variable_Post($ComportementG, 1, 'S');
$AComportementG   = Secur_Variable_Post($AComportementG, 1, 'S');
$Divers           = Secur_Variable_Post($Divers, 65535, 'S');
$ADivers          = Secur_Variable_Post($ADivers, 65535, 'S');
$Diff_Note        = Secur_Variable_Post($Diff_Note, 1, 'S');
$ADiff_Note       = Secur_Variable_Post($ADiff_Note, 1, 'S');
$Pivot_MasquageS  = Secur_Variable_Post($Pivot_MasquageS, 1, 'N');
$APivot_MasquageS = Secur_Variable_Post($APivot_MasquageS, 1, 'N');
$Font_PdfG        = Secur_Variable_Post($Font_PdfG, 80, 'S');
$AFont_PdfG       = Secur_Variable_Post($AFont_PdfG, 80, 'S');
$ANom_Image       = Secur_Variable_Post($ANom_Image, 80, 'S');
$Garder_Image     = Secur_Variable_Post($Garder_Image, 2, 'S');
$garder           = Secur_Variable_Post($garder, 1, 'S');
$Anc_coul         = Secur_Variable_Post($Anc_coul, 7, 'S');
$Nouv_coul        = Secur_Variable_Post($Nouv_coul, 7, 'S');

// Type d'objet pour le commentaire
$Type_Ref = 'G';
$erreur = '';

//Demande de mise à jour
if ($bt_OK) {
    $NomFic = $_FILES['nom_du_fichier']['name'];
    if ($NomFic != '') {
        // Contrôle de l'image à télécharger
        $erreur = Controle_Charg_Image();

        // Erreur constatée sur le chargement
        if ($erreur != '') {
            $_SESSION['message'] = $erreur;
            $image = 'exclamation.png';
            echo '<img src="' . $root . '/assets/img/' . $image . '" BORDER=0 alt="' . $image . '" title="' . $image . '">';
            echo ' ' . $LG_Site_Param_Error . LG_SEMIC . $erreur . '<br />';
        }
        // Sinon on peut télécharger
        else {
            // Téléchargement du fichier après contrôle
            if (!ctrl_fichier_ko()) {
                $NomFic = nettoye_nom_fic($NomFic);
                $nomComplet =  $chemin_images_util . $NomFic;
                if (!move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $nomComplet)) {
                    $erreur = $LG_Site_Param_Upload_Error;
                }
                // On chmod le fichier si on n'est pas sous Windows
                else {
                    if (substr(php_uname(), 0, 7) != 'Windows') chmod($nomComplet, 0644);
                }
            } else $erreur = '-'; // ==> pas de maj en base en cas d'erreur
        }
    }

    if ($erreur == '') {
        // Init des zones de requête
        $req = '';
        //echo 'garder : '.$garder.'<br />';
        //echo '$nom_du_fichier : '.$nom_du_fichier.'<br />';
        //echo '$ANom_Image : '.$ANom_Image.'<br />';
        //if (isset($Garder_Image) and ($Garder_Image) and ($nom_du_fichier == '')) $nom_du_fichier = $ANom_Image;
        if (($garder == 'G') and ($NomFic == '')) $NomFic = $ANom_Image;
        if ($garder == 'S')  $NomFic = '';
        //echo '$nom_du_fichier : '.$NomFic.'<br />';
        if ($Pivot_MasquageS == 0) $Pivot_MasquageS = 9999;
        Aj_Zone_Req('Nom', $NomS, $ANomS, 'A', $req);
        Aj_Zone_Req('Adresse_Mail', $Adresse_MailS, $AAdresse_MailS, 'A', $req);
        Aj_Zone_Req('Affiche_Annee', $Affiche_AnneeG, $AAffiche_AnneeG, 'A', $req);
        Aj_Zone_Req('Pivot_Masquage', $Pivot_MasquageS, $APivot_MasquageS, 'N', $req);
        Aj_Zone_Req('Comportement', $ComportementG, $AComportementG, 'A', $req);
        Aj_Zone_Req('Image_Index', $NomFic, $ANom_Image, 'A', $req);
        Aj_Zone_Req('Font_Pdf', $Font_PdfG, $AFont_PdfG, 'A', $req);
        Aj_Zone_Req('Coul_PDF', '#' . $Nouv_coul, $Anc_coul, 'A', $req);

        // Modification
        if ($req != '') {
            $req = str_replace("Affiche_Annee=null", "Affiche_Annee='N'", $req);
            $req = 'update ' . nom_table('general') . ' set ' . $req;
            $res = maj_sql($req);
        }
        // Traitement des commentaires
        $req_comment = '';
        maj_commentaire(0, $Type_Ref, $Divers, $ADivers, $Diff_Note, $ADiff_Note);
        if ($req_comment != '') $res = maj_sql($req_comment);

        // Retour arrière
        Retour_Ar();
    }
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    # include(__DIR__ . '/assets/js/Edition_Parametres_Graphiques.js');
    include(__DIR__ . '/assets/js/Insert_Tiny.js');

    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut(my_html($titre), $compl, 'Edition_Parametres_Site', '');

    echo '<form id="saisie" enctype="multipart/form-data" method="post" onsubmit="return verification_form(this,\'NomS,Adresse_MailS\')">' . "\n";
    echo '<table width="85%" class="table_form">' . "\n";
    echo '<tr><td colspan="2"> </td></tr>';
    echo '<tr><td class="label" width="35%"> ' . ucfirst($LG_Site_Param_Name) . ' </td>';
    echo '<td class="value"><input type="text" class="oblig" size="80" name="NomS" value="' . $Nom . '"/> ' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="hidden" name="ANomS" value="' . $Nom . '"/></td></tr>' . "\n";
    echo '<tr><td class="label" width="35%"> ' . ucfirst($LG_Site_Param_Mail) . ' </td>';
    echo '<td class="value"><input type="text" class="oblig" size="80" name="Adresse_MailS" value="' . $Adresse_Mail . '"/> ' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="hidden" name="AAdresse_MailS" value="' . $Adresse_Mail . '"/></td></tr>' . "\n";
    echo '<tr><td class="label" width="35%"> ' . ucfirst($LG_Site_Param_Year_Only) . ' </td>';
    echo '<td class="value"><input type="checkbox" name="Affiche_AnneeG" value="O"';
    if ($Affiche_Annee == 'O') echo ' checked';
    echo "/>\n";
    echo '<input type="hidden" name="AAffiche_AnneeG" value="' . $Affiche_Annee . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="35%"> ' . ucfirst($LG_Site_Param_Year_Threshold) . ' </td>';
    echo '<td class="value">';
    if (($Environnement == 'I') and ($SiteGratuit) and (!$Premium)) {
        echo $LG_Site_Param_No_Premium;
        echo '<input type="hidden" name="Pivot_MasquageS" value="9999"/>';
    } else {
        echo '<input type="text" size="4" maxlength="4" name="Pivot_MasquageS" value="' . $Pivot_Masquage . '"/>' . "\n";
    }
    echo '<input type="hidden" name="APivot_MasquageS" value="' . $Pivot_Masquage . '"/></td></tr>' . "\n";

    echo '<tr><td class="label" width="35%"> ' . $LG_Site_Param_Hover_Clic . ' </td><td class="value">';
    echo '<input type="radio" id="ComportementGS" name="ComportementG" value="S"' . ($Comportement == 'S') ? ' checked' : '' . '/>'
        . '<label for="ComportementGS">' . $LG_Site_Param_Hover . '</label> ';
    echo '<input type="radio" id="ComportementGC" name="ComportementG" value="C"' . ($Comportement == 'C') ? ' checked' : '' . '/>'
        . '<label for="ComportementGC">' . $LG_Site_Param_Click . '</label> ';
    echo '<input type="hidden" name="AComportementG" value="' . $Comportement . '"/></td></tr>' . "\n";

    // === Commentaire
    echo '<tr><td class="label" width="35%"> ' . LG_CH_COMMENT . ' </td><td class="value">';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire(0, $Type_Ref);
    echo '<textarea cols="80" rows="4" name="Divers">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="hidden" name="ADivers" value="' . my_html($Commentaire) . '"/></td></tr>' . "\n";

    // Diffusion Internet commentaire
    echo '<tr><td class="label" width="35%"> ' . LG_CH_COMMENT_VISIBILITY . ' </td><td class="value">';
    echo '<input type="checkbox" name="Diff_Note" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_Note" value="' . $Diffusion_Commentaire_Internet . '"/></td></tr>' . "\n";

    // Police de caractères des fichiers pdf générés
    echo '<tr><td class="label" width="35%"> ' . ucfirst($LG_Site_Param_PDF_Font) . ' </td>';
    echo '<td class="value">';
    $list_font_pdf = array_merge($list_font_pdf, $list_font_pdf_plus);
    sort($list_font_pdf);
    echo '<select name="Font_PdfG">' . "\n";
    $nb_pol = count($list_font_pdf);
    for ($nb = 0; $nb < $nb_pol; $nb++) {
        $laPol = $list_font_pdf[$nb];
        echo '<option value="' . $laPol . '"';
        if ($laPol == $font_pdf) echo ' selected="selected"';
        echo '>' . $laPol . '</option>';
    }
    echo '</select>';
    echo '<input type="hidden" name="AFont_PdfG" value="' . $font_pdf . '"/></td></tr>' . "\n";

    // Possibilité de saisir la couleur de la police des pdf sauf pour les sites hébergés non Premium
    if ((!$SiteGratuit) or ($Premium)) {
        $ancien = $coul_pdf;
        echo '<tr><td class="label" width="35%"> ' . ucfirst($LG_Site_Param_PDF_Font_Color) . ' </td>';
        echo '<td class="value">';
        echo my_html($LG_Site_Param_PDF_Font_Color_Current) . LG_SEMIC . '<input readonly="readonly" type="text" id="Anc_coul" name="Anc_coul" size="7" maxlength="7" value="' . $ancien . '" style="background-color:' . $ancien . '"/>' . "\n";
        echo ' ' . my_html($LG_Site_Param_PDF_Font_Color_New) . LG_SEMIC . '<input class="color" readonly="readonly" type="text" id="Nouv_coul" name="Nouv_coul" size="7" maxlength="7" value="' . $ancien . '" style="background-color:' . $ancien . '"/>' . "\n";
        $texte_im = $LG_Site_Param_PDF_Font_Color_Back;
        echo ' <img id="im_dernier_coul" src="' . $root . '/assets/img/' . $Icones['conversion'] . '" alt="' . $texte_im . '" title="' . $texte_im . '" onclick="remet_code_coul(\'' . 'coul' . '\');"/>';
        echo '</td></tr>' . "\n";
    }

    echo '<tr><td class="label" width="35%"> ' . ucfirst($LG_Site_Param_Home_Image) . ' </td>';
    echo '<td class="value">';
    echo '<input type="file" name="nom_du_fichier" value="' . $Image_Index . '" size="50"/> ';
    if ($Image_Index != '') {
        Aff_Img_Redim_Lien($chemin_images_util . $Image_Index, 100, 100);
        echo '<br />';
        echo '<input type="radio" name="garder" value="G" checked/>' . $LG_Site_Param_Image_With . ' ';
        echo '<input type="radio" name="garder" value="S"/>' . $LG_Site_Param_Image_Without . ' ';
        echo '<br /><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . $LG_tip . '" title="' . $LG_tip . '"> ' . $LG_Site_Param_Image_No_Need;
    }
    echo '<input type="hidden" name="ANom_Image" value="' . $Image_Index . '"/></td></tr>' . "\n";
    echo '<tr><td colspan="2"> </td></tr>';
    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');
    echo '</table>' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo '</form>';
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';

    echo '<script type="text/javascript" src="assets/js/jscolor.js"></script>';
} else {
    echo "<body bgcolor=\"#FFFFFF\">";
}
?>
</body>

</html>