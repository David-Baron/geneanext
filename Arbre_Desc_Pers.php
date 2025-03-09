<?php

//=====================================================================
// Arbre descendant d'une personne
// La sortie peut s'effectuer au format HTML ou au format texte
//  JL Servin
// + G Kester : adaptations
// UTF-8
//=====================================================================

session_start();
include('fonctions.php');
$acces = 'L';					// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_desc_tree;			// Titre pour META
$x = Lit_Env();
$index_follow = 'IN';			// NOFOLLOW demandé pour les moteurs
include('Gestion_Pages.php');

$n_personnes = nom_table('personnes');
$n_unions = nom_table('unions');

$conj_demandes = true;

// On restrient le nombre de générations car la mémoire est limitée...
if ($Environnement == 'L') $max_gen_AD = $max_gen_AD_loc;
else $max_gen_AD = $max_gen_AD_int;

// Recherche les infos d'une personne
// Renvoye 1 si personne trouvée
function Retourne_Pers($num) {
	global $References,$Sexes,$Pers,$n_personnes,$n_unions,$Lignes,$Rang_P,$conj_demandes,$Diff_Internet_P, $res, $texte;
	$sql='select Nom, Prenoms, Sexe, Diff_Internet, Ne_Le, Decede_Le from '.$n_personnes.' where reference = '.$num.' limit 1';
	$Conjs_Pers = '';
	$conj = '';
	if ($res = lect_sql($sql)) {
		if ($personne = $res->fetch(PDO::FETCH_NUM)) {
			if ($personne[3] == 'O' or $_SESSION['estPrivilegie']) {
				$Nom     = my_html($personne[0]);
				$Prenoms = my_html($personne[1]);
				if (($Nom != '') or ($Prenoms != '')) {
					$N_P = $Nom.chr(9).$Prenoms.chr(9);
					$References[$Pers] = $num;
					$Sexe_P = $personne[2];
					$Sexes[$Pers] = $Sexe_P;
					$Ne     = affiche_date($personne[4]);
					$Decede = affiche_date($personne[5]);
					$Annees = $Ne.' - '.$Decede;

					// Si les conjoints ont été demandés, on va les chercher
					if ($conj_demandes) {
						$sql = '';
						switch ($Sexe_P) {
						  case 'm' : $sql = 'select Conjoint_2, Reference from ' . $n_unions . ' where Conjoint_1 = '.$num; break;
						  case 'f' : $sql = 'select Conjoint_1, Reference from ' . $n_unions . ' where Conjoint_2 = '.$num; break;
						}
						if ($sql != '') {
							$sql .= ' order by Maries_Le';
							if ($res = lect_sql($sql)) {
								while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
									if (Get_Nom_Prenoms($enreg[0],$Nom,$Prenoms)) {
										if ($Diff_Internet_P == 'O' or $_SESSION['estPrivilegie']) {
											if ($Conjs_Pers != '') $Conjs_Pers .= ', ';
											$Conjs_Pers = $Conjs_Pers.$Nom . ' ' . $Prenoms . '&nbsp;';
											if (!$texte) 
												$Conjs_Pers .= Affiche_Icone_Lien('href="Arbre_Noyau.php?Reference='.$enreg[1].'"','groupe','Noyau').'&nbsp;';
										}
									}
						        }
					        }
						}
					}

					$Lignes[$Pers] = $Rang_P.' '.$N_P.'( '.$Annees.' )'.'/#'.$References[$Pers].'\\#'.$Conjs_Pers;

					++$Pers;
					return 1;
				}
			}
		}
		else {
			return 0;
		}
	}
}

