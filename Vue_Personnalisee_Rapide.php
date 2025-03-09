<?php

//=====================================================================
// Cette page permet à un utilisateur d'obtenir une vue personnalisée 
// de la base à partir de la fiche d'une personne
// UTF-8
//=====================================================================

session_start();

// On simule le bouton OK pour ne pas écrire l'entête de la page
$ok = 'OK';

// Gestion standard des pages
include('fonctions.php');

$acces = 'L';							// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Vue personnalisée rapide';	// Titre pour META
$x = Lit_Env();
include('Gestion_Pages.php');

$Refer = Recup_Variable('Refer','N');

$ref_decujus = -1;
$sql = 'select Reference from '.nom_table('personnes').' where Numero = "1" limit 1';
if ($Res = lect_sql($sql)) {
	if ($pers = $Res->fetch(PDO::FETCH_NUM)) {
		$ref_decujus = $pers[0];
	}
	$Res->closeCursor();
}

$_SESSION['decujus'] = $Refer;
if ($Refer == $ref_decujus) $_SESSION['decujus_defaut'] = 'O';
else                        $_SESSION['decujus_defaut'] = 'N';

// Retour sur la page précédente
Retour_Ar();

?>

</body>
</html>