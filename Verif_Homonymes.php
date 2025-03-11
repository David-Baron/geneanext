<?php
//=====================================================================
// Vérification des homonymes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Namesake_Cheking']; // Titre pour META
$niv_requis = 'C';
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

$D_Nais = false;
if (isset($_POST['D_Nais'])) $D_Nais = true;
if (isset($_GET['D_Nais'])) $D_Nais = true;
// Critère date de décès
$D_Dec = false;
if (isset($_POST['D_Dec'])) $D_Dec = true;
if (isset($_GET['D_Dec'])) $D_Dec = true;

$compl_texte = '';
if ($D_Nais) $compl_texte .= '&D_Nais=O';
if ($D_Dec) $compl_texte .= '&D_Dec=O';

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();

$compl = Ajoute_Page_Info(600, 300);
if ($_SESSION['estGestionnaire']) {
    $compl .= '<a href="' . $root . '/verif_homonymes.php?texte=O' . $compl_texte . '">' . Affiche_Icone('text', $LG_printable_format) . '</a>' . "\n";
}

# include(__DIR__ . '/assets/js/Verif_Homonymes.js');

if (!$texte) {
    Insere_Haut(my_html($titre), $compl, 'Verif_Homonymes', '');
} else {
    Insere_Haut_texte(my_html($titre));
    echo '<br />';
}

if (!$texte) {
    echo '<form id="parliste" action="' . my_self() . '" method="post">' . "\n";
    echo '<table border="0" width="75%" align="center">' . "\n";
    echo '<tr align="center">';
    echo '<td class="rupt_table">';
    echo my_html(LG_NAMESAKE_CRITERIA);
    echo '&nbsp;<input type="checkbox"';
    if ($D_Nais) echo ' checked="checked"';
    echo ' name="D_Nais" value="1"/>' . my_html(LG_NAMESAKE_BIRTH);
    echo '&nbsp;<input type="checkbox"';
    if ($D_Dec) echo ' checked="checked"';
    echo ' name="D_Dec" value="1"/>' . my_html(LG_NAMESAKE_DEATH);
    echo '</td>' . "\n";
    echo '<td class="rupt_table"><input type="submit" value="' . my_html($LG_modify_list) . '"/></td>' . "\n";
    echo '</tr></table>';
    echo '<input type="hidden" id="memo_etat" name="memo_etat"/>';
    echo '</form>' . "\n";
}

// Constitution de la requête d'extraction
$critere = '';
if ($D_Nais) $critere .= ', Ne_le';
if ($D_Dec) $critere .= ', Decede_Le';

$gr_or = 'by nom, prenoms' . $critere;

$n_personnes = nom_table('personnes');

$sql = 'select count(*), nom, prenoms, idNomFam' . $critere
    . ' from ' . $n_personnes
    . ' group ' . $gr_or
    . ' having count(*) > 1'
    . ' order ' . $gr_or;

$echo_modif = Affiche_Icone('fiche_edition', my_html($LG_modify)) . '</a>';
$info = $LG_Menu_Title['Compare_Persons'];
$icone_compare = '<input type="image" src="' . $chemin_images_icones . $Icones['2personnes'] . '" alt="' . $info . '" ' .
    'title="' . $info . '" onclick="return controle(this.form.id);"/>' . "\n";

$nb = 0;
$x_ne = '&deg; ';
$x_Ref = my_html($LG_Reference);

