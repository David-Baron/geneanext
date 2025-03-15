<?php

//=====================================================================
// Calculatrice Sosa
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$x = Lit_Env();
$titre = $LG_Menu_Title['Calc_Sosa'];
$mots = 'Sosa';
require(__DIR__ . '/app/ressources/gestion_pages.php');

function aff_bouton($nb, $evt, $txt)
{
    echo '<td colspan="2">';
    echo '<div id="boutons' . $nb . '">' . "\n";
    echo '<table border="0" cellpadding="0" cellspacing="0">' . "\n";
    echo '<tr><td>';
    echo '<div class="buttons">';
    echo '<button type="submit" class="positive" style="width:100px;" onclick="' . $evt . ';return false;"> ' . my_html($txt) . '</button>';
    echo '</div>';
    echo '</td></tr>';
    echo '</table>' . "\n";
    echo '</div>' . "\n";
    echo '</td>' . "\n";
    echo '<td>&nbsp;</td>';
    echo "</tr>\n";
}
?>
<script type="text/javascript">
    function aj_car(chaine) {
        document.forms.calculette.zone_saisie.value += chaine;
    }

    function fin_pme_entree() {
        curvalue = document.forms.calculette.resultat.value;
        curvalue = curvalue.substring(curvalue.indexOf("=") + 1, curvalue.length);
        //document.forms.calculette.zone_saisie.value = curvalue;
        efface_entree();
    }

    function gener_entree() {
        curvalue = document.forms.calculette.zone_saisie.value;
        nb = 1;
        nb_gen = 1;
        // on calcule le nombre de départ pour une génération jusqu'à dépassement  
        while (nb <= curvalue) {
            nb_gen++;
            nb *= 2;
        }
        // on redescend d'un cran pour calculer le côté
        nb_gen -= 1;
        nb /= 2;
        nb += (nb / 2);
        cote = '';
        if (nb_gen > 1) {
            (curvalue >= nb) ? cote = "<?php echo $LG_Ch_Calc_Mo_Side; ?>": cote = "<?php echo $LG_Ch_Calc_Fa_Side; ?>";
        }
        document.forms.calculette.resultat.value = "<?php echo $LG_Ch_Calc_Gen_Of; ?> " + curvalue +
            " = " + nb_gen + " " + cote;
        //document.forms.calculette.zone_saisie.value = curvalue;
        efface_entree();
    }

    function pere_entree() {
        curvalue = document.forms.calculette.zone_saisie.value;
        aj_car("*2");
        document.forms.calculette.resultat.value = "<?php echo $LG_Ch_Calc_Fa_Of; ?> " +
            curvalue +
            " = " +
            eval(document.forms.calculette.zone_saisie.value);
        //fin_pme_entree()
        efface_entree();
    }

    function mere_entree() {
        curvalue = document.forms.calculette.zone_saisie.value;
        aj_car("*2+1");
        document.forms.calculette.resultat.value = "<?php echo $LG_Ch_Calc_Mo_Of; ?> " +
            curvalue +
            " = " +
            eval(document.forms.calculette.zone_saisie.value);
        fin_pme_entree()
    }

    function enfant_entree() {
        curvalue = document.forms.calculette.zone_saisie.value;
        aj_car("/2");
        document.forms.calculette.resultat.value = "<?php echo $LG_Ch_Calc_Ch_Of; ?> " +
            curvalue +
            " = " +
            Math.floor(eval(document.forms.calculette.zone_saisie.value));
        fin_pme_entree()
    }

    function conjoint_entree() {
        var saisie = parseInt(document.forms.calculette.zone_saisie.value);
        if (saisie != 'NaN') {
            var res = saisie;
            if (pair(saisie)) res++;
            else res -= 1;
            document.forms.calculette.resultat.value = "<?php echo $LG_Ch_Calc_Husb_Wif_Of; ?> " + saisie + " = " + res;
        }
        efface_entree();
    }

    function backspace() {
        curvalue = document.forms.calculette.zone_saisie.value;
        curlength = curvalue.length;
        curvalue = curvalue.substring(0, curlength - 1);
        document.forms.calculette.zone_saisie.value = curvalue;
    }

    function efface_entree() {
        document.forms.calculette.zone_saisie.value = "";
    }
</script>

<?php

function touche_chiffre($chiffre)
{
    echo '<td><input class="BoutonCalc_1" onclick="aj_car(\'' . $chiffre . '\')" type="button" value="' . $chiffre .
        '" name="T' . $chiffre . '" id="T' . $chiffre . '"/></td>' . "\n";
}

$compl = Ajoute_Page_Info(600, 200);
Insere_Haut($titre, $compl, 'Calc_So', '');

echo '<br />';
echo '<table width="85%">' . "\n";
echo '<tr><td>' . "\n";
echo '<form action="' . my_self() . '" id="calculette" method="post">' . "\n";
echo '<table cellspacing="3" cellpadding="0" bgcolor="' . $fond_calc . '" border="0" width="300" align="center">' . "\n";
echo "<tbody>\n";
echo "<tr>\n";
echo '<td colspan="6" valign="middle" align="center" width="100%" bgcolor="' . $fond_calc . '">' . "\n";
echo '<input class="calc_read" style="FONT-SIZE:14pt; WIDTH:97%; HEIGHT:30px" size="25" name="resultat"/>' . "\n";
echo "</td></tr>\n";
echo "<tr>\n";
echo '<td colspan="5" valign="middle" align="center"><font color="#0000ff" size="3">' . "\n";
echo '<input style="FONT-WEIGHT: bold; FONT-SIZE: 10pt; WIDTH: 100%; HEIGHT: 25px" size="15" name="zone_saisie"/></font>' . "\n";
echo "</td>\n";
echo '<td bgcolor="' . $fond_calc . '" align="center" valign="middle">&nbsp;<img id="efface" src="' . $chemin_images_icones . $Icones['efface']
    . '" alt="' . $LG_Ch_Calc_Clear . '" title="' . $LG_Ch_Calc_Clear . '" onclick="efface_entree();"/>';

echo "</td></tr>\n";
echo '<tr align="center">' . "\n";
$x = touche_chiffre(7);
$x = touche_chiffre(8);
$x = touche_chiffre(9);
aff_bouton(1, 'gener_entree()', $LG_Ch_Calc_Gen);

echo '<tr align="center">' . "\n";
$x = touche_chiffre(4);
$x = touche_chiffre(5);
$x = touche_chiffre(6);
aff_bouton(2, 'conjoint_entree()', $LG_Ch_Calc_Husb_Wif);

echo '<tr align="center">' . "\n";
$x = touche_chiffre(1);
$x = touche_chiffre(2);
$x = touche_chiffre(3);
aff_bouton(3, 'pere_entree()', LG_FATHER);

echo '<tr align="center">' . "\n";
$x = touche_chiffre(0);
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
aff_bouton(4, 'mere_entree()', LG_MOTHER);

echo '<tr align="center">' . "\n";
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
aff_bouton(5, 'enfant_entree()', $LG_Ch_Calc_Child);

echo '</tbody>';
echo "</table>\n";
echo "</form>\n";
echo '</td></tr>' . "\n";
echo '</table>' . "\n";

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