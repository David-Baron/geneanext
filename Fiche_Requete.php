<?php

//=====================================================================
// Affichage d'une requête
// (c) JLS
// UTF-8
//=====================================================================

session_start();

include('fonctions.php');
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Request'];       // Titre pour META
$niv_requis = 'P';
$x = Lit_Env();

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Retour),'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de la variable passée dans l'URL : catégorie
$reference = Recup_Variable('reference','N');

$req_sel = 'select Titre, Criteres, Code_SQL from '.nom_table('requetes').' where Reference = '.$reference.' limit 1';

include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

	// requête inconnue, retour...
	if (!$enreg_sel) Retour_Ar();

	$enreg = $enreg_sel;
	
	$compl = Ajoute_Page_Info(600,150);

	// Possibilité de venir en modification pour les gestionnaires
	if ($est_gestionnaire)
		$compl .= '<a href="Edition_Requete.php?reference='.$reference.'">'.Affiche_Icone('fiche_edition',my_html($LG_Menu_Title['Request_Edit'])).'</a>'."\n";

	Insere_Haut(my_html($titre),$compl,'Fiche_Requete',$reference);

	$larg_titre = 25;
	echo '<br />';
	echo '<table width="80%" class="table_form">'."\n";
	echo colonne_titre_tab(LG_QUERY_TITLE).my_html($enreg['Titre']).'</td></tr>'."\n";

	$liste_crit = explode($separ,$enreg['Criteres']);
	$nb_crit = count($liste_crit);
	if ($nb_crit > 0) {
		for ($nb=0; $nb < $nb_crit-1; $nb++) {
			$exp_crit = explode('=',$liste_crit[$nb]);
			echo colonne_titre_tab(trim($exp_crit[0])).my_html(trim($exp_crit[1])).'</td></tr>'."\n";
		}
	}

	echo colonne_titre_tab(LG_QUERY_CODE).$enreg['Code_SQL'].'</td></tr>'."\n";
	echo '</table>'."\n";

	// Formulaire pour le bouton retour
	Bouton_Retour($lib_Retour,'?'.Query_Str());

}
echo '<br />'."\n";
Insere_Bas($compl);
?>
</body>
</html>