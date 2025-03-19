<?php

// appelé en ajax pour avoir les évènements correspondant à un type

require(__DIR__ . '/../app/ressources/fonctions.php');

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

if (isset($_GET['type_evt'])) $type_evt = ($_GET['type_evt']);
else exit;

/*
if (isset($_GET['ref'])) $ref = ($_GET['ref']);
else exit;
*/

$x = Lit_Env();

header('Content-Type: text/xml; charset=UTF-8');
$dom = new DOMDocument('1.0', 'utf-8');
$message = $dom->createElement('message');
$message = $dom->appendChild($message);

$sql = 'SELECT Reference, Titre, Debut, Fin ' .
    'FROM ' . nom_table('evenements') .
    ' WHERE Code_Type ="' . $type_evt . '" ' .
    ' ORDER by Titre, Debut, Fin';

$id_maxi = 0;
$res = lect_sql($sql);
while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
    $dates = html_entity_decode(Etend_les_dates($enreg['Debut'], $enreg['Fin']), ENT_QUOTES, $def_enc);
    // $evenement = $dom->createElement('evenements', utf8_encode($enreg['Titre'].' '.$dates));
    $evenement = $dom->createElement('evenements', $enreg['Titre'] . ' ' . $dates);
    $evenement = $message->appendChild($evenement);
    $evenement->setAttribute('id', $enreg['Reference']);
    $id_maxi = max($enreg['Reference'], $id_maxi);
}
$maxi = $dom->createElement('maxi', $id_maxi);
$maxi = $message->appendChild($maxi);
//$maxi->setAttribute('id', $enreg['Reference']);
echo $dom->saveXML();
