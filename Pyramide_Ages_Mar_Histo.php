<?php

//=====================================================================
// Affichage de l'historique de l'âge au 1er mariage 
// pour les femmes et les hommes
// en fonction de l'année de naissance
// (c) JLS
// UTF-8
//=====================================================================

session_start();

$tab_variables = array('annuler','Horigine');
foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}

include('fonctions.php');

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Retour),'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de la variable passée dans l'URL : type d'historique
$Type_Histo = Recup_Variable('Type','C','UF');

// Gestion standard des pages
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
// Titre pour META
switch ($Type_Histo) {
	case 'U' : $titre = $LG_Menu_Title['Histo_First_Wedding']; 
				$Ch_Histo_Age_Youngest_M = LG_CH_HISTO_AGE_YOUNGEST_M;
				$Ch_Histo_Age_Youngest_W = LG_CH_HISTO_AGE_YOUNGEST_W;
				break;
	case 'F' : $titre = $LG_Menu_Title['Histo_First_Child'];
				$Ch_Histo_Age_Youngest_M = LG_CH_HISTO_AGE_YOUNGEST_FATH;
				$Ch_Histo_Age_Youngest_W = LG_CH_HISTO_AGE_YOUNGEST_MOTH;
				break;
}
$x = Lit_Env();
$index_follow = 'ON';		// NOFOLLOW demandé pour les moteurs
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600,220);

function affiche($donnee) {
	global $tot;
    if ($tot) echo '<b>'.$donnee.'</b>';
    else      echo $donnee;
}


Insere_Haut($titre,$compl,'Pyramide_Ages_Mar_Histo','');

$bloc_annees = 20;
$larg_maxi = 200;

$ref_annee_anc = 0;
$nb_F_Tot      = 0;
$nb_H_Tot      = 0;
$moy_F_Tot     = 0;
$moy_H_Tot     = 0;

// Zones pour les cadet(te)s
$min_mois_h = 9999;
$min_mois_f = 9999;
$ref_min_h = 0;
$ref_min_f = 0;

$n_personnes = nom_table('personnes');
switch ($Type_Histo) {
	case 'U' : // Récupération des personnes pour lesquelles on peut calculer un âge au mariage
				$sql = 'SELECT Sexe, Ne_le, Maries_Le, p.Reference  '.
						'FROM '.$n_personnes.' p, '.nom_table('unions').' u '.
						"WHERE Ne_le LIKE '_________L' ".
						"AND Sexe in ('f','m') ".
						'AND (p.Reference = u.Conjoint_1 or p.Reference = u.Conjoint_2) '.
						"AND Maries_Le LIKE '_________L' ";
				if (!$est_privilegie) {
					$sql = $sql ."and Diff_Internet = 'O' ";
				}
				$sql = $sql . 'order by Ne_Le, p.Reference, Maries_Le';
				break;
	case 'F' : // Récupération des personnes pour lesquelles on peut calculer un âge de 1ère paternité / maternité
				$sql = 'SELECT p.Sexe, p.Ne_le, e.Ne_le, p.Reference  '.
						'FROM '.$n_personnes.' p, '.nom_table('filiations').' f, '.$n_personnes.' e '.
						"WHERE p.Ne_le LIKE '_________L' ".
						"AND p.Sexe in ('f','m') ".
						'AND (p.Reference = f.Pere or p.Reference = f.Mere) '.
						'AND e.Reference = f.Enfant '.
						"AND e.Ne_le LIKE '_________L' ";
				if (!$est_privilegie) {
					$sql = $sql ."and p.Diff_Internet = 'O' ";
				}
				$sql = $sql . 'order by p.Ne_Le, p.Reference, e.Ne_Le';
				break;
}
	
$anc_ref = -1;

