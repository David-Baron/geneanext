<?php
//=====================================================================
// Gestion des rangs d'une union
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$tab_variables = array('ok', 'annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$ok       = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// Récupération des variables de l'affichage précedent pour les variables suffixées ; on les bascule dans des variables indicées
//                        'LeRang','MemoRa','Enfants',
foreach ($_POST as $key => $value) {
    if (strpos($key, 'Calcul') === false) {
        $$key = addslashes(trim($value));
        if (strpos($key, 'LeRang_') !== false) {
            $value = Secur_Variable_Post($value, 1, 'N');
            $LeRang[] = $value;
        }
        if (strpos($key, 'MemoRa_') !== false) {
            $value = Secur_Variable_Post($value, 1, 'N');
            $MemoRa[] = $value;
        }
        if (strpos($key, 'Enfants_') !== false) {
            $value = Secur_Variable_Post($value, 1, 'N');
            $Enfants[] = $value;
        }
    }
}

// Gestion standard des pages
$acces = 'M';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Rank_Edit'];        // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup des variables passées dans l'URL : père et mère
$Pere = Recup_Variable('Pere', 'N');
$Mere = Recup_Variable('Mere', 'N');

//Demande de mise à jour
if ($bt_OK) {
    // Détection et maj ligne par ligne
    $maj_site = false;
    for ($nb = 0; $nb < count($Enfants); $nb++) {
        if ($LeRang[$nb] != $MemoRa[$nb]) {
            $req = 'update ' . nom_table('filiations') . ' set rang = ' . $LeRang[$nb] .
                ' where Pere = ' . $Pere . ' and Mere = ' . $Mere .
                ' and Enfant = ' . $Enfants[$nb];
            $res = maj_sql($req);
            $maj_site = true;
        }
    }
    if ($maj_site) maj_date_site();
    // Retour vers la page précédente
    Retour_Ar();
}

// Première entrée : affichage pour saisie
if (($ok == '') && ($annuler == '')) {

    # include(__DIR__ . '/assets/js/Edition_Rangs.js');		Remplacé par un script embeded vue sa taille
    $compl = Ajoute_Page_Info(650, 200);
    Insere_Haut($titre, $compl, 'Edition_Rangs', $Pere . '/' . $Mere);

    // Récupération de la date de l'union
    $Maries_Le = '';
    $sql = 'select Maries_Le from ' . nom_table('unions') . ' where ('
        . '(Conjoint_1 = ' . $Pere . ' and Conjoint_2 = ' . $Mere . ') or '
        . '(Conjoint_1 = ' . $Mere . ' and Conjoint_2 = ' . $Pere . ') '
        . ') limit 1';
    if ($res = lect_sql($sql)) {
        if ($union = $res->fetch(PDO::FETCH_NUM)) {
            $Maries_Le = $union[0];
        }
        $res->closeCursor();
        unset($res);
    }
    if ($Maries_Le != '') {
        if ((strlen($Maries_Le) == 10) and ($Maries_Le[9] == 'L'))
            echo '<br />' . $LG_Rank_Parents_Union . ' ' . Etend_date($Maries_Le) . '<br /><br />';
        else
            $Maries_Le = '';
    }
    $msg_premier = '';
    $msg_dernier = '';

    $sql = 'select Enfant, rang from ' . nom_table('filiations') . ' where pere = ' . $Pere . ' and mere = ' . $Mere . ' order by rang';
    $res = lect_sql($sql);
    $nb_enr = $res->rowCount();
    $var_ctrl = '';
    for ($nb = 0; $nb < $nb_enr; $nb++) {
        if ($var_ctrl != '') $var_ctrl .= ',';
        $var_ctrl .= 'LeRang_' . sprintf("%03d", $nb);
    }

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'' . $var_ctrl . '\')" action="' . my_self() . '?Pere=' . $Pere . '&amp;Mere=' . $Mere . '">' . "\n";
    echo '<input type="' . $hidden . '" name="Pere" value="' . $Pere . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="Mere" value="' . $Mere . '"/>' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";

    // Récupération des enfants avec le conjoint
    echo '<table align="center" width="90%">';
    echo '<tr class="rupt_table">';
    echo '<th width="25%">' . LG_FIRST_NAME . '</th>';
    echo '<th width="15%">' . $LG_Rank_Born . '</th>';
    echo '<th width="15%">' . $LG_Rank_Dead . '</th>';
    echo '<th width="15%">' . $LG_Rank_Calc_Duration . '</th>';
    echo '<th width="15%">' . $LG_Rank_Calculated . '</th>';
    echo '<th width="15%">' . $LG_Rank_Filled . '</th>';
    echo '</tr>' . "\n";
    echo "\n";
    $rangs_OK = 1;
    $Ne_Prec = '00000000GL';
    $nb_enfants = 0;

    // Mémorisation des enfants
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $Enfant = $row[0];
        $sqlEnf = 'select Prenoms, Ne_le, Decede_Le from ' . nom_table('personnes') . ' where reference = ' . $Enfant . ' limit 1';
        if ($resEnf = lect_sql($sqlEnf)) {
            if ($enrEnf = $resEnf->fetch(PDO::FETCH_NUM)) {
                ++$nb_enfants;
                $Enfants[] = $Enfant;
                $Rangs[]   = $row[1];
                $Prenoms[] = my_html($enrEnf[0]);
                $Nes[]     = $enrEnf[1];
                $Decedes[] = $enrEnf[2];
            }
        }
    }

    $rang_indef = 999;

    // Affichage des enfants
    for ($nb = 0; $nb < $nb_enfants; $nb++) {
        $rangs_OK_ligne = 1;
        // Le rang n'est pas OK s'il est à 0
        if ($Rangs[$nb] == 0) $rangs_OK_ligne = 0;

        if (pair($nb)) $style = 'liste';
        else           $style = 'liste2';
        echo '<tr class="' . $style . '">' . "\n";

        echo '<td> <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Enfants[$nb] . '">' . $Prenoms[$nb] . '</a></td>' . "\n";
        $Date_Nai = Etend_date($Nes[$nb]);
        $R_Cal = 0;
        $Ne = '----------';
        if ($Date_Nai == '') $Date_Nai = ' ';
        else {
            // Contrôle du rang de cette date dans les dates de naissance
            // On cherche les dates inférieures, ce qui donne un rang théorique
            $nb_inf = 0;
            $Ne = $Nes[$nb];
            if ((strlen($Ne) == 10) and ($Ne[9] == 'L')) {
                if ($nb == 0) $msg_premier = Age_Annees_Mois($Maries_Le, $Ne);
                if ($nb == $nb_enfants - 1) $msg_dernier = Age_Annees_Mois($Maries_Le, $Ne);
                $max_ne_inf = '01234567890';
                for ($nb2 = 0; $nb2 < $nb_enfants; $nb2++) {
                    $Ne2 = $Nes[$nb2];
                    if ((strlen($Ne2) == 10) and ($Ne2[9] == 'L') and ($nb2 != $nb)) {
                        if ($Ne2 < $Ne) {
                            ++$nb_inf;
                            $max_ne_inf = $Ne2;
                        }
                    }
                }
                $R_Cal = $nb_inf + 1;
            }
        }
        echo '<td> ' . $Date_Nai . '</td>' . "\n";
        $Date_Dec = Etend_date($Decedes[$nb]);
        if ($Date_Dec == '') $Date_Dec = ' ';
        echo '<td> ' . $Date_Dec . '</td>' . "\n";
        echo '<td>';
        // Calcul du nombre de mois / années par rapport à l'enfant précédent
        if (($Ne[9] == 'L') and ($max_ne_inf[9] == 'L')) {
            //echo '('.$max_ne_inf.' - '.$Ne.') >> '.Age_Annees_Mois($max_ne_inf,$Ne).'   ';
            echo ' ' . Age_Annees_Mois($max_ne_inf, $Ne);
            if (Age_Mois($max_ne_inf, $Ne) < 9) {
                echo ' <img src="' . $root . '/assets/img/' . $Icones['warning'] . '" alt="' . my_html($LG_Rank_Short_Duration) . '" title="' . my_html($LG_Rank_Short_Duration) . '">';
            }
        } else echo ' ';
        echo '</td>' . "\n";
        echo '<td align="center">' . $R_Cal;
        echo '<input type="hidden" name="Calcul[]" value="' . $R_Cal . '"/>';
        if ($R_Cal != $Rangs[$nb]) echo ' img src="' . $root . '/assets/img/' . $Icones['warning'] . '" alt="' . my_html($LG_Rank_Error) . '" title="' . my_html($LG_Rank_Error) . '">';
        echo '</td>' . "\n";
        echo '<td align="center"';
        /*
		if ($R_Cal != $Rangs[$nb]) {
		  echo ' bgcolor="#FF0000"';
		  $rangs_OK = 0;
		}
		*/
        echo '>';

        $suf = sprintf("%03d", $nb);
        $var_R = 'LeRang_' . $suf;

        $texte_image = 'Diminuer le rang';
        echo '<img src="' . $root . '/assets/img/' . $Icones['moins'] . '" alt="' . $texte_image . '" title="' . $texte_image . '" border="0" ';
        echo 'onclick="if (document.forms.saisie.' . $var_R . '.value>0) {document.forms.saisie.' . $var_R . '.value--;}"/>' . "\n";
        echo '<input type="text" class="oblig" name="' . $var_R . '" id="' . $var_R . '" value="' . $Rangs[$nb] . '" size="3" onchange="verification_num(this);"/>' . "\n";
        $texte_image = 'Augmenter le rang';
        echo '<img src="' . $root . '/assets/img/' . $Icones['plus'] . '" alt="' . $texte_image . '" title="' . $texte_image . '" border="0" ';
        echo 'onclick="document.forms.saisie.' . $var_R . '.value++;"/> ' . "\n";
        echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';

        echo '<input type="hidden" name="MemoRa_' . $suf . '" value="' . $Rangs[$nb] . '"/>';
        echo '<input type="hidden" name="Enfants_' . $suf . '" value="' . $Enfants[$nb] . '"/>';

        echo "</td></tr>\n";
    }

    // Libération de la mémoire
    unset($Enfants, $Rangs, $Prenoms, $Nes, $Decedes);

    $res->closeCursor();
    echo '</table>';

    if ($msg_premier != '')
        echo '<br />' . $LG_Rank_First_Children . $msg_premier . $LG_Rank_End_Union;
    if ($msg_dernier != '')
        echo '<br />' . $LG_Rank_Last_Children . $msg_dernier . $LG_Rank_End_Union;

    echo '<br /><br />';
    echo '<table width="90%" align="center" border="0">' . "\n";
    echo '<tr align="right"><td>' . "\n";
    echo '<div class="buttons">';
    echo '<button onclick="Accepter(this);return false;"> ' .
        '<img src="' . $root . '/assets/img/' . $Icones['accepter'] . '" alt=""/>' . $LG_Rank_Accept . '</button>';
    echo '</div>';
    echo '</td></tr>' . "\n";
    echo '<tr><td align="left">' . "\n";
    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    echo '</form>' . "\n";
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
<script type="text/javascript">
    // Accepter les rangs théoriques calculés par le programme 
    function Accepter(theElement) {
        for (var i = 0; i < document.forms["saisie"].length; i++) {
            LeNom = document.forms["saisie"].elements[i].name.substring(0, 6);
            if (LeNom == "Calcul") {
                Reprise = document.forms["saisie"].elements[i].value;
            }
            if (LeNom == "LeRang") {
                document.forms["saisie"].elements[i].value = Reprise;
            }
        }
    }
</script>
</body>

</html>