<?php
//=====================================================================
// Liste des professions les plus exercées
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'L';                                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Most_Used_jobs']; // Titre pour META
$x = Lit_Env();

require(__DIR__ . '/../app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 300);

Insere_Haut(my_html($titre), $compl, 'Liste_Prof_Pop', '');

// Préparation sur la clause de diffusabilité
$p_diff_int = '';
if (!IS_GRANTED('P')) $p_diff_int = " and Diff_Internet = 'O' ";

$sql = 'SELECT count(*) , evt.Titre, evt.Reference' .
    '  FROM ' . nom_table('participe') . ' part, ' . nom_table('evenements') . ' evt, ' . nom_table('personnes') . ' pers' .
    ' WHERE evt.Reference = part.Evenement' .
    ' AND evt.Code_Type = "OCCU"' .
    ' AND pers.Reference = part.Personne' .
    ' AND pers.Reference <> 0' .
    $p_diff_int .
    '  GROUP BY evt.Titre, evt.Reference' .
    '  ORDER BY 1 desc' .
    ' limit ' . $nb_prof;

$res = lect_sql($sql);
$nb_lignes = $res->rowCount();

if ($nb_lignes > 0) {
    echo '<br>' . "\n";
    echo '<table width="35%" border="0" align="center" >' . "\n";
    echo '<tr align="center" class="rupt_table">';
    echo '<th width="60%">' . my_html(LG_MOST_JOBS) . '</th>';
    echo '<th>' . my_html(LG_MOST_PERS) . '</th>';
    echo '</tr>' . "\n";
    $num_lig = 0;

    $deb_visu  = ' <a href="' . $root . '/fiche_evenement?refPar=';
    $deb_modif = 'href="' . $root . '/edition_evenement?refPar=';

    while ($enr = $res->fetch(PDO::FETCH_NUM)) {

        if (pair($num_lig++)) $style = 'liste';
        else                $style = 'liste2';
        echo '<tr class="' . $style . '">' . "\n";
        $evt = $enr[1];
        echo '<td>' . $deb_visu . $enr[2] . '&amp;Nom=' . $evt . '">' . my_html($evt) . '</a>';

        if (IS_GRANTED('C'))
            echo ' ' . Affiche_Icone_Lien($deb_modif . $enr[2] . '"', 'fiche_edition', my_html($LG_modify));

        echo '</td>';
        echo '<td align="right">' . $enr[0] . '</td>';
        echo '</tr>' . "\n";
    }

    $res->closeCursor();

    echo '</table>' . "\n";
    echo '<br><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . LG_TIP . '" title="' . LG_TIP . '"> ' . LG_MOST_JOBS_TIP1;

    if (IS_GRANTED('C'))
        echo '<a href="' . $root . '/fusion_evenements">' . LG_MOST_JOBS_TIP2 . '</a>';
    else
        echo LG_MOST_JOBS_TIP2;
    echo '<br>' . "\n";
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