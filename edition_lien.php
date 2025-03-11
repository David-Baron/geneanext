<?php
//=====================================================================
// Edition d'un lien
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'Type_Lien',
    'AType_Lien',
    'Description',
    'ADescription',
    'URL',
    'AURL',
    'nom_du_fichier',
    'ANom',
    'image_oui',
    'DiversL',
    'ADiversL',
    'Sur_Accueil',
    'ASur_Accueil',
    'Diff_Internet_NoteL',
    'ADiff_Internet_NoteL',
    'Diff_Internet_L',
    'ADiff_Internet_L',
    'Statut_Fiche',
    'AStatut_Fiche',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
    //echo $nom_variables.' : '.$$nom_variables.'<br />';
}

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture

// Recup des variables passées dans l'URL : Identifiant du lien
$Ref = Recup_Variable('Ref', 'N');
$Modif = true;
if ($Ref == -1) $Modif = false;

// Titre pour META
if ($Ref != -1) $titre = $LG_Menu_Title['Link_Edit'];
else $titre = $LG_Menu_Title['Link_Add'];

$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

$Type_Ref = 'L';                        // Type de référence pour les commentaires

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Type_Lien            = Secur_Variable_Post($Type_Lien, 30, 'S');
$AType_Lien           = Secur_Variable_Post($AType_Lien, 30, 'S');
$Description          = Secur_Variable_Post($Description, 255, 'S');
$ADescription         = Secur_Variable_Post($ADescription, 255, 'S');
$URL                  = Secur_Variable_Post($URL, 255, 'S');
$AURL                 = Secur_Variable_Post($AURL, 255, 'S');
$nom_du_fichier       = Secur_Variable_Post($nom_du_fichier, 80, 'S');
$ANom                 = Secur_Variable_Post($ANom, 80, 'S');
$Sur_Accueil          = Secur_Variable_Post($Sur_Accueil, 1, 'N');
$ASur_Accueil         = Secur_Variable_Post($ASur_Accueil, 1, 'N');
$image_oui            = Secur_Variable_Post($image_oui, 2, 'S');
$DiversL              = Secur_Variable_Post($DiversL, 65535, 'S');
$ADiversL             = Secur_Variable_Post($ADiversL, 65535, 'S');
$Diff_Internet_NoteL  = Secur_Variable_Post($Diff_Internet_NoteL, 1, 'S');
$ADiff_Internet_NoteL = Secur_Variable_Post($ADiff_Internet_NoteL, 1, 'S');
$Diff_Internet_L      = Secur_Variable_Post($Diff_Internet_L, 1, 'S');
$ADiff_Internet_L     = Secur_Variable_Post($ADiff_Internet_L, 1, 'S');
$Statut_Fiche         = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$AStatut_Fiche        = Secur_Variable_Post($AStatut_Fiche, 1, 'S');

