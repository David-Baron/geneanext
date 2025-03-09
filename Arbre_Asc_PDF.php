<?php

//=====================================================================
// Affichage d'un arbre en PDF
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

$acces = 'L';                // Type d'accès de la page : (M)ise à jour, (L)ecture
$x = Lit_Env();

// Pour éviter les erreurs en log en cas d'accès direct...
if ($Environnement == 'L') {
    if (!isset($_SESSION['estPrivilegie'])) $_SESSION['estPrivilegie'] = true;
    if (!isset($_SESSION['estCnx'])) $_SESSION['estCnx'] = true;
} else {
    if (!isset($_SESSION['estPrivilegie'])) $_SESSION['estPrivilegie'] = false;
    if (!isset($_SESSION['estCnx'])) $_SESSION['estCnx'] = false;
}

$Refer = Recup_Variable('Refer', 'N');

// Libellé en français d'un jour de la semaine
function lib_fr_jour($jour, $maj = 'n')
{
    global $Jours_Lib;
    if ($jour == 0) $lib_jour = $Jours_Lib[6];
    else $lib_jour = $Jours_Lib[$jour - 1];
    if ($maj) $lib_jour = ucfirst($lib_jour);
    return $lib_jour;
}

function detailPersonne($LaRef, $generation, $numero)
{
    if ($LaRef == 0)
        return "";
    if ($generation == 1)
        return getDetail($LaRef);
    $pivot = pow(2, $generation - 2);
    $x = get_Parents($LaRef, $Num_Pere, $Num_Mere, $Rang);
    if ($numero < $pivot) {
        // pere
        return detailPersonne($Num_Pere, $generation - 1, $numero);
    } else {
        // mere
        return detailPersonne($Num_Mere, $generation - 1, $numero - $pivot);
    }
}

function getDetail($LaRef)
{
    // Accès aux données de la personne
    $sql = 'select Nom, Prenoms, Ne_Le, Decede_Le, Diff_Internet from ' . nom_table('personnes') . ' where reference = ' . $LaRef . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($infos = $res->fetch(PDO::FETCH_NUM)) {
            // Affichage des données si autorisé
            if (($_SESSION['estPrivilegie']) or ($infos[4] == 'O')) {
                $Nom    = trim($infos[0]);
                $Prenom = trim($infos[1]);
                $D_Nais = $infos[2];
                $D_Dec  = $infos[3];
                // On ne retient que le premier prénom
                if ($Prenom != '')
                    $Prenom = UnPrenom($Prenom);
                $N_P = $Nom . ' ' . $Prenom;
                $max_car = 20;
                if (strlen($N_P) > $max_car)
                    $N_P = substr($N_P, 0, $max_car);
                if ($D_Nais != '')
                    $N_P .= ' °' . affiche_date($D_Nais);
                if ($D_Dec != '')
                    $N_P .= " +" . affiche_date($D_Dec);
                return chaine_pdf($N_P);
            }
            return "ND";
        }
    }
}

// Récupération de l'état de la connexion
$est_cnx = ($_SESSION['estCnx'] === true ? true : false);

