<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
// include('fonctions.php');
// Recup de la variable passée dans l'URL : aide demandée
$aide = Recup_Variable('aide','S');

switch ($aide) {
	case 'Admin_Tables' : $objet = 'administration des tables'; break;
	case 'Ajout_Rapide' : $objet = 'ajout rapide'; break;
	case 'Anniversaires' : $objet = 'anniversaires'; break;
	case 'Calc_So' : $objet = 'calculette sosa'; break;
	case 'Completude_Nom' : $objet = 'Informations complétude des informations'; break;
	case 'Conv_Romain' : $objet = 'Informations convertisseur romain - arabe'; break;
	case 'Desc_Directe_Pers' : $objet = 'Informations descendance directe d\'une personne'; break;
	case 'Edition_Contribution' : $objet = 'édition d\'une contribution'; break;
	case 'Edition_Evenement' : $objet = 'édition d\'un évènement'; break;
	case 'Edition_Image' : $objet = 'édition d\'une image'; break;
	case 'Edition_Lier_Eve' : $objet = 'lien évènement à une personne'; break;
	case 'Edition_Lier_Nom' : $objet = 'assigner un nom secondaire à une personne'; break;
	case 'Edition_Lier_Pers' : $objet = 'créer des relations entre deux personnes'; break;
	case 'Edition_NomFam' : $objet = 'édition d\'un nom de famille'; break;
	case 'Edition_Personne' : $objet = 'édition d\'une personne'; break;
	case 'Edition_Rangs' : $objet = 'édition des rangs'; break;
	case 'Edition_Utilisateur' : $objet = 'édition d\'un utilisateur'; break;
	case 'Export' : $objet = 'export'; break;
	case 'Fusion_Evenements' : $objet = 'fusion d\'évènements'; break;
	case 'Histo_Ages_Deces' : $objet = 'historique des âges de décès'; break;
	case 'Histo_Ages_Mariage' : $objet = 'historique des âges de premier mariage'; break;
	case 'Import_CSV' : $objet = 'Import CSV'; break;
	case 'Import_Docs' : $objet = 'import documents'; break;
	case 'Import_Gedcom' : $objet = 'import Gedcom'; break;
	case 'Import_Sauvegarde' : $objet = 'import d\'une sauvegarde'; break;
	case 'Liste_Evenements' : $objet = 'liste des évènements'; break;
	case 'Liste_NomFam' : $objet = 'liste des noms de famille'; break;
	case 'Liste_Nom_Vivants' : $objet = 'liste des personnes vivantes'; break;
	case 'Naissances_Deces_Mois' : $objet = 'naissances et décès par mois'; break;
	case 'Naissances_Mariages_Deces_Mois' : $objet = 'naissances et décès par mois'; break;
	case 'Pers_Isolees' : $objet = 'Personnes isolées'; break;
	case 'Pyramide_Ages_Histo' : $objet = 'historique de l\'âge au décès'; break;
	case 'Pyramide_Ages' : $objet = 'pyramide des âges au décès'; break;
	case 'Pyramide_Ages_Mar_Histo' : $objet = 'historique de l\'âge au décès'; break;
	case 'Recherche_Commentaire' : $objet = 'recherche dans les commenatires'; break;
	case 'Recherche_Cousinage' : $objet = 'recherche de parenté'; break;
	case 'Recherche_Personne_Archive' : $objet = 'recherche de personnes aux archives'; break;
	case 'Recherche_Personne' : $objet = 'recherche de personnes'; break;
	case 'Recherche_Personne_CP' : $objet = 'recherche de personnes par les conjoints ou les parents'; break;
	case 'Recherche_Ville' : $objet = 'recherche de villes'; break;
	case 'Stat_Base_Depart' : $objet = 'statistiques par département'; break;
	case 'Stat_Base_Villes' : $objet = 'statistiques par ville'; break;
	case 'Verif_Internet_Absente' : $objet = 'vérification de la diffusabilité Internet absente'; break;
	case 'Verif_Internet' : $objet = 'vérification de la diffusabilité Internet'; break;
	case 'Verif_Personne' : $objet = 'Vérification d\'une fiche personne'; break;
	case 'Verif_Sosa' : $objet = 'vérification des numéros Sosa'; break;
	case 'Vue_Personnalisee' : $objet = 'vue personnalisée'; break;
	case 'Export_Pour_Deces' : $objet = 'export pour recherche des dates de décès sur matchid.io'; break;
	default : $objet = '';
}	
if ($objet != '') $objet = 'Informations '.$objet; 
Ecrit_Meta($objet,$objet,'');
echo "</head>\n";
$x = Lit_Env();
Ligne_Body();

$auto_contrib = 'Cette page est accessible &agrave; partir du profil contributeur.';

echo '<br />';

