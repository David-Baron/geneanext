<?php

//=====================================================================
// Fonction de recherche générique sur les personnes par les parents ou conjoints
// (c) JLS
// UTF-8
//=====================================================================

session_start();
include('fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
	'ok','annuler',
	'reprise',
	'Sch_Type',
	'NomP','Son','Prenoms','Sexe',
	'Annee','TypeAnnee','Tolerance',
	'Ville_Naissance','Ville_Deces',
	'New_Window','Sortie','Tri',
	'Horigine'
);
foreach ($tab_variables as $nom_variables) {
	if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
	else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok,strlen($lib_Rechercher),'S');
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Annuler),'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_Rechercher) $ok = 'OK';

// Gestion standard des pages
$acces = 'L';								// Type d'accès de la page : (L)ecture
$titre = $LG_Menu_Title['Sch_Pers_CP'];		// Titre pour META
$niv_requis = 'P';							// Page accessible à partir du niveau privilégié
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Suite sécurisation des variables postées
$reprise         = Secur_Variable_Post($reprise,1,'S'); // 1 seul caractère suffit
$Sch_Type        = Secur_Variable_Post($Sch_Type,1,'S');
$NomP            = Secur_Variable_Post($NomP,50,'S');
$Son             = Secur_Variable_Post($Son,1,'S');
$Prenoms         = Secur_Variable_Post($Prenoms,50,'S');
$Sexe            = Secur_Variable_Post($Sexe,1,'S');
$Annee           = Secur_Variable_Post($Annee,4,'S');
$TypeAnnee       = Secur_Variable_Post($TypeAnnee,1,'S');
$Tolerance       = Secur_Variable_Post($Tolerance,1,'N');
$Ville_Naissance = Secur_Variable_Post($Ville_Naissance,25,'S');
$Ville_Deces     = Secur_Variable_Post($Ville_Deces,25,'S');

$Sortie          = Secur_Variable_Post($Sortie,1,'S');
$Tri             = Secur_Variable_Post($Tri,2,'S');
$New_Window      = Secur_Variable_Post($New_Window,1,'S');

function aff_n_dec() {
	global $row;
	$Ne = $row[3];
	$Decede = $row[4];
	if (($Ne != '') or ($Decede != '')) {
		echo '&nbsp;(';
		if ($Ne != '') echo '&deg; '.Etend_date($Ne);
		if ($Decede != '') {
		if ($Ne != '') echo ', ';
			echo '+ '.Etend_date($Decede);
		}
	echo ')';
	}
}

function Ajb_Zone_Req($NomRub,$Rub,$TypRub,&$LaReq,$Zone) {
	global $memo_criteres,$separ;
	if ($Rub != '') {
		$C_Rub = $Rub;
		if (($NomRub == 'Ville_Naissance') or ($NomRub == 'Ville_Deces'))
			$C_Rub = lib_ville($Rub);
		$le_crit = $C_Rub;
		$memo_criteres = $memo_criteres.$Zone.' = '.$C_Rub.$separ;

		if ($LaReq != '') $LaReq = $LaReq.' and ';
		if ($TypRub == 'A') {
			// Recherche de type like ou = ?
			if (strpos($Rub,'*')=== false) {
				$oper = '=';
			}
			else {
				$oper = ' like ';
				$Rub = str_replace('*','%',$Rub);
			}
			$LaReq = $LaReq.' upper('.$NomRub.')'.$oper;
			$LaReq = $LaReq .'"'.strtoupper($Rub).'"';
		}
		else {
			$LaReq = $LaReq.' '.$NomRub.'='.$Rub;
		}
	}
}

$compl = Ajoute_Page_Info(650,300);

if ($bt_OK) Ecrit_Entete_Page($titre,$contenu,$mots);

if ($Sortie != 't') 
	Insere_Haut($titre,$compl,'Recherche_Personne','');
else
	Insere_Haut_texte ($titre);

$n_villes = nom_table('villes');

