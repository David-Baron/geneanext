<?php

//=====================================================================
// Liste des personnes par génération
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';
$titre = $LG_Menu_Title['Pers_Gen'];        // Titre pour META
$x = Lit_Env();

// Sortie en pdf ?
$sortie_pdf = false;
if ((!$SiteGratuit) or ($Premium)) {
    $s_pdf = Recup_Variable('pdf', 'C', 'O');
    if (!$s_pdf) $s_pdf = 'n';
    if ($s_pdf == 'O') $sortie_pdf = true;
    // Pas d'entête HTML sinon le PDF ne s'affichera pas
    if ($sortie_pdf) $no_entete = true;
}

require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();
// Sortie dans un fichier CSV ?
$csv_dem = Recup_Variable('csv', 'C', 'ce');
$CSV = false;
if ($csv_dem === 'c') $CSV = true;
if (($SiteGratuit) and (!$Premium)) $CSV = false;

// Afficher les manquants
$manquant = false;
if (isset($_POST['manquant'])) $manquant = true;
if (($texte) or ($CSV)) {
    $absents = Recup_Variable('absents', 'C', 1);
    if ($absents) $manquant = true;
}

// Restreindre aux manquants
$omanquant = false;
if (isset($_POST['omanquant'])) $omanquant = true;
if (($texte) or ($CSV)) {
    $oabsents = Recup_Variable('oabsents', 'C', 1);
    if ($oabsents) $omanquant = true;
}

$lieux = false;
if (isset($_POST['lieux'])) $lieux = true;
if (($texte) or ($CSV)) {
    $aff_lieux = Recup_Variable('aff_lieux', 'C', 1);
    if ($aff_lieux) $lieux = true;
}


// Affiche le libbellé xème génération sur une ligne
function Affiche_Generation($Gener)
{
    global $root, $Icones, $texte, $num_lig, $sortie, $CSV, $LG_show_noshow, $LG_LPersG_first, $LG_LPersG_next, $LG_LPersG_generation;
    $num_lig = 0;
    if (!$CSV) {
        if ($Gener != 1) {
            HTML_ou_PDF('</table>', $sortie);
            if (! $texte) echo '</div>' . "\n";
        }
        HTML_ou_PDF('<br />', $sortie);
        $ent_table = '<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" align="center">';
        $ent_table_texte = '<table width="95%" align="center">';
        if (! $texte) echo $ent_table;
        else HTML_ou_PDF($ent_table_texte, $sortie);
        HTML_ou_PDF('<tr align="center">', $sortie);
        if (! $texte) echo '<td class="rupt_table" colspan="4">';
        else HTML_ou_PDF('<td colspan="4"><b>', $sortie);
        if ((!$texte) and ($sortie == 'H')) echo '<!-- ^^^G' . $Gener . 'G^^^ -->';
        HTML_ou_PDF($Gener, $sortie);
        if ($Gener == 1) HTML_ou_PDF($LG_LPersG_first, $sortie);
        else HTML_ou_PDF($LG_LPersG_next, $sortie);
        HTML_ou_PDF(' ' . $LG_LPersG_generation, $sortie);
        // Affichage de l'oeil pour afficher / masquer une génération
        if (! $texte) echo ' <img src="' . $root . '/assets/img/' . $Icones['oeil'] . '" alt="' . $LG_show_noshow . '" title="' . $LG_show_noshow . '" ' . Survole_Clic_Div('div' . $Gener) . '/>';
        if ($texte) HTML_ou_PDF('</b>', $sortie);
        HTML_ou_PDF('</td></tr></table>' . "\n", $sortie);
        if (! $texte) echo '<div id="div' . $Gener . '">' . $ent_table . "\n";
        else HTML_ou_PDF($ent_table_texte, $sortie);
    }
    return true;
}

