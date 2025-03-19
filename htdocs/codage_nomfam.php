<?php

require(__DIR__ . '/../app/Phonetique.php');

//	Récupération du nom
$nom = '';
if (!isset($_POST['nom'])) {
    echo 'Erreur';
    return;
}
$nom = rawurldecode($_POST['nom']);
//	Initialisation d'un objet de la classe phonetique
$codePho = new Phonetique();
$code = $codePho->calculer($nom);
echo $codePho->codeVersPhon($code);
