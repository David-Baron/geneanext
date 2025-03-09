<?php
//====================================================================
//  Rectification de zones nulles
// (c) JLS
// a engistrer dans le repertoire www en enlevant l'extension ".txt"
// et a executer dans le navigateur
//=====================================================================

// Gestion standard des pages
session_start();
include('fonctions.php');

$acces = 'M';
$titre = 'Rectification de zones nulles en base';
$x = Lit_Env();
$niv_requis = 'G';				// Page reservee au profil gestionnaire
include('Gestion_Pages.php');

$compl = Ajoute_Page_Info(600,250);
Insere_Haut(my_html($titre),$compl,'Rectif_Null','');

$LG_mod = ' enregistrement(s) rectifi&eacute;(s)';

rectif_null("update ".nom_table('evenements')." set Identifiant_zone = 0 where Identifiant_zone is null",'Ev&egrave;nements : Identifiant_zone');
rectif_null("update ".nom_table('unions')." set Ville_Notaire = 0 where Ville_Notaire is null",'Unions : Ville_Notaire');
rectif_null("update ".nom_table('villes')." set latitude = 0 where latitude is null",'Villes : latitude');
rectif_null("update ".nom_table('villes')." set longitude = 0 where longitude is null",'Villes : longitude');

Insere_Bas($compl);

function rectif_null($req,$lib) {
	global $enr_mod, $LG_mod;
	$res = maj_sql($req);
	echo '<br/ >' . $lib . ' : '. $enr_mod . $LG_mod . '<br/ >';
}

?>
</body>
</html>