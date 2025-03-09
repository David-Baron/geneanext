<?php

// =================================================
// Vérification de la diffusabilité Internet absente
// UTF-8
// =================================================

session_start();

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok','annuler', 'S_Int','idPers',
                         'Horigine','limite');

foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}

include('fonctions.php');

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok,strlen($lib_Rectifier),'S');
$annuler  = Secur_Variable_Post($annuler,strlen($lib_Annuler),'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');

$acces = 'M';							// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Internet_Hidding_Cheking'];
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$dem_mod = 0;
$nb_mod = 0;

$compl = Ajoute_Page_Info(600,200);
Insere_Haut(my_html($titre),$compl,'Verif_Internet_Absente','');

//  ===== Appliquer les corrections demandees
if ($ok == $lib_Rectifier) {

	$nombre = count($idPers);
	if ($idPers =='') $nombre = 0;
	$deb_req  = 'update '.nom_table('personnes').' set Diff_Internet = "O" where reference = ';
	for ($ligne = 0 ; $ligne < $nombre ; $ligne ++) {
		$idPersLu = $idPers[$ligne];
		if ( isset($S_Int[$ligne])) {
			$req = $deb_req.$idPersLu;
			$dem_mod++;
			$Res = maj_sql($req);
			$nb_mod += $enr_mod;
		}
	}
	if ($nb_mod > 0) maj_date_site();

	$plu1 = pluriel($dem_mod);
	$plu2 = pluriel($nb_mod);
	echo '<br>'.$dem_mod.' '.my_html(LG_CHK_INTERNET_RESULT_1.' ; '.$nb_mod.' '.LG_CHK_INTERNET_RESULT_2).'<br>';
}

// ===== Lecture de la base
if ((isset($limite)) and ($limite != '')) {
if (! is_numeric($limite)) $limite = $Lim_Diffu;
$Lim_Diffu = $limite;
}
$A = date('Y')-$Lim_Diffu;
$M = date('m');
$J = date('d');
$xA = str_pad($A, 4, '0', STR_PAD_LEFT);
$xM = str_pad($M, 2, '0', STR_PAD_LEFT);
$xJ = str_pad($J, 2, '0', STR_PAD_LEFT);
$Date = $xA.$xM.$xJ;

$A = $A - 130 ;
$xA = str_pad($A, 4, '0', STR_PAD_LEFT);
$Date2 = $xA.$xM.$xJ;

// Personnes dont la diffusabilité internet devrait être positionnée
// Les personnes décédées avant la borne ou celles nées avant la borne - 130 ans

$sql = 'SELECT Reference, Nom, Prenoms, Diff_Internet, Ne_Le, Decede_Le '.
		' FROM '.nom_table('personnes').' WHERE Reference <> 0 and Diff_Internet = \'N\' '.
		' and ((Ne_Le <= \''.$Date2.'\' and Ne_Le not like\'%A\' AND Ne_le <> \'\') '.
		' or (Decede_Le <= \''.$Date.'\' and Decede_Le not like\'%A\' AND Decede_le <> \'\')) '.
		' ORDER BY Nom , Prenoms';
$res = lect_sql($sql);
$nbPers = $res->rowCount();

$echo_modif = Affiche_Icone('fiche_edition',my_html($LG_modify)).'</a>';
echo '<form action="'.my_self().'" id="saisie" method="post">';

bt_ok_an_sup($lib_Rectifier,$lib_Annuler,'','',false);

$pluriel = pluriel($nbPers);

echo '<br>'.$nbPers.LG_CHK_INTERNET_ABS_1.' '.$Lim_Diffu.' '.LG_CHK_INTERNET_ABS_2.' '.$Lim_Diffu_Dec.' '.LG_CHK_INTERNET_ABS_3;
echo '&nbsp;'.LG_CHK_INTERNET_ABS_CHG_LIMIT.LG_SEMIC.'<input type="text" size="3" name="limite" value="'.$Lim_Diffu.'"/>&nbsp;ans'."\n";
echo '<input type="submit" name="re" value="'.$LG_Check_Again.'"/>'."\n";
echo '<br><br><a href="Verif_Internet.php">'.$LG_Menu_Title['Internet_Cheking'].'</a>';
echo '<br><br>';

if ($nbPers > 0) {
	echo '<table border="0" class="classic" cellspacing="1" cellpadding="3" align="center">'."\n";
	echo '<tr class="rupt_table">';
	echo '<th>'.LG_CHK_INTERNET_PERSON.'</th>'."\n";
	echo '<th>'.$LG_show_on_internet;
	echo '&nbsp;<input type="checkbox" id="selTous" name="selTous" onclick="checkUncheckAll(this);"/>&nbsp;'
		.'<label for="selTous">'.$LG_All.'</label>';
	echo '</th>'."\n";
	echo '<th>'.LG_CHK_INTERNET_BORN.'</th>'."\n";
	echo '<th>'.LG_CHK_INTERNET_DEATH.'</th>'."\n";
	echo '</tr>'."\n";

	$numLig = 0;
	while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
		$idPers = $enreg[0];
		$internet = '';
		if ($enreg[3] == 'O') $internet = 'checked="checked"';
     	if (pair($numLig)) $style = 'liste';
		else               $style = 'liste2';
		echo '<tr  class="'.$style.'">';
	    echo '<td>';
		echo '<a '.Ins_Ref_Pers($enreg[0]).'>'.my_html($enreg[2].' '.$enreg[1]).'</a>'."\n";
		echo '&nbsp;<a '.Ins_Edt_Pers($enreg[0]).'>'.$echo_modif;
	    echo '</td>'."\n";
		echo '<td align="center">';
		echo '<input type="checkbox" name="S_Int['.$numLig.']"'.$internet.'/>';
		echo '<input type="hidden" name="idPers['.$numLig.']" value="'.$idPers.'"/>';
		echo '</td>'."\n";
		echo '<td>'.Etend_Date($enreg[4]).'</td>'."\n";
		echo '<td>'.Etend_Date($enreg[5]).'</td>'."\n";
		echo '</tr>'."\n";
		$numLig += 1;
	}

	echo '</table>'."\n";
	echo Affiche_Icone('tip',$LG_tip).' '.my_html(LG_CHK_INTERNET_ABS_TIP);
	echo '<br>';
}

aff_origine();
bt_ok_an_sup($lib_Rectifier,$lib_Annuler,'','',false,true);
echo '</form>';

Insere_Bas($compl);

?>
</body>
</html>