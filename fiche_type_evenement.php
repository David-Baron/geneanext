<?php

//=====================================================================
// Affichage d'un type d'évènement
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Event_Type'];       // Titre pour META
$niv_requis = 'P';

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
$Code = Recup_Variable('code', 'A');

$req_sel = 'select * from ' . nom_table('types_evenement') . ' where Code_Type = \'' . $Code . '\' limit 1';

require(__DIR__ . '/app/ressources/gestion_pages.php');

if ($bt_An) Retour_Ar();

// type inconnu, retour...
if (!$enreg_sel) Retour_Ar();

$enreg2 = $enreg_sel;
Champ_car($enreg2, 'Libelle_Type');

$compl = Ajoute_Page_Info(600, 150);

if ($enreg2['Code_Modifiable'] == 'O') {
    $compl .= '<a href="' . $root . '/edition_type_evenement.php?code=' . $Code . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>' . "\n";
}

Insere_Haut($titre, $compl, 'Fiche_Type_Evenement', $Code);

echo '<br />';
echo '<table width="80%" class="table_form">' . "\n";
echo '<tr><td class="label" width="25%">' . ucfirst(LG_EVENT_TYPE_CODE) . '</td><td class="value">';
echo $enreg2['Code_Type'] . '</td></tr>' . "\n";
echo '<tr><td class="label" width="25%">' . ucfirst(LG_EVENT_TYPE_LABEL) . '</td><td class="value">';
echo $enreg2['Libelle_Type'] . '</td></tr>' . "\n";
echo '<tr><td class="label" width="25%">' . ucfirst(LG_EVENT_TYPE_IS_MOD) . '</td><td class="value">';
switch ($enreg2['Code_Modifiable']) {
    case 'O':
        echo $LG_Yes;
        break;
    case 'N':
        echo $LG_No;
        break;
    default:
        echo '?';
        break;
}
echo '</td></tr>' . "\n";
echo '<tr><td class="label" width="25%">' . ucfirst(LG_TARGET_OBJECT) . '</td><td class="value">';
echo lib_pfu($enreg2['Objet_Cible']) . '</td></tr>' . "\n";
echo '<tr><td class="label" width="25%">' . ucfirst(LG_EVENT_TYPE_UNIQ) . '</td><td class="value">';
switch ($enreg2['Unicite']) {
    case 'U':
        echo $LG_Yes;
        break;
    case 'M':
        echo $LG_No;
        break;
    default:
        echo '?';
        break;
}
echo '</td></tr>' . "\n";
echo '<tr><td class="label" width="25%">' . ucfirst(LG_EVENT_TYPE_GEDCOM) . '</td><td class="value">';
switch ($enreg2['Type_Gedcom']) {
    case 'O':
        echo $LG_Yes;
        break;
    case 'N':
        echo $LG_No;
        break;
    default:
        echo '?';
        break;
}
echo '</td></tr>' . "\n";
echo '</table>' . "\n";
echo '<br><a href="' . $root . '/liste_evenements.php?tev=' . $Code . '">' . LG_EVENT_TYPE_EVENTS . '</a>';
Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
echo '<br />' . "\n";
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