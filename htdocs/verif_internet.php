<?php

// ============================================
// Vérification de la diffusabilité Internet
// ============================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('C')) {
    header('Location: ' . $root . '/');
    exit();
}

$tab_variables = array(
    'ok',
    'annuler',
    'S_Int',
    'idPers',
    'Horigine',
    'limite'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_Rectifier), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

$acces = 'M';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Internet_Cheking'];    // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

$dem_mod = 0;
$nb_mod = 0;

$compl = Ajoute_Page_Info(600, 200);
Insere_Haut(my_html($titre), $compl, 'Verif_Internet', '');

//  ===== Appliquer les corrections demandees
if ($ok == $lib_Rectifier) {
    $nombre = count($idPers);
    $deb_req  = 'update ' . nom_table('personnes') . ' set Diff_Internet = "N" where reference = ';
    for ($ligne = 0; $ligne < $nombre; $ligne++) {
        $idPersLu = $idPers[$ligne];
        if (! isset($S_Int[$ligne])) {
            $req = $deb_req . $idPersLu;
            $dem_mod++;
            $Res = maj_sql($req);
            $nb_mod += $enr_mod;
        }
    }
    if ($nb_mod) maj_date_site();
    $plu1 = pluriel($dem_mod);
    $plu2 = pluriel($nb_mod);
    echo '<br>' . $dem_mod . ' ' . my_html(LG_CHK_INTERNET_RESULT_1 . ' ; ' . $nb_mod . ' ' . LG_CHK_INTERNET_RESULT_2) . '<br>';
}

// ===== Lecture de la base
if ((isset($limite)) and ($limite != '')) {
    if (! is_numeric($limite)) $limite = $Lim_Diffu;
    $Lim_Diffu = $limite;
}
$A = date('Y') - $Lim_Diffu;
$M = date('m');
$J = date('d');
$xA = str_pad($A, 4, '0', STR_PAD_LEFT);
$xM = str_pad($M, 2, '0', STR_PAD_LEFT);
$xJ = str_pad($J, 2, '0', STR_PAD_LEFT);
$Date = $xA . $xM . $xJ;

$sql = 'SELECT Reference, Nom, Prenoms, Diff_Internet, Ne_Le, Decede_Le ' .
    ' FROM ' . nom_table('personnes') . ' WHERE Reference <> 0 and Diff_Internet = \'O\' ' .
    ' and ((Ne_Le > \'' . $Date . '\' and Ne_Le not like\'%A\') or (Decede_Le > \'' . $Date . '\' and Decede_Le not like\'%A\')) ' .
    ' ORDER BY Nom , Prenoms';
$res = lect_sql($sql);
$nbPers = $res->rowCount();

echo '<form id="saisie" method="post">';

bt_ok_an_sup($lib_Rectifier, $lib_Annuler, '', '', false);

$plu = pluriel($nbPers);
echo '<br>' . $nbPers . LG_CHK_INTERNET_PRES_1 . ' ' . $Lim_Diffu . ' ' . LG_CHK_INTERNET_PRES_2 . ' '
    . '<input type="text" size="3" name="limite" value="' . $Lim_Diffu . '"/> ' . LG_CHK_INTERNET_YEARS . "\n";
echo '<input type="submit" name="re" value="' . $LG_Check_Again . '"/>' . "\n";
echo '<br><br><a href="' . $root . '/verif_internet_absente">' . $LG_Menu_Title['Internet_Hidding_Cheking'] . '</a>';
echo '<br><br>';

echo '<table border="0" class="classic" cellspacing="1" cellpadding="3" align="center">' . "\n";
echo '<tr class="rupt_table">';
echo '<th>' . LG_CHK_INTERNET_PERSON . '</th>' . "\n";
echo '<th>' . $LG_show_on_internet;
echo ' <input type="checkbox" id="selTous" name="selTous" value="on" checked="checked" onclick="checkUncheckAll(this);"/> '
    . '<label for="selTous">' . $LG_All . '</label>';
echo '</th>' . "\n";
echo '<th>' . LG_CHK_INTERNET_BORN . '</th>' . "\n";
echo '<th>' . LG_CHK_INTERNET_DEATH . '</th>' . "\n";
echo '</tr>' . "\n";
$numLig = 0;
while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
    $idPers = $enreg[0];
    $internet = '';
    if ($enreg[3] == 'O') $internet = ' checked="checked"';
    if (pair($numLig)) $style = 'liste';
    else               $style = 'liste2';
    echo '<tr  class="' . $style . '">';
    echo '<td>';
    echo '<a href="' . $root . '/fiche_fam_pers?Refer=' . $enreg[0] . '">' . my_html($enreg[2] . ' ' . $enreg[1]) . '</a>' . "\n";
    echo ' <a href="' . $root . '/edition_personne?Refer=' . $enreg[0] . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
    echo '</td>' . "\n";
    echo '<td align="center">';
    echo  '<input type="checkbox" name="S_Int[' . $numLig . ']"' . $internet . '/>';
    echo  '<input type="hidden" name="idPers[' . $numLig . ']" value="' . $idPers . '"/>';
    echo '</td>' . "\n";
    echo '<td>' . Etend_Date($enreg[4]) . '</td>' . "\n";
    echo '<td>' . Etend_Date($enreg[5]) . '</td>' . "\n";
    echo '</tr>' . "\n";
    $numLig++;;
}

echo '</table>' . "\n";
echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' .$LG_tip . '" title="' . $LG_tip . '"> ' . my_html(LG_CHK_INTERNET_TIP);

echo '<br>';
echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
bt_ok_an_sup($lib_Rectifier, $lib_Annuler, '', '', false, true);
echo '</form>';

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