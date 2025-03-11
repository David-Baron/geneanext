<?php

//=====================================================================
//   Affichage d'une actualité (évènement spécialisé)
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['New'];        // Titre pour META

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');
// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup de la variable passée dans l'URL : référence de l'actualité
$refPar = Recup_Variable('refPar', 'N');

// Pour les sites gratuits non Premium, 	 actualités sont centralisées
$centralise = false;
if (($SiteGratuit) and (!$Premium)) $centralise = true;

$compl = Ajoute_Page_Info(600, 150);
if (($est_gestionnaire) and (!$centralise)) {
    $compl .= Affiche_Icone_Lien('href="'. $root .'/edition_evenement.php?refPar=' . $refPar . '&amp;actu=o"', 'fiche_edition', $LG_Menu_Title['New_Edit']) . '&nbsp;';
}

Insere_Haut($titre, $compl, 'Fiche_Actualite', '');

echo '<br />';

$memo_pref = $pref_tables;
if ($centralise) $pref_tables = 'gra_sg_';
$requete = 'SELECT Identifiant_zone, Identifiant_Niveau, Code_Type, Titre, Debut, Fin' .
    ' FROM ' . nom_table('evenements') .
    ' WHERE reference = ' . $refPar .
    ' LIMIT 1';
$pref_tables = $memo_pref;

$result = lect_sql($requete);
$enreg = $result->fetch(PDO::FETCH_ASSOC);
$nomZone = LectZone($enreg['Identifiant_zone'], $enreg['Identifiant_Niveau']);

// Affichage de l'image par défaut
$Type_Ref = 'E';
$image = Rech_Image_Defaut($refPar, $Type_Ref);
if ($image != '') {
    Aff_Img_Redim_Lien($chemin_images_util . $image, 150, 150, 'image_evt');
    echo '<br />' . my_html($titre_img) . '<br /><br />' . "\n";
}

echo '<br />';
$larg_titre = 25;
echo '<table width="70%" class="table_form" align="center">' . "\n";

echo colonne_titre_tab($LG_Event_Title) . my_html($enreg['Titre']) . '</td></tr>';

if ($nomZone != '	') {
    colonne_titre_tab($LG_Event_Where);
    echo $nomZone . '</td></tr>';
}

colonne_titre_tab($LG_Event_When);
echo Etend_2_dates($enreg['Debut'], $enreg['Fin']) . '</td></tr>';

echo '</table>' . "\n";

//  ===== Affichage du commentaire
if (Rech_Commentaire($refPar, $Type_Ref)) {
    Aff_Comment_Fiche($Commentaire, $Diffusion_Commentaire_Internet);
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
Insere_Bas($compl);

?>
</body>

</html>