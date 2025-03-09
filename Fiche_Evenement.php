<?php
//=====================================================================
// Gerard KESTER Mars 2007
//   Affichage d'un évènement
//
// Intégration et ajouts JL Servin
//=====================================================================

// Gestion standard des pages
session_start();
include('fonctions.php');
$acces = 'L';				// Type d'accès de la page : (L)ecture

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Retour),'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$actu = Recup_Variable('actu','C','xo');
$actualite = ($actu === 'o' ? true : false);

// Titre pour META
if ($actualite) $titre = $LG_Menu_Title['New'];
else $titre = $LG_Menu_Title['Event'];

$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();
else {

	// Recup de la variable passée dans l'URL : référence de l'évènement
	$refPar = Recup_Variable('refPar','N');

	$ajout = '';
	if ($actualite) $ajout = '&amp;actu=o';

	$compl = Ajoute_Page_Info(600,150);
	if ($est_gestionnaire) {
		$compl .= Affiche_Icone_Lien('href="Edition_Evenement.php?refPar='.$refPar.$ajout.'"','fiche_edition',$LG_modify) . '&nbsp;';
	}
	Insere_Haut($titre,$compl,'Fiche_Evenement','');

	//
	//  ========== Programme principal ==========
	//
	$requete = 'SELECT * FROM ' . nom_table('evenements') . ' e, ' . nom_table('types_evenement') .' t'.
			 " WHERE reference = $refPar" .
			 ' AND e.Code_Type = t.Code_Type limit 1';
	if ($result = lect_sql($requete)) {
		if ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
			//  Mise en place des donnees
			$LibelleTypeLu = $enreg['Libelle_Type'];
			$nomZone       = LectZone($enreg['Identifiant_zone'],$enreg['Identifiant_Niveau']);
			$titreLu       = $enreg['Titre'];
			$dDebLu        = $enreg['Debut'];
			$dFinLu        = $enreg['Fin'];
			$statutLu      = $enreg['Statut_Fiche'];
			$objetCibleLu  = $enreg['Objet_Cible'];
			if ($debug) var_dump($enreg);

			$Type_Ref = 'E';

			// Affichage de l'image par défaut
			$image = Rech_Image_Defaut($refPar,$Type_Ref);
			if ($image != '') {
				Aff_Img_Redim_Lien ($chemin_images_util.$image,150,150,'image_evt');
				echo '<br />'.$titre_img.'<br /><br />'."\n";
			}

			echo '<br />';
			$larg_titre = 25;
			echo '<table width="80%" class="table_form" align="center">'."\n";
			echo colonne_titre_tab($LG_Event_Title).$titreLu.'</td></tr>'."\n";
			echo colonne_titre_tab($LG_Event_Type).'<a href="Fiche_Type_Evenement.php?code='.$enreg['Code_Type'].'">'.$LibelleTypeLu.'</a></td></tr>'."\n";
			if ($nomZone != '') 
				echo colonne_titre_tab($LG_Event_Where).$nomZone.'</td></tr>'."\n";
			if (($dDebLu != '') or ($dFinLu != ''))
				echo colonne_titre_tab($LG_Event_When).Etend_2_dates($dDebLu , $dFinLu).'</td></tr>'."\n";
			echo '</table>';

			//  ===== Affichage du commentaire
			if (Rech_Commentaire($refPar,$Type_Ref)) {
				Aff_Comment_Fiche($Commentaire,$Diffusion_Commentaire_Internet);
			}

			// Conditionner l'affichage par la cible de l'évènement

			//  === Affichage des liens avec des personnes
			aff_lien_pers($refPar,'N');

			//  === Affichage des liens avec des filiations
			aff_lien_filiations($refPar,'N');

			// === Affichage des liens avec des unions
			aff_lien_unions($refPar,'N');

			//  Documents liés à l'évènement
			$x = Aff_Documents_Objet($refPar, $Type_Ref , 'O');

			// Affichage de la liste des noms pour l'évenement
			if ($objetCibleLu == 'P')
				echo '<br /><a href="'.Get_Adr_Base_Ref().'Liste_Nom_Evenement.php?refPar='.$refPar.'">Liste des noms pour l\'&eacute;v&egrave;nement</a>';

			echo '<br />'."\n";

			// Formulaire pour le bouton retour
			Bouton_Retour($lib_Retour,'?'.$_SERVER['QUERY_STRING']);

			Insere_Bas($compl);
		}
	}
}
?>
</body>
</html>