// Cherche les enfants d'une personne et les charge
function Retourne_Enfants($num,$sexe_pers) {
	global $Rangs,$Rang_P,$Pers,$References;
	$Ref_P = $References[$num];
	$RangP = $Rangs[$num];
	if (($sexe_pers == 'm') or ($sexe_pers == 'f')) {
		$sql = 'select Enfant, Rang from '.nom_table('filiations').' where ';
		if ($sexe_pers == 'm') $sql .= 'Pere = '.$Ref_P.' order by Mere, Rang';
		if ($sexe_pers == 'f') $sql .= 'Mere = '.$Ref_P.' order by Pere, Rang';
		$res = lect_sql($sql);
		$num = 0; // num est un rang fictif
		$nb_enfants = $res->rowCount();
		while ($row = $res->fetch(PDO::FETCH_NUM)) {
			$Enfant = $row[0];
			$num++;
			// S'il y a plus de 9 enfants, on met le numéro sur 2 caractères afin de corriger les problèmes de tri (1,10,2 ==> 01,02,10)
			if (($nb_enfants > 9) and ($num < 10)) 
				$xnum = '0'.$num;
			else
				$xnum = $num;
			//$Rangs[$Pers] = $RangP.".".$xnum;
			$Rang_P = $RangP.'.'.$xnum;
			$Rangs[$Pers] = $Rang_P;
			$x = Retourne_Pers($Enfant);
		}
	}
}

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer','N');

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();

$conj_demandes = 0;
if (isset($_POST['conj_demandes'])) $conj_demandes = true;
if ($texte) {
	$avec_conjoints = Recup_Variable('avec_conjoints','C',1);
	if ($avec_conjoints) $conj_demandes = true;
}

$comp_texte = '';
if ($conj_demandes) $comp_texte .= '&amp;avec_conjoints=O';

$compl = Ajoute_Page_Info(600,150);
if (!$is_bot) {
	$compl .= Affiche_Icone_Lien('href="'.my_self().'?Refer='.$Refer.'&amp;texte=O'.$comp_texte.'"','text',$LG_printable_format).'&nbsp;';
}

if (! $texte) Insere_Haut('Arbre descendant',$compl,'Arbre_Desc_Pers',$Refer);
else          Insere_Haut_texte ('&nbsp;');

if (! $texte) {
	echo '<form action="'.my_self().'?Refer='.$Refer.'" method="post">'."\n";
	echo '<table border="0" width="60%" align="center">'."\n";
	echo '<tr align="center">';

	echo '<td class="rupt_table"><label for="conj_demandes">'.$LG_Tree_Show_Partners.'</label>&nbsp;:&nbsp;'."\n";
	echo '<input type="checkbox"';
	if ($conj_demandes) echo ' checked="checked"';
	echo ' id="conj_demandes" name="conj_demandes" value="1"/></td>'."\n";

	echo '<td class="rupt_table"><input type="submit" value="'.$LG_Tree_Show_Tree.'"/>';
	echo '</td>'."\n";
	echo '</tr></table>';
	echo '<input type="hidden" id="memo_etat" name="memo_etat"/>';
	echo '</form>'."\n";
}

$Debut    = 0;
$Fin      = 0;
$Pers     = 0;
$Rangs[0] = 1;

