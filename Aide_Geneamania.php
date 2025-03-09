<?php

//=====================================================================
// Aide générale de Généamania
// UTF-8
//=====================================================================

session_start();
include('fonctions.php');
$acces = 'L';					// Type d'accès de la page : (L)ecture
$titre = 'Aide Généamania';
$x = Lit_Env();
include('Gestion_Pages.php');

function Affiche_Image_Aide($nom_image,$texte_image,$explications) {
	global $def_enc;
	echo '<tr>'."\n";
	//echo '<td>'.Affiche_Icone($nom_image,my_html($texte_image)).'</td>'."\n";
	echo '<td>'.Affiche_Icone($nom_image,$texte_image).'</td>'."\n";
	echo '<td>'.$explications.'</td>'."\n";
	echo '</tr>'."\n";
}

function aide_paragraphe($texte) {
	echo '<br />'."\n";
	echo '<table width="100%" border="0" align="left" cellspacing="1" cellpadding="3">';
	echo '<tr class="rupt_table"><td><b>'.$texte.'</b></td></tr>'."\n";
	echo '</table>';
	echo '<br /><br />'."\n";
}

$compl = '';

$titre = 'Aide en ligne de G&eacute;n&eacute;amania '.$Version;

if ($Environnement == 'I')
	$titre .= ' sur Internet';
else
	$titre .= ' en local';

if (($Environnement == 'I') and ($SiteGratuit))
	$lien = 'http://www.geneamania.net/';
else
	$lien = Get_Adr_Base_Ref();

$compl = Affiche_Icone_Lien('href="'.$lien.'Geneamania.pdf"','manuel','manuel Généamania') . '&nbsp;';
Insere_Haut($titre,$compl,'Aide_Geneamania',$compl);

aide_paragraphe('Ic&ocirc;nes cliquables');
echo '<table>'."\n";
Affiche_Image_Aide('home','Accueil','Retour &agrave; la page d\'accueil');
Affiche_Image_Aide('previous','Fleche bleue','Retour sur la page pr&eacute;c&eacute;dente');
Affiche_Image_Aide('information','Information','Appel de la page d\'information d\'une page');
Affiche_Image_Aide('arbre_asc','Arbre ascendant','Appel de l\'arbre ascendant d\'une personne');
Affiche_Image_Aide('arbre_desc','Arbre descendant','Appel de l\'arbre descendant d\'une personne');
Affiche_Image_Aide('page_haut','Fleche grise haute','Dans une liste, remont&eacute;e sur le haut de la page');
Affiche_Image_Aide('gedcom','Export Gedcom','Export Gedcom d\'une personne');
Affiche_Image_Aide('va_URL','Lien','Lien vers une page Internet ou un fichier');
Affiche_Image_Aide('loupe','Detail','D&eacute;tail');
Affiche_Image_Aide('images','Images','Appel des images');
if ($Comportement == 'C') Affiche_Image_Aide('note','Note','Note');
if ($Comportement == 'C') Affiche_Image_Aide('oeil','Oeil','Affiche ou masque des informations');
echo '</table>'."\n";

