<?php


require(__DIR__ . '/../app/bootstrap.php');

$map = [
    '/' => 'index.php',
    // '/admin_tables' => 'admin_tables.php',

    '/aide_geneamania' => 'aide_geneamania.php',
    '/aide_mdp' => 'aide_mdp.php',

    // '/ajout_contribution' => 'ajout_contribution.php',
    // '/ajout_enfants' => 'ajout_enfants.php',
    // '/ajout_rapide' => 'ajout_rapide.php',

    // '/anniversaires' => 'anniversaires.php',

    // '/appel_info' => 'appel_info.php',
    // '/appel_chronologie_personne' => 'appel_chronologie_personne.php',
    // '/appel_image_arbre_asc' => 'appel_image_arbre_asc.php',
    // '/appel_image_france_dep' => 'appel_image_france_dep.php',

    // '/arbre_agnatique_cognatique' => 'arbre_agnatique_cognatique.php',
    // '/arbre_asc_pdf' => 'arbre_asc_pdf.php',
    // '/arbre_asc_pers' => 'arbre_asc_pers.php',
    // '/arbre_desc_pers' => 'arbre_desc_pers.php',
    // '/arbre_noyau' => 'arbre_noyau.php',
    // '/arbre_perso' => 'arbre_perso.php',

    // '/asc_conjoints' => 'asc_conjoints.php',

    // '/cache_montre_rapide' => 'cache_montre_rapide.php',
    // '/cal' => 'cal',
    // '/calc_so' => 'calc_so.php',
    // '/calcul_distance' => 'calcul_distance.php',
    // '/calendriers' => 'calendriers.php',
    // '/captcha_image_gen' => 'captcha_image_gen.php',
    // '/codage_nomfam' => 'codage_nomfam.php',
    // '/color_picker' => 'color_picker.php',
    // '/completude_nom' => 'completude_nom.php',
    // '/controle_nomfam' => 'controle_nomfam.php',
    // '/controle_personnes' => 'controle_personnes.php',
    // '/conv_romain' => 'conv_romain.php',
    // '/create_multiple_docs' => 'create_multiple_docs.php',
    // '/desc_directe_pers' => 'desc_directe_pers.php',

    // '/edition_categorie' => 'edition_categorie.php',
    // '/edition_contribution' => 'edition_contribution.php',
    '/edition_depart' => 'edition_depart.php',
    // '/edition_depot' => 'edition_depot.php',
    // '/edition_document' => 'edition_document.php',
    '/edition_evenement' => 'edition_evenement.php',
    // '/edition_filiation' => 'edition_filiation.php',
    // '/edition_image' => 'edition_image.php',
    // '/edition_lien' => 'edition_lien.php',

    // '/edition_lier_doc' => 'edition_lier_doc.php',
    // '/edition_lier_eve' => 'edition_lier_eve.php',
    // '/edition_lier_nom' => 'edition_lier_nom.php',
    // '/edition_lier_objet' => 'edition_lier_objet.php',
    // '/edition_lier_pers' => 'edition_lier_pers.php',
    // '/edition_lier_source' => 'edition_lier_source.php',

    '/edition_nomfam' => 'edition_nomfam.php',
    '/edition_parametres_graphiques' => 'edition_parametres_graphiques.php',
    // '/edition_parametres_site' => 'edition_parametres_site.php',

    '/edition_personne' => 'edition_personne.php',
    // '/edition_personnes_ville' => 'edition_personnes_ville.php',
    // '/edition_rangs' => 'edition_rangs.php',
    // '/edition_region' => 'edition_region.php',
    // '/edition_requete' => 'edition_requete.php',
    // '/edition_role' => 'edition_role.php',
    // '/edition_source' => 'edition_source.php',
    '/edition_subdivision' => 'edition_subdivision.php',
    // '/edition_type_document' => 'edition_type_document.php',
    // '/edition_type_evenement' => 'edition_type_evenement.php',
    // '/edition_union' => 'edition_union.php',
    // '/edition_utilisateur' => 'edition_utilisateur.php',
    '/edition_ville' => 'edition_ville.php',

    // '/enfants_femme_histo' => 'enfants_femme_histo.php',

    // '/erreur_profil' => 'erreur_profil.php',

    // '/exp_gedcom_personne' => 'exp_gedcom_personne.php',
    // '/exp_gedcom' => 'exp_gedcom.php',
    // '/exp_genweb' => 'exp_genweb.php',
    // '/export_liens' => 'export_liens.php',
    // '/export_pour_deces' => 'export_pour_deces.php',
    // '/export' => 'export.php',

    // '/fiche_actualite' => '.php',
    // '/fiche_categorie' => 'fiche_categorie.php',
    // '/fiche_couple_txt' => 'fiche_couple_txt.php',
    // '/fiche_depot' => 'fiche_depot.php',
    // '/fiche_document' => 'fiche_document.php',
    // '/fiche_evenement' => 'fiche_evenement.php',
    '/fiche_fam_pers_' => 'fiche_fam_pers_.php',
    // '/fiche_homonymes' => 'fiche_homonymes.php',
    // '/fiche_indiv_txt' => 'fiche_indiv_txt.php',
    // '/fiche_lien' => 'fiche_lien.php',
    // '/fiche_nomfam' => 'fiche_nomfam.php',
    // '/fiche_requete' => 'fiche_requete.php',
    // '/fiche_role' => 'fiche_role.php',
    // '/fiche_source' => 'fiche_source.php',
    // '/fiche_subdivision' => 'fiche_subdivision.php',
    // '/fiche_type_document' => 'fiche_type_document.php',
    // '/fiche__type_evenement' => 'fiche__type_evenement.php',
    // '/fiche_utilisateur' => 'fiche_utilisateur.php',
    '/fiche_ville' => 'fiche_ville.php',

    // '/fusion_evenements' => 'fusion_evenements.php',
    // '/fusion_ville' => 'fusion_ville.php',

    // '/galerie_images' => 'galerie_images.php',
    // '/glossaire_gen' => 'glossaire_gen.php',
    // '/glossaire_sosa' => 'glossaire_sosa.php',

    // '/histo_ages_deces' => 'histo_ages_deces.php',
    // '/histo_ages_mariage' => 'histo_ages_mariage.php',
    // '/histo_prenoms' => 'histo_prenoms.php',

    // '/image_arbre_asc' => 'image_arbre_asc.php',
    // '/image_depart' => 'image_depart.php',

    // '/import_csv_evenements' => 'import_csv_evenements.php',
    // '/import_csv_liens' => 'import_csv_liens.php',
    // '/import_csv_villes' => 'import_csv_villes.php',
    // '/import_csv' => 'import_csv.php',
    // '/import_docs' => 'import_docs.php',
    // '/import_gedcom' => 'import_gedcom.php',
    // '/import_sauvegarde' => 'import_sauvegarde.php',

    // '/infos_tech' => 'infos_tech.php',
    // '/init_noms' => 'init_noms.php',
    // '/init_sosa' => 'init_sosa.php',
    '/install' => 'install.php',


    // '/liste_connexions' => 'liste_connexions.php',
    // '/liste_contributions' => 'liste_contributions.php',
    // '/liste_docs_branche' => 'liste_docs_branche.php',
    // '/liste_documents' => 'liste_documents.php',
    // '/liste_eclair' => 'liste_eclair.php',
    // '/liste_evenements_zone' => 'liste_evenements_zone.php',
    // '/liste_evenements' => 'liste_evenements.php',
    // '/liste_images' => 'liste_images.php',
    // '/liste_liens' => 'liste_liens.php',
    // '/liste_nom_evenements' => 'liste_nom_evenements.php',
    // '/liste_nom_pop' => 'liste_nom_pop.php',
    // '/liste_nom_vivants' => 'liste_nom_vivants.php',
    '/liste_nomfam' => 'liste_nomfam.php',
    // '/liste_noms_non_ut' => 'liste_noms_non_ut.php',
    // '/liste_patro' => 'liste_patro.php',
    // '/liste_pers_gen' => 'liste_pers_gen.php',
    // '/liste_pers_mod' => 'liste_pers_mod.php',
    // '/liste_pers_role' => 'liste_pers_role.php',
    '/liste_pers' => 'liste_pers.php',
    // '/liste_pers2' => 'liste_pers2.php',
    // '/liste_prof_pop' => 'liste_prof_pop.php',
    // '/liste_referentiel' => 'liste_referentiel.php',
    // '/liste_sources' => 'liste_sources.php',
    // '/liste_utilisateurs' => 'liste_utilisateurs.php',
    '/liste_villes' => 'liste_villes.php',

    // '/mail_ut' => 'mail_ut.php',

    // '/naissances_mariages_deces_mois' => 'naissances_mariages_deces_mois.php',
    // '/notaires_ville' => 'notaires_ville.php',
    '/noyau_pers' => 'noyau_pers.php',

    // '/parentees' => 'parentees.php',
    // '/pers_isolees' => 'pers_isolees.php',
    // '/premiers_pas_genealogie' => 'premiers_pas_genealogie.php',
    // '/pyramide_ages_histo' => 'pyramide_ages_histo.php',
    // '/pyramide_ages_mar_histo' => 'pyramide_ages_mar_histo.php',
    // '/pyramide_ages' => 'pyramide_ages.php',

    // '/recherche_commentaire' => 'recherche_commentaire.php',
    // '/recherche_cousinage' => 'recherche_cousinage.php',
    // '/recherche_document' => 'recherche_document.php',
    // '/recherche_matchid_unitaire' => 'recherche_matchid_unitaire.php',
    // '/recherche_personne_archive' => 'recherche_personne_archive.php',
    // '/recherche_personne_cp' => 'recherche_personne_cp.php',
    // '/recherche_personne' => 'recherche_personne.php',
    // '/recherche_ville' => 'recherche_ville.php',

    // '/rectif_null' => 'rectif_null.php',
    // '/rectif_utf8' => 'rectif_utf8.php',

    // '/recup_sources' => 'recup_sources.php',

    // '/rpc_document' => 'rpc_document.php',
    // '/rpc_evenement' => 'rpc_evenement.php',
    // '/rpc_personne' => 'rpc_personne.php',

    // '/sel_zone_geo' => 'sel_zone_geo.php',

    // '/stat_base_depart' => 'stat_basee_depart.php',
    // '/stat_base_generations' => 'stat_base_generations.php',
    // '/stat_base_villes' => 'stat_base_villes.php',
    '/stat_base' => 'stat_base.php',

    // '/utilisations_document' => 'utilisations_document.php',

    // '/verif_homonymes' => 'verif_homonymes.php',
    // '/verif_internet_absente' => 'verif_internet_absente.php',
    // '/verif_internet' => 'verif_internet.php',
    // '/verif_personne' => 'verif_personne.php',
    // '/verif_sosa' => 'verif_sosa.php',

    // '/vide_base' => 'vide_base.php',

    // '/vue_personnalisee_rapide' => 'vue_personnalisee_rapide.php',
    // '/vue_personnalisee' => 'vue_personnalisee.php',
];

$path = $request->getPathInfo();

if (isset($map[$path]) && file_exists(__DIR__ . '/../htdocs/' . $map[$path])) {
    $nom_page = $map[$path];
    require(__DIR__ . '/../htdocs/' . $map[$path]);
} else {
    $response->setStatusCode(404);
    $response->setContent('Not Found');
}

$response->send();
