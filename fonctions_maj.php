<?php

// Fonctions spécialisées pour la mise à jour

// Modification d'une requete SQL avec un champ si modifié
function Aj_Zone_Req($NomRub,$Rub,$ARub,$TypRub,&$LaReq) {
	global $debug;
	if ($debug) {
		echo '======================<br />';
		echo ($NomRub).'<br />';
		echo gettype($Rub).'<br />';
		echo gettype($ARub).'<br />';
		echo $NomRub.' nouv  : '.$Rub.'<br />';
		echo $NomRub.' anc : '.$ARub.'<br />';
	}
	if ($Rub !== $ARub) {
		if ($LaReq != '') $LaReq = $LaReq.',';
		$LaReq = $LaReq.' '.$NomRub.'=';
		if ($Rub !== '') {
			if ($TypRub == 'A') {
				$Rub = addslashes($Rub);
				$LaReq = $LaReq .'"'.$Rub.'"';
			}
			else $LaReq = $LaReq .$Rub;
		}
		else {
			echo '$rub : '.$Rub.', vide<br />';
			$LaReq = $LaReq . 'null';
		}
	}
}

// Modification d'une requete SQL avec un champ si saisi
function Ins_Zone_Req($Rub,$TypRub,&$LaReq) {
  if ($LaReq != '') $LaReq = $LaReq.',';
  if ($Rub !== '') {
    if ($TypRub == 'A') {
    	$Rub = addslashes($Rub);
    	$LaReq = $LaReq .'"'.$Rub.'"';
    }
    else $LaReq = $LaReq .$Rub;
  }
  else $LaReq = $LaReq . 'null';
}

// Contrôle d'une image dont le chargement a été demandé par l'utilisateur
// Taille maxi et extension autorisée
function Controle_Charg_Image($nb_fic=-1) {
	global $taille_maxi_images,$nom_du_fichier;
	$erreur = '';
	if ($nb_fic == -1) {
		$nom_fic = $_FILES['nom_du_fichier']['name'];
		$type_fic = $_FILES['nom_du_fichier']['type'];
		$size_fic = $_FILES['nom_du_fichier']['size'];
		$tmp_name_fic = $_FILES['nom_du_fichier']['tmp_name'];
	}
	else {
		$nom_fic = $_FILES['nom_du_fichier']['name'][$nb_fic];
		$type_fic = $_FILES['nom_du_fichier']['type'][$nb_fic];
		$size_fic = $_FILES['nom_du_fichier']['size'][$nb_fic];
		$tmp_name_fic = $_FILES['nom_du_fichier']['tmp_name'][$nb_fic];
	}

  if ($nom_fic == '') {
    $erreur = "veuillez sp&eacute;cifiez un fichier &agrave; envoyer.";
  }
  elseif($type_fic != "image/png" && $type_fic != "image/jpeg"
      && $type_fic != "image/pjpeg" && $type_fic != "image/x-png"
      && $type_fic != "image/gif") {
    $erreur = "le fichier doit &ecirc;tre un JPEG, un GIF ou un PNG.";
  }
  elseif($size_fic > $taille_maxi_images['s']) {
    $erreur = "le fichier doit peser moins de ".($taille_maxi_images['s']/1024)." Ko.";
  }
  if($erreur == '') {
    $size_img = getimagesize($tmp_name_fic);
	if (!OK_taille_img_2plans($size_img[1],$size_img[0])) {
      $erreur = 'dimensions maximum autoris&eacute;es : '.$taille_maxi_images['w'].'x'.$taille_maxi_images['h']. 'pixels';
    }
  }
  return $erreur;
}

function Controle_Image($nom_fichier) {
	global $taille_maxi_images;
	$erreur = '';
	if ($erreur == '') {
		$caract = getimagesize($nom_fichier);
	}
	// Contrôle du type de fichier
	if ($erreur == '') {
		$type_fic = $caract[2];
		if ($type_fic == '') {
			$erreur = 'Le fichier doit &ecirc;tre une image';
		}
	}
	// Contrôle des dimensions de fichier
	if ($erreur == '') {
		if (!OK_taille_img_2plans($caract['h'],$caract['w'])) {
		//if($caract['w'] > $taille_maxi_images['w'] || $caract['h'] > $taille_maxi_images['h']) {
			$erreur = 'Dimensions maximum autoris&eacute;es : '.$taille_maxi_images['w'].'x'.$taille_maxi_images['h'].' pixels';
		}
	}
	// Contrôle du poids
	if ($erreur == '') {
		$fs = filesize($nom_fichier);
		if ($fs > $taille_maxi_images['s']) {
			$erreur = 'Le fichier est trop lourd pour être pris en compte (limite : '.$taille_maxi_images['s'].' octets, poids : '.$fs.')';
		}
	}
	return $erreur;
}

// Contrôle de la taille de l'image en portrait ou paysage
// Si elle est en portrait, le contrôle 800*600 (ou équivalent) devient 600*800
function OK_taille_img_2plans($hauteur,$largeur) {
	global $taille_maxi_images;
	$ctrl_OK = true;
	// Mode portrait
	if ($hauteur > $largeur) {
		if (($hauteur > $taille_maxi_images['w']) or
			($largeur > $taille_maxi_images['h'])) $ctrl_OK = false;
	}
	// Mode paysage
	else {
		if (($hauteur > $taille_maxi_images['h']) or
			($largeur > $taille_maxi_images['w'])) $ctrl_OK = false;
	}
	return $ctrl_OK;
}

