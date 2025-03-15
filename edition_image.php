<?php
//=====================================================================
// Edition d'une image
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'DiversI',
    'ADiversI',
    'Titre',
    'ATitre',
    'nom_du_fichier',
    'ANom',
    'Defaut',
    'ADefaut',
    'Diff_Internet_Image',
    'ADiff_Internet_Image',
    'Diff_Internet_Note',
    'ADiff_Internet_Note',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Gestion standard des pages
$acces = 'M';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Ch_Image_Script_Title;        // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup des variables passées dans l'URL : identifiant, référence et type de référence
$Type_Ref    = Recup_Variable('Type_Ref', 'C', 'PVULE');
$ident_image = Recup_Variable('ident_image', 'N');
if (!$ident_image) $ident_image = -1;
$Reference   = Recup_Variable('Reference', 'N');
if (!$Reference) $Reference = -1;
$Modif = true;
if ($ident_image == -1) $Modif = false;


$ADiversI = Secur_Variable_Post($ADiversI, 65535, 'S');

if ($bt_Sup) {
    // Suppression des commentaires
    if ($ADiversI != '') {
        $req_comment = req_sup_commentaire($ident_image, 'I');
        $res = maj_sql($req_comment);
    }
    // Suppresion du lien de l'image
    $req = 'delete from ' . nom_table('images') . ' where ident_image = ' . $ident_image;
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

//Demande de mise à jour
if ($bt_OK) {

    $Titre                = Secur_Variable_Post($Titre, 80, 'S');
    $ATitre               = Secur_Variable_Post($ATitre, 80, 'S');
    $DiversI              = Secur_Variable_Post($DiversI, 65535, 'S');
    $nom_du_fichier       = Secur_Variable_Post($nom_du_fichier, 80, 'S');
    $ANom                 = Secur_Variable_Post($ANom, 80, 'S');
    $Defaut               = Secur_Variable_Post($Defaut, 1, 'S');
    $ADefaut              = Secur_Variable_Post($ADefaut, 1, 'S');
    $Diff_Internet_Image  = Secur_Variable_Post($Diff_Internet_Image, 1, 'S');
    $ADiff_Internet_Image = Secur_Variable_Post($ADiff_Internet_Image, 1, 'S');
    $Diff_Internet_Note   = Secur_Variable_Post($Diff_Internet_Note, 1, 'S');
    $ADiff_Internet_Note  = Secur_Variable_Post($ADiff_Internet_Note, 1, 'S');

    $erreur = '';
    $msg = '';
    $msg .= ' dans OK ';
    $msg .= ' $files-' . $_FILES['nom_du_fichier']['name'] . '- ';

    // Une demande de chargement a été faite
    $NomFic = $_FILES['nom_du_fichier']['name'];
    if ($NomFic != '') {

        // Contrôle de l'image à télécharger
        $erreur = Controle_Charg_Image();

        // Erreur constatée sur le chargement
        if ($erreur != '') {
            $_SESSION['message'] = $erreur;
            $image = 'exclamation.png';
            echo '<img src="' . $chemin_images_icones . $image . '" BORDER=0 alt="' . $image . '" title="' . $image . '">';
            echo '&nbsp;Erreur : ' . $erreur . '<br />';
        }
        // Sinon on peut télécharger
        else {
            // Téléchargement du fichier après contrôle
            if (!ctrl_fichier_ko()) {
                $NomFic = nettoye_nom_fic($NomFic);
                $nomComplet =  $chemin_images_util . $NomFic;
                move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $nomComplet);
                // On chmod le fichier si on n'est pas sous Windows
                if (substr(php_uname(), 0, 7) != 'Windows') chmod($nomComplet, 0644);
                $nom_du_fichier = $NomFic;
            } else $erreur = '-'; // ==> pas de maj en base en cas d'erreur
        }
    }

    // Init des zones de requête
    $req = '';
    $req_comment = '';
    $maj_site = false;

    if ($erreur == '') {
        // Valeurs par défaut
        if ($Defaut == '') $Defaut = 'N';
        if ($Diff_Internet_Image == '') $Diff_Internet_Image = 'n';

        // Cas de la modification
        if ($ident_image != -1) {
            Aj_Zone_Req('Titre', $Titre, $ATitre, 'A', $req);
            // Pas de demande de chargement ==> on simule "pas de modif de la zone"
            if ($_FILES['nom_du_fichier']['name'] == '') {
                $nom_du_fichier = $ANom;
            }
            Aj_Zone_Req('nom', $nom_du_fichier, $ANom, 'A', $req);
            Aj_Zone_Req('Defaut', $Defaut, $ADefaut, 'A', $req);
            Aj_Zone_Req('Diff_Internet_Img', $Diff_Internet_Image, $ADiff_Internet_Image, 'A', $req);
            // Constitution de la requête de mise à jour des commentaires
            maj_commentaire($ident_image, 'I', $DiversI, $ADiversI, $Diff_Internet_Note, $ADiff_Internet_Note);
        }
        // Cas de la création
        else {
            // On n'autorise la création que si le nom et la description sont saisis
            if (($Titre != '') and ($nom_du_fichier != '')) {
                $req = $Reference . ',"' . $Type_Ref . '"';
                Ins_Zone_Req($nom_du_fichier, 'A', $req);
                Ins_Zone_Req($Defaut, 'A', $req);
                Ins_Zone_Req($Titre, 'A', $req);
                Ins_Zone_Req($Diff_Internet_Image, 'A', $req);
                // Récupération de l'identifiant à positionner
                $nouv_ident = Nouvel_Identifiant('ident_image', 'images');
                // Traitement des commentaires
                if ($DiversI != '') {
                    insere_commentaire($nouv_ident, 'I', $DiversI, $Diff_Internet_Note);
                }
            }
        }
    }

    // Cas de la modification
    if (($Modif) and ($req != '')) {
        $req = 'update ' . nom_table('images') . ' set ' . $req .
            ' where ident_image = ' . $ident_image;
    }
    // Cas de la création
    if ((!$Modif) and ($req != '')) {
        $req = 'insert into ' . nom_table('images') . ' values(' . $nouv_ident . ',' . $req . ")";
    }

    // Exéution des requêtes
    if ($req != '') {
        // Bascule de l'indicateur défaut
        if (($Defaut == 'O') and ($ADefaut != 'O')) {
            $req2 = 'update ' . nom_table('images') . ' set Defaut = "N"'
                . ' where Type_Ref = "' . $Type_Ref . '"'
                . '   and Reference = ' . $Reference;
            $res = maj_sql($req2);
        }
        $res = maj_sql($req);
        $maj_site = true;
    }

    // Exécution de la requête sur les commentaires
    if ($req_comment != '') {
        $res = maj_sql($req_comment);
        $maj_site = true;
    }

    if ($maj_site) maj_date_site();

    // Retour sur la page précédente
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {

    include(__DIR__ . '/assets/js/Insert_Tiny.js');
    # include(__DIR__ . '/assets/js/Edition_Image.js');

    $compl = Ajoute_Page_Info(650, 250);
    Insere_Haut($titre, $compl, 'Edition_Image', $ident_image);

    echo '<form id="saisie" enctype="multipart/form-data" method="post" onsubmit="return verification_form_image(this);" action="' . my_self() . '?' . Query_Str() . '">' . "\n";

    if ($Modif) {
        $sql = 'select * from ' . nom_table('images') . ' where ident_image = ' . $ident_image . ' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
        Champ_car($enreg2, 'nom');
        Champ_car($enreg2, 'Titre');
    } else {
        $enreg2['ident_image'] = 0;
        $enreg2['Reference'] = 0;
        $enreg2['Type_Ref'] = '';
        $enreg2['nom'] = '';
        $enreg2['Defaut'] = '';
        $enreg2['Titre'] = '';
        $enreg2['Diff_Internet_Img'] = '';
    }

    echo '<table width="80%" class="table_form">' . "\n";

    $nom_img = $enreg2['nom'];
    $image = $chemin_images_util . $nom_img;
    $larg_titre = '25';

    echo '<tr><td colspan="2">&nbsp;</td></tr>';

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Ch_Image_Title . '&nbsp;</td><td class="value">';
    $titre = $enreg2['Titre'];
    echo '<input type="text" size="80" name="Titre" value="' . $titre . '" class="oblig"/>&nbsp;' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="' . $hidden . '" name="ATitre" value="' . $titre . '"/>' . "\n";
    echo '</td>' . "\n";
    echo '<td align="center" rowspan="4">';
    if ($nom_img != '') {
        Aff_Img_Redim_Lien($image, 150, 150);
        //Affiche le nom de l'image sans le répertoire
        $path_parts = pathinfo($image);
        echo '<br />' . $path_parts['basename'];
    } else echo '<img id="idimg" src="" border="0" alt="" title="" width="150" height="150"/>';
    echo '</td></tr>' . "\n";

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Ch_Image_Name . '&nbsp;</td><td class="value">';
    echo '<input type="file" name="nom_du_fichier" value="' . $nom_img . '" class="oblig" size="50" onchange="readURL(this,\'idimg\');"/>&nbsp;';
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<br />(' . $LG_Ch_Image_No_Need . ')';
    echo '<input type="' . $hidden . '" name="ANom" value="' . $nom_img . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_CH_COMMENT . '&nbsp;</td><td class="value">';
    $Existe_Commentaire = Rech_Commentaire($ident_image, 'I');
    echo '<textarea cols="50" rows="4" name="DiversI">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="' . $hidden . '" name="ADiversI" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr>' . "\n";

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_CH_COMMENT_VISIBILITY . '&nbsp;</td><td class="value">';
    echo '<input type="checkbox" name="Diff_Internet_Note" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_Internet_Note" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Ch_Image_Default . '&nbsp;</td><td class="value">';
    $val_defaut = $enreg2['Defaut'];
    echo '<input type="radio" id="DefautO" name="Defaut" value="O"';
    if ($val_defaut == 'O') echo ' checked="checked"';
    echo '/><label for="DefautO">' . ucfirst($LG_Yes) . '</label>&nbsp;' . "\n";
    echo '<input type="radio" id="DefautN" name="Defaut" value="N"';
    if ($val_defaut == 'N') echo ' checked="checked"';
    echo '/><label for="DefautN">' . ucfirst($LG_No) . '</label>' . "\n";
    echo '<input type="' . $hidden . '" name="ADefaut" value="' . $val_defaut . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    // Diffusion Internet image
    if ($ident_image != -1) $diff_img = $enreg2['Diff_Internet_Img'];
    else $diff_img = 'o';

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Ch_Image_Visibility . '&nbsp;</td><td class="value">';
    echo '<input type="checkbox" name="Diff_Internet_Image" value="o"';
    if ($diff_img == 'o') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="' . $hidden . '" name="ADiff_Internet_Image" value="' . $diff_img . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    $lib_sup = '';
    if ($ident_image != -1) $lib_sup = $lib_Supprimer;
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, $LG_Ch_Image_This);

    echo '</table>' . "\n";

    echo "</form>";

    $_SESSION['message'] = '';

    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/' . $Icones['home'] . '" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
}

?>
<script type="text/javascript">
    // Vérification des zones obligatoires seulement sur le bouton ok
    // non standard car FF se comporte bizarrement avec la value du type file
    function verification_form_image(formulaire) {
        var retour = true;
        var fic_saisi = formulaire.nom_du_fichier.value;
        if (formulaire.cache.value == 'ok') {
            var ko1 = false;
            if (formulaire.nom_du_fichier.value == '') {
                fic_saisi = formulaire.ANom.value;
            }
            if (fic_saisi == '') {
                formulaire.nom_du_fichier.className = 'absent';
                ko1 = true;
                retour = false;
            } else {
                formulaire.nom_du_fichier.className = 'oblig';
            }
            var ko2 = verification_form(formulaire, 'Titre');
            if (ko2 = false) retour = false;
        }
        return retour;
    }
</script>
</body>

</html>