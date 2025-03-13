<?php
//=====================================================================
// Affichage|fusion de deux personnes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'Sexe',
    'Numero',
    'Ne_le',
    'Ville_Naissance',
    'Decede_Le',
    'Ville_Deces',
    'prof',
    'parents',
    'conjoints',
    'comment',
    'arr_unions_1',
    'arr_unions_2'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
    // echo $nom_variables.' : '.$$nom_variables.'<br />';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen(LG_CH_FUSIONNER), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == LG_CH_FUSIONNER) $ok = 'OK';
// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$acces = 'M';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Compare_Persons'];    // Titre pour META
$niv_requis = 'C';

$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

    // Recup de la variable passée dans l'URL : référence des deux personnes
    $ref1 = Recup_Variable('ref1', 'N');
    $ref2 = Recup_Variable('ref2', 'N');

    // Accès aux données des personnes
    $n_personnes = nom_table('personnes');
    $deb_req = 'SELECT * FROM ' . $n_personnes . ' WHERE Reference = ';
    $req1 = $deb_req . $ref1;
    $result = lect_sql($req1);
    $pers1 = $result->fetch(PDO::FETCH_ASSOC);
    $req2 = $deb_req . $ref2;
    $result = lect_sql($req2);
    $pers2 = $result->fetch(PDO::FETCH_ASSOC);

    if ($bt_OK) {
        Ecrit_Entete_Page(my_html($titre), $contenu, $mots);
        include(__DIR__ . '/assets/js/monSSG.js');
    }

    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut($titre . ' pour ' . $pers1['Prenoms'] . ' ' . $pers1['Nom'], $compl, 'Fiche_Homonymes', '');

    // Demande de fusion
    if ($bt_OK) {

        // Sécurisation des variables postées
        $Sexe                 = Secur_Variable_Post($Sexe, 1, 'N');
        $Numero             = Secur_Variable_Post($Numero, 1, 'N');
        $Ne_le                 = Secur_Variable_Post($Ne_le, 1, 'N');
        $Ville_Naissance     = Secur_Variable_Post($Ville_Naissance, 1, 'N');
        $Decede_Le             = Secur_Variable_Post($Decede_Le, 1, 'N');
        $Ville_Deces         = Secur_Variable_Post($Ville_Deces, 1, 'N');
        $prof                 = Secur_Variable_Post($prof, 1, 'N');
        $parents             = Secur_Variable_Post($parents, 1, 'N');
        $conjoints             = Secur_Variable_Post($conjoints, 1, 'N');
        $comment             = Secur_Variable_Post($comment, 1, 'N');
        $arr_unions_1         = Secur_Variable_Post($arr_unions_1, 30, 'S');
        $arr_unions_2         = Secur_Variable_Post($arr_unions_2, 30, 'S');

        $n_filiations = nom_table('filiations');
        $maj_sexe = false;
        $maj_site = false;
        $req_mod = '';
        update_champ('Sexe', $Sexe, 'A');
        update_champ('Numero', $Numero, 'A');
        update_champ('Ne_le', $Ne_le, 'A');
        update_champ('Ville_Naissance', $Ville_Naissance, 'N');
        update_champ('Decede_Le', $Decede_Le, 'A');
        update_champ('Ville_Deces', $Ville_Deces, 'N');
        // Il y a une demande de mise à jour du sexe ; il ne faudra pas mettre à jour les conjoints
        if (strpos($req_mod, 'Sexe'))
            $maj_sexe = true;
        /*, 'numero'
		, 'ne_le', 'ne_a'
		, 'decede_le', 'decede_a'
		, 'prof', 'parents', 'conjoints', 'comment'
		*/
        if ($req_mod != '') {
            $req_mod = 'update ' . $n_personnes
                . ' set ' . $req_mod
                . ', Date_Modification = current_timestamp'
                . ' where Reference = ' . $ref1;
            $res = maj_sql($req_mod);
            $maj_site = true;
        }

        // Report des professions
        if ($prof == 2) {
            $maj_site = true;
            $n_participe = nom_table('participe');
            $sql = 'insert into ' . $n_participe
                . ' select Evenement,' . $ref1
                . ',Code_Role,Debut,Fin,Pers_Principal,Identifiant_zone,Identifiant_Niveau,Dans_Etiquette_GeneGraphe'
                . ' from ' . $n_participe
                . ' where Personne = ' . $ref2;
            $res = maj_sql($sql);
        }

        // Report des parents
        if ($parents == 2) {
            $maj_site = true;
            // Existe-t-il une filiation pour la personne 1 ?
            $enfant = -1;
            $sql = 'select Enfant from ' . $n_filiations . ' where Enfant = ' . $ref1 . ' limit 1';
            if ($res = lect_sql($sql)) {
                if ($filiation = $res->fetch(PDO::FETCH_NUM)) {
                    $enfant = $filiation[0];
                }
                $res->closeCursor();
                unset($res);
            }
            // Présence de filiation
            if ($enfant != -1) {
                $fin_req = ' where Reference_Objet = ' . $ref1 . " and Type_Objet = 'F'";
                // Suppression des commentaires
                $res = maj_sql('delete from ' . nom_table('commentaires') . $fin_req);
                // Suppression des liens vers les documents
                $res = maj_sql('delete from ' . nom_table('concerne_doc') . $fin_req);
                // Suppression des liens vers les évènements
                $res = maj_sql('delete from ' . nom_table('concerne_objet') . $fin_req);
                // Suppression des liens vers les images
                $res = maj_sql('delete from ' . nom_table('images') . ' where Reference = ' . $ref1 . " and Type_Ref = 'F'");
                // Suppression de la filiation elle-même
                $res = maj_sql('delete from ' . $n_filiations . ' where Enfant = ' . $ref1);
            }
            // Report des informations de la filiation 2
            $sql = 'insert into ' . $n_filiations
                . ' select ' . $ref1
                . ',Pere,Mere,Rang'
                . ',current_timestamp,current_timestamp'
                . ',Statut_Fiche'
                . ' from ' . $n_filiations
                . ' where Enfant = ' . $ref2;
            $res = maj_sql($sql);
        }

        // Bascule des unions et des enfants sur la personne 1
        if ($conjoints == 2) {
            $maj_site = true;
            // Bascule des unions
            $deb_sql = 'update ' . nom_table('unions') . ' set ';
            $sql = $deb_sql . 'Conjoint_1 = ' . $ref1 . ' where Conjoint_1 = ' . $ref2;
            //var_dump($sql);
            $res = maj_sql($sql);
            $sql = $deb_sql . 'Conjoint_2 = ' . $ref1 . ' where Conjoint_2 = ' . $ref2;
            //var_dump($sql);
            $res = maj_sql($sql);
            // Bascule des enfants
            $deb_sql = 'update ' . $n_filiations . ' set ';
            $sql = $deb_sql . 'Pere = ' . $ref1 . ' where Pere = ' . $ref2;
            //var_dump($sql);
            $res = maj_sql($sql);
            $sql = $deb_sql . 'Mere = ' . $ref1 . ' where Mere = ' . $ref2;
            //var_dump($sql);
            $res = maj_sql($sql);
        }

        if ($maj_site) maj_date_site();
    }

    echo '<form id="saisie" method="post" action="' . my_self() . '?' . Query_Str() . '">' . "\n";

    $csp = ' colspan="3"';
    $num_ligne = 0;

    echo '<br />';
    echo '<table width="85%" align="center" border="0"  >' . "\n";

    echo '<tr class="rupt_table"><th> </th>';
    echo '<th width="45%" ><a ' . Ins_Ref_Pers($ref1) . '>' . my_html(LG_CH_FUSION_PERS1) . '</a>';
    if ($_SESSION['estGestionnaire']) {
        echo ' <a ' . Ins_Edt_Pers($ref1) . '><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '"></a>';
    }
    echo '</th>';
    echo '<th width="45%"><a ' . Ins_Ref_Pers($ref2) . '>' . my_html(LG_CH_FUSION_PERS2) . '</a>';
    if ($_SESSION['estGestionnaire']) {
        echo ' <a ' . Ins_Edt_Pers($ref2) . '><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '"></a>';
    }
    echo '</th>';

    $z1 = libSexe($pers1['Sexe']);
    $z2 = libSexe($pers2['Sexe']);
    aff_zones(LG_SEXE, 'Sexe');

    $z1 = $pers1['Numero'];
    $z2 = $pers2['Numero'];
    aff_zones($LG_Sosa_Number, 'Numero');

    $z1 = Etend_date($pers1['Ne_le']);
    $z2 = Etend_date($pers2['Ne_le']);
    aff_zones(LG_PERS_BORN, 'Ne_le');

    $z1 = $pers1['Ville_Naissance'];
    $z2 = $pers2['Ville_Naissance'];
    $nomVille1 = '';
    $nomVille2 = '';
    if ($z1 <> 0) {
        $nomVille1 = lib_ville($z1, 'O');
    }
    if ($z2 <> 0) {
        if ($z2 <> $z1) $nomVille2 = lib_ville($z2, 'O');
        else $nomVille2 = $nomVille1;
    }
    $z1 = $nomVille1;
    $z2 = $nomVille2;
    aff_zones($LG_at, 'Ville_Naissance');

    $z1 = Etend_date($pers1['Decede_Le']);
    $z2 = Etend_date($pers2['Decede_Le']);
    aff_zones(LG_PERS_DEAD, 'Decede_Le');

    $z1 = $pers1['Ville_Deces'];
    $z2 = $pers2['Ville_Deces'];
    $nomVille1 = '';
    $nomVille2 = '';
    if ($z1 <> 0) {
        $nomVille1 = lib_ville($z1, 'O');
    }
    if ($z2 <> 0) {
        if ($z2 <> $z1) $nomVille2 = lib_ville($z2, 'O');
        else $nomVille2 = $nomVille1;
    }
    $z1 = $nomVille1;
    $z2 = $nomVille2;
    aff_zones($LG_at, 'Ville_Deces');

    $z1 = case_prof($ref1);
    $z2 = case_prof($ref2);
    aff_zones(LG_PERS_OCCU, 'prof');

    $z1 = case_parents($ref1);
    $z2 = case_parents($ref2);
    aff_zones(LG_PARENTS, 'parents');

    $z1 = case_conjoints($ref1, $pers1['Sexe']);
    $arr_unions_1 = $arr_unions;
    $z2 = case_conjoints($ref2, $pers2['Sexe']);
    $arr_unions_2 = $arr_unions;
    $lib = LG_HUSB_WIFE;
    aff_zones(LG_PERS_PARTNERS, 'conjoints');

    $z1 = case_comment($ref1);
    $z2 = case_comment($ref2);
    aff_zones($LG_Comment, 'comment');

    echo '</table>';

    echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . LG_TIP . '" title="' . LG_TIP . '">'
        . ' ' . LG_CH_FUSION_TIP1
        . '<br />' . LG_CH_FUSION_TIP2
        . '<br />' . LG_CH_FUSION_TIP3
        . '<br />' . LG_CH_FUSION_TIP4;
    echo '<br />' . "\n";

    echo '<input type="' . $hidden . '" name="arr_unions_1" value="' . $arr_unions_1 . '">';
    echo '<input type="' . $hidden . '" name="arr_unions_2" value="' . $arr_unions_2 . '">';

    // Formulaire pour le bouton retour
    //Bouton_Retour($lib_Retour,'?'.Query_Str());
    bt_ok_an_sup(LG_CH_FUSIONNER, $lib_Retour, '', '', false, false);

    Insere_Bas('');
}

