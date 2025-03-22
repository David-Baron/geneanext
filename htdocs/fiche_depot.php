<?php
//=====================================================================
// Affichage d'un dépôt
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('P')) {
    header('Location: ' . $root . '/');
    exit();
}

$titre = $LG_Menu_Title['Repo_Sources'];        // Titre pour META

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$x = Lit_Env();

// Recup de la variable passée dans l'URL : dépôt
$Ident = Recup_Variable('ident', 'N');
$req_sel = 'select * from ' . nom_table('depots') . ' where Ident = ' . $Ident . ' limit 1';

require(__DIR__ . '/../app/ressources/gestion_pages.php');

    // dépôt inconnu, retour... lol
    if (!$enreg_sel) Retour_Ar();

    $enreg2 = $enreg_sel;
    Champ_car($enreg2, 'Nom');
    unset($enr_sel);

    $compl = Ajoute_Page_Info(600, 150);
    if (IS_GRANTED('C')) {
        $compl .= Affiche_Icone_Lien('href="' . $root . '/edition_depot?ident=' . $Ident . '"', 'fiche_edition', $LG_Menu_Title['Repo_Sources_Edit']) . ' ';
    }

    Insere_Haut(my_html($titre), $compl, 'Fiche_Depot', $Ident);

    // Type d'objet des dépôts de sources
    echo '<br />';
    echo '<table width="70%" class="table_form" align="center">' . "\n";
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_CH_REPOSITORY_NAME) . '</td><td class="value">';
    echo $enreg2['Nom'] . '</td></tr>' . "\n";
    echo '</table>';

    //  ===== Affichage du commentaire
    if (Rech_Commentaire($Ident, 'O')) {
        if (($Commentaire != '') and (IS_GRANTED('P') or ($Diffusion_Commentaire_Internet == 'O'))) {
            echo '<fieldset><legend>Note</legend>' . my_html($Commentaire) . '</fieldset><br>' . "\n";
        }
    }

    echo '<br /><a href="' . $root . '/liste_sources?depot=' . $Ident . '">' . LG_CH_REPOSITORY_LIST . '</a>' . "\n";
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