// Affiche une personne sur une ligne
function Affiche_Personne($Personne, $nb, $nb_gen)
{
    global $root, $Icones, $LG_Show_On_Map, $texte, $num_lig, $Vil_Prec, $Ville, $lieux, $_SESSION, $sortie, $pdf, $CSV, $fp, $LG_Data_noavailable_profile, $LG_LPersG_Implex_or_error, $LG_at, $omanquant;
    if (!$omanquant) {
        // Ordre des champs : Reference, Nom, Prenoms, Numero, Ne_le, Decede_Le, Diff_Internet, Ville_Naissance, Ville_Deces
        //                    0          1    2        3       4      5          6              7                8
        // n.Latitude, n.Longitude, p.Latitude, p.Longitude, '.
        // 9           10           11          12
        if (!$CSV) {
            if (! $texte) {
                echo '<tr>' . "\n";
            } else HTML_ou_PDF('<tr>' . "\n", $sortie);
            // S'il y a une différence, il y a implex probable
            $Implex = '';
            // On ne vérifie que si l'on n'est pas sur le de cujus par défaut
            if ($_SESSION['decujus_defaut'] == 'O') {
                if ($Personne[3] != $nb) $Implex = ' (' . $nb . ')';
                if ((!$texte) && ($Implex != ''))
                    $Implex .= ' <img src="' . $root . '/assets/img/' . $Icones['commentaire'] . '" alt="' . $LG_LPersG_Implex_or_error . '" title="' . $LG_LPersG_Implex_or_error . '">';
            }

            HTML_ou_PDF('<td width="12%">' . $Personne[3] . $Implex . '</td>' . "\n", $sortie);
            if (IS_GRANTED('P') or ($Personne[6] == 'O')) {
                HTML_ou_PDF('<td width="48%">', $sortie);
                if (! $texte)
                    echo '<a href="' . $root . '/fiche_fam_pers?Refer=' . $Personne[0] . '">' . $Personne[1] . ' ' . $Personne[2] . '</a>';
                else
                    HTML_ou_PDF($Personne[1] . ' ' . $Personne[2], $sortie);
                HTML_ou_PDF('</td>' . "\n", $sortie);
                HTML_ou_PDF('<td width="20%">', $sortie);
                $ne = $Personne[4];
                if (($texte) and ($ne != '')) HTML_ou_PDF('° ', $sortie);
                HTML_ou_PDF(Etend_Date($ne), $sortie);
                if ($lieux) {
                    $deb_lieux = ' ';
                    if ($sortie == 'H')    $deb_lieux = '<br />';
                    if ($Personne[7] != '') {
                        HTML_ou_PDF($deb_lieux . $LG_at . ' ' . $Personne[7], $sortie);
                        // else $pdf->WriteHTML(' '.$LG_at.' '.$Personne[7]);
                        if (!$texte) {
                            $Lat_V = $Personne[9];
                            $Long_V = $Personne[10];
                            if (($Lat_V != 0) or ($Long_V != 0)) {
                                echo '<a href="http://www.openstreetmap.org/?lat=' . $Lat_V . '&amp;lon=' . $Long_V . '&amp;mlat=' . $Lat_V . '&amp;mlon=' . $Long_V . '&amp;zoom=10" target="_blank"><img src="' . $root . '/assets/img/' . $Icones['map_go'] . '" alt="' . $LG_Show_On_Map . '" title="' . $LG_Show_On_Map . '"></a>';
                            }
                        }
                    }
                }
                HTML_ou_PDF('</td>' . "\n", $sortie);
                HTML_ou_PDF('<td width="20%">', $sortie);
                $decede = $Personne[5];
                if (($texte) and ($decede != '')) HTML_ou_PDF('+ ', $sortie);
                HTML_ou_PDF(Etend_Date($decede), $sortie);
                if ($lieux) {
                    if ($Personne[8] != '') {
                        HTML_ou_PDF($deb_lieux . $LG_at . ' ' . $Personne[8], $sortie);
                        if (!$texte) {
                            $Lat_V = $Personne[9];
                            $Long_V = $Personne[10];
                            if (($Lat_V != 0) or ($Long_V != 0)) {
                                echo '<a href="http://www.openstreetmap.org/?lat=' . $Lat_V . '&amp;lon=' . $Long_V . '&amp;mlat=' . $Lat_V . '&amp;mlon=' . $Long_V . '&amp;zoom=10" target="_blank"><img src="' . $root . '/assets/img/' . $Icones['map_go'] . '" alt="' . $LG_Show_On_Map . '" title="' . $LG_Show_On_Map . '"></a>';
                            }
                        }
                    }
                }
                HTML_ou_PDF('</td>' . "\n", $sortie);
            } else {
                HTML_ou_PDF('<td colspan="3">' . $LG_Data_noavailable_profile . '</td>' . "\n", $sortie);
            }
            HTML_ou_PDF('  </tr>' . "\n", $sortie);
        }
        // Sortie CSV
        else {
            if (IS_GRANTED('P') or ($Personne[6] == 'O')) {
                $ligne = '';
                $ligne .= $nb_gen . ';';                    // Génération
                $ligne .= $Personne[0] . ';';                // Référence
                $ligne .= '"' . $Personne[1] . '";';        // Nom
                $ligne .= '"' . $Personne[2] . '";';        // Prénoms
                $ligne .= $Personne[3] . ';';                // Numéro
                $ligne .= Retourne_Date_CSV($Personne[4]) . ';'; // Né(e) le
                if ($lieux) $ligne .= $Personne[7] . ';';            // Né(e) à
                $ligne .= Retourne_Date_CSV($Personne[5]) . ';';    // Décédé(e) le
                if ($lieux) $ligne .= $Personne[8] . ';';            // Décédé(e) à
                fputs($fp, $ligne);
            }
        }
    }
    return true;
}
// Affiche une personne sur une ligne
function Affiche_Personne_Absente($Personne, $nb, $nb_gen)
{
    global $root, $texte, $num_lig, $sortie, $CSV, $fp, $lieux,
        $LG_Data_noavailable_profile, $LG_LPersG_missing,
        $LG_LPersG_father_of, $LG_LPersG_mother_of;
    if (!$CSV) {
        if (! $texte) {
            echo '<tr>' . "\n";
        } else HTML_ou_PDF('<tr>' . "\n", $sortie);
        // Numéro
        HTML_ou_PDF('<td width="12%">' . $nb . '</td>' . "\n", $sortie);
        if (IS_GRANTED('P') or ($Personne[6] == 'O')) {
            HTML_ou_PDF('<td width="48%">', $sortie);
            $texte2 = $LG_LPersG_missing;
            if (pair($nb)) {
                $texte2 .= $LG_LPersG_father_of;
            } else {
                $texte2 .= $LG_LPersG_mother_of;
            }
            $texte2 = $texte2 . ' ';
            if (! $texte)
                echo $texte2 . '<a href="' . $root . '/fiche_fam_pers?Refer=' . $Personne[0] . '">' . $Personne[1] . ' ' . $Personne[2] . '</a>';
            else
                HTML_ou_PDF($texte2 . $Personne[1] . ' ' . $Personne[2], $sortie);
            HTML_ou_PDF('</td>' . "\n", $sortie);
            HTML_ou_PDF('<td width="20%"> </td>' . "\n", $sortie);
            HTML_ou_PDF('<td width="20%"> </td>' . "\n", $sortie);
        } else {
            HTML_ou_PDF('<td colspan="3">' . $LG_Data_noavailable_profile . '</td>' . "\n", $sortie);
        }
        HTML_ou_PDF('</tr>' . "\n", $sortie);
    } else {
        if (IS_GRANTED('P') or ($Personne[6] == 'O')) {
            $ligne = '';
            $ligne .= $nb_gen . ';';        // Génération
            $ligne .= ';';                // Référence
            $ligne .= '"?";';            // Nom
            $ligne .= '"?";';            // Prénoms
            $ligne .= ';';                // Numéro
            $ligne .= ';';                 // Né(e) le
            if ($lieux) $ligne .= ';';    // Né(e) à
            $ligne .= ';';                // Décédé(e) le
            if ($lieux) $ligne .= ';';    // Décédé(e) à
            fputs($fp, $ligne);
        }
    }
    return true;
}