// Affiche un Lien
function Aff_Lien($enreg2)
{
    global $chemin_images, $chemin_images_util, $Commentaire, $Diffusion_Commentaire_Internet, $Images, $Ref, $SiteGratuit, $Premium, $lib_Okay, $lib_Annuler, $lib_Supprimer, $LG_Yes, $LG_No, $hidden;

    echo '<br />';
    echo '<div id="content">' . "\n";
    echo '<table id="cols"  border="0" cellpadding="0" cellspacing="0" >' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="border-right:0px solid #9cb0bb">' . "\n";
    echo '  <img src="' . $chemin_images . $Images['clear'] . '" width="800" height="1" alt="clear"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr>' . "\n";
    echo '<td class="left">' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";
    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pane1\', this)" id="tab1">' . my_html(LG_DATA_TAB) . '</a></li>' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pan_Fiche\', this)">' . my_html(LG_RECORD) . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<div class="tab-panes">' . "\n";

    // Onglets données générales du lien
    echo '<div id="pane1">' . "\n";
    echo '<fieldset>';
    echo '<legend>' . my_html(LG_DATA_TAB) . '</legend>';
    echo '<table width="100%" border="0">' . "\n";
    $larg = "30";

    col_titre_tab_noClass(LG_LINK_TYPE, $larg);
    echo '<td colspan="2"><input type="text" size="30" name="Type_Lien" class="oblig" value="' . $enreg2['type_lien'] . '"/>&nbsp;' . "\n";
    Img_Zone_Oblig('imgObligType');
    echo '<input type="' . $hidden . '" name="AType_Lien" value="' . $enreg2['type_lien'] . '"/>';

    // Select avec les types existants
    $req = 'select distinct type_lien from ' . nom_table('liens');
    $result = lect_sql($req);
    if ($result->rowCount() > 0) {
        echo '<br />' . my_html(LG_LINK_OR_EXISTING_TYPE) . LG_SEMIC;
        echo '<select name="types_existants" onchange="document.forms.saisie.Type_Lien.value = document.forms.saisie.types_existants.value">' . "\n";
        echo '<option value="-">' . my_html(LG_LINK_SELECT_TYPE) . '</option>' . "\n";
        while ($enrT = $result->fetch(PDO::FETCH_NUM)) {
            echo '<option value="' . $enrT[0] . '">' . my_html($enrT[0]) . '</option>' . "\n";
        }
        echo '</select>' . "\n";
    }
    echo '</td></tr>' . "\n";

    col_titre_tab_noClass(LG_LINK_DESCRIPTION, $larg);
    $cnt = $enreg2['description'];
    echo '<td colspan="2"><input type="text" size="80" name="Description" class="oblig" value="' . $cnt . '"/>&nbsp;' . "\n";
    Img_Zone_Oblig('imgObligDesc');
    echo '<input type="' . $hidden . '" name="ADescription" value="' . $cnt . '"/></td>' . "\n";
    echo "</tr>\n";

    col_titre_tab_noClass(LG_LINK_URL, $larg);
    $cnt = $enreg2['URL'];
    echo '<td colspan="2"><input type="text" size="80" name="URL" class="oblig" value="' . $cnt . '"/>&nbsp;' . "\n";
    Img_Zone_Oblig('imgObligURL');
    echo '<input type="' . $hidden . '" name="AURL" value="' . $cnt . '"/></td>' . "\n";
    echo "</tr>\n";

    echo '<tr><td valign="middle">Image</td>' . "\n";
    $nom_img = $enreg2['image'];
    echo '<td valign="middle">';
    echo '<input type="file" name="nom_du_fichier"/><br />';
    if ($nom_img != '') {
        echo '<label for="image_oui">' . LG_LINK_KEEP_IMAGE . '</label>.' . LG_SEMIC . '<input type="checkbox" id="image_oui" name="image_oui"';
        echo ' checked="checked"/>';
    } else {
        echo '<input type="' . $hidden . '" name="image_oui">';
    }
    echo '</td>' . "\n";
    echo '<td align="center">';
    if ($nom_img != '') {
        $image = $chemin_images_util . $nom_img;
        Aff_Img_Redim_Lien($image, 100, 100);
        echo '<br />(' . my_html(LG_LINK_NO_REFRESH) . ')&nbsp;';
    } else echo '&nbsp;';
    echo '<input type="' . $hidden . '" name="ANom" value="' . $nom_img . '"/></td>' . "\n";
    echo "</tr>\n";

    if ((!$SiteGratuit) or ($Premium)) {
        col_titre_tab_noClass(LG_LINK_AVAIL_HOME, $larg);
        $cont = $enreg2['Sur_Accueil'];
        echo '<td colspan="2">';
        echo '<input type="radio" id="Sur_Accueil_1" name="Sur_Accueil" value="1"';
        if ($cont) echo ' checked="checked"';
        echo '/><label for="Sur_Accueil_1">' . $LG_Yes . '</label>';
        echo '<input type="radio" id="Sur_Accueil_0" name="Sur_Accueil" value="0"';
        if (!$cont) echo ' checked="checked"';
        echo '/><label for="Sur_Accueil_0">' . $LG_No . '</label>';
        echo '<input type="' . $hidden . '" name="ASur_Accueil" value="' . $cont . '"/></td>' . "\n";
        echo "</tr>\n";
    }

    echo "</table>\n";
    echo '</fieldset>' . "\n";

    // Commentaires
    echo '<fieldset>';
    aff_legend(LG_CH_COMMENT);
    echo '<table width="95%" border="0">' . "\n";
    echo '<tr>' . "\n";
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Ref, 'L');
    echo '<td>';
    echo '<textarea cols="50" rows="4" name="DiversL">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="' . $hidden . '" name="ADiversL" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr><tr>';
    // Diffusion Internet commentaire
    echo '<td><label for="Diff_Internet_NoteL">' . LG_CH_COMMENT_VISIBILITY . '</label>'
        . '&nbsp;<input type="checkbox" id="Diff_Internet_NoteL" name="Diff_Internet_NoteL" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="' . $hidden . '" name="ADiff_Internet_NoteL" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    echo '</div>' . "\n";

    // Données de la fiche
    echo '<div id="pan_Fiche">' . "\n";
    // Visibilité sur internet du lien²
    echo '<fieldset>' . "\n";
    aff_legend(LG_LINK_VISIBILITY);
    echo '<table width="95%" border="0">' . "\n";
    echo '<tr>' . "\n";
    $cont = $enreg2['Diff_Internet'];
    echo '<td colspan="2">';
    echo '<input type="radio" id="Diff_Internet_L_1" name="Diff_Internet_L" value="1"';
    if ($cont) echo ' checked="checked"';
    echo '/><label for="Diff_Internet_L_1">' . $LG_Yes . '</label>';
    echo '<input type="radio" id="Diff_Internet_L_0" name="Diff_Internet_L" value="0"';
    if (!$cont) echo ' checked="checked"';
    echo '/><label for="Diff_Internet_L_0">' . $LG_No . '</label>';
    echo '<input type="' . $hidden . '" name="ADiff_Internet_L" value="' . $cont . '"/></td>' . "\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo '</fieldset>' . "\n";
    // Affiche les données propres à l'enregistrement de la fiche
    $x = Affiche_Fiche($enreg2, 1);
    echo '</div>' . "\n";

    echo '</div> ' . "\n";  //<!-- panes -->

    $lib_sup = '';
    if ($Ref != -1) $lib_sup = $lib_Supprimer;
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_LINK_THIS, false);

    echo '</div>' . "\n";    //  <!-- tab container -->
    echo '</td></tr></table></div>' . "\n";
}

