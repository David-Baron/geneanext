<?php

//===========================================
// Gerard KESTER le 02/11/2006
// intégration et compléments : JLS
// UTF-8
//===========================================

session_start();

// Récupération des variables de l'affichage précédent
$tab_variables = array('annuler','Horigine');

foreach ($tab_variables as $nom_variables) {
  if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
  else $$nom_variables = '';
}

include('fonctions.php');

$annuler  = Secur_Variable_Post($annuler,strlen($lib_Retour),'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'L';							// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Check_Pers'];	// Titre pour META
$x = Lit_Env();
$niv_requis = 'C';						// Il faut un niveau contributeur pour accéder à la page
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

//  Fonctions ==============================================================================================

// Contrôle un parent
// $id : référence du parent
// $parent : type de parent : P/M
function ctrl_parent($id,$parent) {
	global $Date_Nai_OK, $D_Nai_PI, $Date_Dec_OK, $D_Dec_PI, $Sexe_INI, $nomPers, $n_personnes, $erreur;

	if ($parent == 'P') {
		$lib_parent = 'son p&egrave;re';
	}
	else {
		$lib_parent = 'sa m&egrave;re';
	}

	$aj_sexe = '';
	if ($Sexe_INI == 'f') $aj_sexe = 'e';

	$sql = 'SELECT Ne_le, Decede_Le FROM '.$n_personnes.' WHERE reference = ' . $id . ' limit 1';
	$res = lect_sql($sql);
	$enreg = $res->fetch(PDO::FETCH_NUM);
	$naissance = $enreg[0];
	$deces = $enreg[1];
	$dNaisP = false;
	$dDecP = false;
	$erreur = false;
	$dNaisP = ctrl_date($naissance, "date de naissance $lib_parent");
	if ($dNaisP) {
		$vivant = determine_etat_vivant($naissance,$deces);
		if (!$vivant) {
			$dDecP = ctrl_date($deces, "date de d&eacute;c&egrave;s $lib_parent");
		    if ($dDecP) {
		    	// Contrôle que le père n'est pas décédé plus de 9 mois avant la naissance de l'enfant
				if ($parent == 'P') {
					$mesErr = 'Le p&egrave;re de ' . $nomPers . " ne peut pas l'avoir con&ccedil;u$aj_sexe" .
								', il est d&eacute;c&eacute;d&eacute; plus de 9 mois avant la naissance de l\'enfant.';
					ctrlDates(ajout9mois($deces) , $D_Nai_PI , $mesErr , 6 , 1);
				}
		    	// Contrôle que la mère n'est pas décédée avant la naissance de l'enfant
				else {
					$mesErr = 'La m&egrave;re de ' . $nomPers . " est d&eacute;c&eacute;d&eacute;e avant la naissance de l'enfant.";
					ctrlDates($deces , $D_Nai_PI , $mesErr , 8 , 1);
				}
			}
		}
	}
	// Contrôle que le parent a au moins 15 ans à la naissance de l'enfant
	if (($Date_Nai_OK) and (!$erreur)) {
		if ($dNaisP) {
			if ($parent == 'P') {
				$messErr = $nomPers . " est n&eacute;$aj_sexe avant que son p&egrave;re n'ait 15 ans.";
			}
			else {
				$messErr = $nomPers . " est n&eacute;$aj_sexe avant que sa m&egrave;re n'ait 15 ans.";
			}
			ctrlDates($D_Nai_PI , ajout15ans($naissance) , $messErr , 1 , 5);
		}
	}
}

// Contrôle d'une date : présente et précise
function ctrl_date($LaDate , $mes) {
	//echo 'date à controler : '.$LaDate. '<br />';
  	if ($LaDate == '') {
	    alerte('La ' . $mes . " n'est pas renseign&eacute;e.");
		return False;
	}
	if (strlen($LaDate) == 10) {
		if ($LaDate[9] != 'L') {
			alerte('La ' . $mes . " n'est pas une date exacte.");
			return False;
		}
	}
	return true;
}

