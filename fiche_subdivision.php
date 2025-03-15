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

// 2 solutions en cas d'absence :
// - l'utilisateur a saisi un code absent dans l'URL ; le code ne doit pas être saisi dans l'URL, donc tant pis pour lui...
// - on revient de la mpage de modification et on a demandé la suppression ; donc on renvoye sur la page précédente, à priori la liste
if ((!$enreg_sel) or ($Ident == 0)) Retour_Ar();

$compl = Ajoute_Page_Info(600, 150);
if ($est_gestionnaire) {
    $compl = Affiche_Icone_Lien('href="' . $root . '/edition_subdivision.php?Ident=' . $Ident . '"', 'fiche_edition', my_html($LG_Menu_Title['Subdiv_Edit'])) . '&nbsp;';
}
Insere_Haut($titre, $compl, 'Fiche_Subdivision', '');

$ville = '?';
if ($enreg['Nom_Ville'] !== '') {
    $ville = '<a href="' . $root . '/fiche_ville.php?Ident=' . $enreg['id_Ville'] . '">' . my_html($enreg['Nom_Ville']) . '</a>';
}

// Affichage de l'image par défaut pour la subdivision
$image = Rech_Image_Defaut($Ident, 'S');
if ($image != '') {
    Aff_Img_Redim_Lien($chemin_images_util . $image, 150, 150, 'image_subdiv');
    echo '<br />' . my_html($titre_img) . '<br /><br />' . "\n";
}

echo '<br />';
echo '<table width="70%" class="table_form" align="center">' . "\n";
echo '<tr><td class="label" width="30%">' . ucfirst(LG_SUBDIV_NAME) . '</td><td class="value">' . my_html($enreg['Nom_Subdivision']);
if (($enreg['Latitude'] != 0) or ($enreg['Longitude'] != 0)) {
    echo '<a href="http://www.openstreetmap.org/?lat=' . $enreg['Latitude'] . '&amp;lon=' . $enreg['Longitude'] . '&amp;mlat=' . $enreg['Latitude'] . '&amp;mlon=' . $enreg['Longitude'] . '&amp;zoom=10" target="_blank"><img src="' . $root . '/assets/img/' . $Icones['map_go'] . '" alt="' . $LG_Show_On_Map . '" title="' . $LG_Show_On_Map . '"></a>';
}
echo '</td></tr>' . "\n";
echo '<tr><td class="label" width="30%">' . ucfirst(LG_SUBDIV_TOWN) . '</td><td class="value">' . $ville . '</td></tr>';

// Traitement de la position géographique
if (($enreg['Latitude'] != 0) or ($enreg['Longitude'] != 0)) {
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_SUBDIV_ZIP_LATITUDE) . '</td><td class="value">' . $enreg['Latitude'] . '</td></tr>';
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_SUBDIV_ZIP_LONGITUDE) . '</td><td class="value">' . $enreg['Longitude'] . '</td></tr>';
}
echo '</table>';
if (Rech_Commentaire($Ident, 'S')) {
    echo '<br />';
    if (($Commentaire != '') and (($est_privilegie) or ($Diffusion_Commentaire_Internet == 'O'))) {
        echo '<fieldset><legend>Note</legend>' . my_html($Commentaire) . '</fieldset><br>' . "\n";
    }
}

echo '<br />' . "\n";
Aff_Documents_Objet($Ident, 'S', 'O');
Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>