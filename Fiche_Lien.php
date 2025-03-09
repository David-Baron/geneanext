<?php
//=====================================================================
// Affichage d'un lien
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

$acces = 'L';                        // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Link'];    // Titre pour META
$x = Lit_Env();

// Récupération des variables de l'affichage précédent
$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de la variable passée dans l'URL : référence du lien
$Ref = Recup_Variable('Ref', 'N');

$req_sel = 'select * from ' . nom_table('liens') . ' where Ref_Lien = ' . $Ref . ' limit 1';

require(__DIR__ . '/Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();
else {
    // lien inconnu, circulez...
    if (!$enreg_sel) Retour_Ar();
}

if ($enreg_sel) {
    Champ_car($enreg_sel, 'type_lien');
    Champ_car($enreg_sel, 'description');

    $compl = Ajoute_Page_Info(600, 150);
    if ($est_gestionnaire) {
        $compl .= Affiche_Icone_Lien('href="Edition_Lien.php?Ref=' . $Ref . '"', 'fiche_edition', 'Edition fiche lien') . '&nbsp;';
    }

    Insere_Haut($titre, $compl, 'Fiche_Lien', '');

    $URL = $enreg_sel['URL'];

    echo '<br />' . "\n";
    $larg_titre = 25;
    echo '<table width="70%" class="table_form">' . "\n";

    echo colonne_titre_tab(LG_LINK_TYPE) . $enreg_sel['type_lien'] . '</td></tr>' . "\n";

    echo colonne_titre_tab(LG_LINK_DESCRIPTION) . $enreg_sel['description'] . '</td></tr>' . "\n";

    echo colonne_titre_tab(LG_LINK_URL) . '<a href="' . $URL . '" target="_blank">' . $URL . '</a>' . '</td></tr>' . "\n";

    echo colonne_titre_tab(LG_LINK_AVAIL_HOME);
    if ($enreg_sel['Sur_Accueil']) echo my_html($LG_Yes);
    else echo my_html($LG_No);
    echo '</td></tr>' . "\n";

    colonne_titre_tab(LG_LINK_VISIBILITY);
    if ($enreg_sel['Diff_Internet']) echo my_html($LG_Yes);
    else echo my_html($LG_No);
    echo '</td></tr>' . "\n";

    // Affichage de l'image si présente
    $image = $enreg_sel['image'];
    if ($image != '') {
        echo colonne_titre_tab($LG_Image);
        $image = $chemin_images_util . $image;
        Aff_Img_Redim_Lien($image, 150, 150, "id_" . $Ref);
        echo '<br /><br />' . "\n";
    }

    echo '</table>';

    // Affichage du commentaire
    $Type_Ref = 'L';
    if (Rech_Commentaire($Ref, $Type_Ref)) {
        echo '<br />';
        Aff_Comment_Fiche($Commentaire, $Diffusion_Commentaire_Internet);
    }

    echo '<br />' . "\n";

    // Formulaire pour le bouton retour
    Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);

    Insere_Bas($compl);
}
?>
</body>

</html>