//  Controle de deux dates
//  Parametres : les deux dates a comparer , le texte du message d'erreur et deux indices pour indiquer le nom des rubriques (voir $tabRubriques)
function ctrlDates($date1 , $date2 , $mes , $lib1 , $lib2) {
	global $nbErreurs , $tabRubriques, $erreur;
	if ($date1 && $date2) {
		if ($date1 < $date2) {
			$nbErreurs += 1;
			Affiche_Warning($mes);
			echo '(' . $tabRubriques[$lib1] . ' = ' . Etend_date($date1) . ' / ';
			echo $tabRubriques[$lib2] . ' = ' . Etend_date($date2) . ")<br /><br />\n";
			$erreur = true;
		}
	}
}

//  Affichage message d'elerte
function alerte($mes) {
	global $nbErreurs;
	$nbErreurs += 1;
	echo '<table border="0" cellspacing = "4"><tr>' . "\n";
	echo '<td>'.Affiche_Icone('drapeau_orange','erreur').'</td>';
	echo '<td>'.$mes."</td>\n";
	echo '</tr></table>' . "\n";
}

// Ajoute 15 ans à une date
function ajout15ans($laDate) {
	if ($laDate) {
		$an = substr($laDate,0,4);
		$an += 15;
		return $an . substr($laDate,4);
	}
	else 
		return '';
}

// Ajoute 9 mois à une date
function ajout9mois($laDate) {
	if ($laDate) {
		$an = substr($laDate,0,4);
		$mois = intval(substr($laDate,4,2));
		$mois += 9;
		if ($mois > 12) {
			$mois -= 12;
			$an += 1;
		}
		return $an . zerofill2($mois) . substr($laDate,6);
	}
	else 
		return '';	
}

//  Affichage du message s'il n'y a pas d'erreur
function pasErreur() {
  global $nbErreurs;
  if ($nbErreurs == 0) {
	 echo Affiche_Icone('drapeau_vert','ok').'&nbsp;Pas d\'erreur<br />' . "\n";
  }
  $nbErreurs = 0;
}

function titre_blocs($lib_bloc) {
	echo '<p><strong>'.my_html($lib_bloc).'</strong></p>' . "\n";
}

//  Programme principal =============================================================

// La page est réservée aux gestionnaires
controle_utilisateur('G');

//  Tableau des noms de variables
$tabRubriques = Array("" , "Date de naissance" , "Date de bapt&ecirc;me" , "Date de d&eacute;c&egrave;s" ,
	"Date de l'union" , "Naissance du p&egrave;re + 15 ans" , "D&eacute;c&egrave;s du p&egrave;re + 9 mois" ,
	"Naissance de la m&egrave;re + 15 ans" , "D&eacute;c&egrave;s de la m&egrave;re" , "Naissance + 15 ans" ,
	"D&eacute;c&egrave;s + 9 mois" , "Naissance de l'enfant");

//  Pour savoir s'il y a des erreurs ou non
global $nbErreurs;
$nbErreurs = 0;

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer','N');

$n_personnes = nom_table('personnes');

// Lecture de la personne
$sql = 'SELECT * FROM '.$n_personnes.' WHERE reference = ' . $Refer . ' limit 1';
$res = lect_sql($sql);
$enreg = $res->fetch(PDO::FETCH_ASSOC);
//  Pour afficher le nom et le prenom dans le titre de la page
$nomPers = $enreg['Prenoms'] . " " . $enreg['Nom'];
$nom = 'Contr&ocirc;le de la fiche de ' . my_html($nomPers);

$compl = Ajoute_Page_Info(800,400).
			Affiche_Icone_Lien(Ins_Edt_Pers($Refer),'fiche_edition','Modifier').'&nbsp;'.
			Affiche_Icone_Lien(Ins_Ref_Pers($Refer),'fiche_fam','Fiche familiale')."\n";
