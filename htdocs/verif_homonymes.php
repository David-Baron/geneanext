<?php
/*
 * Vérification des homonymes
 */

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('C')) {
    header('Location: ' . $root . '/');
    exit();
}

$titre = $LG_Menu_Title['Namesake_Cheking']; // Titre pour META

$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

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
if (IS_GRANTED('G')) {
    $compl .= '<a href="' . $root . '/verif_homonymes?texte=O' . $compl_texte . '"><img src="' . $root . '/assets/img/' . $Icones['text'] . '" alt="' . $LG_printable_format . '" title="' . $LG_printable_format . '"></a>' . "\n";
}

# include(__DIR__ . '/assets/js/Verif_Homonymes.js');

if (!$texte) {
    Insere_Haut(my_html($titre), $compl, 'Verif_Homonymes', '');
} else {
    echo '</head>' . "\n";
    echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td align="center"><b>' . StripSlashes($titre) . '</b></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '<br />';
}

if (!$texte) {
    echo '<form id="parliste" method="post">' . "\n";
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

$nb = 0;
$x_ne = '&deg; ';
$x_Ref = my_html($LG_Reference);

if ($res = lect_sql($sql)) {
    while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
        if (!$texte) echo '<form action="' . $root . '/fiche_homonymes" id="frm_' . $nb . '">';
        echo '<fieldset>';
        $nom = $enreg[1];
        $prenom = $enreg[2];
        $nb_homonymes = $enreg[0];
        echo '<legend>';
        if (!$texte) {
            echo '<a href="' . $root . '/liste_pers2?Type_Liste=P&amp;idNom=' . $enreg[3] . '&amp;Nom=' . $nom . '">' . my_html($nom) . '</a>';
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
                if (!$texte) {
                    echo $x_Ref . ' : ' . '<a href="' . $root . '/fiche_fam_pers?Refer=' . $enreg2['Reference'] . '">' . $enreg2['Reference'] . '</a>' . "\n";
                } else {
                    echo '&nbsp;&nbsp;&nbsp;' . $x_Ref . '&nbsp;' . $enreg2['Reference'] . "\n";
                }
                if ($enreg2['Ne_le'] != '') echo ',' . $x_ne . Etend_date($enreg2['Ne_le']);
                if ($enreg2['Decede_Le'] != '') echo ', + ' . Etend_date($enreg2['Decede_Le']);
                if (!$texte) {
                    echo '&nbsp;<a href="' . $root . '/edition_personne?Refer=' . $enreg2['Reference'] . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
                }
                echo '</td>' . "\n";
                if (!$texte) {
                    echo '<td width="10%" align="center" ' . $classe . '>';
                    echo '<input type="radio" name="ref1" value="' . $enreg2['Reference'] . '" title="' . LG_NAMESAKE_PERS1 . '"/>';
                    echo '<input type="radio" name="ref2" value="' . $enreg2['Reference'] . '" title="' . LG_NAMESAKE_PERS2 . '"/>';
                    echo '</td>';
                }
                if ($num_pers == 1) {
                    if (!$texte) {
                        echo '<td width="10%" align="center" valign="middle" rowspan="' . $nb_homonymes . '"><input type="image" src="' . $root . '/assets/img/' . $Icones['2personnes'] . '" alt="' . $LG_Menu_Title['Compare_Persons'] . '" ' .
                            'title="' . $LG_Menu_Title['Compare_Persons'] . '" onclick="return controle(this.form.id);"/></td>';
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