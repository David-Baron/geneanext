<?php

//=====================================================================
// Statistiques de naissance, mariage et décès par ville
// (c) JLS
// portions : Eric Lallement
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

// Gestion standard des pages
$acces = 'L';                          // Page en lecture
$titre = $LG_Menu_Title['BDM_Per_Town'];
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

function Rupt_Ville() {
	global $Anc, $case_N, $case_M, $case_D, $case_V, $Num_Ville,$num_lig;
	$style = 'liste';
	if (!pair($num_lig++)) $style = 'liste2';
	echo '<tr align="center" class="'.$style.'">'."\n";
	echo '<td align="left">&nbsp;';
	if ($Anc == '') echo '?';
	else echo $case_V;
	echo '</td>';
	echo '<td>'.$case_N.'</td>';
	echo '<td>'.$case_M.'</td>';
	echo '<td>'.$case_D.'</td>';
	echo '</tr>'."\n";
	if ($case_N = '&nbsp;');
	if ($case_M = '&nbsp;');
	if ($case_D = '&nbsp;');
}

$Depart = Recup_Variable('dep','N');
$compl = Ajoute_Page_Info(600,170);
if ($Depart != 0) {
	$lib_dep = lib_departement($Depart,1);
	Insere_Haut(LG_STAT_TOWN_COUNTY.' '.$lib_dep,$compl,'Stat_Base_Villes',$Depart);
}
else Insere_Haut($LG_Menu_Title['BDM_Per_Town'],$compl,'Stat_Base_Villes','');

// Construction de la requête
$n_personnes = nom_table('personnes');
$n_villes = nom_table('villes');

// Partie ville de naissance
$sql = 'SELECT count(*) , nom_ville, ville_naissance, "N"'.
		' FROM '.$n_personnes.' p, '.$n_villes.' v'.
		' WHERE p.ville_naissance = v.identifiant_zone and p.Reference <> 0 ';
if ($Depart != 0) $sql .= ' and v.Zone_Mere = '.$Depart.' ';
if (!$_SESSION['estPrivilegie']) $sql .= " and Diff_Internet = 'O' ";
$sql .= 'GROUP BY p.ville_naissance union ';

// Partie ville de mariage ==> attention nombre de couples
$sql .= 'SELECT count(*), v.Nom_Ville, Ville_Mariage, "M" '.
		' FROM '.$n_personnes.' m, '.$n_personnes.' f, '.$n_villes.' v, '.nom_table('unions').' u'.
		' WHERE u.Ville_Mariage = v.identifiant_zone '.
		'and u.Conjoint_1 = m.Reference and u.Conjoint_2 = f.Reference ';
if ($Depart != 0) $sql .= ' and v.Zone_Mere = '.$Depart.' ';
if (!$_SESSION['estPrivilegie']) {
	$sql .= "and m.Diff_Internet = 'O' ";
	$sql .= "and f.Diff_Internet = 'O' ";
}
$sql .= 'group by u.Ville_Mariage UNION ';

// Partie décès
$sql .= 'SELECT count(*) , nom_ville, ville_deces, "D" '.
		'FROM '.$n_personnes.' p, '.$n_villes.' v'.
		' WHERE p.ville_deces = v.identifiant_zone and p.Reference <> 0 ';
if ($Depart != 0) $sql .= ' and v.Zone_Mere = '.$Depart.' ';
if (!$_SESSION['estPrivilegie']) $sql .= " and Diff_Internet = 'O' ";
$sql = $sql . 'GROUP BY p.ville_deces ORDER BY 2';

$larg = ' width ="20%"';
echo '<br />';
echo '<table border="0" class="classic" align="center" >'."\n";
echo '<tr align="center">';
echo '<th>Ville</th>';
echo '<th '.$larg.'>'.$LG_birth.'</th>';
echo '<th '.$larg.'>'.$LG_wedding.'</th>';
echo '<th '.$larg.'>'.$LG_death.'</th>';
echo '</tr>'."\n";

$Anc  = '';
$premier = true;
$case_N = '&nbsp;';
$case_M = '&nbsp;';
$case_D = '&nbsp;';
$Anc_num = 0;

$tot_N = 0;
$tot_M = 0;
$tot_D = 0;

$deb = '<a href="' . Get_Adr_Base_Ref();
$deb_Ville  = $deb . 'Fiche_Ville.php?Ident=';
$deb_Nes = $deb.'Liste_Pers2.php?Type_Liste=N&amp;Nom=';
$deb_Maries = $deb.'Liste_Pers2.php?Type_Liste=M&amp;Nom=';
$deb_Decedes = $deb.'Liste_Pers2.php?Type_Liste=D&amp;Nom=';

// Balayage du résultat
$res = lect_sql($sql);
$num_lig = 0;
while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
	
	// Rupture sur le nom de la ville ==> création d'une ligne
	$Ville = $enreg[1];
	$Num_Ville = $enreg[2];
	$nb = $enreg[0];
	if ($Ville != $Anc) {
		if (!$premier) Rupt_Ville();
		$Anc = $Ville;
		$Anc_Num = $Num_Ville;
	}
	$premier = false;
	switch ($enreg[3]) {
		case 'N' : 	$case_N = $deb_Nes.$Ville.'&amp;idNom='.$Num_Ville.'">'.$nb.'</a>'; if ($Num_Ville != 0) $tot_N += $nb; break;
		case 'M' : 	$case_M = $deb_Maries.$Ville.'&amp;idNom='.$Num_Ville.'">'.$nb.'</a>'; if ($Num_Ville != 0) $tot_M += $nb; break;
		case 'D' : 	$case_D = $deb_Decedes.$Ville.'&amp;idNom='.$Num_Ville.'">'.$nb.'</a>'; if ($Num_Ville != 0) $tot_D += $nb; break;
	}	
	$case_V = $deb_Ville.$Num_Ville.'">'.my_html($enreg[1]).'</a>&nbsp;';
}
Rupt_Ville();

$res->closeCursor();

// Affichage des totaux
$style = 'liste';
if (!pair($num_lig++)) $style = 'liste2';
echo '<tr align="center" class="'.$style.'">'."\n";
echo '<td align="left">&nbsp;';
echo my_html(LG_STAT_TOWN_FILLED);
echo '</td>';
echo '<td>'.$tot_N.'</td>';
echo '<td>'.$tot_M.'</td>';
echo '<td>'.$tot_D.'</td>';
echo '</tr>'."\n";

echo '</table>';

// Formulaire pour le bouton retour
$r_compl = '';
$qs = $_SERVER['QUERY_STRING'];
if ($qs != '') $r_compl = '?'.$qs;
Bouton_Retour($lib_Retour,$r_compl);

Insere_Bas($compl);
?>
</body>
</html>