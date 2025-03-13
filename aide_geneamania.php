<?php

//=====================================================================
// Aide générale de Généamania
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L'; // Type d'accès de la page : (L)ecture
$titre = 'Aide Généamania';
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

function Affiche_Image_Aide($nom_image, $texte_image, $explications)
{
    global $root, $Icones;
    echo '<tr>';
    echo '<td><img src="' . $root . '/assets/img/' . $Icones[$nom_image] . '" alt="' . my_html($texte_image) . '" title="' . my_html($texte_image) . '"></td>';
    echo '<td>' . $explications . '</td>';
    echo '</tr>';
}

function aide_paragraphe($texte)
{
    echo '<br />';
    echo '<table width="100%" align="left" cellspacing="1" cellpadding="3">';
    echo '<tr class="rupt_table"><td><b>' . $texte . '</b></td></tr>';
    echo '</table>';
    echo '<br /><br />';
}

$compl = '';

$titre = 'Aide en ligne de Généamania ' . $Version;

if ($Environnement == 'I')
    $titre .= ' sur Internet';
else
    $titre .= ' en local';

if (($Environnement == 'I') and ($SiteGratuit))
    $lien = 'http://www.geneamania.net/';
else
    $lien = $root . '/';

$compl = Affiche_Icone_Lien('href="' . $lien . 'Geneamania.pdf"', 'manuel', 'manuel Généamania') . '&nbsp;';
Insere_Haut($titre, $compl, 'Aide_Geneamania', $compl);

aide_paragraphe('Icônes cliquables');
echo '<table>';
Affiche_Image_Aide('home', 'Accueil', 'Retour à la page d\'accueil');
Affiche_Image_Aide('previous', 'Fleche bleue', 'Retour sur la page précédente');
Affiche_Image_Aide('information', 'Information', 'Appel de la page d\'information d\'une page');
Affiche_Image_Aide('arbre_asc', 'Arbre ascendant', 'Appel de l\'arbre ascendant d\'une personne');
Affiche_Image_Aide('arbre_desc', 'Arbre descendant', 'Appel de l\'arbre descendant d\'une personne');
Affiche_Image_Aide('page_haut', 'Fleche grise haute', 'Dans une liste, remontée sur le haut de la page');
Affiche_Image_Aide('gedcom', 'Export Gedcom', 'Export Gedcom d\'une personne');
Affiche_Image_Aide('va_URL', 'Lien', 'Lien vers une page Internet ou un fichier');
Affiche_Image_Aide('loupe', 'Detail', 'Détail');
Affiche_Image_Aide('images', 'Images', 'Appel des images');
if ($Comportement == 'C') Affiche_Image_Aide('note', 'Note', 'Note');
if ($Comportement == 'C') Affiche_Image_Aide('oeil', 'Oeil', 'Affiche ou masque des informations');
echo '</table>';

if ($Environnement != 'I') {
    echo '<br />Les icônes suivantes ne sont disponibles qu\'en local<br />';
    echo '<table>';
    Affiche_Image_Aide('fiche_edition', 'Modification', 'Modification d\'une fiche');
    Affiche_Image_Aide('ajout_rapide', 'Ajout rapide', 'Ajout rapide de personnes');
    Affiche_Image_Aide('calendrier', 'Calendrier', 'Appel de l\'assistant de saisie de date');
    Affiche_Image_Aide('fiche_fam', 'Fiche familiale', 'Appel de la fiche familiale');
    Affiche_Image_Aide('fiche_controle', 'Controle', 'Contrôler une fiche personne');
    Affiche_Image_Aide('arrange', 'Ordre des rangs', 'Réorganisation des rangs des enfants');
    Affiche_Image_Aide('ajouter', 'Ajouter', 'Ajouter dans un écran de liste (une personne, une ville...)');
    Affiche_Image_Aide('ajout', 'Ajouter', 'Ajouter dans une fiche (une union pour une personne, une filiation...)');
    Affiche_Image_Aide('localisation', 'Carte', 'Sélectionner une zone géographique');
    Affiche_Image_Aide('ajout_URL', 'Url', 'Ouvrir / fermer la balise Url dans les zones de texte (ceci permet de faire un lien vers une page Internet ou vers un fichier local)');
    Affiche_Image_Aide('calculette', 'Calculette', 'Calcule le numéro Sosa sur une fiche personne');
    Affiche_Image_Aide('efface', 'Effacement', 'Efface la zone de saisie sur les calculettes');
    Affiche_Image_Aide('homme', 'Liste par maris', 'Liste des mariages triée par maris');
    Affiche_Image_Aide('femme', 'Liste par femmes', 'Liste des mariages triée par femmes');
    Affiche_Image_Aide('carte_france', 'Carte de France', 'Répartition sur la carte de France');
    echo '</table>';
}