$vide = true;
if ($res = lect_sql($sql)) {
	while ($row = $res->fetch(PDO::FETCH_NUM)) {
		$vide = false;
		$ref = $row[3];
		// On ne traite que sur changement de référence
		if ($ref != $anc_ref) {
			$anc_ref = $ref;
			$sexe = $row[0];
			$ne = $row[1];
			$mois = Age_Mois($ne,$row[2]);
			$annee = intval(substr($ne,0,4));
			$ref_annee = intval($annee / $bloc_annees) * $bloc_annees;
			if ($sexe == 'f') {
				// Cadette
				if ($mois < $min_mois_f) {
					$min_mois_f = $mois;
					$ref_min_f = $ref;
				}
				if (!isset($nb_femmes[$ref_annee])) {
					$nb_femmes[$ref_annee] = 0;
					$mois_femmes[$ref_annee] = 0;
				}
				$nb_femmes[$ref_annee] ++;
				$mois_femmes[$ref_annee] += $mois;
			}	
			else {
				// Cadet
				if ($mois < $min_mois_h) {
					$min_mois_h = $mois;
					$ref_min_h = $ref;
				}
				if (!isset($nb_hommes[$ref_annee])) {
					$nb_hommes[$ref_annee] = 0;
					$mois_hommes[$ref_annee] = 0;
				}
				$nb_hommes[$ref_annee] ++;
				$mois_hommes[$ref_annee] += $mois;
			}
		}
	}
}
if ($debug) {
	echo 'nb_femmes, mois_femmes, nb_hommes, mois_hommes : ';
	var_dump($nb_femmes, $mois_femmes, $nb_hommes, $mois_hommes);
}

