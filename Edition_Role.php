<?php
//=====================================================================
// Edition d'un rôle
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'CodeF',
    'ACodeF',
    'LibelleF',
    'ALibelleF',
    'LibelleInvF',
    'ALibelleInvF',
    'SymetrieF',
    'ASymetrieF',
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

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture

// Recup de la variable passée dans l'URL : rôle
$Code = Recup_Variable('code', 'A');
if ($Code == '-----') $Creation = true;
else                  $Creation = false;
if ($Creation) $titre = $LG_Menu_Title['Role_Add'];
else $titre = $LG_Menu_Title['Role_Edit'];

$n_roles = nom_table('roles');

$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$CodeF        = Secur_Variable_Post($CodeF, 4, 'S');
$ACodeF       = Secur_Variable_Post($ACodeF, 4, 'S');
$LibelleF     = Secur_Variable_Post($LibelleF, 50, 'S');
$ALibelleF    = Secur_Variable_Post($ALibelleF, 50, 'S');
$LibelleInvF  = Secur_Variable_Post($LibelleInvF, 50, 'S');
$ALibelleInvF = Secur_Variable_Post($ALibelleInvF, 50, 'S');
$SymetrieF    = Secur_Variable_Post($SymetrieF, 4, 'S');
$ASymetrieF   = Secur_Variable_Post($ASymetrieF, 4, 'S');

if ($bt_Sup) {
    $req = 'delete from ' . $n_roles . ' where Code_Role = \'' . $Code . '\'';
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
            Aj_Zone_Req('Code_Role', $CodeF, $ACodeF, 'A', $req);
            Aj_Zone_Req('Libelle_Role', $LibelleF, $ALibelleF, 'A', $req);
            Aj_Zone_Req('Symetrie', $SymetrieF, $ASymetrieF, 'A', $req);
            Aj_Zone_Req('Libelle_Inv_Role', $LibelleInvF, $ALibelleInvF, 'A', $req);
            if ($req != '')
                $req = 'update ' . $n_roles . ' set ' . $req .
                    ' where Code_Role = \'' . $Code . '\'';
        }
        // Cas de la création
        else {
            // On n'autorise la création que si le code est saisi
            if ($CodeF != '') {
                Ins_Zone_Req($CodeF, 'A', $req);
                Ins_Zone_Req($LibelleF, 'A', $req);
                Ins_Zone_Req($SymetrieF, 'A', $req);
                Ins_Zone_Req($LibelleInvF, 'A', $req);
                if ($req != '')
                    $req = 'insert into ' . $n_roles . ' values( ' . $req . ')';
            }
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

    # include(__DIR__ . '/assets/js/Edition_Role.js');

    $compl = Ajoute_Page_Info(600, 150);

    if (!$Creation)
        $compl .= Affiche_Icone_Lien('href="Fiche_Role.php?code=' . $Code . '"', 'page', 'Fiche rôle') . '&nbsp;';

    Insere_Haut(my_html($titre), $compl, 'Edition_Role', $Code);

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'CodeF\')" action="' . my_self() . '?code=' . $Code . '">' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . $Horigine . '"/>' . "\n";

    if (!$Creation) {
        $sql = 'select * from ' . $n_roles . ' where Code_Role = \'' . $Code . '\' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
        Champ_car($enreg, 'Libelle_Role');
        Champ_car($enreg, 'Libelle_Inv_Role');
        $CodeF       = $enreg2['Code_Role'];
        $LibelleF    = $enreg2['Libelle_Role'];
        $SymetrieF   = $enreg2['Symetrie'];
        $LibelleInvF = $enreg2['Libelle_Inv_Role'];

        // Le rôle est-il utilisé ?
        // Si oui, on ne pourra pas modifier le code
        $sql = 'select 1 from ' . nom_table('relation_personnes') . ' where Code_Role = \'' . $Code . '\' limit 1';
        $res = lect_sql($sql);
        $utilise = ($enreg = $res->fetch(PDO::FETCH_ASSOC));
        $res->closeCursor();
    } else {
        $CodeF   = '';
        $Libelle = '';
    }

    $larg_titre = '25';
    echo '<table width="80%" class="table_form">' . "\n";
    ligne_vide_tab_form(1);

    col_titre_tab(LG_ROLE_CODE, $larg_titre);
    echo '<td class="value">';
    // On ne peut modifier le code qu'en création ou s'il n'est pas utilisé
    if (($Creation) or (! $utilise)) {
        echo '<input class="oblig" type="text" name="CodeF" value="' . $CodeF . '" size="4" maxlength="4" onchange="verification_code(this);"/>&nbsp;' . "\n";
        // Liste des codes existants
        $codes = ' ';
        $sql = 'select Code_Role from ' . $n_roles;
        $res = lect_sql($sql);
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $codes .= $row[0] . ' ';
        }
        echo '<input type="' . $hidden . '" name="codes" value="' . $codes . '"/>' . "\n";
    } else
        echo $CodeF . "\n";
    Img_Zone_Oblig('imgObligCode');
    echo '</td></tr>' . "\n";

    col_titre_tab(LG_ROLE_SYM, $larg_titre);
    echo '<td class="value">';
    echo '<input type="radio" id="SymetrieF_O" name="SymetrieF" value="O"';
    if (($SymetrieF == 'O') or ($Creation)) echo ' checked="checked"';
    echo '/><label for="SymetrieF_O">' . $LG_Yes . '</label>&nbsp;' . "\n";
    echo '<input type="radio" id="SymetrieF_N" name="SymetrieF" value="N"';
    if ($SymetrieF == 'N') echo ' checked="checked"';
    echo '/><label for="SymetrieF_N">' . $LG_No . '</label>';
    echo '<input type="' . $hidden . '" name="ASymetrieF" value="' . $SymetrieF . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    col_titre_tab(LG_ROLE_LABEL, $larg_titre);
    echo '<td class="value">';
    echo '<input type="text" name="LibelleF" value="' . $LibelleF . '" size="50" onchange="dupplique();"/>' . "\n";
    echo '<input type="' . $hidden . '" name="ALibelleF" value="' . $LibelleF . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    col_titre_tab(LG_ROLE_OPPOS_LABEL, $larg_titre);
    echo '<td class="value">';
    echo '<input type="text" name="LibelleInvF" value="' . $LibelleInvF . '" size="50"/>' . "\n";
    echo '<input type="' . $hidden . '" name="ALibelleInvF" value="' . $LibelleInvF . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    ligne_vide_tab_form(1);
    // Bouton Supprimer en modification si pas d'utilisation du rôle
    $lib_sup = '';
    if ((!$Creation) and (! $utilise)) $lib_sup = $lib_Supprimer;
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_ROLE_THIS);

    echo '</table>' . "\n";

    echo '</form>';

    Insere_Bas($compl);
}

?>
<script type="text/javascript">
    function dupplique() {
        if (document.forms.saisie.LibelleInvF.value == "") {
            document.forms.saisie.LibelleInvF.value = document.forms.saisie.LibelleF.value;
        }
    }

    function verification_code(zone) {
        var codes = document.forms.saisie.codes.value;
        var posi = codes.indexOf(zone.value);
        if (posi > -1) {
            window.alert('<?php echo LG_ROLE_ERROR_EXISTS; ?>' + codes + ').');
            zone.value = '';
        }
    }
</script>
</body>

</html>