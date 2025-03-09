<?php

$salt1 = ';$€°d';
$salt2 = '#\'_^';
$salt3 = '@")[&ù';

//echo $_SERVER['PHP_SELF'].'.';
//$nom_page = $_SERVER['PHP_SELF'];
$nom_page = my_self();
$pos = strrpos($nom_page, '/');
if ($pos !== false) $nom_page = substr($nom_page, $pos + 1);
//echo $nom_page.'.';

// Spécifique index
if ($nom_page == 'index.php') {
    //if (strpos($_SERVER['PHP_SELF'],'index.php') !== false) {
    $LG_index_welcome = 'Bienvenue sur le site de généalogie de';
    $LG_index_responsability = 'Les données sont publiées sous la responsabilité du titulaire du site';
    $LG_index_connexion_error = 'Erreur de code utilisateur et/ou mot de passe';
    $LG_index_title = 'Généalogie';
    $LG_index_desc = 'Accueil de la généalogie de';
    $LG_index_tip_no_param = 'Pensez à paramétrer votre site dans le menu "Gestion du site"';
    $LG_index_quick_search = 'Recherche rapide';
    $LG_index_tip_search = 'Le nom peut contenir le caractère générique * qui remplace alors un nombre quelconque de caractères ' .
        '(exemple : saint* donne les noms commençant par "saint", donc également ceux commençant par "sainte"). ' .
        'La recherche ne tient pas compte des minuscules / majuscules.';
    //$LG_index_tip_infos = 'Plus d\'infos sur les recherches au survol des ';
    $LG_index_tip_maintenance = 'Site en maintenance, merci de revenir ultérieurement';
    $LG_index_menu_pers = 'Personnes';
    $LG_index_menu_names = 'Noms';
    $LG_index_menu_towns = 'Villes';
    $LG_index_searches = 'Recherches';
    $LG_index_news = 'Actualités';
    $LG_index_links = 'Liens';
    $LG_index_last_update = 'Dernière mise à jour du site le';
    $LG_index_forum = 'Forum Généamania';
    $LG_index_ask_site = 'Demandez votre site personnel gratuit';
    $LG_index_version = 'logiciel de généalogie libre et gratuit, version';
    $LG_index_hested_premium = ' site hébergé Premium';
    $LG_index_hested_free = ' site hébergé gratuit';
    $LG_index_birthdays = 'anniversaires de naissance';
    $LG_index_today = 'aujourd\'hui ';
    $LG_index_tomorrow = 'demain ';
    $LG_index_version_mismatched = 'Incohérence dans la version';
    $LG_index_please_migrate = 'veuillez procéder à une migration de la base.';
    $LG_index_migrate_here = 'Migration disponible ici';
    $LG_index_password = 'mot de passe';
    $LG_index_connexion = 'Connexion au site';
    $LG_index_connected_user = 'Vous utilisez actuellement l\'utilisateur';
    $LG_index_connected_level = 'avec des droits de niveau';
    $LG_index_contact_support = 'Site verrouillé, merci de contacter le support';
    $LG_index_info_genegraphe = 'Lancement de GénéGraphe';
    $LG_index_doc_genegraphe = 'Documentation de GénéGraphe';
    $LG_index_getting_started_Windows = 'Guide de démarrage rapide Windows';
    $LG_index_getting_started_hosted = 'Guide de démarrage rapide site hébergé';
    $LG_index_hosted_free = 'site hébergé gratuit';
    $LG_index_hosted_premium = 'site hébergé Premium';
    $LG_index_psw_forgoten = 'mot de passe oublié';
    $LG_index_psw_show = 'Montrer / cacher le mot de passe';
}

// Spécifique Liste des personnes 1/2
if ($nom_page == 'Liste_Pers.php') {
    define('LG_CREATE_PERS_BORN_IN', 'Création de personnes nées à');
    define('LG_CREATE_PERS_DEAD_IN', 'Création de personnes décédées à');
    define('LG_LPERS_OBJ_C', 'Personnes par catégorie');
    define('LG_LPERS_OBJ_D', 'Personnes par ville de décès');
    define('LG_LPERS_OBJ_K', 'Personnes par ville de contrat de mariage');
    define('LG_LPERS_OBJ_M', 'Personnes par ville de mariage');
    define('LG_LPERS_OBJ_N', 'Personnes par ville de naissance');
    define('LG_LPERS_OBJ_P', 'Liste des personnes');
    define('LG_LPERS_PERS_FILE', 'Fiche personne');
    define('LG_LPERS_QUICK_ACCESS', 'Accès rapide');
    define('LG_LPERS_SELECT_NAME', 'Sélectionnez un nom...');
    define('LG_ORDER_BY_DATE', 'Tri par date');
    define('LG_ORDER_BY_MEN', 'Tri par homme');
    define('LG_ORDER_BY_WOMEN', 'Tri par femme');
    define('LG_LPERS_NOTARIES', 'Notaires');
}

// Spécifique Liste des personnes 2/2 ou fiche ville
// if (($nom_page == 'Liste_Pers2.php') or ($nom_page == 'Fiche_NomFam.php') or ($nom_page == 'Fiche_Ville.php')) {
if (in_array($nom_page, array(
    'Liste_Pers2.php',
    'Fiche_NomFam.php',
    'Fiche_Ville.php',
    'Edition_Ville.php'
))) {
    define('LG_LPERS_OBJ_P', 'Personnes s\'appelant');
    define('LG_LPERS_OBJ_PC', 'Personnes [avec leur conjoints] s\'appelant');
    define('LG_LPERS_OBJ_N', 'Personnes nées à');
    define('LG_LPERS_OBJ_D', 'Personnes décédées à');
    define('LG_LPERS_OBJ_M', 'Personnes mariées à');
    define('LG_LPERS_OBJ_K', 'Personnes dont le contrat de mariage est à');
    define('LG_LPERS_OBJ_C', 'Personnes appartenant à la catégorie');
    define('LG_LPERS_OBJ_E', 'Personnes avec un évènement à');
}

if (($nom_page == 'Edition_NomFam.php') or ($nom_page == 'Fiche_NomFam.php')) {
    define('LG_NAME', 'Nom de famille');
    define('LG_NAME_PHONETIC', 'Code phonétique');
    define('LG_NAME_SEARCH', 'Recherche du nom sur les sites hébergés Généamania');
    define('LG_NAME_REC', 'Fiche du nom');
    define('LG_NAME_PRONUNCIATION', 'Prononciation');
    define('LG_NAME_PRONUNCIATION_CALC', 'Prononciation calculée');
    define('LG_NAME_VOWELS', 'Voyelles');
    define('LG_NAME_CONSONANTS', 'Consonnes');
    define('LG_NAME_SAMPLE', 'Exemple');
    define('LG_NAME_SPACE', 'Espace');
    define('LG_NAME_BACKSPACE', 'Effacer');
}

// Spécifique Liste des personnes par génération
if (($nom_page == 'Liste_Pers_Gen.php') or ($nom_page == 'Arbre_Desc_Pers.php')) {
    $LG_LPersG_object = 'Liste par génération';
    $LG_LPersG_Implex_or_error = 'Implex ou erreur de numéro probable, le numéro calculé est entre parenthèses';
    $LG_LPersG_first = 'ère';
    $LG_LPersG_next = 'ème';
    $LG_LPersG_generation = 'génération';
    $LG_LPersG_missing = 'Il manque ';    // Attention, il faut l'espace à la fin
    $LG_LPersG_father_of = 'le père de';
    $LG_LPersG_mother_of = 'la mère de';
    $LG_LPersG_generation = 'Génération';
    $LG_LPersG_number = 'Numéro';
    $LG_LPersG_born_precision = 'Précision naissance';
    $LG_LPersG_born_calendar = 'Calendrier naissance';
    $LG_LPersG_born_where = 'Né(e) à';
    $LG_LPersG_dead_precision = 'Précision décès';
    $LG_LPersG_dead_calendar = 'Calendrier décès';
    $LG_LPersG_dead_where = 'Décédé(e) à';
    $LG_LPersG_limited_max_gen_1 = 'Affichage limité à ';
    $LG_LPersG_limited_max_gen_2 = ' générations, veuillez utiliser éventuellement la ';
    $LG_LPersG_limited_max_gen_3 = 'vue personnalisée';
    $LG_LPersG_limited_max_gen_4 = ' pour remonter au delà';
    $LG_LPersG_display_places = 'Afficher les lieux';
    $LG_LPersG_missing_pers = 'Afficher les personnes manquantes';
    $LG_LPersG_display_missing = 'Oui';
    $LG_LPersG_display_only_missing = 'Seulement';
    $LG_LPersG_hint_eye = 'Afficher / masquer toutes les générations';
}

if (($nom_page == 'Fiche_Lien.php') or ($nom_page == 'Edition_Lien.php')) {
    define('LG_LINK_AVAIL_HOME', 'Présent sur la page d\'accueil');
    define('LG_LINK_DESCRIPTION', 'Description');
    define('LG_LINK_NO_REFRESH', 'non rafraîchie');
    define('LG_LINK_OR_EXISTING_TYPE', 'Ou type existant');
    define('LG_LINK_SELECT_TYPE', 'Sélectionnez un type...');
    define('LG_LINK_THIS', 'ce lien');
    define('LG_LINK_TYPE', 'Type de lien');
    define('LG_LINK_URL', 'Adresse');
    define('LG_LINK_VISIBILITY', 'Visibilité internet');
    define('LG_LINK_KEEP_IMAGE', "Garder l'image");
}

if ($nom_page == 'cal.php') {
    define('LG_CAL_BEG', 'Date initiale');
    define('LG_CAL_CALCULATE', 'Calcul de date');
    define('LG_CAL_FILL_TIP', "Ne saisissez que des chiffres ; format attendu : jjmmaaaa (2 chiffres pour le jour, 2 pour le mois, 4 pour l'année)");
    define('LG_CAL_GREGORIAN', 'Grégorienne');
    define('LG_CAL_MONTH', 'Mois');
    define('LG_CAL_OFFSET', 'Décalage');
    define('LG_CAL_QUICK_FILL', "Saisie rapide d'une date grégorienne");
    define('LG_CAL_RESULT', 'Date calculée');
    define('LG_CAL_REVOLUTIONARY', 'Révolutionnaire');
    define('LG_CAL_TITLE', "Saisie d'une date");
    define('LG_CAL_TODAY', "Aujourd'hui");
    define('LG_CAL_WITH_SELECT', 'Saisie assistée');
    define('LG_CAL_YEAR', 'Année');
    define('LG_CAL_ERROR_DATE', 'Date incohérente');
    define('LG_CAL_ERROR_BOUNDARIES', 'hors limites');
}

if ($nom_page == 'Rectif_Utf8.php') {
    $LG_Rect_Utf_Msg_Beg = 'Fin de la rectification ; ';
    $LG_Rect_Utf_Msg_End = ' requêtes passées';
}

if ($nom_page == 'Edition_Lier_Eve.php') {
    $LG_Link_Ev_Link_Pers = 'Personne';
    $LG_Link_Ev_Link_Event_Type = 'Type d\'évènement';
    $LG_Link_Ev_Link_Event = 'Evènement';
    $LG_Link_Ev_Link_Last_Event = 'Dernier évènement pour le type';
    $LG_Link_Ev_Link_Role = 'Rôle';
    $LG_Link_Ev_Link_NoPlace = 'Pas de lieu';
    $LG_Link_Ev_Link_Place = 'Lieu de la participation';
    $LG_Link_Ev_Link_Beg_Part = 'Début de la participation';
    $LG_Link_Ev_Link_End_Part = 'Fin de la participation';
    $LG_Link_Ev_Link_Paste_Beg = 'Copier la date de début';
    $LG_Link_Ev_Link_Save_GeneGraphe = 'Participation prise en compte dans GénéGraphe';
    $LG_Link_Ev_Link = 'Lier un évènement avec';
    $LG_Link_Ev_Main_Pers = 'Personnage principal';
}

if ($nom_page == 'Edition_Lier_Nom.php') {
    $LG_Link_Name_Pers = 'Personne concernée';
    $LG_Link_Name_New = 'Nouveau nom';
    $LG_Link_Name_Upper = 'Mettre le nom en majuscules';
    $LG_Link_Name_Unknown = 'Nom inconnu';
    $LG_Link_Name_Delete = 'le lien avec ce nom';
    $LG_Link_Name_Not_Found = 'Personne non trouvée';
}

if ($nom_page == 'Edition_Lier_Pers.php') {
    $LG_Link_Pers_Pers = 'Personne';
    $LG_Link_Pers_Last_Pers = 'Dernière personne pour le nom';
    $LG_Link_Pers_Role = 'Rôle';
    $LG_Link_Pers_Beg = 'Début du lien';
    $LG_Link_Pers_End = 'Fin du lien';
    $LG_Link_Pers_Main = 'personnage principal';
    $LG_Link_Pers_No_Matter = 'indifférent';
    $LG_Link_Pers_With = 'Etablir un lien avec ';
    $LG_Link_Pers_Copy_Date = 'Copier la date de début';;
}

if (($nom_page == 'Fiche_Document.php') or ($nom_page == 'Edition_Document.php')
    or ($nom_page == 'Create_Multiple_Docs.php') or ($nom_page == 'Edition_Lier_Doc.php')
) {
    $LG_Docs_Doc = 'Document';
    $LG_Docs_Title = 'Titre';
    $LG_Docs_Nature = 'Nature';
    $LG_Docs_File = 'Fichier';
    $LG_Docs_Doc_Type = 'Type de document';
    $LG_Docs_Last_Doc = 'Dernier document pour le type';
    $LG_Docs_Default_Doc = 'Document par défaut';
    $LG_Docs_Doc_Show = 'Visualiser le document';
    $LG_Docs_Error_No_Type = ' Vous n\'avez pas cr&eacute;&eacute; de type de document';
}

if ($nom_page == 'Liste_Documents.php') {
    define('LG_DOC_LIST_TYPE', 'Type');
    define('LG_DOC_LIST_LAST', 'Dernier document saisi');
    define('LG_DOC_LIST_ADD_1', 'Ajouter : un document');
    define('LG_DOC_LIST_ADD_MANY', 'plusieurs documents');
    define('LG_DOC_LIST_ADD_MANY_TIP', 'Ajouter plusieurs documents');
    define('LG_DOC_LIST_DISPLAY', 'Voir le document');
}

if (($nom_page == 'Edition_Lier_Doc.php')
    or ($nom_page == 'Edition_Lier_Source.php')
) {
    $LG_Link_Doc_Role = 'Rôle';
    $LG_Link_Doc_Beg = 'Début du lien';
    $LG_Link_Doc_End = 'Fin du lien';
    $LG_Link_Doc_Main = 'personnage principal';
    $LG_Link_Doc_No_Matter = 'indifférent';
    $LG_Link_Doc_Fa_Not_Found = 'Père non trouvé';
    $LG_Link_Doc_Mo_Not_Found = 'Mère non trouvée';
    $LG_Link_Doc_Pers_Not_Found = 'Personne non trouvée';
    $LG_Link_Doc_Rel_Pers = 'Personne concernée';
    $LG_Link_Doc_Rel_Union = 'Union concernée';
    $LG_Link_Doc_Rel_Fil =    'Filiation concernée';
    $LG_Link_Doc_Rel_Event = 'Evènement concerné';
    $LG_Link_Doc_Rel_Town = 'Ville concernée';
    $LG_Link_Doc_No = 'Pas de document à lier...';
    $LG_Link_Doc_This = 'cette liaison';
    $LG_Link_Source = 'Source';
    $LG_Link_Source_Link = 'Lier avec une source';
    $LG_Link_Source_Last = 'Dernière source saisie';
    $LG_Link_Source_Not_Exist = 'Pas de source à lier';
    $LG_Link_Source_Title_Beg = 'Pas de source à lier';
    $LG_Link_Source_Title_End = 'Pas de source à lier';
}

if (in_array($nom_page, array('Fiche_Actualite.php', 'Fiche_Evenement.php', 'Edition_Evenement.php', 'Liste_Evenements.php'))) {
    $LG_Event_Title = 'Titre';
    $LG_Event_Type = 'Type d\'évènement';
    $LG_Event_Where = 'A eu lieu à';
    $LG_Event_No_Where = 'Pas de lieu';
    $LG_Event_When = 'Dates';
    $LG_Event_Event_Beg = 'Début';
    $LG_Event_Event_End = 'Fin';
    $LG_Event_Event_Copy_Date = 'Copier la date de début';
    $LG_Event_Event_This = 'cet évènement';
    $LG_Event_New_This = 'cette actualité';
}

