<?php

//=====================================================================
// Liste des personnes vivantes portant un nom ou tous les noms
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (L)ecture
$titre = $LG_Menu_Title['Living_Pers'];
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

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();

if (!$texte) include(__DIR__ . '/assets/js/Liste_Patro.js');        // Même javascript que la liste patronymique

$Nom = 0;
// Récupération des variables de l'affichage précédent
$tab_variables = array('Nom', 'ignorer');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
$Nom     = Secur_Variable_Post($Nom, 1, 'N');
$ignorer = Secur_Variable_Post($ignorer, 1, 'S');

if ($texte) {
    $Nom = Recup_Variable('Nom', 'N', 1);
}

$Nom_Fam = '';
if (!$Nom) $Nom = -1;

// L'utilisateur a demandé à ignorer les personnes sans date de naissance
$crit_nai = '';
if ($ignorer == 'O') $crit_nai = ' and Ne_le <> \'\' ';

// L'utilisateur a demandé un nom
if ($Nom != -2) $crit_nom = ' AND idNomFam = ' . $Nom . ' ';
else            $crit_nom = '';

$sortie = 'H';

$comp_texte = '';
$comp_texte .= '&amp;Nom=' . $Nom;

$compl = Ajoute_Page_Info(600, 180) .
    Affiche_Icone_Lien('href="' . $root . '/liste_nom_vivants.php?texte=O' . $comp_texte . '"', 'text', my_html($LG_printable_format)) . '&nbsp;';
if ((!$SiteGratuit) or ($Premium)) {
    $compl .= Affiche_Icone_Lien('href="' . $root . '/liste_nom_vivants.php?texte=O&amp;pdf=O' . $comp_texte . '"', 'PDF', my_html($LG_pdf_format)) . '&nbsp;';
}

if (! $texte) {
    Insere_Haut($titre, $compl, 'Liste_Nom_Vivants', $Nom);
} else {
    // Sortie dans un PDF
    if ($sortie_pdf) {
        if ($debug) echo 'demande sortie pdf...<br />';
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
        if ($debug) echo 'fin décl pdf...<br />';
    }
    // Sortie au format texte
    else {
        // Affichage du titre : numéros + génération
        Insere_Haut_texte($titre);
    }
}

// Calcul de la date du jour - 130 ans (on suppose que 130 ans est la durée de vie maximum d'un être humain)
$A = date('Y') - 130;
$M = date('m');
$J = date('d');
$xA = zerofill4($A);
$xM = zerofill2($M);
$xJ = zerofill2($J);
$date_lim = $xA . $xM . $xJ;

$num_lig = 0;
$num_nom = 0;

if (!$texte) {
    // Constitution de la requête d'extraction
    $sql = 'select Nom, Ne_le, idNomFam '
        . ' from ' . nom_table('personnes')
        . ' where Reference <> 0 and (Decede_Le = \'\' or Decede_Le is null) ';
    if (!$_SESSION['estPrivilegie']) $sql .= 'and Diff_Internet = \'O\' ';
    $sql .= $crit_nai . 'order by Nom, Prenoms';

    echo '<form action="' . my_self() . '?Nom=' . $Nom . '" method="post">' . "\n";
    echo '<table border="0" width="60%" align="center">' . "\n";
    echo '<tr align="center">';
    echo '<td class="rupt_table">Nom&nbsp;:&nbsp;' . "\n";
    echo '<select name="Nom">' . "\n";
    echo '<option value="-2">' . $LG_All . '</option>' . "\n";
    if ($res = lect_sql($sql)) {
        $Anc_Nom = -1;
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $Ne = $row[1];
            if (is_null($Ne))
                $Ne = '';
            if (determine_etat_vivant($Ne)) {
                $nom_lu = $row[2];
                if ($nom_lu != $Anc_Nom) {
                    echo '<option value="' . $nom_lu . '"';
                    if ($Nom == addslashes($nom_lu)) {
                        echo ' selected="selected"';
                    }
                    echo '>' . my_html($row[0]) . "</option>\n";
                    $Anc_Nom = $nom_lu;
                }
            }
        }
    }
    $res->closeCursor();

    echo "</select>\n";
    echo '</td><td class="rupt_table"><label for="ignorer">' . LG_LIVING_IGNORE . '</label>&nbsp;';
    echo '<input type="checkbox" id="ignorer" name="ignorer" value="O" ';
    if ($ignorer == 'O') echo 'checked="checked"';
    echo '/></td>' . "\n";
    echo '<td class="rupt_table"><input type="submit" value="' . $LG_modify_list . '"/>' . "\n";
    $alt_img = LG_LIVING_SHOW_HIDE;
    echo '&nbsp;&nbsp;&nbsp;&nbsp;<img id="masque" src="' . $chemin_images_icones . $Icones['oeil'] . '" alt="' . $alt_img . '" title="' . $alt_img . '"' .
        ' onmouseover="Survole_Clic_Div_Tous(\'MO\',\'' . $Comportement . '\');" onclick="Survole_Clic_Div_Tous(\'CL\',\'' . $Comportement . '\');"/>';
    echo '</td></tr>';
    echo "  </table>\n";
    echo '<input type="hidden" id="memo_etat" name="memo_etat"/>';
    echo " </form>\n";
}