// Champ à modifier ?
// Dans le cas où la donnée a été sélectionnée sur la personne 2
function update_champ($champ, $valeur, $le_type)
{
    global $req_mod, $pers2;
    if ($valeur == '2') Aj_Zone_Req($champ, $pers2[$champ], '-', $le_type, $req_mod);
}

function libSexe($sexe)
{
    $lib = LG_PERS_SEX_UNDEF;
    switch ($sexe) {
        case 'f':
            $lib = LG_SEXE_WOMAN;
            break;
        case 'm':
            $lib = LG_SEXE_MAN;
    }
    return $lib;
}

function aff_zones($titre, $nom_rb)
{
    global $z1, $z2;
    if (($z1 == '') and ($z2 == '')) {
        $classe = 'ligne_absent';
    } else {
        if ($z1 != $z2) $classe = 'ligne_diff';
        else $classe = 'ligne_no_diff';
    }
    $rb1 = '';
    if ($z1 != '') {
        $rb1 = '<input type="radio" name="' . $nom_rb . '" value="1" checked="checked">';
    }
    $rb2 = '';
    // On ne met le bouton radio sur la 2ème colonne que si le champ est différent
    if (($z2 != '') and ($z2 != $z1)) {
        $rb2 = '<input type="radio" name="' . $nom_rb . '" value="2"';
        if ($z1 == '') {
            $rb2 .= ' checked="checked"';
        }
        $rb2 .= '>';
    }
    echo '<tr class="' . $classe . '"><td>' . ucfirst($titre) . '</td>';
    echo '<td>' . $rb1 . $z1 . '</td>';
    echo '<td>' . $rb2 . $z2 . '</td>';
    echo '</tr>';
}

