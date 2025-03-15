<?php
//=====================================================================
// Utilitaires de calendrier
// - Calcul du jour de la semaine
// - Conversion de calendrier républicain
// - Calcul de la date de Pâques et des dates associées
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Calcul conversion calendriers';    // Titre pour META
$mots = 'Calendriers, Républicain, Jour, Semaine, Pâques';
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

?>
<script type="text/javascript">
    function aj_car(chaine) {
        document.forms.calculette.saisie.value += chaine;
    }

    function fin_pme() {
        document.forms.calculette.saisie.value = parseInt(eval(document.forms.calculette.saisie.value));
    }
</script>
<?php
// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'CJour',
    'CMois',
    'CAn',
    'JourRep',
    'MoisRep',
    'AnneeRep',
    'san',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

function Ecrit_Mois($couleur, $valeur, $mois, $zone)
{
    global $MoisRep;
    echo '<option style="color:' . $couleur . '" value="' . $valeur . '" ';
    if ($zone == $valeur) echo 'selected="selected"';
    echo '>' . $mois . '</option>' . "\n";
    return 0;
}

function aff_color($color, $text)
{
    echo '<font color="' . $color . '">' . my_html($text) . '&nbsp;</font>';
}

// Calcul du jour de la semaine
function Jour_Semaine($jour, $mois, $annee)
{
    global $Jours_Lib;
    // Cf. http://www.chez.com/cosmos2000/Vendredi13/GaussMethode.html pour l'algorithme
    // Calcul par la méthode de Gauss
    //ssaa   l'année, ie. 2004.
    //s   le siècle ou les deux premiers de l'année ssaa; s = 20 pour l'an 2004.
    //d   l'année spécifique ou les deux derniers chiffres de l'année ssaa; d = 04 pour l'an 2004.
    //m   le rang du mois de l'année.
    //q   le quantième du mois.
    //c   un facteur de correction selon le mois:
    //  * c = 4   pour janvier et février d’une année normale;
    //  * c = 3   pour janvier et février d’une année bissextile;
    //  * c = 2   pour avril;
    //  * c = 0   pour décembre;
    //  * c = 1   pour tous les autres mois.
    //j   le rang du jour de la semaine, avec par convention 0=Sam., 1=Dim., 2=Lun., ..., 6=Ven.
    //j = { q + 3m - [3m/10] + 5s + [s/4] + d + [d/4] + c } mod 7 où [] vaut la partie entière
    // exemple 18 janvier 1953
    //q = 18   m = 1   s = 19   d = 53   c= 4
    //j = {18 + 3x1 - [3x1/10] + 5x19 + [19/4] + 53 + [53/4] + 4} mod 7
    //j = (18 + 3 - 0 + 95 + 4 + 53 + 13 + 4) mod 7
    //j = 190 mod 7 = 1    car 190 = 7 x 27 + 1 = 189 + 1
    //j = Dimanche = 18 janvier 1953
    $s = intval(substr($annee, 0, 2));
    $d = intval(substr($annee, 2, 2));
    $m = intval($mois);
    $q = intval($jour);
    switch ($m) {
        case 1:
            if (bissextile($annee)) $c = 3;
            else $c = 4;
            break;
        case 2:
            if (bissextile($annee)) $c = 3;
            else $c = 4;
            break;
        case 4:
            $c = 2;
            break;
        case 12:
            $c = 0;
            break;
        default:
            $c = 1;
    }
    //j = { q + 3m - [3m/10] + 5s + [s/4] + d + [d/4] + c } mod 7
    $j = $q + (3 * $m) - floor((3 * $m) / 10) + (5 * $s) + floor($s / 4) + $d + floor($d / 4) + $c;
    $j = $j % 7;
    switch ($j) {
        //$Jours_Lib = Array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche');
        case 1:
            return $Jours_Lib[6];
            break;
        case 2:
            return $Jours_Lib[0];
            break;
        case 3:
            return $Jours_Lib[1];
            break;
        case 4:
            return $Jours_Lib[2];
            break;
        case 5:
            return $Jours_Lib[3];
            break;
        case 6:
            return $Jours_Lib[4];
            break;
        case 0:
            return $Jours_Lib[5];
            break;
        default:
            return '';
    }
}

function aff_bouton($nb, $txt)
{
    global $chemin_images_icones, $Icones, $def_enc;
    echo '<div id="boutons' . $nb . '">' . "\n";
    echo '<table border="0" cellpadding="0" cellspacing="0">' . "\n";
    echo '<tr><td>';
    echo '<div class="buttons">';
    echo '<button type="submit" class="positive">';
    if ($txt == 'Convertir') echo '<img src="' . $chemin_images_icones . $Icones['conversion'] . '" alt=""/>';
    echo htmlentities($txt, ENT_QUOTES, $def_enc) . '</button>';
    echo '</div>';
    echo '</td></tr>';
    echo '</table>' . "\n";
    echo '</div>' . "\n";
}
function aff_annee_rev($valeur_arabe, $valeur_romaine)
{
    global $AnneeRep;
    echo '	<option value="' . $valeur_arabe . '"';
    if ($AnneeRep == $valeur_arabe) echo 'selected="selected"';
    echo '>' . $valeur_romaine . '</option>';
}

