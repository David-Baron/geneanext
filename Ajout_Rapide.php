<?php

//=====================================================================
// Ajout rapide
// De frère / soeur si filiation définie ==> création de la personne et de sa filiation
// Des parents si la filiation n'est pas définie ==> création des personnes, de leur union et de la filiation
// D'un conjoint ==> création de la personne et de l'union
//=====================================================================

session_start();

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok','annuler',
			'pere','mere','sexe',
		// variables pour le colattéral
			'Nomcol','ANomcol','Prenomscol','Sexecol',
			'Ne_lecol','CNe_lecol','selNecol',
			'Baptise_lecol','CBaptise_lecol','selBaptisecol',
			'Decede_lecol','CDecede_lecol','selDecedecol',
		// variables pour le conjoint
			'Nomconj','ANomconj','Prenomsconj','Sexeconj',
			'Ne_leconj','CNe_leconj','selNeconj',
			'Baptise_leconj','CBaptise_leconj','selBaptiseconj',
			'Decede_leconj','CDecede_leconj','selDecedeconj',
			'Unis_leconj','CUnis_leconj','selUnisconj',
		// variables pour les parents
			// pour le père
			'Nompere','ANompere','Prenomspere','Sexepere',
			'Ne_lepere','CNe_lepere','selNepere',
			'Baptise_lepere','CBaptise_lepere','selBaptisepere',
			'Decede_lepere','CDecede_lepere','selDecedepere',
			// pour la mère
			'Nommere','ANommere','Prenomsmere','Sexemere',
			'Ne_lemere','CNe_lemere','selNemere',
			'Baptise_lemere','CBaptise_lemere','selBaptisemere',
			'Decede_lemere','CDecede_lemere','selDecedemere',
			// pour l'union
			'Unis_leparents','CUnis_leparents','selUnisparents',

			'Auto_Sosa','NSosa','Horigine'
			);

include('fonctions.php');

foreach ($tab_variables as $nom_variables) {
	if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
	else $$nom_variables = '';

	// Sécurisation des variables réceptionnées
	if (strpos($nom_variables,'Nom')        === 0) $$nom_variables = Secur_Variable_Post($$nom_variables,50,'S');
	if (strpos($nom_variables,'ANom')       === 0) $$nom_variables = Secur_Variable_Post($$nom_variables,50,'S');
	if (strpos($nom_variables,'Prenoms')    === 0) $$nom_variables = Secur_Variable_Post($$nom_variables,50,'S');
	if (strpos($nom_variables,'Ne_le')      === 0) $$nom_variables = Secur_Variable_Post($$nom_variables,10,'S');
	if (strpos($nom_variables,'Baptise_le') === 0) $$nom_variables = Secur_Variable_Post($$nom_variables,10,'S');
	if (strpos($nom_variables,'Decede_le')  === 0) $$nom_variables = Secur_Variable_Post($$nom_variables,10,'S');
	if (strpos($nom_variables,'Sexe')       === 0) $$nom_variables = Secur_Variable_Post($$nom_variables,1,'S');
	if (strpos($nom_variables,'Unis_le')    === 0) $$nom_variables = Secur_Variable_Post($$nom_variables,10,'S');
}