if (($nom_page == 'Edition_Type_Evenement.php') or ($nom_page == 'Fiche_Type_Evenement.php')) {
    define('LG_EVENT_TYPE_CODE', 'Code');
    define('LG_EVENT_TYPE_LABEL', 'Libellé');
    define('LG_EVENT_TYPE_UNIQ', 'Unicité des évènements du type');
    define('LG_EVENT_TYPE_IS_MOD', 'Type modifiable');
    define('LG_EVENT_TYPE_GEDCOM', 'Type Gedcom');
    define('LG_EVENT_TYPE_THIS', "ce type d'évènement");
    define('LG_EVENT_TYPE_DELETED', "Type d'évènement supprimé");
    define('LG_EVENT_TYPE_EVENTS', ' Liste des évènements du type');
}

if ($nom_page == 'Liste_Evenements.php') {
    define('LG_EVENT_LIST_lAST', 'Dernier évènement saisi');
    define('LG_EVENT_LIST_TYPE', 'Type');
    define('LG_EVENT_LIST_BEG_PREC', 'Précision Début');
    define('LG_EVENT_LIST_BEG_CAL', 'Calendrier Début');
    define('LG_EVENT_LIST_END_PREC', 'Précision Fin');
    define('LG_EVENT_LIST_END_CAL', 'Calendrier Fin');
    define('LG_EVENT_LIST_TYPE_LABEL', 'Libellé type');
}

if ($nom_page == 'Import_CSV.php') {
    $LG_ICSV_Pers_Ville_Naissance = 'Né(e) à';
    $LG_ICSV_Pers_Ville_Deces = 'Décédé(e) à';
    $LG_ICSV_Pers_Numero = 'Numéro';
    $LG_ICSV_Pers_Surnom = 'Surnom';
}
if ($nom_page == 'Import_CSV_Liens.php') {
    $LG_ICSV_Link_Type = 'Type de lien';
    $LG_ICSV_Link_Desciption = 'Description';
    $LG_ICSV_Link_URL = 'Adresse';
}

if (($nom_page == 'Import_CSV_Evenements.php') or ($nom_page == 'appelle_chronologie_personne.php')) {
    $LG_ICSV_Event_Type = "Type d'évènement";
    $LG_ICSV_Event_Where = 'Lieu';
    $LG_ICSV_Event_Title = 'Titre';
    $LG_ICSV_Event_Beg = 'Début';
    $LG_ICSV_Event_End = 'Fin';
    $LG_ICSV_Country = 'sur le pays de naissance ou décès';
    $LG_ICSV_Part_Beg = 'Début participation';
    $LG_ICSV_Part_End = 'Début participation';
    $LG_ICSV_Event_Where_No = 'pas de lieu demandé';
    $LG_ICSV_Event_No_Birth = 'Date de naissance absente ou non utilisable';
    $LG_ICSV_Event_Ca_Dates = 'approximation sur les dates';
}

if (in_array($nom_page, array(
    'Import_CSV_Villes.php',
    'Fusion_Villes.php',
    'Fiche_Ville.php',
    'Edition_Ville.php'
))) {
    define('LG_ICSV_TOWN_ADD_DOCUMENT', 'Ajout d\'un document');
    define('LG_ICSV_TOWN_COL_CALC', 'Colonne du tableur');
    define('LG_ICSV_TOWN_COL_GEN_FIElD', 'Zone Généamania');
    define('LG_ICSV_TOWN_COL_MATCHING', 'Correspondances');;
    define('LG_ICSV_TOWN_COL_MISSING', 'Absente');;
    define('LG_ICSV_TOWN_COL_PRES_IGN', 'Présente à ignorer');
    define('LG_ICSV_TOWN_COL_PRES_TAKE', 'Présente à prendre en compte');
    define('LG_ICSV_TOWN_COL_SEL_FIELD', 'Sélectionnez une zone');
    define('LG_ICSV_TOWN_GEO_COORDS', 'Coordonnées géographiques');
    define('LG_ICSV_TOWN_LINK_DOCUMENT', 'Lier un document existant à la ville');
    define('LG_ICSV_TOWN_LINK_SOURCE', 'Lier une source à la ville');
    define('LG_SUBDIV_LIST', 'Liste des villes');
    define('LG_ICSV_TOWN_UNKNOWN', 'Ville inconnue');
    define('LG_ICSV_TOWN_NAME', 'Nom de la ville');
    define('LG_ICSV_TOWN_PERS_CREATE', 'Création de personnes ');
    define('LG_ICSV_TOWN_PERS_BORN_CREATE', LG_ICSV_TOWN_PERS_CREATE . 'nées à ');
    define('LG_ICSV_TOWN_PERS_DEAD_CREATE', LG_ICSV_TOWN_PERS_CREATE . 'décédées à ');
    define('LG_ICSV_TOWN_PERS', 'Personnes ');
    define('LG_ICSV_TOWN_PERS_BORN', LG_ICSV_TOWN_PERS . 'nées à ');
    define('LG_ICSV_TOWN_PERS_EVENT', LG_ICSV_TOWN_PERS . 'avec un évènement à ');
    define('LG_ICSV_TOWN_SEARCH', 'Recherche de la ville dans la base des villes Généamania (coordonnées géographiques, code postal)');
    define('LG_ICSV_TOWN_SEARCH_CLOUD', 'Recherche de cette ville sur les sites hébergés');
    define('LG_ICSV_TOWN_SELECT_DEPARTEMENT', 'Sélectionnez un département');
    define('LG_ICSV_TOWN_SUBDIV', 'Liste des lieux-dits');
    define('LG_ICSV_TOWN_TIP', 'Appelez la recherche de la ville dans la base des villes Généamania pour trouver le code postal, la latitude et longitude afin de situer la ville sur une carte');
    define('LG_ICSV_TOWN_THIS', 'cette ville');
    define('LG_ICSV_TOWN_TIP_EDIT', 'Les coordonnées enregistrées permettent de situer la ville sur les cartes libres');
    define('LG_ICSV_TOWN_USED_ERR', 'suppression impossible en raison d\'utilisations');
    define('LG_ICSV_TOWN_ZIP_CODE', 'Code postal');
    define('LG_ICSV_TOWN_ZIP_LATITUDE', 'Latitude');
    define('LG_ICSV_TOWN_ZIP_LONGITUDE', 'Longitude');
    define('LG_CH_FUSIONNER', 'Fusionner');
    define('LG_CH_FUSION_TOWN1', 'Ville 1');
    define('LG_CH_FUSION_TOWN2', 'Ville 2');
    define('LG_CH_FUSION_TARGET', 'Ville cible de la fusion');
}

if ($nom_page == 'Liste_Villes.php') {
    define('LG_LAREAS_SUBDIVS', 'Liste des subdivisions');
    define('LG_LAREAS_TOWNS', 'Liste des villes');
    define('LG_LAREAS_COUNTIES', 'Liste des départements');
    define('LG_LAREAS_REGIONS', 'Liste des régions');
    define('LG_LAREAS_COUNTRIES', 'Liste des pays');
    define('LG_LAREAS_SUBDIV_LAST', 'Dernière subdivision saisie');
    define('LG_LAREAS_SUBDIV_ADD', 'Ajouter une subdivision');
    define('LG_LAREAS_TOWN_LAST', 'Dernière ville saisie');
    define('LG_LAREAS_TOWN_ADD', 'Ajouter une ville');
    define('LG_LAREAS_COUNTY_LAST', 'Dernier département saisi');
    define('LG_LAREAS_COUNTY_ADD', 'Ajouter un département');
    define('LG_LAREAS_REGION_LAST', 'Dernière région saisie');
    define('LG_LAREAS_REGION_ADD', 'Ajouter une région');
    define('LG_LAREAS_TOP', 'Haut de page');
}

if (in_array($nom_page, array(
    'Edition_Subdivision.php',
    'Fiche_Subdivision.php'
))) {
    define('LG_SUBDIV_ADD_DOCUMENT', 'Ajout d\'un document');
    define('LG_SUBDIV_GEO_COORDS', 'Coordonnées géographiques');
    define('LG_SUBDIV_LINK_DOCUMENT', 'Lier un document existant à la subdivision');
    define('LG_SUBDIV_LINK_SOURCE', 'Lier une source à la subdivision');
    define('LG_SUBDIV_LIST', 'Liste des subdivisions');
    define('LG_SUBDIV_NAME', 'Nom de la subdivision');
    define('LG_SUBDIV_PERS', 'Liste des personnes ');
    define('LG_SUBDIV_SEARCH_CLOUD', 'Recherche de cette ville sur les sites hébergés');
    define('LG_SUBDIV_SELECT_TOWN', 'Sélectionnez une ville');
    define('LG_SUBDIV_THIS', 'cette subdivision');
    define('LG_SUBDIV_TOWN', 'Ville');
    define('LG_SUBDIV_USED_ERR', 'suppression impossible en raison d\'utilisations');
    define('LG_SUBDIV_ZIP_LATITUDE', 'Latitude');
    define('LG_SUBDIV_ZIP_LONGITUDE', 'Longitude');
}

if (($nom_page == 'Edition_Role.php') or ($nom_page == 'Liste_Pers_Role.php') or ($nom_page == 'Fiche_Role.php')) {
    define('LG_ROLE_CODE', 'Code');
    define('LG_ROLE_ERROR_EXISTS', 'Attention, code déjà utilisé (codes présents');
    define('LG_ROLE_LABEL', 'Libellé');
    define('LG_ROLE_OPPOS_LABEL', 'Libellé inverse');
    define('LG_ROLE_SYM', 'Symétrie');
    define('LG_ROLE_THIS', 'ce rôle');
    define('LG_ROLE_PERSONS', 'Personnes ayant ce rôle');
}

if (($nom_page == 'Edition_Type_Document.php') or ($nom_page == 'Fiche_Type_Document.php')) {
    define('LG_DOC_TYPE_LABEL', 'Libellé');
    define('LG_DOC_TYPE_THIS', 'ce type de document');
    define('LG_DOC_DOCS', 'Documents du type');
}

// Factoriser les variables sur les personnes

if (in_array($nom_page, array(
    'Liste_Pers_Gen.php',
    'Import_CSV.php',
    'Ajout_Enfants.php',
    'Noyau_Pers.php',
    'Completude_Nom.php',
    'Edition_Personne.php',
    'Ajout_Rapide.php',
    'index.php',
    'Edition_Personnes_Ville.php',
    'Fiche_Homonymes.php',
    'Ajout_Contribution.php',
    'Recherche_Personne.php',
    'Recherche_Personne_CP.php'
))) {
    define('LG_PERS_ADD_NAME', 'Ajout d\'un nom');
    define('LG_PERS_ALT_NAMES', 'Noms secondaires');
    define('LG_PERS_ALT_NAME_ADD', 'Ajouter un nom secondaire à la personne');
    define('LG_PERS_BAPM', 'Baptisé(e) le');
    define('LG_PERS_BAPM_EVENT', 'Baptême');
    define('LG_PERS_BORN', 'Né(e) le');
    define('LG_PERS_BORN_AT', 'Né(e) à');
    define('LG_PERS_BORN_IN', 'Création de personnes nées à');
    define('LG_PERS_CALC_SOSA', 'Calcul du numéro sosa saisi (e.g. =P2)');
    define('LG_PERS_CATEGORY', 'Catégorie');
    define('LG_PERS_CHILDREN_ADD', "Ajout rapide d'enfants pour le couple");
    define('LG_PERS_CHILDREN_PRESENT', 'Enfants déjà saisis');
    define('LG_PERS_COMPLETE_GREEN', 'information présente');
    define('LG_PERS_COMPLETE_ORANGE', 'information partielle');
    define('LG_PERS_COMPLETE_RED', 'information absente');
    define('LG_PERS_CONTROL', 'Contrôle de la personne');
    define('LG_PERS_COPY_NAME', 'Reprend le nom saisi précédemment');
    define('LG_PERS_CREATE_PARENTS', 'Créer la filiation');
    define('LG_PERS_DATA', 'Etat-civil');
    define('LG_PERS_DATE_PATTERN', 'Modèle de date de');
    define('LG_PERS_DEAD', 'Décédé(e) le');
    define('LG_PERS_DEAD_AT', 'Décédé(e) à');
    define('LG_PERS_DEAD_IN', 'Création de personnes décédées à');
    define('LG_PERS_DECUJUS', 'Positionnement de cujus Sosa (numéro 1)');
    define('LG_PERS_DEFAULT_NAME', 'Nom par défaut');
    define('LG_PERS_DOCS', 'Documents');
    define('LG_PERS_DOC_LINK', "Ajout d'un document");
    define('LG_PERS_DOC_LINK_EXISTS', 'Lier un document existant à la personne');
    define('LG_PERS_DOC_LINK_NEW', 'Lier un document à la personne en créant la fiche document');
    define('LG_PERS_EVENTS', 'Evènements');
    define('LG_PERS_FIRST_NAME', 'Prénoms');
    define('LG_PERS_INTERNET', 'Internet');
    define('LG_PERS_IS_COUPLE', 'Union');;
    define('LG_PERS_LINKS', 'Liens');
    define('LG_PERS_LINK_SOURCE', 'Lier une source à la personne');
    define('LG_PERS_NAME', 'Nom');
    define('LG_PERS_NAME_TO_UPCASE', 'Met le nom en majuscules');
    define('LG_PERS_NAME_UPPER', 'Mettre le nom en majuscules');
    define('LG_PERS_NO_CATEGORY', 'Aucune catégorie');
    define('LG_PERS_NUMBER', 'Numéro');
    define('LG_PERS_PARENTS', 'Filiation');
    define('LG_PERS_PARENTS_UNIONS', 'Filiation-unions');
    define('LG_PERS_PARTNERS', 'conjoint(s)');
    define('LG_PERS_PERS', 'Personne');
    define('LG_PERS_PERSONS', 'Personnes');
    define('LG_PERS_QUICK_ADD', 'Ajout rapide pour');
    define('LG_PERS_RANK', 'rang');
    define('LG_PERS_REF', 'Référence');
    define('LG_PERS_SAME_BIRTH_TOWN', 'Reprend la ville de naissance saisie précédemment');
    define('LG_PERS_SAME_DEATH_TOWN', 'Reprend la ville de décès saisie précédemment');
    define('LG_PERS_SAME_NAME', 'Reprend le nom saisi précédemment');
    define('LG_PERS_SEX_UNDEF', 'Sexe indéterminé');
    define('LG_PERS_THIS', 'cette personne (les liens vers des documents, des images, des évènements, des participations à des évènements et des personnes seront également supprimés)');
    define('LG_PERS_TIP_QUICK1', "Vous pouvez aussi utiliser l'ajout rapide");
    define('LG_PERS_TIP_QUICK2', 'à droite pour créer les parents, les conjoints, les frères et soeurs (les liens nécessaires sont automatiquement créés).');
    define('LG_PERS_UNION', 'Unis');
    define('LG_PERS_UNION_UNISEX', 'Union unisexe');
    define('LG_PERS_UNION_MULTISEX', 'Union classique');
    define('LG_PERS_UNIONS', 'Unions');
    define('LG_PERS_UNION_AT', 'Union à');
    define('LG_PERS_UNION_DATE', 'Union le');
    define('LG_PERS_UPDATE_PARENTS', 'Modifier la filiation');
    define('LG_PERS_UPDATE_UNION', "Modification de l'union");
    define('LG_PERS_VISIBILITY', 'Visibilité');
    define('LG_PERS_AUTO_CALC_SOSA', 'Calcul automatique du numéro sosa');
}

if ($nom_page == 'Ajout_Contribution.php') {
    define('LG_CONTRIBS_COPY_REF_NAME', 'Copier le nom de la personne initiale');
    define('LG_CONTRIBS_CHILDREN', 'Enfants');
    define('LG_CONTRIBS_CHILD_1', 'Enfant 1');
    define('LG_CONTRIBS_CHILD_2', 'Enfant 2');
    define('LG_CONTRIBS_UNLOCK', 'Déverrouillage');
    define('LG_CONTRIBS_UNLOCK_TIP1', "Entrez le résultat de l'opération de fin de ligne ");
    define('LG_CONTRIBS_UNLOCK_TIP2', "puis changez de zone ou d'onglet");
    define('LG_CONTRIBS_TRIBUTE', 'Script de captcha goupillé par Patrik');
    define('LG_CONTRIBS_EMAIL', 'Votre email');
    define('LG_CONTRIBS_MESSAGE', 'Message');
    define('LG_CONTRIBS_IP_RECORD', "Votre adresse IP fait l'objet d'un enregistrement et pourra être utilisée au besoin.");
    define('LG_CONTRIBS_CTRL_KO', 'Echec à la vérification du contrôle');
    define('LG_CONTRIBS_SEND_KO', "Echec sur l'envoi de la contribution");
    define('LG_CONTRIBS_FILE_KO', "Le fichier de contribution n'a pu être créé");
    define('LG_CONTRIBS_EMPTY', 'Contribution vide');
}