if ($bt_Sup) {
    // Suppression des commentaires
    if ($ADiversL != '') {
        $req_comment = req_sup_commentaire($Ref, $Type_Ref);
        $res = lect_sql($req_comment);
    }
    // Suppression du lien lui-même
    $req = 'delete from ' . nom_table('liens') . ' where Ref_lien = ' . $Ref . ';';
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

//Demande de mise à jour
if ($bt_OK) {

    $maj_site = false;

    $erreur = '';
    // Une demande de chargement a été faite
    if ($_FILES['nom_du_fichier']['name'] != '') {
        // Contrôle de l'image à télécharger
        $erreur = Controle_Charg_Image();
        // Erreur constatée sur le chargement
        if ($erreur != '') {
            $image = $Icones['warning'];
            echo '<img src="' . $chemin_images_icones . $image . '" BORDER=0 alt="' . $image . '" title="' . $image . '"/>';
            echo '&nbsp;Erreur : ' . $erreur . '<br />';
        }
        // Sinon on peut télécharger
        else {
            if (!ctrl_fichier_ko()) {
                move_uploaded_file(
                    $_FILES['nom_du_fichier']['tmp_name'],
                    $chemin_images_util . $_FILES['nom_du_fichier']['name']
                );
                // la zone nom_du_fichier n'est pas alimentée...
                $nom_du_fichier = $_FILES['nom_du_fichier']['name'];
                $image_oui = "on";
            } else $erreur = '-'; // ==> pas de maj en base en cas d'erreur
        }
    }

    // Init des zones de requête
    $req = '';
    $req2 = '';
    $req_comment = '';
    if ($Diff_Internet_NoteL == '') $Diff_Internet_NoteL = 'N';
    if ($Statut_Fiche == '') $Statut_Fiche = 'N';

    if ($erreur == '') {

        $Creation = false;

        // Cas de la modification
        if ($Ref != -1) {
            Aj_Zone_Req('type_lien', $Type_Lien, $AType_Lien, 'A', $req);
            Aj_Zone_Req('description', $Description, $ADescription, 'A', $req);
            Aj_Zone_Req('URL', $URL, $AURL, 'A', $req);
            // Pas de demande de chargement ==> on simule "pas de modif de la zone"
            if ($_FILES['nom_du_fichier']['name'] == '') {
                $nom_du_fichier = $ANom;
            }
            // Sans image
            if ($image_oui != 'on') {
                $nom_du_fichier = '';
            }
            Aj_Zone_Req('image', $nom_du_fichier, $ANom, 'A', $req);
            Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $req);
            Aj_Zone_Req('Sur_Accueil', $Sur_Accueil, $ASur_Accueil, 'N', $req);
            Aj_Zone_Req('Diff_Internet', $Diff_Internet_L, $ADiff_Internet_L, 'N', $req);
            // Traitement des commentaires
            maj_commentaire($Ref, $Type_Ref, $DiversL, $ADiversL, $Diff_Internet_NoteL, $ADiff_Internet_NoteL);
            if ($req_comment != '') $res = maj_sql($req_comment);
        }
        // Cas de la création
        else {
            // On n'autorise la création que si le type et la description sont saisis
            if (($Description != '') and ($Type_Lien != '')) {
                Ins_Zone_Req($Type_Lien, 'A', $req);
                Ins_Zone_Req($Description, 'A', $req);
                Ins_Zone_Req($URL, 'A', $req);
                Ins_Zone_Req($nom_du_fichier, 'A', $req);
                // Récupération de l'identifiant à positionner
                $nouv_ident = Nouvel_Identifiant("Ref_Lien", "liens");
            }
        }

        // Complément de la requête 1
        if ($req != '') {
            $req = $req . ',';
        }
        // Cas de la modification
        if (($Ref != -1) and ($req != '')) {
            $req = 'update ' . nom_table('liens') . ' set ' . $req .
                'Date_Modification = current_timestamp' .
                ' where Ref_lien  = ' . $Ref . ';';
        }
        // Cas de la création
        if (($Ref == -1) and ($Description != '') and ($Type_Lien != '')) {
            $Creation = true;
            $req = 'insert into ' . nom_table('liens') . ' values(' . $nouv_ident . ',' . $req .
                'current_timestamp,current_timestamp';
            Ins_Zone_Req($Statut_Fiche, 'A', $req);
            Ins_Zone_Req($Sur_Accueil, 'N', $req);
            Ins_Zone_Req($Diff_Internet_L, 'N', $req);
            $req = $req . ')';
        }

        // Exéution de la requête
        if ($req != '') {
            $res = maj_sql($req);
            $maj_site = true;
        }

        // Création d'un enregistrement dans la table commentaires uniquement sur création (déjà fait sur maj)
        if (($DiversL != '') and ($Creation)) {
            insere_commentaire($nouv_ident, $Type_Ref, $DiversL, $Diff_Internet_NoteL);
            $res = maj_sql($req_comment);
            $maj_site = true;
        }

        if ($maj_site) maj_date_site();

        // Retour sur la page précédente
        if (!$debug) Retour_Ar();
    }
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {

    include(__DIR__ . '/assets/js/Insert_Tiny.js');

    $compl = Ajoute_Page_Info(600, 150);
    // Lien vers la fiche uniquement en modification
    if ($Ref != -1)
        $compl .= Affiche_Icone_Lien('href="'. $root .'/fiche_lien.php?Ref=' . $Ref . '"', 'page', 'Fiche lien') . '&nbsp;';

    Insere_Haut($titre, $compl, 'Edition_Lien', $Ref);

    echo '<form id="saisie" method="post" enctype="multipart/form-data" onsubmit="return verification_form(this,\'Type_Lien,Description,URL\')" action="' . my_self() . '?Ref=' . $Ref . '">' . "\n";
    echo '<input type="' . $hidden . '" name="Ref" value="' . $Ref . '"/>' . "\n";
    aff_origine();

    if ($Modif) {
        $sql = 'select * from ' . nom_table('liens') . ' where Ref_Lien = ' . $Ref . ' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
        Champ_car($enreg2, 'type_lien');
        Champ_car($enreg2, 'description');
        Champ_car($enreg2, 'URL');
        Champ_car($enreg2, 'image');
    } else {
        $enreg2['Ref_lien'] = 0;
        $enreg2['type_lien'] = '';
        $enreg2['description'] = '';
        $enreg2['URL'] = '';
        $enreg2['image'] = '';
        $enreg2['Date_Creation'] = '';
        $enreg2['Date_Modification'] = '';
        $enreg2['Statut_Fiche'] = '';
        $enreg2['Sur_Accueil'] = 0;
        $enreg2['Diff_Internet'] = 0;
    }

    // Affichage des données du lien
    $x = Aff_Lien($enreg2);

    echo '</form>';
    include(__DIR__ . '/assets/js/gest_onglets.js');
    echo '<!-- On positionne l\'onglet par défaut -->' . "\n";
    echo '<script type="text/javascript">' . "\n";
    echo '	setupPanes("container1", "tab1", 40);' . "\n";
    echo '</script>' . "\n";

    Insere_Bas($compl);
}

?>
</body>

</html>