<?php

//=====================================================================
// Ajout rapide
// De frère / soeur si filiation définie ==> création de la personne et de sa filiation
// Des parents si la filiation n'est pas définie ==> création des personnes, de leur union et de la filiation
// D'un conjoint ==> création de la personne et de l'union
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'pere',
    'mere',
    'sexe',
    // variables pour le colattéral
    'Nomcol',
    'ANomcol',
    'Prenomscol',
    'Sexecol',
    'Ne_lecol',
    'CNe_lecol',
    'selNecol',
    'Baptise_lecol',
    'CBaptise_lecol',
    'selBaptisecol',
    'Decede_lecol',
    'CDecede_lecol',
    'selDecedecol',
    // variables pour le conjoint
    'Nomconj',
    'ANomconj',
    'Prenomsconj',
    'Sexeconj',
    'Ne_leconj',
    'CNe_leconj',
    'selNeconj',
    'Baptise_leconj',
    'CBaptise_leconj',
    'selBaptiseconj',
    'Decede_leconj',
    'CDecede_leconj',
    'selDecedeconj',
    'Unis_leconj',
    'CUnis_leconj',
    'selUnisconj',
    // variables pour les parents
    // pour le père
    'Nompere',
    'ANompere',
    'Prenomspere',
    'Sexepere',
    'Ne_lepere',
    'CNe_lepere',
    'selNepere',
    'Baptise_lepere',
    'CBaptise_lepere',
    'selBaptisepere',
    'Decede_lepere',
    'CDecede_lepere',
    'selDecedepere',
    // pour la mère
    'Nommere',
    'ANommere',
    'Prenomsmere',
    'Sexemere',
    'Ne_lemere',
    'CNe_lemere',
    'selNemere',
    'Baptise_lemere',
    'CBaptise_lemere',
    'selBaptisemere',
    'Decede_lemere',
    'CDecede_lemere',
    'selDecedemere',
    // pour l'union
    'Unis_leparents',
    'CUnis_leparents',
    'selUnisparents',

    'Auto_Sosa',
    'NSosa',
    'Horigine'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';

    // Sécurisation des variables réceptionnées
    if (strpos($nom_variables, 'Nom')        === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
    if (strpos($nom_variables, 'ANom')       === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
    if (strpos($nom_variables, 'Prenoms')    === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
    if (strpos($nom_variables, 'Ne_le')      === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
    if (strpos($nom_variables, 'Baptise_le') === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
    if (strpos($nom_variables, 'Decede_le')  === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
    if (strpos($nom_variables, 'Sexe')       === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 1, 'S');
    if (strpos($nom_variables, 'Unis_le')    === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
}

$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');
$Auto_Sosa = Secur_Variable_Post($Auto_Sosa, 2, 'S');
$NSosa     = Secur_Variable_Post($NSosa, 20, 'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Ajout rapide';               // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

$lg_date_a = ' ' . LG_AT . ' ';

// Pour éviter les nulls qui posent problème sur la fiche familiale
function Rattrape_null($Contenu, $Nom_Rub)
{
    global     $rubs, $cont;
    if ($Contenu == '') {
        $rubs .= ',' . $Nom_Rub;
        $cont .= ",''";
    }
}

function Aff_Pers($suffixe, $oblig)
{
    global $root, $style_z_oblig, $enregP, $list_opt_villes, $idNomPers, $Nom, $Sexe, $Icones, $hidden, $largP, $lg_date_a;
    if (!$oblig) $style_z_oblig = '';
    if (($suffixe == 'pere') or ($suffixe == 'mere')) {
        if ($suffixe == 'pere') $val = 'm';
        else                    $val = 'f';
        echo '<input type="' . $hidden . '" name="Sexe' . $suffixe . '" value="' . $val . '"/>';
    }
    // En fonction du suffixe, accord sur le genre pour les libellés de colonnes
    switch ($suffixe) {
        case 'pere':
            $accord = '';
            break;
        case 'mere':
            $accord = 'e';
            break;
        default:
            $accord = '(e)';
    }

// Pour les colattéraux, le nom de la personne est proposé sélectionné
    $id_nom = 0;
    $laVal  = '';
    if ($suffixe == 'col') {
        $id_nom = $idNomPers;
        $laVal  = $id_nom . '/' . $Nom;
    }

    echo '<table width="100%">';
    echo '<tr><td width="' . $largP . '%">' . LG_PERS_NAME . '</td>';
    echo '<td><input type="' . $hidden . '" name="Nom' . $suffixe . '" id="Nom' . $suffixe . '" value="' . $laVal . '" ' . $style_z_oblig . '/>';
    echo '<input type="' . $hidden . '" name="ANom' . $suffixe . '" id="ANom' . $suffixe . '" value="' . $laVal . '"/>';
    Select_Noms($id_nom, 'NomSel' . $suffixe, 'Nom' . $suffixe);
    if ($oblig) echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';

    // Possibilité d'ajouter un nom
    echo '<img id="ajout_nom' . $suffixe . '" src="' . $root . '/assets/img/add.png" alt="Ajout d\'un nom" title="Ajout d\'un nom" ' .
        'onclick="inverse_div(\'id_div_ajout_nom' . $suffixe . '\');document.getElementById(\'nouveau_nom' . $suffixe . '\').focus();"/>';
    if (isset($_SESSION['Nom_Saisi'])) {
        echo ' <img src="' . $root . '/assets/img/' . $Icones['copier'] . '" alt="' . LG_PERS_COPY_NAME . '" title="' . LG_PERS_COPY_NAME . '" onclick="reprend_nom(\'' . $suffixe . '\');">';
    }
    echo '<div id="id_div_ajout_nom' . $suffixe . '">';
    echo LG_ADD_NAME . ' <input type="text" size="50" name="nouveau_nom' . $suffixe . '" id="nouveau_nom' . $suffixe . '"/>';
    echo ' <img id="majuscule' . $suffixe . '" src="' . $root . '/assets/img/' . $Icones['majuscule'] . '" alt="' . LG_NAME_TO_UPCASE . '" title="' . LG_NAME_TO_UPCASE . '"' .
        // ' onclick="NomMaj(\''.$suffixe.'\');document.getElementById(\'NomP'.$suffixe.'\').focus();"/>'."\n";
        ' onclick="NomMaj(\'' . $suffixe . '\');"/>';
    echo '<input type="button" name="ferme_OK_nom' . $suffixe . '" value="OK" onclick="ajoute_nom(\'' . $suffixe . '\')"/>';
    echo '<input type="button" name="ferme_An_nom' . $suffixe . '" value="Annuler" onclick="inverse_div(\'id_div_ajout_nom' . $suffixe . '\')"/>';
    echo '</div>';
    if (($suffixe == 'pere') || ($suffixe == 'mere'))
        echo '<input type="button" name="restr_nom' . $suffixe . '" value="' . $Nom . '" ' .
            'onclick="document.forms.saisie.NomSel' . $suffixe . '.value=\'' . $idNomPers . '/' . $Nom . '\';' .
            'document.forms.saisie.Nom' . $suffixe . '.value = document.forms.saisie.NomSel' . $suffixe . '.value;"/>';
    echo '</td>';
    echo '</tr>';
    echo '<tr><td width="25%">' . LG_PERS_FIRST_NAME . '</td>';
    echo '     <td><input type="text" size="50" name="Prenoms' . $suffixe . '" value="" ' . $style_z_oblig . '/> ';
    if ($oblig) echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '</td></tr>';
    if (($suffixe != 'pere') && ($suffixe != 'mere')) {
        echo '<tr><td width="' . $largP . '%">' . LG_SEXE . '</td>';

        // Pour le conjoint, le sexe opposé à celui de la personne est proposé
        $Sexe_Checked_m = '';
        $Sexe_Checked_f = '';
        if ($suffixe == 'conj') {
            switch ($Sexe) {
                case 'm':
                    $Sexe_Checked_f = ' checked';
                    break;
                case 'f':
                    $Sexe_Checked_m = ' checked';
                    break;
            }
        }

        $name = 'Sexe' . $suffixe;
        echo '<td><input type="radio" id="' . $name . '_m" name="' . $name . '" value="m" ' . $Sexe_Checked_m . '/>'
            . '<label for="' . $name . '_m">' . LG_SEXE_MAN . '</label> ';
        echo '<input type="radio" id="' . $name . '_f" name="' . $name . '" value="f" ' . $Sexe_Checked_f . '/>'
            . '<label for="' . $name . '_f">' . LG_SEXE_WOMAN . '</label> ';
        echo '</td></tr>';
    }

    echo '<tr><td width="' . $largP . '%">' . LG_PERS_BORN . '</td>';
    echo '<td>';
    zone_date2('ANe_le' . $suffixe, 'Ne_le' . $suffixe, 'CNe_le' . $suffixe, '');
    echo $lg_date_a;
    echo '<select name="selNe' . $suffixe . '">';
    echo $list_opt_villes;
    echo '</select>';
    echo '</td></tr><tr>';
    echo '<tr><td width="' . $largP . '%">' . LG_PERS_BAPM . '</td>';
    echo '<td>';
    zone_date2('ABaptise_le' . $suffixe, 'Baptise_le' . $suffixe, 'CBaptise_le' . $suffixe, '');
    echo $lg_date_a;
    echo '<select name="selBaptise' . $suffixe . '">';
    echo $list_opt_villes;
    echo '</select>';
    echo '</td></tr><tr>';
    echo '<tr><td width="' . $largP . '%">' . LG_PERS_DEAD . '</td>';
    echo '<td>';
    zone_date2('ADecede_le' . $suffixe, 'Decede_le' . $suffixe, 'CDecede_le' . $suffixe, '');
    echo $lg_date_a;
    echo '<select name="selDecede' . $suffixe . '">';
    echo $list_opt_villes;
    echo '</select>';
    echo '</td></tr>';
    echo '</table>';
}

// Affiche une union
function Aff_Donnees($Refer)
{
    global $chemin_images, $Comportement, $Icones, $Images, $chemin_images, $list_opt_villes, $existe_filiation, $lib_Okay, $lib_Annuler, $Numero, $lg_date_a, $hidden, $largP;

    // Accès à la personne pour récupérer ses lieux de naissance et de décès
    $villeN = 0;
    $villeD = 0;
    $reqP = 'SELECT Ville_Naissance, Ville_Deces, Sexe FROM ' . nom_table('personnes') . ' WHERE reference = ' . $Refer . ' limit 1';

    if ($res = lect_sql($reqP)) {
        if ($enregP = $res->fetch(PDO::FETCH_NUM)) {
            // Mémorisation de la ville de naissance si renseignée
            $villeN = $enregP[0];
            if ($villeN) $lieux[] = $villeN;
            // Mémorisation de la ville de naissance si renseignée et différente de la ville de naissance
            $villeD = $enregP[1];
            if (($villeD) and ($villeD != $villeN)) $lieux[] = $villeD;
            $Sexe = $enregP[2];
        }
        $res->closeCursor();
    }
    // Accès à la filiation pour récupérer les parents et déterminer s'il existe une filiation
    $existe_filiation = Get_Parents($Refer, $Pere, $Mere, $Rang);
    echo '<input type="' . $hidden . '" name="pere" value="' . $Pere . '"/>';
    echo '<input type="' . $hidden . '" name="mere" value="' . $Mere . '"/>';
    echo '<input type="' . $hidden . '" name="sexe" value="' . $Sexe . '"/>';
    // Recherche du lieu de baptême dans les évènements (en standard, le lieu n'est pas renseigné)
    $villeB = 0;
    $sqlB = 'select Identifiant_zone, Identifiant_Niveau from ' . nom_table('evenements') .
        ' where Code_Type = \'BAPM\'' .
        ' and Reference in (select Evenement from ' . nom_table('participe') . ' where Personne = ' . $Refer . ') limit 1';
    if ($resB = lect_sql($sqlB)) {
        if ($enregB = $resB->fetch(PDO::FETCH_NUM)) {
            // Mémorisation de la ville de baptême si renseignée et différente de la ville de naissance et de la ville de décès
            if ($enregB[0] == 4) $villeB = $enregB[1];
            if (($villeB) and ($villeB != $villeN) and ($villeB != $villeD)) $lieux[] = $villeB;
        }
        $resB->closeCursor();
    }
    // Constitution du select des villes
    $list_opt_villes = '<option value="0" selected="selected">-- ville inconnue --</option>';
    if (isset($lieux)) {
        $nb = count($lieux);
        for ($i = 0; $i < $nb; $i++) {
            $list_opt_villes .= '<option value="' . $lieux[$i] . '">' . lib_ville($lieux[$i], 'O') . '</option>';
        }
    }

    echo '<br />';

    echo '<div id="content">';
    echo '<table id="cols"  cellpadding="0" cellspacing="0" >';
    echo '<tr>';
    echo '<td style="border-right:0px solid #9cb0bb">';
    echo '  <img src="' . $chemin_images . $Images['clear'] . '" width="700" height="1" alt="clear"/>';
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="left">';
    echo '<div class="tab-container" id="container1">';
    // Onglets
    echo '<ul class="tabs">';

    echo '<li><a href="#" onclick="return showPane(\'pnlConjoint\', this)" id="tab_conj">' . my_html(ucfirst(LG_HUSB_WIFE)) . '</a></li>';
    // Saisie rapide des parents si la filiation n'existe pas
    if (!$existe_filiation)
        echo '<li><a href="#" onclick="return showPane(\'pane1\', this)" id="tab1">' . my_html(ucfirst(LG_PARENTS)) . '</a></li>';
    // Saisie rapide d'un frère ou d'une soeur si la filiation existe
    else
        echo '<li><a href="#" onclick="return showPane(\'pane1\', this)" id="tab1">' . my_html(ucfirst(LG_BROTHER_SISTER)) . '</a></li>';
    echo '</ul>';

    echo '<div class="tab-panes">';

    // Données du conjoint
    echo '<div id="pnlConjoint">';
    Aff_Pers('conj', 1);
    echo '<br />';
    echo '<table width="100%">';
    echo '<tr>';
    echo '<tr><td width="' . $largP . '%">' . LG_PERS_UNION . '</td>';
    echo '<td>';
    zone_date2('AUnis_leconj', 'Unis_leconj', 'CUnis_leconj', '');
    echo $lg_date_a;
    echo '<select name="selUnisconj">';
    echo $list_opt_villes;
    echo '</select>';
    echo '</td></tr>';
    echo '</table>';
    echo '</div>';

    // Onglets parents ou frère / soeur
    echo '<div id="pane1">';
    // Pavé parents
    if (!$existe_filiation) {
        echo '<fieldset>';
        echo '<legend>' . LG_FATHER . '</legend>' . "\n";
        Aff_Pers('pere', 0);
        echo '</fieldset>';
        echo '<fieldset>';
        echo '<legend>' . LG_MOTHER . '</legend>' . "\n";
        Aff_Pers('mere', 0);
        echo '</fieldset>';
        echo '<br />';
        echo '<table  width="100%">';
        echo '<tr>';
        echo '<tr><td width="' . $largP . '%">' . LG_PERS_UNION . '</td>';
        echo '<td>';
        zone_date2('AUnis_leparents', 'Unis_leparents', 'CUnis_leparents', '');
        echo $lg_date_a;
        // echo '<td><input type="text" readonly="readonly" size="25" name="Unis_leparents" value=""/>';
        // Affiche_Calendrier('imgCalendU','Calendrier_Union(\'parents\')');
        // echo '<input type="'.$hidden.'" name="CUnis_leparents" value=""/>'."\n";
        // echo '  &agrave;  ';
        echo '<select name="selUnisparents">';
        echo $list_opt_villes;
        echo '</select>';
        echo '</td></tr>';
        echo '</table>';
        if (($Numero != '') && (is_numeric($Numero))) {
            echo '<table  width="100%">';
            echo '<tr>';
            echo '<tr><td width="' . $largP . '%">' . LG_PERS_AUTO_CALC_SOSA . '</td>';
            echo '<td><input type="checkbox" name="Auto_Sosa" checked="checked"/></td></tr>';
            echo '</table>';
            echo '<input type="hidden" name="NSosa" value="' . $Numero . '"/>';
        } else {
            echo '<input type="hidden" name="NSosa" value=""/>';
            echo '<input type="hidden" name="Auto_Sosa" value="n"/>';
        }
    } else {
        $x = Aff_Pers('col', 1);
    }
    echo '</div>';

    echo '</div>';  //  <!-- panes -->
    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);
    echo '</div>';  //  <!-- tab container -->

    echo '</td></tr></table></div>';
}

// Création de la personne
function creation_personne($Nom, $Prenoms, $Sexe, $DNai, $DDec, $LNai, $LDec, $DBap, $LBap, $Sosa = '')
{
    global $nouv_ident, $rubs, $cont, $maj_site;
    $rubs = '';
    $cont = '';

    // On commence par enlever les numéros en entête des noms
    $idNom = 0;
    $posi = strpos($Nom, '/');
    if ($posi > 0) {
        $idNom = strval(substr($Nom, 0, $posi));
        $Nom = substr($Nom, $posi + 1);
    }

    // Création du nom de famille ?
    $idNom = Ajoute_Nom($idNom, $Nom);
    if ($idNom == -1) $Nom = '';

    // Alimentation Automatique du numéro Sosa

    // Récupération de l'identifiant à positionner
    $nouv_ident = Nouvel_Identifiant('Reference', 'personnes');
    // Alimentation des colonnes
    Ins_Zone_Req_Rub($nouv_ident, 'N', 'Reference');
    Ins_Zone_Req_Rub($Nom, 'A', 'Nom');
    Ins_Zone_Req_Rub($Prenoms, 'A', 'Prenoms');
    Ins_Zone_Req_Rub($Sexe, 'A', 'Sexe');
    if ($Sosa != '')
        Ins_Zone_Req_Rub($Sosa, 'A', 'Numero');
    Ins_Zone_Req_Rub($DNai, 'A', 'Ne_le');
    Ins_Zone_Req_Rub($DDec, 'A', 'Decede_le');
    Ins_Zone_Req_Rub($LNai, 'N', 'Ville_Naissance');
    Ins_Zone_Req_Rub($LDec, 'N', 'Ville_Deces');
    Ins_Zone_Req_Rub('O', 'A', 'Diff_Internet');  // Diffusable par défaut
    Ins_Zone_Req_Rub('N', 'A', 'Statut_Fiche');   // Non validée par défaut
    Ins_Zone_Req_Rub($idNom, 'N', 'idNomFam');

    // Pour éviter les nulls qui posent problème sur la fiche familiale
    Rattrape_null($Sosa, 'Numero');
    Rattrape_null($DNai, 'Ne_le');
    Rattrape_null($DDec, 'Decede_le');
    Rattrape_null($Sexe, 'Sexe');

    // Mise en forme de la requête
    $req = 'insert into ' . nom_table('personnes') .
        ' (' . $rubs . ',Date_Creation,Date_Modification) values' .
        ' (' . $cont . ',current_timestamp,current_timestamp)';
    // Exécution de la requête
    $res = maj_sql($req);

    // Création du lien personnes / noms
    $req = 'insert into ' . nom_table('noms_personnes') . ' values(' . $nouv_ident . ',' . $idNom . ',\'O\',null)';
    $res = maj_sql($req);

    // Création de l'évènement baptême
    if (($DBap != '') or ($LBap != 0)) {
        $rubs = '';
        $cont = '';
        Ins_Zone_Req_Rub($LBap, 'N', 'Identifiant_zone');
        Ins_Zone_Req_Rub(4, 'N', 'Identifiant_Niveau');
        Ins_Zone_Req_Rub('BAPM', 'A', 'Code_Type');
        Ins_Zone_Req_Rub(LG_PERS_BAPM_EVENT, 'A', 'Titre ');
        if ($DBap != '') {
            Ins_Zone_Req_Rub($DBap, 'A', 'Debut');
            Ins_Zone_Req_Rub($DBap, 'A', 'Fin');
        }
        Ins_Zone_Req_Rub('N', 'A', 'Statut_Fiche');        // Non validée par défaut
        $req = 'insert into ' . nom_table('evenements') .
            ' (' . $rubs . ',Date_Creation,Date_Modification) values' .
            ' (' . $cont . ',current_timestamp,current_timestamp)';
        // Exécution de la requête
        $res = maj_sql($req);
        //$num_evt = $res->insert_id();
        // Création de la participation
        $req = '';
        //Ins_Zone_Req($num_evt,'N',$req);
        Ins_Zone_Req($nouv_ident, 'N', $req);
        Ins_Zone_Req(' ', 'A', $req);
        Ins_Zone_Req($DBap, 'A', $req);
        Ins_Zone_Req($DBap, 'A', $req);
        Ins_Zone_Req('O', 'A', $req);
        $req  = 'insert into ' . nom_table('participe') . ' values(LAST_INSERT_ID(),' . $req . ',0,0,\'n\')';
        // Exécution de la requête
        $res = maj_sql($req);
    }
    $maj_site = true;
}

//Demande de mise à jour
if ($bt_OK) {
    // Init des zones de requête
    $Creation = 0;
    $req = '';
    $maj_site = false;

    // Création d'un colattéral ==> on crée la personne (+ évènement si baptême) et la filiation
    if (($Nomcol != '') and ($Prenomscol != '')) {
        // Création de la personne et du baptême associé
        creation_personne($Nomcol, $Prenomscol, $Sexecol, $CNe_lecol, $CDecede_lecol, $selNecol, $selDecedecol, $CBaptise_lecol, $selBaptisecol);
        // Création de la filiation
        $req = 'insert into ' . nom_table('filiations') . ' values (' .
            $nouv_ident . ',' . $pere . ',' . $mere . ',0,current_timestamp,current_timestamp,\'N\')';
        // Exécution de la requête
        $res = maj_sql($req);
        $maj_site = true;
    }
    // Création d'un conjoint ==> on crée la personne et l'union
    if (($Nomconj != '') and ($Prenomsconj != '')) {
        // Création de la personne et du baptême associé
        creation_personne($Nomconj, $Prenomsconj, $Sexeconj, $CNe_leconj, $CDecede_leconj, $selNeconj, $selDecedeconj, $CBaptise_leconj, $selBaptiseconj);
        // Création de l'union
        $rubs = '';
        $cont = '';
        if ($sexe == 'f') {
            Ins_Zone_Req_Rub($nouv_ident, 'N', 'Conjoint_1');
            Ins_Zone_Req_Rub($Refer, 'N', 'Conjoint_2');
        } else {
            Ins_Zone_Req_Rub($Refer, 'N', 'Conjoint_1');
            Ins_Zone_Req_Rub($nouv_ident, 'N', 'Conjoint_2');
        }
        if ($CUnis_leconj != '') Ins_Zone_Req_Rub($CUnis_leconj, 'A', 'Maries_Le');
        Ins_Zone_Req_Rub($selUnisconj, 'N', 'Ville_Mariage');
        $req = 'insert into ' . nom_table('unions') .
            ' (' . $rubs . ',Date_Creation,Date_Modification) values' .
            ' (' . $cont . ',current_timestamp,current_timestamp)';
        // Exécution de la requête
        $res = maj_sql($req);
        $maj_site = true;
    }
    // Création des parents ==> on crée les personnes (+ évènement si baptême) + leur union et la filiation
    if ((($Nompere != '') and ($Prenomspere != '')) or
        (($Nommere != '') and ($Prenomsmere != ''))
    ) {
        $num_pere = 0;
        $num_mere = 0;
        // Création du père et du baptême associé
        if (($Nompere != '') and ($Prenomspere != '')) {
            $sosa = '';
            if (($Auto_Sosa == 'on') and ($NSosa != '')) {
                $sosa = intval($NSosa) * 2;
            }
            creation_personne($Nompere, $Prenomspere, $Sexepere, $CNe_lepere, $CDecede_lepere, $selNepere, $selDecedepere, $CBaptise_lepere, $selBaptisepere, $sosa);
            $num_pere = $nouv_ident;
        }
        // Création de la mère et du baptême associé
        if (($Nommere != '') and ($Prenomsmere != '')) {
            $sosa = '';
            if (($Auto_Sosa == 'on') && ($NSosa != '')) {
                $sosa = (intval($NSosa) * 2) + 1;
            }
            creation_personne($Nommere, $Prenomsmere, $Sexemere, $CNe_lemere, $CDecede_lemere, $selNemere, $selDecedemere, $CBaptise_lemere, $selBaptisemere, $sosa);
            $num_mere = $nouv_ident;
        }
        // Création de l'union des parents si les 2 parents sont renseignés (ont été créés)
        if ($num_pere && $num_mere) {
            $rubs = '';
            $cont = '';
            Ins_Zone_Req_Rub($num_pere, 'N', 'Conjoint_1');
            Ins_Zone_Req_Rub($num_mere, 'N', 'Conjoint_2');
            if ($CUnis_leparents != '') Ins_Zone_Req_Rub($CUnis_leparents, 'A', 'Maries_Le');
            Ins_Zone_Req_Rub($selUnisparents, 'N', 'Ville_Mariage');
            $req = 'insert into ' . nom_table('unions') .
                ' (' . $rubs . ',Date_Creation,Date_Modification) values' .
                ' (' . $cont . ',current_timestamp,current_timestamp)';
            // Exécution de la requête
            $res = maj_sql($req);
        }
        // Création de la filiation
        $req = 'insert into ' . nom_table('filiations') . ' values (' .
            $Refer . ',' . $num_pere . ',' . $num_mere . ',0,current_timestamp,current_timestamp,\'N\')';
        // Exécution de la requête
        $res = maj_sql($req);
        $maj_site = true;
    }

    // Mise à jour de la date du site
    if ($maj_site) maj_date_site(true);

    // Retour arrière
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    $largP = 25;

    // include(__DIR__ . '/assets/js/Ajout_Rapide.js');
    $compl = '';

    $Nom = '';
    $Prenoms = '';
    $sql = 'select Nom, Prenoms, idNomFam, Sexe, Numero  from ' . nom_table('personnes') . ' where Reference  = ' . $Refer . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $Nom       = my_html($enreg[0]);
            $Prenoms   = my_html($enreg[1]);
            $idNomPers = $enreg[2];
            $Sexe      = $enreg[3];
            $Numero    = $enreg[4];
        }
    }
    $res->closeCursor();

    if (($Nom != '') or ($Prenoms != '')) {
        $compl = Ajoute_Page_Info(600, 200);
        Insere_Haut(LG_PERS_QUICK_ADD . ' ' . $Prenoms . ' ' . $Nom, $compl, 'Ajout_Rapide', $Refer);
        echo '<form id="saisie" method="post" action="' . $root . '/ajout_rapide.php?Refer=' . $Refer . '">';
        echo '<input type="' . $hidden . '" name="Refer" value="' . $Refer . '"/>';
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        if (isset($_SESSION['Nom_Saisi']))
            echo '<input type="' . $hidden . '" name="Nom_Prec" value="' . $_SESSION['Nom_Saisi'] . '"/>';

        // Affichage des données
        $x = Aff_Donnees($Refer);

        echo '</form>';

        include(__DIR__ . '/assets/js/gest_onglets.js');
        echo '<!-- On positionne l\'onglet par défaut -->';
        echo '<script type="text/javascript">';
        echo '	cache_div("id_div_ajout_nomconj")';
        if (!$existe_filiation) {
            echo '	cache_div("id_div_ajout_nompere")';
            echo '	cache_div("id_div_ajout_nommere")';
        } else {
            echo '	cache_div("id_div_ajout_nomcol")';
        }

        echo '	setupPanes("container1", "tab_conj",40);';
        echo '</script>';
    }
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    if ($compl != '') {
        echo $compl;
    }
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
} else {
    echo "<body bgcolor=\"#FFFFFF\">";
}
?>
<script type="text/javascript">
    // Ajoute le nom saisi dans la liste des noms de famille
    function ajoute_nom(cible) {
        var nouv_text = document.getElementById("nouveau_nom" + cible).value;
        var nouv_val = '0/' + nouv_text;
        document.getElementById("Nom" + cible).value = nouv_val;
        nouvel_element = new Option(nouv_text, nouv_val, false, true);
        document.getElementById("NomSel" + cible).options[document.getElementById("NomSel" + cible).length] = nouvel_element;
        document.getElementById("nouveau_nom" + cible).value = "";
        inverse_div('id_div_ajout_nom' + cible);
    }

    // Met le nom en majuscules
    function NomMaj(cible) {
        document.getElementById("nouveau_nom" + cible).value = document.getElementById("nouveau_nom" + cible).value.toUpperCase();
    }

    // Reprend le nom saisi précédemment
    function reprend_nom(cible) {
        nouv_text = document.forms.saisie.Nom_Prec.value;
        document.getElementById("Nom" + cible).value = nouv_text;
        document.getElementById("NomSel" + cible).value = nouv_text;
    }
</script>
</body>

</html>