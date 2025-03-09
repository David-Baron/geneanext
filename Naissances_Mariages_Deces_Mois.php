<?php
//=====================================================================
// Statistiques de naissances, mariages et décès par mois
//=====================================================================

session_start();

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

include('fonctions.php');

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'L';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['BDM_Per_Month'];    // Titre pour META
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 200);
Insere_Haut($titre, $compl, 'Naissances_Mariages_Deces_Mois', '');

$l_col_1 = 15;                            // Largeur de la colonne 1
$l_col = (int)((100 - $l_col_1) / 4);        // Largeur des autres colonnes
$l_col_1 = 100 - ($l_col * 4);            // Pour retomber sur 100%...

$w_col_1 = ' width="' . $l_col_1 . '%"';
$w_col = ' width="' . $l_col . '%"';

if ($debug) {
    var_dump($l_col_1);
    var_dump($l_col);
    var_dump($w_col_1);
    var_dump($w_col);
}

// Initialisation des compteurs
for ($nb = 1; $nb <= 12; $nb++) {
    $nb_N[$nb] = 0;
    $nb_C[$nb] = 0;
    $nb_M[$nb] = 0;
    $nb_D[$nb] = 0;
}

// Extraction des données

$n_pers = nom_table('personnes');

// Naissances
$sql = 'SELECT count( * ) , substr(Ne_le, 5, 2) FROM ' . $n_pers . ' WHERE substr(Ne_le, 10, 1) = "L" ';
if (!$_SESSION['estPrivilegie']) $sql .= ' AND Diff_Internet = "O"';
$sql .= ' GROUP BY 2';
if ($res = lect_sql($sql)) {
    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
        $mois = intval($enreg[1]);
        $nb_N[$mois] = $enreg[0];
    }
    $res->closeCursor();
}

// Décès
$sql = 'SELECT count( * ) , substr(Decede_Le, 5, 2) FROM ' . $n_pers . ' WHERE substr(Decede_Le, 10, 1) = "L"';
if (!$_SESSION['estPrivilegie']) $sql .= ' AND Diff_Internet = "O"';
$sql .= ' GROUP BY 2';
if ($res = lect_sql($sql)) {
    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
        $mois = intval($enreg[1]);
        $nb_D[$mois] = $enreg[0];
    }
    $res->closeCursor();
}

// Mariages
$sql = 'SELECT count( * ) , substr(Maries_le, 5, 2)'
    . ' FROM ' . nom_table('unions')
    . ' WHERE substr(Maries_le, 10, 1) = "L"';
if (!$_SESSION['estPrivilegie']) $sql .=
    ' AND Conjoint_1 IN (SELECT Reference FROM ' . $n_pers . ' WHERE Diff_Internet = "O")' .
    ' AND Conjoint_2 IN (SELECT Reference FROM ' . $n_pers . ' WHERE Diff_Internet = "O")';
$sql     .= ' GROUP BY 2';
if ($res = lect_sql($sql)) {
    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
        $mois = intval($enreg[1]);
        $nb_M[$mois] = $enreg[0];
    }
    $res->closeCursor();
}

/*for ($nb =1; $nb <= 12; $nb++) {
	echo '<!--'.$nb.' : '.$nb_N[$nb].' / '. $nb_M[$nb].' / '.$nb_D[$nb].'<br>-->';
}*/

// Représentativité maximum ?
$max = 0;
$rep_max = 0;
for ($a = 1; $a <= 12; $a++) {
    if ($nb_N[$a] > $max) {
        $max = $nb_N[$a];
        $rep_max = $nb_N[$a];
    }
    if ($nb_M[$a] > $max) {
        $max = $nb_M[$a];
        $rep_max = $nb_M[$a];
    }
    if ($nb_D[$a] > $max) {
        $max = $nb_D[$a];
        $rep_max = $nb_D[$a];
    }
}

for ($m = 1; $m <= 12; $m++) {
    if ($m >= 10) $c = $m - 9;
    else $c = $m + 3;
    $nb_C[$c] = $nb_N[$m];
}

$largeur_max = 200;

echo '<br />';
echo '<table width="90%" border="0" class="classic" cellspacing="1" align="center" >' . "\n";
echo '<tr>' . "\n";
echo '<th' . $w_col_1 . '>' . $LG_Month . '</th>' . "\n";
echo '<th' . $w_col . '">' . $LG_birth_many . '</th>' . "\n";
echo '<th' . $w_col . '>' . $LG_conception . '</th>' . "\n";
echo '<th' . $w_col . '>' . $LG_wedding_many . '</th>' . "\n";
echo '<th' . $w_col . '>' . $LG_death_many . '</th>' . "\n";
echo '</tr>' . "\n";

