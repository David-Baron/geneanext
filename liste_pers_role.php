<?php
//=====================================================================
// Liste des personnes pour un rôle passé en paramètre
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture

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

// Recup des variables passées dans l'URL :
$c_Role = Recup_Variable('Role', 'S');             // Code rôle, 4 caractères maximum
if (strlen($c_Role) > 4)
    $c_Role = substr($c_Role, 0, 4);

$objet = $LG_Menu_Title['Role_List_Pers'];

$titre = $objet;     // Titre pour META
$x = Lit_Env();      // Lecture de l'indicateur d'environnement

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 150);

Insere_Haut($objet, $compl, 'Liste_Pers_Role', '');

$sqlR = "select * from " . nom_table('roles') . " where Code_Role = '$c_Role' limit 1";
$resR = lect_sql($sqlR);
$symetrie = '';
if ($enregR = $resR->fetch(PDO::FETCH_ASSOC)) {
    $symetrie = $enregR['Symetrie'];
    if ($debug) {
        if ($symetrie == 'O')
            $val_sim = $LG_Yes;
        else
            $val_sim = $LG_No;
        echo LG_ROLE_CODE . ' : ' . $c_Role . '<br />';
        echo LG_ROLE_SYM . ' : ' . $val_sim . '<br />';
        echo LG_ROLE_LABEL . ' : ' . $enregR['Libelle_Role'] . '<br />';
        echo LG_ROLE_OPPOS_LABEL . ' : ' . $enregR['Libelle_Inv_Role'] . '<br />' . '<br />';
    }
}

// Constitution de la requête d'extraction
$n_personnes = nom_table('personnes');
$sql = 'SELECT '
    . ' rel.Principale'
    . ', pers_1.Reference as Ref_1, pers_1.Nom as Nom_1, pers_1.Prenoms as Prenoms_1, pers_1.Ne_le as Ne_1, pers_1.Decede_Le as Decede_1'
    . ', pers_2.Reference as Ref_2, pers_2.Nom as Nom_2, pers_2.Prenoms as Prenoms_2, pers_2.Ne_le as Ne_2, pers_2.Decede_Le as Decede_2 '
    . 'FROM ' . nom_table('relation_personnes') . ' rel '
    . '   , ' . $n_personnes . ' pers_1 '
    . '   , ' . $n_personnes . ' pers_2 '
    . 'where rel.Personne_1 = pers_1.Reference'
    . '  and rel.Personne_2 = pers_2.Reference'
    . "  and rel.Code_Role = '" . $c_Role . "' ";

if (!$est_privilegie) {
    $sql = $sql . " and pers_1.Diff_Internet = 'O' ";
    $sql = $sql . " and pers_2.Diff_Internet = 'O' ";
}

$sql = $sql . 'order by pers_1.Nom, pers_1.Prenoms';

$res = lect_sql($sql);

$nb_lig = 0;
if ($res) {
    $nb_lig = $res->rowCount();
}

// Balayage
if ($nb_lig > 0) {

    echo '<br />';

    // echo '<table border="1" align="center">';
    echo '<table class="classic" width="85%" align="center" border="0">' . "\n";
    echo '<tr class="rupt_table">';
    echo '<th>' . $enregR['Libelle_Role'] . '</th>';
    echo '<th>' . $enregR['Libelle_Inv_Role'] . '</th>';
    echo '</tr>' . "\n";

    // Optimisation : préparation echo des images
    $echo_modif = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';

    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        // var_dump($row);
        // echo '<br />';

        $Ne = $row['Ne_1'];
        $Decede = $row['Decede_1'];
        $dates1 = '';
        if (($Ne != '') or ($Decede != '')) {
            if ($Ne != '') $dates1 = '° ' . Etend_date($Ne);
            if ($Decede != '') {
                if ($dates1 != '') $dates1 = $dates1 . ', ';
                $dates1 = $dates1 . '+ ' . Etend_date($Decede);
            }
        }
        $Ne = $row['Ne_2'];
        $Decede = $row['Decede_2'];

        $dates2 = '';
        if (($Ne != '') or ($Decede != '')) {
            if ($Ne != '') $dates2 = '° ' . Etend_date($Ne);
            if ($Decede != '') {
                if ($dates2 != '') $dates2 = $dates2 . ', ';
                $dates2 = $dates2 . '+ ' . Etend_date($Decede);
            }
        }

        // Informations à présenter
        $lien1 = '';
        $lien2 = '';
        if ($est_contributeur) {
            $lien1 = '<a href="' . $root . '/edition_personne.php?Refer=' . $row['Ref_1'] . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
            $lien2 = '<a href="' . $root . '/edition_personne.php?Refer=' . $row['Ref_2'] . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
        }

        echo '<tr>';
        // Si symétrie = N et Principale = N, on inverse
        if (($symetrie == 'N') && ($row['Principale'] == 'N')) {
            echo '<td width="50%"><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $row['Ref_2'] . '">' . $row['Prenoms_2'] . ' ' . $row['Nom_2'] . '</a> ' . $dates2 . ' ' . $lien2 . '</td>';
            echo '<td><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $row['Ref_1'] . '">' . $row['Prenoms_1'] . ' ' . $row['Nom_1'] . '</a> ' . $dates1 . ' ' . $lien1 . '</td>';
        } else {
            echo '<td width="50%"><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $row['Ref_1'] . '">' . $row['Prenoms_1'] . ' ' . $row['Nom_1'] . '</a> ' . $dates1 . ' ' . $lien1 . '</td>';
            echo '<td><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $row['Ref_2'] . '">' . $row['Prenoms_2'] . ' ' . $row['Nom_2'] . '</a> ' . $dates2 . ' ' . $lien2 . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}

if ($res)
    $res->closeCursor();

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '?' . Query_Str());
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