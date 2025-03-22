<?php 

// Récupération des anniversaires de naissance du jour et du lendemain
$nbAuj    = 0;
$nbDemain = 0;
// Date du jour
$LaDate = date('Ymd');
$xAnnee = substr($LaDate, 0, 4);
$xMoisA = substr($LaDate, 4, 2);
$xJourA = substr($LaDate, 6, 2);
// On ne refera les accès aux anniversaires que :
// - s'ils n'ont pas été faits
// - ou s'ils ont été faits sur un autre jour
$deja_acces_anniv = true;
if (!isset($_SESSION['laDateJ'])) {
    $_SESSION['laDateM'] = $xMoisA;
    $_SESSION['laDateJ'] = $xJourA;
    $deja_acces_anniv = false;
} else {
    if ($_SESSION['laDateJ'] != $xJourA) {
        $_SESSION['laDateM'] = $xMoisA;
        $_SESSION['laDateJ'] = $xJourA;
        $deja_acces_anniv = false;
    }
}
// Date du lendemain
$mkDemain = mktime(00, 00, 00, intval($xMoisA), intval($xJourA) + 1, intval($xAnnee));
$Demain = date('Ymd', $mkDemain);
$xMoisD  = substr($Demain, 4, 2);
$xJourD  = substr($Demain, 6, 2);
$n_personnes = nom_table('personnes');
$ajout = ' ';
$nbAuj = $_SESSION['AnnivA'];
$nbDemain = $_SESSION['AnnivD'];
if (!$deja_acces_anniv) {
    if (!$_SESSION['estPrivilegie']) $ajout = ' and Diff_Internet = \'O\'';
    $sql2 = 'SELECT count(*),\'A\' FROM ' . $n_personnes . ' WHERE Ne_le like \'____' . $xMoisA . $xJourA . '_L\'' . $ajout .
        ' union ' .
        'SELECT count(*),\'D\' FROM ' . $n_personnes . ' WHERE Ne_le like \'____' . $xMoisD . $xJourD . '_L\'' . $ajout;
    if ($res = lect_sql($sql2)) {
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $nb    = $row[0];
            $quand = $row[1];
            if ($quand == 'A')
                $nbAuj = $nb;
            else
                $nbDemain = $nb;
        }
        $_SESSION['AnnivA'] = $nbAuj;
        $_SESSION['AnnivD'] = $nbDemain;
    }
}
$date_mod = '';
if ($Modif_Site != '0000-00-00 00:00:00') {
    $date_mod = $LG_index_last_update . ' ' . DateTime_Fr($Modif_Site);
}
echo '<table width="95%" cellspacing="1" cellpadding="3" align="center" class="tab_bord_bas">';

// Affichage du nombre d'anniversaires de naissance pour le jour même et le lendemain
// Va-t-on afficher des anniversaires et la date de modif ?
if ($nbAuj or $nbDemain or ($date_mod != '')) {
    echo '<tr><td>';
    if ($nbAuj or $nbDemain) {
        echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . $LG_tip . '" title="' . $LG_tip . '"> <a href="' . $root . '/anniversaires">' . $LG_index_birthdays . '</a>  : ';
        if ($nbAuj != 0) echo $nbAuj . ' ' . $LG_index_today . ' ';
        if (($nbAuj != 0) and ($nbDemain != 0)) echo $LG_and . ' ';
        if ($nbDemain != 0) echo $nbDemain . ' ' . $LG_index_tomorrow . ' ';
    }
    echo '</td></tr>';
}
echo '</table>';
?>