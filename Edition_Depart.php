<?php
//=====================================================================
// Edition d'une fiche département
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'Nom_Depart',
    'ANom_Depart',
    'Code_Depart',
    'ACode_Depart',
    'Zone_Mere',
    'AZone_Mere',
    'DiversD',
    'ADiversD',
    'Diff_Internet_NoteD',
    'ADiff_Internet_NoteD',
    'Statut_Fiche',
    'AStatut_Fiche',
    'Horigine'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture

// Recup des variables passées dans l'URL : Identifiant du département
$Ident = Recup_Variable('Ident', 'N');
$Modif = true;
if ($Ident == -1) $Modif = false;

// Titre pour META
if ($Modif)
    $titre = $LG_Menu_Title['County_Edit'];
else
    $titre = $LG_Menu_Title['County_Add'];

$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Nom_Depart           = Secur_Variable_Post($Nom_Depart, 50, 'S');
$ANom_Depart          = Secur_Variable_Post($ANom_Depart, 50, 'S');
$Code_Depart          = Secur_Variable_Post($Code_Depart, 3, 'S');
$ACode_Depart         = Secur_Variable_Post($ACode_Depart, 3, 'S');
$Zone_Mere            = Secur_Variable_Post($Zone_Mere, 1, 'N');
$AZone_Mere           = Secur_Variable_Post($AZone_Mere, 1, 'N');
$DiversD              = Secur_Variable_Post($DiversD, 65535, 'S');
$ADiversD             = Secur_Variable_Post($ADiversD, 65535, 'S');
$Diff_Internet_NoteD  = Secur_Variable_Post($Diff_Internet_NoteD, 1, 'S');
$ADiff_Internet_NoteD = Secur_Variable_Post($ADiff_Internet_NoteD, 1, 'S');
$Statut_Fiche         = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$AStatut_Fiche        = Secur_Variable_Post($AStatut_Fiche, 1, 'S');

