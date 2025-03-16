<?php
//=====================================================================
// Edition d'une personne
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

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

$acces = 'M';    
$titre = $LG_Menu_Title['Person_Add'];                                           // Type d'accès de la page : (M)ise à jour, (L)ecture
if ($Modif) $titre = $LG_Menu_Title['Person_Modify'];    // Titre pour META

$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Existe_Union = false;

function lien_aj_union($unisexe)
{
    global $root, $SexePers, $Refer;
    $lib = 'Ajouter une union';
    if ($unisexe == 'o') $lib .= ' unisexe';
    $lien = 'href="' . $root . '/edition_union.php?Conjoint=' . $Refer . '&amp;Ref_Union=-1';
    if ($unisexe == 'o') {
        $us = 'o';
        echo LG_PERS_UNION_UNISEX;
    } else {
        $us = 'n';
        echo LG_PERS_UNION_MULTISEX;
    }
    //echo ' '.Affiche_Icone_Lien($lien.'"','ajout',$lib).'<br />';
    echo ' ' . Affiche_Icone_Lien('href="' . $root . '/edition_union.php?Reference=-1&amp;Personne=0&amp;us=n"', 'ajout', $lib) . '<br />';
}

// Affiche une personne et ses parents
function Aff_PersonneI($enreg2, $Personne, $Decalage)
{
    global $root, $Refer, $Pere_GP, $Mere_GP, $Rang_GP, $Comportement, $Icones, $Images, $chemin_images_util, $Commentaire, $Diffusion_Commentaire_Internet, $Modif, $Existe_Filiation, $Existe_Union, $Existe_Enfants, $SexePers, $SiteGratuit, $Premium, $LG_Data_tab, $LG_File, $lib_OK, $lib_Annuler, $LG_at, $LG_with, $LG_tip, $LG_of, $LG_andof, $death_def_min_year, $url_matchid, $url_matchid_sch, $LG_Menu_Title;

    $Existe_Filiation = false;
    $Existe_Enfants = false;

    echo '<div id="content">';
    echo '<table id="cols" cellpadding="0" cellspacing="0" >';
    echo '<tr>';
    echo '<td style="border-right:0px solid #9cb0bb">';
    echo '  <img src="' . $root . '/assets/img/' . $Images['clear'] . '" width="850" height="1" alt="clear"/>';
    echo '</td></tr>';

    echo '<tr>';
    echo '<td class="left">';
    echo '<div class="tab-container" id="container1">';
    // Onglets
    echo '<ul class="tabs">';
    echo '<li><a href="#" onclick="return showPane(\'pnlData\', this)" id="tab1">' . $LG_Data_tab . '</a></li>';
    // Certains onglets ne sont disponibles qu'en modification
    if ($Refer != -1) {
        echo '<li><a href="#" onclick="return showPane(\'pnlParentsUnions\', this)">' . LG_PERS_PARENTS_UNIONS . '</a></li>';
        echo '<li><a href="#" onclick="return showPane(\'pnlEvts\', this)">' . LG_PERS_EVENTS . '</a></li>';
        echo '<li><a href="#" onclick="return showPane(\'pnlLiens\', this)">' . LG_PERS_LINKS . '</a></li>';
        echo '<li><a href="#" onclick="return showPane(\'pnlNoms\', this)">' . LG_PERS_ALT_NAMES . '</a></li>';
        echo '<li><a href="#" onclick="return showPane(\'pnlDocs\', this)">' . LG_PERS_DOCS . '</a></li>';
    }
    echo '<li><a href="#" onclick="return showPane(\'pnlFile\', this)">' . $LG_File . '</a></li>';
    echo '</ul>';

    echo '<div class="tab-panes">';
    // Onglets données générales de la personne
    echo '<div id="pnlData">';
    // Etat civil de la personne
    echo '<fieldset>';
    echo '<legend>' . ucfirst(LG_PERS_DATA) . '</legend>';
    echo '<table width="98%">';
    // Nom
    if ($enreg2['idNomFam'] == '') $enreg2['idNomFam'] = -1;
    echo '<tr><td width="16%">' . LG_PERS_NAME . '</td>';
    echo '<td><input type="hidden" name="NomP" id="NomP" value="' . $enreg2['idNomFam'] . '/' . $enreg2['Nom'] . '" class="oblig"/>';
    echo '<input type="hidden" name="AidNomP" value="' . $enreg2['idNomFam'] . '"/>';
    Select_Noms($enreg2['idNomFam'], 'NomSel', 'NomP');
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="hidden" name="ANomP" value="' . $enreg2['idNomFam'] . '/' . $enreg2['Nom'] . '"/>';
    // Possibilité d'ajouter un nom
    echo '<img id="ajout_nom" src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="' . LG_PERS_ADD_NAME . '" title="' . LG_PERS_ADD_NAME . '"' .
        'onclick="inverse_div(\'id_div_ajout_nom\');document.getElementById(\'nouveau_nom\').focus();"/>';
    if (isset($_SESSION['Nom_Saisi'])) {
        echo ' <img id="Ireprend_nom" src="' . $root . '/assets/img/' . $Icones['copier'] . '" alt="' . LG_PERS_SAME_NAME . '" title="' . LG_PERS_SAME_NAME . '"' .
            ' onclick="reprend_nom();"/>';
    }
    echo '<div id="id_div_ajout_nom">';
    echo LG_ADD_NAME . ' <input type="text" size="50" name="nouveau_nom" id="nouveau_nom"/>';
    echo ' <img id="majuscule" src="' . $root . '/assets/img/' . $Icones['majuscule'] . '" alt="' . LG_NAME_TO_UPCASE . '" title="' . LG_NAME_TO_UPCASE . '"' .
        ' onclick="NomMaj();document.getElementById(\'NomP\').focus();"/>';
    echo '<input type="button" name="ferme_OK_nom" value="' . my_html($lib_OK) . '" onclick="ajoute_nom()"/>';
    echo '<input type="button" name="ferme_An_nom" value="' . my_html($lib_Annuler). '" onclick="inverse_div(\'id_div_ajout_nom\')"/>';
    echo '</div>';
    echo '</td>';
    // Affichage de l'image par défaut pour la personne
    echo '<td rowspan="3" align="center" valign="middle">';
    // Recherche de la présence d'une image par défaut
    $image = Rech_Image_Defaut($Refer, 'P');
    if ($image != '') {
        $image = $chemin_images_util . $image;
        Aff_Img_Redim_Lien($image, 110, 110, "id_" . $Refer);
    } else echo ' ';
    echo '</td></tr>';

    // Prénoms
    echo '<tr><td width="16%">' . LG_PERS_FIRST_NAME . '</td>';
    echo '<td colspan="2"><input type="text" size="50" name="PrenomsP" id="PrenomsP" value="' . $enreg2['Prenoms'] . '" class="oblig"/> ';
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="hidden" name="APrenomsP" value="' . $enreg2['Prenoms'] . '"/>';
    echo '</td></tr>';

    // Surnom
    echo '<tr><td width="16%">' . LG_PERS_SURNAME . '</td>';
    echo '<td colspan="2"><input type="text" size="50" name="SurnomP" id="SurnomP" value="' . $enreg2['Surnom'] . '"/>';
    echo '<input type="hidden" name="ASurnomP" value="' . $enreg2['Surnom'] . '"/>';
    echo '</td></tr>';

    //Sexe
    $SexePers = $enreg2['Sexe'];
    echo '<tr><td width="16%">' . LG_SEXE . '</td>';
    echo '<td colspan="2"><input type="radio" id="SexeP_m" name="SexeP" value="m"';
    if ($enreg2['Sexe'] == 'm') echo ' checked';
    echo ' /><label for="SexeP_m">' . LG_SEXE_MAN . "</label> ";
    echo '<input type="radio" id="SexeP_f" name="SexeP" value="f"';
    if ($enreg2['Sexe'] == 'f') echo ' checked';
    echo ' /><label for="SexeP_f">' . LG_SEXE_WOMAN . '</label>';
    echo '<input type="hidden" name="ASexeP" value="' . $enreg2['Sexe'] . '"/></td>';
    echo "</tr>";

    //Naissance
    echo '<tr><td width="16%">' . LG_PERS_BORN . '</td>';
    echo '<td colspan="2">';
    zone_date2('ANe_leP', 'Ne_leP', 'CNe_leP', $enreg2['Ne_le']);
    echo ' ' . $LG_at . ' ';
    aff_liste_villes(
        'Ville_NaissanceP',
        1,                  // C'est la première fois que l'on appelle la fonction dans la page
        0,                  // On est susceptible de rappeler la fonction
        $enreg2['Ville_Naissance']
    ); // Clé de sélection de la ligne
    echo '<input type="hidden" name="AVille_NaissanceP" value="' . $enreg2['Ville_Naissance'] . '"/>';
    // Possibilité d'ajouter une ville
    echo '<img id="ajout1" src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="' . LG_ADD_TOWN . '" title="' . LG_ADD_TOWN . '"' .
        ' onclick="inverse_div(\'id_div_ajout1\');document.getElementById(\'nouvelle_ville1\').focus();"/>';
    if (isset($_SESSION['Nom_Saisi'])) {
        echo ' <img id="Ireprend_villeN" src="' . $root . '/assets/img/' . $Icones['copier'] . '" alt="' . LG_PERS_SAME_BIRTH_TOWN . '" title="' . LG_PERS_SAME_BIRTH_TOWN . '"' .
            ' onclick="reprend_villeN();"/>';
    }
    echo '<div id="id_div_ajout1">';
    echo LG_ADD_TOWN_LIST . ' <input type="text" name="nouvelle_ville1" id="nouvelle_ville1" maxlength="80"/>';
    echo '<input type="button" name="ferme_OK" value="' . my_html($lib_OK) . '" onclick="ajoute1();"/>';
    echo '<input type="button" name="ferme_An" value="' . my_html($lib_Annuler) . '" onclick="inverse_div(\'id_div_ajout1\');"/>';
    echo '</div>';
    echo '</td></tr>';

    //Décès
    echo '<tr><td width="16%">' . LG_PERS_DEAD . '</td>';
    echo '<td colspan="2">';
    zone_date2('ADecede_LeP', 'Decede_LeP', 'CDecede_LeP', $enreg2['Decede_Le']);
    echo ' ' . $LG_at . ' ';
    aff_liste_villes('Ville_DecesP', 0, 1, $enreg2['Ville_Deces']); // Clé de sélection de la ligne
    echo '<input type="hidden" name="AVille_DecesP" value="' . $enreg2['Ville_Deces'] . '"/>';
    // Possibilité d'ajouter une ville
    echo '<img id="ajout2" src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="' . LG_ADD_TOWN . '" title="' . LG_ADD_TOWN . '"' .
        'onclick="inverse_div(\'id_div_ajout2\');document.getElementById(\'nouvelle_ville2\').focus();"/>';
    if (isset($_SESSION['Nom_Saisi'])) {
        echo ' <img id="Ireprend_villeD" src="' . $root . '/assets/img/' . $Icones['copier'] . '" alt="' . LG_PERS_SAME_DEATH_TOWN . '" title="' . LG_PERS_SAME_DEATH_TOWN . '"' .
            ' onclick="reprend_villeD();"/>';
    }
    echo '<div id="id_div_ajout2">';
    echo LG_ADD_TOWN_LIST . ' <input type="text" name="nouvelle_ville2" id="nouvelle_ville2" maxlength="80"/>';
    echo '<input type="button" name="ferme_OK" value="' . my_html($lib_OK) . '" onclick="ajoute2()"/>';
    echo '<input type="button" name="ferme_An" value="' . my_html($lib_Annuler) . '" onclick="inverse_div(\'id_div_ajout2\')"/>';
    echo '</div>';

    // Si pas de date de décès et date de naissance compatible, on va afficher un appel à MatchId
    if ($enreg2['Decede_Le'] == '') {
        if ((strlen($enreg2['Ne_le']) == 10) && ($enreg2['Ne_le'][9] == 'L')) {
            $annee = substr($enreg2['Ne_le'], 0, 4);
            if ($annee > $death_def_min_year) {
                // echo '<a href="'.$url_matchid_sch
                // .'?firstName='.UnPrenom($enreg2['Prenoms'])
                // .'&lastName='.$enreg2['Nom']
                // .'&sex='.strtoupper($enreg2['Sexe'])
                // .'&birthDate='.substr($enreg2['Ne_le'],6,2).'%2F'.substr($enreg2['Ne_le'],4,2).'%2F'.substr($enreg2['Ne_le'],0,4).'"'
                // .' target="_blank">Match Id</a> ';
                echo '<a href="' . $root . '/recherche_matchid_unitaire.php?ref=' . $Refer . '" target="_blank">' . $LG_Menu_Title['MatchId_Sch'] . '</a>';
            }
        }
    }

    echo "</td></tr>";
    echo '</table>';
    echo '</fieldset>';

    // Numéro de la personne
    echo '<fieldset>';
    echo '<legend>' . ucfirst(LG_PERS_NUMBER) . '</legend>';
    echo '<table width="95%">';
    echo '<tr><td width="16%">' . LG_PERS_NUMBER . '</td>';
    echo '<td><input type="text" size="20" name="NumeroP" id="NumeroP" value="' . $enreg2['Numero'] . '"/>';
    // Calculette pour étendre le numéro Sosa
    echo '<img id="calc1" src="' . $root . '/assets/img/' . $Icones['calculette'] . '" alt="' . LG_PERS_CALC_SOSA . '" title="' . LG_PERS_CALC_SOSA . '"' .
        ' onclick="etend_num_sosa();document.getElementById(\'NumeroP\').focus();"/>';
    // Bouton pour numéro 1 ==> de cujus
    echo '<img id="im_decujus" src="' . $root . '/assets/img/' . $Icones['decujus'] . '" alt="' . LG_PERS_DECUJUS . '" title="' . LG_PERS_DECUJUS . '"' .
        ' onclick="decujus();document.getElementById(\'NumeroP\').focus();"/>';
    echo '<input type="hidden" name="ANumeroP" value="' . $enreg2['Numero'] . '"/></td>';
    echo "</tr>";
    echo '</table>';
    echo '</fieldset>';

    // Commentaire
    echo '<fieldset>';
    echo '<legend>' . ucfirst(LG_CH_COMMENT) . '</legend>';
    echo '<table width="95%">';
    //Divers
    echo '<tr>';
    echo '<td>';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Refer, 'P');
    echo '<textarea cols="50" rows="4" name="DiversP">' . $Commentaire . '</textarea>';
    echo '<input type="hidden" name="ADiversP" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr><tr>';
    // Diffusion Internet commentaire
    echo '<td><label for="Diff_Internet_NoteP">' . LG_CH_COMMENT_VISIBILITY . '</label>'
        . ' <input type="checkbox" id="Diff_Internet_NoteP" name="Diff_Internet_NoteP" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked';
    echo "/>";
    echo '<input type="hidden" name="ADiff_Internet_NoteP" value="' . $Diffusion_Commentaire_Internet . '"/>';
    echo '</td></tr>';
    echo '</table>';
    echo '</fieldset>';
    echo '</div>';

    // Données de la fiche
    echo '<div id="pnlFile">';
    echo '<fieldset>';
    echo '<legend>' . ucfirst(LG_PERS_VISIBILITY) . '</legend>';
    echo '<table width="95%">';
    // Diffusion internet
    echo '<tr><td width="16%">' . LG_PERS_INTERNET . '</td>';
    echo '<td><input type="checkbox" name="Diff_InternetP" value="O"';
    if (($enreg2["Diff_Internet"] == 'O') or ($Refer == -1)) echo ' checked';
    echo "/>";
    echo '<input type="hidden" name="ADiff_InternetP" value="' . $enreg2["Diff_Internet"] . '"/></td>';
    echo '</tr>';
    echo '</table>';
    echo '</fieldset>';
    // Affiche les données propres à l'enregistrement de la fiche
    Affiche_Fiche($enreg2, 1);

    // Affichage des tags
    echo '<fieldset>';
    echo '<legend>' . ucfirst(LG_PERS_CATEGORY) . '</legend>';
    $categ_Fiche = $enreg2['Categorie'];
    $sql_cat = 'select Identifiant, Image, Titre from ' . nom_table('categories') . ' order by Ordre_Tri';
    $res_cat = lect_sql($sql_cat);
    while ($enr_cat = $res_cat->fetch(PDO::FETCH_NUM)) {
        $num_cat = $enr_cat[0];
        $nom_cat = 'tag_' . $enr_cat[1];
        $titre_cat = $enr_cat[2];
        echo '<input type="hidden" name="ACategorie" value="' . $num_cat . '"/>';
        echo '<input type="radio" id="Categorie_' . $num_cat . '" name="Categorie" value="' . $num_cat . '"';
        if ($categ_Fiche == $num_cat) echo ' checked';
        echo '/><label for="Categorie_' . $num_cat . '">'
            . '<img src="' . $root . '/assets/img/' . $Icones[$nom_cat] . '" alt="' . $titre_cat . '" title="' . $titre_cat . '"/>' . '</label>';
    }
    $nb_tag = 0;
    echo '<input type="radio" id="Categorie_0" name="Categorie" value="0"';
    if ($categ_Fiche == 0) echo ' checked';
    echo '/><label for="Categorie_0">' . LG_PERS_NO_CATEGORY . '</label>';
    echo '</fieldset>';

    if ((!$SiteGratuit) or ($Premium)) {
        //  Sources lies à la personne
        echo '<hr/>';
        $x = Aff_Sources_Objet($Refer, 'P', 'N');
        // Possibilité de lier une source pour la personne
        echo '<br /> ' . my_html(LG_PERS_LINK_SOURCE) . ' : ' .
            Affiche_Icone_Lien('href="' . $root . '/edition_lier_source.php?refObjet=' . $Personne . '&amp;typeObjet=P&amp;refSrc=-1"', 'ajout', LG_PERS_LINK_SOURCE);
    }

    echo '</div>';

    if ($Modif) {
        // Données filiation et unions
        echo '<div id="pnlParentsUnions">';
        echo '<br />';
        echo '<fieldset>';
        echo '<legend>' . ucfirst(LG_PERS_PARENTS) . '</legend>';
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
                    echo ' ' . $LG_of_h . ' <a href="' . $root . '/edition_personne.php?Refer=' . $Pere . '">' . $Prenoms . ' ' . $Nom . '</a>';
                }
            }
            if ($Mere != 0) {
                if ($Pere != 0) echo ' ' . my_html($LG_andof);
                if (Get_Nom_Prenoms($Mere, $Nom, $Prenoms)) {
                    echo ' ' . $LG_of_h . ' <a href="' . $root . '/edition_personne.php?Refer=' . $Mere . '">' . $Prenoms . ' ' . $Nom . '</a>';
                }
            }
            echo ' (' . LG_PERS_RANK . ' : ' . $Rang . ' )';
        }

        if (!$Existe_Filiation) {
            $icone = 'ajout';
            $lib = LG_PERS_CREATE_PARENTS;
            echo my_html($lib) . ' : ';
            echo Affiche_Icone_Lien('href="' . $root . '/edition_filiation.php?Refer=' . $Refer . '"', $icone, $lib);
        } else {
            $icone = 'fiche_edition';
            $lib = LG_PERS_UPDATE_PARENTS;
            echo ' ' . Affiche_Icone_Lien('href="' . $root . '/edition_filiation.php?Refer=' . $Refer . '"', $icone, $lib);
        }

        echo '</fieldset>';
        echo '<br />';

        // Affichage des unions
        echo '<fieldset>';
        echo '<legend>' . ucfirst(LG_PERS_UNIONS) . '</legend>';
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
                echo $LG_with . ' <a href="' . $root . '/edition_personne.php?Refer=' . $enreg2C['Reference'] . '">' . $enreg2C['Prenoms'] . ' ' . $enreg2C['Nom'] . "</a>";
                if ($enreg2C['Sexe'] == $SexePers)
                    $us = 'o';
                else
                    $us = 'n';
                //echo 'unisexe : '.$us;
                echo ' ' . Affiche_Icone_Lien('href="' . $root . '/edition_union.php?Reference=' . $Ref_U . '&amp;Refer=' . $Personne . '&amp;us=' . $us . '"', 'fiche_edition', $lib) . '<br />';
                $resC->closeCursor();
            }
            $resU->closeCursor();
            // Lien pour ajout des unions
            if ($SexePers != '') {
                lien_aj_union('o');
                lien_aj_union('n');
            }
        }
        echo '</fieldset>';

        echo '<br /><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . $LG_tip . '" title="' . $LG_tip . '">'
            . ' ' . LG_PERS_TIP_QUICK1 . ' <img src="' . $root . '/assets/img/' . $Icones['ajout_rapide'] . '" alt="Ajout rapide" title="Ajout rapide"> ' . LG_PERS_TIP_QUICK2;
        echo '</div>';

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
            $sql_EE = 'SELECT Enfant FROM ' . nom_table('filiations') . ' WHERE ' . $cond . ' LIMIT 1';
            if ($res_EE = lect_sql($sql_EE)) {
                if ($enf_lu = $res_EE->fetch(PDO::FETCH_NUM)) {
                    $Enfant = $enf_lu[0];
                }
                $res_EE->closeCursor();
            }
            if ($Enfant != 0) $Existe_Enfants = true;
        }

        // Données des évènements
        echo '<div id="pnlEvts">';
        Aff_Evenements_Pers($Personne, 'O');
        Aff_Ajout_Rapide_Evt('P'); // Ajout rapide d'évènements de type multiple
        echo '</div>';

        // Données des liens avec d'autres personnes
        echo '<div id="pnlLiens">';
        Aff_Liens_Pers($Personne, 'O');
        echo '</div>';

        // Noms secondaires de la personne
        echo '<div id="pnlNoms">';
        // Récupération des noms secondaires (princ = 'N') pour la personne
        $req_ns = 'SELECT b.nomFamille, a.comment, a.idNom FROM ' . nom_table('noms_personnes') . ' a, ' . nom_table('noms_famille') . ' b' .
            ' where b.idNomFam = idNom' .
            ' and a.idPers = ' . $Personne .
            ' and a.princ = \'N\'' .
            ' order by b.nomFamille';
        $res_ns = lect_sql($req_ns);
        // Affichage des noms secondaires disponibles pour la personne
        if ($res_ns->rowCount()) {
            echo '<table width="85%">';
            echo '<tr>';
            echo '<td>Nom</td>';
            echo '<td>' . ucfirst(LG_CH_COMMENT) . '</td>';
            echo '<td> </th>';
            echo '</tr>';
            while ($enr_ns = $res_ns->fetch(PDO::FETCH_NUM)) {
                echo '<tr><td>' . my_html($enr_ns[0]) . '</td><td>' . my_html($enr_ns[1]) . '</td>';
                echo '<td>' .
                    Affiche_Icone_Lien('href="' . $root . '/edition_lier_nom.php?refPers=' . $Personne . '&amp;refNom=' . $enr_ns[2] . '"', 'fiche_edition', 'Modification d\'un nom secondaire') .
                    '</td></tr>';
            }
            echo '</table>';
        }
        // Possibilité de lier un nom secondaire pour la personne
        echo '<br /> ' . LG_PERS_ALT_NAME_ADD . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/edition_lier_nom.php?refPers=' . $Personne . '&amp;refNom=-1"', 'ajout', LG_PERS_ALT_NAME_ADD);
        echo '</div>';

        //	Documents liés à la personne
        echo '<div id="pnlDocs">';
        //
        Aff_Documents_Objet($Personne, 'P', 'N');
        // Possibilité de lier un document pour la personne
        echo '<br /> ' . LG_PERS_DOC_LINK_EXISTS . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/edition_lier_doc.php?refObjet=' . $Personne . '&amp;typeObjet=P&amp;refDoc=-1"', 'ajout', LG_PERS_DOC_LINK);
        echo '<br /> ' . LG_PERS_DOC_LINK_NEW . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/edition_document.php?Reference=-1&amp;refObjet=' . $Personne .
                '&amp;typeObjet=P"', 'ajout', LG_PERS_DOC_LINK);
        echo '</div>';
    }
    echo '</div> <!-- panes -->';
}

