<?php
//=====================================================================
// Affichage d'un évènement
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                // Type d'accès de la page : (L)ecture

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$actu = Recup_Variable('actu', 'C', 'xo');
$actualite = ($actu === 'o' ? true : false);

// Titre pour META
if ($actualite) $titre = $LG_Menu_Title['New'];
else $titre = $LG_Menu_Title['Event'];

$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup de la variable passée dans l'URL : référence de l'évènement
$refPar = Recup_Variable('refPar', 'N');

$ajout = '';
if ($actualite) $ajout = '&amp;actu=o';

$compl = Ajoute_Page_Info(600, 150);
if ($est_gestionnaire) {
    $compl .= Affiche_Icone_Lien('href="' . $root . '/edition_evenement.php?refPar=' . $refPar . $ajout . '"', 'fiche_edition', $LG_modify) . '&nbsp;';
}
Insere_Haut($titre, $compl, 'Fiche_Evenement', '');

$requete = 'SELECT * FROM ' . nom_table('evenements') . ' e, ' . nom_table('types_evenement') . ' t' .
    " WHERE reference = $refPar" .
    ' AND e.Code_Type = t.Code_Type limit 1';

if ($result = lect_sql($requete)) {
    if ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
        //  Mise en place des donnees
        $LibelleTypeLu = $enreg['Libelle_Type'];
        $nomZone       = LectZone($enreg['Identifiant_zone'], $enreg['Identifiant_Niveau']);
        $titreLu       = $enreg['Titre'];
        $dDebLu        = $enreg['Debut'];
        $dFinLu        = $enreg['Fin'];
        $statutLu      = $enreg['Statut_Fiche'];
        $objetCibleLu  = $enreg['Objet_Cible'];
        if ($debug) var_dump($enreg);

        $Type_Ref = 'E';

        // Affichage de l'image par défaut
        $image = Rech_Image_Defaut($refPar, $Type_Ref);
        if ($image != '') {
            Aff_Img_Redim_Lien($chemin_images_util . $image, 150, 150, 'image_evt');
            echo '<br />' . $titre_img . '<br /><br />' . "\n";
        }

        echo '<br />';
        echo '<table width="80%" class="table_form" align="center">' . "\n";
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Event_Title) . '</td><td class="value">' . $titreLu . '</td></tr>';
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Event_Type) . '</td><td class="value">' . '<a href="' . $root . '/fiche_type_evenement.php?code=' . $enreg['Code_Type'] . '">' . $LibelleTypeLu . '</a></td></tr>';
        if ($nomZone != '')
            echo '<tr><td class="label" width="25%">' . ucfirst($LG_Event_Where) . '</td><td class="value">' . $nomZone . '</td></tr>';
        if (($dDebLu != '') or ($dFinLu != ''))
            echo '<tr><td class="label" width="25%">' . ucfirst($LG_Event_When) . '</td><td class="value">' . Etend_2_dates($dDebLu, $dFinLu) . '</td></tr>';
        echo '</table>';

        //  ===== Affichage du commentaire
        if (Rech_Commentaire($refPar, $Type_Ref)) {
            if (($Commentaire != '') and (($est_privilegie) or ($Diffusion_Commentaire_Internet == 'O'))) {
                echo '<fieldset><legend>Note</legend>' . my_html($Commentaire) . '</fieldset><br>' . "\n";
            }
        }

        // Conditionner l'affichage par la cible de l'évènement
        aff_lien_pers($refPar, 'N');  //  === Affichage des liens avec des personnes
        aff_lien_filiations($refPar, 'N');   //  === Affichage des liens avec des filiations
        aff_lien_unions($refPar, 'N'); // === Affichage des liens avec des unions
        Aff_Documents_Objet($refPar, $Type_Ref, 'O'); //  Documents liés à l'évènement
        if ($objetCibleLu == 'P') { // Affichage de la liste des noms pour l'évenement
            echo '<br /><a href="' . $root . '/liste_nom_evenement.php?refPar=' . $refPar . '">Liste des noms pour l\'&eacute;v&egrave;nement</a>';
        }
        echo '<br />' . "\n";
        Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
        echo '<table cellpadding="0" width="100%">';
        echo '<tr>';
        echo '<td align="right">';
        echo $compl;
        echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
        echo "</td>";
        echo '</tr>';
        echo '</table>';
    }
}

?>
</body>

</html>