// Accède à une personne et l'affiche
function Accede_Personne($Reference, $nb, $nb_gen)
{
    global $req_Pers_d, $req_Pers_f, $Personne, $memo_ref;
    $Sql = $req_Pers_d . $Reference . $req_Pers_f . ' limit 1';
    $Res = lect_sql($Sql);
    if ($Personne = $Res->fetch(PDO::FETCH_NUM)) {
        $x = Affiche_Personne($Personne, $nb, $nb_gen);
    }
    $memo_ref = $Reference;
}

// Accède à une personne absente et l'affiche
function Accede_Personne_Absente($Reference, $nb, $nb_gen)
{
    global $req_Pers_d, $req_Pers_f, $Personne, $memo_ref;
    if ($Reference != $memo_ref) {
        $Sql = $req_Pers_d . $Reference . ' limit 1';
        $Res = lect_sql($Sql);
        if ($Personne = $Res->fetch(PDO::FETCH_NUM)) {
            $x = Affiche_Personne_Absente($Personne, $nb, $nb_gen);
        }
        $memo_ref = $Reference;
    } else $x = Affiche_Personne_Absente($Personne, $nb, $nb_gen);
}

$memo_ref = -1;

$comp_texte = '';
if ($manquant) $comp_texte .= '&amp;absents=O';
if ($omanquant) $comp_texte .= '&amp;oabsents=O';
if ($lieux)    $comp_texte .= '&amp;aff_lieux=O';

