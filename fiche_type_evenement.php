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

// Recup de la variable passée dans l'URL : type d'évènement
$Code = Recup_Variable('code', 'A');

$req_sel = 'select * from ' . nom_table('types_evenement') . ' where Code_Type = \'' . $Code . '\' limit 1';

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

    // type inconnu, retour...
    if (!$enreg_sel) Retour_Ar();

    $enreg2 = $enreg_sel;
    Champ_car($enreg2, 'Libelle_Type');

    $compl = Ajoute_Page_Info(600, 150);
    if ($enreg2['Code_Modifiable'] == 'O') {
        $compl .= '<a href="' . $root . '/edition_type_evenement.php?code=' . $Code . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '"></a>' . "\n";
    }

    Insere_Haut($titre, $compl, 'Fiche_Type_Evenement', $Code);

    $hLG_Yes = my_html($LG_Yes);
    $hLG_No  = my_html($LG_No);
    $hLG_Q   = '?';

    echo '<br />';
    $larg_titre = 25;
    echo '<table width="80%" class="table_form">' . "\n";

    colonne_titre_tab(LG_EVENT_TYPE_CODE);
    echo $enreg2['Code_Type'] . '</td></tr>' . "\n";

    colonne_titre_tab(LG_EVENT_TYPE_LABEL);
    echo $enreg2['Libelle_Type'] . '</td></tr>' . "\n";

    colonne_titre_tab(LG_EVENT_TYPE_IS_MOD);
    switch ($enreg2['Code_Modifiable']) {
        case 'O':
            echo $hLG_Yes;
            break;
        case 'N':
            echo $hLG_No;
            break;
        default:
            echo $hLG_Q;
            break;
    }
    echo '</td></tr>' . "\n";

    colonne_titre_tab(LG_TARGET_OBJECT);
    echo lib_pfu($enreg2['Objet_Cible']) . '</td></tr>' . "\n";

    colonne_titre_tab(LG_EVENT_TYPE_UNIQ);
    switch ($enreg2['Unicite']) {
        case 'U':
            echo $hLG_Yes;
            break;
        case 'M':
            echo $hLG_No;
            break;
        default:
            echo $hLG_Q;
            break;
    }
    echo '</td></tr>' . "\n";

    colonne_titre_tab(LG_EVENT_TYPE_GEDCOM);
    switch ($enreg2['Type_Gedcom']) {
        case 'O':
            echo $hLG_Yes;
            break;
        case 'N':
            echo $hLG_No;
            break;
        default:
            echo $hLG_Q;
            break;
    }
    echo '</td></tr>' . "\n";

    echo '</table>' . "\n";

    echo '<br><a href="' . $root . '/liste_evenements.php?tev=' . $Code . '">' . LG_EVENT_TYPE_EVENTS . '</a>';

    // Formulaire pour le bouton retour
    Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);

    echo '<br />' . "\n";
    Insere_Bas($compl);
}
?>
</body>

</html>