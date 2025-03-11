<?php
//=====================================================================
// Saisie du de cujus et de son noyau
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$test = false;

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'nomPDec',
    'prenomPDec',
    'sexePDec',
    'CnaissancePDec',
    'LnaissancePDec',
    'CdecesPDec',
    'LdecesPDec',
    'nomMDec',
    'prenomMDec',
    'sexeMDec',
    'CnaissanceMDec',
    'LnaissanceMDec',
    'CdecesMDec',
    'LdecesMDec',
    'nomDec',
    'prenomDec',
    'sexeDec',
    'CnaissanceDec',
    'LnaissanceDec',
    'CdecesDec',
    'LdecesDec',
    'nomConj',
    'prenomConj',
    'sexeConj',
    'CnaissanceConj',
    'LnaissanceConj',
    'CdecesConj',
    'LdecesConj',
    'nomPConj',
    'prenomPConj',
    'sexePConj',
    'CnaissancePConj',
    'LnaissancePConj',
    'CdecesPConj',
    'LdecesPConj',
    'nomMConj',
    'prenomMConj',
    'sexeMConj',
    'CnaissanceMConj',
    'LnaissanceMConj',
    'CdecesMConj',
    'LdecesMConj'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

$acces = 'M';                          // Type d'accès de la page : (M)ise à jour
$titre = $LG_Menu_Title['Decujus_And_Family'];
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$n_unions = nom_table('unions');
$n_filiations = nom_table('filiations');
$n_personnes = nom_table('personnes');
$n_villes = nom_table('villes');
$n_noms_personnes = nom_table('noms_personnes');
$noms_famille = nom_table('noms_famille');


// Reset de la base pour les tests ; à supprimer !!!!
if ($test) {
    $res = maj_sql('delete from ' . $n_unions);
    $res = maj_sql('delete from ' . $n_filiations);
    $res = maj_sql('delete from ' . $n_personnes);
    $res = maj_sql('delete from ' . $n_villes);
    $res = maj_sql('delete from ' . $n_noms_personnes);
    $res = maj_sql('delete from ' . $noms_famille);
}


