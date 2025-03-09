<?php

//=====================================================================
// Affichage des oncles et tantes ou cousins
// JL Servin
// UTF-8
//=====================================================================

function add_pers($enreg) {
	global $refs, $noms, $prenoms;
	$refs[] = $enreg['Enfant'];
	$noms[] = $enreg['Nom'];
	$prenoms[] = $enreg['Prenoms'];
}

function affiche_pers($nb) {
	global $refs, $noms, $prenoms, $echo_modif;
	echo '&nbsp;<a '.Ins_Ref_Pers($refs[$nb]).'>'.my_html($noms[$nb].' '.$prenoms[$nb]).'</a>'."\n";
	echo '&nbsp;<a '.Ins_Edt_Pers($refs[$nb]).'>'.$echo_modif;
}
	
session_start();
include('fonctions.php');
$acces = 'L';
$index_follow = 'ON';		// NOFOLLOW demandé pour les moteurs

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Retour),'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$x = Lit_Env();

$Refer = Recup_Variable('Refer','N');
$T_Parente = Recup_Variable('TP','S');
if (strlen($T_Parente) > 2)
	$T_Parente = 'OT';

// OT : Oncles et Tantes
// CG : Cousin(e)s Germain(e)s
// CI : Cousin(e)s issu(e)de Germain(e)s

// Titre pour META
switch ($T_Parente) {
	case 'OT' : $titre = $LG_Menu_Title['Pers_Uncles']; break;
	case 'CG' : $titre = $LG_Menu_Title['Pers_Cousins']; break;
	default : $titre = 'titre';
}

$n_filiations = nom_table('filiations');
$n_personnes = nom_table('personnes');

$req_sel = 'select Nom, Prenoms, Diff_Internet from ' . $n_personnes . ' where Reference = '.$Refer.' limit 1';

include('Gestion_Pages.php');

