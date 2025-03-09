<?php

//=====================================================================
// Descendance directe d'une personne
// La sortie peut s'effectuer au format HTML ou au format texte
// JL Servin
// UTF-8
//=====================================================================

session_start();
include('fonctions.php');
$acces = 'L';								// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Direct_Desc'];		// Titre pour META
$x = Lit_Env();
$index_follow = 'IN';						// NOFOLLOW demandé pour les moteurs
include('Gestion_Pages.php');

$n_filiations = nom_table('filiations');
$n_personnes = nom_table('personnes');
$n_unions = nom_table('unions');

$conj_demandes = true;

// Recherche les infos d'une personne
// Renvoye 1 si personne trouvée
function Affiche_Pers($num) {

	global $conj_demandes, $n_personnes, $n_unions, $Diff_Internet_P, $texte, $fin_arbres_asc, $fin_arbres_desc, $LG_Data_noavailable_profile;

	$sql = 'select Reference, Nom, Prenoms, Diff_Internet, Ne_le, Decede_Le, Sexe '.
			' from '.$n_personnes.
			' where Numero = "'.$num.'"	 limit 1';
	if ($res = lect_sql($sql)) {
		if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
			if (($_SESSION['estGestionnaire']) or ($enreg[3] == 'O')) {
				echo $num.'&nbsp;:&nbsp;';
				$Ref_Pers = $enreg[0];
				if (! $texte)
					echo '<a '.Ins_Ref_Pers($Ref_Pers).'>'.my_html($enreg[2]. ' '.$enreg[1]).'</a>';
				else
					echo my_html($enreg[2]. ' '.$enreg[1]);
				$Ne = $enreg[4];
				$Decede = $enreg[5];
				if (($Ne != '') or ($Decede != '')) {
					echo '&nbsp;(';
					if ($Ne != '') echo '&deg; '.Etend_date($Ne);
					if ($Decede != '') {
						if ($Ne != '') echo ', ';
						echo '+ '.Etend_date($Decede);
					}
					echo ')';
				}
				if (!$texte) {
					echo '&nbsp;&nbsp;<a '.Ins_Ref_Arbre($Ref_Pers).$fin_arbres_asc;
					echo '&nbsp;<a '.Ins_Ref_Arbre_Desc($Ref_Pers).$fin_arbres_desc;
				}

				// Si les conjoints ont été demandés, on va les chercher
				if ($conj_demandes) {
					$sql = '';
					switch ($enreg[6]) {
					  case 'm' : $sql = 'select Conjoint_2 from ' . $n_unions . ' where Conjoint_1 = '.$Ref_Pers; break;
					  case 'f' : $sql = 'select Conjoint_1 from ' . $n_unions . ' where Conjoint_2 = '.$Ref_Pers; break;
					}
					if ($sql != '') {
						$sql .= ' order by Maries_Le';
						$Conjs_Pers = '';
						if ($res = lect_sql($sql)) {
							while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
								if (Get_Nom_Prenoms($enreg[0],$Nom,$Prenoms)) {
									if ($Diff_Internet_P == 'O' or $_SESSION['estPrivilegie']) {
										if ($Conjs_Pers != '') $Conjs_Pers .= ', ';
										$Conjs_Pers = $Conjs_Pers.$Nom . ' ' . $Prenoms;
									}
								}
							}
						}
						if ($Conjs_Pers != '')
							echo ' x '.$Conjs_Pers;
					}
				}
			}
			else echo my_html($LG_Data_noavailable_profile);
		}
		echo '<br />'."\n";
	}
}

// Recup de la variable passée dans l'URL : référence de la personne
$Numero = Recup_Variable('Numero','N');

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

$compl = Ajoute_Page_Info(600,250) .
		Affiche_Icone_Lien('href="'.my_self().'?Numero='.$Numero.'&amp;texte=O'.$comp_texte.'"','text',$LG_printable_format).'&nbsp;';

if (! $texte) Insere_Haut($titre,$compl,'Desc_Directe_Pers',$Numero);
else          Insere_Haut_texte ('&nbsp;');


if (! $texte) {
	echo '<form action="'.my_self().'?Numero='.$Numero.'" method="post">'."\n";
	echo '<table border="0" width="60%" align="center">'."\n";
	echo '<tr align="center">';

	echo '<td class="rupt_table">'.my_html($LG_Tree_Show_Partners).'&nbsp;:&nbsp;'."\n";
	echo '<input type="checkbox"';
	if ($conj_demandes) echo ' checked="checked"';
	echo ' name="conj_demandes" value="1"/></td>'."\n";

	echo '<td class="rupt_table"><input type="submit" value="'.my_html($LG_Tree_Show_Desc).'"/>';
	echo '</td>'."\n";
	echo '</tr></table>';
	echo '<input type="hidden" name="memo_etat"/>';
	echo '</form>'."\n";
}

$fin_arbres_asc = '><img src="'.$chemin_images_icones.$Icones['arbre_asc'].'" border="0" title="'.$LG_assc_tree.'" alt="'.$LG_assc_tree.'"/></a>';
$fin_arbres_desc = '><img src="'.$chemin_images_icones.$Icones['arbre_desc'].'" alt="'.$LG_desc_tree.'" border="0" title="'.$LG_desc_tree.'"/></a>&nbsp;';

do {
	Affiche_Pers($Numero);
	$Numero = floor($Numero / 2);
} while ($Numero >= 1);

if (! $texte) Insere_Bas($compl);

?>
</body>
</html>