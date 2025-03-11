<?php
//=====================================================================
// Formulaire de saisie de date
//=====================================================================

require(__DIR__ . '/app/ressources/fonctions.php');

$aff_req = false;
$x = Lit_Env();
$zone2 = Recup_Variable('zone', 'S');
$zoneaff = Recup_Variable('zoneaff', 'S');
$contenu = Recup_Variable('contenu', 'S');
$bg = '';
if (file_exists($chemin_images . $Image_Fond))
    $bg = ' background="' . $chemin_images . $Image_Fond . '"';

$rev = false;
$gre = false;
$precision = '';
if (strlen($contenu) == 10) {
    $precision = $contenu[9];
    if ($contenu[8] == 'G') {
        $gre  = true;
        $an   = substr($contenu, 0, 4);
        $mois = substr($contenu, 4, 2);
        $jour = substr($contenu, 6, 2);
    }
    if ($contenu[8] == 'R') {
        $rev = true;
        // Calcul du nombre de jours de la date
        $jd = gregoriantojd(substr($contenu, 4, 2), substr($contenu, 6, 2), substr($contenu, 0, 4));
        // On passe en date révolutionnaire
        $resu = jdtofrench($jd);
        // On étend la date révolutionnaire
        $S1 = strpos($resu, '/');
        $S2 = strrpos($resu, '/');
        $mois = intval(substr($resu, 0, $S1));
        $jour = substr($resu, $S1 + 1, $S2 - $S1 - 1);
        $an = substr($resu, $S2 + 1, 2);
    }
}

// Valeurs par défaut
if ($precision == '') $precision = 'L';
if ((!$gre) and (!$rev)) $gre  = true;

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php Ecrit_Meta(LG_CAL_TITLE, 'Choix date', ''); ?>
</head>


<?php
echo '<body vlink="#0000ff" link="#0000ff" ' . $bg . ' onload="dateinit(this.form)">';
echo '<form id="formInsert" method="post">';
echo '<fieldset>';
aff_legend(LG_CAL_WITH_SELECT);
echo '<table align="center">';
echo '<tr><td colspan="2" align="center" class="rupt_table">';
echo '<input type="hidden" name="contenu" value="' . $contenu . '"/>';
bouton_radio('Precision', 'E', $LG_year['abt'], $precision == 'E' ? true : false);
bouton_radio('Precision', 'A', $LG_year['bf'], $precision == 'A' ? true : false);
bouton_radio('Precision', 'L', $LG_day['on'], $precision == 'L' ? true : false);
bouton_radio('Precision', 'P', $LG_year['af'], $precision == 'P' ? true : false);
echo '</td></tr>';
echo '<tr class="liste">';
echo '<td><input type="radio" name="typeDate" value="G" onclick="bloque(\'R\')" ';
if ($gre) echo 'checked="checked"';
echo '/>' . my_html(LG_CAL_GREGORIAN) . '</td>';
echo '<td>';
echo '<select id="listJour" name="listJour"';
if ($rev) {
    echo ' disabled="disabled"';
}
echo ">";
for ($j = 1; $j <= 31; $j++) {
    echo "<option value=\"" . sprintf("%02d", $j) . "\"";
    if (isset($jour) and $gre and (sprintf("%02d", $j) == $jour)) {
        echo ' selected="selected"';
    }
    echo ">" . sprintf("%02d", $j);
    echo "</option>\n";
}
echo "</select>";
echo "&nbsp;<select id=\"listMois\" name=\"listMois\"";
if ($rev) {
    echo ' disabled="disabled"';
}
echo ">";
for ($m = 0; $m <= 11; $m++) {
    echo "<option value=\"" . sprintf("%02d", ($m + 1)) . "\"";
    if (isset($mois) and $gre and ($m + 1 == $mois)) {
        echo ' selected="selected"';
    }
    echo ">" . $Mois_Lib[$m] . "</option>\n";
}
echo "</select>";
echo '&nbsp;<input type="text" id="Annee" name="Annee" size="4" maxlength="4" value="';
if (isset($an) and $gre) {
    echo $an;
}
echo '"';
if ($rev) {
    echo ' disabled="disabled"';
}
echo '/>';
echo '&nbsp;' . Affiche_Icone_Clic('aujourdhui', 'aujourdui();', LG_CAL_TODAY) . "\n";