$m_self = my_self();
$compl = Ajoute_Page_Info(600, 250) .
    Affiche_Icone_Lien('href="' . $root . '/liste_pers_gen?texte=O' . $comp_texte . '"', 'text', my_html($LG_printable_format)) . '&nbsp;';
if ((!$SiteGratuit) or ($Premium)) {
    $compl .= Affiche_Icone_Lien('href="' . $root . '/liste_pers_gen?texte=O&amp;pdf=O' . $comp_texte . '"', 'PDF', my_html($LG_pdf_format)) . '&nbsp;';
    if (IS_AUTHENTICATED())
        $compl .= Affiche_Icone_Lien('href="' . $root . '/liste_pers_gen?csv=c' . $comp_texte . '"', 'exp_tab', my_html($LG_csv_export)) . '&nbsp;';
}

$Ind_Ref = 0;
$sortie = 'H';
$erreur = false;

// Si l'accès se fait directement sur la liste sans passer par l'accueil...
if (!isset($_SESSION['decujus_defaut'])) $_SESSION['decujus_defaut'] = 'O';

if (! $texte) Insere_Haut(my_html($titre), $compl, 'Liste_Pers_Gen', '');
else {

    // Sortie dans un PDF
    if ($sortie_pdf) {
        require(__DIR__ . '/html2pdfb.php');
        $sortie = 'P';
        $pdf = new PDF_HTML();
        PDF_AddPolice($pdf);
        $pdf->SetFont($font_pdf, '', 12);
        $pdf->AddPage();
        $pdf->SetFont($font_pdf, 'B', 14);
        PDF_Set_Def_Color($pdf);
        $pdf->Cell(0, 5, chaine_pdf($titre), 'LTRB', 1, 'C');
        $pdf->SetFont($font_pdf, '', 11);
        $pdf->Ln();
    }
    // Sortie au format texte
    else {
        // Affichage du titre : numéros + génération
        echo '</head>' . "\n";
        echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
        echo '<table cellpadding="0" width="100%">' . "\n";
        echo '<tr>' . "\n";
        echo '<td align="center"><b>' . StripSlashes($titre) . '</b></td>' . "\n";
        echo '</tr>' . "\n";
        echo '</table>' . "\n";
    }
}