if ($res = lect_sql($sql)) {
    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
        if (!$texte) echo '<form action="' . $root . '/fiche_homonymes.php" id="frm_' . $nb . '">';
        echo '<fieldset>';
        $nom = $enreg[1];
        $prenom = $enreg[2];
        $nb_homonymes = $enreg[0];
        echo '<legend>';
        if (!$texte) {
            echo '<a href="' . $root . '/liste_pers2.php?Type_Liste=P&amp;idNom=' . $enreg[3] . '&amp;Nom=' . $nom . '">' . my_html($nom) . '</a>';
        } else {
            echo my_html($nom) . '';
        }
        //echo '&nbsp;'.my_html($prenom).'&nbsp;('.$nb_homonymes.' homonyme'.pluriel($nb_homonymes).')'."\n";
        echo '&nbsp;' . $prenom . "\n";
        $nb++;
        echo '</legend>' . "\n";
        $sql2 = 'select Ne_le, Decede_Le, Reference from ' . $n_personnes .
            ' where nom = \'' . addslashes($nom) . '\'' .
            ' and prenoms = \'' . $prenom . '\'' .
            ' order by  Ne_le, Decede_Le ';
        $num_pers = 0;
        if ($res2 = lect_sql($sql2)) {
            echo '<table width="100%">' . "\n";
            while ($enreg2 = $res2->fetch(PDO::FETCH_ASSOC)) {
                $style = 'liste2';
                if (pair($num_pers++)) $style = 'liste';
                $classe = '';
                if (!$texte) $classe = 'class="' . $style . '"';
                echo '<tr>';
                echo '<td ' . $classe . '>';
                $decede = $enreg2['Decede_Le'];
                $ne = $enreg2['Ne_le'];
                $ref = $enreg2['Reference'];
                if (!$texte) {
                    echo $x_Ref . ' : ' . '<a ' . Ins_Ref_Pers($ref) . '>' . $ref . '</a>' . "\n";
                } else {
                    echo '&nbsp;&nbsp;&nbsp;' . $x_Ref . '&nbsp;' . $ref . "\n";
                }
                if ($ne != '') echo ',' . $x_ne . Etend_date($ne);
                if ($decede != '') echo ', + ' . Etend_date($decede);
                if (!$texte) {
                    echo '&nbsp;<a ' . Ins_Edt_Pers($ref) . '>' . $echo_modif;
                }
                echo '</td>' . "\n";
                if (!$texte) {
                    echo '<td width="10%" align="center" ' . $classe . '>';
                    echo '<input type="radio" name="ref1" value="' . $ref . '" title="' . LG_NAMESAKE_PERS1 . '"/>';
                    echo '<input type="radio" name="ref2" value="' . $ref . '" title="' . LG_NAMESAKE_PERS2 . '"/>';
                    echo '</td>';
                }
                if ($num_pers == 1) {
                    if (!$texte) {
                        echo '<td width="10%" align="center" valign="middle" rowspan="' . $nb_homonymes . '">' . $icone_compare . '</td>';
                    }
                }
                echo '</tr>' . "\n";
            }
            $res2->closeCursor();
        }
        echo '</table>';
        echo '</fieldset> ' . "\n";
        if (!$texte) echo '</form>';
    }
    $res->closeCursor();
}

if ($nb == 0) echo '<br />' . my_html(LG_NAMESAKE_ZERO);

if (! $texte) Insere_Bas($compl);

?>
<script type="text/javascript">
    // Contrôle au clic sur le bouton pour afficher 2 personnes
    function controle(formulaire) {
        nbRef1 = 0;
        nbRef2 = 0;
        reference1 = 0;
        reference2 = 0;
        refForm = document.forms[formulaire];
        for (i = 0; i < refForm.ref1.length; i++) {
            if (refForm.ref1[i].checked) {
                nbRef1++;
                reference1 = i;
            }
            if (refForm.ref2[i].checked) {
                nbRef2++;
                reference2 = i;
            }
        }
        if (nbRef1 != 1 || nbRef2 != 1) {
            alert('<?php echo LG_NAMESAKE_CHOOSE_ALERT; ?>');
            return false;
        }
        if (reference1 == reference2) {
            alert('<?php echo LG_NAMESAKE_CHOOSE_DIFF; ?>');
            return false;
        }

        return true;
        //	submit();
    }
</script>
</body>

</html>