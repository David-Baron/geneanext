<?php
//=====================================================================
// Saisie multiple de personnes nées ou décédées dans une ville
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$tab_variables = array(
    'ok',
    'annuler',
    'Type_ListeP',
    'idNomV'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$ok      = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

$acces = 'M';                // Type d'accès de la page : (M)ise à jour, (L)ecture

// Recup des variables passées dans l'URL :
$Type_Liste = Recup_Variable('evt', 'C', 'ND');    // type d'évènement Naissance / Décès
$idNom      = Recup_Variable('idNom', 'N');        // Identifiant ville
$Nom        = Recup_Variable('Nom', 'S');        // Nom de ville

// La liste des personnes par catégorie n'est valable que pour le profil gestionnaire ; sinon, on débranche sur la liste de spersonnes par nom
if ($Type_Liste == 'C') {
    if (!$_SESSION['estGestionnaire']) $Type_Liste = 'N';
}

switch ($Type_Liste) {
    case 'N':
        $objet = LG_PERS_BORN_IN . ' ' . $Nom;
        break;
    case 'D':
        $objet = LG_PERS_DEAD_IN . ' ' . $Nom;
        break;
    default:
        break;
}
$objet = stripcslashes($objet);

$titre = $objet;                       // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Nombre maximum de personnes saisissables
$max_personnes = 5;
if ((!$SiteGratuit) or ($Premium)) $max_personnes *= 3;

//Demande de mise à jour
if ($bt_OK) {

    $Type_ListeP = Secur_Variable_Post($Type_ListeP, 1, 'S');
    $idNomV      = Secur_Variable_Post($idNomV, 1, 'N');

    $maj_site = false;
    $nouv_ident = -1;

    $deb_req_pers = 'insert into ' . nom_table('personnes') .
        '(Reference, Nom, Prenoms, Ne_le, Decede_Le, Sexe, Date_Creation,Date_Modification,Statut_Fiche,idNomFam,Diff_Internet,Ville_Naissance,Ville_Deces) values (';
    $deb_req_nom_pers = 'insert into ' . nom_table('noms_personnes') . ' values(';

    // Balayage des lignes des enfants  	
    for ($num_ligne = 1; $num_ligne <= $max_personnes; $num_ligne++) {
        // Traitement du nom
        $NomP = retourne_var_post('NomP_', $num_ligne);
        // On commence par enlever les numéros en entête des noms
        $idNomP = 0;
        $posi = strpos($NomP, '/');
        if ($posi > 0) {
            $idNomP = strval(substr($NomP, 0, $posi));
            $NomP = substr($NomP, $posi + 1);
        }

        $LesPrenoms = retourne_var_post('PrenomsP_', $num_ligne);
        $D_Nai = retourne_var_post('CNe_leP_', $num_ligne);
        if ($Type_ListeP == 'D') $D_Dec = retourne_var_post('CDecede_leP_', $num_ligne);
        else $D_Dec = '';
        $LeSexe = retourne_var_post('SexeP_', $num_ligne);

        // Gestion des villes
        $VilNai = 0;
        $VilDec = 0;
        if ($Type_ListeP == 'N') $VilNai = $idNomV;
        else {
            $VilNai = retourne_var_post('SelVille_Nai_', $num_ligne);
            $VilDec = $idNomV;
            //echo 'idNom : '.$idNomV.'<br />';
        }

        // Création de la personne si les prénoms sont connus
        if ($LesPrenoms != '') {

            if ($LeSexe != '') $LeSexe = '"' . $LeSexe . '"';
            else $LeSexe = 'null';

            // Création de la personne
            if ($nouv_ident == -1) $nouv_ident = Nouvel_Identifiant('Reference', 'personnes');
            else $nouv_ident++;
            $reqE = $deb_req_pers .
                $nouv_ident . ',"' . $NomP . '","' . $LesPrenoms . '","' . $D_Nai . '","' . $D_Dec . '",' . $LeSexe .
                ',current_timestamp,current_timestamp,\'N\',' . $idNomP . ',\'O\',' . $VilNai . ',' . $VilDec . ')';
            $res = maj_sql($reqE);

            // Référencement du nom de la personne		
            $reqE = $deb_req_nom_pers . $nouv_ident . ',' . $idNomP . ',\'O\',null)';
            $res = maj_sql($reqE);

            $maj_site = true;
        }
    }

    // Mise à jour de la date de mise à jour du site
    if ($maj_site) maj_date_site();

    // Retour arrière
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    // Récupération de la liste des types
    // Recup_Types_Evt('U');

    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut(my_html($objet), $compl, 'Edition_Personnes_Ville', $Type_Liste);

    echo '<form id="saisie" method="post" action="' . my_self() . '?' . Query_Str() . '">' . "\n";

    echo '<input type="' . $hidden . '" name="Type_ListeP" id="Type_ListeP" value="' . $Type_Liste . '"/>';
    echo '<input type="' . $hidden . '" name="idNomV" id="idNomV" value="' . $idNom . '"/>';

    if ($Type_Liste == 'N') $larg = '60';
    else $larg = '80';
    if ($Type_Liste == 'D') $csp = 4;
    else $csp = 5;

    echo '<table border="0" width="' . $larg . '%" align="center">' . "\n";

    // echo '<tr><td colspan="'.$csp.'">'.my_html(LG_PERS_DATE_PATTERN).'&nbsp;';
    // if ($Type_Liste == 'N') echo my_html(lcfirst($LG_birth)); else echo my_html(lcfirst($LG_death));
    // echo '&nbsp;: ';
    // zone_date2('Ad_def', 'd_def', 'Cd_def', '');
    echo '<tr align="center">' . "\n";
    echo '<td class="rupt_table">' . LG_PERS_NAME . '</td>';
    echo '<td class="rupt_table">' . LG_PERS_FIRST_NAME . '</td>';
    echo '<td class="rupt_table">' . $LG_birth . '</td>';
    if ($Type_Liste == 'D') echo '<td class="rupt_table">' . $LG_death . '</td>';
    echo '<td class="rupt_table">' . LG_SEXE . '</td>';
    echo '</tr>' . "\n";

    for ($nb = 1; $nb <= $max_personnes; $nb++) {
        if (pair($nb)) $style = 'liste';
        else           $style = 'liste2';
        echo '<tr>';
        echo '<td class="' . $style . '">';
        // Modes d'ouverture / fermeture des curseurs
        if ($nb == 1) $curs = 'ON';
        else $curs = 'NN';
        Select_Noms(0, 'NomP_' . $nb, 'CNomP_' . $nb, $curs);
        echo '<input type="' . $hidden . '" name="CNomP_' . $nb . '" id="CNomP_' . $nb . '"/></td>';
        echo '<td class="' . $style . '"><input type="text" size="20" name="PrenomsP_' . $nb . '" id="PrenomsP_' . $nb . '" class="oblig"/></td>' . "\n";
        echo '<td class="' . $style . '">';
        //zone_date('Ne_leP_'.$nb, 'CNe_leP_'.$nb, 'imgCal_Nai_'.$nb, 'Calendrier_Pers_N('.$nb.')');
        zone_date2('ANe_leP_' . $nb, 'Ne_leP_' . $nb, 'CNe_leP_' . $nb, '');
        if ($Type_Liste == 'D') {
            if ($nb == 1) aff_liste_villes('SelVille_Nai_' . $nb, true, false, 0);
            else aff_liste_villes('SelVille_Nai_' . $nb, false, false, 0);
        }
        echo '</td>' . "\n";
        if ($Type_Liste == 'D') {
            echo '<td class="' . $style . '">';
            // zone_date('Decede_leP_'.$nb, 'CDecede_leP_'.$nb, 'imgCal_Dec_'.$nb, 'Calendrier_Pers_D('.$nb.')');
            zone_date2('ADecede_leP_' . $nb, 'Decede_leP_' . $nb, 'CDecede_leP_' . $nb, '');
            echo '</td>' . "\n";
        }
        $nom = 'SexeP_' . $nb;
        echo '<td class="' . $style . '"><input type="radio" id="' . $nom . '_m" name="' . $nom . '" value="m"/><label for="' . $nom . '_m">' . LG_SEXE_MAN_I . '</label>';
        echo '<input type="radio" id="' . $nom . '_f" name="' . $nom . '" value="f"/><label for="' . $nom . '_f">' . LG_SEXE_WOMAN_I . '</label></td>';
        echo '</tr>' . "\n";
    }

    //echo '<tr><td colspan="'.$csp.'" align="center">&nbsp;</td></tr>'."\n";
    echo '<tr><td colspan="' . $csp . '" align="center">';
    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);
    echo '</td></tr>' . "\n";
    echo '</table>';

    echo '</form>';
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