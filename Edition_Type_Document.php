<?php
//=====================================================================
// Edition d'un type de document
//=====================================================================

session_start();
include('fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'LibelleF',
    'ALibelleF',
    'Horigine',
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

$acces = 'M';        // Type d'accès de la page : (M)ise à jour, (L)ecture

$code_crea = '-----';
// Recup de la variable passée dans l'URL : type d'évènement
$Code = Recup_Variable('code', 'A');
if ($Code == $code_crea) $Creation = true;
else                     $Creation = false;

// Titre pour META
if ($Creation) $titre = $LG_Menu_Title['Doc_Type_Add'];
else $titre = $LG_Menu_Title['Doc_Type_Edit'];
$x = Lit_Env();

include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$LibelleF  = Secur_Variable_Post($LibelleF, 50, 'S');
$ALibelleF = Secur_Variable_Post($ALibelleF, 50, 'S');

$n_types_doc = nom_table('types_doc');

if ($bt_Sup) {
    $req = 'delete from ' . $n_types_doc . ' where Id_Type_Document = ' . $Code;
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

//Demande de mise à jour
if ($bt_OK) {
    $erreur = '';
    $msg    = '';

    // Init des zones de requête
    $req = '';

    if ($erreur == '') {
        // Cas de la modification
        if (! $Creation) {
            Aj_Zone_Req('Libelle_Type', $LibelleF, $ALibelleF, 'A', $req);
            if ($req != '')
                $req = 'update ' . $n_types_doc . ' set ' . $req .
                    ' where Id_Type_Document = ' . $Code;
        }
        // Cas de la création
        else {
            // On n'autorise la création que si le nom et la description sont saisis
            Ins_Zone_Req($LibelleF, 'A', $req);
            if ($req != '')
                $req = 'insert into ' . $n_types_doc . ' values(null, ' . $req . ')';
        }
    }

    // Exéution de la requête
    if ($req != '') {
        $res = maj_sql($req);
        maj_date_site();
    }

    // Retour sur la page précédente
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {

    $compl = Ajoute_Page_Info(600, 300);
    if ($Code != $code_crea)
        $compl .= Affiche_Icone_Lien('href="' . Get_Adr_Base_Ref() . 'Fiche_Type_Document.php?code=' . $Code . '"', 'page', 'Fiche évènement') . '&nbsp;';

    Insere_Haut($titre, $compl, 'Edition_Type_Document', $Code);

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'LibelleF\')" action="' . my_self() . '?code=' . $Code . '">' . "\n";
    aff_origine();

    if (!$Creation) {
        $sql = 'select * from ' . $n_types_doc . ' where Id_Type_Document = \'' . $Code . '\' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
        Champ_car($enreg2, 'Libelle_Type');
        $LibelleF = $enreg2['Libelle_Type'];

        // Si le type est-il utilisé, on ne pourra pas le supprimer
        $sql = 'select 1 from ' . nom_table('documents') . ' where Id_Type_Document = \'' . $Code . '\' limit 1';
        $res = lect_sql($sql);
        $utilise = ($enreg = $res->fetch(PDO::FETCH_ASSOC));

        $res->closeCursor();
    } else {
        $CodeF    = '';
        $LibelleF = '';
    }

    $larg_titre = '20';

    echo '<table width="70%" class="table_form">' . "\n";
    ligne_vide_tab_form(1);

    colonne_titre_tab(LG_DOC_TYPE_LABEL);
    echo '<input type="text" class="oblig" name="LibelleF" value="' . $LibelleF . '" size="50"/>' . "\n";
    echo '&nbsp;';
    Img_Zone_Oblig('imgObligCode');
    echo '<input type="hidden" name="ALibelleF" value="' . $LibelleF . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    ligne_vide_tab_form(1);
    // Bouton Supprimer en modification si pas d'utilisation du rôle
    $lib_sup = '';
    if ((!$Creation) and (! $utilise)) $lib_sup = $lib_Supprimer;
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_DOC_TYPE_THIS);

    echo '</table>' . "\n";

    echo "</form>";

    Insere_Bas($compl);
}
?>
</body>

</html>