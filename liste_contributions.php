<?php
//=====================================================================
// Liste des contributions proposées par les utilisateurs
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (L)ecture
$titre = $LG_Menu_Title['Contribs_List'];    // Titre pour META
$x = Lit_Env();
$niv_requis = 'C';                        // Page accessible aux contributeurs
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('ignorer', 'retour');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ignorer = Secur_Variable_Post($ignorer, 1, 'S');
$retour  = Secur_Variable_Post($retour, 1, 'S');

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Liste_Contributions', '');

// Ignorer coché par défaut lors du premier affichage
if ((!isset($retour)) or ($retour == '')) $ignorer = 'O';

$echo_nouv = '&nbsp;' . Affiche_Icone('etoile', my_html(LG_CONTRIB_LIST_NEW));
$echo_modif = Affiche_Icone('fiche_edition', my_html(LG_CONTRIB_LIST_PROCESS)) . '</a>';

echo '<form action="' . my_self() . '" method="post">' . "\n";

echo '<input type="hidden" name="retour" value="1"/>' . "\n";

echo '<table border="0" width="60%" align="center">' . "\n";
echo '<tr align="center" class="rupt_table">';
echo '<td><label for="ignorer">' . LG_CONTRIB_LIST_IGNORE . '</label>';
echo ' <input type="checkbox" id="ignorer" name="ignorer" value="O" ';
if ($ignorer == 'O') echo 'checked="checked"';
echo '/></td>' . "\n";
echo '   <td><input type="submit" value="' . my_html($LG_modify_list) . '"/></td>' . "\n";
echo "  </tr>\n";
echo "  </table>\n";
echo " </form>\n";

$dp = opendir($chemin_contributions);

$i = 0;
while ($file = readdir($dp)) {
    // enleve les fichiers . et ..
    if ($file != '.' && $file != '..') {
        // on passe les datas dans un tableau
        $ListFiles[$i] = $file;
        $i++;
    }
}
closedir($dp);

// tri par ordre decroissant
if (count($ListFiles) != 0) {
    rsort($ListFiles);
}

echo '<table width="90%" border="0" class="classic" align="center">' . "\n";
echo '<tr>' . "\n";
echo '<th>' . LG_CONTRIB_LIST_CONTRIB . '</th>';
echo '<th>' . LG_CONTRIB_LIST_PERSON . '</th>';
echo '<th>' . LG_CONTRIB_LIST_WHEN . '</th>' . "\n";
echo '<th>&nbsp;</th>' . "\n";
echo '</tr>' . "\n";


// affiche les fichiers par ordre alphabétique decroissant
$i = 0;
$num_lig = 0;
while ($i < count($ListFiles)) {

    $nom_fic = $ListFiles[$i];
    $nom_fic_complet = $chemin_contributions . $nom_fic;
    $p_traitee = strpos($nom_fic, 'traitee');

    // On affiche les informations sur l'utilisateur n'ignore pas les fichiers traitées ou que le fichier n'est pas traité
    if ((($ignorer != 'O') or ($p_traitee == 0)) and (strpos($nom_fic, 'ontrib_') == 1)) {

        // Calcul du numéro de la contribution à partir du nom du fichier
        $pu1 = strpos($nom_fic, '_');
        $pu2 = strpos($nom_fic, '_', $pu1 + 1);
        if (!$pu2) $pu2 = strpos($nom_fic, '.');
        $num_contrib0 = substr($nom_fic, $pu1 + 1, $pu2 - $pu1 - 1);
        $num_contrib  = ltrim($num_contrib0, '0');

        echo '<tr>';
        echo '<td>' . $num_contrib;
        if (!$p_traitee) echo $echo_nouv;
        echo '</td>';
        // Récupération des informations dans le fichier
        // création : ligne 2
        // personne : ligne 10;
        if ($fp = fopen($nom_fic_complet, 'r')) {
            $cpt      = 0;
            $Creation = '';
            $Ref_Pers = 0;

            // Balayage du fichier (ligne de 255 caractères max)
            while (($ligne = fgets($fp, 255)) and ($cpt <= 10)) {
                $cpt++;
                if ($cpt == 2) {
                    $Creation = substr($ligne, 2);
                }
                if ($cpt == 10) {
                    $Ref_Pers = trim(substr($ligne, 21));
                }
            }
            fclose($fp);
        }

        if (($Ref_Pers != 0) and (Get_Nom_Prenoms($Ref_Pers, $Nom, $Prenoms)))
            echo '<td align="left"><a ' . Ins_Ref_Pers($Ref_Pers) . '>' . $Nom . ' ' . $Prenoms . '</a></td>';
        else
            echo '<td>&nbsp;</td>';

        echo '<td>' . $Creation . '</td>';

        if ($p_traitee) $suffixe = 'T';
        else            $suffixe = '';

        echo '<td align="center">' .
            '<a href="'.$root.'/edition_contribution.php?Contribution=' . $num_contrib0 . $suffixe . '">' .
            $echo_modif .
            '</td>';

        echo '</tr>' . "\n";
    }
    $i++;
}

echo '</table>' . "\n";

Insere_Bas($compl);
?>
</body>

</html>