echo '</td></tr>';
echo '<tr class="liste2">';
echo '<td><input type="radio" name="typeDate" value="R" onclick="bloque(\'G\')"';
if ($rev) echo 'checked="checked"';
echo '/>' . my_html(LG_CAL_REVOLUTIONARY) . '</td>';
echo '<td>';
echo '<select id="listJourR" name="listJourR"';
if ($gre) {
    echo ' disabled="disabled"';
}
echo ">";
for ($j = 1; $j <= 30; $j++) {
    echo '<option value="' . $j . '"';
    if ($rev and ($j == $jour)) {
        echo ' selected="selected"';
    }
    echo ">" . sprintf("%02d", $j);
    echo "</option>\n";
}
echo '</select>';
echo '&nbsp;<select id="listMoisR" name="listMoisR"';
if ($gre) {
    echo ' disabled="disabled"';
}
echo ">";
for ($m = 0; $m <= 12; $m++) {
    echo '<option value="' . sprintf("%02d", ($m + 1)) . '"';
    if ($rev and ($m + 1 == $mois)) {
        echo ' selected="selected"';
    }
    echo ">" . $ListeMoisRev[$m] . "</option>\n";
}
echo '</select>';
echo '&nbsp;<select id="AnneeR" name="AnneeR"';
if ($gre) {
    echo ' disabled="disabled"';
}
echo '>';
for ($a = 0; $a <= 13; $a++) {
    echo '<option value="' . ($a + 1) . '"';
    if ($rev and ($a + 1 == $an)) {
        echo ' selected="selected"';
    }
    echo ">" . $ListeAnneesRev[$a] . "</option>\n";
}
echo '</select>';

echo '<a class="lienCal" id="Calend" href="#" title="Calendrier"></a></td>';
echo '</tr>';
echo '</table>';
echo '</fieldset>';

echo '<fieldset>';
aff_legend(LG_CAL_QUICK_FILL);
echo '<table>';
echo '<tr><td align="center"><input type="text" name="grego_rapide" size="10" maxlength="10" onkeyup="masqueSaisieDate(this.form.grego_rapide);"/></td></tr>';
echo '<tr><td>' . Affiche_Icone('tip', 'Conseil') . ' ' . LG_CAL_FILL_TIP . '</td></tr>' . "\n";
echo '</table>';
echo '</fieldset>';

$LaDate = date('Ym');
$xAnnee = substr($LaDate, 0, 4);
$xMoisA = substr($LaDate, 4, 2);
echo '<fieldset>';
aff_legend(LG_CAL_CALCULATE);
echo '<form action="" id="cDate" method="post">';
echo '<table align="center">';
echo '<tr class="liste2">';
echo '<td>&nbsp;</td>';
echo '<td>' . my_html(LG_CAL_MONTH) . '</td>';
echo '<td>' . my_html(LG_CAL_YEAR) . '</td>';
echo '</tr>';
echo '<tr class="liste">';
echo '<td>' . my_html(LG_CAL_BEG) . '</td>';
echo '<td><input type="text" name="dMois" id="dMois" size="2" value="' . $xMoisA . '"/></td>';
echo '<td><input type="text" name="dAnnee" id="dAnnee" size="4" value="' . $xAnnee . '"/></td>';
echo '<td align="center"><input class="BoutonCalc2" style="width:100%;" onclick="plus_date();" type="button" value="+" name="TP"/></td>';
echo '</tr>';
echo '<tr class="liste">';
echo '<td>' . my_html(LG_CAL_OFFSET) . '</td>';
echo '<td><input type="text" name="decal_Mois" id="decal_Mois" size="2" value="00"/></td>';
echo '<td><input type="text" name="decal_Annee" id="decal_Annee" size="4" value="0000"/></td>';
echo '<td align="center"><input class="BoutonCalc2" style="width:100%;" onclick="moins_date();" type="button" value="-" name="TM"/></td>';
echo '</tr>';
echo '<tr class="liste2">';
echo '<td>' . my_html(LG_CAL_RESULT) . '</td>';
echo '<td><input type="text" readonly="readonly" name="rMois" id="rMois" size="2"/></td>';
echo '<td><input type="text" readonly="readonly" name="rAnnee" id="rAnnee" size="4"/></td>';
echo '<td><input class="BoutonCalc2" style="color:red;" onclick="efface_entree_date();" type="button" value="C" name="T0"/></td>';
echo '</tr>';
echo '</table>';
echo '</form>';
echo '</fieldset>';