switch ($aide) {
	case 'Admin_Tables' : 
		echo "Cette page permet de r&eacute;parer ou optimiser les tables de la base G&eacute;n&eacute;amania.<br />";
		echo "La r&eacute;paration d'une table est n&eacute;cessaire lorsque le logiciel indique 'Table 'nom de la table' is marked as crashed and should be repaired '. Ceci peut arriver lorsqu'il se produit un probl&egrave;me";
		echo "technique sur l'ordinateur. La r&eacute;paration de la table est une solution au m&ecirc;me titre que l'import d'une sauvegarde.<br />";
		echo "L'optimisation d'une table peut &ecirc;tre n&eacute;cessaire lorsqu'il y a de fr&eacute;quentes suppressions sur la table ; la table est alors r&eacute;organis&eacute;e.";
		echo "Normalement, cette op&eacute;ration est inutile dans l'utilisation standard de G&eacute;n&eacute;amania.<br /><br />";
		echo "Cette page n'est disponible que pour le profil gestionnaire.";
		break;
	case 'Ajout_Rapide' : 
		echo "Cette page permet de cr&eacute;er des personnes et les liens associ&eacute;s de mani&egrave;re automatique.<br />";
		echo "A partir d'un personne, on peut :";
		echo "<ul>";
		echo "<li>Cr&eacute;er une soeur ou un fr&egrave;re. Dans ce cas, la personne cr&eacute;&eacute;e b&eacute;n&eacute;ficiera automatiquement de la m&ecirc;me filiation que la personne d'origine.";
		echo "Cette fonction  n'est accessible que si la filiation de la personne d'origine est connue.</li>";
		echo "<li>Cr&eacute;er un conjoint. Dans ce cas, l'union avec la personne d'origine sera automatiquement cr&eacute;&eacute;e.</li>";
		echo "<li>Cr&eacute;er les parents. Les parents et leur union sont cr&eacute;&eacute;s dans la m&ecirc;me page ; la filiation avec la personne d'origine est automatiquement cr&eacute;&eacute;e.";
		echo "Cette fonction  n'est accessible que si la filiation de la personne d'origine n'est pas connue.</li>";
		echo "</ul>";
		echo "Les listes de villes sont aliment&eacute;es &agrave; partir des villes de naissance, bapt&ecirc;me et d&eacute;c&egrave;s de la personne d'origine.<br />";
		echo "Les dates peuvent &ecirc;tre choisies en cliquant sur l'ic&ocirc;ne ".Affiche_Icone('calendrier','calendrier')."<br />";
		echo $auto_contrib;
		break;
	case 'Anniversaires' : 
		echo "Cette page permet de visualiser les anniversaires de naissance, mariage et d&eacute;c&egrave;s sur le mois en cours ou un mois choisi par l'utilisateur.";
		echo "Les anniversaires sont tri&eacute;s par ordre chronologique.<br />";
		echo "Les ic&ocirc;nes ".Affiche_Icone('anniv_nai','Anniversaire de naissance').'&nbsp;'
								.Affiche_Icone('anniv_mar','Anniversaire de mariage').'&nbsp;'
								.Affiche_Icone('anniv_dec','Anniversaire de décès');
		echo " signifient que l'anniversaire de naissance, mariage ou d&eacute;c&egrave;s a lieu le jour m&ecirc;me du mois en cours.<br />";
		echo "L'utilisateur a la possibilit&eacute; de ne pas afficher les personnes d&eacute;c&eacute;d&eacute;es ou pr&eacute;sum&eacute;es d&eacute;c&eacute;d&eacute;es (sur les anniversaires de naissance ou de mariage).<br />";
		echo "NB : l'affichage des personnes dont la visibilit&eacute; internet est restreinte est fonction du profil de l'utilisateur.";
		break;
	case 'Calc_So' : 
		echo 'Cette page permet de calculer le num&eacute;ro <a href="'.$offset_info.'glossaire_gen.php#SOSA">Sosa</a>';
		echo "&nbsp;du conjoint, du p&egrave;re, de la m&egrave;re ou de l'enfant d'une personne.<br />";
		echo "De m&ecirc;me, on peut calculer &agrave; quelle g&eacute;n&eacute;ration correspond un num&eacute;ro et si celui-ci est du c&ocirc;t&eacute; paternel ou maternel.<br />";
		echo "L'utilisateur tape un num&eacute;ro via le clavier ou en cliquant sur les boutons ad hoc ;";
		echo "il doit ensuite cliquer sur le bouton voulu.<br />";
		echo "L'ic&ocirc;ne ".Affiche_Icone('efface','efface')." permet d'effacer la zone de saisie.<br />";
		break;
	case 'Completude_Nom' : 
		echo "Cette page permet de v&eacute;rifier la compl&eacute;tude des informations sur les personnes portant un nom.<br /><br />";
		echo "Sont v&eacute;rifi&eacute;es :";
		echo "<ul>";
		echo "<li>la pr&eacute;sence de la date et du lieu de naissance ;</li>";
		echo "<li>la pr&eacute;sence de la date et du lieu de d&eacute;c&egrave;s si la personne est d&eacute;c&eacute;d&eacute;e (une personne n&eacute;e il y a plus de 130 ans est r&eacute;put&eacute;e d&eacute;c&eacute;d&eacute;e) ;</li>";
		echo "<li>la pr&eacute;sence des 2 parents ;</li>";
		echo "<li>la pr&eacute;sence d'un conjoint avec une date et un lieu d'union (si la personne est d&eacute;c&eacute;d&eacute;e apr&egrave;s l'&acirc;ge de 15 ans).</li>";
		echo "</ul>";
		echo "Une information pr&eacute;sente et pr&eacute;cise est mat&eacute;rialis&eacute;e par un drapeau vert ; une information absente par un drapeau rouge. Une date approximative est mat&eacute;rialis&eacute;e par un drapeau orange.<br /><br />";
		echo "Cette page ne permet pas de valider la pertinence des informations pr&eacute;sentes ; ceci est r&eacute;alis&eacute; via la fonction de v&eacute;rification des personnes.<br />";
		break;
	case 'Conv_Romain' : 
		echo "Cette page permet de convertir des nombres romains en nombres arabes et inversement.<br />";
		echo "L'utilisateur tape un nombre romain ou arabe via le clavier ou en cliquant sur les boutons ad hoc ;";
		echo 'il doit ensuite cliquer sur le bouton conversion ou se positionner dans la zone de saisie et appuyer sur la touche "Entr&eacute;e"<br />';
		echo "L'ic&ocirc;ne ".Affiche_Icone('efface','efface')." permet d'effacer la zone de saisie.<br />";
		echo "Les nombres arabes sont limit&eacute;s &agrave; 3999.<br />";
		echo "Les saisies de lettres romaines peuvent &ecirc;tre faites en minuscules ou majuscules.";
		break;
	case 'Desc_Directe_Pers' : 
		echo "Cette page permet de lister la descendance directe d'une personne vers le de cujus.";
		echo "Pour cela, il faut que la personne soit dans l'ascendance directe du de cujus. G&eacute;n&eacute;amania consid&egrave;re que c'est le cas si le num&eacute;ro sosa";
		echo "de la personne est renseign&eacute; et s'il s'agit d'un nombre.";
		echo "La descendance est recherch&eacute;e, non pas par les filiations, mais par les num&eacute;ros sosa successifs.<br />";
		echo "La sortie peut se faire au format texte ou au format HTML avec des liens cliquables (personnes,"
					.Affiche_Icone('arbre_asc','arbre ascendant')." arbre ascendant,"
					.Affiche_Icone('arbre_desc','arbre descendant')."arbre descendant).<br />";
		echo "Les conjoints sont affichables selon le choix de l'utilisateur.<br />";
		echo "NB : l'affichage des personnes dont la visibilit&eacute; internet est restreinte est fonction du profil de l'utilisateur.";
		break;
	case 'Edition_Contribution' : 
		echo "Cette page permet de prendre en compte une contribution propos&eacute;e par un utilisateur du net.<br />";
		echo "L'ensemble des traitements est d&eacute;clench&eacute; si l'utilisateur clique sur le bouton ".$lib_Okay."<br />";
		echo "En r&egrave;gle g&eacute;n&eacute;rale, l'utilisateur peut choisir de modifier une personne existante, d'en cr&eacute;er une (selon les cas) ou d'ignorer la proposition pour la personne.<br />";
		echo "<ul>";
		echo "<li>Pour le p&egrave;re :<br />";
		echo "Si le p&egrave;re existe, l'utilisateur peut remplacer le p&egrave;re connu ou ignorer la proposition.";
		echo "Si le p&egrave;re n'existe pas, l'utilisateur peut cr&eacute;er le p&egrave;re (la filiation est automatiquement cr&eacute;&eacute;e) ou ignorer la proposition.";
		echo "La page pr&eacute;sente en gras les zones du p&egrave;re qui sont modifi&eacute;es et en italique, les zones absentes de la proposition et qui sont reprises du p&egrave;re existant.</li>";
		echo "<li>Pour la m&egrave;re :<br />";
		echo "Le comportement est le m&ecirc;me. A l'issue du traitement des parents, l'union des parents est &eacute;ventuellement cr&eacute;&eacute;e (s'il y a eu cr&eacute;ation du p&egrave;re et / ou de la m&egrave;re) ou modifi&eacute;e.";
		echo "</li>";
		echo "<li>Pour le conjoint :<br />";
		echo "La page pr&eacute;sente la liste des conjoints connus pour la personne. L'utilisateur peut alors choisir de remplacer un conjoint existant, d'en cr&eacute;er un nouveau ou d'ignorer la proposition ;";
		echo "dans ce cas, il y a cr&eacute;ation automatique de l'union entre le conjoint cr&eacute;&eacute; et la personne.";
		echo "</li>";
		echo "<li>Pour les enfants :<br />";
		echo "La page pr&eacute;sente la liste des enfants connus pour la personne. L'utilisateur peut alors choisir de remplacer un (ou deux) enfant existant(s), d'en cr&eacute;er un (ou deux) nouveau(x) ou d'ignorer la proposition ;";
		echo "dans ce cas, il y a cr&eacute;ation automatique de la filiation entre l'enfant cr&eacute;&eacute; et la personne.";
		echo "Attention, la filiation cr&eacute;&eacute;e ne r&eacute;f&eacute;rence pas le conjoint dans la mesure o&ugrave; le syst&egrave;me ne saurait pas forc&eacute;ment &agrave; quel conjoint rattacher la filiation.";
		echo "</li>";
		echo "</ul>";
		echo "";
		echo "A l'issue du traitement, la contribution est r&eacute;put&eacute;e trait&eacute;e si l'utilisateur clique sur ".$lib_Okay."<br /><br />";
		echo "";
		echo "Cette page n'est disponible que pour le profil gestionnaire.";
		break;
	case 'Edition_Evenement' : 
		echo "Cette page permet de cr&eacute;er, modifier et supprimer un &eacute;v&egrave;nement.<br />";
		echo "Les zones obligatoires sont le titre de l'&eacute;v&egrave;nement et son type.<br />";
		echo "Le lieu de survenance de l'&eacute;v&egrave;nement peut &ecirc;tre choisi en cliquant sur l'ic&ocirc;ne ".Affiche_Icone('localisation','localisation')."<br />";
		echo "Les dates de d&eacute;but et de fin peuvent &ecirc;tre choisis en cliquant sur l'ic&ocirc;ne ".Affiche_Icone('calendrier','calendrier')."&nbsp;";
		echo "alors que l'ic&ocirc;ne ".Affiche_Icone('copie_calend','copie')." permet de copier la date de début dans la date de fin.<br />";
		echo 'La zone "Visibilit&eacute; Internet du commentaire" permet de masquer ou non l\'affichage de la note sur internet ; elle n\'a aucun effet en local.<br />';
		echo $auto_contrib;
		break;
	case 'Edition_Image' : 
		echo "Cette page permet de rattacher, modifier ou supprimer le rattachement d'une image &agrave; une personne, une ville, une union ou un &eacute;v&egrave;nement.<br />";
		echo "En cr&eacute;ation, si la description ou le nom du fichier de l'image sont absents, aucun lien ne sera cr&eacute;&eacute;.";
		echo "En modification, la re-saisie du nom de l'image n'est pas n&eacute;cessaire.<br />";
		echo "L'image est limit&eacute;e &agrave; ".($taille_maxi_images['s']/1024);
		echo "Ko (param&eacute;trable) pour des dimensions maximum de ".$taille_maxi_images['w'].' x '.$taille_maxi_images['h']." pixels<br />";
		echo 'Le bouton radio "Image par d&eacute;faut" permet de sp&eacute;cifier si cette image s\'affichera pas d&eacute;faut pour l\'objet concern&eacute; (e.g. pour une personne sur la fiche familiale, l\'arbre).';
		echo 'La valeur par d&eacute;faut est "Non".<br />';
		echo 'La case &agrave; cocher "Visibilit&eacute; de l\'image sur internet " permet de sp&eacute;cifier si cette image s\'affichera ou non sur Internet pour un profil invit&eacute ; ';
		echo "si elle n'est pas coch&eacute;e, l'utilisateur devra avoir un profil au moins privil&eacute;gi&eacute; pour la voir sur Internet.<br />";
		echo $auto_contrib;
		break;
	case 'Edition_Lier_Eve' : 
		echo "Cette page permet de lier un &eacute;v&egrave;nement &agrave; une personne.<br />";
		echo "Vous pouvez d&eacute;finir plusieurs participations d'une personne &agrave; un &eacute;v&egrave;nement avec des r&ocirc;les diff&eacute;rents. Par contre, une personne ne peut pas participer plusieurs fois &agrave; un m&ecirc;me &eacute;v&egrave;nement avec le m&ecirc;me r&ocirc;le. <br />";
		echo $auto_contrib;
		break;
	case 'Edition_Lier_Nom' : 
		echo "Cette page permet d'assigner un nom secondaire &agrave; une personne.<br />";
		echo "Le nom secondaire est oppos&eacute; au nom principal de la personne en ce sens qu'il en repr&eacute;sente des variantes trouv&eacute;es sur certains actes.<br />";
		echo "Vous pouvez commenter chaque lien vers un nom secondaire, par exemple en indiquant l'acte sur lequel a &eacute;t&eacute; trouv&eacute; le nom.<br />";
		echo "Il est &agrave; noter que si le lien existe, seule la modification du commentaire sera autoris&eacute;e.<br />";
		break;
	case 'Edition_Lier_Pers' : 
		echo "";
		echo "Cette page permet de créer des relations entre deux personnes.<br />";
		echo "Vous pouvez d&eacute;finir plusieurs liens d'une personne avec une autre avec des r&ocirc;les diff&eacute;rents, mais pas avec le m&ecirc;me r&ocirc;le. <br />";
		echo "Les zones obligatoires sont la personne li&eacute;e et le r&ocirc;le.<br />";
		echo "Les dates de d&eacute;but et de fin peuvent &ecirc;tre choisies en cliquant sur l'ic&ocirc;ne ".Affiche_Icone('calendrier','calendrier')."&nbsp;";
		echo "alors que l'ic&ocirc;ne ".Affiche_Icone('copie_calend','copie')." permet de copier la date de début dans la date de fin.<br />";
		echo $auto_contrib;
		break;
	case 'Edition_NomFam' : 
		echo "Cette page permet de modifier un nom de famille ainsi que sa prononciation.<br /><br />";
		echo "<b>Saisie du nom de famille</b><br />";
		echo "Vous pouvez modifier le nom de famille. Pour placer des caract&egrave;res accentu&eacute;s, vous pouvez les saisir";
		echo "en minuscules puis cliquer sur l'ic&ocirc;ne ".Affiche_Icone('majuscule','majuscule')." pour mettre le nom en majuscules.<br />";
		echo "<br /><b>Prononciation</b><br />";
		echo "Pour la prononciation du nom, le bouton &laquo; Prononciation calcul&eacute;e &raquo; d&eacute;termine une prononciation du nom &agrave; partir des r&egrave;gles de prononciation du fran&ccedil;ais.";
		echo "Ces r&egrave;gles sont complexes et parfois difficiles &agrave; appliquer, ainsi la prononciation propos&eacute;e peut ne pas &ecirc;tre correcte.";
		echo "Vous pouvez la corriger.<br />";
		echo "Vous pouvez d&eacute;placer le curseur en cliquant sur les fl&egrave;ches &laquo; <-- &raquo; et &laquo; --> &raquo;.<br />";
		echo "Pour supprimer un son, placez le curseur apr&egrave;s celui-ci et cliquez sur &laquo; Effacer &raquo;.<br />";
		echo "Les boutons marqu&eacute;s d'une ou deux lettres permettent d'ajouter le son correspondant &agrave; l'endroit du curseur.<br />";
		echo "Quand votre souris arrive sur un de ces boutons, quelques exemples de mots contenant le son s'affichent en dessous du tableau.";
		break;
	case 'Edition_Personne' : 
		echo "Les zones obligatoires sont le nom et les pr&eacute;noms.<br />";
		echo "Cette page permet de cr&eacute;er ou modifier une personne.<br />";
		echo "La date de naissance ou de d&eacute;c&egrave;s peut &ecirc;tre choisie en cliquant sur l'ic&ocirc;ne ".Affiche_Icone('calendrier','calendrier')."<br />";
		echo "Les professions sont g&eacute;r&eacute;es dans les &eacute;v&egrave;nements.<br />";
		echo "L'ic&ocirc;ne ".Affiche_Icone('ajout','ajout ville')." permet d'ajouter dynamiquement une ville aux listes des villes de naissance ou de d&eacute;c&egrave;s.<br />";
		echo "L'ic&ocirc;ne ".Affiche_Icone('calculette','calculette')." permet de calculer le num&eacute;ro sosa &agrave; partir de la saisie effectu&eacute;e par l'utilisateur dans le num&eacute;ro. ";
		echo "Les calculs disponibles sont &quot;p&egrave;re&quot; (P), &quot;m&egrave;re&quot; (M), &quot;enfant&quot; (E) ou &quot;conjoint&quot; (C). Par exemple, si l'utilisateur veut calculer la m&egrave;re de la personne de num&eacute;ro ";
		echo "sosa 10, il saisit =M10 dans le num&eacute;ro ; un clic sur l'ic&ocirc;ne transforme le num&eacute;ro saisi en 21 (m&egrave;re de 10 dans la num&eacute;rotation sosa). Il est &agrave; noter que le ";
		echo "calcul est insensible &agrave; la casse ; ainsi =m10 a le m&ecirc;me effet que =M10.";
		echo "<br />";
		echo "L'ic&ocirc;ne ".Affiche_Icone('decujus','de cujus')." permet d'attribuer automatiquement le num&eacute;ro 1 (de cujus) &agrave; la personne.<br />";
		echo "L'ic&ocirc;ne ".Affiche_Icone('copier','copie')." permet de coller le nom, la ville de naissance ou de d&eacute;c&egrave;s de la fiche pr&eacute;c&eacute;dente sur laquelle &eacute;tait l'utilisateur en cr&eacute;ation ou modification.<br />";
		echo "<br />";
		echo "Le bouton ".$lib_Supprimer." n'est affich&eacute; que si la personne n'est pas dans une union, qu'elle n'a pas de filiation ";
		echo "et qu'elle n'est pas dans une filiation en tant que parent.<br /><br />";
		echo $auto_contrib;
		break;
	case 'Edition_Rangs' : 
		echo "Cette page permet rectifier les rangs des enfants d'un couple.<br />";
		echo "Pour chaque enfant, G&eacute;n&eacute;amania calcule un rang th&eacute;orique <b>si la date de naissance est connue de mani&egrave;re pr&eacute;cise</b>.<br />";
		echo "En cas de divergence entre le rang th&eacute;orique et le rang saisi, la zone du rang calcul&eacute; est suivie de l'icône ".Affiche_Icone('warning','Alerte');
		echo "L'utilisateur peut rectifier en masse les rangs en cliquant sur le bouton &quot;Accepter les rangs calcul&eacute;s&quot;.";
		echo "La mise &agrave; jour n'est effective qu'apr&egrave;s avoir cliqu&eacute; sur le bouton &quot;".$lib_Okay."&quot;.<br />";
		echo "De m&ecirc;me, si les dates de naissance sont connues, G&eacute;n&eacute;amania calcule un &eacute;cart th&eacute;orique en mois / ann&eacute;es entre les naissances.";
		echo 'Si l\'&eacute;cart avec l\'enfant pr&eacute;c&eacute;dent est de moins de 9 mois, la zone "Ecart calcul&eacute;" est suivie de l\'ic&ocirc;ne '.Affiche_Icone('warning','Alerte')."<br />";
		break;
	case 'Edition_Utilisateur' : 
		echo "";
		echo "Cette page permet de d&eacute;finir un utilisateur qui sera utilis&eacute; dans la version Internet de G&eacute;n&eacute;amania. Le <strong>nom</strong> est un rappel du nom r&eacute;el de la personne. Le <strong>code utilisateur</strong> et le <strong>mot de passe</strong> serviront pour s'identifier sur la page d'accueil de G&eacute;n&eacute;amania. Le <strong>niveau</strong> sert &agrave; d&eacute;finir les possibilit&eacute;s que vous accordez &agrave; cet utilisateur. On distingue 4 niveaux :";
		echo "<ul>";
		echo "<li>invit&eacute; : il peut consulter toutes les pages en respectant les verrouillages l&eacute;gaux d'acc&egrave;s aux informations personnelles ;</li>";
		echo "<li>privil&eacute;gi&eacute; : cet utilisateur peut consulter toutes les pages sans qu'il y ait de verrouillage d'acc&egrave;s aux informations personnelles. Il peut signaler au gestionnaire  des modifications par le syst&egrave;me des contributions, accessible sur la fiche d'une personne; </li>";
		echo "<li>contributeur : cette personne peut faire des modifications dans la base.</li>";
		echo "<li>gestionnaire : c'est la personne qui peut tout faire sur le logiciel.</li>";
		echo "</ul>";
		echo "Un internaute qui acc&egrave;de &agrave; une g&eacute;n&eacute;alogie a des <strong>droits d'invit&eacute;</strong>. Cela correspond &agrave; toute personne qui veut consulter votre travail. Il n'est pas n&eacute;cessaire de cr&eacute;er un utilisateur invit&eacute;, cela est fait automatiquement. <br />";
		echo "Vous d&eacute;clarerez en <strong>utilisateur privil&eacute;gi&eacute;</strong> une personne en qui vous avez confiance et qui pourra vous signaler des modifications par le syst&egrave;me des contributions. Ces personnes ne peuvent rien modifier. Vous pouvez cr&eacute;er autant d'utilisateurs privil&eacute;gi&eacute;s que vous voulez.<br />";
		echo "Pour travailler dans des conditions de s&eacute;curit&eacute; correctes, il faut &ecirc;tre vigilent lorsque vous d&eacute;finissez un mot de passe. Les recommandations habituelles en la mati&egrave;re sont :";
		echo "<ul>";
		echo "<li>qu'il contienne au moins 8 caract&egrave;res ;</li>";
		echo "<li>qu'il ne soit pas un mot d'une langue quelconque.</li>";
		echo "</ul>";
		echo "M&eacute;langez les lettres majuscules, minuscules, les chiffres et utilisez les caract&egrave;res qui sont plus rarement utilis&eacute;s :";
		echo "<ul>";
		echo "<li>les diacritiques (&eacute;, &egrave;, &agrave;, &ccedil;, &acirc;, &ecirc;, &icirc;, &ocirc;, &ucirc;) ;</li>";
		echo "<li>les symboles (&amp;, #, $, &euro;, &sect;, @, \, /) ; </li>";
		echo "<li>les signes de ponctuation (, ; . : ! ? { } [ ] ( )) ; </li>";
		echo "<li>les symboles math&eacute;matiques (+, -, *, /, %).</li>";
		echo "</ul>";
		echo "Pour m&eacute;moriser plus facilement un mot de passe efficace, vous pouvez prendre une phrase que vous m&eacute;moriserez facilement et vous conservez la premi&egrave;re lettre de chaque mot. Vous pouvez remplacer les s ou S par $, les o ou O par 0 (z&eacute;ro), les a par @. Par exemple, la phrase &laquo;J'ai achet&eacute; 5 oeufs pour 3 euros&raquo; peut donner &laquo;j@50p3&euro;&raquo;.<br />";
		break;
	case 'Export' : 
		echo "Cette page permet d'exporter les donn&eacute;es de la base.";
		echo "L'export peut &ecirc;tre de type sauvegarde ou Internet.";
		echo "Ce dernier mode permet d'exporter ses donn&eacute;es dans un fichier afin de les recharger sur un site Internet.<br />";
		echo "En export Internet, les donn&eacute;es de la table 'compteurs' ne sont pas export&eacute;es ; en effet, il s'agit des statistiques";
		echo "de fr&eacute;quentation du site. De plus, la table 'general' est modifi&eacute;e afin de positionner le mode Internet.<br />";
		echo "L'export 'Site gratuit' permet d'exporter ses donn&eacute;es au format texte afin de les charger sur un site personnel h&eacute;berg&eacute; sur la plateforme G&eacute;n&eacute;amania.<br />";
		echo "L'option 'Masquage des dates r&eacute;centes' permet de ne pas exporter les dates trop r&eacute;centes afin de pr&eacute;server la confidentialit&eacute; de certaines donn&eacute;es (personnes vivantes par exemple).<br />";
		echo "L'utilisateur peut sp&eacute;cifier un pr&eacute;fixe &agrave; attacher au nom du fichier (cette possibilit&eacute; n'est pas offerte sur les sites gratuits standard).<br />";
		echo "L'ic&ocirc;ne ".Affiche_Icone('oeil','oeil')." permet de visualiser la liste des tables &agrave; exporter ; l'utilisateur";
		echo "peut ainsi choisir les tables qu'il souhaite exporter.<br />";
		echo "Le nom du fichier de sauvegarde par d&eacute;faut est Export_Sauvegarde.sql (Export_Complet.sql pour les versions ant&eacute;rieures &agrave; la 2.1) pour ";
		echo "la sauvegarde et Export_Internet.sql pour l'export Internet ; le suffixe &eacute;ventuel est ins&eacute;r&eacute; avant le point ;";
		echo 'le modificateur de nom de fichier &eacute;ventuel est ins&eacute;r&eacute; apr&egrave;s la cha&icirc;ne "Export_".<br />';
		echo "Cette page n'est disponible que pour le profil gestionnaire.";
		break;
	case 'Fusion_Evenements' : 
		echo "";
		echo "Cette page permet de fusionner les &eacute;v&egrave;nements pr&eacute;sents en base.";
		echo "Les &eacute;v&egrave;nements pr&eacute;sentant les m&ecirc;mes lieux, type, titre et dates peuvent &ecirc;tre fusionn&eacute;s automatiquement par G&eacute;n&eacute;amania.<br />";
		echo "La page s'affiche dans un premier temps en mode visualisation pour permettre &agrave; l'utilisateur de voir ce que G&eacute;n&eacute;amania va faire en terme de fusion.<br />";
		echo "Cette page pr&eacute;sente une liste des groupes d'&eacute;v&egrave;nements qui peuvent &ecirc;tre fusionn&eacute;s.";
		echo "Pour chaque groupe, le titre de l'&eacute;v&egrave;nement est pr&eacute;cis&eacute; ; ensuite vient l'&eacute;v&egrave;nement de r&eacute;f&eacute;rence et chaque &eacute;v&egrave;nement &quot;doublon&quot;.";
		echo "L'utilisateur peut visualiser la r&eacute;f&eacute;rence et les doublons en cliquant sur le lien ad-hoc.";
		echo "De plus, G&eacute;n&eacute;amania indique le nombre de participations (donc de personnes), d'images et de documents rattach&eacute;s &agrave; cet &eacute;v&egrave;nement.<br />";
		echo "La fusion sera effective lorsque l'utilisateur d&eacute;cochera la case &quot;Mode simulation&quot; et cliquera sur le bouton &quot;Fusionner&quot;.";
		echo "";
		break;
	case 'Histo_Ages_Deces' : 
		echo "Cette page permet de visualiser la r&eacute;partition des &acirc;ges de d&eacute;c&egrave;s des personnes contenues dans la base pour une p&eacute;riode de naissance donn&eacute;e.";
		echo "Si l'utilisateur n'a pas de profil privil&eacute;gi&eacute;, seules sont prises en compte les personnes dont la visibilit&eacute; Internet est n'est pas restreinte.<br />";
		echo "Contrairement &agrave; l'historique de l'&acirc;ge, les enfants d&eacute;c&eacute;d&eacute;s avant l'&acirc;ge de 1 an sont pris en compte.<br />";
		echo "Pour chaque tranche d'&acirc;ge, le nombre de personnes et le pourcentage que cela repr&eacute;sente sont pr&eacute;cis&eacute;s.";
		break;
	case 'Histo_Ages_Mariage' : 
		echo "Cette page permet de visualiser la r&eacute;partition des &acirc;ges de premier mariage des personnes contenues dans la base pour une p&eacute;riode de naissance donn&eacute;e.";
		echo "Si l'utilisateur n'a pas de profil privil&eacute;gi&eacute;, seules sont prises en compte les personnes dont la visibilit&eacute; Internet n'est pas restreinte.<br />";
		echo "Pour chaque tranche d'&acirc;ge sont pr&eacute;cis&eacute;s le nombre de personnes et le pourcentage que cela repr&eacute;sente.";
		break;
	case 'Import_CSV' : 
		echo "Cette option permet d'int&eacute;grer dans la base des donn&eacute;es issues d'un tableur (Libre Office, Excel...).<br />";
		echo "A ce jour, il est possible d'int&eacute;grer des donn&eacute;es concernant les personnes uniquement (&agrave; l'exclusion des filiations et unions).<br />";
		echo "L'utilisateur doit indiquer la correspondance entre les colonnes du tableur et les champs de G&eacute;n&eacute;mania. Seules sont obligatoires les zones contenant les noms et pr&eacute;noms.<br />";
		echo "Le s&eacute;parateur de champs est le caract&egrave;re Â« ; Â». Les dates sont au format JJ/MM/AAAA ou JJ-MM-AAAA. Les zones textuelles ne sont pas entour&eacute;es de guillemets.<br />";
		echo "Voici un exemple de contenu de fichier :<br />";
		echo "Durand;Robert 1;30/11/1965;Amiens;m;27C<br />";
		echo "Durand;Marcel;3-5-1966;Amiens;m;27D<br />";
		break;
	case 'Import_Docs' : 
		echo "Lorsqu'un utilisateur a &agrave; la fois un site local sur son ordinateur et un site internet, il remonte les donn&eacute;es de son site local";
		echo "vers son site internet en utilisant les fonctions d'export. Toutefois, ceci permet de remonter les donn&eacute;es mais pas les images ou autres documents.";
		echo "L'utilisateur doit alors remonter ces images et documents via un logiciel de transfert de fichiers (exemple Filezilla) lorsque cela est possible.";
		echo "Lorsque cela n'est pas possible, il doit remonter ces fichiers via la fonction d'import de documents.<br />";
		echo "Les images et documents absents sont ceux qui ont &eacute;t&eacute; trouv&eacute;s dans les donn&eacute;es mais pour lequel le fichier n'est pas pr&eacute;sent.<br />";
		echo 'L\'option "Remplacer" permet de ne pas &eacute;craser les fichiers de m&ecirc;me nom pr&eacute;sents.';
		break;
	case 'Import_Gedcom' : 
		echo "Cette page permet de recharger les donn&eacute;es de la base &agrave; partir d'un fichier Gedcom";
		echo " ou d'afficher les donn&eacute;es pr&eacute;sentes dans le fichier.<br />";
		echo "Le nom du fichier de sauvegarde par d&eacute;faut est export_gedcom.ged et se situe dans le r&eacute;pertoire Gedcom.<br />";
		echo "Signification des cases &agrave; cocher :";
		echo "<ul>";
		echo '<li>"Charger les donn&eacute;es dans la base" permet de charger le fichier dans la base ;&nbsp;';
		echo 'lorsqu\'elle n\'est pas coch&eacute;e, le fichier est juste lu et les donn&eacute;es contenues dans le fichier sont affich&eacute;es &agrave; l\'&eacute;cran.</li>';
		echo '<li>"Vidage pr&eacute;alable de la base actuelle" permet de vider la base avant de charger le fichier. Attention, les donn&eacute;es pr&eacute;-existantes seront donc effac&eacute;es.</li>';
		echo '<li>"Visibilit&eacute; internet autoris&eacute;e par d&eacute;faut" permet d\'indiquer que les personnes charg&eacute;es &agrave; partir du fichier seront visibles sur Internet sans restriction.</li>';
		echo '<li>"Visibilit&eacute; internet des notes autoris&eacute;e par d&eacute;faut" permet d\'indiquer que les notes charg&eacute;es &agrave; partir du fichier seront visibles sur Internet de profil.</li>';
		echo '<li>"Visibilit&eacute; internet des images autoris&eacute;e par d&eacute;faut" permet d\'indiquer que les images reprises &agrave; partir du fichier seront visibles sur Internet, si elles ont &eacute;t&eacute; charg&eacute;es par ailleurs.</li>';
		echo '<li>"Valeur par d&eacute;faut des fiches cr&eacute;&eacute;es" permet de sp&eacute;cifier le statut que prendront les fiches cr&eacute;&eacute;es lors de l\'import.</li>';
		echo '<li>"Reprise des dates de modification du fichier" permet d\'indiquer que les dates de modification des personnes et des autres donn&eacute;es seront celles du fichier ;';
		echo "si la case n'est pas coch&eacute;e, la date de modification sera la date du jour.</li>";
		echo "</ul>";
		echo "Le format des lieux permet de s&eacute;lectionner l'arborescence des zones g&eacute;ographiques pr&eacute;sentes dans le fichier. Par d&eacute;faut, le format est compos&eacute; uniquement des villes.";
		echo "Le format est sp&eacute;cifi&eacute; en s&eacute;lectionnant successivement chaque niveau (e.g. ville, d&eacute;partement, r&eacute;gion, pays) dans la liste d&eacute;roulante.";
		echo "L'ic&ocirc;ne ".Affiche_Icone('efface','Efface le format des lieux')." permet d'effacer le format des lieux pr&eacute;c&eacute;demment s&eacute;lectionn&eacute;.";
		echo "L'arborescence est prise automatiquement en compte si elle est sp&eacute;cifi&eacute;e dans l'ent&ecirc;te du fichier &agrave; charger (balises PLAC/FORM).";
		echo "<br /><br />Cette page n'est disponible que pour le profil gestionnaire.";
		break;
	case 'Import_Sauvegarde' : 
		echo "Cette page permet de recharger les donn&eacute;es de la base &agrave; partir d'un fichier de sauvegarde.<br />";
		echo 'L\'utilisateur peut demander &agrave; effacer pr&eacute;alablement le contenu de la base en cochant la case "Vidage pr&eacute;alable de la base actuelle".';
		echo "Attention, dans ce cas, il s'agit de toute la base dans laquelle les donn&eacute;es G&eacute;n&eacute;amania sont implant&eacute;es.";
		echo "N'utilisez pas cette option si G&eacute;n&eacute;amania partage la base d'une autre application !";
		echo "Cette option peut &ecirc;tre utilis&eacute;e dans le cas de la reprise d'une sauvegarde de version ant&eacute;rieure si vous voulez migrer cette sauvegarde vers la version actuelle.<br />";
		echo "Le fichier de sauvegarde peut &ecirc;tre t&eacute;l&eacute;charg&eacute; par l'utilisateur ou s&eacute;lectionn&eacute; parmi les fichiers pr&eacute;sents dans le r&eacute;pertoire des exports.";
		echo "Dans le cas o&ugrave; l'utilisateur t&eacute;l&eacute;charge un fichier et en s&eacute;lectionne un en m&ecirc;me temps, c'est le fichier t&eacute;l&eacute;charg&eacute; qui prime.<br />";
		echo "Sur un site h&eacute;berg&eacute; gratuit, seuls les fichiers .txt sont autoris&eacute;s ; dans les autres cas, les fichiers .txt et .sql sont autoris&eacute;s.<br />";
		echo "Attention : les donn&eacute;es pr&eacute;sentes en base sont supprim&eacute;es par le rechargement (en effet, ";
		echo "la sauvegarde inclue des ordres de suppression et recr&eacute;ation de tables).<br />";
		echo "La sauvegarde peut &ecirc;tre recharg&eacute;e en local (sur votre ordinateur) ou sur votre site web distant ";
		echo "si votre h&eacute;bergeur le permet (connexion distante possible sur le port 3306 par exemple).";
		echo "Il faut cependant noter que cette possibilit&eacute; de rechargement distant est consommatrice de ressources ; il est conseill&eacute; de diminuer le nombre de ";
		echo "donn&eacute;es &agrave; charger sur votre base distante par exclusion de certaines tables (typiquement celles qui n'ont pas &eacute;volu&eacute; [pays, etc...]).<br />";
		echo "Sur Internet, l'utilisateur peut demander &agrave; pr&eacute;server la liste des utilisateurs pr&eacute;sents ; cela &eacute;vite par exemple lors d'un rechargement d'&eacute;craser cette liste &agrave; partir des utilisateurs locaux.<br />";
		echo "Cette page n'est disponible que pour le profil gestionnaire.";
		break;
	case 'Liste_Evenements' : 
		echo "Cette page permet de lister les &eacute;v&egrave;nements.<br />";
		echo "L'utilisateur peut choisir le type de d'&eacute;v&egrave;nement pour lequel il veut la liste (par d&eacute;faut tous les types sont visualis&eacute;s). Il dispose alors en plus du titre de l'&eacute;v&egrave;nement ";
		echo "d'informations sur les personnes concern&eacute;es par l'&eacute;v&egrave;nement (&eacute;ventuellement au travers de la filiation ou de l'union).<br />";
		echo "Seul le gestionnaire a acc&egrave;s &agrave; la modification de l'&eacute;v&egrave;nement.";
		break;
	case 'Liste_NomFam' : 
		echo "Cette page permet de lister les noms de famille.<br />";
		echo "&Agrave; partir de la liste, vous pouvez afficher un nom de famille et &eacute;ventuellement le modifier.<br />";
		echo "L'acc&egrave;s &agrave; la modification d&eacute;pend du profil de l'utilisateur.";
		break;
	case 'Liste_Nom_Vivants' : 
		echo "Cette page permet de lister les personnes vivantes pour un nom donn&eacute; ou pour l'ensemble des noms.<br />";
		echo "Sont consid&eacute;r&eacute;es comme d&eacute;c&eacute;d&eacute;es les personnes n&eacute;es il y a plus de 130 ans et non d&eacute;c&eacute;d&eacute;es. <br />";
		echo "L'utilisateur peut ignorer les personnes dont la date de naissance n'est pas saisie. Il consid&egrave;re alors qu'il n'a";
		echo "pas suffisamment d'informations sur la personne, donc pas de n&eacute;cessit&eacute; de s&eacute;lection, ou que l'anc&ecirc;tre est trop &eacute;loign&eacute;.<br />";
		echo "NB : l'affichage des personnes dont la diffusion internet est interdite est fonction du profil de l'utilisateur.";
		break;
	case 'Naissances_Deces_Mois' : 
		echo "Cette page permet de visualiser la r&eacute;partition mensuelle des naissances et des d&eacute;c&egrave;s des personnes contenues dans la base.";
		echo "En mode Internet, seules sont prises en compte les personnes dont la diffusion Internet est autoris&eacute;e.";
		echo "Le survol &agrave; la souris des barres du graphique permet de visualiser le nombre de personnes concern&eacute;es pour un mois donn&eacute;.";
		break;
	case 'Naissances_Mariages_Deces_Mois' : 
		echo "Cette page permet de visualiser la r&eacute;partition mensuelle des naissances, des conceptions th&eacute;oriques, des mariages et des d&eacute;c&egrave;s des personnes contenues dans la base.<br />";
		echo "La conception th&eacute;orique est calcul&eacute;e en retranchant 9 mois &agrave; la date de naissance.<br />";
		echo "Si l'utilisateur n'a pas de profil privil&eacute;gi&eacute;, seules sont prises en compte les personnes dont la diffusion Internet est autoris&eacute;e ; les mariages sont comptabilis&eacute;s si la diffusion Internet des 2 personnes est autoris&eacute;e.<br />";
		echo "Le survol &agrave; la souris des barres du graphique permet de visualiser le nombre de personnes ou mariages concern&eacute;s pour un mois donn&eacute;.";
		break;
	case 'Pers_Isolees' : 
		echo "Cette page permet de lister les personnes isol&eacute;es de la base.<br />";
		echo "Par personne isol&eacute;e, on entend une personne sans filiation, ni union, ni relation avec une autre personne.<br />";
		echo "Cette page est accessible à partir du profil contributeur.";
		break;
	case 'Pyramide_Ages_Histo' : 
		echo "Cette page permet de visualiser l'&eacute;volution (en fonction de l'année de naissance) de l'&acirc;ge au d&eacute;c&egrave;s des personnes contenues dans la base.";
		echo "Si l'utilisateur n'a pas de profil privil&eacute;gi&eacute;, seules sont prises en compte les personnes dont la visibilit&eacute; Internet est n'est pas restreinte.";
		echo "De plus, les enfants d&eacute;c&eacute;d&eacute;s avant l'&acirc;ge de 1 an ne rentrent pas dans la statistique afin de ne pas biaiser la moyenne.<br />";
		echo "Le survol &agrave; la souris des barres du graphique permet de visualiser le nombre de personnes concern&eacute;es sur la p&eacute;riode.<br />";
		echo "En cliquant sur la p&eacute;riode mentionn&eacute;e au milieu, l'utilisateur peut visualiser la r&eacute;partition des &acirc;ges de d&eacute;c&egrave;s des personnes pour la p&eacute;riode concern&eacute;e.";
		break;
	case 'Pyramide_Ages' : 
		echo "Cette page permet de visualiser la pyramide des &acirc;ges au d&eacute;c&egrave;s des personnes contenues dans la base.<br />";
		echo "Si l'utilisateur n'a pas de profil privil&eacute;gi&eacute;, seules sont prises en compte les personnes dont la la visibilit&eacute; Internet est n'est pas restreinte.";
		echo "Le survol &agrave; la souris des barres du graphique permet de visualiser le nombre de personnes concern&eacute;es pour un &acirc;ge donn&eacute;.";
		echo "De plus, on peut se d&eacute;brancher sur la fiche de la doyenne ou du doyen.";
		break;
	case 'Pyramide_Ages_Mar_Histo' : 
		echo "Cette page permet de visualiser l'&eacute;volution (en fonction de l'année de naissance) de l'&acirc;ge de premier mariage des personnes contenues dans la base.";
		echo "Si l'utilisateur n'a pas de profil privil&eacute;gi&eacute;, seules sont prises en compte les personnes dont la visibilit&eacute; Internet est n'est pas restreinte.";
		echo "Le survol &agrave; la souris des barres du graphique permet de visualiser le nombre de personnes concern&eacute;es sur la p&eacute;riode.<br />";
		echo "En cliquant sur la p&eacute;riode mentionn&eacute;e au milieu, l'utilisateur peut visualiser la r&eacute;partition des &acirc;ges de premier mariage des personnes pour la p&eacute;riode concern&eacute;e.";
		break;
	case 'Recherche_Commentaire' : 
		echo "Cette page, accessible aux personnes de profil gestionnaire, permet &agrave; l'utilisateur d'effectuer une recherche dans les commentaires stock&eacute;s dans la base,";
		echo "quel que soit l'objet point&eacute; par le commentaire (personne, union, zone g&eacute;ographique...).<br />";
		echo "L'utilisateur n'est pas oblig&eacute; de saisir le contenu complet du commentaire ; de m&ecirc;me, la casse (minuscules / majuscules) n'est pas prise en compte.<br />";
		echo 'E.g., si l\'utilisateur saisit le mot "ancien" (sans les guillemets), les commentaires suivants pourront &ecirc;tre trouv&eacute;s :';
		echo "<ul>";
		echo "<li>Ancien d&eacute;partement de la Seine et Oise</li>";
		echo "<li>Naissance sur l'ancienne commune de ...</li>";
		echo "</ul>";
		echo "Le r&eacute;sultat peut avoir un format :";
		echo "<ul>";
		echo "<li>&eacute;cran : la liste est cliquable ; l'utilisateur peut alors acc&eacute;der &agrave; la personne, union...</li>";
		echo "<li>texte : ce format est destin&eacute; &agrave; &ecirc;tre imprim&eacute;.</li>";
		echo "<li>CSV : ce format est destin&eacute; &agrave; &ecirc;tre lu dans un tableur (LibreOffice, Excel...).</li>";
		echo "</ul>";
		break;
	case 'Recherche_Cousinage' :
		$max_gen = $max_gen_loc;
		if ($Environnement == 'I') $max_gen = $max_gen_int;
		echo "Cette page permet de rechercher l'anc&ecirc;tre commun &agrave; 2 personnes.<br />";
		echo "Cette recherche s'effectue sur ".$max_gen." g&eacute;n&eacute;rations au maximum.";
		echo "Si l'anc&ecirc;tre commun est trouv&eacute;, l'utilisateur peut visualiser sa fiche familiale, sous";
		echo "r&eacute;serve de diffusabilit&eacute;, ou ses arbres descendant ou ascendant.";
		echo "De m&ecirc;me pour toutes les personnes pr&eacute;sentes dans les 2 filiations.<br />";
		echo "En local, une case &agrave; cocher permet de sauvegarder la recherche. Cette sauvegarde peut &ecirc;tre utilis&eacute;e dans G&eacute;n&eacute;graphe pour g&eacute;n&eacute;rer le graphique correspondant &agrave; la recherche.";
		break;
	case 'Recherche_Personne_Archive' : 
		echo "Cette page permet de lister les dates &agrave; v&eacute;rifier aux archives.";
		echo "Ces dates concernent les personnes dont les fiches ne sont pas valid&eacute;es pour une ville donn&eacute;e et &eacute;ventuellement suivant une plage de dates.<br /><br />";
		echo "Le r&eacute;sultat peut avoir un format :";
		echo "<ul>";
		echo "<li>&eacute;cran : la fiche familiale des personnes est alors accessible en cliquant sur les nom et pr&eacute;noms de la liste";
		echo "et la fiche personne en cliquant sur l'icone ".Affiche_Icone('fiche_edition',$LG_modify)."</li>";
		echo "<li>texte : ce format est destin&eacute; &agrave; &ecirc;tre imprim&eacute;.</li>";
		echo "<li>CSV : ce format est destin&eacute; &agrave; &ecirc;tre lu dans un tableur (LibreOffice, Excel...).</li>";
		echo "</ul>";
		echo $auto_contrib;
		break;
	case 'Recherche_Personne' : 
		echo "Cette page permet &agrave; l'utilisateur d'effectuer une recherche multi-crit&egrave;re sur les personnes de la base.";
		echo "Elle ram&egrave;ne toutes les personnes r&eacute;pondant aux crit&egrave;res demand&eacute;s.";
		echo "En mode non privil&eacute;gi&eacute;, seules sont prises en compte les personnes dont la visibilt&eacute; Internet n'est pas restreinte.<br />";
		echo "Les crit&egrave;res portant sur des zones de type &quot;caract&egrave;res&quot; sont automatiquement mis en majuscules ; ainsi les pr&eacute;noms 'jean' et 'Jean' sont &eacute;quivalents.<br />";
		echo "Par d&eacute;faut, le champ recherch&eacute; doit &ecirc;tre &eacute;quivalent au champ saisi (sans consid&eacute;ration de casse) ;";
		echo "cependant, sur les zones de type &quot;caract&egrave;res&quot;, il est possible de faire des recherches partielles en introduisant un ou plusieurs caract&egrave;res &quot;joker&quot; * ;";
		echo "ainsi la recherche sur le nom 'du*' donne les personnes s'appelant 'Durand', 'Dupond', 'Dumoulin'...";
		echo "Demander '*du*' ram&egrave;nera toutes les personnes dont le nom contient la chaine de caract&egrave;res 'du' &agrave; un emplacement quelconque.<br />";
		echo "Exemple : pour avoir toutes les femmes de la base, on coche le bouton &quot;Femme&quot; et on lance la recherche.";
		echo "Si on veut affiner la recherche et obtenir les femmes dont l'un des pr&eacute;noms est &quot;Marie', on ajoutera '*marie*' dans la zone Pr&eacute;noms.<br /><br />";
		echo "<br />La recherche sur le nom peut &ecirc;tre orthographique, phon&eacute;tique exacte ou phon&eacute;tique approch&eacute;e.<br />";
		echo "La recherche phonétique exacte donne tous les noms se pronon&ccedil;ant de la m&ecirc;me fa&ccedil;on.<br />";
		echo "La recherche phon&eacute;tique approch&eacute;e fait des approximations sur la prononciation. Cela permet de rapprocher les sons suivants :";
		echo "<ul>";
		echo "<li>&laquo; a &raquo; et &laquo; &acirc; &raquo; ;</li>";
		echo "<li>&laquo; &eacute; &raquo; et &laquo; &egrave; &raquo; ;</li>";
		echo "<li>&laquo; o &raquo; et &laquo; &ocirc; &raquo; ;</li>";
		echo "<li>&laquo; in &raquo; et &laquo; un &raquo; ;</li>";
		echo "<li>&laquo; en &raquo; et &laquo; on &raquo; ;</li>";
		echo "<li>&laquo; n &raquo; et &laquo; gn &raquo;.</li>";
		echo "</ul>";
		echo "La recherche donne alors tous les noms de famille dont la prononciation correspond à celle du nom saisi tout en tenant compte des approximations.<br />";
		echo "<p>La sortie du résultat de la recherche peut s'effectuer sous liste cliquable (sortie &eacute;cran), sous format destin&eacute; &agrave; &ecirc;tre imprim&eacute; (sortie texte) ou sous forme de fichier CSV (pour un tableur, le s&eacute;parateur &eacute;tant le ";" ; disponible à partir du profil privilégié).</p>";
		break;
	case 'Recherche_Personne_CP' : 
		echo "Cette page permet &agrave; l'utilisateur d'effectuer une recherche multi-crit&egrave;re sur les personnes de la base. ";
		echo "Les crit&egrave;res s'appliquent aux conjoints ou parents et non &agrave; la personne elle-m&ecirc;me.<br>";
		echo "Exemple, rechercher les personnes dont un parent femme est n&eacute; &agrave; Paris.<br>";
		echo "Se r&eacute;f&eacute;rer &agrave; la recherche de personnes pour l'utilisation des crit&egrave;res.";
		break;
	case 'Recherche_Ville' : 
		echo "Cette page permet &agrave; l'utilisateur d'effectuer une recherche multi-crit&egrave;res sur les villes de la base.";
		echo "Elle ram&egrave;ne toutes les villes r&eacute;pondant aux crit&egrave;res demand&eacute;s.<br />";
		echo "Le nom de la ville recherch&eacute;e est automatiquement mis en majuscules ; ainsi les villes 'paris' et 'Paris' sont &eacute;quivalentes.<br />";
		echo "Par d&eacute;faut, le nom de la ville recherch&eacute; doit &ecirc;tre &eacute;quivalent au champ saisi (sans consid&eacute;ration de casse) ;";
		echo "il est cependant possible de faire des recherches partielles en introduisant un ou plusieurs caract&egrave;res &quot;joker&quot; * ;";
		echo "ainsi la recherche sur le nom 'p*' donne les villes 'Paris', 'Perpignan'...";
		echo "Demander '*ar*' ram&egrave;nera toutes les villes dont le nom contient la chaine de caract&egrave;res 'ar' &agrave; un emplacement quelconque.<br /><br />";
		echo "<p>La sortie du r&eacute;sultat de la recherche peut s'effectuer sous liste cliquable (sortie &eacute;cran), sous format destin&eacute; &agrave; &ecirc;tre imprim&eacute; (sortie texte) ou sous forme de fichier CSV (pour un tableur, le s&eacute;parateur &eacute;tant le ";" ; disponible &agrave; partir du profil privil&eacute;gi&eacute;).</p>";
		break;
	case 'Stat_Base_Depart' : 
		echo "Cette page permet de visualiser la r&eacute;partition des naissances et des d&eacute;c&egrave;s par d&eacute;partement.";
		echo "En mode Internet, seules sont prises en compte les personnes dont la diffusion Internet est autoris&eacute;e si l'utilisateur n'a pas un profil privil&eacute;gi&eacute;.<br />";
		echo "L'icone ".Affiche_Icone('carte_france','Carte de France')." permet de visualiser la r&eacute;partition g&eacute;ographique sur la carte de la France.";
		break;
	case 'Stat_Base_Villes' : 
		echo "Cette page permet de visualiser la r&eacute;partition des naissances, mariages et des d&eacute;c&egrave;s par villes.<br />";
		echo "En cliquant sur une ville, on peut se d&eacute;brancher sur la fiche de la ville.";
		echo "En cliquant sur un nombre, on peut se d&eacute;brancher sur la liste des personnes n&eacute;es, mari&eacute;es ou d&eacute;c&eacute;d&eacute;es dans la ville.<br />";
		echo "En mode Internet, seules sont prises en compte les personnes dont la diffusion Internet est autoris&eacute;e si l'utilisateur n'a pas un profil privil&eacute;gi&eacute;.";
		break;
	case 'Verif_Internet_Absente' : 
		echo "Cette page permet de visualiser les personnes non visibles sur Internet mais d&eacute;c&eacute;d&eacute;es il y a plus ".$Lim_Diffu." ou n&eacute;es il y a plus de ".$Lim_Diffu_Dec." ans.<br>";
		echo "L'utilisateur peut rectifier les incoh&eacute;rences en cliquant sur le bouton &quot;Rectifier&quot;. ";
		echo "Seules sont modifi&eacute;es les lignes que l'utilisateur a <u>coch&eacute;es</u>.";
		echo "La visibilit&eacute; Internet des personnes coch&eacute;es passe alors &agrave; Oui, tout le monde pourra alors les visualiser.";
		break;
	case 'Verif_Internet' : 
		echo "Cette page permet de visualiser les personnes visibles sur Internet mais n&eacute;es ou d&eacute;c&eacute;d&eacute;es il y a moins de ".$Lim_Diffu." ans.";
		echo "Cela peut mettre en lumi&egrave;re des probl&egrave;mes de confidentialit&eacute; de donn&eacute;es.<br />";
		echo "L'utilisateur peut rectifier les incoh&eacute;rences en cliquant sur le bouton &quot;Rectifier&quot;.";
		echo "Seules sont modifi&eacute;es les lignes que l'utilisateur a <u>d&eacute;coch&eacute;es</u>.";
		echo "La visibilit&eacute; Internet des personnes d&eacute;coch&eacute;es passe alors &agrave; Non et ces personnes ne sont visibles que des utilisateurs ayant un profil au minimum privil&eacute;gi&eacute;.";
		break;
	case 'Verif_Personne' : 
		echo "Cette page affiche le r&eacute;sultat des contr&ocirc;les de la fiche d'une personne. Ils se font &agrave; plusieurs niveaux.";
		echo "<br /><strong>Pour la personne</strong><br />";
		echo "- que la fiche soit visible sur Internet ;<br />";
		echo "- que la fiche soit valid&eacute;e ;<br />";
		echo "- que les dates de naissance et de d&eacute;c&egrave;s (dans le cas des personnes non vivantes) soient pr&eacute;sentes et qu'elles correspondent &agrave; un jour pr&eacute;cis (le ...) ;<br />";
		echo "- que la date de naissance pr&eacute;c&egrave;de ou soit &eacute;gale &agrave; la date de d&eacute;c&egrave;s.";
		echo "<br /><strong>Avec ses parents :</strong><br />";
		echo "- que les dates de d&eacute;c&egrave;s du p&egrave;re et de la m&egrave;re soient pr&eacute;sentes (dans le cas des personnes non vivantes) et qu'elles correspondent &agrave; un jour pr&eacute;cis (le ...) ;<br />";
		echo "- que la personne soit n&eacute;e apr&egrave;s que le p&egrave;re et la m&egrave;re aient 15 ans ; <br />";
		echo "- que la personne soit n&eacute;e au plus tard 9 mois apr&egrave;s le d&eacute;c&egrave;s du p&egrave;re ou de la m&egrave;re.";
		echo "<br /><strong>Avec ses unions :</strong><br />";
		echo "- que la personne ait plus de 15 ans quand elle s'unit &agrave; une autre personne ;<br />";
		echo "- que la personne avec qui elle s'unit soit vivante lors de cette union.";
		echo "<br /><strong>Avec les enfants :</strong><br />";
		echo "- que les dates de naissance des enfants  soient soient pr&eacute;sentes et qu'elles correspondent &agrave; un jour pr&eacute;cis (le ...) ;<br />";
		echo "- que la personne ait au moins 15 ans &agrave; la naissance des enfants ;<br />";
		echo "- que la personne soit d&eacute;c&eacute;d&eacute;e depuis moins de 9 mois lors de la naissance des enfants.";
		break;
	case 'Verif_Sosa' : 
		echo "Cette page permet de visualiser les incoh&eacute;rences entre les num&eacute;ros Sosa saisis par l'utilisateur et ceux calcul&eacute;s par G&eacute;n&eacute;amania.";
		echo "La d&eacute;tection d'incoh&eacute;rence peut &ecirc;tre incorrecte dans le cas de personnes apparaissant plusieurs fois dans l'arbre (implexes).<br />";
		echo "Il est d'autre part &agrave; noter que cette v&eacute;rification ne balaye que les personnes dans l'ascendance du de cujus ; ainsi une personne hors de cette ascendance ne verra pas son num&eacute;ro contr&ocirc;l&eacute;.<br />";
		echo "La personne de r&eacute;f&eacute;rence sur laquelle s'appuie le calcul est le de cujus (num&eacute;ro 1).";
		echo "En cas d'absence de de cujus, G&eacute;n&eacute;amania affiche un message d'erreur.<br />";
		echo "L'utilisateur peut rectifier les incoh&eacute;rences en cliquant sur le bouton &quot;Rectifier&quot;.";
		echo "Seules sont modifi&eacute;es les lignes que l'utilisateur a coch&eacute;es (la case &quot;tous&quot; permet de cocher / d&eacute;cocher toutes les lignes.";
		break;
	case 'Vue_Personnalisee' : 
		echo "Cette page permet de choisir un de cujus diff&eacute;rent de celui par d&eacute;faut pour les listes par g&eacute;n&eacute;rations et patronymique.<br />";
		echo 'Le de cujus personnalis&eacute; est m&eacute;moris&eacute; lorsque l\'utilisateur clique sur bouton "'.$lib_Okay.'". Il n\'est valable que pour la session en cours.';
		break;
	case 'Liste_Noms_Non_Ut' :
		echo 'Cette page permet de visualiser et éventuellement supprimer les noms de famille, principaux ou secondaires, qui ne sont port&eacute;s par aucune personne.';
		break;
	case 'Rectif_Utf8' :
		echo "Lorsque l'on importe un fichier, par exemple lors d'un import Gedcom d'un fichier en UTF-8 sans avoir s&eacute;lectionn&eacute; l'option ad-hoc, il peut arriver que les caract&egrave;res accentu&eacute;s et les \"&ccedil;\" sont mal retranscrits.<br />";
		echo "L'id&eacute;e est alors de rectifier la base pour que ces caract&egrave;res soient corrects.";
		break;
	case 'Verif_Homonymes' :	
		echo "Cette page permet d'afficher les homonymes pr&eacute;sents dans la base. Ceux-ci sont tri&eacute;s par nom, pr&eacute;noms, date de naissance et de date de d&eacute;c&egrave;s.<br />";
		echo 'La liste des personnes peut &ecirc;tre restreinte en cochant les cases "Date de naissance" et "Date de d&eacute;c&egrave;s". Ainsi, si l’on coche la case "Date de naissance", le contr&ocirc;le d’homonymie prendra également en compte la date de naissance ; il s’agit alors d’identifier les doublons r&eacute;els et plus seulement les homonymes.';
		echo "Pour chaque couple nom – pr&eacute;noms, on peut afficher 2 personnes en parall&egrave;le en les s&eacute;lectionnant via les boutons radio et en cliquant sur l'ic&ocirc;ne ".Affiche_Icone('2personnes','Comparaison de 2 personnes').".";
		break;
	case 'Import_CSV_Evenements' :
		echo "Il est possible de cr&eacute;er des &eacute;v&egrave;nements &agrave; partir d'un fichier csv issu d'un tableur.<br />";
		echo "On pourra sp&eacute;cifier un lieu et un type pour les &eacute;v&egrave;nements qui vont &ecirc;tre cr&eacute;&eacute;s.<br /><br />";
		echo "Le fichier peut contenir une ent&ecirc;te (1&egrave;re ligne) qui donnera la liste des champs. Cette ent&ecirc;te contiendra alors : <br />";
		echo "titre;debut;fin<br /><br />";
		echo "Exemple pour la suite du fichier : <br />";
		echo "Prise de la Bastille;14.07.1789;14.07.1789<br />";
		echo "Fête de la Saint-Jean;24.06.1802;24.06.1802<br />";
		echo "essai évènement 3;01.01.1903;02.02.1903<br />";
		break;
	case 'Init_Sosa' :
		echo "Cet utilitaire permet de supprimer les num&eacute;ros sosa de toutes les personnes de la base.<br />";
		echo "Il est particuli&egrave;rement utile lorsque l'on change le decujus et peut &ecirc;tre appel&eacute; avant la v&eacute;rification de la num&eacute;rotation.<br />";
		break;
	case 'Init_Noms' :
		echo "Sur certains imports qui ont mal fonctionn&eacute;, les liens vers les noms de famille sont incomplets ; il faut alors les compl&eacute;ter.<br />";
		echo "C’est l’objet de cet utilitaire.<br />";
		echo "Le param&egrave;tre ini=o permet en plus de refaire la table des noms de famille et des liens.<br />";
		break;
	case 'Liste_Pers_Gen' :
		echo "Cette fonctionnalit&eacute; permet de lister toutes les personnes situ&eacute;es dans l'ascendance directe du de cujus. La liste est tri&eacute;e par g&eacute;n&eacute;ration. A chaque g&eacute;n&eacute;ration, une rupture est effectu&eacute;e afin d'afficher le num&eacute;ro de la g&eacute;n&eacute;ration. On peut ensuite se d&eacute;brancher sur la personne en cliquant sur le lien nom / pr&eacute;nom de la personne.<br />";
		echo "Il est &agrave; noter que le de cujus peut &ecirc;tre temporairement diff&eacute;rent de celui positionn&eacute;e par le gestionnaire de la base. On parle alors de &laquo;&nbsp;vue personnalis&eacute;e&nbsp;&raquo;.<br />";
 		echo "La visibilit&eacute; des personnes est restreinte par le profil de l'utilisateur connect&eacute;. La case &laquo;&nbsp;Simulation acc&egrave;s invit&eacute;&nbsp;&raquo; permet de voir les g&eacute;n&eacute;rations telles que les verraient des personnes non connect&eacute;es (typiquement un utilsateur lambda sur Internet).";
		break;
	case 'Export_Pour_Deces' :
		echo "L'INSEE met à disposition les d&eacute;c&egrave;s survenus depuis 1970. Le site $url_matchid permet de faire des recherches unitaires ou par lot dans ces d&eacute;c&egrave;s. ";
		echo "MatchId dispose d’une recherche intelligente qui peut renvoyer des personnes qui correspondent « &agrave; peu pr&egrave;s » aux crit&egrave;res demand&eacute;s.<br>";
		echo "<br>Cette fonctionnalit&eacute; permet soit :<br>";
		echo "&nbsp;-&nbsp;De lister à l’&eacute;cran les personnes concern&eacute;es en vue d’effectuer un appel unitaire &agrave; matchId.<br>";
		echo "&nbsp;-&nbsp;De constituer un fichier pour faire une recherche par lot sur ce site, dans la rubrique « Appariement ». En retour, matchId fournira un fichier dans lequel on retrouvera les dates et lieux de d&eacute;c&egrave;s des personnes fournies dans le fichier en entr&eacute;e.<br>";
		echo "<br>Les personnes list&eacute;es ou export&eacute;es sont celles dont la ville de naissance est connue, la date de naissance connue exactement avec une ann&eacute;e post&eacute;rieure ou &eacute;gale &agrave; l&rsquor;ann&eacute;e saisie par l’utilisateur.<br>";
		break;
	case 'Recherche_MatchId_Unitaire' :	
		echo "Cette fonctionnalit&eacute; permet d'interroger matchId pour r&eacute;cup&eacute;rer une liste de personnes correspondant « &agrave; peu pr&egrave;s » aux crit&egrave;res envoy&eacute;s par Geneamania.<br>";
		echo "MatchId va renvoyer une liste de personnes correspondant « &agrave; peu pr&egrave;s » aux crit&egrave;res envoy&eacute;s par Geneamania. Les informations de ces personnes sont affich&eacute;es sous celles connues dans Geneamania. "
			."<br>Un bouton pr&eacute;sent sur la ligne de d&eacute;c&egrave;s de chaque personne permet de copier la date de d&eacute;c&egrave;s dans le presse-paier. "
			."Cette date pourra ensuite &ecirc;tre copi&eacute;e dans la fen&ecirc;tre de saisie de date de d&eacute;c&egrave;s de la personne (Ctrl+V sous Windows, champ « Saisie rapide d'une date gr&eacute;gorienne »).<br>";
		echo "<br>Attention : il faut imp&eacute;rativement &ecirc;tre connect&eacute; &agrave; Internet pour obtenir un r&eacute;sultat.<br>";
		break;
	default :
		echo "D&eacute;sol&eacute;, mais il n'y a pas d'aide en ligne pour cette page...";
}
?>
</body>
</html>