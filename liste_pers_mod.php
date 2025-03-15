<?php
//=====================================================================
// Liste des x dernières personnes modifiées
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

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
$acces = 'L';                                  // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Last_Mod_Pers'];     // Titre pour META
$x = Lit_Env();

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 300);

Insere_Haut($titre, $compl, 'Liste_Pers_Mod', '');

// Préparation sur la clause de diffusabilité
$p_diff_int = '';
if (!$est_privilegie) $p_diff_int = " and Diff_Internet = 'O' ";

$sql = 'SELECT Reference, Nom, Prenoms, Date_Modification FROM ' . nom_table('personnes') .
    ' WHERE Reference <> 0' .
    $p_diff_int .
    ' order by Date_Modification desc ' .
    ' limit ' . $nb_pers_mod;

$res = lect_sql($sql);
$nb_lignes = $res->rowCount();

if ($nb_lignes > 0) {
    echo '<br />' . "\n";
    echo '<table width="50%" border="0" class="classic" align="center" >' . "\n";
    echo '<tr align="center">';
    echo '<th>' . my_html(LG_PERS_MOD_PERS) . '</th>';
    echo '<th>' . my_html(LG_PERS_MOD_WHEN) . '</th>';
    echo '</tr>' . "\n";
    $num_lig = 0;

    while ($enr = $res->fetch(PDO::FETCH_NUM)) {

        echo '<tr>';
        echo '<td> <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $enr[0] . '">' . my_html($enr[2] . ' ' . $enr[1]) . '</a>';

        // Lien vers la modification
        if ($est_gestionnaire) echo ' <a href="' . $root . '/edition_personne.php?Refer=' . $enr[0] . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';

        echo '</td>';
        echo '<td align="center">' . DateTime_Fr($enr[3]) . '</td>';
        echo '</tr>' . "\n";
    }

    $res->closeCursor();

    echo '</table>' . "\n";
    echo '<br />' . "\n";
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '');

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