$deb = ' <img align="bottom" src="' . $chemin_images;
$deb_nais = $deb . 'bb3.jpg" height="15" width="';
$deb_mar =  $deb . 'bv.jpg" height="15" width="';
$deb_dec =  $deb . 'br2.jpg" height="15" width="';

$LG_birth_one = lcfirst($LG_birth);
$LG_death_one = lcfirst($LG_death);
$LG_wedding_one = lcfirst($LG_wedding);
$LG_conception_one = lcfirst($LG_conception);

$LG_birth_many = lcfirst($LG_birth_many);
$LG_conception_many = lcfirst($LG_conception_many);
$LG_death_many = lcfirst($LG_death_many);
$LG_wedding_many = lcfirst($LG_wedding_many);

$tot_N = 0;
$tot_M = 0;
$tot_D = 0;
for ($m = 1; $m <= 12; $m++) {

    $nb_nai = $nb_N[$m];
    $nb_con = $nb_C[$m];
    $nb_mar = $nb_M[$m];
    $nb_dec = $nb_D[$m];

    $tot_N += $nb_nai;
    $tot_M += $nb_mar;
    $tot_D += $nb_dec;

    echo '<tr>' . "\n";

    // Mois
    echo '<td' . $w_col_1 . ' align="left">' . $Mois_Lib[$m - 1];
    echo "</td>\n";

    // Naissances
    echo '<td' . $w_col . ' align="right">' . "\n";
    if ($nb_nai > 0) $larg = $largeur_max / $max * $nb_nai;
    else             $larg = 1;
    $larg = intval($larg);
    echo $deb_nais . $larg . '" alt="Pourcent" title="';
    echo $nb_nai . '&nbsp;';
    if ($nb_nai == 1) echo $LG_birth_one;
    else echo $LG_birth_many;
    echo '"/></td>' . "\n";

    // Conceptions
    echo '<td' . $w_col . ' align="right">' . "\n";
    if ($nb_con > 0) $larg = $largeur_max / $max * $nb_con;
    else             $larg = 1;
    $larg = intval($larg);
    echo $deb_nais . $larg . '" alt="Pourcent" title="';
    echo $nb_con . '&nbsp;';
    if ($nb_con == 1) echo $LG_conception_one;
    else echo $LG_conception_many;
    echo '"/></td>' . "\n";

    // Mariages
    echo '<td' . $w_col . ' align="right">' . "\n";
    if ($nb_mar > 0) $larg = $largeur_max / $max * $nb_mar;
    else               $larg = 1;
    $larg = intval($larg);
    echo $deb_mar . $larg . '" alt="Pourcent" title="';
    echo $nb_mar . '&nbsp;';
    if ($nb_mar == 1) echo $LG_wedding_one;
    else echo $LG_wedding_many;
    echo '"/></td>' . "\n";

    // Décès
    echo '<td' . $w_col . ' align="right">' . "\n";
    if ($nb_dec > 0) $larg = $largeur_max / $max * $nb_dec;
    else               $larg = 1;
    $larg = intval($larg);
    echo $deb_dec . $larg . '" alt="Pourcent" title="';
    echo $nb_dec . '&nbsp;';
    if ($nb_dec == 1) echo $LG_death_one;
    else echo $LG_death_many;
    echo '"/></td>' . "\n";

    echo "</tr>\n";
}

// Totaux
echo '<tr>' . "\n";
// Partie centrale
echo '<td' . $w_col_1 . ' align="center"><b>Totaux</b></td>' . "\n";
// Naissances
echo '<td' . $w_col . ' align="right"><b>' . $tot_N . "</b></td>\n";
// Naissances
echo '<td' . $w_col . ' align="right"><b>' . $tot_N . "</b></td>\n";
// Mariages
echo '<td' . $w_col . ' align="right"><b>' . $tot_M . "</b></td>\n";
// Décès
echo '<td' . $w_col . ' align="right"><b>' . $tot_D . '</b></td>' . "\n";
echo "</tr>\n";

echo "</table>\n";
echo '<br />';

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '');

Insere_Bas($compl);
?>
</body>

</html>