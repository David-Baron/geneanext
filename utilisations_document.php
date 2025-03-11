<?php

//=====================================================================
// Liste des utilisations d'un document
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

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

// Gestion standard des pages
$acces = 'L';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Document_Utils'];    // Titre pour META
$niv_requis = 'G';                            // Page accessible uniquement au gestionnaire
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

require(__DIR__ . '/app/ressources/commun_rech_com_util_docs.php');

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
        if (($enreg[1] == 'O') or ($est_gestionnaire)) {
            echo '<h3 align="center">' . my_html($enreg[0]) . '</h3><br />' . "\n";
        }
    }
}

$sql = 'SELECT Reference_Objet, Type_Objet FROM ' . nom_table('concerne_doc') . ' WHERE id_document = ' . $Reference . ' order by Type_Objet';
$res = lect_sql($sql);

$nb = $res->rowCount();
if ($nb == 0) {
    echo '<br />';
    Affiche_Warning(LG_DOC_UT_NO);
} else {
    // $plu = pluriel($nb);
    // echo $nb.' utilisation'.$plu. ' trouv&eacute;e'.$plu.'<br /><br />';
    echo $nb . ' ' . my_html(LG_DOC_UT_COUNT) . '<br /><br />';

    echo '<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" >';
    $echo_modif = Affiche_Icone('fiche_edition', my_html($LG_modify)) . '</a>';
    $num_lig = 0;
    $base_ref = Get_Adr_Base_Ref();

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

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);

Insere_Bas($compl);
?>
</body>

</html>