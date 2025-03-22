<?php
//=====================================================================
// Edition d'une fiche filiation
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
    'PereP',
    'APereP',
    'MereP',
    'AMereP',
    'RangP',
    'ARangP',
    'DiversF',
    'ADiversF',
    'Diff_Internet_NoteF',
    'ADiff_Internet_NoteF',
    'Statut_Fiche',
    'AStatut_Fiche',
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

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Fiche filiation';            // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/../app/ressources/gestion_pages.php');          // Appel de la gestion standard des pages



// Sur demande de suppression
if ($bt_Sup) {

    $fin_req = ' where Reference_Objet = ' . $Refer . " and Type_Objet = 'F'";
    // Suppression des commentaires
    if ($ADiversF != '') {
        $res = maj_sql('delete from ' . nom_table('commentaires') . $fin_req);
    }
    // Suppression des liens vers les documents
    $res = maj_sql('delete from ' . nom_table('concerne_doc') . $fin_req);
    // Suppression des liens vers les évènements
    $res = maj_sql('delete from ' . nom_table('concerne_objet') . $fin_req);
    // Suppression des liens vers les images
    $req = 'delete from ' . nom_table('images') . ' where Reference = ' . $Refer . " and Type_Ref = 'F'";
    $res = maj_sql($req);

    // Suppression de la filiation elle-même
    $req = 'delete from ' . nom_table('filiations') . ' where Enfant = ' . $Refer;
    $res = maj_sql($req);

    maj_date_site();
    Retour_Ar();
}

$PereP                = Secur_Variable_Post($PereP, 1, 'N');
$APereP               = Secur_Variable_Post($APereP, 1, 'N');
$MereP                = Secur_Variable_Post($MereP, 1, 'N');
$AMereP               = Secur_Variable_Post($AMereP, 1, 'N');
$RangP                = Secur_Variable_Post($RangP, 1, 'N');
$ARangP               = Secur_Variable_Post($ARangP, 1, 'N');
$DiversF              = Secur_Variable_Post($DiversF, 65535, 'S');
$ADiversF             = Secur_Variable_Post($ADiversF, 65535, 'S');
$Diff_Internet_NoteF  = Secur_Variable_Post($Diff_Internet_NoteF, 1, 'S');
$ADiff_Internet_NoteF = Secur_Variable_Post($ADiff_Internet_NoteF, 1, 'S');
$Statut_Fiche         = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$AStatut_Fiche        = Secur_Variable_Post($AStatut_Fiche, 1, 'S');

// Recup des variables passées dans l'URL : référence de la femme et du mari + ref union
$Conjoint_1 = Recup_Variable('Conjoint_1', 'N');
if (!$Conjoint_1) $Conjoint_1 = -1;

$Conjoint_2 = Recup_Variable('Conjoint_2', 'N');
if (!$Conjoint_2) $Conjoint_2 = -1;

$Reference = Recup_Variable('Reference', 'N');
if (!$Reference) $Reference = -1;

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

