<?php
//=====================================================================
// Edition d'une région
//=====================================================================

session_start();
include('fonctions.php');              // Appel des fonctions générales

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'Nom_Region',
    'ANom_Region',
    'Code_Region',
    'ACode_Region',
    'DiversR',
    'ADiversR',
    'Diff_Internet_NoteR',
    'ADiff_Internet_NoteR',
    'Zone_Mere',
    'AZone_Mere',
    'Statut_Fiche',
    'AStatut_Fiche',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Edition région';             // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
include('Gestion_Pages.php');          // Appel de la gestion standard des pages

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Nom_Region           = Secur_Variable_Post($Nom_Region, 50, 'S');
$ANom_Region          = Secur_Variable_Post($ANom_Region, 50, 'S');
$Code_Region          = Secur_Variable_Post($Code_Region, 3, 'S');
$ACode_Region         = Secur_Variable_Post($ACode_Region, 3, 'S');
$Zone_Mere            = Secur_Variable_Post($Zone_Mere, 1, 'N');
$AZone_Mere           = Secur_Variable_Post($AZone_Mere, 1, 'N');
$DiversR              = Secur_Variable_Post($DiversR, 65535, 'S');
$ADiversR             = Secur_Variable_Post($ADiversR, 65535, 'S');
$Diff_Internet_NoteR  = Secur_Variable_Post($Diff_Internet_NoteR, 1, 'S');
$ADiff_Internet_NoteR = Secur_Variable_Post($ADiff_Internet_NoteR, 1, 'S');
$Statut_Fiche         = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$AStatut_Fiche        = Secur_Variable_Post($AStatut_Fiche, 1, 'S');

// Recup des variables passées dans l'URL : Identifiant de la région
$Ident = Recup_Variable('Ident', 'N');
$Modif = true;
if ($Ident == -1) $Modif = false;
// Titre pour META
if ($Modif)
    $titre = $LG_Menu_Title['Region_Edit'];
else
    $titre = $LG_Menu_Title['Region_Add'];

// Affiche une région
function Aff_Region($enreg2)
{
    global $db, $chemin_images, $Ident, $Images, $Commentaire, $Diffusion_Commentaire_Internet, $LG_Data_tab, $LG_File, $LG_Edit_Region_Name, $LG_Edit_Region_Code, $LG_Edit_Region_Country, $lib_Okay, $lib_Annuler;

    echo '<div id="content">' . "\n";
    echo '<table id="cols"  border="0" cellpadding="0" cellspacing="0" >' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="border-right:0px solid #9cb0bb">' . "\n";
    echo '  <img src="' . $chemin_images . $Images['clear'] . '" width="590" height="1" alt="clear"/>' . "\n";
    echo '</td>' . "\n";
    echo '</tr>' . "\n";

    echo '<tr>' . "\n";
    echo '<td class="left">' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";
    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pane1\', this)" id="tab1">' . my_html($LG_Data_tab) . '</a></li>' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pane2\', this)">' . my_html($LG_File) . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<div class="tab-panes">' . "\n";
    // Onglets données générales
    echo '<div id="pane1">' . "\n";
    echo '<fieldset>' . "\n";
    echo '<legend>' . my_html($LG_Data_tab) . '</legend>' . "\n";

    echo '<table width="100%" border="0">' . "\n";

    $largP = 12;

    col_titre_tab_noClass($LG_Edit_Region_Name, $largP);
    $Nom_Region_Min = $enreg2['Nom_Region_Min'];
    echo '<td><input type="text" size="70" class="oblig" name="Nom_Region" id="Nom_Region" value="' . $Nom_Region_Min . '"/>&nbsp;' . "\n";
    Img_Zone_Oblig('imgObligNom');
    echo '<input type="hidden" name="ANom_Region" value="' . $Nom_Region_Min . '"/></td>' . "\n";
    echo "</tr>\n";

    col_titre_tab_noClass($LG_Edit_Region_Code, $largP);
    $Region = $enreg2['Region'];
    echo '<td><input type="text" size="3" class="oblig" name="Code_Region" id="Code_Region" value="' . $Region . '"/>&nbsp;' . "\n";
    Img_Zone_Oblig('imgObligCode');
    echo '<input type="hidden" name="ACode_Region" value="' . $Region . '"/></td>' . "\n";
    echo "</tr>\n";

    //Pays
    col_titre_tab_noClass($LG_Edit_Region_Country, $largP);
    echo "<td><select name='Zone_Mere'>\n";
    $sql = 'select Identifiant_zone, Nom_Pays from ' . nom_table('pays') . ' order by Nom_Pays';
    $res = lect_sql($sql);
    $enr_zone = $enreg2['Zone_Mere'];
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($enr_zone == $row[0]) echo ' selected="selected" ';
        echo '>' . my_html($row[1]);
        echo '</option>' . "\n";
    }
    echo '</select>' . "\n";
    echo '<input type="hidden" name="AZone_Mere" value="' . $enr_zone . '"/>';
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    $res->closeCursor();
    echo '</fieldset>' . "\n";

    // Commentaires
    echo '<fieldset>' . "\n";
    aff_legend(LG_CH_COMMENT);
    echo '<table width="95%" border="0">' . "\n";
    echo '<tr>' . "\n";
    echo '<td>';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Ident, 'R');
    echo '<textarea cols="50" rows="4" name="DiversR">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="hidden" name="ADiversR" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr><tr>';
    // Diffusion Internet commentaire
    echo '<td><label for="Diff_Internet_NoteR">' . LG_CH_COMMENT_VISIBILITY . '</label>'
        . '&nbsp;<input type="checkbox" id="Diff_Internet_NoteR" name="Diff_Internet_NoteR" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_Internet_NoteR" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    echo '</div>' . "\n";

    // Données de la fiche
    echo '<div id="pane2">' . "\n";
    // Affiche les données propres à l'enregistrement de la fiche
    $x = Affiche_Fiche($enreg2, true);
    echo '</div>' . "\n";

    echo '</div>' . "\n";        // <!-- panes -->

    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);
}

