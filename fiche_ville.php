<?php
//=====================================================================
// Fiche d'une ville
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                        // Type d'accès de la page : (L)ecture
$titre = $LG_Menu_Title['Town'];

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de'identifiant de la ville passé dans l'URL
$Ident = Recup_Variable('Ident', 'N');

$req_sel = 'SELECT v.*, d.Nom_Depart_Min FROM ' . nom_table('villes') . ' v, ' . nom_table('departements') . ' d' .
    ' WHERE v.Identifiant_zone = ' . $Ident .
    ' AND d.Identifiant_zone = v.Zone_Mere limit 1';

$x = Lit_Env();                    // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

    // 2 solutions en cas d'absence :
    // - l'utilisateur a saisi un code absent dans l'URL ; le code ne doit pas être saisi dans l'URL, donc tant pis pour lui...
    // - on revient de la mpage de modification et on a demandé la suppression ; donc on renvoye sur la page précédente, à priori la liste
    if ((!$enreg_sel) or ($Ident == 0))
        Retour_Ar();
    else {
        $enreg = $enreg_sel;
        $enreg2 = $enreg;

        $compl = Ajoute_Page_Info(600, 150);
        if ($est_gestionnaire) {
            $compl = Affiche_Icone_Lien('href="' . $root . '/edition_ville.php?Ident=' . $Ident . '"', 'fiche_edition', $LG_Menu_Title['Town_Edit']) . '&nbsp;';
        }
        Insere_Haut($titre, $compl, 'Fiche_Ville', '');

        $Type_Ref = 'V';

        $n_ville = $enreg['Nom_Ville'];
        $n_ville_html = my_html($n_ville);
        $n_ville_aff = stripslashes($n_ville);

        $cp = $enreg['Code_Postal'];
        if ($cp == '') $cp = '?';
        $dep = $enreg['Nom_Depart_Min'];
        if ($dep == '') $dep = '?';
        else $dep = my_html($dep);

        $Lat_V = $enreg['Latitude'];
        $Long_V = $enreg['Longitude'];

        // Affichage de l'image par défaut pour la ville
        $image = Rech_Image_Defaut($Ident, $Type_Ref);
        if ($image != '') {
            Aff_Img_Redim_Lien($chemin_images_util . $image, 150, 150, 'image_ville');
            echo '<br>' . my_html($titre_img) . '<br><br>' . "\n";
        }

        echo '<br>';
        $larg_titre = 30;
        echo '<table width="70%" class="table_form" align="center">' . "\n";
        echo colonne_titre_tab(LG_ICSV_TOWN_NAME) . $n_ville_html;
        if (($Lat_V != 0) or ($Long_V != 0)) {
            echo '&nbsp;';
            appelle_carte_osm();
        }
        echo '</td></tr>' . "\n";

        echo colonne_titre_tab(LG_ICSV_TOWN_ZIP_CODE) . $cp . '</td></tr>' . "\n";

        echo colonne_titre_tab(LG_COUNTY) . $dep . '</td></tr>' . "\n";

        // Traitement de la position géographique
        if (($Lat_V != 0) or ($Long_V != 0)) {
            echo colonne_titre_tab(LG_ICSV_TOWN_ZIP_LATITUDE) . $Lat_V . '</td></tr>' . "\n";
            echo colonne_titre_tab(LG_ICSV_TOWN_ZIP_LONGITUDE) . $Long_V . '</td></tr>' . "\n";
        }
        echo '</table>';

        if (($cp == '?') or (($Lat_V == 0) and ($Long_V == 0))) {
            echo Affiche_Icone('tip', $LG_tip) . LG_ICSV_TOWN_TIP;
        }

        // Affichage du commentaire
        if (Rech_Commentaire($Ident, $Type_Ref)) {
            echo '<br>';
            Aff_Comment_Fiche($Commentaire, $Diffusion_Commentaire_Internet);
        }

        //  Documents lies à la ville
        echo '<br>' . "\n";
        $x = Aff_Documents_Objet($Ident, 'V', 'O');

        $deb_lien_visu = '<a href="' . $root . '/liste_pers2.php?Type_Liste=';
        $deb_lien_crea = 'href="' . $root . '/edition_personnes_ville.php?evt=';
        $fin_lien = '&amp;idNom=' . $Ident . '&amp;Nom=' . $n_ville_html . '"';

        $_SESSION['NomP'] = $n_ville; // Pour le pdf histoire d'avoir les bons caractères...
        echo '<br>';
        echo $deb_lien_visu . 'N' . $fin_lien . '>' . LG_LPERS_OBJ_N . ' ' . $n_ville_html . '</a>';
        if ($est_contributeur) echo '&nbsp;' . Affiche_Icone_Lien($deb_lien_crea . 'N' . $fin_lien, 'ajouter', LG_ICSV_TOWN_PERS_BORN_CREATE . $n_ville_aff);
        echo '<br>';
        echo $deb_lien_visu . 'M' . $fin_lien . '>' . LG_LPERS_OBJ_M . ' ' . $n_ville_html . '</a><br>';
        echo $deb_lien_visu . 'K' . $fin_lien . '>' . LG_LPERS_OBJ_K . ' ' . $n_ville_html . '</a><br>';
        echo $deb_lien_visu . 'D' . $fin_lien . '>' . LG_LPERS_OBJ_D . ' ' . $n_ville_html . '</a>';
        if ($est_contributeur) echo '&nbsp;' . Affiche_Icone_Lien($deb_lien_crea . 'D' . $fin_lien, 'ajouter', LG_ICSV_TOWN_PERS_DEAD_CREATE . $n_ville_aff);
        echo '<br><br>' . $deb_lien_visu . 'E' . $fin_lien . '>' . LG_ICSV_TOWN_PERS_EVENT . $n_ville_html . '</a><br>';

        //http://localhost/Liste_Villes.php?Type_Liste=V#BasRhin
        // $n_dep = str_replace($interdits,'',$row[1]);
        // echo '&nbsp;&nbsp;&nbsp;<a href="' . $root . '/liste_villes.php?Type_Liste=V#'.$n_dep .'">'.my_html($row[1])."</a>";

        $interdits = array("-", "'", " ");
        $n_ville2 = str_replace($interdits, '', $n_ville);
        echo '<br><a href="' . $root . '/liste_villes.php?Type_Liste=S#' . $n_ville2 . '">' . LG_ICSV_TOWN_SUBDIV . "</a><br><br>";

        if ($est_contributeur)
            echo '<a href="' . $root . '/fusion_villes.php?Ident=' . $Ident . '">' . $LG_Menu_Title['Town_Merging'] . "</a><br>";

        if ($est_contributeur)
            echo '<br><a href="' . $root . '/liste_evenements_zone.php?zone=' . $Ident . '&niveau=4">' . $LG_Menu_Title['Event_List_Area'] . '</a><br>';

        // Recherche de la ville dans les sites gratuits
        if ((!$SiteGratuit) or ($Premium)) {
            // if (($SiteGratuit) and ($Premium)) {
            if ($est_contributeur)
                echo '<br><a href="' . $adr_rech_gratuits_ville . '?ok=ok&amp;NomV=' . $enreg['Nom_Ville'] . '" target="_blank">' . LG_ICSV_TOWN_SEARCH_CLOUD . '</a>' . "\n";
        }

        // Aide à la recherche d'infos sur la ville ==> cp / coordonnées
        if ((!$SiteGratuit) or ($Premium)) {
            // Remplacer ' - et blanc par des _
            $remplaces = array("'", "-", " ");
            $n_ville_uc = str_replace($remplaces, "_", $n_ville);
            // $n_ville_uc= strtr($n_ville_uc,
            // "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
            // "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
            $n_ville_uc = mb_strtoclean($n_ville_uc);
            $n_ville_uc = strtoupper($n_ville_uc);
            if ($cp == '?') $cp = '';
            echo '<br><a href="' . $adr_rech_ville_ref . '?Ville=' . $n_ville_uc . '&CP=' . $cp . '" target="_blank">' . LG_ICSV_TOWN_SEARCH . '</a>';
        }

        // Formulaire pour le bouton retour
        Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);

        Insere_Bas($compl);
    }
}

function mb_strtoclean($string)
{
    // Valeur a nettoyer (conversion)
    $unwanted_array = array(
        'Š' => 'S',
        'š' => 's',
        'Ž' => 'Z',
        'ž' => 'z',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'A',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'B',
        'ß' => 'Ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'a',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ý' => 'y',
        'ý' => 'y',
        'þ' => 'b',
        'ÿ' => 'y',
        // ' ' => '', '_' => '', '-' => '', '.'=> '', ',' => '', ';' => ''
    );

    return mb_strtolower(strtr($string, $unwanted_array));
}

?>
</body>

</html>