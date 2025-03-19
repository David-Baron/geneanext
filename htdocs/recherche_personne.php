<?php

//=====================================================================
// Fonction de recherche générique sur les personnes
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$tab_variables = array(
    'ok',
    'annuler',
    'reprise',
    'Memo_Req',
    'TitreReq',
    'Liste_Requetes',
    'NomP',
    'Son',
    'Prenoms',
    'Surnom',
    'Sexe',
    'NumeroP',
    'Annee',
    'TypeAnnee',
    'Tolerance',
    'Ville_Naissance',
    'Ville_Deces',
    'Diffu',
    'Statut_Fiche',
    'Categorie',
    'New_Window',
    'Sortie',
    'Tri',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen($lib_Rechercher), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_Rechercher) $ok = 'OK';

// Gestion standard des pages
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Sch_Pers'];     // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Suite sécurisation des variables postées
$reprise         = Secur_Variable_Post($reprise, 1, 'S'); // 1 seul caractère suffit
$Memo_Req        = Secur_Variable_Post($Memo_Req, 1, 'S');
$TitreReq        = Secur_Variable_Post($TitreReq, 80, 'S');
$Liste_Requetes  = Secur_Variable_Post($Liste_Requetes, 1, 'N');
$NomP            = Secur_Variable_Post($NomP, 50, 'S');
$Son             = Secur_Variable_Post($Son, 1, 'S');
$Prenoms         = Secur_Variable_Post($Prenoms, 50, 'S');
$Surnom          = Secur_Variable_Post($Surnom, 50, 'S');
$Sexe            = Secur_Variable_Post($Sexe, 1, 'S');
$Numero          = Secur_Variable_Post($NumeroP, 20, 'S');
$Annee           = Secur_Variable_Post($Annee, 4, 'S');
$TypeAnnee       = Secur_Variable_Post($TypeAnnee, 1, 'S');
$Tolerance       = Secur_Variable_Post($Tolerance, 1, 'N');
$Ville_Naissance = Secur_Variable_Post($Ville_Naissance, 25, 'S');
$Ville_Deces     = Secur_Variable_Post($Ville_Deces, 25, 'S');
$Diffu           = Secur_Variable_Post($Diffu, 1, 'S');
$Statut_Fiche    = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$Sortie          = Secur_Variable_Post($Sortie, 1, 'S');
$Tri             = Secur_Variable_Post($Tri, 2, 'S');
$New_Window      = Secur_Variable_Post($New_Window, 1, 'S');
$Categorie       = Secur_Variable_Post($Categorie, 1, 'S');

function Recup_Lib_Categ($num_categ)
{
    $ret = '';
    $sql = 'select Titre from ' . nom_table('categories') . ' where Identifiant = ' . $num_categ . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($enreg = $res->fetch(PDO::FETCH_NUM))
            $ret = $enreg[0];
    }
    return $ret;
}

function aff_n_dec()
{
    global $row;
    $Ne = $row[3];
    $Decede = $row[4];
    if (($Ne != '') or ($Decede != '')) {
        echo ' (';
        if ($Ne != '') echo '&deg; ' . Etend_date($Ne);
        if ($Decede != '') {
            if ($Ne != '') echo ', ';
            echo '+ ' . Etend_date($Decede);
        }
        echo ')';
    }
}

function Ajb_Zone_Req($NomRub, $Rub, $TypRub, &$LaReq, $Zone)
{
    global $memo_criteres, $separ;
    if ($Rub != '') {
        $C_Rub = $Rub;
        if (($NomRub == 'Ville_Naissance') or ($NomRub == 'Ville_Deces'))
            $C_Rub = lib_ville($Rub);
        $le_crit = $C_Rub;
        if ($NomRub != 'Categorie') {
            echo '   ' . $Zone . ' = ' . $le_crit . '<br />';
        }
        // Récupération de libellé de la catégorie à partir du code
        else {
            echo '   ' . my_html($Zone) . ' = ' . Recup_Lib_Categ($le_crit) . '<br />';
        }
        $memo_criteres = $memo_criteres . $Zone . ' = ' . $C_Rub . $separ;

        if ($LaReq != '') $LaReq = $LaReq . ' and ';
        if ($TypRub == 'A') {
            // Recherche de type like ou = ?
            if (strpos($Rub, '*') === false) {
                $oper = '=';
            } else {
                $oper = ' like ';
                $Rub = str_replace('*', '%', $Rub);
            }
            $LaReq = $LaReq . ' upper(p.' . $NomRub . ')' . $oper;
            $LaReq = $LaReq . '"' . strtoupper($Rub) . '"';
        } else {
            $LaReq = $LaReq . ' p.' . $NomRub . '=' . $Rub;
        }
    }
}