Insere_Haut($nom , $compl , 'Verif_Personne','');

titre_blocs(LG_CHK_PERS_CTRLS);

//  Controle du statut de la fiche
$statut = $enreg['Statut_Fiche'];
if ($statut == Null)
{
	alerte('Le statut de la fiche n\'est pas renseign&eacute;.');
}
else
{
	if ($statut != "O")
	  alerte('La fiche n\'est pas valid&eacute;e.');
}

//  Controle du code de diffusion Internet
$statut = $enreg['Diff_Internet'];
if ($statut == Null)
{
	alerte('Le code de diffusion Internet n\'est pas renseign&eacute;.');
}
else
{
if ($statut != "O")
	alerte('Le code de diffusion Internet n\'est pas valid&eacute;.');
}

$Sexe_INI = $enreg['Sexe'];

//  Extraction des dates de la personne
$D_Nai_PI = $enreg['Ne_le'];
$D_Dec_PI = $enreg['Decede_Le'];
$vivant = determine_etat_vivant($D_Nai_PI,$D_Dec_PI);
$Date_Nai_OK = ctrl_date($D_Nai_PI , "date de naissance");
$Date_Dec_OK = false;
if ($Date_Nai_OK) {
	if (!$vivant) $Date_Dec_OK = ctrl_date($D_Dec_PI , 'date de d&eacute;c&egrave;s');
}

//  Controles dates
if (!$vivant) {
	ctrlDates($D_Dec_PI, $D_Nai_PI , 'La date de d&eacute;c&egrave;s est ant&eacute;rieure &agrave; la date de naissance.' , 3 , 1);
}
pasErreur();

//  Controle avec les parents ====================================================================================
titre_blocs(LG_CHK_PERS_CTRLS_PARENTS);

// Lecture de la filiation
$sql = 'SELECT Pere, Mere FROM '.nom_table('filiations').' WHERE enfant = ' . $Refer . ' limit 1';
$res = lect_sql($sql);
$enreg = $res->fetch(PDO::FETCH_ASSOC);
if ($enreg)
{
	ctrl_parent($enreg['Pere'],'P');
	ctrl_parent($enreg['Mere'],'M');
}
pasErreur();

//  Controle des unions ====================================================================================
titre_blocs(LG_CHK_PERS_CTRLS_UNIONS);
//  Lecture des unions
$sql = 'SELECT * FROM '.nom_table('unions').' WHERE ';
if ($Sexe_INI == 'm') $sql = $sql . "Conjoint_1";
else $sql = $sql . "Conjoint_2";
$sql = $sql . " = '" . $Refer . "';";
$res = lect_sql($sql);

while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
	$dCouple = $enreg['Maries_Le'];
    //  Lecture du conjoint
    $sql = 'SELECT Reference, Nom, Prenoms FROM '.$n_personnes.' WHERE reference = ';
    if ($Sexe_INI == 'm') $sql = $sql . $enreg['Conjoint_2'] . ';';
    else              $sql = $sql . $enreg['Conjoint_1'] . ';';
    $res2 = lect_sql($sql);
    $enreg2 = $res2->fetch(PDO::FETCH_NUM);
    $nom = $enreg2[1];
    $prenom = $enreg2[2];
	$c_ref = $enreg2[0];

    $dCouple_OK = ctrl_date($enreg['Maries_Le'] , "date de l'union avec " . $prenom . " " . $nom);

    $d1 = ajout15ans($D_Nai_PI);
    $mesErr = $nomPers . ' n\'a pas pu se mettre en couple avec <a '.Ins_Ref_Pers($c_ref).'>' . $prenom . ' ' . $nom . '</a>';
    $mesErr = $mesErr . ', elle n\'a pas 15 ans.';
    ctrlDates($dCouple , $d1 , $mesErr , 4 , 9);

    $mesErr = $nomPers . ' n\'a pas pu se mettre en couple avec <a '.Ins_Ref_Pers($c_ref).'>' . $prenom . ' ' . $nom . '</a>';
    $mesErr = $mesErr . ', elle est d&eacute;c&eacute;d&eacute;e avant.';
    ctrlDates($D_Dec_PI , $dCouple , $mesErr , 3 , 4);
}
pasErreur();

