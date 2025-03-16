<?php
//=====================================================================
// Edition d'un utilisateur
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

function aff_option_niveau($niv_option)
{
    global $niv;
    echo '<option value="' . $niv_option . '"';
    if ($niv_option == $niv) echo ' selected="selected"';
    echo '>' . libelleNiveau($niv_option) . '</option>' . "\n";
}

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'Horigine',
    'nom',
    'Anom',
    'codeUtil',
    'AcodeUtil',
    'motPasse',
    'motPasse3',
    'motPasse1',
    'motPasse2',
    'niv',
    'Aniveau',
    'Adresse',
    'AAdresse',
    'controle',
    'Envoi_Mail'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Recup de la variable passée dans l'URL : rôle
$Code = Recup_Variable('code', 'A');
if ($Code == '-----') $Creation = true;
else                  $Creation = false;

// Gestion standard des pages
$acces = 'M';                            // Type d'accès de la page
$niv_requis = 'G';                        // Page accessible pour les gestionnaires uniquement
// Titre pour META
if (!$Creation) $titre = $LG_Menu_Title['User_Edit'];
else $titre = $LG_Menu_Title['User_Add'];
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$nom        = Secur_Variable_Post($nom, 40, 'S');
$Anom       = Secur_Variable_Post($Anom, 40, 'S');
$codeUtil   = Secur_Variable_Post($codeUtil, 35, 'S');
$AcodeUtil  = Secur_Variable_Post($AcodeUtil, 35, 'S');
$motPasse   = Secur_Variable_Post($motPasse, 64, 'S');
$motPasse1  = Secur_Variable_Post($motPasse1, 35, 'S');
$niv        = Secur_Variable_Post($niv, 1, 'S');
$Aniveau    = Secur_Variable_Post($Aniveau, 1, 'S');
$Envoi_Mail = Secur_Variable_Post($Envoi_Mail, 2, 'S');
$Adresse    = Secur_Variable_Post($Adresse, 80, 'S');
$AAdresse   = Secur_Variable_Post($AAdresse, 80, 'S');

$n_utilisateurs = nom_table('utilisateurs');

if ($bt_Sup) {
    $req = 'delete from ' . $n_utilisateurs . ' where idUtil = ' . $Code;
    $res = maj_sql($req);
    Retour_Ar();
}
$mesErreur = '';

//Demande de mise à jour
if ($bt_OK) {
    if ($controle == '-') {
        $mesErreur .= LG_UTIL_ERROR_JS;
    }

    // Vérification si le code utilisateur existe déjà
    if ($codeUtil != $codeUtil) {
        $sql = 'select 1 from ' . $n_utilisateurs . ' where codeUtil = \'' . $codeUtil . '\' limit 1';
        $enreg = lect_sql($sql);
        if ($enreg->rowCount() > 0) {
            $mesErreur .= LG_UTIL_ERROR_EXISTS . ' (' . $codeUtil . ')<br />';
        }
    }

    // Le nom de l'utilisateur et le code utilisateur doivent être saisis
    //if ($nom == '') $mesErreur .= 'Veuillez saisir le nom de la personne ';
    //if ($codeUtil == '') $mesErreur .= 'Veuillez saisir le code utilisateur';

    //	hashage du mot de passe avec grains de sable
    $motPasseSha = '';
    if (strlen($motPasse) == 64) {
        // $motPasseSha = hash('sha256', ';$€°d' . $codeUtil . '#\'_^' . $motPasse . '@")[&ù');
        $motPasseSha = hash('sha256', $salt1 . $codeUtil . $salt2 . $motPasse . $salt3);
        // $salt1 = ';$€°d';
        // $salt2 = '#\'_^';
        // $salt3 = '@")[&ù';
    }
    //if ($Creation) {
    //	if ($motPasseSha == '') $mesErreur .= 'Veuillez saisir le mot de passe ';
    //}

    // Init des zones de requête
    $req = '';

    // Cas de la modification
    if (!$Creation and $mesErreur == '') {
        Aj_Zone_Req('nom', $nom, $Anom, 'A', $req);
        Aj_Zone_Req('codeUtil', $codeUtil, $AcodeUtil, 'A', $req);
        if ($motPasseSha != '') Aj_Zone_Req('motPasseUtil', $motPasseSha, '', 'A', $req);
        Aj_Zone_Req('niveau', $niv, $Aniveau, 'A', $req);
        Aj_Zone_Req('Adresse', $Adresse, $AAdresse, 'A', $req);
        if ($req != '') $req = 'update ' . $n_utilisateurs . ' set ' . $req . ' where idUtil = ' . $Code;
    }
    // Cas de la création
    if ($Creation and $mesErreur == '') {
        Ins_Zone_Req('', 'A', $req);
        Ins_Zone_Req($nom, 'A', $req);
        Ins_Zone_Req($codeUtil, 'A', $req);
        Ins_Zone_Req($motPasseSha, 'A', $req);
        Ins_Zone_Req($niv, 'A', $req);
        Ins_Zone_Req($Adresse, 'A', $req);
        if ($req != '') $req = 'insert into ' . $n_utilisateurs . ' values( ' . $req . ')';
    }
    if ($mesErreur == '') {
        if ($req != '') $res = maj_sql($req);
        if ($Envoi_Mail == 'on') {
            if ($Adresse != '') {
                $message = LG_UTIL_MAIL_1 . $nom . LG_UTIL_MAIL_2 . $codeUtil . LG_UTIL_MAIL_3 . $motPasse3 . ' .';
                envoi_mail($Adresse, LG_UTIL_MAIL_OBJ, $message, '', false);
            }
        }
        Retour_Ar();
    }
}