// Contrôle d'un document dont le chargement a été demandé par l'utilisateur
// Taille maxi et extension autorisée
function Controle_Charg_Doc($nb_fic=-1) {
	global $taille_maxi_images,$nom_du_fichier;
	$erreur = '';
	if ($nb_fic == -1) {
		$nom_fic = $_FILES['nom_du_fichier']['name'];
		$type_fic = $_FILES['nom_du_fichier']['type'];
		$size_fic = $_FILES['nom_du_fichier']['size'];
		$tmp_name_fic = $_FILES['nom_du_fichier']['tmp_name'];
	}
	else {
		$nom_fic = $_FILES['nom_du_fichier']['name'][$nb_fic];
		$type_fic = $_FILES['nom_du_fichier']['type'][$nb_fic];
		$size_fic = $_FILES['nom_du_fichier']['size'][$nb_fic];
		$tmp_name_fic = $_FILES['nom_du_fichier']['tmp_name'][$nb_fic];
	}

  if ($nom_fic == '') {
    $erreur = "veuillez sp&eacute;cifiez un fichier &agrave; envoyer.";
  }

	// Taille maxi limité à 33% de la taille des images
	if ($erreur == '') {
		$taille_max = floor($taille_maxi_images['s']/3);
		if ($size_fic > $taille_max) {
			$erreur = "le fichier doit peser moins de ".($taille_max/1024)." Ko.";
		}
	}
  return $erreur;
}

// Fonction pour l'ajout rapide de subdivision depuis une fiche évènement par exemple
// La fonction retourne le numéro de la subdivision, créée ou non
function Ajoute_Subdivision($subdivision) {
  if (!is_numeric($subdivision)) {
    $num_subdivision = Nouvel_Identifiant('Identifiant_zone','subdivisions');
    $req = 'insert into '.nom_table('subdivisions').' values('.$num_subdivision.',\''.addslashes($subdivision).'\','.
           'current_timestamp,current_timestamp,\'N\',0,0,0)';
    $res = maj_sql($req);
  }
  else
    $num_subdivision = $subdivision;
  return $num_subdivision;
}

// Fonction pour l'ajout rapide de ville depuis une fiche personne par exemple
// La fonction retourne le numéro de la ville, créée ou non
function Ajoute_Ville($ville) {
  if (!is_numeric($ville)) {
    $num_ville = Nouvel_Identifiant('Identifiant_zone','villes');
    $req = 'insert into '.nom_table('villes').' values('.$num_ville.',\''.addslashes($ville).'\',null,'.
           'current_timestamp,current_timestamp,\'N\',0,0,0)';
    $res = maj_sql($req);
  }
  else
    $num_ville = $ville;
  return $num_ville;
}

// Ajout rapide d'un département
function Ajoute_Departement($depart) {
  if (!is_numeric($depart)) {
    $num_depart = Nouvel_Identifiant('Identifiant_zone','departements');
    $req = 'insert into '.nom_table('departements').' values('.$num_depart.',0,\''.addslashes($depart).'\','.
           'current_timestamp,current_timestamp,\'N\',0)';
    $res = maj_sql($req);
  }
  else
    $num_depart = $depart;
  return $num_depart;
}

// Ajout rapide d'une région
function Ajoute_Region($region) {
  if (!is_numeric($region)) {
    $num_region = Nouvel_Identifiant('Identifiant_zone','regions');
    $req = 'insert into '.nom_table('regions').' values('.$num_region.',0,\''.addslashes($region).'\','.
           'current_timestamp,current_timestamp,\'N\',0)';
    $res = maj_sql($req);
  }
  else
    $num_region = $region;
  return $num_region;
}

// Ajout rapide d'un pays
function Ajoute_Pays($pays) {
  if (!is_numeric($pays)) {
    $num_pays = Nouvel_Identifiant('Identifiant_zone','pays');
    $req = 'insert into '.nom_table('pays').' values('.$num_pays.',\'\',\'\',0,  \''.addslashes($pays).'\','.
           'current_timestamp,current_timestamp,\'N\')';
    $res = maj_sql($req);
  }
  else
    $num_pays = $pays;
  return $num_pays;
}

// Fonction pour l'ajout rapide d'un nom depuis une fiche personne par exemple
// La fonction retourne le numéro du nom, créée ou non
function Ajoute_Nom($idNom,$Nom) {
	global $connexion;
  if ($idNom == 0) {
  	$Nom = addslashes($Nom);
  	$sql = 'select idNomFam from '.nom_table('noms_famille').' where upper(nomFamille) = "'.ucwords($Nom).'"';
	if ($res = lect_sql($sql)) {
		if ($ville = $res->fetch(PDO::FETCH_NUM)) {
			$idNom = $ville[0];
		}
	}
	if ($idNom == 0) {
	  	include_once('phonetique.php');
		$objetCodePho = new phonetique();
	  	$codePho = $objetCodePho->calculer($Nom);
  		$codePho = addslashes($codePho);
	    $sql = 'insert into '.nom_table('noms_famille').'(nomFamille,codePhonetique) '.
	    		'values("'.$Nom.'","'.$codePho.'")';
	    $res = maj_sql($sql);
	    $idNom = $connexion->lastInsertId();
	  }
  }
  return $idNom;
}


