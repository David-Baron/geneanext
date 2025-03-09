<?php
//=====================================================================
// Edition d'une catégorie ; pas de création ni de suppression possible
// (c) JLS
// UTF-8
//=====================================================================

session_start();
include('fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok','annuler',
                       'Titre','ATitre',
                       'Ordre','AOrdre',
                       'Horigine',
                       );
foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}

$ok       = Secur_Variable_Post($ok,strlen($lib_Okay),'S');
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Annuler),'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');

// Gestion standard des pages
$acces = 'M';								// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Category_Edit'];	// Titre pour META
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup de la variable passée dans l'URL : catégorie
$Categ = Recup_Variable('categ','N');

//Demande de mise à jour ; pas de création possible
if ($bt_OK) {
	$Titre  = Secur_Variable_Post($Titre,80,'S');
	$ATitre = Secur_Variable_Post($ATitre,80,'S');
	$Ordre  = Secur_Variable_Post($Ordre,1,'N');
	$AOrdre = Secur_Variable_Post($AOrdre,1,'N');
	// Constitution de la requête
	$req = '';
	Aj_Zone_Req('Titre',$Titre,$ATitre,'A',$req);
	Aj_Zone_Req('Ordre_Tri',$Ordre,$AOrdre,'N',$req);
	if ($req != '') {
		$req = 'update '.nom_table('categories').' set '.$req.' where Identifiant = '.$Categ;
		$res = maj_sql($req);
		maj_date_site();
	}
	// Retour sur la page précédente
	Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

	$compl = Ajoute_Page_Info(600,150).
			Affiche_Icone_Lien('href="Fiche_Categorie.php?categ=' .$Categ .'"','page','Fiche lien') . '&nbsp;';

	Insere_Haut(my_html($titre),$compl,'Edition_Categorie',$Categ);

	echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'Titre\')" action="'.my_self().'?categ='.$Categ.'">'."\n";
	aff_origine();

	// Récupération des données
	$sql = 'select * from '.nom_table('categories').' where Identifiant = '.$Categ.' limit 1';
	$res = lect_sql($sql);
	$enreg = $res->fetch(PDO::FETCH_ASSOC);
	$TitreF = my_html($enreg['Titre']);
	$OrdreF = $enreg['Ordre_Tri'];

   	$larg_titre = 25;
	echo '<table width="70%" class="table_form">'."\n";
	ligne_vide_tab_form(1);

	col_titre_tab($LG_Ch_Categ_Title,$larg_titre);
	echo '<td class="value">';
	echo '<input type="text" name="Titre" value="'.$TitreF.'" size="80"/>'."\n";
	Img_Zone_Oblig('Titre');
	echo '<input type="hidden" name="ATitre" value="'.$TitreF.'"/>'."\n";
	echo '</td></tr>'."\n";

	col_titre_tab($LG_Ch_Categ_Order,$larg_titre);
	echo '<td class="value">';
	echo '<img src="'.$chemin_images_icones.$Icones['moins'].'" alt="'.$LG_Ch_Categ_Dec_Order.'" title="'.$LG_Ch_Categ_Dec_Order.'" border="0" ';
	echo 'onclick="if (document.forms.saisie.Ordre.value>1) {document.forms.saisie.Ordre.value--;}"/>'."\n";
	echo '<input type="text" class="oblig" name="Ordre" id="Ordre" value="'.$OrdreF.'" size="3" onchange="verification_num(this);"/>'."\n";
	echo '<img src="'.$chemin_images_icones.$Icones['plus'].'" alt="'.$LG_Ch_Categ_Inc_Order.'" title="'.$LG_Ch_Categ_Inc_Order.'" border="0" ';
	echo 'onclick="document.forms.saisie.Ordre.value++;"/>&nbsp;'."\n";
	Img_Zone_Oblig('ordre_img');
	echo '</td></tr>'."\n";

	col_titre_tab($LG_Ch_Categ_Image,$larg_titre);
	echo '<td class="value">';
	echo '<img src="'.$chemin_images_icones.$Icones['tag_'.$enreg['Image']].'" border="0" alt="'.$TitreF.'" title="'.$TitreF.'"/>';
	echo '</td></tr>'."\n";

	ligne_vide_tab_form(1);
	bt_ok_an_sup($lib_Okay, $lib_Annuler,'','');

	echo '</table>'."\n";

	echo "</form>";

	Insere_Bas($compl);
}

?>
</body>

</html>
