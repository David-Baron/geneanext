<?php
// UTF-8

include('phonetique.php');	//	Appel de la classe de codage phonétique

//	Récupération du nom
$nom = '';
if (!isset($_POST['nom']))
{
	echo 'Erreur';
	return;
}
$nom = rawurldecode($_POST['nom']);
//	Initialisation d'un objet de la classe phonetique
$codePho = new phonetique();
//
$code = $codePho->calculer($nom);
echo $codePho->codeVersPhon($code);
?>