<?php
//=====================================================================
// Liste des utilisateurs
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('G')) {
    header('Location: ' . $root . '/');
    exit();
}

function aff_option_niveau($niv_option)
{
    global $profil;
    echo '<option value="' . $niv_option . '"';
    if ($niv_option == $profil) echo ' selected="selected"';
    echo '>' . libelleNiveau($niv_option) . '</option>' . "\n";
}

$titre = $LG_Menu_Title['Users_List'];        // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

Insere_Haut($titre, '', 'Liste_utilisateurs', '');

// Possibilité d'insérer un utilisateur
echo LG_USERS_LIST_ADD . ' ' . Affiche_Icone_Lien('href="' . $root . '/edition_utilisateur?code=-----"', 'ajouter', $LG_add) . '<br /><br />' . "\n";

// Récupération du dépôt sélectionné sur l'affichage précédent
$profil = 'X';
$defaut = 'X';
if (isset($_POST['profil'])) $profil = $_POST['profil'];
$profil = Secur_Variable_Post($profil, 1, 'S');

// Verrouillage de la gestion des documents sur les gratuits non Premium
//if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Pas de mail possible sur les sites gratuits non Premium et en local
$mails = false;
if ($Environnement == 'I') {
    if ((!$SiteGratuit) or ($Premium)) $mails = true;
}
$mails = true;

echo '<form action="' . my_self() . '" method="post">' . "\n";
echo '<table width="50%" align="center">' . "\n";
echo '<tr align="center" class="rupt_table">';
echo '<td width="50%">' . my_html(LG_UTIL_PROFILE) . ' ' . "\n";
echo '<select name="profil">' . "\n";
echo '<option value="' . $defaut . '"';
if ($profil == $defaut) {
    echo ' selected="selected"';
}
echo '>Tous</option>' . "\n";
aff_option_niveau('I');
aff_option_niveau('P');
aff_option_niveau('C');
aff_option_niveau('G');
echo '</select>' . "\n";
echo '</td>' . "\n";
echo '<td width="50%"><input type="submit" value="' . $LG_modify_list . '"/></td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";
echo '</form>' . "\n";

$crit_profil = '';
if ($profil != $defaut) $crit_profil = ' where niveau = \'' . $profil . '\'';

// Constitution de la requête d'extraction
$sql = 'select idUtil , nom, codeUtil from ' . nom_table('utilisateurs') . $crit_profil . ' order by nom , codeUtil';
$res = lect_sql($sql);

if ($res->rowCount() > 0) {

    if ($mails) echo '<form id="saisie" method="post" action="'.$root.'/mail_ut">' . "\n";

    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        if ($mails)
            echo '<input type="checkbox" name="msg_ut_' . $row[0] . '" value="x" onclick="chkSel(this)"/> ';
        echo '<a href="' . $root . '/fiche_utilisateur?code=' . $row[0] . '">' . my_html($row[1] . ' - ' . $row[2]);
        echo '</a> <a href="' . $root . '/edition_utilisateur?code=' . $row[0] . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>' . "\n";
        echo "<br />\n";
    }

    if ($mails) {

        echo '<br><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Information" title="Information"> ' . LG_UTIL_CHK_MAIL . '<br>' . "\n";
        bt_ok_an_sup('Envoi de mails', '', '', '', false);
        echo '</form>';

        // On cache le bouton d'envoi de mail par défaut et on ne le montre que si au moins 1 case est cochée
        echo '<script type="text/javascript">' . "\n";
        echo '  document.getElementById("bouton_ok").style.visibility = "hidden";' . "\n";
        echo '  function chkSel(theElement) {' . "\n";
        echo '  	var nbSel = 0' . "\n";
        echo '  	var theForm = theElement.form;' . "\n";
        echo '  	for (i = 0; i < theForm.elements.length; i++) {' . "\n";
        echo '  		if (theForm.elements[i].type == \'checkbox\') {' . "\n";
        echo '  			if (theForm.elements[i].checked) nbSel++;' . "\n";
        echo '  		}' . "\n";
        echo '  	}' . "\n";
        echo '  	if (nbSel>0) document.getElementById("bouton_ok").style.visibility = "visible";' . "\n";
        echo '  	else document.getElementById("bouton_ok").style.visibility = "hidden";' . "\n";
        // echo '  	window.alert(nbSel);'."\n";
        echo '  }' . "\n";
        echo '</script>' . "\n";
    }
}
$res->closeCursor();

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>