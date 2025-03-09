<?php
//=====================================================================
// Liste des professions les plus exercées
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

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

require(__DIR__ . '/Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 300);

Insere_Haut(my_html($titre), $compl, 'Liste_Prof_Pop', '');

// Préparation sur la clause de diffusabilité
$p_diff_int = '';
if (!$est_privilegie) $p_diff_int = " and Diff_Internet = 'O' ";

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

    $deb_visu  = '&nbsp;<a href="Fiche_Evenement.php?refPar=';
    $deb_modif = 'href="Edition_Evenement.php?refPar=';

    while ($enr = $res->fetch(PDO::FETCH_NUM)) {

        if (pair($num_lig++)) $style = 'liste';
        else                $style = 'liste2';
        echo '<tr class="' . $style . '">' . "\n";
        $evt = $enr[1];
        echo '<td>' . $deb_visu . $enr[2] . '&amp;Nom=' . $evt . '">' . my_html($evt) . '</a>';

        if ($est_contributeur)
            echo '&nbsp;' . Affiche_Icone_Lien($deb_modif . $enr[2] . '"', 'fiche_edition', my_html($LG_modify));

        echo '</td>';
        echo '<td align="right">' . $enr[0] . '&nbsp;&nbsp;&nbsp;</td>';
        echo '</tr>' . "\n";
    }

    $res->closeCursor();

    echo '</table>' . "\n";

    echo '<br>' . Affiche_Icone('tip', LG_TIP) . ' ' . LG_MOST_JOBS_TIP1;
    if ($est_contributeur)
        echo '<a href="Fusion_Evenements.php">' . LG_MOST_JOBS_TIP2 . '</a>';
    else
        echo LG_MOST_JOBS_TIP2;
    echo '<br>' . "\n";
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '');

Insere_Bas($compl);

?>
</body>

</html>