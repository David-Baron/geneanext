<?php
//=====================================================================
// Edition d'une union
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('C')) {
    header('Location: ' . $root . '/');
    exit();
}

$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'PersonneU',
    'sPers',
    'Conjoint_1U',
    'Conjoint_2U',
    'MConjoint_1',
    'MConjoint_2',
    'AMaries_LeU',
    'CMaries_LeU',
    'Ville_MariageU',
    'AVille_MariageU',
    'ADate_KU',
    'CDate_KU',
    'Notaire_KU',
    'ANotaire_KU',
    'Ville_NotaireU',
    'AVille_NotaireU',
    'DiversU',
    'ADiversU',
    'Diff_Internet_NoteU',
    'ADiff_Internet_NoteU',
    'Statut_Fiche',
    'AStatut_Fiche',
    'Nom_Defaut',
    'Horigine'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Recup des variables passées dans l'URL
$Personne = Recup_Variable('Personne', 'N');
$Ref_Union = Recup_Variable('Reference', 'N');
$Uni_Sexe = Recup_Variable('us', 'C', 'onx'); // Union Unisexe ?
$Uni_Sexe = ($Uni_Sexe == 'o') ? true : false;

$acces = 'M';                          // Type d'accès de la page : (M)ise à jour

$Modif = true;
if ($Ref_Union == -1)
    $Modif = false;

if (!$Modif)
    $titre = LG_UNION_ADD;
else
    $titre = LG_UNION_EDIT;

$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

$PersonneU            = Secur_Variable_Post($PersonneU, 1, 'N');
$sPers                = Secur_Variable_Post($sPers, 1, 'S');
$Conjoint_1U          = Secur_Variable_Post($Conjoint_1U, 1, 'N');
$Conjoint_2U          = Secur_Variable_Post($Conjoint_2U, 1, 'N');
$MConjoint_1          = Secur_Variable_Post($MConjoint_1, 1, 'N');
$MConjoint_2          = Secur_Variable_Post($MConjoint_2, 1, 'N');
$AMaries_LeU          = Secur_Variable_Post($AMaries_LeU, 10, 'S');
$CMaries_LeU          = Secur_Variable_Post($CMaries_LeU, 10, 'S');
$Ville_MariageU       = Secur_Variable_Post($Ville_MariageU, 80, 'S');
$AVille_MariageU      = Secur_Variable_Post($AVille_MariageU, 1, 'N');
$ADate_KU             = Secur_Variable_Post($ADate_KU, 10, 'S');
$CDate_KU             = Secur_Variable_Post($CDate_KU, 10, 'S');
$Notaire_KU           = Secur_Variable_Post($Notaire_KU, 80, 'S');
$ANotaire_KU          = Secur_Variable_Post($ANotaire_KU, 80, 'S');
$Ville_NotaireU       = Secur_Variable_Post($Ville_NotaireU, 80, 'S');
$AVille_NotaireU      = Secur_Variable_Post($AVille_NotaireU, 1, 'N');
$DiversU              = Secur_Variable_Post($DiversU, 65535, 'S');
$ADiversU             = Secur_Variable_Post($ADiversU, 65535, 'S');
$Diff_Internet_NoteU  = Secur_Variable_Post($Diff_Internet_NoteU, 1, 'S');
$ADiff_Internet_NoteU = Secur_Variable_Post($ADiff_Internet_NoteU, 1, 'S');
$Statut_Fiche         = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$AStatut_Fiche        = Secur_Variable_Post($AStatut_Fiche, 1, 'S');
$Nom_Defaut           = Secur_Variable_Post($Nom_Defaut, 50, 'S');

// Nombre maximum d'enfants saisissables en mode rapide
$max_enf_rapides = 3;
if ((!$SiteGratuit) or ($Premium)) $max_enf_rapides *= 2;

// Sur demande de suppression
if ($bt_Sup) {

    $fin_req = ' where Reference_Objet = ' . $Ref_Union . " and Type_Objet = 'U'";
    // Suppression des commentaires
    if ($ADiversU != '') {
        $res = maj_sql('delete from ' . nom_table('commentaires') . $fin_req);
    }
    // Suppression des liens vers les documents
    $res = maj_sql('delete from ' . nom_table('concerne_doc') . $fin_req);
    // Suppression des liens vers les évènements
    $res = maj_sql('delete from ' . nom_table('concerne_objet') . $fin_req);
    // Suppression des liens vers les images
    $req = 'delete from ' . nom_table('images') . ' where Reference = ' . $Ref_Union . " and Type_Ref = 'U'";
    $res = maj_sql($req);

    $req = 'delete from ' . nom_table('unions') . ' where Reference = ' . $Ref_Union;
    $res = maj_sql($req);

    Retour_Ar();
}