if ($Comportement == 'S') {
    aide_paragraphe('Icônes survolables');
    echo '<table>';
    Affiche_Image_Aide('note', 'Note', 'Note');
    Affiche_Image_Aide('oeil', 'Oeil', 'Affiche ou masque des informations');
    echo '</table>';
}

aide_paragraphe('Icônes non cliquables');
echo '<table>';
Affiche_Image_Aide('anniv_nai', 'Bougie', 'Anniversaire de naissance');
Affiche_Image_Aide('anniv_mar', 'Alliance', 'Anniversaire de mariage');
Affiche_Image_Aide('anniv_dec', 'Cierge', 'Anniversaire de décès');
Affiche_Image_Aide('image_defaut', 'Défaut', 'Image par défaut');
Affiche_Image_Aide('couple_donne', 'Puis', 'Donne l\'enfant');
Affiche_Image_Aide('tip', 'Suggestion', 'Suggestion');

echo '</table>';

if ($Environnement != 'I') {
    echo '<br />Les icônes suivantes ne sont disponibles qu\'en local<br />';
    echo '<table>';
    Affiche_Image_Aide('obligatoire', 'Obligatoire', 'Zone obligatoire d\'une fiche');
    Affiche_Image_Aide('internet_oui', 'Visibilite Internet', 'Fiche visible sur Internet');
    Affiche_Image_Aide('internet_non', 'Visibilite Internet', 'Fiche non visible sur Internet');
    Affiche_Image_Aide('fiche_validee', 'Validee', 'Fiche validée');
    Affiche_Image_Aide('fiche_non_validee', 'Non validee', 'Fiche non validée');
    Affiche_Image_Aide('fiche_internet', 'Source Internet', 'Fiche dont les informations sont issues d\'internet');
    Affiche_Image_Aide('drapeau_vert', 'Feu vert', 'Information ne générant pas d\'alerte');
    Affiche_Image_Aide('drapeau_orange', 'Feu orange', 'Information générant une alerte');
    Affiche_Image_Aide('warning', 'Attention', 'Erreur');
    Affiche_Image_Aide('stop', 'Arret', 'Arrêt du traitement suite à une erreur');
    Affiche_Image_Aide('commentaire', 'Commentaire', 'Commentaire');

    echo '</table>';
}

aide_paragraphe('Gestion des personnes');

$ic_modify = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '">';
$ic_add =  '<img src="' . $root . '/assets/img/' . $Icones['ajouter'] . '" alt="' . my_html($LG_add) . '" title="' . my_html($LG_add) . '">';

echo 'Les personnes sont l\'élément central de toute généalogie et par conséquent celle de Généamania.<br />';
echo 'Elles sont accessibles au travers de plusieurs listes (par nom; génération, patronymique...).';

if ($Environnement != 'I') {
    echo 'La création et la modification des personnes est effectuée à partir de la liste des personnes par nom.<br />';
    echo 'On peut :<ul>';
    echo '<li>créer une personne en cliquant sur le bouton ' . $ic_add . ' en haut de la liste ;</li>';
    echo '<li>modifier une personne en cliquant sur l\'icône ' . $ic_modify . ' dans la liste.</li>';
    echo '</ul>';
    echo 'Le rattachement d\'une personne à ses parents s\'effectue à partir la modification d\'une personne.';
    echo 'Il convient alors de cliquer sur l\'icône ' . $ic_add . ' dans le pavé filiations. La modification de filiation s\'effectue';
    echo 'à partir de l\'icône ' . $ic_modify . ' dans le même pavé.<br />';
    echo 'L\'union d\'une personne est possible avec plusieurs conjoints. Pour ajouter une union,';
    echo 'il convient de cliquer sur l\'icône ' . $ic_add . ' dans le pavé unions. La modification s\'effectue';
    echo 'à partir de l\'icône ' . $ic_modify . ' dans le même pavé.<br />';
    echo 'NB 1 : les villes de naissance, décès (...) peuvent être créées à partir des fiches personne, union ou filiation ; ';
    echo 'pour cela l\'utilisateur doit cliquer sur l\'icône ' . $ic_add . ' à côté de la liste déroulante des villes dans la fiche en question ; ';
    echo 'la création de la ville est alors effectuée "a minima" ; ';
    echo 'la création complète d\'une ville s\'effectue dans la gestion des zones géographiques.<br />';
    echo 'NB 2 : le de cujus occupe une place particulière dans la généalogie ;&nbsp;';
    echo 'il ne faut donc pas oublier de le créer. Vous y êtes d\'ailleurs parfois invités par l\'icône 
    <img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Suggestion" title="Suggestion">';
    echo ' ; pour cela créez une personne portant le numéro 1.<br />';
    echo 'NB 3 : la profession est gérée comme un évènement pour la personne. Il est conseillé de créer un évènement pour chaque profession ; ';
    echo 'la personne sera ensuite raccrochée à l\'évènement en spécifiant éventuellement une plage de dates de participation.<br />';
}