if ($Nom != '-1') {

    $sql = "select Reference, Nom, Prenoms, Statut_Fiche, Diff_Internet, Ne_le, Decede_Le, idNomFam"
        . " from " . nom_table('personnes')
        . " where Reference <> 0 and (Decede_Le = '' or Decede_Le is null) ";
    if (!$_SESSION['estPrivilegie']) $sql .= "and Diff_Internet = 'O' ";
    $sql .= $crit_nai . $crit_nom . " order by Nom, Prenoms";
    $res = lect_sql($sql);

    if ($debug) {
        echo $sortie . '<br />';
        echo $sortie_pdf . '<br />';
        echo $texte . '<br />';
    }
    HTML_ou_PDF(LG_LIVING_REF_DATE . ' : ' . Etend_date($date_lim_vivant . 'GL') . ' (' . LG_LIVING_TODAY . ' - ' . $annees_maxi_vivant . ' ' . LG_LIVING_YEARS . ')<br /><br />', $sortie);

    // Initialisations
    $Anc_Nom = '';        // Zone pour le calcul de la rupture sur le nom
    $attente = 0;         // Top attente d'affichage de l'oeil
    $existe_div = 0;    // Top indiquant si un div a été déclaré
    $num_pers = 0;        // Compteur pour identifiant image / div

    while ($row = $res->fetch(PDO::FETCH_NUM)) {

        $naissance = $row[5];
        if (is_null($naissance))
            $naissance = '';
        $vivant = determine_etat_vivant($naissance);

        // On ne traite que les personnes réputées vivantes
        if ($vivant) {
            $Nouv_Nom = my_html($row[1]);
            if ($Nouv_Nom != $Anc_Nom) {
                if ($debug) echo 'Changement de nom<br />';
                if ($Anc_Nom != '') {
                    HTML_ou_PDF('</table>', $sortie);
                    if (($existe_div) and (!$texte)) HTML_ou_PDF('</div>', $sortie);
                    HTML_ou_PDF('<br />', $sortie);
                    $existe_div = 0;
                }
                if ($debug) echo 'Nouv_Nom : ' . $Nouv_Nom . '<br />';
                if (! $texte) {
                    echo '<table width="95%" class="classic" cellspacing="1" cellpadding="3" align="center"><tr align="center"><td class="rupt_table">';
                    echo '<a href="' . $root . '/liste_pers2.php?Type_Liste=P&amp;idNom=' . $row[7] . '&amp;Nom=' . $Nouv_Nom . '">' . $Nouv_Nom . "</a>\n";
                } else {
                    if ($debug) echo 'Entête<br />';
                    HTML_ou_PDF('<table width="95%" align="center" class="tableau_imp"><thead><tr><th>' . $Nouv_Nom . '</th></tr></thead>', $sortie);
                }
                $attente = 1;
                $Anc_Nom = $Nouv_Nom;
                $num_lig = 0;
            }
            if ($row[2] != '') {
                if ($attente) {
                    $num_nom++;
                    $num_pers++;
                    if (! $texte) {
                        oeil_div_simple('ajout' . $num_pers, 'ajout' . $num_pers, my_html($LG_show_noshow), 'div' . $num_pers);
                        echo '</td></tr>';
                    }
                    if (! $texte) echo '</table>';
                    $attente = 0;
                    $existe_div = 1;
                    if (! $texte) echo '<div id="div' . $num_pers . '"><table width="95%" class="classic" cellspacing="1" cellpadding="3" align="center">';
                }
                $style = 'liste2';
                if (pair($num_lig++)) $style = 'liste';
                if (! $texte) echo '<tr class="' . $style . '"><td>';
                else HTML_ou_PDF('<tr><td>', $sortie);
                if (! $texte) {
                    if ($est_contributeur) {
                        if ($row[4] == 'O') echo '<img src="' . $root . '/assets/img/' . $Icones['internet_oui'] . '" alt="' . $LG_show_on_internet . '" title="' . $LG_show_on_internet . '">';
                        else echo '<img src="' . $root . '/assets/img/' . $Icones['internet_non'] . '" alt="' . $LG_noshow_on_internet . '" title="' . $LG_noshow_on_internet . '">';
                    }
                    echo '<a ' . Ins_Ref_Pers($row[0]) . '>' . my_html($row[2]) . '</a>' . "\n";
                } else HTML_ou_PDF($row[2], $sortie);
                if (! $texte) {
                    if ($est_gestionnaire) echo ' <a ' . Ins_Edt_Pers($row[0]) . '><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
                }
                if ($naissance != '') HTML_ou_PDF(' ° ' . Etend_date($naissance), $sortie);
                HTML_ou_PDF('</td></tr>', $sortie);
                if (!$sortie_pdf) echo "\n";
            }
        }
    }
    HTML_ou_PDF('</table>', $sortie);
    if (!$texte) echo '</div>';
    $res->closeCursor();
}

if (! $texte) Insere_Bas($compl);

if ($sortie_pdf) {
    $pdf->Output();
    exit;
}

?>
</body>

</html>