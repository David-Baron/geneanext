<?php
//=====================================================================
// Ajout multiple de documents
// (c) JLS
// UTF-8
//=====================================================================

session_start();
include('fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok','annuler','Horigine');
foreach ($tab_variables as $nom_variables) {
	if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
	else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok,strlen($lib_Okay),'S');
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Annuler),'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');

// Gestion standard des pages
$acces = 'M';                          		// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Document_Multiple_Add'];     // Titre pour META
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$n_images = nom_table('images');
$n_documents = nom_table('documents');

// Pour les sites gratuits non Premium, on a droit à 5 documents au lieu de 10
$nb_docs = 10;
if (($SiteGratuit) and (!$Premium)) $nb_docs = 5;
	
//Demande de mise à jour
if ($bt_OK) {
	
	Ecrit_Entete_Page($titre,$contenu,$mots);
	
	//$nb_docs = count($_FILES["nom_du_fichier"]["name"]);
	
	$existe_erreur = false;
	$erreur = '';
	
	//$chemin = $chemin_images_util;
	$chemin = $chemin_docs_IMG;
	
	/*
	$l_types_aut = '';
	foreach ($Natures_Docs as $key => $value) {
		$l_types_aut .= $key.'/';
	}
	echo 'l_types_aut : '.$l_types_aut.'<br>';
	*/
	
	for ($nb = 0; $nb < $nb_docs; $nb++) {
		//echo $nb.'<br>';
		//echo $_POST['TitreDoc'][$nb].'<br>';
		
		if (isset($_POST['TitreDoc'][$nb])) $TitreDoc = Secur_Variable_Post($_POST['TitreDoc'][$nb],80,'S');
		else $TitreDoc = '';
		
		if (isset($_FILES["nom_du_fichier"]["name"][$nb]))$nom_fic = Secur_Variable_Post($_FILES["nom_du_fichier"]["name"][$nb],160,'S');
		else $nom_fic = '';
		
		if (isset($_POST['Diff'][$nb])) $Diff = Secur_Variable_Post($_POST['Diff'][$nb],1,'S');
		else $Diff = 'N';
			
		if (isset($_POST['TypeDoc'][$nb])) $TypeDoc = Secur_Variable_Post($_POST['TypeDoc'][$nb],80,'S');
		else $TypeDoc = '';
		
		if ($nom_fic != '') {
			//if ($TitreDoc == '') $TitreDoc = $nom_fic;
			// Détermination du type de fichier
			$type_doc = get_file_type($nom_fic,$_FILES['nom_du_fichier']['type'][$nb]);
			if ($type_doc == '') $type_doc = '---';
			
			// Vérification que le type de fichier fait partie de la liste des types autorisés
			$type_valide = array_key_exists($type_doc,$Natures_Docs);
			if ($debug) {
				echo 'type_doc : '.$type_doc.', ';
				echo 'type_valide : '.$type_valide.'<br>';
			}
			
			if (!$type_valide) {
				$erreur = 'Fichier '.$nom_fic.' : type de fichier non supporté';
				Affiche_Stop($erreur);
			}
				
			// Nettoyage du nom de fichier
			$nom_fic = nettoye_nom_fic($nom_fic);
			
			// Cas des images
			if ($type_doc == 'IMG') {
		
				// Distinguer Image vs. Document de type Image	
				
				/*
				// On controle le remplacement
				// Si le fichier existe déjà et que l'utilisateur n'a pas demandé le remplacement, on génère un warning
				if ($erreur == '') {
					if ($utilise_img) $chemin = $chemin_images_util;
					else              $chemin = $chemin_docs_IMG;
					if ($Remplacer == 'N') {
						if (file_exists($chemin.$nom_fic))
							$erreur = 'Fichier '.$nom_fic.' déjà existant';
					}
				}
				*/
	
				// Contrôle des caractéristiques de l'image
				if ($erreur == '') {
					$erreur = Controle_Charg_Image($nb);
				}
	
				// Erreur constatée sur le chargement
				if ($erreur != '') {
					echo '&nbsp;&nbsp;';
					Affiche_Warning($erreur);
				}
				// Sinon on peut télécharger
				else {
					// Téléchargement du fichier après contrôle
					if (!ctrl_fichier_ko($nb)) {				
						move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'][$nb],$chemin.$nom_fic);
						// la zone nom_du_fichier n'est pas alimentée...
						$nom_du_fichier = $nom_fic;
						// On chmod le fichier si on n'est pas sous Windows
						if (substr(php_uname(), 0, 7) != 'Windows') chmod ($chemin.$nom_fic, 0644);
					}
					else $erreur = 'ko post chargement'; 
				}
			}
			
			// Cas des documents HTML ou des PDF
			$type_doc_htm = false;
			$type_doc_pdf = false;
			if ($type_doc == 'HTM') $type_doc_htm = true;
			if ($type_doc == 'PDF') $type_doc_pdf = true;
			
			if ($type_doc_htm or $type_doc_pdf) {
				
				if ($type_doc_htm) $chemin_interne = $chemin_docs_HTM;
				if ($type_doc_pdf) $chemin_interne = $chemin_docs_PDF;
		
				$erreur = '';				
				
				/*
				// On controle le remplacement
				// Si le fichier existe déjà et que l'utilisateur n'a pas demandé le remplacement, on génère un warning
				if ($Remplacer == 'N') {
					if (file_exists($chemin_interne.$nom_fic))
						$erreur = 'Fichier '.$nom_fic.' déjà existant';
				}
				*/
				
				// Contrôle des caractéristiques du fichier
				if (($erreur == '') and ($type_doc_htm)) {
					$erreur = Controle_Charg_Doc($nb);
				}
	
				// Erreur constatée sur le chargement
				if ($erreur != '') {
					echo '&nbsp;&nbsp;';
					Affiche_Warning($erreur);
				}
				// Sinon on peut télécharger
				else {
					// Téléchargement du fichier après contrôle
					move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'][$nb],$chemin_interne.$nom_fic);
					// la zone nom_du_fichier n'est pas alimentée...
					$nom_du_fichier = $nom_fic;
					// On chmod le fichier si on n'est pas sous Windows
					if (substr(php_uname(), 0, 7) != 'Windows') chmod ($chemin_interne.$nom_fic, 0644);
				}
			}		
		}
		// Si c'est OK pour les fchiers on fait les maj en base 
		if ($debug) {
			echo 'erreur : '.$erreur.'<br>';
			echo 'TitreDoc : '.$TitreDoc.'<br>';
			echo 'nom_fic : '.$nom_fic.'<br>';
			echo 'nom_fic : '.$nom_fic.'<br>';
		}
		if ($erreur == '') {
			// On n'autorise la création que si le nom et la description sont saisis
			if (($TitreDoc != '') and ($nom_fic != '')) {
				$req = 'NULL,"'.$type_doc.'"';
				Ins_Zone_Req($TitreDoc,'A',$req);
				Ins_Zone_Req($nom_fic,'A',$req);
				Ins_Zone_Req($Diff,'A',$req);
				Ins_Zone_Req('current_timestamp','N',$req);
				Ins_Zone_Req('current_timestamp','N',$req);
				Ins_Zone_Req($TypeDoc,'N',$req);
				if ($req != '') {
					$req = 'insert into '.$n_documents.' values('.$req.")";
					$res = maj_sql($req);
				}
			}
		
		}
		else $existe_erreur = true;
	}
	if (!$existe_erreur) {
		maj_date_site();
		//Retour_Ar();
	}
}

