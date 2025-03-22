<?php
//=====================================================================
// Liste des personnes écran 2/2
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture

$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup des variables passées dans l'URL :
$Type_Liste = Recup_Variable('Type_Liste', 'C', 'PNDMCKpE'); // type de liste
$idNom      = Recup_Variable('idNom', 'N');             // Famille, ville ou catégorie
$NomL       = Recup_Variable('Nom', 'S');               // Nom de famille, de ville ou de catégorie
if ($NomL == '') $NomL = '?';
$Ville      = Recup_Variable('Ville', 'N');             // Référence de la ville
$texte      = Dem_Texte();                             // texte ou non
$Tri        = Recup_Variable('Tri', 'C', 'FHD');         // tri de la liste par femme ou homme sur mariage

// Cas des robots trompés par des ? dans le nom de la ville, vu dans les logs...
if (! $Type_Liste) {
    $Type_Liste = 'N';
    $idNom = -1;    // Cela ne sert à rien de renvoyer un résultat lors de la requête de sélection !
}

// La liste des personnes par catégorie n'est valable que pour le profil gestionnaire
if (($Type_Liste == 'C') and (isset($_SESSION['estGestionnaire']))) {
    if (!$_SESSION['estGestionnaire']) {
        echo '<center><font color="red"><br><br><br><h2>' . $LG_function_noavailable_profile . '</h2></font></center>';
    }
}

// La liste avec les conjoints n'est pas disponible sur les non Premium
if (($SiteGratuit) and (!$Premium)) {
    if ($Type_Liste == 'p') $Type_Liste = 'P';
}

// $ma_var = 'LG_LPers_Obj_'.$Type_Liste;
switch ($Type_Liste) {
    case 'P':
        $objet = LG_LPERS_OBJ_P;
        break;
    case 'p':
        $objet = LG_LPERS_OBJ_PC;
        break;
    case 'N':
        $objet = LG_LPERS_OBJ_N;
        break;
    case 'D':
        $objet = LG_LPERS_OBJ_D;
        break;
    case 'M':
        $objet = LG_LPERS_OBJ_M;
        break;
    case 'K':
        $objet = LG_LPERS_OBJ_K;
        break;
    case 'C':
        $objet = LG_LPERS_OBJ_C;
        break;
    case 'E':
        $objet = LG_LPERS_OBJ_E;
        break;
}
$_SESSION['NomP'] = stripcslashes($NomL);
$objet = $objet . ' ' . stripcslashes($NomL);

$titre = $objet;     // Titre pour META
$x = Lit_Env();      // Lecture de l'indicateur d'environnement

// Déclenchement entête sur les prénoms
$decl_ent = 20;

// Sortie en pdf ?
$sortie_pdf = false;
if ((!$SiteGratuit) or ($Premium)) {
    $s_pdf = Recup_Variable('pdf', 'C', 'O');
    if (!$s_pdf) $s_pdf = 'n';
    if ($s_pdf == 'O') $sortie_pdf = true;
    if ($sortie_pdf) $no_entete = true;
}

require(__DIR__ . '/../app/ressources/gestion_pages.php');

$n_personnes = nom_table('personnes');
$n_villes = nom_table('villes');
$n_unions = nom_table('unions');

$lien = 'href="' . $root . '/liste_pers2?Type_Liste=' . $Type_Liste .
    '&amp;texte=O&amp;idNom=' . $idNom . '&amp;Nom=' . StripSlashes(str_replace(' ', '%20', $NomL));
if ($Type_Liste != 'P') $lien .= '&amp;Ville=' . $Ville . '&amp;Tri=' . $Tri;

$compl = Ajoute_Page_Info(600, 150) . '<a ' . $lien . '" rel="nofollow"><img src="' . $root . '/assets/img/' . $Icones['text'] . '" alt="Format imprimable" title="Format imprimable" /></a> ';
if ((!$SiteGratuit) or ($Premium))
    $compl .= '<a ' . $lien . '&amp;pdf=O" rel="nofollow"><img src="' . $root . '/assets/img/' . $Icones['PDF'] . '" alt="Liste au format PDF" title="Liste au format PDF" /></a> ';

$sortie = 'H';

