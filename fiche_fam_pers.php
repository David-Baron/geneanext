<?php
//=====================================================================
// Affichage d'une fiche familiale
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';
$titre = LG_FFAM_OBJECT;        // Titre pour META
$x = Lit_Env();
controle_utilisateur('I');
$index_follow = 'IN';            // NOFOLLOW demandé pour les moteurs

$Refer = Recup_Variable('Refer', 'N');
$req_sel = 'select * from ' . nom_table('personnes') . ' where Reference = ' . $Refer . ' limit 1';

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Affichage des enfants avec le conjoint éventuel
function Aff_Enfants($Mari, $Femme, $type_aff = 'E', $exclu = 0)
{
    global $root, $Icones, $chemin_images_util, $est_contributeur, $SiteGratuit, $Premium, $premier_enf, $lst_conj, $premier_lib_v, $h_LG_AT, $LG_Data_noavailable_profile, $Commentaire, $Diffusion_Commentaire_Internet, $rech_comment_ville, $debug;
    if (($Mari) or ($Femme)) {
        $crit = '';
        if ($type_aff == 'E') {
            $premier_enf = true;
            $crit = 'Pere = ' . $Mari . ' and Mere = ' . $Femme;
            // Cas des enfants hors union
            // On sélectionne les enfants avec un conjoint non déjà parcouru, donc 0 compris
            if ((!$Mari) or (!$Femme)) {
                if ((isset($lst_conj)) and ($lst_conj != '')) {
                    $crit = str_replace(' = 0', ' not in (' . $lst_conj . ')', $crit);
                }
            }
        } else {
            if (($Mari) and ($Femme)) $crit = 'Pere = ' . $Mari . ' or Mere = ' . $Femme;
            else {
                if ($Mari) $crit = 'Pere = ' . $Mari;
                if ($Femme) $crit = 'Mere = ' . $Femme;
            }
        }

        $n_filiations = nom_table('filiations');

        $sql = 'select Enfant, rang, Pere, Mere from ' . $n_filiations . ' where ' . $crit . ' order by Rang';
        $resE = lect_sql($sql);

        $nb_enreg = $resE->rowCount();

        // Existe-t-il au moins 1 image par défaut sur l'un des enfants
        // Permettra d'afficher une colonne si présence
        $existe_image_E = false;
        $sqlI = 'select 1 from ' . nom_table('images')
            . " where Defaut = 'O' and Type_Ref = 'P' and Reference in "
            . "(select Enfant from " . $n_filiations . " where " . $crit . ") limit 1";
        $resI = lect_sql($sqlI);
        if ($enregI = $resI->fetch(PDO::FETCH_NUM)) {
            $existe_image_E = true;
        }
        $resI->closeCursor();
        // echo 'existe_image_E : '.$existe_image_E.'<br />';

        if ($type_aff == 'E') {
            if (($premier_enf) or ($nb_enreg > 0)) {
                echo '<br />';
                if (($Mari) and ($Femme)) {
                    echo my_html(LG_FFAM_CHILDREN_WITH) . ' :';
                    if ($est_contributeur) {
                        if ((!$SiteGratuit) or ($Premium))
                            echo ' ' . Affiche_Icone_Lien('href="' . $root . '/ajout_enfants.php?mari=' . $Mari . '&amp;femme=' . $Femme . '"', 'ajout', LG_FFAM_ADD_CHILDREN);
                    }
                } else {
                    if ($nb_enreg > 0) {
                        if ($premier_enf) echo '<hr/>';
                        echo my_html(LG_FFAM_CHLIDREN_NO_UNION) . ' :';
                    }
                }
                echo '<br />' . "\n";
                $premier_enf = false;
            }
        } else {
            if ($nb_enreg > 1) echo '<br />' . my_html(LG_FFAM_BROTHERS_SISTERS) . ' :<br />' . "\n";
        }

        if ($nb_enreg > 0) {

            $rangs_OK   = true;
            $dates_OK   = 1;
            $Ne_Prec    = '00000000GL';
            $Rang_Prec  = 0;
            $nb_enfants = 0;

            $w1 = 5;
            $w2 = 20;
            $w3 = 80;
            $w4 = 5;

            while ($row = $resE->fetch(PDO::FETCH_ASSOC)) {
                $Enfant = $row['Enfant'];
                $LeRang = intval($row['rang']);
                // Les rangs ne sont pas OK s'il y en a 1 à 0
                if ($LeRang == 0) $rangs_OK = false;
                // Séquentialité des rangs ?
                ++$Rang_Prec;
                if ($LeRang != $Rang_Prec) $rangs_OK = false;
                if ($Enfant != $exclu) {

                    $sqlEnf = 'select Nom, Prenoms, Ne_le, Decede_Le, Diff_Internet, Sexe, Ville_Naissance, Ville_Deces, Numero, Surnom '
                        . ' from ' . nom_table('personnes')
                        . ' where Reference = ' . $Enfant . ' limit 1';
                    $resEnf = lect_sql($sqlEnf);
                    $enregEnf = $resEnf->fetch(PDO::FETCH_ASSOC);
                    if ($debug) var_dump($enregEnf);

                    if ($_SESSION['estPrivilegie'] or ($enregEnf['Diff_Internet'] == 'O')) {

                        $sexe = $enregEnf['Sexe'];
                        $sur = $enregEnf['Surnom'];
                        if (is_null($sur))
                            $sur = '';
                        if ($sur != '') $sur = ', ' . lib_sexe_nickname($sexe) . ' ' . $sur;
                        // if ($sur != '') $sur = Lib_sexe(', '.my_html($LG_FFam_alt_name),$sexe).' '.$sur;
                        ++$nb_enfants;

                        if ($nb_enfants == 1) {
                            //echo '<table border="0" width="90%">'."\n";
                            echo '<table border="0" width="90%">' . "\n";
                        }
                        echo '<tr>' . "\n";
                        echo '<td width="' . $w1 . '%"><img src="' . $root . '/assets/img/' . $Icones['couple_donne'] . '" alt="" title=""></td>' . "\n";

                        // Recherche de la présence d'une image par défaut
                        if ($existe_image_E) {
                            $image = Rech_Image_Defaut($Enfant, 'P');
                            if ($image != '') {
                                echo '<td width="' . $w2 . '%" align="center" valign="middle">';
                                $image = $chemin_images_util . $image;
                                Aff_Img_Redim_Lien($image, 100, 100);
                            } else {
                                echo '<td width="' . $w2 . '%"> ';
                            }
                            echo '</td>' . "\n";
                        }

                        // Calcul la génération à partir du numéro de la personne, pour mettre une icone spécifique sur les personnes de la lignée
                        $gen = '';
                        $icone_encadre = '';
                        $numero_enf = trim($enregEnf['Numero']);
                        if (is_numeric($numero_enf)) $gen = Calc_Gener($numero_enf);
                        if ($gen != '') $icone_encadre = Affiche_Icone_Lien('href="' . $root . '/desc_directe_pers.php?Numero=' . $numero_enf . '"', 'fleche_haut', $gen);
                        echo '<td width="' . $w3 . '%">' . $icone_encadre . '<a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Enfant . '">' . $enregEnf['Prenoms'] . ' ' . $enregEnf['Nom'] . '</a>' . $sur . $icone_encadre . ' ';

                        $Ne = $enregEnf['Ne_le'];
                        $Date_Nai = Etend_date_2($Ne);
                        $Date_Dec = Etend_date_2($enregEnf['Decede_Le']);
                        $Ville_Nai = $enregEnf['Ville_Naissance'];
                        $Ville_Dec = $enregEnf['Ville_Deces'];

                        $Commentaire = '';
                        $Diffusion_Commentaire_Internet = 'N';

                        if (($Date_Nai != '') or ($Ville_Nai <> 0)) {
                            echo ' ' . lib_sexe_born($sexe) . ' ';
                            if ($Date_Nai != '') {
                                // On contrôle que la date, si elle est précise est postérieure à la précédente
                                if ((strlen($Ne) == 10) and ($Ne[9] == 'L')) {
                                    if ($Ne < $Ne_Prec) $dates_OK = 0;
                                    $Ne_Prec = $Ne;
                                }
                                echo $Date_Nai . ' ';
                            }
                            if ($Ville_Nai <> 0) {
                                echo $h_LG_AT . ' ' . lib_ville_new($Ville_Nai, 'O', $rech_comment_ville);
                                if ($premier_lib_v) {
                                    appelle_carte_osm();
                                    if (($Commentaire != '') and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
                                        echo Div_Note($Commentaire);
                                    }
                                }
                            }
                        }

                        if (($Date_Dec != '') or ($Ville_Dec <> 0)) {
                            if (($Date_Nai != '') or ($Ville_Nai <> 0)) echo ', ';
                            echo lib_sexe_dead($sexe) . ' ';
                            if ($Date_Dec != '') echo $Date_Dec . ' ';
                            if ($Ville_Dec <> 0) {
                                echo $h_LG_AT . ' ' . lib_ville_new($Ville_Dec, 'O', $rech_comment_ville);
                                if ($premier_lib_v) {
                                    appelle_carte_osm();
                                    if (($Commentaire != '') and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet == 'O'))) {
                                        echo Div_Note($Commentaire);
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '<td  width="' . $w4 . '%">';
                        if ($type_aff == 'E') {
                            if (!$rangs_OK) echo '<img src="' . $root . '/assets/img/' . $Icones['warning'] . '" alt="' . LG_FFAM_RANK_ISSUE . '" title="' . LG_FFAM_RANK_ISSUE . '">';
                            else echo ' ';
                        } else echo ' ';
                        echo '</td>';
                    } else echo '<td colspan="4">' . my_html($LG_Data_noavailable_profile) . '</td>';
                    echo "</tr>\n";
                }
            }
            if ($nb_enfants > 0) {
                echo '</table>' . "\n";
                if ($type_aff == 'E') //and ((! $rangs_OK) or (! $dates_OK)))
                    echo '<a href="' . $root . '/edition_rangs.php?Pere=' . $Mari . '&amp;Mere=' . $Femme . '"><img src="' . $root . '/assets/img/' . $Icones['arrange'] . '" alt="' . LG_FFAM_RANK_REORG . '" title="' . LG_FFAM_RANK_REORG . '"></a>';
            }
        }
        unset($enregEnf);
        $resE->closeCursor();
        unset($resE);
    }
}

$compl = '';

// Personne inconnue, circulez...
if ((!$enreg_sel) or ($Refer == 0)) {
    echo '</head><body>';
    echo '<a href="' . $root . '/">' . $LG_back_to_home . '</a>';
    echo '<div id="bonus"></div>';
} else {

    $enreg = $enreg_sel;
    unset($enreg_sel);
    rectif_null_pers($enreg);
    $enreg2 = $enreg;
    $diff_int = $enreg2['Diff_Internet'];

    if (!$est_privilegie and $diff_int != 'O') {
        echo aff_erreur($LG_Data_noavailable_profile) . '<br />' . '<a href="' . $root . '/">' . my_html($LG_back_to_home) . '</a><br />';
        return;
    }

    $EnrPers = $enreg2;
    $Sexe = $enreg2['Sexe'];

    // Mémorisation de la personne consultée
    memo_pers($Refer, $enreg['Nom'], $enreg['Prenoms']);

    // NB : tout le monde peut contribuer sur Internet
    if ($Environnement == 'I') {
        $txt_im = LG_FFAM_CONTRIBUTE . ' ' . $enreg['Prenoms'] . ' ' . $enreg['Nom'];
        $compl .= '<a href="' . $root . '/ajout_contribution.php?Refer=' . $Refer . '"><img src="' . $root . '/assets/img/' . $Icones['contribuer'] . '" alt="' . my_html($txt_im) . '" title="' . my_html($txt_im) . '"></a>  ';
    }
    if (!$is_bot)
        $compl .= Affiche_Icone_Lien('href="' . $root . '/vue_personnalisee_Rapide.php?Refer=' . $Refer . '"', 'vue_pers', LG_FFAM_SET_AS_DECUJUS) . "\n";

    // Cache la personne ou la montre sur internet
    if ($est_contributeur) {
        if ($diff_int == 'O')
            $compl .= Affiche_Icone_Lien('href="' . $root . '/cache_montre_rapide.php?Refer=' . $Refer . '&amp;Diff=N"', 'internet_non', LG_FFAM_NOSHOW_INTERNET) . "\n";
        else
            $compl .= Affiche_Icone_Lien('href="' . $root . '/cache_montre_rapide.php?Refer=' . $Refer . '&amp;Diff=O"', 'internet_oui', LG_FFAM_SHOW_INTERNET) . "\n";
    }

    $compl .= Lien_Chrono_Pers($Refer) . "\n";
    $compl .= Ajoute_Page_Info(600, 150);
    if ($est_privilegie)
        $compl .= Affiche_Icone_Lien('href="' . $root . '/exp_gedcom_personne.php?Refer=' . $Refer . '"', 'gedcom', $LG_Menu_Title['Exp_Ged_Pers']) . "\n";
    $compl .= '<a href="' . $root . '/arbre_asc_pers.php?Refer=' . $Refer . '">' .
        '<img border="0" src="' . $chemin_images_icones . $Icones['arbre_ascP'] . '" alt="Arbres" onmouseover="inverse_div(\'bonus\');"/>' .
        '</a> ' .
        Affiche_Icone_Lien('href="' . $root . '/arbre_desc_pers.php?Refer=' . $Refer . '"', 'arbre_desc', $LG_desc_tree) . "\n";
    if ($est_contributeur) {
        $compl .= Affiche_Icone_Lien(Ins_Edt_Pers($Refer), 'fiche_edition', $LG_modify) . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/ajout_rapide.php?Refer=' . $Refer . '"', 'ajout_rapide', $LG_quick_adding) . ' ';
    }

    if (Presence_ImagesP($Refer)) {
        $compl = Affiche_Icone_Lien('href="' . $root . '/liste_images.php?Refer=' . $Refer . '&amp;Type_Ref=P"', 'images', 'Images') . ' ' . $compl;
    }

    // Calcul la génération à partir du numéro de la personne
    $gen = '';
    $icone_encadre = '';
    $numero_pers = trim($enreg2['Numero']);
    if (is_numeric($numero_pers)) $gen = Calc_Gener($numero_pers);
    if ($gen != '') $icone_encadre = Affiche_Icone_Lien('href="' . $root . '/desc_directe_pers.php?Numero=' . $numero_pers . '"', 'fleche_haut', $gen);

    $le_nom = str_replace(' ', '%20', $EnrPers['Nom']);
    $params = '&amp;idNom=' . $EnrPers['idNomFam'] . '&amp;Nom=' . $le_nom;
    $icone_nom =  '<a href="' . $root . '/liste_pers2.php?Type_Liste=P' . $params . '"><img id="img_nom" src="' . $root . '/assets/img/' . $Icones['liste_nom'] . '" alt="' . LG_FFAM_ALL_NAME . ' ' . $EnrPers['Nom'] . '" title="' . LG_FFAM_ALL_NAME . ' ' . $EnrPers['Nom'] . '"></a>';
    Insere_Haut($icone_encadre . ' ' . $enreg2['Prenoms'] . ' ' . $enreg2['Nom'] . ' ' . $icone_nom, $compl, 'Fiche_Fam_Pers', $Refer);

    // Sous-menu pour les arbres
    echo '<div id="bonus" style="position:absolute; top:50px; right:50px; border:dotted 1px black;">' . "\n";
    echo '<form method="post" action=""><select name="example" size="1" onchange="document.location = this.options[this.selectedIndex].value;">' .  "\n";
    //	Le menu déroulant : choix standards
    echo '<option value="">Afficher un arbre ...</option>' . "\n" .
        '<option value="Arbre_Asc_Pers.php?Refer=' . $Refer . '">Arbre standard</option>' . "\n" .
        '<option value="Arbre_Agnatique_Cognatique.php?Refer=' . $Refer . '&amp;Type=A">' . my_html(LG_FFAM_MEN_ASC) . '</option>' . "\n" .
        '<option value="Arbre_Agnatique_Cognatique.php?Refer=' . $Refer . '&amp;Type=C">' . my_html(LG_FFAM_WOMEN_ASC) . '</option>' . "\n" .
        '<option value="' . $root . '/appelle_image_arbre_asc.php?Refer=' . $Refer . '">' . my_html(LG_FFAM_PRINTABLE_TREE) . '</option>' . "\n";
    if ((!$SiteGratuit) or ($Premium))
        echo '<option value="' . $root . '/arbre_asc_pdf.php?Refer=' . $Refer . '">PDF 7 g&eacute;n.</option>' . "\n";

    //	Traitement des listes personnalisées
    $sql = 'select descArbre ,  nomFichier from ' . nom_table('arbre') . ' AS a, ' . nom_table('arbrepers') . ' AS p where p.idArbre = a.idArbre AND reference = ' . $Refer;
    $result = lect_sql($sql);
    if ($result->rowCount() > 0) {
        // Lecture du nom du répertoires
        $sql = 'select valeur from ' . nom_table('arbreparam') . ' where ident1 = \'repertoire\' AND ident2 = \'genPdf\'';
        $res = lect_sql($sql);
        if ($row = $res->fetch(PDO::FETCH_NUM)) {
            $rep = $row[0];
        } else {
            echo LG_FFAM_ERROR . '<br />';
            exit;
        }
        //	Menu pour les images
        echo '<optgroup label="' . LG_FFAM_CUST_TREES . '">';
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $description = $row['descArbre'];
            $nomFichier = $row['nomFichier'];
            echo '<option value="Arbre_Perso.php?nomArbre=' . rawurlencode($nomFichier) . '&amp;Refer=' . $Refer . '">' . $description  . '</option>' . "\n";
        }
        echo '</optgroup>';
    }
    echo '</select></form></div>' . "\n";

    // Fiche individuelle et export pdf disponible à partir de privilégié
    if ($est_privilegie) {
        echo Affiche_Icone_Lien(Ins_Ref_Indiv($Refer), 'text', $LG_Menu_Title['Indiv_Text_Report']) . ' ';
        if ((!$SiteGratuit) or ($Premium)) {
            echo Affiche_Icone_Lien(Ins_Ref_Indiv($Refer, 'P'), 'PDF', LG_FFAM_INDIV_TEXT_PDF) . ' ';
        }
        echo '<br />';
    }

    $h_LG_AT = my_html(LG_AT);

    // On ne va chercher les commentaires des villes que sur les premium pour les sites hébergés
    $rech_comment_ville = false;
    if ((!$SiteGratuit) or ($Premium))
        $rech_comment_ville = true;

    // Affichage des données de la personne et affichage des parents
    $x = Aff_Personne($enreg2, $Refer, false, 'H');
    $le_pere = $Pere;
    $la_mere = $Mere;

    //  Liens avec d'autres personnes
    $x = Aff_Liens_Pers($Refer, 'N');

    //  Evenements lies a la personne
    echo '<br />' . "\n";
    Aff_Evenements_Pers($Refer, 'N');

    //  Evenements lies a la filiation
    echo '<br />' . "\n";
    $x = Aff_Evenements_Objet($Refer, 'F', 'N');

    //  Documents lies a la personne
    $x = Aff_Documents_Objet($Refer, 'P', 'O');

    //  Sources lies a la personne
    $x = Aff_Sources_Objet($Refer, 'P', 'O');

    echo '<hr/>';

    // Récupération des conjoints
    $sql = 'select * from ' . nom_table('unions') . ' where Conjoint_1 = ' . $Refer . ' or Conjoint_2 =' . $Refer . ' ORDER BY maries_Le';

    $Un = true;
    $lst_conj = '';

    $n_concerne_objet = nom_table('concerne_objet');
    $n_evenements = nom_table('evenements');

    if ($resUn = lect_sql($sql)) {
        while ($enreg = $resUn->fetch(PDO::FETCH_ASSOC)) {
            $premier_enf = true;
            if (!$Un) {
                echo '<div class="dashhr"><hr align="left" width="90%"/></div>';
            } else echo '<b>' . my_html(LG_FFAM_HUSB_WIFE) . ' :</b><br />' . "\n";
            $Un = false;

            $Ref_Union     = $enreg['Reference'];
            $Date_Mar      = $enreg['Maries_Le'];
            $Ville_Mar     = $enreg['Ville_Mariage'];
            $Mari          = $enreg['Conjoint_1'];
            $Femme         = $enreg['Conjoint_2'];
            $Date_K        = $enreg['Date_K'];
            $Ville_Notaire = $enreg['Ville_Notaire'];
            $Notaire       = $enreg['Notaire_K'];

            $Existe_Commentaire = Rech_Commentaire($Ref_Union, 'U');
            $Existe_Commentaire_cm = $Existe_Commentaire;
            $Diffusion_Commentaire_Internet_cm = $Diffusion_Commentaire_Internet;
            $Commentaire_cm = $Commentaire;

            if ($Femme == $Refer) $Conj = $Mari;
            else $Conj = $Femme;
            if ($lst_conj != '') $lst_conj .= ',';
            $lst_conj .= $Conj;

            $sql = 'select * from ' . nom_table('personnes') . ' where reference = ' . $Conj . ' limit 1';
            $resP = lect_sql($sql);
            $enreg2 = $resP->fetch(PDO::FETCH_ASSOC);
            $resP->closeCursor();
            unset($resP);

            if ($est_privilegie or $enreg2['Diff_Internet'] == 'O') {

                // On n'affiche le lien vers le conjoint que s'il est connu
                if ($Conj != 0) echo '<a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Conj . '">' . $enreg2['Prenoms'] . ' ' . $enreg2['Nom'] . '</a> ; ';

                // Fiche couple et export pdf disponible à partir de privilégié
                if ($est_privilegie) {
                    echo Affiche_Icone_Lien(Ins_Ref_Fam($Ref_Union), 'text', LG_FFAM_COUPLE_REC) . ' ';
                    if ((!$SiteGratuit) or ($Premium)) {
                        echo Affiche_Icone_Lien(Ins_Ref_Fam($Ref_Union, 'P'), 'PDF', LG_FFAM_COUPLE_REC_PDF) . ' ';
                    }
                }
                echo Affiche_Icone_Lien('href="' . $root . '/arbre_noyau.php?Reference=' . $Ref_Union . '"', 'groupe', $LG_Menu_Title['Nuclear_Family']) . ' ';
                echo Affiche_Icone_Lien('href="' . $root . '/asc_conjoints.php?Reference=' . $Ref_Union . '"', 'asc_conj', $LG_Menu_Title['Partners_Ancestors']) . ' ';

                if (($Date_Mar != '') or ($Ville_Mar)) {
                    echo 'mari&eacute;s';
                    echo ' ' . Etend_date_2($Date_Mar);
                    if ($Ville_Mar != 0) {
                        echo ' ' . $h_LG_AT . ' ' . lib_ville_new($Ville_Mar, 'O', false);
                        if ($premier_lib_v) appelle_carte_osm();
                    }
                }

                if (Presence_ImagesU($Ref_Union)) {
                    echo ' ' . Affiche_Icone_Lien('href="' . $root . '/liste_images.php?Refer=' . $Ref_Union . '&amp;Type_Ref=U"', 'images', 'Images du couple') . ' ';
                }

                if (($Date_K != '') or ($Ville_Notaire != 0)) {
                    echo ', ' . my_html(LG_PERS_CONTRACT) . ' ' . Etend_date_2($Date_K);
                    if ($Notaire != '') echo ' ' . my_html(LG_PERS_MAITRE . ' ' . $Notaire);
                    if ($Ville_Notaire != 0) echo ', ' . my_html(LG_PERS_NOTARY) . ' ' . $h_LG_AT . ' ' . lib_ville_new($Ville_Notaire, 'O', true);
                    if ($premier_lib_v) appelle_carte_osm();
                    echo ' ';
                }

                if (($Existe_Commentaire_cm) and (($_SESSION['estPrivilegie']) or ($Diffusion_Commentaire_Internet_cm == 'O'))) {
                    if ($aff_note_old)
                        Div_Note_Old('ajout' . $Ref_Union, 'id_div_ajoutU' . $Ref_Union, $Commentaire_cm);
                    else
                        echo Div_Note($Commentaire_cm);
                }

                // Recherche d'un divorce éventuel
                if ($Date_Mar != '') {
                    if (get_divorce($Ref_Union)) echo $lib_div;
                }

                // Affichage des commentaires liés à l'union
                echo '<br />';
                $x = Aff_Documents_Objet($Ref_Union, 'U', 'O');

                // Affichage des données de la personne et affichage des parents
                echo "<br />\n";
                rectif_null_pers($enreg2);
                $x = Aff_Personne($enreg2, $Conj, true, 'H');

                // Affichage des évènements liés à l'union
                $x = Aff_Evenements_Objet($Ref_Union, 'U', 'N');

                // Affichage des enfants du couple
                Aff_Enfants($Mari, $Femme);
            } else echo my_html($LG_Data_noavailable_profile) . '.<br />';
        }
    }

    // Enfants sans conjoint connu
    $Mari = 0;
    $Femme = 0;
    switch ($Sexe) {
        case 'm':
            $Mari = $Refer;
            $Femme = 0;
            break;
        case 'f':
            $Mari = 0;
            $Femme = $Refer;
            break;
    }
    Aff_Enfants($Mari, $Femme);

    // Affichage de la fratrie
    echo '<hr/>';
    Aff_Enfants($le_pere, $la_mere, 'F', $Refer);
    if (!$is_bot) {
        echo '<a href="' . $root . '/parentees.php?TP=OT&amp;Refer=' . $Refer . '"> ' . my_html($LG_Menu_Title['Pers_Uncles']) . '</a><br />';
        echo '<a href="' . $root . '/parentees.php?TP=CG&amp;Refer=' . $Refer . '"> ' . my_html($LG_Menu_Title['Pers_Cousins']) . '</a>';
    }

    Insere_Bas($compl);
}

?>

<!-- On cache le div de visualisation étendue des arbres  -->
<script type="text/javascript">
    cache_div("bonus");
</script>

</body>

</html>