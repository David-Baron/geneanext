<?php
//=====================================================================
// Edition d'un document
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'NatureDoc',
    'ANatureDoc',
    'TitreDoc',
    'ATitreDoc',
    'TypeDoc',
    'ATypeDoc',
    'NomFic',
    'ANomFic',
    'Diff',
    'ADiff',
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
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// Recup des variables passées dans l'URL : identifiant, référence et type de référence
$Reference = Recup_Variable('Reference', 'N');
if (!$Reference) $Reference = -1;
$Modif = true;
if ($Reference == -1) $Modif = false;

// En cas de création du lien en même temps
$refObjet = Recup_Variable('refObjet', 'N');            // Objet concerné
$typeObjet = Recup_Variable('typeObjet', 'S');          // Type d'objet concerné

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
if (!$Modif)
    $titre = $LG_Menu_Title['Document_Add'];
else
    $titre = $LG_Menu_Title['Document_Edit'];
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$NatureDoc  = Secur_Variable_Post($NatureDoc, 3, 'S');
$ANatureDoc = Secur_Variable_Post($ATitreDoc, 3, 'S');
$TitreDoc  = Secur_Variable_Post($TitreDoc, 80, 'S');
$ATitreDoc = Secur_Variable_Post($ATitreDoc, 80, 'S');
$NonFic    = Secur_Variable_Post($NomFic, 160, 'S');
$ANomFic   = Secur_Variable_Post($ANomFic, 160, 'S');
$Diff      = Secur_Variable_Post($Diff, 1, 'S');
$ADiff     = Secur_Variable_Post($ADiff, 1, 'S');
$TypeDoc   = Secur_Variable_Post($TypeDoc, 80, 'S');
$ATypeDoc  = Secur_Variable_Post($ATypeDoc, 80, 'S');

if ($Diff === '')  $Diff = 'N';
if ($ADiff === '') $ADiff = 'N';

// Affiche un document
function Aff_Formulaire()
{
    global $root, $Icones, $chemin_document, $Reference, $db, $Diff, $ADiff, $TypeDoc, $ATypeDoc, $TitreDoc, $ATitreDoc,
        $LG_Docs_Title, $LG_Docs_File, $LG_Docs_Doc_Type, $LG_show_on_internet,
        $hidden, $larg_titre,
        $NomFic, $ANomFic, $err_td;

    $larg_titre = '25';

    echo '<tr><td colspan="2">&nbsp;</td></tr>';

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Docs_Title . '&nbsp;</td><td class="value">';
    echo '<input type="text" size="80" name="TitreDoc" id="Titre" value="' . $TitreDoc . '" class="oblig"/>&nbsp;' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="' . $hidden . '" name="ATitreDoc" value="' . $ATitreDoc . '"/>';
    echo '</td>';
    echo '<td align="center" rowspan="4">';
    echo '</td></tr>' . "\n";

    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Docs_File . '&nbsp;</td><td class="value">';
    if ($NomFic != '') echo 'Fichier = ' . $NomFic . "<br />\n";
    echo '<input type="file" name="nom_du_fichier" value="' . $NomFic . '" class="oblig" size="65"/>&nbsp;' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '<input type="' . $hidden . '" name="ANomFic" value="' . $ANomFic . '"/>';
    echo '</td></tr>' . "\n";

    //	Type de document
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Docs_Doc_Type . '&nbsp;</td><td class="value">';
    $req = 'SELECT * FROM ' . nom_table('types_doc') . ' ORDER BY Libelle_Type';
    $result = lect_sql($req);
    $err_td = false;
    if ($result->rowCount() == 0) {
        echo 'Vous n\'avez pas cr&eacute;&eacute; de type de document<br />' . "\n";
        // On va masquer le bouton OK car pas de création possible
        $err_td = true;
        return;
    }
    echo '<select name="TypeDoc">';
    while ($enr_type = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $enr_type['Id_Type_Document'] . '"';
        if ($Reference != -1) {
            if ($enr_type['Id_Type_Document'] == $TypeDoc) echo ' selected="selected"';
        }
        echo ' >' . $enr_type['Libelle_Type'] . '</option>';
    }
    echo '</select>';
    echo '<input type="' . $hidden . '" name="ATypeDoc" value="' . $ATypeDoc . '"/>';
    echo '</td></tr>' . "\n";

    // Diffusion Internet
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_show_on_internet . '&nbsp;</td><td class="value">';
    echo '<input type="checkbox" name="Diff" value="O"';
    if ($Diff == 'O') echo ' checked="checked"';
    echo '/><input type="' . $hidden . '" name="ADiff" value="' . $ADiff . '"/>';
    echo '</td></tr>' . "\n";
}

