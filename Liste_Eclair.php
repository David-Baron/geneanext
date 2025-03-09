<?php

//=====================================================================
// Liste éclair par département
// (c) JLS
// UTF-8
//=====================================================================

session_start();

include('fonctions.php');
$acces = 'L';                          // Type d'accès de la page : (L)ecture
$titre = $LG_Menu_Title['County_List'];               // Titre pour META
$x = Lit_Env();

// Sortie en pdf ?
$sortie_pdf = false;
if ((!$SiteGratuit) or ($Premium)) {
	$s_pdf = Recup_Variable('pdf','C','O');
	if (!$s_pdf) $s_pdf = 'n';
	if ($s_pdf == 'O') $sortie_pdf = true;
	if ($sortie_pdf) $no_entete = true;						// Pas d'entête HTML sinon le PDF ne s'affichera pas
}

include('Gestion_Pages.php');

$Depart = -1;
if (isset($_POST['Depart'])) $Depart = $_POST['Depart'];
$Depart = Secur_Variable_Post($Depart,1,'N');
if ($Depart == 0) $Depart=-1;

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();
// Dans le cas de texte, récupération du numéro de département dans l'URL
if ($texte) $Depart = Recup_Variable('Depart','N',1);

$comp_texte = '&amp;Depart='.$Depart;
$compl = Ajoute_Page_Info(600,150).
       '<a href="'.my_self().'?texte=O'.$comp_texte.
       '">'.Affiche_Icone('text',$LG_printable_format).'</a>'."\n";
if ((!$SiteGratuit) or ($Premium))
	$compl .= Affiche_Icone_Lien('href="'.my_self().'?texte=O&amp;pdf=O'.$comp_texte.'"','PDF',$LG_pdf_format).'&nbsp;';

$sortie = 'H';

if (! $texte) Insere_Haut(my_html($titre),$compl,'Liste_Eclair',$Depart);
else  {

	if ($Depart == -1) $Depart = -2;
	if ($Depart != -2) $le_titre = LG_COUNTY_LIST_ONE.' : '.lib_departement($Depart);
	else               $le_titre = LG_COUNTY_LIST_ALL;

	// Sortie dans un PDF
    if($sortie_pdf) {
    	require('html2pdfb.php');
    	$sortie = 'P';
		$pdf = new PDF_HTML();
		PDF_AddPolice($pdf);
		$pdf->SetFont($font_pdf,'',12);
		$pdf->AddPage();
		$pdf->SetFont($font_pdf,'B',14);
		PDF_Set_Def_Color($pdf);
		$pdf->Cell(0, 5, chaine_pdf($le_titre) , 'LTRB' , 1, 'C');
		$pdf->SetFont($font_pdf,'',11);
		$pdf->Ln();
		
		// PDF_AddPolice($pdf);
		// $pdf->SetFont($font_pdf,'',12);
		// $pdf->AddPage();
		// $pdf->SetFont($font_pdf,'B',14);
		// PDF_Set_Def_Color($pdf);
		// $pdf->Cell(0, 5, chaine_pdf($titre) , 'LTRB' , 1, 'C');
		// $pdf->SetFont($font_pdf,'',11);
		// $pdf->Ln();

		
	}
	// Sortie au format texte
	else {
        Insere_Haut_texte (my_html($le_titre));
	}

}
if (! $texte)
	include('jscripts/Liste_Patro.js');		// Même javascript que la liste patronymique

$style_fond = 'style="background-image:url(\''.$chemin_images.'bar_off.gif\');background-repeat:repeat-x;"';
$ent_table = '<table width="95%" border="0" class="classic" cellspacing="1" cellpadding="3" align="center">';
$ent_table_texte = '<table width="95%" align="center">';

$n_personnes = nom_table('personnes');
$n_villes = nom_table('villes');
$n_departements = nom_table('departements');


if (!$texte) {
	
	$sql ='select distinct d.Identifiant_zone, d.Nom_Depart_Min '.
	    'from '.$n_villes.' v, '.$n_departements.' d '.
	    'where d.Identifiant_zone <> 0 '.
	    'and d.Identifiant_zone = v.Zone_Mere '.
	    'order by d.Nom_Depart_Min';

	echo '<form action="'.my_self().'?Depart='.$Depart.'" method="post">'."\n";
	echo '<table border="0" width="50%" align="center">'."\n";
	echo '<tr align="center">';
	echo '<td width="50%" class="rupt_table">'.LG_COUNTY.LG_SEMIC."\n";
	echo '<select name="Depart">'."\n";
	echo '<option value="-2">'.$LG_All.'</option>'."\n";
	if ($res = lect_sql($sql)) {
		while ($row = $res->fetch(PDO::FETCH_NUM)) {
			echo '<option value="'.$row[0].'"';
			if ($Depart == $row[0]) echo ' selected="selected"';
			echo '>'.$row[1]."</option>\n";
		}
	}
	$res->closeCursor();
	echo "</select>\n";
	echo "</td>\n";
	echo '<td width="50%" class="rupt_table"><input type="submit" value="'.$LG_modify_list.'"/>'."\n";
	$alt_img = my_html(LG_COUNTY_LIST_SHOW_HIDE);
	echo '&nbsp;&nbsp;&nbsp;&nbsp;<img id="masque" src="'.$chemin_images_icones.$Icones['oeil'].'" alt="'.$alt_img.'" title="'.$alt_img.'"'.
	      ' onmouseover="Survole_Clic_Div_Tous(\'MO\',\''.$Comportement.'\');" onclick="Survole_Clic_Div_Tous(\'CL\',\''.$Comportement.'\');"/>';
	echo '</td>'."\n";
	echo '</tr></table>';
	echo '<input type="hidden" id="memo_etat" name="memo_etat"/>';
	echo '</form>'."\n";
}