$ok        = Secur_Variable_Post($ok,strlen($lib_Okay),'S');
$annuler   = Secur_Variable_Post($annuler,strlen($lib_Annuler),'S');
$Horigine  = Secur_Variable_Post($Horigine,100,'S');
$Auto_Sosa = Secur_Variable_Post($Auto_Sosa,2,'S');
$NSosa     = Secur_Variable_Post($NSosa,20,'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Ajout rapide';               // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer','N');

$lg_date_a = '&nbsp;'.LG_AT.'&nbsp;';

// Pour éviter les nulls qui posent problème sur la fiche familiale
function Rattrape_null($Contenu, $Nom_Rub) {
	global 	$rubs, $cont;
	if ($Contenu == '') {
		$rubs .= ','.$Nom_Rub;
		$cont .= ",''";
	}
}

function Aff_Pers($suffixe,$oblig) {
	global $style_z_oblig, $enregP, $list_opt_villes, $idNomPers, $Nom, $Sexe, $chemin_images_icones, $Icones
		, $hidden, $largP, $lg_date_a
		;
	if (!$oblig) $style_z_oblig = '';
	if (($suffixe == 'pere') or ($suffixe == 'mere')) {
		if ($suffixe == 'pere') $val = 'm';
		else                    $val = 'f';
		echo '<input type="'.$hidden.'" name="Sexe'.$suffixe.'" value="'.$val.'"/>'."\n";
	}
	// En fonction du suffixe, accord sur le genre pour les libellés de colonnes
	switch ($suffixe) {
		case 'pere' : $accord = '';    break;
		case 'mere' : $accord = 'e';   break;
		default     : $accord = '(e)';
	}
	
	echo '<table border="0" width="100%">'."\n";
	col_titre_tab_noClass(LG_PERS_NAME, $largP);

	// Pour les colattéraux, le nom de la personne est proposé sélectionné
	$id_nom = 0;
	$laVal  = '';
	if ($suffixe == 'col') {
		$id_nom = $idNomPers;
		$laVal  = $id_nom.'/'.$Nom;
	}

	echo '<td><input type="'.$hidden.'" name="Nom'.$suffixe.'" id="Nom'.$suffixe.'" value="'.$laVal.'" '.$style_z_oblig.'/>';
	echo '<input type="'.$hidden.'" name="ANom'.$suffixe.'" id="ANom'.$suffixe.'" value="'.$laVal.'"/>'."\n";
	Select_Noms($id_nom,'NomSel'.$suffixe,'Nom'.$suffixe);
	if ($oblig) Img_Zone_Oblig('imgObligNom'.$suffixe);

	// Possibilité d'ajouter un nom
	$texte_im = 'Ajout d\'un nom';
	echo '<img id="ajout_nom'.$suffixe.'" src="'.$chemin_images_icones.$Icones['ajout'].'" alt="'.$texte_im.'" title="'.$texte_im.'" '.
	   'onclick="inverse_div(\'id_div_ajout_nom'.$suffixe.'\');document.getElementById(\'nouveau_nom'.$suffixe.'\').focus();"/>'."\n";
	if (isset($_SESSION['Nom_Saisi'])) {
		   echo '&nbsp;'.Affiche_Icone_Clic('copier','reprend_nom(\''.$suffixe.'\');',my_html(LG_PERS_COPY_NAME))."\n";
	}
	echo '<div id="id_div_ajout_nom'.$suffixe.'">'."\n";
	echo my_html(LG_ADD_NAME).'&nbsp;<input type="text" size="50" name="nouveau_nom'.$suffixe.'" id="nouveau_nom'.$suffixe.'"/>'."\n";
	$texte_im = my_html(LG_NAME_TO_UPCASE);
	echo '&nbsp;<img id="majuscule'.$suffixe.'" src="'.$chemin_images_icones.$Icones['majuscule'].'" alt="'.$texte_im.'" title="'.$texte_im.'"'.
	   // ' onclick="NomMaj(\''.$suffixe.'\');document.getElementById(\'NomP'.$suffixe.'\').focus();"/>'."\n";
	   ' onclick="NomMaj(\''.$suffixe.'\');"/>'."\n";
	echo '<input type="button" name="ferme_OK_nom'.$suffixe.'" value="OK" onclick="ajoute_nom(\''.$suffixe.'\')"/>'."\n";
	echo '<input type="button" name="ferme_An_nom'.$suffixe.'" value="Annuler" onclick="inverse_div(\'id_div_ajout_nom'.$suffixe.'\')"/>'."\n";
	echo '</div>'."\n";
	if (($suffixe == 'pere') or ($suffixe == 'mere'))
		echo '<input type="button" name="restr_nom'.$suffixe.'" value="'.$Nom.'" '.
		'onclick="document.forms.saisie.NomSel'.$suffixe.'.value=\''.$idNomPers.'/'.$Nom.'\';'.
		         'document.forms.saisie.Nom'.$suffixe.'.value = document.forms.saisie.NomSel'.$suffixe.'.value;"/>'."\n";
	echo '</td>';
	echo '</tr>'."\n";
	
	col_titre_tab_noClass(LG_PERS_FIRST_NAME,25);
	echo '     <td><input type="text" size="50" name="Prenoms'.$suffixe.'" value="" '.$style_z_oblig.'/>&nbsp;'."\n";
	if ($oblig) Img_Zone_Oblig('imgObligPrenoms'.$suffixe);
	echo '</td></tr>';
	if (($suffixe != 'pere') and ($suffixe != 'mere')) {
		col_titre_tab_noClass(LG_SEXE, $largP);

		// Pour le conjoint, le sexe opposé à celui de la personne est proposé
		$Sexe_Checked_m = '';
		$Sexe_Checked_f = '';
		if ($suffixe == 'conj') {
			switch ($Sexe) {
				case 'm' : $Sexe_Checked_f = 'checked="checked"'; break;
				case 'f' : $Sexe_Checked_m = 'checked="checked"'; break;
			}
		}

		$name = 'Sexe'.$suffixe;
		echo '<td><input type="radio" id="'.$name.'_m" name="'.$name.'" value="m" '.$Sexe_Checked_m.'/>'
			.'<label for="'.$name.'_m">'.LG_SEXE_MAN.'</label>&nbsp;';
		echo '<input type="radio" id="'.$name.'_f" name="'.$name.'" value="f" '.$Sexe_Checked_f.'/>'
			.'<label for="'.$name.'_f">'.LG_SEXE_WOMAN.'</label>&nbsp;';
		echo '</td></tr>'."\n";
	}
	col_titre_tab_noClass(LG_PERS_BORN, $largP);
	echo '<td>';
	zone_date2('ANe_le'.$suffixe, 'Ne_le'.$suffixe, 'CNe_le'.$suffixe, '');
	echo $lg_date_a;
	echo '<select name="selNe'.$suffixe.'">'."\n";
	echo $list_opt_villes;
	echo '</select>'."\n";
	echo '</td></tr><tr>';
	
	col_titre_tab_noClass(LG_PERS_BAPM, $largP);
	echo '<td>';
	zone_date2('ABaptise_le'.$suffixe, 'Baptise_le'.$suffixe, 'CBaptise_le'.$suffixe, '');
	echo $lg_date_a;
	echo '<select name="selBaptise'.$suffixe.'">'."\n";
	echo $list_opt_villes;
	echo '</select>'."\n";
	echo '</td></tr><tr>';
	
	col_titre_tab_noClass(LG_PERS_DEAD, $largP);
	echo '<td>';
	zone_date2('ADecede_le'.$suffixe, 'Decede_le'.$suffixe, 'CDecede_le'.$suffixe, '');
	echo $lg_date_a;	
	echo '<select name="selDecede'.$suffixe.'">'."\n";
	echo $list_opt_villes;
	echo '</select>'."\n";
	echo '</td></tr>';
	echo '</table>';
}

// Affiche une union
function Aff_Donnees($Refer) {
	global $chemin_images,$Comportement,$Icones,$Images,$chemin_images,$list_opt_villes,$existe_filiation
		, $lib_Okay, $lib_Annuler, $Numero, $lg_date_a
		, $hidden, $largP
	;

	// Accès à la personne pour récupérer ses lieux de naissance et de décès
	$villeN = 0;
	$villeD = 0;
	$reqP = 'SELECT Ville_Naissance, Ville_Deces, Sexe FROM ' . nom_table('personnes').' WHERE reference = '.$Refer.' limit 1';

	if ($res = lect_sql($reqP)) {
		if ($enregP = $res->fetch(PDO::FETCH_NUM)) {
			// Mémorisation de la ville de naissance si renseignée
			$villeN = $enregP[0];
			if ($villeN) $lieux[] = $villeN;
			// Mémorisation de la ville de naissance si renseignée et différente de la ville de naissance
			$villeD = $enregP[1];
			if (($villeD) and ($villeD != $villeN)) $lieux[] = $villeD;
			$Sexe = $enregP[2];
		}
		$res->closeCursor();
	}
	// Accès à la filiation pour récupérer les parents et déterminer s'il existe une filiation
	$existe_filiation = Get_Parents($Refer,$Pere,$Mere,$Rang);
	echo '<input type="'.$hidden.'" name="pere" value="'.$Pere.'"/>'."\n";
	echo '<input type="'.$hidden.'" name="mere" value="'.$Mere.'"/>'."\n";
	echo '<input type="'.$hidden.'" name="sexe" value="'.$Sexe.'"/>'."\n";
    // Recherche du lieu de baptême dans les évènements (en standard, le lieu n'est pas renseigné)
    $villeB = 0;
    $sqlB = 'select Identifiant_zone, Identifiant_Niveau from '.nom_table('evenements').
            ' where Code_Type = \'BAPM\''.
            ' and Reference in (select Evenement from '.nom_table('participe').' where Personne = '.$Refer.') limit 1';
    if ($resB = lect_sql($sqlB)) {
      if ($enregB = $resB->fetch(PDO::FETCH_NUM)) {
		// Mémorisation de la ville de baptême si renseignée et différente de la ville de naissance et de la ville de décès
        if ($enregB[0] == 4) $villeB = $enregB[1];
		if (($villeB) and ($villeB != $villeN) and ($villeB != $villeD)) $lieux[] = $villeB;
      }
      $resB->closeCursor();
    }
	// Constitution du select des villes
	$list_opt_villes = '<option value="0" selected="selected">-- ville inconnue --</option>'."\n";
	if (isset($lieux)) {
		$nb = count($lieux);
		for ($i=0;$i<$nb;$i++) {
			$list_opt_villes .= '<option value="'.$lieux[$i].'">'.lib_ville($lieux[$i],'O').'</option>'."\n";
		}
	}

	echo '<br />';

	echo '<div id="content">'."\n";
	echo '<table id="cols"  border="0" cellpadding="0" cellspacing="0" >'."\n";
	echo '<tr>'."\n";
	echo '<td style="border-right:0px solid #9cb0bb">'."\n";
	echo '  <img src="'.$chemin_images.$Images['clear'].'" width="700" height="1" alt="clear"/>'."\n";
	echo '</td>'."\n";
	echo '</tr>'."\n";

	echo '<tr>'."\n";
	echo '<td class="left">'."\n";
	echo '<div class="tab-container" id="container1">'."\n";
	// Onglets
	echo '<ul class="tabs">'."\n";
	
	echo '<li><a href="#" onclick="return showPane(\'pnlConjoint\', this)" id="tab_conj">'.my_html(ucfirst(LG_HUSB_WIFE)).'</a></li>'."\n";
	// Saisie rapide des parents si la filiation n'existe pas
	if (!$existe_filiation)
		echo '<li><a href="#" onclick="return showPane(\'pane1\', this)" id="tab1">'.my_html(ucfirst(LG_PARENTS)).'</a></li>'."\n";
	// Saisie rapide d'un frère ou d'une soeur si la filiation existe
	else
		echo '<li><a href="#" onclick="return showPane(\'pane1\', this)" id="tab1">'.my_html(ucfirst(LG_BROTHER_SISTER)).'</a></li>'."\n";
	echo '</ul>'."\n";

	echo '<div class="tab-panes">'."\n";

	// Données du conjoint
	echo '<div id="pnlConjoint">'."\n";
	$suffixe = 'conj';
	$x = Aff_Pers($suffixe,1);
	echo '<br />';
	echo '<table border="0" width="100%">'."\n";
	echo '<tr>'."\n";
	col_titre_tab_noClass(LG_PERS_UNION, $largP);
	echo '<td>';
	zone_date2('AUnis_le'.$suffixe, 'Unis_le'.$suffixe, 'CUnis_le'.$suffixe, '');
	echo $lg_date_a;
	echo '<select name="selUnis'.$suffixe.'">'."\n";
	echo $list_opt_villes;
	echo '</select>'."\n";
	echo '</td></tr>';
	echo '</table>';
	echo '</div>'."\n";

	// Onglets parents ou frère / soeur
	echo '<div id="pane1">'."\n";
	// Pavé parents
	if (!$existe_filiation) {
		echo '<fieldset>'."\n";
		aff_legend(LG_FATHER);
		$x = Aff_Pers('pere',0);
		echo '</fieldset>'."\n";
		echo '<fieldset>'."\n";
		aff_legend(LG_MOTHER);
		$x = Aff_Pers('mere',0);
		$suffixe = 'parents';
		echo '</fieldset>'."\n";
		echo '<br />';
		echo '<table border="0"  width="100%">'."\n";
		echo '<tr>'."\n";
		col_titre_tab_noClass(LG_PERS_UNION, $largP);
		echo '<td>';
		zone_date2('AUnis_le'.$suffixe, 'Unis_le'.$suffixe, 'CUnis_le'.$suffixe, '');
		echo $lg_date_a;
		// echo '<td><input type="text" readonly="readonly" size="25" name="Unis_le'.$suffixe.'" value=""/>';
		// Affiche_Calendrier('imgCalendU','Calendrier_Union(\''.$suffixe.'\')');
		// echo '<input type="'.$hidden.'" name="CUnis_le'.$suffixe.'" value=""/>'."\n";
		// echo ' &nbsp;&agrave;&nbsp;&nbsp;';
		echo '<select name="selUnis'.$suffixe.'">'."\n";
		echo $list_opt_villes;
		echo '</select>'."\n";
		echo '</td></tr>';
		echo '</table>';
		if (($Numero != '') and (is_numeric($Numero))) {
			echo '<table border="0"  width="100%">'."\n";
			echo '<tr>'."\n";
			col_titre_tab_noClass(LG_PERS_AUTO_CALC_SOSA, $largP);
			echo '<td><input type="checkbox" name="Auto_Sosa" checked="checked"/></td></tr>'."\n";
			echo '</table>';
			echo '<input type="'.$hidden.'" name="NSosa" value="'.$Numero.'"/>'."\n";
		}
		else {
			echo '<input type="'.$hidden.'" name="NSosa" value=""/>'."\n";
			echo '<input type="'.$hidden.'" name="Auto_Sosa" value="n"/>'."\n";
		}
	}
	else {
		$x = Aff_Pers('col',1);
	}
	echo '</div>'."\n";

	echo '</div>'."\n";  //  <!-- panes -->
    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);
	echo '</div>'."\n";  //  <!-- tab container -->

	echo '</td></tr></table></div>'."\n";

}