if ($bt_Sup) {
    // Suppression du document
    $req = 'delete from ' . nom_table('documents') . ' where id_document = ' . $Reference;
    $res = maj_sql($req);
    $nomComplet = get_chemin_docu($NatureDoc) . $ANomFic;
    unlink($nomComplet);
    maj_date_site();
    Retour_Ar();
}

//	Demande de mise à jour
if ($bt_OK) {

    $erreur = '';
    //	Nom du fichier
    $NomFic = $_FILES['nom_du_fichier']['name'];

    // Une demande de chargement a été faite
    if ($NomFic != '') {

        // Contrôle du type de fichier
        $NatureDoc = get_file_type($NomFic, $_FILES['nom_du_fichier']['type']);
        // Type inconnu, erreur !
        if ($NatureDoc == '') $erreur = "Impossible de d&eacute;terminer le type du fichier !";

        // Contrôles complémentaires en fonction du type
        // Pas de contrôle complémentaire sur les PDF...
        if ($erreur == '') {
            switch ($NatureDoc) {
                case 'IMG':
                    $erreur = Controle_Charg_Image();
                    break;
                case 'HTM':
                    $erreur = Controle_Charg_Doc();
                    break;
                case 'TXT':
                    $erreur = Controle_Charg_Doc();
                    break;
            }
            $chemin_docu = get_chemin_docu($NatureDoc);
        }

        // On peut télécharger
        if ($erreur == '') {
            // Téléchargement du fichier après contrôle
            if (!ctrl_fichier_ko()) {
                $NomFic = nettoye_nom_fic($NomFic);
                $nomComplet =  $chemin_docu . $NomFic;
                if (!move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $nomComplet)) {
                    $erreur = 'Impossible de placer le fichier dans le bon r&eacute;pertoire';
                }
                // On chmod le fichier si on n'est pas sous Windows
                if (substr(php_uname(), 0, 7) != 'Windows') chmod($nomComplet, 0644);
            } else $erreur = '-'; // ==> pas de maj en base en cas d'erreur
        }
    }

    // Init des zones de requête
    $req = '';
    $maj_site = false;

    // Erreur constatée sur le chargement
    if ($erreur != '') {
        $_SESSION['message'] = $erreur;
        $image = 'error.png';
        echo '<img src="' . $chemin_images . $image . '" BORDER=0 alt="' . $image . '" title="' . $image . '">';
        echo '&nbsp;Erreur : ' . $erreur . '<br />';
    } else {
        // Cas de la modification
        if ($Reference != -1) {
            // Pas de demande de chargement ==> on simule "pas de modif de la zone"
            if ($NomFic == '')     $NomFic = $ANomFic;

            Aj_Zone_Req('Nature_Document', $NatureDoc, $ANatureDoc, 'A', $req);
            Aj_Zone_Req('Titre', $TitreDoc, $ATitreDoc, 'A', $req);
            Aj_Zone_Req('Nom_Fichier', $NomFic, $ANomFic, 'A', $req);
            Aj_Zone_Req('Diff_Internet', $Diff, $ADiff, 'A', $req);
            Aj_Zone_Req('Id_Type_Document', $TypeDoc, $ATypeDoc, 'N', $req);
            if ($req != '') {
                $req = 'update ' . nom_table('documents') . ' set ' . $req .
                    ',Date_Modification = current_timestamp' .
                    ' where id_document = ' . $Reference;
            }
        }
        // Cas de la création
        else {
            // On n'autorise la création que si le nom et la description sont saisis
            if (($TitreDoc != '') and ($NomFic != '')) {
                $req = 'NULL,"' . $NatureDoc . '"';
                Ins_Zone_Req($TitreDoc, 'A', $req);
                Ins_Zone_Req($NomFic, 'A', $req);
                Ins_Zone_Req($Diff, 'A', $req);
                Ins_Zone_Req('current_timestamp', 'N', $req);
                Ins_Zone_Req('current_timestamp', 'N', $req);
                Ins_Zone_Req($TypeDoc, 'N', $req);
                if ($req != '') {
                    $req = 'insert into ' . nom_table('documents') . ' values(' . $req . ")";
                }
            }
        }
    }

    // Exéution des requêtes
    if ($req != '') {
        $res = maj_sql($req);
        $maj_site = true;
    }

    // Demande de création du lien en même temps
    if (($refObjet) and ($maj_site)) {
        $req = 'insert into ' . nom_table('concerne_doc') . ' values(LAST_INSERT_ID(),' . $refObjet . ',"' . $typeObjet . '","N")';
        $result = maj_sql($req);
    }

    if ($maj_site) maj_date_site();

    // Retour sur la page précédente
    if ($erreur == '')
        Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {
    include(__DIR__ . '/assets/js/Insert_Tiny.js');
    include(__DIR__ . '/assets/js/Edition_Document.js');

    $compl = Ajoute_Page_Info(600, 20);

    if ($Reference != -1)
        $compl .= Affiche_Icone_Lien('href="' . $root . '/fiche_document.php?Reference=' . $Reference . '"', 'page', my_html($LG_Menu_Title['Document'])) . '&nbsp;';

    Insere_Haut(my_html($titre), $compl, 'Edition_Document', $Reference);

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this)" enctype="multipart/form-data" action="' . my_self() . '?' . Query_Str() . '">' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";

    if ($Modif) {
        $sql = 'select * from ' . nom_table('documents') . ' where id_document = ' . $Reference . ' limit 1';
        $res = lect_sql($sql);
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $enreg2 = $enreg;
        Champ_car($enreg2, 'Titre');
    } else {
        $enreg2['Id_Document'] = 0;
        $enreg2['Nature_Document'] = '';
        $enreg2['Titre'] = '';
        $enreg2['URL'] = '';
        $enreg2['Nom_Fichier'] = '';
        $enreg2['Diff_Internet'] = '';
        $enreg2['Date_Creation'] = '';
        $enreg2['Date_Modification'] = '';
        $enreg2['Id_Type_Document'] = 0;
    }

    $TitreDoc   = $enreg2['Titre'];
    $ATitreDoc  = $TitreDoc;
    $TypeDoc    = $enreg2['Id_Type_Document'];
    $ATypeDoc   = $TypeDoc;
    $NomFic     = $enreg2['Nom_Fichier'];
    $ANomFic    = $NomFic;
    $Diff       = $enreg2['Diff_Internet'];
    $ADiff      = $Diff;
    $NatureDoc  = $enreg2['Nature_Document'];
    $ANatureDoc = $NatureDoc;

    echo '<input type="' . $hidden . '" name="NatureDoc" value="' . $NatureDoc . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="ANatureDoc" value="' . $ANatureDoc . '"/>' . "\n";

    echo '<table width="80%" class="table_form">' . "\n";

    Aff_Formulaire();

    // Pas de bouton Supprimer sur une création ou si le document est lié à un objet
    $affBtSup = true;
    if ($Reference == -1) {
        $affBtSup = false;
    } else {
        $req = 'SELECT 1 FROM ' . nom_table('concerne_doc') . ' WHERE id_document = ' . $Reference . ' limit 1';
        $result = lect_sql($req);
        if ($result->rowCount() > 0) {
            $affBtSup = false;
            echo '<tr><td colspan="2">&nbsp;</td></tr>';
            echo '<tr><td colspan="2"><a href="' . $root . '/utilisations_document.php?Doc=' . $Reference . '">' . my_html($LG_Menu_Title['Document_Utils']) . '</a></td></tr>' . "\n";
        }
    }

    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    // Bouton Supprimer en modification si pas d'utilisation du rôle
    $lib_sup = '';
    if ($affBtSup) $lib_sup = $lib_Supprimer;
    // Bouton OK que s'il existe des types ; sinon, on ne pourra pas créer
    $lib_OK = '';
    if (!$err_td) $lib_OK = $lib_Okay;
    bt_ok_an_sup($lib_OK, $lib_Annuler, $lib_sup, 'ce document');

    echo '</table>' . "\n";

    echo "</form>";

    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
}
?>
</body>

</html>