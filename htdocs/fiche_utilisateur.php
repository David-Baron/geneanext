<?php

//=====================================================================
// Affichage d'un utilisateur
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['User'];

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$annuler = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$x = Lit_Env();
$niv_requis = 'G';
$code = Recup_Variable('code', 'N');

$req_sel = 'select * from ' . nom_table('utilisateurs') . " where idUtil = $code limit 1";

require(__DIR__ . '/../app/ressources/gestion_pages.php');

if ((!$enreg_sel) or ($code == 0)) Retour_Ar();

$enreg = $enreg_sel;
$compl = Affiche_Icone_Lien('href="' . $root . '/edition_utilisateur.php?code=' . $code . '"', 'fiche_edition', 'Edition fiche utilisateur') . '&nbsp;';

Insere_Haut($titre, $compl, 'Fiche_utilisateur', '');

echo '<br>';
echo '<table width="60%" class="table_form" align="center">' . "\n";
echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_NAME) . '</td><td class="value">' . $enreg['nom'] . '</td></tr>';
echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_CODE) . '</td><td class="value">' . $enreg['codeUtil'] . '</td></tr>';
echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_PROFILE) . '</td><td class="value">' . libelleNiveau($enreg['niveau']) . '</td></tr>';
echo '<tr><td class="label" width="30%">' . ucfirst(LG_UTIL_EMAIL) . '</td><td class="value">' . $enreg['Adresse'] . '</td></tr>';

$Last_cnx = '';
if ($Environnement == 'I') {
    $sql = 'select max(dateCnx) from ' . nom_table('connexions') . ' where idUtil = ' . $code;
    if ($res = lect_sql($sql)) {
        if ($row = $res->fetch(PDO::FETCH_NUM)) {
            $Last_cnx = $row[0];
            echo '<tr><td class="label" width="' . $larg_titre . '%">' . ucfirst(LG_UTIL_LAST_CNX) . '</td><td class="value">';
            if ($Last_cnx != '') echo DateTime_Fr($Last_cnx);
            else echo LG_UTIL_NO_CNX;
            echo '</td></tr>' . "\n";
        }
    }
    $res->closeCursor();
}
echo '</table>' . "\n";
if (($Environnement == 'I') && ($Last_cnx != '')) {
    echo '<br><a href="' . $root . '/liste_connexions.php?Util=' . $code . '">' . LG_UTIL_CONNEXIONS . '</a>';
}
Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
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