//Demande de mise à jour
if ($bt_OK) {
    // Init des zones de requête
    $req = '';
    $req_comment = '';
    $maj_site = false;
    $Type_Ref = 'R';
    if (!is_numeric($Code_Region)) $Code_Region = 0;
    // Cas de la modification
    if ($Ident != -1) {
        Aj_Zone_Req('Region', $Code_Region, $ACode_Region, 'N', $req);
        Aj_Zone_Req('Nom_region_Min', $Nom_Region, $ANom_Region, 'A', $req);
        Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $req);
        Aj_Zone_Req('Zone_Mere', $Zone_Mere, $AZone_Mere, 'N', $req);
        // Traitement des commentaires
        maj_commentaire($Ident, $Type_Ref, $DiversR, $ADiversR, $Diff_Internet_NoteR, $ADiff_Internet_NoteR);
    }
    // Cas de la création
    else {
        // On n'autorise la création que si le nom est saisi
        if ($Nom_Region != '') {
            Ins_Zone_Req($Code_Region, 'N', $req);
            Ins_Zone_Req($Nom_Region, 'A', $req);
            // Récupération de l'identifiant à positionner
            $nouv_ident = Nouvel_Identifiant('Identifiant_zone', 'regions');
        }
    }

    // Cas de la modification
    if (($Ident != -1) and ($req != '')) {
        $req = 'update ' . nom_table('regions') . ' set ' . $req .
            ',Date_Modification = current_timestamp' .
            ' where identifiant_zone  = ' . $Ident;
    }
    // Cas de la création
    if (($Ident == -1) and ($Nom_Region != '')) {
        $req = 'insert into ' . nom_table('regions') . ' values(' . $nouv_ident . ',' . $req .
            ',current_timestamp,current_timestamp';
        Ins_Zone_Req($Statut_Fiche, 'A', $req);
        Ins_Zone_Req($Zone_Mere, 'N', $req);
        $req = $req . ')';
        // Création d'un enregistrement dans la table commentaires
        if ($DiversR != '') {
            insere_commentaire($nouv_ident, $Type_Ref, $DiversR, $Diff_Internet_NoteR);
        }
    }
    // Exéution des requêtes
    if ($req != '') {
        $res = maj_sql($req);
        $maj_site = true;
    }
    // Exécution de la requête sur les commentaires
    if ($req_comment != '') {
        $res = maj_sql($req_comment);
        $maj_site = true;
    }

    if ($maj_site) maj_date_site();

    // Retour vers la page précédente
    Retour_Ar();
}

$compl = '';

// Première entrée : affichage pour saisie
if (($ok == '') && ($annuler == '')) {

    include('Insert_Tiny.js');

    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut($titre, $compl, 'Edition_Region', $Ident);
    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'Nom_Region,Code_Region\')" action="' . my_self() . '?Ident=' . $Ident . '">' . "\n";

    echo '<input type="hidden" name="Ident" value="' . $Ident . '"/>' . "\n";
    aff_origine();

    if ($Modif) {
        // Récupération des données de la région
        $sql = 'select * ' .
            ' from ' . nom_table('regions') .
            ' where Identifiant_zone = ' . $Ident . ' limit 1';

        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
        Champ_car($enreg2, 'Nom_Region_Min');
    } else {
        $enreg2['Identifiant_zone'] = 0;
        $enreg2['Region'] = 0;
        $enreg2['Nom_Region_Min'] = '';
        $enreg2['Date_Creation'] = '';
        $enreg2['Date_Modification'] = '';
        $enreg2['Statut_Fiche'] = '';
        $enreg2['Zone_Mere'] = 0;
    }

    // Affichage des données de la région
    echo '<br />';
    $x = Aff_Region($enreg2);

    echo '</div> <!-- tab container -->' . "\n";
    echo '</td></tr></table></div>' . "\n";

    echo '</form>' . "\n";

    include('gest_onglets.js');
    //echo '<!-- On cache les div d\'ajout des villes et on positionne l\'onglet par défaut -->'."\n";
    echo '<script type="text/javascript">' . "\n";
    echo '	setupPanes("container1", "tab1", 40);' . "\n";
    echo '</script>' . "\n";
}
Insere_Bas($compl);
?>
</body>

</html>