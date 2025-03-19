<?php
//=====================================================================
// Liste des connexions
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'L';
$titre = $LG_Menu_Title['Connections'];
$x = Lit_Env();
$niv_requis = 'G';
require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Sortie dans un fichier CSV ?
$csv_dem = Recup_Variable('csv', 'C', 'ce');
$CSV = false;
if ($csv_dem === 'c') $CSV = true;
if (($SiteGratuit) and (!$Premium)) $CSV = false;

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Récupération de la personne sélectionnée sur l'affichage précédent ou demandée
$id_Util = '-1';
$defaut = '-1';
if (isset($_POST['id_Util'])) $id_Util = $_POST['id_Util'];
$id_Util = Secur_Variable_Post($id_Util, 1, 'N');

$Util = Recup_Variable('Util', 'N');
if ($Util) $id_Util = $Util;

$compl = Ajoute_Page_Info(600, 150);
if ((!$SiteGratuit) or ($Premium)) {
    if ($_SESSION['estCnx']) {
        $filtre = '';
        if ($id_Util != -1) $filtre = '&amp;Util=' . $id_Util;
        $compl .= Affiche_Icone_Lien('href="' . $root . '/liste_connexions.php?csv=c' . $filtre . '"', 'exp_tab', my_html($LG_csv_export)) . '&nbsp;';
    }
}

Insere_Haut($titre, $compl, 'Liste_Connexions', '');

$n_utilisateurs = nom_table('utilisateurs');
$n_connexions   = nom_table('connexions');
$sel = ' selected="selected"';

echo '<form action="' . my_self() . '" method="post">' . "\n";
echo '<table border="0" width="50%" align="center">' . "\n";
echo '<tr align="center" class="rupt_table">';
echo '<td width="50%">' . LG_CH_CONN_LIST_USER . '&nbsp;:&nbsp;' . "\n";
echo '<select name="id_Util">' . "\n";
echo '<option value="' . $defaut . '"';
if ($id_Util == $defaut) echo $sel;
echo '>' . my_html($LG_All) . '</option>' . "\n";
$sql = 'select idUtil , nom from ' . $n_utilisateurs . ' order by nom';
$res = lect_sql($sql);
while ($row = $res->fetch(PDO::FETCH_NUM)) {
    echo '<option value="' . $row[0] . '"';
    if ($row[0] == $id_Util) echo $sel;
    echo '>' . my_html($row[1]) . '</option>';
}
echo '</select>' . "\n";
echo '</td>' . "\n";
echo '<td width="50%"><input type="submit" value="' . $LG_modify_list . '"/></td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";
echo '</form>' . "\n";

$crit_id_Util = '';
if ($id_Util != $defaut) $crit_id_Util = ' and c.idUtil = ' . $id_Util;

// Optimisation : préparation echo des images
$echo_modif = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '">';

// Constitution de la requête d'extraction des connexions
$sql = 'select c.idUtil, dateCnx, Adresse_IP, nom ' .
    'from ' . $n_utilisateurs . ' u, ' . $n_connexions . ' c ' .
    'where c.idUtil = u.idUtil ' .
    $crit_id_Util . ' order by dateCnx desc, nom ';
$res = lect_sql($sql);
if (!$CSV) {
    $num_lig = 0;
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $num_lig++;
        // 1ère ligne lue, on écrit l'entête du tableau
        if ($num_lig == 1) {
            echo '<table width="80%" border="0" class="classic" align="center">' . "\n";
            echo '<tr>';
            echo '<th width="40%">' . LG_CH_CONN_LIST_USER . '</th>';
            echo '<th width="30%">' . LG_CH_CONN_LIST_DATE . '</th>';
            echo '<th width="30%">' . LG_CH_CONN_LIST_IP . '</th>';
            echo '</tr>' . "\n";
        }
        echo '<tr>';
        echo '<td><a href="' . $root . '/fiche_utilisateur.php?code=' . $row[0] . '">' . $row[3];
        echo '</a>&nbsp;<a href="' . $root . '/edition_utilisateur.php?code=' . $row[0] . '">' . $echo_modif . '</td>';
        echo '<td>' . DateTime_Fr($row[1]) . '</td>';
        echo '<td>' . $row[2] . '</td>';
        echo '</tr>' . "\n";
    }
    if ($num_lig > 1) echo '</table>';
} else {
    // Sortie CSV
    if ($CSV) {
        $nom_fic = $chemin_exports . 'liste_connexions.csv';
        $fp = fopen($nom_fic, 'w+');

        // Ecriture entête
        $ligne = '';
        $ligne .= 'Id;';
        $ligne .= LG_CH_CONN_LIST_USER . ';';
        $ligne .= LG_CH_CONN_LIST_DATE . ';';
        $ligne .= LG_CH_CONN_LIST_IP . ';';
        fputs($fp, $ligne);

        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $ligne = '';
            $ligne .= $row[0] . ';';                    // Id
            $ligne .= $row[3] . ';';                    // Utilisateur
            $ligne .= DateTime_Fr($row[1]) . ';';        // Date de connexion
            $ligne .= $row[2] . ';';                    // Adresse IP
            fputs($fp, $ligne);
        }

        fclose($fp);
        echo '<br /><br />' . my_html($LG_csv_available_in) . ' <a href="' . $nom_fic . '" target="_blank">' . $nom_fic . '</a><br />' . "\n";
    }
}

$res->closeCursor();

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '?' . Query_Str());

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