<?php

//=====================================================================
// Lien d'une personne à un nom secondaire
// Parametres à renseigner :
// - refPers : référence de la personne
// - refNom : référence du nom (-1 pour la création du lien) TODO: lol
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'Horigine',
    'supprimer',
    'annuler',
    'refNomF',
    'Comment',
    'AComment',
    'NomP'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Alt_Name'];   // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');          // Appel de la gestion standard des pages

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$refNomF   = Secur_Variable_Post($refNomF, 1, 'N');
$Comment   = Secur_Variable_Post($Comment, 50, 'S');
$AComment  = Secur_Variable_Post($AComment, 50, 'S');
$NomP      = Secur_Variable_Post($NomP, 55, 'S');

// Recup des variables passées dans l'URL
$refPers = Recup_Variable('refPers', 'N');              // Personne concernée
$refNom = Recup_Variable('refNom', 'N');                // Nom (-1 en création de la fiche)

//  Suppression du lien
if ($bt_Sup) {
    $req  = 'delete from ' . nom_table('noms_personnes') .
        ' where idPers = ' . $refPers .
        ' and idNom  = ' . $refNom .
        ' and princ = "N"';
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

//  Création ou modification
if ($bt_OK) {

    $req = '';

    //  Création
    if ($refNom == -1) {

        // On commence par enlever les numéros en entête des noms
        $idNomP = 0;
        $posi = strpos($NomP, '/');
        if ($posi > 0) {
            $idNomP = strval(substr($NomP, 0, $posi));
            $NomP = substr($NomP, $posi + 1);
        }

        // Création du nom de famille ?
        $idNomP = Ajoute_Nom($idNomP, $NomP);

        if ($idNomP != -1) {
            Ins_Zone_Req($refPers, 'N', $req);
            Ins_Zone_Req($idNomP, 'N', $req);
            Ins_Zone_Req('N', 'A', $req);
            Ins_Zone_Req($Comment, 'A', $req);
            $req = 'insert into ' . nom_table('noms_personnes') . ' values(' . $req . ')';
            $result = maj_sql($req);
            maj_date_site();
        }
    }
    //  Mise a jour ; on ne peut modifier que le commentaire
    else {
        Aj_Zone_Req('comment', $Comment, $AComment, 'A', $req);
        if ($req != '') {
            $req  = 'update ' . nom_table('noms_personnes') . ' set ' . $req .
                ' where idPers  = ' . $refPers . ' and idNom = ' . $refNom;
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

    $compl = Ajoute_Page_Info(600, 200);
    Insere_Haut($titre, $compl, 'Edition_Lier_Nom', $refPers);

    //  Debut de la page
    echo '<form id="saisie" method="post" action="' . my_self() . '?refPers=' . $refPers . '&amp;refNom=' . $refNom . '">' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo '<br />' . "\n";

    //  Valeurs par defaut
    $comment   = '';
    if ($refNom != -1) {
        $requete  = 'SELECT comment FROM ' . nom_table('noms_personnes') .
            " WHERE idPers = $refPers AND idNom = $refNom";
        $result    = lect_sql($requete);
        $enreg  = $result->fetch(PDO::FETCH_NUM);
        //  Valeurs lues dans la base
        if ($enreg) {
            $comment   = $enreg[0];
        }
    }

    // Rappel du nom de la personne
    if (Get_Nom_Prenoms($refPers, $Nom, $Prenoms)) {
        echo my_html($LG_Link_Name_Pers) . LG_SEMIC . $Prenoms . '&nbsp;'  . $Nom . "<br />\n";

        $larg_titre = '20';
        echo '<table width="80%" class="table_form">' . "\n";
        echo '<tr><td colspan="2">&nbsp;</td></tr>';

        // Nom
        echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . $LG_Name . '&nbsp;</td><td class="value">';
        //  En création, on peut choisir le nom
        if ($refNom == -1) {
            echo '<input type="hidden" name="NomP" id="NomP" value="-1/--"/>' . "\n";
            // Select des noms de famille existants
            Select_Noms('', 'NomSel', 'NomP');
            echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';

            // Possibilité d'ajouter un nom
            $texte_im = $LG_Link_Name_New;
            echo '<img id="ajout_nom" src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="' . $texte_im . '" title="' . $texte_im . '"' .
                'onclick="inverse_div(\'id_div_ajout_nom\');document.getElementById(\'nouveau_nom\').focus();"/>' . "\n";
            echo '<div id="id_div_ajout_nom">' . "\n";
            echo 'Nom &agrave; ajouter&nbsp;<input type="text" size="50" name="nouveau_nom" id="nouveau_nom"/>' . "\n";
            $texte_im = $LG_Link_Name_Upper;
            echo '&nbsp;<img id="majuscule" src="' . $root . '/assets/img/' . $Icones['majuscule'] . '" alt="' . $texte_im . '" title="' . $texte_im . '"' .
                ' onclick="NomMaj();document.getElementById(\'NomP\').focus();"/>' . "\n";
            echo '<input type="button" name="ferme_OK_nom" value="OK" onclick="ajoute_nom();"/>' . "\n";
            echo '<input type="button" name="ferme_An_nom" value="Annuler" onclick="inverse_div(\'id_div_ajout_nom\');"/>' . "\n";
            echo '</div>' . "\n";
        }
        //  En modification, le nom est fixe
        else {
            if (Get_Nom($refNom, $NomFam)) {
                echo $NomFam;
            } else {
                echo '<center><font color="red"><br><br><br><h2>' . $LG_Link_Name_Unknown . '</h2></font></center>';
            }
            echo '<div id="id_div_ajout_nom"></div>' . "\n";
        }
        echo "</td></tr>\n";

        // Commentaire
        echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_CH_COMMENT . '&nbsp;</td><td class="value">';
        echo '<input type="text" size="50" name="Comment" value="' . $comment . '"/>&nbsp;' . "\n";
        echo '<input type="hidden" name="AComment" value="' . $comment . '"' . "/>\n";
        echo "</td></tr>\n";

        echo '<tr><td colspan="2">&nbsp;</td></tr>';
        $lib_sup = '';
        if (($refPers != -1) and ($refNom != -1)) $lib_sup = $lib_Supprimer;
        bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, $LG_Link_Name_Delete);
    } else {
        echo '<center><font color="red"><br><br><br><h2>' . $LG_Link_Name_Not_Found . '</h2></font></center>';
    }

    echo "</table>";
    echo "</form>\n";

    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
} ?>

<script type="text/javascript">
    // Ajoute le nom saisi dans la liste des noms de famille
    function ajoute_nom() {
        nouv_text = document.forms.saisie.nouveau_nom.value;
        nouv_val = '0/' + nouv_text;
        document.forms.saisie.NomP.value = nouv_val;
        nouvel_element = new Option(nouv_text, nouv_val, false, true);
        //document.forms.saisie.idNomP.options[document.forms.saisie.idNomP.length] = nouvel_element;
        document.forms.saisie.NomSel.options[document.forms.saisie.NomSel.length] = nouvel_element;
        document.forms.saisie.nouveau_nom.value = "";
        inverse_div('id_div_ajout_nom');
    }

    // Met le nom en majuscules
    function NomMaj() {
        document.forms.saisie.nouveau_nom.value = document.forms.saisie.nouveau_nom.value.toUpperCase();
    }

    cache_div("id_div_ajout_nom");
</script>

</body>

</html>