// Affiche ou non un bouton de suppression en fonction des utilisations
// dans les évènements et les images
// A revoir ?
function bouton_suppression($type_objet,$reference,$lib) {
	$utilise = 0;
	$sql = 'select 1 from '.nom_table('concerne_objet').
		 ' where Reference_Objet = '.$reference.
		 ' and Type_Objet = \''.$type_objet.'\''.
		 ' limit 1';
	$res = lect_sql($sql);
	$utilise += ($res->rowCount());
	if ($utilise == 0) {
		$sql = 'select 1 from '.nom_table('images').
			 ' where Reference = '.$reference.
			 ' and Type_Ref = \''.$type_objet.'\''.
			 ' limit 1';
			   $res = lect_sql($sql);
		$utilise += ($res->rowCount());
	}
	if ($utilise != 0) $res->closeCursor();
	if (($reference != -1) and (! $utilise))
		echo '<input type="submit" name="supprimer" value="Supprimer" onclick="confirmer(\''.$lib.'\',document.forms.saisie.supprimer)"/>'."\n";
}

function utils_evt_images($type_objet,$reference) {
	$utilise = 0;
	if ($reference != -1) {
		$sql = 'select 1 from '.nom_table('concerne_objet').
			 ' where Reference_Objet = '.$reference.
			 ' and Type_Objet = \''.$type_objet.'\''.
			 ' limit 1';
		$res = lect_sql($sql);
		$utilise += ($res->rowCount());
		if ($utilise == 0) {
			$sql = 'select 1 from '.nom_table('images').
				 ' where Reference = '.$reference.
				 ' and Type_Ref = \''.$type_objet.'\''.
				 ' limit 1';
				   $res = lect_sql($sql);
			$utilise += ($res->rowCount());
		}
		if ($utilise != 0) $res->closeCursor();
	}
	if ($utilise) return true;
	else return false;
}

// Insère un enregistrement dans la table commentaires
// $force_slash : il faut forcer la fonction addslashes lorsque l'on n'est pas dans le cas de données lues par POST ou GET (e.g. sur l'import Gedcom)
function insere_commentaire($Refer,$Type_Ref,$DiversP,$Diff_Internet_NoteP,$force_slash='n') {
	global $req_comment;
	if ($force_slash) $DiversP = addslashes($DiversP);
	$req_comment = 'insert into '.nom_table('commentaires').
		'(Reference_Objet,Type_Objet,Note,Diff_Internet_Note) values'.
		'('.$Refer.',\''.$Type_Ref.'\',\''.$DiversP.'\',\''.$Diff_Internet_NoteP.'\')';
}

// Supprime les commentaires pour un objet donné
function req_sup_commentaire($Refer,$Type_Ref) {
	$req_comment = 'delete from '.nom_table('commentaires').
	' where Reference_Objet = '.$Refer.' and Type_Objet = \''.$Type_Ref.'\'';
	return $req_comment;
}

// Fonction de mise à jour des commentaires
// $Refer                                   : référence de l'objet pointé ==> un commentaire max par objet...
// $Type_Ref                                : type d'objet pointé : personne, image...
// $Note,$ANote                             : note saisie et ancienne valeur
// $Diff_Internet_Note,$ADiff_Internet_Note : critère de diffusion internet saisi et ancienne valeur
function maj_commentaire($Refer,$Type_Ref,$Note,$ANote,$Diff_Internet_Note,$ADiff_Internet_Note) {
	global $req_comment;
	if ((strpos($Note,'script') !== false) and (strpos($Note,'/script') !== false))
		$Note = '-';
	// Valeur par défaut de la diffusion non cochée
	if ($Diff_Internet_Note == '') $Diff_Internet_Note = 'N';
	if ($ADiff_Internet_Note == '') $ADiff_Internet_Note = 'N';
	if (($Note != $ANote) or ($Diff_Internet_Note != $ADiff_Internet_Note)) {
	  if ($Note != '') $Pres_Divers = 1;
	  else 			   $Pres_Divers = 0;
	  if ($ANote != '') $APres_Divers = 1;
	  else 				$APres_Divers = 0;
	  // 3 cas de figure pour la table des commentaires :
	  // Absence antérieure : on crée l'enregistrement
	  if (!$APres_Divers) {
		insere_commentaire($Refer,$Type_Ref,$Note,$Diff_Internet_Note);
	  }
	  // Absence actuelle : on supprime l'enregistrement
	  if (!$Pres_Divers) {
	  	$req_comment = req_sup_commentaire($Refer,$Type_Ref);
	  }
	  // Présence antérieure et actuelle : on modifie l'enregistrement
	  if (($Pres_Divers) and ($APres_Divers)) {
	  	Aj_Zone_Req('Note',$Note,$ANote,'A',$req_comment);
	  	Aj_Zone_Req('Diff_Internet_Note',$Diff_Internet_Note,$ADiff_Internet_Note,'A',$req_comment);
		$req_comment = 'update '.nom_table('commentaires').' set '.$req_comment.
			' where Reference_Objet = '.$Refer.
			' and Type_Objet = \''.$Type_Ref.'\'';
	  }
	}
}

