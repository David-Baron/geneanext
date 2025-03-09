<?php
//=====================================================================
// Affichage d'un dépôt
// (c) JLS
// UTF-8
//=====================================================================

// Gestion standard des pages
session_start();
include('fonctions.php');
$acces = 'L';								// Type d'accès de la page : (M)ise à jour, (L)ecture
$niv_requis = 'P';
$titre = $LG_Menu_Title['Repo_Sources'];		// Titre pour META

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler = Secur_Variable_Post($annuler,strlen($lib_Retour),'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$x = Lit_Env();

// Recup de la variable passée dans l'URL : dépôt
$Ident = Recup_Variable('ident','N');
$req_sel = 'select * from ' . nom_table('depots') . ' where Ident = '.$Ident.' limit 1';

include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

	// dépôt inconnu, retour...
	if (!$enreg_sel) Retour_Ar();

	$enreg2 = $enreg_sel;
	Champ_car($enreg2,'Nom');
	unset($enr_sel);

	$compl = Ajoute_Page_Info(600,150);
	if ($est_contributeur) {
		$compl .= Affiche_Icone_Lien('href="Edition_Depot.php?ident='.$Ident.'"','fiche_edition',my_html($LG_Menu_Title['Repo_Sources_Edit'])) . '&nbsp;';
	}

	Insere_Haut(my_html($titre),$compl,'Fiche_Depot',$Ident);

	// Type d'objet des dépôts de sources
	$Type_Ref = 'O';

	$larg_titre = 25;
	echo '<br />';
	echo '<table width="70%" class="table_form" align="center">'."\n";
	colonne_titre_tab(LG_CH_REPOSITORY_NAME);
	echo $enreg2['Nom'].'</td></tr>'."\n";
	echo '</table>';

	//  ===== Affichage du commentaire
	if (Rech_Commentaire($Ident,$Type_Ref)) {
		Aff_Comment_Fiche($Commentaire,$Diffusion_Commentaire_Internet);
	}
	
	echo '<br /><a href="Liste_Sources.php?depot='.$Ident.'">'.my_html(LG_CH_REPOSITORY_LIST).'</a>	'."\n";

	// Formulaire pour le bouton retour
	Bouton_Retour($lib_Retour,'?'.Query_Str());

	Insere_Bas($compl);
}
?>
</body>
</html>
