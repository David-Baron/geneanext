<?php

//=====================================================================
// Lien d'un objet à un document
// Parametres à renseigner :
// - refObjet : référence de l'objet
// - typeObjet : P => personne - U => union - F => filiation
//               V => ville - E => évènement
// - refDoc : référence du document (-1 pour la création du lien) TODO: lol
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

function Lire_Nom_Prenoms_Unions($refUnion)
{
    global $NomPere, $PrenomsPere, $NomMere, $PrenomsMere, $LG_Link_Doc_Fa_Not_Found, $LG_Link_Doc_Mo_Not_Found;
    $RefPere     = '';
    $RefMere     = '';
    $sql = 'select Conjoint_1, Conjoint_2 from ' . nom_table('unions') . ' where Reference  = ' . $refUnion . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $RefPere = $enreg[0];
            $RefMere = $enreg[1];
        }
    }
    $res->closeCursor();
    if (!Get_Nom_Prenoms($RefPere, $NomPere, $PrenomsPere)) {
        echo aff_erreur('Père non trouvé');
    }
    if (!Get_Nom_Prenoms($RefMere, $NomMere, $PrenomsMere)) {
        echo aff_erreur('Mère non trouvée');
    }
}

//	================================================================================
function lire_Evenement($refEvt)
{
    global $libEvt;
    // Si l'évènement est connu, on ne pourra pas le modifier
    if ($refEvt != -1) {
        $requete = 'SELECT Titre, Debut, Fin FROM ' . nom_table('evenements') . ' WHERE reference = ' . $refEvt . ' limit 1';
        if ($result = lect_sql($requete)) {
            $enreg = $result->fetch(PDO::FETCH_ASSOC);
            $titre = $enreg['Titre'];
            $deb = $enreg['Debut'];
            $fin = $enreg['Fin'];
            $result->closeCursor();
            if ($deb or $fin) $per = ' (' . Etend_2_dates($deb, $fin) . ')';
            else              $per = '';
            // Libellé pour l'évènement
            $libEvt = $titre . $per;
        }
    }
}

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'Horigine',
    'supprimer',
    'annuler',
    'Defaut',
    'ADefaut',
    'refDoc',
    'ArefDoc',
    'typeObjet',
    'refObjet'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Edition liaison document';   // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Defaut    = Secur_Variable_Post($Defaut, 1, 'S');
$ADefaut   = Secur_Variable_Post($ADefaut, 1, 'S');
$refDoc    = Secur_Variable_Post($refDoc, 11, 'N');
$ArefDoc   = Secur_Variable_Post($ArefDoc, 11, 'N');
$refObjet  = Secur_Variable_Post($refObjet, 11, 'N');
$typeObjet = Secur_Variable_Post($typeObjet, 1, 'S');

if ($Defaut == '') $Defaut = 'N';
if ($ADefaut == '') $ADefaut = 'N';

$n_documents = nom_table('documents');
$n_types_doc = nom_table('types_doc');
$n_concerne_doc = nom_table('concerne_doc');
$n_types_doc = nom_table('types_doc');

// Recup des variables passées dans l'URL
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {
    $refObjet = Recup_Variable('refObjet', 'N');            // Objet concerné
    $typeObjet = Recup_Variable('typeObjet', 'S');          // Type d'objet concerné
    $refDoc = Recup_Variable('refDoc', 'N');                // Identifiant du document (-1 en création du lien)
}

$libObjet = lib_pfu($typeObjet, false);
$libObjet = $art_indet . ' ' . $libObjet;
$entete = 'Liaison d\'' . $libObjet . ' avec un document';

