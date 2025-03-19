<?php
//=====================================================================
// Code commun à
// - Recherche dans les commentaires
// - Utilisation d'un document
//=====================================================================

//--------------------------------------------------------------------------
// Retourne le nom et le prénom d'une personne : code retour 1 : trouvé, 0 : sinon
//--------------------------------------------------------------------------
function Get_Nom_PrenomsSexe($Pers, &$Nom, &$Prenoms, &$Sexe)
{
    global $Diff_Internet_P;
    $Nom = '';
    $Prenoms = '';
    $Diff_Internet_P = 'N';
    $sql = 'select Nom, Prenoms, Diff_Internet, Sexe from ' . nom_table('personnes') . ' where Reference  = ' . $Pers . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $Nom     = my_html($enreg[0]);
            $Prenoms = my_html($enreg[1]);
            $Diff_Internet_P = $enreg[2];
            $Sexe = $enreg[3];
        }
        $res->closeCursor();
    }
    if (($Nom != '') or ($Prenoms != '')) return 1;
    else return 0;
}


// Récupération des informations
function recup_Info($nom_rub, $nom_de_la_table, $nom_ident)
{
    global $Ref_Objet, $def_enc;
    $info = '';
    $req_C = 'select ' . $nom_rub . ' from ' . nom_table($nom_de_la_table) . ' where ' . $nom_ident . ' = ' . $Ref_Objet . ' limit 1';
    if ($res_C = lect_sql($req_C)) {
        if ($enreg_C = $res_C->fetch(PDO::FETCH_NUM)) {
            $info = my_html($enreg_C[0]);
        }
    }
    return $info;
}

// Accès aux données ; paramètres : type d'objet cible et référence de l'objet cible
function acces_donnees($Objet_Cible, $Ref_Objet)
{
    global $Nom, $Prenoms,
        $Nom_M, $Prenoms_M, $Sexe_M,
        $Nom_F, $Prenoms_F, $Sexe_F,
        $Mari, $Femme,
        $Titre, $Nom_Zone;
    switch ($Objet_Cible) {
        // Personne ou filiation
        case 'P':
        case 'F':
            Get_Nom_Prenoms($Ref_Objet, $Nom, $Prenoms);
            break;
        // Union
        case 'U':
            $req_C = 'select Conjoint_1, Conjoint_2 from ' . nom_table('unions') . ' where Reference = ' . $Ref_Objet . ' limit 1';
            if ($res_C = lect_sql($req_C)) {
                if ($enreg_C = $res_C->fetch(PDO::FETCH_NUM)) {
                    // Récupération des infos du mari
                    $Mari = $enreg_C[0];
                    Get_Nom_PrenomsSexe($Mari, $Nom_M, $Prenoms_M, $Sexe_M);
                    // Récupération des infos de la femme
                    $Femme = $enreg_C[1];
                    Get_Nom_PrenomsSexe($Femme, $Nom_F, $Prenoms_F, $Sexe_F);
                }
            }
            break;
        // Evènement
        case 'E':
            $Titre = recup_Info('Titre', 'evenements', 'Reference');
            break;
        // Ville
        case 'V':
            $Nom_Zone = recup_Info('Nom_Ville', 'villes', 'Identifiant_zone');
            break;
        // Subdivision
        case 's':
            $Nom_Zone = recup_Info('Nom_Subdivision', 'subdivisions', 'Identifiant_zone');
            break;
        // Département
        case 'D':
            $Nom_Zone = recup_Info('Nom_Depart_Min', 'departements', 'Identifiant_zone');
            break;
        // Région
        case 'R':
            $Nom_Zone = recup_Info('Nom_Region_Min', 'regions', 'Identifiant_zone');
            break;
        // Image
        case 'I':
            $Titre = recup_Info('Titre', 'images', 'ident_image');
            break;
        // Nom de famille
        case 'O':
            $Titre = recup_Info('nomFamille', 'noms_famille', 'idNomFam');
            break;
        // Lien
        case 'L':
            $Titre = recup_Info('description', 'liens', 'Ref_lien');
            break;
        // Source
        case 'S':
            $Titre = recup_Info('Titre', 'sources', 'Ident');
            break;
    }
}

