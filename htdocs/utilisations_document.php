<?php

//=====================================================================
// Liste des utilisations d'un document
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('G')) {
    header('Location: ' . $root . '/');
    exit();
}

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

$titre = $LG_Menu_Title['Document_Utils'];    // Titre pour META

$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');
require(__DIR__ . '/../app/ressources/commun_rech_com_util_docs.php');

// Recup des variables passées dans l'URL : référence du document
$Reference = Recup_Variable('Doc', 'N');
if (!$Reference) $Reference = -1;

$compl = Ajoute_Page_Info(600, 260);
Insere_Haut(my_html($titre), $compl, 'Utilisations_Document', '');

//Utilisation(s) du document

$sql = 'SELECT Titre, Diff_Internet FROM ' . nom_table('documents') . ' WHERE id_document = ' . $Reference . ' limit 1';
$res = lect_sql($sql);
if ($res = lect_sql($sql)) {
    if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
        if (($enreg[1] == 'O') or IS_GRANTED('G')) {
            echo '<h3 align="center">' . my_html($enreg[0]) . '</h3><br />' . "\n";
        }
    }
}

$sql = 'SELECT Reference_Objet, Type_Objet FROM ' . nom_table('concerne_doc') . ' WHERE id_document = ' . $Reference . ' order by Type_Objet';
$res = lect_sql($sql);

$nb = $res->rowCount();
if ($nb == 0) {
    echo '<br />';
    echo '<img src="' . $root . '/assets/img/error.png" alt="Avertissement"/>' . LG_DOC_UT_NO . '<br>';
} else {
    // $plu = pluriel($nb);
    // echo $nb.' utilisation'.$plu. ' trouv&eacute;e'.$plu.'<br /><br />';
    echo $nb . ' ' . my_html(LG_DOC_UT_COUNT) . '<br /><br />';

    echo '<table width="95%" class="classic" cellspacing="1" cellpadding="3" >';
    $echo_modif = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '"></a>';

    $num_lig = 0;

    while ($row = $res->fetch(PDO::FETCH_NUM)) {

        $Objet_Cible = $row[1];
        $Ref_Objet = $row[0];
        $cible = lib_pfu($Objet_Cible, true);

        acces_donnees($Objet_Cible, $Ref_Objet);

        echo '<tr><td>';
        affiche_donnees($Objet_Cible, $Ref_Objet, 'U');
        echo '</td></tr>' . "\n";
    }

    echo '</table>';
}

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