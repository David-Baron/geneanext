<?php
//=====================================================================
// Fiche couple au format texte
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

function affiche_image($ref)
{
    global $pdf, $image, $chemin_images_util, $sortie_pdf;
    $image = Rech_Image_Defaut($ref, 'P');
    if ($image != '') {
        $ch_img = $chemin_images_util . $image;
        if ($sortie_pdf)
            $pdf->Image($ch_img, 15, null, 30);
        else {
            echo '<table><tr><td align="center" valign="middle">';
            Aff_Img_Redim_Lien($ch_img, 100, 100, 'id' . $ref);
            echo '</td></tr></table>';
        }
    }
}

// Affiche au besoin le fieldset pour les unions
function aff_cadre_union()
{
    global $cadre_union, $sortie_pdf;
    if (!$cadre_union) {
        if (!$sortie_pdf) echo '<fieldset><legend>' . LG_TARGET_OBJECT_UNION . '</legend>';
        $cadre_union = true;
    }
}

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = LG_COUPLE_REPORT_TITLE;
$niv_requis = 'P';                        // Page accessible uniquement aux privilégiés

// Sortie en pdf ?
$sortie_pdf = false;
if ((!$SiteGratuit) or ($Premium)) {
    $s_pdf = Recup_Variable('pdf', 'C', 'O');
    if (!$s_pdf) $s_pdf = 'n';
    if ($s_pdf == 'O') $sortie_pdf = true;
}

if ($sortie_pdf) $no_entete = true;        // Pas d'entête HTML sinon le PDF ne s'affichera pas
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Recup de la variable passée dans l'URL : référence de l'union, et pdf o ?
$Reference = Recup_Variable('Reference', 'N');

$decal = '   ';

