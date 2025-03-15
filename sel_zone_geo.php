<?php
// Choix d'une zone géographique

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

//FenCalend=window.open('sel_zone_geo.php?zoneLib='+zoneLib
//                                     +'&zoneValue='+zoneValue
//                                     +'&valZone='+valZone
//                                     +'&valNiveau='+valniveau,
//                                      'FenCalend', chParam);
$zoneLib   = $_GET['zoneLib'];
$zoneValue = $_GET['zoneValue'];

$idZone = $_GET['valZone'];
$Niveau = $_GET['valNiveau'];

$modif = 'O';
if (isset($_GET['modif'])) $modif  =  $_GET['modif'];

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php switch ($Niveau) {
        case 1:
            Ecrit_Meta(LG_CHOOSE_AREA_COUNTRY, LG_CHOOSE_AREA_COUNTRY, '');
            break;
        case 2:
            Ecrit_Meta(LG_CHOOSE_AREA_REGION, LG_CHOOSE_AREA_REGION, '');
            break;
        case 3:
            Ecrit_Meta(LG_CHOOSE_AREA_COUNTY, LG_CHOOSE_AREA_COUNTY, '');
            break;
        case 4:
            Ecrit_Meta(LG_CHOOSE_AREA_TOWN, LG_CHOOSE_AREA_TOWN, '');
            break;
        case 5:
            Ecrit_Meta(LG_CHOOSE_AREA_SUBDIVISION, LG_CHOOSE_AREA_SUBDIVISION, '');
            break;
        default:
            break;
    } ?>

    <script type="text/javascript">
        // Ajoute une zone géographique dans la liste
        function ajoute() {
            nouv_val = document.forms.formZone.nouvelle_zone.value;
            nouvel_element = new Option(nouv_val, nouv_val, false, true);
            document.forms.formZone.ZoneGeo.options[document.forms.formZone.ZoneGeo.length] = nouvel_element;
            document.forms.formZone.nouvelle_zone.value = "";
            inverse_div('id_div_ajout');
        }

        //Rechargement de la zone géographique dans l'input de la fenêtre appelante
        function lien() {
            msg = '';
            laZoneGeo = document.forms.formZone.ZoneGeo.value;
            leLib = document.forms.formZone.ZoneGeo.options[document.forms.formZone.ZoneGeo.selectedIndex].text;
            //window.alert(laZoneGeo);
            //window.alert(leLib);
            // Recharge dans les zones passées en paramètre
            window.opener.document.forms['saisie'].elements['<?php echo $zoneValue; ?>'].value = laZoneGeo;
            window.opener.document.forms['saisie'].elements['<?php echo $zoneLib; ?>'].value = leLib;
            window.close();
        }
    </script>
</head>
<?php

$x  = Lit_Env();

$bg = '';
if (file_exists(__DIR__ . '/assets/img/' . $Image_Fond))
    $bg = ' background="' . $root . '/assets/img/' . $Image_Fond . '"';

echo '<body vlink="#0000ff" link="#0000ff" ' . $bg . '>';

// formulaire
echo '<form id="formZone" method="post" action="">';
echo '<table width="100%" border="0">';
echo '<tr>';
echo '<td align="center">';

// En entrée : 
// $Niveau : niveau de la zone géographique
// $idZone   : zone géographique

// Construction de la reqête de select en fonction du niveau de la zone géographique
switch ($Niveau) {
    case 1:
        $n_zone = 'Nom_Pays';
        $n_table = 'pays';
        break;
    case 2:
        $n_zone = 'Nom_Region_Min';
        $n_table = 'regions';
        break;
    case 3:
        $n_zone = 'Nom_Depart_Min';
        $n_table = 'departements';
        break;
    case 4:
        $n_zone = 'Nom_Ville';
        $n_table = 'villes';
        break;
    case 5:
        $n_zone = 'Nom_Subdivision';
        $n_table = 'subdivisions';
        break;
}
// Lancement de la requête sur les zones
$sql = 'select Identifiant_zone, ' . $n_zone . ' from ' . nom_table($n_table) . ' order by ' . $n_zone;
$res = lect_sql($sql);
// Affichage d'un select avec le résultat
echo '<select name="ZoneGeo">';
echo '<option value="0" > </option>';
while ($row = $res->fetch(PDO::FETCH_NUM)) {
    $idZone_req = $row[0];
    echo '<option value="' . $idZone_req . '"';
    if ($idZone_req == $idZone) echo ' selected="selected" ';
    echo '>' . my_html($row[1]) . '</option>';
}
echo "</select>\n";
if ($modif == 'O') {
    echo '&nbsp;<img id="ajout" src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="Ajout zone" ' .
        'onclick="inverse_div(\'id_div_ajout\');document.getElementById(\'nouvelle_zone\').focus();"/>';
    echo '<div id="id_div_ajout">';
    echo 'Zone &agrave; ajouter &agrave; la liste&nbsp;<input type="text" name="nouvelle_zone"/>';
    echo '<input type="button" name="ferme_OK" value="OK" onclick="ajoute()"/>';
    echo '<input type="button" name="ferme_An" value="Annuler" onclick="inverse_div(\'id_div_ajout\')"/>';
    echo '</div>';
    echo '<script type="text/javascript">';
    echo '<!--';
    echo 'cache_div(\'id_div_ajout\');';
    echo '//-->';
    echo '</script>';
}
echo '</td></tr>';
echo '<tr><td>';
echo '<table class="table_form" align="center"><tr align="center">';
echo '<tr><td colspan="3">&nbsp;</td></tr>';

echo '<tr><td colspan="3" align="center">';
echo '<div class="buttons">';
echo '<button type="submit" class="positive" onclick="lien();"><img src="' . $root . '/assets/img/' . $Icones['fiche_validee'] . '" alt=""/>' . $lib_Okay . '</button>';
echo '<button type="submit" onclick="window.close();"><img src="' . $root . '/assets/img/' . $Icones['cancel'] . '" alt=""/>' . $lib_Annuler . '</button>';
echo '</div>';
echo '</td></tr>';
echo '</table>';
echo '</td></tr>';
echo '</table>';
echo '</form>';

?>
</body>

</html>