<?php

// appelé en ajax pour avoir les documents correspondant à un type

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$debug = false;

// paramètres passés :
// type_doc : type de document pour filtrer
// typeObjet : le type d'objet pour lequel on veut faire le lien
// refObjet  : la référence de l'objet pour lequel on veut faire le lien
// les 2 derniers paramètres permettent de ne pas aller chercher les documents déjà liés

if ($debug) {
    $f_log = open_log();
    ecrire($f_log, '---' . my_self());
    if (isset($_GET['type_doc'])) ecrire($f_log, 'type_doc : ' . $_GET['type_doc']);
    if (isset($_GET['typeObjet'])) ecrire($f_log, 'typeObjet : ' . $_GET['typeObjet']);
    if (isset($_GET['refObjet'])) ecrire($f_log, 'refObjet : ' . $_GET['refObjet']);
}

if (isset($_GET['type_doc'])) $type_doc = ($_GET['type_doc']);
else exit;
if (isset($_GET['typeObjet'])) $typeObjet = ($_GET['typeObjet']);
else exit;
if (isset($_GET['refObjet'])) $refObjet = ($_GET['refObjet']);
else exit;

if ($debug) ecrire($f_log, 'suite...');

$x = Lit_Env();

header('Content-Type: text/xml; charset=UTF-8');

$dom = new DOMDocument('1.0', 'utf-8');
$message = $dom->createElement('message');
$message = $dom->appendChild($message);

$sql = 'SELECT Id_Document, Nature_Document, Titre  ' .
    'FROM ' . nom_table('documents') .
    ' WHERE Id_Type_Document =' . $type_doc .
    '  AND Id_Document NOT IN ( SELECT Id_Document FROM ' . nom_table('concerne_doc') .
    ' WHERE Type_Objet="' . $typeObjet . '" AND Reference_Objet=' . $refObjet . ')' .
    ' ORDER by Titre';

if ($debug) ecrire($f_log, 'sql : ' . $sql);

$id_maxi = 0;
$res = lect_sql($sql);
while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
    //$dates = html_entity_decode(aff_annees_pers($enreg['Ne_le'],$enreg['Decede_Le']), ENT_QUOTES, $def_enc );
    if ($debug) {
        ecrire($f_log, 'enreg : ' . $enreg['Id_Document']);
        ecrire($f_log, 'enreg : ' . $enreg['Nature_Document']);
        ecrire($f_log, 'enreg : ' . $enreg['Titre']);
        //ecrire($f_log,'enreg : '.$dates);
    }
    // $refDoc = $dom->createElement('refDoc', utf8_encode($enreg['Titre'].' ('.$Natures_Docs[$enreg['Nature_Document']].')'));
    $refDoc = $dom->createElement('refDoc', $enreg['Titre'] . ' (' . $Natures_Docs[$enreg['Nature_Document']] . ')');
    $refDoc = $message->appendChild($refDoc);
    $refDoc->setAttribute('id', $enreg['Id_Document']);
    $id_maxi = max($enreg['Id_Document'], $id_maxi);
}

$maxi = $dom->createElement('maxi', $id_maxi);
$maxi = $message->appendChild($maxi);

echo $dom->saveXML();
/* 
Donne :
<?xml version="1.0" encoding="utf-8"?>
<message>
<refDoc id="1037">Bonaventure &nbsp;(~1661-1712/)</refDoc>
<refDoc id="1041">François Philippe &nbsp;(1688-?)</refDoc>
<refDoc id="855">Marie &nbsp;(?-/1752)</refDoc>
<maxi>1041</maxi>
</message>
*/
if ($debug) fclose($f_log);