if (($nom_page == 'Recherche_Personne.php') or ($nom_page == 'Recherche_Personne_CP.php')) {
    define('LG_PERS_REQ', 'Requête');
    define('LG_PERS_REQ_ALIVE', 'Vivant');
    define('LG_PERS_REQ_BORN_IN', 'naissance en ');
    define('LG_PERS_REQ_BORN_TOWN', 'Ville de naissance');
    define('LG_PERS_REQ_DEATH_IN', 'décès en ');
    define('LG_PERS_REQ_DEATH_TOWN', 'Ville de décès');
    define('LG_PERS_REQ_ERROR', 'Requête en erreur');
    define('LG_PERS_REQ_FIELDS', 'Critères demandés');
    define('LG_PERS_REQ_FILE_STATUS', 'Statut de la fiche');
    define('LG_PERS_REQ_FIND_NAME', 'Recherche du nom sur les sites gratuits');
    define('LG_PERS_REQ_MORE_LESS_1', '(+ ou -');
    define('LG_PERS_REQ_MORE_LESS_2', 'an');
    define('LG_PERS_REQ_NEW_TAB', 'Nouvel onglet pour les fiches');
    define('LG_PERS_REQ_NOT_FILLED', 'Non saisie');
    define('LG_PERS_REQ_OFF_DOWN', 'Augmenter la tolérance en années');
    define('LG_PERS_REQ_OFF_UP', 'Diminuer la tolérance en années');
    define('LG_PERS_REQ_OFF_YEARS', 'ans pour les naissances ou les décès');
    define('LG_PERS_REQ_PERS_FOUND_1', 'personne');
    define('LG_PERS_REQ_PERS_FOUND_2', 'trouvée');
    define('LG_PERS_REQ_REQUEST_CHOOSE', "Choisissez une requête OU des critères dans l'autre pavé");
    define('LG_PERS_REQ_REQUEST_SAVE', 'Enregister la requête');
    define('LG_PERS_REQ_REQUEST_TITLE', 'sous le titre');
    define('LG_PERS_REQ_REQUEST_USE', 'Utiliser une requête mémorisée');
    define('LG_PERS_REQ_SAVE', 'Enregistrement de la requête sous le titre');
    define('LG_PERS_REQ_SORT', 'Tri');
    define('LG_PERS_REQ_SORT_BORN', 'date de naissance');
    define('LG_PERS_REQ_SORT_DEATH', 'date de décès');
    define('LG_PERS_REQ_SORT_NS', 'nom / prénom');
    define('LG_PERS_REQ_SOUND_EXACT', 'prononciation exacte');
    define('LG_PERS_REQ_SOUND_NEAR', 'prononciation approchante');
    define('LG_PERS_REQ_SPELL_EXACT', 'orthographe exacte');
    define('LG_PERS_REQ_YEAR', "Pour l'année");
    define('LG_PERS_SCH_TYPE', "Type de recherche");
    define('LG_PERS_SCH_TYPE_PARENT', "Parent");
    define('LG_PERS_SCH_TYPE_PARTNER', "Conjoint");
    define('LG_PERS_SCH_TIP', "Les informations saisies concernent le conjoint ou parent dont on veut lister les personnes");
}

if ($nom_page == 'sel_zone_geo.php') {
    define('LG_CHOOSE_AREA_COUNTRY', 'Choix d\'un pays');
    define('LG_CHOOSE_AREA_REGION', 'Choix d\'une région');
    define('LG_CHOOSE_AREA_COUNTY', 'Choix d\'un département');
    define('LG_CHOOSE_AREA_TOWN', 'Choix d\'une ville');
    define('LG_CHOOSE_AREA_SUBDIVISION', 'Choix d\'une subdivision');
}

if ($nom_page == 'Liste_Referentiel.php') {
    define('LG_REF_LIST_ROLES', 'Liste des rôles');
    define('LG_REF_LIST_EV_TYPES', 'Liste des types d\'évènements');
    define('LG_REF_LIST_DOC_TYPES', 'Liste des types de documents');
    define('LG_REF_LIST_CATEG', 'Liste des catégories');
    define('LG_REF_LIST_REQ', 'Liste des requêtes sur les personnes');
    define('LG_REF_LIST_REPO_SOURCES', 'Liste des dépôts de sources');
}

if ($nom_page == 'Edition_Rangs.php') {
    $LG_Rank_Born = 'Né(e)';
    $LG_Rank_Dead = 'Décédé(e)';
    $LG_Rank_Calc_Duration = 'Ecart calculé';
    $LG_Rank_Calculated = 'Rang calculé';
    $LG_Rank_Filled = 'Rang saisi';
    $LG_Rank_Error = 'Problème sur le rang';
    $LG_Rank_Accept = 'Accepter les rangs calculés';
    $LG_Rank_First_Children = 'Premier enfant né au bout de ';
    $LG_Rank_Last_Children = 'Dernier enfant né au bout de ';
    $LG_Rank_End_Union = ' d\'union';
    $LG_Rank_Short_Duration = 'Enfant né moins de 9 mois après le précédent';
    $LG_Rank_Parents_Union = 'Union des parents';
}

if (($nom_page == 'Fiche_Source.php') or ($nom_page == 'Edition_Source.php') or ($nom_page == 'Liste_Sources.php')) {
    define('LG_SRC_TITLE', 'Titre');
    define('LG_SRC_AUTHOR', 'Auteur');
    define('LG_SRC_CLASS', 'Classement');
    define('LG_SRC_REPO', 'Dépôt');
    define('LG_SRC_REFER', 'Cote');
    define('LG_SRC_WEB', 'Adresse Internet');
    define('LG_SRC_TRUST', 'Fiabilité');
    define('LG_SRC_TRUST_H', 'Haute');
    define('LG_SRC_TRUST_M', 'Moyenne');
    define('LG_SRC_TRUST_L', 'Faible');
    define('LG_SRC_THIS', 'cette source');
    define('LG_SRC_LAST', 'Dernière source saisie');
    define('LG_SRC_ADD', 'Ajouter une source');
}

if ($nom_page == 'Stat_Base_Generations.php') {
    define('LG_STAT_GEN_DEC', 'Diminuer la génération');
    define('LG_STAT_GEN_FIRST_GEN', 'Génération de début');
    define('LG_STAT_GEN_GEN', 'Génération');
    define('LG_STAT_GEN_INC', 'Augmenter la génération');
    define('LG_STAT_GEN_LAST_GEN', 'Génération de fin');
    define('LG_STAT_GEN_MISSING', 'manquante');
    define('LG_STAT_GEN_ON', 'sur');
    define('LG_STAT_GEN_PERSONS', 'personne');
    define('LG_STAT_GEN_POSSIBLE', 'possible');
    define('LG_STAT_GEN_RESULT', 'Résultat');
    define('LG_STAT_GEN_RESULT_RELATED', 'Par rapport à la génération précédente');
}

if ($nom_page == 'Stat_Base_Depart.php') {
    define('LG_STAT_COUNTY_WITH', 'Avec le département renseigné');
    define('LG_STAT_COUNTY_MAP', 'Carte de France');
}

if ($nom_page == 'Stat_Base_Villes.php') {
    define('LG_STAT_TOWN_COUNTY', 'Statistiques par ville pour le département');
    define('LG_STAT_TOWN_FILLED', 'Avec la ville renseignée');
}

if (($nom_page == 'Stat_Base.php') or ($nom_page == 'Histo_Prenoms.php')) {
    define('LG_STAT_ALL_BY_AGE', 'Statistiques par âge');
    define('LG_STAT_ALL_BY_PLACE', 'Statistiques géographiques');
    define('LG_STAT_ALL_CHILDREN', 'Filiations');
    define('LG_STAT_ALL_EVENTS', 'Evènements');
    define('LG_STAT_ALL_FLAG_GREEN_ALT', 'drapeau vert');
    define('LG_STAT_ALL_FLAG_GREEN_TITLE', 'Respect de la charte des sites gratuits');
    define('LG_STAT_ALL_FLAG_ORANGE_ALT', 'drapeau orange');
    define('LG_STAT_ALL_FLAG_ORANGE_TITLE', 'Risque de blocage pour non respect de la charte des sites gratuits');
    define('LG_STAT_ALL_NAMES', 'Patronymes');
    define('LG_STAT_ALL_OCC', 'Fréquences');
    define('LG_STAT_ALL_PERSONS', 'Personnes');
    define('LG_STAT_ALL_SOSA', 'Sosa directs');
    define('LG_STAT_ALL_TOWNS', 'Villes');
    define('LG_STAT_ALL_UNIONS', 'Unions');
    define('LG_STAT_ALL_VISIBLE', 'visible');
    define('LG_STAT_ALL_VISIBLE_WITH', 'dont');
    define('LG_STAT_SURNAMES', 'Prénoms au cours du temps');
    define('LG_STAT_SURNAMES_MEN', 'Hommes');
    define('LG_STAT_SURNAMES_WOMEN', 'Femmes');
    define('LG_STAT_SURNAMES_SORT_FREQ', 'Tri par fréquence');
    define('LG_STAT_SURNAMES_ASC', 'Ascendante');
    define('LG_STAT_SURNAMES_DESC', 'Descendante');
    define('LG_STAT_SURNAMES_FIRST', 'Premier prénom');
    define('LG_STAT_SURNAMES_ALL', 'Tous les prénoms');
}

if ($nom_page == 'Liste_Nom_Evenement.php') {
    define('LG_NAMES_FOR_EVENT_PERS_COUNT', 'Nombre de personnes');
    define('LG_NAMES_FOR_EVENT_EVENT', 'Evènement');
}

if ($nom_page == 'Liste_Noms_Non_Ut.php') {
    $LG_Names_NU_Del = 'suppression';
    $LG_Names_NU_Req = 'demandée';
}

if (($nom_page == 'Naissances_Mariages_Deces_Mois.php') or ($nom_page == 'Anniversaires.php')) {
    $LG_birth_many = 'Naissances';
    $LG_death_many = 'Décès';
    $LG_wedding_many = 'Mariages';
    $LG_conception = 'Conception théorique';
    $LG_conception_many = 'Conceptions';
    $LG_Ignore = 'Ignorer les personnes décédées pour les naissances ou mariages (pivot';
    $LG_Choose_Month = 'Choisissez un mois';
    $LG_Day_Birth = 'Anniversaire de naissance';
    $LG_Day_Death = 'Anniversaire de décès';
}

if ($nom_page == 'Enfants_Femme_Histo.php') {
    define('LG_CH_PER_MOTHER_BORN', 'Année de naissance de la mère');
    define('LG_CH_PER_MOTHER_AVG', 'Nombre moyen d\'enfants');
    define('LG_CH_PER_MOTHER_MAX_WOMAN', 'Femme ayant eu le plus grand nombre d\'enfants');
    define('LG_CH_PER_MOTHER_SHE_HAD', 'elle a eu');
}

if (in_array($nom_page, array(
    'Pyramide_Ages_Mar_Histo.php',
    'Histo_Ages_Mariage.php',
    'Pyramide_Ages_Histo.php',
    'Histo_Ages_Deces.php',
    'Pyramide_Ages.php'
))) {
    define('LG_CH_HISTO_AGE', 'Age');
    define('LG_CH_HISTO_AGE_ALL', 'Ensemble');
    define('LG_CH_HISTO_AGE_MEN', 'Hommes');
    define('LG_CH_HISTO_AGE_OLDEST_M', 'Doyen au décès');
    define('LG_CH_HISTO_AGE_OLDEST_W', 'Doyenne au décès');
    define('LG_CH_HISTO_AGE_PERS', 'personne(s)');
    define('LG_CH_HISTO_AGE_WED', 'Année de naissance');
    define('LG_CH_HISTO_AGE_WOMEN', 'Femmes');
    define('LG_CH_HISTO_AGE_YOUNGEST_FATH', 'Cadet au premier enfant');
    define('LG_CH_HISTO_AGE_YOUNGEST_M', 'Cadet au mariage');
    define('LG_CH_HISTO_AGE_YOUNGEST_MOTH', 'Cadette au premier enfant');
    define('LG_CH_HISTO_AGE_YOUNGEST_W', 'Cadette au mariage');
    define('LG_CH_HISTO_AVERAGE_AGE', 'Age moyen');
    define('LG_CH_HISTO_DEATH_TITLE', 'Age de décès pour la période ');
    define('LG_CH_HISTO_REPARTITION', 'Répartition des âges');
    define('LG_CH_HISTO_YEARS', 'ans');
    define('LG_CH_HISTO_AGE_CTL_CHILD', 'Contrôle des personnes recommandé car au moins un enfant est né avant un de ses parents !');
    define('LG_CH_HISTO_AGE_CTL_UNION', "Contrôle des personnes recommandé car au moins une union est antérieure à la date de naissance d'un conjoint !");
    define('LG_CH_HISTO_AGE_CTL_BIRTH', 'Contrôle des personnes recommandé car au moins une personne est décédée avant sa naissance !');
}

if ($nom_page == 'Liste_Connexions.php') {
    define('LG_CH_CONN_LIST_USER', 'Utilisateur');
    define('LG_CH_CONN_LIST_DATE', 'Date de connexion');
    define('LG_CH_CONN_LIST_IP', 'Adresse IP');
}

if ($nom_page == 'Recherche_Cousinage.php') {
    define('LG_CH_RELATED_2PERS', 'Vous devez saisir 2 personnes ...');
    define('LG_CH_RELATED_AND', 'Et');
    define('LG_CH_RELATED_BETWEEN', 'Entre');
    define('LG_CH_RELATED_CANON_LAW', 'Parenté en droit canon');
    define('LG_CH_RELATED_CIVIL_LAW', 'Parenté en droit civil');
    define('LG_CH_RELATED_DEGREE', 'degré');
    define('LG_CH_RELATED_GENERATIONS', 'générations.');
    define('LG_CH_RELATED_NO_COMMON', "Pas d'ancêtre commun trouvé sur ");
    define('LG_CH_RELATED_ON', 'au');
    define('LG_CH_RELATED_PERS_DIFF', 'Vous devez saisir 2 personnes différentes...');
    define('LG_CH_RELATED_SAME', 'idem ancêtre...');
    define('LG_CH_RELATED_SAVE', 'Sauver la demande pour GénéGraphe');
    define('LG_CH_RELATED_THEN', 'puis');
    define('LG_CH_RELATED_TIP_BEG', 'recherche limitée à ');
    define('LG_CH_RELATED_TIP_END', 'générations au maximum');
}

if (in_array($nom_page, array(
    'Edition_Utilisateur.php',
    'Fiche_Utilisateur.php',
    'Liste_Utilisateurs.php'
))) {
    define('LG_UTIL_CODE', 'Code');
    define('LG_UTIL_CONNEXIONS', 'Liste des connexions de la personne');
    define('LG_UTIL_EMAIL', 'Adresse mail');
    define('LG_UTIL_ERROR_EXISTS', 'Il existe déjà un utilisateur avec le même code utilisateur');
    define('LG_UTIL_ERROR_JS', 'Il semble que Javascript soit désactivé, veuillez l\'activer');
    define('LG_UTIL_LAST_CNX', 'Dernière connexion');
    define('LG_UTIL_MAIL_1', 'Un utilisateur a été créé pour vous sous le nom ');
    define('LG_UTIL_MAIL_2', ', votre code est ');
    define('LG_UTIL_MAIL_3', ' et le mot de passe est ');
    define('LG_UTIL_MAIL_OBJ', 'Création de votre utilisateur Geneamania');
    define('LG_UTIL_NAME', 'Nom');
    define('LG_UTIL_NO_CNX', 'aucune connexion');
    define('LG_UTIL_PROFILE', 'Niveau d\'autorisation');
    define('LG_UTIL_PSW', 'Mot de passe');
    define('LG_UTIL_PSW_CONFIRM', 'Confirmez le mot de passe');
    define('LG_UTIL_PSW_COPY', 'Copier le mot de passe généré');
    define('LG_UTIL_PSW_GENER', 'Générer un mot de passe');
    define('LG_UTIL_SEND_MAIL', 'Envoyer un mail suite à la création');
    define('LG_UTIL_THIS', 'cet utilisateur');
    define('LG_UTIL_WARN', 'vous êtes sur la fiche de l\'utilisateur sous lequel vous êtes connecté(e)');
    define('LG_USERS_LIST_ADD', 'Ajouter un utilisateur');
    define('LG_UTIL_CHK_MAIL', 'Si vous voulez envoyer un mail, vous devez sélectionner les utilisateurs concernés');
}

if (($nom_page == 'Edition_Utilisateur.php') or ($nom_page == 'index.php')) {
    define('LG_USER_MANDATORY', 'Pensez à remplir les champs obligatoires...');
    define('LG_USER_DIFF_PSW', 'Les deux mots de passe ne sont pas identiques');
    define('LG_USER_SHORT_PSW', 'Le mot de passe doit faire au moins 6 caractères');
    define('LG_USER_PSW_REQUESTED', 'Il faut saisir un mot de passe');
}

