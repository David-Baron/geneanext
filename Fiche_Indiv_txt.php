<?php
//=====================================================================
// Fiche couple au format texte ou pdf
//=====================================================================

function fin_fs()
{
    global $sortie_pdf;
    if (!$sortie_pdf) echo '</fieldset>';
}

function aff_rupt($libelle)
{
    global $sortie, $sortie_pdf, $rupt_Fiche_Indiv;
    if (!$sortie_pdf) echo '<fieldset><legend>' . $libelle . '</legend>';
    else HTML_ou_PDF('<br /><u>' . $rupt_Fiche_Indiv . ' ' . $libelle . ' ' . $rupt_Fiche_Indiv . " : </u>\n", $sortie);
}

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

// Affichage des enfants ou de la fratrie
function aff_enf_frat($typo)
{
    global $Reference, $mere_pers, $pere_pers, $sortie, $date_nd, $decal;
    $sp_LG_at = ' ' . LG_AT . ' ';
    $crit = '';
    switch ($typo) {
        case 'E':
            $crit = '(Pere = ' . $Reference . ' or Mere =' . $Reference . ')';
            $lib = LG_PERS_CHILDREN;
            break;
        case 'F':
            if (($pere_pers) and ($mere_pers))
                $crit = '(Pere = ' . $pere_pers . ' or Mere = ' . $mere_pers . ')';
            else {
                if ($pere_pers) $crit = 'Pere = ' . $pere_pers;
                if ($mere_pers) $crit = 'Mere = ' . $mere_pers;
            }
            if ($crit != '')
                $crit .= ' and Enfant <> ' . $Reference;
            $lib = LG_PERS_BROTHERS_SISTERS;
            break;
    }
    $num_enf = 0;
    if ($crit != '') {
        $sql = 'select Sexe, Prenoms, Nom, Surnom, Ne_le, Decede_Le, Ville_Naissance, Ville_Deces' .
            ' from ' . nom_table('filiations') . ' f,' .
            nom_table('personnes')    . ' p' .
            ' where ' . $crit .
            ' and p.Reference = f.Enfant' .
            ' order by Ne_le, Rang';

        if ($resEnf = lect_sql($sql)) {
            $nom_ville = '';
            $memo_ville = 0;
            while ($enregEnf = $resEnf->fetch(PDO::FETCH_ASSOC)) {

                $enreg2 = $enregEnf;
                $num_enf++;

                if ($num_enf == 1) {
                    aff_rupt($lib);
                }

                $sur = $enregEnf['Surnom'];
                if ($sur != '') $sur = ', dite ' . $sur;
                //HTML_ou_PDF('<br />N&deg; '.$num_enf.' : <b>'.$enreg2['Prenoms'].' '.$enreg2['Nom'].$sur.'</b><br />'."\n",$sortie);
                HTML_ou_PDF('<br />' . $num_enf . ') <b>' . $enregEnf['Prenoms'] . ' ' . $enregEnf['Nom'] . $sur . '</b><br />' . "\n", $sortie);

                $Sexe = $enreg2['Sexe'];
                $Ville_Naissance = $enreg2['Ville_Naissance'];
                $Ville_Deces = $enreg2['Ville_Deces'];
                $nom_ville_nai = '';
                if ($Ville_Naissance != 0) {
                    if ($Ville_Naissance != $memo_ville) {
                        $nom_ville = lib_ville($Ville_Naissance, 'N');
                        $memo_ville = $Ville_Naissance;
                    }
                    $nom_ville_nai = $sp_LG_at . $nom_ville;
                }
                $nom_ville_dec = '';
                if ($Ville_Deces != 0) {
                    if ($Ville_Deces != $memo_ville) {
                        $nom_ville = lib_ville($Ville_Deces, 'N');
                        $memo_ville = $Ville_Deces;
                    }
                    $nom_ville_dec = $sp_LG_at . $nom_ville;
                }
                $Date_Nai = $enreg2['Ne_le'];
                if (strlen($Date_Nai) == 10)
                    $Date_Nai = Etend_date($Date_Nai);
                else
                    $Date_Nai = $date_nd;
                $Date_Dec = $enreg2['Decede_Le'];
                if (strlen($Date_Dec) == 10)
                    $Date_Dec = Etend_date($Date_Dec);
                else
                    $Date_Dec = $date_nd;
                $ligne_dates = '';
                if (($Date_Nai != $date_nd) or ($nom_ville_nai != '')) {
                    $ligne_dates .= $decal . Lib_sexe('Né', $Sexe) . ' ' . $Date_Nai . $nom_ville_nai;
                }
                if (($Date_Dec != $date_nd) or ($nom_ville_dec != '')) {
                    if ($ligne_dates != '')
                        $ligne_dates .= ', d';
                    else
                        $ligne_dates .= $decal . 'D';
                    $ligne_dates .= Lib_sexe('écédé', $Sexe) . ' ' . $Date_Dec . $nom_ville_dec;
                }
                HTML_ou_PDF($ligne_dates . '<br />' . "\n", $sortie);
            }
            unset($resEnf);
        }
    }
    if ($num_enf) {
        // Fin du fieldset
        $x = fin_fs();
    } else HTML_ou_PDF('<br />' . "\n", $sortie);
}

