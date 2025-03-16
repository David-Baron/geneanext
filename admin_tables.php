<?php
//=====================================================================
// Administration des tables, Optimisation, réparation
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

// Récupération de la liste des tables dans un tableau
function recup_liste_tables()
{
    global $db, $pref_tables, $LG_Ch_Adm_T_Err_List;
    $liste_tables = [];
    $sql = 'SHOW TABLES FROM `' . $db . '` like \'' . $pref_tables . '%\'';
    $result = lect_sql($sql);
    if (!$result) {
        echo $LG_Ch_Adm_T_Err_List . '<br />';
        exit;
    }

    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tablename = $row[0];
        $sel = false;
        // Le filtre sur le préfixe ne doit être actif que si le préfixe est renseigné
        if ($pref_tables != '') {
            if (strpos($tablename, $pref_tables) === 0) $sel = true;
        } else $sel = true;
        // Restriction au préfixe à cause du like
        if ($sel) $liste_tables[] = $tablename;
    }
    $result->closeCursor();
    return  $liste_tables;
}

$lib_ok = $LG_Ch_Adm_T_lib_ok;

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'type_action',
    'n_table',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_ok), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_ok) $ok = 'OK'; // TODO: lol

// Gestion standard des pages
$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Tables_Admin'];
$niv_requis = 'G';                        // Page réservée au gestionnaire
$x = Lit_Env();                            // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Verrouillage de la page sur les gratuits
if ($SiteGratuit) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$type_action = Secur_Variable_Post($type_action, 15, 'S');

if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);

$compl = Ajoute_Page_Info(600, 250);
Insere_Haut(my_html($titre), $compl, 'Admin_Tables', '');

$liste_tables = recup_liste_tables();

if ($bt_OK) {

    // Détermination du type d'action demandé
    $Optimisation = ($type_action == 'O') ? true : false;
    $Reparation   = ($type_action == 'R') ? true : false;

    // Pour chaque table cochée
    if (isset($_POST['n_table'])) {
        $tab_tables = $_POST['n_table'];
        $long = 25;
        foreach ($tab_tables as $tablename) {
            $toto = array_search($tablename, $liste_tables);
            // Restriction de la longueur okazou
            if (strlen($tablename) > $long) $tablename = substr($tablename, 0, $long);
            // Exécution de l'action si la table a un nom existant
            if (array_search($tablename, $liste_tables) !== false) {
                //echo 'table : '.$tablename.'<br />';
                $sql = '';
                if ($Optimisation) $sql = 'OPTIMIZE TABLE `' . $tablename . '`';
                if ($Reparation)   $sql = 'REPAIR TABLE `' . $tablename . '`';
                $res = lect_sql($sql);
                if ($Optimisation) echo my_html($LG_Ch_Adm_T_Optim);
                if ($Reparation)   echo my_html($LG_Ch_Adm_T_Repair);
                echo ' de la table ' . $tablename . '<br />';
            }
        }
    }
}

echo '<form id="saisie" method="post">';
echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";

echo '<table width="60%" class="table_form">';
echo '<tr><td class="label" width="25%">' . $LG_Ch_Adm_T_Action . '</td><td class="value">';
echo '<input type="radio" name="type_action" value="R" checked="checked"/>' . my_html($LG_Ch_Adm_T_Repair);
echo '<input type="radio" name="type_action" value="O"/>' . my_html($LG_Ch_Adm_T_Optim);
echo '</td></tr>';
echo '<tr><td class="label" width="25%">' . $LG_Ch_Adm_T_Tables . '</td><td class="value">';
echo '<input type="checkbox" name="selTous" value="on" onclick="checkUncheckAll(this);"/>' . $LG_Ch_Adm_T_All_None . '<br /><hr/>';
$nb_tables = count($liste_tables);
for ($nb = 0; $nb < $nb_tables; $nb++) {
    $tablename = $liste_tables[$nb];
    echo '<input type="checkbox" name="n_table[]" value="' . $tablename . '"/>' . $tablename . '<br />';
}
echo '</td></tr>';

echo '<tr><td colspan="2"></td></tr>';
bt_ok_an_sup($lib_ok, $lib_Annuler, '', '');

echo '</table></form>';

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