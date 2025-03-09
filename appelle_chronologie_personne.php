<?php
session_start();

//=====================================================================
// Frise Chronologique pour les évènements de la personne et les dates de naissance des enfants
//=====================================================================

// Gestion standard des pages
include('fonctions.php');

function Lien_vers_Pers()
{
    global $composants, $texte, $sortie;
    if (count($composants) >= 5) {
        if (! $texte) {
            return '<a ' . Ins_Ref_Pers($composants[5], false) . '>' . my_html($composants[3] . ' ' . $composants[2]) . '</a>&nbsp;'
                . Lien_Chrono_Pers($composants[5]);
        } else
            return $composants[3] . ' ' . $composants[2];
    }
}

function lib_sexe_enfant($sexe)
{
    switch ($sexe) {
        case 'm':
            $lib_sexe = LG_SON;
            break;
        case 'f':
            $lib_sexe = LG_DAUGHTER;
            break;
        default:
            $lib_sexe = LG_CHILD;
            break;
    }
    return $lib_sexe;
}

// Récupération des variables de l'affichage précédent
$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Chronology;
$x = Lit_Env();
$index_follow = 'ON';                    // NOFOLLOW demandé pour les moteurs

// Sortie en pdf ?
$sortie_pdf = false;
if ((!$SiteGratuit) or ($Premium)) {
    $s_pdf = Recup_Variable('pdf', 'C', 'O');
    if (!$s_pdf) $s_pdf = 'n';
    if ($s_pdf == 'O') $sortie_pdf = true;
    // Pas d'entête HTML sinon le PDF ne s'affichera pas
    if ($sortie_pdf) $no_entete = true;
}

include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

$m_self = my_self();

$compl = Ajoute_Page_Info(600, 150) .
    '<a href="' . $m_self . '?texte=O&amp;Refer=' . $Refer . '">' .
    Affiche_Icone('text', 'Format imprimable') . '</a>' . "\n";

if ((!$SiteGratuit) or ($Premium))
    //$compl .= Affiche_Icone_Lien('href="'.$m_self.'?texte=O&amp;pdf=O.'"','PDF',my_html($LG_pdf_format)).'&nbsp;';
    $compl .= Affiche_Icone_Lien('href="' . $m_self . '?texte=O&amp;pdf=O&amp;Refer=' . $Refer . '"', 'PDF', my_html($LG_pdf_format)) . '&nbsp;';

$sortie = 'H';

if (! $texte) Insere_Haut($titre, $compl, 'appelle_chronologie_personne', $Refer);
else {
    // Sortie dans un PDF
    if ($sortie_pdf) {
        require('html2pdfb.php');
        $sortie = 'P';
        $pdf = new PDF_HTML();
        // $pdf = new HTML2PDF('P','A4','fr', true, 'UTF-8');
        // $font_pdf = 'LibreBaskerville';
        // $font_pdf = 'Courier';
        PDF_AddPolice($pdf);
        $pdf->SetFont($font_pdf, '', 12);
        $pdf->AddPage();
        $pdf->SetFont($font_pdf, 'B', 14);
        PDF_Set_Def_Color($pdf);
        $pdf->Cell(0, 5, $titre, 'LTRB', 1, 'C');
        $pdf->SetFont($font_pdf, '', 11);
        $pdf->Ln();
    }
    // Sortie au format texte
    else {
        // Affichage du titre : numéros + génération
        Insere_Haut_texte('Chronologie');
    }
}

$les_dates = [];
$erreur = false;

$n_personnes = nom_table('personnes');
$n_unions = nom_table('unions');
$n_filiations = nom_table('filiations');
$n_evenements = nom_table('evenements');
$n_participe = nom_table('participe');
$n_pays = nom_table('pays');
$n_regions = nom_table('regions');
$n_departements = nom_table('departements');
$n_villes = nom_table('villes');


$dates_OK = false;

$sql = 'select Nom, Prenoms, Ne_Le, Decede_Le, Diff_Internet, Sexe, Ville_Naissance, Ville_Deces ' .
    'from ' . $n_personnes . ' where reference = ' . $Refer . ' limit 1';