$compl = Ajoute_Page_Info(600, 150);

Insere_Haut('Calculs de calendrier', $compl, 'calendriers', '');
?>
<!--
  <a href="#calcul">Calcul</a> |
  <a href="#calrep">Calendrier r&eacute;publicain</a> |
  <a href="#paques">Date de P&acirc;ques</a>
<br />
-->
<?php
$h_month = my_html(LG_CALEND_MONTH);
$h_year = my_html(LG_CALEND_YEAR);

echo '<a name="calcul"></a>';

$LaDate = date('Ym');
$xAnnee = substr($LaDate, 0, 4);
$xMoisA = substr($LaDate, 4, 2);

?>
<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_CALEND_CALC_ON_DATE; ?></b></td>
    </tr>
</table>
<br><br>
<?php
echo '<form action="" id="cDate" method="post" action="' . my_self() . '">';
echo '<table bgcolor="#E5E4E2">';
echo '<tr>';
echo '<th>&nbsp;</th>';
echo '<th>' . $h_month . '</th>';
echo '<th>' . $h_year . '</th>';
echo '</tr>';
echo '<tr>';
echo '<td>' . my_html(LG_CALEND_INITIALE) . '</td>';
echo '<td><input type="text" name="dMois" id="dMois" size="2" value="' . $xMoisA . '"/></td>';
echo '<td><input type="text" name="dAnnee" id="dAnnee" size="4" value="' . $xAnnee . '"/></td>';
echo '<td align="center"><input class="BoutonCalc2" style="width:100%;" onclick="plus_date();" type="button" value="+" name="TP"/></td>';
echo '</tr>';
echo '<tr>';
echo '<td>' . my_html(LG_CALEND_OFFSET) . '</td>';
echo '<td><input type="text" name="decal_Mois" id="decal_Mois" size="2" value="00"/></td>';
echo '<td><input type="text" name="decal_Annee" id="decal_Annee" size="4" value="0000"/></td>';
echo '<td align="center"><input class="BoutonCalc2" style="width:100%;" onclick="moins_date();" type="button" value="-" name="TM"/></td>';
echo '</tr>';
echo '<tr>';
echo '<td>' . my_html(LG_CALEND_CALC) . '</td>';
echo '<td><input type="text" readonly="readonly" name="rMois" id="rMois" size="2"/></td>';
echo '<td><input type="text" readonly="readonly" name="rAnnee" id="rAnnee" size="4"/></td>';
echo '<td><input class="BoutonCalc2" style="color:red;" onclick="efface_entree_date();" type="button" value="C" name="T0"/></td>';
echo '</tr>';
echo '</table>';
echo '</form>';
echo '<br />';

?>
<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_CALEND_DAY; ?></b></td>
    </tr>
</table>
<br><br>
<?php
echo '<form id="calc_jour" method="post" action="' . my_self() . '">';
echo '  <p>Date : <input type="text" size="2" maxlength="2" name="CJour" value="' . $CJour . '"/>';
echo '  <select name="CMois">';
echo '    <option selected="selected" value="">Mois ?</option>';
for ($nb = 1; $nb <= 12; $nb++) {
    Ecrit_Mois('black', $nb, $Mois_Lib[$nb - 1], $CMois);
}
echo '</select>';
echo '<input type="text" size="4" maxlength="4" name="CAn" value="';
if ($CAn == "") {
    echo date("Y");
} else
    echo $CAn;
echo '"/>';
$ctrl_date = checkdate(intval($CMois), intval($CJour), intval($CAn));
if ($ctrl_date == 1) {
    $jour = Jour_Semaine(intval($CJour), intval($CMois), intval($CAn));
    echo '&nbsp;&nbsp;' . $jour;
}
echo '</p>';
echo aff_bouton(1, LG_CALEND_CALCULATE);
echo '</form>';

echo '<br />';
echo '<a name="calrep"></a>';
?>
<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_CALEND_REV_CONVERT; ?></b></td>
    </tr>
