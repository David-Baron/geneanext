<?php

//=====================================================================
// Affichage d'une requête
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Request'];       // Titre pour META
$niv_requis = 'P';
$x = Lit_Env();

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de la variable passée dans l'URL : catégorie
$reference = Recup_Variable('reference', 'N');

$req_sel = 'select Titre, Criteres, Code_SQL from ' . nom_table('requetes') . ' where Reference = ' . $reference . ' limit 1';

require(__DIR__ . '/../app/ressources/gestion_pages.php');

// requête inconnue, retour...
if (!$enreg_sel) Retour_Ar();

$enreg = $enreg_sel;

$compl = Ajoute_Page_Info(600, 150);

// Possibilité de venir en modification pour les gestionnaires
if ($est_gestionnaire)
    $compl .= '<a href="' . $root . '/edition_requete.php?reference=' . $reference . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_Menu_Title['Request_Edit'] . '" title="' . $LG_Menu_Title['Request_Edit'] . '"></a>' . "\n";

Insere_Haut(my_html($titre), $compl, 'Fiche_Requete', $reference);

echo '<br />';
echo '<table width="80%" class="table_form">' . "\n";
echo '<tr><td class="label" width="25%">' . ucfirst(LG_QUERY_TITLE) . '</td><td class="value">' . my_html($enreg['Titre']) . '</td></tr>';

$liste_crit = explode($separ, $enreg['Criteres']);
$nb_crit = count($liste_crit);
if ($nb_crit > 0) {
    for ($nb = 0; $nb < $nb_crit - 1; $nb++) {
        $exp_crit = explode('=', $liste_crit[$nb]);
        echo '<tr><td class="label" width="25%">' . ucfirst(trim($exp_crit[0])) . '</td><td class="value">' . my_html(trim($exp_crit[1])) . '</td></tr>';
    }
}
echo '<tr><td class="label" width="25%">' . ucfirst(LG_QUERY_CODE) . '</td><td class="value">' . $enreg['Code_SQL'] . '</td></tr>';
echo '</table>' . "\n";

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '?' . Query_Str());

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