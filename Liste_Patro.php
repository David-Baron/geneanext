<?php

//=====================================================================
// Liste patronymique
// Tous les patronymes dans l'ascendance du de cujus sont affichés ;
// pour chaque patronyme, on affiche les unions en descendant vers le de cujus
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

$acces = 'L';                                    // Type d'accès de la page : (L)ecture
$titre = $LG_Menu_Title['Patronymic_List'];        // Titre pour META
$x = Lit_Env();

// Sortie en pdf ?
$sortie_pdf = false;
if ((!$SiteGratuit) or ($Premium)) {
    $s_pdf = Recup_Variable('pdf', 'C', 'O');
    if (!$s_pdf) $s_pdf = 'n';
    if ($s_pdf == 'O') $sortie_pdf = true;
    if ($sortie_pdf) $no_entete = true;
}

require(__DIR__ . '/Gestion_Pages.php');

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();

$limiter = 0;
if (isset($_POST['limiter'])) $limiter = 1;
if ($texte) {
    $nom_decujus = Recup_Variable('nom_decujus', 'C', 1);
    if ($nom_decujus) $limiter = 1;
}
$lieux = 0;
if (isset($_POST['lieux'])) $lieux = 1;
if ($texte) {
    $aff_lieux = Recup_Variable('aff_lieux', 'C', 1);
    if ($aff_lieux) $lieux = 1;
}

$simu_invit = false;
if (isset($_POST['simu_invit'])) $simu_invit = true;
if ($texte) {
    $simu_invit = Recup_Variable('simu_invit', 'C', 1);
    if ($simu_invit) $simu_invit = true;
}
if ($simu_invit) $est_privilegie = false;

// Stocke les infos  d'une personne dans la liste
function Stocke_Personne($Personne)
{
    global $Num_Pers, $Longueur, $Liste_Pers;
    $Liste_Pers[++$Num_Pers] = $Personne['Nom'] . '/' .
        // On prend 1M-Num_Pers pour inverser les personnes sur une même famille
        str_pad(1000000 - $Num_Pers, $Longueur, ' ', STR_PAD_LEFT) . '/' .
        $Personne['Reference'] . '/' . $Personne['idNomFam'];
    return 1;
}

// Affiche une personne sur une ligne
function Affiche_Personne2($Personne)
{
    global $Vil_Prec, $Ville, $texte, $lieux, $est_privilegie, $LG_Data_noavailable_profile, $LG_at, $sortie;
    if (($est_privilegie) or ($Personne['Diff_Internet'] == 'O')) {
        if (! $texte) {
            echo '<a ' . Ins_Ref_Pers($Personne['Reference']) . '>' . my_html($Personne['Nom'] . ' ' . $Personne['Prenoms']) . '</a>';
        } else {
            HTML_ou_PDF($Personne['Nom'] . ' ' . $Personne['Prenoms'], $sortie);
        }
        HTML_ou_PDF('<br />' . "\n", $sortie);
        $date = $Personne['Ne_le'];
        $Vil_Cour = $Personne['Ville_Naissance'];
        if (($date != '') or ($Vil_Cour != 0)) {
            HTML_ou_PDF('° ', $sortie);
            if ($date != '') HTML_ou_PDF(Etend_date($date) . ' ', $sortie);
            if (($lieux) and ($Vil_Cour != 0)) {
                if ($Vil_Cour != $Vil_Prec) {
                    $Ville = lib_ville($Vil_Cour);
                    $Vil_Prec = $Vil_Cour;
                }
                HTML_ou_PDF($LG_at . ' ' . $Ville, $sortie);
                if (!$texte) appelle_carte_osm();
            }
            HTML_ou_PDF('<br />' . "\n", $sortie);
        }
        $date = $Personne['Decede_Le'];
        $Vil_Cour = $Personne['Ville_Deces'];
        if (($date != '') or ($Vil_Cour != 0)) {
            HTML_ou_PDF('+ ', $sortie);
            if ($date != '') HTML_ou_PDF(Etend_date($date) . ' ', $sortie);
            if (($lieux) and ($Vil_Cour != 0)) {
                if ($Vil_Cour != $Vil_Prec) {
                    $Ville = lib_ville($Vil_Cour);
                    $Vil_Prec = $Vil_Cour;
                }
                HTML_ou_PDF($LG_at . ' ' . $Ville, $sortie);
                if (!$texte) appelle_carte_osm();
            }
            HTML_ou_PDF('<br />' . "\n", $sortie);
        }
    } else {
        echo HTML_ou_PDF(my_html($LG_Data_noavailable_profile) . '<br />', $sortie);
    }
    return 1;
}

