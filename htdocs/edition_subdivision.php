<?php
//=====================================================================
// Edition d'un lieu-dit (subdivision)
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
    'Nom_SubDiv',
    'ANom_SubDiv',
    'Zone_Mere',
    'AZone_Mere',
    'Latitude',
    'ALatitude',
    'Longitude',
    'ALongitude',
    'Divers',
    'ADivers',
    'Diff_Internet_Note',
    'ADiff_Internet_Note',
    'Statut_Fiche',
    'AStatut_Fiche'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

// Recup des variables passées dans l'URL : Identifiant du lieu-dit
$Ident = Recup_Variable('Ident', 'N');
$Modif = true;
if ($Ident == -1) $Modif = false;

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
if (!$Modif) $titre = $LG_Menu_Title['Subdiv_Edit'];
else $titre = $LG_Menu_Title['Subdiv_Add'];
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/../app/ressources/gestion_pages.php');


$Nom_SubDiv                = Secur_Variable_Post($Nom_SubDiv, 80, 'S');
$ANom_SubDiv            = Secur_Variable_Post($ANom_SubDiv, 80, 'S');
$Zone_Mere                = Secur_Variable_Post($Zone_Mere, 1, 'N');
$AZone_Mere                = Secur_Variable_Post($AZone_Mere, 1, 'N');
$Latitude                = Secur_Variable_Post($Latitude, 1, 'N');
$ALatitude                = Secur_Variable_Post($ALatitude, 1, 'N');
$Longitude                = Secur_Variable_Post($Longitude, 1, 'N');
$ALongitude                = Secur_Variable_Post($ALongitude, 1, 'N');
$Divers                    = Secur_Variable_Post($Divers, 65535, 'S');
$ADivers                = Secur_Variable_Post($ADivers, 65535, 'S');
$Diff_Internet_Note        = Secur_Variable_Post($Diff_Internet_Note, 1, 'S');
$ADiff_Internet_Note    = Secur_Variable_Post($ADiff_Internet_Note, 1, 'S');
$Statut_Fiche            = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$AStatut_Fiche            = Secur_Variable_Post($AStatut_Fiche, 1, 'S');

DEFINE('TYPE_OBJET', 's');
$n_subdivisions = 'subdivisions';
DEFINE('N_TABLE', nom_table($n_subdivisions));


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
        $sqlC = 'select Commentaire from ' . nom_table('commentaires') . ' where Reference_Objet = ' . $Ident . ' and Type_Objet = "' . TYPE_OBJET . '" limit 1';
        $sqlD = 'select Id_Document from ' . nom_table('concerne_doc') . ' where Reference_Objet = ' . $Ident . ' and Type_Objet = "' . TYPE_OBJET . '" limit 1';
        $sqlE = 'select Evenement from ' . nom_table('concerne_objet') . ' where Reference_Objet = ' . $Ident . ' and Type_Objet = "' . TYPE_OBJET . '" limit 1';
        $sqlE2 = 'select Reference from ' . nom_table('evenements') . ' e, ' . nom_table('niveaux_zones') . ' n ' .
            'where Identifiant_zone = ' . $Ident . ' and e.Identifiant_Niveau=n.Identifiant_Niveau and Libelle_Niveau = "Subdivision" limit 1';
        $sqlS = 'select Ident from ' . nom_table('concerne_source') . ' where Reference_Objet = ' . $Ident . ' and Type_Objet = "' . TYPE_OBJET . '" limit 1';
        $sqlI = 'select ident_image from ' . nom_table('images') . ' where Reference = ' . $Ident . ' and Type_Ref = "' . TYPE_OBJET . '" limit 1';
        $ut = exec_req($sqlC, 'Commentaires', $aff);
        $ut = exec_req($sqlD, 'Documents', $aff);
        $ut = exec_req($sqlE, 'Evènements sur un lieu-dit', $aff);
        $ut = exec_req($sqlE2, 'Lieux d\'évènements', $aff);
        $ut = exec_req($sqlS, 'Sources', $aff);
        $ut = exec_req($sqlI, 'Images', $aff);
    }
    return $utils;
}

