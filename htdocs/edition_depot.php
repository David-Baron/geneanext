<?php
//=====================================================================
// Creation et modification d'un dépôt de sources
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('C')) {
    header('Location: ' . $root . '/');
    exit();
}

$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'NomD',
    'ANomD',
    'Divers',
    'ADivers',
    'Diff_Note',
    'ADiff_Note',
    'Horigine'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Recup de la variable passée dans l'URL : identifiant du dépôt
$Ident = Recup_Variable('ident', 'N');

if ($Ident == -1) $Creation = true;
else $Creation = false;

// Titre pour META
if (!$Creation)
    $titre = $LG_Menu_Title['Repo_Sources_Edit'];
else
    $titre = $LG_Menu_Title['Repo_Sources_Add'];

$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');


$NomD       = Secur_Variable_Post($NomD, 100, 'S');
$ANomD      = Secur_Variable_Post($ANomD, 100, 'S');
$Divers     = Secur_Variable_Post($Divers, 65535, 'S');
$ADivers    = Secur_Variable_Post($ADivers, 65535, 'S');
$Diff_Note  = Secur_Variable_Post($Diff_Note, 1, 'S');
$ADiff_Note = Secur_Variable_Post($ADiff_Note, 1, 'S');

// Type d'objet des dépôts de sources
$Type_Ref = 'O';

$n_depots = nom_table('depots');

// Suppression demandée
if ($bt_Sup) {
    // Suppression des commentaires
    if ($Divers != '') {
        $req = req_sup_commentaire($Ident, $Type_Ref);
        $res = maj_sql($req);
    }
    // Suppression du dépôt
    $req = 'DELETE FROM ' . $n_depots . " WHERE Ident = $Ident";
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

if ($bt_OK) {

    $req_comment = '';
    $maj_site = false;

    if ($Creation) {
        // Création d'un dépôt
        Ins_Zone_Req($NomD, 'A', $req);
        if ($req != '') {
            $req = 'insert into ' . $n_depots . ' values(null,' . $req . ',null)';
            $result = maj_sql($req);
            if ($result) $maj_site = true;
        }
        // Création d'un enregistrement dans la table commentaires
        if ($Divers != '') {
            insere_commentaire($connexion->lastInsertId(), $Type_Ref, $Divers, $Diff_Note);
        }
    } else {
        // Modification
        Aj_Zone_Req('Nom', $NomD, $ANomD, 'A', $req);
        if ($req != '') {
            $req = 'update ' . $n_depots . ' set ' . $req .
                ' where Ident = ' . $Ident;
            $result = maj_sql($req);
            if ($result) $maj_site = true;
        }
        // Traitement des commentaires
        maj_commentaire($Ident, $Type_Ref, $Divers, $ADivers, $Diff_Note, $ADiff_Note);
    }

    // Exécution de la requête sur les commentaires
    if ($req_comment != '') {
        $res = maj_sql($req_comment);
        if ($res) $maj_site = true;
    }

    if ($maj_site) maj_date_site();

    Retour_Ar();
}


// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {

    include(__DIR__ . '/assets/js/Insert_Tiny.js');

    $compl = Ajoute_Page_Info(600, 150);

    if (!$Creation)
        $compl .= Affiche_Icone_Lien('href="' . $root . '/fiche_depot?ident=' . $Ident . '"', 'page', my_html($LG_Menu_Title['Repo_Sources'])) . '&nbsp;';

    Insere_Haut(my_html($titre), $compl, 'Edition_Depot', $Ident);

    if (!$Creation) {
        $sql = 'select * from ' . $n_depots . ' where Ident = ' . $Ident . ' limit 1';
        $res    = lect_sql($sql);
        $enreg  = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
        Champ_car($enreg2, 'Nom');
        unset($enreg);
        $NomD = $enreg2['Nom'];
    } else {
        $NomD = '';
    }

    $larg_titre = 30;
    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'NomD\')" action="' . my_self() . '?ident=' . $Ident . '">' . "\n";
    echo '<table width="85%" class="table_form">' . "\n";
    echo '<tr><td colspan="2">&nbsp;</td></tr>';

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_CH_REPOSITORY_NAME . '&nbsp;</td><td class="value">';
    echo '<input type="text" class="oblig" size="100" name="NomD" value="' . $NomD . '"/>&nbsp;' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="' . $hidden . '" name="ANomD" value="' . $NomD . '"/></td></tr>' . "\n";

    // === Commentaire
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_CH_COMMENT . '&nbsp;</td><td class="value">';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Ident, $Type_Ref);
    echo '<textarea cols="80" rows="4" name="Divers">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="' . $hidden . '" name="ADivers" value="' . htmlentities($Commentaire, ENT_QUOTES, $def_enc) . '"/></td></tr>' . "\n";

    // Diffusion Internet commentaire
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_CH_COMMENT_VISIBILITY . '&nbsp;</td><td class="value">';
    echo '<input type="checkbox" name="Diff_Note" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked';
    echo "/>\n";
    echo '<input type="' . $hidden . '" name="ADiff_Note" value="' . $Diffusion_Commentaire_Internet . '"/></td></tr>' . "\n";

    echo '<tr><td colspan="2">&nbsp;</td></tr>';

    // Bouton Supprimer en modification si pas d'utilisation du dépôt
    $lib_sup = '';
    if ((!$Creation) and ($Ident != 0)) {
        $sql = 'select 1 from ' . nom_table('sources') . ' where Ident_Depot = ' . $Ident . ' limit 1';
        $res = lect_sql($sql);
        $utilise = ($enreg = $res->fetch(PDO::FETCH_ASSOC));
        $res->closeCursor();
        if (! $utilise) $lib_sup = $lib_Supprimer;
    }

    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_CH_REPOSITORY_THIS);

    echo '</table>' . "\n";

    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";

    echo '</form>';
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