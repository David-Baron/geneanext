<?php
//=====================================================================
// Affichage d'une source
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$niv_requis = 'P';
$titre = $LG_Menu_Title['Source'];        // Titre pour META

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

// Recup de la variable passée dans l'URL : source
$Ident = Recup_Variable('ident', 'N');

$req_sel = 'select s.*, d.Nom from ' . nom_table('sources') . ' s left outer join ' .  nom_table('depots') . ' d'
    . ' on s.Ident_Depot = d.Ident '
    . ' where s.Ident = ' . $Ident
    . ' limit 1';

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// source inconnue, retour...
if (!$enreg_sel)
    Retour_Ar();
else {
    $Adresse_Web = $enreg_sel['Adresse_Web'];
    $Fiabilite_Source = $enreg_sel['Fiabilite_Source'];
    $compl = Ajoute_Page_Info(600, 150);
    if ($est_contributeur) {
        $compl .= Affiche_Icone_Lien('href="' . $root . '/edition_source.php?ident=' . $Ident . '"', 'fiche_edition', $LG_Menu_Title['Source_Edit']) . '&nbsp;';
    }

    Insere_Haut($titre, $compl, 'Fiche_Source', $Ident);

    echo '<br />';
    echo '<table width="70%" class="table_form" align="center">' . "\n";
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_SRC_TITLE) . '</td><td class="value">' . $enreg_sel['Titre'] . '</td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_SRC_AUTHOR) . '</td><td class="value">' . $enreg_sel['Auteur'] . '</td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_SRC_CLASS) . '</td><td class="value">' . $enreg_sel['Classement'] . '</td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_SRC_REPO) . '</td><td class="value"><a href="' . $root . '/fiche_depot.php?ident=' . $enreg_sel['Ident_Depot'] . '">' . $enreg_sel['Nom'] . '</a></td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_SRC_REFER) . '</td><td class="value">' . $enreg_sel['Cote'] . '</td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_SRC_WEB) . '</td><td class="value">' . '</td></tr>';
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_SRC_TRUST) . '</td><td class="value">';
    switch ($Fiabilite_Source) {
        case 'H':
            echo LG_SRC_TRUST_H;
            break;
        case 'M':
            echo LG_SRC_TRUST_M;
            break;
        case 'F':
            echo LG_SRC_TRUST_L;
            break;
        default:
            echo '?';
    }
    echo '</td></tr>';
    echo '</table>';
    if (Rech_Commentaire($Ident, 'S')) {
        if (($Commentaire != '') and (($est_privilegie) or ($Diffusion_Commentaire_Internet == 'O'))) {
            echo '<fieldset><legend>Note</legend>' . my_html($Commentaire) . '</fieldset><br>' . "\n";
        }
    }

    Bouton_Retour($lib_Retour, '?' . Query_Str());
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
} ?>
</body>

</html>