if ($res = lect_sql($sql)) {
    if ($infos = $res->fetch(PDO::FETCH_NUM)) {
        // Protection des données sur Internet
        if (($est_privilegie) or ($infos[4] == 'O')) {

            $lib_Ne     = '';
            $lib_Decede = '';

            $Nom             = $infos[0];
            $Prenoms         = $infos[1];
            $Ne              = $infos[2];
            $Decede          = $infos[3];
            $Sexe            = $infos[5];
            $Ville_Naissance = $infos[6];
            $Ville_Deces     = $infos[7];

            $Decede_P = '99991231';
            $maxi = '99991231';

            $approx = false;

            $Lg_Ne = strlen($Ne);
            $Lg_Decede = strlen($Decede);

            // Traitement des dates qui ne font pas 10 caractères
            if (($Ne != '') and ($Lg_Ne != 10))
                $Ne = '00000101GE';
            if (($Decede != '') and ($Lg_Decede != 10))
                $Decede = '30000101GE';

            if (($Ne != '') and ($Ne[9] == 'E')) {
                $Ne[9] = 'L';
                $approx = true;
            }
            if (($Decede != '') and ($Decede[9] == 'E')) {
                $Decede[9] = 'L';
                $approx = true;
            }

            // Approximation aussi sur une date de décès post ou ante
            if (($Decede != '') and (($Decede[9] == 'P') or ($Decede[9] == 'A'))) {
                $Decede[9] = 'L';
                $approx = true;
            }

            if ($Ne != '') {
                if ($Ne[9] == 'L') {
                    $les_dates[] = $Ne . '/N';
                }
                $lib_Ne = ', ' . $LG_born;
                if ($Sexe == 'f') $lib_Ne .= 'e';
                $lib_Ne .= ' ' . Etend_date($Ne);
                $Ne_P = substr($Ne, 0, 8);
                $Ne_PC = $Ne;
                if ($Decede != '') {
                    if ($Decede[9] == 'L') {
                        $les_dates[] = $Decede . '/D';
                        $Decede_P = substr($Decede, 0, 8);
                        $maxi = $Decede_P;
                        $Decede_PC = $Decede;
                    }
                    $lib_Decede = ', ' . $LG_dead;
                    if ($Sexe == 'f') $lib_Decede .= 'e';
                    $lib_Decede .= ' ' . Etend_date($Decede);
                } else {
                    // Récupération de la date du jour, car elle peut servir de borne maximum
                    $temps = time();

                    //ecrire($fp,$temps);

                    $aujourdui = date('Y', $temps) . date('m', $temps) . date('d', $temps) . 'GL';
                    //ecrire($fp,$aujourdui);
                    //fclose($fp);
                    // On regarde la date de naissance ; si la personne est née il y a plus de 130 ans, on fait naissance + 130, sinon on prend la date du jour en max
                    $nb_mois = Age_Mois($Ne_PC, $aujourdui);
                    if ($nb_mois > (12 * 130)) {
                        $annee = intval(substr($Ne_PC, 0, 4)) + 130;
                        $maxi = $annee . substr($Ne_PC, 4, 4);
                    } else $maxi = substr($aujourdui, 0, 8);
                }
                $dates_OK = true;
            }

            echo HTML_ou_PDF('<h3 align="center">' . $Prenoms . ' ' . $Nom . $lib_Ne . $lib_Decede, $sortie);
            if ($approx) echo HTML_ou_PDF('<br />-- ' . $LG_ICSV_Event_Ca_Dates . ' --', $sortie);
            echo HTML_ou_PDF('</h3>', $sortie);

            if (Get_Parents($Refer, $Pere, $Mere, $Rang)) {
                $lib_P = '';
                $lib_fil = '';
                if (($Pere != 0) or ($Mere != 0)) {
                    switch ($Sexe) {
                        case 'm':
                            $lib_fil = LG_SON;
                            break;
                        case 'f':
                            $lib_fil = LG_DAUGHTER;
                            break;
                        default:
                            $lib_fil = LG_CHILD;
                            break;
                    }
                }
                if ($Pere != 0) {
                    if (Get_Nom_Prenoms($Pere, $Nom, $Prenoms)) {
                        if ($Diff_Internet_P) $lib_P = $lib_P . $LG_of . ' ' . $Prenoms . ' ' . $Nom;
                    }
                }
                if ($Mere != 0) {
                    if (Get_Nom_Prenoms($Mere, $Nom, $Prenoms)) {
                        if ($Diff_Internet_P) {
                            if ($Pere != 0) $lib_P = $lib_P . ' ' . $LG_and . ' ';
                            $lib_P = $lib_P . $LG_of . ' ' . $Prenoms . ' ' . $Nom;
                        }
                    }
                }
                if ($lib_P != '') {
                    if ($sortie_pdf) echo HTML_ou_PDF('<br />', $sortie);
                    echo HTML_ou_PDF('<h3 align="center">' . ucfirst($lib_fil . ' ') . $lib_P . '</h3>', $sortie);
                }
            }

            // Si la date de naissance n'est pas précise ou environ, erreur
            if ($Ne != '') {
                if (($Ne[9] == 'A') or ($Ne[9] == 'P'))
                    $dates_OK = false;
            }

            if ($dates_OK) {
                $crit_enf = 'Pere = ' . $Refer . ' or Mere = ' . $Refer;
                $crit_unions = 'u.Conjoint_1 = ' . $Refer . ' or u.Conjoint_2 =' . $Refer;
                switch ($Sexe) {
                    case 'm':
                        $crit_enf = 'Pere = ' . $Refer;
                        $crit_unions = 'u.Conjoint_1 = ' . $Refer;
                        $sel_union = 6;
                        $ref_conj = 2;
                        $z_deces = 10;
                        break;
                    case 'f':
                        $crit_enf = 'Mere = ' . $Refer;
                        $crit_unions = 'u.Conjoint_2 = ' . $Refer;
                        $sel_union = 3;
                        $ref_conj = 1;
                        $z_deces = 9;
                        break;
                }

                // Unions
                $sql = 'select u.Maries_Le, u.Conjoint_1, u.Conjoint_2, ' .
                    ' c1.Nom, c1.Prenoms, c1.Diff_Internet, ' .
                    ' c2.Nom, c2.Prenoms, c2.Diff_Internet, ' .
                    ' c1.Decede_Le, c2.Decede_Le ' .
                    ' from ' . $n_unions . ' u, ' .
                    $n_personnes . ' c1, ' .
                    $n_personnes . ' c2 ' .
                    ' where ' . $crit_unions .
                    ' and c1.Reference = u.Conjoint_1 ' .
                    ' and c2.Reference = u.Conjoint_2 ' .
                    ' and u.Maries_Le like "%L" ';
                $resUnions = lect_sql($sql);
                while ($row = $resUnions->fetch(PDO::FETCH_NUM)) {
                    $les_dates[] = $row[0] . '/M/' . $row[$sel_union] . '/' . $row[$sel_union + 1] . '/' . $row[$sel_union + 2] . '/' . $row[$ref_conj];
                    $d_deces = $row[$z_deces];
                    if ((strlen($d_deces) == 10) and ($d_deces[9] == 'L')) {
                        $les_dates[] = $d_deces . '/DC/' . $row[$sel_union] . '/' . $row[$sel_union + 1] . '/' . $row[$sel_union + 2] . '/' . $row[$ref_conj];
                    }
                }

                //Enfants
                $sql = 'select Nom, Prenoms, Ne_Le, Decede_Le, Diff_Internet, Reference, Sexe' .
                    ' from ' . $n_filiations . ' f, ' .
                    $n_personnes . ' p ' .
                    ' where ' . $crit_enf .
                    ' and Enfant = p.Reference';
                $resEnfants = lect_sql($sql);
                while ($row = $resEnfants->fetch(PDO::FETCH_NUM)) {
                    $Ne      = $row[2];
                    $Decede  = $row[3];
                    if (strlen($Ne) == 10) {
                        //if ((strlen($Ne) == 10) and (($Ne[9] == 'E') or ($Ne[9] == 'L'))) {
                        $les_dates[] = $Ne . '/ENN/' . $row[0] . '/' . $row[1] . '/' . $row[4] . '/' . $row[5] . '/' . $row[6];
                    }
                    if ((strlen($Decede) == 10) and ($Decede[9] == 'L')) {
                        $les_dates[] = $Decede . '/END/' . $row[0] . '/' . $row[1] . '/' . $row[4] . '/' . $row[5] . '/' . $row[6];
                    }
                }

                $sql  = 'select Titre, p.Debut as dDebP , p.Fin as dFinP,' .
                    ' e.Debut as dDebE , e.Fin as dFinE' .
                    ' from ' . $n_evenements . ' e ,' .
                    $n_participe . ' p ' .
                    ' where personne = ' . $Refer .
                    ' and p.Evenement = e.Reference' .
                    ' and (e.Debut like "%L" or e.Fin like "%L" or p.Debut like "%L" or p.Fin like "%L")'
                    . ' and ((e.Debut between "' . $Ne_PC . '" and "' . $maxi . '") or (e.Fin between "' . $Ne_PC . '" and "' . $maxi . '"))';
                $resEvts = lect_sql($sql);
                while ($row = $resEvts->fetch(PDO::FETCH_NUM)) {
                    $dDebP = $row[1];
                    $dFinP = $row[2];
                    $dDebE = $row[3];
                    $dFinE = $row[4];
                    if ((strlen($dDebP) == 10) and ($dDebP[9] == 'L')) {
                        $les_dates[] = $dDebP . '/EVDP/' . $row[0];
                    }
                    if ((strlen($dFinP) == 10) and ($dFinP[9] == 'L')) {
                        $les_dates[] = $dFinP . '/EVFP/' . $row[0];
                    }
                    if ((strlen($dDebE) == 10) and ($dDebE[9] == 'L')) {
                        $les_dates[] = $dDebE . '/EVDE/' . $row[0];
                    }
                    if ((strlen($dFinE) == 10) and ($dFinE[9] == 'L')) {
                        $les_dates[] = $dFinE . '/EVFE/' . $row[0];
                    }
                }

                // récupération des évènements du pays de naissance et / ou de décès
                $Pays_Naissance = 0;
                $Pays_Deces     = 0;
                $deb_req_ville = 'select Identifiant_zone from ' . $n_pays .
                    ' where Identifiant_zone = (select Zone_Mere from ' . $n_regions .
                    ' where Identifiant_zone = (select Zone_Mere from ' . $n_departements .
                    ' where Identifiant_zone = (select Zone_Mere from ' . $n_villes;
                if ($Ville_Naissance != 0) {
                    // Récupération du pays de la ville de naissance
                    $sql = $deb_req_ville .    ' where Identifiant_zone = ' . $Ville_Naissance . '))) limit 1';
                    if ($res = lect_sql($sql)) {
                        if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                            $Pays_Naissance = $enr[0];
                        }
                    }
                }
                if (($Ville_Deces != 0) and ($Ville_Deces != $Ville_Naissance)) {
                    // Récupération du pays de la ville de naissance
                    $sql = $deb_req_ville .    ' where Identifiant_zone = ' . $Ville_Deces . '))) limit 1';
                    if ($res = lect_sql($sql)) {
                        if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                            $Pays_Deces = $enr[0];
                        }
                    }
                }
                if ($Ville_Deces != $Ville_Naissance)
                    $Pays_Deces = $Pays_Naissance;

                if (($Pays_Naissance != 0) or ($Pays_Deces != 0)) {
                    if ($Pays_Naissance != $Pays_Deces)
                        $crit = ' in (' . $Pays_Naissance . ',' . $Pays_Deces . ')';
                    else
                        $crit = ' = ' . $Pays_Naissance;
                    $sql  = 'select Titre, e.Debut as dDebE , e.Fin as dFinE'
                        . ' from ' . $n_evenements . ' e '
                        . ' where Identifiant_zone ' . $crit
                        . ' and Identifiant_Niveau = 1'
                        . ' and (e.Debut like "%L" or e.Fin like "%L")'
                        . ' and (e.Debut between "' . $Ne_PC . '" and "' . $maxi . '")';

                    $resEvts = lect_sql($sql);
                    while ($row = $resEvts->fetch(PDO::FETCH_NUM)) {
                        $dDebE = $row[1];
                        $dFinE = $row[2];
                        if ((strlen($dDebE) == 10) and ($dDebE[9] == 'L')) {
                            $les_dates[] = $dDebE . '/EVDC/' . $row[0];
                        }
                        if ((strlen($dFinE) == 10) and ($dFinE[9] == 'L')) {
                            $les_dates[] = $dFinE . '/EVFC/' . $row[0];
                        }
                    }
                }
            } else Affiche_Stop($LG_ICSV_Event_No_Birth);
        } else {
            Affiche_Stop($LG_Data_noavailable_profile);
        }
    }
}