if ($Environnement != 'I') {
  echo '<br />Les ic&ocirc;nes suivantes ne sont disponibles qu\'en local<br />';
  echo '<table>'."\n";
  Affiche_Image_Aide('fiche_edition','Modification','Modification d\'une fiche');
  Affiche_Image_Aide('ajout_rapide','Ajout rapide','Ajout rapide de personnes');
  Affiche_Image_Aide('calendrier','Calendrier','Appel de l\'assistant de saisie de date');
  Affiche_Image_Aide('fiche_fam','Fiche familiale','Appel de la fiche familiale');
  Affiche_Image_Aide('fiche_controle','Controle','Contr&ocirc;ler une fiche personne');
  Affiche_Image_Aide('arrange','Ordre des rangs','R&eacute;organisation des rangs des enfants');
  Affiche_Image_Aide('ajouter','Ajouter','Ajouter dans un &eacute;cran de liste (une personne, une ville...)');
  Affiche_Image_Aide('ajout','Ajouter','Ajouter dans une fiche (une union pour une personne, une filiation...)');
  Affiche_Image_Aide('localisation','Carte','S&eacute;lectionner une zone g&eacute;ographique');
  Affiche_Image_Aide('ajout_URL','Url','Ouvrir / fermer la balise Url dans les zones de texte (ceci permet de faire un lien vers une page Internet ou vers un fichier local)');
  Affiche_Image_Aide('calculette','Calculette','Calcule le num&eacute;ro Sosa sur une fiche personne');
  Affiche_Image_Aide('efface','Effacement','Efface la zone de saisie sur les calculettes');
  Affiche_Image_Aide('homme','Liste par maris','Liste des mariages tri&eacute;e par maris');
  Affiche_Image_Aide('femme','Liste par femmes','Liste des mariages tri&eacute;e par femmes');
  Affiche_Image_Aide('carte_france','Carte de France','R&eacute;partition sur la carte de France');
  echo '</table>'."\n";
}

if ($Comportement == 'S') {
  aide_paragraphe('Ic&ocirc;nes survolables');
  echo '<table>'."\n";
  Affiche_Image_Aide('note','Note','Note');
  Affiche_Image_Aide('oeil','Oeil','Affiche ou masque des informations');
  echo '</table>'."\n";
}

aide_paragraphe('Ic&ocirc;nes non cliquables');
echo '<table>'."\n";
Affiche_Image_Aide('anniv_nai','Bougie','Anniversaire de naissance');
Affiche_Image_Aide('anniv_mar','Alliance','Anniversaire de mariage');
Affiche_Image_Aide('anniv_dec','Cierge','Anniversaire de d&eacute;c&egrave;s');
Affiche_Image_Aide('image_defaut','Défaut','Image par d&eacute;faut');
Affiche_Image_Aide('couple_donne','Puis','Donne l\'enfant');
Affiche_Image_Aide('tip','Suggestion','Suggestion');

echo '</table>'."\n";

if ($Environnement != 'I') {
	echo '<br />Les ic&ocirc;nes suivantes ne sont disponibles qu\'en local<br />';
	echo '<table>'."\n";
	Affiche_Image_Aide('obligatoire','Obligatoire','Zone obligatoire d\'une fiche');
	Affiche_Image_Aide('internet_oui','Visibilite Internet','Fiche visible sur Internet');
	Affiche_Image_Aide('internet_non','Visibilite Internet','Fiche non visible sur Internet');
	Affiche_Image_Aide('fiche_validee','Validee','Fiche valid&eacute;e');
	Affiche_Image_Aide('fiche_non_validee','Non validee','Fiche non valid&eacute;e');
	Affiche_Image_Aide('fiche_internet','Source Internet','Fiche dont les informations sont issues d\'internet');
	Affiche_Image_Aide('drapeau_vert','Feu vert','Information ne g&eacute;n&eacute;rant pas d\'alerte');
	Affiche_Image_Aide('drapeau_orange','Feu orange','Information g&eacute;n&eacute;rant une alerte');
	Affiche_Image_Aide('warning','Attention','Erreur');
	Affiche_Image_Aide('stop','Arret','Arr&ecirc;t du traitement suite &agrave; une erreur');
	Affiche_Image_Aide('commentaire','Commentaire','Commentaire');

	echo '</table>'."\n";
}

aide_paragraphe('Gestion des personnes');

$ic_modify = Affiche_Icone('fiche_edition',my_html($LG_modify));
$ic_add = Affiche_Icone('ajouter',my_html($LG_add));

echo 'Les personnes sont l\'&eacute;l&eacute;ment central de toute g&eacute;n&eacute;alogie et par cons&eacute;quent celle de G&eacute;n&eacute;amania.<br />'."\n";
echo 'Elles sont accessibles au travers de plusieurs listes (par nom; g&eacute;n&eacute;ration, patronymique...).'."\n";

