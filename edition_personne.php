<?php
//=====================================================================
// Edition d'une personne
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'Horigine',
    'NomP',
    'ANomP',
    'AidNomP',            // 'idNomP', est recalculé
    'PrenomsP',
    'APrenomsP',
    'SurnomP',
    'ASurnomP',
    'SexeP',
    'ASexeP',
    'NumeroP',
    'ANumeroP',
    'CNe_leP',
    'ANe_leP',
    'Ville_NaissanceP',
    'AVille_NaissanceP',
    'CDecede_LeP',
    'ADecede_LeP',
    'Ville_DecesP',
    'AVille_DecesP',
    'DiversP',
    'ADiversP',
    'Diff_Internet_NoteP',
    'ADiff_Internet_NoteP',
    'Diff_InternetP',
    'ADiff_InternetP',
    'Statut_Fiche',
    'AStatut_Fiche',
    'Categorie',
    'ACategorie'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées - phase 1
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');
$Modif = true;
if ($Refer == -1) $Modif = false;

$acces = 'M';                                              // Type d'accès de la page : (M)ise à jour, (L)ecture
if ($Modif) $titre = $LG_Menu_Title['Person_Modify'];    // Titre pour META
else $titre = $LG_Menu_Title['Person_Add'];
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Existe_Union = false;

$largP = 16;

function lien_aj_union($unisexe)
{
    global $root, $SexePers, $Refer;
    $lib = 'Ajouter une union';
    if ($unisexe == 'o') $lib .= ' unisexe';
    //echo my_html($lib).'&nbsp;:&nbsp;';
    $lien = 'href="'. $root .'/edition_union.php?Conjoint=' . $Refer . '&amp;Ref_Union=-1';
    if ($unisexe == 'o') {
        $us = 'o';
        echo my_html(LG_PERS_UNION_UNISEX);
    } else {
        $us = 'n';
        echo my_html(LG_PERS_UNION_MULTISEX);
    }
    //echo '&nbsp;'.Affiche_Icone_Lien($lien.'"','ajout',$lib).'<br />';
    echo '&nbsp;' . Affiche_Icone_Lien(Ins_Edt_Union(-1, $Refer, $us), 'ajout', $lib) . '<br />' . "\n";
}

