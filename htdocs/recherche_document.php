<?php

//=====================================================================
// Recherche dans les documents, quel que soit l'objet associé
// Sortie possible :
//   - à l'écran avec les liens vers les personnes
//   - au format texte pour impression
//   - au format csv pour un import dans un tableur
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('P')) {
    header('Location: ' . $root . '/');
    exit();
}

$tab_variables = array(
    'ok',
    'annuler',
    'Recherche',
    'Nature',
    'Sortie',
    'Type_Doc',
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
$acces = 'L';
$titre = $LG_Menu_Title['Find_Doc'];        // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Verrouillage de la gestion des documents sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

$compl = '';

    if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);

    if ($Sortie == 't') {
        echo '</head>' . "\n";
        echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
        echo '<table cellpadding="0" width="100%">' . "\n";
        echo '<tr>' . "\n";
        echo '<td align="center"><b>' . StripSlashes($titre) . '</b></td>' . "\n";
        echo '</tr>' . "\n";
        echo '</table>' . "\n";
    } else {
        $compl = Ajoute_Page_Info(600, 260);
        Insere_Haut($titre, $compl, 'Recherche_Document', '');
    }

    //Demande de recherche
    if ($bt_OK) {

        # require(__DIR__ . '/app/ressources/commun_rech_com_util_docs.php');

        $Recherche = Secur_Variable_Post($Recherche, 80, 'S');
        $Nature    = Secur_Variable_Post($Nature, 3, 'S');
        $Sortie    = Secur_Variable_Post($Sortie, 1, 'S');

        echo 'Recherche <br />';
        $criteres = '';
        if ($Nature != '-') {
            echo '- ' . my_html(LG_DOC_SCH_ON) . ' ' . $Natures_Docs[$Nature] . '<br />';
            $criteres .= ' and Nature_Document = "' . $Nature . '" ';
        }
        if ($Type_Doc != '-') {
            echo '- ' . my_html(LG_DOC_SCH_TYPE) . ' ' . $Type_Doc . '<br />';
            $criteres .= 'and d.Id_Type_Document = ' . $Type_Doc . ' ';
        }
        if ($Recherche != '') {
            echo '- ' . my_html(LG_DOC_SCH_TITLES) . ' ' . $Recherche . '<br />';
            $criteres .= 'and  upper(Titre) like "%' . trim(strtoupper($Recherche)) . '%"';
        }

        $sql = 'select Id_Document, Nature_Document, Titre, Nom_Fichier, ' .
            'Diff_Internet, Date_Creation, Date_Modification, Libelle_Type ' .
            'from ' . nom_table('documents') . ' d,' . nom_table('types_doc') . ' t ' .
            ' where d.Id_Type_Document = t.Id_Type_Document ' .
            $criteres .
            ' order by Titre';

        $res = lect_sql($sql);
        $nb = $res->RowCount();
        //$plu = pluriel($nb);
        echo $nb . ' ' . my_html(LG_DOC_SCH_FOUND) . '<br /><br />';
        //$num_fields = $res->field_count;

        $num_lig = 0;
        $nom_fic_rech = 'recherche_documents.csv';

        while ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
            $num_lig++;
            $refDoc = $enreg['Id_Document'];
            $Titre = $enreg['Titre'];
            $Nom_Fichier = $enreg['Nom_Fichier'];

            switch ($Sortie) {
                case 'e':
                    echo '<a href="' . $root . '/fiche_document?Reference=' . $refDoc . '">' . my_html($Titre) . '</a>';
                    echo ' (' . $Natures_Docs[$enreg['Nature_Document']] . ")\n";
                    echo ' <a href="' . $root . '/edition_document?Reference=' . $refDoc . '"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a>';
                    $le_type = Get_Type_Mime($enreg['Nature_Document']);
                    $chemin_docu = get_chemin_docu($enreg['Nature_Document']);
                    echo '   ' . Affiche_Icone_Lien('href="' . $chemin_docu . $Nom_Fichier . '" type="' . $le_type . '"', 'oeil', LG_DOC_SCH_SEE, 'n');
                    echo '<br />' . "\n";
                    break;
                case 't':
                    echo my_html($Titre);
                    echo ' (' . $Natures_Docs[$enreg['Nature_Document']] . ")\n";
                    echo '<br />' . "\n";
                    break;
                case 'c':
                    if ($num_lig == 1) {
                        $nom_fic = $chemin_exports . $nom_fic_rech;
                        $fp = fopen($nom_fic, 'w+');
                        $ligne = LG_DOC_SCH_HEADER_CSV;
                        fputs($fp, $ligne);
                    }
                    $ligne = $Natures_Docs[$enreg['Nature_Document']] . ';' .
                        $Titre . ';' .
                        $Nom_Fichier . ';' .
                        $enreg['Diff_Internet'] . ';' .
                        DateTime_Fr($enreg['Date_Creation']) . ';' .
                        DateTime_Fr($enreg['Date_Modification']) . ';' .
                        $enreg['Libelle_Type'] . ';';
                    fputs($fp, $ligne);
                    break;
            }
        }
        if (($Sortie == 'c') and ($num_lig)) {
            fclose($fp);
            echo '<br />' . my_html($LG_csv_available_in) . ' <a href="' . $chemin_exports . $nom_fic_rech . '" target="_blank">' . $nom_fic_rech . '</a><br />' . "\n";
        }

        // Nouvelle recherche
        if ($Sortie != 't') {
            echo '<form id="nouvelle" method="post">' . "\n";
            echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
            echo '<br />';
            echo '<div class="buttons">';
            echo '<button type="submit" class="positive">' .
                '<img src="' . $root . '/assets/img/' . $Icones['chercher'] . '" alt=""/>' . LG_DOC_SCH_NEW . '</button>';
            echo '</div>';
            echo '</form>' . "\n";
        }
    }

    // Première entrée : affichage pour saisie
    if ((!$bt_OK) && (!$bt_An)) {

        echo '<form id="saisie" method="post">' . "\n";
        echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
        echo '<table width="80%" class="table_form">' . "\n";
        echo '<tr><td colspan="2">&nbsp;</td></tr>';
        echo '<tr><td class="label" width="30%">' . ucfirst(LG_DOC_SCH_LB_TITLE) . '</td><td class="value">';
        echo '<input type="text" name="Recherche" size="80"/></td></tr>' . "\n";
        echo '<tr><td class="label" width="30%">' . ucfirst(LG_DOC_SCH_LB_NATURE) . '</td><td class="value">';
        echo '<select name="Nature" size="1">';
        echo '<option value="-">-- Toutes --</option>' . "\n";
        foreach ($Natures_Docs as $key => $value) {
            echo '<option value="' . $key . '">' . $value . '</option>' . "\n";
        }
        echo '</select>' . "\n";
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="30%">' . ucfirst(LG_DOC_SCH_LB_TYPE) . '</td><td class="value">';
        $sql = 'select Id_Type_Document, Libelle_Type from ' . nom_table('types_doc') . ' order by Libelle_Type';
        echo '<select name="Type_Doc" size="1">';
        echo '<option value="-">-- ' . my_html($LG_All) . ' --</option>' . "\n";
        if ($res = lect_sql($sql)) {
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                echo '<option value="' . $row[0] . '">' . my_html($row[1]) . '</option>' . "\n";
            }
        }
        $res->closeCursor();
        echo '</select>' . "\n";
        echo '</td></tr>' . "\n";
        echo '</td></tr>' . "\n";
        echo '<tr><td class="label" width="30%">' . ucfirst($LG_Ch_Output_Format) . '</td><td class="value">';
        echo '<input type="radio" id="Sortie_e" name="Sortie" value="e" checked/><label for="Sortie_e">' . $LG_Ch_Output_Screen . '</label> ';
        echo '<input type="radio" id="Sortie_t" name="Sortie" value="t"/><label for="Sortie_t">' . $LG_Ch_Output_Text . '</label> ';
        echo '<input id="Sortie_c" type="radio" name="Sortie" value="c"/><label for="Sortie_c">' . $LG_Ch_Output_CSV . '</label>';
        echo '</td></tr>' . "\n";

        echo '<tr><td colspan="2">&nbsp;</td></tr>';
        bt_ok_an_sup($lib_Rechercher, $lib_Annuler, '', '');
        echo '<tr><td colspan="2">&nbsp;</td></tr>';

        echo '</table>' . "\n";
        echo '</form>' . "\n";
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