//Demande de mise à jour
if ($bt_OK) {
    $maj_site = false;
    // Sécurisation des variables postées du formulaire
    $nomPDec            = Secur_Variable_Post($nomPDec, 50, 'S');
    $prenomPDec            = Secur_Variable_Post($prenomPDec, 50, 'S');
    $sexePDec            = Secur_Variable_Post($sexePDec, 1, 'S');
    $CnaissancePDec        = Secur_Variable_Post($CnaissancePDec, 10, 'S');
    $LnaissancePDec        = Secur_Variable_Post($LnaissancePDec, 80, 'S');
    $CdecesPDec            = Secur_Variable_Post($CdecesPDec, 10, 'S');
    $LdecesPDec            = Secur_Variable_Post($LdecesPDec, 80, 'S');

    $nomMDec            = Secur_Variable_Post($nomMDec, 50, 'S');
    $prenomMDec            = Secur_Variable_Post($prenomMDec, 50, 'S');
    $sexeMDec            = Secur_Variable_Post($sexeMDec, 1, 'S');
    $CnaissanceMDec        = Secur_Variable_Post($CnaissanceMDec, 10, 'S');
    $LnaissanceMDec        = Secur_Variable_Post($LnaissanceMDec, 80, 'S');
    $CdecesMDec            = Secur_Variable_Post($CdecesMDec, 10, 'S');
    $LdecesMDec            = Secur_Variable_Post($LdecesMDec, 80, 'S');

    $nomDec                = Secur_Variable_Post($nomDec, 50, 'S');
    $prenomDec            = Secur_Variable_Post($prenomDec, 50, 'S');
    $sexeDec            = Secur_Variable_Post($sexeDec, 1, 'S');
    $CnaissanceDec        = Secur_Variable_Post($CnaissanceDec, 10, 'S');
    $LnaissanceDec        = Secur_Variable_Post($LnaissanceDec, 80, 'S');
    $CdecesDec            = Secur_Variable_Post($CdecesDec, 10, 'S');
    $LdecesDec            = Secur_Variable_Post($LdecesDec, 80, 'S');

    $nomConj            = Secur_Variable_Post($nomConj, 50, 'S');
    $prenomConj            = Secur_Variable_Post($prenomConj, 50, 'S');
    $sexeConj            = Secur_Variable_Post($sexeConj, 1, 'S');
    $CnaissanceConj        = Secur_Variable_Post($CnaissanceConj, 10, 'S');
    $LnaissanceConj        = Secur_Variable_Post($LnaissanceConj, 80, 'S');
    $CdecesConj            = Secur_Variable_Post($CdecesConj, 10, 'S');
    $LdecesConj            = Secur_Variable_Post($LdecesConj, 80, 'S');

    $nomPConj            = Secur_Variable_Post($nomPConj, 50, 'S');
    $prenomPConj        = Secur_Variable_Post($prenomPConj, 50, 'S');
    $sexePConj            = Secur_Variable_Post($sexePConj, 1, 'S');
    $CnaissancePConj    = Secur_Variable_Post($CnaissancePConj, 10, 'S');
    $LnaissancePConj    = Secur_Variable_Post($LnaissancePConj, 80, 'S');
    $CdecesPConj        = Secur_Variable_Post($CdecesPConj, 10, 'S');
    $LdecesPConj        = Secur_Variable_Post($LdecesPConj, 80, 'S');

    $nomMConj            = Secur_Variable_Post($nomMConj, 50, 'S');
    $prenomMConj        = Secur_Variable_Post($prenomMConj, 50, 'S');
    $sexeMConj            = Secur_Variable_Post($sexeMConj, 1, 'S');
    $CnaissanceMConj    = Secur_Variable_Post($CnaissanceMConj, 10, 'S');
    $LnaissanceMConj    = Secur_Variable_Post($LnaissanceMConj, 80, 'S');
    $CdecesMConj        = Secur_Variable_Post($CdecesMConj, 10, 'S');
    $LdecesMConj        = Secur_Variable_Post($LdecesMConj, 80, 'S');

    //$id_ville = Nouvel_Identifiant('Identifiant_zone','villes')-1;
    $max_id_pers = 1;        // Base à priori vide
    $max_id_ville = 0;        // "

    $Diff_InternetP = 'O';
    $Statut_Fiche = 'N';

    $deb_req_P = 'insert into ' . $n_personnes . ' values(';
    $deb_req_F = 'insert into ' . $n_filiations . ' values(';
    // On ne charge pas toutes les colonnes sur les unions
    $deb_req_U = 'insert into ' . $n_unions . ' (Conjoint_1,Conjoint_2,Date_Creation,Date_Modification,Statut_Fiche) values(';

    $PDec = false;
    if (($nomPDec != '') and ($prenomPDec != '')) $PDec = true;
    if ($PDec) {
        $Pdec = $max_id_pers;
        $req = $deb_req_P . $max_id_pers++;            // Reference
        Ins_Zone_Req($nomPDec, 'A', $req);            // Nom
        Ins_Zone_Req($prenomPDec, 'A', $req);            // Prenoms
        Ins_Zone_Req($sexePDec, 'A', $req);            // Sexe
        Ins_Zone_Req('2', 'A', $req);                    // Numero
        Ins_Zone_Req($CnaissancePDec, 'A', $req);        // Ne_le
        Ins_Zone_Req($CdecesPDec, 'A', $req);            // Decede_Le
        $idV = cree_ville($LnaissancePDec);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Naissance
        $idV = cree_ville($LdecesPDec);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Deces
        $req .= ",'" . $Diff_InternetP . "'" .            // Diff_Internet
            ',current_timestamp' .                // Date_Creation
            ',current_timestamp' .                // Date_Modification
            ",'" . $Statut_Fiche . "'" .                // Statut_Fiche
            ',null' .                            // idNomFam
            ',0' .                                // Categorie
            ',null' .                            // Surnom
            ')';
        $res = maj_sql($req);
    }

    $MDec = false;
    if (($nomMDec != '') and ($prenomMDec != '')) $MDec = true;
    if ($MDec) {
        $MDec = $max_id_pers;
        $req = $deb_req_P . $max_id_pers++;            // Reference
        Ins_Zone_Req($nomMDec, 'A', $req);            // Nom
        Ins_Zone_Req($prenomMDec, 'A', $req);            // Prenoms
        Ins_Zone_Req($sexeMDec, 'A', $req);            // Sexe
        Ins_Zone_Req('3', 'A', $req);                    // Numero
        Ins_Zone_Req($CnaissanceMDec, 'A', $req);        // Ne_le
        Ins_Zone_Req($CdecesMDec, 'A', $req);            // Decede_Le
        $idV = cree_ville($LnaissanceMDec);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Naissance
        $idV = cree_ville($LdecesMDec);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Deces
        $req .= ",'" . $Diff_InternetP . "'" .            // Diff_Internet
            ',current_timestamp' .                // Date_Creation
            ',current_timestamp' .                // Date_Modification
            ",'" . $Statut_Fiche . "'" .                // Statut_Fiche
            ',null' .                            // idNomFam
            ',0' .                                // Categorie
            ',null' .                            // Surnom
            ')';
        $res = maj_sql($req);
    }

    $Dec = false;
    if (($nomDec != '') and ($prenomDec != '')) $Dec = true;
    if ($Dec) {
        $Dec = $max_id_pers;
        $req = $deb_req_P . $max_id_pers++;            // Reference
        Ins_Zone_Req($nomDec, 'A', $req);                // Nom
        Ins_Zone_Req($prenomDec, 'A', $req);            // Prenoms
        Ins_Zone_Req($sexeDec, 'A', $req);            // Sexe
        Ins_Zone_Req('1', 'A', $req);                    // Numero
        Ins_Zone_Req($CnaissanceDec, 'A', $req);        // Ne_le
        Ins_Zone_Req($CdecesDec, 'A', $req);            // Decede_Le
        $idV = cree_ville($LnaissanceDec);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Naissance
        $idV = cree_ville($LdecesDec);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Deces
        $req .= ",'" . $Diff_InternetP . "'" .            // Diff_Internet
            ',current_timestamp' .                // Date_Creation
            ',current_timestamp' .                // Date_Modification
            ",'" . $Statut_Fiche . "'" .                // Statut_Fiche
            ',null' .                            // idNomFam
            ',0' .                                // Categorie
            ',null' .                            // Surnom
            ')';
        $res = maj_sql($req);
    }

    $Conj = false;
    if (($nomConj != '') and ($prenomConj != '')) $Conj = true;
    if ($Conj) {
        $Conj = $max_id_pers;
        $req = $deb_req_P . $max_id_pers++;            // Reference
        Ins_Zone_Req($nomConj, 'A', $req);            // Nom
        Ins_Zone_Req($prenomConj, 'A', $req);            // Prenoms
        Ins_Zone_Req($sexeConj, 'A', $req);            // Sexe
        $req .= ',null';                            // Numero
        Ins_Zone_Req($CnaissanceConj, 'A', $req);        // Ne_le
        Ins_Zone_Req($CdecesConj, 'A', $req);            // Decede_Le
        $idV = cree_ville($LnaissanceConj);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Naissance
        $idV = cree_ville($LdecesConj);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Deces
        $req .= ",'" . $Diff_InternetP . "'" .            // Diff_Internet
            ',current_timestamp' .                // Date_Creation
            ',current_timestamp' .                // Date_Modification
            ",'" . $Statut_Fiche . "'" .                // Statut_Fiche
            ',null' .                            // idNomFam
            ',0' .                                // Categorie
            ',null' .                            // Surnom
            ')';
        $res = maj_sql($req);
    }

    $PConj = false;
    if (($nomPConj != '') and ($prenomPConj != '')) $PConj = true;
    if ($PConj) {
        $PConj = $max_id_pers;
        $req = $deb_req_P . $max_id_pers++;            // Reference
        Ins_Zone_Req($nomPConj, 'A', $req);            // Nom
        Ins_Zone_Req($prenomPConj, 'A', $req);        // Prenoms
        Ins_Zone_Req($sexePConj, 'A', $req);            // Sexe
        $req .= ',null';                            // Numero
        Ins_Zone_Req($CnaissancePConj, 'A', $req);    // Ne_le
        Ins_Zone_Req($CdecesPConj, 'A', $req);        // Decede_Le
        $idV = cree_ville($LnaissancePConj);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Naissance
        $idV = cree_ville($LdecesPConj);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Deces
        $req .= ",'" . $Diff_InternetP . "'" .            // Diff_Internet
            ',current_timestamp' .                // Date_Creation
            ',current_timestamp' .                // Date_Modification
            ",'" . $Statut_Fiche . "'" .                // Statut_Fiche
            ',null' .                            // idNomFam
            ',0' .                                // Categorie
            ',null' .                            // Surnom
            ')';
        $res = maj_sql($req);
    }

    $MConj = false;
    if (($nomMConj != '') and ($prenomMConj != '')) $MConj = true;
    if ($MConj) {
        $MConj = $max_id_pers;
        $req = $deb_req_P . $max_id_pers++;            // Reference
        Ins_Zone_Req($nomMConj, 'A', $req);            // Nom
        Ins_Zone_Req($prenomMConj, 'A', $req);        // Prenoms
        Ins_Zone_Req($sexeMConj, 'A', $req);            // Sexe
        $req .= ',null';                            // Numero
        Ins_Zone_Req($CnaissanceMConj, 'A', $req);    // Ne_le
        Ins_Zone_Req($CdecesMConj, 'A', $req);        // Decede_Le
        $idV = cree_ville($LnaissanceMConj);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Naissance
        $idV = cree_ville($LdecesMConj);
        Ins_Zone_Req($idV, 'N', $req);                // Ville_Deces
        $req .= ",'" . $Diff_InternetP . "'" .            // Diff_Internet
            ',current_timestamp' .                // Date_Creation
            ',current_timestamp' .                // Date_Modification
            ",'" . $Statut_Fiche . "'" .                // Statut_Fiche
            ',null' .                            // idNomFam
            ',0' .                                // Categorie
            ',null' .                            // Surnom
            ')';
        $res = maj_sql($req);
    }

    // Création des filiations
    if ((($Pdec) or ($MDec)) and ($Dec)) {
        $req = $deb_req_F . $Dec . ',' . $PDec . ',' . $MDec . ",0,current_timestamp,current_timestamp,'" . $Statut_Fiche . "')";
        $res = maj_sql($req);
    }
    if ((($PConj) or ($MConj)) and ($Conj)) {
        $req = $deb_req_F . $Conj . ',' . $PConj . ',' . $MConj . ",0,current_timestamp,current_timestamp,'" . $Statut_Fiche . "')";
        $res = maj_sql($req);
    }
    // Création des unions
    if (($Pdec) and ($MDec)) {
        $req = $deb_req_U . $PDec . "," . $MDec . ",current_timestamp,current_timestamp,'" . $Statut_Fiche . "')";
        $res = maj_sql($req);
    }
    if (($Dec) and ($Conj)) {
        $req = $deb_req_U . $Dec . "," . $Conj . ",current_timestamp,current_timestamp,'" . $Statut_Fiche . "')";
        $res = maj_sql($req);
    }
    if (($PConj) and ($MConj)) {
        $req = $deb_req_U . $PConj . "," . $MConj . ",current_timestamp,current_timestamp,'" . $Statut_Fiche . "')";
        $res = maj_sql($req);
    }

    Creation_Noms_Commun();
    $maj_site = true;

    if ($maj_site) maj_date_site(true);

    // Retour vers la page précédente
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    # include(__DIR__ . '/assets/js/Noyau.js');

    $compl = Ajoute_Page_Info(650, 250);

    Insere_Haut($titre, $compl, 'noyau', '');

    $vide = true;
    $sqlP = 'select Reference from ' . $n_personnes . ' where Reference <> 0 limit 1';
    $sqlV = 'select Identifiant_zone from ' . $n_villes . ' where Identifiant_zone <> 0 limit 1';
    $ut = exec_req_vide($sqlP);
    $ut = exec_req_vide($sqlV);

    if ($debug) $vide = true;
    if (!$vide) {
        Affiche_Stop(LG_DECUJUS_ERR_NO_EMPTY);
    } else {
        echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'NomP,PrenomsP\')">';

        $texte_im_maj = LG_PERS_NAME_TO_UPCASE;
        $icone_maj = $chemin_images_icones . $Icones['majuscule'];

        echo '<div class="labels">';
        echo '<table>';
        echo '<tr>';
        echo '<td>';
        echo '<fieldset>';
        aff_legend(LG_FATHER);
        case_personne('PDec');
        echo '</fieldset>';
        echo '</td>';
        echo '<td>';
        echo '<fieldset>';
        aff_legend(LG_MOTHER);
        case_personne('MDec');
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="2">';
        echo '<fieldset>';
        aff_legend(LG_DECUJUS_DECUJUS);
        case_personne('Dec');
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="2">';
        echo '<fieldset>';
        aff_legend(LG_HUSB_WIFE);
        case_personne('Conj');
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td>';
        echo '<fieldset>';
        aff_legend(LG_FATHER);
        case_personne('PConj');
        echo '</fieldset>';
        echo '</td>';
        echo '<td>';
        echo '<fieldset>';
        aff_legend(LG_MOTHER);
        case_personne('MConj');
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';

        echo '</table>';
        echo '</div>';

        bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);

        echo '</form>';
    }
}