if ($nom_page == 'Import_Gedcom.php') {
    $LG_Ch_UTF8 = 'Fichier au format UTF-8';
    $LG_Ch_Encoding = 'Encodage des caractères';
    define('LG_IMP_GED_DEFAULT_STATUS', 'Valeur par défaut du statut des fiches créées');
    define('LG_IMP_GED_DEFAULT_VISIBILITY', 'Visibilité internet autorisée par défaut');
    define('LG_IMP_GED_DEFAULT_VISIBILITY_COMMENTS', 'Visibilité internet des notes autorisée par défaut');
    define('LG_IMP_GED_ERR_TYPE', "Le fichier sélectionné n'a pas la bonne extension");
    define('LG_IMP_GED_FILE', 'Fichier GEDCOM à télécharger');
    define('LG_IMP_GED_IMAGE_DEFAULT_VISIBILITY', 'Visibilité internet des images autorisée par défaut');
    define('LG_IMP_GED_IMPORT_DATES', 'Reprise des dates de modification du fichier');
    define('LG_IMP_GED_INSERT', 'Charger les données dans la base');
    define('LG_IMP_GED_PLACES', 'Format des lieux');
    define('LG_IMP_GED_REMIND_EVT', 'Vous pouvez aussi fusionner les évènements semblables ; pour ce faire, appelez la fonction de ');
    define('LG_IMP_GED_REMIND_INTERNET', 'Pensez enfin à mettre à jour la visibilité Internet des fiches des contemporains  ; pour ce faire, appelez la fonction ');
    define('LG_IMP_GED_REMIND_SOSA_1', 'Pensez à attribuer le numéro 1 à la personne de votre choix ; pour ce faire, passez par la ');
    define('LG_IMP_GED_REMIND_SOSA_2', 'Liste par noms');
    define('LG_IMP_GED_REMIND_SOSA_3', 'Vous pourrez ensuite mettre à jour en masse les numéros Sosa par ');
    define('LG_IMP_GED_RESET', 'Vidage préalable de la base actuelle');
    define('LG_IMP_GED_DEL_1_M', 'supprimé');
    define('LG_IMP_GED_DEL_1_F', 'supprimée');
    define('LG_IMP_GED_DEL_MANY_M', 'supprimés');
    define('LG_IMP_GED_DEL_MANY_F', 'supprimées');
    define('LG_IMP_GED_RESUME', 'Résumé du traitement');
}

if ($nom_page == 'Liste_Docs_Branche.php') {
    define('LG_DOC_BRANCH_ORIGINE', 'Personne origine de la branche');
    define('LG_DOC_BRANCH_DOC_TYPE', 'Type de document image');
    define('LG_DOC_BRANCH_SEL_TYPE', 'Type...');
    define('LG_DOC_BRANCH_SEL_NAME', 'Sélectionnez un nom...');
}

if (($nom_page == 'Edition_Image.php') or ($nom_page == 'Liste_Images.php')) {
    $LG_Ch_Image_Script_Title = 'Liaison d\'une image';
    $LG_Ch_Image_Title = 'Titre';
    $LG_Ch_Image = 'Image';
    $LG_Ch_Image_Name = 'Nom';
    $LG_Ch_Image_No_Need = "inutile de re-sélectionner un fichier si l'image est affichée à droite";
    $LG_Ch_Image_Default = 'Image par défaut';
    $LG_Ch_Image_Visibility = "Visibilité Internet de l'image";
    $LG_Ch_Image_This = 'cette image';
}

if ($nom_page == 'Fiche_Homonymes.php') {
    define('LG_CH_FUSIONNER', 'Fusionner');
    define('LG_CH_FUSION_TIP1', 'La fusion, si elle est demandée, est effectuée sur la personne 1 (à gauche).');
    define('LG_CH_FUSION_TIP2', 'L\'utilisateur sélectionne à gauche ou à droite les données à copier / ajouter.');
    define('LG_CH_FUSION_TIP3', 'Les conjoints et enfants sont ajoutés à ceux existants.');
    define('LG_CH_FUSION_TIP4', 'Les données non présentes à l\'écran ne sont pas copiées / ajoutées.');
    define('LG_CH_FUSION_PERS1', 'Personne 1');
    define('LG_CH_FUSION_PERS2', 'Personne 2');
    define('LG_CH_MARIED', 'mariés');
    define('LG_CH_BORN', 'né');
    define('LG_CH_DEAD', 'décédé');
    define('LG_CH_COUPLE', 'Fiche couple');
}

if ($nom_page == 'Recherche_Personne_Archive.php') {
    $LG_Ch_Search_Town = 'Ville de recherche';
    $LG_Ch_Search_Beg = 'Année de début';
    $LG_Ch_Search_End = 'Année de fin';
    $LG_Ch_Search_Copy_Date = "Reprise de l'année de début";
    $LG_Ch_Search_Consider = 'Données à prendre en compte';
    $LG_Ch_Search_Birth = 'naissances';
    $LG_Ch_Search_Wed = 'mariages';
    $LG_Ch_Search_Death = 'décès';
    $LG_Ch_Search_Consider_Valid = 'Fiches à prendre en compte';
    $LG_Ch_Search_Valid = 'validées';
    $LG_Ch_Search_Non_Valid = 'non validées';
    $LG_Ch_Search_Internet = 'source internet';
    $LG_Ch_Search_Sort = 'Tri';
    $LG_Ch_Search_Sort_Date = 'par date';
    $LG_Ch_Search_Sort_Pers = 'par personne';
    $LG_Ch_Search_Suffix = 'ajouter le nom de la ville en suffixe';
    $LG_Ch_Search_Pers_1 = 'personne';
    $LG_Ch_Search_Pers_2 = 'trouvée';
}

if ($nom_page == 'Recherche_Commentaire.php') {
    define('LG_SCH_COMMENT_RESTRICTION', 'Restriction aux fiches de type');
    define('LG_SCH_COMMENT_NO_RESTRICTION', '-- Pas de restriction --');
    define('LG_SCH_COMMENT', 'Recherche des commentaires contenant');
    define('LG_SCH_COMMENT_CONTAINING', 'Commentaire contenant');
    define('LG_SCH_COMMENT_FOUND_1', 'commentaire(s)');
    define('LG_SCH_COMMENT_FOUND_2', 'trouvé(s)');
    define('LG_SCH_COMMENT_ON', 'Sur ');
    define('LG_SCH_COMMENT_OF', ' de ');
    define('LG_SCH_COMMENT_AND', ' et ');
}

if (($nom_page == 'Edition_Categorie.php') or ($nom_page == 'Fiche_Categorie.php')) {
    $LG_Ch_Categ_Title = 'Titre';
    $LG_Ch_Categ_Order = 'Ordre de tri';
    $LG_Ch_Categ_Inc_Order = 'Augmenter l\'ordre';
    $LG_Ch_Categ_Dec_Order = 'Diminuer l\'ordre';
    $LG_Ch_Categ_Image = 'Image';
}

if ($nom_page == 'Edition_Depart.php') {
    $LG_County_Data = 'Général';
    $LG_County_Name = 'Nom';
    $LG_County_Id = 'Code';
    $LG_County_Region = 'Région';
    define('LG_DEPART_UNKNOWN', 'Département inconnu');
    define('LG_LAREAS_COUNTIES', 'Liste des départements');
}

if (($nom_page == 'Edition_Depot.php') or ($nom_page == 'Fiche_Depot.php')) {
    define('LG_CH_REPOSITORY_NAME', 'Nom');
    define('LG_CH_REPOSITORY_LIST', 'Liste des sources du dépôt');
    define('LG_CH_REPOSITORY_THIS', 'ce dépôt');
}

if (($nom_page == 'Edition_Requete.php') or ($nom_page == 'Fiche_Requete.php')) {
    define('LG_QUERY_CODE', 'Code');
    define('LG_QUERY_THIS', 'cette requête');
    define('LG_QUERY_TITLE', 'Titre');
}

if ($nom_page == 'Admin_Tables.php') {
    $LG_Ch_Adm_T_lib_ok = 'Procéder à l\'action';
    $LG_Ch_Adm_T_Err_List = 'Erreur DB, impossible de lister les tables';
    $LG_Ch_Adm_T_Action = 'Action';
    $LG_Ch_Adm_T_Repair = 'Réparation';
    $LG_Ch_Adm_T_Optim = 'Optimisation';
    $LG_Ch_Adm_T_Tables = 'Tables';
    $LG_Ch_Adm_T_All_None = 'Toutes / aucune';
}

if ($nom_page == 'appelle_image_france_dep.php') {
    $LG_Img_FR_Birth = 'Répartition des naissances';
    $LG_Img_FR_Wed = 'Répartition des mariages';
    $LG_Img_FR_Death = 'Répartition des décès';
}

if (($nom_page == 'Arbre_Agnatique_Cognatique.php')
    or ($nom_page == 'Arbre_Asc_Pers.php')
    or ($nom_page == 'Arbre_Noyau.php')
    or ($nom_page == 'Desc_Directe_Pers.php')
    or ($nom_page == 'Arbre_Desc_Pers.php')
) {
    $LG_Tree_Men_Asc = 'Arbre agnatique';
    $LG_Tree_Women_Asc  = 'Arbre cognatique';
    $LG_Tree_Lim_1 = 'Affichage limité à ';
    $LG_Tree_Lim_2 = ' générations, veuillez utiliser la ';
    $LG_Tree_Lim_3 = 'vue personnalisée';
    $LG_Tree_Lim_4 = ' pour remonter au delà';
    $LG_Tree_Icon_Click = 'Cliquez sur les icônes ';
    $LG_Tree_Icon_Hover = 'Déplacez votre souris sur les icônes ';
    $LG_Tree_Show_Image = ' dans les cases <br />pour faire apparaitre les images puis les faire disparaitre.';
    $LG_Tree_Pdf_7Gen = 'Arbre au format PDF 7 générations';
    $LG_Tree_Show_Hide_Child = 'Afficher / masquer les enfants du couple';
    $LG_Tree_Show_Partners = 'Afficher les conjoints';
    $LG_Tree_Show_Tree = 'Afficher l\'arbre';
    $LG_Tree_Show_Desc = 'Afficher la descendance';
}

if ($nom_page == 'Noyau_Pers.php') {
    define('LG_DECUJUS_ERR_NO_EMPTY', 'Fonctionnalité non disponible ; des personnes et / ou des villes existent.');
    define('LG_DECUJUS_DECUJUS', 'Personnage central - decujus');
}

if ($nom_page == 'Calcul_Distance.php') {
    $LG_Ch_Dist_Between = 'Entre';
    $LG_Ch_Dist_And = 'Et';
    $LG_Ch_Dist_Tip = 'On ne peut calculer une distance qu\'entre deux villes dont on a saisi la latitude et la longitude';
    $LG_Ch_Dist_Res1 = 'La distance entre ';
    $LG_Ch_Dist_Res2 = ' et ';
    $LG_Ch_Dist_Res3 = ' est de ';
    $LG_Ch_Dist_Res4 = ' km ';
}

if ($nom_page == 'Edition_Parametres_Graphiques.php') {
    $LG_Graphics_3Gen = '3ème Génération';
    $LG_Graphics_BG = 'Fond de page et première lettre accueil';
    $LG_Graphics_BG_Label = 'Fond des libellés';
    $LG_Graphics_BG_Value = 'Fond des valeurs';
    $LG_Graphics_Bar_List = 'Barre et liste';
    $LG_Graphics_Borders = 'Bordures';
    $LG_Graphics_Color_Current = 'Actuelle';
    $LG_Graphics_Color_New = 'Nouvelle';
    $LG_Graphics_Even = 'Lignes impaires';
    $LG_Graphics_First = 'Premier';
    $LG_Graphics_Form = 'Formulaire de saisie';
    $LG_Graphics_Form_Without_Tab = 'Pour les formulaires sans onglets';
    $LG_Graphics_Init_Color = 'Revenir à la couleur actuelle';
    $LG_Graphics_Last = 'Dernier';
    $LG_Graphics_Lists = 'Pour les listes';
    $LG_Graphics_Next = 'Suivant';
    $LG_Graphics_Odd = 'Lignes paires';
    $LG_Graphics_Pred = 'Graphismes prédéfinis';
    $LG_Graphics_Req_Cols = 'Graphisme à la demande - couleurs';
    $LG_Graphics_Req_Img = 'Graphisme à la demande - images';
    $LG_Graphics_Show_Year = 'Présence de l\'année';
    $LG_Graphics_Stop = 'Stop';
    $LG_Graphics_Table_Border = 'Bordure des tableaux (onglets)';
    $LG_Graphics_Tree = 'Arbre ascendant imprimé';
    $LG_Graphics_Welcome = 'ienvenue sur le site de';
}

if ($nom_page == 'Edition_Filiation.php') {
    $LG_Ch_Filiation_Events = 'Evènements';
    $LG_Ch_Filiation_Docs = 'Documents';
    $LG_Ch_Filiation_Parent_Choice = 'Choix par les parents';
    $LG_Ch_Filiation_Related_Choice = 'Choix par un collatéral';
    $LG_Ch_Filiation_Last_Union = 'Dernier couple saisi';
    $LG_Ch_Filiation_Brother = 'Frère de';
    $LG_Ch_Filiation_Sister = 'Soeur de';
    $LG_Ch_Filiation_Related = 'Colattéral de';
    $LG_Ch_Filiation_Rank = 'Rang';
    $LG_Ch_Filiation_Rank_Inc = 'Augmenter le rang';
    $LG_Ch_Filiation_Rank_Dec = 'Diminuer le rang';
    $LG_Ch_Filiation_Link_Doc = 'Lier un document existant à la filiation';
    $LG_Ch_Filiation_Link_New_Doc = 'Lier un document à la filiation en créant la fiche document';
    $LG_Ch_Filiation_Add_Doc = 'Ajout d\'un document';
}

if (($nom_page == 'Conv_Romain.php')
    or ($nom_page == 'Calc_So.php')
) {
    $LG_Ch_Calc_Max = 'Borne maxi';
    $LG_Ch_Calc_Clear = 'Effacement de la saisie';
    $LG_Ch_Calc_Gen = 'Génération';
    $LG_Ch_Calc_Husb_Wif = 'Conjoint';
    $LG_Ch_Calc_Child = 'Enfant';
    $LG_Ch_Calc_Mo_Side = 'maternelle';
    $LG_Ch_Calc_Fa_Side = 'paternelle';
    $LG_Ch_Calc_Gen_Of = 'Génération de';
    $LG_Ch_Calc_Fa_Of = 'Père de';
    $LG_Ch_Calc_Mo_Of = 'Mère de';
    $LG_Ch_Calc_Ch_Of = 'Enfant de';
    $LG_Ch_Calc_Husb_Wif_Of = 'Conjoint de';
}

if ($nom_page == 'Edition_Parametres_Site.php') {
    $LG_Site_Param_Name = 'Nom';
    $LG_Site_Param_Mail = 'Adresse mail';
    $LG_Site_Param_Year_Only = 'Affichage de l\'année seule dans les dates sur Internet';
    $LG_Site_Param_Year_Threshold = 'Année pivot de masquage des dates sur Internet';
    $LG_Site_Param_No_Premium = 'Option non disponible sur les sites non Premium';
    $LG_Site_Param_Hover_Clic = 'Comportement';
    $LG_Site_Param_Hover = 'Survol';
    $LG_Site_Param_Click = 'Clic';
    $LG_Site_Param_PDF_Font = 'Police de caractères des pdf générés';
    $LG_Site_Param_PDF_Font_Color = 'Couleur de la police de caractères des pdf générés';
    $LG_Site_Param_PDF_Font_Color_Current = 'Actuelle';
    $LG_Site_Param_PDF_Font_Color_New = 'Nouvelle';
    $LG_Site_Param_PDF_Font_Color_Back = 'Revenir à la couleur actuelle';
    $LG_Site_Param_Home_Image = 'Image de la page d\'accueil';
    $LG_Site_Param_Image_With = 'Avec';
    $LG_Site_Param_Image_Without = 'Sans image';
    $LG_Site_Param_Image_No_Need = 'Inutile de re-sélectionner un fichier si vous ne voulez pas changer l\'image';
    $LG_Site_Param_Upload_Error = 'Impossible de placer le fichier dans le répertoire';
    $LG_Site_Param_Error = 'Erreur';
}

if ($nom_page == 'Edition_Region.php') {
    $LG_Edit_Region_Name = 'Nom';
    $LG_Edit_Region_Code = 'Code';
    $LG_Edit_Region_Country = 'Pays';
}

