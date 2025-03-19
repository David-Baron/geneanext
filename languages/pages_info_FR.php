<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    // include('fonctions.php');
    // Recup de la variable passée dans l'URL : aide demandée
    $aide = Recup_Variable('aide', 'S');

    switch ($aide) {
        case 'Admin_Tables':
            $objet = 'administration des tables';
            break;
        case 'Ajout_Rapide':
            $objet = 'ajout rapide';
            break;
        case 'Anniversaires':
            $objet = 'anniversaires';
            break;
        case 'Calc_So':
            $objet = 'calculette sosa';
            break;
        case 'Completude_Nom':
            $objet = 'Informations complétude des informations';
            break;
        case 'Conv_Romain':
            $objet = 'Informations convertisseur romain - arabe';
            break;
        case 'Desc_Directe_Pers':
            $objet = 'Informations descendance directe d\'une personne';
            break;
        case 'Edition_Contribution':
            $objet = 'édition d\'une contribution';
            break;
        case 'Edition_Evenement':
            $objet = 'édition d\'un évènement';
            break;
        case 'Edition_Image':
            $objet = 'édition d\'une image';
            break;
        case 'Edition_Lier_Eve':
            $objet = 'lien évènement à une personne';
            break;
        case 'Edition_Lier_Nom':
            $objet = 'assigner un nom secondaire à une personne';
            break;
        case 'Edition_Lier_Pers':
            $objet = 'créer des relations entre deux personnes';
            break;
        case 'Edition_NomFam':
            $objet = 'édition d\'un nom de famille';
            break;
        case 'Edition_Personne':
            $objet = 'édition d\'une personne';
            break;
        case 'Edition_Rangs':
            $objet = 'édition des rangs';
            break;
        case 'Edition_Utilisateur':
            $objet = 'édition d\'un utilisateur';
            break;
        case 'Export':
            $objet = 'export';
            break;
        case 'Fusion_Evenements':
            $objet = 'fusion d\'évènements';
            break;
        case 'Histo_Ages_Deces':
            $objet = 'historique des âges de décès';
            break;
        case 'Histo_Ages_Mariage':
            $objet = 'historique des âges de premier mariage';
            break;
        case 'Import_CSV':
            $objet = 'Import CSV';
            break;
        case 'Import_Docs':
            $objet = 'import documents';
            break;
        case 'Import_Gedcom':
            $objet = 'import Gedcom';
            break;
        case 'Import_Sauvegarde':
            $objet = 'import d\'une sauvegarde';
            break;
        case 'Liste_Evenements':
            $objet = 'liste des évènements';
            break;
        case 'Liste_NomFam':
            $objet = 'liste des noms de famille';
            break;
        case 'Liste_Nom_Vivants':
            $objet = 'liste des personnes vivantes';
            break;
        case 'Naissances_Deces_Mois':
            $objet = 'naissances et décès par mois';
            break;
        case 'Naissances_Mariages_Deces_Mois':
            $objet = 'naissances et décès par mois';
            break;
        case 'Pers_Isolees':
            $objet = 'Personnes isolées';
            break;
        case 'Pyramide_Ages_Histo':
            $objet = 'historique de l\'âge au décès';
            break;
        case 'Pyramide_Ages':
            $objet = 'pyramide des âges au décès';
            break;
        case 'Pyramide_Ages_Mar_Histo':
            $objet = 'historique de l\'âge au décès';
            break;
        case 'Recherche_Commentaire':
            $objet = 'recherche dans les commenatires';
            break;
        case 'Recherche_Cousinage':
            $objet = 'recherche de parenté';
            break;
        case 'Recherche_Personne_Archive':
            $objet = 'recherche de personnes aux archives';
            break;
        case 'Recherche_Personne':
            $objet = 'recherche de personnes';
            break;
        case 'Recherche_Personne_CP':
            $objet = 'recherche de personnes par les conjoints ou les parents';
            break;
        case 'Recherche_Ville':
            $objet = 'recherche de villes';
            break;
        case 'Stat_Base_Depart':
            $objet = 'statistiques par département';
            break;
        case 'Stat_Base_Villes':
            $objet = 'statistiques par ville';
            break;
        case 'Verif_Internet_Absente':
            $objet = 'vérification de la diffusabilité Internet absente';
            break;
        case 'Verif_Internet':
            $objet = 'vérification de la diffusabilité Internet';
            break;
        case 'Verif_Personne':
            $objet = 'Vérification d\'une fiche personne';
            break;
        case 'Verif_Sosa':
            $objet = 'vérification des numéros Sosa';
            break;
        case 'Vue_Personnalisee':
            $objet = 'vue personnalisée';
            break;
        case 'Export_Pour_Deces':
            $objet = 'export pour recherche des dates de décès sur matchid.io';
            break;
        default:
            $objet = '';
    }
    if ($objet != '') $objet = 'Informations ' . $objet;
    Ecrit_Meta($objet, $objet, '');
    echo "</head>\n";
    $x = Lit_Env();
    echo '<body>';

    $auto_contrib = 'Cette page est accessible à partir du profil contributeur.';

    echo '<br />';

    switch ($aide) {
        case 'Admin_Tables':
            echo "Cette page permet de réparer ou optimiser les tables de la base Généamania.<br />";
            echo "La réparation d'une table est nécessaire lorsque le logiciel indique 'Table 'nom de la table' is marked as crashed and should be repaired '. Ceci peut arriver lorsqu'il se produit un problème";
            echo "technique sur l'ordinateur. La réparation de la table est une solution au même titre que l'import d'une sauvegarde.<br />";
            echo "L'optimisation d'une table peut être nécessaire lorsqu'il y a de fréquentes suppressions sur la table ; la table est alors réorganisée.";
            echo "Normalement, cette opération est inutile dans l'utilisation standard de Généamania.<br /><br />";
            echo "Cette page n'est disponible que pour le profil gestionnaire.";
            break;
        case 'Ajout_Rapide':
            echo "Cette page permet de créer des personnes et les liens associés de manière automatique.<br />";
            echo "A partir d'un personne, on peut :";
            echo "<ul>";
            echo "<li>Créer une soeur ou un frère. Dans ce cas, la personne créée bénéficiera automatiquement de la même filiation que la personne d'origine.";
            echo "Cette fonction  n'est accessible que si la filiation de la personne d'origine est connue.</li>";
            echo "<li>Créer un conjoint. Dans ce cas, l'union avec la personne d'origine sera automatiquement créée.</li>";
            echo "<li>Créer les parents. Les parents et leur union sont créés dans la même page ; la filiation avec la personne d'origine est automatiquement créée.";
            echo "Cette fonction  n'est accessible que si la filiation de la personne d'origine n'est pas connue.</li>";
            echo "</ul>";
            echo "Les listes de villes sont alimentées à partir des villes de naissance, baptême et décès de la personne d'origine.<br />";
            echo "Les dates peuvent être choisies en cliquant sur l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['calendrier'] . '" alt="calendrier" title="calendrier">' . "<br />";
            echo $auto_contrib;
            break;
        case 'Anniversaires':
            echo "Cette page permet de visualiser les anniversaires de naissance, mariage et décès sur le mois en cours ou un mois choisi par l'utilisateur.";
            echo "Les anniversaires sont triés par ordre chronologique.<br />";
            echo "Les icônes " . '<img src="' . $root . '/assets/img/' . $Icones['anniv_nai'] . '" alt="Anniversaire de naissance" title="Anniversaire de naissance">' . '&nbsp;'
                . '<img src="' . $root . '/assets/img/' . $Icones['anniv_mar'] . '" alt="Anniversaire de mariage" title="Anniversaire de mariage">' . '&nbsp;'
                . '<img src="' . $root . '/assets/img/' . $Icones['anniv_dec'] . '" alt="Anniversaire de décès" title="Anniversaire de décès">';
            echo " signifient que l'anniversaire de naissance, mariage ou décès a lieu le jour même du mois en cours.<br />";
            echo "L'utilisateur a la possibilité de ne pas afficher les personnes décédées ou présumées décédées (sur les anniversaires de naissance ou de mariage).<br />";
            echo "NB : l'affichage des personnes dont la visibilité internet est restreinte est fonction du profil de l'utilisateur.";
            break;
        case 'Calc_So':
            echo 'Cette page permet de calculer le numéro <a href="' . $offset_info . 'glossaire_gen.php#SOSA">Sosa</a>';
            echo "&nbsp;du conjoint, du père, de la mère ou de l'enfant d'une personne.<br />";
            echo "De même, on peut calculer à quelle génération correspond un numéro et si celui-ci est du côté paternel ou maternel.<br />";
            echo "L'utilisateur tape un numéro via le clavier ou en cliquant sur les boutons ad hoc ;";
            echo "il doit ensuite cliquer sur le bouton voulu.<br />";
            echo "L'icône " . '<img src="' . $root . '/assets/img/' . $Icones['efface'] . '" alt="efface" title="efface">' . " permet d'effacer la zone de saisie.<br />";
            break;
        case 'Completude_Nom':
            echo "Cette page permet de vérifier la complétude des informations sur les personnes portant un nom.<br /><br />";
            echo "Sont vérifiées :";
            echo "<ul>";
            echo "<li>la présence de la date et du lieu de naissance ;</li>";
            echo "<li>la présence de la date et du lieu de décès si la personne est décédée (une personne née il y a plus de 130 ans est réputée décédée) ;</li>";
            echo "<li>la présence des 2 parents ;</li>";
            echo "<li>la présence d'un conjoint avec une date et un lieu d'union (si la personne est décédée après l'&acirc;ge de 15 ans).</li>";
            echo "</ul>";
            echo "Une information présente et précise est matérialisée par un drapeau vert ; une information absente par un drapeau rouge. Une date approximative est matérialisée par un drapeau orange.<br /><br />";
            echo "Cette page ne permet pas de valider la pertinence des informations présentes ; ceci est réalisé via la fonction de vérification des personnes.<br />";
            break;
        case 'Conv_Romain':
            echo "Cette page permet de convertir des nombres romains en nombres arabes et inversement.<br />";
            echo "L'utilisateur tape un nombre romain ou arabe via le clavier ou en cliquant sur les boutons ad hoc ;";
            echo 'il doit ensuite cliquer sur le bouton conversion ou se positionner dans la zone de saisie et appuyer sur la touche "Entrée"<br />';
            echo "L'icône " . '<img src="' . $root . '/assets/img/' . $Icones['efface'] . '" alt="efface" title="efface">' . " permet d'effacer la zone de saisie.<br />";
            echo "Les nombres arabes sont limités à 3999.<br />";
            echo "Les saisies de lettres romaines peuvent être faites en minuscules ou majuscules.";
            break;
        case 'Desc_Directe_Pers':
            echo "Cette page permet de lister la descendance directe d'une personne vers le de cujus.";
            echo "Pour cela, il faut que la personne soit dans l'ascendance directe du de cujus. Généamania considère que c'est le cas si le numéro sosa";
            echo "de la personne est renseigné et s'il s'agit d'un nombre.";
            echo "La descendance est recherchée, non pas par les filiations, mais par les numéros sosa successifs.<br />";
            echo "La sortie peut se faire au format texte ou au format HTML avec des liens cliquables (personnes,"
                . '<img src="' . $root . '/assets/img/' . $Icones['arbre_asc'] . '" alt="arbre ascendant" title="arbre ascendant">' . " arbre ascendant,"
                . '<img src="' . $root . '/assets/img/' . $Icones['arbre_desc'] . '" alt="arbre descendant" title="arbre descendant">' . "arbre descendant).<br />";
            echo "Les conjoints sont affichables selon le choix de l'utilisateur.<br />";
            echo "NB : l'affichage des personnes dont la visibilité internet est restreinte est fonction du profil de l'utilisateur.";
            break;
        case 'Edition_Contribution':
            echo "Cette page permet de prendre en compte une contribution proposée par un utilisateur du net.<br />";
            echo "L'ensemble des traitements est déclenché si l'utilisateur clique sur le bouton " . $lib_Okay . "<br />";
            echo "En règle générale, l'utilisateur peut choisir de modifier une personne existante, d'en créer une (selon les cas) ou d'ignorer la proposition pour la personne.<br />";
            echo "<ul>";
            echo "<li>Pour le père :<br />";
            echo "Si le père existe, l'utilisateur peut remplacer le père connu ou ignorer la proposition.";
            echo "Si le père n'existe pas, l'utilisateur peut créer le père (la filiation est automatiquement créée) ou ignorer la proposition.";
            echo "La page présente en gras les zones du père qui sont modifiées et en italique, les zones absentes de la proposition et qui sont reprises du père existant.</li>";
            echo "<li>Pour la mère :<br />";
            echo "Le comportement est le même. A l'issue du traitement des parents, l'union des parents est éventuellement créée (s'il y a eu création du père et / ou de la mère) ou modifiée.";
            echo "</li>";
            echo "<li>Pour le conjoint :<br />";
            echo "La page présente la liste des conjoints connus pour la personne. L'utilisateur peut alors choisir de remplacer un conjoint existant, d'en créer un nouveau ou d'ignorer la proposition ;";
            echo "dans ce cas, il y a création automatique de l'union entre le conjoint créé et la personne.";
            echo "</li>";
            echo "<li>Pour les enfants :<br />";
            echo "La page présente la liste des enfants connus pour la personne. L'utilisateur peut alors choisir de remplacer un (ou deux) enfant existant(s), d'en créer un (ou deux) nouveau(x) ou d'ignorer la proposition ;";
            echo "dans ce cas, il y a création automatique de la filiation entre l'enfant créé et la personne.";
            echo "Attention, la filiation créée ne référence pas le conjoint dans la mesure o&ugrave; le système ne saurait pas forcément à quel conjoint rattacher la filiation.";
            echo "</li>";
            echo "</ul>";
            echo "";
            echo "A l'issue du traitement, la contribution est réputée traitée si l'utilisateur clique sur " . $lib_Okay . "<br /><br />";
            echo "";
            echo "Cette page n'est disponible que pour le profil gestionnaire.";
            break;
        case 'Edition_Evenement':
            echo "Cette page permet de créer, modifier et supprimer un évènement.<br />";
            echo "Les zones obligatoires sont le titre de l'évènement et son type.<br />";
            echo "Le lieu de survenance de l'évènement peut être choisi en cliquant sur l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['localisation'] . '" alt="localisation" title="localisation">' . "<br />";
            echo "Les dates de début et de fin peuvent être choisis en cliquant sur l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['calendrier'] . '" alt="calendrier" title="calendrier">' . "&nbsp;";
            echo "alors que l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['copie_calend'] . '" alt="copie" title="copie">' . " permet de copier la date de début dans la date de fin.<br />";
            echo 'La zone "Visibilité Internet du commentaire" permet de masquer ou non l\'affichage de la note sur internet ; elle n\'a aucun effet en local.<br />';
            echo $auto_contrib;
            break;
        case 'Edition_Image':
            echo "Cette page permet de rattacher, modifier ou supprimer le rattachement d'une image à une personne, une ville, une union ou un évènement.<br />";
            echo "En création, si la description ou le nom du fichier de l'image sont absents, aucun lien ne sera créé.";
            echo "En modification, la re-saisie du nom de l'image n'est pas nécessaire.<br />";
            echo "L'image est limitée à " . ($taille_maxi_images['s'] / 1024);
            echo "Ko (paramétrable) pour des dimensions maximum de " . $taille_maxi_images['w'] . ' x ' . $taille_maxi_images['h'] . " pixels<br />";
            echo 'Le bouton radio "Image par défaut" permet de spécifier si cette image s\'affichera pas défaut pour l\'objet concerné (e.g. pour une personne sur la fiche familiale, l\'arbre).';
            echo 'La valeur par défaut est "Non".<br />';
            echo 'La case à cocher "Visibilité de l\'image sur internet " permet de spécifier si cette image s\'affichera ou non sur Internet pour un profil invit&eacute ; ';
            echo "si elle n'est pas cochée, l'utilisateur devra avoir un profil au moins privilégié pour la voir sur Internet.<br />";
            echo $auto_contrib;
            break;
        case 'Edition_Lier_Eve':
            echo "Cette page permet de lier un évènement à une personne.<br />";
            echo "Vous pouvez définir plusieurs participations d'une personne à un évènement avec des rôles différents. Par contre, une personne ne peut pas participer plusieurs fois à un même évènement avec le même rôle. <br />";
            echo $auto_contrib;
            break;
        case 'Edition_Lier_Nom':
            echo "Cette page permet d'assigner un nom secondaire à une personne.<br />";
            echo "Le nom secondaire est opposé au nom principal de la personne en ce sens qu'il en représente des variantes trouvées sur certains actes.<br />";
            echo "Vous pouvez commenter chaque lien vers un nom secondaire, par exemple en indiquant l'acte sur lequel a été trouvé le nom.<br />";
            echo "Il est à noter que si le lien existe, seule la modification du commentaire sera autorisée.<br />";
            break;
        case 'Edition_Lier_Pers':
            echo "";
            echo "Cette page permet de créer des relations entre deux personnes.<br />";
            echo "Vous pouvez définir plusieurs liens d'une personne avec une autre avec des rôles différents, mais pas avec le même rôle. <br />";
            echo "Les zones obligatoires sont la personne liée et le rôle.<br />";
            echo "Les dates de début et de fin peuvent être choisies en cliquant sur l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['calendrier'] . '" alt="calendrier" title="calendrier">' . "&nbsp;";
            echo "alors que l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['copie_calend'] . '" alt="copie" title="copie">' . " permet de copier la date de début dans la date de fin.<br />";
            echo $auto_contrib;
            break;
        case 'Edition_NomFam':
            echo "Cette page permet de modifier un nom de famille ainsi que sa prononciation.<br /><br />";
            echo "<b>Saisie du nom de famille</b><br />";
            echo "Vous pouvez modifier le nom de famille. Pour placer des caractères accentués, vous pouvez les saisir";
            echo "en minuscules puis cliquer sur l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['majuscule'] . '" alt="majuscule" title="majuscule">' . " pour mettre le nom en majuscules.<br />";
            echo "<br /><b>Prononciation</b><br />";
            echo "Pour la prononciation du nom, le bouton &laquo; Prononciation calculée &raquo; détermine une prononciation du nom à partir des règles de prononciation du fran&ccedil;ais.";
            echo "Ces règles sont complexes et parfois difficiles à appliquer, ainsi la prononciation proposée peut ne pas être correcte.";
            echo "Vous pouvez la corriger.<br />";
            echo "Vous pouvez déplacer le curseur en cliquant sur les flèches &laquo; <-- &raquo; et &laquo; --> &raquo;.<br />";
            echo "Pour supprimer un son, placez le curseur après celui-ci et cliquez sur &laquo; Effacer &raquo;.<br />";
            echo "Les boutons marqués d'une ou deux lettres permettent d'ajouter le son correspondant à l'endroit du curseur.<br />";
            echo "Quand votre souris arrive sur un de ces boutons, quelques exemples de mots contenant le son s'affichent en dessous du tableau.";
            break;
        case 'Edition_Personne':
            echo "Les zones obligatoires sont le nom et les prénoms.<br />";
            echo "Cette page permet de créer ou modifier une personne.<br />";
            echo "La date de naissance ou de décès peut être choisie en cliquant sur l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['calendrier'] . '" alt="calendrier" title="calendrier">' . "<br />";
            echo "Les professions sont gérées dans les évènements.<br />";
            echo "L'icône " . '<img src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="ajout ville" title="ajout ville">' . " permet d'ajouter dynamiquement une ville aux listes des villes de naissance ou de décès.<br />";
            echo "L'icône " . '<img src="' . $root . '/assets/img/' . $Icones['calculette'] . '" alt="calculette" title="calculette">' . " permet de calculer le numéro sosa à partir de la saisie effectuée par l'utilisateur dans le numéro. ";
            echo "Les calculs disponibles sont &quot;père&quot; (P), &quot;mère&quot; (M), &quot;enfant&quot; (E) ou &quot;conjoint&quot; (C). Par exemple, si l'utilisateur veut calculer la mère de la personne de numéro ";
            echo "sosa 10, il saisit =M10 dans le numéro ; un clic sur l'icône transforme le numéro saisi en 21 (mère de 10 dans la numérotation sosa). Il est à noter que le ";
            echo "calcul est insensible à la casse ; ainsi =m10 a le même effet que =M10.";
            echo "<br />";
            echo "L'icône " . '<img src="' . $root . '/assets/img/' . $Icones['decujus'] . '" alt="de cujus" title="de cujus">' . " permet d'attribuer automatiquement le numéro 1 (de cujus) à la personne.<br />";
            echo "L'icône " . '<img src="' . $root . '/assets/img/' . $Icones['copier'] . '" alt="copie" title="copie">' . " permet de coller le nom, la ville de naissance ou de décès de la fiche précédente sur laquelle était l'utilisateur en création ou modification.<br />";
            echo "<br />";
            echo "Le bouton " . $lib_Supprimer . " n'est affiché que si la personne n'est pas dans une union, qu'elle n'a pas de filiation ";
            echo "et qu'elle n'est pas dans une filiation en tant que parent.<br /><br />";
            echo $auto_contrib;
            break;
        case 'Edition_Rangs':
            echo "Cette page permet rectifier les rangs des enfants d'un couple.<br />";
            echo "Pour chaque enfant, Généamania calcule un rang théorique <b>si la date de naissance est connue de manière précise</b>.<br />";
            echo "En cas de divergence entre le rang théorique et le rang saisi, la zone du rang calculé est suivie de l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['warning'] . '" alt="Alerte" title="Alerte">';
            echo "L'utilisateur peut rectifier en masse les rangs en cliquant sur le bouton &quot;Accepter les rangs calculés&quot;.";
            echo "La mise à jour n'est effective qu'après avoir cliqué sur le bouton &quot;" . $lib_Okay . "&quot;.<br />";
            echo "De même, si les dates de naissance sont connues, Généamania calcule un écart théorique en mois / années entre les naissances.";
            echo 'Si l\'écart avec l\'enfant précédent est de moins de 9 mois, la zone "Ecart calculé" est suivie de l\'icône ' .'<img src="' . $root . '/assets/img/' . $Icones['warning'] . '" alt="Alerte" title="Alerte">' . "<br />";
            break;
        case 'Edition_Utilisateur':
            echo "";
            echo "Cette page permet de définir un utilisateur qui sera utilisé dans la version Internet de Généamania. Le <strong>nom</strong> est un rappel du nom réel de la personne. Le <strong>code utilisateur</strong> et le <strong>mot de passe</strong> serviront pour s'identifier sur la page d'accueil de Généamania. Le <strong>niveau</strong> sert à définir les possibilités que vous accordez à cet utilisateur. On distingue 4 niveaux :";
            echo "<ul>";
            echo "<li>invité : il peut consulter toutes les pages en respectant les verrouillages légaux d'accès aux informations personnelles ;</li>";
            echo "<li>privilégié : cet utilisateur peut consulter toutes les pages sans qu'il y ait de verrouillage d'accès aux informations personnelles. Il peut signaler au gestionnaire  des modifications par le système des contributions, accessible sur la fiche d'une personne; </li>";
            echo "<li>contributeur : cette personne peut faire des modifications dans la base.</li>";
            echo "<li>gestionnaire : c'est la personne qui peut tout faire sur le logiciel.</li>";
            echo "</ul>";
            echo "Un internaute qui accède à une généalogie a des <strong>droits d'invité</strong>. Cela correspond à toute personne qui veut consulter votre travail. Il n'est pas nécessaire de créer un utilisateur invité, cela est fait automatiquement. <br />";
            echo "Vous déclarerez en <strong>utilisateur privilégié</strong> une personne en qui vous avez confiance et qui pourra vous signaler des modifications par le système des contributions. Ces personnes ne peuvent rien modifier. Vous pouvez créer autant d'utilisateurs privilégiés que vous voulez.<br />";
            echo "Pour travailler dans des conditions de sécurité correctes, il faut être vigilent lorsque vous définissez un mot de passe. Les recommandations habituelles en la matière sont :";
            echo "<ul>";
            echo "<li>qu'il contienne au moins 8 caractères ;</li>";
            echo "<li>qu'il ne soit pas un mot d'une langue quelconque.</li>";
            echo "</ul>";
            echo "Mélangez les lettres majuscules, minuscules, les chiffres et utilisez les caractères qui sont plus rarement utilisés :";
            echo "<ul>";
            echo "<li>les diacritiques (é, è, à, &ccedil;, &acirc;, ê, &icirc;, ô, &ucirc;) ;</li>";
            echo "<li>les symboles (&amp;, #, $, &euro;, &sect;, @, \, /) ; </li>";
            echo "<li>les signes de ponctuation (, ; . : ! ? { } [ ] ( )) ; </li>";
            echo "<li>les symboles mathématiques (+, -, *, /, %).</li>";
            echo "</ul>";
            echo "Pour mémoriser plus facilement un mot de passe efficace, vous pouvez prendre une phrase que vous mémoriserez facilement et vous conservez la première lettre de chaque mot. Vous pouvez remplacer les s ou S par $, les o ou O par 0 (zéro), les a par @. Par exemple, la phrase &laquo;J'ai acheté 5 oeufs pour 3 euros&raquo; peut donner &laquo;j@50p3&euro;&raquo;.<br />";
            break;
        case 'Export':
            echo "Cette page permet d'exporter les données de la base.";
            echo "L'export peut être de type sauvegarde ou Internet.";
            echo "Ce dernier mode permet d'exporter ses données dans un fichier afin de les recharger sur un site Internet.<br />";
            echo "En export Internet, les données de la table 'compteurs' ne sont pas exportées ; en effet, il s'agit des statistiques";
            echo "de fréquentation du site. De plus, la table 'general' est modifiée afin de positionner le mode Internet.<br />";
            echo "L'export 'Site gratuit' permet d'exporter ses données au format texte afin de les charger sur un site personnel hébergé sur la plateforme Généamania.<br />";
            echo "L'option 'Masquage des dates récentes' permet de ne pas exporter les dates trop récentes afin de préserver la confidentialité de certaines données (personnes vivantes par exemple).<br />";
            echo "L'utilisateur peut spécifier un préfixe à attacher au nom du fichier (cette possibilité n'est pas offerte sur les sites gratuits standard).<br />";
            echo "L'icône " . '<img src="' . $root . '/assets/img/' . $Icones['oeil'] . '" alt="oeil" title="oeil">' . " permet de visualiser la liste des tables à exporter ; l'utilisateur";
            echo "peut ainsi choisir les tables qu'il souhaite exporter.<br />";
            echo "Le nom du fichier de sauvegarde par défaut est Export_Sauvegarde.sql (Export_Complet.sql pour les versions antérieures à la 2.1) pour ";
            echo "la sauvegarde et Export_Internet.sql pour l'export Internet ; le suffixe éventuel est inséré avant le point ;";
            echo 'le modificateur de nom de fichier éventuel est inséré après la cha&icirc;ne "Export_".<br />';
            echo "Cette page n'est disponible que pour le profil gestionnaire.";
            break;
        case 'Fusion_Evenements':
            echo "";
            echo "Cette page permet de fusionner les évènements présents en base.";
            echo "Les évènements présentant les mêmes lieux, type, titre et dates peuvent être fusionnés automatiquement par Généamania.<br />";
            echo "La page s'affiche dans un premier temps en mode visualisation pour permettre à l'utilisateur de voir ce que Généamania va faire en terme de fusion.<br />";
            echo "Cette page présente une liste des groupes d'évènements qui peuvent être fusionnés.";
            echo "Pour chaque groupe, le titre de l'évènement est précisé ; ensuite vient l'évènement de référence et chaque évènement &quot;doublon&quot;.";
            echo "L'utilisateur peut visualiser la référence et les doublons en cliquant sur le lien ad-hoc.";
            echo "De plus, Généamania indique le nombre de participations (donc de personnes), d'images et de documents rattachés à cet évènement.<br />";
            echo "La fusion sera effective lorsque l'utilisateur décochera la case &quot;Mode simulation&quot; et cliquera sur le bouton &quot;Fusionner&quot;.";
            echo "";
            break;
        case 'Histo_Ages_Deces':
            echo "Cette page permet de visualiser la répartition des &acirc;ges de décès des personnes contenues dans la base pour une période de naissance donnée.";
            echo "Si l'utilisateur n'a pas de profil privilégié, seules sont prises en compte les personnes dont la visibilité Internet est n'est pas restreinte.<br />";
            echo "Contrairement à l'historique de l'&acirc;ge, les enfants décédés avant l'&acirc;ge de 1 an sont pris en compte.<br />";
            echo "Pour chaque tranche d'&acirc;ge, le nombre de personnes et le pourcentage que cela représente sont précisés.";
            break;
        case 'Histo_Ages_Mariage':
            echo "Cette page permet de visualiser la répartition des &acirc;ges de premier mariage des personnes contenues dans la base pour une période de naissance donnée.";
            echo "Si l'utilisateur n'a pas de profil privilégié, seules sont prises en compte les personnes dont la visibilité Internet n'est pas restreinte.<br />";
            echo "Pour chaque tranche d'&acirc;ge sont précisés le nombre de personnes et le pourcentage que cela représente.";
            break;
        case 'Import_CSV':
            echo "Cette option permet d'intégrer dans la base des données issues d'un tableur (Libre Office, Excel...).<br />";
            echo "A ce jour, il est possible d'intégrer des données concernant les personnes uniquement (à l'exclusion des filiations et unions).<br />";
            echo "L'utilisateur doit indiquer la correspondance entre les colonnes du tableur et les champs de Génémania. Seules sont obligatoires les zones contenant les noms et prénoms.<br />";
            echo "Le séparateur de champs est le caractère Â« ; Â». Les dates sont au format JJ/MM/AAAA ou JJ-MM-AAAA. Les zones textuelles ne sont pas entourées de guillemets.<br />";
            echo "Voici un exemple de contenu de fichier :<br />";
            echo "Durand;Robert 1;30/11/1965;Amiens;m;27C<br />";
            echo "Durand;Marcel;3-5-1966;Amiens;m;27D<br />";
            break;
        case 'Import_Docs':
            echo "Lorsqu'un utilisateur a à la fois un site local sur son ordinateur et un site internet, il remonte les données de son site local";
            echo "vers son site internet en utilisant les fonctions d'export. Toutefois, ceci permet de remonter les données mais pas les images ou autres documents.";
            echo "L'utilisateur doit alors remonter ces images et documents via un logiciel de transfert de fichiers (exemple Filezilla) lorsque cela est possible.";
            echo "Lorsque cela n'est pas possible, il doit remonter ces fichiers via la fonction d'import de documents.<br />";
            echo "Les images et documents absents sont ceux qui ont été trouvés dans les données mais pour lequel le fichier n'est pas présent.<br />";
            echo 'L\'option "Remplacer" permet de ne pas écraser les fichiers de même nom présents.';
            break;
        case 'Import_Gedcom':
            echo "Cette page permet de recharger les données de la base à partir d'un fichier Gedcom";
            echo " ou d'afficher les données présentes dans le fichier.<br />";
            echo "Le nom du fichier de sauvegarde par défaut est export_gedcom.ged et se situe dans le répertoire Gedcom.<br />";
            echo "Signification des cases à cocher :";
            echo "<ul>";
            echo '<li>"Charger les données dans la base" permet de charger le fichier dans la base ;&nbsp;';
            echo 'lorsqu\'elle n\'est pas cochée, le fichier est juste lu et les données contenues dans le fichier sont affichées à l\'écran.</li>';
            echo '<li>"Vidage préalable de la base actuelle" permet de vider la base avant de charger le fichier. Attention, les données pré-existantes seront donc effacées.</li>';
            echo '<li>"Visibilité internet autorisée par défaut" permet d\'indiquer que les personnes chargées à partir du fichier seront visibles sur Internet sans restriction.</li>';
            echo '<li>"Visibilité internet des notes autorisée par défaut" permet d\'indiquer que les notes chargées à partir du fichier seront visibles sur Internet de profil.</li>';
            echo '<li>"Visibilité internet des images autorisée par défaut" permet d\'indiquer que les images reprises à partir du fichier seront visibles sur Internet, si elles ont été chargées par ailleurs.</li>';
            echo '<li>"Valeur par défaut des fiches créées" permet de spécifier le statut que prendront les fiches créées lors de l\'import.</li>';
            echo '<li>"Reprise des dates de modification du fichier" permet d\'indiquer que les dates de modification des personnes et des autres données seront celles du fichier ;';
            echo "si la case n'est pas cochée, la date de modification sera la date du jour.</li>";
            echo "</ul>";
            echo "Le format des lieux permet de sélectionner l'arborescence des zones géographiques présentes dans le fichier. Par défaut, le format est composé uniquement des villes.";
            echo "Le format est spécifié en sélectionnant successivement chaque niveau (e.g. ville, département, région, pays) dans la liste déroulante.";
            echo "L'icône " . '<img src="' . $root . '/assets/img/' . $Icones['efface'] . '" alt="Efface le format des lieux" title="Efface le format des lieux">' . " permet d'effacer le format des lieux précédemment sélectionné.";
            echo "L'arborescence est prise automatiquement en compte si elle est spécifiée dans l'entête du fichier à charger (balises PLAC/FORM).";
            echo "<br /><br />Cette page n'est disponible que pour le profil gestionnaire.";
            break;
        case 'Import_Sauvegarde':
            echo "Cette page permet de recharger les données de la base à partir d'un fichier de sauvegarde.<br />";
            echo 'L\'utilisateur peut demander à effacer préalablement le contenu de la base en cochant la case "Vidage préalable de la base actuelle".';
            echo "Attention, dans ce cas, il s'agit de toute la base dans laquelle les données Généamania sont implantées.";
            echo "N'utilisez pas cette option si Généamania partage la base d'une autre application !";
            echo "Cette option peut être utilisée dans le cas de la reprise d'une sauvegarde de version antérieure si vous voulez migrer cette sauvegarde vers la version actuelle.<br />";
            echo "Le fichier de sauvegarde peut être téléchargé par l'utilisateur ou sélectionné parmi les fichiers présents dans le répertoire des exports.";
            echo "Dans le cas o&ugrave; l'utilisateur télécharge un fichier et en sélectionne un en même temps, c'est le fichier téléchargé qui prime.<br />";
            echo "Sur un site hébergé gratuit, seuls les fichiers .txt sont autorisés ; dans les autres cas, les fichiers .txt et .sql sont autorisés.<br />";
            echo "Attention : les données présentes en base sont supprimées par le rechargement (en effet, ";
            echo "la sauvegarde inclue des ordres de suppression et recréation de tables).<br />";
            echo "La sauvegarde peut être rechargée en local (sur votre ordinateur) ou sur votre site web distant ";
            echo "si votre hébergeur le permet (connexion distante possible sur le port 3306 par exemple).";
            echo "Il faut cependant noter que cette possibilité de rechargement distant est consommatrice de ressources ; il est conseillé de diminuer le nombre de ";
            echo "données à charger sur votre base distante par exclusion de certaines tables (typiquement celles qui n'ont pas évolué [pays, etc...]).<br />";
            echo "Sur Internet, l'utilisateur peut demander à préserver la liste des utilisateurs présents ; cela évite par exemple lors d'un rechargement d'écraser cette liste à partir des utilisateurs locaux.<br />";
            echo "Cette page n'est disponible que pour le profil gestionnaire.";
            break;
        case 'Liste_Evenements':
            echo "Cette page permet de lister les évènements.<br />";
            echo "L'utilisateur peut choisir le type de d'évènement pour lequel il veut la liste (par défaut tous les types sont visualisés). Il dispose alors en plus du titre de l'évènement ";
            echo "d'informations sur les personnes concernées par l'évènement (éventuellement au travers de la filiation ou de l'union).<br />";
            echo "Seul le gestionnaire a accès à la modification de l'évènement.";
            break;
        case 'Liste_NomFam':
            echo "Cette page permet de lister les noms de famille.<br />";
            echo "&Agrave; partir de la liste, vous pouvez afficher un nom de famille et éventuellement le modifier.<br />";
            echo "L'accès à la modification dépend du profil de l'utilisateur.";
            break;
        case 'Liste_Nom_Vivants':
            echo "Cette page permet de lister les personnes vivantes pour un nom donné ou pour l'ensemble des noms.<br />";
            echo "Sont considérées comme décédées les personnes nées il y a plus de 130 ans et non décédées. <br />";
            echo "L'utilisateur peut ignorer les personnes dont la date de naissance n'est pas saisie. Il considère alors qu'il n'a";
            echo "pas suffisamment d'informations sur la personne, donc pas de nécessité de sélection, ou que l'ancêtre est trop éloigné.<br />";
            echo "NB : l'affichage des personnes dont la diffusion internet est interdite est fonction du profil de l'utilisateur.";
            break;
        case 'Naissances_Deces_Mois':
            echo "Cette page permet de visualiser la répartition mensuelle des naissances et des décès des personnes contenues dans la base.";
            echo "En mode Internet, seules sont prises en compte les personnes dont la diffusion Internet est autorisée.";
            echo "Le survol à la souris des barres du graphique permet de visualiser le nombre de personnes concernées pour un mois donné.";
            break;
        case 'Naissances_Mariages_Deces_Mois':
            echo "Cette page permet de visualiser la répartition mensuelle des naissances, des conceptions théoriques, des mariages et des décès des personnes contenues dans la base.<br />";
            echo "La conception théorique est calculée en retranchant 9 mois à la date de naissance.<br />";
            echo "Si l'utilisateur n'a pas de profil privilégié, seules sont prises en compte les personnes dont la diffusion Internet est autorisée ; les mariages sont comptabilisés si la diffusion Internet des 2 personnes est autorisée.<br />";
            echo "Le survol à la souris des barres du graphique permet de visualiser le nombre de personnes ou mariages concernés pour un mois donné.";
            break;
        case 'Pers_Isolees':
            echo "Cette page permet de lister les personnes isolées de la base.<br />";
            echo "Par personne isolée, on entend une personne sans filiation, ni union, ni relation avec une autre personne.<br />";
            echo "Cette page est accessible à partir du profil contributeur.";
            break;
        case 'Pyramide_Ages_Histo':
            echo "Cette page permet de visualiser l'évolution (en fonction de l'année de naissance) de l'&acirc;ge au décès des personnes contenues dans la base.";
            echo "Si l'utilisateur n'a pas de profil privilégié, seules sont prises en compte les personnes dont la visibilité Internet est n'est pas restreinte.";
            echo "De plus, les enfants décédés avant l'&acirc;ge de 1 an ne rentrent pas dans la statistique afin de ne pas biaiser la moyenne.<br />";
            echo "Le survol à la souris des barres du graphique permet de visualiser le nombre de personnes concernées sur la période.<br />";
            echo "En cliquant sur la période mentionnée au milieu, l'utilisateur peut visualiser la répartition des &acirc;ges de décès des personnes pour la période concernée.";
            break;
        case 'Pyramide_Ages':
            echo "Cette page permet de visualiser la pyramide des &acirc;ges au décès des personnes contenues dans la base.<br />";
            echo "Si l'utilisateur n'a pas de profil privilégié, seules sont prises en compte les personnes dont la la visibilité Internet est n'est pas restreinte.";
            echo "Le survol à la souris des barres du graphique permet de visualiser le nombre de personnes concernées pour un &acirc;ge donné.";
            echo "De plus, on peut se débrancher sur la fiche de la doyenne ou du doyen.";
            break;
        case 'Pyramide_Ages_Mar_Histo':
            echo "Cette page permet de visualiser l'évolution (en fonction de l'année de naissance) de l'&acirc;ge de premier mariage des personnes contenues dans la base.";
            echo "Si l'utilisateur n'a pas de profil privilégié, seules sont prises en compte les personnes dont la visibilité Internet est n'est pas restreinte.";
            echo "Le survol à la souris des barres du graphique permet de visualiser le nombre de personnes concernées sur la période.<br />";
            echo "En cliquant sur la période mentionnée au milieu, l'utilisateur peut visualiser la répartition des &acirc;ges de premier mariage des personnes pour la période concernée.";
            break;
        case 'Recherche_Commentaire':
            echo "Cette page, accessible aux personnes de profil gestionnaire, permet à l'utilisateur d'effectuer une recherche dans les commentaires stockés dans la base,";
            echo "quel que soit l'objet pointé par le commentaire (personne, union, zone géographique...).<br />";
            echo "L'utilisateur n'est pas obligé de saisir le contenu complet du commentaire ; de même, la casse (minuscules / majuscules) n'est pas prise en compte.<br />";
            echo 'E.g., si l\'utilisateur saisit le mot "ancien" (sans les guillemets), les commentaires suivants pourront être trouvés :';
            echo "<ul>";
            echo "<li>Ancien département de la Seine et Oise</li>";
            echo "<li>Naissance sur l'ancienne commune de ...</li>";
            echo "</ul>";
            echo "Le résultat peut avoir un format :";
            echo "<ul>";
            echo "<li>écran : la liste est cliquable ; l'utilisateur peut alors accéder à la personne, union...</li>";
            echo "<li>texte : ce format est destiné à être imprimé.</li>";
            echo "<li>CSV : ce format est destiné à être lu dans un tableur (LibreOffice, Excel...).</li>";
            echo "</ul>";
            break;
        case 'Recherche_Cousinage':
            $max_gen = $max_gen_loc;
            if ($Environnement == 'I') $max_gen = $max_gen_int;
            echo "Cette page permet de rechercher l'ancêtre commun à 2 personnes.<br />";
            echo "Cette recherche s'effectue sur " . $max_gen . " générations au maximum.";
            echo "Si l'ancêtre commun est trouvé, l'utilisateur peut visualiser sa fiche familiale, sous";
            echo "réserve de diffusabilité, ou ses arbres descendant ou ascendant.";
            echo "De même pour toutes les personnes présentes dans les 2 filiations.<br />";
            echo "En local, une case à cocher permet de sauvegarder la recherche. Cette sauvegarde peut être utilisée dans Génégraphe pour générer le graphique correspondant à la recherche.";
            break;
        case 'Recherche_Personne_Archive':
            echo "Cette page permet de lister les dates à vérifier aux archives.";
            echo "Ces dates concernent les personnes dont les fiches ne sont pas validées pour une ville donnée et éventuellement suivant une plage de dates.<br /><br />";
            echo "Le résultat peut avoir un format :";
            echo "<ul>";
            echo "<li>écran : la fiche familiale des personnes est alors accessible en cliquant sur les nom et prénoms de la liste";
            echo "et la fiche personne en cliquant sur l'icone " . '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '">' . "</li>";
            echo "<li>texte : ce format est destiné à être imprimé.</li>";
            echo "<li>CSV : ce format est destiné à être lu dans un tableur (LibreOffice, Excel...).</li>";
            echo "</ul>";
            echo $auto_contrib;
            break;
        case 'Recherche_Personne':
            echo "Cette page permet à l'utilisateur d'effectuer une recherche multi-critère sur les personnes de la base.";
            echo "Elle ramène toutes les personnes répondant aux critères demandés.";
            echo "En mode non privilégié, seules sont prises en compte les personnes dont la visibilté Internet n'est pas restreinte.<br />";
            echo "Les critères portant sur des zones de type &quot;caractères&quot; sont automatiquement mis en majuscules ; ainsi les prénoms 'jean' et 'Jean' sont équivalents.<br />";
            echo "Par défaut, le champ recherché doit être équivalent au champ saisi (sans considération de casse) ;";
            echo "cependant, sur les zones de type &quot;caractères&quot;, il est possible de faire des recherches partielles en introduisant un ou plusieurs caractères &quot;joker&quot; * ;";
            echo "ainsi la recherche sur le nom 'du*' donne les personnes s'appelant 'Durand', 'Dupond', 'Dumoulin'...";
            echo "Demander '*du*' ramènera toutes les personnes dont le nom contient la chaine de caractères 'du' à un emplacement quelconque.<br />";
            echo "Exemple : pour avoir toutes les femmes de la base, on coche le bouton &quot;Femme&quot; et on lance la recherche.";
            echo "Si on veut affiner la recherche et obtenir les femmes dont l'un des prénoms est &quot;Marie', on ajoutera '*marie*' dans la zone Prénoms.<br /><br />";
            echo "<br />La recherche sur le nom peut être orthographique, phonétique exacte ou phonétique approchée.<br />";
            echo "La recherche phonétique exacte donne tous les noms se pronon&ccedil;ant de la même fa&ccedil;on.<br />";
            echo "La recherche phonétique approchée fait des approximations sur la prononciation. Cela permet de rapprocher les sons suivants :";
            echo "<ul>";
            echo "<li>&laquo; a &raquo; et &laquo; &acirc; &raquo; ;</li>";
            echo "<li>&laquo; é &raquo; et &laquo; è &raquo; ;</li>";
            echo "<li>&laquo; o &raquo; et &laquo; ô &raquo; ;</li>";
            echo "<li>&laquo; in &raquo; et &laquo; un &raquo; ;</li>";
            echo "<li>&laquo; en &raquo; et &laquo; on &raquo; ;</li>";
            echo "<li>&laquo; n &raquo; et &laquo; gn &raquo;.</li>";
            echo "</ul>";
            echo "La recherche donne alors tous les noms de famille dont la prononciation correspond à celle du nom saisi tout en tenant compte des approximations.<br />";
            echo "<p>La sortie du résultat de la recherche peut s'effectuer sous liste cliquable (sortie écran), sous format destiné à être imprimé (sortie texte) ou sous forme de fichier CSV (pour un tableur, le séparateur étant le ";
            " ; disponible à partir du profil privilégié).</p>";
            break;
        case 'Recherche_Personne_CP':
            echo "Cette page permet à l'utilisateur d'effectuer une recherche multi-critère sur les personnes de la base. ";
            echo "Les critères s'appliquent aux conjoints ou parents et non à la personne elle-même.<br>";
            echo "Exemple, rechercher les personnes dont un parent femme est né à Paris.<br>";
            echo "Se référer à la recherche de personnes pour l'utilisation des critères.";
            break;
        case 'Recherche_Ville':
            echo "Cette page permet à l'utilisateur d'effectuer une recherche multi-critères sur les villes de la base.";
            echo "Elle ramène toutes les villes répondant aux critères demandés.<br />";
            echo "Le nom de la ville recherchée est automatiquement mis en majuscules ; ainsi les villes 'paris' et 'Paris' sont équivalentes.<br />";
            echo "Par défaut, le nom de la ville recherché doit être équivalent au champ saisi (sans considération de casse) ;";
            echo "il est cependant possible de faire des recherches partielles en introduisant un ou plusieurs caractères &quot;joker&quot; * ;";
            echo "ainsi la recherche sur le nom 'p*' donne les villes 'Paris', 'Perpignan'...";
            echo "Demander '*ar*' ramènera toutes les villes dont le nom contient la chaine de caractères 'ar' à un emplacement quelconque.<br /><br />";
            echo "<p>La sortie du résultat de la recherche peut s'effectuer sous liste cliquable (sortie écran), sous format destiné à être imprimé (sortie texte) ou sous forme de fichier CSV (pour un tableur, le séparateur étant le ";
            " ; disponible à partir du profil privilégié).</p>";
            break;
        case 'Stat_Base_Depart':
            echo "Cette page permet de visualiser la répartition des naissances et des décès par département.";
            echo "En mode Internet, seules sont prises en compte les personnes dont la diffusion Internet est autorisée si l'utilisateur n'a pas un profil privilégié.<br />";
            echo "L'icone " . '<img src="' . $root . '/assets/img/' . $Icones['carte_france'] . '" alt="Carte de France" title="Carte de France">' . " permet de visualiser la répartition géographique sur la carte de la France.";
            break;
        case 'Stat_Base_Villes':
            echo "Cette page permet de visualiser la répartition des naissances, mariages et des décès par villes.<br />";
            echo "En cliquant sur une ville, on peut se débrancher sur la fiche de la ville.";
            echo "En cliquant sur un nombre, on peut se débrancher sur la liste des personnes nées, mariées ou décédées dans la ville.<br />";
            echo "En mode Internet, seules sont prises en compte les personnes dont la diffusion Internet est autorisée si l'utilisateur n'a pas un profil privilégié.";
            break;
        case 'Verif_Internet_Absente':
            echo "Cette page permet de visualiser les personnes non visibles sur Internet mais décédées il y a plus " . $Lim_Diffu . " ou nées il y a plus de " . $Lim_Diffu_Dec . " ans.<br>";
            echo "L'utilisateur peut rectifier les incohérences en cliquant sur le bouton &quot;Rectifier&quot;. ";
            echo "Seules sont modifiées les lignes que l'utilisateur a <u>cochées</u>.";
            echo "La visibilité Internet des personnes cochées passe alors à Oui, tout le monde pourra alors les visualiser.";
            break;
        case 'Verif_Internet':
            echo "Cette page permet de visualiser les personnes visibles sur Internet mais nées ou décédées il y a moins de " . $Lim_Diffu . " ans.";
            echo "Cela peut mettre en lumière des problèmes de confidentialité de données.<br />";
            echo "L'utilisateur peut rectifier les incohérences en cliquant sur le bouton &quot;Rectifier&quot;.";
            echo "Seules sont modifiées les lignes que l'utilisateur a <u>décochées</u>.";
            echo "La visibilité Internet des personnes décochées passe alors à Non et ces personnes ne sont visibles que des utilisateurs ayant un profil au minimum privilégié.";
            break;
        case 'Verif_Personne':
            echo "Cette page affiche le résultat des contrôles de la fiche d'une personne. Ils se font à plusieurs niveaux.";
            echo "<br /><strong>Pour la personne</strong><br />";
            echo "- que la fiche soit visible sur Internet ;<br />";
            echo "- que la fiche soit validée ;<br />";
            echo "- que les dates de naissance et de décès (dans le cas des personnes non vivantes) soient présentes et qu'elles correspondent à un jour précis (le ...) ;<br />";
            echo "- que la date de naissance précède ou soit égale à la date de décès.";
            echo "<br /><strong>Avec ses parents :</strong><br />";
            echo "- que les dates de décès du père et de la mère soient présentes (dans le cas des personnes non vivantes) et qu'elles correspondent à un jour précis (le ...) ;<br />";
            echo "- que la personne soit née après que le père et la mère aient 15 ans ; <br />";
            echo "- que la personne soit née au plus tard 9 mois après le décès du père ou de la mère.";
            echo "<br /><strong>Avec ses unions :</strong><br />";
            echo "- que la personne ait plus de 15 ans quand elle s'unit à une autre personne ;<br />";
            echo "- que la personne avec qui elle s'unit soit vivante lors de cette union.";
            echo "<br /><strong>Avec les enfants :</strong><br />";
            echo "- que les dates de naissance des enfants  soient soient présentes et qu'elles correspondent à un jour précis (le ...) ;<br />";
            echo "- que la personne ait au moins 15 ans à la naissance des enfants ;<br />";
            echo "- que la personne soit décédée depuis moins de 9 mois lors de la naissance des enfants.";
            break;
        case 'Verif_Sosa':
            echo "Cette page permet de visualiser les incohérences entre les numéros Sosa saisis par l'utilisateur et ceux calculés par Généamania.";
            echo "La détection d'incohérence peut être incorrecte dans le cas de personnes apparaissant plusieurs fois dans l'arbre (implexes).<br />";
            echo "Il est d'autre part à noter que cette vérification ne balaye que les personnes dans l'ascendance du de cujus ; ainsi une personne hors de cette ascendance ne verra pas son numéro contrôlé.<br />";
            echo "La personne de référence sur laquelle s'appuie le calcul est le de cujus (numéro 1).";
            echo "En cas d'absence de de cujus, Généamania affiche un message d'erreur.<br />";
            echo "L'utilisateur peut rectifier les incohérences en cliquant sur le bouton &quot;Rectifier&quot;.";
            echo "Seules sont modifiées les lignes que l'utilisateur a cochées (la case &quot;tous&quot; permet de cocher / décocher toutes les lignes.";
            break;
        case 'Vue_Personnalisee':
            echo "Cette page permet de choisir un de cujus différent de celui par défaut pour les listes par générations et patronymique.<br />";
            echo 'Le de cujus personnalisé est mémorisé lorsque l\'utilisateur clique sur bouton "' . $lib_Okay . '". Il n\'est valable que pour la session en cours.';
            break;
        case 'Liste_Noms_Non_Ut':
            echo 'Cette page permet de visualiser et éventuellement supprimer les noms de famille, principaux ou secondaires, qui ne sont portés par aucune personne.';
            break;
        case 'Rectif_Utf8':
            echo "Lorsque l'on importe un fichier, par exemple lors d'un import Gedcom d'un fichier en UTF-8 sans avoir sélectionné l'option ad-hoc, il peut arriver que les caractères accentués et les \"&ccedil;\" sont mal retranscrits.<br />";
            echo "L'idée est alors de rectifier la base pour que ces caractères soient corrects.";
            break;
        case 'Verif_Homonymes':
            echo "Cette page permet d'afficher les homonymes présents dans la base. Ceux-ci sont triés par nom, prénoms, date de naissance et de date de décès.<br />";
            echo 'La liste des personnes peut être restreinte en cochant les cases "Date de naissance" et "Date de décès". Ainsi, si l’on coche la case "Date de naissance", le contrôle d’homonymie prendra également en compte la date de naissance ; il s’agit alors d’identifier les doublons réels et plus seulement les homonymes.';
            echo "Pour chaque couple nom – prénoms, on peut afficher 2 personnes en parallèle en les sélectionnant via les boutons radio et en cliquant sur l'icône " . '<img src="' . $root . '/assets/img/' . $Icones['2personnes'] . '" alt="Comparaison de 2 personnes" title="Comparaison de 2 personnes">' . ".";
            break;
        case 'Import_CSV_Evenements':
            echo "Il est possible de créer des évènements à partir d'un fichier csv issu d'un tableur.<br />";
            echo "On pourra spécifier un lieu et un type pour les évènements qui vont être créés.<br /><br />";
            echo "Le fichier peut contenir une entête (1ère ligne) qui donnera la liste des champs. Cette entête contiendra alors : <br />";
            echo "titre;debut;fin<br /><br />";
            echo "Exemple pour la suite du fichier : <br />";
            echo "Prise de la Bastille;14.07.1789;14.07.1789<br />";
            echo "Fête de la Saint-Jean;24.06.1802;24.06.1802<br />";
            echo "essai évènement 3;01.01.1903;02.02.1903<br />";
            break;
        case 'Init_Sosa':
            echo "Cet utilitaire permet de supprimer les numéros sosa de toutes les personnes de la base.<br />";
            echo "Il est particulièrement utile lorsque l'on change le decujus et peut être appelé avant la vérification de la numérotation.<br />";
            break;
        case 'Init_Noms':
            echo "Sur certains imports qui ont mal fonctionné, les liens vers les noms de famille sont incomplets ; il faut alors les compléter.<br />";
            echo "C’est l’objet de cet utilitaire.<br />";
            echo "Le paramètre ini=o permet en plus de refaire la table des noms de famille et des liens.<br />";
            break;
        case 'Liste_Pers_Gen':
            echo "Cette fonctionnalité permet de lister toutes les personnes situées dans l'ascendance directe du de cujus. La liste est triée par génération. A chaque génération, une rupture est effectuée afin d'afficher le numéro de la génération. On peut ensuite se débrancher sur la personne en cliquant sur le lien nom / prénom de la personne.<br />";
            echo "Il est à noter que le de cujus peut être temporairement différent de celui positionnée par le gestionnaire de la base. On parle alors de &laquo;&nbsp;vue personnalisée&nbsp;&raquo;.<br />";
            echo "La visibilité des personnes est restreinte par le profil de l'utilisateur connecté. La case &laquo;&nbsp;Simulation accès invité&nbsp;&raquo; permet de voir les générations telles que les verraient des personnes non connectées (typiquement un utilsateur lambda sur Internet).";
            break;
        case 'Export_Pour_Deces':
            echo "L'INSEE met à disposition les décès survenus depuis 1970. Le site $url_matchid permet de faire des recherches unitaires ou par lot dans ces décès. ";
            echo "MatchId dispose d’une recherche intelligente qui peut renvoyer des personnes qui correspondent « à peu près » aux critères demandés.<br>";
            echo "<br>Cette fonctionnalité permet soit :<br>";
            echo "&nbsp;-&nbsp;De lister à l’écran les personnes concernées en vue d’effectuer un appel unitaire à matchId.<br>";
            echo "&nbsp;-&nbsp;De constituer un fichier pour faire une recherche par lot sur ce site, dans la rubrique « Appariement ». En retour, matchId fournira un fichier dans lequel on retrouvera les dates et lieux de décès des personnes fournies dans le fichier en entrée.<br>";
            echo "<br>Les personnes listées ou exportées sont celles dont la ville de naissance est connue, la date de naissance connue exactement avec une année postérieure ou égale à l&rsquor;année saisie par l’utilisateur.<br>";
            break;
        case 'Recherche_MatchId_Unitaire':
            echo "Cette fonctionnalité permet d'interroger matchId pour récupérer une liste de personnes correspondant « à peu près » aux critères envoyés par Geneamania.<br>";
            echo "MatchId va renvoyer une liste de personnes correspondant « à peu près » aux critères envoyés par Geneamania. Les informations de ces personnes sont affichées sous celles connues dans Geneamania. "
                . "<br>Un bouton présent sur la ligne de décès de chaque personne permet de copier la date de décès dans le presse-paier. "
                . "Cette date pourra ensuite être copiée dans la fenêtre de saisie de date de décès de la personne (Ctrl+V sous Windows, champ « Saisie rapide d'une date grégorienne »).<br>";
            echo "<br>Attention : il faut impérativement être connecté à Internet pour obtenir un résultat.<br>";
            break;
        default:
            echo "Désolé, mais il n'y a pas d'aide en ligne pour cette page...";
    }
    ?>
    </body>

</html>