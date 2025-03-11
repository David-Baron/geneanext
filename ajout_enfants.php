<?php
//=====================================================================
// Ajout rapide d'enfants pour un couple
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok', 'annuler', 'Nom_Defaut', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

$Reference = Recup_Variable('Reference', 'N');
if (!$Reference) $Reference = -1;

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = LG_PERS_CHILDREN_ADD;
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Page interdite sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$Nom_Defaut = Secur_Variable_Post($Nom_Defaut, 50, 'S');

// Nombre maximum d'enfants saisissables en mode rapide
$max_enf_rapides = 6;

// Recup des variables passées dans l'URL : référence de la femme et du mari + ref union
$Conjoint_1 = Recup_Variable('mari', 'N');
if (!$Conjoint_1) $Conjoint_1 = -1;

$Conjoint_2 = Recup_Variable('femme', 'N');
if (!$Conjoint_2) $Conjoint_2 = -1;

//Demande de mise à jour
if ($bt_OK) { // TODO: lol

    $maj_site = false;

    // Détermination du nombre de lignes d'enfants et d'évènements;
    // on se base sur le nombre de variables PrenomsE_xx et Titre_xx
    $nb_l_enfants = 0;
    $nb_l_events = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'PrenomsE_') !== false) $nb_l_enfants++;
        if (strpos($key, 'Titre_') !== false) $nb_l_events++;
    }

    // Traitement de l'ajout rapide d'enfants à partir du formulaire dynamique
    $LeSexe = '';
    $nouv_ident = -1;
    $LesPrenoms = '';
    $idNom_Defaut = -1;

    // Balayage des lignes des enfants
    $deb_req_pers = 'insert into ' . nom_table('personnes')
        . '(Reference, Nom, Prenoms, Ne_le, Decede_Le, Sexe, '
        . 'Date_Creation,Date_Modification,Statut_Fiche,idNomFam,Diff_Internet,Ville_Naissance,Ville_Deces,Categorie)'
        . ' values (';
    $deb_req_nom_pers = 'insert into ' . nom_table('noms_personnes') .
        ' values(';
    $deb_req_fil = 'insert into ' . nom_table('filiations') .
        '(Enfant,Pere,Mere,Date_Creation,Date_Modification,Statut_Fiche) values (';

    for ($num_ligne = 1; $num_ligne <= $max_enf_rapides; $num_ligne++) {
        // On prend celui par défaut
        $LeNom = $Nom_Defaut;
        // On ne va chercher l'identifiant du nom par défaut qu'une fois
        if ($idNom_Defaut == -1) $idNom_Defaut = recherche_nom($Nom_Defaut);
        $idNom = $idNom_Defaut;

        $LesPrenoms = retourne_var_post('PrenomsE_', $num_ligne);
        $D_Nai = retourne_var_post('CNe_leE_', $num_ligne);
        $D_Dec = retourne_var_post('CDecede_leE_', $num_ligne);
        $LeSexe = retourne_var_post('SexeE_', $num_ligne);
        $VilNai = retourne_var_post('SelVille_Nai_', $num_ligne);
        $VilDec = retourne_var_post('SelVille_Dec_', $num_ligne);

        // Création de la personne si les prénoms sont connus
        if ($LesPrenoms != '') {

            if ($LeSexe != '') $LeSexe = '"' . $LeSexe . '"';
            else $LeSexe = 'null';

            // Création de la personne
            if ($nouv_ident == -1) $nouv_ident = Nouvel_Identifiant('Reference', 'personnes');
            else $nouv_ident++;
            $reqE = $deb_req_pers .
                $nouv_ident . ',"' . $LeNom . '","' . $LesPrenoms . '","' . $D_Nai . '","' . $D_Dec . '",' . $LeSexe .
                ',current_timestamp,current_timestamp,\'N\',' . $idNom . ',\'O\',' . $VilNai . ',' . $VilDec . ',0)';
            $res = maj_sql($reqE);
            $req = $deb_req_nom_pers . $nouv_ident . ',' . $idNom . ',\'O\',null)';
            $res = maj_sql($req);

            // Création de la filiation
            $reqE = $deb_req_fil . $nouv_ident . ',' . $Conjoint_1 . ',' . $Conjoint_2 . ',current_timestamp,current_timestamp,\'N\')';
            $res = maj_sql($reqE);

            $maj_site = true;
        }
    }

    // Traitement de l'ajout rapide d'évènements à partir du formulaire dynamique
    if ($nb_l_events > 0) {

        $deb_req_evt = 'insert into ' . nom_table('evenements') .
            ' (Identifiant_zone,Identifiant_Niveau,Code_Type,Titre,Date_Creation,Date_Modification,Statut_Fiche) ' .
            ' values ' .
            ' (0,0,\'';
        $deb_req_con_obj = 'insert into ' . nom_table('concerne_objet') .
            ' (Evenement,Reference_Objet,Type_Objet) ' .
            ' values (';

        for ($num_ligne = 1; $num_ligne <= $nb_l_events; $num_ligne++) {
            $LeType = retourne_var_post('Type_', $num_ligne);
            $LeTitre = retourne_var_post('Titre_', $num_ligne);

            if ($LeTitre != '') {
                $req = $deb_req_evt . $LeType . '\',\'' . $LeTitre . '\',current_timestamp,current_timestamp,\'N\')';
                $res = maj_sql($req);
                $req = $deb_req_con_obj . $connexion->lastInsertId() . ',' . $Reference . ',\'U\')';
                $res = maj_sql($req);
                $maj_site = true;
            }
        }
    }

    // Mise à jour de la date de mise à jour du site
    if ($maj_site) maj_date_site(true);

    // Retour arrière
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut($titre, $compl, 'Ajout_Enfants', $Conjoint_1 . "/" . $Conjoint_2);

    $nom_pere = '';
    $nom_mere = '';

    echo '<form id="saisie" method="post" action="' . $root . '/ajout_enfants.php?' . Query_Str() . '">';
    aff_origine();

    echo '<br />';
    if ($Conjoint_1 != 0) {
        if (Get_Nom_Prenoms($Conjoint_1, $Nom, $Prenoms)) {
            echo my_html(LG_FATHER) . LG_SEMIC . '<a ' . Ins_Ref_Pers($Conjoint_1) . '>' . $Prenoms . ' ' . $Nom . '</a>&nbsp;' . Affiche_Icone_Lien(Ins_Edt_Pers($Conjoint_1), 'fiche_edition', 'Modifier') . '<br />';
            $nom_pere = $Nom;
        }
    }
    if ($Conjoint_2 != 0) {
        if (Get_Nom_Prenoms($Conjoint_2, $Nom, $Prenoms)) {
            echo my_html(LG_MOTHER) . LG_SEMIC . '<a ' . Ins_Ref_Pers($Conjoint_2) . '>' . $Prenoms . ' ' . $Nom . '</a>&nbsp;' . Affiche_Icone_Lien(Ins_Edt_Pers($Conjoint_2), 'fiche_edition', 'Modifier') . '<br />';
            $nom_mere = $Nom;
        }
    }
    echo '<br />';

    // Enfants déjà saisis
    echo '<hr/>' . my_html(LG_PERS_CHILDREN_PRESENT) . LG_SEMIC . '<br />';
    if (($Conjoint_1 != 0) and ($Conjoint_2 != 0)) {
        $sqlE = 'select Enfant from ' . nom_table('filiations') .
            ' where pere = ' . $Conjoint_1 . ' and mere = ' . $Conjoint_2 . ' order by rang';
        $resE = lect_sql($sqlE);
        if ($resE->rowCount() > 0) {
            while ($row = $resE->fetch(PDO::FETCH_NUM)) {
                $Enfant = $row[0];
                if (Get_Nom_Prenoms($Enfant, $Nom, $Prenoms)) {
                    echo '<a ' . Ins_Edt_Pers($Enfant) . '>' . $Prenoms . '&nbsp;' . $Nom . '</a>&nbsp;';
                    echo '<a ' . Ins_Edt_Filiation($Enfant) . '>' . Affiche_Icone('fiche_edition', 'Modifier la filiation') . '</a>' . '<br />';
                }
            }
        }
    }

    // Ajout rapide d'enfants avec création
    echo '<br />';
    echo '<hr/>' . my_html(LG_PERS_CHILDREN_ADD);
    echo '<br />' . my_html(LG_PERS_DEFAULT_NAME) . ' :&nbsp;';

    echo '<input type="radio" name="Nom_Defaut" value="' . $nom_pere . '" checked="checked"/>' . $nom_pere . '&nbsp;';
    echo '<input type="radio" name="Nom_Defaut" value="' . $nom_mere . '"/>' . $nom_mere . '&nbsp;';

    echo '<table border="0" id="tblSampleE" width="80%" align="center">';
    echo '<tr align="center">';
    echo '<td class="rupt_table">' . my_html(LG_PERS_FIRST_NAME) . '</td>';
    echo '<td class="rupt_table">' . my_html(LG_PERS_BORN) . '</td>';
    echo '<td class="rupt_table">' . my_html(LG_PERS_DEAD) . '</td>';
    echo '<td class="rupt_table">' . my_html(LG_SEXE) . '</td>';
    echo '</tr>';

    for ($nb = 1; $nb <= $max_enf_rapides; $nb++) {
        if (pair($nb)) $style = 'class="liste"';
        else           $style = 'class="liste2"';
        echo '<tr>';
        echo '<td ' . $style . '><input type="text" size="20" name="PrenomsE_' . $nb . '" id="PrenomsE_' . $nb . '"/></td>';
        echo '<td ' . $style . '>';
        zone_date2('ANe_leE__' . $nb, 'Ne_leE_' . $nb, 'CNe_leE_' . $nb, '');
        aff_liste_villes('SelVille_Nai_' . $nb, true, false, 0);
        echo '</td>';
        echo '<td ' . $style . '>';
        zone_date2('ADecede_leE_' . $nb, 'Decede_leE_' . $nb, 'CDecede_leE_' . $nb, '');
        aff_liste_villes('SelVille_Dec_' . $nb, false, false, 0);
        echo '</td>';
        echo '<td ' . $style . '><input type="radio" name="SexeE_' . $nb . '" value="m"/>H';
        echo '<input type="radio" name="SexeE_' . $nb . '" value="f"/>F</td>';
        echo '</tr>';
    }

    echo '</table>';
    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);

    echo '</form>';

    Insere_Bas($compl);
} else {
    echo "<body bgcolor=\"#FFFFFF\">";
}
?>

</body>

</html>