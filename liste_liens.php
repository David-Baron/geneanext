<?php

//=====================================================================
// Liste des liens
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$tab_variables = array('ok', 'annuler', 'supprimer', 'S_Sup', 'idLien');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

$acces = 'M';
$titre = $LG_Menu_Title['Links'];            // Titre pour META
$x = Lit_Env();

$lib_sup = my_html(LG_LINKS_DEL);

$niv_requis = 'I';
require(__DIR__ . '/app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 150);

// Le gestionnaire a la colonne de modification en plus
$nb_Cols = 3;
if ($est_gestionnaire) $nb_Cols++;

Insere_Haut($titre, $compl, 'Liste_Liens', '');

$Anc_Type = '';

//  ===== Appliquer les corrections demandees
if ($bt_Sup) {
    Ecrit_Entete_Page($titre, $contenu, $mots);
    $refs = '';
    if ($S_Sup <> '') {
        foreach ($S_Sup as $key => $value) {
            //echo '1-'.$key.' / '.$value.'<br />';
            //echo '2-'.$idLien[$key].'<br />';
            if ($refs != '') $refs .= ',';
            $refs .= $idLien[$key];
        }
    }
    if ($refs != '') {
        $enr_mod = 0;
        $req = 'delete from ' . nom_table('commentaires') . ' where Reference_Objet in (' . $refs . ") and Type_Objet = 'L'";
        $res = maj_sql($req);
        $req = 'delete from ' . nom_table('liens') . ' where Ref_lien in (' . $refs . ')';
        $res = maj_sql($req);
        $plu = pluriel($enr_mod);
        echo $enr_mod . ' ' . my_html(LG_LINKS_DEL_REP1 . $plu) . ' ' . my_html(LG_LINKS_DEL_REP2 . $plu) . '<br />';
    }
}

# include(__DIR__ . '/assets/js/Liste_Liens.js');
$suf = 0; // Suffixe pour les div

// Possibilité d'insérer un lien
if ($est_gestionnaire) {
    echo my_html(LG_LINKS_ADD) . ' ' . Affiche_Icone_Lien('href="' . $root . '/edition_lien.php?Ref=-1"', 'ajouter', $LG_add) . '<br />' . "\n";
    echo my_html(LG_LINKS_IMPORT) . ' ' . Affiche_Icone_Lien('href="' . $root . '/import_csv_liens.php"', 'ajout_multiple', $LG_csv_import) . '<br /><br />' . "\n";
    echo '<form action="' . my_self() . '" id="saisie" method="post">';
    echo '<input name="compteur" type="' . $hidden . '" value="0"/>';
}

$num_lig = 0;
$diff_int = ''; // Préparation sur la clause de diffusabilité
//$est_privilegie = false;
if (!$est_privilegie) $diff_int = ' where Diff_Internet = 1 ';

$sql = 'select Ref_lien, type_lien, description, URL, image, Diff_Internet '
    . 'from ' . nom_table('liens')
    . $diff_int
    . ' order by type_lien, description';
$res = lect_sql($sql);

while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    $id_lien = $row['Ref_lien'];
    $num_lig++;
    $suf++;
    $Nouv_Type = my_html($row['type_lien']);
    // Rupture sur le type de lien
    if ($Nouv_Type != $Anc_Type) {
        if ($Anc_Type != '') {
            echo "</table>\n";
            echo '</div>' . "\n";
            echo "<br />\n";
        }
        echo '<table width="95%" class="classic" align="center">' . "\n";
        echo '<tr align="center" ><td class="rupt_table" colspan="' . $nb_Cols . '">' . $Nouv_Type;
        echo '&nbsp;&nbsp;<img id="ajout' . $suf . '" src="' . $root . '/assets/img/' . $Icones['oeil'] . '" alt="' . LG_LINKS_EYE . '" ' .
            Survole_Clic_Div('div' . $suf) . '/>' . "\n";
        // Possibilité d'extraire les liens du type de lien. Page interdite sur les gratuits non Premium
        if (($est_gestionnaire) and ((!$SiteGratuit) or ($Premium))) {
            echo '&nbsp;' . Affiche_Icone_Lien('href="' . $root . '/export_liens.php?Categ=' . $row['type_lien'] . '"', 'exp_tab', my_html($LG_csv_export));
        }
        echo '</td></tr></table>' . "\n";
        echo '<div id="div' . $suf . '">' . "\n";
        echo '<table width="95%" class="classic" align="center">' . "\n";
        $Anc_Type = $Nouv_Type;
        $num_lig_coul = 0;
    }
    $image = $row['image'];
    $style = 'liste2';
    if (pair($num_lig_coul++)) $style = 'liste';
    echo '<tr class="' . $style . '">' . "\n";
    echo '<td width="25%"><a href="' . $root . '/fiche_lien.php?Ref=' . $id_lien . '">' . my_html($row['description']) . "</a></td>\n";
    echo "<td";
    if ($image == '') {
        echo " colspan=\"2\"";
    }
    echo '><a href="' . $row['URL'] . '" target="_blank">' . my_html($row['URL']) . '</a></td>' . "\n";
    if ($image != "") {
        $image = $chemin_images_util . $image;
        echo '<td width="35%">';
        Aff_Img_Redim_Lien($image, 200, 200, "id" . $suf);
        echo '</td>' . "\n";
    }
    // Icone de modification et checkbox de suppression pour le gestionnaire
    if ($est_gestionnaire) {

        echo '<td align="center" width="10%"><a href="' . $root . '/edition_lien.php?Ref=' . $row['Ref_lien'] . '"><img src="' . $chemin_images_icones . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"/></a>' . "\n";
        echo '&nbsp;<input type="checkbox" name="S_Sup[' . $num_lig . ']" id="S_Sup_' . $num_lig . '" ';
        echo 'onclick="traite(' . $num_lig . ')"';
        echo "/>\n";
        echo  '<input type="' . $hidden . '" name="idLien[' . $num_lig . ']" value="' . $id_lien . '"/>';
        if ($row['Diff_Internet'] == 1) echo '<img src="' . $root . '/assets/img/' . $Icones['internet_oui'] . '" alt="' . $LG_show_on_internet . '" title="' . $LG_show_on_internet . '"> ';
        else                            echo '<img src="' . $root . '/assets/img/' . $Icones['internet_non'] . '" alt="' . $LG_noshow_on_internet . '" title="' . $LG_noshow_on_internet . '"> ';
        echo '</td>';
    }
    echo "  </tr>\n";
}
$res->closeCursor();

// Fermeture table s'il y en au eu au moins 1
if ($Anc_Type != '') {
    echo "</table>";
    echo '</div>' . "\n";
}

echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . LG_TIP . '" title="' . LG_TIP . '">' . LG_CH_IMAGE_MAGNIFY . '.<br />';

if ($est_gestionnaire) {
    echo '<div id="boutons">';
    bt_ok_an_sup('', '', $lib_sup, LG_LINKS_THIS, false, false);
    echo '</div>';
    echo '</form>';
    /* Finalement, choix d'afficher le bouton, sinon la signification des checkboxes peut être obscure :-)
	echo '
	<script type="text/javascript">
	<!--
	cache_div("boutons");
	-->
	</script>';
	*/
?>
    <script type="text/javascript">
        // Ajoute une ligne à la table des évènements
        function traite(num_lig) {
            //var zone = document.getElementsByName('S_Sup['+ligne+']');
            if (document.getElementById('S_Sup_' + num_lig).checked) {
                document.forms.saisie.compteur.value++;
            } else {
                document.forms.saisie.compteur.value--;
            }
            if (document.forms.saisie.compteur.value > 0) {
                montre_div("boutons");
            } else {
                cache_div("boutons");
            }
        }
    </script>
<?php
}

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>