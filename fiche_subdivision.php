<?php
//=====================================================================
// Fiche d'une Subdivison
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Subdiv'];    // Titre pour META

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de'identifiant de la Subdivision passé dans l'URL
$Ident = Recup_Variable('Ident', 'N');

$req_sel = 'SELECT s.*, v.Nom_Ville, v.Identifiant_zone as id_Ville FROM ' . nom_table('subdivisions') . ' s, ' . nom_table('villes') . ' v' .
    ' WHERE s.Identifiant_zone = ' . $Ident .
    ' AND v.Identifiant_zone = s.Zone_Mere limit 1';

$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

    // 2 solutions en cas d'absence :
    // - l'utilisateur a saisi un code absent dans l'URL ; le code ne doit pas être saisi dans l'URL, donc tant pis pour lui...
    // - on revient de la mpage de modification et on a demandé la suppression ; donc on renvoye sur la page précédente, à priori la liste
    if ((!$enreg_sel) or ($Ident == 0)) Retour_Ar();

    else {

        $enreg = $enreg_sel;
        $enreg2 = $enreg;

        $compl = Ajoute_Page_Info(600, 150);
        if ($est_gestionnaire) {
            $compl = Affiche_Icone_Lien('href="' . $root . '/edition_subdivision.php?Ident=' . $Ident . '"', 'fiche_edition', my_html($LG_Menu_Title['Subdiv_Edit'])) . '&nbsp;';
        }
        Insere_Haut($titre, $compl, 'Fiche_Subdivision', '');

        $Type_Ref = 'S';

        $n_subdivision = $enreg['Nom_Subdivision'];
        $n_subdivision_html = my_html($n_subdivision);
        $n_subdivision_aff = stripslashes($n_subdivision);

        $ville = $enreg['Nom_Ville'];
        if ($ville == '') $ville = '?';
        //else $ville = my_html($ville);
        else $ville = '<a href="' . $root . '/fiche_ville.php?Ident=' . $enreg['id_Ville'] . '">' . my_html($ville) . '</a>';

        //http://localhost/Liste_Villes.php?Type_Liste=V#BasRhin
        // $n_dep = str_replace($interdits,'',$row[1]);
        // echo '&nbsp;&nbsp;&nbsp;<a href="' . $root . '/liste_villes.php?Type_Liste=V#'.$n_dep .'">'.my_html($row[1])."</a>";

        $Lat_V = $enreg['Latitude'];
        $Long_V = $enreg['Longitude'];

        // Affichage de l'image par défaut pour la subdivision
        $image = Rech_Image_Defaut($Ident, $Type_Ref);
        if ($image != '') {
            Aff_Img_Redim_Lien($chemin_images_util . $image, 150, 150, 'image_subdiv');
            echo '<br />' . my_html($titre_img) . '<br /><br />' . "\n";
        }

        echo '<br />';
        $larg_titre = 30;
        echo '<table width="70%" class="table_form" align="center">' . "\n";
        echo colonne_titre_tab(LG_SUBDIV_NAME) . $n_subdivision_html;
        if (($Lat_V != 0) or ($Long_V != 0)) {
            echo '&nbsp;';
            appelle_carte_osm();
        }
        echo '</td></tr>' . "\n";

        echo colonne_titre_tab(LG_SUBDIV_TOWN) . $ville . '</td></tr>' . "\n";

        // Traitement de la position géographique
        if (($Lat_V != 0) or ($Long_V != 0)) {
            echo colonne_titre_tab(LG_SUBDIV_ZIP_LATITUDE) . $Lat_V . '</td></tr>' . "\n";
            echo colonne_titre_tab(LG_SUBDIV_ZIP_LONGITUDE) . $Long_V . '</td></tr>' . "\n";
        }
        echo '</table>';

        // Affichage du commentaire
        if (Rech_Commentaire($Ident, $Type_Ref)) {
            echo '<br />';
            Aff_Comment_Fiche($Commentaire, $Diffusion_Commentaire_Internet);
        }

        //  Documents lies à la ville
        echo '<br />' . "\n";
        $x = Aff_Documents_Objet($Ident, $Type_Ref, 'O');

        // Formulaire pour le bouton retour
        Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);

        Insere_Bas($compl);
    }
}
?>
</body>

</html>