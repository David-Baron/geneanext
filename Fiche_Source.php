<?php
//=====================================================================
// Affichage d'une source
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

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

require(__DIR__ . '/Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {
    // source inconnue, retour...
    if (!$enreg_sel)
        Retour_Ar();
    else {
        $Adresse_Web = $enreg_sel['Adresse_Web'];
        $Fiabilite_Source = $enreg_sel['Fiabilite_Source'];
        $compl = Ajoute_Page_Info(600, 150);
        if ($est_contributeur) {
            $compl .= Affiche_Icone_Lien('href="Edition_Source.php?ident=' . $Ident . '"', 'fiche_edition', $LG_Menu_Title['Source_Edit']) . '&nbsp;';
        }

        Insere_Haut($titre, $compl, 'Fiche_Source', $Ident);

        // Type d'objet des sources
        $Type_Ref = 'S';

        $larg_titre = 25;
        $fin_ligne = '</td></tr>' . "\n";
        echo '<br />';
        echo '<table width="70%" class="table_form" align="center">' . "\n";
        echo colonne_titre_tab(LG_SRC_TITLE) . $enreg_sel['Titre'] . $fin_ligne;
        echo colonne_titre_tab(LG_SRC_AUTHOR) . $enreg_sel['Auteur'] . $fin_ligne;
        echo colonne_titre_tab(LG_SRC_CLASS) . $enreg_sel['Classement'] . $fin_ligne;
        echo colonne_titre_tab(LG_SRC_REPO) . '<a href="Fiche_Depot.php?ident=' . $enreg_sel['Ident_Depot'] . '">' . $enreg_sel['Nom'] . '</a>' . $fin_ligne;
        echo colonne_titre_tab(LG_SRC_REFER) . $enreg_sel['Cote'] . $fin_ligne;
        echo colonne_titre_tab(LG_SRC_WEB);
        if ($Adresse_Web != '') echo '<a href="' . $Adresse_Web . '">' . $Adresse_Web . '</a>';
        echo $fin_ligne;
        echo colonne_titre_tab(LG_SRC_TRUST);
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
        echo $fin_ligne;
        echo '</table>';

        //  ===== Affichage du commentaire
        if (Rech_Commentaire($Ident, $Type_Ref)) {
            Aff_Comment_Fiche($Commentaire, $Diffusion_Commentaire_Internet);
        }

        // Formulaire pour le bouton retour
        Bouton_Retour($lib_Retour, '?' . Query_Str());

        Insere_Bas($compl);
    }
}
?>
</body>

</html>