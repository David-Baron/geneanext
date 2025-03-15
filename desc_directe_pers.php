<?php

//=====================================================================
// Descendance directe d'une personne au format HTML ou au format texte
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Direct_Desc'];        // Titre pour META
$x = Lit_Env();
$index_follow = 'IN';                        // NOFOLLOW demandé pour les moteurs
require(__DIR__ . '/app/ressources/gestion_pages.php');

$n_filiations = nom_table('filiations');
$n_personnes = nom_table('personnes');
$n_unions = nom_table('unions');

$conj_demandes = true;

// Recherche les infos d'une personne
// Renvoye 1 si personne trouvée
function Affiche_Pers($num)
{

    global $root, $conj_demandes, $n_personnes, $n_unions, $Diff_Internet_P, $texte, $fin_arbres_asc, $fin_arbres_desc, $LG_Data_noavailable_profile;

    $sql = 'select Reference, Nom, Prenoms, Diff_Internet, Ne_le, Decede_Le, Sexe ' .
        ' from ' . $n_personnes .
        ' where Numero = "' . $num . '"	 limit 1';
    if ($res = lect_sql($sql)) {
        if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            if (($_SESSION['estGestionnaire']) or ($enreg[3] == 'O')) {
                echo $num . ' : ';
                $Ref_Pers = $enreg[0];
                if (! $texte)
                    echo '<a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Ref_Pers . '">' . my_html($enreg[2] . ' ' . $enreg[1]) . '</a>';
                else
                    echo my_html($enreg[2] . ' ' . $enreg[1]);
                $Ne = $enreg[4];
                $Decede = $enreg[5];
                if (($Ne != '') or ($Decede != '')) {
                    echo ' (';
                    if ($Ne != '') echo '&deg; ' . Etend_date($Ne);
                    if ($Decede != '') {
                        if ($Ne != '') echo ', ';
                        echo '+ ' . Etend_date($Decede);
                    }
                    echo ')';
                }
                if (!$texte) {
                    echo '  <a href="' . $root . '/arbre_asc_pers.php?Refer=' . $Ref_Pers . '"' . $fin_arbres_asc;
                    echo ' <a href="' . $root . '/arbre_desc_pers.php?Refer=' . $Ref_Pers . '"' . $fin_arbres_desc;
                }

                // Si les conjoints ont été demandés, on va les chercher
                if ($conj_demandes) {
                    $sql = '';
                    switch ($enreg[6]) {
                        case 'm':
                            $sql = 'select Conjoint_2 from ' . $n_unions . ' where Conjoint_1 = ' . $Ref_Pers;
                            break;
                        case 'f':
                            $sql = 'select Conjoint_1 from ' . $n_unions . ' where Conjoint_2 = ' . $Ref_Pers;
                            break;
                    }
                    if ($sql != '') {
                        $sql .= ' order by Maries_Le';
                        $Conjs_Pers = '';
                        if ($res = lect_sql($sql)) {
                            while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                                if (Get_Nom_Prenoms($enreg[0], $Nom, $Prenoms)) {
                                    if ($Diff_Internet_P == 'O' or $_SESSION['estPrivilegie']) {
                                        if ($Conjs_Pers != '') $Conjs_Pers .= ', ';
                                        $Conjs_Pers = $Conjs_Pers . $Nom . ' ' . $Prenoms;
                                    }
                                }
                            }
                        }
                        if ($Conjs_Pers != '')
                            echo ' x ' . $Conjs_Pers;
                    }
                }
            } else echo my_html($LG_Data_noavailable_profile);
        }
        echo '<br />' . "\n";
    }
}

// Recup de la variable passée dans l'URL : référence de la personne
$Numero = Recup_Variable('Numero', 'N');

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();

$conj_demandes = 0;
if (isset($_POST['conj_demandes'])) $conj_demandes = true;
if ($texte) {
    $avec_conjoints = Recup_Variable('avec_conjoints', 'C', 1);
    if ($avec_conjoints) $conj_demandes = true;
}

$comp_texte = '';
if ($conj_demandes) $comp_texte .= '&amp;avec_conjoints=O';

$compl = Ajoute_Page_Info(600, 250) .
    Affiche_Icone_Lien('href="' . $root . '/desc_directe_pers.php?Numero=' . $Numero . '&amp;texte=O' . $comp_texte . '"', 'text', $LG_printable_format) . ' ';

if (! $texte) Insere_Haut($titre, $compl, 'Desc_Directe_Pers', $Numero);
else {
    echo '</head>' . "\n";
    echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td align="center"><b> </b></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
}


if (! $texte) {
    echo '<form action="' . my_self() . '?Numero=' . $Numero . '" method="post">' . "\n";
    echo '<table width="60%" align="center">' . "\n";
    echo '<tr align="center">';

    echo '<td class="rupt_table">' . my_html($LG_Tree_Show_Partners) . ' : ' . "\n";
    echo '<input type="checkbox"';
    if ($conj_demandes) echo ' checked="checked"';
    echo ' name="conj_demandes" value="1"/></td>' . "\n";

    echo '<td class="rupt_table"><input type="submit" value="' . my_html($LG_Tree_Show_Desc) . '"/>';
    echo '</td>' . "\n";
    echo '</tr></table>';
    echo '<input type="hidden" name="memo_etat"/>';
    echo '</form>' . "\n";
}

$fin_arbres_asc = '><img src="' . $root . '/assets/img/' . $Icones['arbre_asc'] . '" title="' . $LG_assc_tree . '" alt="' . $LG_assc_tree . '"/></a>';
$fin_arbres_desc = '><img src="' . $root . '/assets/img/' . $Icones['arbre_desc'] . '" alt="' . $LG_desc_tree . '" title="' . $LG_desc_tree . '"/></a> ';

do {
    Affiche_Pers($Numero);
    $Numero = floor($Numero / 2);
} while ($Numero >= 1);

if (! $texte) {
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