// Affiche une personne et ses parents
function Aff_PersonneI($enreg2, $Personne, $Decalage)
{
    global $root, $Refer, $Pere_GP, $Mere_GP, $Rang_GP, $Comportement, $Icones, $Images, $chemin_images_util, $chemin_images, $chemin_images_icones, $Commentaire, $Diffusion_Commentaire_Internet, $Modif, $Existe_Filiation, $Existe_Union, $Existe_Enfants, $SexePers, $SiteGratuit, $Premium, $LG_Data_tab, $LG_File, $lib_OK, $lib_Annuler, $LG_at, $LG_with, $LG_tip, $LG_of, $LG_andof, $largP, $death_def_min_year, $url_matchid, $url_matchid_sch, $LG_Menu_Title;

    $Existe_Filiation = false;
    $Existe_Enfants = false;

    $lib_OK_h      = my_html($lib_OK);
    $lib_Annuler_h = my_html($lib_Annuler);
    $lg_add_town_list_h = my_html(LG_ADD_TOWN_LIST);

    echo '<div id="content">' . "\n";
    echo '<table id="cols" border="0" cellpadding="0" cellspacing="0" >' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="border-right:0px solid #9cb0bb">' . "\n";
    echo '  <img src="' . $chemin_images . $Images['clear'] . '" width="850" height="1" alt="clear"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr>' . "\n";
    echo '<td class="left">' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";
    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pnlData\', this)" id="tab1">' . my_html($LG_Data_tab) . '</a></li>' . "\n";
    // Certains onglets ne sont disponibles qu'en modification
    if ($Refer != -1) {
        echo '<li><a href="#" onclick="return showPane(\'pnlParentsUnions\', this)">' . my_html(LG_PERS_PARENTS_UNIONS) . '</a></li>' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pnlEvts\', this)">' . my_html(LG_PERS_EVENTS) . '</a></li>' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pnlLiens\', this)">' . my_html(LG_PERS_LINKS) . '</a></li>' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pnlNoms\', this)">' . my_html(LG_PERS_ALT_NAMES) . '</a></li>' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pnlDocs\', this)">' . my_html(LG_PERS_DOCS) . '</a></li>' . "\n";
    }
    echo '<li><a href="#" onclick="return showPane(\'pnlFile\', this)">' . my_html($LG_File) . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<div class="tab-panes">' . "\n";
    // Onglets données générales de la personne
    echo '<div id="pnlData">' . "\n";
    // Etat civil de la personne
    echo '<fieldset>' . "\n";
    aff_legend(LG_PERS_DATA);
    echo '<table width="98%" border="0">' . "\n";
    // Nom
    $c_zone = $enreg2['Nom'];
    $id_nom = $enreg2['idNomFam'];
    if ($id_nom == '') $id_nom = -1;
    col_titre_tab_noClass(LG_PERS_NAME, $largP);
    echo '<td><input type="hidden" name="NomP" id="NomP" value="' . $id_nom . '/' . $c_zone . '" class="oblig"/>' . "\n";
    echo '<input type="hidden" name="AidNomP" value="' . $id_nom . '"/>' . "\n";
    Select_Noms($id_nom, 'NomSel', 'NomP');
    Img_Zone_Oblig('imgObligNom');
    echo '<input type="hidden" name="ANomP" value="' . $id_nom . '/' . $c_zone . '"/>' . "\n";
    // Possibilité d'ajouter un nom
    echo '<img id="ajout_nom" src="' . $chemin_images_icones . $Icones['ajout'] . '" alt="' . LG_PERS_ADD_NAME . '" title="' . LG_PERS_ADD_NAME . '"' .
        'onclick="inverse_div(\'id_div_ajout_nom\');document.getElementById(\'nouveau_nom\').focus();"/>' . "\n";
    if (isset($_SESSION['Nom_Saisi'])) {
        echo '&nbsp;<img id="Ireprend_nom" src="' . $chemin_images_icones . $Icones['copier'] . '" alt="' . LG_PERS_SAME_NAME . '" title="' . LG_PERS_SAME_NAME . '"' .
            ' onclick="reprend_nom();"/>' . "\n";
    }
    echo '<div id="id_div_ajout_nom">' . "\n";
    echo LG_ADD_NAME . '&nbsp;<input type="text" size="50" name="nouveau_nom" id="nouveau_nom"/>' . "\n";
    echo '&nbsp;<img id="majuscule" src="' . $chemin_images_icones . $Icones['majuscule'] . '" alt="' . LG_NAME_TO_UPCASE . '" title="' . LG_NAME_TO_UPCASE . '"' .
        ' onclick="NomMaj();document.getElementById(\'NomP\').focus();"/>' . "\n";
    echo '<input type="button" name="ferme_OK_nom" value="' . $lib_OK_h . '" onclick="ajoute_nom()"/>' . "\n";
    echo '<input type="button" name="ferme_An_nom" value="' . $lib_Annuler_h . '" onclick="inverse_div(\'id_div_ajout_nom\')"/>' . "\n";
    echo '</div>' . "\n";
    echo '</td>' . "\n";
    // Affichage de l'image par défaut pour la personne
    echo '<td rowspan="3" align="center" valign="middle">';
    // Recherche de la présence d'une image par défaut
    $image = Rech_Image_Defaut($Refer, 'P');
    if ($image != '') {
        $image = $chemin_images_util . $image;
        Aff_Img_Redim_Lien($image, 110, 110, "id_" . $Refer);
    } else echo '&nbsp;';
    echo '</td></tr>' . "\n";

    // Prénoms
    $c_zone = $enreg2['Prenoms'];
    col_titre_tab_noClass(LG_PERS_FIRST_NAME, $largP);
    echo '<td colspan="2"><input type="text" size="50" name="PrenomsP" id="PrenomsP" value="' . $c_zone . '" class="oblig"/>&nbsp;' . "\n";
    Img_Zone_Oblig('imgObligPrenom');
    echo '<input type="hidden" name="APrenomsP" value="' . $c_zone . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    // Surnom
    $c_zone = $enreg2['Surnom'];
    col_titre_tab_noClass(LG_PERS_SURNAME, $largP);
    echo '<td colspan="2"><input type="text" size="50" name="SurnomP" id="SurnomP" value="' . $c_zone . '"/>' . "\n";
    echo '<input type="hidden" name="ASurnomP" value="' . $c_zone . '"/>' . "\n";
    echo '</td></tr>' . "\n";

    //Sexe
    $SexePers = $enreg2['Sexe'];
    col_titre_tab_noClass(LG_SEXE, $largP);
    echo '<td colspan="2"><input type="radio" id="SexeP_m" name="SexeP" id="SexeM" value="m"';
    if ($enreg2['Sexe'] == 'm') echo ' checked="checked"';
    echo ' /><label for="SexeP_m">' . LG_SEXE_MAN . "</label>&nbsp;";
    echo '<input type="radio" id="SexeP_f" name="SexeP" id="SexeF" value="f"';
    if ($enreg2['Sexe'] == 'f') echo ' checked="checked"';
    echo ' /><label for="SexeP_f">' . LG_SEXE_WOMAN . '</label>';
    echo '<input type="hidden" name="ASexeP" value="' . $SexePers . '"/></td>' . "\n";
    echo "</tr>\n";

    //Naissance
    col_titre_tab_noClass(LG_PERS_BORN, $largP);
    echo '<td colspan="2">';
    $date_naissance = $enreg2['Ne_le'];
    zone_date2('ANe_leP', 'Ne_leP', 'CNe_leP', $date_naissance);
    echo '&nbsp;' . my_html($LG_at) . '&nbsp;';
    aff_liste_villes(
        'Ville_NaissanceP',
        1,                  // C'est la première fois que l'on appelle la fonction dans la page
        0,                  // On est susceptible de rappeler la fonction
        $enreg2['Ville_Naissance']
    ); // Clé de sélection de la ligne
    echo '<input type="hidden" name="AVille_NaissanceP" value="' . $enreg2['Ville_Naissance'] . '"/>' . "\n";
    // Possibilité d'ajouter une ville
    echo '<img id="ajout1" src="' . $chemin_images_icones . $Icones['ajout'] . '" alt="' . LG_ADD_TOWN . '" title="' . LG_ADD_TOWN . '"' .
        ' onclick="inverse_div(\'id_div_ajout1\');document.getElementById(\'nouvelle_ville1\').focus();"/>' . "\n";
    if (isset($_SESSION['Nom_Saisi'])) {
        echo '&nbsp;<img id="Ireprend_villeN" src="' . $chemin_images_icones . $Icones['copier'] . '" alt="' . LG_PERS_SAME_BIRTH_TOWN . '" title="' . LG_PERS_SAME_BIRTH_TOWN . '"' .
            ' onclick="reprend_villeN();"/>' . "\n";
    }
    echo '<div id="id_div_ajout1">' . "\n";
    echo $lg_add_town_list_h . '&nbsp;<input type="text" name="nouvelle_ville1" id="nouvelle_ville1" maxlength="80"/>' . "\n";
    echo '<input type="button" name="ferme_OK" value="' . $lib_OK_h . '" onclick="ajoute1();"/>' . "\n";
    echo '<input type="button" name="ferme_An" value="' . $lib_Annuler_h . '" onclick="inverse_div(\'id_div_ajout1\');"/>' . "\n";
    echo '</div>' . "\n";
    echo '</td></tr>';

    //Décès
    col_titre_tab_noClass(LG_PERS_DEAD, $largP);
    echo '<td colspan="2">';
    zone_date2('ADecede_LeP', 'Decede_LeP', 'CDecede_LeP', $enreg2['Decede_Le']);
    echo '&nbsp;' . my_html($LG_at) . '&nbsp;';
    aff_liste_villes(
        'Ville_DecesP',
        0,                  // Ce n'est pas la première fois que l'on appelle la fonction dans la page
        1,                  // On n'est pas susceptible de rappeler la fonction
        $enreg2['Ville_Deces']
    ); // Clé de sélection de la ligne
    echo '<input type="hidden" name="AVille_DecesP" value="' . $enreg2['Ville_Deces'] . '"/>';
    // Possibilité d'ajouter une ville
    echo '<img id="ajout2" src="' . $chemin_images_icones . $Icones['ajout'] . '" alt="' . LG_ADD_TOWN . '" title="' . LG_ADD_TOWN . '"' .
        'onclick="inverse_div(\'id_div_ajout2\');document.getElementById(\'nouvelle_ville2\').focus();"/>' . "\n";
    if (isset($_SESSION['Nom_Saisi'])) {
        echo '&nbsp;<img id="Ireprend_villeD" src="' . $chemin_images_icones . $Icones['copier'] . '" alt="' . LG_PERS_SAME_DEATH_TOWN . '" title="' . LG_PERS_SAME_DEATH_TOWN . '"' .
            ' onclick="reprend_villeD();"/>' . "\n";
    }
    echo '<div id="id_div_ajout2">' . "\n";
    echo $lg_add_town_list_h . '&nbsp;<input type="text" name="nouvelle_ville2" id="nouvelle_ville2" maxlength="80"/>' . "\n";
    echo '<input type="button" name="ferme_OK" value="' . $lib_OK_h . '" onclick="ajoute2()"/>' . "\n";
    echo '<input type="button" name="ferme_An" value="' . $lib_Annuler_h . '" onclick="inverse_div(\'id_div_ajout2\')"/>' . "\n";
    echo '</div>' . "\n";

    // Si pas de date de décès et date de naissance compatible, on va afficher un appel à MatchId
    if ($enreg2['Decede_Le'] == '') {
        if ((strlen($date_naissance) == 10) && ($date_naissance[9] == 'L')) {
            $annee = substr($date_naissance, 0, 4);
            if ($annee > $death_def_min_year) {
                // echo '<a href="'.$url_matchid_sch
                // .'?firstName='.UnPrenom($enreg2['Prenoms'])
                // .'&lastName='.$enreg2['Nom']
                // .'&sex='.strtoupper($enreg2['Sexe'])
                // .'&birthDate='.substr($date_naissance,6,2).'%2F'.substr($date_naissance,4,2).'%2F'.substr($date_naissance,0,4).'"'
                // .' target="_blank">Match Id</a> ';
                echo '<a href="'. $root .'/recherche_matchid_unitaire.php'
                    . '?ref=' . $Refer . '"'
                    . ' target="_blank">' . $LG_Menu_Title['MatchId_Sch'] . '</a>';
            }
        }
    }

    echo "</td></tr>\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    // Numéro de la personne
    echo '<fieldset>' . "\n";
    aff_legend(LG_PERS_NUMBER);
    echo '<table width="95%" border="0">' . "\n";
    $c_zone = $enreg2['Numero'];
    col_titre_tab_noClass(LG_PERS_NUMBER, $largP);
    echo '<td><input type="text" size="20" name="NumeroP" id="NumeroP" value="' . $c_zone . '"/>' . "\n";
    // Calculette pour étendre le numéro Sosa
    echo '<img id="calc1" src="' . $chemin_images_icones . $Icones['calculette'] . '" alt="' . LG_PERS_CALC_SOSA . '" title="' . LG_PERS_CALC_SOSA . '"' .
        ' onclick="etend_num_sosa();document.getElementById(\'NumeroP\').focus();"/>' . "\n";
    // Bouton pour numéro 1 ==> de cujus
    echo '<img id="im_decujus" src="' . $chemin_images_icones . $Icones['decujus'] . '" alt="' . LG_PERS_DECUJUS . '" title="' . LG_PERS_DECUJUS . '"' .
        ' onclick="decujus();document.getElementById(\'NumeroP\').focus();"/>' . "\n";
    echo '<input type="hidden" name="ANumeroP" value="' . $c_zone . '"/></td>' . "\n";
    echo "</tr>\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    // Commentaire
    echo '<fieldset>' . "\n";
    aff_legend(LG_CH_COMMENT);
    echo '<table width="95%" border="0">' . "\n";
    //Divers
    echo '<tr>' . "\n";
    echo '<td>';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Refer, 'P');
    echo '<textarea cols="50" rows="4" name="DiversP">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="hidden" name="ADiversP" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr><tr>';
    // Diffusion Internet commentaire
    echo '<td><label for="Diff_Internet_NoteP">' . LG_CH_COMMENT_VISIBILITY . '</label>'
        . '&nbsp;<input type="checkbox" id="Diff_Internet_NoteP" name="Diff_Internet_NoteP" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_Internet_NoteP" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";
    echo '</div>' . "\n";

    // Données de la fiche
    echo '<div id="pnlFile">' . "\n";
    $c_zone = $enreg2["Diff_Internet"];
    echo '<fieldset>' . "\n";
    aff_legend(LG_PERS_VISIBILITY);
    echo '<table width="95%" border="0">' . "\n";
    // Diffusion internet
    col_titre_tab_noClass(LG_PERS_INTERNET, $largP);
    echo '<td><input type="checkbox" name="Diff_InternetP" value="O"';
    if (($c_zone == 'O') or ($Refer == -1)) echo 'checked="checked"';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_InternetP" value="' . $c_zone . '"/></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";
    // Affiche les données propres à l'enregistrement de la fiche
    $x = Affiche_Fiche($enreg2, 1);

    // Affichage des tags
    echo '<fieldset>' . "\n";
    aff_legend(LG_PERS_CATEGORY);
    $categ_Fiche = $enreg2['Categorie'];
    $sql_cat = 'select Identifiant, Image, Titre from ' . nom_table('categories') . ' order by Ordre_Tri';
    $res_cat = lect_sql($sql_cat);
    while ($enr_cat = $res_cat->fetch(PDO::FETCH_NUM)) {
        $num_cat = $enr_cat[0];
        $nom_cat = 'tag_' . $enr_cat[1];
        $titre_cat = $enr_cat[2];
        echo '<input type="hidden" name="ACategorie" value="' . $num_cat . '"/>';
        echo '<input type="radio" id="Categorie_' . $num_cat . '" name="Categorie" value="' . $num_cat . '"';
        if ($categ_Fiche == $num_cat) echo ' checked="checked"';
        echo '/><label for="Categorie_' . $num_cat . '">'
            . '<img src="' . $chemin_images_icones . $Icones[$nom_cat] . '" border="0" alt="' . $titre_cat . '" title="' . $titre_cat . '"/>' . '</label>&nbsp;&nbsp;' . "\n";
    }
    $nb_tag = 0;
    echo '<input type="radio" id="Categorie_0" name="Categorie" value="0"';
    if ($categ_Fiche == 0) echo ' checked="checked"';
    echo '/><label for="Categorie_0">' . LG_PERS_NO_CATEGORY . '</label>';
    echo '</fieldset>' . "\n";

    if ((!$SiteGratuit) or ($Premium)) {
        //  Sources lies à la personne
        echo '<hr/>';
        $x = Aff_Sources_Objet($Refer, 'P', 'N');
        // Possibilité de lier une source pour la personne
        echo '<br />&nbsp;' . my_html(LG_PERS_LINK_SOURCE) . ' : ' .
            Affiche_Icone_Lien('href="'. $root .'/edition_lier_source.php?refObjet=' . $Personne . '&amp;typeObjet=P&amp;refSrc=-1"', 'ajout', LG_PERS_LINK_SOURCE) . "\n";
    }

    echo '</div>' . "\n";

    if ($Modif) {
        // Données filiation et unions
        echo '<div id="pnlParentsUnions">' . "\n";
        echo '<br />' . "\n";
        echo '<fieldset>' . "\n";
        aff_legend(LG_PERS_PARENTS);
        // Affichage de la filiation
        if (Get_Parents($Personne, $Pere, $Mere, $Rang)) {
            if (($Pere != 0) or ($Mere != 0)) {
                $Existe_Filiation = true;
                switch ($SexePers) {
                    case 'm':
                        echo ucfirst(LG_SON);
                        break;
                    case 'f':
                        echo ucfirst(LG_DAUGHTER);
                        break;
                    default:
                        echo ucfirst(LG_CHILD);
                        break;
                }
            }
            $LG_of_h = my_html($LG_of);
            if ($Pere != 0) {
                if (Get_Nom_Prenoms($Pere, $Nom, $Prenoms)) {
                    echo ' ' . $LG_of_h . ' <a href="'. $root .'/edition_personne.php?Refer=' . $Pere . '">' . $Prenoms . '&nbsp;' . $Nom . '</a>' . "\n";
                }
            }
            if ($Mere != 0) {
                if ($Pere != 0) echo ' ' . my_html($LG_andof);
                if (Get_Nom_Prenoms($Mere, $Nom, $Prenoms)) {
                    echo ' ' . $LG_of_h . ' <a href="'. $root .'/edition_personne.php?Refer=' . $Mere . '">' . $Prenoms . '&nbsp;' . $Nom . '</a>' . "\n";
                }
            }
            echo '&nbsp;(' . LG_PERS_RANK . ' :&nbsp;' . $Rang . '&nbsp;)';
        }

        if (!$Existe_Filiation) {
            $icone = 'ajout';
            $lib = LG_PERS_CREATE_PARENTS;
            echo my_html($lib) . '&nbsp;:&nbsp;';
            echo Affiche_Icone_Lien('href="'. $root .'/edition_filiation.php?Refer=' . $Refer . '"', $icone, $lib);
        } else {
            $icone = 'fiche_edition';
            $lib = LG_PERS_UPDATE_PARENTS;
            echo '&nbsp;' . Affiche_Icone_Lien('href="'. $root .'/edition_filiation.php?Refer=' . $Refer . '"', $icone, $lib);
        }

        echo '</fieldset>' . "\n";
        echo '<br />' . "\n";

        // Affichage des unions
        echo '<fieldset>' . "\n";
        aff_legend(LG_PERS_UNIONS);
        if (($SexePers == 'm') or ($SexePers == 'f')) {
            $sqlU = 'select Conjoint_1, Conjoint_2, Reference from ' . nom_table('unions') .
                ' where Conjoint_1=' . $Refer . ' or Conjoint_2=' . $Refer . ' order by Maries_Le';
            $resU = lect_sql($sqlU);
            while ($rowU = $resU->fetch(PDO::FETCH_NUM)) {
                $Existe_Union = true;
                if ($Refer == $rowU[0]) $Conj = $rowU[1];
                else $Conj = $rowU[0];
                $Ref_U = $rowU[2];
                $sqlC    = 'select Reference, Nom, Prenoms, Sexe from ' . nom_table('personnes') . ' where reference = ' . $Conj . ' limit 1';
                $resC    = lect_sql($sqlC);
                $enregC  = $resC->fetch(PDO::FETCH_ASSOC);
                $enreg2C = $enregC;
                Champ_car($enreg2C, 'Nom');
                Champ_car($enreg2C, 'Prenoms');
                $lib = LG_PERS_UPDATE_UNION;
                echo my_html($LG_with) . ' <a href="'. $root .'/edition_personne.php?Refer=' . $enreg2C['Reference'] . '">' . $enreg2C['Prenoms'] . '&nbsp;' . $enreg2C['Nom'] . "</a>\n";
                if ($enreg2C['Sexe'] == $SexePers)
                    $us = 'o';
                else
                    $us = 'n';
                //echo 'unisexe : '.$us;
                echo '&nbsp;' . Affiche_Icone_Lien(Ins_Edt_Union($Ref_U, $Refer, $us), 'fiche_edition', $lib) . '<br />' . "\n";
                $resC->closeCursor();
            }
            $resU->closeCursor();
            // Lien pour ajout des unions
            if ($SexePers != '') {
                lien_aj_union('o');
                lien_aj_union('n');
            }
        }
        echo '</fieldset>' . "\n";

        echo '<br />' . Affiche_Icone('tip', $LG_tip)
            . ' ' . LG_PERS_TIP_QUICK1 . ' ' . Affiche_Icone('ajout_rapide', 'Ajout rapide') . ' ' . LG_PERS_TIP_QUICK2 . "\n";

        echo '</div>' . "\n";

        // La personne existe-t-elle en tant que parent ?
        // Vu que c'est pour autoriser la suppression, on ne fait la recherche que si pas de filiation et pas d'union
        if ((!$Existe_Filiation) and (!$Existe_Union)) {
            switch ($SexePers) {
                case 'm':
                    $cond = 'Pere=' . $Personne;
                    break;
                case 'f':
                    $cond = 'Mere=' . $Personne;
                    break;
                default:
                    $cond = 'Pere=' . $Personne . ' or Mere=' . $Personne;
                    break;
            }
            $Enfant = 0;
            $sql_EE = 'select Enfant from ' . nom_table('filiations') . ' where ' . $cond . ' limit 1';
            if ($res_EE = lect_sql($sql_EE)) {
                if ($enf_lu = $res_EE->fetch(PDO::FETCH_NUM)) {
                    $Enfant = $enf_lu[0];
                }
                $res_EE->closeCursor();
            }
            if ($Enfant != 0) $Existe_Enfants = true;
        }

        // Données des évènements
        echo '<div id="pnlEvts">' . "\n";
        $x = Aff_Evenements_Pers($Personne, 'O');

        // Ajout rapide d'évènements de type multiple
        Aff_Ajout_Rapide_Evt('P');

        echo '</div>' . "\n";

        // Données des liens avec d'autres personnes
        echo '<div id="pnlLiens">' . "\n";
        $x = Aff_Liens_Pers($Personne, 'O');
        echo '</div>' . "\n";

        // Noms secondaires de la personne
        echo '<div id="pnlNoms">' . "\n";
        // Récupération des noms secondaires (princ = 'N') pour la personne
        $req_ns = 'SELECT b.nomFamille, a.comment, a.idNom FROM ' . nom_table('noms_personnes') . ' a, ' . nom_table('noms_famille') . ' b' .
            ' where b.idNomFam = idNom' .
            ' and a.idPers = ' . $Personne .
            ' and a.princ = \'N\'' .
            ' order by b.nomFamille';
        $res_ns = lect_sql($req_ns);
        // Affichage des noms secondaires disponibles pour la personne
        if ($res_ns->rowCount()) {
            echo '<table width="85%" border="0">' . "\n";
            echo '<tr>';
            echo '<td>Nom</td>';
            echo '<td>' . my_html(ucfirst(LG_CH_COMMENT)) . '</td>';
            echo '<td>&nbsp;</th>';
            echo '</tr>' . "\n";
            while ($enr_ns = $res_ns->fetch(PDO::FETCH_NUM)) {
                echo '<tr><td>' . my_html($enr_ns[0]) . '</td><td>' . my_html($enr_ns[1]) . '</td>' . "\n";
                echo '<td>' .
                    Affiche_Icone_Lien('href="'. $root .'/edition_lier_nom.php?refPers=' . $Personne . '&amp;refNom=' . $enr_ns[2] . '"', 'fiche_edition', 'Modification d\'un nom secondaire') .
                    '</td></tr>' . "\n";
            }
            echo '</table>' . "\n";
        }
        // Possibilité de lier un nom secondaire pour la personne
        echo '<br />&nbsp;' . my_html(LG_PERS_ALT_NAME_ADD) . LG_SEMIC .
            Affiche_Icone_Lien('href="'. $root .'/edition_lier_nom.php?refPers=' . $Personne . '&amp;refNom=-1"', 'ajout', LG_PERS_ALT_NAME_ADD) . "\n";
        echo '</div>' . "\n";

        //	Documents liés à la personne
        echo '<div id="pnlDocs">' . "\n";
        //
        Aff_Documents_Objet($Personne, 'P', 'N');
        // Possibilité de lier un document pour la personne
        echo '<br />&nbsp;' . my_html(LG_PERS_DOC_LINK_EXISTS) . LG_SEMIC .
            Affiche_Icone_Lien('href="'. $root .'/edition_lier_doc.php?refObjet=' . $Personne . '&amp;typeObjet=P&amp;refDoc=-1"', 'ajout', LG_PERS_DOC_LINK) . "\n";
        echo '<br />&nbsp;' . my_html(LG_PERS_DOC_LINK_NEW) . LG_SEMIC .
            Affiche_Icone_Lien('href="'. $root .'/edition_document.php?Reference=-1&amp;refObjet=' . $Personne .
                '&amp;typeObjet=P"', 'ajout', LG_PERS_DOC_LINK) . "\n";
        echo '</div>' . "\n";
    }
    echo '</div> <!-- panes -->' . "\n";
}