</table>
<br><br>
<?php
echo '<form id="conv_rep" method="post" action="' . my_self() . '">';
echo '  <p>Date R&eacute;publicaine : <input type="text" size="2" maxlength="2" name="JourRep" value="' . $JourRep . '"/>';
echo '  <select name="MoisRep">';
echo '    <option selected="selected" value="">' . $h_month . ' ?</option>';
// Printemps : couleur vert...
// Eté       : couleur jaune...
// Automne   : couleur marron...
// Hiver     : couleur argent...
// Yellow ==> orange
// Silver ==> grey
Ecrit_Mois('maroon', 2, 'Brumaire', $MoisRep);
Ecrit_Mois('green', 8, 'Flor&eacute;al', $MoisRep);
Ecrit_Mois('maroon', 3, 'Frimaire', $MoisRep);
Ecrit_Mois('orange', 12, 'Fructidor', $MoisRep);
Ecrit_Mois('green', 7, 'Germinal', $MoisRep);
Ecrit_Mois('orange', 10, 'Messidor', $MoisRep);
Ecrit_Mois('grey', 4, 'Niv&ocirc;se', $MoisRep);
Ecrit_Mois('grey', 5, 'Pluvi&ocirc;se', $MoisRep);
Ecrit_Mois('green', 9, 'Prairial', $MoisRep);
Ecrit_Mois('orange', 11, 'Thermidor', $MoisRep);
Ecrit_Mois('maroon', 1, 'Vend&eacute;miaire', $MoisRep);
Ecrit_Mois('grey', 6, 'Vent&ocirc;se', $MoisRep);
Ecrit_Mois('black', 13, 'sanculottides', $MoisRep);
echo '</select>' . "\n";

echo '<select name="AnneeRep">';
echo '<option selected="selected" value="">' . $h_year . ' ?</option>';
aff_annee_rev(1, 'I');
aff_annee_rev(2, 'II');
aff_annee_rev(3, 'III');
aff_annee_rev(4, 'IV');
aff_annee_rev(5, 'V');
aff_annee_rev(6, 'VI');
aff_annee_rev(7, 'VII');
aff_annee_rev(8, 'VIII');
aff_annee_rev(9, 'IX');
aff_annee_rev(10, 'X');
aff_annee_rev(11, 'XI');
aff_annee_rev(12, 'XII');
aff_annee_rev(13, 'XIII');
aff_annee_rev(14, 'XIV');
echo '</select>' . "\n";

if ($JourRep >= 1 and $JourRep <= 30 and $MoisRep != "" and $AnneeRep != "") {
    $nombre = frenchtojd($MoisRep, $JourRep, $AnneeRep);
    $LaDate = jdtogregorian($nombre);
    $s1 = strpos($LaDate, '/', 1);
    $s2 = strpos($LaDate, '/', $s1 + 1);
    $j  = substr($LaDate, $s1 + 1, $s2 - $s1 - 1);
    $mois = substr($LaDate, 0, $s1);
    $a  = substr($LaDate, $s2 + 1, 4);
    $jour = Jour_Semaine(intval($j), intval($mois), intval($a));
    echo '&nbsp;&nbsp;' . $jour . '&nbsp;' . Etend_Date_Inv(jdtogregorian($nombre)) . '<br />';
}

echo '<br /><i>' . $h_month . ' ';
aff_color('green', LG_CALEND_SPRING);
aff_color('orange', LG_CALEND_SUMMER);
aff_color('maroon', LG_CALEND_AUTUMN);
aff_color('grey', LG_CALEND_WINTER);
aff_color('black', LG_CALEND_BROWSER_DEP);
echo '</i><br />';
echo '</p>';
echo aff_bouton(2, LG_CALEND_CONVERT);
echo '</form>';

echo '<br />';
echo '<a name="paques"></a>';

?>
<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_CALEND_EASTER_CALC; ?></b></td>
    </tr>
</table>
<br><br>
<?php
echo '<form id="calcpaq" method="post" action="' . my_self() . '"';
echo '<p>' . $h_year . LG_SEMIC . '<input type="text" size="4" maxlength="4" name="san" value="' . $san . '"/></p>';
$annee = $san;
if (is_numeric($san) and ($san > 0)) {
    $annee = intval($san);
    $paques = juliantojd(03, 21, $annee) + easter_days($annee);
    $deb_input = '<input type="text" size="14" readonly="readonly" value="';
    echo my_html(LG_CALEND_LENT) . LG_SEMIC . $deb_input . Etend_Date_Inv(jdtojulian($paques - 42)) . '"/>&nbsp;&nbsp;';
    echo my_html(LG_CALEND_PALM_SUNDAY) . LG_SEMIC . $deb_input . Etend_Date_Inv(jdtojulian($paques - 7)) . '"/>&nbsp;&nbsp;';
    echo my_html(LG_CALEND_EASTER) . LG_SEMIC . $deb_input . Etend_Date_Inv(jdtojulian($paques)) . '"/>&nbsp;&nbsp;';
    echo my_html(LG_CALEND_ASCENSION) . LG_SEMIC . $deb_input . Etend_Date_Inv(jdtojulian($paques + 39)) . '"/>&nbsp;&nbsp;';
    echo my_html(LG_CALEND_PENTECOST) . LG_SEMIC . $deb_input . Etend_Date_Inv(jdtojulian($paques + 49)) . '"/><br />' . "\n";
}
echo aff_bouton(3, LG_CALEND_CALCULATE);
echo '</form>';

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>