//Demande de recherche
if ($bt_OK) {

	if (($NomP != '') and (strpos($NomP,';') != 0)) {
		$x = explode(';',$NomP);
		$NomP = $x[0];
		$Prenoms = $x[1];
		$Prenoms = $Prenoms . '*';
	}

	$erreur = 0;
	if ($Sortie == 'c') {
		// Traiter le cas d'erreur sur l'ouverture du fichier
		$nom_fic = $chemin_exports.'recherche.csv';
		$fp = ouvre_fic($nom_fic,'w+');
	}
	$tab = '&nbsp;&nbsp;&nbsp;';
	// Init des zones de requête
	echo my_html(LG_PERS_REQ_FIELDS).LG_SEMIC.'<br />';
	$req = '';
	$memo_criteres = '';
	$crit_type = LG_PERS_SCH_TYPE_PARENT;
	if ($Sch_Type == 'c') $crit_type = LG_PERS_SCH_TYPE_PARTNER;
	// echo $tab.$crit_type.'<br />';
	
	$n_personnes = nom_table('personnes');
	$n_unions = nom_table('unions');

	// Constitution de la requête d'extraction
	Ajb_Zone_Req('Prenoms',$Prenoms,'A',$req,LG_PERS_FIRST_NAME);
	if (($Sexe == 'm') or ($Sexe == 'f')) {
		Ajb_Zone_Req('Sexe',$Sexe,'A',$req,LG_SEXE);
		$crit_type .= ' = ';
		if ($Sexe == 'f') {
			$crit_sexe_parent = 'Mere';
			$crit_type .= LG_SEXE_WOMAN;
		} else {
			$crit_sexe_parent = 'Pere';
			$crit_type .= LG_SEXE_MAN;
		}
	}
	echo $tab.$crit_type.'<br />';
	
	if ($Ville_Naissance > -1)
		Ajb_Zone_Req('Ville_Naissance',$Ville_Naissance,'N',$req,LG_PERS_REQ_BORN_TOWN);
	if ($Ville_Deces > -1)
	Ajb_Zone_Req('Ville_Deces',$Ville_Deces,'N',$req,LG_PERS_REQ_DEATH_TOWN);
	// Traitement spécifique de l'année : naissance, décès, vivant
	if ($Annee != '') {
		// On met l'année sur 4 caractères
		if (!is_numeric($Annee)) $Annee = 9999;
		$Annee = str_pad(trim($Annee),4,'0',STR_PAD_LEFT);
		$aff_annee = '';
		if ($Tolerance != '0') $plu_tolerance = pluriel($Tolerance);
		$supReq = '';
		switch($TypeAnnee) {
			case 'n' : if ($Tolerance == '0') {
							$supReq = ' substr(Ne_le,1,4) = '.$Annee;
						}
						else {
							$annee_inf = $Annee - $Tolerance;
							$annee_sup = $Annee + $Tolerance;
							$supReq = ' substr(Ne_le,1,4) between '.$annee_inf.' and '.$annee_sup;
						}
						$supReq .= ' and substr(Ne_le,10,1) = \'L\'';
						$aff_annee = LG_PERS_REQ_BORN_IN.$Annee;
						if ($Tolerance != '0') $aff_annee .= my_html(' '.LG_PERS_REQ_MORE_LESS_1.$Tolerance.' '.LG_PERS_REQ_MORE_LESS_2.$plu_tolerance.')');
						break;
			case 'd' : if ($Tolerance == '0') {
							$supReq = ' substr(Decede_Le,1,4) = '.$Annee;
						}
						else {
							$annee_inf = $Annee - $Tolerance;
							$annee_sup = $Annee + $Tolerance;
							$supReq = ' substr(Decede_Le,1,4) between '.$annee_inf.' and '.$annee_sup;
						}
						$supReq .= ' and substr(Decede_Le,10,1) = \'L\'';
						$aff_annee = LG_PERS_REQ_DEATH_IN.$Annee;
						if ($Tolerance != '0') $aff_annee .= ' (+ ou -'.$Tolerance.' an'.$plu_tolerance.')';
						break;
			case 'v' : $supReq = ' substr(Ne_le,1,4) <= '.$Annee.' and'.
								 ' substr(Decede_Le,1,4) >= '.$Annee.' and '.
								 ' substr(Ne_le,10,1) = \'L\' and '.
								 ' substr(Decede_Le,10,1) = \'L\'';
					$aff_annee = 'vivant en '.$Annee;
					break;
		}
		echo $tab.$aff_annee.'<br />';
		$memo_criteres = $memo_criteres.$aff_annee.$separ;
		if ($req != '') $req = $req.' and ';
		$req = $req.$supReq;
	}
	// Gestion du nom
	$NomP_I = $NomP;
	if ($NomP != '') {
		$NomP_SL = $NomP;
		echo $tab.my_html($LG_Name).' = '.$NomP_SL.' (';
		if ($req != '') $req .= ' and ';
		switch ($Son) {
			case 'o': $crit_nom = LG_PERS_REQ_SPELL_EXACT; break;
			case 'p': $crit_nom = LG_PERS_REQ_SOUND_EXACT; break;
			case 'a': $crit_nom = LG_PERS_REQ_SOUND_NEAR; break;
		}
		echo $crit_nom.')<br />';
		$memo_criteres = $memo_criteres.my_html($LG_Name) .' = '.$NomP.' ('.$crit_nom.')'.$separ;

		if (strpos($NomP,'*') === false) $oper = '=';
		else {
			$oper = ' like ';
			$NomP = str_replace('*','%',$NomP);
		}

		// L'utilisateur a demandé une recherche phonétique
		if ($Son != 'o') {
			// Transformation du nom en phonétique
			include 'phonetique.php';
			$codePho = new phonetique();
			$NomP = $codePho->calculer($NomP);
		}
		switch ($Son) {
			// Demande basée sur l'orthographe exacte
			case 'o': $req .= 'Reference IN ('.
								   ' SELECT idPers '.
								   ' FROM '.nom_table('noms_personnes').
								   ' WHERE idNom IN ('.
									 ' SELECT idNomFam'.
									 ' FROM '.nom_table('noms_famille').
									 ' WHERE upper(nomFamille) '.$oper.' "'.strtoupper($NomP).'")) '; break;
			// Demande basée sur la phonétique exacte
			case 'p': $req .= 'Reference IN ('.
								   ' SELECT idPers'.
								   ' FROM '.nom_table('noms_personnes').
								   ' WHERE idNom IN ('.
									 ' SELECT idNomFam'.
									 ' FROM '.nom_table('noms_famille').
									 ' WHERE codePhonetique '.$oper.' "'.$NomP.'")) '; break;
			// Demande basée sur la phonétique approchante
			case 'a': $req .= 'Reference IN ('.
								   ' SELECT idPers'.
								   ' FROM '.nom_table('noms_personnes').
								   ' WHERE idNom IN ('.
									 ' SELECT idNomFam'.
									 ' FROM '.nom_table('noms_famille').
									 ' WHERE lower(codePhonetique) '.$oper.' "'.strtolower($NomP).'")) '; break;
			default: break;
		}
	}

	// var_dump($req);

	// Exéution de la requête
	if ($req != '') {

		// Constitution de la partie champs à récupérer
		// Pour les sorties csv, on va récupérer tous les champs alors que sur les autres sorties, la référence, le nom et le prénom suffisent
		if ($Sortie == 'c') {
			$req2 = 'select p.Reference,p.Nom,p.Prenoms,p.Sexe,p.Numero,p.Ne_le,p.Ville_Naissance,n.Nom_Ville,'.
					'p.Decede_Le,p.Ville_Deces,d.Nom_Ville,p.Diff_Internet,p.Date_Creation,p.Date_Modification,p.Statut_Fiche,p.Surnom'.
					' from '.$n_personnes.' p, '.$n_villes. ' n, '.$n_villes. ' d '.
					'where p.Reference <> 0 '.
					'and p.Ville_Naissance = n.Identifiant_zone '.
					'and p.Ville_Deces = d.Identifiant_zone';
		}
		else {
			$req2 = 'select Reference, Nom, Prenoms, Ne_le, Decede_Le from '.$n_personnes.' p where Reference <> 0';
		}
		
		
		// Surcouche non privilégiés sur les 2 niveaux : personnes et parent / conjoint
		$crit_diffu = '';
		if (!$est_privilegie) $crit_diffu = " and Diff_Internet = 'O' ";
		$req2 = $req2 . $crit_diffu;
		$req = $req . $crit_diffu;

		if ($Sch_Type == 'p') {
			// select * from personnes where reference in 
				// (select enfant from filiations where Mere in 
					// (select reference from personnes where prenoms like 'Maurice%'))
			$req = $req2 . ' and Reference in(select Enfant from '.nom_table('filiations').' where '.$crit_sexe_parent.' in (select Reference from '.$n_personnes.' where '.$req.')) order by ';
		}
		if ($Sch_Type == 'c') {
			// select * from personnes 
			// where (reference in (select Conjoint_1 from unions where Conjoint_2 in (select reference from personnes where prenoms like 'Maurice%')) 
			// or reference in (select Conjoint_2 from unions where Conjoint_1 in (select reference from personnes where prenoms like 'Maurice%'))
			$req = $req2 . ' and (Reference in(select Conjoint_1 from '.$n_unions.' where Conjoint_2  in (select Reference from '.$n_personnes.' where '.$req .'))'
						. ' or Reference in(select Conjoint_2 from '.$n_unions.' where Conjoint_1  in (select Reference from '.$n_personnes.' where '.$req .'))'
						.') order by ';
		}
		// echo 'req2 : '.$req2.'<br /><br />';
		// echo 'req : '.$req.'<br /><br />';

		// Critère de tri par défaut
		$Critere = 'p.Nom,p.Prenoms';
		switch($Tri) {
			case 'dn' : $Critere = 'p.Ne_le,p.Nom,p.Prenoms'; break;
			case 'dd' : $Critere = 'p.Decede_Le,p.Nom,p.Prenoms'; break;
		}
		$req .= $Critere;
		if ($res = lect_sql($req)) {
			$nb_lignes = $res->rowCount();
			$plu = pluriel($nb_lignes);
			echo $nb_lignes.my_html(' '.LG_PERS_REQ_PERS_FOUND_1.$plu.' '.LG_PERS_REQ_PERS_FOUND_2.$plu).'<br /><br />';
			// Recherche du nom sur les sites gratuits ; pas sur les sites gratuits non premium
			if (($Sortie == 'e') and ((!$SiteGratuit) or ($Premium))) {
				if ($NomP != '') {
					echo '<a href="'.$adr_rech_gratuits.'?ok=ok&amp;NomP='.str_replace('%','*',$NomP).'" target="_blank">'
							.my_html(LG_PERS_REQ_FIND_NAME).'</a><br /><br />'."\n";
				}
			}
			// Récup de la liste des champs
			$champs = get_fields($req,true);
			if ($champs) {
				$num_fields = count($champs);
			}

			if ($Sortie == 'c') {
				$ligne = '';
				for ($nb=0; $nb < $num_fields; $nb++) {
					$nom_champ = $champs[$nb];
					if ($nom_champ == 'Ne_le') {
						$nom_champ = 'Precision_Naissance;'.$nom_champ.';Calendrier_Naissance';
						$c_ne = $nb;
					}
					if ($nom_champ == 'Decede_Le') {
						$nom_champ = 'Precision_Deces;'.$nom_champ.';Calendrier_Deces';
						$c_decede = $nb;
					}
					$ligne .= $nom_champ.';';
				}
				ecrire($fp,$ligne);
			}
			$target = ($New_Window == 'O') ? true : false;
			while ($row = $res->fetch(PDO::FETCH_NUM)) {
				$ref = $row[0];
				$prenom = my_html($row[2]);
				$nom = my_html($row[1]);

				switch ($Sortie) {
					case 'e' : echo '<a '.Ins_Ref_Pers($ref,$target).'>'.$nom.'&nbsp;'.$prenom.'</a>';
								aff_n_dec();
								if ($est_gestionnaire) echo '&nbsp;'.Affiche_Icone_Lien(Ins_Edt_Pers($ref),'fiche_edition',$LG_modify);
								echo '<br />'."\n";
								break;
					case 't' : echo $nom.'&nbsp;'.$prenom;
								aff_n_dec();
								echo '<br />'."\n";
								break;
					case 'c' : $ligne = '';
								for ($nb=0; $nb < $num_fields; $nb++) {
									$contenu = $row[$nb];
									// On retravaille les champs naissance et décès
									if (($nb == $c_ne) or ($nb == $c_decede)) {
										$ligne .= Retourne_Date_CSV($contenu) . ';';
									}
									else $ligne .= '"'.$contenu.'";';
								}
								ecrire($fp,$ligne);
								break;
				}
			}
			if ($Sortie == 'c') {
				fclose($fp);
				echo '<br />'.my_html($LG_csv_available_in).' <a href="'.$nom_fic.'">'.$nom_fic.'</a><br />'."\n";
			}
		}
		else {
			echo '<br />'.my_html(LG_PERS_REQ).LG_SEMIC.$req;
			aff_erreur(LG_PERS_REQ_ERROR);
		}
	}

    if ($Sortie != 't') {
	    // Nouvelle recherche
	    echo '<form id="nouvelle" method="post" action="'.my_self()	.'">'."\n";
	    aff_origine();
		echo '<input type="'.$hidden.'" name="reprise" value=""/>';
		echo '<input type="'.$hidden.'" name="Sch_Type" value="'.$Sch_Type.'"/>';
		echo '<input type="'.$hidden.'" name="NomP" value="'.$NomP.'"/>';
		echo '<input type="'.$hidden.'" name="Son" value="'.$Son.'"/>';
		echo '<input type="'.$hidden.'" name="Prenoms" value="'.$Prenoms.'"/>';
		echo '<input type="'.$hidden.'" name="Sexe" value="'.$Sexe.'"/>';
		echo '<input type="'.$hidden.'" name="Annee" value="'.$Annee.'"/>';
		echo '<input type="'.$hidden.'" name="Tolerance" value="'.$Tolerance.'"/>';
		echo '<input type="'.$hidden.'" name="TypeAnnee" value="'.$TypeAnnee.'"/>';
		echo '<input type="'.$hidden.'" name="Ville_Naissance" value="'.$Ville_Naissance.'"/>';
		echo '<input type="'.$hidden.'" name="Ville_Deces" value="'.$Ville_Deces.'"/>';
		echo '<input type="'.$hidden.'" name="Tri" value="'.$Tri.'"/>';
		echo '<input type="'.$hidden.'" name="New_Window" value="'.$New_Window.'"/>';
	    echo '<br />';
       	echo '<div class="buttons">';
	   	echo '<button type="submit" class="positive"><img src="'.$chemin_images_icones.$Icones['chercher'].'" alt=""/>'.$lib_Nouv_Rech.'</button>';
       	if ((!$SiteGratuit) or ($Premium)) {
		   	echo '<button type="submit" onclick="document.forms.nouvelle.reprise.value=\'reprise\'; "'.
		   	 ' class="positive"><img src="'.$chemin_images_icones.$Icones['chercher_plus'].'" alt=""/>'.$lib_Nouv_Rech_Aff.'</button>';
       	}
		echo '</div>';
	    echo '</form>'."\n";
    }
  }

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

	$larg_titre = '20';

	$sql = 'select Identifiant_zone, Nom_Ville from '.$n_villes.' order by Nom_Ville';
	$res = lect_sql($sql);

	echo '<form id="saisie" method="post" action="'.my_self().'">'."\n";
	aff_origine();

	echo '<br />';
	echo '<table width="90%" class="table_form">'."\n";

	$checked = ' checked="checked"';
	
	// Conjoint ou parent ?
	colonne_titre_tab(LG_PERS_SCH_TYPE);
	echo '<input type="radio" id = "Sch_Type_p" name="Sch_Type" value="p"';
	if ($reprise) {
		if ($Sch_Type=='p') echo $checked;
	}
	else
		echo $checked;
	echo '/><label for="Sch_Type_p">'.LG_PERS_SCH_TYPE_PARENT.'</label>&nbsp;'."\n";
	echo '<input type="radio" id = "Sch_Type_c" name="Sch_Type" value="c"';
	if ($reprise) {
		if ($Sch_Type=='c') echo $checked;
	}
	echo '/><label for="Sch_Type_c">'.LG_PERS_SCH_TYPE_PARTNER.'</label>';
	echo '</td></tr>'."\n";

	// Nom et type d'orthographe / son
	colonne_titre_tab($LG_Name);
	echo '<input type="text" size="50" name="NomP"';
	if ($reprise) echo ' value="'.$NomP.'"';
	echo '/>';
	echo '<input type="radio" id="son_o" name="Son" value="o"';
	if ($reprise) {
		if ($Son=='o') echo $checked;
	}
	else echo $checked;;
	echo '/><label for="son_o">'.LG_PERS_REQ_SPELL_EXACT.'</label>&nbsp;';;
	echo '<input type="radio" id="son_p" name="Son" value="p"';
	if ($reprise) {
		if ($Son=='p') echo $checked;
	}
	echo '/><label for="son_p">'.LG_PERS_REQ_SOUND_EXACT.'</label>&nbsp;';
	echo '<input type="radio" id="son_a" name="Son" value="a"';
	if ($reprise) {
		if ($Son=='a') echo $checked;
	}
	echo '/><label for="son_a">'.LG_PERS_REQ_SOUND_NEAR.'</label>';
	echo '</td></tr>'."\n";

	// Prénoms
	colonne_titre_tab(LG_PERS_FIRST_NAME);
	echo '<input type="text" size="50" name="Prenoms"';
	if ($reprise) echo ' value="'.$Prenoms.'"';
	echo '/></td></tr>'."\n";

	// Sexe
	colonne_titre_tab(LG_SEXE);
	echo '<input type="radio" id="Sexe_m" name="Sexe" value="m"';
	if ($reprise) {
		if ($Sexe=='m') echo $checked;
	}
	else
		echo $checked;
	echo '/><label for="Sexe_m">'.LG_SEXE_MAN.'</label>&nbsp;';
	echo '<input type="radio" id="Sexe_f" name="Sexe" value="f"';
	if ($reprise) {
		if ($Sexe=='f') echo $checked;
	}
	echo '/><label for="Sexe_f">'.LG_SEXE_WOMAN.'</label>';
	echo '</td></tr>'."\n";
	
	// Année
	colonne_titre_tab(LG_PERS_REQ_YEAR);
	echo '<input type="text" size="4" name="Annee"';
	if ($reprise) echo ' value="'.$Annee.'"';
	echo '/>'."\n";
	echo '<input type="radio" id="TypeAnnee_n" name="TypeAnnee" value="n"';
	if ($reprise) {
		if ($TypeAnnee=='n') echo $checked;
	}
	else echo ' checked="checked"';
	echo '/><label for="TypeAnnee_n">'.$LG_birth.'</label>&nbsp;';
    echo '<input type="radio" id="TypeAnnee_d" name="TypeAnnee" value="d"';
   	if ($reprise) {
		if ($TypeAnnee=='d') echo $checked;
	}
    echo '/><label for="TypeAnnee_d">'.$LG_death.'</label>&nbsp;';
    $texte_image = LG_PERS_REQ_OFF_DOWN;
	echo '&nbsp;(+/- <img src="'.$chemin_images_icones.$Icones['moins'].'" alt="'.$texte_image.'" title="'.$texte_image.'" border="0" ';
	echo 'onclick="if (document.forms.saisie.Tolerance.value>0) {document.forms.saisie.Tolerance.value--;}"/>'."\n";
	echo '<input type="text" size="2" name="Tolerance" value="';
	if ($reprise) echo $Tolerance;
	else echo '0';
	echo '" onchange="verification_num(this);"/>'."\n";
    $texte_image = LG_PERS_REQ_OFF_UP;
	echo '<img src="'.$chemin_images_icones.$Icones['plus'].'" alt="'.$texte_image.'" title="'.$texte_image.'" border="0" ';
	echo 'onclick="document.forms.saisie.Tolerance.value++;"/> '.my_html(LG_PERS_REQ_OFF_YEARS).')&nbsp;&nbsp;'."\n";
    echo '<input type="radio" id="TypeAnnee" name="TypeAnnee" value="v"';
   	if ($reprise) {
		if ($TypeAnnee=='v') echo $checked;
	}
    echo '/><label for="TypeAnnee">'.LG_PERS_REQ_ALIVE."</label>\n";
    echo '</td></tr>'."\n";

	// Lieu de naissance
    colonne_titre_tab(LG_PERS_BORN_AT);
    echo '<select name="Ville_Naissance">'."\n";
    echo '<option value="-1"/>';
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
      echo '<option value="'.$row[0].'"';
      if ($reprise) {
      	if ($Ville_Naissance==$row[0]) echo 'selected="selected"';
      }
      echo '>';
      if ($row[0] == 0) echo my_html(LG_PERS_REQ_NOT_FILLED);
      else              echo my_html($row[1]);
      echo '</option>'."\n";
    }
    echo '</select>'."\n";
    echo '</td></tr>'."\n";

    // Lieu de décès
	colonne_titre_tab(LG_PERS_DEAD_AT);
    echo '<select name="Ville_Deces">'."\n";
	$res->closeCursor();
	$res = lect_sql($sql);
    echo '<option value="-1"/>';
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
      echo '<option value="'.$row[0].'"';
      if ($reprise) {
      	if ($Ville_Deces==$row[0]) echo 'selected="selected"';
      }
      echo '>';
      if ($row[0] == 0) echo my_html(LG_PERS_REQ_NOT_FILLED);
      else              echo my_html($row[1])."\n";
      echo '</option>';
    }
    echo '</select>'."\n";
    echo '</td></tr>'."\n";
    $res->closeCursor();

    ligne_vide_tab_form(1);

	// Tri du résultat
	colonne_titre_tab(LG_PERS_REQ_SORT);
	echo '<input type="radio" id="Tri_np" name="Tri" value="np"';
    if ($reprise) {
		if ($Tri=='np') echo $checked;
	}
    echo '/><label for="Tri_np">'.LG_PERS_REQ_SORT_NS.'</label>&nbsp;';
	echo '<input type="radio" id="Tri_dn" name="Tri" value="dn"';
    if ($reprise) {
		if ($Tri=='dn') echo $checked;
	}
    echo '/><label for="Tri_dn">'.LG_PERS_REQ_SORT_BORN.'</label>&nbsp;';
	echo '<input type="radio" id="Tri_dd" name="Tri" value="dd"';
    if ($reprise) {
		if ($Tri=='dd') echo $checked;
	}
    echo '/><label for="Tri_dd">'.LG_PERS_REQ_SORT_DEATH.'</label>'."\n";
	echo '</td></tr>'."\n";
	
	// Sortie du résultat
    colonne_titre_tab($LG_Ch_Output_Format);
	echo '<input type="radio" id="Sortie_e" name="Sortie" value="e" checked="checked"/><label for="Sortie_e">'.$LG_Ch_Output_Screen.'</label>&nbsp;';
	echo '<input type="radio" id="Sortie_t" name="Sortie" value="t"/><label for="Sortie_t">'.$LG_Ch_Output_Text.'</label>&nbsp;';
	// L'export CSV n'est disponible qu'à partir du profil privilégié
	if ($est_privilegie) echo '<input id="Sortie_c" type="radio" name="Sortie" value="c"/><label for="Sortie_c">'.$LG_Ch_Output_CSV.'</label>';
	echo '</td></tr>'."\n";

	// Ouverture des fiches dans un nouvel onglet ?
	colonne_titre_tab(LG_PERS_REQ_NEW_TAB);
	echo '<input type="checkbox" name="New_Window"';
    if ($reprise) {
		if ($New_Window=='O') echo $checked;
	}
    echo ' value="O"/>';
	echo '</td></tr>'."\n";

	//ligne_vide_tab_form(1);
	bt_ok_an_sup($lib_Rechercher,$lib_Annuler,'','');

	echo '</table>'."\n";
	echo '<br />'.Affiche_Icone('tip',$LG_tip).' '.my_html(LG_PERS_SCH_TIP);

    echo '</form>';
	

}

if ($Sortie != 't') Insere_Bas($compl);
?>
</body>
</html>