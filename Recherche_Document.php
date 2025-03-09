<?php

//=====================================================================
// Recherche dans les documents, quel que soit l'objet associé
// Sortie possible :
//   - à l'écran avec les liens vers les personnes
//   - au format texte pour impression
//   - au format csv pour un import dans un tableur
//
// (c) JLS
// UTF-8
//=====================================================================

session_start();

include('fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok','annuler',
						'Recherche','Nature','Sortie','Type_Doc',
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
$acces = 'L';
$titre = $LG_Menu_Title['Find_Doc'];		// Titre pour META
$niv_requis = 'C';							// Page pour contributeur minimum
$x = Lit_Env();
include('Gestion_Pages.php');

// Verrouillage de la gestion des documents sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = '';

if ($est_gestionnaire) {

	if ($bt_OK) Ecrit_Entete_Page($titre,$contenu,$mots);

	if ($Sortie == 't') {
		Insere_Haut_texte ($titre);
	}
	else {
		$compl = Ajoute_Page_Info(600,260);
		Insere_Haut($titre,$compl,'Recherche_Document','');
	}

	//Demande de recherche
	if ($bt_OK) {

		//include_once('Commun_Rech_Com_Util_Docs.php');

		$Recherche = Secur_Variable_Post($Recherche,80,'S');
		$Nature    = Secur_Variable_Post($Nature,3,'S');
		$Sortie    = Secur_Variable_Post($Sortie,1,'S');

		echo 'Recherche <br />';
		$criteres = '';
		if ($Nature != '-') {
			echo '- '.my_html(LG_DOC_SCH_ON).' '.$Natures_Docs[$Nature].'<br />';
			$criteres .= ' and Nature_Document = "'.$Nature.'" ';
		}
		if ($Type_Doc != '-') {
			echo '- '.my_html(LG_DOC_SCH_TYPE).' '.$Type_Doc.'<br />';
			$criteres .= 'and d.Id_Type_Document = '.$Type_Doc.' ';
		}
		if ($Recherche != '') {
			echo '- '.my_html(LG_DOC_SCH_TITLES).' '.$Recherche.'<br />';
			$criteres .= 'and  upper(Titre) like "%'.trim(strtoupper($Recherche)).'%"';
		}

		$sql = 'select Id_Document, Nature_Document, Titre, Nom_Fichier, '.
				'Diff_Internet, Date_Creation, Date_Modification, Libelle_Type '.
				'from '.nom_table('documents').' d,'.nom_table('types_doc').' t '.
				' where d.Id_Type_Document = t.Id_Type_Document '.
				$criteres.
				' order by Titre';

		$res = lect_sql($sql);
		$nb = $res->RowCount();
		//$plu = pluriel($nb);
		echo $nb.' '.my_html(LG_DOC_SCH_FOUND).'<br /><br />';
		//$num_fields = $res->field_count;

      	$echo_modif = Affiche_Icone('fiche_edition',my_html($LG_modify)).'</a>';
      	$num_lig = 0;
      	$base_ref = Get_Adr_Base_Ref();
		
		$nom_fic_rech = 'recherche_documents.csv';

		while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
			$num_lig++;
			$refDoc = $enreg['Id_Document'];
			$Titre = $enreg['Titre'];
			$Nom_Fichier = $enreg['Nom_Fichier'];
			
			switch ($Sortie) {
				case 'e' :
							echo '<a href="Fiche_Document.php?Reference=' . $refDoc . '">'.my_html($Titre).'</a>';
							echo ' (' . $Natures_Docs[$enreg['Nature_Document']] . ")\n";
							echo '&nbsp;<a href="Edition_Document.php?Reference='. $refDoc . '">'.$echo_modif;
							$le_type = Get_Type_Mime($enreg['Nature_Document']);
							$chemin_docu = get_chemin_docu($enreg['Nature_Document']);
							echo '&nbsp; &nbsp;'.Affiche_Icone_Lien('href="'.$chemin_docu.$Nom_Fichier.'" type="'.$le_type.'"','oeil',LG_DOC_SCH_SEE,'n');
							echo '<br />'."\n";
							break;
				case 't' :
							echo my_html($Titre);
							echo ' (' . $Natures_Docs[$enreg['Nature_Document']] . ")\n";
							echo '<br />'."\n";
							break;
				case 'c' : if ($num_lig == 1) {
								$nom_fic = $chemin_exports.$nom_fic_rech;
								$fp = ouvre_fic($nom_fic,'w+');
								$ligne = LG_DOC_SCH_HEADER_CSV;
								ecrire($fp,$ligne);
							}
							$ligne = $Natures_Docs[$enreg['Nature_Document']].';'.
									$Titre.';'.
									$Nom_Fichier.';'.
									$enreg['Diff_Internet'].';'.
									DateTime_Fr($enreg['Date_Creation']).';'.
									DateTime_Fr($enreg['Date_Modification']).';'.
									$enreg['Libelle_Type'].';';
							ecrire($fp,$ligne);
							break;
			}

		}
		if (($Sortie == 'c') and ($num_lig)) {
			fclose($fp);
			echo '<br />'.my_html($LG_csv_available_in).' <a href="'.$chemin_exports.$nom_fic_rech.'" target="_blank">'.$nom_fic_rech.'</a><br />'."\n";
		}

		// Nouvelle recherche
		if ($Sortie != 't') {
		    echo '<form id="nouvelle" method="post" action="'.my_self().'">'."\n";
		    aff_origine();
		    echo '<br />';
		   	echo '<div class="buttons">';
		   	echo '<button type="submit" class="positive">'.
		        '<img src="'.$chemin_images_icones.$Icones['chercher'].'" alt=""/>'.my_html(LG_DOC_SCH_NEW).'</button>';
			echo '</div>';
		    echo '</form>'."\n";
		}
    }

	// Première entrée : affichage pour saisie
	if ((!$bt_OK) && (!$bt_An)) {

		echo '<form id="saisie" method="post" action="'.my_self().'">'."\n";
		aff_origine();

		$larg_titre = 30;
		echo '<table width="80%" class="table_form">'."\n";
		ligne_vide_tab_form(1);

		colonne_titre_tab(LG_DOC_SCH_LB_TITLE);
		echo '<input type="text" name="Recherche" size="80"/></td></tr>'."\n";
		
		colonne_titre_tab(LG_DOC_SCH_LB_NATURE);
		echo '<select name="Nature" size="1">';
		echo '<option value="-">-- Toutes --</option>'."\n";
		foreach ($Natures_Docs as $key => $value) {
			echo '<option value="'.$key.'">'.$value.'</option>'."\n";
		}
		echo '</select>'."\n";
		echo '</td></tr>'."\n";
		
		colonne_titre_tab(LG_DOC_SCH_LB_TYPE);
		$sql = 'select Id_Type_Document, Libelle_Type from '.nom_table('types_doc').' order by Libelle_Type';
		echo '<select name="Type_Doc" size="1">';
		echo '<option value="-">-- '.my_html($LG_All).' --</option>'."\n";
		if ($res = lect_sql($sql)) {
			while ($row = $res->fetch(PDO::FETCH_NUM)) {
				echo '<option value="'.$row[0].'">'.my_html($row[1]).'</option>'."\n";
			}
		}
		$res->closeCursor();
		echo '</select>'."\n";	echo '</td></tr>'."\n";
		echo '</td></tr>'."\n";

		colonne_titre_tab($LG_Ch_Output_Format);
		affiche_sortie(true);
		echo '</td></tr>'."\n";

		ligne_vide_tab_form(1);
		bt_ok_an_sup($lib_Rechercher,$lib_Annuler,'','');
		ligne_vide_tab_form(1);

		echo '</table>'."\n";
		echo '</form>'."\n";

	}

	if ($Sortie != 't') Insere_Bas($compl);
}
else echo $LG_function_noavailable_profile."\n";

?>
</body>
</html>