// Demande de suppression
if ($bt_Sup) {
    // Suppression dans les arbres
    $req = 'DELETE FROM ' . nom_table('arbrepers') . ' WHERE reference = ' . $Refer;
    $res = maj_sql($req);
    // Suppression des phptos de la personne dans les arbres
    $req = 'DELETE FROM ' . nom_table('arbrephotos') . ' WHERE reference = ' . $Refer;
    $res = maj_sql($req);
    $req = 'DELETE FROM ' . nom_table('arbreunion') . ' WHERE refParent1 = ' . $Refer . ' OR refParent2 = ' . $Refer;
    $res = maj_sql($req);
    // Suppression des commentaires
    if ($ADiversP != '') {
        $req = 'DELETE FROM ' . nom_table('commentaires') . ' WHERE Reference_Objet = ' . $Refer . ' AND Type_Objet = \'P\'';
        $res = maj_sql($req);
    }
    // Suppression des liens vers les documents
    $req = 'DELETE FROM ' . nom_table('concerne_doc') . ' WHERE Reference_Objet = ' . $Refer . ' AND Type_Objet = \'P\'';
    $res = maj_sql($req);
    // Suppression des liens vers les évènements
    $req = 'DELETE FROM ' . nom_table('concerne_objet') . ' WHERE Reference_Objet = ' . $Refer . ' AND Type_Objet = \'P\'';
    $res = maj_sql($req);
    // Suppression des liens vers les images
    $req = 'DELETE FROM ' . nom_table('images') . ' WHERE Reference = ' . $Refer . ' AND Type_Ref = \'P\'';
    $res = maj_sql($req);
    // Suppression des liens vers les noms
    $req = 'DELETE FROM ' . nom_table('noms_personnes') . ' WHERE idPers = ' . $Refer;
    $res = maj_sql($req);
    // Suppression des participations à des évènements
    $req = 'DELETE FROM ' . nom_table('participe') . ' WHERE Personne = ' . $Refer;
    $res = maj_sql($req);
    // Suppression des liens avec d'autres personnes
    $req = 'DELETE FROM ' . nom_table('relation_personnes') . ' WHERE Personne_1 = ' . $Refer . ' OR Personne_2 = ' . $Refer;
    $res = maj_sql($req);
    // Suppression de la personne
    $req = 'DELETE FROM ' . nom_table('personnes') . ' WHERE Reference = ' . $Refer;
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
        $req_dec = 'UPDATE ' . nom_table('personnes') . ' SET Numero = "" WHERE Numero = "1"';
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
    } else { // Cas de la création
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

        $deb_req_evt = 'INSERT INTO ' . nom_table('evenements') .
            ' (Identifiant_zone, Identifiant_Niveau, Code_Type, Titre, Date_Creation, Date_Modification, Statut_Fiche) ' .
            ' VALUES ' .
            ' (0,0,\'';
        $deb_req_part = 'INSERT INTO ' . nom_table('participe') .
            ' (Evenement, Personne, Code_Role, Pers_Principal, Identifiant_zone, Identifiant_Niveau) ' .
            ' VALUES ' .
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
        $sql = 'SELECT * FROM ' . nom_table('personnes') . ' WHERE Reference = ' . $Refer . ' LIMIT 1';
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
            Affiche_Icone_Lien('href="' . $root . '/liste_images.php?Refer=' . $Refer . '&amp;Type_Ref=P"', 'images', 'Images') . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/arbre_asc_pers.php?Refer=' . $Refer . '"', 'arbre_asc', $LG_assc_tree) . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/arbre_desc_pers.php?Refer=' . $Refer . '"', 'arbre_desc', $LG_desc_tree) . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/ajout_rapide.php?Refer=' . $Refer . '"', 'ajout_rapide', $LG_quick_adding) . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/verif_personne.php?Refer=' . $Refer . '"', 'fiche_controle', $LG_LPers_Check_Pers) . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/fiche_fam_pers.php?Refer=' . $Refer . '"', 'fiche_fam', 'Fiche familiale');
    }

    Insere_Haut($titre, $compl, 'Edition_Personne', $Refer);

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'NomP,PrenomsP\')" action="' . my_self() . '?Refer=' . $Refer . '" >';
    echo '<input type="hidden" name="Refer" value="' . $Refer . '"/>';
    echo '<input type="hidden" name="Horigine" value="' . $Horigine . '"/>';

    if (isset($_SESSION['Nom_Saisi'])) {
        echo '<input type="hidden" name="Nom_Prec" value="' . $_SESSION['Nom_Saisi'] . '"/>';
        echo '<input type="hidden" name="VilleN_Prec" value="' . $_SESSION['VilleN_Saisie'] . '"/>';
        echo '<input type="hidden" name="VilleD_Prec" value="' . $_SESSION['VilleD_Saisie'] . '"/>';
    }

    // Affichage des données de la personne et affichage des parents
    rectif_null_pers($enreg2);
    Aff_PersonneI($enreg2, $Refer, false);

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

    echo '</div>';  //  <!-- tab container -->
    echo '</td></tr></table></div>';

    echo '</form>';

    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
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