// Création de la personne
 function creation_personne($Nom,$Prenoms,$Sexe,$DNai,$DDec,$LNai,$LDec,$DBap,$LBap,$Sosa='') {
	global $nouv_ident,$rubs,$cont,$maj_site;
	$rubs = '';
	$cont = '';

   	// On commence par enlever les numéros en entête des noms
	$idNom = 0;
	$posi = strpos($Nom,'/');
	if ($posi > 0) {
		$idNom = strval(substr($Nom,0,$posi));
		$Nom = substr($Nom,$posi+1);
	}

	// Création du nom de famille ?
	$idNom = Ajoute_Nom($idNom,$Nom);
	if ($idNom == -1) $Nom = '';

	// Alimentation Automatique du numéro Sosa

	// Récupération de l'identifiant à positionner
	$nouv_ident = Nouvel_Identifiant('Reference','personnes');
	// Alimentation des colonnes
	Ins_Zone_Req_Rub($nouv_ident,'N','Reference');
	Ins_Zone_Req_Rub($Nom,'A','Nom');
	Ins_Zone_Req_Rub($Prenoms,'A','Prenoms');
	Ins_Zone_Req_Rub($Sexe,'A','Sexe');
	if ($Sosa != '')
		Ins_Zone_Req_Rub($Sosa,'A','Numero');
	Ins_Zone_Req_Rub($DNai,'A','Ne_le');
	Ins_Zone_Req_Rub($DDec,'A','Decede_le');
	Ins_Zone_Req_Rub($LNai,'N','Ville_Naissance');
	Ins_Zone_Req_Rub($LDec,'N','Ville_Deces');
	Ins_Zone_Req_Rub('O','A','Diff_Internet');  // Diffusable par défaut
	Ins_Zone_Req_Rub('N','A','Statut_Fiche');   // Non validée par défaut
	Ins_Zone_Req_Rub($idNom,'N','idNomFam');
	
	// Pour éviter les nulls qui posent problème sur la fiche familiale
	Rattrape_null($Sosa,'Numero');
	Rattrape_null($DNai,'Ne_le');
	Rattrape_null($DDec,'Decede_le');
	Rattrape_null($Sexe,'Sexe');

	// Mise en forme de la requête
	$req = 'insert into '.nom_table('personnes').
		' ('.$rubs.',Date_Creation,Date_Modification) values'.
		' ('.$cont.',current_timestamp,current_timestamp)';
	// Exécution de la requête
	$res = maj_sql($req);

	// Création du lien personnes / noms
 	$req = 'insert into '.nom_table('noms_personnes').' values('.$nouv_ident.','.$idNom.',\'O\',null)';
	$res = maj_sql($req);

	// Création de l'évènement baptême
	if (($DBap != '') or ($LBap != 0)) {
		$rubs = '';
		$cont = '';
		Ins_Zone_Req_Rub($LBap,'N','Identifiant_zone');
		Ins_Zone_Req_Rub(4 ,'N','Identifiant_Niveau');
		Ins_Zone_Req_Rub('BAPM','A','Code_Type');
		Ins_Zone_Req_Rub(LG_PERS_BAPM_EVENT,'A','Titre ');
		if ($DBap != '') {
			Ins_Zone_Req_Rub($DBap,'A','Debut');
			Ins_Zone_Req_Rub($DBap,'A','Fin');
		}
		Ins_Zone_Req_Rub('N','A','Statut_Fiche');        // Non validée par défaut
		$req = 'insert into '.nom_table('evenements').
			' ('.$rubs.',Date_Creation,Date_Modification) values'.
			' ('.$cont.',current_timestamp,current_timestamp)';
		// Exécution de la requête
		$res = maj_sql($req);
		//$num_evt = $res->insert_id();
		// Création de la participation
		$req = '';
		//Ins_Zone_Req($num_evt,'N',$req);
		Ins_Zone_Req($nouv_ident,'N',$req);
		Ins_Zone_Req(' ','A',$req);
		Ins_Zone_Req($DBap,'A',$req);
		Ins_Zone_Req($DBap,'A',$req);
		Ins_Zone_Req('O','A',$req);
		$req  = 'insert into ' . nom_table('participe') . ' values(LAST_INSERT_ID(),'.$req.',0,0,\'n\')';
		// Exécution de la requête
		$res = maj_sql($req);
	}
	$maj_site = true;
}

  //Demande de mise à jour
  if ($bt_OK) {
    // Init des zones de requête
    $Creation = 0;
    $req = '';
    $maj_site = false;

	// Création d'un colattéral ==> on crée la personne (+ évènement si baptême) et la filiation
	if (($Nomcol != '') and ($Prenomscol != '')){
		// Création de la personne et du baptême associé
		creation_personne($Nomcol,$Prenomscol,$Sexecol,$CNe_lecol,$CDecede_lecol,$selNecol,$selDecedecol,$CBaptise_lecol,$selBaptisecol);
		// Création de la filiation
		$req = 'insert into '.nom_table('filiations').' values ('.
			$nouv_ident.','.$pere.','.$mere.',0,current_timestamp,current_timestamp,\'N\')';
		// Exécution de la requête
		$res = maj_sql($req);
		$maj_site = true;
	}
	// Création d'un conjoint ==> on crée la personne et l'union
	if (($Nomconj != '') and ($Prenomsconj != '')){
		// Création de la personne et du baptême associé
		creation_personne($Nomconj,$Prenomsconj,$Sexeconj,$CNe_leconj,$CDecede_leconj,$selNeconj,$selDecedeconj,$CBaptise_leconj,$selBaptiseconj);
		// Création de l'union
		$rubs = '';
		$cont = '';
		if ($sexe == 'f') {
			Ins_Zone_Req_Rub($nouv_ident,'N','Conjoint_1');
			Ins_Zone_Req_Rub($Refer,'N','Conjoint_2');
		}
		else {
			Ins_Zone_Req_Rub($Refer,'N','Conjoint_1');
			Ins_Zone_Req_Rub($nouv_ident,'N','Conjoint_2');
		}
		if ($CUnis_leconj != '') Ins_Zone_Req_Rub($CUnis_leconj,'A','Maries_Le');
		Ins_Zone_Req_Rub($selUnisconj,'N','Ville_Mariage');
		$req = 'insert into '.nom_table('unions').
			' ('.$rubs.',Date_Creation,Date_Modification) values'.
			' ('.$cont.',current_timestamp,current_timestamp)';
		// Exécution de la requête
		$res = maj_sql($req);
		$maj_site = true;
	}
	// Création des parents ==> on crée les personnes (+ évènement si baptême) + leur union et la filiation
	if ((($Nompere != '') and ($Prenomspere != '')) or
		(($Nommere != '') and ($Prenomsmere != ''))) {
		$num_pere = 0;
		$num_mere = 0;
		// Création du père et du baptême associé
		if (($Nompere != '') and ($Prenomspere != '')){
			$sosa = '';
			if (($Auto_Sosa == 'on')and ($NSosa != '')) {
				$sosa = intval($NSosa) * 2;
			}
			creation_personne($Nompere,$Prenomspere,$Sexepere,$CNe_lepere,$CDecede_lepere,$selNepere,$selDecedepere,$CBaptise_lepere,$selBaptisepere,$sosa);
			$num_pere = $nouv_ident;

		}
		// Création de la mère et du baptême associé
		if (($Nommere != '') and ($Prenomsmere != '')){
			$sosa = '';
			if (($Auto_Sosa == 'on')and ($NSosa != '')) {
				$sosa = (intval($NSosa) * 2) + 1;
			}
			creation_personne($Nommere,$Prenomsmere,$Sexemere,$CNe_lemere,$CDecede_lemere,$selNemere,$selDecedemere,$CBaptise_lemere,$selBaptisemere,$sosa);
			$num_mere = $nouv_ident;
		}
		// Création de l'union des parents si les 2 parents sont renseignés (ont été créés)
		if ($num_pere and $num_mere) {
			$rubs = '';
			$cont = '';
			Ins_Zone_Req_Rub($num_pere,'N','Conjoint_1');
			Ins_Zone_Req_Rub($num_mere,'N','Conjoint_2');
			if ($CUnis_leparents != '') Ins_Zone_Req_Rub($CUnis_leparents,'A','Maries_Le');
			Ins_Zone_Req_Rub($selUnisparents,'N','Ville_Mariage');
			$req = 'insert into '.nom_table('unions').
				' ('.$rubs.',Date_Creation,Date_Modification) values'.
				' ('.$cont.',current_timestamp,current_timestamp)';
			// Exécution de la requête
			$res = maj_sql($req);
		}
		// Création de la filiation
		$req = 'insert into '.nom_table('filiations').' values ('.
			$Refer.','.$num_pere.','.$num_mere.',0,current_timestamp,current_timestamp,\'N\')';
		// Exécution de la requête
		$res = maj_sql($req);
		$maj_site = true;
	}

	// Mise à jour de la date du site
	if ($maj_site) maj_date_site(true);

    // Retour arrière
    Retour_Ar();

  }

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

	$largP = 25;

	// include('jscripts/Ajout_Rapide.js');
	$compl = '';

	$Nom = '';
	$Prenoms = '';
	$sql = 'select Nom, Prenoms, idNomFam, Sexe, Numero  from '.nom_table('personnes').' where Reference  = '.$Refer.' limit 1';
	if ($res = lect_sql($sql)) {
		if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
			$Nom       = my_html($enreg[0]);
			$Prenoms   = my_html($enreg[1]);
			$idNomPers = $enreg[2];
			$Sexe      = $enreg[3];
			$Numero    = $enreg[4];
		}
	}
	$res->closeCursor();

	if (($Nom != '') or ($Prenoms != '')) {
		$compl = Ajoute_Page_Info(600,200);
		Insere_Haut(LG_PERS_QUICK_ADD.'&nbsp;'.$Prenoms.' '.$Nom,$compl,'Ajout_Rapide',$Refer);
		echo '<form id="saisie" method="post" action="'.my_self().'?Refer='.$Refer.'">'."\n";
		echo '<input type="'.$hidden.'" name="Refer" value="'.$Refer.'"/>'."\n";
		aff_origine();
		if (isset($_SESSION['Nom_Saisi']))
			echo '<input type="'.$hidden.'" name="Nom_Prec" value="'.$_SESSION['Nom_Saisi'].'"/>'."\n";

		// Affichage des données
		$x = Aff_Donnees($Refer);

		echo '</form>';

		include ('gest_onglets.js');
		echo '<!-- On positionne l\'onglet par défaut -->'."\n";
		echo '<script type="text/javascript">'."\n";
		echo '	cache_div("id_div_ajout_nomconj")'."\n";
		if (!$existe_filiation) {
			echo '	cache_div("id_div_ajout_nompere")'."\n";
			echo '	cache_div("id_div_ajout_nommere")'."\n";
		}
		else {
			echo '	cache_div("id_div_ajout_nomcol")'."\n";
		}

		echo '	setupPanes("container1", "tab_conj",40);'."\n";
		echo '</script>'."\n";
	}
  Insere_Bas($compl);
  }
  else {
    echo "<body bgcolor=\"#FFFFFF\">";
  }
?>
<script type="text/javascript">
<!--

// Ajoute le nom saisi dans la liste des noms de famille
function ajoute_nom(cible) {
	var nouv_text = document.getElementById("nouveau_nom"+cible).value;
	var nouv_val = '0/' + nouv_text;
	document.getElementById("Nom"+cible).value = nouv_val;
	nouvel_element = new Option(nouv_text,nouv_val,false,true);
	document.getElementById("NomSel"+cible).options[document.getElementById("NomSel"+cible).length] = nouvel_element;
	document.getElementById("nouveau_nom"+cible).value = "";
	inverse_div('id_div_ajout_nom'+cible);
}

// Met le nom en majuscules
function NomMaj(cible) {
	document.getElementById("nouveau_nom"+cible).value = document.getElementById("nouveau_nom"+cible).value.toUpperCase();
}

// Reprend le nom saisi précédemment
function reprend_nom(cible) {
  nouv_text = document.forms.saisie.Nom_Prec.value;
  document.getElementById("Nom"+cible).value = nouv_text;
  document.getElementById("NomSel"+cible).value = nouv_text;
}

//-->
</script> 
</body>
</html>