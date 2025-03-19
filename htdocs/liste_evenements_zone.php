<?php
//====================================================================
//  Affichage de la liste des évènements sur une zone géographique
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$niv_requis = 'C';

$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$titre = $LG_Menu_Title['Event_List_Area'];       // Titre pour META

$x = Lit_Env();
$niv_requis = 'P';                        // Page réservée au profil privilégié
require(__DIR__ . '/../app/ressources/gestion_pages.php');          // Appel de la gestion standard des pages

    $idZone = Recup_Variable('zone', 'N');
    $nivZone = Recup_Variable('niveau', 'N');
    // Niveau ville par défaut
    if (!$nivZone)
        $nivZone = 4;

    $compl = Ajoute_Page_Info(600, 200);

    Insere_Haut($titre, $compl, 'Liste_Evenements_Zone', '');

    $n_evenements = nom_table('evenements');
    $n_personnes = nom_table('personnes');
    $n_participe = nom_table('participe');
    $n_concerne_objet = nom_table('concerne_objet');
    $n_unions = nom_table('unions');

    // Optimisation : préparation echo des images
    $texte = $LG_add;
    $echo_modif = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" border="0" alt="' . $texte . '" title="' . $texte . '"/></a>';

    // Récupération des informations sur la zone géographique
    switch ($nivZone) {
        case '5':
            $lib_niv = LG_AREA_SUBDIV;
            $lib_zone = lib_subdivision($idZone);
            break;
        case '4':
            $lib_niv = LG_AREA_TOWN;
            $lib_zone = lib_ville($idZone);
            break;
        case '3':
            $lib_niv = LG_AREA_COUNTY;
            $lib_zone = lib_departement($idZone);
            break;
        case '2':
            $lib_niv = LG_AREA_REGION;
            $lib_zone = lib_region($idZone);
            break;
        case '1':
            $lib_niv = LG_AREA_COUNTRY;
            $lib_zone = lib_pays($idZone);
            break;
        default:
            break;
    }

    echo '<br>' . $lib_niv . ' : ' . $lib_zone . '<br><br>';

    // Constitution de la requête d'extraction
    $requete = 'SELECT e.Reference, e.Titre, t.Objet_Cible, t.Code_Type, t.Libelle_Type, e.Debut, e.Fin ' .
        'from ' . $n_evenements . ' e , ' . nom_table('types_evenement') . ' t ' .
        'where e.Code_Type = t.Code_Type '
        . ' and Identifiant_zone = ' . $idZone
        . ' and Identifiant_Niveau = ' . $nivZone
        . ' ORDER BY e.Titre';
    $result = lect_sql($requete);

    //  Affichage des évènements
    if ($result->rowCount() > 0) {

        while ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($debug) var_dump($enreg);
            $ref_evt = $enreg['Reference'];
            $page = 'fiche_evenement';
            if ($enreg['Code_Type'] == 'AC3U') $page = 'fiche_actualite';
            echo '<a href="' . $root . '/' . $page . '.php?refPar=' . $ref_evt . '">' . $enreg['Titre'] . "</a>\n";
            echo Etend_2_dates($enreg['Debut'], $enreg['Fin'], true) . '&nbsp';
            echo '<br>';
        }
    }

    // Formulaire pour le bouton retour
    Bouton_Retour($lib_Retour, '?' . Query_Str());

    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
?>
</body>

</html>