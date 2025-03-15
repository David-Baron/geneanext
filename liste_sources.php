<?php
//====================================================================
//  Affichage de la liste des sources éventuellement par dépôt
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Source_List'];            // Titre pour META
$x = Lit_Env();
$niv_requis = 'C';                        // Page réservée au profil contributeur
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Verrouillage sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

$t = $titre;
$compl = Ajoute_Page_Info(600, 150);

Insere_Haut($t, $compl, 'Liste_Sources', '');

$n_sources = nom_table('sources');

// Récupération du dépôt sélectionné sur l'affichage précédent
$depot = -1;
$defaut = -1;
if (isset($_POST['depot'])) $depot = $_POST['depot'];
$depot = Secur_Variable_Post($depot, 1, 'N');

// Le dépôt est-il passé dans l'URL ?
if ($depot == -1) {
    $depot = Recup_Variable('depot', 'N');
    if (!$depot) $depot = -1;
}

$sql = 'select Ident, Nom from ' . nom_table('depots') . ' order by Nom';

echo '<form method="post">' . "\n";
echo '<table width="50%" align="center">' . "\n";
echo '<tr align="center" class="rupt_table">';
echo '<td width="50%">' . my_html(LG_SRC_REPO) . ' ' . "\n";
echo '<select name="depot">' . "\n";
echo '<option value="' . $defaut . '"';
if ($depot == $defaut) {
    echo ' selected="selected"';
}
echo '>' . my_html($LG_All) . '</option>' . "\n";
if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($depot == $row[0]) {
            echo ' selected="selected"';
        }
        echo '>' . my_html($row[1]) . '</option>' . "\n";
    }
}
$res->closeCursor();
echo '</select>' . "\n";
echo '</td>' . "\n";
echo '<td width="50%"><input type="submit" value="' . my_html($LG_modify_list) . '"/></td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";
echo '</form>' . "\n";

// Lien direct sur la dernière source saisie
$MaxRef = 0;
$requete = 'SELECT MAX(Ident) FROM ' . $n_sources;
$result = lect_sql($requete);
if ($enreg = $result->fetch(PDO::FETCH_NUM)) {
    $MaxRef = $enreg[0];
}
if ($MaxRef > 0) {
    echo '<a href="' . $root . '/edition_source.php?ident=' . $MaxRef . '">' . my_html(LG_SRC_LAST) . '</a><br />';
}

// Possibilité d'insérer une source
echo my_html(LG_SRC_ADD) . ' ' . Affiche_Icone_Lien('href="' . $root . '/edition_source.php?ident=-1"', 'ajouter', $LG_add) . '<br /><br />';

//  Affichage des sources
$crit_depot = '';
if ($depot != -1) $crit_depot = ' WHERE Ident_Depot = ' . $depot;

// Constitution de la requête d'extraction
$requete = 'SELECT Ident,Titre FROM ' . $n_sources . $crit_depot . ' ORDER BY Titre';
$result = lect_sql($requete);

while ($enreg = $result->fetch(PDO::FETCH_NUM)) {
    $ident = $enreg[0];
    echo '<a href="' . $root . '/fiche_source.php?ident=' . $ident . '">' . my_html($enreg[1]) . '</a> ';
    echo ' <a href="' . $root . '/edition_source.php?ident=' . $ident . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' .$LG_modify . '" title="' . $LG_modify . '"></a>';
    // '';
    echo '<br />' . "\n";
}
echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/' . $Icones['home'] . '" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>