<?php

//=====================================================================
// Calcul de distance entre 2 villes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'Ref_Ville1',
    'Ref_Ville2'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Calculer), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_Calculer) $ok = 'OK';

// Gestion standard des pages
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Calculate_Distance'];          // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Verrouillage sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Ref_Ville1 = Secur_Variable_Post($Ref_Ville1, 1, 'N');
$Ref_Ville2 = Secur_Variable_Post($Ref_Ville2, 1, 'N');

$compl = Ajoute_Page_Info(600, 200);
$Ind_Ref = 0;

if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);

Insere_Haut(my_html($titre), $compl, 'Calcul_Distance', '');

// Premium ?

if (!$bt_OK) {
    $Ref_Ville1 = 0;
    $Ref_Ville2 = 0;
}

$n_villes = nom_table('villes');

echo '<form id="saisie" method="post" action="' . my_self() . '">' . "\n";

// Constitution de la liste des villes
$sql = 'select Identifiant_zone, Nom_Ville from ' . $n_villes . ' where Latitude <> 0 or Longitude <> 0'
    . ' order by Nom_Ville';
$res = lect_sql($sql);

if ($res->rowCount() > 0) {

    $larg_titre = '20';

    echo '<table width="80%" class="table_form">' . "\n";

    ligne_vide_tab_form(1);

    colonne_titre_tab($LG_Ch_Dist_Between);
    echo '<select name="Ref_Ville1">' . "\n";
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($Ref_Ville1 == $row[0]) echo ' selected="selected" ';
        echo '>' . my_html($row[1]) . '</option>' . "\n";
    }
    echo "</select>\n";
    echo '</td></tr>' . "\n";

    $res->closeCursor();
    $res = lect_sql($sql);

    colonne_titre_tab($LG_Ch_Dist_And);
    echo '<select name="Ref_Ville2">' . "\n";
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($Ref_Ville2 == $row[0]) echo ' selected="selected" ';
        echo '>' . my_html($row[1]) . '</option>' . "\n";
    }
    echo "</select>\n";
    echo '</td></tr>' . "\n";

    ligne_vide_tab_form(1);
    echo '<tr><td colspan="2">';
    echo Affiche_Icone('tip', my_html($LG_tip)) . '&nbsp;' .
        my_html($LG_Ch_Dist_Tip);
    echo '</td></tr>';

    ligne_vide_tab_form(1);
    bt_ok_an_sup($lib_Calculer, $lib_Annuler, '', '');

    echo '</table>' . "\n";
}
$res->closeCursor();

echo '</form>';

if ($bt_OK) {

    $erreur = false;

    if ($Ref_Ville1 == $Ref_Ville2) {
        Affiche_Warning('Vous devez saisir 2 villes différentes...');
        $erreur = true;
    }
    if (($Ref_Ville1 == 0) or ($Ref_Ville2 == 0)) {
        Affiche_Warning('Vous devez saisir 2 villes...');
        $erreur = true;
    }

    if (! $erreur) {

        $lat1 = 0;
        $long1 = 0;
        $lat2 = 0;
        $long2 = 0;
        $nom1 = '';
        $nom2 = '';

        $sql = 'select Identifiant_zone, Latitude, Longitude, Nom_Ville from ' . $n_villes . ' where Identifiant_zone in (' . $Ref_Ville1 . ',' . $Ref_Ville2 . ')';
        $res = lect_sql($sql);
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            if ($row[0] == $Ref_Ville1) {
                $lat1 = $row[1];
                $long1 = $row[2];
                $nom1 = $row[3];
            }
            if ($row[0] == $Ref_Ville2) {
                $lat2 = $row[1];
                $long2 = $row[2];
                $nom2 = $row[3];
            }
        }

        $point1 = array("lat" => $lat1, "long" => $long1);
        $point2 = array("lat" => $lat2, "long" => $long2);
        $km = distanceCalculation($point1['lat'], $point1['long'], $point2['lat'], $point2['long']); // Calculer la distance en kilomètres (par défaut)
        echo my_html($LG_Ch_Dist_Res1 . $nom1 . $LG_Ch_Dist_Res2 . $nom2 . $LG_Ch_Dist_Res3 . $km . $LG_Ch_Dist_Res4) . '<br />';
    }
}

Insere_Bas($compl);
?>
</body>

</html>