// Affiche le libellé de l'évènement dans un lien objet-évènement
// $refEvt : référence de l'évènement (-1 en création)
// $type_objet : type de l'objet lié : PFU ==> Personne, Filiation, Union
// $ref_Objet : référence de l'objet lié
function libelle_lien_evt($refEvt,$type_objet,$ref_Objet) {
	$lib_evt = '';
	// Si l'évènement est connu, on ne pourra pas le modifier
	if ($refEvt != -1) {
		$requete = 'SELECT Titre, Debut, Fin FROM ' . nom_table('evenements') . ' e, ' . nom_table('types_evenement') .' t'.
					' WHERE reference = '.$refEvt.' limit 1';
		if ($result = lect_sql($requete)) {
			$enreg = $result->fetch(PDO::FETCH_ASSOC);
			$titre = my_html($enreg['Titre']);
			$deb = $enreg['Debut'];
			$fin = $enreg['Fin'];
			$result->closeCursor();
			if ($deb or $fin) $per = ' ('.Etend_2_dates($deb , $fin).')';
			else              $per = '';
			// Libellé pour l'évènement
			$lib_evt = $titre.$per;
			// Libellé spécifique pour évènement connu et un objet connu
			if ($ref_Objet == -1) {
				echo 'Lier une '.lib_pfu ($type_objet). ' avec l\'&eacute;v&egrave;nement "' . $lib_evt . '"<br />'."\n";
			}
		}
	}
	return $lib_evt;
}

// Affiche la liste des évènements dans un select
// Paramètres : $nom_select : le nom du select
//              $req : contenu de la requête à passer
function select_liste_evenements($nom_select,$req) {
	global $style_z_oblig;
    $result = lect_sql($req);
    if ($result->rowCount() > 0) {
		echo '<select name="' . $nom_select . '"'.$style_z_oblig.'>'."\n";
		$anc_type = '';
		$max = 0;
		while ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
			$nouv_type = $enreg['Code_Type'];
			// En rupture sur le type écriture d'un OptGroup
			if ($nouv_type != $anc_type) {
				echo '<optgroup label="'.$nouv_type.'">';
				$anc_type = $nouv_type;
			}
			// Ecriture de l'évènement
			$ref_ev = $enreg['Reference'];
			if ($ref_ev > $max) $max = $ref_ev;
			echo '<option value="' . $ref_ev . '">'.
				my_html($enreg['Titre']).'&nbsp;'.
				Etend_2_dates($enreg['Debut'] , $enreg['Fin']).
				'</option>'."\n";
		}
		echo '</select>'."\n";
    }
    else echo 'Pas d\'évènement disponible à la sélection';
}

function Ins_Zone_Req_Rub($Rub,$TypRub,$Nom_Rub) {
	global $rubs, $cont;
	if ($Rub != '') {
		if ($rubs != '') $rubs .= ',';
		$rubs .= $Nom_Rub;
		if ($cont != '') $cont .= ',';
		if ($TypRub == 'A') $cont = $cont .'"'.addslashes($Rub).'"';
		else                $cont = $cont .$Rub;
	}
}

// Récupération de la liste des types d'évènement
function Recup_Types_Evt($Cible) {
	global 	$id_types,$libelles_types;

	$PCond = '';
	if ($Cible == 'P') $PCond = 'and Unicite = "M" ';

	$id_types       = [];
	$libelles_types = [];

	$requete  = 'SELECT Code_Type, Libelle_Type FROM ' . nom_table('types_evenement').
				' WHERE Objet_Cible = "'.$Cible.'"'.$PCond.
				' ORDER BY Libelle_Type';

	if ($res = lect_sql($requete)) {
		while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
			$id_types[]       = $enreg[0];
			$libelles_types[] = $enreg[1];
		}
	}
}

// Ajout rapide d'évènements
function Aff_Ajout_Rapide_Evt($Cible) {
	global $chemin_images_icones, $Icones
		, $LG_Add_Event_Mult_Quick, $LG_Add_Event_Quick, $LG_Add_Event, $LG_Del_Event;
	echo '<br /><br />'."\n";
	if ($Cible == 'P') echo my_html($LG_Add_Event_Mult_Quick).' : '."\n";
	else echo my_html($LG_Add_Event_Quick).' : '."\n";
	echo '<img src="'.$chemin_images_icones.$Icones['ajout'].'" id = "ajout" alt="'.$LG_Add_Event.'" title="'.$LG_Add_Event.'" onclick=\'addRowToTable();\'/>'."\n";
	echo '<img src="'.$chemin_images_icones.$Icones['supp'].'" id="delete" alt="'.$LG_Del_Event.'" title="'.$LG_Del_Event.'" onclick=\'removeRowFromTable();\'/> '."\n";
	echo '<table border="0" id="tblSample" width="80%">'."\n";
	echo '<tr><td colspan="2"></td></tr>'."\n";
	echo '<tr><td>Type</td><td>Titre</td></tr>'."\n";
	echo '</table>'."\n";
}

// Mise à jour de la date de dernière mise à jour du site
function maj_date_site($trt_vide=false) {
	$req = 'update '.nom_table('general').' set Date_Modification = current_timestamp';
	if ($trt_vide) $req .= ', Base_Vide = false';
    $res = maj_sql($req);
}

// Retourne l'identifiant à attribué : max + 1
function Nouvel_Identifiant($Cle,$Table) {
	$identifiant = 0;
	$sql = 'select max('.$Cle.') from '.nom_table($Table);
	$resmax = lect_sql($sql);
	$enrmax = $resmax->fetch(PDO::FETCH_NUM);
	$identifiant = $enrmax[0] + 1;
	$resmax->closeCursor();
	return $identifiant;
}

