<?php
//=====================================================================
// Affichage d'un lien
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

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

require(__DIR__ . '/../app/ressources/gestion_pages.php');

// lien inconnu, circulez...
if (!$enreg_sel) Retour_Ar();

if ($enreg_sel) {
    Champ_car($enreg_sel, 'type_lien');
    Champ_car($enreg_sel, 'description');

    $compl = Ajoute_Page_Info(600, 150);
    if (IS_GRANTED('G')) {
        $compl .= Affiche_Icone_Lien('href="' . $root . '/edition_lien?Ref=' . $Ref . '"', 'fiche_edition', 'Edition fiche lien') . '&nbsp;';
    }

    Insere_Haut($titre, $compl, 'Fiche_Lien', '');

    echo '<br />' . "\n";
    echo '<table width="70%" class="table_form">' . "\n";
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_LINK_TYPE) . '</td><td class="value">' . $enreg_sel['type_lien'] . '</td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_LINK_DESCRIPTION) . '</td><td class="value">' . $enreg_sel['description'] . '</td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst($LG_LINK_URL) . '</td><td class="value"><a href="' . $enreg_sel['URL'] . '" target="_blank">' . $enreg_sel['URL'] . '</a></td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_LINK_AVAIL_HOME) . '</td><td class="value">';
    if ($enreg_sel['Sur_Accueil']) echo $LG_Yes;
    else echo $LG_No;
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_LINK_VISIBILITY) . '</td><td class="value">';
    if ($enreg_sel['Diff_Internet']) echo $LG_Yes;
    else echo $LG_No;
    echo '</td></tr>' . "\n";

    // Affichage de l'image si présente
    $image = $enreg_sel['image'];
    if ($image != '') {
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Image) . '</td><td class="value">';
        $image = $chemin_images_util . $image;
        Aff_Img_Redim_Lien($image, 150, 150, "id_" . $Ref);
        echo '<br /><br />' . "\n";
    }

    echo '</table>';

    // Affichage du commentaire
    $Type_Ref = 'L';
    if (Rech_Commentaire($Ref, $Type_Ref)) {
        echo '<br />';
        if (($Commentaire != '') and (IS_GRANTED('P') or ($Diffusion_Commentaire_Internet == 'O'))) {
            echo '<fieldset><legend>Note</legend>' . my_html($Commentaire) . '</fieldset><br>' . "\n";
        }
    }

    echo '<br />' . "\n";
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
}
?>
</body>

</html>