$compl = Ajoute_Page_Info(650, 300);

if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);

if ($Sortie != 't') {
    Insere_Haut($titre, $compl, 'Recherche_Personne', '');
} else {
    echo '</head>' . "\n";
    echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td align="center"><b>' . StripSlashes($titre) . '</b></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
}

//Demande de recherche
if ($bt_OK) {

    if (($NomP != '') and (strpos($NomP, ';') != 0)) {
        $x = explode(';', $NomP);
        $NomP = $x[0];
        $Prenoms = $x[1];
        $Prenoms = $Prenoms . '*';
    }

    $erreur = 0;
    if ($Sortie == 'c') {
        // Traiter le cas d'erreur sur l'ouverture du fichier
        $nom_fic = $chemin_exports . 'recherche.csv';
        $fp = fopen($nom_fic, 'w+');
    }
    // Init des zones de requête
    echo LG_PERS_REQ_FIELDS . ' ' . '<br />';
    $req = '';
    $memo_criteres = '';
    // Constitution de la requête d'extraction
    if ($Liste_Requetes == 0) {
        Ajb_Zone_Req('Prenoms', $Prenoms, 'A', $req, LG_PERS_FIRST_NAME);
        Ajb_Zone_Req('Surnom', $Surnom, 'A', $req, LG_PERS_SURNAME);
        if (($Sexe == 'm') or ($Sexe == 'f'))
            Ajb_Zone_Req('Sexe', $Sexe, 'A', $req, LG_SEXE);
        Ajb_Zone_Req('Numero', $Numero, 'A', $req, LG_PERS_NUMBER);
        if ($Ville_Naissance > -1)
            Ajb_Zone_Req('Ville_Naissance', $Ville_Naissance, 'N', $req, LG_PERS_REQ_BORN_TOWN);
        if ($Ville_Deces > -1)
            Ajb_Zone_Req('Ville_Deces', $Ville_Deces, 'N', $req, LG_PERS_REQ_DEATH_TOWN);
        if (($Diffu == 'O') or ($Diffu == 'N'))
            Ajb_Zone_Req('Diff_Internet', $Diffu, 'A', $req, $LG_show_on_internet);
        Ajb_Zone_Req('Statut_Fiche', $Statut_Fiche, 'A', $req, LG_PERS_REQ_FILE_STATUS);
        Ajb_Zone_Req('Categorie', $Categorie, 'A', $req, LG_PERS_CATEGORY);
        // Traitement spécifique de l'année : naissance, décès, vivant
        if ($Annee != '') {
            // On met l'année sur 4 caractères
            if (!is_numeric($Annee)) $Annee = 9999;
            $Annee = str_pad(trim($Annee), 4, '0', STR_PAD_LEFT);
            $aff_annee = '';
            if ($Tolerance != '0') $plu_tolerance = pluriel($Tolerance);
            $supReq = '';
            switch ($TypeAnnee) {
                case 'n':
                    if ($Tolerance == '0') {
                        $supReq = ' substr(Ne_le,1,4) = ' . $Annee;
                    } else {
                        $annee_inf = $Annee - $Tolerance;
                        $annee_sup = $Annee + $Tolerance;
                        $supReq = ' substr(Ne_le,1,4) between ' . $annee_inf . ' and ' . $annee_sup;
                    }
                    $supReq .= ' and substr(Ne_le,10,1) = \'L\'';
                    $aff_annee = LG_PERS_REQ_BORN_IN . $Annee;
                    if ($Tolerance != '0') $aff_annee .= my_html(' ' . LG_PERS_REQ_MORE_LESS_1 . $Tolerance . ' ' . LG_PERS_REQ_MORE_LESS_2 . $plu_tolerance . ')');
                    break;
                case 'd':
                    if ($Tolerance == '0') {
                        $supReq = ' substr(Decede_Le,1,4) = ' . $Annee;
                    } else {
                        $annee_inf = $Annee - $Tolerance;
                        $annee_sup = $Annee + $Tolerance;
                        $supReq = ' substr(Decede_Le,1,4) between ' . $annee_inf . ' and ' . $annee_sup;
                    }
                    $supReq .= ' and substr(Decede_Le,10,1) = \'L\'';
                    $aff_annee = LG_PERS_REQ_DEATH_IN . $Annee;
                    if ($Tolerance != '0') $aff_annee .= ' (+ ou -' . $Tolerance . ' an' . $plu_tolerance . ')';
                    break;
                case 'v':
                    $Annee_Min = $Annee - $annees_maxi_vivant;
                    $supReq = " (substr(Ne_le,1,4) between $Annee_Min and $Annee and substr(Ne_le,10,1) = 'L')"
                        . " and ((substr(Decede_Le,1,4) >= $Annee and substr(Decede_Le,10,1) = 'L') or Decede_Le = '')";


                    $aff_annee = 'vivant en ' . $Annee;
                    break;
            }
            echo '   ' . $aff_annee . '<br />';
            $memo_criteres = $memo_criteres . $aff_annee . $separ;
            if ($req != '') $req = $req . ' and ';
            $req = $req . $supReq;
        }
        // Gestion du nom
        $NomP_I = $NomP;
        if ($NomP != '') {
            $NomP_SL = $NomP;
            echo '   ' . $LG_Name . ' = ' . $NomP_SL . ' (';
            if ($req != '') $req .= ' and ';
            switch ($Son) {
                case 'o':
                    $crit_nom = LG_PERS_REQ_SPELL_EXACT;
                    break;
                case 'p':
                    $crit_nom = LG_PERS_REQ_SOUND_EXACT;
                    break;
                case 'a':
                    $crit_nom = LG_PERS_REQ_SOUND_NEAR;
                    break;
            }
            echo $crit_nom . ')<br />';
            $memo_criteres = $memo_criteres . my_html($LG_Name) . ' = ' . $NomP . ' (' . $crit_nom . ')' . $separ;

            if (strpos($NomP, '*') === false) $oper = '=';
            else {
                $oper = ' like ';
                $NomP = str_replace('*', '%', $NomP);
            }

            // L'utilisateur a demandé une recherche phonétique
            if ($Son != 'o') {
                // Transformation du nom en phonétique
                require(__DIR__ . '/../app/Phonetique.php');
                $codePho = new Phonetique();
                $NomP = $codePho->calculer($NomP);
            }
            switch ($Son) {
                // Demande basée sur l'orthographe exacte
                case 'o':
                    $req .= 'Reference IN (' .
                        ' SELECT idPers ' .
                        ' FROM ' . nom_table('noms_personnes') .
                        ' WHERE idNom IN (' .
                        ' SELECT idNomFam' .
                        ' FROM ' . nom_table('noms_famille') .
                        ' WHERE upper(nomFamille) ' . $oper . ' "' . strtoupper($NomP) . '")) ';
                    break;
                // Demande basée sur la phonétique exacte
                case 'p':
                    $req .= 'Reference IN (' .
                        ' SELECT idPers' .
                        ' FROM ' . nom_table('noms_personnes') .
                        ' WHERE idNom IN (' .
                        ' SELECT idNomFam' .
                        ' FROM ' . nom_table('noms_famille') .
                        ' WHERE codePhonetique ' . $oper . ' "' . $NomP . '")) ';
                    break;
                // Demande basée sur la phonétique approchante
                case 'a':
                    $req .= 'Reference IN (' .
                        ' SELECT idPers' .
                        ' FROM ' . nom_table('noms_personnes') .
                        ' WHERE idNom IN (' .
                        ' SELECT idNomFam' .
                        ' FROM ' . nom_table('noms_famille') .
                        ' WHERE lower(codePhonetique) ' . $oper . ' "' . strtolower($NomP) . '")) ';
                    break;
                default:
                    break;
            }
        }
        // L'utilisateur a demandé à mémoriser la requête
        if ($Memo_Req == 'O') {
            require(__DIR__ . '/../app/ressources/fonctions_maj.php');
            echo my_html(LG_PERS_REQ_SAVE) . ' ' . $TitreReq . '<br />';
            $req_memo = '';
            Ins_Zone_Req($TitreReq, 'A', $req_memo);
            $memo_criteres = html_entity_decode($memo_criteres, ENT_QUOTES, $def_enc);
            Ins_Zone_Req($memo_criteres, 'A', $req_memo);
            $req_sl = $req;
            Ins_Zone_Req($req_sl, 'A', $req_memo);
            $req_memo = 'insert into ' . nom_table('requetes') . ' values(null,' . $req_memo . ')';
            $res = maj_sql($req_memo);
        }
    }
    // L'utilisateur a demandé à utiliser une requête mémorisée
    else {
        $sql = 'select Criteres, Code_SQL from ' . nom_table('requetes') . ' where Reference  = ' . $Liste_Requetes . ' limit 1';
        if ($res = lect_sql($sql)) {
            if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                $crit = my_html($enreg[0]);
                $req  = $enreg[1];
                // Affichage des critères mémorisés
                $liste_crit = explode($separ, $crit);
                $nb_crit = count($liste_crit);
                if ($nb_crit > 0) {
                    for ($nb = 0; $nb < $nb_crit - 1; $nb++) {
                        $le_crit = $liste_crit[$nb];
                        $est_cat = false;
                        if (strpos($le_crit, 'Catégorie =') !== false) $est_cat = true;
                        if (!$est_cat) {
                            echo '   ' . $le_crit . '<br />';
                        }
                        // Récupération de libellé de la catégorie à partir du code
                        else {
                            $decoup = explode("=", $le_crit);
                            $num_cat = intval(trim($decoup[1]));
                            echo '   ' . trim($decoup[0]) . ' = ' . Recup_Lib_Categ($num_cat) . '<br />';
                        }
                    }
                }
            }
        }
        $res->closeCursor();
    }

    // Exéution de la requête
    if ($req != '') {

        // Constitution de la partie champs à récupérer
        // Pour les sorties csv, on va récupérer tous les champs alors que sur les autres sorties, la référence, le nom et le prénom suffisent
        if ($Sortie == 'c') {
            $req2 = 'select p.Reference,p.Nom,p.Prenoms,p.Sexe,p.Numero,p.Ne_le,p.Ville_Naissance,n.Nom_Ville,' .
                'p.Decede_Le,p.Ville_Deces,d.Nom_Ville,p.Diff_Internet,p.Date_Creation,p.Date_Modification,p.Statut_Fiche,p.Surnom' .
                ' from ' . nom_table('personnes') . ' p, ' .
                nom_table('villes') . ' n, ' .
                nom_table('villes') . ' d ' .
                'where p.Reference <> 0 ' .
                'and p.Ville_Naissance = n.Identifiant_zone ' .
                'and p.Ville_Deces = d.Identifiant_zone';
        } else {
            $req2 = 'select Reference, Nom, Prenoms, Ne_le, Decede_Le from ' . nom_table('personnes') . ' p where Reference <> 0';
        }
        // Surcouche non privilégiés
        if (!$est_privilegie) $req2 = $req2 . ' and Diff_Internet = \'O\'';

        $req = $req2 . ' and ' . $req . ' order by ';
        // Critère de tri par défaut
        $Critere = 'p.Nom,p.Prenoms';
        switch ($Tri) {
            case 'dn':
                $Critere = 'p.Ne_le,p.Nom,p.Prenoms';
                break;
            case 'dd':
                $Critere = 'p.Decede_Le,p.Nom,p.Prenoms';
                break;
        }
        $req .= $Critere;

        if ($res = lect_sql($req)) {
            $nb_lignes = $res->rowCount();
            $plu = pluriel($nb_lignes);
            echo $nb_lignes . ' ' . LG_PERS_REQ_PERS_FOUND_1 . $plu . ' ' . LG_PERS_REQ_PERS_FOUND_2 . $plu . '<br /><br />';
            // Recherche du nom sur les sites gratuits ; pas sur les sites gratuits non premium
            if (($Sortie == 'e') and ((!$SiteGratuit) or ($Premium))) {
                if ($NomP != '') {
                    echo '<a href="' . $adr_rech_gratuits . '?ok=ok&amp;NomP=' . str_replace('%', '*', $NomP) . '" target="_blank">'
                        . LG_PERS_REQ_FIND_NAME . '</a><br /><br />' . "\n";
                }
            }
            // Récup de la liste des champs
            $champs = get_fields($req, true);
            if ($champs) {
                $num_fields = count($champs);
            }

            if ($Sortie == 'c') {
                $ligne = '';
                for ($nb = 0; $nb < $num_fields; $nb++) {
                    $nom_champ = $champs[$nb];
                    if ($nom_champ == 'Ne_le') {
                        $nom_champ = 'Precision_Naissance;' . $nom_champ . ';Calendrier_Naissance';
                        $c_ne = $nb;
                    }
                    if ($nom_champ == 'Decede_Le') {
                        $nom_champ = 'Precision_Deces;' . $nom_champ . ';Calendrier_Deces';
                        $c_decede = $nb;
                    }
                    $ligne .= $nom_champ . ';';
                }
                fputs($fp, $ligne);
            }
            $target = ($New_Window == 'O') ? true : false;
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                $ref = $row[0];
                $prenom = my_html($row[2]);
                $nom = my_html($row[1]);

                switch ($Sortie) {
                    case 'e':
                        echo '<a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref . '" target="_blank">' . $nom . ' ' . $prenom . '</a>';
                        aff_n_dec();
                        if ($est_gestionnaire) echo ' ' . Affiche_Icone_Lien('href="' . $root . '/edition_personne.php?Refer=' . $ref . '"', 'fiche_edition', $LG_modify);
                        echo '<br />' . "\n";
                        break;
                    case 't':
                        echo $nom . ' ' . $prenom;
                        aff_n_dec();
                        echo '<br />' . "\n";
                        break;
                    case 'c':
                        $ligne = '';
                        for ($nb = 0; $nb < $num_fields; $nb++) {
                            $contenu = $row[$nb];
                            // On retravaille les champs naissance et décès
                            if (($nb == $c_ne) or ($nb == $c_decede)) {
                                $ligne .= Retourne_Date_CSV($contenu) . ';';
                            } else $ligne .= '"' . $contenu . '";';
                        }
                        fputs($fp, $ligne);
                        break;
                }
            }
            if ($Sortie == 'c') {
                fclose($fp);
                echo '<br />' . my_html($LG_csv_available_in) . ' <a href="' . $nom_fic . '">' . $nom_fic . '</a><br />' . "\n";
            }
        } else {
            echo '<br />' . my_html(LG_PERS_REQ) . ' ' . $req;
            echo '<center><font color="red"><br><br><br><h2>' . LG_PERS_REQ_ERROR . '</h2></font></center>';
        }
    }

    if ($Sortie != 't') {
        // Nouvelle recherche
        echo '<form id="nouvelle" method="post" action="' . my_self()    . '">' . "\n";
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        echo '<input type="hidden" name="reprise" value=""/>';
        echo '<input type="hidden" name="NomP" value="' . $NomP . '"/>';
        echo '<input type="hidden" name="Son" value="' . $Son . '"/>';
        echo '<input type="hidden" name="Prenoms" value="' . $Prenoms . '"/>';
        echo '<input type="hidden" name="Surnom" value="' . $Surnom . '"/>';
        echo '<input type="hidden" name="NumeroP" value="' . $NumeroP . '"/>';
        echo '<input type="hidden" name="Sexe" value="' . $Sexe . '"/>';
        echo '<input type="hidden" name="Annee" value="' . $Annee . '"/>';
        echo '<input type="hidden" name="Tolerance" value="' . $Tolerance . '"/>';
        echo '<input type="hidden" name="TypeAnnee" value="' . $TypeAnnee . '"/>';
        echo '<input type="hidden" name="Ville_Naissance" value="' . $Ville_Naissance . '"/>';
        echo '<input type="hidden" name="Ville_Deces" value="' . $Ville_Deces . '"/>';
        echo '<input type="hidden" name="Diffu" value="' . $Diffu . '"/>';
        echo '<input type="hidden" name="Statut_Fiche" value="' . $Statut_Fiche . '"/>';
        echo '<input type="hidden" name="Categorie" value="' . $Categorie . '"/>';
        echo '<input type="hidden" name="Tri" value="' . $Tri . '"/>';
        echo '<input type="hidden" name="New_Window" value="' . $New_Window . '"/>';
        echo '<br />';
        echo '<div class="buttons">';
        echo '<button type="submit" class="positive"><img src="' . $root . '/assets/img/' . $Icones['chercher'] . '" alt=""/>' . $lib_Nouv_Rech . '</button>';
        if ((!$SiteGratuit) or ($Premium)) {
            echo '<button type="submit" onclick="document.forms.nouvelle.reprise.value=\'reprise\'; "' .
                ' class="positive"><img src="' . $root . '/assets/img/' . $Icones['chercher_plus'] . '" alt=""/>' . $lib_Nouv_Rech_Aff . '</button>';
        }
        echo '</div>';
        echo '</form>' . "\n";
    }
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    $sql = 'select Identifiant_zone, Nom_Ville from ' . nom_table('villes') . ' order by Nom_Ville';
    $res = lect_sql($sql);

    $sql_ref = 'select max(Reference) from ' . nom_table('requetes');
    $resmax = lect_sql($sql_ref);
    $enrmax = $resmax->fetch(PDO::FETCH_NUM);
    $LeMax = $enrmax[0];
    if ($LeMax == '') $LeMax = 0;
    $resmax->closeCursor();

    echo '<form id="saisie" method="post">' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo '<br />';
    echo '<table width="90%" class="table_form">' . "\n";

    if ((!$SiteGratuit) or ($Premium)) {
        // La mémorisation n'est ouverte qu'au gestionnaire
        if ($est_gestionnaire) {
            echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_REQ_REQUEST_SAVE) . '</td><td class="value">';
            $LeMax2 = $LeMax + 1;
            echo '<input type="checkbox" name="Memo_Req" value="O"/> ' . LG_PERS_REQ_REQUEST_TITLE . ' <input type="text" size="80" name="TitreReq" value="Requ&ecirc;te ' . $LeMax2 . '"/>';
            echo '</td></tr>' . "\n";
        }
        // L'utilisation d'une requête est à partir du niveau privilégié
        if ($est_privilegie) {
            if ($LeMax > 0) {
                echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_REQ_REQUEST_USE) . '</td><td class="value">';
                $sql_req = 'select Reference, Titre from ' . nom_table('requetes') . ' order by Titre';
                $res_req = lect_sql($sql_req);
                echo '<select name="Liste_Requetes">' . "\n";
                echo '<option value="0">-- ' . LG_PERS_REQ_REQUEST_CHOOSE . ' --</option>' . "\n";
                while ($row = $res_req->fetch(PDO::FETCH_NUM)) {
                    echo '<option value="' . $row[0] . '">' . $row[1] . '</option>' . "\n";
                }
                echo "</select>\n";
                $res_req->closeCursor();
                echo '</td></tr>' . "\n";
            }
            echo '<tr><td colspan="2"> </td></tr>';
        }
    }

    echo '<tr><td class="label" width="20%">' . ucfirst($LG_Name) . '</td><td class="value">';
    echo '<input type="text" size="50" name="NomP"';
    if ($reprise) echo ' value="' . $NomP . '"';
    echo '/>';
    echo '<input type="radio" id="son_o" name="Son" value="o"';
    if ($reprise) {
        if ($Son == 'o') echo ' checked';
    } else echo ' checked';;
    echo '/><label for="son_o">' . LG_PERS_REQ_SPELL_EXACT . '</label> ';
    echo '<input type="radio" id="son_p" name="Son" value="p"';
    if ($reprise) {
        if ($Son == 'p') echo ' checked';
    }
    echo '/><label for="son_p">' . LG_PERS_REQ_SOUND_EXACT . '</label> ';
    echo '<input type="radio" id="son_a" name="Son" value="a"';
    if ($reprise) {
        if ($Son == 'a') echo ' checked';
    }
    echo '/><label for="son_a">' . LG_PERS_REQ_SOUND_NEAR . '</label>';
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_FIRST_NAME) . '</td><td class="value">';
    echo '<input type="text" size="50" name="Prenoms"';
    if ($reprise) echo ' value="' . $Prenoms . '"';
    echo '/></td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_SURNAME) . '</td><td class="value">';
    echo '<input type="text" size="50" name="Surnom"';
    if ($reprise) echo ' value="' . $Surnom . '"';
    echo '/></td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_SEXE) . '</td><td class="value">';
    echo '<input type="radio" id="Sexe_m" name="Sexe" value="m"';
    if ($reprise) {
        if ($Sexe == 'm') echo ' checked';
    }
    echo '/><label for="Sexe_m">' . LG_SEXE_MAN . '</label> ';
    echo '<input type="radio" id="Sexe_f" name="Sexe" value="f"';
    if ($reprise) {
        if ($Sexe == 'f') echo ' checked';
    }
    echo '/><label for="Sexe_f">' . LG_SEXE_WOMAN . '</label>';
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_NUMBER) . '</td><td class="value">';
    echo '<input type="text" size="20" name="NumeroP" id="NumeroP"';
    if ($reprise) echo ' value="' . $NumeroP . '"';
    echo '/>';
    // Calculette pour étendre le numéro Sosa
    echo '<img id="calc1" src="' . $root . '/assets/img/' . $Icones['calculette'] . '" alt="' . LG_PERS_CALC_SOSA . '" title="' . LG_PERS_CALC_SOSA . '"' .
        ' onclick="etend_num_sosa();document.getElementById(\'NumeroP\').focus();"/>' . "\n";
    echo ' </td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_REQ_YEAR) . '</td><td class="value">';
    echo '<input type="text" size="4" name="Annee"';
    if ($reprise) echo ' value="' . $Annee . '"';
    echo '/>' . "\n";
    echo '<input type="radio" id="TypeAnnee_n" name="TypeAnnee" value="n"';
    if ($reprise) {
        if ($TypeAnnee == 'n') echo ' checked';
    } else echo ' checked="checked"';
    echo '/><label for="TypeAnnee_n">' . $LG_birth . '</label> ';
    echo '<input type="radio" id="TypeAnnee_d" name="TypeAnnee" value="d"';
    if ($reprise) {
        if ($TypeAnnee == 'd') echo ' checked';
    }
    echo '/><label for="TypeAnnee_d">' . $LG_death . '</label> ';
    echo ' (+/- <img src="' . $root . '/assets/img/' . $Icones['moins'] . '" alt="' . LG_PERS_REQ_OFF_DOWN . '" title="' . LG_PERS_REQ_OFF_DOWN . '" ';
    echo 'onclick="if (document.forms.saisie.Tolerance.value>0) {document.forms.saisie.Tolerance.value--;}"/>' . "\n";
    echo '<input type="text" size="2" name="Tolerance" value="';
    if ($reprise) echo $Tolerance;
    else echo '0';
    echo '" onchange="verification_num(this);"/>' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['plus'] . '" alt="' . LG_PERS_REQ_OFF_UP . '" title="' . LG_PERS_REQ_OFF_UP . '" ';
    echo 'onclick="document.forms.saisie.Tolerance.value++;"/> ' . LG_PERS_REQ_OFF_YEARS . ')  ' . "\n";
    echo '<input type="radio" id="TypeAnnee" name="TypeAnnee" value="v"';
    if ($reprise) {
        if ($TypeAnnee == 'v') echo ' checked';
    }
    echo '/><label for="TypeAnnee">' . LG_PERS_REQ_ALIVE . "</label>\n";
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_BORN_AT) . '</td><td class="value">';
    echo '<select name="Ville_Naissance">' . "\n";
    echo '<option value="-1"/>';
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($reprise) {
            if ($Ville_Naissance == $row[0]) echo 'selected="selected"';
        }
        echo '>';
        if ($row[0] == 0) echo LG_PERS_REQ_NOT_FILLED;
        else              echo $row[1];
        echo '</option>' . "\n";
    }
    echo '</select>' . "\n";
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_DEAD_AT) . '</td><td class="value">';
    echo '<select name="Ville_Deces">' . "\n";
    $res->closeCursor();
    $res = lect_sql($sql);
    echo '<option value="-1"/>';
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($reprise) {
            if ($Ville_Deces == $row[0]) echo 'selected="selected"';
        }
        echo '>';
        if ($row[0] == 0) echo LG_PERS_REQ_NOT_FILLED;
        else              echo $row[1] . "\n";
        echo '</option>';
    }
    echo '</select>' . "\n";
    echo '</td></tr>' . "\n";
    $res->closeCursor();

    if ($est_gestionnaire) {
        echo '<tr><td class="label" width="20%">' . ucfirst($LG_show_on_internet) . '</td><td class="value">';
        echo '<input type="radio" id="Diffu_o" name="Diffu" value="O"';
        if ($reprise) {
            if ($Diffu == 'O') echo ' checked';
        }
        echo '/><label for="Diffu_o">' . $LG_Yes . '</label> ';
        echo '<input type="radio" id="Diffu_n" name="Diffu" value="N"';
        if ($reprise) {
            if ($Diffu == 'N') echo ' checked';
        }
        echo '/><label for="Diffu_n">' . $LG_No . '</label>';
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_REQ_FILE_STATUS) . '</td><td class="value">';
        echo '<input type="radio" id="Statut_Fiche_o" name="Statut_Fiche" value="O"';
        if ($reprise) {
            if ($Statut_Fiche == 'O') echo ' checked';
        }
        echo '/><label for="Statut_Fiche_o">' . LG_CHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="Statut_Fiche_n" name="Statut_Fiche" value="N"';
        if ($reprise) {
            if ($Statut_Fiche == 'N') echo ' checked';
        }
        echo '/><label for="Statut_Fiche_n">' . LG_NOCHECKED_RECORD_SHORT . '</label> ';
        echo '<input type="radio" id="Statut_Fiche_i" name="Statut_Fiche" value="I"';
        if ($reprise) {
            if ($Statut_Fiche == 'I') echo ' checked';
        }
        echo '/><label for="Statut_Fiche_i">' . LG_FROM_INTERNET . '</label>';
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_CATEGORY) . '</td><td class="value">';
        $sql_cat = 'select Identifiant, Image, Titre from ' . nom_table('categories') . ' order by Ordre_Tri';
        $res_cat = lect_sql($sql_cat);
        while ($enr_cat = $res_cat->fetch(PDO::FETCH_NUM)) {
            $num_cat = $enr_cat[0];
            $nom_cat = 'tag_' . $enr_cat[1];
            $titre_cat = $enr_cat[2];
            echo '<input type="radio" name="Categorie" value="' . $num_cat . '"';
            if ($reprise) {
                if ($Categorie == $num_cat) echo ' checked';
            }
            echo '/><img src="' . $root . '/assets/img/' . $Icones[$nom_cat] . '" alt="' . $titre_cat . '" title="' . $titre_cat . '"/>' . '  ' . "\n";
        }
        $nb_tag = 0;
        echo '<input type="radio" id ="categ_0" name="Categorie" value="0"/><label for="categ_0">' . LG_PERS_NO_CATEGORY . '</label>';
        echo '</td></tr>' . "\n";
    }

    echo '<tr><td colspan="2"> </td></tr>';
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_REQ_SORT) . '</td><td class="value">';
    echo '<input type="radio" id="Tri_np" name="Tri" value="np"';
    if ($reprise) {
        if ($Tri == 'np') echo ' checked';
    }
    echo '/><label for="Tri_np">' . LG_PERS_REQ_SORT_NS . '</label> ';
    echo '<input type="radio" id="Tri_dn" name="Tri" value="dn"';
    if ($reprise) {
        if ($Tri == 'dn') echo ' checked';
    }
    echo '/><label for="Tri_dn">' . LG_PERS_REQ_SORT_BORN . '</label> ';
    echo '<input type="radio" id="Tri_dd" name="Tri" value="dd"';
    if ($reprise) {
        if ($Tri == 'dd') echo ' checked';
    }
    echo '/><label for="Tri_dd">' . LG_PERS_REQ_SORT_DEATH . '</label>' . "\n";
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst($LG_Ch_Output_Format) . '</td><td class="value">';
    echo '<input type="radio" id="Sortie_e" name="Sortie" value="e" checked="checked"/><label for="Sortie_e">' . $LG_Ch_Output_Screen . '</label> ';
    echo '<input type="radio" id="Sortie_t" name="Sortie" value="t"/><label for="Sortie_t">' . $LG_Ch_Output_Text . '</label> ';
    // L'export CSV n'est disponible qu'à partir du profil privilégié
    if ($est_privilegie) echo '<input id="Sortie_c" type="radio" name="Sortie" value="c"/><label for="Sortie_c">' . $LG_Ch_Output_CSV . '</label>';
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="20%">' . ucfirst(LG_PERS_REQ_NEW_TAB) . '</td><td class="value">';
    echo '<input type="checkbox" name="New_Window"';
    if ($reprise) {
        if ($New_Window == 'O') echo ' checked';
    }
    echo ' value="O"/>';
    echo '</td></tr>' . "\n";
    bt_ok_an_sup($lib_Rechercher, $lib_Annuler, '', '');
    echo '</table>' . "\n";
    echo '</form>';
}

if ($Sortie != 't') {
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