if ($Environnement != 'I') {
	echo 'La cr&eacute;ation et la modification des personnes est effectu&eacute;e &agrave; partir de la liste des personnes par nom.<br />'."\n";
	echo 'On peut :<ul>'."\n";
	echo '<li>cr&eacute;er une personne en cliquant sur le bouton '.$ic_add.' en haut de la liste ;</li>'."\n";
	echo '<li>modifier une personne en cliquant sur l\'ic&ocirc;ne '.$ic_modify.' dans la liste.</li>'."\n";
	echo '</ul>'."\n";
	echo 'Le rattachement d\'une personne &agrave; ses parents s\'effectue &agrave; partir la modification d\'une personne.'."\n";
	echo 'Il convient alors de cliquer sur l\'ic&ocirc;ne '.$ic_add.' dans le pav&eacute; filiations. La modification de filiation s\'effectue'."\n";
	echo '&agrave; partir de l\'ic&ocirc;ne '.$ic_modify.' dans le m&ecirc;me pav&eacute;.<br />'."\n";
	echo 'L\'union d\'une personne est possible avec plusieurs conjoints. Pour ajouter une union,'."\n";
	echo 'il convient de cliquer sur l\'ic&ocirc;ne '.$ic_add.' dans le pav&eacute; unions. La modification s\'effectue'."\n";
	echo '&agrave; partir de l\'ic&ocirc;ne '.$ic_modify.' dans le m&ecirc;me pav&eacute;.<br />'."\n";
	echo 'NB 1 : les villes de naissance, d&eacute;c&egrave;s (...) peuvent &ecirc;tre cr&eacute;&eacute;es &agrave; partir des fiches personne, union ou filiation ; '."\n";
	echo 'pour cela l\'utilisateur doit cliquer sur l\'ic&ocirc;ne '.$ic_add.' &agrave; c&ocirc;t&eacute; de la liste d&eacute;roulante des villes dans la fiche en question ; '."\n";
	echo 'la cr&eacute;ation de la ville est alors effectu&eacute;e "a minima" ; '."\n";
	echo 'la cr&eacute;ation compl&egrave;te d\'une ville s\'effectue dans la gestion des zones g&eacute;ographiques.<br />'."\n";
	echo 'NB 2 : le de cujus occupe une place particuli&egrave;re dans la g&eacute;n&eacute;alogie ;&nbsp;'."\n";
	echo 'il ne faut donc pas oublier de le cr&eacute;er. Vous y &ecirc;tes d\'ailleurs parfois invit&eacute;s par l\'ic&ocirc;ne '.Affiche_Icone('tip','Suggestion');
	echo ' ; pour cela cr&eacute;ez une personne portant le num&eacute;ro 1.<br />'."\n";
	echo 'NB 3 : la profession est g&eacute;r&eacute;e comme un &eacute;v&egrave;nement pour la personne. Il est conseill&eacute; de cr&eacute;er un &eacute;v&egrave;nement pour chaque profession ; ';
	echo 'la personne sera ensuite raccroch&eacute;e &agrave; l\'&eacute;v&egrave;nement en sp&eacute;cifiant &eacute;ventuellement une plage de dates de participation.<br />'."\n";
}

aide_paragraphe('Gestion des zones g&eacute;ographiques');
echo 'Les zones g&eacute;ographiques sont de plusieurs niveaux hi&eacute;rarchis&eacute;s de la fa&ccedil;on suivante '.':<br />'."\n";
echo 'Pays -&gt; R&eacute;gion -&gt; D&eacute;partement -&gt; Ville -&gt; Subdivision.<br />'."\n";
echo 'On peut visualiser des listes pour chaque niveau. On a alors pour une entit&eacute;, la liste des entit&eacute;s de niveau inf&eacute;rieur.<br />'."\n";

