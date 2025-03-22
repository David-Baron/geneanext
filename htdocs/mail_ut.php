<?php

//=====================================================================
// Envoi d'un mail aux utilisateurs du site
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('G')) {
    header('Location: ' . $root . '/');
    exit();
}

$max_emails = 49;
// R�duction du nombre d'emails autoris�s sur le site de test
if ($SiteGratuit) {
    if (strpos($_SERVER['REQUEST_URI'], 'test_geneamania') !== false) $max_emails = 2;
}

// R�cup�ration des variables de l'affichage pr�c�dent
$tab_variables = array('ok', 'annuler', 'Horigine', 'idents', 'sujet', 'message');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');
$idents   = Secur_Variable_Post($idents, 500, 'S');
$sujet    = Secur_Variable_Post($sujet, 80, 'S');
$message  = Secur_Variable_Post($message, 250, 'S');

// var_dump($idents);

// On n'autorise que les chiffres et la virgule
$idents = preg_replace('/([^,0-9]+)/i', '', $idents);

$titre = 'Envoi d\'un mail';        // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement

require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Interdit sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

$entetePage = $titre;

$compl = Ajoute_Page_Info(600, 200);
Insere_Haut($titre, $compl, 'mail_ut', '');

// Demande d'envoi des mails
if (($bt_OK) and ($sujet != '') and ($message != '')) {

    Ecrit_Entete_Page($titre, $contenu, $mots);

    // R�cup�ration des identifiants re�us
    $crit = rtrim($idents, ',');
    $req = 'select nom, Adresse from ' . nom_table('utilisateurs') . ' where idUtil in(' . $crit . ') and Adresse is not null';
    $res = lect_sql($req);
    if ($res->rowCount() > $max_emails) {
        echo '<center><font color="red"><br><br><br><h2>Le nombre de mails à envoyer est supérieur au maximum autorisé (' . $max_emails . ')</h2></font></center>';
    }
    // Envoi des messages
    else {
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            envoi_mail($enreg[1], $sujet, $message, '');
        }
    }

}

// Premi�re entr�e : affichage pour saisie
else {

    // R�cup�ration des identifiants d'utilisateurs re�us depuis la liste
    $nom_var = 'msg_ut_';
    $l_nom_var = strlen($nom_var);
    $idents = '';
    foreach ($_POST as $key => $value) {
        if (strpos($key, $nom_var) !== false) {
            $ind_var = substr($key, $l_nom_var);
            $idents .= $ind_var . ',';
        }
    }
    echo '<br />' . "\n";
    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'sujet,message\')">' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo '<input type="hidden" name="idents" value="' . $idents . '"/>' . "\n";
    echo '<input type="hidden" name="maxi" value="' . $max_emails . '"/>' . "\n";
    echo '<table width="70%" class="table_form">' . "\n";
    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    echo '<tr><td class="label" width="25%"> ' . ucfirst(LG_MAIL_SUBJECT) . ' </td>';
    echo '<td class="value">';
    echo '<input class="oblig" type="text" name="sujet" id="sujet" size="80" maxlength="80"/>&nbsp;' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="25%"> ' . ucfirst(LG_MAIL_MSG) . ' </td>';
    echo '<td class="value">';
    echo '<textarea cols="50" rows="5" name="message" id="message"></textarea>&nbsp;' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '</td></tr>' . "\n";

    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', true);

    echo '</table>';

    if ((!$SiteGratuit) and ($FromTo_Mail = 'support@geneamania.net')) {
        echo '<br><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Information" title="Information"> ' . LG_MAIL_FROM . $FromTo_Mail . '<br>' . "\n";
    }
    echo '</form>';
}

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';

?>
</body>

</html>