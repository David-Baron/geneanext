<?php

//=====================================================================
// Envoi d'un mail aux utilisateurs du site
// (c) JLS 2012
//=====================================================================

session_start();
include('fonctions.php');

$max_emails = 49;
// Réduction du nombre d'emails autorisés sur le site de test
if ($SiteGratuit) {
	if (strpos($_SERVER['REQUEST_URI'], 'test_geneamania') !== false) $max_emails = 2;
}

// Récupération des variables de l'affichage précédent
$tab_variables = array( 'ok', 'annuler', 'Horigine' , 'idents', 'sujet', 'message');
foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}

$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');
$idents   = Secur_Variable_Post($idents,500,'S');
$sujet    = Secur_Variable_Post($sujet,80,'S');
$message  = Secur_Variable_Post($message,250,'S');

// var_dump($idents);

// On n'autorise que les chiffres et la virgule
$idents = preg_replace('/([^,0-9]+)/i', '', $idents);

// Gestion standard des pages
$acces = 'M';						// Type d'accès de la page : (M)ise à jour
$titre = 'Envoi d\'un mail';		// Titre pour META
$x = Lit_Env();						// Lecture de l'indicateur d'environnement
$niv_requis = 'G';					// Les contributions sont ouvertes à tout le monde
include('Gestion_Pages.php');		// Appel de la gestion standard des pages

// Interdit sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$entetePage = $titre;

$compl = Ajoute_Page_Info(600,200);
Insere_Haut($titre, $compl, 'mail_ut','');

// Demande d'envoi des mails
if (($bt_OK) and ($sujet != '') and ($message != '')) {
	
	Ecrit_Entete_Page($titre,$contenu,$mots);
	
	// Récupération des identifiants reçus
	$crit = rtrim($idents, ',');
	$req = 'select nom, Adresse from '.nom_table('utilisateurs').' where idUtil in('.$crit.') and Adresse is not null';
	$res = lect_sql($req);
	if ($res->rowCount() > $max_emails) {
		aff_erreur('Le nombre de mails à envoyer est supérieur au maximum autorisé ('.$max_emails.')');	
	}
	// Envoi des messages
	else {
		while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
			envoi_mail($enreg[1],$sujet,$message,'');
		}
	}
	
	Bouton_Retour($lib_Retour,'');
}

// Première entrée : affichage pour saisie
else {
	
	if ($debug) {	
		echo 'PHP_SELF : '.my_self().'<br />';
		echo 'SCRIPT_NAME : '.$_SERVER['SCRIPT_NAME'].'<br />';
		echo 'HTTP_HOST : '.$_SERVER['HTTP_HOST'].'<br />';
		echo 'SCRIPT_FILENAME : '.$_SERVER['SCRIPT_FILENAME'].'<br />';
		echo 'REQUEST_URI : '.$_SERVER['REQUEST_URI'].'<br />';
	}
	
	echo '<br />'."\n";
	echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'sujet,message\')" action="'.my_self().'">'."\n";
	aff_origine();
		
	// Récupération des identifiants d'utilisateurs reçus depuis la liste
	$nom_var = 'msg_ut_';
	$l_nom_var = strlen($nom_var);
	$idents = '';
	foreach ($_POST as $key => $value) {
		if (strpos($key,$nom_var) !== false) {
			$ind_var = substr($key, $l_nom_var);
			$idents .= $ind_var . ',';
		}
	}
	echo '<input type="hidden" name="idents" value="'.$idents.'"/>'."\n";
	echo '<input type="hidden" name="maxi" value="'.$max_emails.'"/>'."\n";
	// var_dump($idents);
	
	$larg_titre = '25';
	
	echo '<table width="70%" class="table_form">'."\n";
	ligne_vide_tab_form(1);
	
	col_titre_tab(LG_MAIL_SUBJECT,$larg_titre);
	echo '<td class="value">';
	echo '<input class="oblig" type="text" name="sujet" id="sujet" size="80" maxlength="80"/>&nbsp;'."\n";
	Img_Zone_Oblig('imgOblig1');
	echo '</td></tr>'."\n";
	
	col_titre_tab(LG_MAIL_MSG,$larg_titre);
	echo '<td class="value">';
	echo '<textarea cols="50" rows="5" name="message" id="message"></textarea>&nbsp;'."\n";
    Img_Zone_Oblig('imgOblig2');
    echo '</td></tr>'."\n";

	bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', true);    
    
	echo '</table>';
		
	if ((!$SiteGratuit) and ($FromTo_Mail = 'support@geneamania.net'))
		echo '<br>'.Affiche_Icone('tip','Information').' '.LG_MAIL_FROM.$FromTo_Mail.'<br>'."\n";

	echo '</form>';
	
}

Insere_Bas($compl);

?>
</body>
</html>
