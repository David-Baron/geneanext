<?php
//=====================================================================
// Liste des utilisateurs
// (c) G Kester ; ajouts JLS - 2025
//=====================================================================

session_start();

function aff_option_niveau($niv_option) {
	global $profil;
	echo '<option value="'.$niv_option.'"' ;
	if ($niv_option == $profil) echo ' selected="selected"';
	echo '>'.libelleNiveau($niv_option).'</option>'."\n";
}

include('fonctions.php');

$acces = 'L';							// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Users_List'];		// Titre pour META
$x = Lit_Env();
$niv_requis = 'G';						// réservé aux gestionnaires
include('Gestion_Pages.php');

Insere_Haut($titre ,'','Liste_utilisateurs','');

// Possibilité d'insérer un utilisateur
echo my_html(LG_USERS_LIST_ADD).LG_SEMIC.Affiche_Icone_Lien('href="Edition_Utilisateur.php?code=-----"','ajouter',$LG_add).'<br /><br />'."\n";	

// Récupération du dépôt sélectionné sur l'affichage précédent
$profil = 'X';
$defaut = 'X';
if (isset($_POST['profil'])) $profil = $_POST['profil'];
$profil = Secur_Variable_Post($profil, 1, 'S');

// Verrouillage de la gestion des documents sur les gratuits non Premium
//if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Pas de mail possible sur les sites gratuits non Premium et en local
$mails = false;
if ($Environnement == 'I') {
	if ((!$SiteGratuit) or ($Premium)) $mails = true;
}
$mails = true;

echo '<form action="'.my_self().'" method="post">'."\n";
echo '<table border="0" width="50%" align="center">'."\n";
echo '<tr align="center" class="rupt_table">';
echo '<td width="50%">'.my_html(LG_UTIL_PROFILE).LG_SEMIC."\n";
echo '<select name="profil">'."\n";
echo '<option value="'.$defaut.'"';
if ($profil == $defaut) {
	echo ' selected="selected"';
}
echo '>Tous</option>'."\n";
aff_option_niveau('I');
aff_option_niveau('P');
aff_option_niveau('C');
aff_option_niveau('G');
echo '</select>'."\n";
echo '</td>'."\n";
echo '<td width="50%"><input type="submit" value="'.$LG_modify_list.'"/></td>'."\n";
echo '</tr>'."\n";
echo '</table>'."\n";
echo '</form>'."\n";

$crit_profil = '';
if ($profil != $defaut) $crit_profil = ' where niveau = \''.$profil.'\'';

// Constitution de la requête d'extraction
$sql = 'select idUtil , nom, codeUtil from '.nom_table('utilisateurs') . $crit_profil . ' order by nom , codeUtil';
$res = lect_sql($sql);

if ($res->rowCount() > 0) {

	if ($mails) echo '<form id="saisie" method="post" action="Mail_Ut.php">'."\n";
	
	// Optimisation : préparation echo des images
    $echo_modif = Affiche_Icone('fiche_edition',my_html($LG_modify)).'</a>';

    while ($row = $res->fetch(PDO::FETCH_NUM)) {
    	if ($mails) 
			echo '<input type="checkbox" name="msg_ut_'.$row[0].'" value="x" onclick="chkSel(this)"/>&nbsp;';
        echo '<a href="Fiche_Utilisateur.php?code='.$row[0].'">'.my_html($row[1].' - '.$row[2]);
        echo '</a>&nbsp;<a href="Edition_Utilisateur.php?code='.$row[0].'">'.$echo_modif."\n";
        echo "<br />\n";
    }

    if ($mails) {
		
		echo '<br>'.Affiche_Icone('tip','Information').' '.LG_UTIL_CHK_MAIL.'<br>'."\n";
	   	bt_ok_an_sup('Envoi de mails', '', '', '', false);
		echo '</form>';
		
		// On cache le bouton d'envoi de mail par défaut et on ne le montre que si au moins 1 case est cochée
		echo '<script type="text/javascript">'."\n";
		echo '  document.getElementById("bouton_ok").style.visibility = "hidden";'."\n";
		echo '  function chkSel(theElement) {'."\n";
		echo '  	var nbSel = 0'."\n";
		echo '  	var theForm = theElement.form;'."\n";
		echo '  	for (i = 0; i < theForm.elements.length; i++) {'."\n";
		echo '  		if (theForm.elements[i].type == \'checkbox\') {'."\n";
		echo '  			if (theForm.elements[i].checked) nbSel++;'."\n";
		echo '  		}'."\n";
		echo '  	}'."\n";
		echo '  	if (nbSel>0) document.getElementById("bouton_ok").style.visibility = "visible";'."\n";
		echo '  	else document.getElementById("bouton_ok").style.visibility = "hidden";'."\n";
		// echo '  	window.alert(nbSel);'."\n";
		echo '  }'."\n";
		echo '</script>'."\n";		
    }
    
}
$res->closeCursor();

Insere_Bas('');
?>
</body>
</html>