function case_personne($suff)
{
    global $texte_im_maj, $icone_maj, $hidden;
    echo '<label for="nom' . $suff . '">' . my_html(LG_PERS_NAME) . ' :</label><input type="text" id="nom' . $suff . '" name="nom' . $suff . '" class="oblig" />&nbsp;';
    Img_Zone_Oblig('ObligNom' . $suff);
    echo '&nbsp;<img id="maj' . $suff . '" src="' . $icone_maj . '" alt="' . $texte_im_maj . '" title="' . $texte_im_maj . '"' .
        ' onclick="document.forms.saisie.nom' . $suff . '.value = document.forms.saisie.nom' . $suff . '.value.toUpperCase();"/>';
    echo '<br />';
    echo '<label for="prenom' . $suff . '">' . my_html(LG_PERS_FIRST_NAME) . ' :</label><input type="text" id="prenom' . $suff . '" name="prenom' . $suff . '" class="oblig" />&nbsp;';
    Img_Zone_Oblig('ObligPrenoms' . $suff);
    echo '<br />';
    $chkM = '';
    $chkF = '';
    if (($suff == 'PDec') or ($suff == 'PConj')) $chkM = ' checked="checked"';
    if (($suff == 'MDec') or ($suff == 'MConj')) $chkF = ' checked="checked"';
    echo '<label for="sexe' . $suff . '">' . my_html(LG_SEXE) .
        ' :</label>' .
        '<input type="radio" name="sexe' . $suff . '" id="sexe' . $suff . '" value="m"' . $chkM . '/>' . my_html(LG_SEXE_MAN) .
        '<input type="radio" name="sexe' . $suff . '"  value="f"' . $chkF . '/>' . my_html(LG_SEXE_WOMAN) . '<br />';
    echo '<label for="naissance' . $suff . '">' . my_html(LG_PERS_BORN) . ' :</label>';
    zone_date2('Anaissance' . $suff, 'naissance' . $suff, 'Cnaissance' . $suff, '');
    echo my_html(LG_AT) . '&nbsp;<input type="text" id="Lnaissance' . $suff . '" name="Lnaissance' . $suff . '" /><br />';
    echo '<label for="deces' . $suff . '">' . my_html(LG_PERS_DEAD) . ' :</label>';
    zone_date2('Adeces' . $suff, 'deces' . $suff, 'Cdeces' . $suff, '');
    echo my_html(LG_AT) . '&nbsp;<input type="text" id="Ldeces' . $suff . '" name="Ldeces' . $suff . '" /><br />';
}

