<?php

// appelé en ajax pour avoir les évènements correspondant à un type

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

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
    if (isset($_GET['type_evt'])) ecrire($f_log, 'evt : ' . $_GET['type_evt']);
    if (isset($_GET['ref'])) ecrire($f_log, 'ref : ' . $_GET['ref']);
}

if (isset($_GET['type_evt'])) $type_evt = ($_GET['type_evt']);
else exit;

/*
if (isset($_GET['ref'])) $ref = ($_GET['ref']);
else exit;
*/

if ($debug) ecrire($f_log, 'suite...');

$x = Lit_Env();

header('Content-Type: text/xml; charset=UTF-8');
$dom = new DOMDocument('1.0', 'utf-8');
$message = $dom->createElement('message');
$message = $dom->appendChild($message);

$sql = 'SELECT Reference, Titre, Debut, Fin ' .
    'FROM ' . nom_table('evenements') .
    ' WHERE Code_Type ="' . $type_evt . '" ' .
    ' ORDER by Titre, Debut, Fin';

if ($debug) ecrire($f_log, 'sql : ' . $sql);

$id_maxi = 0;
$res = lect_sql($sql);
while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
    $dates = html_entity_decode(Etend_les_dates($enreg['Debut'], $enreg['Fin']), ENT_QUOTES, $def_enc);
    if ($debug) {
        ecrire($f_log, 'enreg-Reference : ' . $enreg['Reference']);
        ecrire($f_log, 'enreg-Titre : ' . $enreg['Titre']);
        ecrire($f_log, 'enreg-dates : ' . $dates);
    }
    if ($debug) ecrire($f_log, 'création évènement dans le dom');
    // $evenement = $dom->createElement('evenements', utf8_encode($enreg['Titre'].' '.$dates));
    $evenement = $dom->createElement('evenements', $enreg['Titre'] . ' ' . $dates);
    if ($debug) ecrire($f_log, 'appendChild dans le dom');
    $evenement = $message->appendChild($evenement);
    if ($debug) ecrire($f_log, 'set attribute dans le dom');
    $evenement->setAttribute('id', $enreg['Reference']);
    $id_maxi = max($enreg['Reference'], $id_maxi);
    if ($debug) ecrire($f_log, 'id_maxi : ' . $id_maxi);
}
if ($debug) ecrire($f_log, 'fin boucle sur les évènements');

if ($debug) ecrire($f_log, 'création maxi dans le dom');
$maxi = $dom->createElement('maxi', $id_maxi);
if ($debug) ecrire($f_log, 'ajout maxi dans le dom');
$maxi = $message->appendChild($maxi);
//$maxi->setAttribute('id', $enreg['Reference']);

if ($debug) ecrire($f_log, 'enregistrement du dom');
echo $dom->saveXML();
if ($debug) ecrire($f_log, 'fin rpc');

if ($debug) fclose($f_log);