if ($nom_page == 'Edition_Union.php') {
    define('LG_UNION_ADD', 'Ajout d\'une union');
    define('LG_UNION_ADD_DOC', 'Lier un document existant à l\'union');
    define('LG_UNION_ADD_DOC_NEW', 'Lier un document à l\'union en créant la fiche document');
    define('LG_UNION_CHILDREN', 'Enfants');
    define('LG_UNION_CHILDREN_DEF_NAME', 'Nom par défaut');
    define('LG_UNION_CHILDREN_QUICK', 'Saisie rapide d\'enfants');
    define('LG_UNION_CONTRACT', 'Contrat');
    define('LG_UNION_CONTRACT_NOTARY', 'par maître');
    define('LG_UNION_CONTRACT_NOTARY_WHERE', 'Notaire à');
    define('LG_UNION_CONTRACT_WHEN', 'Reçu le');
    define('LG_UNION_EDIT', 'Modification d\'une union');
    define('LG_UNION_EVENTS', 'Evènements');
    define('LG_UNION_FIRST_NAME', 'Prénoms');
    define('LG_UNION_HUS_1ST', 'Conjoint 1');
    define('LG_UNION_HUS_2ND', 'Conjoint 2');
    define('LG_UNION_HUS_WIFE', 'Conjoints');
    define('LG_UNION_THIS', 'cette union');
    define('LG_UNION_UPDATE_PARENTS', 'Modifier la filiation');
    define('LG_UNION_WHEN', 'Unis le');
    define('LG_UNION_WHERE_WHEN', 'Date et lieu');
}

if (($nom_page == 'Edition_Union.php') or ($nom_page == 'Fiche_Couple_txt.php')) {
    define('LG_COUPLE_REPORT_TITLE', 'Fiche couple format texte');
    define('LG_COUPLE_REPORT_PERSON', 'Personne');
    define('LG_COUPLE_REPORT_NICK_M', 'dit');
    define('LG_COUPLE_REPORT_NICK_F', 'dite');
    define('LG_COUPLE_REPORT_UNION', 'Marié');
    define('LG_COUPLE_REPORT_COMMENT', 'Note');
    define('LG_COUPLE_REPORT_CONTRACT', 'Contrat reçu ');
    define('LG_COUPLE_REPORT_CONTRACT_NOTARY', ' par maître ');
    define('LG_COUPLE_REPORT_CONTRACT_NOTARY_WHERE', 'notaire à');
    define('LG_COUPLE_REPORT_HUSB_WIF', 'Conjoint');
    define('LG_COUPLE_REPORT_CHILDREN', 'Enfant(s)');
    define('LG_COUPLE_REPORT_BORN', 'né');
    define('LG_COUPLE_REPORT_DEAD', 'décédé');
}


if ($nom_page == 'exp_GenWeb.php') {
    define('LG_GENWEB_ERROR_FILE', 'impossible de créer');
    define('LG_GENWEB_EXTRACT', 'Extraire la liste');
    define('LG_GENWEB_FILE', 'dans un fichier');
    define('LG_GENWEB_MSG', 'Export GenWeb terminé ; disponible dans le fichier');
    define('LG_GENWEB_SCREEN', 'à l\'écran');
    define('LG_GENWEB_SUFFIX', 'ajouter le nom du département en suffixe');
}

if ($nom_page == 'Galerie_Images.php') {
    define('LG_IMAGES_GAL_CHOOSE_TYPE', 'Choisissez le type du document image');
    define('LG_IMAGES_GAL_UNION', 'Union de');
    define('LG_IMAGES_GAL_UNION_AND', 'et de');
    define('LG_IMAGES_GAL_SON', 'fils de');
    define('LG_IMAGES_GAL_DAUGHTER', 'fille de');
    define('LG_IMAGES_GAL_CHILD', 'enfant de');
    define('LG_IMAGES_GAL_FILIATION', 'Filiation');
}

if (($nom_page == 'Export_Liens.php') or ($nom_page == 'Liste_Liens.php')) {
    define('LG_LINKS_DEL', 'Supprimer les liens sélectionnés');
    define('LG_LINKS_DEL_REP1', 'suppression');
    define('LG_LINKS_DEL_REP2', 'de liens effectuée');
    define('LG_LINKS_ADD', 'Ajouter un lien');
    define('LG_LINKS_IMPORT', 'Import CSV de liens (tableur)');
    define('LG_LINKS_EYE', 'Oeil');
    define('LG_LINKS_THIS', 'le(s) lien(s) sélectionné(s)');
    define('LG_LINKS_EXTRACT', 'Extraction des liens pour la catégorie');
    define('LG_LINKS_EXTRACT_RES1', 'lien');
    define('LG_LINKS_EXTRACT_RES2', 'trouvé');
    define('LG_LINKS_EXTRACT_HEADER', 'Type de lien;Description;URL');
    define('LG_LINKS_EXTRACT_ERROR1', 'Le fichier');
    define('LG_LINKS_EXTRACT_ERROR2', "n'a pas pu être créé ; assurez vous qu'il n'est pas déjà ouvert par ailleurs et que vous avez les droits.");
}

if (($nom_page == 'Verif_Internet.php') or ($nom_page == 'Verif_Internet_Absente.php')) {
    define('LG_CHK_INTERNET_BORN', 'Date de naissance');
    define('LG_CHK_INTERNET_DEATH', 'Date de décès');
    define('LG_CHK_INTERNET_PERSON', 'Personne');
    define('LG_CHK_INTERNET_YEARS', 'ans');
    define('LG_CHK_INTERNET_PRES_1', ' personne(s) née(s) ou décédée(s) il y a moins de');
    define('LG_CHK_INTERNET_PRES_2', 'ans et autorisée(s) à la diffusion sur Internet. Changer la limite pour cette vérification');
    define('LG_CHK_INTERNET_RESULT_1', 'rectification(s) demandée(s)');
    define('LG_CHK_INTERNET_RESULT_2', 'rectification(s) effectuée(s).');
    define('LG_CHK_INTERNET_TIP', 'Décochez les lignes à rectifier.');
    define('LG_CHK_INTERNET_ABS_TIP', 'Cochez les lignes à rectifier.');
    define('LG_CHK_INTERNET_ABS_1', ' personne(s) décédée(s) il y a plus de');
    define('LG_CHK_INTERNET_ABS_2', 'ans ou née(s) il y a plus de');
    define('LG_CHK_INTERNET_ABS_3', 'et non visible(s) sur Internet.');
    define('LG_CHK_INTERNET_ABS_CHG_LIMIT', 'Changer la limite pour cette vérification');
}

if ($nom_page == 'Verif_Sosa.php') {
    define('LG_CHK_SOSA_CALC_NUMBER', 'Numéro théorique');
    define('LG_CHK_SOSA_NON_MATCHING', 'Liste des écarts constatés');
    define('LG_CHK_SOSA_NUMBER', 'Numéro lu');
    define('LG_CHK_SOSA_PERSON', 'Personne');
    define('LG_CHK_SOSA_RESULT_1', 'rectification(s) demandée(s)');
    define('LG_CHK_SOSA_RESULT_2', 'rectification(s) effectuée(s).');
}

if ($nom_page == 'Vue_Personnalisee.php') {
    define('LG_CUST_VIEW_DEFAULT', 'De cujus par défaut');
    define('LG_CUST_VIEW_OTHER', 'Autre de cujus');
    define('LG_CUST_VIEW_PRIVATE', 'Données non publiques');
    define('LG_CUST_VIEW_SELECT', 'Sélectionnez un de cujus');
}

if ($nom_page == 'Arbre_Perso.php') {
    define('LG_PERS_TREE_CHOOSE_OTHER', 'Voir une autre page ...');
    define('LG_PERS_TREE_ERROR_NO_SHOW', 'Cet arbre contient des données non disponibles sur Internet');
    define('LG_PERS_TREE_MISSING_DIR', "Dans la table arbreParam, il manque l\'enregistrement de clés 'repertoire' et genImg");
    define('LG_PERS_TREE_MISSING_ROW', "Dans la table arbre, il manque l'enregistrement ");
    define('LG_PERS_TREE_NOT_FOUND', 'Fichier inexistant ');
    define('LG_PERS_TREE_OPEN_ERROR', 'Ouverture impossible de ');
}

if ($nom_page == 'Edition_Lier_Objet.php') {
    define('LG_LINK_EVT_TITLE', 'Lier un évènement');
    define('LG_LINK_TO_PARENTS', 'Lier un évènement à la filiation');
    define('LG_LINK_TO_UNION', "Lier un évènement à l'union");
    define('LG_LINK_UNION_NF', 'Union non trouvée');
    define('LG_LINK_WITH_1', 'Lien de');
    define('LG_LINK_WITH_2', 'avec');
    define('LG_LINK_THIS', 'cette liaison');
}

if ($nom_page == 'Calendriers.php') {
    define('LG_CALEND_ASCENSION', 'Ascension');
    define('LG_CALEND_AUTUMN', "d'automne,");
    define('LG_CALEND_BROWSER_DEP', '(visible selon les navigateurs)');
    define('LG_CALEND_CALC', 'Date calculée');
    define('LG_CALEND_CALCULATE', 'Calculer');
    define('LG_CALEND_CALC_ON_DATE', 'Calcul sur les dates');
    define('LG_CALEND_CONVERT', 'Convertir');
    define('LG_CALEND_DAY', 'Calcul du jour de la semaine');
    define('LG_CALEND_EASTER', 'Pâques');
    define('LG_CALEND_EASTER_CALC', 'Calcul de la date de Pâques et principales fêtes associées');
    define('LG_CALEND_INITIALE', 'Date initiale');
    define('LG_CALEND_LENT', 'Carême');
    define('LG_CALEND_MONTH', 'Mois');
    define('LG_CALEND_OFFSET', 'Décalage');
    define('LG_CALEND_PALM_SUNDAY', 'Rameaux');
    define('LG_CALEND_PENTECOST', 'Pentecôte');
    define('LG_CALEND_REV_CONVERT', 'Conversions de calendrier républicain');
    define('LG_CALEND_SPRING', "de printemps,");
    define('LG_CALEND_SUMMER', "d'été,");
    define('LG_CALEND_WINTER', "d'hiver");
    define('LG_CALEND_YEAR', 'Année');
}

if ($nom_page == 'Verif_Homonymes.php') {
    define('LG_NAMESAKE_CRITERIA', 'Critères complémentaires');
    define('LG_NAMESAKE_BIRTH', 'Date de naissance');
    define('LG_NAMESAKE_DEATH', 'Date de décès');
    define('LG_NAMESAKE_CHOOSE_ALERT', 'Veuillez saisir une personne dans chaque colonne');
    define('LG_NAMESAKE_CHOOSE_DIFF', 'Veuillez saisir 2 personnes différentes ; les 2 boutons cochés ne doivent pas être sur la même ligne.');
    define('LG_NAMESAKE_ZERO', "Pas d'homonymes détectés");
    define('LG_NAMESAKE_PERS1', 'Personne 1, celle sur laquelle seront fusionnées les données');
    define('LG_NAMESAKE_PERS2', "Personne 2, celle qui faisait l'objet du doublon potentiel");
}

if ($nom_page == 'Utilisations_Document.php') {
    define('LG_DOC_UT_COUNT', 'utilisation(s) trouvée(s)');
    define('LG_DOC_UT_NO', "Pas d'utilisation du document");
    define('LG_DOC_UT_ON', 'Pour');
}

if ($nom_page == 'Recherche_Ville.php') {
    define('LG_TOWN_FOUND', 'ville(s) trouvée(s)');
    define('LG_TOWN_NEW_TAB', 'Nouvel onglet pour les fiches');
    define('LG_TOWN_SCH_NAME', 'Nom');
    define('LG_TOWN_SCH_STATUS', 'Statut de la fiche');
    define('LG_TOWN_SCH_ZIP', 'Code Postal');
}

if ($nom_page == 'Recherche_Document.php') {
    define('LG_DOC_SCH_ON', 'sur les documents de nature');
    define('LG_DOC_SCH_TYPE', 'de type');
    define('LG_DOC_SCH_TITLES', 'des titres contenant');
    define('LG_DOC_SCH_FOUND', 'document(s) trouvé(s)');
    define('LG_DOC_SCH_SEE', 'Voir le document');
    define('LG_DOC_SCH_HEADER_CSV', 'Nature_Document;Titre;Nom_Fichier;Diffusion_Internet;Date_Creation;Date_Modification;Type_Document;');
    define('LG_DOC_SCH_NEW', 'Nouvelle recherche');
    define('LG_DOC_SCH_LB_TITLE', 'Titre contenant');
    define('LG_DOC_SCH_LB_NATURE', 'Nature');
    define('LG_DOC_SCH_LB_TYPE', 'Type');
}

if ($nom_page == 'Pers_Isolees.php') {
    define('LG_PERS_NO_LK_FOUND_MEN', ' homme(s) isolé(s) trouvé(s)');
    define('LG_PERS_NO_LK_FOUND_WOMEN', ' femme(s) isolée(s) trouvée(s)');
    define('LG_PERS_NO_LK_FOUND_UNDEF', ' personnes(s) isolée(s) de sexe indéterminé trouvé(s)');
}

if ($nom_page == 'Notaires_Ville.php') {
    define('LG_NOTARY_TITLE', 'Notaires dans les unions sur la ville de ');
}

if ($nom_page == 'Liste_Pers_Mod.php') {
    define('LG_PERS_MOD_PERS', 'Personnes');
    define('LG_PERS_MOD_WHEN', 'Date de modification');
}

if (($nom_page == 'Liste_Nom_Pop.php') or ($nom_page == 'Liste_Prof_Pop.php')) {
    define('LG_MOST_JOBS', 'Profession');
    define('LG_MOST_NAMES', 'Nom');
    define('LG_MOST_PERS', 'Nombre de personnes');
    define('LG_MOST_JOBS_TIP1', 'Les résultats seront cohérents si les évènements ont été ');
    define('LG_MOST_JOBS_TIP2', 'fusionnés');
}

if ($nom_page == 'Liste_Nom_Vivants.php') {
    define('LG_LIVING_IGNORE', 'Ignorer les personnes sans date de naissance');
    define('LG_LIVING_SHOW_HIDE', 'Afficher / masquer tous les noms');
    define('LG_LIVING_REF_DATE', 'Date pivot de naissance');
    define('LG_LIVING_TODAY', "aujourd'hui");
    define('LG_LIVING_YEARS', 'ans');
}

if ($nom_page == 'Liste_NomFam.php') {
    define('LG_NAMES_LIST_LAST', 'Dernier nom de famille saisi');
    define('LG_NAMES_LIST_ADD', 'Ajouter un nom de famille');
}

if ($nom_page == 'Liste_Patro.php') {
    define('LG_PATRO_THEN', 'puis');
    define('LG_PATRO_DISP_PLACE', 'Afficher les lieux');
    define('LG_PATRO_RESTRICT', 'Limiter au nom du de cujus');
    define('LG_PATRO_SHOW_NOSHOW_FIL', 'Afficher / masquer toutes les filiations');
    define('LG_PATRO_FILIATION', 'Filiation patronymique');
}

if ($nom_page == 'Liste_Eclair.php') {
    define('LG_COUNTY_LIST_SHOW_HIDE', 'Afficher / masquer tous les noms');
    define('LG_COUNTY_LIST_ONE', 'Liste éclair pour le département');
    define('LG_COUNTY_LIST_ALL', 'Liste éclair pour tous les départements');
}

if ($nom_page == 'Liste_Contributions.php') {
    define('LG_CONTRIB_LIST_WHEN', 'Date de la contribution');
    define('LG_CONTRIB_LIST_PROCESS', 'Traiter la contribution');
    define('LG_CONTRIB_LIST_PERSON', 'Personne concernée');
    define('LG_CONTRIB_LIST_NEW', 'Contribution non traitée');
    define('LG_CONTRIB_LIST_IGNORE', 'Ignorer les contributions traitées');
    define('LG_CONTRIB_LIST_CONTRIB', 'Contribution');
}