// Affiche la liste des noms disponibles dans un select
function Select_Noms($id_nom,$NomSel,$NomCache,$PrDe='OO') {
	global $resSN;
	echo '<select name="'.$NomSel.'" id="'.$NomSel.'" class="oblig" onchange="document.forms.saisie.'.$NomCache.'.value=this.value;">'."\n";
	// En premier appel, lancement de la requête, sinon, repositionnement
	//if ($PrDe[0] == 'O') {
		$sql = 'select idNomFam, nomFamille from '.nom_table('noms_famille').' order by nomFamille';
		$resSN = lect_sql($sql);
	//}
	//else $resSN->data_seek(0);
	echo '<option value="-1/" >-- Nom --</option>'."\n";
	while ($row = $resSN->fetch(PDO::FETCH_NUM)) {
		echo '<option value="'.$row[0].'/'.$row[1].'"';
		if ($id_nom == $row[0]) echo ' selected="selected" ';
		echo '>'.$row[1]."</option>\n";
	}
	echo "</select>\n";
	//if ($PrDe[1] == 'O') $resSN->closeCursor();
}

// Recherche d'un nom dans la table des noms
function recherche_nom($nom) {
	$identifiant = 0;
	$sql = 'select idNomFam from '.nom_table('noms_famille').' where nomFamille  = \''.addslashes($nom).'\' limit 1';
	if ($res = lect_sql($sql)) {
		if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
			$identifiant = $enreg[0];
		}
	}
	$res->closeCursor();
	return $identifiant;
}

// Contrôle d'un fichier téléchargé
// taille, chargement "standard"
function ctrl_fichier_ko($nb_fic=-1) {
	global $SiteGratuit, $Premium, $debug;
	$erreur = false;
	$message = '';
	$taille_maxi = ini_get('upload_max_filesize');
	// Taille maxi en octets
	$val = trim($taille_maxi);
	if ($debug) echo 'val-1 : '.$val.'<br />';
	$lg = strlen($val)-1;
	$last = strtolower($val[$lg]);
	$val = substr($val, 0, $lg);
	switch($last) {
	    case 'g': $val *= 1024; if ($debug) echo 'val-g : '.$val.'<br />';
	    case 'm': $val *= 1024; if ($debug) echo 'val-m : '.$val.'<br />';
	    case 'k': $val *= 1024; if ($debug) echo 'val-k : '.$val.'<br />';;
	}
	$taille_maxi_o = $val;

	// Contrôle de la taille du fichier par rapport à upload_max_filesize et post_max_size
	if ($nb_fic == -1) {
		$err_fic = $_FILES['nom_du_fichier']['error'];
		$size_fic = $_FILES['nom_du_fichier']['size'];
		$nom_fic = $_FILES['nom_du_fichier']['name'];
		$tmp_fic = $_FILES['nom_du_fichier']['tmp_name'];
	}
	else {
		$err_fic = $_FILES['nom_du_fichier']['error'][$nb_fic];
		$size_fic = $_FILES['nom_du_fichier']['size'][$nb_fic];
		$nom_fic = $_FILES['nom_du_fichier']['name'][$nb_fic];
		$tmp_fic = $_FILES['nom_du_fichier']['tmp_name'][$nb_fic];
	}
	if ($err_fic) {
		switch ($err_fic) {
	           case 1: // UPLOAD_ERR_INI_SIZE
	           			$message = 'Le fichier excède le poids autorisé ('.$taille_maxi.')'; break;
	           case 2: // UPLOAD_ERR_FORM_SIZE
	           			$message = 'Le fichier excède le poids autorisé par le champ MAX_FILE_SIZE s\'il a été donné'; break;
	           case 3: // UPLOAD_ERR_PARTIAL
	           			$message = 'Le fichier n\'a été que partiellement téléchargé'; break;
	           case 4: // UPLOAD_ERR_NO_FILE
	           			$message = 'Aucun fichier n\'a été téléchargé'; break;
		}
		$erreur = true;
		aff_erreur($message);
	}

	// Dans le cas d'un site gratuit non Premium, on contrôle que le fichier ne fait pas plus de la moitié du max autorisé
	if (!$erreur) {
		if ($SiteGratuit and (!$Premium)) {
			$taille_maxi_o /= 2;
			if ($size_fic > $taille_maxi_o) {
				$erreur = true;
				aff_erreur('Le fichier excède le poids autorisé ('.$taille_maxi_o.' o ; vous pouvez augmenter cette limite en prenant l\'option Premium)');
			}
		}
	}

	// Contrôle du nom du fichier (faille null caractère notamment)
	if (!$erreur) {
		if( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $nom_fic) ) {
			$erreur = true;
	    	aff_erreur('Nom de fichier invalide');
		}
	}

	// Si pas d'erreur, on vérifie que le fichier a été uploadé, sinon présomption d'attaque
	if (!$erreur) {
	    if( !is_uploaded_file($tmp_fic) ) {
	        aff_erreur('Fichier chargé irrégulièrement.§§§!!');
	        $erreur = true;
	    }
	}

	return $erreur;
}

// Récupération de l'extension
function get_extension($nom_fic) {
	$extension = '';
	if ($nom_fic != '') $extension = substr(strrchr($nom_fic, '.'), 1);
	return $extension;
}