// Affiche une subdivision
function Aff_Subdiv($enreg2)
{
    global $root, $Icones, $Images, $Ident, $Environnement, $Commentaire, $Diffusion_Commentaire_Internet, $enreg, $id_image, $largP, $lib_Okay, $lib_Annuler, $lib_Supprimer;

    $n_subdiv = $enreg['Nom_Subdivision'];
    $n_subdiv_html = $enreg2['Nom_Subdivision'];
    $n_subdiv_aff = stripslashes($n_subdiv);

    $largP = 20;

    echo '<br />';
    echo '<div id="content">' . "\n";
    echo '<table id="cols" cellpadding="0" cellspacing="0" align="center">' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="border-right:0px solid #9cb0bb">' . "\n";
    echo '  <img src="' . $root . '/assets/img/' . $Icones['clear'] . '" width="700" height="1" alt="clear"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '<tr>' . "\n";
    echo '<td class="left">' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";
    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pnl_Gen\', this)" id="tab1">' . LG_CH_DATA_TAB . '</a></li>' . "\n";
    if ($Ident != -1) {
        echo '<li><a href="#" onclick="return showPane(\'pane_Docs\', this)">' . LG_CH_DOCS . '</a></li>' . "\n";
    }
    echo '<li><a href="#" onclick="return showPane(\'pnl_Fiche\', this)">' . LG_CH_FILE . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<div class="tab-panes">' . "\n";
    // Onglet données générales
    echo '<div id="pnl_Gen">' . "\n";
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_CH_DATA_TAB) . '</legend>' . "\n";
    echo '<table width="100%">' . "\n";
    echo '<tr><td width="20%">' . LG_SUBDIV_NAME . '</td>';
    echo '<td><input class="oblig" type="text" size="50" name="Nom_SubDiv" id="Nom_SubDiv" value="' . $n_subdiv_html . '"/> ' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="hidden" name="ANom_SubDiv" value="' . $n_subdiv_html . '"/></td></tr>' . "\n";
    echo '<tr><td width="20%">' . LG_SUBDIV_TOWN . '</td>';
    echo "<td><select name='Zone_Mere'>\n";
    $sql = 'select Identifiant_zone, Nom_Ville from ' . nom_table('villes') . ' order by Nom_Ville';
    $res = lect_sql($sql);
    $enr_zone = $enreg2['Zone_Mere'];
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($enr_zone == $row[0]) echo ' selected';
        if ($row[0] == 0) echo '> ';
        else echo '>' . my_html($row[1]);
        echo '</option>' . "\n";
    }
    echo "</select>\n";
    echo '<input type="hidden" name="AZone_Mere" value="' . $enr_zone . '"/></td>' . "\n";
    echo "</tr>\n";
    $res->closeCursor();
    echo "</table>\n";
    echo '</fieldset>' . "\n";

    // Coordonnées géographiques
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_SUBDIV_GEO_COORDS) . '</legend>' . "\n";
    echo '<table width="100%">' . "\n";
    champ_carte(LG_SUBDIV_ZIP_LATITUDE, 'Latitude', $enreg2['Latitude']);
    echo '</td></tr>' . "\n";
    champ_carte(LG_SUBDIV_ZIP_LONGITUDE, 'Longitude', $enreg2['Longitude']);
    $id_image = 'carte_osm';
    echo ' <img src="' . $root . '/assets/img/' . $Icones['map_go'] . '" alt="' . LG_CALL_OPENSTREETMAP . '" title="' . LG_CALL_OPENSTREETMAP . '" onclick="apelle_carte(Latitude, Longitude);">';
    echo "</td></tr>\n";
    echo '<tr><td colspan="2">';
    echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . LG_TIP . '" title="' . LG_TIP . '"><a href="http://www.OpenStreetMap.com" target="_blank">OpenStreetMap</a></td></tr>' . "\n";
    echo '</td></tr>' . "\n";
    echo "</table>\n";
    echo '</fieldset>' . "\n";

    // Commentaires
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_CH_COMMENT) . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    //Divers
    echo '<tr>' . "\n";
    echo '<td>';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($Ident, TYPE_OBJET);
    echo '<textarea cols="50" rows="4" name="Divers">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="hidden" name="ADivers" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr><tr>';
    // Diffusion Internet commentaire
    echo '<td><label for="Diff_Internet_Note">' . LG_CH_COMMENT_VISIBILITY . '</label>'
        . ' <input type="checkbox" id="Diff_Internet_Note" name="Diff_Internet_Note" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="hidden" name="ADiff_Internet_Note" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    echo '</div>' . "\n";

    // Données de la fiche
    echo '<div id="pnl_Fiche">' . "\n";
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

    //  Sources lies à la subdivision
    if ($Ident != -1) {
        echo '<hr/>';
        $x = Aff_Sources_Objet($Ident, 'S', 'N');
        // Possibilité de lier un document pour la subdivision
        echo '<br /> ' . my_html(LG_SUBDIV_LINK_SOURCE) . ' '
            . Affiche_Icone_Lien('href="' . $root . '/edition_lier_source?refObjet=' . $Ident . '&amp;typeObjet=' . TYPE_OBJET . '&amp;refSrc=-1"', 'ajout', 'Ajout d\'une source') . "\n";
    }
    echo '</div>' . "\n";

    if ($Ident != -1) {

        //	Documents liés à la subdivision
        echo '<div id="pane_Docs">' . "\n";
        Aff_Documents_Objet($Ident, TYPE_OBJET, 'N');
        // Possibilité de lier un document pour la personne
        echo '<br />' . LG_SUBDIV_LINK_DOCUMENT . ' '
            . Affiche_Icone_Lien('href="' . $root . '/edition_lier_doc?refObjet=' . $Ident . '&amp;typeObjet=' . TYPE_OBJET . '&amp;refDoc=-1"', 'ajout', LG_SUBDIV_ADD_DOCUMENT) . "\n";
        echo '</div>' . "\n";
    }

    echo '</div>' . "\n";    // <!-- panes -->

    // Possibilité de supprimer la subdivision ?
    $lib_sup = '';
    if (($Environnement == 'L') and ($Ident != -1)) {
        if (!est_utilisee(false)) $lib_sup = $lib_Supprimer;
    }

    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_SUBDIV_THIS, false);
}

