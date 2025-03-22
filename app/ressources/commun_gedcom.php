<?php

// Code commun générique pour le traitement des Gedcom

// Libellé pour la France pour optimisation
$lib_FR = '';
$ref_FR = 0;

$n_villes = nom_table('villes');

// Libellé et référence pour la France
function get_Lib_FR()
{
    global $lib_FR, $ref_FR;
    $sql_FR = 'select Identifiant_zone, Nom_Pays from ' . nom_table('pays') . ' where Code_Pays_ISO_Alpha3 = "FRA" limit 1';
    if ($res_FR = lect_sql($sql_FR)) {
        if ($enr = $res_FR->fetch(PDO::FETCH_NUM)) {
            $ref_FR = $enr[0];
            $lib_FR = $enr[1];
        }
        $res_FR->closeCursor();
    }
}

// Libellé d'une ville pour Gedcom
function lib_villeG($num_ville)
{
    global $Z_Mere, $lib_FR, $ref_FR, $n_villes;
    $libelle_ville = '';
    $libelle_depart = '';
    $libelle_region = '';
    $libelle_pays = '';
    $CP = '';
    $Z_Mere = 0;

    if ($num_ville != 0) {
        $sql = 'select nom_ville, Zone_Mere, Code_Postal from ' . $n_villes . ' where identifiant_zone = ' . $num_ville . ' limit 1';
        if ($res = lect_sql($sql)) {
            if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                $libelle_ville = $enr[0];
                $Z_Mere        = $enr[1];
                $CP            = $enr[2];
            }
        }
    }
    if ($Z_Mere != 0) {
        $libelle_depart = lib_departement($Z_Mere, 'n');
        if ($Z_Mere != 0) {
            $libelle_region = lib_region($Z_Mere, 'n');
            if ($Z_Mere != 0) {
                if ($Z_Mere == $ref_FR)
                    $libelle_pays = $lib_FR;
                else
                    $libelle_pays = lib_pays($Z_Mere, 'n');
            }
        }
    }
    return $libelle_ville . ', ' . $CP . ', ' . $libelle_depart . ', ' . $libelle_region . ', ' . $libelle_pays;
}

/* Etend une date pour une sortie GEDCOM */
function Etend_date_GedCom($LaDate)
{
    global $Mois_Abr;
    $Date_Ged = '';
    if (strlen($LaDate) == 10) {
        switch ($LaDate[9]) {
            case 'E':
                $Precision = 'ABT ';
                break;
            case 'A':
                $Precision = 'BEF ';
                break;
            case 'P':
                $Precision = 'AFT ';
                break;
            default:
                $Precision = '';
        }
        $Jour  = substr($LaDate, 6, 2);
        $Mois  = substr($LaDate, 4, 2);
        $Annee = substr($LaDate, 0, 4);
        $Date_Ged = $Precision . $Jour . ' ' . $Mois_Abr[intval($Mois) - 1] . ' ' . $Annee;
    }
    return $Date_Ged;
}

// Début requête extraction personne pour Gedcom
function Debut_Ext_Pers_Ged()
{
    return 'select ' .
        'Reference,' . 'Nom,' . 'Prenoms,' . 'Sexe,' .
        //'Numero,'.
        'Ne_le,' .
        //'B_Le,'.
        'Decede_Le,' .
        //'Profession,'.
        'Ville_Naissance,' . 'Ville_Deces,' .
        'Diff_Internet,' .
        //'Date_Creation,'.
        'DATE_FORMAT(Date_Modification,\'%d %b %Y\'),' .  // jour mois abrégé et année
        'DATE_FORMAT(Date_Modification,\'%H:%i:%S \')' .
        ',Surnom' .    // [11]
        //'Statut_Fiche'.
        //', Divers'.  // Passé dans une autre table
        ' from ' . nom_table('personnes') . ' where ';
}