function case_prof($reference)
{
    $profession = '';
    $sqlP = 'select Titre from ' . nom_table('evenements') . ' where Code_Type = "OCCU"'
        . ' and Reference in (select Evenement from ' . nom_table('participe') . ' where Personne = ' . $reference . ')';
    if ($resP = lect_sql($sqlP)) {
        while ($enregP = $resP->fetch(PDO::FETCH_NUM)) {
            if ($profession != '') $profession .= ', ';
            $profession .= my_html($enregP[0]);
        }
        $resP->closeCursor();
    }
    return $profession;
}

function case_comment($reference)
{
    global $Commentaire;
    $Existe_Commentaire = Rech_Commentaire($reference, 'P');
    return $Commentaire;
}

function case_parents($reference)
{
    $lib_parents = '';
    if (Get_Parents($reference, $Pere, $Mere, $Rang)) {
        if ($Pere != 0) {
            if (Get_Nom_Prenoms($Pere, $Nom, $Prenoms)) {
                $lib_parents .= '<a ' . Ins_Ref_Pers($Pere) . '>' . $Prenoms . ' ' . $Nom . '</a>';
            }
        }
        if ($Mere != 0) {
            if (Get_Nom_Prenoms($Mere, $Nom, $Prenoms)) {
                if ($lib_parents != '') $lib_parents .= ' et ';
                $lib_parents .= '<a ' . Ins_Ref_Pers($Mere) . '>' . $Prenoms . ' ' . $Nom . '</a>';
            }
        }
        if ($lib_parents != '') $lib_parents .= "\n";
    }
    return $lib_parents;
}

