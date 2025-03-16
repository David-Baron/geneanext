<?php
//=====================================================================
// Affichage d'un rôle
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

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

require(__DIR__ . '/app/ressources/gestion_pages.php');

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
        $compl .= Affiche_Icone_Lien('href="' . $root . '/edition_role.php?code=' . $Code . '"', 'fiche_edition', 'Edition rôle') . '&nbsp;';
    }

    Insere_Haut($titre, $compl, 'Fiche_Role', $Code);

    $c_role = $enreg2['Code_Role'];
    
    echo '<br />';
    echo '<table width="70%" class="table_form" align="center">' . "\n";
    echo '<tr><td class="label" width="25%"> ' . ucfirst(LG_ROLE_CODE) . ' </td><td class="value">' . $c_role . '</td></tr>';
    echo '<tr><td class="label" width="25%"> ' . ucfirst(LG_ROLE_SYM) . ' </td><td class="value">';
    if ($enreg2['Symetrie'] == 'O'){
        echo $LG_Yes;}
    else{
        echo $LG_No;}
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="25%"> ' . ucfirst(LG_ROLE_LABEL) . ' </td><td class="value">' . $enreg2['Libelle_Role'] . '</td></tr>';
    echo '<tr><td class="label" width="25%"> ' . ucfirst(LG_ROLE_OPPOS_LABEL) . ' </td><td class="value">' . $enreg2['Libelle_Inv_Role'] . '</td></tr>';
    echo '</table>';

    // Appel de la liste des personnes pour ce rôle
    echo '<br /><a href="' . $root . '/liste_pers_role.php?Role=' . $c_role . '">' . LG_ROLE_PERSONS . '</a>' . "\n";

    // Formulaire pour le bouton retour
    Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);

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