// Entête d'un fichier Gedcom
function Entete_Gedcom($fic, $nomfic)
{
    global $Version, $cr, $Adresse_Mail, $def_enc;
    $type_car = 'ANSI';
    if ($def_enc == 'UTF-8')
        $type_car = 'UTF-8';
    fwrite($fic, "0 HEAD$cr");
    fwrite($fic, "1 SOUR Geneamania$cr");
    fwrite($fic, "2 VERS $Version$cr");
    fwrite($fic, "2 NAME Geneamania-PHP$cr");
    fwrite($fic, "2 CORP JLS Corp$cr");
    fwrite($fic, "3 ADR $Adresse_Mail$cr");
    fwrite($fic, "1 DATE " . date("d M Y") . "$cr");        //date de création du fichier
    fwrite($fic, "1 CHAR $type_car$cr");                //jeu de caractères utilisé					
    fwrite($fic, "1 SUBM @S0@$cr");                    //auteur  (pointeur vers l'auteur @S0@)
    fwrite($fic, "1 FILE $nomfic$cr");                 //nom du fichier GEDCOM
    fwrite($fic, "1 GEDC$cr");                        //identification GEDCOM
    fwrite($fic, "2 VERS 5.5$cr");                    //version utilisée
    fwrite($fic, "2 FORM LINEAGE_LINKED$cr");        //format utilisé
    fwrite($fic, "1 PLAC$cr");
    fwrite($fic, "2 FORM " . LG_GEDCOM_FORM . "$cr");
    fwrite($fic, "0 @S0@ SUBM$cr");                    //définition de l'auteur @S0@
    fwrite($fic, "1 NAME Jean-Luc SERVIN$cr");
}

// Données d'une personne dans un fichier Gedcom
function Personne_Gedcom($fic, $enr, $leger = false)
{
    global $cr, $Commentaire, $Environnement, $Diffusion_Commentaire_Internet;
    fwrite($fic, '0 @I' . $enr[0] . '@ INDI' . $cr);
    fwrite($fic, '1 NAME ' . $enr[2] . '/' . $enr[1] . '/' . $cr);
    $surnom = $enr[11];
    $sexe = $enr[3];
    if ($surnom != '') fwrite($fic, '1 NICK ' . $surnom . $cr);
    if ($sexe != '') fwrite($fic, '1 SEX ' . strtoupper($sexe) . $cr);
    // Naissance
    $LaVilleN = $enr[6];
    $LaDate   = $enr[4];
    if (($LaVilleN != 0) or ($LaDate != '')) {
        fwrite($fic, '1 BIRT' . $cr);
        if ($LaDate != '') fwrite($fic, '2 DATE ' . Etend_date_GedCom($LaDate) . $cr);
        if ($LaVilleN != 0) {
            $Lib_Ville = lib_villeG($LaVilleN);
            fwrite($fic, '2 PLAC ' . $Lib_Ville . $cr);
        }
    }
    // Décès
    $LaVilleD = $enr[7];
    $LaDate   = $enr[5];
    if (($LaVilleD != 0) or ($LaDate != '')) {
        fwrite($fic, '1 DEAT' . $cr);
        if ($LaDate != '') fwrite($fic, '2 DATE ' . Etend_date_GedCom($LaDate) . $cr);
        if ($LaVilleD != 0) {
            // On ne recherche le libellé de la ville que sur changement
            if ($LaVilleD != $LaVilleN) $Lib_Ville = lib_villeG($LaVilleD);
            fwrite($fic, '2 PLAC ' . $Lib_Ville . $cr);
        }
    }

    // En mode export léger, les notes et évènements ne sont pas exportés
    if (!$leger) {
        // Notes pour la personne
        if (Rech_Commentaire($enr[0], 'P')) {
            if (($Environnement == 'L') or ($Diffusion_Commentaire_Internet == 'O')) {
                Ecrit_Note_Gedcom($Commentaire, $fic);
            }
        }

        // Evènements liés Ã  la personne
        $req = 'select Identifiant_zone, Identifiant_Niveau, Code_Type, Debut, Fin, Titre, Reference from ' . nom_table('evenements') .
            ' where Code_Type in (SELECT Code_Type from ' . nom_table('types_evenement') .
            ' where Type_Gedcom = \'O\' and Objet_Cible = \'P\')' .
            ' and Reference in (select Evenement from ' . nom_table('participe') . ' where Personne = ' . $enr[0] . ')';
        if ($resEv = lect_sql($req)) {
            while ($enrEv = $resEv->fetch(PDO::FETCH_NUM)) {
                //if ($enrEv[2] == 'OCCU') fwrite($fic,'1 '.$enrEv[2].' '.$enrEv[5].$cr);
                //else                     fwrite($fic,'1 '.$enrEv[2].$cr);
                fwrite($fic, '1 ' . $enrEv[2] . ' ' . $enrEv[5] . $cr);
                // Date de l'évènement : début ou plage de dates
                $deb_lu = $enrEv[3];
                $fin_lu = $enrEv[4];
                if (($deb_lu != '') or ($fin_lu != '')) {
                    $debut = '';
                    $fin = '';
                    // Si date de fin = date de début, on ne fait pas de between, sinon lors de l'import on obtient after
                    if ($fin_lu == $deb_lu) $fin_lu = '';
                    if ($deb_lu != '') $debut = Etend_date_GedCom($enrEv[3]);
                    if ($fin_lu != '') $fin = Etend_date_GedCom($enrEv[4]);
                    if (($deb_lu != '') and ($fin_lu == '')) fwrite($fic, '2 DATE ' . $debut . $cr);
                    if (($deb_lu != '') and ($fin_lu != '')) fwrite($fic, '2 DATE BET ' . $debut . ' AND ' . $fin . $cr);
                }
                // Lieu de l'évènement
                if ($enrEv[0] != 0) {
                    if ($enrEv[1] == 4)
                        $lib_ville_occ = lib_villeG($enrEv[0]);
                    else
                        $lib_ville_occ = lectZone($enrEv[0], $enrEv[1], 'N');
                    fwrite($fic, '2 PLAC ' . $lib_ville_occ . $cr);
                }
                // Notes pour l'évènement
                if (Rech_Commentaire($enrEv[6], 'E')) {
                    if (($Environnement == 'L') or ($Diffusion_Commentaire_Internet == 'O')) {
                        Ecrit_Note_Gedcom($Commentaire, $fic);
                    }
                }
            }
            $resEv->closeCursor();
        }
        // Export des images liées directement Ã  la personne
        $sqlI = 'select nom from ' . nom_table('images') . ' where Reference = ' . $enr[0] . ' and Type_Ref = "P"';
        $resI = lect_sql($sqlI);
        while ($rowI = $resI->fetch(PDO::FETCH_NUM)) {
            $ext = Extension_Fic($rowI[0]);
            if (strpos('/jpg/jpeg/jpe', $ext) != false) tag_obje_img('JPEG', $rowI[0], $fic);
            if ($ext == 'gif') tag_obje_img('GIF', $rowI[0], $fic);
            if ($ext == 'bmp') tag_obje_img('BMP', $rowI[0], $fic);
        }
    }

    // Date de modification de la fiche
    $date_mod = $enr[9];
    if ($date_mod != '') {
        fwrite($fic, '1 CHAN ' . $cr);
        fwrite($fic, '2 DATE ' . $date_mod . $cr);
        fwrite($fic, '3 TIME ' . $enr[10] . $cr);
    }
}

