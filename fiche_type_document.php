<?php

//=====================================================================
// Affichage d'un type de document 
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$acces = 'L';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Fiche d\'un type de document';    // Titre pour META

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$niv_requis = 'G';        // Fonction réservée au gestionnaire
$x = Lit_Env();

// Recup de la variable passée dans l'URL : type de document
$Code = Recup_Variable('code', 'N');

$req_sel = 'select * from ' . nom_table('types_doc') . ' where Id_Type_Document = \'' . $Code . '\' limit 1';

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

    // type inconnu, retour...
    if (!$enreg_sel) Retour_Ar();

    $enreg2 = $enreg_sel;
    Champ_car($enreg2, 'Libelle_Type');

    $compl = Ajoute_Page_Info(600, 150);
    $compl .= '<a href="' . $root . '/edition_type_document.php?code=' . $Code . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '"></a>' . "\n";

    Insere_Haut($titre, $compl, 'Fiche_Type_Document', $Code);

    echo '<br />';
    echo '<table width="70%" class="table_form">' . "\n";
    echo '<tr><td class="label" width="25%">' . ucfirst(LG_DOC_TYPE_LABEL) . '</td><td class="value">';
    echo $enreg2['Libelle_Type'] . '</td></tr>' . "\n";

    echo '</table><br />' . "\n";

    //Affichage des documents du type
    $req = 'select  Id_Document, Nature_Document, Titre from ' . nom_table('documents') . ' where Id_Type_Document = ' . $Code;
    $result = lect_sql($req);
    if ($result->rowCount() > 0) {
        $icone_mod = '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" border="0" alt="' . $LG_modify . '"/>';
        echo '<br />' . "\n";
        echo '<fieldset>';
        echo '<legend>' . ucfirst(LG_DOC_DOCS) . '</legend>' . "\n";
        while ($enreg = $result->fetch(PDO::FETCH_NUM)) {
            echo '<a href="' . $root . '/fiche_document.php?Reference=' . $enreg[0] . '">'
                . my_html($enreg[2]) . ' (' . $Natures_Docs[$enreg[1]] . ')</a>'
                . '&nbsp;<a href="' . $root . '/edition_document.php?Reference=' . $enreg[0] . $icone_mod . '</a><br />'
                . "\n";
        }
        echo '</fieldset>' . "\n";
    }

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