// Gestion standard des pages
session_start();
include('fonctions.php');
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Indiv_Text_Report']; // Titre pour META
$niv_requis = 'P';                     // Page accessible uniquement aux privilégiés

// Sortie en pdf ?
$sortie_pdf = false;
if ((!$SiteGratuit) or ($Premium)) {
    $s_pdf = Recup_Variable('pdf', 'C', 'O');
    if (!$s_pdf) $s_pdf = 'n';
    if ($s_pdf == 'O') $sortie_pdf = true;
}

if ($sortie_pdf) $no_entete = true;        // Pas d'entête HTML sinon le PDF ne s'affichera pas
$x = Lit_Env();                            // Lecture de l'indicateur d'environnement

// Recup de la variable passée dans l'URL : référence de la personne, et pdf o ?
$Reference = Recup_Variable('Reference', 'N');
// Récupération des informations concernant la personne
$req_sel = 'select * from ' . nom_table('personnes') . ' where Reference = ' . $Reference . ' limit 1';

include('Gestion_Pages.php');

// Dans le cas de la sortie pdf, l'accès n'est pas réalisé dans Gestion_Pages...
if ($sortie_pdf) {
    $res_sel = lect_sql($req_sel);
    $enreg_sel = $res_sel->fetch(PDO::FETCH_ASSOC);
}

// Personne inconnue, circulez...
if ((!$enreg_sel) or ($Reference == 0)) Retour_Ar();

rectif_null_pers($enreg_sel);
$enr_pers = $enreg_sel;
unset($enreg_sel);
$enr2_pers = $enr_pers;
$sexe = $enr_pers['Sexe'];

$decal = '   ';
$date_nd = 'le ?';

$gen = Calc_Gener($enr_pers['Numero']);

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

    /*
	$cadre = 'LTR';
	if ($gen == '')
		$cadre = 'LTBR';
	$pdf->Cell(0, 5, chaine_pdf($enr_pers['Prenoms'].' '.$enr_pers['Nom']) , $cadre , 1, 'C');
	$pdf->SetFont($font_pdf,'',11);
	if ($gen != '')
		$pdf->Cell(0, 5, chaine_pdf($gen) , 'LBR' , 1, 'C');
	*/
    $cadre = '';
    $pdf->Cell(0, 5, chaine_pdf($enr_pers['Prenoms'] . ' ' . $enr_pers['Nom']), $cadre, 1, 'C');
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
    Insere_Haut_texte($enr_pers['Prenoms'] . ' ' . $enr_pers['Nom']);
    echo '<table cellpadding="0" width="100%">' . "\n";
    if ($gen != '')
        echo '<tr><td align="center">' . Calc_Gener($enr_pers['Numero']) . '</td></tr>' . "\n";
    echo '</table>' . "\n";
    if (!$sortie_pdf) {
        echo '<fieldset>';
        aff_legend(LG_PERS_PERS);
    }
}

// Affichage des données de la personne et de ses parents
$sur = $enr2_pers['Surnom'];
if ($sur != '')
    HTML_ou_PDF('<br /><b>' . $enr2_pers['Prenoms'] . ', ' . lib_sexe_nickname($sexe) . ' ' . $sur . '</b><br />' . "\n", $sortie);
