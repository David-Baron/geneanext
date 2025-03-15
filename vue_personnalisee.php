<?php

//=====================================================================
// Vue personnalisée de la base
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$lib_ref = 'Défaut	';

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'reference',
    'decujus_defaut',
    'Personne'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok      = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Custom_View'];          // Titre pour META
$x = Lit_Env();
$index_follow = 'NN';                    // NOINDEX NOFOLLOW demandé pour les moteurs
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$reference      = Secur_Variable_Post($reference, 2, 'S');
$decujus_defaut = Secur_Variable_Post($decujus_defaut, 1, 'N');
$Personne       = Secur_Variable_Post($Personne, 1, 'N');

//Demande de modification du de cujus
if ($bt_OK) {

    //Demande de de cujus par défaut
    if ($reference == 'D') {
        if ($decujus_defaut) {
            $_SESSION['decujus'] = $decujus_defaut;
            $_SESSION['decujus_defaut'] = 'O';
        }
    } else {
        if ($Personne) {
            $_SESSION['decujus'] = $Personne;
            if ($Personne == $decujus_defaut) $_SESSION['decujus_defaut'] = 'O';
            else                              $_SESSION['decujus_defaut'] = 'N';
        }
    }

    // Retour sur la page précédente
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {
    $compl = '';
    $compl = Ajoute_Page_Info(500, 150);
    Insere_Haut(my_html($titre), $compl, 'Vue_Personnalisee', '');

    // Détermination du de cujus par défaut
    $ref_decujus = 0;
    $lib_defaut = '';
    $sql = 'select Reference, Nom, Prenoms, Ne_Le, Decede_Le, Diff_Internet from ' . nom_table('personnes') . ' where Numero = \'1\' limit 1';
    if ($Res = lect_sql($sql)) {
        if ($pers = $Res->fetch(PDO::FETCH_NUM)) {
            $ref_decujus = $pers[0];
            if (($pers[5] == 'O') or ($_SESSION['estPrivilegie']))
                $lib_defaut = my_html($pers[1] . ' ' . $pers[2]) . aff_annees_pers($pers[3], $pers[4]);
            else
                $lib_defaut = my_html(LG_CUST_VIEW_PRIVATE);
        }
        $Res->closeCursor();
    }

    echo '<form id="saisie" method="post" action="' . my_self() . '">' . "\n";

    $decujus = -1;
    if (!isset($_SESSION['decujus'])) $decujus = -1;
    else $decujus = $_SESSION['decujus'];

    if (!$_SESSION['estPrivilegie']) $where = " Diff_Internet = 'O' ";
    else $where = '';

    echo '<br />';
    echo '<table align="center" border="0"><tr><td>' . "\n";
    echo '<fieldset>';
    echo '<legend>' . ucfirst(LG_CUST_VIEW_SELECT) . '</legend>' . "\n";
    if ($ref_decujus) {
        echo '<input type="hidden" name="decujus_defaut" value="' . $ref_decujus . '"/>' . "\n";

        $def = false;
        if (($decujus == $ref_decujus) or ($decujus == -1)) $def = true;

        echo '<input type="radio" id="reference_D" name="reference" value="D" ';
        if ($def) echo ' checked="checked" ';
        echo '/><label for="reference_D">' . LG_CUST_VIEW_DEFAULT . '</label>' . LG_SEMIC . $lib_defaut . "<br />\n";
        echo '<input type="radio" name="reference" id="reference_P" value="P" ';
        if (!$def) echo ' checked="checked" ';
        echo '/><label for="reference_P">' . LG_CUST_VIEW_OTHER . '</label>' . LG_SEMIC;
        aff_liste_pers(
            'Personne',          // Nom du select
            1,                       // 1ère fois
            1,                       // dernière fois
            $decujus,                // critère de sélection
            $where,                     // crtitère de  sélection
            'Nom, Prenoms',          // critère de tri de la liste
            0,                      // zone non obligatoire
            'onchange="document.forms.saisie.reference[1].checked = true;"'
        );
        echo '</fieldset>' . "\n";

        echo '</td></tr>' . "\n";
        bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');
        echo '</table>' . "\n";
        echo '</form>';
    }
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/' . $Icones['home'] . '" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
}

?>

</body>

</html>