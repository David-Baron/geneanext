<?php
//=====================================================================
// Edition d'une ville
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'Nom_VilleV',
    'ANom_VilleV',
    'Code_PostalV',
    'ACode_PostalV',
    'Zone_MereV',
    'AZone_MereV',
    'Latitude',
    'ALatitude',
    'Longitude',
    'ALongitude',
    'DiversV',
    'ADiversV',
    'Diff_Internet_NoteV',
    'ADiff_Internet_NoteV',
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

// Recup des variables passées dans l'URL : Identifiant de la ville
$Ident = Recup_Variable('Ident', 'N');
$Modif = true;
if ($Ident == -1) $Modif = false;

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
if ($Modif)
    $titre = $LG_Menu_Title['Town_Edit'];
else
    $titre = $LG_Menu_Title['Town_Add'];
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Nom_VilleV           = Secur_Variable_Post($Nom_VilleV, 80, 'S');
$ANom_VilleV          = Secur_Variable_Post($ANom_VilleV, 80, 'S');
$Code_PostalV         = Secur_Variable_Post($Code_PostalV, 25, 'S');
$ACode_PostalV        = Secur_Variable_Post($ACode_PostalV, 10, 'S');
$Zone_MereV           = Secur_Variable_Post($Zone_MereV, 1, 'N');
$AZone_MereV          = Secur_Variable_Post($AZone_MereV, 1, 'N');
$Latitude            = Secur_Variable_Post($Latitude, 1, 'N');
$ALatitude           = Secur_Variable_Post($ALatitude, 1, 'N');
$Longitude           = Secur_Variable_Post($Longitude, 1, 'N');
$ALongitude          = Secur_Variable_Post($ALongitude, 1, 'N');
$DiversV              = Secur_Variable_Post($DiversV, 65535, 'S');
$ADiversV             = Secur_Variable_Post($ADiversV, 65535, 'S');
$Diff_Internet_NoteV  = Secur_Variable_Post($Diff_Internet_NoteV, 1, 'S');
$ADiff_Internet_NoteV = Secur_Variable_Post($ADiff_Internet_NoteV, 1, 'S');
$Statut_Fiche         = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$AStatut_Fiche        = Secur_Variable_Post($AStatut_Fiche, 1, 'S');

// Exécute la requête et s'il n'y a pas d'utilisations préalables
function exec_req($req, $lib, $aff)
{
    global $utils, $msg_erreur;
    $ret = 0;
    if (!$utils) {
        $res = lect_sql($req);
        $ret = $res->fetch(PDO::FETCH_NUM);
        if ($ret) {
            $utils = true;
            if ($aff)
                $msg_erreur .= my_html($lib . ' : utilisation(s) ; autres utilisations non recherchées');
        }
    }
    return $ret;
}

function est_utilisee($aff)
{
    global $utils, $Ident, $Environnement;
    $utils = false;
    if ($Environnement == 'L') {
        // requêtes de contrôle
        $sqlP = 'select Reference from ' . nom_table('personnes') . ' where Ville_Naissance = ' . $Ident . ' or Ville_Deces = ' . $Ident . ' limit 1';
        $sqlV = 'select Reference from ' . nom_table('unions') . ' where Ville_Mariage = ' . $Ident . ' or Ville_Notaire = ' . $Ident . ' limit 1';
        $sqlC = 'select Commentaire from ' . nom_table('commentaires') . ' where Reference_Objet = ' . $Ident . ' and Type_Objet = "V" limit 1';
        $sqlD = 'select Id_Document from ' . nom_table('concerne_doc') . ' where Reference_Objet = ' . $Ident . ' and Type_Objet = "V" limit 1';
        $sqlE = 'select Evenement from ' . nom_table('concerne_objet') . ' where Reference_Objet = ' . $Ident . ' and Type_Objet = "V" limit 1';
        $sqlE2 = 'select Reference from ' . nom_table('evenements') . ' e, ' . nom_table('niveaux_zones') . ' n ' .
            'where Identifiant_zone = ' . $Ident . ' and e.Identifiant_Niveau=n.Identifiant_Niveau and Libelle_Niveau = "Ville" limit 1';
        $sqlS = 'select Ident from ' . nom_table('concerne_source') . ' where Reference_Objet = ' . $Ident . ' and Type_Objet = "V" limit 1';
        $sqlI = 'select ident_image from ' . nom_table('images') . ' where Reference = ' . $Ident . ' and Type_Ref = "V" limit 1';
        $ut = exec_req($sqlP, 'Ville de naissance ou de décès', $aff);
        $ut = exec_req($sqlV, 'Ville de mariage ou de contrat de mariage', $aff);
        $ut = exec_req($sqlC, 'Commentaires', $aff);
        $ut = exec_req($sqlD, 'Documents', $aff);
        $ut = exec_req($sqlE, 'Evènements sur une ville', $aff);
        $ut = exec_req($sqlE2, 'Lieux d\'évènements', $aff);
        $ut = exec_req($sqlS, 'Sources', $aff);
        $ut = exec_req($sqlI, 'Images', $aff);
    }
    return $utils;
}