// Première entrée : affichage pour saisie
if (((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) || $mesErreur != '') {
    $compl = Ajoute_Page_Info(900, 450);

    if (!$Creation)
        $compl .= Affiche_Icone_Lien('href="' . $root . '/fiche_utilisateur.php?code=' . $Code . '"', 'page', $LG_Menu_Title['User']) . '&nbsp;';

    Insere_Haut($titre, $compl, 'Edition_Utilisateur', $Code);
    include(__DIR__ . '/assets/js/ctrlMotPasse.js');

    echo '<form id="saisie" method="post" action="' . my_self() . '?' . Query_Str() . '">' . "\n";

    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo '<input type="hidden" name="motPasse" value=""/>' . "\n";
    echo '<input type="hidden" name="code_pers" value="' . $Code . '"/>' . "\n";
    echo '<input type="hidden" name="controle" id="controle" value="-"/>' . "\n";
    //	Message d'erreur
    if ($mesErreur != '') {
        echo '<center><font color="red"><br><br><br><h2>' . my_html($mesErreur) . '</h2></font></center>';
    }

    if (!$Creation) {
        $sql = 'select * from ' . $n_utilisateurs . ' where idUtil = ' . $Code . ' limit 1';
        $res    = lect_sql($sql);
        $enreg  = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
        Champ_car($enreg2, 'nom');
        Champ_car($enreg2, 'codeUtil');
        unset($enreg);
        $nom        = $enreg2['nom'];
        $codeUtil   = $enreg2['codeUtil'];
        $motPasse   = '';
        $niv        = $enreg2['niveau'];
        $Adresse_Ut = $enreg2['Adresse'];
    } else {
        $nom        = '';
        $codeUtil   = '';
        $motPasse   = '';
        $niv        = '';
        $Adresse_Ut = '';
    }

    echo '<input type="hidden" name="motPasse3" value=""/>' . "\n";
    echo '<table width="80%" class="table_form">' . "\n";
    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_NAME) . '</td><td class="value">';
    echo '<input class="oblig" type="text" name="nom" id="nom" value="' . $nom . '" size="40" maxlength="40"/>&nbsp;' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="hidden" name="Anom" value="' . $nom . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_CODE) . '</td><td class="value">';
    echo '<input class="oblig" type="text" name="codeUtil" id="codeUtil" value="' . $codeUtil . '" size="35"/>' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="hidden" name="AcodeUtil" value="' . $codeUtil . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_PSW) . '</td><td class="value">';
    echo '<input type="password" ';
    if ($Creation) echo ' class="oblig"';
    echo ' name="motPasse1" value="" size="35"/>' . "\n";
    if ($Creation) echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '&nbsp;<input type="button" onclick=" generer_passe()" value="' . LG_UTIL_PSW_GENER . '"/>&nbsp;';
    echo '<input type="text" readonly="readonly" name="PasseGen" id="PasseGen" value="" size="12"/>' . "\n";
    echo '&nbsp;<img src="' . $root . '/assets/img/' . $Icones['copier'] . '" alt = "' . LG_UTIL_PSW_COPY . '" title = "' . LG_UTIL_PSW_COPY
        . '" onclick="document.forms.saisie.motPasse1.value = document.forms.saisie.PasseGen.value;document.forms.saisie.motPasse2.value = document.forms.saisie.PasseGen.value;"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_PSW_CONFIRM) . '</td><td class="value">';
    echo '<input type="password" ';
    if ($Creation) echo ' class="oblig"';
    echo ' name="motPasse2" value="" size="35"/>' . "\n";
    if ($Creation) echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_PROFILE) . '</td><td class="value">';
    // Le niveau n'est pas modifiable pour le user de connexion
    if ((!$Creation) and ($Code == $_SESSION['idUtil'])) {
        echo libelleNiveau($niv);
        echo '<input type="hidden" name="niv" value="' . $niv . '"/>' . "\n";
    } else {
        echo '<select name="niv">' . "\n";
        aff_option_niveau('I');
        aff_option_niveau('P');
        aff_option_niveau('C');
        aff_option_niveau('G');
        echo '</select>' . "\n";
    }
    echo '<input type="hidden" name="Aniveau" value="' . $niv . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_EMAIL) . '</td><td class="value">';
    echo '<input type="text" name="Adresse" value="' . $Adresse_Ut . '" size="80"/>' . "\n";
    echo '<input type="hidden" name="AAdresse" value="' . $Adresse_Ut . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    // Possibilité d'envoyer un mail à la création de l'utilisateur
    if ((!$SiteGratuit) or ($Premium)) {
        if (($Creation) and ($Environnement == 'I')) {
            echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_SEND_MAIL) . '</td><td class="value">';
            echo '<input type="checkbox" name="Envoi_Mail"/>' . "\n";
            echo '</td></tr>' . "\n";
        }
    }

    echo '<tr><td colspan="2">&nbsp;</td></tr>';

    echo '<tr><td colspan="2" align="center">';

    $lib_sup = '';
    if ((!$Creation) and ($Code != $_SESSION['idUtil'])) {
        $lib_sup = $lib_Supprimer;
    }

    echo '<input type="hidden" name="cache" id="cache" value=""/>' . "\n";
    echo '<input type="hidden" name="ok" id="ok" value=""/>' . "\n";
    echo '<input type="hidden" name="annuler" id="annuler" value=""/>' . "\n";
    echo '<input type="hidden" name="supprimer" id="supprimer" value=""/>' . "\n";

    echo '<div id="boutons">' . "\n";
    echo '<br />';
    echo '<table border="0" cellpadding="0" cellspacing="0">' . "\n";
    echo '<tr><td>&nbsp;';
    echo '<div class="buttons">';
    echo '<button type="submit" class="positive" ' .
        'onclick=";document.forms.saisie.cache.value=\'ok\';document.forms.saisie.ok.value=\'OK\';return avantEnvoiEU(this.form);"> ' .
        '<img src="' . $root . '/assets/img/' . $Icones['fiche_validee'] . '" alt=""/>' . $lib_Okay . '</button>';
    echo '<button type="submit" ' .
        'onclick="document.forms.saisie.cache.value=\'an\';document.forms.saisie.annuler.value=\'' . $lib_Annuler . '\';"> ' .
        '<img src="' . $root . '/assets/img/' . $Icones['cancel'] . '" alt=""/>' . $lib_Annuler . '</button>';
    if ($lib_sup != '')
        echo '<button type="submit" class="negative" ' .
            'onclick="confirmer(\'' . LG_UTIL_THIS . '\',this);"> ' .
            '<img src="' . $root . '/assets/img/' . $Icones['supprimer'] . '" alt=""/>' . $lib_sup . '</button>';
    echo '</div>';
    echo '</td></tr>';
    echo '</table>' . "\n";
    echo '</div>' . "\n";

    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";

    if ((!$Creation) and ($Code == $_SESSION['idUtil'])) {
        echo '<br /><img src="' . $root . '/assets/img/' . $Icones['warning'] . '" alt="Attention" title="Attention"> ' . LG_UTIL_WARN . '.';
    }

    echo "</form>";

    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
} ?>
</body>

</html>