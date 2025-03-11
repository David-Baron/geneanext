<?php
//=====================================================================
// Liste des personnes écran 1/2
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                // Type d'accès de la page : (M)ise à jour, (L)ecture M pour avoir le getXMLHttpRequest pdf

// Recup de la variable passée dans l'URL : type de liste, texte ou non
$Type_Liste = Recup_Variable('Type_Liste', 'C', 'PNDMCK');
$texte = Dem_Texte();

// La liste des personnes par catégorie est valable à partir du profil contributeur ; si inférieur, on débranche sur la liste des personnes par nom
if ($Type_Liste == 'C') {
    if (!$_SESSION['estContributeur']) $Type_Liste = 'N';
}

// Objet / titre de la page
switch ($Type_Liste) {
    case 'P':
        $objet = LG_LPERS_OBJ_P;
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
    case 'C':
        $objet = LG_LPERS_OBJ_C;
        break;
    case 'K':
        $objet = LG_LPERS_OBJ_K;
        break;
}
$titre = $objet;        // Titre pour META

$x = Lit_Env();

// Sortie en pdf ?
$sortie_pdf = false;
if ((!$SiteGratuit) or ($Premium)) {
    $s_pdf = Recup_Variable('pdf', 'C', 'O');
    if (!$s_pdf) $s_pdf = 'n';
    if ($s_pdf == 'O') $sortie_pdf = true;
    if ($sortie_pdf) $no_entete = true;
}

require(__DIR__ . '/app/ressources/gestion_pages.php');

function Aff_Ne_Dec($row)
{
    global $sortie;
    $Ne = $row['Ne_le'];
    $Decede = $row['Decede_Le'];
    if (($Ne != '') or ($Decede != '')) {
        HTML_ou_PDF(' (', $sortie);
        // if ($Ne != '') HTML_ou_PDF('&deg;'.Etend_date($Ne),$sortie);
        if ($Ne != '') HTML_ou_PDF('° ' . Etend_date($Ne), $sortie);
        if ($Decede != '') {
            if ($Ne != '') HTML_ou_PDF(', ', $sortie);
            HTML_ou_PDF('+ ' . Etend_date($Decede), $sortie);
        }
        HTML_ou_PDF(')', $sortie);
    }
}

$deb_lien = 'href="' . $root . '/liste_pers.php?Type_Liste=' . $Type_Liste . '&amp;texte=O';
$compl = Ajoute_Page_Info(600, 300) .
    Affiche_Icone_Lien_TXT_PDF($deb_lien . '"', my_html($LG_printable_format), 'T') . '&nbsp;';
if ((!$SiteGratuit) or ($Premium))
    $compl .= Affiche_Icone_Lien_TXT_PDF($deb_lien . '&amp;pdf=O"', my_html($LG_pdf_format), 'P') . '&nbsp;';

if (! $texte) Insere_Haut(my_html($objet), $compl, 'Liste_Pers', $Type_Liste);

$sortie = 'H';

if (!$texte) include(__DIR__ . '/assets/js/edition_geneamania.js');

// Préparation sur la clause de diffusabilité
$p_diff_int = '';
if (!$est_privilegie) $p_diff_int = " and Diff_Internet = 'O' ";

$n_personnes = nom_table('personnes');
$n_villes = nom_table('villes');
$n_unions = nom_table('unions');

// Lien vers les 3 dernières personnes saisies ou modifiées
if (isset($_SESSION['mem_pers'])) {
    if ((!$texte) and ($_SESSION['mem_pers'])) {
        for ($nb = 0; $nb < 3; $nb++) {
            if ($_SESSION['mem_pers'][$nb] != 0) {
                echo '<a ' . Ins_Ref_Pers($_SESSION['mem_pers'][$nb]) . '>' .
                    my_html($_SESSION['mem_prenoms'][$nb] . ' ' . $_SESSION['mem_nom'][$nb]) . '</a>&nbsp;' . "\n";
            }
        }
        if ($_SESSION['mem_pers'][0] != 0) echo '<br>' . "\n";
    }
}

// Lien direct sur la dernière personne saisie et possibilité d'insérer une personne
if ((!$texte) and ($est_contributeur)) {
    $echo_modif = Affiche_Icone('fiche_edition', $LG_modify) . '</a>';
    $MaxRef = 0;
    // On va chercher la dernière personne
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
        echo $LG_last_pers . ' : <a ' . Ins_Ref_Pers($MaxRef) . '>' . $aff_nom . '</a>&nbsp;';
        echo '&nbsp;<a ' . Ins_Edt_Pers($MaxRef) . '>' . $echo_modif . '<br>' . "\n";
    }
    $resmax->closeCursor();
    // Possibilité d'insérer une personne
    echo $LG_add_pers . ' : ' . Affiche_Icone_Lien(Ins_Edt_Pers(-1), 'ajouter', $LG_add) . '<br><br>' . "\n";
}

