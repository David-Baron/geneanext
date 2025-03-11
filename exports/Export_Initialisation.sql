# Export Initialisation de la base geneamania
# le 3/03/2025 à 18h59
# version Genemania 2025.03 beta 1
# prefixe 
#
# Traitement de la table arbre;
DROP TABLE IF EXISTS `arbre`;
CREATE TABLE `arbre` (
  `idArbre` int NOT NULL AUTO_INCREMENT,
  `nomFichier` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descArbre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `largeurPage` int NOT NULL DEFAULT '0',
  `hauteurPage` int NOT NULL DEFAULT '0',
  `nbPagesHor` int NOT NULL DEFAULT '0',
  `nbPagesVer` int NOT NULL DEFAULT '0',
  `lienPDF` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `dateCre` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateMod` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idArbre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table arbreetiquette;
DROP TABLE IF EXISTS `arbreetiquette`;
CREATE TABLE `arbreetiquette` (
  `idArbre` int NOT NULL DEFAULT '0',
  `idEtiquette` int NOT NULL DEFAULT '0',
  `texte` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nomEtiq` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `margeHaute` int NOT NULL DEFAULT '0',
  `margeBasse` int NOT NULL DEFAULT '0',
  `margeDroite` int NOT NULL DEFAULT '0',
  `margeGauche` int NOT NULL DEFAULT '0',
  `positionX` int NOT NULL DEFAULT '0',
  `positionY` int NOT NULL DEFAULT '0',
  `largeur` int NOT NULL DEFAULT '0',
  `hauteur` int NOT NULL DEFAULT '0',
  `couleurFond` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `couleurBord` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `largeurBordure` int NOT NULL DEFAULT '0',
  `cadreForme` int NOT NULL DEFAULT '0',
  `cadreLargeur` int NOT NULL DEFAULT '0',
  `cadreHauteur` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idArbre`,`idEtiquette`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table arbremodeleetiq;
DROP TABLE IF EXISTS `arbremodeleetiq`;
CREATE TABLE `arbremodeleetiq` (
  `idModele` int NOT NULL AUTO_INCREMENT,
  `typeModele` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nomModele` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descModele` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `modeleDefaut` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dateCre` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateMod` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idModele`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO arbremodeleetiq values ('1','P','Modèle par défaut','<html><head></head><body><p align=\"center\" style=\"margin-top: 0\"><b><font size=\"4\" color=\"#000000\" face=\"Georgia\">&lt;prenomUsuel&gt; &lt;nom&gt; </font></b></p><p align=\"center\" style=\"margin-top: 0\"><font size=\"4\" color=\"#000000\" face=\"Georgia\">&lt;SI&gt;&lt;dateNais(&quot;&quot;)&gt;&lt;ALORS&gt; +&lt;dateNais(&quot;&quot;)&gt; &lt;villeNais&gt;&lt;FINSI&gt; </font></p><p align=\"center\" style=\"margin-top: 0\"><font size=\"4\" color=\"#000000\" face=\"Georgia\">&lt;SI&gt;&lt;dateDeces(&quot;&quot;)&gt;&lt;ALORS&gt; +&lt;dateDeces(&quot;&quot;)&gt; &lt;villeDeces&gt;&lt;FINSI&gt;</font></p></body></html>','D','2009-01-25 20:45:37','2009-01-25 20:45:37');
INSERT INTO arbremodeleetiq values ('2','U','Modèle par défaut','<html><head></head><body><p style=\"margin-top: 0\"><font size=\"3\" face=\"Georgia\">&lt;dateMariage(&quot;&quot;)&gt;&lt;SI&gt;&lt;villeMariage&gt;&lt;ALORS&gt;</font>    </p><p style=\"margin-top: 0\"><font size=\"3\" face=\"Georgia\">(&lt;villeMariage&gt;)&lt;FINSI&gt; </font></p></body></html>','D','2009-01-25 20:45:37','2009-01-25 20:45:37');
INSERT INTO arbremodeleetiq values ('3','P','Essai','<html><nl>  <head><nl>    <nl>  </head><nl>  <body><nl>    <p style=\"margin-top: 0\"><nl>      &lt;nom&gt;&lt;prenomUsuel&gt;<nl>    </p><nl>    <p style=\"margin-top: 0\"><nl>      &lt;evenement&gt;&lt;titreEvt&gt;<nl>    </p><nl>  </body><nl></html><nl>','','2011-08-10 22:19:27','2011-08-10 22:23:58');
#
# Traitement de la table arbreparam;
DROP TABLE IF EXISTS `arbreparam`;
CREATE TABLE `arbreparam` (
  `ident1` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ident2` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `valeur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` tinyint NOT NULL DEFAULT '0',
  `limites` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ordre` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`ident1`,`ident2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO arbreparam values ('dimension','ecartGener','110','Distance entre deux générations','1','50,200','23');
INSERT INTO arbreparam values ('dimension','ecartUnion','20','Ecart entre deux personnes unies','1','10,50','22');
INSERT INTO arbreparam values ('dimension','personne','14','Dimension du symbole d\'une personne','1','10,25','21');
INSERT INTO arbreparam values ('dimension','titre','','Dimensions','0','','20');
INSERT INTO arbreparam values ('femme','coulFond','255,255,255','Couleur de fond','3','','71');
INSERT INTO arbreparam values ('femme','titre','','Symbole d\'une femme','0','','70');
INSERT INTO arbreparam values ('homme','coulFond','255,255,255','Couleur de fond','3','','61');
INSERT INTO arbreparam values ('homme','titre','','Symbole d\'un homme','0','','60');
INSERT INTO arbreparam values ('parametres','dateModif','2011-08-10 22:18:54','Date de la dernière modification des préférences','5','','127');
INSERT INTO arbreparam values ('parametres','genFiche','n','Génération des fiches individuelles','7','Aucun,Par arbre,Commun;a,p,c','122');
INSERT INTO arbreparam values ('parametres','recherche','Oui','Utilisation de la recherche de personne','6','','121');
INSERT INTO arbreparam values ('parametres','titre','','Renseignements généraux','0','','120');
INSERT INTO arbreparam values ('parametres','version','4.0','Numéro de la version','5','','126');
INSERT INTO arbreparam values ('personne','coulFond','255,255,255','Couleur de fond','3','','81');
INSERT INTO arbreparam values ('personne','titre','','Symbole d\'une personne (sexe inconnu)','0','','80');
INSERT INTO arbreparam values ('repertoire','genImg','fichiers/images','Répertoire pour générer les images','4','2','103');
INSERT INTO arbreparam values ('repertoire','genPdf','fichiers/pdf','Répertoire pour générer les fichiers PDF','4','2','102');
INSERT INTO arbreparam values ('repertoire','polices','c:/windows/fonts','Répertoire contenant les polices du système','4','1','101');
INSERT INTO arbreparam values ('repertoire','titre','','Répertoires','0','','100');
#
# Traitement de la table arbrepers;
DROP TABLE IF EXISTS `arbrepers`;
CREATE TABLE `arbrepers` (
  `idArbre` int NOT NULL DEFAULT '0',
  `reference` int NOT NULL DEFAULT '0',
  `posX` int NOT NULL DEFAULT '0',
  `posY` int NOT NULL DEFAULT '0',
  `ecartEtiqX` int NOT NULL DEFAULT '0',
  `ecartEtiqY` int NOT NULL DEFAULT '0',
  `ecartLienX` int NOT NULL DEFAULT '0',
  `ecartLienY` int NOT NULL DEFAULT '0',
  `evtEtiq` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `idModele` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idArbre`,`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table arbrephotos;
DROP TABLE IF EXISTS `arbrephotos`;
CREATE TABLE `arbrephotos` (
  `idArbre` int NOT NULL DEFAULT '0',
  `numImage` int NOT NULL DEFAULT '0',
  `reference` int NOT NULL DEFAULT '0',
  `nomFichier` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ratio` float(7,6) NOT NULL DEFAULT '0.000000',
  `posX` int NOT NULL DEFAULT '0',
  `posY` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idArbre`,`numImage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table arbreunion;
DROP TABLE IF EXISTS `arbreunion`;
CREATE TABLE `arbreunion` (
  `idArbre` int NOT NULL DEFAULT '0',
  `refParent1` int NOT NULL DEFAULT '0',
  `refParent2` int NOT NULL DEFAULT '0',
  `typeLienParents` tinyint NOT NULL DEFAULT '0',
  `ecartPtDebParents` int NOT NULL DEFAULT '0',
  `ecartPtFinParents` int NOT NULL DEFAULT '0',
  `ratio1Parents` float(9,6) NOT NULL DEFAULT '0.000000',
  `ratio2Parents` float(9,6) NOT NULL DEFAULT '0.000000',
  `ratio3Parents` float(9,6) NOT NULL DEFAULT '0.000000',
  `typeLienFam` tinyint NOT NULL DEFAULT '0',
  `ratio1Fam` float(9,6) NOT NULL DEFAULT '0.000000',
  `ratio2Fam` float(9,6) NOT NULL DEFAULT '0.000000',
  `ratio3Fam` float(9,6) NOT NULL DEFAULT '0.000000',
  `idModele` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idArbre`,`refParent1`,`refParent2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table categories;
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `Identifiant` int NOT NULL AUTO_INCREMENT,
  `Image` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Titre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Ordre_Tri` int NOT NULL,
  PRIMARY KEY (`Identifiant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO categories values(1, "bleu", "Catégorie bleue", 1);
INSERT INTO categories values(2, "vert", "Catégorie verte", 2);
INSERT INTO categories values(3, "orange", "Catégorie orange", 3);
INSERT INTO categories values(4, "rose", "Catégorie rose", 4);
INSERT INTO categories values(5, "violet", "Catégorie violette", 5);
INSERT INTO categories values(6, "rouge", "Catégorie rouge", 6);
INSERT INTO categories values(7, "jaune", "Catégorie jaune", 7);
#
# Traitement de la table commentaires;
DROP TABLE IF EXISTS `commentaires`;
CREATE TABLE `commentaires` (
  `Commentaire` int NOT NULL AUTO_INCREMENT,
  `Reference_Objet` int NOT NULL DEFAULT '0',
  `Type_Objet` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `Diff_Internet_Note` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'O',
  PRIMARY KEY (`Commentaire`,`Reference_Objet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table compteurs;
DROP TABLE IF EXISTS `compteurs`;
CREATE TABLE `compteurs` (
  `date_acc` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `origine` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `adresse` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `parametres` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table concerne_doc;
DROP TABLE IF EXISTS `concerne_doc`;
CREATE TABLE `concerne_doc` (
  `Id_Document` int NOT NULL DEFAULT '0',
  `Reference_Objet` int NOT NULL DEFAULT '0',
  `Type_Objet` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Defaut` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`Id_Document`,`Reference_Objet`,`Type_Objet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table concerne_objet;
DROP TABLE IF EXISTS `concerne_objet`;
CREATE TABLE `concerne_objet` (
  `Evenement` int NOT NULL DEFAULT '0',
  `Reference_Objet` int NOT NULL DEFAULT '0',
  `Type_Objet` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Evenement`,`Reference_Objet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table concerne_source;
DROP TABLE IF EXISTS `concerne_source`;
CREATE TABLE `concerne_source` (
  `Ident` int NOT NULL AUTO_INCREMENT,
  `Id_Source` int NOT NULL DEFAULT '0',
  `Reference_Objet` int NOT NULL,
  `Type_Objet` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Id_Source_Tempo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Ident`),
  KEY `Id_Source` (`Id_Source`),
  KEY `Reference_Objet` (`Reference_Objet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table connexions;
DROP TABLE IF EXISTS `connexions`;
CREATE TABLE `connexions` (
  `idUtil` int NOT NULL,
  `dateCnx` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Adresse_IP` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idUtil`,`dateCnx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table contributions;
DROP TABLE IF EXISTS `contributions`;
CREATE TABLE `contributions` (
  `Contribution` int NOT NULL AUTO_INCREMENT,
  `Reference_Personne` int NOT NULL DEFAULT '0',
  `Mail` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Statut` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `Adresse_IP` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Date_Creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Contribution`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table departements;
DROP TABLE IF EXISTS `departements`;
CREATE TABLE `departements` (
  `Identifiant_zone` int NOT NULL DEFAULT '0',
  `Departement` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Nom_Depart_Min` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Zone_Mere` int NOT NULL DEFAULT '0',
  KEY `Reference` (`Identifiant_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO departements values ('271','01','Ain','0000-01-01 00:00:00','0000-01-01 00:00:00','O','266');
INSERT INTO departements values ('272','02','Aisne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','251');
INSERT INTO departements values ('273','03','Allier','0000-01-01 00:00:00','0000-01-01 00:00:00','O','267');
INSERT INTO departements values ('274','04','Alpes-de-Haute-Provence','0000-01-01 00:00:00','0000-01-01 00:00:00','O','269');
INSERT INTO departements values ('275','05','Hautes-Alpes','0000-01-01 00:00:00','0000-01-01 00:00:00','O','269');
INSERT INTO departements values ('276','06','Alpes-Maritimes','0000-01-01 00:00:00','0000-01-01 00:00:00','O','269');
INSERT INTO departements values ('277','07','Ardèche','0000-01-01 00:00:00','0000-01-01 00:00:00','O','266');
INSERT INTO departements values ('278','08','Ardennes','0000-01-01 00:00:00','0000-01-01 00:00:00','O','250');
INSERT INTO departements values ('279','09','Ariège','0000-01-01 00:00:00','0000-01-01 00:00:00','O','264');
INSERT INTO departements values ('280','10','Aube','0000-01-01 00:00:00','0000-01-01 00:00:00','O','250');
INSERT INTO departements values ('281','11','Aude','0000-01-01 00:00:00','0000-01-01 00:00:00','O','268');
INSERT INTO departements values ('282','12','Aveyron','0000-01-01 00:00:00','0000-01-01 00:00:00','O','264');
INSERT INTO departements values ('283','13','Bouches-du-Rhône','0000-01-01 00:00:00','0000-01-01 00:00:00','O','269');
INSERT INTO departements values ('284','14','Calvados','0000-01-01 00:00:00','0000-01-01 00:00:00','O','254');
INSERT INTO departements values ('285','15','Cantal','0000-01-01 00:00:00','0000-01-01 00:00:00','O','267');
INSERT INTO departements values ('286','16','Charente','0000-01-01 00:00:00','0000-01-01 00:00:00','O','262');
INSERT INTO departements values ('287','17','Charente-Maritime','0000-01-01 00:00:00','0000-01-01 00:00:00','O','262');
INSERT INTO departements values ('288','18','Cher','0000-01-01 00:00:00','0000-01-01 00:00:00','O','253');
INSERT INTO departements values ('289','19','Corrèze','0000-01-01 00:00:00','0000-01-01 00:00:00','O','265');
INSERT INTO departements values ('290','2A','Corse-du-Sud','0000-01-01 00:00:00','0000-01-01 00:00:00','O','270');
INSERT INTO departements values ('291','2B','Haute-Corse','0000-01-01 00:00:00','0000-01-01 00:00:00','O','270');
INSERT INTO departements values ('292','21','Côte-d\'Or','0000-01-01 00:00:00','0000-01-01 00:00:00','O','255');
INSERT INTO departements values ('293','22','Côtes-d\'Armor','0000-01-01 00:00:00','0000-01-01 00:00:00','O','261');
INSERT INTO departements values ('294','23','Creuse','0000-01-01 00:00:00','0000-01-01 00:00:00','O','265');
INSERT INTO departements values ('295','24','Dordogne','0000-01-01 00:00:00','2006-07-08 22:29:45','O','263');
INSERT INTO departements values ('296','25','Doubs','0000-01-01 00:00:00','0000-01-01 00:00:00','O','259');
INSERT INTO departements values ('297','26','Drôme','0000-01-01 00:00:00','0000-01-01 00:00:00','O','266');
INSERT INTO departements values ('298','27','Eure','0000-01-01 00:00:00','0000-01-01 00:00:00','O','252');
INSERT INTO departements values ('299','28','Eure-et-Loir','0000-01-01 00:00:00','0000-01-01 00:00:00','O','253');
INSERT INTO departements values ('300','29','Finistère','0000-01-01 00:00:00','0000-01-01 00:00:00','O','261');
INSERT INTO departements values ('301','30','Gard','0000-01-01 00:00:00','0000-01-01 00:00:00','O','268');
INSERT INTO departements values ('302','31','Haute-Garonne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','264');
INSERT INTO departements values ('303','32','Gers','0000-01-01 00:00:00','0000-01-01 00:00:00','O','264');
INSERT INTO departements values ('304','33','Gironde','0000-01-01 00:00:00','0000-01-01 00:00:00','O','263');
INSERT INTO departements values ('305','34','Hérault','0000-01-01 00:00:00','0000-01-01 00:00:00','O','268');
INSERT INTO departements values ('306','35','Ille-et-Vilaine','0000-01-01 00:00:00','0000-01-01 00:00:00','O','261');
INSERT INTO departements values ('307','36','Indre','0000-01-01 00:00:00','0000-01-01 00:00:00','O','253');
INSERT INTO departements values ('308','37','Indre-et-Loire','0000-01-01 00:00:00','0000-01-01 00:00:00','O','253');
INSERT INTO departements values ('309','38','Isère','0000-01-01 00:00:00','0000-01-01 00:00:00','O','266');
INSERT INTO departements values ('310','39','Jura','0000-01-01 00:00:00','0000-01-01 00:00:00','O','259');
INSERT INTO departements values ('311','40','Landes','0000-01-01 00:00:00','0000-01-01 00:00:00','O','263');
INSERT INTO departements values ('312','41','Loir-et-Cher','0000-01-01 00:00:00','0000-01-01 00:00:00','O','253');
INSERT INTO departements values ('313','42','Loire','0000-01-01 00:00:00','0000-01-01 00:00:00','O','266');
INSERT INTO departements values ('314','43','Haute-Loire','0000-01-01 00:00:00','0000-01-01 00:00:00','O','267');
INSERT INTO departements values ('315','44','Loire-Atlantique','0000-01-01 00:00:00','0000-01-01 00:00:00','O','260');
INSERT INTO departements values ('316','45','Loiret','0000-01-01 00:00:00','0000-01-01 00:00:00','O','253');
INSERT INTO departements values ('317','46','Lot','0000-01-01 00:00:00','0000-01-01 00:00:00','O','264');
INSERT INTO departements values ('318','47','Lot-et-Garonne','0000-01-01 00:00:00','2007-07-19 22:20:34','O','263');
INSERT INTO departements values ('319','48','Lozère','0000-01-01 00:00:00','0000-01-01 00:00:00','O','268');
INSERT INTO departements values ('320','49','Maine-et-Loire','0000-01-01 00:00:00','0000-01-01 00:00:00','O','260');
INSERT INTO departements values ('321','50','Manche','0000-01-01 00:00:00','0000-01-01 00:00:00','O','254');
INSERT INTO departements values ('322','51','Marne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','250');
INSERT INTO departements values ('323','52','Haute-Marne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','250');
INSERT INTO departements values ('324','53','Mayenne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','260');
INSERT INTO departements values ('325','54','Meurthe-et-Moselle','0000-01-01 00:00:00','0000-01-01 00:00:00','O','257');
INSERT INTO departements values ('326','55','Meuse','0000-01-01 00:00:00','0000-01-01 00:00:00','O','257');
INSERT INTO departements values ('327','56','Morbihan','0000-01-01 00:00:00','0000-01-01 00:00:00','O','261');
INSERT INTO departements values ('328','57','Moselle','0000-01-01 00:00:00','0000-01-01 00:00:00','O','257');
INSERT INTO departements values ('329','58','Nièvre','0000-01-01 00:00:00','0000-01-01 00:00:00','O','255');
INSERT INTO departements values ('330','59','Nord','0000-01-01 00:00:00','0000-01-01 00:00:00','O','256');
INSERT INTO departements values ('331','60','Oise','0000-01-01 00:00:00','0000-01-01 00:00:00','O','251');
INSERT INTO departements values ('332','61','Orne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','254');
INSERT INTO departements values ('333','62','Pas-de-Calais','0000-01-01 00:00:00','0000-01-01 00:00:00','O','256');
INSERT INTO departements values ('334','63','Puy-de-Dôme','0000-01-01 00:00:00','0000-01-01 00:00:00','O','267');
INSERT INTO departements values ('335','64','Pyrénées-Atlantiques','0000-01-01 00:00:00','0000-01-01 00:00:00','O','263');
INSERT INTO departements values ('336','65','Hautes-Pyrénées','0000-01-01 00:00:00','0000-01-01 00:00:00','O','264');
INSERT INTO departements values ('337','66','Pyrénées-Orientales','0000-01-01 00:00:00','0000-01-01 00:00:00','O','268');
INSERT INTO departements values ('338','67','Bas-Rhin','0000-01-01 00:00:00','2007-07-19 22:20:46','O','258');
INSERT INTO departements values ('339','68','Haut-Rhin','0000-01-01 00:00:00','2024-11-04 21:24:33','O','258');
INSERT INTO departements values ('340','69','Rhône','0000-01-01 00:00:00','0000-01-01 00:00:00','O','266');
INSERT INTO departements values ('341','70','Haute-Saône','0000-01-01 00:00:00','0000-01-01 00:00:00','O','259');
INSERT INTO departements values ('342','71','Saône-et-Loire','0000-01-01 00:00:00','0000-01-01 00:00:00','O','255');
INSERT INTO departements values ('343','72','Sarthe','0000-01-01 00:00:00','0000-01-01 00:00:00','O','260');
INSERT INTO departements values ('344','73','Savoie','0000-01-01 00:00:00','0000-01-01 00:00:00','O','266');
INSERT INTO departements values ('345','74','Haute-Savoie','0000-01-01 00:00:00','0000-01-01 00:00:00','O','266');
INSERT INTO departements values ('346','75','Paris','0000-01-01 00:00:00','0000-01-01 00:00:00','O','249');
INSERT INTO departements values ('347','76','Seine-Maritime','0000-01-01 00:00:00','2006-02-19 18:41:56','O','252');
INSERT INTO departements values ('348','77','Seine-et-Marne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','249');
INSERT INTO departements values ('349','78','Yvelines','0000-01-01 00:00:00','0000-01-01 00:00:00','O','249');
INSERT INTO departements values ('350','79','Deux-Sèvres','0000-01-01 00:00:00','0000-01-01 00:00:00','O','262');
INSERT INTO departements values ('351','80','Somme','0000-01-01 00:00:00','0000-01-01 00:00:00','O','251');
INSERT INTO departements values ('352','81','Tarn','0000-01-01 00:00:00','0000-01-01 00:00:00','O','264');
INSERT INTO departements values ('353','82','Tarn-et-Garonne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','264');
INSERT INTO departements values ('354','83','Var','0000-01-01 00:00:00','0000-01-01 00:00:00','O','269');
INSERT INTO departements values ('355','84','Vaucluse','0000-01-01 00:00:00','0000-01-01 00:00:00','O','269');
INSERT INTO departements values ('356','85','Vendée','0000-01-01 00:00:00','0000-01-01 00:00:00','O','260');
INSERT INTO departements values ('357','86','Vienne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','262');
INSERT INTO departements values ('358','87','Haute-Vienne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','265');
INSERT INTO departements values ('359','88','Vosges','0000-01-01 00:00:00','0000-01-01 00:00:00','O','257');
INSERT INTO departements values ('360','89','Yonne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','255');
INSERT INTO departements values ('361','90','Territoire de Belfort','0000-01-01 00:00:00','0000-01-01 00:00:00','O','259');
INSERT INTO departements values ('362','91','Essonne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','249');
INSERT INTO departements values ('363','92','Hauts-de-Seine','0000-01-01 00:00:00','0000-01-01 00:00:00','O','249');
INSERT INTO departements values ('364','93','Seine-Saint-Denis','0000-01-01 00:00:00','0000-01-01 00:00:00','O','249');
INSERT INTO departements values ('365','94','Val-de-Marne','0000-01-01 00:00:00','0000-01-01 00:00:00','O','249');
INSERT INTO departements values ('366','95','Val-d\'Oise','0000-01-01 00:00:00','0000-01-01 00:00:00','O','249');
INSERT INTO departements values ('367','971','Guadeloupe','0000-01-01 00:00:00','0000-01-01 00:00:00','O','245');
INSERT INTO departements values ('368','972','Martinique','0000-01-01 00:00:00','0000-01-01 00:00:00','O','246');
INSERT INTO departements values ('369','973','Guyane','0000-01-01 00:00:00','0000-01-01 00:00:00','O','247');
INSERT INTO departements values ('370','974','Réunion','0000-01-01 00:00:00','0000-01-01 00:00:00','O','248');
INSERT INTO departements values ('0','','','2004-06-26 16:30:37','2004-06-26 16:30:37','','0');
#
# Traitement de la table depots;
DROP TABLE IF EXISTS `depots`;
CREATE TABLE `depots` (
  `Ident` int NOT NULL AUTO_INCREMENT,
  `Nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Ident_Depot_Tempo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Ident`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table documents;
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `Id_Document` int NOT NULL AUTO_INCREMENT,
  `Nature_Document` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Titre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Nom_Fichier` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Diff_Internet` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `Date_Creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Id_Type_Document` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id_Document`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table evenements;
DROP TABLE IF EXISTS `evenements`;
CREATE TABLE `evenements` (
  `Reference` int NOT NULL AUTO_INCREMENT,
  `Identifiant_zone` int DEFAULT '0',
  `Identifiant_Niveau` int DEFAULT NULL,
  `Code_Type` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Titre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '-',
  `Debut` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Fin` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO evenements values(null,0,0,"AC3U","la Revue Française de Généalogie teste Geneamania dans son numéro d'été","20150107GL","20150107GL",current_timestamp,current_timestamp,"V");
INSERT INTO evenements values(null,0,0,"AC3U","sortie de la version 2022.02","20221115GL","20221115GL",current_timestamp,current_timestamp,"V");
#
# Traitement de la table filiations;
DROP TABLE IF EXISTS `filiations`;
CREATE TABLE `filiations` (
  `Enfant` int NOT NULL DEFAULT '0',
  `Pere` int DEFAULT '0',
  `Mere` int DEFAULT '0',
  `Rang` int DEFAULT '0',
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  UNIQUE KEY `Reference` (`Enfant`,`Pere`,`Mere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table general;
DROP TABLE IF EXISTS `general`;
CREATE TABLE `general` (
  `Environnement` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Nom` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '???',
  `Version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Lettre_B` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Image_Fond` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Coul_Fond_Table` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Adresse_Mail` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'support@geneamania.net',
  `Image_Arbre_Asc` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Affiche_Mar_Arbre_Asc` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Affiche_Annee` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `Comportement` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'C',
  `Degrade` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'R',
  `Image_Barre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'bar_off_bleu.gif',
  `Date_Modification` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Coul_Lib` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '#B8A165',
  `Coul_Val` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '#B1A980',
  `Coul_Bord` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '#49453B',
  `Coul_Paires` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '#B3A17E',
  `Coul_Impaires` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '#C2BA98',
  `Pivot_Masquage` smallint DEFAULT '9999',
  `Image_Index` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Font_Pdf` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Arial',
  `Coul_PDF` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '#000000',
  `Base_Vide` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO general values ('L','???','2025.03 beta 1','B_Gothique.png','vert_base.jpg','#92826D','support@geneamania.net','arbre_asc_hor_carre.png', 'O', 'N', 'C', 'V', 'bar_off_vert_fonce.gif', current_timestamp,'#DCDCDC', '#F5F5F5', '#49453B', '#EFEFEF', '#FEFEFE',9999, null, 'Arial','#000000', true);
#
# Traitement de la table images;
DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `ident_image` int NOT NULL DEFAULT '0',
  `Reference` int NOT NULL DEFAULT '0',
  `Type_Ref` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `nom` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Defaut` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `Titre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Diff_Internet_Img` enum('o','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'o',
  UNIQUE KEY `ident_image` (`ident_image`),
  KEY `Reference` (`Reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table liens;
DROP TABLE IF EXISTS `liens`;
CREATE TABLE `liens` (
  `Ref_lien` int NOT NULL DEFAULT '0',
  `type_lien` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `URL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Sur_Accueil` tinyint(1) NOT NULL DEFAULT '0',
  `Diff_Internet` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `Ref_lien` (`Ref_lien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO liens values ('0','développement','Généamania','http://www.geneamania.net','Geneamania.png','0000-01-01 00:00:00','2007-07-09 13:55:11','O','0','0');
#
# Traitement de la table liste_diffusion;
DROP TABLE IF EXISTS `liste_diffusion`;
CREATE TABLE `liste_diffusion` (
  `Adresse_IP` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Mail` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Diffusion_Active` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Date_Creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Traite` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'O',
  KEY `Adresse_IP` (`Adresse_IP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table niveaux_zones;
DROP TABLE IF EXISTS `niveaux_zones`;
CREATE TABLE `niveaux_zones` (
  `Identifiant_Niveau` int NOT NULL DEFAULT '0',
  `Libelle_Niveau` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  KEY `Reference` (`Identifiant_Niveau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO niveaux_zones values ('1','Pays');
INSERT INTO niveaux_zones values ('2','Région');
INSERT INTO niveaux_zones values ('3','Département');
INSERT INTO niveaux_zones values ('4','Ville');
INSERT INTO niveaux_zones values ('5','Subdivision');
#
# Traitement de la table noms_famille;
DROP TABLE IF EXISTS `noms_famille`;
CREATE TABLE `noms_famille` (
  `idNomFam` int NOT NULL AUTO_INCREMENT,
  `nomFamille` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `codePhonetique` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  PRIMARY KEY (`idNomFam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table noms_personnes;
DROP TABLE IF EXISTS `noms_personnes`;
CREATE TABLE `noms_personnes` (
  `idPers` int NOT NULL DEFAULT '0',
  `idNom` int NOT NULL DEFAULT '0',
  `princ` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `comment` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`idPers`,`idNom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table participe;
DROP TABLE IF EXISTS `participe`;
CREATE TABLE `participe` (
  `Evenement` int NOT NULL DEFAULT '0',
  `Personne` int NOT NULL DEFAULT '0',
  `Code_Role` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Debut` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Fin` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Pers_Principal` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Identifiant_zone` int NOT NULL DEFAULT '0',
  `Identifiant_Niveau` int NOT NULL DEFAULT '0',
  `Dans_Etiquette_GeneGraphe` enum('o','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`Evenement`,`Personne`,`Code_Role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table pays;
DROP TABLE IF EXISTS `pays`;
CREATE TABLE `pays` (
  `Identifiant_zone` int NOT NULL DEFAULT '0',
  `Code_Pays_ISO_Alpha` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Code_Pays_ISO_Alpha3` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Code_Pays_ISO_Num` int NOT NULL DEFAULT '0',
  `Nom_Pays` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Satut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  KEY `Reference` (`Identifiant_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO pays values ('1','AD','AND','20','ANDORRE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('2','AE','ARE','784','EMIRATS ARABES UNIS','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('3','AF','AFG','4','AFGHANISTAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('4','AG','ATG','28','ANTIGUA-ET-BARBUDA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('5','AI','AIA','660','ANGUILLA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('6','AL','ALB','8','ALBANIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('7','AM','ARM','51','ARMENIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('8','AN','ANT','530','ANTILLES NEERLANDAISES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('9','AO','AGO','24','ANGOLA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('10','AR','ARG','32','ARGENTINE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('11','AS','ASM','16','SAMOA AMERICAINES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('12','AT','AUT','40','AUTRICHE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('13','AU','AUS','36','AUSTRALIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('14','AW','ABW','533','ARUBA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('15','AZ','AZE','31','AZERBAIDJAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('16','BA','BIH','70','BOSNIE-HERZEGOVINE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('17','BB','BRB','52','BARBADE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('18','BD','BGD','50','BANGLADESH','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('19','BE','BEL','56','BELGIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('20','BF','BFA','854','BURKINA FASO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('21','BG','BGR','100','BULGARIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('22','BH','BHR','48','BAHREIN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('23','BI','BDI','108','BURUNDI','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('24','BJ','BEN','204','BENIN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('25','BM','BMU','60','BERMUDES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('26','BN','BRN','96','BRUNEI DARUSSALAM','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('27','BO','BOL','68','BOLIVIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('28','BR','BRA','76','BRESIL','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('29','BS','BHS','44','BAHAMAS','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('30','BT','BTN','64','BHOUTAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('31','BV','BVT','74','BOUVET, ILE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('32','BW','BWA','72','BOTSWANA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('33','BY','BLR','112','BELARUS','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('34','BZ','BLZ','84','BELIZE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('35','CA','CAN','124','CANADA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('36','CC','CCK','166','COCOS (KEELING), ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('37','CD','COD','179','REPUBLIQUE DEMOCRATIQUE DU CONGO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('38','CF','CAF','140','CENTRAFRICAINE, REPUBLIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('39','CG','COG','178','CONGO, REPUBLIQUE DU','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('40','CH','CHE','756','SUISSE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('41','CI','CIV','384','C TE D\'IVOIRE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('42','CK','COK','184','COOK, ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('43','CL','CHL','152','CHILI','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('44','CM','CMR','120','CAMEROUN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('45','CN','CHN','156','CHINE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('46','CO','COL','170','COLOMBIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('47','CR','CRI','188','COSTA RICA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('48','CU','CUB','192','CUBA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('49','CV','CPV','132','CAP-VERT','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('50','CX','CXR','162','CHRISTMAS, ILE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('51','CY','CYP','196','CHYPRE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('52','CZ','CZE','203','TCHEQUE, REPUBLIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('53','DE','DEU','276','ALLEMAGNE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('54','DJ','DJI','262','DJIBOUTI','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('55','DK','DNK','208','DANEMARK','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('56','DM','DMA','212','DOMINIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('57','DO','DOM','214','DOMINICAINE, REPUBLIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('58','DZ','DZA','12','ALGERIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('59','EC','ECU','218','EQUATEUR','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('60','EE','EST','233','ESTONIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('61','EG','EGY','818','EGYPTE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('62','EH','ESH','732','SAHARA OCCIDENTAL','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('63','ER','ERI','232','ERYTHREE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('64','ES','ESP','724','ESPAGNE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('65','ET','ETH','231','ETHIOPIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('66','FI','FIN','246','FINLANDE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('67','FJ','FJI','242','FIDJI, LES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('68','FK','FLK','238','FALKLAND, ILES (MALVINAS)','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('69','FM','FSM','583','MICRONESIE (ETATS FEDERES DE)','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('70','FO','FRO','234','FEROE, ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('71','FR','FRA','250','FRANCE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('72','FX','FXX','249','FRANCE METROPOLITAINE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('73','GA','GAB','266','GABON','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('74','GB','GBR','826','ROYAUME-UNI','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('75','GD','GRD','308','GRENADE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('76','GE','GEO','268','GEORGIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('77','GF','GUF','254','GUYANE FRANCAISE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('78','GG','GBG','830','GUERNESEY','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('79','GH','GHA','288','GHANA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('80','GI','GIB','292','GIBRALTAR','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('81','GJ','GJO','9','GAZA ET JERICHO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('82','GL','GRL','304','GROENLAND','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('83','GM','GMB','270','GAMBIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('84','GN','GIN','324','GUINEE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('85','GP','GLP','312','GUADELOUPE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('86','GQ','GNQ','226','GUINEE EQUATORIALE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('87','GR','GRC','300','GRECE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('88','GS','SGS','239','GEORGIE DU SUD ET LES ILES SANDWICH DU SUD','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('89','GT','GTM','320','GUATEMALA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('90','GU','GUM','316','GUAM','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('91','GW','GNB','624','GUINEE-BISSAU','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('92','GY','GUY','328','GUYANA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('93','HK','HKG','344','HONG-KONG','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('94','HM','HMD','334','HEARD ET ILES MCDONALD, ILE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('95','HN','HND','340','HONDURAS','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('96','HR','HRV','191','CROATIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('97','HT','HTI','332','HAITI','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('98','HU','HUN','348','HONGRIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('99','ID','IDN','360','INDONESIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('100','IE','IRL','372','IRLANDE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('101','IL','ISR','376','ISRAEL','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('102','IM','GBM','830','MAN (ILE DE)','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('103','IN','IND','356','INDE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('104','IO','IOT','86','OCEAN INDIEN, TERRITOIRE BRITANNIQUE DE L\'','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('105','IQ','IRQ','368','IRAQ','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('106','IR','IRN','364','IRAN (REPUBLIQUE ISLAMIQUE D\')','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('107','IS','ISL','352','ISLANDE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('108','IT','ITA','380','ITALIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('109','JE','GBJ','830','JERSEY','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('110','JM','JAM','388','JAMAIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('111','JO','JOR','400','JORDANIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('112','JP','JPN','392','JAPON','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('113','KE','KEN','404','KENYA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('114','KG','KGZ','417','KIRGHIZISTAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('115','KH','KHM','116','CAMBODGE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('116','KI','KIR','296','KIRIBATI, ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('117','KM','COM','174','COMORES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('118','KN','KNA','659','SAINT-KITTS-ET-NEVIS','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('119','KP','PRK','408','COREE DU NORD','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('120','KR','KOR','410','COREE DU SUD','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('121','KW','KWT','414','KOWEIT','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('122','KY','CYM','136','CAIMANES, ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('123','KZ','KAZ','398','KAZAKHSTAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('124','LA','LAO','418','LAOS, REPUBLIQUE DEMOCRATIQUE POPULAIRE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('125','LB','LBN','422','LIBAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('126','LC','LCA','662','SAINTE-LUCIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('127','LI','LIE','438','LIECHTENSTEIN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('128','LK','LKA','144','SRI LANKA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('129','LR','LBR','430','LIBERIA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('130','LS','LSO','426','LESOTHO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('131','LT','LTU','440','LITUANIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('132','LU','LUX','442','LUXEMBOURG','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('133','LV','LVA','428','LETTONIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('134','LY','LBY','434','LIBYENNE, JAMAHIRIYA ARABE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('135','MA','MAR','504','MAROC','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('136','MC','MCO','492','MONACO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('137','MD','MDA','498','MOLDOVA, REPUBLIQUE DE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('138','MG','MDG','450','MADAGASCAR','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('139','MH','MHL','584','MARSHALL, ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('140','MK','MKD','807','EX-REPUBLIQUE YOUGOSLAVE DE MACEDOINE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('141','ML','MLI','466','MALI','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('142','MM','MMR','104','MYANMAR','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('143','MN','MNG','496','MONGOLIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('144','MO','MAC','446','MACAO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('145','MP','MNP','580','MARIANNES DU NORD, ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('146','MQ','MTQ','474','MARTINIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('147','MR','MRT','478','MAURITANIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('148','MS','MSR','500','MONTSERRAT','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('149','MT','MLT','470','MALTE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('150','MU','MUS','480','MAURICE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('151','MV','MDV','462','MALDIVES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('152','MW','MWI','454','MALAWI','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('153','MX','MEX','484','MEXIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('154','MY','MYS','458','MALAISIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('155','MZ','MOZ','508','MOZAMBIQUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('156','NA','NAM','516','NAMIBIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('157','NC','NCL','540','NOUVELLE-CALEDONIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('158','NE','NER','562','NIGER','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('159','NF','NFK','574','NORFOLK, ILE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('160','NG','NGA','566','NIGERIA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('161','NI','NIC','558','NICARAGUA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('162','NL','NLD','528','PAYS-BAS','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('163','NO','NOR','578','NORVEGE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('164','NP','NPL','524','NEPAL','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('165','NR','NRU','520','NAURU','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('166','NU','NIU','570','NIOUE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('167','NZ','NZL','554','NOUVELLE-ZELANDE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('168','OM','OMN','512','OMAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('169','PA','PAN','591','PANAMA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('170','PE','PER','604','PEROU','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('171','PF','PYF','258','POLYNESIE FRANCAISE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('172','PG','PNG','598','PAPOUASIE-NOUVELLE-GUINEE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('173','PH','PHL','608','PHILIPPINES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('174','PK','PAK','586','PAKISTAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('175','PL','POL','616','POLOGNE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('176','PM','SPM','666','SAINT-PIERRE-ET-MIQUELON','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('177','PN','PCN','612','PITCAIRN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('178','PR','PRI','630','PORTO RICO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('179','PT','PRT','620','PORTUGAL','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('180','PW','PLW','585','PALAU','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('181','PY','PRY','600','PARAGUAY','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('182','QA','QAT','634','QATAR','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('183','RE','REU','638','REUNION','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('184','RO','ROM','642','ROUMANIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('185','RU','RUS','643','RUSSIE, FEDERATION DE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('186','RW','RWA','646','RWANDA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('187','SA','SAU','682','ARABIE SAOUDITE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('188','SB','SLB','90','SALOMON, ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('189','SC','SYC','690','SEYCHELLES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('190','SD','SDN','736','SOUDAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('191','SE','SWE','752','SUEDE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('192','SG','SGP','702','SINGAPOUR','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('193','SH','SHN','654','SAINTE-HELENE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('194','SI','SVN','705','SLOVENIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('195','SJ','SJM','744','SVALBARD ET ILES JAN MAYEN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('196','SK','SVK','703','SLOVAQUIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('197','SL','SLE','694','SIERRA LEONE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('198','SM','SMR','674','SAINT-MARIN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('199','SN','SEN','686','SENEGAL','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('200','SO','SOM','706','SOMALIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('201','SR','SUR','740','SURINAME','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('202','ST','STP','678','SAO TOME-ET-PRINCIPE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('203','SV','SLV','222','SALVADOR','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('204','SY','SYR','760','SYRIENNE, REPUBLIQUE ARABE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('205','SZ','SWZ','748','SWAZILAND','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('206','TC','TCA','796','TURKS ET CAIQUES, ILES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('207','TD','TCD','148','TCHAD','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('208','TF','ATF','260','TERRES AUSTRALES FRANCAISES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('209','TG','TGO','768','TOGO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('210','TH','THA','764','THAILANDE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('211','TJ','TJK','762','TADJIKISTAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('212','TK','TKL','772','TOKELAOU','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('213','TM','TKM','795','TURKMENISTAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('214','TN','TUN','788','TUNISIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('215','TO','TON','776','TONGA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('216','TP','TMP','626','TIMOR ORIENTAL','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('217','TR','TUR','792','TURQUIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('218','TT','TTO','780','LA TRINITE-ET-TOBAGO','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('219','TV','TUV','798','TUVALU','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('220','TW','TWN','158','TAIWAN, PROVINCE DE CHINE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('221','TZ','TZA','834','TANZANIE, REPUBLIQUE-UNIE DE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('222','UA','UKR','804','UKRAINE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('223','UG','UGA','800','OUGANDA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('224','UM','UMI','581','ILES MINEURES ELOIGNEES DES ETATS-UNIS','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('225','US','USA','840','ETATS-UNIS','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('226','UY','URY','858','URUGUAY','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('227','UZ','UZB','860','OUZBEKISTAN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('228','VA','VAT','336','VATICAN, LE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('229','VC','VCT','670','SAINT-VINCENT-ET-LES GRENADINES','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('230','VE','VEN','862','VENEZUELA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('231','VG','VGB','92','ILES VIERGES (BRITANNIQUES ET MONSERRAT)','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('232','VI','VIR','850','ILES VIERGES (ETATS-UNIS)','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('233','VN','VNM','704','VIET NAM','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('234','VU','VUT','548','VANUATU','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('235','WF','WLF','876','WALLIS ET FUTUNA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('236','WS','WSM','882','SAMOA','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('237','XZ','XXG','138','ZONE FRANC (Etats africains de la)','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('238','X2','X2I','126','PAYS INDETERMINES DE L\'INTRA UEM (PAYS DE L\'UEM)','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('239','YE','YEM','887','YEMEN','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('240','YT','MYT','175','MAYOTTE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('241','YU','YUG','891','YOUGOSLAVIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('242','ZA','ZAF','710','AFRIQUE DU SUD','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('243','ZM','ZMB','894','ZAMBIE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('244','ZW','ZWE','716','ZIMBABWE','0000-01-01 00:00:00','0000-01-01 00:00:00','V');
INSERT INTO pays values ('0','','','0','','2010-01-04 22:16:38','2010-01-04 22:16:38','V');
#
# Traitement de la table personnes;
DROP TABLE IF EXISTS `personnes`;
CREATE TABLE `personnes` (
  `Reference` int NOT NULL DEFAULT '0',
  `Nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Prenoms` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Sexe` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Numero` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Ne_le` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Decede_Le` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Ville_Naissance` int DEFAULT NULL,
  `Ville_Deces` int DEFAULT NULL,
  `Diff_Internet` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `idNomFam` int DEFAULT NULL,
  `Categorie` int NOT NULL DEFAULT '0',
  `Surnom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Reference`),
  KEY `Nom` (`Nom`),
  KEY `idNomFam` (`idNomFam`),
  KEY `numero` (`Numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO personnes values ('0','','',NULL,'','','','0','0','N','2004-07-11 22:46:33','2004-07-11 22:46:33','','0','0','');
#
# Traitement de la table regions;
DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `Identifiant_zone` int NOT NULL DEFAULT '0',
  `Region` int NOT NULL DEFAULT '0',
  `Nom_Region_Min` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Zone_Mere` int NOT NULL DEFAULT '0',
  KEY `Reference` (`Identifiant_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO regions values ('245','1','Guadeloupe','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('246','2','Martinique','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('247','3','Guyane','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('248','4','Réunion','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('249','11','Ile-de-France','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('250','21','Champagne-Ardenne','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('251','22','Picardie','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('252','23','Haute-Normandie','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('253','24','Centre','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('254','25','Basse-Normandie','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('255','26','Bourgogne','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('256','31','Nord-Pas-de-Calais','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('257','41','Lorraine','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('258','42','Alsace','0000-01-01 00:00:00','2006-02-21 01:17:52','O','71');
INSERT INTO regions values ('259','43','Franche-Comté','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('260','52','Pays de la Loire','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('261','53','Bretagne','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('262','54','Poitou-Charentes','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('263','72','Aquitaine','0000-01-01 00:00:00','2007-07-21 22:03:03','','71');
INSERT INTO regions values ('264','73','Midi-Pyrénées','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('265','74','Limousin','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('266','82','Rhône-Alpes','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('267','83','Auvergne','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('268','91','Languedoc-Roussillon','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('269','93','Provence-Alpes-Côte d\'Azur','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('270','94','Corse','0000-01-01 00:00:00','0000-01-01 00:00:00','V','71');
INSERT INTO regions values ('0','0','','2010-01-04 22:16:38','2010-01-04 22:16:38','V','0');
#
# Traitement de la table relation_personnes;
DROP TABLE IF EXISTS `relation_personnes`;
CREATE TABLE `relation_personnes` (
  `Personne_1` int NOT NULL DEFAULT '0',
  `Personne_2` int NOT NULL DEFAULT '0',
  `Code_Role` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Debut` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Fin` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Principale` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'O',
  PRIMARY KEY (`Personne_1`,`Code_Role`,`Personne_2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table requetes;
DROP TABLE IF EXISTS `requetes`;
CREATE TABLE `requetes` (
  `Reference` int NOT NULL AUTO_INCREMENT,
  `Titre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Criteres` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Code_SQL` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table roles;
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `Code_Role` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Libelle_Role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Symetrie` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'O',
  `Libelle_Inv_Role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Code_Role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO roles values ('','-- Défaut --','O','');
#
# Traitement de la table sources;
DROP TABLE IF EXISTS `sources`;
CREATE TABLE `sources` (
  `Ident` int NOT NULL AUTO_INCREMENT,
  `Titre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Auteur` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Classement` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Ident_Depot` int NOT NULL,
  `Ident_Depot_Tempo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Cote` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Adresse_Web` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Fiabilite_Source` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Ident_Source_Tempo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Ident`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table subdivisions;
DROP TABLE IF EXISTS `subdivisions`;
CREATE TABLE `subdivisions` (
  `Identifiant_zone` int NOT NULL DEFAULT '0',
  `Nom_Subdivision` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '-',
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Zone_Mere` int NOT NULL DEFAULT '0',
  `Latitude` float DEFAULT NULL,
  `Longitude` float DEFAULT NULL,
  PRIMARY KEY (`Identifiant_zone`),
  KEY `Zone_Mere` (`Zone_Mere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table types_doc;
DROP TABLE IF EXISTS `types_doc`;
CREATE TABLE `types_doc` (
  `Id_Type_Document` int NOT NULL AUTO_INCREMENT,
  `Libelle_Type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`Id_Type_Document`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table types_evenement;
DROP TABLE IF EXISTS `types_evenement`;
CREATE TABLE `types_evenement` (
  `Code_Type` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Libelle_Type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Code_Modifiable` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Objet_Cible` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Unicite` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `Type_Gedcom` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`Code_Type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO types_evenement values ('AC3U','Actualités','N','-','M','N');
INSERT INTO types_evenement values ('ADOP','Adoption','N','F','U','O');
INSERT INTO types_evenement values ('ANUL','Nullité du mariage','N','U','U','O');
INSERT INTO types_evenement values ('BAPM','Baptême non mormon','N','P','U','O');
INSERT INTO types_evenement values ('BARM','Bar mitzvah','N','P','U','O');
INSERT INTO types_evenement values ('BASM','Bas mitzah','N','P','U','O');
INSERT INTO types_evenement values ('BLES','Bénédiction religieuse','N','P','M','O');
INSERT INTO types_evenement values ('BURI','Sépulture','N','P','U','O');
INSERT INTO types_evenement values ('CAST','Rang ou statut','N','P','M','O');
INSERT INTO types_evenement values ('CENS','Recensement de population','N','P','M','O');
INSERT INTO types_evenement values ('CHR','Baptême religieux','N','P','U','O');
INSERT INTO types_evenement values ('CHRA','Baptême adulte non mormon','N','P','U','O');
INSERT INTO types_evenement values ('CONF','Confirmation (religieuse)','N','P','U','O');
INSERT INTO types_evenement values ('CREM','Crémation','N','P','U','O');
INSERT INTO types_evenement values ('DIV','Divorce','N','U','M','O');
INSERT INTO types_evenement values ('DIVF','Dossier de divorce d\'une personne','N','U','M','O');
INSERT INTO types_evenement values ('DSCR','Description physique','N','P','M','O');
INSERT INTO types_evenement values ('EDUC','Niveau d\'instruction','N','P','M','O');
INSERT INTO types_evenement values ('EMIG','Emigration','N','P','M','O');
INSERT INTO types_evenement values ('ENGA','Fiançailles','N','U','U','O');
INSERT INTO types_evenement values ('EVEN','Evènement','N','P','M','O');
INSERT INTO types_evenement values ('FACT','Fait ou caractéristique','N','P','M','O');
INSERT INTO types_evenement values ('FCOM','Première communion','N','P','U','O');
INSERT INTO types_evenement values ('GRAD','Diplôme ou certificat','N','P','M','O');
INSERT INTO types_evenement values ('IDNO','Identification externe','N','P','M','O');
INSERT INTO types_evenement values ('IMMI','Immigration','N','P','M','O');
INSERT INTO types_evenement values ('MARB','Publication des bans','N','U','U','O');
INSERT INTO types_evenement values ('MARL','Autorisation légale de mariage','N','U','U','O');
INSERT INTO types_evenement values ('MARS','Convention ou contrat avant mariage','N','U','U','O');
INSERT INTO types_evenement values ('NATI','Nationalité','N','P','M','O');
INSERT INTO types_evenement values ('NATU','Naturalisation','N','P','M','O');
INSERT INTO types_evenement values ('OCCU','Profession','N','P','M','O');
INSERT INTO types_evenement values ('ORDN','Ordination religieuse','N','P','U','O');
INSERT INTO types_evenement values ('PROB','Validation d\'un testament','N','P','M','O');
INSERT INTO types_evenement values ('PROP','Bien ou possession','N','P','M','O');
INSERT INTO types_evenement values ('RELI','Religion','N','P','M','O');
INSERT INTO types_evenement values ('RESI','Domicile','N','P','M','O');
INSERT INTO types_evenement values ('RETI','Retraite','N','P','U','O');
INSERT INTO types_evenement values ('SSN','Numéro de sécurité sociale','N','P','U','O');
INSERT INTO types_evenement values ('TITL','Titre de noblesse ou honorifique','N','P','M','O');
INSERT INTO types_evenement values ('WILL','Testament','N','P','M','O');
#
# Traitement de la table unions;
DROP TABLE IF EXISTS `unions`;
CREATE TABLE `unions` (
  `Reference` int NOT NULL AUTO_INCREMENT,
  `Conjoint_1` int NOT NULL DEFAULT '0',
  `Conjoint_2` int NOT NULL DEFAULT '0',
  `Maries_Le` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Ville_Mariage` int DEFAULT NULL,
  `Date_K` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Notaire_K` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Ville_Notaire` int DEFAULT '0',
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Reference`,`Conjoint_1`,`Conjoint_2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Traitement de la table utilisateurs;
DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE `utilisateurs` (
  `idUtil` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(40) NOT NULL,
  `codeUtil` varchar(35) NOT NULL,
  `motPasseUtil` char(64) NOT NULL,
  `niveau` char(1) NOT NULL DEFAULT 'I',
  `Adresse` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`idUtil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO utilisateurs values(null, 'invité', 'invité', '', 'I', null);
INSERT INTO utilisateurs values(null,'Gestionnaire', 'gestionnaire', '63e86b1e912220bdf2cafb57f5ad38673c104fa002f6d1139c3a00c459c048ed', 'G',null);
# gestionnaire de la base : gestionnaire/gestionnaire 
#
# Traitement de la table villes;
DROP TABLE IF EXISTS `villes`;
CREATE TABLE `villes` (
  `Identifiant_zone` int NOT NULL DEFAULT '0',
  `Nom_Ville` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '-',
  `Code_Postal` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Date_Creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `Date_Modification` datetime DEFAULT CURRENT_TIMESTAMP,
  `Statut_Fiche` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Zone_Mere` int NOT NULL DEFAULT '0',
  `Latitude` float DEFAULT NULL,
  `Longitude` float DEFAULT NULL,
  PRIMARY KEY (`Identifiant_zone`),
  KEY `Zone_Mere` (`Zone_Mere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO villes values ('0','','','2004-06-26 16:26:42','2004-06-26 16:26:42','','0','0','0');
# ------- fin ------------