function get_crit_sexe($P_Sexe)
{
    global $Uni_Sexe;
    $crit_sexe = '';
    if (!$Uni_Sexe) {
        if ($P_Sexe == 'm') $crit_sexe = 'Sexe = "f"';
        if ($P_Sexe == 'f') $crit_sexe = 'Sexe = "m"';
    } else {
        $crit_sexe = 'Sexe = "' . $P_Sexe . '"';
    }
    //	if ($crit_sexe != '') $crit_sexe .= ' or Sexe is Null';
    return $crit_sexe;
}

// Dernière personne saisie, homme ou femme
function dernier($Sexe)
{
    $sql = 'select max(Reference) from ' . nom_table('personnes') . ' where Sexe="' . $Sexe . '"';
    $resmax = lect_sql($sql);
    $enrmax = $resmax->fetch(PDO::FETCH_NUM);
    $LeMax = $enrmax[0];
    $resmax->closeCursor();
    return $LeMax;
}

// Affiche l'union
function Aff_Union($enreg2, $Ref_Union, $Decalage)
{
    global $root, $Icones, $Images, $hidden, $max_enf_rapides, $Commentaire, $Diffusion_Commentaire_Internet, $lib_OK, $lib_Okay, $lib_Annuler, $lib_Supprimer, $LG_add, $def_enc, $Uni_Sexe, $Conjoint, $Conjoint_1, $Conjoint_2, $Sexe_1, $Sexe_2, $LG_birth, $LG_death
        // Info de la personne dont on vient
        , $Personne, $Nom, $Prenoms, $Ne_Pers, $Dec_Pers, $P_Sexe;

    $lib_OK_h      = my_html($lib_OK);
    $lib_Annuler_h = my_html($lib_Annuler);

    $depuis_pers = ($Personne != 0) ? true : false;

    echo '<div id="content">' . "\n";
    echo '<table id="cols" cellpadding="0" cellspacing="0" >' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="border-right:0px solid #9cb0bb">';
    echo '  <img src="' . $root . '/assets/img/' . $Icones['clear'] . '" width="1000" height="1" alt="clear"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr>' . "\n";
    echo '<td class="left">' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";
    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pnlDonGen\', this)" id="tab1">' . LG_CH_DATA_TAB . '</a></li>' . "\n";
    // Certains onglets ne sont disponibles qu'en modification
    if ($Ref_Union != -1) {
        echo '<li><a href="#" onclick="return showPane(\'pnlEnf\', this)">' . LG_UNION_CHILDREN . '</a></li>' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pnlEvts\', this)">' . LG_UNION_EVENTS . '</a></li>' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pnlDocs\', this)">' . LG_CH_DOCS . '</a></li>' . "\n";
    }
    echo '<li><a href="#" onclick="return showPane(\'pnlFiche\', this)">' . LG_CH_FILE . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<div class="tab-panes">' . "\n";
    // Onglets données générales de l'union
    echo '<div id="pnlDonGen">' . "\n";

    // Pavé conjoints
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_UNION_HUS_WIFE) . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";

    //Récupération des infos du conjoint
    if ($Conjoint_1 != -1) {
        $x1 = Get_Nom_Prenoms_Dates($Conjoint_1, $Nom_1, $Prenoms_1, $Ne_1, $Dec_1);
        ne_dec_approx($Ne_1, $Dec_1);
        $Sexe_1 = $P_Sexe;
    }
    if ($Conjoint_2 != -1) {
        $x1 = Get_Nom_Prenoms_Dates($Conjoint_2, $Nom_2, $Prenoms_2, $Ne_2, $Dec_2);
        ne_dec_approx($Ne_2, $Dec_2);
        $Sexe_2 = $P_Sexe;
    }


    if ($Sexe_1 == $Sexe_2)
        $Uni_Sexe = true;
    //echo 'Sexe_1 : '.$Sexe_1.', Sexe_2 : '.$Sexe_2.', ';
    //if ($Uni_Sexe) echo 'Uni_Sexe<br />'; else echo 'Pas Uni_Sexe<br />';

    // Conjoint 1
    echo '<tr><td width="12%">' . LG_UNION_HUS_1ST . '</td>';
    echo '<td>';
    // On affiche une liste si on ne vient pas de la fiche personne ou si le conjoint 1 est inconnu
    if ((!$depuis_pers) or ($Conjoint_1 == -1) or (($depuis_pers) and ($Conjoint_1 != $Personne))) {
        aff_liste_pers_restreinte('Conjoint_1U', true, true, $Conjoint_1, get_crit_sexe($Sexe_2), 'Nom, Prenoms', true, '', $Ne_2, $Dec_2, 'C');
        echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
        if (($Conjoint_1 == -1) && (!$Uni_Sexe)) {
            echo " <input type=\"button\" onclick=\"sel_der('m')\" value=\"Dernier homme saisi\" name=\"DH\"/>";
        }
    } else {
        echo ' ' . $Nom_1 . ' ' . $Prenoms_1 . "\n";
        echo '<input type="hidden" name="Conjoint_1U" value="' . $Conjoint_1 . '"/>';
    }
    echo '</td></tr>' . "\n";

    // Conjoint 2
    echo '<tr><td width="12%">' . LG_UNION_HUS_2ND . '</td>';
    echo '<td>';
    // On affiche une liste si on ne vient pas de la fiche personne ou si le conjoint 2 est inconnu
    if ((!$depuis_pers) || ($Conjoint_2 == -1) || (($depuis_pers) && ($Conjoint_2 != $Personne))) {
        aff_liste_pers_restreinte('Conjoint_2U', true, true, $Conjoint_2, get_crit_sexe($Sexe_1), 'Nom, Prenoms', true, '', $Ne_1, $Dec_1, 'C');
        echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
        if (($Conjoint_2 == -1) && (!$Uni_Sexe)) {
            echo " <input type=\"button\" onclick=\"sel_der('f')\" value=\"Derni&egrave;re femme saisie\" name=\"DF\"/>";
        }
    } else {
        echo ' ' . $Nom_2 . ' ' . $Prenoms_2 . "\n";
        echo '<input type="hidden" name="Conjoint_2U" value="' . $Conjoint_2 . '"/>';
    }
    echo '</td></tr>' . "\n";

    // Fin du pavé conjoint
    echo '</table></fieldset>';

    // Pavé date et lieu de l'union
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_UNION_WHERE_WHEN) . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    // Date mariage
    echo '<tr><td width="12%">' . LG_UNION_WHEN . '</td>';
    echo '<td>';
    zone_date2('AMaries_LeU', 'Maries_LeU', 'CMaries_LeU', $enreg2['Maries_Le']);
    echo LG_AT . ' ';
    aff_liste_villes('Ville_MariageU', true, false, $enreg2['Ville_Mariage']);    // Clé de sélection de la ligne
    echo '<input type="hidden" name="AVille_MariageU" value="' . $enreg2["Ville_Mariage"] . '"/>' . "\n";
    echo '<img id="ajout1" src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="' . LG_ADD_TOWN . '" title="' . LG_ADD_TOWN . '"' .
        ' onclick="inverse_div(\'id_div_ajout1\');document.getElementById(\'nouvelle_ville1\').focus();"/>' . "\n";
    echo '<div id="id_div_ajout1">';
    echo LG_ADD_TOWN_LIST . ' <input type="text" name="nouvelle_ville1"  id="nouvelle_ville1" maxlength="80"/>' . "\n";
    echo '<input type="button" name="ferme_OK" value="' . $lib_OK_h . '" onclick="ajoute1();"/>' . "\n";
    echo '<input type="button" name="ferme_An" value="' . $lib_Annuler_h . '" onclick="inverse_div(\'id_div_ajout1\');"/>' . "\n";
    echo '</div>';
    echo "</td></tr>\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    // Pavé contrat
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_UNION_CONTRACT) . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";

    //Contrat
    echo '<tr><td width="12%">' . LG_UNION_CONTRACT_WHEN . '</td>';
    echo '<td>';
    zone_date2('ADate_KU', 'Date_KU', 'CDate_KU', $enreg2['Date_K']);
    echo ' ' . LG_UNION_CONTRACT_NOTARY . ' <input type="text" size="60" name="Notaire_KU" value="' . $enreg2['Notaire_K'] . '"/></td></tr>' . "\n";
    echo '<tr><td width="12%">' . LG_UNION_CONTRACT_NOTARY_WHERE . '</td>';
    echo '<td>';
    aff_liste_villes('Ville_NotaireU', false, false, $enreg2['Ville_Notaire']);
    echo '<input type="hidden" name="ANotaire_KU" value="' . $enreg2["Notaire_K"] . '"/>';
    echo '<input type="hidden" name="AVille_NotaireU" value="' . $enreg2["Ville_Notaire"] . '"/>';
    echo ' <img id="ajout2" src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="' . LG_ADD_TOWN . '" title="' . LG_ADD_TOWN . '"' .
        'onclick="inverse_div(\'id_div_ajout2\');document.getElementById(\'nouvelle_ville2\').focus();"/>' . "\n";
    echo '<div id="id_div_ajout2">';
    echo LG_ADD_TOWN_LIST . ' <input type="text" name="nouvelle_ville2" maxlength="80"/>' . "\n";
    echo '<input type="button" name="ferme_OK" value="' . $lib_OK_h . '" onclick="ajoute2();"/>' . "\n";
    echo '<input type="button" name="ferme_An" value="' . $lib_Annuler_h . '" onclick="inverse_div(\'id_div_ajout2\');"/>' . "\n";
    echo '</div>';
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    // Commentaires
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_CH_COMMENT) . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    echo '<tr>' . "\n";
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Ref_Union, 'U');
    echo '<td>';
    echo '<textarea cols="50" rows="4" name="DiversU">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="hidden" name="ADiversU" value="' . htmlentities($Commentaire, ENT_QUOTES, $def_enc) . '"/>';
    echo '</td></tr><tr>';
    // Diffusion Internet commentaire
    echo '<td>' . LG_CH_COMMENT_VISIBILITY . ' <input type="checkbox" name="Diff_Internet_NoteU" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_Internet_NoteU" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";
    echo '</div>' . "\n";

    // Données de la fiche
    echo '<div id="pnlFiche">' . "\n";
    // Affiche les données propres à l'enregistrement de la fiche
    echo '<fieldset>';
    echo '<legend>Statut</legend>';
    echo '<input type="radio" id="Statut_FicheO" name="Statut_Fiche" value="O" ' . ($enreg2['Statut_Fiche'] == 'O' ? ' checked' : '') . '/>'
        . '<label for="Statut_FicheO">' . LG_CHECKED_RECORD_SHORT . '</label> ';
    echo '<input type="radio" id="Statut_FicheN" name="Statut_Fiche" value="N" ' . ($enreg2['Statut_Fiche'] == 'N' ? ' checked' : '') . '/>'
        . '<label for="Statut_FicheN">' . LG_NOCHECKED_RECORD_SHORT . '</label> ';
    echo '<input type="radio" id="Statut_FicheI" name="Statut_Fiche" value="I" ' . ($enreg2['Statut_Fiche'] == 'I' ? ' checked' : '') . '/>'
        . '<label for="Statut_FicheI">' . LG_FROM_INTERNET . '</label> ';
    echo '<input type="hidden" name="AStatut_Fiche" value="' . $enreg2['Statut_Fiche'] . '"/>';
    echo '</fieldset>';
    echo '<fieldset>';
    echo '<legend>Traçabilité</legend>';
    echo 'Création : ' . DateTime_Fr($enreg2['Date_Creation']) . '<br>';
    echo 'Modification : ' . DateTime_Fr($enreg2['Date_Modification']);
    echo '</fieldset>';
    // Possibilité de lier une source pour l'union
    if ($Ref_Union != -1) {
        //  Sources lies à l'union
        echo '<hr/>';
        $x = Aff_Sources_Objet($Ref_Union, 'U', 'N');
        echo '<br /> Lier une nouvelle source &agrave; l\'union : ' .
            Affiche_Icone_Lien('href="' . $root . '/edition_lier_source?refObjet=' . $Ref_Union . '&amp;typeObjet=U&amp;refSrc=-1"', 'ajout', 'Ajout d\'une source') . "\n";
    }
    echo '</div>' . "\n";

    // Pour le moment, enfants non possibles sur les unions unisexes
    if (($Ref_Union != -1) and (!$Uni_Sexe)) {

        $nom_pere = $Nom_1;
        $nom_mere = $Nom_2;

        // Enfants
        // Enfants déjà saisis
        echo '<div id="pnlEnf">' . "\n";
        $sqlE = 'select Enfant from ' . nom_table('filiations') .
            ' where pere = ' . $Conjoint_1 . ' and mere = ' . $Conjoint_2 . ' order by rang';
        $resE = lect_sql($sqlE);
        if ($resE->rowCount() > 0) {
            while ($row = $resE->fetch(PDO::FETCH_NUM)) {
                $Enfant = $row[0];
                if (Get_Nom_Prenoms($Enfant, $Nom, $Prenoms)) {
                    echo '<a href="' . $root . '/edition_personne?Refer=' . $Enfant . '">' . $Prenoms . ' ' . $Nom . '</a> ';
                    echo '<a href="' . $root . '/edition_filiation?Refer=' . $Enfant . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . LG_UNION_UPDATE_PARENTS . '" title="' . LG_UNION_UPDATE_PARENTS . '"></a><br />';
                }
            }
        }

        // Ajout rapide d'enfants avec création
        echo '<br />' . "\n";
        echo '<hr/>' . LG_UNION_CHILDREN_QUICK;
        echo '<br />' . LG_UNION_CHILDREN_DEF_NAME . ' ' . "\n";

        echo '<input type="radio" name="Nom_Defaut" value="' . $nom_pere . '" checked="checked"/>' . $nom_pere . ' ';
        echo '<input type="radio" name="Nom_Defaut" value="' . $nom_mere . '"/>' . $nom_mere . ' ' . "\n";

        echo '<table id="tblSampleE" width="100%">' . "\n";
        echo '<tr align="center">' . "\n";
        echo '<td class="rupt_table">' . LG_FIRST_NAME . '</td>';
        echo '<td class="rupt_table">' . $LG_birth . '</td>';
        echo '<td class="rupt_table">' . $LG_death . '</td>';
        echo '<td class="rupt_table">' . LG_SEXE . '</td>' . "\n";
        echo '</tr>' . "\n";

        for ($nb = 1; $nb <= $max_enf_rapides; $nb++) {
            if (pair($nb)) $style = 'class="liste"';
            else           $style = 'class="liste2"';
            echo '<tr>';
            echo '<td ' . $style . '><input type="text" size="20" name="PrenomsE_' . $nb . '" id="PrenomsE_' . $nb . '"/></td>' . "\n";
            echo '<td ' . $style . '>';
            zone_date2('ANe_leE_' . $nb, 'Ne_leE_' . $nb, 'CNe_leE_' . $nb, '');
            aff_liste_villes('SelVille_Nai_' . $nb, false, false, 0);
            echo '</td>' . "\n";
            echo '<td ' . $style . '>';
            zone_date2('ADecede_leE_' . $nb, 'Decede_leE_' . $nb, 'CDecede_leE_' . $nb, '');
            aff_liste_villes('SelVille_Dec_' . $nb, false, false, 0);
            echo '</td>' . "\n";
            echo '<td ' . $style . '><input type="radio" name="SexeE_' . $nb . '" value="m"/>' . LG_SEXE_MAN_I;
            echo '<input type="radio" name="SexeE_' . $nb . '" value="f"/>' . LG_SEXE_WOMAN_I . '</td>';
            echo '</tr>' . "\n";
        }

        echo '</table>' . "\n";

        echo '</div>' . "\n";

        // Données des évènements
        echo '<div id="pnlEvts">' . "\n";
        $x = Aff_Evenements_Objet($Ref_Union, 'U', 'O');

        // Ajout rapide d'évènements
        Aff_Ajout_Rapide_Evt('U');

        echo '</div>' . "\n";
    }

    // Documents liés à l'union
    echo '<div id="pnlDocs">' . "\n";
    //
    Aff_Documents_Objet($Ref_Union, 'U', 'N');
    // Possibilité de lier un document à l'union
    echo '<br /> ' . LG_UNION_ADD_DOC . ' '
        . Affiche_Icone_Lien('href="' . $root . '/edition_lier_doc?refObjet=' . $Ref_Union .
            '&amp;typeObjet=U&amp;refDoc=-1"', 'ajout', $LG_add) . "\n";
    echo '<br /> ' . LG_UNION_ADD_DOC_NEW . ' '
        . Affiche_Icone_Lien('href="' . $root . '/edition_document?Reference=-1&amp;refObjet=' . $Ref_Union .
            '&amp;typeObjet=U"', 'ajout', $LG_add) . "\n";
    echo '</div>' . "\n";

    echo '</div>' . "\n";    // <!-- panes -->

    // Suppression possible de l'union si elle n'est pas utilisée dans un évènement et si elle n'a pas d'image
    $lib_sup = '';
    if ($Ref_Union != -1) {
        if (!utils_evt_images('U', $Ref_Union)) $lib_sup = $lib_Supprimer;
    }

    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_UNION_THIS, false);

    echo '</div>' . "\n";    //<!-- tab container -->
    echo '</td></tr></table></div>' . "\n";
}