// Demande de suppression
if ($bt_Sup) {
    // Suppression dans les arbres
    $req = 'delete from ' . nom_table('arbrepers') . ' where reference = ' . $Refer;
    $res = maj_sql($req);
    // Suppression des phptos de la personne dans les arbres
    $req = 'delete from ' . nom_table('arbrephotos') . ' where reference = ' . $Refer;
    $res = maj_sql($req);
    $req = 'delete from ' . nom_table('arbreunion') . ' where refParent1 = ' . $Refer . ' or refParent2 = ' . $Refer;
    $res = maj_sql($req);
    // Suppression des commentaires
    if ($ADiversP != '') {
        $req = 'delete from ' . nom_table('commentaires') . ' where Reference_Objet = ' . $Refer . ' and Type_Objet = \'P\'';
        $res = maj_sql($req);
    }
    // Suppression des liens vers les documents
    $req = 'delete from ' . nom_table('concerne_doc') . ' where Reference_Objet = ' . $Refer . ' and Type_Objet = \'P\'';
    $res = maj_sql($req);
    // Suppression des liens vers les évènements
    $req = 'delete from ' . nom_table('concerne_objet') . ' where Reference_Objet = ' . $Refer . ' and Type_Objet = \'P\'';
    $res = maj_sql($req);
    // Suppression des liens vers les images
    $req = 'delete from ' . nom_table('images') . ' where Reference = ' . $Refer . ' and Type_Ref = \'P\'';
    $res = maj_sql($req);
    // Suppression des liens vers les noms
    $req = 'delete from ' . nom_table('noms_personnes') . ' where idPers = ' . $Refer;
    $res = maj_sql($req);
    // Suppression des participations à des évènements
    $req = 'delete from ' . nom_table('participe') . ' where Personne = ' . $Refer;
    $res = maj_sql($req);
    // Suppression des liens avec d'autres personnes
    $req = 'delete from ' . nom_table('relation_personnes') . ' where  Personne_1 = ' . $Refer . ' or Personne_2 = ' . $Refer;
    $res = maj_sql($req);
    // Suppression de la personne
    $req = 'delete from ' . nom_table('personnes') . ' where Reference = ' . $Refer;
    $res = maj_sql($req);

    // Suppresion de la personne de la liste des personnes mémorisées
    $indice = array_search($Refer, $_SESSION['mem_pers']);
    for ($nb = $indice; $nb < $postes_memo_pers; $nb++) {
        $_SESSION['mem_pers'][$nb]    = $_SESSION['mem_pers'][$nb + 1];
        $_SESSION['mem_nom'][$nb]     = $_SESSION['mem_nom'][$nb + 1];
        $_SESSION['mem_prenoms'][$nb] = $_SESSION['mem_prenoms'][$nb + 1];
    }
    // On initialise les infos sur le poste max
    $_SESSION['mem_pers'][$postes_memo_pers - 1]    = 0;
    $_SESSION['mem_nom'][$postes_memo_pers - 1]     = '-';
    $_SESSION['mem_prenoms'][$postes_memo_pers - 1] = '-';

    maj_date_site();
    Retour_Ar();
}