// Affiche une ville
function Aff_Ville($enreg2)
{
    global $root, $Icones, $Images, $Ident, $Environnement, $Commentaire, $Diffusion_Commentaire_Internet, $enreg, $est_gestionnaire, $id_image, $largP, $debug, $lib_Okay, $lib_Annuler, $lib_Supprimer;

    $n_ville = $enreg['Nom_Ville'];
    $n_ville_html = $enreg2['Nom_Ville'];
    $n_ville_aff = stripslashes($n_ville);

    $largP = 20;

    echo '<br>';
    echo '<div id="content">' . "\n";
    echo '<table id="cols" border="0" cellpadding="0" cellspacing="0" align="center">' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="border-right:0px solid #9cb0bb">' . "\n";
    echo '  <img src="' . $root . '/assets/img/' . $Icones['clear'] . '" width="700" height="1" alt="clear"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr>' . "\n";
    echo '<td class="left">' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";
    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pnl_Gen\', this)" id="tab1">' . my_html(LG_CH_DATA_TAB) . '</a></li>' . "\n";
    if ($Ident != -1) {
        echo '<li><a href="#" onclick="return showPane(\'pnl_Listes\', this)">' . my_html('Listes pour la ville') . '</a></li>' . "\n";
        echo '<li><a href="#" onclick="return showPane(\'pnl_Docs\', this)">' . my_html(LG_CH_DOCS) . '</a></li>' . "\n";
    }
    echo '<li><a href="#" onclick="return showPane(\'pnl_Fiche\', this)">' . my_html(LG_CH_FILE) . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<div class="tab-panes">' . "\n";
    // Onglet données générales
    echo '<div id="pnl_Gen">' . "\n";
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_CH_DATA_TAB) . '</legend>' . "\n";
    echo '<table width="100%" border="0">' . "\n";
    echo '<tr><td width="20%">' . LG_ICSV_TOWN_NAME . '</td>';
    echo '<td><input class="oblig" type="text" size="50" name="Nom_VilleV" id="Nom_VilleV" value="' . $n_ville_html . '"/> ' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="hidden" name="ANom_VilleV" value="' . $n_ville_html . '"/></td></tr>' . "\n";
    echo '<tr><td width="20%">' . LG_ICSV_TOWN_ZIP_CODE . '</td>';
    echo '<td><input type="text" size="10" name="Code_PostalV" value="' . $enreg2["Code_Postal"] . '"/>' . "\n";
    echo '<input type="hidden" name="ACode_PostalV" value="' . $enreg2["Code_Postal"] . '"/></td></tr>' . "\n";
    echo '<tr><td width="20%">' . LG_COUNTY . '</td>';
    echo "<td><select name='Zone_MereV'>\n";
    $sql = 'select Identifiant_zone, Nom_Depart_Min from ' . nom_table('departements') . ' order by Nom_Depart_Min';
    $res = lect_sql($sql);
    $enr_zone = $enreg2['Zone_Mere'];
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($enr_zone == $row[0]) echo ' selected="selected" ';
        if ($row[0] == 0) echo '> ';
        else echo '>' . my_html($row[1]);
        echo '</option>' . "\n";
    }
    echo "</select>\n";
    echo '<input type="hidden" name="AZone_MereV" value="' . $enr_zone . '"/></td>' . "\n";
    echo "</tr>\n";
    $res->closeCursor();
    echo "</table>\n";
    echo '</fieldset>' . "\n";

    // Coordonnées géographiques
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_ICSV_TOWN_GEO_COORDS) . '</legend>' . "\n";
    echo '<table width="100%" border="0">' . "\n";
    champ_carte(LG_ICSV_TOWN_ZIP_LATITUDE, 'Latitude', $enreg2['Latitude']);
    echo '</td></tr>' . "\n";
    champ_carte(LG_ICSV_TOWN_ZIP_LONGITUDE, 'Longitude', $enreg2['Longitude']);
    $id_image = 'carte_osm';
    echo ' <img src="' . $root . '/assets/img/' . $Icones['map_go'] . '" alt="' . LG_CALL_OPENSTREETMAP . '" title="' . LG_CALL_OPENSTREETMAP . '" onclick="apelle_carte(Latitude, Longitude)">';
    echo "</td></tr>\n";
    echo '<tr><td colspan="2">';
    echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . LG_TIP . '" title="' . LG_TIP . '"><a href="http://www.OpenStreetMap.com" target="_blank">OpenStreetMap</a></td></tr>' . "\n";
    echo '</td></tr>' . "\n";
    echo "</table>\n";
    echo '</fieldset>' . "\n";

    // Commentaire
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_CH_COMMENT) . '</legend>' . "\n";
    echo '<table width="95%" border="0">' . "\n";
    //Divers
    echo '<tr>' . "\n";
    echo '<td>';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Ident, 'V');
    echo '<textarea cols="50" rows="4" name="DiversV">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="hidden" name="ADiversV" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr><tr>';
    // Diffusion Internet commentaire
    echo '<td><label for="Diff_Internet_NoteV">' . LG_CH_COMMENT_VISIBILITY . '</label>'
        . ' <input type="checkbox" id="Diff_Internet_NoteV" name="Diff_Internet_NoteV" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_Internet_NoteV" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    echo '</div>' . "\n";

    // Données de la fiche
    echo '<div id="pnl_Fiche">' . "\n";
    // Affiche les données propres à l'enregistrement de la fiche
    $x = Affiche_Fiche($enreg2, true);

    //  Sources lies à la ville
    if ($Ident != -1) {
        echo '<hr/>';
        $x = Aff_Sources_Objet($Ident, 'V', 'N');
        // Possibilité de lier un document pour la ville
        echo '<br> ' . my_html(LG_ICSV_TOWN_LINK_SOURCE) . LG_SEMIC
            . Affiche_Icone_Lien('href="' . $root . '/edition_lier_source.php?refObjet=' . $Ident . '&amp;typeObjet=V&amp;refSrc=-1"', 'ajout', 'Ajout d\'une source') . "\n";
    }
    echo '</div>' . "\n";

    if ($Ident != -1) {
        // Liste des personnes sur la ville
        echo '<div id="pnl_Listes">' . "\n";
        echo '<br>';

        $deb_lien_visu = '<a href="' . $root . '/liste_pers2.php?Type_Liste=';
        $deb_lien_crea = 'href="' . $root . '/edition_personnes_ville.php?evt=';
        $fin_lien = '&amp;idNom=' . $Ident . '&amp;Nom=' . $n_ville . '"';

        echo '<br>';
        echo $deb_lien_visu . 'N' . $fin_lien . '>' . LG_LPERS_OBJ_N . ' ' . $n_ville_html . '</a>';
        if ($est_gestionnaire) echo ' ' . Affiche_Icone_Lien($deb_lien_crea . 'N' . $fin_lien, 'ajouter', LG_ICSV_TOWN_PERS_BORN_CREATE . $n_ville_aff);
        echo '<br>';
        echo $deb_lien_visu . 'M' . $fin_lien . '>' . LG_LPERS_OBJ_M . ' ' . $n_ville_html . '</a><br>';
        echo $deb_lien_visu . 'K' . $fin_lien . '>' . LG_LPERS_OBJ_K . ' ' . $n_ville_html . '</a><br>';
        echo $deb_lien_visu . 'D' . $fin_lien . '>' . LG_LPERS_OBJ_D . ' ' . $n_ville_html . '</a>';
        if ($est_gestionnaire) echo ' ' . Affiche_Icone_Lien($deb_lien_crea . 'D' . $fin_lien, 'ajouter', LG_ICSV_TOWN_PERS_DEAD_CREATE . $n_ville_aff);

        echo '</div>' . "\n";

        //	Documents liés à la ville
        echo '<div id="pnl_Docs">' . "\n";
        Aff_Documents_Objet($Ident, 'V', 'N');
        // Possibilité de lier un document pour la personne
        echo '<br>' . LG_ICSV_TOWN_LINK_DOCUMENT . LG_SEMIC
            . Affiche_Icone_Lien('href="' . $root . '/edition_lier_doc.php?refObjet=' . $Ident . '&amp;typeObjet=V&amp;refDoc=-1"', 'ajout', LG_ICSV_TOWN_ADD_DOCUMENT) . "\n";
        echo '</div>' . "\n";
    }

    echo '</div>' . "\n";    // <!-- panes -->

    // Possibilité de supprimer la ville ?
    $lib_sup = '';
    if (($Environnement == 'L') and ($Ident != -1)) {
        if (!est_utilisee(false)) $lib_sup = $lib_Supprimer;
    }

    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_ICSV_TOWN_THIS, false);
}