if ($nom_page == 'Edition_Contribution.php') {
    define('LG_CONTRIB_EDIT_BROWSER', 'Navigateur');
    define('LG_CONTRIB_EDIT_CHILD', 'Enfants');
    define('LG_CONTRIB_EDIT_DATAS', 'Données du contributeur');
    define('LG_CONTRIB_EDIT_FILE_ERROR', "Echec sur l'ouverture de ");
    define('LG_CONTRIB_EDIT_FILE_N_EXISTS', "Le fichier n'existe pas.");
    define('LG_CONTRIB_EDIT_FOR', 'pour');
    define('LG_CONTRIB_EDIT_HUB_WIFE', 'Conjoint');
    define('LG_CONTRIB_EDIT_IP', 'IP');
    define('LG_CONTRIB_EDIT_MAIL', 'Mail');
    define('LG_CONTRIB_EDIT_MESSAGE', 'Message');
    define('LG_CONTRIB_EDIT_SERVER', 'Nom du serveur');
    define('LG_CONTRIB_EDIT_SERVER_IP', 'IP du serveur');
    define('LG_CONTRIB_EDIT_THIS', 'cette contribution');
    define('LG_CONTRIB_EDIT_TIP1', 'signification des enrichissements typographiques');
    define('LG_CONTRIB_EDIT_TIP2', 'Zone modifiée par rapport à la zone actuelle');
    define('LG_CONTRIB_EDIT_TIP3', 'Zone absente de la proposition et reprise de la zone actuelle');
    define('LG_CONTRIB_EDIT_TIP_CHILD', "aucun enrichissement typographique n'est possible car il peut y avoir plusieurs enfants");
    define('LG_CONTRIB_EDIT_TIP_HUSB_WIFE', "aucun enrichissement typographique n'est possible car il peut y avoir plusieurs conjoints");
    define('LG_CONTRIB_EDIT_VERSION', 'Version de Génémania');
    define('LG_CONTRIB_EDIT_PARENTS', 'Parents');
    define('LG_CONTRIB_EDIT_TITLE', "Prise en compte d'une contribution");
    define('LG_CONTRIB_EDIT_UPCASE', 'Nom en majuscules');
    define('LG_CONTRIB_EDIT_ADD', 'Ajouter');
    define('LG_CONTRIB_EDIT_REPLACE', 'Remplacer');
    define('LG_CONTRIB_EDIT_REPLACE_HUSB_WIFE', 'Remplacer conjoint');
    define('LG_CONTRIB_EDIT_REPLACE_CHILDREN', 'Remplacer enfant');
}

if ($nom_page == 'Infos_Tech.php') {
    define('LG_TECH_INFO_VERSION', 'Version de Génémania');
    define('LG_TECH_INFO_ENVIR_LOCAL', 'Environnement : Local');
    define('LG_TECH_INFO_ENVIR_INTERNET', 'Environnement : Internet');
}

// if (($nom_page == 'Import_CSV_Liens.php') or ($nom_page == 'Import_CSV_Evenements.php') or ($nom_page == 'Import_CSV_Villes.php')) {
if (strpos($nom_page, 'Import_CSV') !== false) {
    define('LG_IMP_CSV_REQ_FIELDS', 'Champs demandés');
    define('LG_IMP_CSV_IN_COL', 'dans la colonne');
    define('LG_IMP_CSV_ERR_MATCH_1', 'Erreur de correspondance avec entête présente ; champ ');
    define('LG_IMP_CSV_ERR_MATCH_2', ' inconnu');
    define('LG_IMP_CSV_ERROR_LINE', 'Erreur sur la ligne');
    define('LG_IMP_CSV_LINKS_RESET_LINKS', "Vidage préalable d'une catégorie de liens");
    define('LG_IMP_CSV_LINKS_SEL_TYPE', 'Sélectionnez un type si nécessaire');
    define('LG_IMP_CSV_LINKS_DEL_BEFORE', 'Vidage préalable des liens de type');
    define('LG_IMP_CSV_LINKS_CREATED', 'lien(s) créé(s)');
    define('LG_IMP_CSV_PERS_CREATED', 'personne(s) créée(s)');
    define('LG_IMP_CSV_EVTS_CREATED', 'évènement(s) créé(s)');
    define('LG_IMP_CSV_TOWNS_CREATED', 'ville(s) créée(s)');
    define('LG_IMP_CSV_HEADER_NO', 'Absente');
    define('LG_IMP_CSV_HEADER_YES_IGNORE', 'Présente à ignorer');
    define('LG_IMP_CSV_HEADER_YES_CONSIDER', 'Présente à prendre en compte');
    define('LG_IMP_CSV_COLS_MATCH', 'Correspondances');
    define('LG_IMP_CSV_COL_MATCH_ERROR', 'Erreur correspondance');
    define('LG_IMP_CSV_COLS_GEN', 'Zone Généamania');
    define('LG_IMP_CSV_COLS_CSV', 'Colonne du tableur');
    define('LG_IMP_CSV_ERR_OPEN_FILE', 'Fichier impossible à ouvrir');
    define('LG_IMP_CSV_ERR_TYPE', "Le fichier sélectionné n'a pas la bonne extension");
    define('LG_IMP_CSV_DEFAULT_SHOW', 'Visibilité internet autorisée par défaut');
    define('LG_IMP_CSV_COL_GEN', 'Sélectionnez une zone');
    define('LG_IMP_CSV_NO_PREMIUM', 'Option non disponible sur les sites non Premium');
}

if (($nom_page == 'exp_Gedcom_Personne.php') or ($nom_page == 'exp_Gedcom.php') or ($nom_page == 'xx.php')) {
    define('LG_GEDCOM_FORM', 'Ville,Code lieu,Département,Région,Pays');
    define('LG_GEDCOM_FILE', "L'export Gedcom est disponible dans le fichier");
    define('LG_GEDCOM_FILE_ERROR', "Fichier impossible à créer");
    define('LG_GEDCOM_PERS', 'personne(s) à traiter.');
    define('LG_GEDCOM_PERS_PROCESS', 'personnes traitées.');
    define('LG_GEDCOM_UNIONS', 'unions(s) à traiter.');
    define('LG_GEDCOM_UNIONS_PROCESS', 'unions traitées.');
    define('LG_GEDCOM_FILE_EXPORT', "L'export Gedcom de la base est disponible dans le fichier ");
    define('LG_GEDCOM_FILE_EXPORT_LIGHT', "L'export Gedcom léger (sans commentaires ni images) de la base est disponible dans le fichier ");
}

function lib_sexe_born($sexe)
{
    switch ($sexe) {
        case 'm':
            $ret = 'né';
            break;
        case 'f':
            $ret = 'née';
            break;
        default:
            $ret = 'né(?)';
    }
    return $ret;
}

function lib_sexe_dead($sexe)
{
    switch ($sexe) {
        case 'm':
            $ret = 'décédé';
            break;
        case 'f':
            $ret = 'décédée';
            break;
        default:
            $ret = 'décédé(?)';
    }
    return $ret;
}

function lib_sexe_nickname($sexe)
{
    switch ($sexe) {
        case 'm':
            $ret = 'dit';
            break;
        case 'f':
            $ret = 'dite';
            break;
        default:
            $ret = 'dit(?)';
    }
    return my_html($ret);
}
if (($nom_page == 'Fiche_Indiv_txt.php') or ($nom_page == 'Fiche_Fam_Pers.php') or ($nom_page == 'Fiche_Couple_txt.php')) {
    define('LG_PERS_PERS', 'Personne');
    define('LG_PERS_BROTHERS_SISTERS', 'Frères et soeurs');
    define('LG_PERS_CHILDREN', 'Enfants');
    define('LG_PERS_OLD', 'âge');
    define('LG_PERS_MARIED', 'Mariés');
    define('LG_PERS_CONTRACT', 'contrat reçu');
    define('LG_PERS_MAITRE', 'par maître');
    define('LG_PERS_NOTARY', 'notaire');
    define('LG_PERS_EVENTS', 'Evènements');
    define('LG_PERS_EVENT', 'Evènement');
    define('LG_PERS_WHERE', 'lieu');
    define('LG_PERS_PARTICIPATION', 'Participation');
}

// Spécifique Fiche familiale
if ($nom_page == 'Fiche_Fam_Pers.php') {
    define('LG_FFAM_OBJECT', 'Fiche familiale');
    define('LG_FFAM_CHILDREN_WITH', 'Enfants avec le conjoint');
    define('LG_FFAM_ADD_CHILDREN', 'Créer des enfants pour le couple');
    define('LG_FFAM_CHLIDREN_NO_UNION', 'Enfants hors union');
    define('LG_FFAM_BROTHERS_SISTERS', 'Frères et soeurs');
    define('LG_FFAM_RANK_ISSUE', 'Problème sur le rang');
    define('LG_FFAM_RANK_REORG', 'Ré-organisation des rangs');
    define('LG_FFAM_CONTRIBUTE', 'Ajouter une contribution pour');
    define('LG_FFAM_SET_AS_DECUJUS', 'Choisir provisoirement cette personne comme de cujus (ou Sosa 1)');
    define('LG_FFAM_ALT_NAME', 'dit');
    define('LG_FFAM_ALL_NAME', 'Afficher toutes les personnes portant le nom');
    define('LG_FFAM_MEN_ASC', 'Arbre agnatique');
    define('LG_FFAM_WOMEN_ASC', 'Arbre cognatique');
    define('LG_FFAM_PRINTABLE_TREE', 'Arbre imprimable');
    define('LG_FFAM_INDIV_TEXT_PDF', 'Fiche individuelle au format PDF');
    define('LG_FFAM_ERROR', "Dans la table arbreParam, il manque l'enregistrement de clés 'repertoire' et genPdf");
    define('LG_FFAM_CUST_TREES', 'Arbres personnalis&eacute;s (images)');
    define('LG_FFAM_HUSB_WIFE', 'Conjoints');
    define('LG_FFAM_COUPLE_REC', 'Fiche couple');
    define('LG_FFAM_COUPLE_REC_PDF', 'Fiche couple au format PDF');
    define('LG_FFAM_SHOW_INTERNET', "Autorise l'affichage de la personne sur Internet");
    define('LG_FFAM_NOSHOW_INTERNET', "Interdit l'affichage de la personne sur Internet");
}

if ($nom_page == 'Fusion_Evenements.php') {
    define('LG_EVT_MERGE_OK', 'Fusionner');
    define('LG_EVT_MERGE_PROCESS', 'Traitements des évènements');
    define('LG_EVT_MERGE_REF', 'Référence');
    define('LG_EVT_MERGE_OTHER', 'A fusionner');
    define('LG_EVT_MERGE_SIMULATE', 'Mode simulation');
    define('LG_EVT_MERGE_TIP', 'Sont fusionnés les évènements présentant les mêmes lieux, type, titre et dates.');
    $lg_evt_participation = 'participation';
    $lg_evt_participations = $lg_evt_participation . 's';
    $lg_evt_image = 'image';
    $lg_evt_images = $lg_evt_image . 's';
    $lg_evt_document = 'document';
    $lg_evt_documents = $lg_evt_document . 's';
    define('LG_EVT_MERGE_ACTION', 'à basculer');
    define('LG_EVT_MERGE_IS_COMMENT', 'Préésence de commentaires, pas de fusion');
    $lg_evt_nb_event = 'évènement concerné.';
    $lg_evt_nb_events = 'évènements concernés.';
    $lg_evt_done = array("m" => "basculé", "ms" => "basculés", "f" => "basculée", "fs" => "basculées");
}

if ($nom_page == 'Init_Sosa.php') {
    define('LG_DEL_SOSA_KEEP', 'Garder le de cujus éventuel');
}

if ($nom_page == 'Init_Noms.php') {
    define('LG_INIT_NAMES_DONE', 'Identifiants recalculés');
    define('LG_INIT_NAMES_NONE', "Pas d'identifiant à recalculer");
    define('LG_INIT_NAMES_REF', 'Référence');
    define('LG_INIT_NAMES_INIT', 'Ré-initialisation demandée');
}

if ($nom_page == 'Verif_Personne.php') {
    define('LG_CHK_PERS_CTRLS', 'Contrôles de la personne');
    define('LG_CHK_PERS_CTRLS_PARENTS', 'Contrôles des parents');
    define('LG_CHK_PERS_CTRLS_UNIONS', 'Contrôles des unions');
    define('LG_CHK_PERS_CTRLS_CHILDREN', 'Contrôles des enfants');
}

if ($nom_page == 'Premiers_Pas_Genealogie.php') {
    define('LG_START_DEF', 'Définition de la généalogie');
    define('LG_START_SOURCES', "Sources d'information");
    define('LG_START_CIVIL_REGISTRATION', "L'état civil");
    define('LG_START_CHURCH_RECORDS', 'Registres paroissiaux');
    define('LG_START_YOUR_TURN', 'A vous de jouer...');
}

if ($nom_page == 'Glossaire_Gen.php') {
    define('LG_GLOSS_ACT', 'Acte');
    define('LG_GLOSS_ADULTERINE', 'Adultérin');
    define('LG_GLOSS_AGNATIC', 'Agnat, Agnatique');
    define('LG_GLOSS_GRAND_FA_MOTHER', 'Aïeul(e)');
    define('LG_GLOSS_ANCESTORS', 'Aïeux');
    define('LG_GLOSS_ELDER', 'Aîné');
    define('LG_GLOSS_FIRSTBORN', 'Aînesse');
    define('LG_GLOSS_RELATED', 'Apparenté');
    define('LG_GLOSS_ARMORIAL', 'Armorial');
    define('LG_GLOSS_ARMS', 'Armoiries');
    define('LG_GLOSS_REG_ITEM', 'Article');
    define('LG_GLOSS_ANCESTOR', 'Ascendant');
    define('LG_GLOSS_COMMON_ANCESTOR', 'Auteur');
    define('LG_GLOSS_BANNS', 'Bans');
    define('LG_GLOSS_BAPTISM', 'Baptême');
    define('LG_GLOSS_YOUNGEST', 'Benjamin');
    define('LG_GLOSS_GREAT_GRANDPARENT', 'Bisaïeul');
    define('LG_GLOSS_BLAZON', 'Blason');
    define('LG_GLOSS_BWB', 'B.M.S.');
    define('LG_GLOSS_BRANCH', 'Branche');
    define('LG_GLOSS_CA', 'CA');
    define('LG_GLOSS_LAND_REGISTRY', 'Cadastre');
    define('LG_GLOSS_CADET', 'Cadet');
    define('LG_GLOSS_POLL_TAX', 'Capitation');
    define('LG_GLOSS_CARTULARY', 'Cartulaire');
    define('LG_GLOSS_CHARTER', 'Charte');
    define('LG_GLOSS_BOURG_RECENS', 'Cherche de feux');
    define('LG_GLOSS_COGNATIC', 'Cognat - Cognatique');
    define('LG_GLOSS_COLLATERAL', 'Collatéral');
    define('LG_GLOSS_CONSANGUINEOUS', 'Consanguin');
    define('LG_GLOSS_CONSANGUINITY', 'Consanguinité');
    define('LG_GLOSS_CONSCRIPTION', 'Conscription');
    define('LG_GLOSS_REFERENCE', 'Cote');
    define('LG_GLOSS_CURATORSHIP', 'Curatelle');
    define('LG_GLOSS_DECUJUS', 'De cujus');
    define('LG_GLOSS_DEG_RELATIONSHIP', 'Degré de parenté');
    define('LG_GLOSS_DESCENDANTS', 'Descendance');
    define('LG_GLOSS_INBREEDING_EXEMPTIONS', 'Dispenses de consanguinité');
    define('LG_GLOSS_AFFINITY_EXEMPTIONS', "Dispenses d'affinité");
    define('LG_GLOSS_ENDOGAMY', 'Endogamie');
    define('LG_GLOSS_REGISTRATION', 'Enregistrement');
    define('LG_GLOSS_ANNOUNCEMENT', 'Faire-part');
    define('LG_GLOSS_HOUSE', 'Feu');
    define('LG_GLOSS_FILIATION', 'Filiation');
    define('LG_GLOSS_COLLECTION', 'Fonds');
    define('LG_GLOSS_GEDCOM', 'Gedcom');
    define('LG_GLOSS_GENERATION', 'Génération');
    define('LG_GLOSS_FULL_ORIGIN', 'Germains');
    define('LG_GLOSS_REGISTRY', 'Greffe');
    define('LG_GLOSS_HERALDRY', 'Héraldique');
    define('LG_GLOSS_HEIRS', 'Hoirs');
    define('LG_GLOSS_ILLEGITIMATE', 'Illégitime');
    define('LG_GLOSS_IMPLEX', 'Implexe');
    define('LG_GLOSS_INDEX', 'Index');
    define('LG_GLOSS_PUB_RECORD', 'Insinuation');
    define('LG_GLOSS_NO_WILL', 'Intestat');
    define('LG_GLOSS_INVENTORY', 'Inventaire');
    define('LG_GLOSS_POST_MORTEM_INVENTORY', 'Inventaire après décès');
    define('LG_GLOSS_LEGITIMATION', 'Légitimation');
    define('LG_GLOSS_BUNDLE', 'Liasse');
    define('LG_GLOSS_LINEAGE', 'Lignage');
    define('LG_GLOSS_HISTORY_BOOK', 'Livre de raison');
    define('LG_GLOSS_FAMILY_REC_BOOK', 'Livret de famille');
    define('LG_GLOSS_MILITARY_RECORD', 'Livret militaire');
    define('LG_GLOSS_FAMILY', 'Maison');
    define('LG_GLOSS_MATRONYM', 'Matronyme');
    define('LG_GLOSS_MARGINAL_MENTION', 'Mention marginale');
    define('LG_GLOSS_RECORD', 'Minute');
    define('LG_GLOSS_NATURAL', 'Naturel');
    define('LG_GLOSS_NUMBERING', 'Numérotation');
    define('LG_GLOSS_NOBLE_FAMILIES', 'Nobiliaire');
    define('LG_GLOSS_URGENT_BAPTISM', 'Ondoiement');
    define('LG_GLOSS_ONOMASTICS', 'Onomastique');
    define('LG_GLOSS_PALEOGRAPHY', 'Paléographie');
    define('LG_GLOSS_RELATIVES', 'Parentèle');
    define('LG_GLOSS_PARISH', 'Paroisse');
    define('LG_GLOSS_PATRONYMIC', 'Patronyme');
    define('LG_GLOSS_POSTERITY', 'Postérité');
    define('LG_GLOSS_YOUNGER', 'Puîné');
    define('LG_GLOSS_LINEAGE_ANCESTOR', 'Quartier');
    define('LG_GLOSS_CENSUS', 'Recensement');
    define('LG_GLOSS_PARISH_REGISTERS', 'Registres paroissiaux');
    define('LG_GLOSS_SOSA', 'Sosa');
    define('LG_GLOSS_SOURCES', 'Sources');
    define('LG_GLOSS_NICKNAME', 'Surnom');
    define('LG_GLOSS_10Y_TABLE', 'Table décennale');
    define('LG_GLOSS_TABELLION', 'Tabellion');
    define('LG_GLOSS_COMMON', 'Usuel');
    define('LG_GLOSS_UTERINE', 'Utérin');
    define('LG_GLOSS_5PC_TAX', 'Vingtième');
    define('LG_GLOSS_MORE_INFO', 'En savoir plus');
    define('LG_GLOSS_TIP_1', 'Cliquez sur les mots en');
    define('LG_GLOSS_TIP_2', 'bleu');
    define('LG_GLOSS_TIP_3', 'ils renvoient à un autre mot ou sur');
    define('LG_GLOSS_TIP_4', 'pour avoir plus de détails');
}