//Demande de mise à jour
if ($bt_OK) {
    // Init des zones de requête
    $req = '';
    $Type_Ref = 'D';
    $req_comment = '';
    $maj_site = false;

    // Cas de la modification
    if ($Ident != -1) {
        Aj_Zone_Req('Departement', $Code_Depart, $ACode_Depart, 'A', $req);
        Aj_Zone_Req('Nom_Depart_Min', $Nom_Depart, $ANom_Depart, 'A', $req);
        Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $req);
        Aj_Zone_Req('Zone_Mere', $Zone_Mere, $AZone_Mere, 'N', $req);
        // Traitement des commentaires
        maj_commentaire($Ident, $Type_Ref, $DiversD, $ADiversD, $Diff_Internet_NoteD, $ADiff_Internet_NoteD);
    }
    // Cas de la création
    else {
        // On n'autorise la création que si le nom est saisi
        if ($Nom_Depart != '') {
            Ins_Zone_Req($Code_Depart, 'A', $req);
            Ins_Zone_Req($Nom_Depart, 'A', $req);
            // Récupération de l'identifiant à positionner
            $nouv_ident = Nouvel_Identifiant('Identifiant_zone', 'departements');
        }
    }

    // Cas de la modification
    if (($Ident != -1) and ($req != '')) {
        $req = 'update ' . nom_table('departements') . ' set ' . $req .
            ',Date_Modification = current_timestamp' .
            ' where identifiant_zone  = ' . $Ident;
    }
    // Cas de la création
    if (($Ident == -1) and ($Nom_Depart != '')) {
        $req = 'insert into ' . nom_table('departements') . ' values(' . $nouv_ident . ',' . $req .
            ',current_timestamp,current_timestamp';
        Ins_Zone_Req($Statut_Fiche, 'A', $req);
        Ins_Zone_Req($Zone_Mere, 'N', $req);
        $req = $req . ')';
        // Création d'un enregistrement dans la table commentaires
        if ($DiversD != '') {
            insere_commentaire($nouv_ident, $Type_Ref, $DiversD, $Diff_Internet_NoteD);
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

    // Retour sur la page précédente
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if (($ok == '') && ($annuler == '')) {
    $compl = Ajoute_Page_Info(600, 300);
    Insere_Haut(my_html($titre), $compl, 'Edition_Depart', $Ident);

    if ($Modif) {
        // Récupération des données du département
        $sql = 'select * ' .
            ' from ' . nom_table('departements') .
            ' where Identifiant_zone = ' . $Ident . ' limit 1';

        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
    } else {
        $enreg2['Identifiant_zone'] = 0;
        $enreg2['Departement'] = '';
        $enreg2['Nom_Depart_Min'] = '';
        $enreg2['Date_Creation'] = '';
        $enreg2['Date_Modification'] = '';
        $enreg2['Statut_Fiche'] = '';
        $enreg2['Zone_Mere'] = 0;
    }

    // ville inconnue, supprimée entre temps, retour...
    if ((!$enreg) and ($Ident != -1)) {
        aff_erreur(LG_DEPART_UNKNOWN);
        echo '<a href="Liste_Villes.php?Type_Liste=D">' . LG_LAREAS_COUNTIES . '</a>';
    } else {
        if ($Modif)
            Champ_car($enreg2, 'Nom_Depart_Min');
        // Affichage des données du département
        include(__DIR__ . '/assets/js/Insert_Tiny.js');
        echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'Nom_Depart,Code_Depart\')" action="' . my_self() . '?Ident=' . $Ident . '">' . "\n";
        echo '<input type="' . $hidden . '" name="Ident" value="' . $Ident . '"/>';

        $largP = 10;

        echo '<br />';
        echo '<div id="content">' . "\n";
        echo '<table id="cols" border="0" cellpadding="0" cellspacing="0" align="center">' . "\n";
        echo '<tr>' . "\n";
        echo '<td style="border-right:0px solid #9cb0bb">' . "\n";
        echo '  <img src="' . $chemin_images . $Images['clear'] . '" width="600" height="1" alt="clear"/>' . "\n";
        echo '</td></tr>' . "\n";
        echo '<tr>' . "\n";
        echo '<td class="left">' . "\n";
        echo '<div class="tab-container" id="container1">' . "\n";
        // Onglets
        echo '<ul class="tabs">' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pane1\', this)" id="tab1">' . my_html($LG_Data_tab) . '</a></li>' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pane2\', this)">' . my_html($LG_record) . '</a></li>' . "\n";
        echo '</ul>' . "\n";

        echo '<div class="tab-panes">' . "\n";
        // Onglets données générales
        echo '<div id="pane1">' . "\n";
        echo '<fieldset>' . "\n";
        echo '<legend>' . my_html($LG_County_Data) . '</legend>' . "\n";

        echo '<table width="100%" border="0">' . "\n";
        col_titre_tab_noClass($LG_County_Name, $largP);
        echo '<td><input type="text" size="70" class="oblig" name="Nom_Depart" id="Nom_Depart" value="' . $enreg2['Nom_Depart_Min'] . '" />&nbsp;' . "\n";
        Img_Zone_Oblig('imgObligNom');
        echo '<input type="' . $hidden . '" name="ANom_Depart" value="' . $enreg2['Nom_Depart_Min'] . '"/></td>' . "\n";
        echo "</tr>\n";

        col_titre_tab_noClass($LG_County_Id, $largP);
        echo '<td><input type="text" size="3" class="oblig" name="Code_Depart" id="Code_Depart" value="' . $enreg2['Departement'] . '" />&nbsp;' . "\n";
        Img_Zone_Oblig('imgObligCode');
        echo '<input type="' . $hidden . '" name="ACode_Depart" value="' . $enreg2['Departement'] . '"/></td>' . "\n";
        echo "</tr>\n";

        col_titre_tab_noClass($LG_County_Region, $largP);
        echo "<td><select name='Zone_Mere'>\n";
        $sql = 'select Identifiant_zone, Nom_Region_Min from ' . nom_table('regions') . ' order by Nom_Region_Min';
        $res = lect_sql($sql);
        $enr_zone = $enreg2['Zone_Mere'];
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            echo '<option value="' . $row[0] . '"';
            if ($enr_zone == $row[0]) echo ' selected="selected" ';
            if ($row[0] == 0) echo '>&nbsp;';
            else echo '>' . my_html($row[1]);
            echo '</option>' . "\n";
        }
        echo '</select>' . "\n";
        echo '<input type="' . $hidden . '" name="AZone_Mere" value="' . $enreg2["Zone_Mere"] . '"/>';
        echo '</td></tr>' . "\n";
        echo '</table>' . "\n";
        $res->closeCursor();
        echo '</fieldset>' . "\n";

        // Commentaires
        echo '<fieldset>' . "\n";
        aff_legend(LG_CH_COMMENT);
        echo '<table width="95%" border="0">' . "\n";
        //Divers
        echo '<tr>' . "\n";
        echo '<td>';
        // Accès au commentaire
        $Existe_Commentaire = Rech_Commentaire($Ident, 'D');
        echo '<textarea cols="50" rows="4" name="DiversD">' . $Commentaire . '</textarea>' . "\n";
        echo '<input type="' . $hidden . '" name="ADiversD" value="' . my_html($Commentaire) . '"/>';
        echo '</td></tr><tr>';
        // Diffusion Internet commentaire
        echo '<td><label for="Diff_Internet_NoteD">' . LG_CH_COMMENT_VISIBILITY . '</label>'
            . '&nbsp;<input type="checkbox" id="Diff_Internet_NoteD" name="Diff_Internet_NoteD" value="O"';
        if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
        echo "/>\n";
        echo '<input type="' . $hidden . '" name="ADiff_Internet_NoteD" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
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

        echo '</div>' . "\n";  //<!-- panes -->

        bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);

        aff_origine();

        echo '</div> <!-- tab container -->' . "\n";
        echo '</td></tr>';

        echo '</table></div>' . "\n";

        echo '</form>' . "\n";

        include(__DIR__ . '/assets/js/gest_onglets.js');
    }
    // On cache les div d'ajout des villes et on positionne l'onglet par défaut
    echo '<script type="text/javascript">' . "\n";
    echo '	setupPanes("container1", "tab1", 40);' . "\n";
    echo '</script>' . "\n";

    Insere_Bas($compl);
}
?>

</body>

</html>