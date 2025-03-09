<?php
//=====================================================================
// Liste des noms les plus portés dans la base
//=====================================================================

session_start();

$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

include('fonctions.php');

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'L';                                      // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Most_Used_Names'];     // Titre pour META
$x = Lit_Env();

include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 300);

Insere_Haut($titre, $compl, 'Liste_Nom_Pop', '');

// Préparation sur la clause de diffusabilité
$p_diff_int = '';
if (!$est_privilegie) $p_diff_int = " and Diff_Internet = 'O' ";

$sql = 'SELECT count(*) , f.nomFamille, f.idNomFam' .
    ' FROM ' . nom_table('noms_personnes') . ' n, ' . nom_table('noms_famille') . ' f, ' . nom_table('personnes') . ' p' .
    ' WHERE f.idNomFam = n.idNom' .
    ' AND p.Reference = n.idPers' .
    ' AND p.Reference <> 0' .
    $p_diff_int .
    ' GROUP BY f.nomFamille' .
    ' order by 1 desc' .
    ' limit ' . $nb_noms;

$res = lect_sql($sql);
$nb_lignes = $res->rowCount();

if ($nb_lignes > 0) {
    echo '<br />' . "\n";

    echo '<table width="35%" border="0" align="center" >' . "\n";
    echo '<tr align="center" class="rupt_table">';
    echo '<th width="60%">' . LG_MOST_NAMES . '</th>';
    echo '<th>' . LG_MOST_PERS . '</th>';
    echo '</tr>' . "\n";
    $num_lig = 0;

    $deb_visu  = '&nbsp;<a href="Fiche_NomFam.php?idNom=';
    $deb_modif = 'href="Edition_NomFam.php?idNom=';

    while ($enr = $res->fetch(PDO::FETCH_NUM)) {

        if (pair($num_lig++)) $style = 'liste';
        else                $style = 'liste2';
        echo '<tr class="' . $style . '">' . "\n";
        $nom = $enr[1];
        echo '<td>' . $deb_visu . $enr[2] . '&amp;Nom=' . $nom . '">' . my_html($nom) . '</a>';

        if ($est_gestionnaire)
            echo '&nbsp;' . Affiche_Icone_Lien($deb_modif . $enr[2] . '"', 'fiche_edition', my_html($LG_modify));

        echo '</td>';
        echo '<td align="right">' . $enr[0] . '&nbsp;&nbsp;&nbsp;</td>';
        echo '</tr>' . "\n";
    }

    $res->closeCursor();

    echo '</table>' . "\n";
    echo '<br />' . "\n";
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '');

Insere_Bas($compl);

?>
</body>

</html>