if (!$vide) {
	$larg_sexe = '40%';
	echo '<br />';
	echo '<table width="90%" border="0" class="classic" align="center" >'."\n";
	echo '<tr>';
	echo '<th width="'.$larg_sexe.'" colspan="2">'.LG_CH_HISTO_AGE_MEN.'</th>';
	echo '<th>'.LG_CH_HISTO_AGE_WED.'</th>';
	echo '<th width="'.$larg_sexe.'" colspan="2">'.LG_CH_HISTO_AGE_WOMEN.'</th>';
	echo '</tr>'."\n";

	// Détermination des années de naissance mini et maxi
	// et des moyennes
	$min_age = 9999;
	$max_age = 0;
	$max_moy_hommes = 0;
	$max_moy_femmes = 0;
	$tot_mois_hommes = 0;
	$tot_mois_femmes = 0;
	$tot_nb_hommes = 0;
	$tot_nb_femmes = 0;

	foreach ($nb_femmes as $key => $value) {
		$mois = $mois_femmes[$key];
		$nb = $nb_femmes[$key];
		$moyennes_femmes[$key] = $mois/$nb;
		$min_age = min($key, $min_age);
		$max_age = max($key, $max_age);
		$tot_mois_femmes += $mois;
		$tot_nb_femmes += $nb;
		if ($debug) echo 'tot mois femmes : '.$tot_mois_femmes.'<br />';
	}
	$max_moy_femmes = round(max($moyennes_femmes));
	foreach ($nb_hommes as $key => $value) {
		$mois = $mois_hommes[$key];
		$nb = $nb_hommes[$key];
		$moyennes_hommes[$key] = $mois/$nb;
		$min_age = min($key, $min_age);
		$max_age = max($key, $max_age);
		$tot_mois_hommes += $mois;
		$tot_nb_hommes += $nb;
		if ($debug) echo 'tot mois hommes : '.$tot_mois_hommes.'<br />';
	}
	$max_moy_hommes = round(max($moyennes_hommes));
	$max_moy = max($max_moy_femmes, $max_moy_hommes);
	if ($debug) {
		echo 'moyennes_femmes, moyennes_hommes, min_age, max_age, max_moy_femmes, max_moy_hommes; max_moy ';
		var_dump($moyennes_femmes, $moyennes_hommes, $min_age, $max_age, $max_moy_femmes, $max_moy_hommes, $max_moy);
	}
	$deb_barre_h = '<img src="'.$barre_homme.'" height="15" width="';
	$deb_barre_f = '<img src="'.$barre_femme.'" height="15" width="';

	// Affichage
	for ($nb = $min_age; $nb <= $max_age; $nb += $bloc_annees) {
		echo '<tr>';	
		// Année de naissance
		$borne_max = $nb + $bloc_annees - 1;
		
		// Homme : moyenne en barre et moyenne en nombre
		if (isset($moyennes_hommes[$nb])) {
			$moyenne = round($moyennes_hommes[$nb]);
			$age = Decompose_Mois($moyenne);
			$larg = intval($moyenne/$max_moy*$larg_maxi);
			echo '<td>'.$age.'</td>';
			echo '<td align="right">';
			if ($debug) echo $nb_hommes[$nb].' H ';
			echo $deb_barre_h.$larg.'" alt="Moyenne" title="'.$nb_hommes[$nb].' '.LG_CH_HISTO_AGE_PERS.'"/>';
			if ($debug) echo $moyenne.'/'.$moyennes_hommes[$nb];
			echo '</td>';
		}
		else {
			echo '<td>-</td><td>-</td>';
			
		}

		echo '<td align="center">';
		echo '<a href="Histo_Ages_Mariage.php?Type='.$Type_Histo.'&amp;Debut='.$nb.'&amp;Fin='.$borne_max.'" title="'.LG_CH_HISTO_REPARTITION.'">'.$nb.'-'.$borne_max.'</a>';
		echo '</td>';	

		// Femme : moyenne en barre et moyenne en nombre
		if (isset($moyennes_femmes[$nb])) {
			$moyenne = round($moyennes_femmes[$nb]);
			$age = Decompose_Mois($moyenne);
			$larg = intval($moyenne/$max_moy*$larg_maxi);
			echo '<td>';
			if ($debug) echo $nb_femmes[$nb].' F ';
			echo $deb_barre_f.$larg.'" alt="Moyenne" title="'.$nb_femmes[$nb].' '.LG_CH_HISTO_AGE_PERS.'"/>';
			if ($debug) echo $moyenne.'/'.$moyennes_femmes[$nb];
			echo '</td><td>'.$age;
			echo '</td>';
		}
		else {
			echo '<td>-</td><td>-</td>';
			
		}
		echo '</tr>'."\n";
	}
	echo '<tr>';
	echo '<td><b>'.$age.'</b></td>';
	echo '<td align="right">';
	$moyenne = $tot_mois_hommes/$tot_nb_hommes;
	$age = Decompose_Mois($moyenne);
	$larg = intval($moyenne/$max_moy*$larg_maxi);
	if ($debug) echo $tot_nb_hommes.' F ';
	echo $deb_barre_h.$larg.'" alt="Moyenne" title="'.$tot_nb_hommes.' '.LG_CH_HISTO_AGE_PERS.'"/>';
	echo '</td>';
	echo '<td align="center"><b>'.LG_CH_HISTO_AGE_ALL.'</b></td>';
	echo '<td>';
	$moyenne = $tot_mois_femmes/$tot_nb_femmes;
	$age = Decompose_Mois($moyenne);
	$larg = intval($moyenne/$max_moy*$larg_maxi);
	if ($debug) echo $tot_nb_femmes.' H ';
	echo $deb_barre_f.$larg.'" alt="Moyenne" title="'.$tot_nb_femmes.' '.LG_CH_HISTO_AGE_PERS.'"/>';
	echo '</td>';
	echo '<td><b>'.$age.'</b></td>';
	echo '</tr>';
	echo '</table>';

	$res->closeCursor();

	$Nom = '';
	$Prenoms = '';
	if (Get_Nom_Prenoms($ref_min_h,$Nom,$Prenoms)) {
		echo '<br />'.$Ch_Histo_Age_Youngest_M.' : <a '.Ins_Ref_Pers($ref_min_h).'>'.$Prenoms.'&nbsp;'.$Nom.'</a> ('.Decompose_Mois($min_mois_h).')';
	}
	if (Get_Nom_Prenoms($ref_min_f,$Nom,$Prenoms)) {
		echo '<br />'.$Ch_Histo_Age_Youngest_W.' : <a '.Ins_Ref_Pers($ref_min_f).'>'.$Prenoms.'&nbsp;'.$Nom.'</a> ('.Decompose_Mois($min_mois_f).')';
	}
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour,'?'.Query_Str());

Insere_Bas($compl);
?>
</body>
</html>