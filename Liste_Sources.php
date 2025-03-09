<?php
//====================================================================
// 
//  Affichage de la liste des sources éventuellement par dépôt
//
// (c) JLS 2012
// UTF-8
//=====================================================================

// Gestion standard des pages
session_start();
include('fonctions.php');
$acces = 'L';							// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Source_List'];			// Titre pour META
$x = Lit_Env();
$niv_requis = 'C';						// Page réservée au profil contributeur
include('Gestion_Pages.php');

// Verrouillage sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

$t = $titre;
$compl = Ajoute_Page_Info(600,150);

Insere_Haut($t,$compl,'Liste_Sources','');

$n_sources = nom_table('sources');

// Récupération du dépôt sélectionné sur l'affichage précédent
$depot = -1;
$defaut = -1;
if (isset($_POST['depot'])) $depot = $_POST['depot'];
$depot = Secur_Variable_Post($depot, 1, 'N');

// Le dépôt est-il passé dans l'URL ?
if ($depot == -1) {
	$depot = Recup_Variable('depot','N');
	if (!$depot) $depot = -1;
}

$sql = 'select Ident, Nom from '. nom_table('depots') . ' order by Nom';
  
echo '<form action="'.my_self().'" method="post">'."\n";
echo '<table border="0" width="50%" align="center">'."\n";
echo '<tr align="center" class="rupt_table">';
echo '<td width="50%">'.my_html(LG_SRC_REPO).LG_SEMIC."\n";
echo '<select name="depot">'."\n";
echo '<option value="'.$defaut.'"';
if ($depot == $defaut) {echo ' selected="selected"';}
echo '>'.my_html($LG_All).'</option>'."\n";
if ($res = lect_sql($sql)) {
	while ($row = $res->fetch(PDO::FETCH_NUM)) {
		echo '<option value="'.$row[0].'"';
		if ($depot == $row[0]) {echo ' selected="selected"';}
		echo '>'.my_html($row[1]).'</option>'."\n";
	}
}
$res->closeCursor();
echo '</select>'."\n";
echo '</td>'."\n";
echo '<td width="50%"><input type="submit" value="'.my_html($LG_modify_list).'"/></td>'."\n";
echo '</tr>'."\n";
echo '</table>'."\n";
echo '</form>'."\n";

$ed_source = 'Edition_Source.php?ident=';
$lec_source = 'Fiche_Source.php?ident=';
$echo_modif = Affiche_Icone('fiche_edition',my_html($LG_modify)).'</a>'."\n";

// Lien direct sur la dernière source saisie
$MaxRef = 0;
$requete = 'select MAX(Ident) FROM ' . $n_sources;
$result = lect_sql($requete);
if ($enreg = $result->fetch(PDO::FETCH_NUM)) {
 	$MaxRef = $enreg[0];
}
if ($MaxRef > 0) {
	echo '<a href="' . $ed_source.$MaxRef . '">'.my_html(LG_SRC_LAST).'</a><br />';
}

// Possibilité d'insérer une source
echo my_html(LG_SRC_ADD).LG_SEMIC.Affiche_Icone_Lien('href="'.$ed_source.'-1"','ajouter',$LG_add).'<br /><br />'."\n";     

//  Affichage des sources

$crit_depot = '';
if ($depot != -1) $crit_depot = ' where Ident_Depot = '.$depot;

// Constitution de la requête d'extraction
$requete = 'select Ident,Titre from '. $n_sources . $crit_depot . ' order by Titre';
$result = lect_sql($requete);

while ($enreg = $result->fetch(PDO::FETCH_NUM)) {
	$ident = $enreg[0];
	echo '<a href="'. $lec_source. $ident. '">'.my_html($enreg[1]).'</a>&nbsp;';
	echo '&nbsp;<a href="' .$ed_source. $ident. '">'.$echo_modif."\n";
	echo '<br />'."\n";
}
Insere_Bas($compl);
?>
</body>
</html>