// Accède à une personne et la stocke dans la liste
function Accede_Personne($Reference)
{
    global $Personne, $n_personnes;
    $Sql = 'select Reference, Nom, Diff_Internet, idNomFam from ' . $n_personnes . ' where Reference = ' . $Reference . ' limit 1';
    $Res = lect_sql($Sql);
    if ($Personne = $Res->fetch(PDO::FETCH_ASSOC)) {
        $x = Stocke_Personne($Personne);
    }
}

// Accède à une personne et l'affiche
function Accede_Personne2($Reference)
{
    global $Personne, $Res, $DifU, $n_personnes, $est_privilegie;
    $Sql = 'select Reference, Nom, Prenoms, Numero, Ne_le, Decede_Le, ' .
        'Diff_Internet, Ville_Naissance, Ville_Deces, Sexe, idNomFam from ' . $n_personnes . ' where Reference = ' . $Reference . ' limit 1';
    $Res = lect_sql($Sql);
    if ($Personne = $Res->fetch(PDO::FETCH_ASSOC)) {
        $x = Affiche_Personne2($Personne);
    }
    // Diffusabilité de l'union
    // Si l'1 des 2 personnes n'est pas diffusable, l'union ne le sera pas
    if ((!$est_privilegie) and ($Personne['Diff_Internet'] != 'O')) {
        ++$DifU;
    }
}

$comp_texte = '';
if ($limiter) $comp_texte .= '&amp;nom_decujus=O';
if ($lieux) $comp_texte .= '&amp;aff_lieux=O';
if ($simu_invit) $comp_texte .= '&amp;simu_invit=O';

$lien = 'href="' . my_self() . '?texte=O' . $comp_texte;

$compl = Ajoute_Page_Info(600, 150) .
    Affiche_Icone_Lien($lien . '"', 'text', $LG_printable_format) . '&nbsp;';
if ((!$SiteGratuit) or ($Premium))
    $compl .= Affiche_Icone_Lien($lien . '&amp;pdf=O"', 'PDF', $LG_pdf_format) . '&nbsp;';

$Ind_Ref = 0;

$sortie = 'H';

if (! $texte) Insere_Haut($titre, $compl, 'Liste_Patro', '');
else {
    // Sortie dans un PDF
    if ($sortie_pdf) {
        require('html2pdfb.php');
        $sortie = 'P';
        $pdf = new PDF_HTML();
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
        Insere_Haut_texte($titre);
        echo '<br />';
    }
}

// Initialisations
$Num_Pere = 0;
$Num_Mere = 0;
$icone_Puis = Affiche_Icone('couple_donne', LG_PATRO_THEN) . ' ' . "\n";

// Max 1 000 000 (1 million) de personnes dans les filiations...
// 6 car le premier a le num 1, ce qui fait max 6 chiffres lorsque l'on soustrait à 1 M
$Longueur = 6;

$Num_Pers = 0;

$n_personnes = nom_table('personnes');

