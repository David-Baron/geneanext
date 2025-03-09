<?php
//=====================================================================
// Exportation
//  JL Servin
// + G Kester : adaptations
// UTF-8
//=====================================================================

session_start();
include('fonctions.php');

// Retourne vrai si le nom de la table correspond
function est_table($nom_compar,$table_name) {
  return (strcmp(nom_table($nom_compar),$table_name) == 0) ? true : false;
}

// Conservation ou non de la date en fonction d'une date pivot
function conserve_date($La_Date) {
	global $Date_Pivot;
	if ($La_Date > $Date_Pivot) return '';
	else return $La_Date;
}

function remplace($ch_source, $ch_cible, $ch_fin, $garder=false) {
	global $schema;
	$px = 0;
	do {
		$px = strpos($schema,$ch_source);
		if ($px) {
			$px2 = strpos($schema, $ch_fin, $px+1);
			// On garde ou pas le dernier caractère ?
			if (!$garder) $off = 1;
			else $off = 0;
			if ($px2) $schema = substr($schema,0,$px).$ch_cible.substr($schema,$px2+$off);
		}
	} while ($px != 0);
}

$lib_ok = 'Exporter';
$lib_an = 'Retour...';

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok','annuler',
                       'type_export',
                       'n_table','ut_suf','suffixe',
                       's_dates_recentes','pivot',
                       'Horigine'
                       );
foreach ($tab_variables as $nom_variables) {
   if (isset($_POST[$nom_variables])) {
       $$nom_variables = $_POST[$nom_variables];
   } else $$nom_variables = '';
}
$ok       = Secur_Variable_Post($ok,strlen($lib_ok),'S');
$annuler  = Secur_Variable_Post($annuler,strlen($lib_an),'S');
$Horigine = Secur_Variable_Post($Horigine,100,'S');

// On retravaille éventuellement les boutons pour avoir un comportement standard
if ($annuler == $lib_an) $annuler = 'Annuler';
if ($ok == $lib_ok) $ok = 'OK';

// Gestion standard des pages
$acces = 'L';                  	// Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Export';             	// Titre pour META
$niv_requis = 'G';				// Page réservée au gestionnaire
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Sécurisation des variables postées
$type_export      = Secur_Variable_Post($type_export,15,'S');
$ut_suf           = Secur_Variable_Post($ut_suf,1,'S');
$suffixe          = Secur_Variable_Post($suffixe,20,'S');
$s_dates_recentes = Secur_Variable_Post($s_dates_recentes,2,'S');
$pivot            = Secur_Variable_Post($pivot,3,'N');

$sans_dates_recentes = ($s_dates_recentes == 'on') ? true : false;

if ($sans_dates_recentes) {
	$limite = $Lim_Diffu;
	if ((isset($pivot)) and ($pivot != '')) {
		if (is_numeric($pivot)) $limite = $pivot;
	}
	$A = date('Y') - $limite;
	$M = date('m');
	$J = date('d');
	$xA = str_pad($A, 4, '0', STR_PAD_LEFT);
	$xM = str_pad($M, 2, '0', STR_PAD_LEFT);
	$xJ = str_pad($J, 2, '0', STR_PAD_LEFT);
	$Date_Pivot = $xA.$xM.$xJ;
}

/*

Règles de gestion des exports
	Pour internet :
		_ on drop toutes les tables sauf la table des compteurs
		- on exporte toutes les structures sauf la table des compteurs
		- on exporte toutes les données
		- on modifie l'indicateur d'environnement
	Pour sauvegarde :
		- aucune restriction
	Pour initialisation :
		_ on drop toutes les tables
		- on exporte toutes les structures
		- on exporte toutes les données sauf :
			compteurs
			filiations
			images
			unions
			concerne_objet
			participe
			commentaires
			concerne_doc
			documents
			liste_diffusion
			relation_personnes
			arbre
			arbreetiquette
			arbrepers
			arbrephotos
			arbreunion
			requetes
			depots
			sources
			concerne_source
			connexions
			general, utilisateurs, evenements, categories pour lesquelles on charge un contenu particulier
			conditions particulières d'extraction des données :
				liens : ' where Ref_lien = 0'; ==> on conserve le lien par défaut vers Généamania
				personnes : ' where Reference = 0'; ==>  On n'extrait que la personne de référence 0
				villes : ' where Identifiant_zone = 0'; ==> On n'extrait que la ville d'identifiant 0

				// Vérifier pour le lien défaut

				roles : ' where Code_Role = \'\''; ==> On n'extrait que le rôle vide
				types_evenement : ' where Code_Modifiable = \'N\''; ==> On n'extrait que les évènements non modifiables
	Pour site gratuit :
		_ on vide toutes les tables sauf la table des compteurs ==> dans la page d'import
		- on exporte toutes les données
		- on modifie l'indicateur d'environnement ==> dans la page d'import
*/

