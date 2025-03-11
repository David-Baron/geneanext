<?php

//=====================================================================
// Edition d'une requête
// On ne peut que modifier le titre ou supprimer la requête
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'Titre',
    'ATitre'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');

$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Request_Edit'];     // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Recup de la variable passée dans l'URL : référence de la requête
$reference = Recup_Variable('reference', 'N');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$n_requetes = nom_table('requetes');

if ($bt_Sup) {
    $req = 'delete from ' . $n_requetes . ' where Reference = ' . $reference;
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

$Titre  = Secur_Variable_Post($Titre, 80, 'S');
$ATitre = Secur_Variable_Post($ATitre, 80, 'S');

//Demande de mise à jour
if ($bt_OK) {
    // Constitution de la requête
    $req = '';
    Aj_Zone_Req('Titre', $Titre, $ATitre, 'A', $req);
    if ($req != '') {
        $req = 'update ' . $n_requetes . ' set ' . $req . ' where Reference = ' . $reference;
        $res = maj_sql($req);
        maj_date_site();
    }
    // Retour sur la page précédente
    Retour_Ar();
}

if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {

    $compl = Ajoute_Page_Info(600, 150) .
        Affiche_Icone_Lien('href="'. $root .'/fiche_requete.php?reference=' . $reference . '"', 'page', my_html($LG_Menu_Title['Request'])) . '&nbsp;';

    Insere_Haut($titre, $compl, 'Edition_Requete', $reference);

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'Titre\')" action="' . my_self() . '?reference=' . $reference . '">' . "\n";

    $sql = 'select * from ' . $n_requetes . ' where Reference = ' . $reference . ' limit 1';
    $res = lect_sql($sql);
    if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
        $enreg2 = $enreg;
        Champ_car($enreg, 'Titre');
        Champ_car($enreg, 'Criteres');
        $Titre    = $enreg2['Titre'];
        $Criteres = $enreg2['Criteres'];
        $Code_SQL = $enreg2['Code_SQL'];

        $larg_titre = 25;
        echo '<table width="80%" class="table_form">' . "\n";
        ligne_vide_tab_form(1);

        echo colonne_titre_tab(LG_QUERY_TITLE);
        echo '<input class="oblig" type="text" name="Titre" id="Titre" value="' . $Titre . '" size="80"/>&nbsp;' . "\n";
        echo '<input type="' . $hidden . '" name="ATitre" value="' . $Titre . '"/>' . "\n";
        Img_Zone_Oblig('imgObligTitre');
        echo '</td></tr>' . "\n";

        $liste_crit = explode($separ, $enreg['Criteres']);
        $nb_crit = count($liste_crit);
        if ($nb_crit > 0) {
            for ($nb = 0; $nb < $nb_crit - 1; $nb++) {
                $exp_crit = explode('=', $liste_crit[$nb]);
                echo colonne_titre_tab(trim($exp_crit[0])) . my_html(trim($exp_crit[1])) . '</td></tr>' . "\n";
            }
        }

        echo colonne_titre_tab(LG_QUERY_CODE);
        echo '<textarea cols="50" rows="4" name="Code_SQL" readonly="readonly">' . $Code_SQL . '</textarea>' . "\n";
        echo '</td></tr>' . "\n";

        ligne_vide_tab_form(1);
        bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_Supprimer, LG_QUERY_THIS);

        echo '</table>' . "\n";
        echo '</form>' . "\n";

        echo '<br />' . "\n";
    }
    Insere_Bas($compl);
}
?>
</body>

</html>