// Affiche une union
function Aff_Filiation($enreg)
{
    global $root, $Icones, $Images, $Refer, $Nom,
        $enreg, $Sexe,
        $Commentaire, $Diffusion_Commentaire_Internet,
        $lib_Okay, $lib_Annuler, $lib_Supprimer,
        $ne_pers, $dec_pers, $LG_Father, $LG_Mother, $LG_Data_tab, $LG_Ch_Filiation_Events, $LG_Ch_Filiation_Docs, $LG_File, $LG_Ch_Filiation_Parent_Choice, $LG_Ch_Filiation_Related_Choice, $LG_Ch_Filiation_Last_Union, $LG_Ch_Filiation_Rank, $LG_Ch_Filiation_Brother, $LG_Ch_Filiation_Sister, $LG_Ch_Filiation_Related, $LG_Ch_Filiation_Rank_Inc, $LG_Ch_Filiation_Rank_Dec, $LG_Ch_Filiation_Link_Doc, $LG_Ch_Filiation_Add_Doc, $LG_Ch_Filiation_Link_New_Doc;

    $LePere = $enreg['Pere'];
    if ($LePere == '') $LePere = 0;
    $LaMere = $enreg['Mere'];
    if ($LaMere == "") $LaMere = 0;
    $LeRang = $enreg['Rang'];
    if ($LeRang == '') $LeRang = 0;

    // Création ou modification de la fiche ?
    $creation = 0;
    $modification = 0;
    if ($LePere or $LaMere) $modification = 1;
    $creation = !$modification;

    // Dernière personnes saisies
    if (! $LePere) echo '<input type="hidden" name="MaxConjoint_1S" value="' . dernier('m') . '"/>' . "\n";
    if (! $LaMere) echo '<input type="hidden" name="MaxConjoint_2S" value="' . dernier('f') . '"/>' . "\n";

    echo '<br />';

    echo '<div id="content">' . "\n";
    echo '<table id="cols" cellpadding="0" cellspacing="0" >' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="border-right:0px solid #9cb0bb">';
    echo '  <img src="' . $root . '/assets/img/' . $Icones['clear'] . '" width="850" height="1" alt="clear"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr>' . "\n";
    echo '<td class="left">' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";
    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pnlDonGen\', this)" id="tab1">' . $LG_Data_tab . '</a></li>' . "\n";
    // Certains onglets ne sont disponibles qu'en modification
    if ($modification) {
        echo '<li><a href="#" onclick="return showPane(\'pnlEvts\', this)">' . $LG_Ch_Filiation_Events . '</a></li>' . "\n";
    }
    echo '<li><a href="#" onclick="return showPane(\'pnlDocs\', this)">' . $LG_Ch_Filiation_Docs . '</a></li>' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pnlFiche\', this)">' . $LG_File . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<div class="tab-panes">' . "\n";
    // Onglets données générales de la filiation
    echo '<div id="pnlDonGen">' . "\n";
    // Pavé parents
    echo '<fieldset>' . "\n";
    echo '<legend>' . $LG_Ch_Filiation_Parent_Choice . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    // Père
    echo '<tr><td width="10%">' . LG_FATHER . '</td>';
    echo "<td>\n";
    //aff_liste_pers_restreinte($nom_select,$premier,$dernier,$cle_sel,$crit,$order,$oblig, $oc, $pivot_inf, $pivot_sup, $type_ctrl)
    aff_liste_pers_restreinte('PereP', true, true, $LePere, 'Sexe = "m" or Sexe is Null', 'Nom, Prenoms', false, '', $ne_pers, $dec_pers, 'F');
    echo '&nbsp;<input type="button" onclick="removeP(\'' . addslashes($Nom) . '\')" value="Que les ' . $Nom . '" name="RP"/>';
    if (!$LePere) echo '&nbsp;<input type="button" onclick="sel_derP(\'m\')" value="Dernier homme saisi" name="DH"/>';
    echo '<input type="hidden" name="APereP" value="' . $LePere . '"/>';
    echo '</td></tr>' . "\n";

    // Mère
    echo '<tr><td width="10%">' . LG_MOTHER . '</td>';
    echo "<td>\n";
    //aff_liste_pers_restreinte($nom_select,$premier,$dernier,$cle_sel,$crit,$order,$oblig, $oc, $pivot_inf, $pivot_sup, $type_ctrl)
    aff_liste_pers_restreinte('MereP', true, true, $LaMere, 'Sexe = "f" or Sexe is Null', 'Nom, Prenoms', false, '', $ne_pers, $dec_pers, 'F');
    echo '&nbsp;<input type="button" onclick="removeM(\'' . addslashes($Nom) . '\')" value="Que les ' . $Nom . '" name="RM"/>';
    if (!$LaMere) echo '&nbsp;<input type="button" onclick="sel_derP(\'f\')" value="Derni&egrave;re femme saisie" name="DF"/>';
    echo '<input type="hidden" name="AMereP" value="' . $LaMere . '"/>';
    echo '</td></tr>' . "\n";

    echo "<tr>\n";
    echo '<td colspan="2">' . "\n";
    echo '<input type="button" onclick="sel_der()" value="' . $LG_Ch_Filiation_Last_Union . '" name="DH"/>';
    // Enfant du dernier couple saisi en création
    $n_unions = nom_table('unions');
    $sql = 'SELECT Conjoint_1, Conjoint_2 from ' . $n_unions . ' where Reference = (SELECT max(Reference) from ' . $n_unions . ')';
    $resmax = lect_sql($sql);
    $enrmax = $resmax->fetch(PDO::FETCH_NUM);
    if ($enrmax) {
        echo '<input type="hidden" name="MaxConjoint_1" value="' . $enrmax[0] . '"/>' . "\n";
        echo '<input type="hidden" name="MaxConjoint_2" value="' . $enrmax[1] . '"/>' . "\n";
    }
    $resmax->closeCursor();

    echo '</td></tr>' . "\n";
    echo '</table></fieldset>';

    // Pavé colattéral
    switch ($Sexe) {
        case 'm':
            $lib = $LG_Ch_Filiation_Brother;
            break;
        case 'f':
            $lib = $LG_Ch_Filiation_Sister;
            break;
        default:
            $lib = $LG_Ch_Filiation_Related;
            break;
    }
    echo '<fieldset>' . "\n";
    echo '<legend>' . my_html($LG_Ch_Filiation_Related_Choice) . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    echo '<tr><td width="10%">' . $lib . '</td>';
    echo '<td>' . "\n";
    $sql = "select Pere, Mere, Nom, Prenoms, Ne_le, Decede_Le " .
        " from " . nom_table('filiations') . " f, " . nom_table('personnes') . " p " .
        " where Enfant = Reference " .
        " and p.Nom = '" . addslashes($Nom) . "'" .
        " order by Prenoms";
    $res = lect_sql($sql);
    echo "<select name='CollatP' onchange=\"sel_col()\">\n";
    echo "<option value=\"0\">&nbsp;</option>";
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '/' . $row[1] . '"';
        echo '>' . my_html($row[2] . ' ' . $row[3]) . aff_annees_pers($row[4], $row[5]) . '</option>' . "\n";
    }
    $res->closeCursor();
    echo " </select>\n";

    echo '</td>' . "\n";
    echo "</tr>\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    //Rang
    echo '<fieldset>' . "\n";
    echo '<legend>' . $LG_Ch_Filiation_Rank . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    echo '<tr><td width="10%">' . $LG_Ch_Filiation_Rank . '</td>';
    echo '<td>';
    echo '<img src="' . $root . '/assets/img/' . $Icones['moins'] . '" alt="' . $LG_Ch_Filiation_Rank_Dec . '" title="' . $LG_Ch_Filiation_Rank_Dec . '" ';
    echo 'onclick="if (document.forms.saisie.RangP.value>0) {document.forms.saisie.RangP.value--;}"/>' . "\n";
    echo '<input type="text" size="2" name="RangP" value="' . $LeRang . '" onchange="verification_num(this);"/>' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['plus'] . '" alt="' . $LG_Ch_Filiation_Rank_Inc . '" title="' . $LG_Ch_Filiation_Rank_Inc . '" ';
    echo 'onclick="document.forms.saisie.RangP.value++;"/>' . "\n";
    echo '<input type="hidden" name="ARangP" value="' . $LeRang . '"/>';
    echo "</td>\n";
    echo "</tr>\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    // Commentaires
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_CH_COMMENT) . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    //Divers
    echo '<tr>' . "\n";
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($enreg['Enfant'], 'F');
    echo '<td>';
    echo '<textarea cols="50" rows="4" name="DiversF">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="hidden" name="ADiversF" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr>' . "\n";
    // Diffusion Internet commentaire
    echo '<tr><td><label for="Diff_Internet_NoteF">' . LG_CH_COMMENT_VISIBILITY . '</label>'
        . '&nbsp;<input type="checkbox" id="Diff_Internet_NoteF" name="Diff_Internet_NoteF" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_Internet_NoteF" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    echo '</div>' . "\n";

    if ($modification) {
        // Données des évènements
        echo '<div id="pnlEvts">' . "\n";
        $x = Aff_Evenements_Objet($Refer, 'F', 'O');

        // Ajout rapide d'évènements
        Aff_Ajout_Rapide_Evt('F');

        echo '</div>' . "\n";
    }

    // Documents liés à l'union
    echo '<div id="pnlDocs">' . "\n";
    //
    Aff_Documents_Objet($Refer, 'F', 'N');
    // Possibilité de lier un document à la filiation
    echo '<br />&nbsp;' . my_html($LG_Ch_Filiation_Link_Doc . ' : ') .
        Affiche_Icone_Lien('href="' . $root . '/edition_lier_doc?refObjet=' . $Refer .
            '&amp;typeObjet=F&amp;refDoc=-1"', 'ajout', 'Ajout d\'un document') . "\n";
    echo '<br />&nbsp;' . my_html($LG_Ch_Filiation_Link_New_Doc . ' : ') .
        Affiche_Icone_Lien('href="' . $root . '/edition_document?Reference=-1&amp;refObjet=' . $Refer .
            '&amp;typeObjet=F"', 'ajout', $LG_Ch_Filiation_Add_Doc) . "\n";
    echo '</div>' . "\n";

    // Données de la fiche
    echo '<div id="pnlFiche">' . "\n";
    // Affiche les données propres à l'enregistrement de la fiche
    echo '<fieldset>';
    echo '<legend>Statut</legend>';
    echo '<input type="radio" id="Statut_FicheO" name="Statut_Fiche" value="O" ' . ($enreg['Statut_Fiche'] == 'O' ? ' checked' : '') . '/>'
        . '<label for="Statut_FicheO">' . LG_CHECKED_RECORD_SHORT . '</label> ';
    echo '<input type="radio" id="Statut_FicheN" name="Statut_Fiche" value="N" ' . ($enreg['Statut_Fiche'] == 'N' ? ' checked' : '') . '/>'
        . '<label for="Statut_FicheN">' . LG_NOCHECKED_RECORD_SHORT . '</label> ';
    echo '<input type="radio" id="Statut_FicheI" name="Statut_Fiche" value="I" ' . ($enreg['Statut_Fiche'] == 'I' ? ' checked' : '') . '/>'
        . '<label for="Statut_FicheI">' . LG_FROM_INTERNET . '</label> ';
    echo '<input type="hidden" name="AStatut_Fiche" value="' . $enreg['Statut_Fiche'] . '"/>';
    echo '</fieldset>';
    echo '<fieldset>';
    echo '<legend>Traçabilité</legend>';
    echo 'Création : ' . DateTime_Fr($enreg['Date_Creation']) . '<br>';
    echo 'Modification : ' . DateTime_Fr($enreg['Date_Modification']);
    echo '</fieldset>';
    //  Sources lies à la filiation
    echo '<hr/>';
    $x = Aff_Sources_Objet($Refer, 'F', 'N');
    // Possibilité de lier une source pour la filiation
    echo '<br />&nbsp;Lier une nouvelle source &agrave; la filiation : ' .
        Affiche_Icone_Lien('href="' . $root . '/edition_lier_source?refObjet=' . $Refer . '&amp;typeObjet=F&amp;refSrc=-1"', 'ajout', 'Ajout d\'une source') . "\n";
    echo '</div>' . "\n";

    echo '</div>' . "\n";    // <!-- panes -->

    // Suppression possible de la filiation si elle n'est pas utilisée dans un évènement et si elle n'a pas d'image
    $lib_sup = '';
    if ($modification) {
        if (!utils_evt_images('F', $Refer)) $lib_sup = $lib_Supprimer;
    }

    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, 'cette filiation', false);

    echo '</div>' . "\n";    //<!-- tab container -->
    echo '</td></tr></table></div>' . "\n";
}