//  Suppression du lien
if ($bt_Sup) {
    $req  = 'delete from ' . $n_concerne_doc . ' where id_document = ' . $refDoc .
        ' and reference_objet  = ' . $refObjet . ' and type_Objet = "' . $typeObjet . '"';
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

//  Création ou modification
if ($bt_OK) {

    $req = '';
    //  Création
    if ($ArefDoc == -1) {
        Ins_Zone_Req($refDoc, 'N', $req);
        Ins_Zone_Req($refObjet, 'N', $req);
        Ins_Zone_Req($typeObjet, 'A', $req);
        Ins_Zone_Req($Defaut, 'A', $req);
        $req = 'insert into ' . $n_concerne_doc . ' values(' . $req . ')';
        $result = maj_sql($req);
        maj_date_site();
    }
    //  Mise a jour
    else {
        if ($Defaut == 'O' && $ADefaut == 'N') {
            $idDocAncien = '';
            //	On efface l'ancien document par défaut pour cette nature de document et cette personne
            $req = 'SELECT d.Id_Document FROM ' . $n_concerne_doc . ' c, ' . $n_documents . ' d ' .
                'WHERE c.Id_Document = d.Id_Document AND Reference_Objet=' . $refObjet . ' AND Type_Objet="' . $typeObjet .
                '" AND Defaut=\'O\' AND Nature_Document = (SELECT Nature_Document FROM ' . nom_table('concerne_doc') .
                ' WHERE Id_Document=' . $refDoc . ')';
            $res = lect_sql($req);
            if ($row = $res->fetch(PDO::FETCH_NUM)) {
                $idDocAncien = $row[0];
            }
            $res->closeCursor();
            if ($idDocAncien != '') {
                $req  = 'UPDATE ' . $n_concerne_doc . ' SET Defaut="N"' .
                    ' WHERE Id_Document=' . $idDocAncien . ' AND Reference_Objet=' . $refObjet . ' AND Type_Objet="' . $typeObjet . '"';
                $result = maj_sql($req);
            }
        }
        $req = '';
        Aj_Zone_Req('Defaut', $Defaut, $ADefaut, 'A', $req);
        if ($req != '') {
            $req  = 'UPDATE ' . $n_concerne_doc . ' SET ' . $req .
                ' WHERE Id_Document=' . $ArefDoc . ' AND Reference_Objet=' . $refObjet . ' AND Type_Objet="' . $typeObjet . '"';
            $result = maj_sql($req);
            maj_date_site();
        }
    }
    Retour_Ar();
}
//
//  ========== Programme principal ==========
//  Affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {
    $compl = Ajoute_Page_Info(600, 250);

    include(__DIR__ . '/assets/js/Edition_Lier_Doc.js');

    Insere_Haut($entete, $compl, 'Edition_Lier_Doc', $refObjet);
    $ArefDoc = $refDoc;
    //	Lecture des anciennes valeurs
    $sql = 'SELECT Defaut FROM ' . $n_concerne_doc . ' WHERE Id_Document = ' . $refDoc .
        ' AND Reference_Objet=' . $refObjet . ' AND Type_Objet="' . $typeObjet . '" limit 1';
    $res = lect_sql($sql);
    if ($row = $res->fetch(PDO::FETCH_NUM)) {
        $Defaut = $row[0];
        $ADefaut = $Defaut;
    }
    $res->closeCursor();
    //  Debut de la page
    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'refDoc\')" action="' . my_self() . '?refObjet=' . $refObjet .
        '&amp;typeObjet=' . $typeObjet . '&amp;refDoc=' . $refDoc . '">' . "\n";
    echo '<input type="' . $hidden . '" name="typeObjet" id="typeObjet" value="' . $typeObjet . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="refObjet" id="refObjet" value="' . $refObjet . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="maxi" id="maxi" />';
    echo '<input type=' . $hidden . ' name="ArefDoc" value="' . $ArefDoc . '"/>' . "\n";
    aff_origine();
    echo '<br />' . "\n";

    // Rappel du nom de l'objet
    switch ($typeObjet) {
        case 'P':
            if (Get_Nom_Prenoms($refObjet, $Nom, $Prenoms)) {
                echo my_html($LG_Link_Doc_Rel_Pers) . LG_SEMIC . $Prenoms . '&nbsp;' . $Nom . "<br /><br />\n";
            } else aff_erreur($LG_Link_Doc_Pers_Not_Found);
            break;
        case 'U':
            $NomPere = '';
            $PrenomsPere = '';
            $NomsMere = '';
            $PrenomsMere = '';
            Lire_Nom_Prenoms_Unions($refObjet);
            echo my_html($LG_Link_Doc_Rel_Union) . LG_SEMIC . $PrenomsPere . '&nbsp;' . $NomPere . ' ' . $LG_and . ' '
                . $PrenomsMere . '&nbsp;' . $NomMere . "<br /><br />\n";
            break;
        case 'F':
            if (Get_Nom_Prenoms($refObjet, $Nom, $Prenoms)) {
                echo my_html($LG_Link_Doc_Rel_Fil) . LG_SEMIC . $Prenoms . '&nbsp;' . $Nom . "<br /><br />\n";
            } else aff_erreur($LG_Link_Doc_Pers_Not_Found);
            break;
        case 'E':
            $libEvt = '';
            lire_Evenement($refObjet);
            echo my_html($LG_Link_Doc_Rel_Event) . LG_SEMIC . $libEvt . "<br /><br />\n";
            break;
        case 'V':
            echo my_html($LG_Link_Doc_Rel_Town) . LG_SEMIC . lib_ville($refObjet, 'O') . "<br /><br />\n";
            break;
    }
    echo '<table width="70%" class="table_form" align="center">' . "\n";

    $larg_titre = 30;
    $existe_docs = true;

    //  En création, on peut choisir le document
    if ($refDoc == -1) {
        $existe_docs = false;
        echo colonne_titre_tab($LG_Docs_Doc_Type);
        // On ne retient que les documents non liés
        $sql_types = 'SELECT DISTINCT d.Id_Type_Document, t.Libelle_Type' .
            ' FROM ' . $n_documents . ' d, ' . $n_types_doc . ' t' .
            ' WHERE d.Id_Type_Document = t.Id_Type_Document' .
            '  AND Id_Document NOT IN ( SELECT Id_Document FROM ' . $n_concerne_doc .
            ' WHERE Type_Objet="' . $typeObjet . '" AND Reference_Objet=' . $refObjet . ')' .
            ' ORDER by t.Libelle_Type';
        $res = lect_sql($sql_types);
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            if (!$existe_docs) echo '<select name="type_doc" id="type_doc" onchange="updateDocs(this.value)" class="oblig">';
            $existe_docs = true;
            echo '<option value="' . $enreg[0] . '">' . $enreg[1] . '</option>';
        }
        if ($existe_docs) {
            echo '</select>&nbsp;';
            echo Img_Zone_Oblig('imgObligTDoc');
        } else {
            echo my_html($LG_Link_Doc_No);
        }
        echo "</td></tr>\n";

        if ($existe_docs) {
            // Nom du document
            echo colonne_titre_tab($LG_Docs_Doc);
            echo '<select name="refDoc" id="refDoc" class="oblig"></select>&nbsp;';
            echo Img_Zone_Oblig('imgObligDocs');
            echo '<div class="buttons">';
            echo '<button type="submit" class="positive" ' .
                'onclick="document.forms.saisie.refDoc.value = document.forms.saisie.maxi.value;return false;"> ' .
                '<img src="' . $chemin_images_icones . $Icones['dernier_ajoute'] . '" alt=""/>' . my_html($LG_Docs_Last_Doc) . '</button>';
            echo '</div>';
            echo "</td></tr>\n";
        }
    }
    //  En modification, le nom est fixe
    else {
        $sql = 'select Titre, Nom_Fichier from ' . $n_documents . ' WHERE Id_Document = ' . $refDoc . ' limit 1';
        $Titre_Doc = '';
        $Nom_Fic_Doc = '';
        $resN = lect_sql($sql);
        if ($row = $resN->fetch(PDO::FETCH_NUM)) {
            $Titre_Doc = $row[0];
            $Nom_Fic_Doc = $row[1];
        }
        echo colonne_titre_tab($LG_Docs_Doc);
        echo '<input type=' . $hidden . ' name="refDoc" value="' . $refDoc . '"/>' . "\n";
        echo $Titre_Doc . '</td></tr>' . "\n";
        $resN->closeCursor();
        echo colonne_titre_tab($LG_Docs_File);
        echo $Nom_Fic_Doc . '</td></tr>' . "\n";
    }

    echo colonne_titre_tab($LG_Docs_Default_Doc);
    echo '<input type="checkbox" name="Defaut" value="O"';
    if ($Defaut == 'O')
        echo ' checked="checked"';
    echo '/><input type=' . $hidden . ' name="ADefaut" value="' . $ADefaut . '"/>';
    echo '</td></tr>' . "\n";
    echo "</table>";

    // Liste des documents existants
    /*
	$sql = 'SELECT Id_Document,Titre FROM '.nom_table('documents').
		' WHERE Id_Document NOT IN ( SELECT Id_Document FROM ' . nom_table('concerne_doc') .
		' WHERE Type_Objet="' . $typeObjet . '" AND Reference_Objet=' . $refObjet . ') ORDER BY Titre';
	*/

    // Affichge des boutons
    echo "<br />\n";
    $lib_sup = '';
    if (($refObjet != -1) and ($refDoc != -1)) {
        $lib_sup = $lib_Supprimer;
    }
    $lib_ok = '';
    if ($existe_docs) $lib_ok = $lib_Okay;
    bt_ok_an_sup($lib_ok, $lib_Annuler, $lib_sup, $LG_Link_Doc_This, false);

    echo "</form>\n";
    Insere_Bas($compl);
}
?>

</body>

</html>