if ($nom_page == 'Export.php') {
    define('LG_EXPORT_TYPE', "Type d'export");
    define('LG_EXPORT_FILE_SUFFIXE', "Suffixe du fichier d'export");
    define('LG_EXPORT_FILE_SUFFIXE_WITH', 'Avec');
    define('LG_EXPORT_FILE_SUFFIXE_WITHOUT', 'Sans le suffixe');
    define('LG_EXPORT_TARGET_INTERNET', 'Internet');
    define('LG_EXPORT_TARGET_HOSTED', 'pour site hébergé sur Généamania');
    define('LG_EXPORT_TARGET_BACKUP', 'Sauvegarde');
    define('LG_EXPORT_OMIT_RECENT', 'Masquage des dates récentes');
    define('LG_EXPORT_DATE_THRES', 'Pivot');
    define('LG_EXPORT_YEARS', 'ans');
    define('LG_EXPORT_LIST_ERROR', 'Impossible de lister les tables');
    define('LG_EXPORT_HOVER', 'passez votre souris');
    define('LG_EXPORT_CLICK', 'cliquez');
    define('LG_EXPORT_TIP1', 'Toutes les tables sont sélectionnées par défaut ; ');
    define('LG_EXPORT_TIP2', " sur l'icône qui suit pour faire apparaître la liste des tables");
    define('LG_EXPORT_SHOW', 'Montrer les tables');
    define('LG_EXPORT_ALL_NONE', 'Toutes / aucune');
    define('LG_EXPORT_NO_EXPORT', "Les données de cette table ne sont pas exportées lors d'un export Internet");
    define('LG_EXPORT_MODIFY', "Les données de cette table sont modifiées lors d'un export Internet");
    define('LG_EXPORT_RES_NO_EXTRACT', "Pas d'extraction des données");
    define('LG_EXPORT_DATA_OK', 'Données OK');
    define('LG_EXPORT_STRUCTURE_OK', 'Structure OK');
    define('LG_EXPORT_RAWS', 'ligne(s)');
    define('LG_EXPORT_FILE', "L'export de la base est disponible dans le fichier");
    define('LG_EXPORT_GUEST', 'invité');
    define('LG_EXPORT_CATEG_BLUE', 'Catégorie bleue');
    define('LG_EXPORT_CATEG_GREEN', 'Catégorie verte');
    define('LG_EXPORT_CATEG_ORANGE', 'Catégorie orange');
    define('LG_EXPORT_CATEG_PINK', 'Catégorie rose');
    define('LG_EXPORT_CATEG_PURPLE', 'Catégorie violette');
    define('LG_EXPORT_CATEG_RED', 'Catégorie rouge');
    define('LG_EXPORT_CATEG_YELLOW', 'Catégorie jaune');
    define('LG_EXPORT_EVT1', "la Revue Française de Généalogie teste Geneamania dans son numéro d'été");
}

if (($nom_page == 'controle_nomFam.php') or ($nom_page == 'Edition_NomFam.php')) {
    define('LG_CTRL_NAME_EXISTS', "Le nom de famille existe déjà dans la base. Voulez-vous fusionner ce nom avec celui existant ?");
}

if ($nom_page == 'Import_Sauvegarde.php') {
    define('LG_IMP_BACKUP_RESET', 'Vidage préalable de la base actuelle');
    define('LG_IMP_BACKUP_RESET_TIP', 'Ne validez cette option que si vous savez ce que vous faites !');
    define('LG_IMP_BACKUP_FILE', 'Fichier de sauvegarde à télécharger');
    define('LG_IMP_BACKUP_FILE_SELECT', 'ou sélectionnez en un parmi ceux présents ');
    define('LG_IMP_BACKUP_FILE_SHOW', 'Montrer les fichiers');
    define('LG_IMP_BACKUP_TARGET', 'Base de destination');
    define('LG_IMP_BACKUP_TARGET_LOCAL', 'Locale');
    define('LG_IMP_BACKUP_TARGET_INTERNET', 'Internet');
    define('LG_IMP_BACKUP_INTERNET_PARAMS', 'Paramètres de connexion pour internet');
    define('LG_IMP_BACKUP_INTERNET_PARAMS_DB', 'Base');
    define('LG_IMP_BACKUP_INTERNET_PARAMS_USER', 'Utilisateur');
    define('LG_IMP_BACKUP_INTERNET_PARAMS_PSW', 'Mot de passe');
    define('LG_IMP_BACKUP_INTERNET_PARAMS_SITE', 'Site');
    define('LG_IMP_BACKUP_INTERNET_PARAMS_PORT', 'port');
    define('LG_IMP_BACKUP_INTERNET_PARAMS_SAVE', 'Mémorisation des paramètres de connexion dans un fichier');
    define('LG_IMP_BACKUP_INTERNET_SAVE_REQUEST', 'demande de mémorisation');
    define('LG_IMP_BACKUP_KEEP_USERS', 'Préserver la liste des utilisateurs');
    define('LG_IMP_BACKUP_KEEP_USERS2', 'Préservation de la liste des utilisateurs');
    define('LG_IMP_BACKUP_READ_LINES', 'ligne(s) traitée(s)');
    define('LG_IMP_BACKUP_LINES_ERROR', 'Nombre de lignes incohérent pour la table, vidage...');
    define('LG_IMP_BACKUP_FILE_ERROR', 'impossible de créer');
    define('LG_IMP_BACKUP_TABLE_ERROR', 'Impossible de lister les tables');
    define('LG_IMP_BACKUP_FILE_ERROR_TXT', "Le fichier doit avoir l'extension txt");
    define('LG_IMP_BACKUP_FILE_ERROR_TXT_SQL', "Le fichier doit avoir l'extension txt ou sql");
    define('LG_IMP_BACKUP_NO_FILE', 'Pas de fichier sélectionné');
    define('LG_IMP_BACKUP_NO_PREFIX', 'pas de préfixe');
    define('LG_IMP_BACKUP_PREFIX', 'Préfixe de rechargement');
    define('LG_IMP_BACKUP_ERR_VERS', "Votre version locale n'est pas en phase avec la version courante ; arrêt de l'import");
    define('LG_IMP_BACKUP_LOCAL_VERS', 'version locale');
    define('LG_IMP_BACKUP_CUR_VERS', 'version courante');
    define('LG_IMP_BACKUP_TABLE_IN_PROGRESS', 'Traitement de la table');
    define('LG_IMP_BACKUP_REQ', 'requêtes');
    define('LG_IMP_BACKUP_LINES', 'lignes');
    define('LG_IMP_BACKUP_ITEM_READ', 'lues dans le fichier');
    define('LG_IMP_BACKUP_ITEM_OK', 'requêtes exécutées avec succès');
    define('LG_IMP_BACKUP_TABLE_DELETE', 'suppression de la table créée à tort');
}

if ($nom_page == 'Import_Docs.php') {
    define('LG_IMP_DOCS_MISS_IMG', 'Images absentes');
    define('LG_IMP_DOCS_MISS_DOC', 'Documents absents');
    define('LG_IMP_DOCS_MISS_IMG_DOC', 'Image / document');
    define('LG_IMP_DOCS_DOC_NOT_FORESSEN1', 'Image ou document de type image ');
    define('LG_IMP_DOCS_DOC_NOT_FORESSEN2', ' non prévu');
    define('LG_IMP_DOCS_FILE_EXISTS1', 'Fichier ');
    define('LG_IMP_DOCS_FILE_EXISTS2', ' déjà existant');
    define('LG_IMP_DOCS_IMPORT', 'Import de ');
    define('LG_IMP_DOCS_IMG', 'image');
    define('LG_IMP_DOCS_HTML', 'page HTML');
    define('LG_IMP_DOCS_PDF', 'fichier PDF');
    define('LG_IMP_DOCS_SIZE', ' octets ');
}

if ($nom_page == 'Export_Pour_Deces.php') {
    define('LG_EXPORT_DEATH_DEAD', 'Ignorer les personnes avec date de décès');
    define('LG_EXPORT_DEATH_EXTRACT', 'Personne(s) exportée(s)');
    define('LG_EXPORT_DEATH_MIN_YEAR', 'Année minimale de naissance');
    define('LG_EXPORT_DEATH_ERROR1', 'Le fichier');
    define('LG_EXPORT_DEATH_ERROR2', "n'a pas pu être créé ; assurez vous qu'il n'est pas déjà ouvert par ailleurs et que vous avez les droits.");
    define('LG_EXPORT_DEATH_GOTO1', 'Rendez-vous sur ');
    define('LG_EXPORT_DEATH_GOTO2', "pour l'exploiter.");
    define('LG_EXPORT_DEATH_INTERNET', "Assurez-vous d'être connecté(e) à Internet pour pouvoir accéder à MatchId");
    define('LG_EXPORT_DEATH_BIRTH_PLACE', "Lieu de naissance");
}

if ($nom_page == 'Recherche_MatchId_Unitaire.php') {
    define('LG_SCH_MATCH_ANSWER', 'Réponse matchId');
    define('LG_SCH_MATCH_PERS', 'Personne');
    define('LG_SCH_MATCH_SHOW_JSON', 'Afficher / masquer la réponse de matchId');
    define('LG_SCH_MATCH_COPY_DATE', 'Copier la date de décès dans le presse-papier');
    define('LG_SCH_MATCH_NO_INTERNET', "Assurez-vous d'être connecté à Internet");
    define('LG_SCH_MATCH_ERROR', 'Erreur retournée par matchId');
}

if ($nom_page == 'Vide_Base.php') {
    define('LG_RESET_CONFIRM', 'Confirmez-vous le vidage de la base ?');
    define('LG_RESET_NOT_CONFIRMED', 'Vidage de la base non confirmé');
    define('LG_RESET_DATA_BEG', 'Données');
    define('LG_RESET_DATA_END', 'supprimées');
}

if ($nom_page == 'Mail_Ut.php') {
    define('LG_MAIL_SUBJECT', 'Sujet');
    define('LG_MAIL_MSG', 'Sujet');
    define('LG_MAIL_FROM', 'Il est préférable d\'utiliser une autre adresse mail de contact que ');
}

define('LG_AREA_COUNTRY', 'Pays');
define('LG_AREA_REGION', 'Région');
define('LG_AREA_COUNTY', 'Département');
define('LG_AREA_TOWN', 'Ville');
define('LG_AREA_SUBDIV', 'Subdivision');

// Divers
$Mois_Lib = array(
    'janvier',
    'février',
    'mars',
    'avril',
    'mai',
    'juin',
    'juillet',
    'août',
    'septembre',
    'octobre',
    'novembre',
    'décembre'
);
// without entities
$Mois_Lib_h = array(
    'janvier',
    'février',
    'mars',
    'avril',
    'mai',
    'juin',
    'juillet',
    'août',
    'septembre',
    'octobre',
    'novembre',
    'décembre'
);
$Mois_Abr = array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC');
$Jours_Lib = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');

define('LG_SEMIC', '&nbsp;:&nbsp;');
$LG_tip = 'Conseil';
define('LG_TIP', 'Conseil');
$LG_help = 'Aide';
$LG_star = 'étoile';
$LG_and = 'et';
$LG_at = 'à';
define('LG_AT', 'à');
$LG_here = 'ici';
$LG_add = 'Ajouter';
$LG_modify = 'Modifier';
$LG_last_pers = 'Dernière personne saisie';
$LG_add_pers = 'Ajouter une personne';
$LG_printable_format = 'Format imprimable';
$LG_pdf_format = 'Format PDF';
$LG_csv_export = 'Export CSV';
$LG_csv_import = 'Import CSV';
$LG_gedcom_export = 'Export gedcom';
$LG_top = 'Haut de la page';
$LG_noshow_on_internet = 'Pas de visibilité Internet';
$LG_show_on_internet = 'Visibilité Internet';
$LG_checked_record = 'Fiche validée';
define('LG_CHECKED_RECORD_SHORT', 'Validée');
$LG_nochecked_record = 'Fiche non validée';
define('LG_NOCHECKED_RECORD_SHORT', 'Non validée');
$LG_record = 'Fiche';
define('LG_RECORD', 'Fiche');
define('LG_FROM_INTERNET', 'Source Internet');
$LG_show_noshow = 'Afficher / masquer';
$LG_Data_noavailable_profile = 'Données non disponibles';
$LG_function_noavailable_profile = 'Fonctionnalité non disponible pour votre profil';
$LG_modify_list = 'Modifier la liste';
$LG_Sosa_Number = 'Numéro Sosa';
$LG_person = 'Personne';
$LG_birth = 'Naissance';
$LG_born = 'né';
$LG_death = 'Décès';
$LG_dead = 'décédé';
$LG_wedding = 'Mariage';
define('LG_COUNTY', 'Département');
define('LG_FIRST_NAME', 'Prénoms');

define('LG_SEXE', 'Sexe');
define('LG_SEXE_MAN', 'Homme');
define('LG_SEXE_WOMAN', 'Femme');
define('LG_SEXE_MAN_I', 'H');
define('LG_SEXE_WOMAN_I', 'F');

define('LG_PERS_OCCU', 'Profession');

$LG_with = 'avec';
$LG_csv_available_in = "L'export CSV est disponible dans le fichier";
$LG_show_comment = 'Visualiser la note';
$LG_back_to_home = 'Retour à l\'accueil';
$LG_desc_tree = 'Arbre descendant';
$LG_assc_tree = 'Arbre ascendant';
$LG_quick_adding = 'Ajout rapide';
$LG_Yes = 'oui';
$LG_No = 'non';
$LG_Simu_No_Granted = 'Simulation accès invité';
define('LG_CHILD', 'enfant');
define('LG_SON', 'fils');
define('LG_DAUGHTER', 'fille');
define('LG_HUSB_WIFE', 'conjoint');
define('LG_BROTHER_SISTER', 'frère / soeur');
define('LG_PARENTS', 'parents');
define('LG_SON_OF', 'fils de');
define('LG_DAUGHTER_OF', 'fille de');
define('LG_CHILD_OF', 'enfant de');
$LG_Place_Select = 'Sélection d\'une zone';
$LG_Requested_File = 'Fichier demandé';
$LG_Default_Status = 'Statut par défaut des fiches';
$LG_Event = 'évènement';
$LG_LPers_Check_Pers = 'Contrôle de la personne';
define('LG_FATHER', 'Père');
define('LG_MOTHER', 'Mère');
$LG_of = 'de';
$LG_andof = 'et';
$LG_name_pers = 'personnes portant ce nom';
$LG_Name = 'Nom';
define('LG_PERS_SURNAME', 'Surnom');
$LG_Reference = 'Référence';
$LG_Image = 'Image';
define('LG_NAME_TO_UPCASE', 'Mettre le nom en majuscules');
define('LG_ADD_NAME', 'Ajouter un nom');
define('LG_GEN_FIRST', '1ère génération');
define('LG_GEN_NEXT', 'ème génération');
define('LG_GEN_FATHER', ' côté paternel');
define('LG_GEN_MOTHER', ' côté maternel');