// HTML_ou_PDF('<br /><b>'.$enr2_pers['Prenoms'].', '.${'lg_pers_nickname_'.$sexe}.' '.$sur.'</b><br />'."\n",$sortie);
// Affichage de l'image
affiche_image($enr2_pers['Reference']);
Aff_Personne($enr2_pers, $enr2_pers['Reference'], false, 'T', $sortie_pdf);
$pere_pers = $Pere;
$mere_pers = $Mere;
HTML_ou_PDF('<br />' . "\n", $sortie);

if (Rech_Commentaire($Reference, 'P')) {
    HTML_ou_PDF($decal . 'Note : ' . $Commentaire . '<br />', $sortie);
}
// Fin du fieldset
$x = fin_fs();

// Affichage des conjoints
$sql = 'select * from ' . nom_table('unions') . ' where Conjoint_1 = ' . $Reference . ' or Conjoint_2 =' . $Reference . ' order by maries_Le';

if ($resUn = lect_sql($sql)) {
    $num_conj = 0;
    while ($enregUn = $resUn->fetch(PDO::FETCH_ASSOC)) {
        $num_conj++;

        if ($num_conj == 1)
            aff_rupt('Conjoints');

        HTML_ou_PDF('<br />' . "\n", $sortie);

        if ($enregUn['Conjoint_2'] == $Reference) $Conj = $enregUn['Conjoint_1'];
        else $Conj = $enregUn['Conjoint_2'];

        $sql = 'select * from ' . nom_table('personnes') . ' where reference = ' . $Conj . ' limit 1';
        $resP = lect_sql($sql);
        $enregP = $resP->fetch(PDO::FETCH_ASSOC);
        $resP->closeCursor();
        rectif_null_pers($enregP);

        $sur = $enregP['Surnom'];
        if ($sur != '') $sur = ', ' . lib_sexe_nickname($enregP['Sexe']) . ' ' . $sur;
        HTML_ou_PDF($num_conj . ') <b>' . $enregP['Prenoms'] . ' ' . $enregP['Nom'] . $sur . '</b><br />' . "\n", $sortie);
        Aff_Personne($enregP, $enregP['Reference'], true, 'T', $sortie_pdf);

        $Ref_Union     = $enregUn['Reference'];
        $Date_Mar      = $enregUn['Maries_Le'];
        $Ville_Mar     = $enregUn['Ville_Mariage'];
        $Mari          = $enregUn['Conjoint_1'];
        $Femme         = $enregUn['Conjoint_2'];
        $Date_K        = $enregUn['Date_K'];
        $Ville_Notaire = $enregUn['Ville_Notaire'];
        $Notaire       = $enregUn['Notaire_K'];

        if (($Date_Mar != '') or ($Ville_Mar)) {
            HTML_ou_PDF($decal . LG_PERS_MARIED . ' ' . Etend_date($Date_Mar), $sortie);
            // Recherche d'un divorce éventuel
            if ($Date_Mar != '') {
                if (get_divorce($Ref_Union)) HTML_ou_PDF($lib_div, $sortie);
            }
            if ($Ville_Mar != 0) HTML_ou_PDF(' ' . LG_AT . ' ' . lib_ville($Ville_Mar), $sortie);
        }

        if (($Date_K != '') or ($Ville_Notaire != 0)) {
            HTML_ou_PDF(', ' . LG_PERS_CONTRACT . ' ' . Etend_date($Date_K), $sortie);
            if ($Notaire != '') HTML_ou_PDF(' ' . LG_PERS_MAITRE . ' ' . $Notaire . ', ' . LG_PERS_NOTARY, $sortie);
            if ($Ville_Notaire != 0) HTML_ou_PDF(' ' . LG_AT . ' ' . lib_ville($Ville_Notaire), $sortie);
        }
    }
    unset($resUn);
}

if ($num_conj) {
    // Fin du fieldset
    $x = fin_fs();
} else HTML_ou_PDF('<br />' . "\n", $sortie);

// Affichage des enfants
aff_enf_frat('E');

