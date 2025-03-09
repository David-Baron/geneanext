<?php

//=====================================================================
// Import ou lecture d'un fichier csv avec des évènements
// (c) JLS
//=====================================================================

session_start();

include('fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok','annuler','nom_du_fichier','Horigine',
                       'nom_du_fichier','val_statut','entete','idNiveauF','idZoneF','type_evt');
foreach ($tab_variables as $nom_variables) {
	if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
	else $$nom_variables = '';
}

// Sécurisation des variables postées - phase 1
$ok       = Secur_Variable_Post($ok,strlen($lib_Okay),'S');
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Annuler),'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');

include('fonctions_maj.php');
$acces = 'L';
$titre = $LG_Menu_Title['Imp_CSV_Events'];
$x = Lit_Env();
$niv_requis = 'G';
include('Gestion_Pages.php');

// Page interdite sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// $champ_table  : champ dans la table à charger
// $champ_lib    : libellé du champ
// $champ_classe : clase du champ : (C)aractère, (N)umérique, (D)ate
$champ_table[]  = 'Titre';
$champ_lib[]    = $LG_ICSV_Event_Title;
$champ_classe[] = 'C';
$champ_table[]  = 'Debut';
$champ_lib[]    = $LG_ICSV_Event_Beg;
$champ_classe[] = 'D';
$champ_table[]  = 'Fin';
$champ_lib[]    = $LG_ICSV_Event_End;
$champ_classe[] = 'D';

$n_events = nom_table('evenements');
$n_types_evenement = nom_table('types_evenement');

// Champs du formulaire
$radical_variable_champ   = 'var_champ_';
$radical_variable_csv     = 'var_csv_';

if ($bt_OK) Ecrit_Entete_Page($titre,'','');

$compl = Ajoute_Page_Info(600,300);
Insere_Haut($titre,$compl,'Import_CSV_Evenements','');

include('Commun_Import_CSV.php');

//Demande de chargement
if ($ok=='OK') {
	// Sécurisation des variables postées - phase 2
	$nom_du_fichier = Secur_Variable_Post($nom_du_fichier,100,'S');
	$val_statut     = Secur_Variable_Post($val_statut,1,'S');
	$entete         = Secur_Variable_Post($entete,1,'S');
	$idNiveauF      = Secur_Variable_Post($idNiveauF,1,'N');
	$idZoneF        = Secur_Variable_Post($idZoneF,1,'N');
	$type_evt       = Secur_Variable_Post($type_evt,4,'S');
	if ($idZoneF == -1) $idZoneF = 0;
		
	// Pas de limite de temps en local
	// Sur le net, limite fixée à la valeur paramétrée ; plus importante sur les sites Premium
	if ($Environnement == 'L') {
		set_time_limit(0);
	}
	if ($SiteGratuit) {
		set_time_limit($lim_temps);
	}

	echo my_html($LG_Requested_File).' : '.$_FILES['nom_du_fichier']['name'].'<br />';

	$status = '';
	switch ($val_statut) {
		case 'O' : $status = LG_CHECKED_RECORD_SHORT; break;
		case 'N' : $status = LG_NOCHECKED_RECORD_SHORT; break;
		case 'I' : $status = LG_FROM_INTERNET; break;
	}
	echo my_html($LG_Default_Status.' : '.$status).'<br />';

	//Restitution du type d'évènement
	$requete  = 'select Libelle_Type from ' . $n_types_evenement . " where Code_Type = '".$type_evt. "' limit 1";
	$result = lect_sql($requete);
	$enreg = $result->fetch(PDO::FETCH_NUM);
	echo my_html($LG_ICSV_Event_Type.' : '.$enreg[0]).'<br />';	
	
	//Restitution du lieu
	echo my_html($LG_ICSV_Event_Where).' : ';
	if ($idNiveauF != 0) {
		echo LectZone($idZoneF,$idNiveauF).'<br />';
	}
	else {
		echo my_html($LG_ICSV_Event_Where_No).'<br />';
	}
		
	$erreur = false;

    $tmp_file = $_FILES['nom_du_fichier']['tmp_name'];
    $nom_du_fichier = $_FILES['nom_du_fichier']['name'];

    // Une demande de chargement a été faite
	if ($nom_du_fichier != '') {
		
		$erreur = ctrl_fichier_ko();

		if (!$erreur) {
			// Seuls sont autorisés les fichiers csv
			if (Extension_Fic($nom_du_fichier) != 'csv') {
				aff_erreur(LG_IMP_CSV_ERR_TYPE);
				$erreur = true;
			}
		}

		// On peut télécharger s'il n'y a pas d'erreur
		if (!$erreur) {

			$path = $chemin_exports.$nom_du_fichier;
			move_uploaded_file($tmp_file, $path);
			
			// Traitement du fichier
			// ini_set('auto_detect_line_endings',TRUE);
			$mode = 'r';
			
			if ($fp=fopen($path,$mode)) {

				$nb_enr_crees = 0;

				$nb_enr = 0;
				$modif = false;
				
				$num_ident = '';
				$deb_req = 'insert into '.$n_events.' (';
				$deb_req_suite = ',Date_Creation,Date_Modification,Statut_Fiche,Identifiant_zone,Identifiant_Niveau,Code_Type';
				$fin_req = ',current_timestamp,'.	// Date_Creation
							'current_timestamp,'.	// Date_Modification
							'"'.$val_statut.'",'.	// Statut_Fiche
							'"'.$idZoneF.'",'.		// Identifiant_zone
							'"'.$idNiveauF.'",'.		// Identifiant_Niveau
							'"'.$type_evt.'"'.		// Code_Type
							')';

				//(Titre,Debut,FinTitre,Debut,Fin,Date_Creation,Date_Modification,Statut_Fiche,Identifiant_zone,Identifiant_Niveau,Code_Type) 
				//("Evt 1",19400101GL,19400211GL,current_timestamp,current_timestamp,"O","71","1","ADOP")
							
				// include('Commun_Import_CSV.php');
				insert_champs();
				
				fclose($fp);
				
				if ($modif) {
					maj_date_site();
					// $plu = pluriel($nb_enr_crees);
					echo $nb_enr_crees.' '.my_html(LG_IMP_CSV_EVTS_CREATED).'<br />';
				}
			}
			else {
				echo my_html(LG_IMP_CSV_ERR_OPEN_FILE).'<br />';
			}
		}
    }
}

