<?php
//=====================================================================
// Prise en compte d'une contribution du net
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'Horigine',

    'Ref_Pers',
    'Sexe',

    'referencepere_ini',
    'idNomFampere_ini',
    'nompere_ini',
    'prenomspere_ini',
    'ne_lepere_ini',
    'id_ne_zonepere_ini',
    'ne_zonepere_ini',
    'decede_lepere_ini',
    'id_decede_zonepere_ini',
    'decede_zonepere_ini',

    'choix_pere',  // radio button Ajouter, Remplacer, Ignorer
    'presence_prop_pere',
    'nompere_prop',
    'prenomspere_prop',
    'maj_pere',
    'ne_lepere_prop',
    'id_ne_zonepere_prop',
    'ne_zonepere_prop',
    'decede_lepere_prop',
    'id_decede_zonepere_prop',
    'decede_zonepere_prop',

    'referencemere_ini',
    'idNomFammere_ini',
    'nommere_ini',
    'prenomsmere_ini',
    'ne_lemere_ini',
    'id_ne_zonemere_ini',
    'ne_zonemere_ini',
    'decede_lemere_ini',
    'id_decede_zonemere_ini',
    'decede_zonemere_ini',

    'choix_mere',  // radio button Ajouter, Remplacer, Ignorer
    'presence_prop_mere',
    'nommere_prop',
    'prenomsmere_prop',
    'maj_mere',
    'ne_lemere_prop',
    'id_ne_zonemere_prop',
    'ne_zonemere_prop',
    'decede_lemere_prop',
    'id_decede_zonemere_prop',
    'decede_zonemere_prop',

    'nb_conj',    // Nombre de conjoints en base

    'choix_conj', // radio button Ajouter, Remplacer conjoint 1 à nb_conj, Ignorer
    'presence_prop_conj',
    'nomconj_prop',
    'prenomsconj_prop',
    'maj_conj',
    'ne_leconj_prop',
    'id_ne_zoneconj_prop',
    'ne_zoneconj_prop',
    'decede_leconj_prop',
    'id_decede_zoneconj_prop',
    'decede_zoneconj_prop',

    'nb_enf',

    'choix_enfant1', // radio button Ajouter, Remplacer enfant 1, Remplacer enfant 2, Ignorer
    'presence_prop_enfant1',
    'nomenfant1_prop',
    'prenomsenfant1_prop',
    'maj_enfant1',
    'ne_leenfant1_prop',
    'id_ne_zoneenfant1_prop',
    'ne_zoneenfant1_prop',
    'decede_leenfant1_prop',
    'id_decede_zoneenfant1_prop',
    'decede_zoneenfant1_prop',

    'choix_enfant2', // radio button Ajouter, Remplacer enfant 1, Remplacer enfant 2, Ignorer
    'presence_prop_enfant2',
    'nomenfant2_prop',
    'prenomsenfant2_prop',
    'maj_enfant2',
    'ne_leenfant2_prop',
    'id_ne_zoneenfant2_prop',
    'ne_zoneenfant2_prop',
    'decede_leenfant2_prop',
    'id_decede_zoneenfant2_prop',
    'decede_zoneenfant2_prop',

);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) {
        $$nom_variables = $_POST[$nom_variables];
        // Sécurisation des variables réceptionnées
        if (strpos($nom_variables, 'nom')         === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
        if (strpos($nom_variables, 'prenoms')     === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
        if (strpos($nom_variables, 'ne_le')       === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
        if (strpos($nom_variables, 'decede_le')   === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
        if (strpos($nom_variables, 'id_')         === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 80, 'S');
        if (strpos($nom_variables, 'ne_zone')     === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
        if (strpos($nom_variables, 'decede_zone') === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
        if (strpos($nom_variables, 'choix_')      === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
        if (strpos($nom_variables, 'presence_')   === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 1, 'S');
        if (strpos($nom_variables, 'reference')   === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 1, 'N');
        if (strpos($nom_variables, 'nb_')         === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 1, 'N');
        if (strpos($nom_variables, 'maj_')        === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 1, 'S');
        if (strpos($nom_variables, 'idNomFam')    === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 1, 'N');
    } else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour
$titre = LG_CONTRIB_EDIT_TITLE;

$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
//	Gestion des droits
require(__DIR__ . '/app/ressources/gestion_pages.php');          // Appel de la gestion standard des pages

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Largeur de la colonne de titre exprimée en pourcentage
$largP = 25;

$Ref_Pers = Secur_Variable_Post($Ref_Pers, 1, 'N');
$Sexe     = Secur_Variable_Post($Sexe, 1, 'S');

// 2ème passe de récupération : on va récupérer les blocs variables
$tab_variables2 = [];
// bloc des conjoints
if ($nb_conj > 0) {
    for ($nb = 1; $nb <= $nb_conj; $nb++) {
        $tab_variables2[] = 'referenceconj_ini' . $nb;
        $tab_variables2[] = 'nomconj_ini' . $nb;
        $tab_variables2[] = 'prenomsconj_ini' . $nb;
        $tab_variables2[] = 'ne_leconj_ini' . $nb;
        $tab_variables2[] = 'id_ne_zoneconj_ini' . $nb;
        $tab_variables2[] = 'ne_zoneconj_ini' . $nb;
        $tab_variables2[] = 'decede_leconj_ini' . $nb;
        $tab_variables2[] = 'id_decede_zoneconj_ini' . $nb;
        $tab_variables2[] = 'decede_zoneconj_ini' . $nb;
    }
}
// bloc des enfants
if ($nb_enf > 0) {
    for ($nb = 1; $nb <= $nb_enf; $nb++) {
        $tab_variables2[] = 'referenceenfant_ini' . $nb;
        $tab_variables2[] = 'nomenfant_ini' . $nb;
        $tab_variables2[] = 'prenomsenfant_ini' . $nb;
        $tab_variables2[] = 'ne_leenfant_ini' . $nb;
        $tab_variables2[] = 'id_ne_zoneenfant_ini' . $nb;
        $tab_variables2[] = 'ne_zoneenfant_ini' . $nb;
        $tab_variables2[] = 'decede_leenfant_ini' . $nb;
        $tab_variables2[] = 'id_decede_zoneenfant_ini' . $nb;
        $tab_variables2[] = 'decede_zoneenfant_ini' . $nb;
    }
}
// Récupération des zones initiales pour les conjoints et les enfants
if (($nb_conj > 0) or ($nb_enf > 0)) {
    foreach ($tab_variables2 as $nom_variables) {
        if (isset($_POST[$nom_variables])) {
            $$nom_variables = $_POST[$nom_variables];
            // Sécurisation des variables réceptionnées
            if (strpos($nom_variables, 'reference')   === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 1, 'N');
            if (strpos($nom_variables, 'nom')         === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
            if (strpos($nom_variables, 'prenoms')     === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
            if (strpos($nom_variables, 'ne_le')       === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
            if (strpos($nom_variables, 'decede_le')   === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
            if (strpos($nom_variables, 'id_')         === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 1, 'N');
            if (strpos($nom_variables, 'ne_zone')     === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
            if (strpos($nom_variables, 'decede_zone') === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
            // NB : les 2 dernières zones sont plus grandes que le champ car en htmlentities
        } else $$nom_variables = '';
    }
}

// Recup de la variable passée dans l'URL : référence de la contribution ; si se termine par T ==> traitée
$Contribution = Recup_Variable('Contribution', 'A');

// Traitement d'un nom de famille
function Traite_nom_Ins(&$nom_traite, $indic_maj, $ANom = '')
{
    global $idNom, $ident_nom, $premier_nom, $deb_ins_noms;
    if ($nom_traite == '') $nom_traite = '?';
    if ($nom_traite != '?') {
        if ((isset($indic_maj)) and ($indic_maj == 'O')) $nom_traite = strtoupper($nom_traite);
        // Traitements uniquement sur changement de nom (en création, il y a changement
        if ($nom_traite != $ANom) {
            $existe_nom = false;
            $ident_nom = recherche_nom($nom_traite);
            if ($ident_nom) $existe_nom = true;
            // Création du nom dans la table
            if (! $existe_nom) {
                // Initialisation technique des noms
                if ($premier_nom) {
                    // Appel du fichier contenant la classe
                    require(__DIR__ . '/app/Phonetique.php');
                    // Initialisation d'un objet de la classe
                    $codePho = new Phonetique();
                    $idNom = Nouvel_Identifiant('idNomFam', 'noms_famille') - 1;
                    $premier_nom = false;
                }
                //    Calcul d'un code phonétique
                $code = $codePho->calculer($nom_traite);
                $idNom++;
                $ident_nom = $idNom;
                // Création de l'enregistrement dans la table des noms de famille
                $req_N = $deb_ins_noms . $ident_nom . ',\'' . addslashes($nom_traite) . '\',\'' . $code . '\')';
                $res_N = maj_sql($req_N);
            }
        }
    }
}

function Recup_Nom_Fic($Contribution)
{
    global $chemin_contributions;
    $dercar = substr($Contribution, strlen($Contribution) - 1, 1);
    if ($dercar == 'T') {
        $suf_traitee = '_traitee';
        $Contribution = substr($Contribution, 0, strlen($Contribution) - 1);
    } else                $suf_traitee = '';
    $nom_fic_base = 'contrib_' . $Contribution . $suf_traitee . '.txt';
    $nom_fic = $chemin_contributions . $nom_fic_base;
    return $nom_fic;
}

function Recup_Don_Pers($refer)
{
    global
        $db, $enreg,
        $donnees_ini;
    init_ini();
    $sql = 'select * from ' . nom_table('personnes') . ' where Reference = ' . $refer . ' limit 1';
    if ($res = lect_sql($sql)) {
        $enreg = $res->fetch(PDO::FETCH_ASSOC);
        $res->closeCursor();
        $donnees_ini = array(
            'reference'         =>    $refer,
            'nom'            => $enreg['Nom'],
            'prenoms'        => $enreg['Prenoms'],
            'ne_le'          => $enreg['Ne_le'],
            'id_ne_zone'     => $enreg['Ville_Naissance'],
            'ne_zone'          => '',
            'decede_le'      => $enreg['Decede_Le'],
            'id_decede_zone' => $enreg['Ville_Deces'],
            'decede_zone'    => '',
            'idNomFam'       => $enreg['idNomFam']
        );
    }
}

function zone_hidden_pers($suffixe, $num)
{
    global $donnees_ini, $def_enc;
    // Zones du formulaire pour les données initiales
    echo '<input type="hidden" name="reference' . $suffixe . '_ini' . $num . '" value="' . $donnees_ini['reference'] . '"/>' . "\n";
    echo '<input type="hidden" name="nom' . $suffixe . '_ini' . $num . '" value="' . html_entity_decode($donnees_ini['nom'], ENT_QUOTES, $def_enc) . '"/>' . "\n";
    echo '<input type="hidden" name="prenoms' . $suffixe . '_ini' . $num . '" value="' . html_entity_decode($donnees_ini['prenoms'], ENT_QUOTES, $def_enc) . '"/>' . "\n";
    echo '<input type="hidden" name="ne_le' . $suffixe . '_ini' . $num . '" value="' . $donnees_ini['ne_le'] . '"/>' . "\n";
    echo '<input type="hidden" name="id_ne_zone' . $suffixe . '_ini' . $num . '" value="' . $donnees_ini['id_ne_zone'] . '"/>' . "\n";
    echo '<input type="hidden" name="ne_zone' . $suffixe . '_ini' . $num . '" value="' . html_entity_decode($donnees_ini['ne_zone'], ENT_QUOTES, $def_enc) . '"/>' . "\n";
    echo '<input type="hidden" name="decede_le' . $suffixe . '_ini' . $num . '" value="' . $donnees_ini['decede_le'] . '"/>' . "\n";
    echo '<input type="hidden" name="id_decede_zone' . $suffixe . '_ini' . $num . '" value="' . $donnees_ini['id_decede_zone'] . '"/>' . "\n";
    echo '<input type="hidden" name="decede_zone' . $suffixe . '_ini' . $num . '" value="' . html_entity_decode($donnees_ini['decede_zone'], ENT_QUOTES, $def_enc) . '"/>' . "\n";
    echo '<input type="hidden" name="idNomFam' . $suffixe . '_ini' . $num . '" value="' . $donnees_ini['idNomFam'] . '"/>' . "\n";
}

function init_ini()
{
    global $donnees_ini;
    $donnees_ini = '';
    $donnees_ini =
        array(
            'reference'      => 0,
            'nom'            => '',
            'prenoms'        => '',
            'ne_le'          => '',
            'id_ne_zone'     => '',
            'ne_zone'          => '',
            'decede_le'      => '',
            'id_decede_zone' => '',
            'decede_zone'    => '',
            'idNomFam'       => ''
        );
}

// Affiche les données d'une personne dans un cadre
function Aff_Pers_Cadre($ch_entete, $chaine, $couleur)
{
    global $def_enc;
    echo htmlentities($ch_entete, ENT_QUOTES, $def_enc) . "\n";
    echo '<table width="100%" bgcolor="' . $couleur . '">' . "\n";
    echo '<tr>' . "\n";
    echo '<td>' . $chaine . '</td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
}

function traite_ville($zone)
{
    global $villes, $id_villes, $db;
    $num_ville = 0;
    if ($zone != '') {
        // Si la ville est non numérique, elle a été ajoutée
        if (!is_numeric($zone)) {
            $x = array_search($zone, $villes);
            // Si la ville n'est pas dans le tableau, on va la chercher en base
            if ($x === false) {
                $sql = 'select Identifiant_zone from ' . nom_table('villes') . ' where Nom_Ville =\'' . $zone . '\' limit 1';
                if ($res = lect_sql($sql)) {
                    if ($enr = $res->fetch(PDO::FETCH_NUM)) {
                        $x = $enr[0];
                        $num_ville = $x;
                    }
                }
                $res->closeCursor();
            }
            // Si la ville n'est pas dans le tableau, on va la créer si on la trouve pas en base
            if ($x === false) {
                $num_ville = Ajoute_Ville($zone);
                $villes[] = $zone;
                $id_villes[] = $num_ville;
            } else {
                if (!$num_ville) $num_ville = $id_villes[$x];
            }
        } else $num_ville = $zone;
    }
    return $num_ville;
}

function Traite_Ligne($ligne, $rub)
{
    global $$rub;
    $retour = 0;
    $l_nom = strlen($rub);
    if (substr($ligne, 0, 1) != '#') {
        // Exemple de ligne
        // Reference_Personne : 46
        if (strlen($ligne) > $l_nom) {
            if (substr($ligne, 0, $l_nom) == $rub) {
                $$rub = substr($ligne, $l_nom + 2);
                $$rub = trim($$rub);
                $retour = 1;
            }
        }
    }
    return $retour;
}

function Traite_Ligne2($ligne, $rub)
{
    $retour = '';
    $comment = '# ';
    //$l_nom = strlen($rub);
    if (substr($ligne, 0, 2) == '# ') {
        // Exemple de ligne
        //# IP serveur : 91.121.53.53
        $rub = $comment . $rub;
        // La rubrique est trouvée
        if (strpos($ligne, $rub) !== false) {
            $pp = strpos($ligne, ':');
            if ($pp !== false) {
                $retour = trim(substr($ligne, $pp + 1));
            }
        }
    }
    return $retour;
}

// Détermine l'enrichissement en fonction des zones
// Gras : zone modifiée
// Italique : pas de proposition ==> reprise de l'initiale en sortie
function enrichit($rub_ini, $rub_prop)
{
    global     $deb, $fin,                         // Balises d'enrichissement début et fin
        $donnees_ini, $donnees_prop;     // Tableaux des données
    $donnee_ini  = $donnees_ini[$rub_ini];
    $donnee_prop = $donnees_prop[$rub_prop];
    //echo 'rubrique : '.$rub_ini.' ini : '.$donnee_ini.', prop :'.$donnee_prop.'<br />';
    $deb = '';
    $fin = '';
    if ($donnee_prop != '') {
        if (($donnee_ini != '') and ($donnee_ini != $donnee_prop)) {
            $deb = '<b>';
            $fin = '</b>';
        }
    } else {
        if ($donnee_ini != '') {
            $deb = '<i>';
            $fin = '</i>';
            $donnees_prop[$rub_prop] = $donnees_ini[$rub_ini];
            if (strpos($rub_prop, '_zone') != 0) $donnees_prop['id_' . $rub_prop] = $donnees_ini['id_' . $rub_ini];
        }
    }
}

function affiche_ini($couleur)
{
    global $donnees_ini, $def_enc, $h_at;
    if ($donnees_ini['reference'] != 0) {
        $lib_ville_ne = '';
        $lib_ville_deces = '';
        $ch_ini = htmlentities($donnees_ini['prenoms'] . ' ' . $donnees_ini['nom'], ENT_QUOTES, $def_enc);
        $ch_ini .= '<br />&deg; ' . Etend_date($donnees_ini['ne_le']);
        $ville_ne = $donnees_ini['id_ne_zone'];
        if ($ville_ne != 0) {
            $lib_ville_ne = lib_ville($ville_ne);
            $ch_ini .= $h_at . $lib_ville_ne;
        }
        $donnees_ini['ne_zone'] = $lib_ville_ne;
        $ch_ini .= '<br />+ ' . Etend_date($donnees_ini['decede_le']);
        $ville_deces = $donnees_ini['id_decede_zone'];
        if ($ville_deces != 0) {
            if ($ville_deces == $ville_ne) $lib_ville_deces = $lib_ville_ne;
            else                           $lib_ville_deces = lib_ville($ville_deces);
            $ch_ini .= $h_at . $lib_ville_deces;
        }
        $donnees_ini['decede_zone'] = $lib_ville_deces;
    } else {
        $ch_ini = '-';
    }

    echo '<table width="100%" bgcolor="' . $couleur . '">';
    echo '<tr>';
    echo '<td>' . $ch_ini . '</td>';
    echo '</tr>';
    echo '</table>' . "\n";

    return $ch_ini;
}

function Aff_Pers2($suffixe, $donnees_ini, $donnees_prop)
{
    global
        $Icones, $chemin_images, $db, $Sexe, $nb_pers, $Reference_Personne,
        $deb, $fin,                         // Balises d'enrichissement début et fin
        $donnees_ini, $donnees_prop,     // Tableaux des données
        $def_enc, $h_at;

    $coul_anc  = '#99CC99';
    $coul_nouv = '#FFCC99';
    $lib = '';

    // Détection de la présence d'une proposition
    if (($donnees_prop['prenoms'] != '') or
        ($donnees_prop['nom'] != '') or
        ($donnees_prop['ne_le'] != '') or
        ($donnees_prop['ne_zone'] != '') or
        ($donnees_prop['decede_le'] != '') or
        ($donnees_prop['decede_zone'] != '')
    ) $presence_prop = 'O';
    else $presence_prop = 'N';

    echo '<table width="100%" border="0">';
    echo '<tr><td width="75%">';

    if (($suffixe == 'pere') or ($suffixe == 'mere')) {
        // Affichage des données initiales
        $ch_ini = affiche_ini($coul_anc);
        zone_hidden_pers($suffixe, '');
        $ch_prop = '';
        if ($presence_prop == 'O') {
            $x = enrichit('prenoms', 'prenoms');
            $ch_prop = $deb . my_html($donnees_prop['prenoms']) . $fin;
            $x = enrichit('nom', 'nom');
            $ch_prop .= ' ' . $deb . my_html($donnees_prop['nom']) . $fin;
            $x = enrichit('ne_le', 'ne_le');
            $ch_prop .= '<br />&deg; ' . $deb . Etend_date($donnees_prop['ne_le']) . $fin;
            $x = enrichit('ne_zone', 'ne_zone');
            if ($donnees_prop['ne_zone'] != '') {
                $ch_prop .= $h_at . $deb . $donnees_prop['ne_zone'] . $fin;
            }
            $x = enrichit('decede_le', 'decede_le');
            $ch_prop .= '<br />+ ' . $deb . Etend_date($donnees_prop['decede_le']) . $fin;
            $x = enrichit('decede_zone', 'decede_zone');
            if ($donnees_prop['decede_zone'] != '') {
                $ch_prop .= $h_at . $deb . $donnees_prop['decede_zone'] . $fin;
            }
        } else {
            $ch_prop .= 'Pas de proposition';
        }
    }

    if ($suffixe == 'conj') {
        $nb_pers = 0;
        // Affichage de la liste des conjoints actuels
        $sql = 'select Conjoint_1, Conjoint_2 from ' . nom_table('unions') . ' where ';
        switch ($Sexe) {
            case 'm':
                $sql = $sql . 'Conjoint_1 = ' . $Reference_Personne;
                break;
            case 'f':
                $sql = $sql . 'Conjoint_2 = ' . $Reference_Personne;
                break;
            default:
                $sql = $sql . 'Conjoint_1 = ' . $Reference_Personne . ' or Conjoint_2 =' . $Reference_Personne;
                break;
        }
        //    tri des unions par date
        $sql .= ' order by Maries_Le';
        if ($resUn = lect_sql($sql)) {
            echo '  <fieldset>' . "\n";
            echo '    <legend>Conjoint(s) actuel(s)</legend>' . "\n";
            while ($enreg = $resUn->fetch(PDO::FETCH_NUM)) {
                $mari  = $enreg[0];
                $femme = $enreg[1];
                if ($Reference_Personne != 0) {
                    if ($Reference_Personne == $mari) $personne = $femme;
                    if ($Reference_Personne == $femme) $personne = $mari;
                    $nb_pers++;
                    Recup_Don_Pers($personne);
                    echo 'Conjoint ' . $nb_pers . '<br />';
                    $ch_ini = affiche_ini($coul_anc);
                    zone_hidden_pers($suffixe, $nb_pers);
                }
            }
            echo '  </fieldset>' . "\n";
            $resUn->closeCursor();
            echo '<input type="hidden" name="nb_conj" value="' . $nb_pers . '"/>' . "\n";
        }
    }

    // Dans le cas de l'enfant1, on va récupérer la liste des enfants existants
    if ($suffixe == 'enfant1') {
        $nb_pers = 0;
        $sql = 'select Enfant from ' . nom_table('filiations') . ' where ';
        switch ($Sexe) {
            case 'm':
                $sql = $sql . 'Pere = ' . $Reference_Personne;
                break;
            case 'f':
                $sql = $sql . 'Mere = ' . $Reference_Personne;
                break;
            default:
                $sql = $sql . 'Pere = ' . $Reference_Personne . ' or Mere =' . $Reference_Personne;
                break;
        }
        //    tri des enfants par rang
        $sql .= ' order by Rang';

        if ($resUn = lect_sql($sql)) {
            echo '  <fieldset>' . "\n";
            echo '    <legend>Enfant(s) actuel(s)</legend>' . "\n";
            while ($enreg = $resUn->fetch(PDO::FETCH_NUM)) {
                if ($Reference_Personne != 0) {
                    $nb_pers++;
                    Recup_Don_Pers($enreg[0]);
                    echo 'Enfant ' . $nb_pers . '<br />';
                    $ch_ini = affiche_ini($coul_anc);
                    zone_hidden_pers('enfant', $nb_pers);
                }
            }
            echo '  </fieldset>' . "\n";
            $resUn->closeCursor();
            echo '<input type="hidden" name="nb_enf" value="' . $nb_pers . '"/>' . "\n";
        }
    }

    echo '</td>' . "\n";

    switch ($suffixe) {
        case 'pere':
        case 'mere':
            $alignement = 'middle';
            break;
        case 'conj':
        case 'enfant1':
            $alignement = 'bottom';
            break;
        case 'enfant2':
            $alignement = 'middle';
            break;
    }
    echo '<td valign="' . $alignement . '" rowspan="2">';
    $nom_rb = 'choix_' . $suffixe;

    if ($presence_prop == 'O') {
        echo '<input type="checkbox" id="maj_' . $suffixe . '" name="maj_' . $suffixe . '" value="O"/>'
            . '<label for="maj_' . $suffixe . '" >' . LG_CONTRIB_EDIT_UPCASE . '</label><br /><br />';
        if (($suffixe == 'pere') or ($suffixe == 'mere')) {
            if ($donnees_ini['reference'] != 0) $action = LG_CONTRIB_EDIT_REPLACE;
            else                                $action = LG_CONTRIB_EDIT_ADD;
            echo '<input type="radio" name="' . $nom_rb . '" value="' . $action . '"/>' . $action . '<br />' . "\n";
        }

        if (($suffixe == 'conj') or ($suffixe == 'enfant1') or ($suffixe == 'enfant2')) {
            if ($suffixe == 'conj') $action = LG_CONTRIB_EDIT_REPLACE_HUSB_WIFE;
            else                    $action = LG_CONTRIB_EDIT_REPLACE_CHILDREN;
            $action = $action . ' ';
            // 1er choix : ajouter
            echo '<input type="radio" name="' . $nom_rb . '" value="Ajouter"/>Ajouter<br />' . "\n";
            // Si 1 ou des conjoints/enfants existent, on propose de les remplacer
            if ($nb_pers > 0) {
                for ($nb = 1; $nb <= $nb_pers; $nb++) {
                    echo '<input type="radio" name="' . $nom_rb . '" value="' . $action . $nb . '"/>' . $action . $nb . '<br />' . "\n";
                }
            }
        }
        echo '<input type="radio" name="' . $nom_rb . '" value="Ignorer" checked="checked"/>Ignorer<br />' . "\n";
    }

    // S'il n'y a pas de proposition, cela revient à prendre le choix ignorer
    if ($presence_prop == 'N') {
        echo '<input type="hidden" name="' . $nom_rb . '" value="Ignorer"/>' . "\n";
    }

    echo '</td>' . "\n";
    echo '</tr>';
    echo '<tr><td>';

    switch ($suffixe) {
        case 'conj':
            $fin_lib_field = '';
            break;
        case 'enfant1':
            $fin_lib_field = ' enfant 1';
            break;
        case 'enfant2':
            $fin_lib_field = ' enfant 2';
            break;
    }


    switch ($suffixe) {
        case 'pere':
            $lib = 'Proposition du net';
            break;
        case 'mere':
            $lib = 'Proposition du net';
            break;
        case 'conj':
        case 'enfant1':
        case 'enfant2':
            // Affichage de la proposition ==> pas d'enrichissement possible
            echo '<fieldset>' . "\n";
            echo '<legend>Proposition' . $fin_lib_field . '</legend>' . "\n";
            $ch_prop = my_html($donnees_prop['prenoms'] . ' ' . $donnees_prop['nom']);
            $ch_prop .= '<br />&deg; ' . Etend_date($donnees_prop['ne_le']);
            if ($donnees_prop['ne_zone'] != '') {
                $ch_prop .= $h_at . $donnees_prop['ne_zone'];
            }
            $ch_prop .= '<br />+ ' . Etend_date($donnees_prop['decede_le']);
            if ($donnees_prop['decede_zone'] != '') {
                $ch_prop .= $h_at . $donnees_prop['decede_zone'];
            }
            //echo '</fieldset>'."\n";
            break;
    }
    echo my_html($lib) . "\n";
    echo '<table width="100%" bgcolor="' . $coul_nouv . '">';
    echo '<tr>';
    echo '<td>' . $ch_prop . '</td>';
    //echo '<td>'.my_html($ch_prop).'</td>';
    echo '</tr>';
    echo '</table>';
    "\n";

    echo '</td></tr>';
    echo '</table>' . "\n";

    // Zones du formulaire pour les données proposées
    echo '<input type="hidden" name="presence_prop_' . $suffixe . '" value="' . $presence_prop . '"/>' . "\n";
    echo '<input type="hidden" name="nom' . $suffixe . '_prop" value="' . my_html($donnees_prop['nom']) . '"/>' . "\n";
    echo '<input type="hidden" name="prenoms' . $suffixe . '_prop" value="' . my_html($donnees_prop['prenoms']) . '"/>' . "\n";
    echo '<input type="hidden" name="ne_le' . $suffixe . '_prop" value="' . $donnees_prop['ne_le'] . '"/>' . "\n";
    echo '<input type="hidden" name="id_ne_zone' . $suffixe . '_prop" value="' . $donnees_prop['id_ne_zone'] . '"/>' . "\n";
    echo '<input type="hidden" name="ne_zone' . $suffixe . '_prop" value="' . my_html($donnees_prop['ne_zone']) . '"/>' . "\n";
    echo '<input type="hidden" name="decede_le' . $suffixe . '_prop" value="' . $donnees_prop['decede_le'] . '"/>' . "\n";
    echo '<input type="hidden" name="id_decede_zone' . $suffixe . '_prop" value="' . $donnees_prop['id_decede_zone'] . '"/>' . "\n";
    echo '<input type="hidden" name="decede_zone' . $suffixe . '_prop" value="' . my_html($donnees_prop['decede_zone']) . '"/>' . "\n";
}

//Demande de mise à jour
if ($bt_OK) {

    // Récupération de l'identifiant à attribuer
    $nouv_ident = Nouvel_Identifiant('Reference', 'personnes');

    // Début de requêtes d'insert pour les noms
    $deb_ins_noms = 'insert into ' . nom_table('noms_famille') . ' values(';
    $deb_ins_lien = 'insert into ' . nom_table('noms_personnes') . ' values(';

    // Détection des ajouts de ville
    $villes = array();
    $id_villes = '';
    if ($choix_pere != 'Ignorer') {
        $id_ne_zonepere_prop        = traite_ville($id_ne_zonepere_prop);
        $id_decede_zonepere_prop    = traite_ville($id_decede_zonepere_prop);
    }
    if ($choix_mere != 'Ignorer') {
        $id_ne_zonemere_prop        = traite_ville($id_ne_zonemere_prop);
        $id_decede_zonemere_prop    = traite_ville($id_decede_zonemere_prop);
    }
    if ($choix_conj != 'Ignorer') {
        $id_ne_zoneconj_prop        = traite_ville($id_ne_zoneconj_prop);
        $id_decede_zoneconj_prop    = traite_ville($id_decede_zoneconj_prop);
    }
    if ($choix_enfant1 != 'Ignorer') {
        $id_ne_zoneenfant1_prop     = traite_ville($id_ne_zoneenfant1_prop);
        $id_decede_zoneenfant1_prop = traite_ville($id_decede_zoneenfant1_prop);
    }
    if ($choix_enfant2 != 'Ignorer') {
        $id_ne_zoneenfant2_prop     = traite_ville($id_ne_zoneenfant2_prop);
        $id_decede_zoneenfant2_prop = traite_ville($id_decede_zoneenfant2_prop);
    }

    $creation_pere = 0;
    $creation_mere = 0;
    $creation_conj = 0;
    $creation_enfant1 = 0;
    $creation_enfant2 = 0;

    $premier_nom = true;
    $nom_prec = '';

    // Traitement du père
    // Présence d'une proposition pour le père et demande de traitement
    if (($presence_prop_pere == 'O') and ($choix_pere != 'Ignorer')) {
        $req = '';
        $req_N = '';
        // Création du père
        if ($choix_pere == 'Ajouter') {
            //echo '>>> Ajouter père<br />';
            $rubs = '';
            $cont = '';                                        // Initialisation des rubriques et du contenu
            // Traitement du nom du père
            Traite_nom_Ins($nompere_prop, $maj_pere);
            if ($prenomspere_prop == '') $prenomspere_prop = '?';
            Ins_Zone_Req_Rub($nouv_ident, 'N', 'Reference');
            Ins_Zone_Req_Rub($nompere_prop, 'A', 'Nom');
            Ins_Zone_Req_Rub($prenomspere_prop, 'A', 'Prenoms');
            Ins_Zone_Req_Rub($ne_lepere_prop, 'A', 'Ne_le');
            Ins_Zone_Req_Rub($decede_lepere_prop, 'A', 'Decede_Le');
            Ins_Zone_Req_Rub($id_ne_zonepere_prop, 'N', 'Ville_Naissance');
            Ins_Zone_Req_Rub($id_decede_zonepere_prop, 'N', 'Ville_Deces');
            Ins_Zone_Req_Rub('N', 'A', 'Diff_Internet');
            if ($rubs != '') {
                $req = 'insert into ' . nom_table('personnes') .
                    ' (' . $rubs . ',Sexe,Date_Creation,Date_Modification,Statut_Fiche,idNomFam) values' .
                    ' (' . $cont . ',\'m\',current_timestamp,current_timestamp,\'I\',' . $ident_nom . ')';
                $creation_pere = $nouv_ident++;
                // Création de l'enregistrement dans la table des liens personnes / noms
                $req_N = $deb_ins_lien . $creation_pere . ',' . $ident_nom . ',\'O\',null)';
            }
        }
        // Modification du père
        if ($choix_pere == 'Remplacer') {
            //echo '>>> Modifier père<br />';
            Traite_nom_Ins($nompere_prop, $maj_pere, $nompere_ini);
            if ($nompere_prop == $nompere_ini) $ident_nom = $idNomFampere_ini;
            Aj_Zone_Req('Nom', $nompere_prop, $nompere_ini, 'A', $req);
            Aj_Zone_Req('Prenoms', $prenomspere_prop, $prenomspere_ini, 'A', $req);
            Aj_Zone_Req('Ne_le', $ne_lepere_prop, $ne_lepere_ini, 'A', $req);
            Aj_Zone_Req('Decede_Le', $decede_lepere_prop, $decede_lepere_ini, 'A', $req);
            Aj_Zone_Req('Ville_Naissance', $id_ne_zonepere_prop, $id_ne_zonepere_ini, 'N', $req);
            Aj_Zone_Req('Ville_Deces', $id_decede_zonepere_prop, $id_decede_zonepere_ini, 'N', $req);
            Aj_Zone_Req('Statut_Fiche', 'I', '', 'A', $req);
            Aj_Zone_Req('idNomFam', $ident_nom, $idNomFampere_ini, 'N', $req);
            if ($req != '') {
                $req = 'update ' . nom_table('personnes') . ' set ' . $req .
                    ', Date_Modification = current_timestamp' .
                    ' where Reference  = ' . $referencepere_ini;
                // Modification du nom du père ? Création de l'enregistrement dans la table des liens personnes / noms
                if ($nompere_prop != $nompere_ini) {
                    $req_N = $deb_ins_lien . $referencepere_ini . ',' . $ident_nom . ',\'O\',null)';
                }
            }
        }
        // Exéution de la requête
        if ($req != '') {
            $res = maj_sql($req);
            if ($req_N != '') $res_N = maj_sql($req_N);
            $maj_site = true;
        }
    }

    // Traitement de la mère
    // Présence d'une proposition pour le père et demande de traitement
    if (($presence_prop_mere == 'O') and ($choix_mere != 'Ignorer')) {
        /*echo 'Nom : '.$nommere_prop.'<br />';
		echo 'Prénoms : '.$prenomsmere_prop.'<br />';
		echo 'Date de naissance : '.$ne_lemere_prop.'<br />';
		echo 'Identifiant zone naissance : '.$id_ne_zonemere_prop.'<br />';
		echo 'Zone naissance : '.$ne_zonemere_prop.'<br />';
		echo 'Date de décès : '.$decede_lemere_prop.'<br />';
		echo 'Identifiant zone décès : '.$id_decede_zonemere_prop.'<br />';
		echo 'Zone décès  : '.$decede_zonemere_prop.'<br />';*/
        $req = '';
        $req_N = '';
        // Création de la mère
        if ($choix_mere == 'Ajouter') {
            //echo '>>> Ajouter mère<br />';
            $rubs = '';
            $cont = '';                                        // Initialisation des rubriques et du contenu
            // Traitement du nom de la mère
            Traite_nom_Ins($nommere_prop, $maj_mere);
            if ($prenomsmere_prop == '') $prenomsmere_prop = '?';
            Ins_Zone_Req_Rub($nouv_ident, 'N', 'Reference');
            Ins_Zone_Req_Rub($nommere_prop, 'A', 'Nom');
            Ins_Zone_Req_Rub($prenomsmere_prop, 'A', 'Prenoms');
            Ins_Zone_Req_Rub($ne_lemere_prop, 'A', 'Ne_le');
            Ins_Zone_Req_Rub($decede_lemere_prop, 'A', 'Decede_Le');
            Ins_Zone_Req_Rub($id_ne_zonemere_prop, 'N', 'Ville_Naissance');
            Ins_Zone_Req_Rub($id_decede_zonemere_prop, 'N', 'Ville_Deces');
            Ins_Zone_Req_Rub('N', 'A', 'Diff_Internet');
            if ($rubs != '') {
                $req = 'insert into ' . nom_table('personnes') .
                    ' (' . $rubs . ',Sexe,Date_Creation,Date_Modification,Statut_Fiche,idNomFam) values' .
                    ' (' . $cont . ',\'m\',current_timestamp,current_timestamp,\'I\',' . $ident_nom . ')';
                $creation_mere = $nouv_ident++;
                // Création de l'enregistrement dans la table des liens personnes / noms
                $req_N = $deb_ins_lien . $creation_mere . ',' . $ident_nom . ',\'O\',null)';
            }
        }
        // Modification de la mère
        if ($choix_mere == 'Remplacer') {
            //echo '>>> Modifier mère<br />';
            Traite_nom_Ins($nommere_prop, $maj_mere, $nommere_ini);
            if ($nommere_prop == $nommere_ini) $ident_nom = $idNomFammere_ini;
            Aj_Zone_Req('Nom', $nommere_prop, $nommere_ini, 'A', $req);
            Aj_Zone_Req('Prenoms', $prenomsmere_prop, $prenomsmere_ini, 'A', $req);
            Aj_Zone_Req('Ne_le', $ne_lemere_prop, $ne_lemere_ini, 'A', $req);
            Aj_Zone_Req('Decede_Le', $decede_lemere_prop, $decede_lemere_ini, 'A', $req);
            Aj_Zone_Req('Ville_Naissance', $id_ne_zonemere_prop, $id_ne_zonemere_ini, 'N', $req);
            Aj_Zone_Req('Ville_Deces', $id_decede_zonemere_prop, $id_decede_zonemere_ini, 'N', $req);
            Aj_Zone_Req('Statut_Fiche', 'I', '', 'A', $req);
            if ($req != '') {
                $req = 'update ' . nom_table('personnes') . ' set ' . $req .
                    ', Date_Modification = current_timestamp' .
                    ' where Reference  = ' . $referencemere_ini;
                // Modification du nom de la mère ? Création de l'enregistrement dans la table des liens personnes / noms
                if ($nommere_prop != $nommere_ini) {
                    $req_N = $deb_ins_lien . $referencemere_ini . ',' . $ident_nom . ',\'O\',null)';
                }
            }
        }
        // Exéution de la requête
        if ($req != '') {
            $res = maj_sql($req);
            if ($req_N != '') $res_N = maj_sql($req_N);
            $maj_site = true;
        }
    }

    // Calcul des références du père et de la mère si au moins une création
    // Si le père ou la mère n'est pas créé, on reprend la référence initiale
    if ($creation_pere or $creation_mere) {
        $ref_pere = 0;
        // Si on a créé le père, on retient sa référence
        if ($creation_pere) $ref_pere = $creation_pere;
        // Si on ne l'a pas créé, on retient la référence du père connu
        if (!$creation_pere) $ref_pere = $referencepere_ini;
        $ref_mere = 0;
        // Si on a créé la mère, on retient sa référence
        if ($creation_mere) $ref_mere = $creation_mere;
        // Si on ne l'a pas créée, on retient la référence de la mère connue
        if (!$creation_mere) $ref_mere = $referencemere_ini;
    }

    // Traitement de la filiation suite à la création d'un père ou d'une mère
    if ($creation_pere or $creation_mere) {
        // Vérification de l'existence préalable de la filiation
        $trouve = 0;
        $sql = 'select 1 from ' . nom_table('filiations') . ' where enfant = ' . $Ref_Pers . ' limit 1';
        if ($res = lect_sql($sql)) {
            if ($parents = $res->fetch(PDO::FETCH_NUM)) $trouve = 1;
            $res->closeCursor();
        }

        // La filiation n'existe pas il faut le créer
        if (!$trouve) {
            $req = 'insert into ' . nom_table('filiations') .
                ' values(' . $Ref_Pers . ',' .
                $ref_pere . ',' .
                $ref_mere . ',' .
                '0,current_timestamp,current_timestamp,\'I\')';
            $res = maj_sql($req);
        }
        // Sinon, il faut la modifier
        else {
            $req = 'update ' . nom_table('filiations') . ' set ';
            if ($creation_pere)    $req .= 'Pere = ' . $creation_pere . ', ';
            if ($creation_mere)    $req .= 'Mere = ' . $creation_mere . ', ';
            $req .= 'Date_Modification = current_timestamp, ' .
                'Statut_Fiche = \'I\' ' .
                'where Enfant = ' . $Ref_Pers;
            $res = maj_sql($req);
        }
    }

    // Traitement de l'union suite à la création de l'un des parents
    if ($creation_pere or $creation_mere) {
        $ref_pere = 0;
        // Si on a créé le père, on retient sa référence
        if ($creation_pere) $ref_pere = $creation_pere;
        // Si on ne l'a pas créé, on retient la référence du père connu
        if (!$creation_pere) $ref_pere = $referencepere_ini;
        $ref_mere = 0;
        // Si on a créé la mère, on retient sa référence
        if ($creation_mere) $ref_mere = $creation_mere;
        // Si on ne l'a pas créée, on retient la référence de la mère connue
        if (!$creation_mere) $ref_mere = $referencemere_ini;
        // Si au final, les 2 parents sont connus, on crée l'union entre eux
        if ($ref_pere and $ref_mere) {
            $req = 'insert into ' . nom_table('unions') .
                '(Conjoint_1, Conjoint_2, Ville_Mariage, Ville_Notaire, Date_Creation, Date_Modification, Statut_Fiche) ' .
                ' values(' . $ref_pere . ',' . $ref_mere . ',0,0,current_timestamp,current_timestamp,\'I\')';
            $res = maj_sql($req);
        }
    }


    // Traitement du conjoint
    // Présence d'une proposition pour le conjoint et demande de traitement
    if (($presence_prop_conj == 'O') and ($choix_conj != 'Ignorer')) {
        $req = '';
        $req_N = '';
        // Création du conjoint
        if ($choix_conj == 'Ajouter') {
            //echo '>>> Ajouter conjoint<br />';
            $rubs = '';
            $cont = '';                                        // Initialisation des rubriques et du contenu
            // Traitement du nom du conjoint
            Traite_nom_Ins($nomconj_prop, $maj_conj);
            if ($prenomsconj_prop == '') $prenomsconj_prop = '?';
            // Détermination du sexe du conjoint en fonction du sexe de la personne (revoir ultérieurement pour conjoint du même sexe)
            switch ($Sexe) {
                case 'm':
                    $sexe_conj = 'f';
                    break;
                case 'f':
                    $sexe_conj = 'm';
                    break;
                default:
                    $sexe_conj = '';
            }
            Ins_Zone_Req_Rub($nouv_ident, 'N', 'Reference');
            Ins_Zone_Req_Rub($nomconj_prop, 'A', 'Nom');
            Ins_Zone_Req_Rub($prenomsconj_prop, 'A', 'Prenoms');
            Ins_Zone_Req_Rub($ne_leconj_prop, 'A', 'Ne_le');
            Ins_Zone_Req_Rub($decede_leconj_prop, 'A', 'Decede_Le');
            Ins_Zone_Req_Rub($id_ne_zoneconj_prop, 'N', 'Ville_Naissance');
            Ins_Zone_Req_Rub($id_decede_zoneconj_prop, 'N', 'Ville_Deces');
            Ins_Zone_Req_Rub($sexe_conj, 'A', 'Sexe');
            Ins_Zone_Req_Rub('N', 'A', 'Diff_Internet');
            if ($rubs != '') {
                $req = 'insert into ' . nom_table('personnes') .
                    ' (' . $rubs . ',Date_Creation,Date_Modification,Statut_Fiche,idNomFam) values' .
                    ' (' . $cont . ',current_timestamp,current_timestamp,\'I\',' . $ident_nom . ')';
                $creation_conj = $nouv_ident++;
                // Création de l'enregistrement dans la table des liens personnes / noms
                $req_N = $deb_ins_lien . $creation_conj . ',' . $ident_nom . ',\'O\',null)';
            }
        }
        // Modification du conjoint
        if (substr($choix_conj, 0, 9) == 'Remplacer') {
            // Détermination de la référence du conjoint à modifier ; le numéro est récupéré dans le libellé du choix
            $bpos = strpos($choix_conj, ' ', 10);
            $num_conj = substr($choix_conj, $bpos + 1);
            $rub_ini = 'referenceconj_ini' . $num_conj;
            $ref_ini = $$rub_ini;
            //echo '>>> Modifier conjoint'.$num_conj.'<br />';
            Traite_nom_Ins($nomconj_prop, $maj_conj, $nomconj_ini);
            if ($nomconj_prop == $nomconj_ini) $ident_nom = $idNomFamconj_ini;
            Aj_Zone_Req('Nom', $nomconj_prop, $nomconj_ini, 'A', $req);
            Aj_Zone_Req('Prenoms', $prenomsconj_prop, $prenomsconj_ini, 'A', $req);
            Aj_Zone_Req('Ne_le', $ne_leconj_prop, $ne_leconj_ini, 'A', $req);
            Aj_Zone_Req('Decede_Le', $decede_leconj_prop, $decede_leconj_ini, 'A', $req);
            Aj_Zone_Req('Ville_Naissance', $id_ne_zoneconj_prop, $id_ne_zoneconj_ini, 'N', $req);
            Aj_Zone_Req('Ville_Deces', $id_decede_zoneconj_prop, $id_decede_zoneconj_ini, 'N', $req);
            Aj_Zone_Req('Statut_Fiche', 'I', '', 'A', $req);
            if ($req != '') {
                $req = 'update ' . nom_table('personnes') . ' set ' . $req .
                    ', Date_Modification = current_timestamp' .
                    ' where Reference  = ' . $ref_ini;
                // Modification du nom du conjoint ? Création de l'enregistrement dans la table des liens personnes / noms
                if ($nomconj_prop != $nomconj_ini) {
                    $req_N = $deb_ins_lien . $referenceconj_ini . ',' . $ident_nom . ',\'O\',null)';
                }
            }
        }
        // Exéution de la requête
        if ($req != '') {
            $res = maj_sql($req);
            if ($req_N != '') $res_N = maj_sql($req_N);
            $maj_site = true;
        }
    }

    // Traitement de l'union suite à la création d'un conjoint
    if ($creation_conj) {
        switch ($Sexe) {
            case 'm':
                $Conjoint_1 = $Ref_Pers;
                $Conjoint_2 = $creation_conj;
                break;
            case 'f':
                $Conjoint_1 = $creation_conj;
                $Conjoint_2 = $Ref_Pers;
                break;
            default:
                $Conjoint_1 = $Ref_Pers;
                $Conjoint_2 = $creation_conj;
        }
        $req = 'insert into ' . nom_table('unions') .
            '(Conjoint_1, Conjoint_2, Ville_Mariage, Ville_Notaire, Date_Creation, Date_Modification, Statut_Fiche) ' .
            ' values(' . $Conjoint_1 . ',' . $Conjoint_2 . ',0,0,current_timestamp,current_timestamp,\'I\')';
        $res = maj_sql($req);
    }


    // Traitement de l'enfant1
    // Présence d'une proposition pour l'enfant1 et demande de traitement
    if (($presence_prop_enfant1 == 'O') and ($choix_enfant1 != 'Ignorer')) {
        $req = '';
        $req_N = '';
        // Création de l'enfant1
        if ($choix_enfant1 == 'Ajouter') {
            //echo '>>> Ajouter enfant1<br />';
            $rubs = '';
            $cont = '';                                        // Initialisation des rubriques et du contenu
            // Traitement du nom de l'enfant1
            Traite_nom_Ins($nomenfant1_prop, $maj_enfant1);
            if ($prenomsenfant1_prop == '') $prenomsenfant1_prop = '?';
            Ins_Zone_Req_Rub($nouv_ident, 'N', 'Reference');
            Ins_Zone_Req_Rub($nomenfant1_prop, 'A', 'Nom');
            Ins_Zone_Req_Rub($prenomsenfant1_prop, 'A', 'Prenoms');
            Ins_Zone_Req_Rub($ne_leenfant1_prop, 'A', 'Ne_le');
            Ins_Zone_Req_Rub($decede_leenfant1_prop, 'A', 'Decede_Le');
            Ins_Zone_Req_Rub($id_ne_zoneenfant1_prop, 'N', 'Ville_Naissance');
            Ins_Zone_Req_Rub($id_decede_zoneenfant1_prop, 'N', 'Ville_Deces');
            Ins_Zone_Req_Rub('N', 'A', 'Diff_Internet');
            if ($rubs != '') {
                $req = 'insert into ' . nom_table('personnes') .
                    ' (' . $rubs . ',Sexe,Date_Creation,Date_Modification,Statut_Fiche,idNomFam) values' .
                    ' (' . $cont . ',\'m\',current_timestamp,current_timestamp,\'I\',' . $ident_nom . ')';
                $creation_enfant1 = $nouv_ident++;
                // Création de l'enregistrement dans la table des liens personnes / noms
                $req_N = $deb_ins_lien . $creation_enfant1 . ',' . $ident_nom . ',\'O\',null)';
            }
        }
        // Modification de l'enfant1
        if (substr($choix_enfant1, 0, 9) == 'Remplacer') {
            // Détermination de la référence de l'enfant1 à modifier ; le numéro est récupéré dans le libellé du choix
            $bpos = strpos($choix_enfant1, ' ', 10);
            $num_enfant1 = substr($choix_enfant1, $bpos + 1);
            $rub_ini = 'referenceenfant_ini' . $num_enfant1;
            $ref_ini = $$rub_ini;
            //echo '>>> Modifier enfant1'.$num_enfant1.'<br />';
            Traite_nom_Ins($nomenfant1_prop, $maj_enfant1, $nomenfant1_ini);
            if ($nomenfant1_prop == $nomenfant1_ini) $ident_nom = $idNomFamenfant1_ini;
            Aj_Zone_Req('Nom', $nomenfant1_prop, $nomenfant1_ini, 'A', $req);
            Aj_Zone_Req('Prenoms', $prenomsenfant1_prop, $prenomsenfant1_ini, 'A', $req);
            Aj_Zone_Req('Ne_le', $ne_leenfant1_prop, $ne_leenfant1_ini, 'A', $req);
            Aj_Zone_Req('Decede_Le', $decede_leenfant1_prop, $decede_leenfant1_ini, 'A', $req);
            Aj_Zone_Req('Ville_Naissance', $id_ne_zoneenfant1_prop, $id_ne_zoneenfant1_ini, 'N', $req);
            Aj_Zone_Req('Ville_Deces', $id_decede_zoneenfant1_prop, $id_decede_zoneenfant1_ini, 'N', $req);
            Aj_Zone_Req('Statut_Fiche', 'I', '', 'A', $req);
            if ($req != '') {
                $req = 'update ' . nom_table('personnes') . ' set ' . $req .
                    ', Date_Modification = current_timestamp' .
                    ' where Reference  = ' . $ref_ini;
                // Modification du nom de l'enfant ? Création de l'enregistrement dans la table des liens personnes / noms
                if ($nomenfant1_prop != $nomenfant1_ini) {
                    $req_N = $deb_ins_lien . $referenceenfant1_ini . ',' . $ident_nom . ',\'O\',null)';
                }
            }
        }
        // Exéution de la requête
        if ($req != '') {
            $res = maj_sql($req);
            if ($req_N != '') $res_N = maj_sql($req_N);
            $maj_site = true;
        }
    }

    /*
		Règles de gestion pour la filiation :
			Modification d'enfant : la filiation n'est pas mise à jour.
			Création d'un enfant : la filiation est créée avec la personne
	*/

    // revoir les filiations avec sexe de la personne non connu


    // Traitement de la filiation suite à la création de l'enfant1
    if ($creation_enfant1) {
        $req = 'insert into ' . nom_table('filiations') .
            ' values(' . $creation_enfant1 . ',';
        switch ($Sexe) {
            case 'm':
                $req .= $Ref_Pers . ',0';
                break;
            case 'f':
                $req .= '0,' . $Ref_Pers;
                break;
            default:
                $req = '';
        }
        if ($req != '') {
            $req .= ',0,current_timestamp,current_timestamp,\'I\')';
            $res = maj_sql($req);
        }
    }

    // Traitement de l'enfant2
    // Présence d'une proposition pour l'enfant2 et demande de traitement
    if (($presence_prop_enfant2 == 'O') and ($choix_enfant2 != 'Ignorer')) {
        $req = '';
        $req_N = '';
        // Création de l'enfant2
        if ($choix_enfant2 == 'Ajouter') {
            //echo '>>> Ajouter enfant2<br />';
            $rubs = '';
            $cont = '';                                        // Initialisation des rubriques et du contenu
            // Traitement du nom de l'enfant2
            Traite_nom_Ins($nomenfanté_prop, $maj_enfant2);
            if ($prenomsenfant2_prop == '') $prenomsenfant2_prop = '?';
            Ins_Zone_Req_Rub($nouv_ident, 'N', 'Reference');
            Ins_Zone_Req_Rub($nomenfant2_prop, 'A', 'Nom');
            Ins_Zone_Req_Rub($prenomsenfant2_prop, 'A', 'Prenoms');
            Ins_Zone_Req_Rub($ne_leenfant2_prop, 'A', 'Ne_le');
            Ins_Zone_Req_Rub($decede_leenfant2_prop, 'A', 'Decede_Le');
            Ins_Zone_Req_Rub($id_ne_zoneenfant2_prop, 'N', 'Ville_Naissance');
            Ins_Zone_Req_Rub($id_decede_zoneenfant2_prop, 'N', 'Ville_Deces');
            Ins_Zone_Req_Rub('N', 'A', 'Diff_Internet');
            if ($rubs != '') {
                $req = 'insert into ' . nom_table('personnes') .
                    ' (' . $rubs . ',Sexe,Date_Creation,Date_Modification,Statut_Fiche,idNomFam) values' .
                    ' (' . $cont . ',\'m\',current_timestamp,current_timestamp,\'I\',' . $ident_nom . ')';
                $creation_enfant2 = $nouv_ident++;
                // Création de l'enregistrement dans la table des liens personnes / noms
                $req_N = $deb_ins_lien . $creation_enfant2 . ',' . $ident_nom . ',\'O\',null)';
            }
        }
        // Modification de l'enfant2
        if (substr($choix_enfant2, 0, 9) == 'Remplacer') {
            // Détermination de la référence de l'enfant2 à modifier ; le numéro est récupéré dans le libellé du choix
            $bpos = strpos($choix_enfant2, ' ', 10);
            $num_enfant2 = substr($choix_enfant2, $bpos + 1);
            $rub_ini = 'referenceenfant_ini' . $num_enfant2;
            $ref_ini = $$rub_ini;
            Traite_nom_Ins($nomenfant2_prop, $maj_enfant2, $nomenfant2_ini);
            if ($nomenfant2_prop == $nomenfant2_ini) $ident_nom = $idNomFamenfant2_ini;
            Aj_Zone_Req('Nom', $nomenfant2_prop, $nomenfant2_ini, 'A', $req);
            Aj_Zone_Req('Prenoms', $prenomsenfant2_prop, $prenomsenfant2_ini, 'A', $req);
            Aj_Zone_Req('Ne_le', $ne_leenfant2_prop, $ne_leenfant2_ini, 'A', $req);
            Aj_Zone_Req('Decede_Le', $decede_leenfant2_prop, $decede_leenfant2_ini, 'A', $req);
            Aj_Zone_Req('Ville_Naissance', $id_ne_zoneenfant2_prop, $id_ne_zoneenfant2_ini, 'N', $req);
            Aj_Zone_Req('Ville_Deces', $id_decede_zoneenfant2_prop, $id_decede_zoneenfant2_ini, 'N', $req);
            Aj_Zone_Req('Statut_Fiche', 'I', '', 'A', $req);
            if ($req != '') {
                $req = 'update ' . nom_table('personnes') . ' set ' . $req .
                    ', Date_Modification = current_timestamp' .
                    ' where Reference  = ' . $ref_ini;
                // Modification du nom de l'enfant ? Création de l'enregistrement dans la table des liens personnes / noms
                if ($nomenfant2_prop != $nomenfant2_ini) {
                    $req_N = $deb_ins_lien . $referenceenfant2_ini . ',' . $ident_nom . ',\'O\',null)';
                }
            }
        }
        // Exéution de la requête
        if ($req != '') {
            $res = maj_sql($req);
            if ($req_N != '') $res_N = maj_sql($req_N);
            $maj_site = true;
        }
    }

    /*
		Règles de gestion pour la filiation :
			Modification d'enfant : la filiation n'est pas mise à jour.
			Création d'un enfant : la filiation est créée avec la personne
	*/

    // revoir les filiations avec sexe de la personne non connu


    // Traitement de la filiation suite à la création de l'enfant2
    if ($creation_enfant2) {
        $req = 'insert into ' . nom_table('filiations') .
            ' values(' . $creation_enfant2 . ',';
        switch ($Sexe) {
            case 'm':
                $req .= $Ref_Pers . ',0';
                break;
            case 'f':
                $req .= '0,' . $Ref_Pers;
                break;
            default:
                $req = '';
        }
        if ($req != '') {
            $req .= ',0,current_timestamp,current_timestamp,\'I\')';
            $res = maj_sql($req);
        }
    }

    // On renomme le fichier pour indiquer qu'il est traité
    $nom_fic_en = Recup_Nom_Fic($Contribution);
    //echo $nom_fic_en.'<br />';
    if (strpos($nom_fic_en, '_traitee') == 0) {
        $nom_fic_so = substr($nom_fic_en, 0, strlen($nom_fic_en) - 4) . '_traitee.txt';
        if (file_exists($nom_fic_en)) rename($nom_fic_en, $nom_fic_so);
    }

    if ($maj_site) maj_date_site();

    // Retour arrière
    Retour_Ar();
}

// Sur demande de suppression
if ($bt_Sup) {
    $nom_fic = Recup_Nom_Fic($Contribution);
    if (file_exists($nom_fic)) unlink($nom_fic);
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    // La page n'est autorisée qu'en configuration locale
    //if ($Environnement != 'I') {

    $compl = '';

    // Récupérer les informations présentes dans le fichier
    $nom_fic = Recup_Nom_Fic($Contribution);
    // Contrôle de l'existence du fichier qui normalement devrait être présent
    // vu que le nom en a été passé par la liste qui accède au répertoire
    if (!file_exists($nom_fic)) {
        echo '<center><font color="red"><br><br><br><h2>' . LG_CONTRIB_EDIT_FILE_N_EXISTS . ' ' . $nom_fic . '</h2></font></center>';
    }
    // Le fichier existe, on peut le traiter ==> récupération des données présentes dans le fichier
    else {
        $h_at = ' ' . my_html(LG_AT) . ' ';
        if ($fp = fopen($nom_fic, 'r')) {
            $cpt = 0;
            $message = '';
            // Balayage du fichier (ligne de 255 caractères max)
            while ($ligne = fgets($fp, 255)) {
                $cpt++;
                //echo $ligne.'<br />';
                if (strlen($ligne) > 0) {
                    $car1   = substr($ligne, 0, 1);

                    if ($car1 != '#') {

                        Traite_Ligne($ligne, 'Reference_Personne');

                        Traite_Ligne($ligne, 'Nompere');
                        Traite_Ligne($ligne, 'Prenomspere');
                        Traite_Ligne($ligne, 'Ne_lepere');
                        Traite_Ligne($ligne, 'NeZonepere');
                        Traite_Ligne($ligne, 'Nepere');
                        Traite_Ligne($ligne, 'Decede_lepere');
                        Traite_Ligne($ligne, 'DecedeZonepere');
                        Traite_Ligne($ligne, 'Decedepere');

                        Traite_Ligne($ligne, 'Nommere');
                        Traite_Ligne($ligne, 'Prenomsmere');
                        Traite_Ligne($ligne, 'Ne_lemere');
                        Traite_Ligne($ligne, 'NeZonemere');
                        Traite_Ligne($ligne, 'Nemere');
                        Traite_Ligne($ligne, 'Decede_lemere');
                        Traite_Ligne($ligne, 'DecedeZonemere');
                        Traite_Ligne($ligne, 'Decedemere');

                        Traite_Ligne($ligne, 'Nomconj');
                        Traite_Ligne($ligne, 'Prenomsconj');
                        Traite_Ligne($ligne, 'Ne_leconj');
                        Traite_Ligne($ligne, 'NeZoneconj');
                        Traite_Ligne($ligne, 'Neconj');
                        Traite_Ligne($ligne, 'Decede_leconj');
                        Traite_Ligne($ligne, 'DecedeZoneconj');
                        Traite_Ligne($ligne, 'Decedeconj');

                        Traite_Ligne($ligne, 'Nomenfant1');
                        Traite_Ligne($ligne, 'Prenomsenfant1');
                        Traite_Ligne($ligne, 'Ne_leenfant1');
                        Traite_Ligne($ligne, 'NeZoneenfant1');
                        Traite_Ligne($ligne, 'Neenfant1');
                        Traite_Ligne($ligne, 'Decede_leenfant1');
                        Traite_Ligne($ligne, 'DecedeZoneenfant1');
                        Traite_Ligne($ligne, 'Decedeenfant1');

                        Traite_Ligne($ligne, 'Nomenfant2');
                        Traite_Ligne($ligne, 'Prenomsenfant2');
                        Traite_Ligne($ligne, 'Ne_leenfant2');
                        Traite_Ligne($ligne, 'NeZoneenfant2');
                        Traite_Ligne($ligne, 'Neenfant2');
                        Traite_Ligne($ligne, 'Decede_leenfant2');
                        Traite_Ligne($ligne, 'DecedeZoneenfant2');
                        Traite_Ligne($ligne, 'Decedeenfant2');
                    } else {
                        $x = Traite_Ligne2($ligne, 'version G');
                        //$x = Traite_Ligne2($ligne,'version Génémania');
                        if ($x != '') $vers_gen = $x;
                        $x = Traite_Ligne2($ligne, 'IP serveur');
                        if ($x != '') $ip_serveur = $x;
                        $x = Traite_Ligne2($ligne, 'Nom serveur');
                        if ($x != '') $serveur = $x;
                        $x = Traite_Ligne2($ligne, 'User agent');
                        if ($x != '') $navig = $x;
                        $x = Traite_Ligne2($ligne, 'IP utilisateur');
                        if ($x != '') $ip_util = $x;
                        $x = Traite_Ligne2($ligne, 'Mail');
                        if ($x != '') $mail = $x;
                        $x = Traite_Ligne2($ligne, 'Message');
                        if ($x != '') $message = $x;
                    }
                }
            }
            fclose($fp);

            $Ref_Pers = $Reference_Personne;

            // Récupération des données de la personne
            $sql = 'select Nom, Prenoms, Sexe from ' . nom_table('personnes') . ' where Reference  = ' . $Ref_Pers . ' limit 1';
            $trouve = 0;
            if ($res = lect_sql($sql)) {
                if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                    $Nom     = my_html($enreg[0]);
                    $Prenoms = my_html($enreg[1]);
                    $Sexe    = $enreg[2];
                    $trouve  = 1;
                }
                $res->closeCursor();
            }
            if ($trouve) {
                $compl = Ajoute_Page_Info(600, 300);
                Insere_Haut($titre . ' ' . LG_CONTRIB_EDIT_FOR . ' ' . $Prenoms . ' ' . $Nom, $compl, 'Edition_Contribution', $Contribution);
                echo '<br />' . "\n";
                echo '<form id="saisie" method="post" action="' . my_self() . '?Contribution=' . $Contribution . '">' . "\n";
                echo '<input type="hidden" name="Ref_Pers" value="' . $Ref_Pers . '"/>' . "\n";
                echo '<input type="hidden" name="Sexe" value="' . $Sexe . '"/>' . "\n";
                echo '<input type="hidden" name="Horigine" value="' . $Horigine . '"/>' . "\n";

                // Affichage des données
                echo '<div id="content">' . "\n";
                echo '<table id="cols"  border="0" cellpadding="0" cellspacing="0" >' . "\n";
                echo '<tr>' . "\n";
                echo '<td style="border-right:0px solid #9cb0bb">' . "\n";
                echo '  <img src="' . $chemin_images . $Icones['clear'] . '" width="790" height="1" alt="clear"/>' . "\n";
                echo '</td>' . "\n";
                echo '</tr>' . "\n";

                echo '<tr>' . "\n";
                echo '<td class="left">' . "\n";
                echo '<div class="tab-container" id="container1">' . "\n";
                // Onglets
                echo '<ul class="tabs">' . "\n";
                echo '<li><a href="#" onclick="return showPane(\'pnlParents\', this)" id="tab1">' . LG_CONTRIB_EDIT_PARENTS . '</a></li>' . "\n";
                echo '<li><a href="#" onclick="return showPane(\'pnlConjoint\', this)">' . LG_CONTRIB_EDIT_HUB_WIFE . '</a></li>' . "\n";
                echo '<li><a href="#" onclick="return showPane(\'pnlEnfants\', this)">' . LG_CONTRIB_EDIT_CHILD . '</a></li>' . "\n";
                // Captcha pour autoriser le OK
                echo '<li><a href="#" onclick="return showPane(\'pnlDatas\', this)">' . LG_CONTRIB_EDIT_DATAS . '</a></li>' . "\n";
                echo '</ul>' . "\n";

                echo '<div class="tab-panes">' . "\n";

                $icone_tip = '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . LG_TIP . '" title="' . LG_TIP . '">';

                // Connait-on les parents de la personne ?
                $pere = 0;
                $mere = 0;
                $rang = 0;
                $x = Get_Parents($Reference_Personne, $pere, $mere, $rang);

                // Onglet parents
                echo '<div id="pnlParents">' . "\n";
                echo '<fieldset>' . "\n";
                echo '<legend>' . LG_FATHER . '</legend>' . "\n";
                init_ini();
                if ($pere != 0) {
                    Recup_Don_Pers($pere);
                }
                $donnees_prop = '';
                $donnees_prop = array(
                    'nom'            => $Nompere,
                    'prenoms'        => $Prenomspere,
                    'ne_le'          => $Ne_lepere,
                    'id_ne_zone'     => $NeZonepere,
                    'ne_zone'        => $Nepere,
                    'decede_le'      => $Decede_lepere,
                    'id_decede_zone' => $DecedeZonepere,
                    'decede_zone'    => $Decedepere
                );
                $x = Aff_Pers2('pere', $donnees_ini, $donnees_prop);
                echo '</fieldset>' . "\n";
                echo '<fieldset>' . "\n";
                echo '<legend>' . LG_MOTHER . '</legend>' . "\n";
                init_ini();
                if ($mere != 0) {
                    Recup_Don_Pers($mere);
                }

                $donnees_prop = '';
                $donnees_prop = array(
                    'nom'            => $Nommere,
                    'prenoms'        => $Prenomsmere,
                    'ne_le'          => $Ne_lemere,
                    'id_ne_zone'     => $NeZonemere,
                    'ne_zone'        => $Nemere,
                    'decede_le'      => $Decede_lemere,
                    'id_decede_zone' => $DecedeZonemere,
                    'decede_zone'    => $Decedemere
                );
                $x = Aff_Pers2('mere', $donnees_ini, $donnees_prop);
                echo '</fieldset>' . "\n";

                echo '<br />' . $icone_tip . ' ' . LG_CONTRIB_EDIT_TIP1 . ' ' . '<br />';
                echo '  - <b>' . LG_CONTRIB_EDIT_TIP2 . '</b><br />';
                echo '  - <i>' . LG_CONTRIB_EDIT_TIP3 . '</i><br /><br />';

                echo '</div>' . "\n";

                // Onglet conjoint
                echo '<div id="pnlConjoint">' . "\n";
                init_ini();
                $donnees_prop = '';
                $donnees_prop = array(
                    'nom'            => $Nomconj,
                    'prenoms'        => $Prenomsconj,
                    'ne_le'          => $Ne_leconj,
                    'id_ne_zone'     => $NeZoneconj,
                    'ne_zone'        => $Neconj,
                    'decede_le'      => $Decede_leconj,
                    'id_decede_zone' => $DecedeZoneconj,
                    'decede_zone'    => $Decedeconj
                );

                echo '  <fieldset>' . "\n";
                $x = Aff_Pers2('conj', $donnees_ini, $donnees_prop);
                echo '  </fieldset>' . "\n";
                echo '<br />' . $icone_tip . ' ' . my_html(LG_CONTRIB_EDIT_TIP_HUSB_WIFE);

                echo '</div>' . "\n";

                // Onglet enfants
                echo '<div id="pnlEnfants">' . "\n";
                init_ini();
                $donnees_prop = '';
                $donnees_prop = array(
                    'nom'            => $Nomenfant1,
                    'prenoms'        => $Prenomsenfant1,
                    'ne_le'          => $Ne_leenfant1,
                    'id_ne_zone'     => $NeZoneenfant1,
                    'ne_zone'        => $Neenfant1,
                    'decede_le'      => $Decede_leenfant1,
                    'id_decede_zone' => $DecedeZoneenfant1,
                    'decede_zone'    => $Decedeenfant1
                );

                echo '  <fieldset>' . "\n";
                $x = Aff_Pers2('enfant1', $donnees_ini, $donnees_prop);
                echo '  </fieldset>' . "\n";
                init_ini();
                $donnees_prop = '';
                $donnees_prop = array(
                    'nom'            => $Nomenfant2,
                    'prenoms'        => $Prenomsenfant2,
                    'ne_le'          => $Ne_leenfant2,
                    'id_ne_zone'     => $NeZoneenfant2,
                    'ne_zone'        => $Neenfant2,
                    'decede_le'      => $Decede_leenfant2,
                    'id_decede_zone' => $DecedeZoneenfant2,
                    'decede_zone'    => $Decedeenfant2
                );

                echo '  <fieldset>' . "\n";
                $x = Aff_Pers2('enfant2', $donnees_ini, $donnees_prop);
                echo '  </fieldset>' . "\n";
                echo '<br />' . $icone_tip . ' ' . my_html(LG_CONTRIB_EDIT_TIP_CHILD);
                echo '</div>' . "\n";

                // Onglet déverrouillage bouton OK
                echo '<div id="pnlDatas">' . "\n";

                echo '<table width="100%">' . "\n";
                echo '<tr><td width="25%">' . LG_CONTRIB_EDIT_VERSION . '</td>';
                echo '<td>' . $vers_gen . '</td></tr>';
                echo '<tr><td width="25%">' . LG_CONTRIB_EDIT_SERVER_IP . '</td>';
                echo '<td>' . $ip_serveur . '</td></tr>';
                echo '<tr><td width="25%">' . LG_CONTRIB_EDIT_SERVER . '</td>';
                echo '<td>' . $serveur . '</td></tr>';
                echo '<tr><td width="25%">' . LG_CONTRIB_EDIT_BROWSER . '</td>';
                echo '<td>' . $navig . '</td></tr>';
                echo '<tr><td width="25%">' . LG_CONTRIB_EDIT_IP . '</td>';
                echo '<td>' . $ip_util . '</td></tr>';
                echo '<tr><td width="25%">' . LG_CONTRIB_EDIT_MAIL . '</td>';
                echo '<td>' . $mail . '</td></tr>';
                echo '<tr><td width="25%">' . LG_CONTRIB_EDIT_MESSAGE . '</td>';
                echo '<td>' . $message . '</td></tr>';
                echo ' </table>';
                echo '</div>' . "\n";
                echo '</div>' . "\n";   //  <!-- panes -->

                bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_Supprimer, LG_CONTRIB_EDIT_THIS, false);

                echo '</div>' . "\n";  //  <!-- tab container -->
                echo '</td></tr></table></div>' . "\n";

                echo '</form>';
                include(__DIR__ . '/assets/js/gest_onglets.js');
                echo '<!-- On positionne l\'onglet par défaut -->' . "\n";
                echo '<script type="text/javascript">' . "\n";
                echo '	setupPanes("container1", "tab1",0);' . "\n";
                echo '</script>' . "\n";
            }
        }
        // Echec sur l'ouverture du fichier
        else {
            echo '<center><font color="red"><br><br><br><h2>' . LG_CONTRIB_EDIT_FILE_ERROR . my_html($nom_fic) . '</h2></font></center>';
        }
    }
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
} else {
    echo "<body bgcolor=\"#FFFFFF\">";
}
?>
</body>

</html>