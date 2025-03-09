<?php
//=====================================================================
// Statistiques de naissance, mariage et décès par département
//=====================================================================

session_start();

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

include('fonctions.php');

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['BDM_Per_Depart'];   // Titre pour META
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

function Rupt_Depart()
{
    global $Anc, $case_N, $case_M, $case_D, $Anc_Num, $num_lig, $deb_visu, $def_enc;
    if ($Anc != '////\\\\') {
        if (pair($num_lig++)) $style = 'liste';
        else                $style = 'liste2';
        echo '<tr align="center" class="' . $style . '">' . "\n";
        echo '<td align="left">';
        if ($Anc == '') echo '?';
        else {
            echo $deb_visu . $Anc_Num . '">' . my_html($Anc) . '</a>&nbsp;';
        }
        echo '</td>';
        echo '<td>' . $case_N . '</td>';
        echo '<td>' . $case_M . '</td>';
        echo '<td>' . $case_D . '</td>' . "\n";
        echo '</tr>' . "\n";
        $case_N = '&nbsp;';
        $case_M = '&nbsp;';
        $case_D = '&nbsp;';
    }
}

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($LG_Menu_Title['BDM_Per_Depart'], $compl, 'Stat_Base_Depart', '');

$n_personnes = nom_table('personnes');
$n_departements = nom_table('departements');
$n_villes = nom_table('villes');

// Nombre de personnes nées dans la base par département
$sql = 'SELECT count(*) , d.Nom_Depart_Min, d.Identifiant_zone, "N" ' .
    'FROM ' . $n_personnes . ' p, ' .
    $n_departements . ' d, ' .
    $n_villes . ' v ' .
    'WHERE p.Reference <> 0 and p.ville_naissance <> 0 ' .
    'AND p.ville_naissance = v.identifiant_zone ' .
    'AND v.Zone_Mere = d.identifiant_zone ';
if (!$_SESSION['estPrivilegie']) $sql .= " and Diff_Internet = 'O' ";
$sql .= 'GROUP BY d.Nom_Depart_Min UNION ';
// Nombre de couples dans la base par département
$sql .= 'select count(*), d.Nom_Depart_Min, d.Identifiant_zone, "M" ' .
    'from ' . $n_personnes . ' m, ' . $n_personnes . ' f, ' .
    nom_table('unions') . ' u, ' .
    $n_departements . ' d, ' .
    $n_villes . ' v ' .
    'where u.Ville_Mariage <> 0 ' .
    'AND u.Ville_Mariage = v.identifiant_zone ' .
    'and u.Conjoint_1 = m.Reference and u.Conjoint_2 = f.Reference ' .
    'AND v.zone_mere = d.identifiant_zone ';
if (!$_SESSION['estPrivilegie']) {
    $sql .= " and m.Diff_Internet = 'O' ";
    $sql .= " and f.Diff_Internet = 'O' ";
}
$sql .= 'GROUP BY d.Nom_Depart_Min UNION ';

// Nombre de personnes décédées dans la base par département
$sql .= 'SELECT count(*) , d.Nom_Depart_Min, d.Identifiant_zone, "D" ' .
    'FROM ' . $n_personnes . ' p, ' .
    $n_departements . ' d, ' .
    $n_villes . ' v ' .
    'WHERE p.Reference <> 0 and p.ville_deces <> 0 ' .
    'AND p.ville_deces = v.identifiant_zone ' .
    'AND v.Zone_Mere = d.identifiant_zone ';
if (!$_SESSION['estPrivilegie']) $sql .= " and Diff_Internet = 'O' ";
$sql .= 'GROUP BY d.Nom_Depart_Min ORDER BY 2';

$larg = ' width ="20%"';
echo '<br />';
echo '<table border="0" class="classic" align="center" >' . "\n";
echo '<tr>';
echo '<th>' . my_html(LG_COUNTY) . '</th>';
echo '<th' . $larg . '>' . my_html($LG_birth) . '</th>';
echo '<th' . $larg . '>' . my_html($LG_wedding) . '</th>';
echo '<th' . $larg . '>' . my_html($LG_death) . '</th>';
echo '</tr>' . "\n";

$Anc  = '////\\\\';
$case_N = '&nbsp;';
$case_M = '&nbsp;';
$case_D = '&nbsp;';
$Anc_num = 0;

$tot_N = 0;
$tot_M = 0;
$tot_D = 0;

$deb_visu = '&nbsp;<a href="' . Get_Adr_Base_Ref() . 'Stat_Base_Villes.php?dep=';

// Balayage du résultat
$res = lect_sql($sql);
$num_lig = 0;
while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
    // Rupture sur le nom du département ==> création d'une ligne
    $Depart = $enreg[1];
    $Num_Depart = $enreg[2];
    $nb = $enreg[0];
    if ($Depart != $Anc) {
        Rupt_Depart();
        $Anc = $Depart;
        $Anc_Num = $Num_Depart;
    }
    switch ($enreg[3]) {
        case 'N':
            $case_N = $nb;
            if ($Num_Depart != 0) $tot_N += $nb;
            break;
        case 'M':
            $case_M = $nb;
            if ($Num_Depart != 0) $tot_M += $nb;
            break;
        case 'D':
            $case_D = $nb;
            if ($Num_Depart != 0) $tot_D += $nb;
            break;
    }
}
Rupt_Depart();

// Affichage des totaux
$style = 'liste';
if (!pair($num_lig++)) $style = 'liste2';
echo '<tr align="center" class="' . $style . '">' . "\n";
echo '<td align="left">&nbsp;';
echo my_html(LG_STAT_COUNTY_WITH);
echo '</td>';
echo '<td>' . $tot_N . '</td>';
echo '<td>' . $tot_M . '</td>';
echo '<td>' . $tot_D . '</td>';
echo '</tr>' . "\n";

$img_carte = '<img src="' . $chemin_images . $Icones['carte_france'] . '" alt="' . LG_STAT_COUNTY_MAP . '" title="' . LG_STAT_COUNTY_MAP . '" border="0"/></a>' . "\n";
$deb_carte = '<a href="' . Get_Adr_Base_Ref() . 'appelle_image_france_dep.php?Type_Liste=';

echo '<tr  class="' . $style . '" align="center">';
echo '<td>&nbsp;</td>';
echo '<td>' . $deb_carte . 'N">' . $img_carte . '</td>';
echo '<td>' . $deb_carte . 'M">' . $img_carte . '</td>';
echo '<td>' . $deb_carte . 'D">' . $img_carte . '</td>';
echo '</tr>';

echo '</table>';

$res->closeCursor();

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '');

Insere_Bas($compl);
?>
</body>

</html>