$compl = Ajoute_Page_Info(600,300);

if ($bt_OK) Ecrit_Entete_Page($titre,$contenu,$mots);

Insere_Haut('Export de la base',$compl,'Export','');

if ($bt_OK) {

		$Internet       = ($type_export == 'Internet') ? true : false;
		$Sauvegarde     = ($type_export == 'Sauvegarde') ? true : false;
		$Initialisation = ($type_export == 'Initialisation') ? true : false;
		$expSiteGratuit = ($type_export == 'SiteGratuit') ? true : false;
		$SQLite         = ($type_export == 'SQLite') ? true : false;
		
		$h_structure_ok = my_html(LG_EXPORT_STRUCTURE_OK);
		$h_data_ok = my_html(LG_EXPORT_DATA_OK);
		$h_raws = my_html(LG_EXPORT_RAWS);

		if ($SQLite) $comment = '--';
		else $comment = '#';

		// Version de l'initialisation
		$nouv_vers = $Version;

		$opt_noCharset = true;  // Suppression du Charset Latin par défaut sur la structure
		$gz = false;

		// Récupération de la date
		$temps = time();
		$jour = date('j', $temps);  //format numerique : 1->31
		$annee = date('Y', $temps); //format numerique : 4 chiffres
		$mois = date('m', $temps);
		$heure = date('H', $temps);
		$minutes = date('i', $temps);
		$date = $jour.'/'.$mois.'/'.$annee.' '.$LG_at.' '.$heure.'h'.$minutes;
		echo my_html($comment." Export $type_export de la base $db le $date").'<br />';

		// Création et ouverture du fichier
		// Pour les sites gratuits sur le net, l'extension doit être txt ==> protection de l'intégrité de la base
		// Pour les autres types d'export, on sort en sql direct
		if ($expSiteGratuit) 
			$ext = 'txt';
		else
			$ext = 'sql';
		// L'utilisateur a-t-il demandé à utiliser un suffixe ?
		if ($ut_suf != 'O') $suffixe = '';
		// Constitution du nom du fichier
		//$nom_fic_svg = $chemin_exports.'Export_'.$mod_nom_fic.$type_export.$suffixe.$ext;
		if ($Initialisation) $mod_nom_fic = '';
		$pre_suf = '_';
		if ($suffixe == '') $pre_suf = '';
		$nom_fic_svg = construit_fic($chemin_exports,'Export_'.$type_export.$pre_suf.$suffixe.'#',$ext);
		// Tentative de création du fichier
		// $fp = fopen($nom_fic_svg, 'wb');
		$fp = ouvre_fic($nom_fic_svg,'wb');
		if (! $fp) die("impossible de cr&eacute;er $nom_fic_svg.");
		// $_fputs = ($gz) ? @gzputs : @fputs;

		// Ecriture entête du fichier
		ecrire($fp,$comment." Export ".$type_export." de la base $db");
		if (($SiteGratuit) and (!$Initialisation)) {
			ecrire($fp,$comment." site heberge");
		}
		ecrire($fp,$comment." le $date");
		ecrire($fp,$comment." version Genemania $Version");
		ecrire($fp,$comment." prefixe $pref_tables");

		// Sauvegarde de la version dans un fichier en cas d'initialisation
		if ($Initialisation) {
			$nom_fic_vers = 'version.txt';
			// Tentative de création du fichier
			$fps = fopen($nom_fic_vers, 'wb');
			if (! $fps) die("Impossible de cr&eacute;er $nom_fic_vers.");
			fwrite($fps,$Version);
			fclose($fps);
		}

		// Pour chaque table cochée
		if (isset($_POST['n_table'])) {
			$tab_tables = $_POST['n_table'];
			foreach($tab_tables as $tablename) {

				// Nettoyage du nom de la table okazou
				$interdits = array('&', '?', '(', ')', ' ');
				$tablename = str_replace($interdits, '', $tablename);

				$Export_Oui = true;
				if (isset($indexes)) unset($indexes);
				if (isset($uniques)) unset($uniques);

	           // Entête de traitement de la table dans le fichier
	           ecrire($fp,$comment);
	           ecrire($fp,$comment." Traitement de la table $tablename;");

				// Ecriture de la structure de la table, sauf pour les extractions à destination des sites gratuits
				if (!$expSiteGratuit) {
					echo "<br />$tablename : <font color='blue'> $h_structure_ok, </font>";
					// On drop toutes les tables sur l'export Internet sauf la table compteur
					if ((($Internet) and (!est_table('compteurs',$tablename))) or (!$Internet))
						ecrire($fp,"DROP TABLE IF EXISTS `$tablename`;");
					//requete de creation de la table
					$query = "SHOW CREATE TABLE $tablename";
					$resCreate = lect_sql($query);
					$row = $resCreate->fetch(PDO::FETCH_NUM);
					$schema = $row[1].";";
					if ($debug) echo 'Sch&eacute;ma : '.$schema.'<br />';
					// On ne conserve pas les valeurs d'auto-increment
					$ch_auto = ' AUTO_INCREMENT=';
					$p1 = strpos($schema,$ch_auto);
					if ($p1) {
						$p2 = strpos($schema,' ',$p1+1);
						if ($p2) $schema = substr($schema,0,$p1).substr($schema,$p2);
					}
					// Traitements pour rendre le script compatible SQLite
					if ($SQLite) {
						echo '<br />';
						echo '1- :'.$schema.'<br />';
						// int(nnn)<<>>INTEGER
						remplace(' int(', ' INTEGER', ')');
						// tinyint(nnn)<<>>INTEGER
						remplace(' tinyint(', ' INTEGER', ')');
						// AUTO_INCREMENT<<>>PRIMARY KEY AUTOINCREMENT
						remplace('AUTO_INCREMENT', 'PRIMARY KEY AUTOINCREMENT', ',',true);
						// varchar(nnn)<<>>TEXT
						remplace(' varchar(', ' TEXT', ')');
						// char(nnn)<<>>TEXT
						remplace(' char(', ' TEXT', ')');
						// enum('o','n') <<>> TEXT
						remplace('enum(\'o\',\'n\') ', ' TEXT', ')');
						//PRIMARY KEY (`xxx`) à supprimer avec la virgule qui précède
						$px = strpos($schema,'PRIMARY KEY (');
						if ($px) {
							$nb = $px;
							do {
								$nb--;
								$car = $schema[$nb];
							} while ($car != ',');
							$schema[$nb] = ' ';
							$px2 = strpos($schema, ')', $px+1);
							if ($px2) $schema = substr($schema,0,$px).substr($schema,$px2+1);
						}
						// Suppression des clés que l'on va remplacer par des CREATE INDEX
						do {
							$px = strpos($schema,'KEY `');
							if ($px) {
								$nb = $px;
								do {
									$nb--;
									$car = $schema[$nb];
								} while ($car != ',');
								$pun = substr($schema, $nb, $px-$nb);
								$est_unique = (strpos($schema,'UNIQUE ') !== false) ? 'O' : 'N';
								$schema[$nb] = ' ';
								$px2 = strpos($schema, ')', $px+1);
								// Récupération du nom de la clé
								$cle = substr($schema,$px,$px2-$px);
								$pxb = strpos($cle, '(');
								$cle = substr($cle,$pxb+1);
								$cle = str_replace('`','',$cle);
								$indexes[] = $cle;
								$uniques[] = $est_unique;
								if ($est_unique) $px = $nb + 1;
								if ($px2) $schema = substr($schema,0,$px).substr($schema,$px2+1);
							}
						} while ($px);
						//ENGINE et DEFAULT CHARSET à supprimer						
						$schema = str_replace('ENGINE=MyISAM','',$schema);
						$schema = str_replace('DEFAULT CHARSET=latin1','',$schema);
						//CHARACTER SET à supprimer
						$schema = str_replace('CHARACTER SET utf8 COLLATE utf8_unicode_ci ','',$schema);
						$schema = str_replace('CHARACTER SET latin1 ','',$schema);
						$schema = str_replace('DEFAULT CHARSET=ascii COLLATE=ascii_bin','',$schema);
						$schema = str_replace('CHARACTER SET utf8 COLLATE utf8_swedish_ci','',$schema);
						$schema = str_replace('CHARACTER SET utf8 COLLATE utf8_bin','',$schema);
						echo '2- :'.$schema.'<br />';
					}

					// Suppression du charset
					//if ($opt_noCharset) $schema = str_replace("DEFAULT CHARSET=latin1","",$schema);
					// et il faut conditionner le create pour la table sur l'export Internet
					if (($Internet) and (est_table('compteurs',$tablename)))
						$schema = str_replace('CREATE TABLE','CREATE TABLE IF NOT EXISTS',$schema);
					ecrire($fp,"$schema\n");

					// Ecriture des indexes pour SQLite
					if (($SQLite) and (isset($indexes))) {
						$c_ind = count($indexes);
						if ($c_ind > 0) {
							for ($nb=0; $nb<$c_ind; $nb++) {
								$un = '';
								if ($uniques[$nb] == 'O')$un = 'UNIQUE ';
								ecrire($fp,'CREATE '.$un.'INDEX '.$tablename.'_'.$nb.' ON '.$tablename.'('.$indexes[$nb].');');
							}
						}
					}

				}
				else {
					if (!est_table('compteurs',$tablename)) {
						ecrire($fp,"[$tablename]\n");
					}
					echo '<br />'.$tablename. ' : ';
				}

				$Donnees_Oui = true;

				// pas de données compteur sur l'export Internet ou pour le site gratuit
				if (($Internet or $expSiteGratuit) and (est_table('compteurs',$tablename)))
					$Donnees_Oui = false;

				$Cond = '';

				// En initialisation, positionnement des extractions de données et des conditions
				if ($Initialisation) {
					// Tables dont on n'exporte pas les données
					if ((est_table('compteurs',$tablename))
					or (est_table('filiations',$tablename))
					or (est_table('genealogies',$tablename))
					or (est_table('images',$tablename))
					or (est_table('unions',$tablename))
					or (est_table('concerne_objet',$tablename))
					or (est_table('participe',$tablename))
					or (est_table('commentaires',$tablename))
					or (est_table('concerne_doc',$tablename))
					or (est_table('contributions',$tablename))
					or (est_table('documents',$tablename))
					or (est_table('liste_diffusion',$tablename))
					or (est_table('arbre',$tablename))
					or (est_table('arbreetiquette',$tablename))
					or (est_table('arbrepers',$tablename))
					or (est_table('arbrephotos',$tablename))
					or (est_table('arbreunion',$tablename))
					or (est_table('noms_personnes',$tablename))
					or (est_table('noms_famille',$tablename))
					or (est_table('requetes',$tablename))
					or (est_table('sources',$tablename))
					or (est_table('concerne_source',$tablename))
					or (est_table('connexions',$tablename))
					or (est_table('villes_france',$tablename))
					or (est_table('subdivisions',$tablename))
					or (est_table('types_doc',$tablename))
					or (est_table('relation_personnes',$tablename))) {
					   $Donnees_Oui = false;
					}
					// Requête forcée pour la table général
					if (est_table('general',$tablename)) {
						$Donnees_Oui = false;
						$lesDonnees = 'INSERT INTO '.nom_table('general').
						" values ('L','???','".$nouv_vers."','B_Gothique.png','vert_base.jpg','#92826D','support@geneamania.net'".
						",'arbre_asc_hor_carre.png', 'O', 'N', 'C', 'V', 'bar_off_vert_fonce.gif', current_timestamp".
						",'#DCDCDC', '#F5F5F5', '#49453B', '#EFEFEF', '#FEFEFE',9999, null, 'Arial','#000000', true".
						");";
						ecrire($fp,"$lesDonnees");
					}
					// Requêtes forcées pour la table utilisateurs
					if (est_table('utilisateurs',$tablename)) {
						$Donnees_Oui = false;
						$deb = 'INSERT INTO '.nom_table('utilisateurs'). ' values(null,';
						$lesDonnees = $deb." '".LG_EXPORT_GUEST."', '".LG_EXPORT_GUEST."', '', 'I', null);";
						ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb."'Gestionnaire', 'gestionnaire', '63e86b1e912220bdf2cafb57f5ad38673c104fa002f6d1139c3a00c459c048ed', 'G',null);";
						ecrire($fp,"$lesDonnees");
						ecrire($fp,$comment." gestionnaire de la base : gestionnaire/gestionnaire ");
					}
					// Requêtes forcées pour la table évènements
					if (est_table('evenements',$tablename)) {
						$Donnees_Oui = false;
						$deb = 'INSERT INTO '.nom_table('evenements'). ' values(null,0,0,"AC3U",';
						//$lesDonnees = $deb.'"première diffusion du logiciel sous le nom de monSSG","20060228GL","20060228GL",current_timestamp,current_timestamp,"V");';
						//ecrire($fp,"$lesDonnees");
						//$lesDonnees = $deb.'"monSSG devient Généamania","20070518GL","20070518GL",current_timestamp,current_timestamp,"V");';
						//ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb.'"'.LG_EXPORT_EVT1.'","20150107GL","20150107GL",current_timestamp,current_timestamp,"V");';
						ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb.'"sortie de la version 2022.02","20221115GL","20221115GL",current_timestamp,current_timestamp,"V");';
						ecrire($fp,"$lesDonnees");
					}
					// Requêtes forcées pour la table categories
					if (est_table('categories',$tablename)) {
						$Donnees_Oui = false;
						$deb = 'INSERT INTO '.nom_table('categories'). ' values';
						$lesDonnees = $deb.'(1, "bleu", "'.LG_EXPORT_CATEG_BLUE.'", 1);';
						ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb.'(2, "vert", "'.LG_EXPORT_CATEG_GREEN.'", 2);';
						ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb.'(3, "orange", "'.LG_EXPORT_CATEG_ORANGE.'", 3);';
						ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb.'(4, "rose", "'.LG_EXPORT_CATEG_PINK.'", 4);';
						ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb.'(5, "violet", "'.LG_EXPORT_CATEG_PURPLE.'", 5);';
						ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb.'(6, "rouge", "'.LG_EXPORT_CATEG_RED.'", 6);';
						ecrire($fp,"$lesDonnees");
						$lesDonnees = $deb.'(7, "jaune", "'.LG_EXPORT_CATEG_YELLOW.'", 7);';
						ecrire($fp,"$lesDonnees");
					}

					// Conditions particulières d'extraction
					// Lien vers Généamania en standard
					if (est_table('liens',$tablename)) $Cond = ' where Ref_lien = 0';
					// On n'extrait que la personne de référence 0
					if (est_table('personnes',$tablename)) $Cond = ' where Reference = 0';
					// On n'extrait que la ville d'identifiant 0
					if (est_table('villes',$tablename)) $Cond = ' where Identifiant_zone = 0';
					// On n'extrait que le rôle vide
					if (est_table('roles',$tablename)) $Cond = ' where Code_Role = \'\'';
					// On n'extrait que les évènements non modifiables
					if (est_table('types_evenement',$tablename)) $Cond = ' where Code_Modifiable = \'N\'';
					// On n'extrait que le dépôt d'identifiant 0
					if (est_table('depots',$tablename)) $Cond = ' where Ident = 0';
				}

				if (!$Donnees_Oui) {
	            	echo "<font color='red'>".my_html(LG_EXPORT_RES_NO_EXTRACT)."</font>";
	           	}
				else {
					// ecrire le contenu de la table
					$query = "SELECT * FROM $tablename".$Cond;
					$resData = lect_sql($query);
					$nb_lig = $resData->rowCount();
					echo '<font color="green"> '.$h_data_ok.', '.$nb_lig.' '.$h_raws.' </font>'."\n";
					if ($nb_lig > 0) {
						$sInsert = 'INSERT INTO '.$tablename.' values ';
						while ($rowdata = $resData->fetch(PDO::FETCH_NUM)) {

							/*
							Tables présentant des dates à masquer
							evenements ==> Debut (5), Fin (6)
							participe ==> Debut (3), Fin (4)
							personnes ==> Ne_le (5), Decede_Le (6)
							unions ==> Maries_Le (3), Date_K (5)
							*/
							if ($sans_dates_recentes) {
								if (est_table('evenements',$tablename)) {
									$rowdata[5] = conserve_date($rowdata[5]);
									$rowdata[6] = conserve_date($rowdata[6]);
								}
								if (est_table('participe',$tablename)) {
									$rowdata[3] = conserve_date($rowdata[3]);
									$rowdata[4] = conserve_date($rowdata[4]);
								}
								if (est_table('personnes',$tablename)) {
									$rowdata[5] = conserve_date($rowdata[5]);
									$rowdata[6] = conserve_date($rowdata[6]);
								}
								if (est_table('unions',$tablename)) {
									$rowdata[3] = conserve_date($rowdata[3]);
									$rowdata[5] = conserve_date($rowdata[5]);
								}
							}

							if (est_table('personnes',$tablename)) {
								// On ne cherche les NULL que sur la table personne, colonne Sexe
								if ($rowdata[3] == '') $rowdata[3] = 'NULL';
							}
							// Forçage indicateur d'environnement sur Export Internet
							if (($Internet) and (est_table('general',$tablename))) {
								$rowdata[0] = 'I';
							}
							// Ne marche pas bien en 1 passe...
							//$lesDonnees = "<guillemet>".implode("<guillemet>,
							//<guillemet>", $rowdata)."<guillemet>";
							$lesDonnees = implode('<guillemet>,<guillemet>', $rowdata);
							$lesDonnees = '<guillemet>'.$lesDonnees.'<guillemet>';
							$lesDonnees = str_replace("<guillemet>", "'",addslashes($lesDonnees));
							if (est_table('personnes',$tablename)) {
								$lesDonnees = str_replace("'NULL'", "NULL",$lesDonnees);
							}
							// On met l'insert pour l'export autre que vers les sites gratuits
							if (!$expSiteGratuit) {
								$lesDonnees = "$sInsert($lesDonnees);";
							}
							else {
								// Pour la table des commentaires, on vire les retours charriots, sinon soucis !
								if (est_table('commentaires',$tablename)) {
									$lesDonnees = str_replace($cr,'<br />',$lesDonnees);
								}
							}
							if ($SQLite) {
								$lesDonnees = str_replace("\\'","''",$lesDonnees);
								$lesDonnees = str_replace('\\"','"',$lesDonnees);
							}
							ecrire($fp,"$lesDonnees");
						}
					}
             $resData->closeCursor();
           }
         }
       }
       ecrire($fp,$comment." ------- fin ------------");

       // fermer le fichier
       if ($gz) gzclose($fp);
       else fclose($fp);

		echo '<br /><br />'.my_html(LG_EXPORT_FILE).' <a href="'.$nom_fic_svg.'" target="_blank">'.$nom_fic_svg.'</a><br />'."\n";
		Bouton_Retour($lib_Retour);
}
else {

	echo '<form id="saisie" method="post" action="'.my_self().'">'."\n";
	$larg_titre = "25";
	echo '<table width="70%" class="table_form">'."\n";
    ligne_vide_tab_form(1);

    colonne_titre_tab(LG_EXPORT_TYPE);
	if (! $SiteGratuit) {
		echo '<input type="radio" id="type_export_I" name="type_export" value="Internet" checked="checked"/>'
			.'<label for="type_export_I">'.LG_EXPORT_TARGET_INTERNET.'</label>';
		echo '<input type="radio" id="type_export_G" name="type_export" value="SiteGratuit"/>'
			.'<label for="type_export_G">'.LG_EXPORT_TARGET_HOSTED.'</label>';
	}
	echo '<input type="radio" id="type_export_S" name="type_export" value="Sauvegarde"/';
	if ($SiteGratuit) echo ' checked="checked"';
	echo '><label for="type_export_S">'.LG_EXPORT_TARGET_BACKUP.'</label>'."\n";
	if (file_exists('initialisation.txt')) {
		echo '<br /><input type="radio" id="type_export_N" name="type_export" value="Initialisation" onclick="document.forms.saisie.ut_suf[1].checked = true;"/>'
			.'<label for="type_export_N">'.'Initialisation</label>'."\n";
		echo '<input type="radio" id="type_export_Q" name="type_export" value="SQLite" onclick="document.forms.saisie.ut_suf[1].checked = true;"/>'
			.'<label for="type_export_Q">'.'SQLite</label>'."\n";
	}
    echo '</td>';
    echo '</tr>'."\n";

	if ((!$SiteGratuit) or ($SiteGratuit and $Premium)) {
		// Suffixe par défaut
		$temps = time();
		$jour = date('d', $temps);  //format numerique : 01->31
		$annee = date('Y', $temps); //format numerique : 4 chiffres
		$mois = date('m', $temps);
		$suffixe = '_'.$annee.$mois.$jour;
	    colonne_titre_tab(LG_EXPORT_FILE_SUFFIXE);
		echo '<input type="text" size="20" name="suffixe" value="'.$suffixe.'"/>'."\n";
		echo '<input type="radio" id="ut_suf_O" name="ut_suf" value="O" checked="checked"/>'
			.'<label for="ut_suf_O">'.LG_EXPORT_FILE_SUFFIXE_WITH.'</label>'."\n";
		echo '<input type="radio" id="ut_suf_N" name="ut_suf" value="N"/>'
			.'<label for="ut_suf_N">'.LG_EXPORT_FILE_SUFFIXE_WITHOUT.'</label>'."\n";
	    echo '</td>';
	    echo '</tr>'."\n";
	}
	else echo '<tr><td><input type="hidden" name="suffixe" value="">&nbsp;</td></tr>'."\n";

    colonne_titre_tab(LG_EXPORT_OMIT_RECENT);
	echo '<input type="checkbox" id="s_dates_recentes" name="s_dates_recentes"/>';
	echo '&nbsp;<label for="s_dates_recentes">'.LG_EXPORT_DATE_THRES.'</label>&nbsp;:&nbsp;<input type="text" size="3" maxlength="3" name="pivot" value="'.$Lim_Diffu.'"/>&nbsp;'.my_html(LG_EXPORT_YEARS)."\n";
    echo '</td>';
    echo '</tr>'."\n";

	colonne_titre_tab('Tables');
	$sql = 'show tables from `'.$db.'` like \''.$pref_tables.'%\'';
	$result = lect_sql($sql);
	if (!$result) {
		aff_erreur(LG_EXPORT_LIST_ERROR);
		exit;
	}

	$l_action = LG_EXPORT_HOVER;
	if ($Comportement == 'C') $l_action = LG_EXPORT_CLICK;
	echo my_html(LG_EXPORT_TIP1.$l_action.LG_EXPORT_TIP2)."\n";

	$nom_div = 'lediv';
	$x = Oeil_Div('ajout',LG_EXPORT_SHOW,$nom_div);

	echo '<input type="checkbox" name="selTous" value="on" onclick="checkUncheckAll(this);" checked="checked"/>&nbsp;'.my_html(LG_EXPORT_ALL_NONE).'<br /><hr/>';

	while ($row = $result->fetch(PDO::FETCH_NUM)) {
		$tablename = $row[0];
		$sel = false;
		// Le filtre sur le préfixe ne doit être actif que si le préfixe est renseigné
		if ($pref_tables != '') {
			if (strpos($tablename,$pref_tables) === 0) $sel = true;
		}
		else $sel = true;
		// On ne prend pas la table relation_personne ; erreur environnement jls
		// doublon relation_personne / relation_personnes
		if ($sel) {
			if (est_table('relation_personne',$tablename)) {
				if (!est_table('relation_personnes',$tablename))
					$sel = false;
			}
		}
		// Restriction au préfixe à cause du like
		if ($sel) {
			echo '<input type="checkbox" name="n_table[]" value="'.$tablename.'" checked="checked"/>'.$tablename;
			if (est_table('compteurs',$tablename))
				echo '&nbsp;'.Affiche_Icone('commentaire',LG_EXPORT_NO_EXPORT);
			if (est_table('general',$tablename))
				echo '&nbsp;'.Affiche_Icone('commentaire',LG_EXPORT_MODIFY);
			echo '<br />'."\n";
		}
	}
	$result->closeCursor();

	fin_div_cache($nom_div);
	echo '</td></tr>'."\n";

	ligne_vide_tab_form(1);

	bt_ok_an_sup($lib_ok, $lib_Annuler, '', '');

	echo '</table></form>'	;

}
Insere_Bas($compl);

?>
</body>
</html>