//  Controle avec les enfants ====================================================================================
titre_blocs(LG_CHK_PERS_CTRLS_CHILDREN);

// Lecture de la filiation
$sql = 'SELECT * FROM '.nom_table('filiations').' WHERE ';
if ($Sexe_INI == 'm')
{
	$sql = $sql . "pere";
	$lib1 = "le p&egrave;re";
	$article = 'il';
	$accord = '';
}
else
{
	$sql = $sql . "mere";
	$lib1 = "la m&egrave;re";
	$article = 'elle';
	$accord = 'e';
}
$sql = $sql . " = '" . $Refer . "';";
$res = lect_sql($sql);
while ($enreg = $res->fetch(PDO::FETCH_ASSOC))
{
	$id = $enreg['Enfant'];

	// Lecture de l'enfant
	$sql = 'SELECT Nom, Prenoms, Ne_le, Decede_Le FROM '.$n_personnes.' WHERE reference = ' . $id . ' limit 1';
	$res2 = lect_sql($sql);
	$enreg2 = $res2->fetch(PDO::FETCH_ASSOC);
	$nom = $enreg2['Nom'];
	$prenom = $enreg2['Prenoms'];
	$Date_Nai_E = $enreg2['Ne_le'];
	$Date_Dec_E = $enreg2['Decede_Le'];
	$vivant = determine_etat_vivant($Date_Nai_E,$Date_Dec_E);
	$Date_Nai_OK_E = ctrl_date($Date_Nai_E, "date de naissance de l'enfant " . $prenom . " " . $nom);
	$Date_Dec_OK_E = false;
	if (!$vivant) $Date_Dec_OK_E = ctrl_date($Date_Dec_E, "date de d&eacute;c&egrave;s de l'enfant " . $prenom . " " . $nom);

	//  Contrôles par rapport à la personne initiale
	//  L'enfant ne peut être né moins de 15 ans après la date de naissance de la personne initiale
	if ($Date_Nai_OK) {
		if ($Date_Nai_OK_E) {
			$d1 = ajout15ans($D_Nai_PI);
			$mesErr = $nomPers . ' ne peut pas être ' . $lib1 . ' de <a '.Ins_Ref_Pers($id).'>'. $prenom . ' ' . $nom . '</a>, ';
			$mesErr = $mesErr .$article.' n\'a pas 15 ans &agrave; la naissance de l\'enfant.';
			ctrlDates($Date_Nai_E , $d1 , $mesErr , 11 , 9);
		}
	}
	//  L'enfant ne peut être né plus de 9 mois après la date de décès de la personne initiale
	if ($Date_Dec_OK) {
		if ($Date_Nai_OK_E) {
		$d1 = ajout9mois($D_Dec_PI);
		$mesErr = $nomPers . ' ne peut pas &ecirc;tre ' . $lib1 . ' de <a '.Ins_Ref_Pers($id).'>'. $prenom . ' ' . $nom . '</a>, ';
		$mesErr = $mesErr . $article. ' est d&eacute;c&eacute;d&eacute;'.$accord.' depuis plus de 9 mois &agrave; la naissance de l\'enfant.';
		ctrlDates($d1 , $Date_Nai_E , $mesErr , 10 , 11);
		}
	}
}
pasErreur();

// Formulaire pour le bouton retour
aff_origine();
Bouton_Retour($lib_Retour,'?'.$_SERVER['QUERY_STRING']);

//  Bas d'ecran
  Insere_Bas($compl);
?>
</body>
</html>