$msg_erreur = '';
// Demande de suppression
if ($bt_Sup) {
    if ($Environnement == 'L') {
        if (!est_utilisee(true)) {
            $req = 'DELETE FROM ' . N_TABLE . ' WHERE Identifiant_zone = ' . $Ident;
            $res = maj_sql($req);
            maj_date_site();
            Retour_Ar();
        } else {
            $msg_erreur .= ' ; ' . LG_SUBDIV_USED_ERR . '<br />';
        }
    }
}

//Demande de mise à jour
if ($bt_OK) {
    // Init des zones de requête
    $req = '';
    $req_comment = '';
    $maj_site = false;
    // Cas de la modification
    if ($Ident != -1) {
        Aj_Zone_Req('Nom_Subdivision', $Nom_SubDiv, $ANom_SubDiv, 'A', $req);
        Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $req);
        Aj_Zone_Req('Zone_Mere', $Zone_Mere, $AZone_Mere, 'N', $req);
        Aj_Zone_Req('Latitude', $Latitude, $ALatitude, 'N', $req);
        Aj_Zone_Req('Longitude', $Longitude, $ALongitude, 'N', $req);
        // Traitement des commentaires
        maj_commentaire($Ident, TYPE_OBJET, $Divers, $ADivers, $Diff_Internet_Note, $ADiff_Internet_Note);
    }
    // Cas de la création
    else {
        // On n'autorise la création que si le nom est saisi
        if ($Nom_SubDiv != '') {
            Ins_Zone_Req($Nom_SubDiv, 'A', $req);
            // Récupération de l'identifiant à positionner
            $nouv_ident = Nouvel_Identifiant('Identifiant_zone', $n_subdivisions);
        }
    }

    // Cas de la modification
    if (($Ident != -1) and ($req != '')) {
        $req = 'update ' . N_TABLE . ' set ' . $req .
            ',Date_Modification = current_timestamp' .
            ' where identifiant_zone  = ' . $Ident;
    }
    // Cas de la création
    if (($Ident == -1) and ($Nom_SubDiv != '')) {
        $req = 'insert into ' . N_TABLE . ' values(' . $nouv_ident . ',' . $req . ',current_timestamp,current_timestamp';
        Ins_Zone_Req($Statut_Fiche, 'A', $req);
        Ins_Zone_Req($Zone_Mere, 'N', $req);
        Ins_Zone_Req($Latitude, 'A', $req);
        Ins_Zone_Req($Longitude, 'A', $req);
        $req = $req . ')';
        // Création d'un enregistrement dans la table commentaires
        if ($Divers != '') insere_commentaire($nouv_ident, $Type_Ref, $Divers, $Diff_Internet_Note);
    }

    // Exéution de la requête création / modification de la subdivision
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
        $compl .= Affiche_Icone_Lien('href="' . $root . '/liste_images?Refer=' . $Ident . '&amp;Type_Ref=' . TYPE_OBJET . '"', 'images', 'Images') . ' ' .
            Affiche_Icone_Lien('href="' . $root . '/fiche_subdivision?Ident=' . $Ident . '"', 'page', $LG_Menu_Title['Subdiv']) . ' ';
    }

    if ($bt_Sup) Ecrit_Entete_Page($titre, $contenu, $mots);

    Insere_Haut($titre, $compl, 'Edition_Subdivision', $Ident);

    if ($msg_erreur != '') {
        echo $msg_erreur . '<br/ >';
        $msg_erreur = '';
    }

    if ($Modif) {
        // Récupération des données de la subdivision
        $sql = 'select * from ' . N_TABLE . ' where Identifiant_zone = ' . $Ident . ' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
    } else {
        $enreg['Identifiant_zone'] = 0;
        $enreg['Nom_Subdivision'] = '';
        $enreg['Date_Creation'] = '';
        $enreg['Date_Modification'] = '';
        $enreg['Statut_Fiche'] = '';
        $enreg['Zone_Mere'] = 0;
        $enreg['Latitude'] = 0;
        $enreg['Longitude'] = 0;
    }

    // Subdivision inconnue, supprimée entre temps, retour...
    if ((!$enreg) and ($Ident != -1)) {
        echo '<br/ >Subdivision supprimée<br/ >';
        echo '<a href="' . $root . '/liste_villes?Type_Liste=S">' . LG_SUBDIV_LIST . '</a>';
    } else {
        include(__DIR__ . '/../public/assets/js/Insert_Tiny.js');
        // include('/../public/assets/js/Edition_Ville.js');

        echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'Nom_SubDiv\')" action="' . my_self() . '?Ident=' . $Ident . '">' . "\n";
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";

        $enreg2 = $enreg;
        if ($Ident != -1) Champ_car($enreg2, 'Nom_Subdivision');

        // Affichage des données de la subdivision
        $x = Aff_Subdiv($enreg2);

        echo '</div>' . "\n";  //  <!-- tab container -->
        echo '</td></tr></table></div>' . "\n";

        echo '</form>' . "\n";
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
include(__DIR__ . '/../public/assets/js/gest_onglets.js');

function champ_carte($libelle, $nom_champ, $valeur)
{
    global $largP;
    echo '<tr><td width="' . $largP . '%">' . my_html($libelle) . '</td>';
    echo '<td><input type="text" size="10" name="' . $nom_champ . '" id="' . $nom_champ . '" value="' . $valeur . '" onchange="affiche_icone_carte(\'carte_osm\')"; />' . "\n";
    echo '<input type="hidden" name="A' . $nom_champ . '" value="' . $valeur . '"/>' . "\n";
}

?>

<script type="text/javascript">
    // On positionne l'onglet par défaut
    setupPanes("container1", "tab1", 40);
    affiche_icone_carte('carte_osm');

    function affiche_icone_carte(obj) {
        if ((document.getElementById('Latitude').value == 0) && (document.getElementById('Longitude').value == 0))
            document.getElementById(obj).style.display = "none";
        else
            document.getElementById(obj).style.display = "inline";
    }
</script>
</body>

</html>