$msg_erreur = '';
// Demande de suppression
if ($bt_Sup) {
    if ($Environnement == 'L') {
        if (!est_utilisee(true)) {
            $req = 'DELETE FROM ' . nom_table('villes') . ' WHERE Identifiant_zone = ' . $Ident;
            $res = maj_sql($req);
            maj_date_site();
            Retour_Ar();
        } else {
            $msg_erreur .= ' ; ' . LG_ICSV_TOWN_USED_ERR . '<br>';
        }
    }
}

//Demande de mise à jour
if ($bt_OK) {
    // Init des zones de requête
    $req = '';
    $Type_Ref = 'V';
    $req_comment = '';
    $maj_site = false;
    // Cas de la modification
    if ($Ident != -1) {
        Aj_Zone_Req('Nom_Ville', $Nom_VilleV, $ANom_VilleV, 'A', $req);
        Aj_Zone_Req('Code_Postal', $Code_PostalV, $ACode_PostalV, 'A', $req);
        Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $req);
        Aj_Zone_Req('Zone_Mere', $Zone_MereV, $AZone_MereV, 'N', $req);
        Aj_Zone_Req('Latitude', $Latitude, $ALatitude, 'N', $req);
        Aj_Zone_Req('Longitude', $Longitude, $ALongitude, 'N', $req);
        // Traitement des commentaires
        maj_commentaire($Ident, $Type_Ref, $DiversV, $ADiversV, $Diff_Internet_NoteV, $ADiff_Internet_NoteV);
    }
    // Cas de la création
    else {
        // On n'autorise la création que si le nom est saisi
        if ($Nom_VilleV != '') {
            Ins_Zone_Req($Nom_VilleV, 'A', $req);
            Ins_Zone_Req($Code_PostalV, 'A', $req);
            // Récupération de l'identifiant à positionner
            $nouv_ident = Nouvel_Identifiant('Identifiant_zone', 'villes');
        }
    }

    // Cas de la modification
    if (($Ident != -1) and ($req != '')) {
        $req = 'update ' . nom_table('villes') . ' set ' . $req .
            ',Date_Modification = current_timestamp' .
            ' where identifiant_zone  = ' . $Ident;
    }
    // Cas de la création
    if (($Ident == -1) and ($Nom_VilleV != '')) {
        $req = 'insert into ' . nom_table('villes') . ' values(' . $nouv_ident . ',' . $req .
            ',current_timestamp,current_timestamp';
        Ins_Zone_Req($Statut_Fiche, 'A', $req);
        Ins_Zone_Req($Zone_MereV, 'N', $req);
        Ins_Zone_Req($Latitude, 'A', $req);
        Ins_Zone_Req($Longitude, 'A', $req);
        $req = $req . ')';
        // Création d'un enregistrement dans la table commentaires
        if ($DiversV != '') insere_commentaire($nouv_ident, $Type_Ref, $DiversV, $Diff_Internet_NoteV);
    }

    // Exéution de la requête création / modification de la ville
    if ($req != '') {
        $res = maj_sql($req);
        $maj_site = true;
    }

    // Exécution de la requête sur les commentaires
    if ($req_comment != '') {
        $res = maj_sql($req_comment);
        $maj_site = true;
    }

    if ($maj_site) maj_date_site(true);
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if (($ok == '') && ($annuler == '')) {

    $compl = Ajoute_Page_Info(600, 150);
    if ($Ident != -1) {
        $compl .= Affiche_Icone_Lien('href="' . $root . '/liste_images.php?Refer=' . $Ident . '&amp;Type_Ref=V"', 'images', 'Images') . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/fiche_ville.php?Ident=' . $Ident . '"', 'page', 'Fiche ville') . ' ';
    }

    if ($bt_Sup) Ecrit_Entete_Page($titre, $contenu, $mots);

    Insere_Haut($titre, $compl, 'Edition_Ville', $Ident);

    if ($msg_erreur != '') {
        echo $msg_erreur . '<br>';
        $msg_erreur = '';
    }

    if ($Modif) {
        // Récupération des données de la ville
        $sql = 'select * from ' . nom_table('villes') . ' where Identifiant_zone = ' . $Ident . ' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
    } else {
        $enreg['Identifiant_zone'] = 0;
        $enreg['Nom_Ville'] = '';
        $enreg['Code_Postal'] = '';
        $enreg['Date_Creation'] = '';
        $enreg['Date_Modification'] = '';
        $enreg['Statut_Fiche'] = '';
        $enreg['Zone_Mere'] = 0;
        $enreg['Latitude'] = 0;
        $enreg['Longitude'] = 0;
    }

    // ville inconnue, supprimée entre temps, retour...
    if ((!$enreg) and ($Ident != -1)) {
        echo '<center><font color="red"><br><br><br><h2>' . LG_ICSV_TOWN_UNKNOWN . '</h2></font></center>';
        echo '<a href="' . $root . '/liste_villes.php?Type_Liste=V">' . LG_SUBDIV_LIST . '</a>';
    } else {
        include(__DIR__ . '/assets/js/Insert_Tiny.js');

        echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'Nom_VilleV\')" action="' . my_self() . '?Ident=' . $Ident . '">' . "\n";
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";

        $enreg2 = $enreg;
        if ($Ident != -1) Champ_car($enreg2, 'Nom_Ville');

        // Affichage des données de la ville
        $x = Aff_Ville($enreg2);

        echo '</div>' . "\n";  //  <!-- tab container -->
        echo '</td></tr></table></div>' . "\n";

        echo '</form>' . "\n";

        include(__DIR__ . '/assets/js/gest_onglets.js');
        // On cache les div d'ajout des villes et on positionne l'onglet par défaut
        echo '<script type="text/javascript">' . "\n";
        echo 'function affiche_icone_carte(obj) {' . "\n";
        echo 'if ((document.getElementById(\'Latitude\').value == 0) && (document.getElementById(\'Longitude\').value == 0))' . "\n";
        echo '	document.getElementById(obj).style.display = "none";' . "\n";
        echo 'else' . "\n";
        echo '	document.getElementById(obj).style.display = "inline";' . "\n";
        echo '}' . "\n";
        echo '	setupPanes("container1", "tab1", 40);' . "\n";
        echo '	affiche_icone_carte(\'carte_osm\');' . "\n";
        echo '</script>' . "\n";
    }
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
}

function champ_carte($libelle, $nom_champ, $valeur)
{
    global $largP;
    echo '<tr><td width="' . $largP . '%">' . my_html($libelle) . '</td>';
    echo '<td><input type="text" size="10" name="' . $nom_champ . '" id="' . $nom_champ . '" value="' . $valeur . '" onchange="affiche_icone_carte(\'carte_osm\')"; />' . "\n";
    echo '<input type="hidden" name="A' . $nom_champ . '" value="' . $valeur . '"/>' . "\n";
}

?>

</body>

</html>