if (! $texte) {
    Insere_Haut($objet, $compl, 'Liste_Pers', $Type_Liste);
} else {
    // Sortie dans un PDF
    if ($sortie_pdf) {
        $objet_pdf = chaine_pdf($objet . ' ' . $_SESSION['NomP']);
        require(__DIR__ . '/html2pdfb.php');
        $sortie = 'P';
        $pdf = new PDF_HTML();
        PDF_AddPolice($pdf);
        $pdf->SetFont($font_pdf, '', 12);
        $pdf->AddPage();
        $pdf->SetFont($font_pdf, 'B', 14);
        PDF_Set_Def_Color($pdf);
        $pdf->Cell(0, 5, $objet_pdf, 'LTRB', 1, 'C');
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
        echo '<td align="center"><b>' . StripSlashes($objet) . '</b></td>' . "\n";
        echo '</tr>' . "\n";
        echo '</table>' . "\n";
        echo '<br>';
    }
}

$liste_pers = false;
if (($Type_Liste == 'P') or ($Type_Liste == 'p'))
    $liste_pers = true;

// Constitution de la requête d'extraction
if (($Type_Liste != 'M') and ($Type_Liste != 'K')) {
    switch ($Type_Liste) {
        case 'P':
        case 'p':
            $cond = 'Reference in (select idPers from ' . nom_table('noms_personnes') . ' where idNom = ' . $idNom . ') ';
            break;
        case 'N':
            $cond = 'Ville_Naissance = "' . $idNom . '" ';
            if ($idNom == 0) $cond .= 'and Ne_Le <> "" ';
            break;
        case 'D':
            $cond = 'Ville_Deces = "' . $idNom . '" ';
            if ($idNom == 0) $cond .= 'and Decede_Le <> "" ';
            break;
        case 'C':
            $cond = 'Categorie = "' . $idNom . '" ';
            break;
        case 'E':
            $cond = 'Reference in (select Personne from ' . nom_table('participe') . ' where Evenement in '
                . '(select Reference from ' . nom_table('evenements') . '	 where Identifiant_Niveau = 4 and Identifiant_zone = ' . $idNom . ')) ';
            break;
    }
    $sql = 'select Reference, Nom, Prenoms, Statut_Fiche, Diff_Internet,Ne_le, Decede_Le ' .
        ' from ' . $n_personnes .
        ' where Reference <> 0 ' .
        ' and ' . $cond;
    if (!IS_GRANTED('P')) $sql = $sql . "and Diff_Internet = 'O' ";
    $sql = $sql . 'order by Nom, Prenoms';
} else {
    if ($Type_Liste == 'M') {
        $critere_ville = 'Ville_Mariage';
        $critere_date = 'Maries_Le';
    } else {
        $critere_ville = 'Ville_Notaire';
        $critere_date = 'Date_K';
    }
    // Requête pour la liste des personnes par ville de mariage et contrat de mariage
    $sql = 'select m.Reference as Referencem, f.Reference as Referencef, ' .
        ' m.Nom as Nomm, m.Prenoms as Prenomsm, f.Nom as Nomf, f.Prenoms as Prenomsf, ' .
        ' u.' . $critere_date . ', u.Statut_Fiche, u.Reference as ReferenceU ' .
        'from ' . $n_personnes . ' m, ' . $n_personnes . ' f, ' .
        $n_villes . ' v, ' . $n_unions . ' u where ';
    if ($idNom == 0) {
        if ($Type_Liste == 'M') {
            $sql = $sql . 'u.Maries_Le <> "" and ';
        } else {
            $sql = $sql . 'u.Date_K <> "" and ';
        }
    }
    // Les non privilégiés ne peuvent pas voir tout le monde
    if (!IS_GRANTED('P')) {
        $sql = $sql . " m.Diff_Internet = 'O' and ";
        $sql = $sql . " f.Diff_Internet = 'O' and ";
    }
    $sql = $sql . 'u.' . $critere_ville . ' = ' . $idNom .
        ' and u.' . $critere_ville . ' = v.identifiant_zone' .
        ' and u.Conjoint_1 = m.Reference and u.Conjoint_2 = f.Reference' .
        ' order by ';
    switch ($Tri) {
        case 'F':
            $sql .= 'Nomf, Prenomsf';
            break;
        case 'H':
            $sql .= 'Nomm, Prenomsm';
            break;
        case 'D':
            $sql .= $critere_date . ', Nomm, Prenomsm';
            break;
        default:
            $sql .= $critere_date . ', Nomm, Prenomsm';
    }
}

$res = lect_sql($sql);
$sql1 = $sql;