// Accès aux donnnées de la personne pour vérifier les autorisations
//$x = Get_Nom_Prenoms($Refer,$Nom,$Prenoms);
$Diff_Internet_P = 'O';
if ($Diff_Internet_P == 'O' or $_SESSION['estPrivilegie']) {
    require(__DIR__ . '/phpToPDF.php');
    $PDF = new phpToPDF();
    $PDF->AddPage('P', 'A4', 0);
    $PDF->SetMargins(0, 0, 0);
    $PDF->SetAutoPageBreak(true, 0);
    PDF_AddPolice($PDF);
    $PDF->SetFont($font_pdf, "B", 16);
    PDF_Set_Def_Color($PDF);
    $PDF->Text(6, 10, $LG_assc_tree);
    $PDF->Text(6, 16, chaine_pdf($LG_7_Gens));
    $PDF->SetFont($font_pdf, '', 8);

    // Date GMT au format anglais
    //$PDF->Text(10,21,"(". gmdate( "D, d M Y H:i:s" ) .")");

    // version française
    $lib_jour = lib_fr_jour(date('w'), true);
    $lib_mois = $Mois_Lib[date('n') - 1];
    $PDF->Text(6, 9 * 32, chaine_pdf($lib_jour . ' ' . date('j') . ' ' . $lib_mois) . ' ' . date('Y H:i'));

    // generation 1
    $PDF->SetFont($font_pdf, 'B', 12);
    $PDF->SetXY(5, 138);
    //$PDF->Cell(64,20,detailPersonne($Refer,1,0),1);
    $PDF->Cell(64, 20, detailPersonne($Refer, 1, 0), 0);
    $PDF->RoundedRect(5, 138, 64, 20, 1, 'D');
    $PDF->Line(10, 138, 10, 77);
    $PDF->Line(10, 158, 10, 221);

    // generation 2
    $PDF->SetFont($font_pdf, '', 11);
    for ($i = 0; $i < 2; $i++) {
        $PDF->Line(10, 144 * $i + 77, 15, 144 * $i + 77);
        $PDF->SetXY(15, 144 * $i + 67);
        $PDF->Cell(60, 20, detailPersonne($Refer, 2, $i), 1);
        $PDF->Line(30, 144 * $i + 67, 30, 144 * $i  + 41);
        $PDF->Line(30, 144 * $i + 87, 30, 144 * $i + 113);
    }

    // generation 3
    $PDF->SetFont($font_pdf, '', 10);
    for ($i = 0; $i < 4; $i++) {
        $PDF->Line(30, 72 * $i + 41, 35, 72 * $i + 41);
        $PDF->SetXY(35, 72 * $i + 35);
        $PDF->Cell(54, 14, detailPersonne($Refer, 3, $i), 1);
        $PDF->Line(55, 72 * $i + 35, 55, 72 * $i + 23);
        $PDF->Line(55, 72 * $i + 49, 55, 72 * $i + 59);
    }

    // generation 4
    $PDF->SetFont($font_pdf, '', 10);
    for ($i = 0; $i < 8; $i++) {
        $PDF->Line(55, 36 * $i + 23, 60, 36 * $i + 23);
        $PDF->SetXY(60, 36 * $i + 19);
        $PDF->Cell(58, 8, detailPersonne($Refer, 4, $i), 1);
        $PDF->Line(93, 36 * $i + 19, 93, 36 * $i + 14);
        $PDF->Line(93, 36 * $i + 27, 93, 36 * $i + 32);
    }

    // generation 5
    $PDF->SetFont($font_pdf, '', 8);
    for ($i = 0; $i < 16; $i++) {
        $PDF->Line(93, 18 * $i + 14, 95, 18 * $i + 14);
        $PDF->SetXY(95, 18 * $i + 10);
        $PDF->Cell(44, 7, detailPersonne($Refer, 5, $i), 1);
        $PDF->Line(135, 18 * $i + 10, 135, 18 * $i  + 9);
        $PDF->Line(135, 18 * $i + 17, 135, 18 * $i + 18);
    }

    // generation 6 + 7
    $PDF->SetFont($font_pdf, '', 6);
    for ($i = 0; $i < 32; $i++) {
        $PDF->Line(135, 9 * $i + 9, 140, 9 * $i + 9);
        $PDF->SetXY(140, 9 * $i + 6);
        $PDF->Cell(35, 6, detailPersonne($Refer, 6, $i), 1);
        // parents = generation 7
        $PDF->Line(175, 9 * $i + 9, 176, 9 * $i + 9);
        $PDF->SetXY(176, 9 * $i + 5);
        $PDF->Cell(33, 4, detailPersonne($Refer, 7, 2 * $i), 1);
        $PDF->SetXY(176, 9 * $i + 9);
        $PDF->Cell(33, 4, detailPersonne($Refer, 7, 2 * $i + 1), 1);
    }

    header('X-Robots-Tag: noindex', true);
    //$PDF->Output('F.PDF');
    $PDF->Output();
}
// Erreur si la personne n'a pas les autorisations
else {
    $index_follow = 'IN';                    // NOFOLLOW demandé pour les moteurs en cas d'erreur
    require(__DIR__ . '/Gestion_Pages.php');
    Insere_Haut('Arbre ascendant', '', 'Arbre_Asc_PDF', $Refer);
    echo Affiche_Stop($LG_Data_noavailable_profile);
}
