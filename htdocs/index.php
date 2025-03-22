<?php

//=====================================================================
// 
//=====================================================================

$nom_page = 'index.php';

require(__DIR__ . '/../app/ressources/fonctions.php');

$index_follow = 'IF';
$meta_title = ($settings['Nom'] !== '???' ? $settings['Nom'] : 'Site genealogique') . ' | Accueil';
$meta_robots = '';
if ($index_follow != 'IF') {
    $p1 = '';
    $p2 = '';
    if ($index_follow[0] == 'N') $p1 = 'NO';
    if ($index_follow[1] == 'N') $p2 = 'NO';
    $meta_robots = $p1 . 'INDEX, ' . $p2 . 'FOLLOW';
}
$title = ($settings['Nom'] !== '???') ? $LG_index_welcome . ' ' . $settings['Nom'] : 'Site genealogique en construction';

echo '<!DOCTYPE html>';
echo '<html lang="' . $locale . '">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>' . my_html($meta_title) . '</title>';
echo '<meta name="description" content="' . $LG_index_desc . '">';
echo '<meta name="keywords" content="' . '$mots' . '">';
echo '<meta name="robots" content="' . $meta_robots . '">';
echo '<meta name="REVISIT-AFTER" content="7 days">';
echo '<link rel="shortcut icon" href="' . $root . '/assets/favicon.ico" type="image/x-icon">';
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">';
echo '</head>';
echo '<body>';
echo '<div class="container-fluid">';
echo '<div class="row text-center">';
echo '<h1>' . $title . '</h1>';
echo '</div>';

require(__DIR__ . '/_menu_bs.php');

echo '<main>';

echo '<div class="row">';

/* echo '<table width="60%" align="center">';
echo '<tr><td><fieldset style="width:90%;"><legend>' . $LG_index_quick_search . ' <img src="' . $root . '/assets/img/' . $Icones['help'] . '" alt="' . $LG_index_tip_search . '" title="' . $LG_index_tip_search . '"></legend>';
echo '<table align="center">';
echo '<tr><td>';
echo '<fieldset><legend>' . $LG_index_menu_pers . '</legend>';
echo '<form method="post" action="' . $root . '/Recherche_Personne" >';
echo '<table>';
echo '<tr><td>' . LG_PERS_NAME . ' :</td><td><input type="text" size="30" name="NomP"/></td>';
echo '<td rowspan="2" valign="middle"><input type="submit" name="ok" value="' . $lib_Rechercher . '" style="background:url(' . $root . '/assets/img/' . $Icones['chercher'] . ') no-repeat;padding-left:18px;" /></td></tr>';
echo '<tr><td>' . LG_PERS_FIRST_NAME . ' :</td><td><input type="text" size="30" name="Prenoms"/></td></tr>';
echo '</table>';
echo '<input type="hidden" name="Sortie" value="e">';
echo '<input type="hidden" name="Son" value="o">';
echo '</form>';
echo '</fieldset>';
echo '</td>';
echo '<td valign="middle">';
echo '<fieldset><legend>' . $LG_index_menu_towns . '</legend>';
echo '<form method="post" action="' . $root . '/Recherche_Ville" >';
echo '<input type="text" size="30" name="NomV"/>';
echo '<input type="hidden" name="Sortie" value="e">';
echo '<input type="hidden" name="Code_Postal" value="">';
echo '<input type="hidden" name="Departement" value="-1">';
echo '<input type="submit" name="ok" value="' . $lib_Rechercher . '" style="background:url(' . $root . '/assets/img/' . $Icones['chercher'] . ') no-repeat;padding-left:18px;" />';
echo '</form>';
echo '</fieldset>';
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</fieldset></td></tr></table>'; */
echo '</div>';

echo '</main>';

echo '<footer class="text-center">';
if ($SiteGratuit) {
    echo '<div>' . $LG_index_responsability . '</div>';
}

if ($settings['Date_Modification'] != '0000-00-00 00:00:00') {
    echo '<div>' . $LG_index_last_update . ' ' . DateTime_Fr($settings['Date_Modification']) . '</div>';
}
echo '</footer>';
echo '</div>';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>