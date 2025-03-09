<?php
//====================================================================
//  Affichage de la liste des évènements sur une zone géographique
//=====================================================================

session_start();
include('fonctions.php');
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
include('Gestion_Pages.php');          // Appel de la gestion standard des pages

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();
else {

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
    $echo_modif = '<img src="' . $chemin_images_icones . $Icones['fiche_edition'] . '" border="0" alt="' . $texte . '" title="' . $texte . '"/></a>';

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
            $page = 'Fiche_Evenement';
            if ($enreg['Code_Type'] == 'AC3U') $page = 'Fiche_Actualite';
            echo '<a href="' . $page . '.php?refPar=' . $ref_evt . '">' . $enreg['Titre'] . "</a>\n";
            echo Etend_2_dates($enreg['Debut'], $enreg['Fin'], true) . '&nbsp';
            echo '<br>';
        }
    }

    // Formulaire pour le bouton retour
    Bouton_Retour($lib_Retour, '?' . Query_Str());

    Insere_Bas($compl);
}
?>
</body>

</html>