echo '<table align="center">';
echo '<tr>';
echo '<td colspan="2" align="center">';
echo '<div id="boutons">' . "\n";
echo '<table border="0" cellpadding="0" cellspacing="0">' . "\n";
echo '<tr><td>&nbsp;';
echo '<div class="buttons">';
echo '<button type="submit" class="positive" onclick="lien();"><img src="' . $chemin_images_icones . $Icones['fiche_validee'] . '" alt=""/>' . $lib_Okay . '</button>';
echo '<button type="submit" onclick="window.close();"><img src="' . $chemin_images_icones . $Icones['cancel'] . '" alt=""/>' . $lib_Annuler . '</button>';
echo '<button type="submit" class="negative" onclick="efface();"><img src="' . $chemin_images_icones . $Icones['supprimer'] . '" alt=""/>' . $lib_Erase . '</button>';
echo '</div>';
echo '</td></tr>';
echo '</table>';

echo '</form>';
?>

<script type="text/javascript">
    <?php
    echo "var error_date = '" . LG_CAL_ERROR_DATE . "';";
    echo "var error_boundaries = '" . LG_CAL_ERROR_BOUNDARIES . "';";
    // Libellés des mois grégoriens
    echo 'var lmoisg_lg = new Array("null"' .
        ',"' . $Mois_Lib_h[0] . '"' .
        ',"' . $Mois_Lib_h[1] . '"' .
        ',"' . $Mois_Lib_h[2] . '"' .
        ',"' . $Mois_Lib_h[3] . '"' .
        ',"' . $Mois_Lib_h[4] . '"' .
        ',"' . $Mois_Lib_h[5] . '"' .
        ',"' . $Mois_Lib_h[6] . '"' .
        ',"' . $Mois_Lib_h[7] . '"' .
        ',"' . $Mois_Lib_h[8] . '"' .
        ',"' . $Mois_Lib_h[9] . '"' .
        ',"' . $Mois_Lib_h[10] . '"' .
        ',"' . $Mois_Lib_h[11] . '");';
    // Libellés des mois grégoriens
    echo 'var lmoisr_lg = new Array("null"' .
        ',"' . $Mois_Lib_rev_h[0] . '"' .
        ',"' . $Mois_Lib_rev_h[1] . '"' .
        ',"' . $Mois_Lib_rev_h[2] . '"' .
        ',"' . $Mois_Lib_rev_h[3] . '"' .
        ',"' . $Mois_Lib_rev_h[4] . '"' .
        ',"' . $Mois_Lib_rev_h[5] . '"' .
        ',"' . $Mois_Lib_rev_h[6] . '"' .
        ',"' . $Mois_Lib_rev_h[7] . '"' .
        ',"' . $Mois_Lib_rev_h[8] . '"' .
        ',"' . $Mois_Lib_rev_h[9] . '"' .
        ',"' . $Mois_Lib_rev_h[10] . '"' .
        ',"' . $Mois_Lib_rev_h[11] . '"' .
        ',"' . $Mois_Lib_rev_h[12] . '");'
    ?>

    // Conversion adaptée depuis
    //http://www.imcce.fr/page.php?nav=fr/ephemerides/astronomie/Promenade/pages4/435.html

    w = self;

    // objets a ranger
    function oar(OK, CODE1, CODE2) {
        this.OK
        this.CODE1
        this.CODE2
        this.CODE6
        this.JSEM
    }

    function date(JJD, AN, MOIS, JOUR, TYPEA, NBMOIS) {
        this.JJD
        this.AN
        this.MOIS
        this.JOUr
        this.TYPEA
        this.NBMOIS
        this.NBJRS
    }

    function trunc(x) {
        if (x > 0.0) return (Math.floor(x));
        else return Math.ceil(x);
    }

    function round(x) {
        if (x > 0.0) return (Math.floor(x + 0.5));
        else return Math.ceil(x - 0.5);
    }

    function JULIEN() {
        IJOUR = date.JOUR;
        Y = date.AN;
        M = date.MOIS;
        A = Y + M / 100 + IJOUR / 10000;
        if (date.MOIS <= 2) {
            Y = Y - 1;
            M = M + 12;
        };
        if (Y < 0) date.JJD = Math.ceil(365.25 * Y - 0.75);
        else date.JJD = Math.floor(365.25 * Y);
        date.JJD = date.JJD + trunc(30.6001 * (M + 1)) + IJOUR + 1720994.5;
        if (A > 1582.1014) date.JJD = date.JJD + 2 - trunc(Y / 100) + trunc(trunc(Y / 100) / 4);
    }

    function JJDATE() {
        Z1 = date.JJD + 0.5;
        Z = trunc(Z1);
        if (Z < 2299161) A = Z;
        else {
            ALPHA = trunc((Z - 1867216.25) / 36524.25);
            A = Z + 1 + ALPHA - trunc(ALPHA / 4);
        }
        B = A + 1524;
        C = trunc((B - 122.1) / 365.25);
        D = trunc(365.25 * C);
        E = trunc((B - D) / 30.6001);
        date.JOUR = trunc(B - D - trunc(30.6001 * E));
        if (E < 13.5) date.MOIS = trunc(E - 1);
        else date.MOIS = trunc(E - 13);
        if (date.MOIS >= 3) date.AN = trunc(C - 4716);
        else date.AN = trunc(C - 4715);
    }

    function BISG() {
        date.NBMOIS = 12;
        date.TYPEA = 0;
        if ((date.AN % 4) == 0) date.TYPEA = 1;
        if ((date.AN % 100) == 0 && (date.AN % 400) != 0) date.TYPEA = 0;
    }

    function BISR() {
        date.NBMOIS = 13;
        if (((date.AN + 1) % 4) == 0) date.TYPEA = 1;
        else date.TYPEA = 0;
    }

    function DEBANR(an) {
        dt = an;
        dt = (dt - 1) / 4;
        jan = trunc(dt);
        dt = trunc(dt);
        dj = dt * 1461;
        dan = an - (jan * 4);
        dj = 2375838.5 + dj + (dan - 1) * 365 + 1;
        if ((dan / 4) == 1) dj++;
        return (dj);
    }

    function JREPU() {
        BORNE = new Array(0, 365, 730, 1096, 1461);
        DT = date.JJD - 2375839.5;
        AAN = DT / 1461;
        date.AN = trunc(AAN);
        DT = DT - (Math.floor(AAN) * 1461) + 1;
        date.AN = date.AN * 4;
        for (I = 0; I < 4; I++) {
            if ((BORNE[I] < DT) && (DT <= BORNE[I + 1])) {
                JAN = I + 1;
                DT = DT - BORNE[I];
            }
        }
        date.AN = date.AN + JAN;
        if (JAN == 3) date.TYPEA = 1;
        else date.TYPEA = 0;
        DJ = (DT - 1) / 30;
        date.MOIS = trunc(DJ) + 1;
        date.JOUR = trunc(DT - (trunc(DJ) * 30));
        DT = date.JOUR;
        while (DT > 10) DT = DT - 10;
        DT = DT - 1;
        oar.CODE6 = round(DT);
    }

    function REPUJ() {
        dj1 = DEBANR(date.AN);
        DT = date.AN;
        DT = (DT - 1) / 4;
        JAN = trunc(DT);
        DT = trunc(DT);
        date.JJD = DT * 1461;
        mul = date.JJD * 1000;
        DAN = date.AN - (JAN * 4);
        date.JJD = 2375838.5 + date.JJD + (DAN - 1) * 365 + 30 * (date.MOIS - 1) + date.JOUR;
        if ((DAN / 4) == 1) date.JJD++;
        DT = date.JOUR;
        while (DT > 10) DT = DT - 10;
        DT = DT - 1;
        oar.CODE6 = round(DT);
    }

    function NOMJOUR() {
        A = ((eval(date.JJD) + 2.5) / 7) % 1.0;
        oar.JSEM = round(A * 7);
    }

    function JJDATE() {
        Z1 = date.JJD + 0.5;
        Z = trunc(Z1);
        if (Z < 2299161) A = Z;
        else {
            ALPHA = trunc((Z - 1867216.25) / 36524.25);
            A = Z + 1 + ALPHA - trunc(ALPHA / 4);
        }
        B = A + 1524;
        C = trunc((B - 122.1) / 365.25);
        D = trunc(365.25 * C);
        E = trunc((B - D) / 30.6001);
        date.JOUR = trunc(B - D - trunc(30.6001 * E));
        if (E < 13.5) date.MOIS = trunc(E - 1);
        else date.MOIS = trunc(E - 13);
        if (date.MOIS >= 3) date.AN = trunc(C - 4716);
        else date.AN = trunc(C - 4715);
    }

    function BISG() {
        date.NBMOIS = 12;
        date.TYPEA = 0;
        if ((date.AN % 4) == 0) date.TYPEA = 1;
        if ((date.AN % 100) == 0 && (date.AN % 400) != 0) date.TYPEA = 0;
    }

    function BISR() {
        date.NBMOIS = 13;
        if (((date.AN + 1) % 4) == 0) date.TYPEA = 1;
        else date.TYPEA = 0;
    }


    function dateinit(form) {
        date.AN = 1792;
        date.MOIS = 9;
        date.JOUR = 22;
        JULIEN();
        JREPU();
    }

    // Bloque les zones grégoriennes ou révolutionnaires
    function bloque(zones) {
        // Bloque les zones grégoriennes
        if (zones == 'G') {
            document.forms.formInsert.listJour.disabled = true;
            document.forms.formInsert.listMois.disabled = true;
            document.forms.formInsert.Annee.disabled = true;
            document.forms.formInsert.listJourR.disabled = false;
            document.forms.formInsert.listMoisR.disabled = false;
            document.forms.formInsert.AnneeR.disabled = false;
        }
        // Bloque les zones révolutionnaires
        else {
            document.forms.formInsert.listJour.disabled = false;
            document.forms.formInsert.listMois.disabled = false;
            document.forms.formInsert.Annee.disabled = false;
            document.forms.formInsert.listJourR.disabled = true;
            document.forms.formInsert.listMoisR.disabled = true;
            document.forms.formInsert.AnneeR.disabled = true;
        }
    }

    // L'utilisateur à cliqué sur le bouton Aujourd'hui ; on lui présente la date du jour
    function aujourdui() {
        var madate = new Date();
        var annee = madate.getFullYear();
        var mois = madate.getMonth();
        // le mois commence à zéro
        mois++;
        if (mois < 10) mois = "0" + mois;
        var jour = madate.getDate();
        if (jour < 10) jour = "0" + jour;
        bloque('R');
        // On clique le bouton de la précision "Le"
        document.forms.formInsert.Precision[0].checked = false;
        document.forms.formInsert.Precision[1].checked = false;
        document.forms.formInsert.Precision[2].checked = true;
        document.forms.formInsert.Precision[3].checked = false;
        // On clique le bouton "Grégorien"
        document.forms.formInsert.typeDate[0].checked = true;
        document.forms.formInsert.typeDate[1].checked = false;
        // Positionnement de la date
        document.forms.formInsert.listJour.value = jour;
        document.forms.formInsert.listMois.value = mois;
        document.forms.formInsert.Annee.value = annee;
    }

    // Fonction de vérification de date avec les années bissextile par jerome.o, adaptation JLS
    // Accepte en entrée les dates sous la forme : 02/02/2004, 2-2-2004 avec des / ou -
    // Retourne false si la date est fausse ou érronée
    function date_valide(input) {
        //var regex = new RegExp("[/-]");
        //var date = input.split(regex);
        var nbJours = new Array('', 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        var result = true;
        var jour = input.substr(6, 2);
        var mois = input.substr(4, 2);
        var annee = input.substr(0, 4);

        if (annee % 4 == 0 && annee % 100 > 0 || annee % 400 == 0)
            nbJours['2'] = 29;

        if (isNaN(annee))
            result = false;

        if (isNaN(mois) || mois > 12 || mois < 1)
            result = false;

        if (isNaN(jour) || jour > nbJours[Math.round(mois)] || jour < 1)
            result = false;

        return result;
    }

    //lien qui sélectionne la date dans le formulaire de la fenêtre appelante
    function lien() {
        nb_zones_abs = 0;
        msg = '';
        // détection précision choisie : 'E' par défaut
        precision = 'x';
        for (var i = 0; i < document.forms.formInsert.Precision.length; i++) {
            if (document.forms.formInsert.Precision[i].checked) {
                precision = document.forms.formInsert.Precision[i].value;
            }
        }
        switch (precision) {
            case "E":
                exPre = "ca";
                break;
            case "L":
                exPre = "le";
                break;
            case "A":
                exPre = "avant le";
                break;
            case "P":
                exPre = "après le";
                break;
        }
        if (precision == 'x') {
            msg = 'précision ';
            ++nb_zones_abs;
        }
        type_date = 'x';
        for (var i = 0; i < document.forms.formInsert.typeDate.length; i++) {
            if (document.forms.formInsert.typeDate[i].checked) {
                type_date = document.forms.formInsert.typeDate[i].value;
            }
        }
        if (type_date == 'x') {
            if (nb_zones_abs > 0) msg = msg + ' et';
            msg = msg + ' type de date ';
            ++nb_zones_abs;
        }
        // Reconstitution de la date
        // Cas d'une date grégorienne
        if (type_date == 'G') {
            lmoisg = lmoisg_lg;

            // Année
            lAnnee = document.forms.formInsert.Annee.value;
            AnneeSaisie = lAnnee;
            if (AnneeSaisie == '') {
                if (nb_zones_abs > 0) msg = msg + ' et';
                msg = msg + ' année ';
                ++nb_zones_abs;
            }

            // Détection du mois
            for (var i = 0; i < document.forms.formInsert.listMois.length; i++) {
                if (document.forms.formInsert.listMois[i].selected) {
                    leMois = document.forms.formInsert.listMois[i].value;
                }
            }
            // Le parseInt ne marchait pas sur 8 et 9 !!!
            MoisSaisi = lmoisg[parseFloat(leMois)];
            // Détection du jour
            for (var i = 0; i < document.forms.formInsert.listJour.length; i++) {
                if (document.forms.formInsert.listJour[i].selected) {
                    leJour = document.forms.formInsert.listJour[i].value;
                }
            }
            JourSaisi = leJour;

            // On pad l'année sur 4 caractères
            lAnneeX = String(lAnnee);
            if (AnneeSaisie != '') {
                while (lAnneeX.length < 4) {
                    lAnneeX = '0' + lAnneeX;
                }
            }
            laDate = lAnneeX + String(leMois) + String(leJour);

            //laDate = String(lAnnee)+String(leMois)+String(leJour);

        }
        // Cas d'une date révolutionnaire
        if (type_date == 'R') {

            lmoisr = lmoisr_lg;
            lmoisr_old = new Array("null", "vendémiaire", "brumaire", "frimaire", "nivôse",
                "pluviôse", "ventôse", "germinal",
                "floréal", "prairial", "messidor", "thermidor",
                "fructidor", "sanculottides");

            ListeAnneesRev = new Array("I", "II", "III", "IV", "V", "VI", "VII",
                "VIII", "IX", "X", "XI", "XII", "XIII", "XIV");

            // Année
            for (var i = 0; i < document.forms.formInsert.AnneeR.length; i++) {
                if (document.forms.formInsert.AnneeR[i].selected) {
                    lAnnee = document.forms.formInsert.AnneeR[i].value;
                }
            }

            AnneeSaisie = ' de l\'an ' + ListeAnneesRev[parseInt(lAnnee) - 1];
            // Détection du mois
            for (var i = 0; i < document.forms.formInsert.listMoisR.length; i++) {
                if (document.forms.formInsert.listMoisR[i].selected) {
                    leMois = document.forms.formInsert.listMoisR[i].value;
                }
            }
            MoisSaisi = lmoisr[parseFloat(leMois)];
            // Détection du jour
            for (var i = 0; i < document.forms.formInsert.listJourR.length; i++) {
                if (document.forms.formInsert.listJourR[i].selected) {
                    leJour = document.forms.formInsert.listJourR[i].value;
                }
            }
            JourSaisi = leJour;

            date.AN = parseInt(lAnnee);
            date.MOIS = parseFloat(leMois);
            date.JOUR = parseInt(leJour);
            REPUJ(); /* calcul du JJD */
            if (date.JJD < 2375839.5 || date.JJD > 2380686.5) {
                alert(error_boundaries);
                return;
            }
            //affich_nbjrs(this.form);
            an = date.AN;
            mois = date.MOIS;
            jour = date.JOUR;

            NOMJOUR();
            //form.jjul.value=eval(date.JJD+0.5);
            /*grégorien*/
            JJDATE();
            BISG();

            // Date convertie en grégorien inverse
            lAnnee = String(date.AN);
            leMois = date.MOIS;

            if (leMois < 10) leMois = '0' + leMois;
            leMois = String(leMois);
            leJour = date.JOUR;

            if (leJour < 10) leJour = '0' + leJour;
            leJour = String(leJour);
            laDate = lAnnee + leMois + leJour;

            date.MOIS = mois;
            date.JOUR = jour;
        }

        // Doit-on prendre la date de saisie rapide ?
        //if (laDate.length < 8) {
        var laDateRap = document.forms.formInsert.grego_rapide.value;
        if (laDateRap.length == 10) {
            JourSaisi = laDateRap.substr(0, 2);
            MoisSaisi = laDateRap.substr(3, 2);
            AnneeSaisie = laDateRap.substr(6, 4);
            laDate = AnneeSaisie + MoisSaisi + JourSaisi;
            MoisSaisi = lmoisg[parseFloat(MoisSaisi)];
        }
        //}

        // Contrôle de la date
        var laDate_OK = true;
        if (nb_zones_abs == 0) {
            laDate_OK = date_valide(laDate);
        }

        // L'année grégorienne n'est pas saisie, on la repmlace par la date rapide
        if (nb_zones_abs == 1) {
            if (laDateRap.length == 10) {
                nb_zones_abs = 0;
                msg = '';
            }
        }

        if (!laDate_OK) {
            msg = error_date;
            //nb_zones_abs = 0;
        }

        if (msg != '') {
            if (nb_zones_abs > 1)
                window.alert('Les zone ' + msg + ' sont obligatoires.');
            if (nb_zones_abs == 1)
                window.alert('La zone ' + msg + ' est obligatoire.');
            if (nb_zones_abs == 0)
                window.alert(msg);
        } else {
            laDateAff = exPre + ' ' + JourSaisi + ' ' + MoisSaisi + ' ' + AnneeSaisie;
            laDate += type_date + precision;

            // Recharge dans les zones passées en paramètre
            window.opener.document.forms['saisie'].elements['<?php echo $zone2; ?>'].value = laDate;
            window.opener.document.forms['saisie'].elements['<?php echo $zoneaff; ?>'].value = laDateAff;
            window.close();
        }
    }

    // Efface la date
    function efface() {
        window.opener.document.forms['saisie'].elements['<?php echo $zone2; ?>'].value = '';
        window.opener.document.forms['saisie'].elements['<?php echo $zoneaff; ?>'].value = '';
        window.close();
    }

    function masqueSaisieDate(obj) {
        var ch;
        var ch_gauche, ch_droite;
        ch = obj.value;
        ch.toString();
        if (((ch.slice(2, 3)) != ("/")) && (ch.length >= 3)) {
            ch_gauche = ch.slice(0, 2);
            ch_droite = ch.slice(2);
            obj.value = ch_gauche + "/" + ch_droite;
        }
        if (((ch.slice(5, 6)) != ("/")) && (ch.length >= 6)) {
            ch_gauche = ch.slice(0, 5);
            ch_droite = ch.slice(5);
            obj.value = ch_gauche + "/" + ch_droite;
        }
        return;
    }
</script>

</body>

</html>