// Récupération de l'union
$sql = 'select * from ' . nom_table('unions') . ' where Reference = ' . $Reference . ' limit 1';
if ($res = lect_sql($sql)) {
    if ($enr_union = $res->fetch(PDO::FETCH_ASSOC)) {
        $Conjoint_1  = $enr_union['Conjoint_1'];
        $Conjoint_2 = $enr_union['Conjoint_2'];

        $n_personnes = nom_table('personnes');
        $deb_p = 'select * from ' . $n_personnes;
        // Récupération des informations concernant le mari
        $sql = $deb_p . ' where reference = ' . $Conjoint_1 . ' limit 1';
        $res = lect_sql($sql);
        $enr_mari = $res->fetch(PDO::FETCH_ASSOC);
        rectif_null_pers($enr_mari);
        $enr2_mari = $enr_mari;

        // Récupération des informations concernant la femme
        $sql = $deb_p . ' where reference = ' . $Conjoint_2 . ' limit 1';
        $res = lect_sql($sql);
        $enr_femme = $res->fetch(PDO::FETCH_ASSOC);
        rectif_null_pers($enr_femme);
        $enr2_femme = $enr_femme;

        $gen = Calc_Gener($enr_mari['Numero']);

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
            $cadre = '';
            $pdf->Cell(0, 5, chaine_pdf($enr_mari['Numero'] . ' x ' . $enr_femme['Numero']), $cadre, 1, 'C');
            $pdf->SetFont($font_pdf, '', 11);
            if ($gen != '')
                $pdf->Cell(0, 5, chaine_pdf($gen), '', 1, 'C');

            if ($gen != '')
                $haut = 12;
            else
                $haut = 7;
            $pdf->RoundedRectH(10, 9, $pdf->GetPageWidth() - 20, $haut, 3.5, 'D');
            $pdf->Ln();
        }
        // Sortie au format texte
        else {
            $sortie = 'H';
            // Affichage du titre : numéros + génération
            echo '</head>' . "\n";
            echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
            echo '<table cellpadding="0" width="100%">' . "\n";
            echo '<tr>' . "\n";
            echo '<td align="center"><b>' . StripSlashes($enr_mari['Numero'] . '</b > x <b>' . $enr_femme['Numero']) . '</b></td>' . "\n";
            echo '</tr>' . "\n";
            echo '</table>' . "\n";
            echo '<table cellpadding="0" width="100%">' . "\n";
            echo '<tr><td align="center">' . Calc_Gener($enr_mari['Numero']) . '</td></tr>' . "\n";
            echo '</table>' . "\n";
        }

        // Affichage des données du mari et affichage des parents
        HTML_ou_PDF('<br />', $sortie);
        if (!$sortie_pdf) echo '<fieldset><legend>' . LG_COUPLE_REPORT_PERSON . '</legend>';
        $sur = $enr2_mari['Surnom'];
        if ($sur != '') $sur = ', ' . LG_COUPLE_REPORT_NICK_M . ' ' . $sur;
        HTML_ou_PDF('<b>' . $enr2_mari['Prenoms'] . ' ' . $enr2_mari['Nom'] . $sur . '</b><br />' . "\n", $sortie);
        // Affichage de l'image
        affiche_image($enr2_mari['Reference']);
        Aff_Personne($enr2_mari, $enr2_mari['Reference'], true, 'T', $sortie_pdf);
        HTML_ou_PDF('<br />' . "\n", $sortie);
        if (Rech_Commentaire($enr_union['Conjoint_1'], 'P')) {
            HTML_ou_PDF($decal . LG_COUPLE_REPORT_COMMENT . ' : ' . $Commentaire . '<br /><br />', $sortie);
        }
        if (!$sortie_pdf) echo '</fieldset>';

        $cadre_union = false;
        $Date_Mar  = $enr_union['Maries_Le'];
        $Ville_Mar = $enr_union['Ville_Mariage'];
        if (($Date_Mar != '') or ($Ville_Mar != 0)) {
            aff_cadre_union();
            HTML_ou_PDF(LG_COUPLE_REPORT_UNION . ' ' . Etend_date($Date_Mar), $sortie);
            if ($Ville_Mar != 0) {
                HTML_ou_PDF(' ' . LG_AT . ' ' . lib_ville($Ville_Mar, 'N'), $sortie);
            }
            // Recherche d'un divorce éventuel
            if ($Date_Mar != '') {
                if (get_divorce($Reference)) HTML_ou_PDF($lib_div, $sortie);
            }
            HTML_ou_PDF('<br />' . "\n", $sortie);
        }
        $Date_K        = $enr_union['Date_K'];
        $Ville_Notaire = $enr_union['Ville_Notaire'];
        $Notaire       = $enr_union['Notaire_K'];

        if (($Date_K != '') or ($Ville_Notaire != 0)) {
            aff_cadre_union();
            HTML_ou_PDF(LG_COUPLE_REPORT_CONTRACT . Etend_date($Date_K), $sortie);
            if ($Notaire != '') HTML_ou_PDF(LG_COUPLE_REPORT_CONTRACT_NOTARY . $Notaire, $sortie);
            if ($Ville_Notaire != '') HTML_ou_PDF(', ' . LG_COUPLE_REPORT_CONTRACT_NOTARY_WHERE . ' ' . lib_ville($Ville_Notaire, 'N'), $sortie);
            HTML_ou_PDF('<br />' . "\n", $sortie);
        }

        if (Rech_Commentaire($enr_union['Reference'], 'U')) {
            aff_cadre_union();
            HTML_ou_PDF(LG_COUPLE_REPORT_COMMENT . ' : ' . $Commentaire . '<br />', $sortie);
        }
        if ((!$sortie_pdf) and ($cadre_union)) echo '</fieldset>';

        // Affichage des données de la femme et affichage des parents
        if ($sortie_pdf) HTML_ou_PDF('<br />', $sortie);
        if (!$sortie_pdf) echo '<fieldset><legend>' . LG_COUPLE_REPORT_HUSB_WIF . '</legend>';
        $sur = $enr2_femme['Surnom'];
        if ($sur != '') $sur = ', ' . LG_COUPLE_REPORT_NICK_F . ' ' . $sur;
        HTML_ou_PDF('<b>' . $enr2_femme['Prenoms'] . ' ' . $enr2_femme['Nom'] . $sur . '</b><br />' . "\n", $sortie);
        // Affichage de l'image
        affiche_image($enr2_femme['Reference']);
        Aff_Personne($enr2_femme, $enr2_femme['Reference'], true, 'T', $sortie_pdf);
        if (Rech_Commentaire($enr_union['Conjoint_2'], 'P')) {
            HTML_ou_PDF('<br />' . $decal . LG_COUPLE_REPORT_COMMENT . ' : ' . $Commentaire . '<br />', $sortie);
        }
        $res->closeCursor();
        if (!$sortie_pdf) echo '</fieldset>';

        // Récupération des enfants avec le conjoint
        if ($sortie_pdf) HTML_ou_PDF('<br />', $sortie);
        if (!$sortie_pdf) echo '<fieldset><legend>' . LG_COUPLE_REPORT_CHILDREN . '</legend>';
        else HTML_ou_PDF(LG_COUPLE_REPORT_CHILDREN . ' : <br />' . "\n", $sortie);
        $sql = 'select Enfant from ' . nom_table('filiations') .
            ' where pere = ' . $enr_mari['Reference'] .
            ' and mere = ' . $enr_femme['Reference'] .
            ' order by rang';
        $res = lect_sql($sql);
        $LaVille  = 0;
        $LibVille = '';
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $Enfant = $row['Enfant'];
            $sqlEnf = 'select Nom, Prenoms, Ne_le, Decede_Le, Ville_Naissance, Ville_Deces, Sexe, Surnom ' .
                ' from ' . $n_personnes . ' where reference = ' . $Enfant . ' limit 1';
            $resEnf = lect_sql($sqlEnf);
            $enr_enf = $resEnf->fetch(PDO::FETCH_ASSOC);
            $Sexe = $enr_enf['Sexe'];
            $sur = $enr_enf['Surnom'];
            if ($sur != '') $sur = Lib_sexe(', dit', $Sexe) . ' ' . $sur;
            HTML_ou_PDF('   ' . $enr_enf['Prenoms'] . ' ' . $enr_enf['Nom'] . $sur, $sortie);
            // Affichage des informations de naissance
            $Date_Nai = Etend_date($enr_enf['Ne_le']);
            $Ville_Nai = $enr_enf['Ville_Naissance'];
            if (($Date_Nai != '') or ($Ville_Nai != 0)) {
                HTML_ou_PDF(Lib_sexe(', ' . LG_COUPLE_REPORT_BORN, $Sexe) . ' ', $sortie);
                if ($Date_Nai != '') {
                    HTML_ou_PDF($Date_Nai . ' ', $sortie);
                }
                if ($Ville_Nai != 0) {
                    // On ne va chercher la ville que sur changement...
                    if ($Ville_Nai != $LaVille) {
                        $Lib_Ville_Nai = lib_ville($Ville_Nai, 'N');
                        $LaVille = $Ville_Nai;
                        $LibVille = $Lib_Ville_Nai;
                    } else {
                        $Lib_Ville_Nai = $LibVille;
                    }
                    HTML_ou_PDF(LG_AT . ' ' . $Lib_Ville_Nai, $sortie);
                }
            }
            // Affichage des informations de décès
            $Date_Dec = Etend_date($enr_enf['Decede_Le']);
            $Ville_Dec = $enr_enf['Ville_Deces'];
            if (($Date_Dec != '') or ($Ville_Dec != 0)) {
                HTML_ou_PDF(Lib_sexe(', ' . LG_COUPLE_REPORT_DEAD, $Sexe) . ' ', $sortie);
                if ($Date_Dec != '') {
                    HTML_ou_PDF($Date_Dec . ' ', $sortie);
                }
                if ($Ville_Dec != 0) {
                    // On ne va chercher la ville que sur changement...
                    if ($Ville_Dec != $LaVille) {
                        $Lib_Ville_Dec = lib_ville($Ville_Dec, 'N');
                        $LaVille = $Ville_Dec;
                        $LibVille = $Lib_Ville_Dec;
                    } else {
                        $Lib_Ville_Dec = $LibVille;
                    }
                    HTML_ou_PDF(LG_AT . ' ' . $Lib_Ville_Dec . ' ', $sortie);
                }
            }
            HTML_ou_PDF('<br />' . "\n", $sortie);
        }
        $res->closeCursor();
        if (!$sortie_pdf) echo '</fieldset>';
    }
    if ($sortie_pdf) {
        $pdf->Output();
        exit;
    }
}
?>
</body>

</html>