// Nettoye le nom de fichier
function nettoye_nom_fic($nom_fic) {
	$nom_fic = strtr($nom_fic,
			'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
			'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
	// Supprimer les caracteres autres que lettres, chiffres et point
	$nom_fic = preg_replace('/([^._a-z0-9]+)/i', '', $nom_fic);
	return $nom_fic;
}

// Récupération du type de fichier
function get_file_type($nom_fic,$typeLu) {
	$extension = strtoupper(get_extension($nom_fic));
	$le_type = '';
	switch ($extension) {
		case 'HTM' :
		case 'HTML' : $le_type = 'HTM'; break;
		case 'BMP' :
		case 'GIF' :
		case 'JPG' :
		case 'JPEG' :
		case 'PNG' : $le_type = 'IMG'; break;
		case 'PDF' : $le_type = 'PDF'; break;
		case 'DOC' :
		case 'DOCX' :
		case 'ODT' :
		case 'RTF' :
		case 'TXT' : $le_type = 'TXT'; break;
	}
	// Contrôle complémentaire sur les images, on écrase le type si le fichier lu ne correspond pas à une image
	if (($le_type == 'IMG') and ($typeLu != '')) {
		if (strpos($typeLu,'image/') !== 0) $le_type = '';
	}
	// Contrôle complémentaire sur les fichiers HTML, on écrase le type si le fichier lu ne correspond pas à une page
	if (($le_type == 'HTM') and ($typeLu != '')) {
		if ($typeLu != "text/html")  $le_type = '';
	}

	/*
	Open Office : odt : application/vnd.oasis.opendocument.text
	Word : docx : application/msword, rtf : application/msword
	txt : text/plain
	*/
	return $le_type;
}

// Récupère le contenu d'une variable postée
// Utilisé pour les formulaires dynamiques notamment
function retourne_var_post($nom_var,$numero) {
	$retour = '';
	$concat_nom = $nom_var.$numero;
	if (isset($_POST[$concat_nom])) {
		$retour = strip_tags(trim($_POST[$concat_nom]));
		$retour = addslashes($retour);
	}
	return $retour;
}

// Ajoute des slash au besoin
function ajoute_sl($cont) {
	$cont = addslashes($cont);
	return $cont;
}
function ajoute_sl_rt($cont) {
	$cont = addslashes($cont);
	return $cont;
}

function envoi_mail($mail,$sujet,$message_txt,$message_html,$aff=true) {
	global $FromTo_Mail, $FromTo_Texte;

	if ($message_html == '') $message_html = '<html><head></head><body>'.$message_txt.'</body></html>';

	// On filtre les serveurs qui rencontrent des bogues.
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) {
		$passage_ligne = "\r\n";
	}
	else {
		$passage_ligne = "\n";
	}

	//=====Création de la boundary
	$boundary = "-----=".md5(rand());

	//=====Définition du sujet.
	$sujet = '=?iso8859-1?B?'.base64_encode($sujet).'?=';

	//=====Création du header de l'e-mail.
	$header = 'From: "'.$FromTo_Texte.'" <'.$FromTo_Mail.'>'.$passage_ligne;
	$header.= 'Reply-to: "'.$FromTo_Texte.'" <'.$FromTo_Mail.'>'.$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;

	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format texte.
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format HTML
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;

	//echo $header.'<br />';

	//=====Envoi de l'e-mail.
	//if(mail($destinataire,$objet,utf8_decode(utf8_encode($message)),$entete))
	if (mail($mail,$sujet,$message,$header)) {
		if ($aff) echo'Envoi du mail à '.$mail.'<br />';
	}
	else
		echo 'Echec de l\'envoi à '.$mail.'<br />';
}

//--------------------------------------------------------------------------
// Retourne le nom, le prénom et les dates d'une personne
// code retour true : trouvé, false : sinon
//--------------------------------------------------------------------------
function Get_Nom_Prenoms_Dates($Pers,&$Nom,&$Prenoms,&$D_Naissance,&$D_Deces) {
	global $db,$Diff_Internet_P, $def_enc, $P_Sexe;
	$Nom = '';
	$Prenoms = '';
	$D_Naissance = '';
	$D_Deces = '';
	$P_Sexe = '';
	$sql = 'select Nom, Prenoms, Ne_le, Decede_Le, Sexe from '.nom_table('personnes').' where Reference  = '.$Pers.' limit 1';
	if ($res = lect_sql($sql)) {
		if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
		  $Nom         = my_html($enreg[0]);
		  $Prenoms     = my_html($enreg[1]);
		  $D_Naissance = $enreg[2];
		  $D_Deces     = $enreg[3];
		  $P_Sexe      = $enreg[4];
		}
	}
	$res->closeCursor();
	if (($Nom != '') or ($Prenoms != '')) return true;
	else return false;
}