// Lien vers les 3 dernières personnes saisies ou modifiées
if (!$texte) {
    if (isset($_SESSION['mem_pers'])) {
        for ($nb = 0; $nb < 3; $nb++) {
            if ($_SESSION['mem_pers'][$nb] != 0) {
                echo '<a href="' . $root . '/fiche_fam_pers?Refer=' . $_SESSION['mem_pers'][$nb] . '">' .
                    my_html($_SESSION['mem_prenoms'][$nb] . ' ' . $_SESSION['mem_nom'][$nb]) . '</a>&nbsp;' . "\n";
            }
        }
        if ($_SESSION['mem_pers'][0] != 0) echo '<br><br>' . "\n";
    }
}

// Lien direct sur la dernière personne saisie et possibilité d'insérer une personne
if ((!$texte) and IS_GRANTED('G')) {
    $MaxRef = 0;
    if (isset($_SESSION['dern_pers'])) {
        $compl_req = $_SESSION['dern_pers'];
    } else
        $compl_req = '(SELECT max( Reference ) FROM ' . $n_personnes . ')';
    $sql = 'SELECT Reference, Nom, Prenoms FROM ' . $n_personnes . ' a ' .
        'WHERE a.Reference = ' . $compl_req;
    $resmax = lect_sql($sql);
    $enrmax = $resmax->fetch(PDO::FETCH_NUM);
    $MaxRef = $enrmax[0];
    $_SESSION['dern_pers'] = $MaxRef;
    // Lien direct sur la dernière personne saisie
    if ($MaxRef > 0) {
        $aff_nom = UnPrenom($enrmax[2]) . ' ' . $enrmax[1];
        echo my_html($LG_last_pers) . ' : <a href="' . $root . '/fiche_fam_pers?Refer=' . $MaxRef . '">' . my_html($aff_nom) . '</a>&nbsp;';
        echo ' <a href="' . $root . '/edition_personne?Refer=' . $MaxRef . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a><br>' . "\n";
    }
    $resmax->closeCursor();
    // Possibilité d'insérer une personne
    echo my_html($LG_add_pers) . ' : ' . Affiche_Icone_Lien('href="' . $root . '/edition_personne?Refer=-1"', 'ajouter', my_html($LG_add)) . '<br><br>' . "\n";

    if ($Type_Liste == 'P') {
        if (IS_GRANTED('C'))
            echo '<a href="' . $root . '/completude_nom?idNom=' . $idNom . '&amp;Nom=' . $NomL . '">' . $LG_Menu_Title['Name_Is_Complete'] . $LG_name_pers . '</a><br>' . "\n";
        if ((!$SiteGratuit) or ($Premium)) {
            echo '<a href="' . $root . '/liste_pers2?Type_Liste=p'
                . '&amp;idNom=' . $idNom . '&amp;Nom=' . $NomL . '">'
                . LG_LPERS_OBJ_PC . ' ' . stripslashes($NomL) . '</a><br>';
        }
    }
    echo '<br>';
}

$nb_lig = 0;
if ($res) {
    $nb_lig = $res->rowCount();
    $Anc_Lettre = '';
    if ((!$texte) and (strpos('PNCDp', $Type_Liste) !== false)) {
        if ($nb_lig > $decl_ent) {
            echo '<table width="100%" border="0" cellspacing="1">' . "\n";
            echo '<tr align="center">' . "\n";
            $premier = true;
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                $prenoms = $row[2];
                $nom = $row[1];
                if ($prenoms == '') $prenoms = '?';
                if ($liste_pers) $Nouv_Lettre = $prenoms[0];
                else $Nouv_Lettre = $nom[0];
                if ($Nouv_Lettre != $Anc_Lettre) {
                    echo '<td class="rupt_table"><a ';
                    if ($premier) {
                        echo 'id="top" ';
                        $premier = false;
                    }
                    echo 'href="#' . $Nouv_Lettre . '">' . $Nouv_Lettre . '</a></td>';
                    $Anc_Lettre = $Nouv_Lettre;
                }
            }
            echo '</tr>' . "\n";
            echo '</table>' . "\n";
        }
        if ($nb_lig > 0) {
            $res->closeCursor();
            $res = lect_sql($sql1);
        }
    }
}