// Ecrit un bloc de lignes OBJE pour une image dans un fichier Gedcom
//[ bmp | gif | jpeg | ole | pcx | tiff | wav ] : FORM possibles
function tag_obje_img($typ_form, $nom_img, $fic)
{
    global $cr, $Environnement, $chemin_images_util;
    fwrite($fic, '2 OBJE ' . $cr);
    fwrite($fic, '3 ' . $typ_form . ' ' . $cr);
    $nom_complet_img = $chemin_images_util . $nom_img;
    if ($Environnement == 'L') $nom_complet_img = $_SERVER['DOCUMENT_ROOT'] . $nom_complet_img;
    fwrite($fic, '3 FILE ' . $nom_complet_img . ' ' . $cr);
}

// Ecriture des notes dans le fichier Gedcom
function Ecrit_Note_Gedcom($Commentaire, $fic)
{
    global $def_enc, $cr;

    // Etape 1 : suppression des balises <p> ==> l'objectif est de remplacer les <p> </p> par des CONT
    $Commentaire = str_replace('<p>', '', $Commentaire);
    // Etape 2 : remplacement des balises </p> par des <br />
    $Commentaire = str_replace('</p>', '<br />', $Commentaire);
    // Etape 3 : remplacement des balises <br /> par des <br />
    $Commentaire = str_replace('<br />', '<br />', $Commentaire);
    // Etape 4 : split des lignes
    $tmp = explode('<br />', $Commentaire);
    $nb = count($tmp);

    for ($nb2 = 0; $nb2 <= $nb - 1; $nb2++) {
        $Commentaire = $tmp[$nb2];

        if ($nb2 == 0) $deb = '1 NOTE ';
        else           $deb = '2 CONT ';
        $note = strip_tags(html_entity_decode($Commentaire, ENT_QUOTES, $def_enc));
        $order   = array("\r\n", "\n", "\r");
        $replace = ' ';
        // Suppression des CR
        $note = str_replace($order, $replace, $note);

        if ($note != '') {
            $long_enr = 245;
            if (strlen($note) <= $long_enr) {
                fwrite($fic, $deb . $note . $cr);
            } else {
                $ligne = substr($note, 0, $long_enr);
                $deplace = $long_enr;
                fwrite($fic, $deb . $ligne . $cr);
                do {
                    $ligne = substr($note, $deplace, $long_enr);
                    $long = strlen($ligne);
                    fwrite($fic, '2 CONC ' . $ligne . $cr);
                    $deplace += $long;
                } while ($long == $long_enr);
            }
        }
    }
}
