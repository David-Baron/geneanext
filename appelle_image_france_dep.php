<?php

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de la variable passée dans l'URL : type de liste
$Type_Liste = Recup_Variable('Type_Liste', 'C', 'NMD');
// Recup de la variable passée dans l'URL : texte ou non
//$texte = Dem_Texte();

switch ($Type_Liste) {
    case 'N':
        $objet = $LG_Img_FR_Birth;
        break;
    case 'M':
        $objet = $LG_Img_FR_Wed;
        break;
    case 'D':
        $objet = $LG_Img_FR_Death;
        break;
    default:
        break;
}

$acces = 'L';                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $objet;            // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = '';

Insere_Haut(my_html($objet), $compl, 'appelle_image_france_dep', $Type_Liste);

echo '<table width="90%">';
echo '<tr><td align="center"><img src="image_depart.php?Type_Liste=' . $Type_Liste . '" alt="carte"/></td></tr>';
echo '</table>';

// Formulaire pour le bouton retour
echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
echo '<br />';
Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
if ($compl != '') {
    echo $compl;
}
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';

?>
</body>

</html>