if (! $texte) {
    echo '<form id="parliste" action="' . my_self() . '" method="post">' . "\n";
    echo '<table border="0" width="75%" align="center">' . "\n";
    echo '<tr align="center">';

    echo '<td class="rupt_table">';
    echo '<input type="checkbox"';
    if ($lieux) echo ' checked="checked"';
    echo ' id="lieux" name="lieux" value="1"/><label for="lieux">' . $LG_LPersG_display_places . '</label>';
    echo '</td>' . "\n";

    if (IS_GRANTED('G')) {
        echo '<td class="rupt_table">';
        echo my_html($LG_LPersG_missing_pers) . '&nbsp;:&nbsp;';
        echo '<input type="checkbox"';
        if ($manquant) echo ' checked="checked"';
        echo ' name="manquant" id="manquant" value="1"'
            . ' onclick="if (document.getElementById(\'manquant\').checked==true) {document.getElementById(\'omanquant\').checked=false;}"'
            . '  value="1"/><label for="manquant">' . $LG_LPersG_display_missing . '</label>&nbsp;';
        echo '<input type="checkbox"';
        if ($omanquant) echo ' checked="checked"';
        echo ' name="omanquant" id="omanquant" value="1"'
            . ' onclick="if (document.getElementById(\'omanquant\').checked==true) {document.getElementById(\'manquant\').checked=false;}"'
            . '  value="1"/><label for="omanquant">' . $LG_LPersG_display_only_missing . '</label>&nbsp;';
        echo '</td>' . "\n";
    }

    echo '<td class="rupt_table"><input type="submit" value="' . my_html($LG_modify_list) . '"/>';
    $alt_img = my_html($LG_LPersG_hint_eye);
    echo '&nbsp;&nbsp;&nbsp;&nbsp;<img id="masque" src="' . $root . '/assets/img/' . $Icones['oeil'] . '" alt="' . $alt_img . '" title="' . $alt_img . '"' .
        ' onmouseover="Survole_Clic_Div_Tous(\'MO\',\'' . $Comportement . '\');" onclick="Survole_Clic_Div_Tous(\'CL\',\'' . $Comportement . '\');"/>';
    echo '</td>' . "\n";
    echo '</tr></table>';
    echo '<input type="hidden" id="memo_etat" name="memo_etat"/>';
    echo '</form>' . "\n";
}

// Initialisations
$Num_Pere = 0;
$Num_Mere = 0;
if (!$lieux) {
    $req_Pers_d = 'select Reference, Nom, Prenoms, Numero, Ne_le, Decede_Le, Diff_Internet ' .
        'from ' . nom_table('personnes') . ' where Reference = ';
    $req_Pers_f = '';
} else {
    'from ' . nom_table('personnes') . ' where Reference = ';
    $req_Pers_d = 'select Reference, Nom, Prenoms, Numero, Ne_le, Decede_Le, Diff_Internet, n.Nom_Ville, d.Nom_Ville, n.Latitude, n.Longitude, d.Latitude, d.Longitude ' .
        'from ' . nom_table('personnes') . ' p, ' . nom_table('villes') . ' n, ' . nom_table('villes') . ' d ' .
        'where p.Reference = ';
    $req_Pers_f = ' and n.Identifiant_zone = p.Ville_Naissance' .
        ' and d.Identifiant_zone = p.Ville_Deces';
}

// On restreint le nombre de générations car la mémoire est limitée...
if ($Environnement == 'L') $max_gen_AD = $max_gen_AD_loc;
else $max_gen_AD = $max_gen_AD_int;