aide_paragraphe('Gestion des zones géographiques');
echo 'Les zones géographiques sont de plusieurs niveaux hiérarchisés de la fa&ccedil;on suivante ' . ':<br />';
echo 'Pays -&gt; Région -&gt; Département -&gt; Ville -&gt; Subdivision.<br />';
echo 'On peut visualiser des listes pour chaque niveau. On a alors pour une entité, la liste des entités de niveau inférieur.<br />';

if ($Environnement != 'I') {
    echo 'En mode local, on peut créer ou modifier des régions, départements ou villes. Pour les pays présentant une';
    echo 'autre organisation, il conviendra de se calquer sur ce découpage.<br />';
    echo 'La création et la modification des zones géographiques est effectuée à partir de la liste de même niveau';
    echo '(régions, départements, villes).';
    echo 'On peut :<ul>';
    echo '<li>créer une zone en cliquant sur le bouton ' . $ic_add . ' en haut de la liste ;</li>';
    echo '<li>modifier une zone en cliquant sur l\'icône ' . $ic_modify . ' dans la liste.</li>';
    echo '</ul>';
}

if ($Environnement != 'I') {
    aide_paragraphe('Paramétrage de l\'application');
    echo 'Le paramétrage peut être de type :<br />';
    echo '<ul>';
    echo '<li>';
    echo 'graphique ; ceci permet de modifier le thème graphique des pages du site.<br />';
    echo '4 paramètres sont disponibles :<br />';
    echo '<ul>';
    echo '<li>La lettre B sur la page d\'accueil du site.&nbsp;';
    echo 'Si l\'utilisateur coche le bouton "Pas de lettre graphique", une grande lettre B non graphique sera affichée.</li>';
    "\n";
    echo '<li>L\'image de fond d\'écran de toutes les pages du site.&nbsp;';
    echo 'Si l\'utilisateur coche le bouton "Pas de fond de page", le fond d\'écran sera blanc.</li>';
    "\n";
    echo '<li>La couleur des entêtes cellules de table.&nbsp;';
    echo 'Cette couleur est utilisée par exemple dans la liste par génération pour séparer une génération de la précédente.&nbsp;';
    echo 'Vous la retrouvez également sur cette page pour séparer les chapitres de l\'aide.&nbsp;';
    echo 'L\'utilisateur clique sur une couleur pour la sélectionner ; elle apparait alors à droite de l\'écran.&nbsp;';
    "\n";
    echo 'Si l\'utilisateur ne veut pas de couleur particulière il clique sur le bouton "Blanc".</li>';
    echo '<li>La couleur des dégradés.&nbsp;';
    echo 'Cette couleur est utilisée par exemple sur les cartes ;&nbsp;';
    echo 'le dégradé symbolise alors la présence plus ou moins importante de personnes en fonction de la teinte.</li>';
    echo '</ul></li>';
    echo '<li>';
    echo 'non graphique.<br />';
    echo 'plusieurs paramètres sont disponibles :<br />';
    echo '<ul>';
    echo '<li>Le nom qui va s\'afficher sur la page d\'accueil.</li>';
    echo '<li>L\'adresse mail de contact.</li>';
    echo '<li>Affichage de l\'année seule dans les dates sur Internet (sans effet en local, cette option permet d\'assurer la confidentialité des dates sur le Web).</li>';
    echo '<li>Comportement (clic ou survol dans le menu de la page d\'accueil ou sur certaines icônes).</li>';
    echo '</ul></li>';
    echo '<li>';
    echo 'modèle d\'arbre ascendant imprimable.<br />';
    echo 'plusieurs paramètres sont disponibles :<br />';
    echo '<ul>';
    echo '<li>Modèle de fond de page (dessin de l\'arbre).</li>';
    echo '<li>Affichage des années de mariage sur la sortie imprimée.</li>';
    echo '</ul></li>';
    echo '</ul>';
    echo 'Il est accessible depuis la page d\'accueil du site.';
}

Insere_Bas($compl);
?>
</body>

</html>