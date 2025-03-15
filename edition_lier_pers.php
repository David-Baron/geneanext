<?php

//=====================================================================
// Lien entre deux persones
// Parametres a renseigner :
// - refPers1 : la reference de la 1re personne
// - refPers2 : la reference de la 2e personne (mettre -1 pour creer un lien) TODO: lol
// - refRolePar : l'identifiant de la table role
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'Horigine',
    'annuler',
    'supprimer',
    //    champs recus de la transaction precedente et sauvegardes dans cette page
    'refPers2F',
    'personnes',
    //    anciennes valeurs
    'roleAnc',
    'dDebAnc',
    'dFinAnc',
    'APrincipalF',
    //    valeurs saisies dans le formulaire
    'refRoleF',
    'dDebCache',
    'dFinCache',
    'PrincipalF'
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
$acces = 'M';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Link_Pers'];    // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

//$refPers2F   = Secur_Variable_Post($refPers2F,1,'N');
$personnes   = Secur_Variable_Post($personnes, 1, 'N');
$roleAnc     = Secur_Variable_Post($roleAnc, 4, 'S');
$dDebAnc     = Secur_Variable_Post($dDebAnc, 10, 'S');
$dFinAnc     = Secur_Variable_Post($dFinAnc, 10, 'S');
$APrincipalF = Secur_Variable_Post($APrincipalF, 1, 'S');
$refRoleF    = Secur_Variable_Post($refRoleF, 4, 'S');
$dDebCache   = Secur_Variable_Post($dDebCache, 10, 'S');
$dFinCache   = Secur_Variable_Post($dFinCache, 10, 'S');
$PrincipalF  = Secur_Variable_Post($PrincipalF, 1, 'S');

// Recup des variables passées dans l'URL
$refPers1 = Recup_Variable('ref1', 'N');
$refPers2 = Recup_Variable('ref2', 'N');
$orig = Recup_Variable('orig', 'N');
$refRolePar = Recup_Variable('role', 'S');

$Creation = false;
if ($refPers2 == -1) $Creation = true;

//  Pour retourner a la page precedente ou non
$retourPreced = false;

//  preparation des traitements
$valeurs = '';
$traitCre = false;
$traitMaj = false;
$traitSup = false;
$message = '';

