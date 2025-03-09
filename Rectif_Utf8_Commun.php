<?php

// Rectification UTF_8, partie commune
// UTF-8

function rectif_UTF8() {
	global $nb_req;
	$champs[] = 'noms_famille,nomFamille';
	$champs[] = 'personnes,Nom';
	$champs[] = 'personnes,Prenoms';
	$champs[] = 'villes,Nom_Ville';
	$champs[] = 'departements,Nom_Depart_Min';
	$champs[] = 'regions,Nom_Region_Min';
	$champs[] = 'evenements,Titre';
	$champs[] = 'types_evenement,Libelle_Type';
	$champs[] = 'commentaires,Note';

	$lettre_A = array('Ã©', 'Ã¨', 'Ã§', 'Ã¯', 'Ã®', 'Ãª', 'Ã´', 'Ã¢', 'Ã‰', 'Ã«', 'ÃŠ', 'Ã¶');
	$lettre_B = array('é' ,  'è',  'ç',  'ï',  'î', 'ê' ,  'ô',  'â', 'É',  'ë',  'Ê' ,  'ö');
	//Ã ==> à, e, attente
	
	//ö

	$c_champ = count($champs);
	$c_lettres = count($lettre_A);

	$nb_req = 0;
	for ($nb1 = 0; $nb1 < $c_champ; $nb1++) {
		$tempo = explode(',',$champs[$nb1]);
		$col = $tempo[1];
		$deb_req = 'update '.nom_table($tempo[0]).' set '.$col.' = REPLACE('.$col;
		$fin_req = ' where '.$col.' like ';
		for ($nb2 = 0; $nb2 < $c_lettres; $nb2++) {
			$lA = $lettre_A[$nb2];
			$lB = $lettre_B[$nb2];
			$req = $deb_req . ',\'' . $lA . '\',\'' . $lB. '\')' . $fin_req . '\'%' . $lA . '%\'	';
			$res = maj_sql($req);
			$nb_req++;
		}
	}
}
?>