if ($Depart != -1) {

	// Constitution de la requête d'extraction
	$sql = 'SELECT Nom, Ville_Naissance, Nom_Ville, idNomFam '.
		'FROM '.$n_personnes.' p, '.$n_villes.' v '.
		'WHERE v.Identifiant_zone = p.Ville_Naissance ';
	// Département demandé
	if ($Depart != -2) $sql .= 'AND v.Zone_Mere ='.$Depart.' ';
	$sql .=  'AND Reference <>0 ';
	if (!$_SESSION['estPrivilegie']) $sql = $sql ." and Diff_Internet = 'O' ";
	$sql .= 'union '.
		'SELECT Nom, Ville_Deces, Nom_Ville, idNomFam '.
		'FROM '.$n_personnes.' p, '.$n_villes.' v '.
		'WHERE v.Identifiant_zone = p.Ville_Deces ';
	// Département demandé
	if ($Depart != -2) $sql .= 'AND v.Zone_Mere ='.$Depart.' ';
	$sql .=  'AND Reference <>0 ';
	if (!$_SESSION['estPrivilegie']) $sql = $sql ." and Diff_Internet = 'O' ";
	$sql = $sql ." order by 1,3";

    $res = lect_sql($sql);

    $Anc_Nom = '';

	$attente = false; // Top attente d'affichage de l'oeil
	$existe_div = 0;
	$num_div = 0;

	$deb_lien = '<a href="'.Get_Adr_Base_Ref().'Liste_Pers2.php?Type_Liste=P&amp;idNom=';

    while ($row = $res->fetch(PDO::FETCH_NUM)) {
		$Nouv_Nom = my_html($row[0]);
		// Rupture sur le nom
		if ($Nouv_Nom != $Anc_Nom) {
		    if ($Anc_Nom != '') {
		    	if ($attente) HTML_ou_PDF('</td></tr>'."\n",$sortie);
				HTML_ou_PDF('</table>'."\n",$sortie);
		    	if (!$texte) {
					if ($existe_div) echo '</div>';
					echo '<br />';
		    	}
		    	else {
		    		if (!$sortie_pdf) HTML_ou_PDF('<hr width="95%"/>',$sortie);
		    		else HTML_ou_PDF('<br> ',$sortie);
		    	}
				$existe_div = 0;
			}
			if (! $texte) {
		        echo $ent_table.'<tr align="center" >'."\n";
				echo '<td class="rupt_table">'.$deb_lien.$row[3].'&amp;Nom='.$Nouv_Nom.'">'.$Nouv_Nom."</a>\n";
			}
			else {
		        HTML_ou_PDF($ent_table_texte.'<tr align="center" >'."\n",$sortie);
				HTML_ou_PDF('<td>'.$Nouv_Nom."\n",$sortie);
				if ($sortie_pdf) HTML_ou_PDF(' :',$sortie);
			}
			$attente = true;
	        $Anc_Nom = $Nouv_Nom;
	  }
	  if ($row[2] != '') {
		if ($attente) {
			$num_div++;
			if (! $texte) {
				// Affichage de l'oeil pour afficher / masquer un patronyme
				oeil_div_simple('im_'.$num_div,'ajout'.$Nouv_Nom,my_html($LG_show_noshow ),'div'.$num_div);
			}
			HTML_ou_PDF('</td></tr></table>'."\n",$sortie);
			$attente = false;
			$existe_div = 1;
			if (! $texte) echo '<div id="div'.$num_div.'">'.$ent_table;
			else          HTML_ou_PDF($ent_table_texte,$sortie);
			$num_lig = 0;
		}
		if (! $texte) {
			if (pair($num_lig++)) $style = 'liste';
			else $style = 'liste2';
	    	echo '<tr class="'.$style.'">'."\n";
		}
		else HTML_ou_PDF('<tr align="left">'."\n",$sortie);
	    HTML_ou_PDF('<td>'.$row[2].'</td>'."\n",$sortie);
	    HTML_ou_PDF('</tr>'."\n",$sortie);
	  }
    }
    $res->closeCursor();

    HTML_ou_PDF('</table>',$sortie);
    if (! $texte) echo '</div>';
}

if (! $texte) Insere_Bas($compl);

if($sortie_pdf) {
	$pdf->Output();
	exit;
}

?>
</body>
</html>