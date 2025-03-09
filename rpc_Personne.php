<?php

// appelé en ajax pour avoir les personnes correspondant à un nom 

session_start();
include_once('fonctions.php');

$debug = false;

function Etend_les_dates($date1, $date2, $forcage = false)
{
    $texte = '';
    if ($date1 != $date2) {
        if ($date1 != '') $texte .= Etend_date($date1, $forcage);
        if ($date2 != '') {
            if ($date1 != '') $texte .= ' - ';
            $texte .= Etend_date($date2, $forcage);
        }
    } else {
        if ($date1 != '') $texte .= Etend_date($date1, $forcage);
    }
    if ($texte != '') $texte = '(' . $texte . ')';
    return $texte;
}


if ($debug) {
    $f_log = open_log();
    ecrire($f_log, 'evt : ' . $_GET['idNomFam']);
    ecrire($f_log, 'ref : ' . $_GET['ref']);
}

if (isset($_GET['idNomFam'])) $idNomFam = ($_GET['idNomFam']);
else exit;

if ($debug) ecrire($f_log, 'suite...');

$x = Lit_Env();

header('Content-Type: text/xml; charset=UTF-8');

$dom = new DOMDocument('1.0', 'utf-8');
$message = $dom->createElement('message');
$message = $dom->appendChild($message);

$sql = 'SELECT Reference, Prenoms, Ne_le, Decede_Le '
    . 'FROM ' . nom_table('personnes')
    . ' WHERE idNomFam =' . $idNomFam;
if (!$_SESSION['estPrivilegie']) $sql = $sql . " and Diff_Internet = 'O' ";
$sql = $sql . ' ORDER by Prenoms, Ne_Le';

if ($debug) ecrire($f_log, 'sql : ' . $sql);

$id_maxi = 0;
$res = lect_sql($sql);

// Affichage de la requête SQL en debug
// $personnes = $dom->createElement('personnes', $sql);
// $personnes = $message->appendChild($personnes);
// $personnes->setAttribute('id', '0');

while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
    $dates = html_entity_decode(aff_annees_pers($enreg['Ne_le'], $enreg['Decede_Le']), ENT_QUOTES, $def_enc);
    $prenoms = $enreg['Prenoms'];
    $interdits = array('&');
    $prenoms = str_replace($interdits, '', $prenoms);
    if ($debug) {
        ecrire($f_log, 'enreg : ' . $enreg['Reference']);
        ecrire($f_log, 'enreg : ' . $prenoms);
        ecrire($f_log, 'enreg : ' . $dates);
    }
    // $personnes = $dom->createElement('personnes', utf8_encode($prenoms.' '.$dates));
    $personnes = $dom->createElement('personnes', $prenoms . ' ' . $dates);
    $personnes = $message->appendChild($personnes);
    $personnes->setAttribute('id', $enreg['Reference']);
    $id_maxi = max($enreg['Reference'], $id_maxi);
}

$maxi = $dom->createElement('maxi', $id_maxi);
// $maxi = $dom->createElement('maxi', utf8_encode($id_maxi));
$maxi = $message->appendChild($maxi);

echo $dom->saveXML();
/* 
Donne :
<?xml version="1.0" encoding="utf-8"?>
<message>
<personnes id="1037">Bonaventure &nbsp;(~1661-1712/)</personnes>
<personnes id="1041">François Philippe &nbsp;(1688-?)</personnes>
<personnes id="855">Marie &nbsp;(?-/1752)</personnes>
<maxi>1041</maxi>
</message>
*/
if ($debug) fclose($f_log);
