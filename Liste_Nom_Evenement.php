<?php
//=====================================================================
// Liste des noms pour un évènement
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
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup de la variable passée dans l'URL : référence de l'évènement
$refPar = Recup_Variable('refPar', 'N');

$compl = Ajoute_Page_Info(600, 300);

Insere_Haut($LG_Menu_Title['Names_For_Event'], $compl, 'Liste_Nom_Evenement.php', $refPar);

$requete = 'SELECT Titre FROM ' . nom_table('evenements') .
    ' WHERE reference = ' . $refPar . ' limit 1';
$result = lect_sql($requete);
$enreg = $result->fetch(PDO::FETCH_NUM);
echo my_html(LG_NAMES_FOR_EVENT_EVENT) . LG_SEMIC . $enreg[0] . '<br />';

// Préparation sur la clause de diffusabilité
$p_diff_int = '';
if (!$est_privilegie) $p_diff_int = " and Diff_Internet = 'O' ";

$sql = 'SELECT count( * ) , f.nomFamille, f.idNomFam' .
    ' FROM ' . nom_table('noms_personnes') . ' n, ' . nom_table('noms_famille') . ' f, ' . nom_table('personnes') . ' p, ' . nom_table('participe') . ' pa' .
    ' WHERE f.idNomFam = n.idNom' .
    ' AND p.Reference = n.idPers' .
    ' AND p.Reference <>0' .
    $p_diff_int .
    ' AND pa.Personne = p.Reference' .
    ' AND Evenement = ' . $refPar .
    ' GROUP BY f.nomFamille' .
    ' ORDER BY 1 DESC';

$res = lect_sql($sql);
$nb_lignes = $res->rowCount();

if ($nb_lignes > 0) {
    $lg_col_1 = 60;
    echo '<table width="50%" border="0" class="classic" align="center">';
    echo '<tr align="center">';
    echo '<th width="' . $lg_col_1 . '%">' . $LG_Name . '</th>';
    echo '<th>' . LG_NAMES_FOR_EVENT_PERS_COUNT . '</th>';
    echo '</tr>' . "\n";

    $deb_visu  = '&nbsp;<a href="' . $root . '/fiche_nomfam.php?idNom=';
    $deb_modif = 'href="' . $root . '/edition_nomfam.php?idNom=';

    while ($enr = $res->fetch(PDO::FETCH_NUM)) {
        $nom = $enr[1];

        echo '<tr>' . "\n";
        echo '<td width="' . $lg_col_1 . '%">' . $deb_visu . $enr[2] . '&amp;Nom=' . $nom . '">' . $nom . '</a>';

        if ($est_gestionnaire)
            echo '&nbsp;' . Affiche_Icone_Lien($deb_modif . $enr[2] . '"', 'fiche_edition', $LG_modify);

        echo '</td>';
        echo '<td align="right">' . $enr[0] . '&nbsp;&nbsp;&nbsp;</td>';
        echo '</tr>' . "\n";
    }

    $res->closeCursor();

    echo '</table>' . "\n";
    echo '<br />' . "\n";
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
Insere_Bas($compl);

?>
</body>

</html>