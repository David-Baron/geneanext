<li class="dropdown">
    <a data-toggle="dropdown" class="dropbtn">Menu rapide...</a>
    <div class="dropdown-content">
        <?php if (IS_GRANTED('C')) { ?>
            <div><i><b>Accès rapide</b></i></div>
            <a href="/edition_personne?Refer=-1"><?= $LG_Menu_Title['Person_Add']; ?></a>
            <a href="/edition_ville?Ident=-1"><?= $LG_Menu_Title['Town_Add']; ?></a>
            <a href="/edition_evenement?refPar=-1"><?= $LG_Menu_Title['Event_Add']; ?></a>
            <a href="/edition_nomfam?idNom=-1">Ajouter un nom de famille </a>
        <?php } ?>
        <?php if (IS_GRANTED('G')) { ?>
            <a href="/edition_parametres_graphiques">Graphisme du site</a>
            <?php if ($Base_Vide) { ?>
                <a href="/noyau_pers">Saisie du de cujus et de son noyau familial</a>
            <?php } ?>
        <?php } ?>
        <?php if (!$Base_Vide) { ?>
            <?php if (IS_GRANTED('I')) { ?>
                <div><i><b>Listes des personnes</b></i></div>
                <a href="<?= $root; ?>/liste_pers?Type_Liste=P">Par nom</a>
                <a href="<?= $root; ?>/liste_pers_gen">Par génération</a>
                <a href="<?= $root; ?>/liste_pers?Type_Liste=N">Par ville de naissance</a>
                <a href="<?= $root; ?>/liste_pers?Type_Liste=M">Par ville de mariage</a>
                <a href="<?= $root; ?>/liste_pers?Type_Liste=K">Par ville de contrat de mariage</a>
                <a href="<?= $root; ?>/liste_pers?Type_Liste=D">Par ville de décès</a>
                <a href="<?= $root; ?>/liste_patro">Liste patronymique</a>
                <a href="<?= $root; ?>/liste_eclair"><?= $LG_Menu_Title['County_List']; ?></a>
                <a href="<?= $root; ?>/liste_nom_vivants"><?= $LG_Menu_Title['Living_Pers']; ?></a>
                <a href="<?= $root; ?>/liste_nomfam">Liste des noms de famille</a>
            <?php } ?>
            <?php if (IS_GRANTED('C')) { ?>
                <a href="<?= $root; ?>/liste_pers?Type_Liste=C">Par catégorie</a>
            <?php } ?>
        <?php } ?>
        <?php if (IS_GRANTED('I')) { ?>
            <div><i><b>Listes des zones géographiques</b></i></div>
            <a href="<?= $root; ?>/liste_villes?Type_Liste=S">Subdivisions</a>
            <a href="<?= $root; ?>/liste_villes?Type_Liste=V">Villes</a>
            <a href="<?= $root; ?>/liste_villes?Type_Liste=D">Départements</a>
            <a href="<?= $root; ?>/liste_villes?Type_Liste=R">Régions</a>
            <a href="<?= $root; ?>/liste_villes?Type_Liste=P">Pays</a>

            <div><i><b>Recherche</b></i></div>
            <a href="<?= $root; ?>/recherche_personne">De personnes</a>
            <?php if (IS_GRANTED('P')) { ?>
                <a href="<?= $root; ?>/recherche_personne_cp">De personnes par les conjoints ou parents</a>
                <?php if ((!$SiteGratuit) or ($Premium)) { ?>
                    <a href="<?= $root; ?>/liste_referentiel?Type_Liste=Q">Liste des requêtes sur les personnes</a>
                <?php } ?>
            <?php } ?>
            <a href="https://genealogies.geneamania.net/recherche_heberges.php">Recherche sur les sites gratuits</a>
            <a href="<?= $root; ?>/recherche_cousinage"><?= $LG_Menu_Title['Search_Related']; ?></a>

            <a href="<?= $root; ?>/recherche_ville"><?= $LG_Menu_Title['Town_Search']; ?></a>
            <?php if (IS_GRANTED('C')) { ?>
                <a href="<?= $root; ?>/recherche_personne_archive">Aux archives</a>
                <a href="<?= $root; ?>/recherche_commentaire"><?= $LG_Menu_Title['Search_Comment']; ?></a>
            <?php } ?>
        <?php } ?>
        <?php if ((!$SiteGratuit) or ($Premium)) { ?>
            <a href="<?= $root; ?>/recherche_document">Dans les documents</a>
        <?php } ?>
        <?php if (IS_GRANTED('C')) { ?>
            <div><i><b>Gestion des contributions</b></i></div>
            <a href="<?= $root; ?>/liste_contributions"><?= $LG_Menu_Title['Contribs_List']; ?></a>
        <?php } ?>
        <?php if (IS_GRANTED('P')) { ?>
            <div><i><b>Gestion des catégories</b></i></div>
            <a href="<?= $root; ?>/liste_referentiel?Type_Liste=C">Liste des catégories</a>
        <?php } ?>
        <?php if (IS_GRANTED('P')) { ?>
            <div><i><b>Gestion des évènements et des relations</b></i></div>
            <a href="<?= $root; ?>/liste_evenements"><?= $LG_Menu_Title['Event_List']; ?></a>
            <a href="<?= $root; ?>/liste_evenements?actu=o"><?= $LG_Menu_Title['News_List']; ?></a>
            <a href="<?= $root; ?>/liste_evenements?prof=o"><?= $LG_Menu_Title['Jobs_List']; ?></a>
            <?php if (IS_GRANTED('C')) { ?>
                <a href="<?= $root; ?>/liste_referentiel?Type_Liste=R">Liste des rôles</a>
                <a href="<?= $root; ?>/liste_referentiel?Type_Liste=T"><?= $LG_Menu_Title['Event_Type_List']; ?></a>
                <a href="<?= $root; ?>/fusion_evenements"><?= $LG_Menu_Title['Event_Merging']; ?></a>
            <?php } ?>
        <?php } ?>
        <?php if ((!$SiteGratuit) or ($Premium)) { ?>
            <?php if (IS_GRANTED('C')) { ?>
                <div><i><b>Gestion des dépôts et des sources</b></i></div>
                <a href="<?= $root; ?>/liste_referentiel?Type_Liste=O">Liste des dépôts de sources</a>
                <a href="<?= $root; ?>/liste_sources"><?= $LG_Menu_Title['Source_List']; ?></a>
            <?php } ?>
            <?php if (IS_GRANTED('I')) { ?>
                <div><i><b>Documents</b></i></div>
                <a href="<?= $root; ?>/liste_documents"><?= $LG_Menu_Title['Documents_List']; ?></a>
                <a href="<?= $root; ?>/galerie_images"><?= $LG_Menu_Title['Galery']; ?></a>
                <a href="<?= $root; ?>/liste_docs_branche"><?= $LG_Menu_Title['Galery_Branch']; ?></a>
            <?php } ?>
            <?php if (IS_GRANTED('C')) { ?>
                <a href="<?= $root; ?>/create_multiple_docs"><?= $LG_Menu_Title['Document_Multiple_Add']; ?></a>
                <a href="<?= $root; ?>/liste_referentiel?Type_Liste=D">Liste des types de documents</a>
            <?php } ?>
        <?php } ?>
        <?php if (IS_GRANTED('G')) { ?>
            <div><i><b>Imports - exports</b></i></div>
            <a href="<?= $root; ?>/export">Export de la base</a>
            <a href="<?= $root; ?>/exp_genweb">Export GenWeb</a>
            <a href="<?= $root; ?>/exp_gedcom"><?= $LG_Menu_Title['Exp_Ged']; ?></a>
            <a href="<?= $root; ?>/exp_gedcom?leger=o"><?= $LG_Menu_Title['Exp_Ged_Light']; ?></a>
            <a href="<?= $root; ?>/export_pour_deces"><?= $LG_Menu_Title['Export_Death']; ?></a>
            <a href="<?= $root; ?>/import_gedcom">Import Gedcom</a>
            <a href="<?= $root; ?>/import_sauvegarde"><?= $LG_Menu_Title['Import_Backup']; ?></a>
            <?php if ((!$SiteGratuit) or ($Premium)) { ?>
                <a href="<?= $root; ?>/import_csv">Import CSV (tableur)</a>
                <a href="<?= $root; ?>/import_csv_liens"><?= $LG_Menu_Title['Imp_CSV_Links']; ?></a>
                <a href="<?= $root; ?>/import_csv_evenements"><?= $LG_Menu_Title['Imp_CSV_Events']; ?></a>
                <a href="<?= $root; ?>/import_csv_villes"><?= $LG_Menu_Title['Imp_CSV_Towns']; ?></a>
            <?php } ?>
            <a href="<?= $root; ?>/import_docs">Import_Docs</a>
        <?php } ?>
        <?php if (IS_GRANTED('C')) { ?>
            <div><i><b>Vérifications</b></i></div>
            <a href="<?= $root; ?>/verif_sosa"><?= $LG_Menu_Title['Check_Sosa']; ?></a>
            <a href="<?= $root; ?>/verif_internet"><?= $LG_Menu_Title['Internet_Cheking']; ?></a>
            <a href="<?= $root; ?>/verif_internet_absente"><?= $LG_Menu_Title['Internet_Hidding_Cheking']; ?></a>
            <a href="<?= $root; ?>/pers_isolees"><?= $LG_Menu_Title['Non_Linked_Pers']; ?></a>
            <a href="<?= $root; ?>/verif_homonymes"><?= $LG_Menu_Title['Namesake_Cheking']; ?></a>
            <?php if ((!$SiteGratuit) or ($Premium)) { ?>
                <a href="<?= $root; ?>/controle_personnes"><?= $LG_Menu_Title['Check_Persons']; ?></a>
            <?php } ?>
        <?php } ?>
        <div><i><b>Vue personnalisée</b></i></div>
        <a href="<?= $root; ?>/vue_personnalisee"><?= $LG_Menu_Title['Custom_View']; ?></a>
        <?php if (IS_GRANTED('I')) { ?>
            <div><i><b>Utilitaires</b></i></div>
            <a href="<?= $root; ?>/calendriers">Les calendriers</a>
            <a href="<?= $root; ?>/calc_so"><?= $LG_Menu_Title['Calc_Sosa']; ?></a>
            <a href="<?= $root; ?>/conv_romain">Convertisseur de nombres romains</a>
            <?php if (IS_GRANTED('G')) { ?>
                <a href="<?= $root; ?>/init_sosa"><?= $LG_Menu_Title['Delete_Sosa']; ?></a>
                <?php if (!$SiteGratuit) { ?>
                    <a href="<?= $root; ?>/init_noms"><?= $LG_Menu_Title['Init_Names']; ?></a>
                <?php } ?>
                <!-- <a href="/rectif_utf8"><?= $LG_Menu_Title['Rect_Utf']; ?></a> -->
            <?php } ?>
            <?php if ((!$SiteGratuit) or ($Premium)) { ?>
                <a href="<?= $root; ?>/calcul_distance"><?= $LG_Menu_Title['Calculate_Distance']; ?></a>
                <?php if (IS_GRANTED('C')) { ?>
                    <a href="<?= $root; ?>/liste_noms_non_ut"><?= $LG_Menu_Title['Name_Not_Used']; ?></a>
                <?php } ?>
            <?php } ?>
            <?php if (IS_GRANTED('G')) { ?>
                <a href="<?= $root; ?>/vide_base"><?= $LG_Menu_Title['Reset_DB']; ?></a>
                <?php if (!$SiteGratuit) { ?>
                    <a href="<?= $root; ?>/infos_tech"><?= $LG_Menu_Title['Tech_Info']; ?></a>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <?php if (IS_GRANTED('I')) { ?>
            <div><i><b>Informations</b></i></div>
            <a href="<?= $root; ?>/premiers_pas_genealogie"><?= $LG_Menu_Title['Start']; ?></a>
            <a href="<?= $root; ?>/glossaire_gen"><?= $LG_Menu_Title['Glossary']; ?></a>
            <a href="<?= $root; ?>/stat_base"><?= $LG_Menu_Title['Statistics']; ?></a>
            <a href="<?= $root; ?>/liste_liens"><?= $LG_Menu_Title['Links']; ?></a>
            <a href="<?= $root; ?>/anniversaires">Anniversaires</a>
        <?php } ?>
        <?php if (IS_GRANTED('G')) { ?>
            <div><i><b>Gestion du site</b></i></div>
            <a href="<?= $root; ?>/edition_parametres_site"><?= $LG_Menu_Title['Site_parameters']; ?></a>
            <a href="<?= $root; ?>/edition_parametres_graphiques"><?= $LG_Menu_Title['Design']; ?></a>
            <a href="<?= $root; ?>/liste_utilisateurs"><?= $LG_Menu_Title['Users_List']; ?></a>
            <a href="<?= $root; ?>/liste_connexions"><?= $LG_Menu_Title['Connections']; ?></a>
            <a href="<?= $root; ?>/admin_tables"><?= $LG_Menu_Title['Tables_Admin']; ?></a>
        <?php } ?>
    </div>
</li>