//Demande de mise à jour
if ($clic_boutons) {
    //  Suppression du lien
    if ($bt_Sup) {
        $message .= 'Suppression d\'un lien<br />';
        $requete  = 'DELETE FROM ' . nom_table('relation_personnes') . ' WHERE Personne_1 = ' . $refPers1
            .= ' AND Personne_2 = ' . $refPers2 . ' AND Code_Role = \'' . $refRolePar . '\'';
        $result   = maj_sql($requete);
        if ($result == 1) {
            $message .= 'Suppression correcte<p>';
            $retourPreced = true;
            maj_date_site();
        }
    }

    //  Création ou modification
    if ($bt_OK) {
        //  Creation
        if ($Creation) {
            // Avant de créer, on vérifie que le lien n'existe pas déjà
            $req_ex  = 'select 1 from ' . nom_table('relation_personnes') . ' where Personne_1 = ' . $refPers1 .
                ' and Personne_2 = ' . $personnes . ' and Code_Role = "' . $refRoleF . '" limit 1';
            $res = lect_sql($req_ex);
            $existe = false;
            if ($row = $res->fetch(PDO::FETCH_NUM)) {
                $existe = true;
            }
            // Pas de création si le lien existe déjà
            if (!$existe) {
                $message .= 'Cr&eacute;ation d\'un nouveau lien<br />';
                Ins_Zone_Req($refPers1, 'N', $valeurs);
                Ins_Zone_Req($personnes, 'N', $valeurs);
                Ins_Zone_Req($refRoleF, 'A', $valeurs);
                // pour éviter le null...
                $zones = explode(',', $valeurs);
                if ($zones[count($zones) - 1] == 'null') {
                    $zones[count($zones) - 1] = '\'\'';
                    $valeurs = implode(',', $zones);
                }
                Ins_Zone_Req($dDebCache, 'A', $valeurs);
                Ins_Zone_Req($dFinCache, 'A', $valeurs);
                Ins_Zone_Req($PrincipalF, 'A', $valeurs);
                $requete  = 'INSERT INTO ' . nom_table('relation_personnes') . " (Personne_1,Personne_2,Code_Role,Debut,Fin,Principale) ";
                $requete .= "VALUES ($valeurs)";
                $result = maj_sql($requete);
                if ($result == 1) $message .= 'Cr&eacute;ation correcte<p>';
            } else $message .= 'Cr&eacute;ation impossible car lien d&eacute;j&agrave; pr&eacute;sent<p>';
            $retourPreced = true;
            maj_date_site();
        }
        //  Mise a jour
        else {
            // Bascule de l'indicateur principal ?
            if ($PrincipalF != $APrincipalF) {
                if ($orig == 2) {
                    if ($APrincipalF == 'O') {
                        $APrincipalF = 'N';
                    } else {
                        if ($APrincipalF == 'N') $APrincipalF = 'O';
                    }
                    if ($PrincipalF == 'O') {
                        $PrincipalF = 'N';
                    } else {
                        if ($PrincipalF == 'N') $PrincipalF = 'O';
                    }
                }
                Aj_Zone_Req('Principale', $PrincipalF, $APrincipalF, 'A', $valeurs);
            }

            $message .= 'Mise &agrave; jour d\'un lien<br />';
            Aj_Zone_Req('Code_Role', $refRoleF, $roleAnc, 'A', $valeurs);
            Aj_Zone_Req('Debut', $dDebCache, $dDebAnc, 'A', $valeurs);
            Aj_Zone_Req('Fin', $dFinCache, $dFinAnc, 'A', $valeurs);
            if ($valeurs != '') {
                $requete  = 'UPDATE ' . nom_table('relation_personnes') . " SET $valeurs";
                $requete .= " WHERE Personne_1 = $refPers1 AND Personne_2 = $refPers2 AND Code_Role = '$refRolePar'";
                $result = maj_sql($requete);
                if ($result == 1) {
                    $message .= 'Mise &agrave; jour correcte<p>';
                    $retourPreced = true;
                    maj_date_site();
                }
            } else $retourPreced = true;
        }
    }

    //  Retour à la page précédente
    if ($retourPreced) Retour_Ar();
} else {
    //
    //  ========== Programme principal ==========

    //  Affichage pour saisie

    // Récupération des noms des personnes
    $x = Get_Nom_Prenoms($refPers1, $Nom1, $Prenoms1);
    if (!$Creation)
        $x = Get_Nom_Prenoms($refPers2, $Nom2, $Prenoms2);

    if ($Creation) {
        $Prenoms = $Prenoms1;
        $Nom = $Nom1;
    } else {
        if ($orig == 1) {
            $Prenoms = $Prenoms1;
            $Nom = $Nom1;
        } else {
            $Prenoms = $Prenoms2;
            $Nom = $Nom2;
        }
    }
    $titre = $LG_Link_Pers_With . $Prenoms . ' ' . $Nom;

    $compl = Ajoute_Page_Info(600, 300);
    Insere_Haut($titre, $compl, 'Edition_Lier_Pers', '');

    //  Valeurs par defaut
    $RoleLu   = '';
    $dDebLue  = ' ';
    $dFinLue  = ' ';
    $PrincLue = 'O';
    if (!$Creation) {
        $requete  = 'SELECT * FROM ' . nom_table('relation_personnes') .
            " WHERE Personne_1 = $refPers1 AND Personne_2 = $refPers2";
        $result    = lect_sql($requete);
        $enreg  = $result->fetch(PDO::FETCH_ASSOC);
        //  Valeurs lues dans la base
        if ($enreg) {
            $RoleLu   = $enreg['Code_Role'];
            $dDebLue  = $enreg['Debut'];
            $dFinLue  = $enreg['Fin'];
            $PrincLue = $enreg['Principale'];
            // On retourne éventuellement l'indicateur en fonction de la personne dont on vient
            if ($orig == 2) {
                if ($PrincLue == 'O') {
                    $PrincLue = 'N';
                } else {
                    if ($PrincLue == 'N') $PrincLue = 'O';
                }
            }
        }
    }
    //  Debut de la page
    echo '<form id="saisie" method="post" action="' . my_self() . '?' . Query_Str() . '">' . "\n";

    echo '<input type="' . $hidden . '" name="maxi" id="maxi" />';

    echo '<input type="' . $hidden . '" id="creation" value = "';
    if ($Creation) echo 'o';
    else echo 'n';
    echo '" />';

    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo "<br />";

    $larg_titre = '30';
    echo '<table width="75%" class="table_form" align="center">' . "\n";

    //  En création, on peut choisir la personne
    if ($Creation) {

        // On retient les noms de personnes qui ne sont pas déjà reliées
        // les évènements uniques qui ne sont pas déjà utilisés
        $sql_types = 'SELECT DISTINCT p.idNomFam, p.Nom' .
            ' FROM ' . nom_table('personnes') . ' p' .
            ' WHERE p.Reference <> ' . $refPers1 . ' and p.Reference <> 0' .
            '  and reference not in (select Personne_1 from ' . nom_table('relation_personnes') . ' where Personne_2 = ' . $refPers1 . ')' .
            '  and reference not in (select Personne_2 from ' . nom_table('relation_personnes') . ' where Personne_1 = ' . $refPers1 . ')' .
            ' ORDER by p.Nom';
        $res = lect_sql($sql_types);

        echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Name . '&nbsp;</td><td class="value">';
        echo '<select name="nom" id="noms" onchange="updatePersonnes(this.value)" class="oblig">';
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            echo '<option value="' . $enreg[0] . '">' . $enreg[1] . '</option>';
        }
        echo '</select>' . "\n";
        echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
        echo "</td></tr>\n";
        echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Link_Pers_Pers . '&nbsp;</td><td class="value">';
        echo '<select name="personnes" id="personnes" class="oblig"></select>';
        echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
        echo '<div class="buttons">';
        echo '<button type="submit" class="positive" ' .
            'onclick="document.forms.saisie.personnes.value = document.forms.saisie.maxi.value;return false;"> ' .
            '<img src="' . $chemin_images_icones . $Icones['dernier_ajoute'] . '" alt=""/>' . my_html($LG_Link_Pers_Last_Pers) . '</button>';
        echo '</div>';
        echo "</td></tr>\n";
    }
    //  En modification, la personne est fixe
    else {
        echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Link_Pers_Pers . '&nbsp;</td><td class="value">';
        if ($orig == 1) echo '&nbsp;' . $Prenoms2 . '&nbsp;' . $Nom2 . "\n";
        else echo '&nbsp;' . $Prenoms1 . '&nbsp;' . $Nom1 . "\n";
        echo '</td></tr>' . "\n";
    }

    // Rôle
    $refRole = '';
    $requete = 'SELECT Code_Role, Libelle_Role FROM ' . nom_table('roles') . ' ORDER BY Libelle_Role';
    $result = lect_sql($requete);

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Link_Pers_Role . '&nbsp;</td><td class="value">';
    echo '<select name="refRoleF" class="oblig">' . "\n";
    while ($enreg = $result->fetch(PDO::FETCH_NUM)) {
        $code = $enreg[0];
        echo '<option value="' . $code . '"';
        if ($code == $RoleLu) echo ' selected="selected"';
        echo '>' . my_html($enreg[1]) . "</option>\n";
    }
    echo '</select>&nbsp;';
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="' . $hidden . '" name="roleAnc" value="' . $RoleLu . '"/>' . "\n";
    echo "</td></tr>\n";

    //  ===== Date de début de lien
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Link_Pers_Beg . '&nbsp;</td><td class="value">';
    zone_date2('dDebAnc', 'dDebAff', 'dDebCache', $dDebLue);
    echo "</td></tr>\n";

    //  ===== Date de fin de lien
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Link_Pers_End . '&nbsp;</td><td class="value">';
    zone_date2('dFinAnc', 'dFinAff', 'dFinCache', $dFinLue);
    $txt_img = $LG_Link_Pers_Copy_Date;
    echo '&nbsp;&nbsp;<img src="' . $chemin_images_icones . $Icones['copie_calend'] .
        '" alt="' . $txt_img . '"  title="' . $txt_img . '"onclick="copieDate();"/>' . "\n";
    echo "</td></tr>\n";

    // ==== Personnage principal
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . my_html($Prenoms) . ' ' . my_html($Nom) . ', ' . $LG_Link_Pers_Main . '&nbsp;</td><td class="value">';
    echo '<input type="radio" id="PrincipalF_O" name="PrincipalF" value="O"';
    if ($PrincLue == 'O') echo ' checked';
    echo '/><label for="PrincipalF_O">' . $LG_Yes . '</label>&nbsp;';
    echo '<input type="radio" id="PrincipalF_N" name="PrincipalF" value="N"';
    if ($PrincLue == 'N') echo ' checked';
    echo '/><label for="PrincipalF_N">' . $LG_No . '</label>&nbsp;';
    echo '<input type="radio" id="PrincipalF_I" name="PrincipalF" value="I"';
    if (($PrincLue == 'I') or ($Creation)) echo ' checked';
    echo '/><label for="PrincipalF_I">' . $LG_Link_Pers_No_Matter . '</label>';
    echo '<input type="' . $hidden . '" name="APrincipalF" value="' . $PrincLue . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    // Bouton Supprimer en modification si pas de lien
    $lib_sup = '';
    if ($refPers2 != -1) $lib_sup = $lib_Supprimer;
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, $LG_this_link);

    echo "</table>";
    echo "</form>\n";

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
<script type="text/javascript">
    function updatePersonnes(id) {
        //window.alert('rpc_Personne.php?idNomFam=' + id);
        xhr.open('get', 'rpc_Personne.php?idNomFam=' + id);
        //xhr.open('get', 'rpc.php?type_evt=' + types_evt + '&ref=' + document.getElementById('ref').value);
        xhr.onreadystatechange = handleResponse;
        xhr.send(null);
    }

    function handleResponse() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
            // Récupération de la liste des évènements
            var data = xhr.responseXML.getElementsByTagName('personnes');
            document.getElementById('personnes').innerHTML = '';
            for (var i = 0; i < data.length; i++) {
                //window.alert(data[i].getAttribute("id"));
                var option = document.createElement('option');
                option.setAttribute('value', data[i].getAttribute("id"));
                option.appendChild(document.createTextNode(data[i].firstChild.nodeValue));
                document.getElementById('personnes').appendChild(option);
            }
            //window.alert(document.getElementById('personnes').innerHTML);
            // Récupération du maxi des évènements
            var data2 = xhr.responseXML.getElementsByTagName('maxi');
            document.getElementById('maxi').value = data2[0].firstChild.nodeValue;

        }
    }

    function initForm() {
        if (document.getElementById('creation').value == 'o') {
            document.getElementById('noms').selectedIndex = 0;
            updatePersonnes(document.getElementById('noms').value);
        }
    }

    if (window.addEventListener) {
        window.addEventListener("load", initForm, false);
    } else if (window.attachEvent) {
        window.attachEvent("onload", initForm);
    }

    function sel_der() {
        document.forms.saisie.refPers2F.value = document.forms.saisie.refMax.value;
    }

    function copieDate() {
        document.forms.saisie.dFinAff.value = document.forms.saisie.dDebAff.value;
        document.forms.saisie.dFinCache.value = document.forms.saisie.dDebCache.value;
    }

    var xhr = getXMLHttpRequest();
</script>

</body>

</html>