// Récupération de la référence de la personne '1'
if ($decujus = get_decujus()) {

    $Sql = $req_Pers_d . $decujus . $req_Pers_f . ' limit 1';
    $Res = lect_sql($Sql);
    $Personne = $Res->fetch(PDO::FETCH_NUM);

    // Sortie CSV
    if ($CSV) {
        $nom_fic = $chemin_exports . 'liste_personnes_' . $mod_nom_fic . 'gen.csv';
        $fp = fopen($nom_fic, 'w+');

        //$req_Pers_d = 'select Reference, Nom, Prenoms, Numero, Ne_le, Decede_Le, Diff_Internet, n.Nom_Ville, d.Nom_Ville, n.Latitude, n.Longitude, d.Latitude, d.Longitude '.
        // Ecriture de la ligne d'entête
        $ligne = '';
        $ligne .= $LG_LPersG_generation . ';';
        $ligne .= $LG_Reference . ';';
        $ligne .= LG_PERS_NAME . ';';
        $ligne .= LG_PERS_FIRST_NAME . ';';
        $ligne .= $LG_LPersG_number . ';';
        $ligne .= $LG_LPersG_born_precision . ';';
        $ligne .= LG_PERS_BORN . ';';
        $ligne .= $LG_LPersG_born_calendar . ';';
        if ($lieux) $ligne .= $LG_LPersG_born_where . ';';
        $ligne .= $LG_LPersG_dead_precision . ';';
        $ligne .= LG_PERS_DEAD . ';';
        $ligne .= $LG_LPersG_dead_calendar . ';';
        if ($lieux) $ligne .= $LG_LPersG_dead_where . ';';
        fputs($fp, $ligne);
    }

    if ((! $texte) and (! $CSV)) {
        echo '<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" align="center">';
        echo '<tr bgcolor="#F4F0EC" align="center">';
        echo '<td width="12%">' . my_html($LG_Sosa_Number) . '</td>';
        echo '<td width="48%">' . my_html($LG_person) . '</td>';
        echo '<td width="20%">' . my_html(LG_PERS_BORN) . '</td>';
        echo '<td width="20%">' . my_html(LG_PERS_DEAD) . '</td>';
        echo '</tr>';
        echo '</table>' . "\n";
    }

    $x = Affiche_Generation(1);
    $x = Affiche_Personne($Personne, '1', 1);
    $Precedent[1] = $Personne[0];
    $Prec_Max = 1;
    $num_theo = 1;

    /*
if($sortie_pdf) {
	$pdf->Output();
	exit;
}
*/

    $nb_gen = 2;
    do {
        $Ind_Cour = 0;
        $acces_P = 0;
        for ($nb = 1; $nb <= $Prec_Max; $nb++) {
            $prec = $Precedent[$nb];
            if ($prec) {
                $x = Get_Parents($prec, $Num_Pere, $Num_Mere, $Rang);
            } else {
                $Num_Pere = 0;
                $Num_Mere = 0;
            }
            $Courant[++$Ind_Cour] = $Num_Pere;
            $Courant[++$Ind_Cour] = $Num_Mere;
            if ($Num_Pere or $Num_Mere) $acces_P = 1;
        }
        //echo '<br />Génération '.$nb_gen.'////';var_dump($Courant);echo '<br />';

        if ($Ind_Cour > 0) {
            $num_theo = pow(2, $nb_gen - 1);
            $premier = true;
            for ($nb = 1; $nb <= $Ind_Cour; $nb++) {
                $cour = $Courant[$nb];
                //if (($premier) and ($cour)) {
                if ($premier) {
                    $x = Affiche_Generation($nb_gen);
                    $premier = false;
                }
                if ($cour) {
                    $x = Accede_Personne($cour, $num_theo, $nb_gen);
                } else {
                    if (($manquant) or ($omanquant)) {
                        $w = $Precedent[floor(($nb + 1) / 2)];
                        if ($w) {
                            $x = Accede_Personne_Absente($w, $num_theo, $nb_gen);
                        }
                    }
                }
                $num_theo++;
            }
        }
        // Bascule des personnes de l'ensemble courant vers l'ensemble précédent
        if ($Ind_Cour > 0) {
            $Precedent = $Courant;
            $Prec_Max = $Ind_Cour;
        }
        ++$nb_gen;
    } while (($acces_P > 0) and ($nb_gen <= $max_gen_AD));

    $Res->closeCursor();

    if (! $CSV) {
        HTML_ou_PDF('</table>', $sortie);
        if (! $texte) {
            echo '</div>';
            if ($nb_gen > $max_gen_AD) {
                echo '<br /><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . $LG_tip . '" title="' . $LG_tip . '">' .
                    my_html($LG_LPersG_limited_max_gen_1 . $max_gen_AD . $LG_LPersG_limited_max_gen_2) .
                    '<a href="' . $root . '/vue_personnalisee">' . my_html($LG_LPersG_limited_max_gen_3) . '</a>' .
                    my_html($LG_LPersG_limited_max_gen_4) . "\n";
            }
            include(__DIR__ . '/assets/js/Liste_Patro.js');        // Même javascript que la liste patronymique
        }
    }
} else {
    if (! $CSV) include(__DIR__ . '/../public/assets/js/Liste_Patro.js');
    echo '<img src="' . $root . '/assets/img/error.png" alt="Avertissement"> ';
    echo 'De cujus non trouvé, veuillez attribuer le numéro 1 &agrave; la personne de votre choix ; ';
    echo 'pour ce faire, passez par la <a href="' . $root . '/liste_pers?Type_Liste=P">liste par noms</a>.';
}

if ($CSV) {
    fclose($fp);
    echo '<br /><br />' . my_html($LG_csv_available_in) . ' <a href="' . $nom_fic . '" target="_blank">' . $nom_fic . '</a><br />' . "\n";
}
if (! $texte) {
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
}

if (!$erreur and $sortie_pdf) {
    $pdf->Output();
    exit;
}

?>
</body>

</html>