function exec_req_vide($req)
{
    global $vide, $debug;
    $ret = false;
    $z1 = 0;
    if ($vide) {
        if ($res = lect_sql($req)) {
            if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                $z1 = $enreg[0];
            }
            $res->closeCursor();
            unset($res);
        }
        if ($z1 > 0) {
            $ret = true;
            $vide = false;
        }
        if ($debug) echo 'z1 : ' . $z1 . '<br />';
    }
    return $ret;
}

function cree_ville($nom_ville)
{
    global $n_villes, $max_id_ville, $tab_villes, $debug;
    if ($debug) echo 'cre ville : ' . $nom_ville . '<br />';
    $id_ville = 0;
    if ($nom_ville <> '') {
        $nom_ville = ajoute_sl_rt($nom_ville);
        $x = false;
        if (isset($tab_villes)) $x = array_search($nom_ville, $tab_villes);
        // La zone a été trouvée dans le tableau
        if ($x !== false) {
            $id_ville = $tab_villes[$x];
            if ($debug) echo 'Zone trouvée dans tableau : ' . $nom_ville . ', ' . $x . '<br />';
        }
        // Sinon, on la crée
        else {
            $tab_villes[$max_id_ville] = $nom_ville;
            $max_id_ville++;
            $req = 'insert into ' . $n_villes .
                ' values(' . $max_id_ville . ',' . "'" . $nom_ville . "'" . ',null,current_timestamp,current_timestamp,' . "'N'" . ',0,null,null)';
            $id_ville = $max_id_ville;
            $res = maj_sql($req);
        }
    }
    return $id_ville;
}

?>

</body>

</html>