// Affiche la liste des personnes dans un select
// Paramètres : $nom_select : nom du select
//              $premier : première fois que l'on appelle le select dans la page
//              $dernier : dernière fois que l'on appelle le select dans la page
//              $cle_sel : clé à sélectionner
//              $crit : critere de sélection
//              $order : critère de tri
//              $oblig : zone obligatoire ?
//              $oc : action complémentaire sur select exemple onchange="..."
//              $pivot_inf, $pivot_sup : dates pivot pour le rejet
//              $type_ctrl : C : conjoint, F : filiation
function aff_liste_pers_restreinte($nom_select,$premier,$dernier,$cle_sel,$crit,$order,$oblig, $oc, $pivot_inf, $pivot_sup, $type_ctrl) {
	global $db,$res,$_SESSION, $def_enc;
	// Pivot inférieur - 1 an
	if ($pivot_inf <> '') {
		$deb_pivot = substr($pivot_inf,0,4);
		$fin_pivot = substr($pivot_inf,4);
		$tmp_annee = str_pad(intval($deb_pivot) - 1,4,'0', STR_PAD_LEFT);
		$pivot_inf = $tmp_annee . $fin_pivot;
	}
	// Pivot supérieur - 1 an
	if ($pivot_sup <> '') {
		$deb_pivot = substr($pivot_sup,0,4);
		$fin_pivot = substr($pivot_sup,4);
		$tmp_annee = str_pad(intval($deb_pivot) + 1,4,'0', STR_PAD_LEFT);
		$pivot_sup = $tmp_annee . $fin_pivot;
	}
	//echo '$pivot_inf : '.$pivot_inf.'</br>';
	//echo '$pivot_sup : '.$pivot_sup.'</br>';
	if (!$oblig) $style_z_oblig = '';
	echo '<select name="'.$nom_select.'" class="oblig" '.$oc.'>'."\n";
	if ($premier) {
		$sql = 'select Reference, Nom, Prenoms, Ne_Le, Decede_Le from '.nom_table('personnes');
		// clause where
		$crit_sel = '';
		if ($crit != '') $crit_sel = $crit;
		if (!$_SESSION['estPrivilegie']) {
			if ($crit_sel != '') $crit_sel .= ' and ';
			$crit_sel .= ' Diff_Internet = \'O\' ';
		}
		// clause where
		if ($crit_sel != '') $sql .= ' where '.$crit_sel;
		// clause order by
		if ($order != '') $sql .= ' order by '.$order;
		$res = lect_sql($sql);
	}
	else {
		$res->data_seek(0);
	}
	$nb_ret = 0;
	while ($row = $res->fetch(PDO::FETCH_NUM)) {
		$ne = $row[3];
		$decede = $row[4];
		$ne_sel = $ne;
		$dec_sel = $decede;
		ne_dec_approx($ne_sel,$dec_sel);
		//echo 'ne / decede : '.$ne.' / '.$decede.'<br />';
		//echo 'ne_sel / decede_sel : '.$ne_sel.' / '.$dec_sel.'<br />';
		$retenu = true;
		// Décédé 1 an avant le pivot ==> rejet
		if (($pivot_inf <> '') and ($retenu)) {
			if (($dec_sel <> '') and ($dec_sel < $pivot_inf)) $retenu = false;
		}
		// Né 1 an après le pivot ==> rejet
		if (($pivot_sup <> '') and ($retenu)) {
			if (($ne_sel <> '') and ($ne_sel > $pivot_sup)) $retenu = false;
		}
		// Contrôle complémentaire pour la filiation
		// La naissance du parent doit être inférieure à celle de l'enfant
		if (($retenu) and ($type_ctrl == 'F')) {
			if (($ne_sel <> '') and ($pivot_inf <> '') and ($ne_sel > $pivot_inf)) $retenu = false;
		}

		//$retenu = true;
		if ($retenu) {
			$nb_ret ++;
			echo '<option value="'.$row[0].'"';
			if ($cle_sel == $row[0]) echo ' selected="selected" ';
			echo '>'.my_html($row[1].' '.$row[2]).aff_annees_pers($ne,$decede).'</option>'."\n";
		}
	}
	echo '</select>&nbsp;';
	echo '<!--nb_ret : '.$nb_ret.'-->';
	if ($dernier) $res->closeCursor();
}

function enleve_chemin($nom_fic) {
	if ($nom_fic != '') {
		$posi = strrpos($nom_fic, '/');
		return substr($nom_fic, $posi+1);
	}
	else return '-';
}

// Création des noms et liaison avec les personnes
function Creation_Noms_Commun() {
	global $Cre_Noms, $idNom, $Init,
            $n_personnes,$n_noms,$n_liens_noms, $nb_cre_noms;

    //    Appel du fichier contenant la classe
	include 'phonetique.php';
	//    Initialisation d'un objet de la classe
	$codePho = new phonetique();

	$Anom ='';
	$n_noms = nom_table('noms_famille');
	$n_liens_noms = nom_table('noms_personnes');
	$idNom = Nouvel_Identifiant('idNomFam','noms_famille')-1;
	$nb_cre_noms = 0;

	$req =[];
	$msg = '';
	$deb_ins_noms = 'insert into '.$n_noms.' values(';
	$deb_upd_pers = 'update '.$n_personnes.' set idNomFam=';
	$deb_ins_lien = 'insert into '.$n_liens_noms.' values(';
	$sql = 'SELECT UPPER(Nom), Reference FROM '.$n_personnes.' where idNomFam is null order by UPPER(Nom)';
  	if ($res = lect_sql($sql)) {
    	while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
    		$nom = $enreg[0];
    		$refPers = $enreg[1];
    		// Traitements en rupture sur le nom
    		if ($nom != $Anom) {
    			$Anom = $nom;
    			// Le nom existe-t-il en base ?
    			$existe_nom = false;
    			if (! $Init) {
    				$ident_nom = recherche_nom($nom);
    				if ($ident_nom) {
    					$existe_nom = true;
    				}
    			}
    			// Création du nom dans la table
    			if (! $existe_nom) {
					//    Calcul d'un code phonétique
					$code = $codePho->calculer($nom);
					$idNom ++;
					$ident_nom = $idNom;
					// Création de l'enregistrement dans la table des noms de famille
					$req[] = $deb_ins_noms.$ident_nom.',\''.addslashes($nom).'\',\''.$code.'\')';
					$nb_cre_noms++;
    			}
    		}
    		// Modification de la table des personnes
			$req[] = $deb_upd_pers.$ident_nom.' where Reference='.$refPers;
			// Création de l'enregistrement dans la table des liens personnes / noms
			$req[] = $deb_ins_lien.$refPers.','.$ident_nom.',\'O\',null)';
    	}
    	$res->closeCursor();
    	$c_req = count($req);
		for ($nb = 0; $nb < $c_req; $nb++) {
			$res = lect_sql($req[$nb]);
		}
		if ($msg != '') echo $msg.'<br />';
		$req ='';
  	}
  	$Cre_Noms = true;
}