//var_dump($bt_An);

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

	$compl = '';

	// Personne inconnue, page vide...
	if ((!$enreg_sel) or ($Refer == 0)) {
		Insere_Haut(my_html($titre),$compl,'Parentees',$Refer);
	}
	else {
		
		$enr_pers = $enreg_sel;
		Champ_car($enr_pers,'Nom');
		Champ_car($enr_pers,'Prenoms');
		$NomP = $enr_pers['Nom'];
		$PrenomsP = $enr_pers['Prenoms'];

		$compl .= Ajoute_Page_Info(600,150);
		Insere_Haut(my_html($titre),$compl,'Parentees',$Refer);

		// Message d'erreur si pas de droits d'accès
		if (!$est_privilegie and $enr_pers['Diff_Internet'] != 'O') {
			echo aff_erreur($LG_Data_noavailable_profile).'<br />' . '<a href="' . Get_Adr_Base_Ref() . '">'.my_html($LG_back_to_home).'</a><br />';
		}
		else {
			echo '<h3 align="center">'.$PrenomsP.' '.$NomP.'</h3>';

			$n_filiations = nom_table('filiations');
			$n_personnes = nom_table('personnes');
			$deb_sql = 'select Enfant, Nom, Prenoms, Diff_Internet from ' . $n_filiations . ' f, ' . $n_personnes . ' p ';

			$refs = [];
			$noms = [];
			$prenoms = [];

			$oncles_tantes_min = 0;
			$oncles_tantes_max = 0;
			$cousins_min = 0;
			$cousins_max = 0;
			$cousins_issus_min = 0;
			$cousins_issus_max = 0;

			// Les non-privilégiés ne peuvent voir que les personnes diffusables
			$crit_restriction = '';
			if (!$est_privilegie) 
				$crit_restriction = 'and Diff_Internet = "O"';


			if (Get_Parents($Refer,$Pere,$Mere,$Rang)) {
				// Récupération des oncles et tantes
				if ($Pere != 0) {
					if (Get_Parents($Pere,$PerePere,$MerePere,$Rang)) {
						$sql = $deb_sql
								.' where f.Enfant = p.Reference and'
								.' ((Pere = '.$PerePere.' and Mere = '.$MerePere. ') ';
						if ($PerePere) $sql .= ' or Pere = '.$PerePere;
						if ($MerePere) $sql .= ' or Mere = '.$MerePere;
						$sql .= ')'.$crit_restriction;
						$resE = lect_sql($sql);
						while ($enreg = $resE->fetch(PDO::FETCH_ASSOC)) {
							if ($enreg['Enfant'] != $Pere) {
								add_pers($enreg);
							}
						}
					}
				}
				if ($Mere != 0) {
					if (Get_Parents($Mere,$PereMere,$MereMere,$Rang)) {
						$sql = $deb_sql
								.' where f.Enfant = p.Reference and'
								.' ((Pere = '.$PereMere.' and Mere = '.$MereMere. ') ';
						if ($PereMere) $sql .= ' or Pere = '.$PereMere;
						if ($MereMere) $sql .= ' or Mere = '.$MereMere;
						$sql .= ')'.$crit_restriction;
						$resE = lect_sql($sql);
						while ($enreg = $resE->fetch(PDO::FETCH_ASSOC)) {
							if ($enreg['Enfant'] != $Mere) {
								add_pers($enreg);				
							}
						}
					}
				}
				$nb_refs = count($refs);
				
				if ($debug) {
					var_dump($refs);
					var_dump($nb_refs);
				}

				if ($refs == '') $nb_refs = 0;
					
				if ($nb_refs > 0) {
					$oncles_tantes_max = $nb_refs - 1;

					// Récupération des cousins
					if (($T_Parente == 'CG') or ($T_Parente == 'CI')) {
						for ($nb=$oncles_tantes_min; $nb<=$oncles_tantes_max; $nb++) {
							$sql = 'select Enfant, Nom, Prenoms, Diff_Internet from ' . $n_filiations . ' f, ' . $n_personnes . ' p '
										.' where f.Enfant = p.Reference and'
										.' (Pere = '.$refs[$nb].' or Mere = '.$refs[$nb]. ')'
										.$crit_restriction;
							$resE = lect_sql($sql);
							while ($enreg = $resE->fetch(PDO::FETCH_ASSOC)) {
								if ($enreg['Enfant'] != $Mere) {
									add_pers($enreg);				
								}
							}
						}
						$nb_refs = count($refs);
						if ($nb_refs > $oncles_tantes_max + 1) {
							$cousins_min = $oncles_tantes_max + 1;
							$cousins_max  = $nb_refs - 1;
						}
					}
			}

				// Récupération des cousins issus de germain
				/*
				if (($T_Parente == 'CG') or ($T_Parente == 'CI')) {
					
				}
				*/
				
				switch ($T_Parente) {
					case 'OT' : $min = $oncles_tantes_min; $max = $oncles_tantes_max; break;
					case 'CG' : $min = $cousins_min; $max = $cousins_max; break;
				}
				
				$echo_modif = Affiche_Icone('fiche_edition',my_html($LG_modify)).'</a><br />';
				$nb_refs = count($refs);
				if ($debug) {
					var_dump($refs);
					var_dump($nb_refs);
				}
				if ($refs == '') $nb_refs = 0;
				if ($nb_refs) {
					for ($nb=$min; $nb<=$max; $nb++) {
						affiche_pers($nb);
					}
				}
				// Pour les cousins, on va récupérer les enfants des oncles et tantes
			}
		}
	}

	switch ($T_Parente) {
		case 'OT' : echo '<br /><a href="Parentees.php?TP=CG&amp;Refer='.$Refer.'"> '.my_html($LG_Menu_Title['Pers_Cousins']).'</a>'; break;
		case 'CG' : echo '<br /><a href="Parentees.php?TP=OT&amp;Refer='.$Refer.'"> '.my_html($LG_Menu_Title['Pers_Uncles']).'</a>'; break;
	}

	// Formulaire pour le bouton retour
	Bouton_Retour($lib_Retour,'?'.Query_Str());

	Insere_Bas($compl);
}
?>
</body>
</html>