$LG_Month = 'Mois';
$LG_All = 'Tous';
define('LG_TARGET_OBJECT', 'Objet cible');
define('LG_TARGET_OBJECT_PERS', 'Personne');
define('LG_TARGET_OBJECT_UNION', 'Union');
define('LG_TARGET_OBJECT_FILIATION', 'Filiation');
define('LG_TARGET_OBJECT_OTHER', 'Autre');
$LG_Comment = 'commentaire';
define('LG_CH_COMMENT', 'commentaire');
define('LG_CH_COMMENT_VISIBILITY', 'Visibilité Internet du commentaire');
define('LG_CH_IMAGE_MAGNIFY', "Cliquez sur l'image pour l'agrandir");
define('LG_FFAM_CHRONOLOGIE', 'Chronologie');
$LG_Ch_Output_Format = 'Format de sortie';
$LG_Ch_Output_Screen = 'écran';
$LG_Ch_Output_Text = 'texte';
$LG_Ch_Output_CSV = 'CSV';
$LG_Chronology = 'Chronologie';
$LG_7_Gens = '7 générations';
$LG_Add_Existing_Event = 'Ajouter un évènement existant';
$LG_Add_Event = 'Ajouter un évènement';
$LG_Add_Event_Mult_Quick = 'Ajout rapide d\'évènements de type multiple';
$LG_Add_Event_Quick = 'Ajout rapide d\'évènements';
$LG_Del_Event = 'Supprimer le dernièr évènement';

define('LG_EVENT_NOPLACE', 'Pas de lieu');

define('LG_ADD_TOWN', 'Ajout d\'une ville');
define('LG_ADD_TOWN_LIST', 'Ville à ajouter aux listes');

$LG_csv_file_upload = 'Fichier CSV à télécharger';
$LG_csv_header = 'Ligne d\'entête dans le fichier';

$LG_update_link = 'Modification de la liaison';
$LG_this_link = 'ce lien';
$LG_see_document = 'Voir le document';
$LG_html_file = 'Fichier HTML';
$LG_image_file = 'Fichier image';
$LG_pdf_file = 'Fichier PDF';
$LG_text_file = 'Fichier texte';
$LG_audio_file = 'Fichier audio';
$LG_video_file = 'Fichier vidéo';
$LG_display_list = 'Afficher la liste';

$LG_first = '1er';
$LG_year['abt'] = 'environ';
$LG_year['ca'] = 'ca';
$LG_year['on'] = 'en';
$LG_year['bf'] = 'avant';
$LG_year['af'] = 'après';
$LG_day['ca'] = 'ca';
$LG_day['on'] = 'le';
$LG_day['bf'] = 'avant le';
$LG_day['af'] = 'après le';

// Boutons
$lib_OK = 'OK';
$lib_Okay = 'Valider';
$lib_Annuler = 'Annuler';
$lib_Retour = 'Retour...';
$lib_Rechercher = 'Rechercher';
$lib_Rectifier = 'Rectifier';
$lib_Supprimer = 'Supprimer';
$lib_Deconnecter = 'Se déconnecter';
$lib_Connecter = 'Se connecter';
$lib_Calculer = 'Calculer';
$lib_Nouv_Rech = 'Nouvelle recherche';
$lib_Nouv_Rech_Aff = 'Affiner la recherche';
$lib_Erase = 'Effacer';
$LG_Check_Again = 'Revérifier';
$lib_Afficher = 'Afficher';
$lib_Exporter = 'Exporter';

define('LG_CH_DATA_TAB', 'Données générales');
define('LG_CH_DOCS', 'Documents');
define('LG_CH_FILE', 'Fiche');
$LG_Data_tab = 'Données générales';
define('LG_DATA_TAB', 'Données générales');
$LG_File = 'Fiche';
define('LG_CALL_OPENSTREETMAP', 'Appelle la carte OpenStreetMap avec les coordonnées géographiques');
$LG_Show_On_Map = 'Situe la ville sur une carte OpenStreetMap';
define('LG_TIP_OPENSTREETMAP', 'Les coordonnées permettent de situer une zone sur les cartes libres ');


// Titres des pages
if (!is_info()) {
    $LG_Menu_Title['Rect_Utf']                    = 'Rectification des caractères UTF-8 en base';
    $LG_Menu_Title['Link_Ev_Pers']                = 'Edition d\'un lien évènement-personne';
    $LG_Menu_Title['Link_Pers']                    = 'Lier deux personnes';
    $LG_Menu_Title['Find_Doc']                    = 'Recherche de document';
    $LG_Menu_Title['Subdiv']                    = 'Fiche d\'une subdivision';
    $LG_Menu_Title['Subdiv_Edit']                = 'Modification d\'une subdivision';
    $LG_Menu_Title['Subdiv_Add']                = 'Ajout d\'une subdivision';
    $LG_Menu_Title['Town']                        = 'Fiche d\'une ville';
    $LG_Menu_Title['Town_Edit']                    = 'Modification d\'une ville';
    $LG_Menu_Title['Town_Add']                    = 'Ajout d\'une ville';
    $LG_Menu_Title['County_Edit']                = 'Modification d\'un département';
    $LG_Menu_Title['County_Add']                = 'Ajout d\'un département';
    $LG_Menu_Title['Region_Edit']                = 'Modification d\'une region';
    $LG_Menu_Title['Region_Add']                = 'Ajout d\'une region';
    $LG_Menu_Title['Person_Add']                = 'Ajout d\'une personne';
    $LG_Menu_Title['Person_Modify']                = 'Modification d\'une personne';
    $LG_Menu_Title['Event_Add']                    = 'Ajout d\'un évènement';
    $LG_Menu_Title['Name_Is_Complete']            = 'Complétude des informations des ';
    $LG_Menu_Title['Gen_Is_Complete']            = 'Complétude des personnes par génération';
    $LG_Menu_Title['Names_For_Event']            = 'Liste des noms pour un évènement';
    $LG_Menu_Title['Delete_Sosa']                = 'Supprimer les numéros sosa';
    $LG_Menu_Title['Site_parameters']            = 'Paramètres généraux du site';
    $LG_Menu_Title['Name']                         = 'Fiche d\'un nom de famille';
    $LG_Menu_Title['Name_Edit']                 = 'Modification d\'un nom de famille';
    $LG_Menu_Title['Name_Add']                     = 'Ajout d\'un nom de famille';
    $LG_Menu_Title['Document_Multiple_Add']        = 'Ajout multiple de documents';
    $LG_Menu_Title['Check_Persons']                = 'Contrôle des personnes';
    $LG_Menu_Title['Convert_Roman_To_Arabic']    = 'Convertisseur de nombres romains - arabes';
    $LG_Menu_Title['Category']                    = 'Fiche d\'une catégorie';
    $LG_Menu_Title['Category_Edit']                = 'Edition d\'une catégorie';
    $LG_Menu_Title['First_Wedding']                = 'Age de premier mariage pour la période de naissance';
    $LG_Menu_Title['Namesake_Cheking']            = 'Vérification des homonymes';
    $LG_Menu_Title['Internet_Cheking']            = 'Vérification des visibilités Internet';
    $LG_Menu_Title['Internet_Hidding_Cheking']    = 'Vérification des visibilités Internet restreintes';
    $LG_Menu_Title['Documents_List']            = 'Liste des documents';
    $LG_Menu_Title['Document']                    = 'Fiche d\'un document';
    $LG_Menu_Title['Document_Edit']                = 'Modification d\'une fiche document';
    $LG_Menu_Title['Document_Add']                = 'Création d\'une fiche document';
    $LG_Menu_Title['Document_Utils']            = 'Utilisation(s) du document';
    $LG_Menu_Title['Request']                    = 'Fiche d\'une requête';
    $LG_Menu_Title['Request_Edit']                = 'Modification d\'une requête';
    $LG_Menu_Title['Pers_Uncles']                = 'Oncles et tantes';
    $LG_Menu_Title['Pers_Cousins']                = 'Cousins et cousines';
    $LG_Menu_Title['Pers_Gen']                    = 'Liste par génération';
    $LG_Menu_Title['Calculate_Distance']        = 'Calcul de distance à vol d\'oiseau';
    $LG_Menu_Title['Death_Age']                    = 'Pyramide des âges au décès';
    $LG_BDM_Per                                    = 'Naissances, mariages et décès par ';
    $LG_Menu_Title['BDM_Per_Month']                = $LG_BDM_Per . 'mois';
    $LG_Menu_Title['BDM_Per_Town']                = $LG_BDM_Per . 'ville';
    $LG_Menu_Title['BDM_Per_Depart']            = $LG_BDM_Per . 'département';
    $LG_Histo                                    = 'Historique de l\'âge au ';
    $LG_Menu_Title['Histo_Death']                = $LG_Histo . 'décès';
    $LG_Menu_Title['Histo_First_Wedding']        = $LG_Histo . 'premier mariage';
    $LG_Menu_Title['Histo_First_Child']            = $LG_Histo . 'premier enfant';
    $LG_Menu_Title['Children_Per_Mother']        = 'Historique des enfants par femme';
    $LG_Menu_Title['Statistics']                 = 'Statistiques de la base';
    $LG_Menu_Title['Last_Mod_Pers']             = 'Liste des dernières personnes modifiées';
    $LG_Menu_Title['Most_Used_Names']             = 'Noms les plus portés';
    $LG_Menu_Title['Most_Used_jobs']             = 'Professions les plus exercées';
    $LG_Menu_Title['Connections']                 = 'Liste des connexions';
    $LG_Menu_Title['Links']                     = 'Liste des liens';
    $LG_Menu_Title['Living_Pers']                 = 'Liste par nom des personnes vivantes';
    $LG_Menu_Title['Search_Related']             = 'Recherche de parenté';
    $LG_Menu_Title['Search_Comment']             = 'Recherche dans les commentaires';
    $LG_Menu_Title['Galery']                     = 'Galerie de documents images';
    $LG_Menu_Title['Galery_Branch']             = 'Galerie de documents images par branche';
    $LG_Menu_Title['Compare_Persons']             = 'Comparaison de 2 personnes';
    $LG_Menu_Title['Decujus_And_Family']         = 'Saisie du de cujus et de son noyau familial';
    $LG_Menu_Title['Archive_Preparation']         = 'Préparation de recherche aux archives';
    $LG_Menu_Title['Rank_Edit']                 = 'Edition des rangs';
    $LG_Menu_Title['Name_Not_Used']             = 'Noms non utilisés';
    $LG_Menu_Title['Tables_Admin']                = 'Administration des tables';
    $LG_Menu_Title['Calc_Sosa']                    = 'Calculatrice Sosa';
    $LG_Menu_Title['Direct_Desc']                = 'Descendance directe';
    $LG_Menu_Title['Event']                        = 'Fiche d\'un évènement';
    $LG_Menu_Title['Event_Edit']                = 'Modification d\'un évènement';
    $LG_Menu_Title['Event_Add']                    = 'Ajout d\'un évènement';
    $LG_Menu_Title['New']                        = 'Fiche d\'une actualité';
    $LG_Menu_Title['New_Edit']                    = 'Modification d\'une actualité';
    $LG_Menu_Title['New_Add']                    = 'Ajout d\'une actualité';
    $LG_Menu_Title['Link']                        = 'Fiche d\'un lien';
    $LG_Menu_Title['Link_Edit']                    = 'Modification d\'un lien';
    $LG_Menu_Title['Link_Add']                    = 'Ajout d\'un lien';
    $LG_Menu_Title['Alt_Name']                    = 'Nom secondaire d\'une personne';
    $LG_Menu_Title['Role']                        = 'Fiche d\'un rôle';
    $LG_Menu_Title['Role_Edit']                    = 'Modification d\'un rôle';
    $LG_Menu_Title['Role_Add']                    = 'Création d\'un rôle';
    $LG_Menu_Title['Event_Type']                = 'Fiche d\'un type d\'évènement';
    $LG_Menu_Title['Event_Type_Edit']            = "Modification d'un type d'évènement";
    $LG_Menu_Title['Event_Type_Add']            = "Création d'un type d'évènement";
    $LG_Menu_Title['Event_Type_List']            = "Liste des types d'évènements";
    $LG_Menu_Title['Doc_Type']                    = 'Fiche d\'un type de document';
    $LG_Menu_Title['Doc_Type_Edit']                = 'Modification d\'un type de document';
    $LG_Menu_Title['Doc_Type_Add']                = 'Création d\'un type de document';
    $LG_Menu_Title['Repo_Sources']                 = 'Fiche d\'un dépôt de sources';
    $LG_Menu_Title['Repo_Sources_Edit']         = 'Création d\'un dépôt de sources';
    $LG_Menu_Title['Repo_Sources_Add']             = 'Modification d\'un dépôt de sources';
    $LG_Menu_Title['Source']                     = 'Fiche d\'une source';
    $LG_Menu_Title['Source_Add']                 = 'Création d\'une source';
    $LG_Menu_Title['Source_Edit']                 = 'Modification d\'une source';
    $LG_Menu_Title['Source_List']                 = 'Liste des sources';
    $LG_Menu_Title['User']                         = 'Fiche d\'un utilisateur';
    $LG_Menu_Title['User_Add']                     = 'Création d\'un utilisateur';
    $LG_Menu_Title['User_Edit']                 = 'Modification d\'un utilisateur';
    $LG_Menu_Title['Users_List']                 = 'Liste des utilisateurs';
    $LG_Menu_Title['exp_GenWeb']                 = 'Export cousins GenWeb';
    $LG_Menu_Title['Sch_Pers']                     = 'Recherche de personnes';
    $LG_Menu_Title['Sch_Pers_CP']                 = 'Recherche de personnes par les conjoints ou parents';
    $LG_Menu_Title['Image_List']                 = 'Liste des images';
    $LG_Menu_Title['Custom_View']                 = 'Vue personnalisée';
    $LG_Menu_Title['Check_Sosa']                 = 'Vérification de la numérotation sosa';
    $LG_Menu_Title['Pers_Tree']                 = "Affichage d''un arbre personnalisé";
    $LG_Menu_Title['Town_Search_Title']         = 'Recherche de villes';
    $LG_Menu_Title['Town_Search']                 = 'De villes';
    $LG_Menu_Title['Non_Linked_Pers']             = 'Liste des personnes isolées';
    $LG_Menu_Title['Names_List']                 = 'Liste des noms de famille';
    $LG_Menu_Title['Patronymic_List']             = 'Liste patronymique';
    $LG_Menu_Title['Event_List']                = 'Liste des évènements';
    $LG_Menu_Title['Event_List_Area']            = 'Liste des évènements pour une zone';
    $LG_Menu_Title['News_List']                    = 'Liste des actualités';
    $LG_Menu_Title['Jobs_List']                    = 'Liste des professions';
    $LG_Menu_Title['County_List']                = 'Liste éclair';
    $LG_Menu_Title['Contribs_List']                = 'Liste des contributions';
    $LG_Menu_Title['Tech_Info']                    = 'Informations techniques';
    $LG_Menu_Title['Imp_CSV_Links']                = 'Import CSV de liens (tableur)';
    $LG_Menu_Title['Imp_CSV_Events']            = "Import CSV d'évènements (tableur)";
    $LG_Menu_Title['Imp_CSV_Towns']                = 'Import CSV de villes (tableur)';
    $LG_Menu_Title['Exp_Ged_Pers']                = 'Export Gedcom personne';
    $LG_Menu_Title['Exp_Ged']                    = 'Export Gedcom';
    $LG_Menu_Title['Exp_Ged_Light']                = 'Export Gedcom "léger"';
    $LG_Menu_Title['Indiv_Text_Report']            = 'Fiche individuelle format texte';
    $LG_Menu_Title['Nuclear_Family']            = 'Noyau familial';
    $LG_Menu_Title['Partners_Ancestors']        = 'Ascendants des conjoints';
    $LG_Menu_Title['Event_Merging']                = "Fusion d'évènements";
    $LG_Menu_Title['Init_Names']                = 'Identifiants manquants pour les noms de famille';
    $LG_Menu_Title['Check_Pers']                = "Contrôle d'une personne";
    $LG_Menu_Title['Start']                        = 'Premiers pas en généalogie';
    $LG_Menu_Title['Glossary']                    = 'Glossaire de généalogie';
    $LG_Menu_Title['Sosa']                        = 'Numérotation Sosa';
    $LG_Menu_Title['Import_Backup']                = "Import d'une sauvegarde";
    $LG_Menu_Title['Import_Docs']                = "Import de documents et d'images";
    $LG_Menu_Title['Role_List_Pers']            = 'Personnes pour un rôle';
    $LG_Menu_Title['Design']                    = 'Graphisme du site';
    $LG_Menu_Title['Export_Death']                = 'Export pour recherche des dates de décès';
    $LG_Menu_Title['Reset_DB']                    = 'Vidage de la base';
    $LG_Menu_Title['MatchId_Sch']                = 'Recherche matchId';
    $LG_Menu_Title['Town_Merging']                = 'Fusion de villes';
}
