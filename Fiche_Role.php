<?php
//=====================================================================
// Affichage d'un rôle
//=====================================================================

// Gestion standard des pages
session_start();
include('fonctions.php');
$acces = 'L';                        // Type d'accès de la page : (M)ise à jour, (L)ecture
$niv_requis = 'P';
$titre = $LG_Menu_Title['Role'];        // Titre pour META

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

// Recup de la variable passée dans l'URL : rôle
$Code = Recup_Variable('code', 'A');
$req_sel = 'select * from ' . nom_table('roles') . ' where Code_Role = "' . $Code . '" limit 1';

include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();
else {
    // Rôle inconnu, retour...
    if (!$enreg_sel) Retour_Ar();
}

if ($enreg_sel) {
    $enreg2 = $enreg_sel;
    Champ_car($enreg2, 'Libelle_Role');
    Champ_car($enreg2, 'Libelle_Inv_Role');

    $compl = Ajoute_Page_Info(600, 150);
    if ($est_gestionnaire) {
        $compl .= Affiche_Icone_Lien('href="Edition_Role.php?code=' . $Code . '"', 'fiche_edition', 'Edition rôle') . '&nbsp;';
    }

    Insere_Haut($titre, $compl, 'Fiche_Role', $Code);

    $larg_titre = 25;
    $c_role = $enreg2['Code_Role'];
    echo '<br />';
    echo '<table width="70%" class="table_form" align="center">' . "\n";
    echo col_titre_tab(LG_ROLE_CODE, $larg_titre) . '<td class="value">' . $c_role . '</td></tr>' . "\n";
    echo col_titre_tab(LG_ROLE_SYM, $larg_titre) . '<td class="value">';
    if ($enreg2['Symetrie'] == 'O')
        echo $LG_Yes;
    else
        echo $LG_No;
    echo '</td></tr>' . "\n";
    echo col_titre_tab(LG_ROLE_LABEL, $larg_titre) . '<td class="value">' . $enreg2['Libelle_Role'] . '</td></tr>' . "\n";
    echo col_titre_tab(LG_ROLE_OPPOS_LABEL, $larg_titre) . '<td class="value">' . $enreg2['Libelle_Inv_Role'] . '</td></tr>' . "\n";
    echo '</table>';

    // Appel de la liste des personnes pour ce rôle
    echo '<br /><a href="Liste_Pers_Role.php?Role=' . $c_role . '">' . LG_ROLE_PERSONS . '</a>' . "\n";

    // Formulaire pour le bouton retour
    Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);

    Insere_Bas($compl);
}
?>
</body>

</html>