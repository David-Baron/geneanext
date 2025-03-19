<?php

//=====================================================================
// Affichage d'une catégorie
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Category'];    // Titre pour META

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$niv_requis = 'P';
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/../app/ressources/gestion_pages.php');          // Appel de la gestion standard des pages

// Recup de la variable passée dans l'URL : catégorie
$Categ = Recup_Variable('categ', 'N');

$compl = Ajoute_Page_Info(600, 150);

$sql = 'select * from ' . nom_table('categories') . ' where Identifiant = ' . $Categ . ' limit 1';
if ($res = lect_sql($sql)) {
    if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
        $TitreF = my_html($enreg['Titre']);
        $OrdreF = $enreg['Ordre_Tri'];
        $compl .= '<a href="' . $root . '/edition_categorie.php?categ=' . $Categ . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '"></a>' . "\n";

        Insere_Haut(my_html($titre), $compl, 'Fiche_Categorie', $Categ);

        echo '<table width="70%" class="table_form">' . "\n";
        echo '<tr><td colspan="2">&nbsp;</td></tr>';
        echo '<tr><td class="label" width="25%"> ' . ucfirst($LG_Ch_Categ_Title) . ' </td>';
        echo '<td class="value">' . $TitreF . '</td></tr>' . "\n";
        echo '<tr><td class="label" width="25%"> ' . ucfirst($LG_Ch_Categ_Order) . ' </td>';
        echo '<td class="value">' . $OrdreF . '</td></tr>' . "\n";
        echo '<tr><td class="label" width="25%"> ' . ucfirst($LG_Ch_Categ_Image) . ' </td>';
        echo '<td class="value">';
        echo '<img src="' . $root . '/assets/img/' . $Icones['tag_' . $enreg['Image']] . '" border="0" alt="' . $TitreF . '" title="' . $TitreF . '"/>';
        echo '</td></tr>' . "\n";
        echo '</table>' . "\n";

        // Appel de la liste des personnes présentes dans cette catégorie
        $_SESSION['NomP'] = $enreg['Titre']; // Pour le pdf histoire d'avoir les bons caractères...
        echo '<br /><a href="' . $root . '/liste_pers2.php?Type_Liste=C&amp;idNom=' . $enreg['Identifiant'] . '&amp;Nom=' . $TitreF . '">Personnes de cette cat&eacute;gorie</a>' . "\n";

        // Formulaire pour le bouton retour
        Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
    } else {
        // 2 solutions en cas d'absence :
        // - l'utilisateur a saisi un code absent dans l'URL ; le code ne doit pas être saisi dans l'URL, donc tant pis pour lui...
        // - on revient de la mpage de modification et on a demandé la suppression ; donc on renvoye sur la page précédente, à priori la liste
        Retour_Ar();
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
?>
</body>

</html>