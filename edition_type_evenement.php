<?php
//=====================================================================
// Edition d'un type d'évènement
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'CodeF',
    'ACodeF',
    'LibelleF',
    'ALibelleF',
    'Code_ModifiableF',
    'ACode_ModifiableF',
    'Objet_CibleF',
    'AObjet_CibleF',
    'UniciteF',
    'AUniciteF',
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

// Recup de la variable passée dans l'URL : type d'évènement
$Code = Recup_Variable('code', 'A');
if ($Code == '-----') $Creation = true;
else                  $Creation = false;

// Titre pour META
if ($Creation) $titre = $LG_Menu_Title['Event_Type_Add'];
else $titre = $LG_Menu_Title['Event_Type_Edit'];

$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$n_types_evenement = nom_table('types_evenement');

if ($bt_Sup) {
    $req = 'delete from ' . $n_types_evenement . ' where Code_Type = \'' . $Code . '\'';
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

//Demande de mise à jour
if ($bt_OK) {

    $CodeF             = Secur_Variable_Post($CodeF, 4, 'S');
    $ACodeF            = Secur_Variable_Post($ACodeF, 4, 'S');
    $LibelleF          = Secur_Variable_Post($LibelleF, 50, 'S');
    $ALibelleF         = Secur_Variable_Post($ALibelleF, 50, 'S');
    $Code_ModifiableF  = Secur_Variable_Post($Code_ModifiableF, 1, 'S');
    $ACode_ModifiableF = Secur_Variable_Post($ACode_ModifiableF, 1, 'S');
    $Objet_CibleF      = Secur_Variable_Post($Objet_CibleF, 4, 'S');
    $AObjet_CibleF     = Secur_Variable_Post($AObjet_CibleF, 4, 'S');
    $UniciteF          = Secur_Variable_Post($UniciteF, 1, 'S');
    $AUniciteF         = Secur_Variable_Post($AUniciteF, 1, 'S');

    $erreur = '';
    $msg    = '';

    // Init des zones de requête
    $req = '';
    if ($Objet_CibleF == '') $Objet_CibleF = '-';

    if ($erreur == '') {
        // Cas de la modification
        if (! $Creation) {
            Aj_Zone_Req('Code_Type', $CodeF, $ACodeF, 'A', $req);
            Aj_Zone_Req('Libelle_Type', $LibelleF, $ALibelleF, 'A', $req);
            Aj_Zone_Req('Code_Modifiable', $Code_ModifiableF, $ACode_ModifiableF, 'A', $req);
            Aj_Zone_Req('Objet_Cible', $Objet_CibleF, $AObjet_CibleF, 'A', $req);
            Aj_Zone_Req('Unicite', $UniciteF, $AUniciteF, 'A', $req);
            if ($req != '')
                $req = 'update ' . $n_types_evenement . ' set ' . $req .
                    ' where Code_Type = \'' . $Code . '\'';
        }
        // Cas de la création
        else {
            // On n'autorise la création que si le nom et la description sont saisis
            if ($CodeF != '') {
                Ins_Zone_Req($CodeF, 'A', $req);
                Ins_Zone_Req($LibelleF, 'A', $req);
                Ins_Zone_Req($Code_ModifiableF, 'A', $req);
                Ins_Zone_Req($Objet_CibleF, 'A', $req);
                Ins_Zone_Req($UniciteF, 'A', $req);
                if ($req != '')
                    $req = 'insert into ' . $n_types_evenement . ' values(' . $req . ',\'N\')';
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

    $compl = Ajoute_Page_Info(600, 150);
    if (!$Creation)
        $compl .= Affiche_Icone_Lien('href="' . $root . '/fiche_type_evenement.php?code=' . $Code . '"', 'page', 'Fiche type évènement') . '&nbsp;';

    Insere_Haut(my_html($titre), $compl, 'Edition_Type_Evenement', $Code);

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'CodeF\')" action="' . my_self() . '?code=' . $Code . '">' . "\n";
    echo '<input type="' . $hidden . '" name="Horigine" value="' . $Horigine . '"/>' . "\n";

    if (!$Creation) {
        $sql = 'select * from ' . $n_types_evenement . ' where Code_Type = \'' . $Code . '\' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        if ($enreg) {
            $enreg2 = $enreg;
            Champ_car($enreg2, 'Libelle_Type');
            $CodeF            = $enreg2['Code_Type'];
            $LibelleF         = $enreg2['Libelle_Type'];
            $Code_ModifiableF = $enreg2['Code_Modifiable'];
            $Objet_CibleF     = $enreg2['Objet_Cible'];
            $UniciteF         = $enreg2['Unicite'];

            // Le type est-il utilisé ?
            // Si oui, on ne pourra pas modifier le code
            $sql = 'select 1 from ' . nom_table('evenements') . ' where Code_Type = \'' . $Code . '\' limit 1';
            $res = lect_sql($sql);
            $utilise = ($enregU = $res->fetch(PDO::FETCH_NUM));
            $res->closeCursor();
        }
    } else {
        $CodeF            = '';
        $LibelleF         = '';
        $Code_ModifiableF = 'O';
        $Objet_CibleF     = '';
        $UniciteF         = 'M';
    }

    // Type d'évènement inconnu, supprimé entre temps, retour...
    if ((!$Creation) and (!$enreg)) {
        aff_erreur(LG_EVENT_TYPE_DELETED);
        echo '<a href="' . $root . '/liste_referentiel.php?Type_Liste=T">' . $LG_Menu_Title['Event_Type_List'] . '</a>';
    } else {

        // Zone technique de la table non modifiable et non affichée
        $ACode_ModifiableF = $Code_ModifiableF;
        echo '<input type="' . $hidden . '" name="Code_ModifiableF" value="' . $Code_ModifiableF . '"/>' . "\n";
        echo '<input type="' . $hidden . '" name="ACode_ModifiableF" value="' . $Code_ModifiableF . '"/>' . "\n";

        $checked = ' checked="checked"';
        $larg_titre = '25';

        echo '<table width="80%" class="table_form">' . "\n";
        echo '<tr><td colspan="2">&nbsp;</td></tr>';

        colonne_titre_tab(LG_EVENT_TYPE_CODE);
        // On ne peut modifier le code qu'en création ou s'il n'est pas utilisé
        if (($Creation) or (! $utilise)) {
            echo '<input class="oblig" type="text" name="CodeF" id="CodeF" value="' . $CodeF . '" size="4" maxlength="4" onchange="verification_code(this);"/>' . "\n";
            echo '&nbsp;';
            echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';

            // Liste des types existants
            $codes = ' ';
            $sql = 'select Code_Type from ' . $n_types_evenement;
            $res = lect_sql($sql);
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                $codes .= $row[0] . ' ';
            }
            echo '<input type="' . $hidden . '" name="codes" value="' . $codes . '"/>' . "\n";
        } else
            echo $CodeF . "\n";
        echo '<input type="' . $hidden . '" name="ACodeF" value="' . $CodeF . '"/>' . "\n";
        echo '</td></tr>' . "\n";

        colonne_titre_tab(LG_EVENT_TYPE_LABEL);
        echo '<input type="text" name="LibelleF" value="' . $LibelleF . '" size="50"/>' . "\n";
        echo '<input type="' . $hidden . '" name="ALibelleF" value="' . $LibelleF . '"/>' . "\n";
        echo '</td></tr>' . "\n";

        colonne_titre_tab(LG_TARGET_OBJECT);
        bouton_radio('Objet_CibleF', 'P', LG_TARGET_OBJECT_PERS, ($Objet_CibleF == 'P'));
        bouton_radio('Objet_CibleF', 'U', LG_TARGET_OBJECT_UNION, ($Objet_CibleF == 'U'));
        bouton_radio('Objet_CibleF', 'F', LG_TARGET_OBJECT_FILIATION, ($Objet_CibleF == 'F'));
        bouton_radio('Objet_CibleF', '', LG_TARGET_OBJECT_OTHER, (($Objet_CibleF != 'U') && ($Objet_CibleF != 'F') && ($Objet_CibleF != 'P')));
        echo '<input type="' . $hidden . '" name="AObjet_CibleF" value="' . $Objet_CibleF . '"/>' . "\n";
        echo '</td></tr>' . "\n";

        colonne_titre_tab(LG_EVENT_TYPE_UNIQ);
        bouton_radio('UniciteF', 'U', $LG_Yes, ($UniciteF == 'U'));
        bouton_radio('UniciteF', 'M', $LG_No, ($UniciteF == 'M'));
        echo '<input type="' . $hidden . '" name="AUniciteF" value="' . $UniciteF . '"/>' . "\n";
        echo '</td></tr>' . "\n";

        echo '<tr><td colspan="2">&nbsp;</td></tr>';
        // Bouton Supprimer en modification si pas d'utilisation du rôle
        $lib_sup = '';
        if ((!$Creation) and (!$utilise)) $lib_sup = $lib_Supprimer;
        bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_EVENT_TYPE_THIS);

        echo '</table>' . "\n";

        echo "</form>";
    }

    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/' . $Icones['home'] . '" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';

    echo '<script type="text/javascript">' . "\n";
    echo 'function verification_code(zone) {' . "\n";
    echo ' var codes = document.forms.saisie.codes.value;' . "\n";
    echo ' var posi  = codes.indexOf(zone.value);' . "\n";
    echo ' if (posi > -1) {' . "\n";
    // echo '  window.alert("Attention, code déjà utilisé (codes présents :"+codes+").");'."\n";
    echo '  window.alert("Attention, code déjà utilisé ");' . "\n";
    echo '  zone.value = "";' . "\n";
    echo ' }' . "\n";
    echo '}' . "\n";
    echo '</script>' . "\n";
} ?>
</body>

</html>