//Demande de mise à jour
if ($bt_OK) {

    $maj_site = false;

    // Détection d'un ajout de ville
    $Ville_MariageU = Ajoute_Ville($Ville_MariageU);
    $Ville_NotaireU = Ajoute_Ville($Ville_NotaireU);

    // Init des zones de requête
    $Type_Ref = 'U';
    $req = '';
    $Creation = false;
    if ($Statut_Fiche == '') {
        $Statut_Fiche = 'N';
    }
    // Cas de la modification
    if ($Ref_Union != -1) {
        Aj_Zone_Req('Conjoint_1', $Conjoint_1U, $MConjoint_1, 'N', $req);
        Aj_Zone_Req('Conjoint_2', $Conjoint_2U, $MConjoint_2, 'N', $req);
        Aj_Zone_Req('Maries_Le', $CMaries_LeU, $AMaries_LeU, 'A', $req);
        Aj_Zone_Req('Ville_Mariage', $Ville_MariageU, $AVille_MariageU, 'N', $req);
        Aj_Zone_Req('Date_K', $CDate_KU, $ADate_KU, 'A', $req);
        Aj_Zone_Req('Notaire_K', $Notaire_KU, $ANotaire_KU, 'A', $req);
        Aj_Zone_Req('Ville_Notaire', $Ville_NotaireU, $AVille_NotaireU, 'N', $req);
        Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $req);
        // Traitement des commentaires
        maj_commentaire($Ref_Union, $Type_Ref, $DiversU, $ADiversU, $Diff_Internet_NoteU, $ADiff_Internet_NoteU);
        if ($req_comment != '') {
            $res = lect_sql($req_comment);
            $maj_site = true;
        }
    }
    // Cas de la création
    else {
        // On n'autorise la création que si les 2 conjoints sont saisis
        $Creation = false;
        if (($Conjoint_1U != 0) and ($Conjoint_2U != 0))
            $Creation = true;
        if ($Creation) {
            Ins_Zone_Req($Conjoint_1U, 'N', $req);
            Ins_Zone_Req($Conjoint_2U, 'N', $req);
            Ins_Zone_Req($CMaries_LeU, 'A', $req);
            Ins_Zone_Req($Ville_MariageU, 'N', $req);
            Ins_Zone_Req($CDate_KU, 'A', $req);
            Ins_Zone_Req($Notaire_KU, 'A', $req);
            Ins_Zone_Req($Ville_NotaireU, 'N', $req);
        }
    }

    if ($req != '') $req = $req . ',';

    // Cas de la modification
    if (($Ref_Union != -1) and ($req != '')) {
        $req = 'update ' . nom_table('unions') . ' set ' . $req . 'Date_Modification = current_timestamp where Reference = ' . $Ref_Union;
    }
    // Cas de la création
    if ($Creation) {
        $req = 'insert into ' . nom_table('unions') .
            '(Conjoint_1,Conjoint_2,Maries_Le,Ville_Mariage,Date_K,Notaire_K,Ville_Notaire,Date_Creation,Date_Modification,Statut_Fiche) ' .
            'values(' . $req . 'current_timestamp,current_timestamp';
        Ins_Zone_Req($Statut_Fiche, 'A', $req);
        $req = $req . ")";
    }

    // Exéution de la requête
    if ($req != '') {
        $res = maj_sql($req);
        $maj_site = true;
    }

    // Création d'un enregistrement dans la table commentaires uniquement sur création (déjà fait sur maj)
    if (($DiversU != '') and ($Creation)) {
        insere_commentaire($connexion->lastInsertId(), $Type_Ref, $DiversU, $Diff_Internet_NoteU);
        $res = maj_sql($req_comment);
        $maj_site = true;
    }

    // Détermination du nombre de lignes d'enfants et d'évènements;
    // on se base sur le nombre de variables PrenomsE_xx et Titre_xx
    $nb_l_enfants = 0;
    $nb_l_events = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'PrenomsE_') !== false) $nb_l_enfants++;
        if (strpos($key, 'Titre_') !== false) $nb_l_events++;
    }

    // Traitement de l'ajout rapide d'enfants à partir du formulaire dynamique
    $LeSexe = '';
    $nouv_ident = -1;
    $LesPrenoms = '';
    $idNom_Defaut = -1;

    // Balayage des lignes des enfants
    $deb_req_pers = 'insert into ' . nom_table('personnes') .
        '(Reference, Nom, Prenoms, Ne_le, Decede_Le, Sexe, Date_Creation,Date_Modification,Statut_Fiche,idNomFam,Diff_Internet,Ville_Naissance,Ville_Deces) values (';
    $deb_req_nom_pers = 'insert into ' . nom_table('noms_personnes') .
        ' values(';
    $deb_req_fil = 'insert into ' . nom_table('filiations') .
        '(Enfant,Pere,Mere,Date_Creation,Date_Modification,Statut_Fiche) values (';

    for ($num_ligne = 1; $num_ligne <= $max_enf_rapides; $num_ligne++) {
        // On prend celui par défaut
        $LeNom = $Nom_Defaut;
        // On ne va chercher l'identifiant du nom par défaut qu'une fois

        $LesPrenoms = retourne_var_post('PrenomsE_', $num_ligne);
        $D_Nai = retourne_var_post('CNe_leE_', $num_ligne);
        $D_Dec = retourne_var_post('CDecede_leE_', $num_ligne);
        $LeSexe = retourne_var_post('SexeE_', $num_ligne);
        $VilNai = retourne_var_post('SelVille_Nai_', $num_ligne);
        $VilDec = retourne_var_post('SelVille_Dec_', $num_ligne);

        // Création de la personne si les prénoms sont connus
        if ($LesPrenoms != '') {

            if ($idNom_Defaut == -1) $idNom_Defaut = recherche_nom($Nom_Defaut);
            $idNom = $idNom_Defaut;

            if ($LeSexe != '') $LeSexe = '"' . $LeSexe . '"';
            else $LeSexe = 'null';

            // Création de la personne
            if ($nouv_ident == -1) $nouv_ident = Nouvel_Identifiant('Reference', 'personnes');
            else $nouv_ident++;
            $reqE = $deb_req_pers .
                $nouv_ident . ',"' . $LeNom . '","' . $LesPrenoms . '","' . $D_Nai . '","' . $D_Dec . '",' . $LeSexe .
                ',current_timestamp,current_timestamp,\'N\',' . $idNom . ',\'O\',' . $VilNai . ',' . $VilDec . ')';
            $res = maj_sql($reqE);
            $req = $deb_req_nom_pers . $nouv_ident . ',' . $idNom . ',\'O\',null)';
            $res = maj_sql($req);

            // Création de la filiation
            $reqE = $deb_req_fil . $nouv_ident . ',' . $Conjoint_1U . ',' . $Conjoint_2U . ',current_timestamp,current_timestamp,\'N\')';
            $res = maj_sql($reqE);

            $maj_site = true;
        }
    }

    // Traitement de l'ajout rapide d'évènements à partir du formulaire dynamique
    if ($nb_l_events > 0) {

        $deb_req_evt = 'insert into ' . nom_table('evenements') .
            ' (Identifiant_zone,Identifiant_Niveau,Code_Type,Titre,Date_Creation,Date_Modification,Statut_Fiche) ' .
            ' values ' .
            ' (0,0,\'';
        $deb_req_con_obj = 'insert into ' . nom_table('concerne_objet') .
            ' (Evenement,Reference_Objet,Type_Objet) ' .
            ' values (';

        for ($num_ligne = 1; $num_ligne <= $nb_l_events; $num_ligne++) {
            $LeType = retourne_var_post('Type_', $num_ligne);
            $LeTitre = retourne_var_post('Titre_', $num_ligne);

            if ($LeTitre != '') {
                $req = $deb_req_evt . $LeType . '\',\'' . $LeTitre . '\',current_timestamp,current_timestamp,\'N\')';
                $res = maj_sql($req);
                $req = $deb_req_con_obj . $connexion->lastInsertId() . ',' . $Ref_Union . ',\'U\')';
                $res = maj_sql($req);
                $maj_site = true;
            }
        }
    }

    // Mise à jour de la date de mise à jour du site
    if ($maj_site) maj_date_site();

    // Retour arrière
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    // Récupération de la liste des types
    Recup_Types_Evt('U');

    include(__DIR__ . '/assets/js/Ajout_Evenement.js');
    include(__DIR__ . '/assets/js/Insert_Tiny.js');

    // Récupération des infos du conjoint passé en paramètre
    $x = Get_Nom_Prenoms_Dates($Personne, $Nom, $Prenoms, $Ne_Pers, $Dec_Pers);

    if ($Modif) {
        $sql = 'select * from ' . nom_table('unions') . ' where Reference = ' . $Ref_Union . ' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
    } else {
        $enreg2['Reference'] = 0;
        $enreg2['Conjoint_1'] = 0;
        $enreg2['Conjoint_2'] = 0;
        $enreg2['Maries_Le'] = '';
        $enreg2['Ville_Mariage'] = 0;
        $enreg2['Date_K'] = '';
        $enreg2['Notaire_K'] = '';
        $enreg2['Ville_Notaire'] = 0;
        $enreg2['Date_Creation'] = '';
        $enreg2['Date_Modification'] = '';
        $enreg2['Statut_Fiche'] = '';
    }
    //if ($Ref_Union != -1) $enreg2 = Champs_car($res,$enreg);
    //else                  $enreg2 = $enreg;

    // En modification, on récupère les 2 conjoints
    if ($Ref_Union != -1) {
        $Conjoint_1 = $enreg2['Conjoint_1'];
        $Conjoint_2 = $enreg2['Conjoint_2'];
    }
    // En création, l'1 des conjoints n'est pas connu
    else {
        // Par défaut, l'homme est en conjoint 1
        if ($P_Sexe == 'm') {
            $Conjoint_1  = $Personne;
            $Conjoint_2  = -1;
            $Sexe_1 = $P_Sexe;
            if ($Uni_Sexe)
                $Sexe_2 = 'm';
            else $Sexe_2 = 'f';
        } else {
            $Conjoint_1  = -1;
            $Conjoint_2  = $Personne;
            if ($Uni_Sexe) {
                $Sexe_1 = 'f';
                $Sexe_2 = 'f';
            } else {
                $Sexe_1 = 'm';
                $Sexe_2 = $P_Sexe;
            }
        }
    }

    /*echo '$Uni_Sexe : '.$Uni_Sexe.'/';
	echo '$P_Sexe : '.$P_Sexe.'/';
	echo '$Conjoint_1 : '.$Conjoint_1.'/';
	echo '$Conjoint_2 : '.$Conjoint_2.'/';*/

    $compl = Ajoute_Page_Info(600, 150);
    if ($Ref_Union != -1) {
        $compl = Affiche_Icone_Lien('href="' . $root . '/liste_images?Refer=' . $Ref_Union . '&amp;Type_Ref=U"', 'images', 'Images') . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/fiche_couple_txt?Reference=' . $Ref_Union . '"', 'fiche_fam', 'Fiche couple') . "\n";
    }
    Insere_Haut($titre, $compl, 'Edition_Union', $Conjoint_1 . "/" . $Conjoint_2);

    //echo '<form id="saisie" method="post" onsubmit="return verification_form_union(this,\'Conjoint_1U,Conjoint_2U\')" action="'.my_self().'?'.Query_Str().'">'."\n";
    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'Conjoint_1U,Conjoint_2U\')">' . "\n";

    echo '<input type="hidden" name="MConjoint_1" value="' . $Conjoint_1 . '"/>' . "\n";
    echo '<input type="hidden" name="MConjoint_2" value="' . $Conjoint_2 . '"/>' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . $Horigine . '"/>' . "\n";

    // Dernière personnes saisies
    if ($Conjoint_1  == -1) {
        echo '<input type="hidden" name="MaxConjoint_1" value="';
        if (!$Uni_Sexe) echo dernier('m');
        else echo dernier('f');
        echo '"/>' . "\n";
    }
    if ($Conjoint_2  == -1) {
        echo '<input type="hidden" name="MaxConjoint_2" value="';
        if (!$Uni_Sexe) echo dernier('f');
        else echo dernier('m');
        echo '"/>' . "\n";
    }

    // Affichage des données de l'union
    $x = Aff_Union($enreg2, $Ref_Union, false);

    echo '</form>';
    include(__DIR__ . '/assets/js/gest_onglets.js');
    //echo '<!-- On cache les div d\'ajout des villes et on positionne l\'onglet par défaut -->'."\n";
    echo '<script type="text/javascript">' . "\n";
    echo '	cache_div("id_div_ajout1");' . "\n";
    echo '	cache_div("id_div_ajout2");' . "\n";
    echo '	setupPanes("container1", "tab1", 40);' . "\n";
    echo '</script>' . "\n";

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
?>
<script type="text/javascript">
    // Ajoute la ville saisie à la fin des listbox
    function ajoute1() {
        inverse_div('id_div_ajout1');
        nouv_val = document.forms.saisie.nouvelle_ville1.value;
        document.forms.saisie.nouvelle_ville1.value = "";
        Insert_Sel_1_2("Ville_MariageU", "Ville_NotaireU", nouv_val);
    }

    function ajoute2() {
        inverse_div('id_div_ajout2');
        nouv_val = document.forms.saisie.nouvelle_ville2.value;
        document.forms.saisie.nouvelle_ville1.value = "";
        Insert_Sel_1_2("Ville_NotaireU", "Ville_MariageU", nouv_val);
    }

    // Sélection de la dernière personne saisie pour le sexe
    function sel_der(sexe) {
        if (sexe == 'm') {
            document.forms.saisie.Conjoint_1U.value = document.forms.saisie.MaxConjoint_1.value;
        }
        if (sexe == 'f') {
            document.forms.saisie.Conjoint_2U.value = document.forms.saisie.MaxConjoint_2.value;
        }
    }
</script>
</body>

</html>