if ($Environnement != 'I') {
	echo 'En mode local, on peut cr&eacute;er ou modifier des r&eacute;gions, d&eacute;partements ou villes. Pour les pays pr&eacute;sentant une'."\n";
	echo 'autre organisation, il conviendra de se calquer sur ce d&eacute;coupage.<br />'."\n";
	echo 'La cr&eacute;ation et la modification des zones g&eacute;ographiques est effectu&eacute;e &agrave; partir de la liste de m&ecirc;me niveau'."\n";
	echo '(r&eacute;gions, d&eacute;partements, villes).'."\n";
	echo 'On peut :<ul>'."\n";
	echo '<li>cr&eacute;er une zone en cliquant sur le bouton '.$ic_add.' en haut de la liste ;</li>'."\n";
	echo '<li>modifier une zone en cliquant sur l\'ic&ocirc;ne '.$ic_modify.' dans la liste.</li>'."\n";
	echo '</ul>'."\n";
}

if ($Environnement != 'I') {
  aide_paragraphe('Param&eacute;trage de l\'application');
  echo 'Le param&eacute;trage peut &ecirc;tre de type :<br />'."\n";
  echo '<ul>';
  echo '<li>';
  echo 'graphique ; ceci permet de modifier le th&egrave;me graphique des pages du site.<br />'."\n";
  echo '4 param&egrave;tres sont disponibles :<br />';
  echo '<ul>';
  echo '<li>La lettre B sur la page d\'accueil du site.&nbsp;';
  echo 'Si l\'utilisateur coche le bouton "Pas de lettre graphique", une grande lettre B non graphique sera affich&eacute;e.</li>';"\n";
  echo '<li>L\'image de fond d\'&eacute;cran de toutes les pages du site.&nbsp;';
  echo 'Si l\'utilisateur coche le bouton "Pas de fond de page", le fond d\'&eacute;cran sera blanc.</li>';"\n";
  echo '<li>La couleur des ent&ecirc;tes cellules de table.&nbsp;';
  echo 'Cette couleur est utilis&eacute;e par exemple dans la liste par g&eacute;n&eacute;ration pour s&eacute;parer une g&eacute;n&eacute;ration de la pr&eacute;c&eacute;dente.&nbsp;'."\n";
  echo 'Vous la retrouvez &eacute;galement sur cette page pour s&eacute;parer les chapitres de l\'aide.&nbsp;'."\n";
  echo 'L\'utilisateur clique sur une couleur pour la s&eacute;lectionner ; elle apparait alors &agrave; droite de l\'&eacute;cran.&nbsp;';"\n";
  echo 'Si l\'utilisateur ne veut pas de couleur particuli&egrave;re il clique sur le bouton "Blanc".</li>'."\n";
  echo '<li>La couleur des d&eacute;grad&eacute;s.&nbsp;';
  echo 'Cette couleur est utilis&eacute;e par exemple sur les cartes ;&nbsp;'."\n";
  echo 'le d&eacute;grad&eacute; symbolise alors la pr&eacute;sence plus ou moins importante de personnes en fonction de la teinte.</li>'."\n";
  echo '</ul></li>';
  echo '<li>';
  echo 'non graphique.<br />'."\n";
  echo 'plusieurs param&egrave;tres sont disponibles :<br />';
  echo '<ul>';
  echo '<li>Le nom qui va s\'afficher sur la page d\'accueil.</li>';
  echo '<li>L\'adresse mail de contact.</li>';
  echo '<li>Affichage de l\'ann&eacute;e seule dans les dates sur Internet (sans effet en local, cette option permet d\'assurer la confidentialit&eacute; des dates sur le Web).</li>';
  echo '<li>Comportement (clic ou survol dans le menu de la page d\'accueil ou sur certaines ic&ocirc;nes).</li>';
  echo '</ul></li>';
  echo '<li>';
  echo 'mod&egrave;le d\'arbre ascendant imprimable.<br />'."\n";
  echo 'plusieurs param&egrave;tres sont disponibles :<br />';
  echo '<ul>';
  echo '<li>Mod&egrave;le de fond de page (dessin de l\'arbre).</li>';
  echo '<li>Affichage des ann&eacute;es de mariage sur la sortie imprim&eacute;e.</li>';
  echo '</ul></li>';
  echo '</ul>';
  echo 'Il est accessible depuis la page d\'accueil du site.'."\n";
}

Insere_Bas($compl);
?>
</body>
</html>