// Récupération de la référence de la personne '1'
if ($decujus = get_decujus()) {

    $sql = 'select Reference, Nom, idNomFam from ' . $n_personnes . ' where Reference = ' . $decujus . ' limit 1';
    $Res = lect_sql($sql);
    $Personne = $Res->fetch(PDO::FETCH_ASSOC);
    $x = Stocke_Personne($Personne);
    $nom_decujus_base = $Personne['Nom'];

    if (! $texte) {
        echo '<form action="' . my_self() . '" method="post">' . "\n";
        echo '<table border="0" width="80%" align="center">' . "\n";
        echo '<tr align="center">';

        echo '<td class="rupt_table">';
        echo '<input type="checkbox"';
        if ($lieux) echo ' checked="checked"';
        echo ' name="lieux" id="lieux" value="1"/><label for="lieux">' . LG_PATRO_DISP_PLACE . '</label>';
        echo '</td>' . "\n";

        echo '<td class="rupt_table">';
        echo '<input type="checkbox"';
        if ($limiter) echo ' checked="checked"';
        echo ' name="limiter" id="limiter" value="1"/><label for="limiter">' . LG_PATRO_RESTRICT . '</label>&nbsp;(' . my_html($nom_decujus_base) . ')';
        echo '</td>' . "\n";

        if ($est_contributeur) {
            echo '<td class="rupt_table">';
            echo '<input type="checkbox"';
            if ($simu_invit) echo ' checked="checked"';
            echo ' name="simu_invit" id="simu_invit" value="1"/><label for="simu_invit">' . $LG_Simu_No_Granted . '</label>';
            echo '</td>' . "\n";
        }

        echo '<td class="rupt_table"><input type="submit" value="' . my_html($LG_modify_list) . '"/>';
        $alt_img = my_html(LG_PATRO_SHOW_NOSHOW_FIL);
        echo '&nbsp;&nbsp;&nbsp;&nbsp;<img id="masque" src="' . $chemin_images_icones . $Icones['oeil'] . '" alt="' . $alt_img . '" title="' . $alt_img . '"' .
            ' onmouseover="Survole_Clic_Div_Tous(\'MO\',\'' . $Comportement . '\');" onclick="Survole_Clic_Div_Tous(\'CL\',\'' . $Comportement . '\');"/>';
        echo '</td>' . "\n";
        echo '</tr></table>';
        echo '<input type="hidden" id="memo_etat" name="memo_etat"/>';
        echo '</form>' . "\n";
    }

    // Constitution de l'arborescence à partir de la personne '1'
    $Precedent[1] = $Personne['Reference'];
    $Prec_Max     = 1;

    // recherche par niveau jusqu'à ce que l'on ne trouve plus de personnes sur le niveau
    $nb_gen = 2;
    do {
        $Ind_Cour = 0;
        // Balayage des personnes du niveau précédent pour chercher les parents
        for ($nb = 1; $nb <= $Prec_Max; $nb++) {
            $x = Get_Parents($Precedent[$nb], $Num_Pere, $Num_Mere, $Rang);
            if ($Num_Pere != 0) {
                $Courant[++$Ind_Cour] = $Num_Pere;
            }
            if ($Num_Mere != 0) {
                $Courant[++$Ind_Cour] = $Num_Mere;
            }
        }

        if ($Ind_Cour > 0) {
            for ($nb = 1; $nb <= $Ind_Cour; $nb++) {
                $x = Accede_Personne($Courant[$nb]);
            }
        }

        // Bascule des personnes du courant vers le précedent
        if ($Ind_Cour > 0) {
            $Precedent = $Courant;
            $Prec_Max = $Ind_Cour;
        }
        ++$nb_gen;
    } while ($Ind_Cour > 0);

    $Res->closeCursor();

    // Libération de la mémoire
    unset($Precedent);
    unset($Courant);

    // Optimisation : sauveagarde du nom de la table unions
    $n_union = nom_table('unions');

    // Tri de la table des personnes qui contient NOM/rang lecture/référence
    sort($Liste_Pers);

    /*
	// Affichage de la liste des personnes
	echo '<hr>';
	$i = 0;
	while ($i < $Num_Pers) {
		$Ligne = $Liste_Pers[$i];
		echo $Ligne.'<br />';
		$i++;
	}
	*/

    // Affichage de la liste
    $i = 0;
    $num_div = 0;
    $Anc_Nom  = '';
    $Nouv_Nom = '';
    $num_lig = 0;
    $fin = false;
    $deb = false;
    $aff = true;
    $deb_lien_nom = '<a href="' . Get_Adr_Base_Ref() . 'Liste_Pers2.php?Type_Liste=P&amp;idNom=';

    $h_LG_PATRO_FILIATION = my_html(LG_PATRO_FILIATION);
    $h_LG_show_noshow  = my_html($LG_show_noshow);
    $h_LG_PATRO_THEN = my_html(LG_PATRO_THEN);

    while (($i < $Num_Pers) and (!$fin)) {
        $Ligne = $Liste_Pers[$i];
        $ar_ligne = explode('/', $Ligne);
        $Nouv_Nom = $ar_ligne[0];
        if ($Anc_Nom != $Nouv_Nom) {
            if ($limiter) {
                if ($Nouv_Nom == $nom_decujus_base) $deb = true;
                if (($Nouv_Nom != $nom_decujus_base) and ($deb)) $fin = true;
                if ((!$deb) or ($fin)) $aff = false;
                else $aff = true;
            }
            if ($aff) {
                if (!$limiter) {
                    if ($Anc_Nom != '') {
                        HTML_ou_PDF('</table>', $sortie);
                        if (! $texte) echo '</div>' . "\n";
                        HTML_ou_PDF('<br />' . "\n", $sortie);
                    }
                }
                $num_div++;
                if ($texte) $classe = 'tableau_imp';
                else $classe = 'classic';
                HTML_ou_PDF('<table width="95%" border="0" class="' . $classe . '" cellspacing="1" align="center" >', $sortie);
                if (($texte) and (!$sortie_pdf)) echo '<thead>';
                HTML_ou_PDF('<tr align="center">', $sortie);
                if ($texte) HTML_ou_PDF('<th>', $sortie);
                else echo '<td class="rupt_table">';
                if ($texte) {
                    if ($pdf) $pdf->Cell(0, 5, $h_LG_PATRO_FILIATION . ' ' . $Nouv_Nom, 'B', 1, 'C');
                    else
                        HTML_ou_PDF('<b>' . $h_LG_PATRO_FILIATION . ' ' . $Nouv_Nom, $sortie);
                }
                if (!$texte) echo $deb_lien_nom . $ar_ligne[3] . '&amp;Nom=' . $Nouv_Nom . '">' . $Nouv_Nom . '</a>';
                HTML_ou_PDF('</b>', $sortie);
                // Affichage de l'oeil pour afficher / masquer un patronyme
                if (! $texte) oeil_div_simple('ajout' . $num_div, 'ajout' . $i, $h_LG_show_noshow, 'div' . $num_div);
                if (!$texte) HTML_ou_PDF('</td></tr>', $sortie);
                if (($texte) and (!$sortie_pdf)) echo '</th></tr></thead><tr><td></td></tr>';
                HTML_ou_PDF('</table>', $sortie);
                if (!$texte) echo '<div id="div' . $num_div . '">';
                HTML_ou_PDF('<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" align="center">', $sortie);
                $num_lig = 0;
            }
        }

        if ($aff) {
            // Ligne du couple
            $style = '';
            if (! $texte) {
                if (pair($num_lig++)) $style = 'liste';
                else $style = 'liste2';
            }
            HTML_ou_PDF('<tr class="' . $style . '" align="center">', $sortie);
            // Gestion de la rupture sur le nom
            if ($Anc_Nom != $Nouv_Nom) {
                HTML_ou_PDF('<td width="5%"> </td>', $sortie);
            } else {
                HTML_ou_PDF('<td width="5%">' . "\n", $sortie);
                if (! $texte) echo $icone_Puis;
                HTML_ou_PDF($h_LG_PATRO_THEN . ' ', $sortie);
                HTML_ou_PDF('</td>', $sortie);
            }
            // Affichage de de la personne
            $DifU = 0;
            HTML_ou_PDF('<td width="35%">', $sortie);
            //$P2 = strpos($Ligne,'/',$P1+1);
            //$Ref = substr($Ligne,$P2+1,strlen($Ligne)-$P1);
            $Ref = $ar_ligne[2];
            //echo "Ref : ".$Ref."&nbsp;";
            $x = Accede_Personne2($Ref);
            HTML_ou_PDF('</td>', $sortie);
            // Affichage d'un conjoint
            HTML_ou_PDF('<td width="35%">', $sortie);
            $Aff_Union = '';
            $SexePers = $Personne['Sexe'];
            if (($SexePers == 'm') or ($SexePers == 'f')) {
                $sqlU = 'select Conjoint_1, Conjoint_2, Maries_Le, Ville_Mariage from ' . $n_union . ' where ';
                switch ($SexePers) {
                    case 'm':
                        $sqlU = $sqlU . 'Conjoint_1 = ' . $Ref;
                        break;
                    case 'f':
                        $sqlU = $sqlU . 'Conjoint_2 = ' . $Ref;
                        break;
                }
                $sqlU .= ' limit 1';
                $resU = lect_sql($sqlU);
                $rowU = $resU->fetch(PDO::FETCH_NUM);
                $Aff_Union = 'x';
                if ($rowU) {
                    $Mari  = $rowU[0];
                    $Femme = $rowU[1];
                    switch ($SexePers) {
                        case 'm':
                            $Conj = $Femme;
                            break;
                        case 'f':
                            $Conj = $Mari;
                            break;
                    }
                    $x = Accede_Personne2($Conj);
                    if ($rowU[2] != '') {
                        $Aff_Union = $Aff_Union . ' ' . Etend_date($rowU[2]);
                    }
                    if ($lieux) {
                        $Vil_Cour = $rowU[3];
                        if ($Vil_Cour != 0) {
                            if ($Vil_Cour != $Vil_Prec) {
                                $Ville = lib_ville($Vil_Cour);
                                $Vil_Prec = $Vil_Cour;
                            }
                            $Aff_Union = $Aff_Union . ' ' . $LG_at . ' ' . $Ville;
                        }
                    }
                }
                $resU->closeCursor();
            }
            HTML_ou_PDF('</td>', $sortie);

            // Affichage de l'union
            HTML_ou_PDF('<td width="25%">', $sortie);
            if ($DifU == 0) {
                if ($Aff_Union != 'x') {
                    HTML_ou_PDF($Aff_Union, $sortie);
                    if (($lieux) and (!$texte)) appelle_carte_osm();
                } else HTML_ou_PDF(' ', $sortie);
            } else {
                echo HTML_ou_PDF(my_html($LG_Data_noavailable_profile) . '<br />', $sortie);
            }
            HTML_ou_PDF('</td></tr>', $sortie);
        }

        $Anc_Nom = $Nouv_Nom;
        $i++;
    }

    HTML_ou_PDF('</table>', $sortie);
    if (!$texte) echo '</div>' . "\n";

    if (!$texte) include(__DIR__ . '/assets/js/Liste_Patro.js');
} else $x = Erreur_DeCujus();

if ($sortie_pdf) {
    //echo 'sortie pdf : '.$sortie_pdf;
    $pdf->Output();
    exit;
}

if (! $texte) Insere_Bas($compl);
?>
</body>

</html>