if ($dates_OK) {
    if ($sortie_pdf)
        HTML_ou_PDF('<br />', $sortie);
    // Ajout d'1 réf tous les 10 ans
    //$ages_10 = Age_Mois($Ne_PC,$Decede_PC)/12/10;
    $a = Age_Mois($Ne_PC, $maxi . 'GL');
    $ages_10 = Age_Mois($Ne_PC, $maxi . 'GL') / 12 / 10;
    if ($debug) {
        echo 'ages_10 : ' . $ages_10 . '<br />';
        echo 'Ne_PC : ' . $Ne_PC . '<br />';
        echo 'maxi : ' . $maxi . '<br />';
    }
    $la_date = $Ne_PC;
    for ($nb = 1; $nb < $ages_10; $nb++) {
        $annee = intval(substr($la_date, 0, 4)) + 10;
        if ($debug) echo 'calc annee : ' . $annee . '<br />';
        $la_date = $annee . substr($la_date, 4, 6);
        if ($debug) echo 'calc : ' . $la_date . '<br />';
        $les_dates[] = $la_date . '/AGE10/' . $nb . '0 ans';
    }

    if ($debug) {
        var_dump($les_dates);
        echo '<br />';
    }
    sort($les_dates);
    if ($debug) {
        var_dump($les_dates);
        echo '<br />';
    }

    $somme_offset = 0;
    $date_prec = '';
    $offsets = [];

    if ($debug) echo 'Max : ' . $Decede_P . '<br />';

    for ($nb = 0; $nb < count($les_dates); $nb++) {
        if ($debug) echo $les_dates[$nb] . '<br />';
        $la_date = substr($les_dates[$nb], 0, 8);
        $la_dateC = substr($les_dates[$nb], 0, 10);
        if ($debug) echo 'la_dateC : ' . $la_dateC . '<br />';
        //if ($la_date <= $Decede_P) {
        //if (($la_date <= $maxi) and ($la_date >= $Ne_PC)) {
        if ($la_date <= $maxi) {
            if ($debug) echo 'date : ' . $la_date . '<br />';
            if ($nb == 0) {
                $offset = 0;
            } else {
                $am = Age_Mois($date_prec, $la_dateC);
                if ($am > 0)
                    $offset = $am / 12;
                else
                    $offset = 1;
                if ($debug) echo 'off : ' . $offset . '<br />';
            }
            $date_prec = $la_dateC;
            $offsets[] = $offset;
            $somme_offset += $offset;
        }
    }

    // Pour éviter le multi-colonnes sur des personnes décédées jeunes
    $somme_offset = max(50, $somme_offset);

    if ($debug) echo 'date de décès : ' . $Decede_P . '<br />';

    echo HTML_ou_PDF('<ul id="timeline" style="columns: ' . $somme_offset . 'em;">', $sortie);
    for ($nb = 0; $nb < count($les_dates); $nb++) {
        $la_date = substr($les_dates[$nb], 0, 8);
        if ($debug) echo 'la_date : ' . $la_date . ', Decede_P : ' . $Decede_P . '<br />';
        //if (($la_date <= $maxi) and ($la_date >= $Ne_PC)) {
        if ($la_date <= $maxi) {
            //echo $les_dates[$nb].'<br />';
            $composants = explode('/', $les_dates[$nb]);
            if ($debug) {
                var_dump($composants);
                echo '<br />';
            }
            //$p_gauche = etend_date(substr($les_dates[$nb],0,10));
            if ($composants[1] == 'AGE10') {
                $p_gauche = $composants[2];
                $p_droite = '';
            } else {
                $p_gauche = etend_date(substr($les_dates[$nb], 0, 10));
                $app_lien = false;
                switch ($composants[1]) {
                    case 'N':
                        $p_droite = $LG_birth;
                        break;
                    case 'M':
                        $p_droite = $LG_wedding;
                        if (($est_privilegie) or ($composants[4] == 'O')) {
                            $p_droite .= ' ' . $LG_with . ' ';
                            $app_lien = true;
                        }
                        break;
                    case 'D':
                        $p_droite = $LG_death;
                        break;
                    case 'DC':
                        $p_droite = $LG_death . ' ' . LG_HUSB_WIFE . ' ' . Lien_vers_Pers();
                        break;
                    case 'ENN':
                        $p_droite = $LG_birth . ' ' . lib_sexe_enfant($composants[6]);
                        if (($est_privilegie) or ($composants[4] == 'O')) {
                            $p_droite .= ' : ';
                            $app_lien = true;
                        }
                        break;
                    case 'END':
                        $p_droite = $LG_death . ' ' . lib_sexe_enfant($composants[6]);
                        if (($est_privilegie) or ($composants[4] == 'O')) {
                            $p_droite .= ' : ';
                            $app_lien = true;
                        }
                        break;
                    case 'EVDC':
                        $p_droite = $LG_ICSV_Event_Beg . ' ' . $LG_Event . ' ' . $LG_ICSV_Country . ' ' . $composants[2];
                        break;
                    case 'EVFC':
                        $p_droite = $LG_ICSV_Event_End . ' ' . $LG_Event . ' ' . $LG_ICSV_Country . ' ' . $composants[2];
                        break;
                    case 'EVDE':
                        $p_droite = $LG_ICSV_Event_Beg . ' ' . $LG_Event . ' ' . $composants[2];
                        break;
                    case 'EVFE':
                        $p_droite = $LG_ICSV_Event_End . ' ' . $LG_Event . ' ' . $composants[2];
                        break;
                    case 'EVDP':
                        $p_droite = $LG_ICSV_Part_Beg . ' ' . $composants[2];
                        break;
                    case 'EVFP':
                        $p_droite = $LG_ICSV_Part_End . ' ' . $composants[2];
                        break;
                    default:
                        $p_droite = $les_dates[$nb];
                }
            }
            if ($sortie_pdf)
                echo HTML_ou_PDF('<br /> ' . $p_gauche, $sortie);
            else
                echo HTML_ou_PDF('<li><time style="margin-top: ' . $offsets[$nb] . 'em">' . $p_gauche . '</time>', $sortie);
            if ($sortie_pdf) echo HTML_ou_PDF(' : ', $sortie);
            if ($app_lien) echo HTML_ou_PDF($p_droite . Lien_vers_Pers(), $sortie);
            else echo HTML_ou_PDF($p_droite, $sortie);
        }
    }
    echo HTML_ou_PDF('</ul>', $sortie);
}

// Formulaire pour le bouton retour
if (!$texte) {
    echo '<br />';
    Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
}

if (! $texte) Insere_Bas($compl);

if ($dates_OK and $sortie_pdf) {
    $pdf->Output();
    exit;
}

if (!$dates_OK and $sortie_pdf) {
    Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
}

?>
</body>

</html>