// Accès aux données ; paramètres : type d'objet cible et référence de l'objet cible
// fonction : (C)ommentaire ou (U)tilisation
function affiche_donnees($Objet_Cible, $Ref_Objet, $fonction)
{
    global $root, $Nom, $Prenoms,
        $Nom_M, $Prenoms_M, $Sexe_M,
        $Nom_F, $Prenoms_F, $Sexe_F,
        $Titre, $Nom_Zone,
        $Mari, $Femme,
        $base_ref, $cible, $base_ref, $echo_modif;

    if ($fonction == 'U') $action = my_html(LG_DOC_UT_ON);
    else                  $action = 'Sur';

    switch ($Objet_Cible) {
        case 'P':
            echo $action . ' <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Ref_Objet . '">' . $Prenoms . ' ' . $Nom . '</a>';
            echo '&nbsp;<a href="' . $root . '/edition_personne.php?Refer=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'U':
            echo $action . ' ' . $cible . ' de <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Mari . '">' . $Prenoms_M . ' ' . $Nom_M . '</a>' .
                ' et <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Femme . '">' . $Prenoms_F . ' ' . $Nom_F . '</a>';
            $us = 'n';
            if ($Sexe_M == $Sexe_F) $us = 'o';
            echo '&nbsp;<a href="' . $root . '/edition_union.php?Reference=' . $Ref_Objet . '&amp;us=' . $us . '">' . $echo_modif;
            break;
        case 'F':
            echo $action . ' ' . $cible . ' de <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Ref_Objet . '">' . $Prenoms . ' ' . $Nom . '</a>';
            echo '&nbsp;<a href="' . $root . '/edition_filiation.php?Refer=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'E':
            echo $action . ' ' . $cible . ' de <a href="' . $root . '/fiche_evenement.php?refPar=' . $Ref_Objet . '">' . $Titre . '</a>';
            echo '&nbsp;<a href="' . $root . '/edition_evenement.php?refPar=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'V':
            echo $action . ' ' . $cible . ' de <a href="' . $root . '/fiche_ville.php?Ident=' . $Ref_Objet . '">' . $Nom_Zone . '</a>';
            echo '&nbsp;<a href="' . $root . '/edition_ville.php?Ident=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 's':
            echo $action . ' ' . $cible . ' de <a href="' . $root . '/fiche_subdivision.php?Ident=' . $Ref_Objet . '">' . $Nom_Zone . '</a>';
            echo '&nbsp;<a href="' . $root . '/edition_subdivision.php?Ident=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'D':
            echo $action . ' ' . $cible . ' de ' . $Nom_Zone;
            echo '&nbsp;<a href="' . $root . '/edition_depart.php?Ident=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'R':
            echo $action . ' ' . $cible . ' de ' . $Nom_Zone;
            echo '&nbsp;<a href="' . $root . '/edition_region.php?Ident=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'I':
            echo $action . ' ' . $cible . ' ' . $Titre;
            echo '&nbsp;<a href="' . $root . '/edition_image.php?ident_image=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'O':
            echo $action . ' ' . $cible . ' <a href="' . $root . '/fiche_nomfam.php?idNom=' . $Ref_Objet . '">' . $Titre . '</a>';
            echo '&nbsp;<a href="' . $root . '/edition_nomfam.php?idNom=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'L':
            echo $action . ' ' . $cible . ' <a href="' . $root . '/fiche_lien.php?Ref=' . $Ref_Objet . '">' . $Titre . '</a>';
            echo '&nbsp;<a href="' . $root . '/edition_lien.php?Ref=' . $Ref_Objet . '">' . $echo_modif;
            break;
        case 'S':
            echo $action . ' ' . $cible . ' <a href="' . $root . '/fiche_source.php?ident=' . $Ref_Objet . '">' . $Titre . '</a>';
            echo '&nbsp;<a href="' . $root . '/edition_source.php?ident=' . $Ref_Objet . '">' . $echo_modif;
            break;
    }
}
