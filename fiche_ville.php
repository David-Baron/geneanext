<?php
//=====================================================================
// Fiche d'une ville
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

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
if ((!$enreg_sel) or ($Ident == 0)) Retour_Ar();

// 2 solutions en cas d'absence :
// - l'utilisateur a saisi un code absent dans l'URL ; le code ne doit pas être saisi dans l'URL, donc tant pis pour lui...
// - on revient de la mpage de modification et on a demandé la suppression ; donc on renvoye sur la page précédente, à priori la liste

$enreg = $enreg_sel;
$enreg2 = $enreg;

$compl = Ajoute_Page_Info(600, 150);
if ($est_gestionnaire) {
    $compl = Affiche_Icone_Lien('href="' . $root . '/edition_ville.php?Ident=' . $Ident . '"', 'fiche_edition', $LG_Menu_Title['Town_Edit']) . '&nbsp;';
}
Insere_Haut($titre, $compl, 'Fiche_Ville', '');

$Type_Ref = 'V';

$n_ville = $enreg['Nom_Ville'];
$n_ville_html = my_html($enreg['Nom_Ville']);
$n_ville_aff = stripslashes($enreg['Nom_Ville']);

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
echo '<table width="70%" class="table_form" align="center">' . "\n";
echo '<tr><td class="label" width="30%">' . ucfirst(LG_ICSV_TOWN_NAME) . '</td><td class="value">' . my_html($enreg['Nom_Ville']);
if (($Lat_V != 0) or ($Long_V != 0)) {
    echo '<a href="http://www.openstreetmap.org/?lat=' . $Lat_V . '&amp;lon=' . $Long_V . '&amp;mlat=' . $Lat_V . '&amp;mlon=' . $Long_V . '&amp;zoom=10" target="_blank"><img src="' . $root . '/assets/img/' . $Icones['map_go'] . '" alt="' . $LG_Show_On_Map . '" title="' . $LG_Show_On_Map . '"></a>';
}
echo '</td></tr>' . "\n";
echo '<tr><td class="label" width="30%">' . ucfirst(LG_ICSV_TOWN_ZIP_CODE) . '</td><td class="value">' . $cp . '</td></tr>';
echo '<tr><td class="label" width="30%">' . ucfirst(LG_COUNTY) . '</td><td class="value">' . $dep . '</td></tr>';
// Traitement de la position géographique
if (($Lat_V != 0) or ($Long_V != 0)) {
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_ICSV_TOWN_ZIP_LATITUDE) . '</td><td class="value">' . $Lat_V . '</td></tr>';
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_ICSV_TOWN_ZIP_LONGITUDE) . '</td><td class="value">' . $Long_V . '</td></tr>';
}
echo '</table>';

if (($cp == '?') or (($Lat_V == 0) and ($Long_V == 0))) {
    echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . my_html($LG_tip) . '" title="' . my_html($LG_tip) . '">' . LG_ICSV_TOWN_TIP;
}

// Affichage du commentaire
if (Rech_Commentaire($Ident, $Type_Ref)) {
    echo '<br>';
    if (($Commentaire != '') and (($est_privilegie) or ($Diffusion_Commentaire_Internet == 'O'))) {
        echo '<fieldset><legend>Note</legend>' . my_html($Commentaire) . '</fieldset><br>' . "\n";
    }
}

//  Documents lies à la ville

$x = Aff_Documents_Objet($Ident, 'V', 'O');
$_SESSION['NomP'] = my_html($enreg['Nom_Ville']); // Pour le pdf histoire d'avoir les bons caractères...
$interdits = array("-", "'", " ");