// Balayage
if ($nb_lig > 0) {
    $Anc_Lettre = '';
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        switch ($Type_Liste) {
            case 'P':
            case 'p':
            case 'N':
            case 'C':
            case 'E':
            case 'D':
                if (! $texte) {

                    // Traitement en rupture sur Initiale si nécessaire sur la liste des personnes
                    if ($nb_lig > $decl_ent) {
                        $prenoms = $row['Prenoms'];
                        $nom = $row['Nom'];
                        if ($prenoms == '') $prenoms = '?';
                        if ($liste_pers) $Nouv_Lettre = $prenoms[0];
                        else $Nouv_Lettre = $nom[0];
                        if ($Nouv_Lettre != $Anc_Lettre) {
                            echo '<br><a name="' . $Nouv_Lettre . '">' . $Nouv_Lettre . '</a> <img src="' . $root . '/assets/img/' . $Icones['href="#top"'] . '" alt="Haut de page" title="Haut de page">';
                            $Anc_Lettre = $Nouv_Lettre;
                        }
                    }

                    if (IS_GRANTED('C')) {
                        if ($row['Diff_Internet'] == 'O') echo '<img src="' . $root . '/assets/img/' . $Icones['internet_oui'] . '" alt="' . $LG_show_on_internet . '" title="' . $LG_show_on_internet . '">';
                        else                              echo '<img src="' . $root . '/assets/img/' . $Icones['internet_non'] . '" alt="' . $LG_noshow_on_internet . '" title="' . $LG_noshow_on_internet . '">';
                    }
                    switch ($row['Statut_Fiche']) {
                        case 'O':
                            echo '<img src="' . $root . '/assets/img/' . $Icones['fiche_validee'] . '" alt="' . $LG_checked_record . '" title="' . $LG_checked_record . '">';
                            break;
                        case 'N':
                            echo '<img src="' . $root . '/assets/img/' . $Icones['fiche_non_validee'] . '" alt="' . $LG_nochecked_record . '" title="' . $LG_nochecked_record . '">';
                            break;
                        case 'I':
                            echo '<img src="' . $root . '/assets/img/' . $Icones['fiche_internet'] . '" alt="' . LG_FROM_INTERNET . '" title="' . LG_FROM_INTERNET . '">';
                            break;
                    }
                    echo '&nbsp;';
                }
                $Ref = $row['Reference'];

                $conj_ajout = '';
                // Recherche des conjoints pour la liste avec conjoints					
                if ($Type_Liste == 'p') {
                    $ajout_priv = '';
                    if (!IS_GRANTED('P')) {
                        $ajout_priv = "and  p1.Diff_Internet = 'O' and  p2.Diff_Internet = 'O' ";
                    }
                    $sql_conj = 'select p1.Reference as Ref1, p1.Nom as Nom1, p1.Prenoms as Prenoms1, p2.Reference as Ref2, p2.Nom as Nom2, p2.Prenoms as Prenoms2 '
                        . 'from ' . $n_unions . ' u, ' . $n_personnes . ' p1, ' . $n_personnes . ' p2 '
                        . 'where p1.Reference = u.Conjoint_1 '
                        . 'and p2.Reference = u.Conjoint_2 '
                        . 'and (u.Conjoint_1 = ' . $Ref . ' or u.Conjoint_2 = ' . $Ref . ') '
                        . $ajout_priv
                        . 'order by u.Maries_Le';
                    $nb_conj = 0;
                    if ($res_conj = lect_sql($sql_conj)) {
                        while ($enreg_conj = $res_conj->fetch(PDO::FETCH_ASSOC)) {
                            $nb_conj++;
                            if ($conj_ajout != '') $conj_ajout .= ', ';
                            $Ref1 = $enreg_conj['Ref1'];
                            $Ref2 = $enreg_conj['Ref2'];
                            if ($Ref1 == $Ref) {
                                $pr_conj = $enreg_conj['Prenoms2'];
                                $nom_conj = $enreg_conj['Nom2'];
                            } else {
                                $pr_conj = $enreg_conj['Prenoms1'];
                                $nom_conj = $enreg_conj['Nom1'];
                            }
                            $conj_ajout .= UnPrenom($pr_conj) . ' ' . $nom_conj;
                        }
                    }
                    //var_dump($nb_conj);
                    if ($nb_conj) $conj_ajout = ' [' . $conj_ajout . ']';
                }
                if (! $texte) echo '<a href="' . $root . '/fiche_fam_pers?Refer=' . $Ref . '">' . my_html($row['Prenoms'] . ' ' . $row['Nom']) . '</a>' . $conj_ajout . "\n";
                else echo HTML_ou_PDF($row['Prenoms'] . ' ' . $row['Nom'] . $conj_ajout . "\n", $sortie);
                // else echo HTML_ou_PDF(my_html($row['Prenoms'].' '.$row['Nom'].$conj_ajout)."\n",$sortie);
                $Ne = $row['Ne_le'];
                $Decede = $row['Decede_Le'];
                if (($Ne != '') or ($Decede != '')) {
                    HTML_ou_PDF(' (', $sortie);
                    if ($Ne != '') HTML_ou_PDF('° ' . Etend_date($Ne), $sortie);
                    if ($Decede != '') {
                        if ($Ne != '') HTML_ou_PDF(', ', $sortie);
                        HTML_ou_PDF('+ ' . Etend_date($Decede), $sortie);
                    }
                    HTML_ou_PDF(')', $sortie);
                }
                if ((IS_GRANTED('G')) and (! $texte)) {
                    echo '&nbsp;<a href="' . $root . '/edition_personne?Refer=' . $Ref . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '">';
                    echo '&nbsp;<a href="' . $root . '/verif_personne?Refer=' . $Ref . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_controle'] . '" alt="' . $LG_LPers_Check_Pers . '" title="' . $LG_LPers_Check_Pers . '">';
                }
                break;
            case 'M':
            case 'K':
                if ((IS_GRANTED('G')) and (! $texte)) {
                    switch ($row['Statut_Fiche']) {
                        case 'O':
                            echo '<img src="' . $root . '/assets/img/' . $Icones['fiche_validee'] . '" alt="' . $LG_checked_record . '" title="' . $LG_checked_record . '">';
                            break;
                        case 'N':
                            echo '<img src="' . $root . '/assets/img/' . $Icones['fiche_non_validee'] . '" alt="' . $LG_nochecked_record . '" title="' . $LG_nochecked_record . '">';
                            break;
                        case 'I':
                            echo '<img src="' . $root . '/assets/img/' . $Icones['fiche_internet'] . '" alt="' . LG_FROM_INTERNET . '" title="' . LG_FROM_INTERNET . '">';
                            break;
                    }
                    echo '&nbsp;&nbsp;&nbsp;';
                }
                if ($Tri == 'F') {
                    $Ref1     = $row['Referencef'];
                    $Nom1     = $row['Nomf'];
                    $Prenoms1 = $row['Prenomsf'];
                    $Ref2     = $row['Referencem'];
                    $Nom2     = $row['Nomm'];
                    $Prenoms2 = $row['Prenomsm'];
                } else {
                    $Ref1     = $row['Referencem'];
                    $Nom1     = $row['Nomm'];
                    $Prenoms1 = $row['Prenomsm'];
                    $Ref2     = $row['Referencef'];
                    $Nom2     = $row['Nomf'];
                    $Prenoms2 = $row['Prenomsf'];
                }
                if (!$texte) {
                    echo '<a href="' . $root . '/fiche_fam_pers?Refer=' . $Ref1 . '">' . my_html($Nom1 . ' ' . $Prenoms1) . '</a>';
                    echo '&nbsp;x&nbsp;<a href="' . $root . '/fiche_fam_pers?Refer=' . $Ref2 . '">' . my_html($Nom2 . ' ' . $Prenoms2) . '</a>' . "\n";
                } else {
                    HTML_ou_PDF(my_html($Nom1 . ' ' . $Prenoms1), $sortie);
                    HTML_ou_PDF('&nbsp;x&nbsp;' . my_html($Nom2 . ' ' . $Prenoms2) . "\n", $sortie);
                }
                if ($row[$critere_date] != '') HTML_ou_PDF('&nbsp;(x ' . Etend_date($row[$critere_date]) . ')', $sortie);
                if (IS_GRANTED('G') and (!$texte)) {
                    echo '&nbsp;<a href="' . $root . '/edition_union?Reference=' . $row['ReferenceU'] . '&amp;Personne=0&amp;us=x"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '">';
                }
                break;
            default:
                break;
        }
        HTML_ou_PDF("<br>\n", $sortie);
    }
}

if ($sortie_pdf) {
    $pdf->Output();
    exit;
}

if ($res)
    $res->closeCursor();

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
?>
</body>

</html>