// Affichage de la fratrie
aff_enf_frat('F');

// Affichage des évènements de la personne, sauf profession déjà affichée si pas de dates
$sql = 'SELECT Libelle_Type, Titre, p.Debut AS dDebP , p.Fin AS dFinP , p.Evenement as refEve ,' .
    ' e.Identifiant_Zone as idZoneE , e.Identifiant_Niveau as NiveauE, ' .
    ' p.Identifiant_zone as idZoneP, p. Identifiant_Niveau as NiveauP, r.Code_Role, ' .
    ' Libelle_Role, e.Debut AS dDebE , e.Fin AS dFinE' .
    ' FROM ' . nom_table('evenements') . ' e ,' .
    nom_table('participe') . ' p ,' .
    nom_table('types_evenement') . ' t ,' .
    nom_table('roles') . ' r ' .
    ' WHERE Personne = ' . $Reference .
    ' AND (e.Code_Type != "OCCU" ' .
    'or (p.Debut != "" and p.Debut is not null) or (p.Fin != "" and p.Fin is not null)' .
    ' or (e.Debut != "" and e.Debut is not null) or (e.Fin != "" and e.Fin is not null))' .
    ' AND e.Code_Type = t.Code_Type AND p.Evenement = e.Reference AND p.Code_Role = r.Code_Role' .
    ' order by Libelle_Type, dDebE, dFinE';
if ($resEvt = lect_sql($sql)) {
    $num_evt = 0;
    while ($enregEvt = $resEvt->fetch(PDO::FETCH_ASSOC)) {
        //$zone = LectZone($idZone,$enreg['Niveau']);
        $enreg2 = $enregEvt;
        $num_evt++;

        if ($num_evt == 1)
            aff_rupt(LG_PERS_EVENTS);

        HTML_ou_PDF('<br />' . $num_evt . ') <b>' . $enreg2['Libelle_Type'] . ' : ' . $enreg2['Titre'] . $sur . '</b><br />' . "\n", $sortie);
        // Dates et lieux de l'évènement
        $dDeb = $enreg2['dDebE'];
        $dFin = $enreg2['dFinE'];
        $idZone = $enreg2['idZoneE'];
        $Niveau = $enreg2['NiveauE'];
        $ligne = '';
        if (($dDeb != '') or ($dFin != '')) {
            $plage = Etend_2_dates($dDeb, $dFin);
            $ligne .= $plage . ' ';
        }
        if ($idZone) {
            $zone = LectZone($idZone, $Niveau);
            if ($ligne != '')
                $ligne .= ', ';
            $ligne .= LG_PERS_WHERE . ' : ' . $zone . ' ';
        }
        if ($ligne != '')
            HTML_ou_PDF($decal . LG_PERS_EVENT . ' : ' . $ligne . '<br />' . "\n", $sortie);
        // Dates et lieux de la participation
        $dDeb = $enreg2['dDebP'];
        $dFin = $enreg2['dFinP'];
        $idZone = $enreg2['idZoneP'];
        $Niveau = $enreg2['NiveauP'];
        $Code_Role  = $enreg2['Code_Role'];
        $ligne = '';
        if (($dDeb != '') or ($dFin != '')) {
            $plage = Etend_2_dates($dDeb, $dFin);
            $ligne .= $plage . ' ';
        }
        if ($idZone) {
            $zone = LectZone($idZone, $Niveau);
            if ($ligne != '')
                $ligne .= ', ';
            $ligne .= 'lieu : ' . $zone . ' ';
        }
        if ($ligne != '')
            HTML_ou_PDF($decal . LG_PERS_PARTICIPATION . ' : ' . $ligne . '<br />' . "\n", $sortie);
        // Rôle
        $Libelle_Role = $enreg2['Libelle_Role'];
        if (($Libelle_Role != '') and ($Code_Role != ''))
            HTML_ou_PDF($decal . 'Rôle : ' . $Libelle_Role . '<br />' . "\n", $sortie);
    }
}
if ($num_evt) {
    // Fin du fieldset
    $x = fin_fs();
}

if ($sortie_pdf) {
    $pdf->Output();
    exit;
}

?>
</body>

</html>