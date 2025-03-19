<?php
//=====================================================================
// Exportation au format GenWeb
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['exp_GenWeb'];    // Titre pour META
$niv_requis = 'G';                        // Page réservée au gestionnaire
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Ecriture de la ligne à l'écran ou dans un fichier
function Ecrit_GenWeb($texte)
{
    global $fp, $cr, $exp_file;
    if ($exp_file) {
        if ($texte == "<br>") $texte = $cr;
        fputs($fp, "$texte");
    } else {
        if ($texte == "<br>") echo '<br />';
        else echo (my_html($texte));
    }
}

// Récupération des variables de l'affichage précédent
$tab_variables = array('Depart', 'destination', 'ut_suf');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) {
        $$nom_variables = $_POST[$nom_variables];
    } else $$nom_variables = '';
}

// Sécurisation des variables postées
$Depart = Secur_Variable_Post($Depart, 1, 'N');
if (!$Depart) $Depart = -1;
$destination = Secur_Variable_Post($destination, 30, 'S');
// Destination écran par défaut
if (!$destination) $destination = LG_GENWEB_SCREEN;
$ut_suf = Secur_Variable_Post($ut_suf, 2, 'S');

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'exp_GenWeb', $Depart);

// Sortie dans un fichier ou à l'écran ?
$exp_file = false;
if ($destination == LG_GENWEB_FILE) $exp_file = true;
if ($debug) {
    var_dump($destination);
    var_dump(LG_GENWEB_FILE);
    var_dump($exp_file);
}

// Création éventuelle du fichier d'export GenWeb
if ($exp_file) {
    $nom_fic = construit_fic($chemin_exports, $nom_fic_GenWeb);
    // Ajout éventuel du suffixe
    if ($ut_suf == 'on') {
        $posi = strrpos($nom_fic, '.');
        if ($posi !== false) {
            $nom_fic = substr($nom_fic, 0, $posi) . '_' . lib_departement($Depart, 'n') . substr($nom_fic, $posi);
        }
    }
    $fp = fopen($nom_fic, "wb");
    if (! $fp) die(my_html(LG_GENWEB_ERROR_FILE) . ' ' . $nom_fic);
}

// Recherche de la liste des départements
$sql = 'select distinct d.Identifiant_zone, d.Nom_Depart_Min ' .
    'from ' . nom_table('villes') . ' v, ' . nom_table('departements') . ' d ' .
    'where d.Identifiant_zone <> 0 ' .
    'and d.identifiant_zone = v.zone_mere ' .
    'order by d.Nom_Depart_Min';
$res = lect_sql($sql);

echo '<form id="saisie" action="' . my_self() . '?Depart=' . $Depart . '" method="post">';
echo '<br />';
echo '<table border="0" width="80%" align="center">' . "\n";
echo '<tr class="rupt_table" align="center">';
echo '<td>' . LG_COUNTY . LG_SEMIC . "\n";
echo '<select  name="Depart">';
while ($row = $res->fetch(PDO::FETCH_NUM)) {
    $row_dep = $row[0];
    echo "<option value=\"" . $row_dep . "\"";
    if ($Depart == $row_dep) echo ' selected="selected" ';
    echo ">" . my_html($row[1]) . '</option>' . "\n";
}
echo "</select>\n";
$res->closeCursor();
echo "</td>\n";

echo '<td align="left"><input type="radio" id="destination_s" name="destination" value="' . LG_GENWEB_SCREEN . '" checked="checked"'
    . ' onclick="document.getElementById(\'ut_suf\').checked=false;" />'
    . '<label for="destination_s">' . LG_GENWEB_SCREEN . '</label>&nbsp;' . "\n";
echo '<input type="radio" id="destination_f" name="destination" value="' . LG_GENWEB_FILE . '"/>'
    . '<label for="destination_f">' . LG_GENWEB_FILE . '</label>';
echo '&nbsp;( <input type="checkbox" name="ut_suf" id="ut_suf" onclick="document.forms.saisie.destination[1].checked=true;"/> '
    . '<label for="ut_suf">' . LG_GENWEB_SUFFIX . '</label> )' . "\n";
echo '</td>';
echo '<td><input type="submit" value="' . LG_GENWEB_EXTRACT . '"/></td>' . "\n";
echo "</tr></table>\n";
echo "</form>\n";

if ($Depart != -1) {
    // Constitution de la requête d'extraction'
    // on a 1 enreg par couple nom;ville
    $sql = 'select p.Nom, v.Nom_Ville ' .
        'from ' . nom_table('personnes') . ' p , ' . nom_table('villes') . ' v ' .
        'where p.Reference <> 0 ' .
        '  and v.zone_mere = ' . $Depart .
        '  and p.ville_naissance = v.identifiant_zone ' .
        'UNION ' .
        'select p.Nom, v.Nom_Ville ' .
        'from ' . nom_table('personnes') . ' p , ' . nom_table('villes') . ' v ' .
        'where p.Reference <> 0 ' .
        '  and v.zone_mere = ' . $Depart .
        '  and p.ville_deces = v.identifiant_zone ' .
        'order by Nom,Nom_Ville';

    $res = lect_sql($sql);

    $Anc_Nom   = '';
    $Anc_Ville = '';

    // balayage des enreg nom, nom_ville
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $Nouv_Nom = $row[0];
        $Nouv_Ville = $row[1];
        //echo "Nouv_Nom : ".$Nouv_Nom." Nouv_Ville : ".$Nouv_Ville."<br>";
        if ($Nouv_Nom != $Anc_Nom) {
            $Anc_Ville = '';
            if ($Anc_Nom != '') {
                $x = Ecrit_GenWeb('<br>');
            }
            $x = Ecrit_GenWeb($Nouv_Nom . ";");
            $Anc_Nom   = $Nouv_Nom;
        } else {
            $x = Ecrit_GenWeb(',');
        }
        if ($Nouv_Ville != $Anc_Ville) {
            $x = Ecrit_GenWeb($Nouv_Ville);
            $Anc_Ville = $Nouv_Ville;
        }
    }

    echo "<br />\n";

    $res->closeCursor();

    // Fermeture éventuelle du fichier d'export GenWeb
    if ($exp_file) {
        fclose($fp);
        echo '<br /><br />' . my_html(LG_GENWEB_MSG) . ' <a href="' . $nom_fic . '" target="_blank">' . $nom_fic . '</a><br>' . "\n";
    }
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