// Transformation de la date présente dans le fichier : format accepté : jj/mm/ssaa, jj-mm-ssaa, jj.mm.ssaa
function traite_date_csv($la_date) {
	global $arr, $MoisAnAbr, $ListeAnneesRev, $MoisRevAbr4, $nb_enr, $debug;

	$ret = '';
	$err_format_date = 'La date n\'a pas le bon format :&nbsp;';
	$greg = false;

	if ($la_date != '') {
		
		$date_ok = false;
		
		$la_date = trim($la_date);
		$c_date = explode('/',$la_date);
		if (count($c_date) == 3) $date_ok = true;
		
		if (!$date_ok) {
			$c_date = explode('-',$la_date);
			if (count($c_date) == 3) $date_ok = true;
		}
		
		if (!$date_ok) {
			$c_date = explode('.',$la_date);
			if (count($c_date) == 3) $date_ok = true;
		}
		
		if ($date_ok) {
			$jour  = zerofill2($c_date[0]);
			$mois  = zerofill2($c_date[1]);
			$annee = $c_date[2];
		}
		
		// Pour le moment on ne traite que les dates numériques ==> pas de date révolutionnaire
		//$MoisRevAbr4 = 'VENDBRUMFRIMNIVOPLUVVENTGERMFLORPRAIMESSTHERFRUCSANC';
		if ($date_ok) {
			if ((!is_numeric($jour)) or 
				(!is_numeric($mois)) or 
				(!is_numeric($annee)))
				$date_ok = false;
			else $greg = true;
		}
		if ($debug) {
			if (!is_numeric($jour)) echo 'jour non num'.'<br />';
			if (!is_numeric($mois)) echo 'mois non num'.'<br />';
			if (!is_numeric($annee)) echo 'année non num>'.$annee.'<<br />';
		}
		// Contrôle de la validité d'une date grégorienne
		if (($date_ok) and ($greg)) {
			if(!checkdate($mois,$jour,$annee)) $date_ok = false;
		}
		
		if ($date_ok) {
			$ret = $annee.$mois.$jour.'GL';
		}
		
		if (!$date_ok) {
			echo $err_format_date.$la_date.' sur la ligne '.$nb_enr.'<br />';
		}
	}
	return $ret;
}

function aff_corr_csv($nb) {
	global $radical_variable_csv;
	echo '<td><select name="'.$radical_variable_csv.$nb.'" id="'.$radical_variable_csv.$nb.'">'."\n";
	echo '<option value="-1">'.my_html('Sélectionnez une colonne').'</option>'."\n";
	for ($nb2=ord('A'); $nb2<=ord('Z'); $nb2++) echo '<option>'.chr($nb2).'</option>';
	echo '</select></td>'."\n";
}

function aff_corr_csv2($nb) {
	global $radical_variable_csv;
	echo '<td><select name="'.$radical_variable_csv.$nb.'" id="'.$radical_variable_csv.$nb.'">'."\n";
	echo '<option value="-1">'.my_html('Sélectionnez une colonne').'</option>'."\n";
	$num_col = 0;
	for ($nb2=ord('A'); $nb2<=ord('Z'); $nb2++) echo '<option value="'.$num_col++.'">'.chr($nb2).'</option>';
	echo '</select></td>'."\n";
}

/* Affiche une zone date avec son calendrier
	$prev_field : champ de mémorisation du contenu initial
	$disp_field : champ affiché mais non saisissable
	$hidden_field : champ caché
	$image_name : nom de l'image
	$fonction : fonction d'appel du calendrier */
function zone_date2($prev_field, $disp_field, $hidden_field, $value='') {
	global $hidden;
	$fonction = "Calendrier2('".$hidden_field."', '".$disp_field."')";
	$et_value = '';
	if ($value != '') {
		$et_value = 'value="'.Etend_date($value).'" ';
		$value = 'value="'.$value.'" ';
	}
	echo '<input type="'.$hidden.'" name="'.$prev_field.'" '.$value.'/>';
	echo '<input type="text" readonly="readonly" size="25" name="'.$disp_field.'" onclick="'.$fonction.'"'.$et_value.'/>';
	Affiche_Calendrier('img_'.$disp_field, $fonction);
	echo '<input type="'.$hidden.'" name="'.$hidden_field.'" id="'.$hidden_field.'"'.$value.'/>';
}

?>