// Première entrée : affichage pour saisie
//if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {

	
	$compl = Ajoute_Page_Info(600,220);
	Insere_Haut($titre,$compl,'Create_Multiple_Docs','');

	$req_td = 'SELECT Id_Type_Document, Libelle_Type FROM '.nom_table('types_doc') . ' ORDER BY Libelle_Type';
	$result_td = lect_sql($req_td);
	$types_docs = '';
	while ($enr_type = $result_td->fetch(PDO::FETCH_NUM)) {
		$types_docs .= '<option value="'.$enr_type[0].'">'.$enr_type[1].'</option>';
	}
	if ($types_docs == '') {
		// On va masquer le bouton OK car pas de création possible
		echo '<br><br>';
		Affiche_Warning($LG_Docs_Error_No_Type);
		echo '<br><br>';
		$err_td = true;
	}
	else {
		echo '<br>';
		echo '<form id="saisie" method="post" enctype="multipart/form-data" action="'.my_self().'">'."\n";
		echo '<table class="classic" border="0" width="90%" align="center">';
		echo '<tr>';
		echo '<th>'.$LG_Docs_Title.'</th>';
		echo '<th>'.$LG_Docs_File.'</th>';
		echo '<th>'.$LG_Docs_Doc_Type.'</th>';
		echo '<th>'.$LG_show_on_internet.'</th>';
		echo '</tr>';

		for ($nb = 1; $nb <= $nb_docs; $nb++) {
			echo '<tr>';
			echo '<td><input type="text" size="80" name="TitreDoc[]" class="oblig" /></td>';
			echo '<td><input type="file" name="nom_du_fichier[]" size="50"/></td>';
			echo '<td><select name="TypeDoc[]">'.$types_docs.'</select></td>';
			echo '<td align="center"><input type="checkbox" name="Diff[]" value="O"/></td>';
			echo '</tr>'."\n";
		}
		echo '</table>'."\n";

		echo '<table border="0" width="80%" align="center">';
		bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');
		echo '</table>'."\n";
		
		echo "</form>";
	}

	Insere_Bas($compl);
//}
?>
</body>
</html>