echo '<br>' . "\n";
echo '<br>';
echo '<a href="' . $root . '/liste_pers2.php?Type_Liste=N&amp;idNom=' . $Ident . '&amp;Nom=' . my_html($enreg['Nom_Ville']) . '">' . LG_LPERS_OBJ_N . ' ' . my_html($enreg['Nom_Ville']) . '</a>';
if ($est_contributeur) {
    echo '&nbsp;' . Affiche_Icone_Lien('href="' . $root . '/edition_personnes_ville.php?evt=N&amp;idNom=' . $Ident . '&amp;Nom=' . my_html($enreg['Nom_Ville']) . '"', 'ajouter', LG_ICSV_TOWN_PERS_BORN_CREATE . my_html($enreg['Nom_Ville']));
}
echo '<br>';
echo '<a href="' . $root . '/liste_pers2.php?Type_Liste=M&amp;idNom=' . $Ident . '&amp;Nom=' . my_html($enreg['Nom_Ville']) . '">' . LG_LPERS_OBJ_M . ' ' . my_html($enreg['Nom_Ville']) . '</a><br>';
echo '<a href="' . $root . '/liste_pers2.php?Type_Liste=K&amp;idNom=' . $Ident . '&amp;Nom=' . my_html($enreg['Nom_Ville']) . '">' . LG_LPERS_OBJ_K . ' ' . my_html($enreg['Nom_Ville']) . '</a><br>';
echo '<a href="' . $root . '/liste_pers2.php?Type_Liste=D&amp;idNom=' . $Ident . '&amp;Nom=' . my_html($enreg['Nom_Ville']) . '">' . LG_LPERS_OBJ_D . ' ' . my_html($enreg['Nom_Ville']) . '</a>';
if ($est_contributeur) {
    echo '&nbsp;' . Affiche_Icone_Lien('href="' . $root . '/edition_personnes_ville.php?evt=D&amp;idNom=' . $Ident . '&amp;Nom=' . my_html($enreg['Nom_Ville']) . '"', 'ajouter', LG_ICSV_TOWN_PERS_DEAD_CREATE . my_html($enreg['Nom_Ville']));
}
echo '<br><br><a href="' . $root . '/liste_pers2.php?Type_Liste=E&amp;idNom=' . $Ident . '&amp;Nom=' . my_html($enreg['Nom_Ville']) . '">' . LG_ICSV_TOWN_PERS_EVENT . my_html($enreg['Nom_Ville']) . '</a><br>';

$n_ville2 = str_replace($interdits, '', my_html($enreg['Nom_Ville']));
echo '<br><a href="' . $root . '/liste_villes.php?Type_Liste=S#' . $n_ville2 . '">' . LG_ICSV_TOWN_SUBDIV . "</a><br><br>";
if ($est_contributeur) {
    echo '<a href="' . $root . '/fusion_villes.php?Ident=' . $Ident . '">' . $LG_Menu_Title['Town_Merging'] . "</a><br>";
    echo '<br><a href="' . $root . '/liste_evenements_zone.php?zone=' . $Ident . '&niveau=4">' . $LG_Menu_Title['Event_List_Area'] . '</a><br>';
    if ((!$SiteGratuit) or ($Premium)) { // Recherche de la ville dans les sites gratuits
        echo '<br><a href="' . $adr_rech_gratuits_ville . '?ok=ok&amp;NomV=' . $enreg['Nom_Ville'] . '" target="_blank">' . LG_ICSV_TOWN_SEARCH_CLOUD . '</a>' . "\n";
    }
}

// Aide à la recherche d'infos sur la ville ==> cp / coordonnées
if ((!$SiteGratuit) or ($Premium)) {
    // Remplacer ' - et blanc par des _
    $remplaces = array("'", "-", " ");
    $n_ville_uc = str_replace($remplaces, "_", my_html($enreg['Nom_Ville']));
    // $n_ville_uc= strtr($n_ville_uc,
    // "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
    // "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
    $n_ville_uc = mb_strtoclean($n_ville_uc);
    $n_ville_uc = strtoupper($n_ville_uc);
    if ($cp == '?') $cp = '';
    echo '<br><a href="' . $adr_rech_ville_ref . '?Ville=' . $n_ville_uc . '&CP=' . $cp . '" target="_blank">' . LG_ICSV_TOWN_SEARCH . '</a>';
}

Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';

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