$debut = microtime_float();

if (! $texte) {
    // Constitution de la requête d'extraction
    switch ($Type_Liste) {
        case 'P': // Requête pour la liste des personnes
            $sql = 'SELECT count(*), f.nomFamille, f.idNomFam, hex(f.nomFamille)' .
                ' FROM ' . nom_table('noms_personnes') . ' n, ' . nom_table('noms_famille') . ' f, ' . $n_personnes . ' p' .
                ' WHERE f.idNomFam = n.idNom' .
                ' AND p.Reference = n.idPers' .
                ' AND p.Reference <> 0' .
                $p_diff_int .
                ' GROUP BY f.nomFamille, f.idNomFam, hex(f.nomFamille) order by hex(f.nomFamille)';
            // ' GROUP BY 4 order by 4';
            break;
        case 'N': // Requête pour la liste des personnes par ville de naissance
            $sql = 'select count(*), v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude, hex(v.Nom_Ville) ' .
                'from ' . $n_personnes . ' p, ' . $n_villes . ' v where ' .
                ' p.ville_naissance <> 0 ' .
                'and p.ville_naissance = v.identifiant_zone ' .
                $p_diff_int .
                'group by  v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude, hex(v.Nom_Ville) order by hex(v.Nom_Ville)';
            // 'group by 6 order by 6';
            break;
        case 'D': // Requête pour la liste des personnes par ville de décès
            $sql = 'select count(*), v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude, hex(v.Nom_Ville) ' .
                'from ' . $n_personnes . ' p, ' . $n_villes . ' v where ' .
                ' p.ville_deces <> 0 ' .
                'and p.ville_deces = v.identifiant_zone ' .
                $p_diff_int .
                'group by v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude, hex(v.Nom_Ville) order by hex(v.Nom_Ville)';
            // 'group by v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude, hex(v.Nom_Ville) order by hex(v.Nom_Ville)';
            // 'group by 6 order by 6';
            break;
        case 'M': // Requête pour la liste des personnes par ville de mariage
            $sql = 'select count(*), v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude, hex(v.Nom_Ville) ' .
                'from ' . $n_personnes . ' m, ' . $n_personnes . ' f, ' .
                $n_villes . ' v, ' . $n_unions . ' u where ';
            if (!$est_privilegie) {
                $sql = $sql . " m.Diff_Internet = 'O' and ";
                $sql = $sql . " f.Diff_Internet = 'O' and ";
            }
            $sql = $sql . 'u.Ville_Mariage <> 0 ' .
                //'and u.Maries_Le <> "" '.
                'and u.Ville_Mariage = v.identifiant_zone ' .
                'and u.Conjoint_1 = m.Reference and u.Conjoint_2 = f.Reference ' .
                'group by v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude, hex(v.Nom_Ville) order by hex(v.Nom_Ville)';
            // 'group by 6 order by 6';
            break;
        case 'C': // Requête pour la liste des personnes par catégorie
            $sql = 'select count(*),c.Titre, c.Identifiant, c.Image ' .
                'from ' . $n_personnes . ' p, ' . nom_table('categories') . ' c' .
                ' where p.Categorie = c.Identifiant ' .
                'group by c.Titre, c.Identifiant, c.Image order by c.Titre';
            break;
        case 'K': // Requête pour la liste des personnes par ville de contrat mariage
            $sql = 'select count(*),v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude ' .
                'from ' . $n_personnes . ' m, ' . $n_personnes . ' f, ' .
                $n_villes . ' v, ' . $n_unions . ' u where ';
            if (!$est_privilegie) {
                $sql = $sql . " m.Diff_Internet = 'O' and ";
                $sql = $sql . " f.Diff_Internet = 'O' and ";
            }
            $sql = $sql . 'u.Ville_Notaire <> 0 ' .
                //'and u.Date_K <> "" '.
                'and u.Ville_Notaire = v.identifiant_zone ' .
                'and u.Conjoint_1 = m.Reference and u.Conjoint_2 = f.Reference ' .
                'group by v.Nom_Ville, v.Identifiant_zone, v.Latitude, v.Longitude order by v.Nom_Ville';
            break;
    }

    // Cartouche d'accès rapide
    // Sur les sites hébergés, uniquement pour les Premium
    if ((!$SiteGratuit) or ($Premium)) {
        if ((!$texte) and ($Type_Liste == 'P')) {
            include(__DIR__ . '/assets/js/Liste_Pers.js');
            $self = my_self();
            echo '<form id="saisieP" method="post" action="' . $self . '?' . Query_Str() . '">' . "\n";
            echo '<input type="' . $hidden . '" id="page" value = "' . $self . '" />';
            echo '<input type="' . $hidden . '" id="creation" value = "n" />';
            echo '<fieldset><legend>' . my_html(LG_LPERS_QUICK_ACCESS) . '</legend>';
            $sql_noms = 'SELECT DISTINCT idNomFam, Nom FROM ' . $n_personnes    . ' WHERE Reference <> 0';
            if (!$est_privilegie) {
                $sql_noms .= " AND Diff_Internet = 'O'";
            }
            $sql_noms .= ' ORDER by Nom';
            $res = lect_sql($sql_noms);
            echo '<select name="nom" id="noms" onchange="updatePersonnes(this.value)">';
            echo '<option value="-1">' . my_html(LG_LPERS_SELECT_NAME) . '</option>';
            while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                echo '<option value="' . $enreg[0] . '">' . my_html($enreg[1]) . '</option>';
            }
            echo '</select>' . "\n";
            echo '<select name="personnes" id="personnes" onchange="updateLiensIcones(this.value)"></select>';
            echo '<a id="icone_visu" href="' . $root . '/fiche_fam_pers.php?Refer=1">' . Affiche_Icone('page', LG_LPERS_PERS_FILE) . '</a>';
            echo '<a id="icone_modif" href="' . $root . '/edition_personne.php?Refer=1">' . Affiche_Icone('fiche_edition', my_html($LG_modify)) . '</a>';
            echo '</fieldset>';
            echo '</form>';
        }
    }

    // Partie initiales
    $nb_lignes = 0;
    $count_ok = false;
    if ($res = lect_sql($sql)) {
        $nb_lignes = $res->rowCount();
        $count_ok = true;
    }
    if ($Type_Liste != 'C') {
        if ($nb_lignes > 0) {
            echo '<table width="100%" cellspacing="1">' . "\n";
            echo '<tr align="center">' . "\n";
            $Anc_Lettre = '';
            $premier = true;
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                $Nom = $row[1];
                if ($Nom == '') $Nom = '?';
                $Nouv_Lettre = $Nom[0];
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
            echo '<br>' . "\n";
        }
    }

    $echo_haut = Affiche_Icone_Lien('href="#top"', 'page_haut', my_html($LG_top)) . '<br>';

    $deb_lien = '<a href="' . $root . '/liste_pers2.php?Type_Liste=' . $Type_Liste;
    $deb_lien_crea = 'href="' . $root . '/edition_personnes_ville.php?evt=';

    if (($Type_Liste == 'M') or ($Type_Liste == 'K')) {
        $echo_femme = Affiche_Icone('femme', my_html(LG_ORDER_BY_WOMEN));
        $echo_homme = Affiche_Icone('homme', my_html(LG_ORDER_BY_MEN));
        $echo_calend = Affiche_Icone('calendrier', my_html(LG_ORDER_BY_DATE));
    }

    if ($count_ok) {
        $res->closeCursor();
    }
    // Affichage principal
    if ($nb_lignes > 0) {
        $res = lect_sql($sql);
        $Anc_Lettre = '';
        $premier = true;
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $NomObj = $row[1];
            if ($NomObj == '') $NomObj = '?';
            $NomA = my_html($NomObj);
            if ($Type_Liste != 'C') {
                $Nouv_Lettre = $NomObj[0];
                // Traitement en rupture sur Initiale
                if ($Nouv_Lettre != $Anc_Lettre) {
                    if (!$premier) echo "<br>\n";
                    $premier = false;
                    echo '<a id="' . $Nouv_Lettre . '">' . $Nouv_Lettre . '</a>&nbsp;' . $echo_haut;
                    $Anc_Lettre = $Nouv_Lettre;
                }
            }
            // Dans le cas de la catégorie, on affiche l'icone qui correspond
            else {
                echo '<img src="' . $chemin_images_icones . $Icones['tag_' . $row[3]] . '" alt="' . $NomObj . '" title="' . $NomObj . '"/>' . '&nbsp;';
            }
            // Traitement de chaque nom
            // Récupération de la latitude et de la logitude de la ville
            if (($Type_Liste == 'N') or ($Type_Liste == 'D') or ($Type_Liste == 'M') or ($Type_Liste == 'K')) {
                $Lat_V = $row[3];
                $Long_V = $row[4];
            }
            $le_nom = str_replace(' ', '%20', $row[1]);
            $params = '&amp;idNom=' . $row[2] . '&amp;Nom=' . $le_nom;
            switch ($Type_Liste) {
                case 'P':
                case 'N':
                case 'D':
                case 'C':
                    echo $deb_lien . $params . '">' . $NomA . '</a> ';
                    if ($est_gestionnaire) {
                        if ($Type_Liste == 'N') echo '&nbsp;' . Affiche_Icone_Lien($deb_lien_crea . 'N' . $params . '"', 'ajouter', LG_CREATE_PERS_BORN_IN . ' ' . $NomObj);
                        if ($Type_Liste == 'D') echo '&nbsp;' . Affiche_Icone_Lien($deb_lien_crea . 'D' . $params . '"', 'ajouter', LG_CREATE_PERS_DEAD_IN . ' ' . $NomObj);
                    }
                    if (($Type_Liste == 'N') or ($Type_Liste == 'D')) {
                        appelle_carte_osm();
                        echo '&nbsp;';
                    }
                    echo '(' . $row[0] . ')<br>' . "\n";
                    break;
                case 'M':
                case 'K':
                    echo $NomA . '&nbsp;(' . $row[0] . ')&nbsp;' . '<a href="' . $root . '/notaires_ville.php?Ville=' . $row[2] . '&amp;Nom=' . $le_nom . '">' . LG_LPERS_NOTARIES . '</a>&nbsp;';
                    appelle_carte_osm();
                    echo $deb_lien . $params . '&amp;Tri=F">' . $echo_femme . '</a>';
                    echo $deb_lien . $params . '&amp;Tri=H">' . $echo_homme . '</a>';
                    echo $deb_lien . $params . '&amp;Tri=D">' . $echo_calend . '</a><br>' . "\n";
                    break;
                default:
                    break;
            }
        }
        $res->closeCursor();
    }
}
// Sortie au format texte
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
        $pdf->Cell(0, 5, chaine_pdf($objet), 'LTRB', 1, 'C');
        $pdf->SetFont($font_pdf, '', 11);
        $pdf->Ln();
    }
    // Sortie au format texte
    else {
        // Affichage du titre : numéros + génération
        Insere_Haut_texte(my_html($objet));
    }

    // Constitution de la requête d'extraction
    switch ($Type_Liste) {
        case 'P': // Requête pour la liste des personnes
            $sql = 'select Reference, Nom, Prenoms, Statut_Fiche, Diff_Internet, Ne_le, Decede_Le ' .
                'from ' . $n_personnes .
                ' where Reference <> 0 ' .
                $p_diff_int .
                ' order by nom, prenoms';
            break;
        case 'N': // Requête pour la liste des personnes par ville de naissance
            $sql = 'select v.Nom_Ville, p.Reference, p.Nom, p.Prenoms, p.Ne_le, p.Decede_Le ' .
                'from ' . $n_personnes . ' p, ' . $n_villes . ' v where ' .
                ' p.ville_naissance <> 0 ' .
                'and p.ville_naissance = v.identifiant_zone ' .
                $p_diff_int .
                'order by v.nom_ville, p.nom, p.prenoms';
            break;
        case 'D': // Requête pour la liste des personnes par ville de décès
            $sql = 'select v.Nom_Ville, p.Reference, p.Nom, p.Prenoms, p.Ne_le, p.Decede_Le  ' .
                'from ' . $n_personnes . ' p, ' . $n_villes . ' v where ' .
                ' p.Ville_Deces <> 0 ' .
                'and p.Ville_Deces = v.identifiant_zone ' .
                $p_diff_int .
                'order by v.nom_ville, p.nom, p.prenoms';
            break;
        case 'C': // Requête pour la liste des personnes par catégorie
            $sql = 'select Reference, Nom, Prenoms, Statut_Fiche, Diff_Internet, Ne_le, Decede_Le, Titre ' .
                'from ' . $n_personnes . ' p, ' . nom_table('categories') . ' c' .
                ' where p.Categorie = c.Identifiant ' .
                ' order by c.Titre, nom, prenoms';
            break;
        case 'M': // Requête pour la liste des personnes par ville de mariage
            $sql = 'select v.Nom_Ville, m.Reference as Referencem, f.Reference as Referencef, ' .
                ' m.Nom as Nomm, m.Prenoms as Prenomsm, f.Nom as Nomf, f.Prenoms as Prenomsf, u.Maries_Le ' .
                'from ' . $n_personnes . ' m, ' . $n_personnes . ' f, ' .
                $n_villes . ' v, ' . $n_unions . ' u where ';
            if (!$est_privilegie) {
                $sql = $sql . " m.Diff_Internet = 'O' and ";
                $sql = $sql . " f.Diff_Internet = 'O' and ";
            }
            $sql = $sql . 'u.Ville_Mariage <> 0 ' .
                'and u.Ville_Mariage = v.identifiant_zone ' .
                'and u.Conjoint_1 = m.Reference and u.Conjoint_2 = f.Reference ' .
                'order by v.nom_ville, Nomf, Prenomsf';
            break;
        case 'K': // Requête pour la liste des personnes par ville de contrat de mariage
            $sql = 'select v.Nom_Ville, m.Reference as Referencem, f.Reference as Referencef, ' .
                ' m.Nom as Nomm, m.Prenoms as Prenomsm, f.Nom as Nomf, f.Prenoms as Prenomsf, u.Date_K ' .
                'from ' . $n_personnes . ' m, ' . $n_personnes . ' f, ' .
                $n_villes . ' v, ' . $n_unions . ' u where ';
            if (!$est_privilegie) {
                $sql = $sql . " m.Diff_Internet = 'O' and ";
                $sql = $sql . " f.Diff_Internet = 'O' and ";
            }
            $sql = $sql . 'u.Ville_Notaire <> 0 ' .
                'and u.Ville_Notaire = v.identifiant_zone ' .
                'and u.Conjoint_1 = m.Reference and u.Conjoint_2 = f.Reference ' .
                'order by v.nom_ville, Nomf, Prenomsf';
            break;
        default:
            break;
    }

    $res = lect_sql($sql);

    switch ($Type_Liste) {
        case 'P':
            $Nom_Rub = 'Nom';
            break;
        case 'N':
            $Nom_Rub = 'Nom_Ville';
            break;
        case 'D':
            $Nom_Rub = 'Nom_Ville';
            break;
        case 'M':
            $Nom_Rub = 'Nom_Ville';
            break;
        case 'C':
            $Nom_Rub = 'Titre';
            break;
        case 'K':
            $Nom_Rub = 'Nom_Ville';
            break;
        default:
            break;
    }

    if ($res->rowCount() > 0) {

        //$res->closeCursor();
        //$res = lect_sql($sql1);


        $Anc_Nom = '';
        $premier = true;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $Nouv_Nom = $row[$Nom_Rub];
            if ($Nouv_Nom != $Anc_Nom) {
                if ($premier) HTML_ou_PDF("<br>\n", $sortie);
                $premier = false;
                if ($texte) HTML_ou_PDF("<br>\n", $sortie);
                HTML_ou_PDF('<b>' . $Nouv_Nom . '</b><br>', $sortie);
                $Anc_Nom = $Nouv_Nom;
            }

            switch ($Type_Liste) {
                case 'P':
                case 'N':
                case 'C':
                case 'D':
                    $Ref = $row['Reference'];
                    break;
                case 'M':
                case 'K':
                    $Refm = $row['Referencem'];
                    $Reff = $row['Referencef'];
                    break;
                default:
                    break;
            }

            if (($Type_Liste != 'M') and ($Type_Liste != 'K')) $Ref = $row['Reference'];
            HTML_ou_PDF('   ', $sortie);

            switch ($Type_Liste) {
                case 'C':
                case 'P':
                    HTML_ou_PDF($row['Prenoms'], $sortie);
                    Aff_Ne_Dec($row);
                    break;
                case 'N':
                case 'D':
                    HTML_ou_PDF($row['Nom'] . ' ' . $row['Prenoms'], $sortie);
                    Aff_Ne_Dec($row);
                    break;
                case 'M':
                    HTML_ou_PDF($row['Nomf'] . ' ' . $row['Prenomsf'], $sortie);
                    HTML_ou_PDF('   ' . $row['Nomm'] . ' ' . $row['Prenomsm'], $sortie);
                    if ($row['Maries_Le'] != '') HTML_ou_PDF(' (x ' . Etend_date($row['Maries_Le']) . ')', $sortie);
                    break;
                case 'K':
                    HTML_ou_PDF($row['Nomf'] . ' ' . $row['Prenomsf'], $sortie);
                    HTML_ou_PDF('   ' . $row['Nomm'] . ' ' . $row['Prenomsm'], $sortie);
                    if ($row['Date_K'] != '') HTML_ou_PDF(' (x ' . Etend_date($row['Date_K']) . ')', $sortie);
                    break;
                default:
                    break;
            }
            HTML_ou_PDF("<br>\n", $sortie);
        }
    }
    $res->closeCursor();

    if ($sortie_pdf) {
        $pdf->Output();
        exit;
    }
}

if (! $texte) Insere_Bas($compl);

?>
</body>

</html>