<?php
//=====================================================================
// Creation et modification d'une source
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'Titre',
    'ATitre',
    'Auteur',
    'AAuteur',
    'Classement',
    'AClassement',
    'Depot',
    'ADepot',
    'Cote',
    'ACote',
    'Adresse_Web',
    'AAdresse_Web',
    'Fiabilite_Source',
    'AFiabilite_Source',
    'Divers',
    'ADivers', //'Diff_Note','ADiff_Note',
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

// Gestion standard des pages
$acces = 'M';        // Type d'accès de la page : (M)ise à jour, (L)ecture

// Recup de la variable passée dans l'URL : identifiant de la source
$Ident = Recup_Variable('ident', 'N');

if ($Ident == -1) $Creation = true;
else $Creation = false;

// Titre pour META
if (!$Creation)
    $titre = $LG_Menu_Title['Source_Edit'];
else
    $titre = $LG_Menu_Title['Source_Add'];

$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Type d'objet des sources
$Type_Ref = 'S';

$n_sources = nom_table('sources');

// Suppression demandée
if ($bt_Sup) {
    // Suppression des commentaires
    if ($Divers != '') {
        $req = req_sup_commentaire($Ident, $Type_Ref);
        $res = maj_sql($req);
    }
    // Suppression de la source
    $req = 'delete from ' . $n_sources . ' where Ident = ' . $Ident . ' limit 1';
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

if ($bt_OK) {

    $Titre                = Secur_Variable_Post($Titre, 100, 'S');
    $ATitre                = Secur_Variable_Post($ATitre, 100, 'S');
    $Auteur                = Secur_Variable_Post($Auteur, 100, 'S');
    $AAuteur            = Secur_Variable_Post($AAuteur, 100, 'S');
    $Classement            = Secur_Variable_Post($Classement, 100, 'S');
    $AClassement        = Secur_Variable_Post($AClassement, 100, 'S');
    $Depot                = Secur_Variable_Post($Depot, 1, 'N');
    $ADepot                = Secur_Variable_Post($ADepot, 1, 'N');
    $Cote                = Secur_Variable_Post($Cote, 100, 'S');
    $ACote                = Secur_Variable_Post($ACote, 100, 'S');
    $Adresse_Web        = Secur_Variable_Post($Adresse_Web, 100, 'S');
    $AAdresse_Web        = Secur_Variable_Post($AAdresse_Web, 100, 'S');
    $Divers                = Secur_Variable_Post($Divers, 65535, 'S');
    $ADivers            = Secur_Variable_Post($ADivers, 65535, 'S');
    $Fiabilite_Source    = Secur_Variable_Post($Fiabilite_Source, 1, 'S');
    $AFiabilite_Source    = Secur_Variable_Post($AFiabilite_Source, 1, 'S');

    $req_comment = '';
    $maj_site = false;

    if ($Creation) {
        // Création d'une source
        Ins_Zone_Req($Titre, 'A', $req);
        Ins_Zone_Req($Auteur, 'A', $req);
        Ins_Zone_Req($Classement, 'A', $req);
        Ins_Zone_Req($Depot, 'N', $req);
        Ins_Zone_Req('', 'A', $req); // Ident_Depot_Tempo à null
        Ins_Zone_Req($Cote, 'A', $req);
        Ins_Zone_Req($Adresse_Web, 'A', $req);
        Ins_Zone_Req($Fiabilite_Source, 'A', $req);
        if ($req != '') {
            $req = 'insert into ' . $n_sources . ' values(null,' . $req . ",null)";
            $result = maj_sql($req);
            if ($result) $maj_site = true;
        }
        // Création d'un enregistrement dans la table commentaires
        if ($Divers != '') {
            insere_commentaire($connexion->lastInsertId(), $Type_Ref, $Divers, 'N');
        }
    } else {
        // Modification
        Aj_Zone_Req('Titre', $Titre, $ATitre, 'A', $req);
        Aj_Zone_Req('Auteur', $Auteur, $AAuteur, 'A', $req);
        Aj_Zone_Req('Classement', $Classement, $AClassement, 'A', $req);
        Aj_Zone_Req('Ident_Depot', $Depot, $ADepot, 'N', $req);
        Aj_Zone_Req('Cote', $Cote, $ACote, 'A', $req);
        Aj_Zone_Req('Adresse_Web', $Adresse_Web, $AAdresse_Web, 'A', $req);
        Aj_Zone_Req('Fiabilite_Source', $Fiabilite_Source, $AFiabilite_Source, 'A', $req);
        if ($req != '') {
            $req = 'update ' . $n_sources . ' set ' . $req . ' where Ident = ' . $Ident;
            $result = maj_sql($req);
            if ($result) $maj_site = true;
        }
        // Traitement des commentaires
        maj_commentaire($Ident, $Type_Ref, $Divers, $ADivers, 'N', 'N');
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
        $compl .= Affiche_Icone_Lien('href="Fiche_Source.php?ident=' . $Ident . '"', 'page', $LG_Menu_Title['Source']) . '&nbsp;';

    Insere_Haut($titre, $compl, 'Edition_Source', $Ident);

    if (!$Creation) {
        $sql = 'select * from ' . $n_sources . ' where Ident = ' . $Ident . ' limit 1';
        $res    = lect_sql($sql);
        $enreg  = $res->fetch(PDO::FETCH_ASSOC);
        $Titre = $enreg['Titre'];
        $Auteur = $enreg['Auteur'];
        $Classement = $enreg['Classement'];
        $Cote = $enreg['Cote'];
        $Depot = $enreg['Ident_Depot'];
        $Adresse_Web = $enreg['Adresse_Web'];
        $Fiabilite_Source = $enreg['Fiabilite_Source'];
    } else {
        $Titre = '';
        $Auteur = '';
        $Classement = '';
        $Depot = '';
        $Cote = '';
        $Adresse_Web = '';
        $Fiabilite_Source = '';
    }

    $larg_titre = 25;
    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'Titre\')" action="' . my_self() . '?ident=' . $Ident . '">' . "\n";
    echo '<table width="80%" class="table_form">' . "\n";
    ligne_vide_tab_form(1);

    // Titre
    colonne_titre_tab(LG_SRC_TITLE);
    echo '<input type="text" class="oblig" size="100" name="Titre" value="' . $Titre . '"/>&nbsp;' . "\n";
    Img_Zone_Oblig('imgObligNom');
    echo '<input type="' . $hidden . '" name="ATitre" value="' . $Titre . '"/></td></tr>' . "\n";

    // Auteur
    colonne_titre_tab(LG_SRC_AUTHOR);
    echo '<input type="text" size="100" name="Auteur" value="' . $Auteur . '"/>&nbsp;' . "\n";
    echo '<input type="' . $hidden . '" name="AAuteur" value="' . $Auteur . '"/></td></tr>' . "\n";

    // Classement
    colonne_titre_tab(LG_SRC_CLASS);
    echo '<input type="text" size="100" name="Classement" value="' . $Classement . '"/>&nbsp;' . "\n";
    echo '<input type="' . $hidden . '" name="AClassement" value="' . $Classement . '"/></td></tr>' . "\n";

    // Dépôt
    colonne_titre_tab(LG_SRC_REPO);

    $sql = 'select Ident, Nom from ' . nom_table('depots') . ' order by Nom';
    echo '<select name="Depot">' . "\n";
    if ($res = lect_sql($sql)) {
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            echo '<option value="' . $row[0] . '"';
            if ($Depot == $row[0]) {
                echo ' selected="selected"';
            }
            echo '>' . my_html($row[1]) . '</option>' . "\n";
        }
    }
    $res->closeCursor();
    echo '</select>' . "\n";
    echo '<input type="' . $hidden . '" name="ADepot" value="' . $Depot . '"/></td></tr>' . "\n";

    // Cote
    colonne_titre_tab(LG_SRC_REFER);
    echo '<input type="text" size="100" name="Cote" value="' . $Cote . '"/>&nbsp;' . "\n";
    echo '<input type="' . $hidden . '" name="ACote" value="' . $Cote . '"/></td></tr>' . "\n";

    // Adresse_Web
    colonne_titre_tab(LG_SRC_WEB);
    echo '<input type="text" size="100" name="Adresse_Web" value="' . $Adresse_Web . '"/>&nbsp;' . "\n";
    echo '<input type="' . $hidden . '" name="AAdresse_Web" value="' . $Cote . '"/></td></tr>' . "\n";

    // Fiabilité de la source
    colonne_titre_tab(LG_SRC_TRUST);
    rb_fiab("H", LG_SRC_TRUST_H);
    rb_fiab("M", LG_SRC_TRUST_M);
    rb_fiab("F", LG_SRC_TRUST_L);
    echo '<input type="' . $hidden . '" name="AFiabilite_Source" value="' . $Fiabilite_Source . '"/></td></tr>' . "\n";

    // === Commentaire
    colonne_titre_tab(LG_CH_COMMENT);
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Ident, $Type_Ref);
    echo '<textarea cols="80" rows="4" name="Divers">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="' . $hidden . '" name="ADivers" value="' . htmlentities($Commentaire, ENT_QUOTES, $def_enc) . '"/></td></tr>' . "\n";

    // Inutile vu que la fiche est réservée ; de plus les sources ne seront jamais affichées par les invités
    // Diffusion Internet commentaire
    /*
	colonne_titre_tab('Visibilité Internet du commentaire');
	echo '<td class="value">&nbsp;<input type="checkbox" name="Diff_Note" value="O"';
	if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
	echo "/>\n";
  	echo '<input type="'.$hidden.'" name="ADiff_Note" value="'.$Diffusion_Commentaire_Internet.'"/></td></tr>'."\n";
	*/

    ligne_vide_tab_form(1);

    // Bouton Supprimer en modification si pas d'utilisation du dépôt
    $lib_sup = '';
    if (!$Creation) {
        $sql = 'select 1 from ' . nom_table('concerne_source') . ' where Id_Source = ' . $Ident . ' limit 1';
        $res = lect_sql($sql);
        $utilise = ($enreg = $res->fetch(PDO::FETCH_ASSOC));
        $res->closeCursor();
        if (! $utilise) $lib_sup = $lib_Supprimer;
    }

    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_SRC_THIS);

    echo '</table>' . "\n";

    aff_origine();

    echo '</form>';
    Insere_Bas($compl);
}

function rb_fiab($niv, $lib)
{
    global $Fiabilite_Source;
    echo '<input type="radio" name="Fiabilite_Source" id="Fiabilite_Source_' . $niv . '" value="' . $niv . '"';
    if ($niv == $Fiabilite_Source) echo ' checked="checked"';
    echo '/><label for="Fiabilite_Source_' . $niv . '">' . $lib . "</label>&nbsp;\n";
}

?>
</body>

</html>