// On part de la personne demandée
$x = Retourne_Pers($Refer);
if (!$x) {
	aff_erreur('Personne non trouvée');
}
else {
	$x = Retourne_Enfants(0,$Sexes[0]);
	$nb_Ajouts = 1;

	// On balaye tant que l'on trouve des enfants par rapport à la génération -1
	$nb_gen = 1;
	while (($nb_Ajouts > 0) and ($nb_gen < $max_gen_AD)) {
		$Debut = $Fin + 1;
		$Fin   = $Pers - 1;
		for ($nb = $Debut;$nb <= $Fin; $nb++) {
			$x = Retourne_Enfants($nb,$Sexes[$nb]);
		}
		$nb_Ajouts = $Pers - $Debut - 1;
		$nb_gen++;
	}

	$res->closeCursor();

	// Libération de la mémoire
	unset($References);
	unset($Sexes);
	unset($Rangs);

	// Tri des lignes mémorisées ; en majeur : l'arborescence des rangs
	sort($Lignes);

	//for ($nb = 0; $nb < $Pers; $nb++) echo 'Ligne : '.$Lignes[$nb].'<br />';

	// Préparation des liens pour optimisation
	$fin_arbres_asc = '><img src="'.$chemin_images_icones.$Icones['arbre_asc'].'" border="0" title="'.$LG_assc_tree.'" alt="'.$LG_assc_tree.'"/></a>';
	$fin_arbres_desc = '><img src="'.$chemin_images_icones.$Icones['arbre_desc'].'" alt="'.$LG_desc_tree.'" border="0" title="'.$LG_desc_tree.'"/></a>'."\n";
	
	// Préparation image ligne pour optimisation
	if ($texte)
		$img_ligne = '&nbsp;&nbsp;<img src="'.$chemin_images_icones.$Icones['ligne_vert'].'" border="0" title="" alt="ligne"/>';
	else {
		$img_ligne_v = '&nbsp;<img src="'.$chemin_images_icones.$Icones['fleche_bas'].'" border="0" title="" alt="ligne"/>';
		$img_ligne = '&nbsp;<img src="'.$chemin_images_icones.$Icones['couple_donne'].'" border="0" title="" alt="ligne"/>';
	}

	//$icone_Puis = Affiche_Icone('couple_donne','puis').'&nbsp;'."\n";

	for ($nb = 0;$nb < $Pers; $nb++) {
		// Il faut exclure la référence et faire le lien...
		$Ligne = $Lignes[$nb];
		$pos_Ref   = strrpos($Ligne, '/#');
		$pos_Conj =  strrpos($Ligne, '\\#');
		// On ne retrouve pas la référence...
		if ($pos_Ref === false) {
			echo $Ligne;
		}
		else {
			$pParO = strpos($Ligne, '(');
			$pParF = strpos($Ligne, ')',$pParO);
			$posb = strpos($Ligne, ' ');
			$Ref  = substr($Ligne,$pos_Ref+2,$pos_Conj-2-$pos_Ref);
			// Compte le nombre de points avec le blanc et affiche des traits à la place
			$num = substr($Ligne,0,$posb);
			$nbp = substr_count($num, '.');
			if ($texte) {
				for ($nbi = 0; $nbi < $nbp; $nbi++)
					echo $img_ligne;
			}
			else {
				for ($nbi = 0; $nbi < $nbp-1; $nbi++)
					echo $img_ligne_v;
				if ($nbp > 0) echo $img_ligne;
			}
			//echo "pos $pos posb $posb pParO $pParO pParF $pParF Ref $Ref <br />";
			if (! $texte) {
				echo $num.'&nbsp;'.
				'<a '.Ins_Ref_Pers($Ref).'>'. substr($Ligne,$posb+1,$pParO-$posb-2). '</a>&nbsp;'.
					substr($Ligne,$pParO,$pParF-$pParO+1).
					"\n";
				echo '&nbsp;&nbsp;<a '.Ins_Ref_Arbre($Ref).$fin_arbres_asc;
				echo '&nbsp;<a '.Ins_Ref_Arbre_Desc($Ref).$fin_arbres_desc;
			}
			else {
				echo substr($Ligne,0,$posb).'&nbsp;'.
					substr($Ligne,$posb+1,$pParO-$posb-2).
					'&nbsp;'.
					substr($Ligne,$pParO,$pParF-$pParO+1).
					"\n";
			}
			// On affiche les conjoints
			if ($conj_demandes) {
				$conjs = substr($Ligne,$pos_Conj+2);
				if ($conjs != '') echo '&nbsp;&nbsp;x&nbsp;&nbsp;'.$conjs;
			}
		}
	echo "<br />\n";
	}

	unset($Lignes);

	if (($nb_gen == $max_gen_AD) and ($nb_Ajouts > 0)) {
		echo '<br />'.Affiche_Icone('tip',my_html($LG_tip)).
		 my_html($LG_LPersG_limited_max_gen_1.$max_gen_AD.$LG_LPersG_limited_max_gen_2).
					'<a href="Vue_Personnalisee.php">'.my_html($LG_LPersG_limited_max_gen_3).'</a>'."\n";
	}
}

if (! $texte) Insere_Bas($compl);

?>
</body>
</html>