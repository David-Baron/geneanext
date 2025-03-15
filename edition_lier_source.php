<?php

//=====================================================================
// Lien d'un objet à une source
// Parametres à renseigner :
// - refObjet : référence de l'objet
// - typeObjet : P => personne - E => évènement - U => union - F => filiation
//               V => ville
// - refSrc : référence de la source (-1 pour la création du lien)
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

function Lire_Nom_Prenoms_Unions($refUnion)
{
    global $db, $NomPere, $PrenomsPere, $NomMere, $PrenomsMere,
        $LG_Link_Doc_Fa_Not_Found, $LG_Link_Doc_Mo_Not_Found;
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
    //
    if (!Get_Nom_Prenoms($RefPere, $NomPere, $PrenomsPere)) {
        echo '<center><font color="red"><br><br><br><h2>' . $LG_Link_Doc_Fa_Not_Found . '</h2></font></center>';
    }
    if (!Get_Nom_Prenoms($RefMere, $NomMere, $PrenomsMere)) {
        echo '<center><font color="red"><br><br><br><h2>' . $LG_Link_Doc_Mo_Not_Found . '</h2></font></center>';
    }
}

//	================================================================================
function lire_Evenement($refEvt)
{
    global $db, $libEvt;
    // Si l'évènement est connu, on ne pourra pas le modifier
    if ($refEvt != -1) {
        $requete = 'SELECT Titre, Debut, Fin FROM ' . nom_table('evenements') . ' WHERE reference = ' . $refEvt;
        if ($result = lect_sql($requete)) {
            $enreg = $result->fetch(PDO::FETCH_ASSOC);
            $titre = my_html($enreg['Titre']);
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
    'refSrc',
    'ArefSrc',
    'typeObjet',
    'refObjet'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Edition liaison source';   // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$refSrc    = Secur_Variable_Post($refSrc, 11, 'N');
$ArefSrc   = Secur_Variable_Post($ArefSrc, 11, 'N');
$refObjet  = Secur_Variable_Post($refObjet, 11, 'N');
$typeObjet = Secur_Variable_Post($typeObjet, 1, 'S');

// Recup des variables passées dans l'URL
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {
    $refObjet = Recup_Variable('refObjet', 'N');            // Objet concerné
    $typeObjet = Recup_Variable('typeObjet', 'S');          // Type d'objet concerné
    $refSrc = Recup_Variable('refSrc', 'N');                // Identifiant de la source (-1 en création du lien)
}

/*
$libObjet = lib_pfu ($typeObjet,false);
$libObjet = $art_indet.' '.$libObjet;
$entete = 'Liaison d\'' . $libObjet . ' avec une source';
*/
$entete = $LG_Link_Source_Link;

//  Suppression du lien
if ($bt_Sup) {
    $req  = 'delete from ' . nom_table('concerne_source') . ' where Id_Source = ' . $refSrc .
        ' and Reference_Objet  = ' . $refObjet . ' and Type_Objet = "' . $typeObjet . '"';
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

// Modification
if ($bt_OK) {
    $req = '';
    //  Création
    if ($ArefSrc == -1) {
        Ins_Zone_Req($refSrc, 'N', $req);
        Ins_Zone_Req($refObjet, 'N', $req);
        Ins_Zone_Req($typeObjet, 'A', $req);
        Ins_Zone_Req('', 'A', $req); // Id_Source_Tempo à null
        $req = 'insert into ' . nom_table('concerne_source') . ' values(0,' . $req . ')';
        $result = maj_sql($req);
        maj_date_site();
    }
    Retour_Ar();
}

//  ========== Programme principal ==========
//  Affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {
    $compl = Ajoute_Page_Info(600, 250);
    $nb_sources = 0;
    Insere_Haut($entete, $compl, 'Edition_Lier_Source', $refObjet);
    $ArefSrc = $refSrc;

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'refSrc\')" action="' . my_self() . '?' . Query_Str() . '">' . "\n";
    echo '<input type="hidden" name="typeObjet" value="' . $typeObjet . '"/>' . "\n";
    echo '<input type="hidden" name="refObjet" value="' . $refObjet . '"/>' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo '<br />' . "\n";
    // Rappel du nom de l'objet
    switch ($typeObjet) {
        case 'P':
            if (Get_Nom_Prenoms($refObjet, $Nom, $Prenoms)) {
                echo my_html($LG_Link_Doc_Rel_Pers) . LG_SEMIC . $Prenoms . '&nbsp;' . $Nom . "<br /><br />\n";
            } else {
                echo '<center><font color="red"><br><br><br><h2>' . $LG_Link_Doc_Pers_Not_Found . '</h2></font></center>';
            }
            break;
        case 'U':
            $NomPere = '';
            $PrenomsPere = '';
            $NomsMere = '';
            $PrenomsMere = '';
            Lire_Nom_Prenoms_Unions($refObjet);
            echo my_html($LG_Link_Doc_Rel_Fil) . LG_SEMIC . $PrenomsPere . '&nbsp;' . $NomPere . ' ' . $LG_and . ' '
                . $PrenomsMere . '&nbsp;' . $NomMere . "<br /><br />\n";
            break;
        case 'F':
            if (Get_Nom_Prenoms($refObjet, $Nom, $Prenoms)) {
                echo my_html($LG_Link_Doc_Rel_Fil) . LG_SEMIC . $Prenoms . '&nbsp;' . $Nom . "<br /><br />\n";
            } else {
                echo '<center><font color="red"><br><br><br><h2>' . $LG_Link_Doc_Pers_Not_Found . '</h2></font></center>';
            }
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
    // Nom de la source
    echo '<tr><td width="25%" class="label">&nbsp;' . my_html($LG_Link_Source) . '&nbsp;';
    echo '</td><td class="value">' . "\n";
    //  En création, on peut choisir la source
    $n_existe = 'Ident NOT IN ( SELECT Id_Source FROM ' . nom_table('concerne_source')
        . ' WHERE Type_Objet="' . $typeObjet . '" AND Reference_Objet=' . $refObjet . ') ';
    if ($refSrc == -1) {
        // Liste des sources existantes
        $sql = 'SELECT Ident, Titre FROM ' . nom_table('sources') . ' where ' . $n_existe;
        $resN = lect_sql($sql);
        while ($row = $resN->fetch(PDO::FETCH_NUM)) {
            $nb_sources++;
            if ($nb_sources == 1) {
                echo '<select name="refSrc" class="oblig">' . "\n";
                echo '<option value="-1" >-- ' . my_html($LG_Link_Source) . ' --</option>' . "\n";
            }
            echo '<option value="' . $row[0] . '"';
            if ($refSrc == $row[0]) echo ' selected="selected" ';
            echo '>' . $row[1] . "</option>\n";
        }
        if ($nb_sources > 0) {
            echo "</select>\n";
            //	Utilisation de la dernier source créée
            $sql = 'SELECT MAX(Ident) FROM ' . nom_table('sources') . ' where ' . $n_existe;
            $resMax = lect_sql($sql);
            $rowMax = $resMax->fetch(PDO::FETCH_NUM);
            $resMax->closeCursor();
            echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
            echo "<input type='hidden' value='$rowMax[0]' name='refMax'/>\n";
            echo '<input type="button" onclick="document.forms.saisie.refSrc.value = document.forms.saisie.refMax.value;" value="'
                . my_html($LG_Link_Source_Last) . '" name="dernSrc"/>';
        } else {
            echo my_html($LG_Link_Source_Not_Exist) . '...';
        }
        $resN->closeCursor();
    }
    //  En modification, la source est fixe
    else {
        echo '<input type="hidden" name="refSrc" value="' . $refSrc . '"/>' . "\n";
        $sql = 'select Titre from ' . nom_table('sources') . ' WHERE Ident = ' . $refSrc . ' limit 1';
        $nb_sources = 1;
        $resN = lect_sql($sql);
        if ($row = $resN->fetch(PDO::FETCH_NUM)) {
            echo $row[0] . "\n";
        }
        $resN->closeCursor();
    }
    echo '<input type="hidden" name="ArefSrc" value="' . $ArefSrc . '"/>' . "\n";
    echo "</td></tr>\n";

    echo "</table>\n";

    // Affichge des boutons
    echo "<br />\n";
    $lib_sup = '';
    if (($refObjet != -1) and ($refSrc != -1)) {
        $lib_sup = $lib_Supprimer;
    }
    $lib_ok = '';
    if ($nb_sources > 0) $lib_ok = $lib_Okay;
    // Pas de bouton OK en modification
    if ($ArefSrc != -1) $lib_ok = '';
    bt_ok_an_sup($lib_ok, $lib_Annuler, $lib_sup, $LG_Link_Doc_This, false);

    echo "</form>\n";
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