//	Conjoints et enfants
function case_conjoints($reference, $sexe)
{
    global $nb_conjoints, $arr_unions;
    $lib_conjoints = '';
    $sql = 'select * from ' . nom_table('unions') . ' where ';
    switch ($sexe) {
        case 'm':
            $sql = $sql . 'Conjoint_1 = ' . $reference;
            break;
        case 'f':
            $sql = $sql . 'Conjoint_2 = ' . $reference;
            break;
        default:
            $sql = $sql . 'Conjoint_1 = ' . $reference . ' or Conjoint_2 =' . $reference;
            break;
    }
    //    Tri des unions par date
    $sql .= ' ORDER BY maries_Le';
    $arr_unions = '';
    $numUnion = 0;
    $resUn = lect_sql($sql);
    $nb_conjoints = $resUn->rowCount();
    if ($nb_conjoints > 0) {
        while ($enreg = $resUn->fetch(PDO::FETCH_ASSOC)) {
            if ($numUnion > 0) {
                $lib_conjoints .= '<hr align="left" width="85%"/>';
            }
            $numUnion++;
            //
            $Ref_Union = $enreg['Reference'];
            $Date_Mar  = $enreg['Maries_Le'];
            $Ville_Mar = $enreg['Ville_Mariage'];
            $Mari      = $enreg['Conjoint_1'];
            $Femme     = $enreg['Conjoint_2'];
            //
            $Existe_Commentaire = Rech_Commentaire($enreg['Reference'], 'U');
            //          
            switch ($sexe) {
                case 'm':
                    $Conj = $Femme;
                    break;
                case 'f':
                    $Conj = $Mari;
                    break;
                default:
                    break;
            }
            // Sauvegarde de la liste des unions
            if ($arr_unions != '') $arr_unions .= ',';
            $arr_unions .= $Ref_Union;
            //
            if (Get_Nom_Prenoms($Conj, $Nom, $Prenoms)) {
                $lib_conjoints .= '<a ' . Ins_Ref_Pers($Conj) . '>' . $Prenoms . ' ' . $Nom . '</a> '
                    . Affiche_Icone_Lien(Ins_Ref_Fam($Ref_Union), 'text', my_html(LG_CH_COUPLE));
            }
            //
            if (($Date_Mar != '') or ($Ville_Mar)) {
                $lib_conjoints .= ' ' . my_html(LG_CH_MARIED);
                $lib_conjoints .= ' ' . Etend_date($Date_Mar);
                $lib_conjoints .= ' &agrave; ' . lib_ville($Ville_Mar);
            }
            //	récupération des enfants
            $enfants = Aff_Enfants2($Mari, $Femme);
            $lib_conjoints .= $enfants;
        }
    }
    return $lib_conjoints;
}