if ($est_gestionnaire) {
	// Première entrée : affichage pour saisie
	if (($ok=='') && ($annuler=='')) {

		include('jscripts/Edition_Evenement.js');
		
		echo '<br />';
		$idZoneLu = -1;
		
		$larg_titre = '35';
		echo '<form id="saisie" method="post" enctype="multipart/form-data" action="'.my_self().'">'."\n";
		aff_origine();
		echo '<input type="'.$hidden.'" name="idZoneF" value="'.$idZoneLu.'"/>'."\n";

   		echo '<table width="90%" class="table_form">'."\n";
		colonne_titre_tab($LG_csv_file_upload);
		echo '<input type="file" name="nom_du_fichier" size="80"/></td>';
		echo '</tr>'."\n";

		form_status();
		
		ligne_vide_tab_form(1);
		colonne_titre_tab($LG_ICSV_Event_Where);
		// Niveau de la zone géographique associée
		$name_radio = 'idNiveauF';
		echo '<input type="radio" name="'.$name_radio.'" id="'.$name_radio.'_0" value="0" checked="checked" onclick="cache_image_zone()"/><label for="'.$name_radio.'_0">'
			.LG_EVENT_NOPLACE.'</label>&nbsp;'."\n";	
		$req = 'select * from '.nom_table('niveaux_zones');
		$result = lect_sql($req);
		while ($enr_zone = $result->fetch(PDO::FETCH_ASSOC)) {
			$id_niveau = $enr_zone['Identifiant_Niveau'];
			$id = $name_radio.'_'.$id_niveau;
			echo '<input type="radio" name="idNiveauF" id="'.$id.'" value="'.$id_niveau.'" onclick="bascule_image(\'img_zone\')"/><label for="'.$id.'">'.$enr_zone['Libelle_Niveau'].'</label>&nbsp;'."\n";
		}
		echo '<input type="text" readonly="readonly" name="zoneAff" value=""/>'."\n";
		echo '<img id="img_zone" style="display:none; visibility:hidden;" src="' . $chemin_images . $Icones['localisation'].'"  alt="'.$LG_Place_Select.'" title="'.$LG_Place_Select.'"'.
			 ' onclick="Appelle_Zone_Lect()"/>'."\n";
		echo "</td></tr>\n";
		colonne_titre_tab($LG_ICSV_Event_Type);
		// Select avec les types existants
		$req = 'select Code_Type, Libelle_Type from '.$n_types_evenement.' order by Libelle_Type';
		$result = lect_sql($req);
		if ($result->rowCount() > 0) {
			echo '<select name="type_evt">'."\n";
			echo '<option value="-">'.LG_IMP_CSV_LINKS_SEL_TYPE.'</option>'."\n";
			while ($enrT = $result->fetch(PDO::FETCH_NUM)) {
				echo '<option value="'.$enrT[0] .'">'.my_html($enrT[1]).'</option>'."\n";
			}
			echo '</select>'."\n";
		}
		echo '</td></tr>'."\n";
				
		form_header();

		form_match();

		ligne_vide_tab_form(1);
		bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');
		ligne_vide_tab_form(1);

		echo '</table>';
		echo '</form>';
    }
}
else echo my_html($LG_function_noavailable_profile);

Insere_Bas($compl);

?>
</body>
</html>