//Demande de mise à jour
if ($bt_OK) {

    // Init des zones de requête
    $Type_Ref = 'F';
    $Creation = 0;
    $req = '';
    $req_comment = '';
    if ($Diff_Internet_NoteF == '') $Diff_Internet_NoteF = 'N';

    // Rang 0 par défaut
    if ($RangP == '') $RangP = 0;

    // Fiche non validée par défaut
    if ($Statut_Fiche == '') {
        $Statut_Fiche = 'N';
    }

    // Type d'objet pour les commentaires
    $Type_Ref = 'F';

    $maj_site = false;

    // Cas de la modification ==> le père ou la mère était servi
    if (($APereP != 0) or ($AMereP != 0)) {
        Aj_Zone_Req('Pere', $PereP, $APereP, 'N', $req);
        Aj_Zone_Req('Mere', $MereP, $AMereP, 'N', $req);
        Aj_Zone_Req('Rang', $RangP, $ARangP, 'A', $req);
        Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $req);
        // Traitement des commentaires
        maj_commentaire($Refer, $Type_Ref, $DiversF, $ADiversF, $Diff_Internet_NoteF, $ADiff_Internet_NoteF);
        if ($req_comment != '') $res = lect_sql($req_comment);
    }
    // Cas de la création
    else {
        // On n'autorise la création que si le père ou la mère est saisi
        if (($PereP != 0) or ($MereP != 0)) {
            $Creation = 1;
        }
        if ($Creation == 1) {
            Ins_Zone_Req($Refer, 'N', $req);
            Ins_Zone_Req($PereP, 'N', $req);
            Ins_Zone_Req($MereP, 'N', $req);
            Ins_Zone_Req($RangP, 'A', $req);
        }
    }

    if ($req != '') $req = $req . ',';

    // Cas de la modification
    if (($Creation == 0) and ($req != '')) {
        $req = 'update ' . nom_table('filiations') . ' set ' . $req .
            'Date_Modification = current_timestamp' .
            ' where Enfant = ' . $Refer . ';';
    }
    // Cas de la création
    if ($Creation == 1) {
        $req = 'insert into ' . nom_table('filiations') . ' values(' . $req .
            'current_timestamp,current_timestamp';
        Ins_Zone_Req($Statut_Fiche, 'A', $req) .
            $req = $req . ')';
    }

    // Si le père et la mère sont à 0, on ne traite pas la requête, sinon on aura une filiation sans parents qui n'apparaitra jamais
    // et des filiations multiples possibles
    if (($PereP == 0) and ($MereP == 0)) $req = '';

    // Exéution de la requête
    if ($req != '') {
        $res = maj_sql($req);
        $maj_site = true;
    }

    // Création d'un enregistrement dans la table commentaires uniquement sur création (déjà fait sur maj)
    if (($DiversF != '') and ($Creation)) {
        insere_commentaire($Refer, $Type_Ref, $DiversF, $Diff_Internet_NoteF);
        $res = maj_sql($req_comment);
        $maj_site = true;
    }

    // Traitement de l'ajout rapide d'évènements à partir du formulaire dynamique
    foreach ($_POST as $key => $value) {
        $$key = addslashes(trim($value));
        if (strpos($key, 'Type_') !== false) $LeType = $value;
        if (strpos($key, 'Titre_') !== false) {
            $LeTitre = $value;
            $req = 'insert into ' . nom_table('evenements') .
                ' (Identifiant_zone,Identifiant_Niveau,Code_Type,Titre,Date_Creation,Date_Modification,Statut_Fiche) ' .
                ' values ' .
                ' (0,0,\'' . $LeType . '\',\'' . addslashes($LeTitre) . '\',current_timestamp,current_timestamp,\'N\')';
            if ($req != '') $res = maj_sql($req);
            $req = 'insert into ' . nom_table('concerne_objet') .
                ' (Evenement,Reference_Objet,Type_Objet) ' .
                ' values ' .
                ' (' . $connexion->lastInsertId() . ',' . $Refer . ',\'F\')';
            if ($req != '') {
                $res = maj_sql($req);
                $maj_site = true;
            }
        }
    }

    if ($maj_site) maj_date_site();

    // Retour arrière
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    // Récupération de la liste des types
    Recup_Types_Evt('F');

    include(__DIR__ . '/assets/js/Edition_Filiation.js');
    include(__DIR__ . '/assets/js/Ajout_Evenement.js');
    include(__DIR__ . '/assets/js/Insert_Tiny.js');
    $compl = '';

    $sql = 'select Nom, Prenoms, Sexe, Ne_le, Decede_Le from ' . nom_table('personnes') . ' where reference = ' . $Refer . ' limit 1';
    $res = lect_sql($sql);
    $enreg2 = $res->fetch(PDO::FETCH_NUM);
    $Nom      = $enreg2[0];
    $Prenoms  = $enreg2[1];
    $Sexe     = $enreg2[2];
    $ne_pers  = $enreg2[3];
    $dec_pers = $enreg2[4];
    $res->closeCursor();

    Insere_Haut('Filiation de&nbsp;' . $Prenoms . ' ' . $Nom, $compl, 'Edition_Filiation', $Refer);
    echo '<form id="saisie" method="post" action="' . my_self() . '?Refer=' . $Refer . '">' . "\n";
    echo '<input type="hidden" name="Refer" value="' . $Refer . '"/>' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . $Horigine . '"/>' . "\n";
    $sql = 'select * from ' . nom_table('filiations') . ' where Enfant = ' . $Refer . ' limit 1';
    $res = lect_sql($sql);
    if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
        $lu = true;
    } else {
        $enreg['Enfant'] = 0;
        $enreg['Pere'] = 0;
        $enreg['Mere'] = 0;
        $enreg['Rang'] = 0;
        $enreg['Date_Creation'] = '';
        $enreg['Date_Modification'] = '';
        $enreg['Statut_Fiche'] = '';
    }

    // Affichage des données de la filiation
    $x = Aff_Filiation($enreg);

    echo '</form>';
    include(__DIR__ . '/assets/js/gest_onglets.js');
    echo '<!-- On positionne l\'onglet par défaut -->' . "\n";
    echo '<script type="text/javascript">' . "\n";
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
</body>

</html>