function Aff_Enfants2($Mari, $Femme)
{
    global $n_personnes;
    $lib_enfants = '';
    if (($Mari) or ($Femme)) {
        $sql = 'select Enfant from ' . nom_table('filiations') . ' where pere = ' . $Mari . ' and mere = ' . $Femme . ' order by rang';
        $resE = lect_sql($sql);
        if ($resE->rowCount() > 0) {
            $lib_enfants .= '<br />Enfants :<br />' . "\n";
            while ($row = $resE->fetch(PDO::FETCH_NUM)) {
                $Enfant = $row[0];
                //
                $sqlEnf = 'select Nom, Prenoms, Ne_le, Decede_Le, Diff_Internet, Sexe from ' . $n_personnes .
                    ' where Reference = ' . $Enfant . ' limit 1';
                $resEnf = lect_sql($sqlEnf);
                $enrEnf = $resEnf->fetch(PDO::FETCH_ASSOC);
                $sexe = $enrEnf['Sexe'];
                $lib_enfants .= '  <a ' . Ins_Ref_Pers($Enfant) . '>' . my_html($enrEnf['Prenoms'] . ' ' . $enrEnf['Nom']) . '</a> ';
                $Ne = $enrEnf['Ne_le'];
                $Date_Nai = Etend_date($Ne);
                if ($Date_Nai != '') {
                    $lib_enfants .= '<br />   ' . Lib_sexe(my_html(LG_CH_BORN), $sexe) . ' ' . $Date_Nai;
                }
                $Date_Dec = Etend_date($enrEnf['Decede_Le']);
                if ($Date_Dec != '') {
                    if ($Date_Nai != '') $lib_enfants .= ', ';
                    $lib_enfants .= Lib_sexe(my_html(LG_CH_DEAD), $sexe) . ' ' . $Date_Dec;
                }
                $lib_enfants .= '<br />' . "\n";
            }
        }
        $resE->closeCursor();
    }
    return $lib_enfants;
}

//	Pour éviter les cellules de tableaux sans bordure
function mefValeur($valeur)
{
    if ($valeur == '') {
        return ' ';
    }
    return $valeur;
}

?>
</body>

</html>