//Demande de mise à jour
if ($bt_OK) {
    $maj_site = false;

    // Sécurisation des variables postées - phase 2
    $NomP                    = Secur_Variable_Post($NomP, 60, 'S');            // Plus long que la longueur du champ car il y a le numéro en entête à cet instant
    $ANomP                    = Secur_Variable_Post($ANomP, 60, 'S');
    $AidNomP                = Secur_Variable_Post($AidNomP, 1, 'N');
    $PrenomsP                = Secur_Variable_Post($PrenomsP, 50, 'S');
    $APrenomsP                = Secur_Variable_Post($APrenomsP, 50, 'S');
    $SurnomP                = Secur_Variable_Post($SurnomP, 50, 'S');
    $SurnomsP                = Secur_Variable_Post($ASurnomP, 50, 'S');
    $SexeP                    = Secur_Variable_Post($SexeP, 1, 'S');
    $ASexeP                    = Secur_Variable_Post($ASexeP, 1, 'S');
    $NumeroP                = Secur_Variable_Post($NumeroP, 20, 'S');
    $ANumeroP                = Secur_Variable_Post($ANumeroP, 20, 'S');
    $CNe_leP                = Secur_Variable_Post($CNe_leP, 10, 'S');
    $ANe_leP                = Secur_Variable_Post($ANe_leP, 10, 'S');
    $Ville_NaissanceP        = Secur_Variable_Post($Ville_NaissanceP, 80, 'S');
    $AVille_NaissanceP        = Secur_Variable_Post($AVille_NaissanceP, 1, 'N');
    $CDecede_LeP            = Secur_Variable_Post($CDecede_LeP, 10, 'S');
    $ADecede_LeP            = Secur_Variable_Post($ADecede_LeP, 10, 'S');
    $Ville_DecesP            = Secur_Variable_Post($Ville_DecesP, 80, 'S');
    $AVille_DecesP            = Secur_Variable_Post($AVille_DecesP, 1, 'N');
    $DiversP                = Secur_Variable_Post($DiversP, 65535, 'S');
    $ADiversP                = Secur_Variable_Post($ADiversP, 65535, 'S');
    $Diff_Internet_NoteP  = Secur_Variable_Post($Diff_Internet_NoteP, 1, 'S');
    $ADiff_Internet_NoteP = Secur_Variable_Post($ADiff_Internet_NoteP, 1, 'S');
    $Diff_InternetP            = Secur_Variable_Post($Diff_InternetP, 1, 'S');
    $ADiff_InternetP        = Secur_Variable_Post($ADiff_InternetP, 1, 'S');
    $Statut_Fiche            = Secur_Variable_Post($Statut_Fiche, 1, 'S');
    $AStatut_Fiche            = Secur_Variable_Post($AStatut_Fiche, 1, 'S');
    $Categorie                = Secur_Variable_Post($Categorie, 1, 'N');
    $ACategorie                = Secur_Variable_Post($ACategorie, 1, 'N');

    // Détection d'un ajout de ville
    $Ville_NaissanceP = Ajoute_Ville($Ville_NaissanceP);
    $Ville_DecesP = Ajoute_Ville($Ville_DecesP);

    // Init des zones de requête
    $Type_Ref = 'P';
    $req = '';
    $req_comment = '';
    if ($Statut_Fiche == '') $Statut_Fiche = 'N';
    if ($Diff_InternetP == '') $Diff_InternetP = 'N';

    // On commence par enlever les numéros en entête des noms
    $idNomP = 0;
    $posi = strpos($NomP, '/');
    if ($posi > 0) {
        $idNomP = strval(substr($NomP, 0, $posi));
        $NomP = substr($NomP, $posi + 1);
    }
    $posi = strpos($ANomP, '/');
    if ($posi > 0) $ANomP = substr($ANomP, $posi + 1);

    // Création du nom de famille ?
    $idNomP = Ajoute_Nom($idNomP, $NomP);
    if ($idNomP == -1) $NomP = '';

    // Modification du caractère de cujus d'une personne
    // On supprime les autres de cujus
    if (($NumeroP == '1') and ($ANumeroP != '1')) {
        $req_dec = 'update ' . nom_table('personnes') . ' set Numero = "" where Numero = "1"';
        $res = maj_sql($req_dec);
        $_SESSION['decujus'] = $Refer;
        $_SESSION['decujus_defaut'] = 'O';
    }

    // Cas de la modification
    if ($Modif) {
        if (($SexeP == '') and ($ASexeP != ''))
            $SexeP = $ASexeP;
        Aj_Zone_Req('Nom', $NomP, $ANomP, 'A', $req);
        Aj_Zone_Req('Prenoms', $PrenomsP, $APrenomsP, 'A', $req);
        Aj_Zone_Req('Sexe', $SexeP, $ASexeP, 'A', $req);
        Aj_Zone_Req('Numero', $NumeroP, $ANumeroP, 'A', $req);
        Aj_Zone_Req('Ne_le', $CNe_leP, $ANe_leP, 'A', $req);
        Aj_Zone_Req('Ville_Naissance', $Ville_NaissanceP, $AVille_NaissanceP, 'N', $req);
        Aj_Zone_Req('Decede_Le', $CDecede_LeP, $ADecede_LeP, 'A', $req);
        Aj_Zone_Req('Ville_Deces', $Ville_DecesP, $AVille_DecesP, 'N', $req);
        Aj_Zone_Req('Diff_Internet', $Diff_InternetP, $ADiff_InternetP, 'A', $req);
        Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $req);
        Aj_Zone_Req('idNomFam', $idNomP, $AidNomP, 'N', $req);
        Aj_Zone_Req('Categorie', $Categorie, $ACategorie, 'N', $req);
        Aj_Zone_Req('Surnom', $SurnomP, $ASurnomP, 'A', $req);
        // Traitement des commentaires
        maj_commentaire($Refer, $Type_Ref, $DiversP, $ADiversP, $Diff_Internet_NoteP, $ADiff_Internet_NoteP);

        // Mémorisation de la personne
        $_SESSION['mem_nom'][0]     = $NomP;
        $_SESSION['mem_prenoms'][0] = $PrenomsP;

        // Mémorisation pour utilisation ultérieure
        $_SESSION['Nom_Saisi']    = $idNomP . '/' . $NomP;
        $_SESSION['VilleN_Saisie'] = $Ville_NaissanceP;
        $_SESSION['VilleD_Saisie'] = $Ville_DecesP;
    }
    // Cas de la création
    else {
        // On n'autorise la création que si le nom ou le prénom est saisi
        $Creation = 0;
        if (($NomP != '') and ($PrenomsP != '')) {
            $Creation = 1;
        }
        if ($Creation == 1) {
            // Mémorisation de la personne
            $_SESSION['mem_nom'][0]     = $NomP;
            $_SESSION['mem_prenoms'][0] = $PrenomsP;

            $_SESSION['Nom_Saisi']    = $idNomP . '/' . $NomP;
            $_SESSION['VilleN_Saisie'] = $Ville_NaissanceP;
            $_SESSION['VilleD_Saisie'] = $Ville_DecesP;
            Ins_Zone_Req($NomP, 'A', $req);
            Ins_Zone_Req($PrenomsP, 'A', $req);
            Ins_Zone_Req($SexeP, 'A', $req);
            Ins_Zone_Req($NumeroP, 'A', $req);
            Ins_Zone_Req($CNe_leP, 'A', $req);
            Ins_Zone_Req($CDecede_LeP, 'A', $req);
            Ins_Zone_Req($Ville_NaissanceP, 'N', $req);
            Ins_Zone_Req($Ville_DecesP, 'N', $req);
            Ins_Zone_Req($Diff_InternetP, 'A', $req);
            // Récupération de l'identifiant à positionner
            $nouv_ident = Nouvel_Identifiant('Reference', 'personnes');

            // Création d'un enregistrement dans la table commentaires
            if ($DiversP != '') {
                $Type_Ref = 'P';
                insere_commentaire($nouv_ident, $Type_Ref, $DiversP, $Diff_Internet_NoteP);
            }
        }
    }

    // Complément de la requête 1
    if ($req != '') {
        $req = $req . ',';
    }
    // Cas de la modification
    if (($Modif) and ($req != '')) {
        $req = 'update ' . nom_table('personnes') . ' set ' . $req .
            'Date_Modification = current_timestamp' .
            ' where Reference  = ' . $Refer;
        $res = maj_sql($req);
        // Mise à jour des liens personnes / noms sur changement de nom
        if ($idNomP != $AidNomP) {
            $req = 'update ' . nom_table('noms_personnes') . ' set idNom = ' . $idNomP .
                ' where idPers = ' . $Refer . ' and  princ = \'O\'';
            $res = maj_sql($req);
        }
        $maj_site = true;
    }
    // Cas de la création
    if ((!$Modif) and ($Creation == 1)) {
        $req = 'insert into ' . nom_table('personnes') . ' values(' . $nouv_ident . ',' . $req . 'current_timestamp,current_timestamp';
        Ins_Zone_Req($Statut_Fiche, 'A', $req) .
            Ins_Zone_Req($idNomP, 'N', $req);
        Ins_Zone_Req($Categorie, 'N', $req);
        Ins_Zone_Req($SurnomP, 'A', $req);
        $req = $req . ')';
        $res = maj_sql($req);
        // Création du lien personnes / noms
        $req = 'insert into ' . nom_table('noms_personnes') . '  values(' . $nouv_ident . ',' . $idNomP . ',\'O\',null)';
        $res = maj_sql($req);
        $maj_site = true;
    }

    // Exécution de la requête sur les commentaires
    if ($req_comment != '') {
        $res = maj_sql($req_comment);
        $maj_site = true;
    }

    // Détermination du nombre de lignes d'évènements;
    // on se base sur le nombre de variables Titre_xx
    $nb_l_events = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'Titre_') !== false) $nb_l_events++;
    }

    // Traitement de l'ajout rapide d'évènements à partir du formulaire dynamique
    if ($nb_l_events > 0) {

        $deb_req_evt = 'insert into ' . nom_table('evenements') .
            ' (Identifiant_zone,Identifiant_Niveau,Code_Type,Titre,Date_Creation,Date_Modification,Statut_Fiche) ' .
            ' values ' .
            ' (0,0,\'';
        $deb_req_part = 'insert into ' . nom_table('participe') .
            ' (Evenement,Personne,Code_Role,Pers_Principal,Identifiant_zone,Identifiant_Niveau) ' .
            ' values ' .
            ' (';

        for ($num_ligne = 1; $num_ligne <= $nb_l_events; $num_ligne++) {
            $LeType = retourne_var_post('Type_', $num_ligne);
            $LeTitre = retourne_var_post('Titre_', $num_ligne);

            if ($LeTitre != '') {
                $req = $deb_req_evt . $LeType . '\',\'' . $LeTitre . '\',current_timestamp,current_timestamp,\'N\')';
                $res = maj_sql($req);
                $req = $deb_req_part . $connexion->lastInsertId() . ',' . $Refer . ',\' \',\'N\',0,0)';

                $res = maj_sql($req);
                $maj_site = true;
            }
        }
    }

    if ($maj_site) maj_date_site(true);

    // Retour vers la page précédente
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {

    // Récupération de la liste des types
    Recup_Types_Evt('P');

    include(__DIR__ . '/assets/js/Edition_Personne.js');
    include(__DIR__ . '/assets/js/Ajout_Evenement.js');
    include(__DIR__ . '/assets/js/Insert_Tiny.js');

    // Récupération des données de la personne
    if ($Modif) {
        $sql = 'select * from ' . nom_table('personnes') . ' where Reference = ' . $Refer . ' limit 1';
        $res = lect_sql($sql);
        if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
            $enreg2 = $enreg;
            if (is_null($enreg2['Surnom']))
                $enreg2['Surnom'] = '';
            Champ_car($enreg2, 'Nom');
            Champ_car($enreg2, 'Prenoms');
            Champ_car($enreg2, 'Surnom');
            $Sexe = $enreg2['Sexe'];
            // Mémorisation de la personne consultée
            memo_pers($Refer, $enreg['Nom'], $enreg['Prenoms']);
        }
        // Si la personne a été supprimée entre-temps
        else {
            header('Location: index.php');
            // Retour_Ar();
            // die();
        }
    } else {
        $enreg2['Reference'] = 0;
        $enreg2['Nom'] = '';
        $enreg2['Prenoms'] = '';
        $enreg2['Sexe'] = '';
        $enreg2['Numero'] = '';
        $enreg2['Ne_le'] = '';
        $enreg2['Decede_Le'] = '';
        $enreg2['Ville_Naissance'] = 0;
        $enreg2['Ville_Deces'] = 0;
        $enreg2['Diff_Internet'] = '';
        $enreg2['Date_Creation'] = '';
        $enreg2['Date_Modification'] = '';
        $enreg2['Statut_Fiche'] = 'N';
        $enreg2['idNomFam'] = 0;
        $enreg2['Categorie'] = 0;
        $enreg2['Surnom'] = '';
    }
    $EnrPers = $enreg2;
    $compl = Ajoute_Page_Info(650, 250);
    if ($Modif) {
        $compl .=
            Affiche_Icone_Lien(Ins_Ref_ImagesP($Refer), 'images', 'Images') . '&nbsp;' .
            Affiche_Icone_Lien(Ins_Ref_Arbre($Refer), 'arbre_asc', $LG_assc_tree) . '&nbsp;' .
            Affiche_Icone_Lien(Ins_Ref_Arbre_Desc($Refer), 'arbre_desc', $LG_desc_tree) . '&nbsp;' .
            Affiche_Icone_Lien('href="'. $root .'/ajout_rapide.php?Refer=' . $Refer . '"', 'ajout_rapide', $LG_quick_adding) . '&nbsp;' .
            Affiche_Icone_Lien('href="'. $root .'/verif_personne.php?Refer=' . $Refer . '"', 'fiche_controle', $LG_LPers_Check_Pers) . '&nbsp;' .
            Affiche_Icone_Lien(Ins_Ref_Pers($Refer), 'fiche_fam', 'Fiche familiale') . "\n";
    }

    Insere_Haut($titre, $compl, 'Edition_Personne', $Refer);

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'NomP,PrenomsP\')" action="' . my_self() . '?Refer=' . $Refer . '" >' . "\n";
    echo '<input type="hidden" name="Refer" value="' . $Refer . '"/>' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . $Horigine . '"/>' . "\n";

    if (isset($_SESSION['Nom_Saisi'])) {
        echo '<input type="hidden" name="Nom_Prec" value="' . $_SESSION['Nom_Saisi'] . '"/>' . "\n";
        echo '<input type="hidden" name="VilleN_Prec" value="' . $_SESSION['VilleN_Saisie'] . '"/>' . "\n";
        echo '<input type="hidden" name="VilleD_Prec" value="' . $_SESSION['VilleD_Saisie'] . '"/>' . "\n";
    }

    // Affichage des données de la personne et affichage des parents
    rectif_null_pers($enreg2);
    $x = Aff_PersonneI($enreg2, $Refer, false);

    $lib_sup = '';
    /* Bouton Supprimer ?
	   On ne peut supprimer une personne que si :
	   - on est en modification
	   - elle n'est pas dans une union
	   - elle n'a pas de filiation
	   - elle n'est pas dans une filiation en tant que parent
	*/

    if ($Modif) {
        if ((!$Existe_Filiation) and (!$Existe_Union) and (!$Existe_Enfants)) $lib_sup = $lib_Supprimer;
    }

    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_PERS_THIS, false);

    echo '</div>' . "\n";  //  <!-- tab container -->
    echo '</td></tr></table></div>' . "\n";

    echo '</form>';

    Insere_Bas($compl);
} else {
    echo "<body bgcolor=\"#FFFFFF\">";
}

include(__DIR__ . '/assets/js/gest_onglets.js');
?>

<!-- On cache les div d'ajout des villes et du nom et on positionne l'onglet par défaut -->
<script type="text/javascript">
    <?php
    if ($Existe_Union) echo '
	var e = document.getElementById("SexeP_f");
	e.setAttribute("disabled","disabled");
	e.setAttribute("title","Non modifiable, présence d\'une union");
	e = document.getElementById("SexeP_m");
	e.setAttribute("disabled","disabled");
	e.setAttribute("title","Non modifiable, présence d\'une union");';
    ?>
    cache_div("id_div_ajout1");
    cache_div("id_div_ajout2");
    cache_div("id_div_ajout_nom");
    setupPanes("container1", "tab1", 50);
</script>

</body>

</html>