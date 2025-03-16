<?php
//=====================================================================
// FIche d'un document
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Document'];    // Titre pour META

// Récupération des variables de l'affichage précédent
$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de la variable passée dans l'URL : référence de l'évènement
$Reference = Recup_Variable('Reference', 'N');

$req_sel = 'SELECT * FROM ' . nom_table('documents') . ' d, ' . nom_table('types_doc') . ' t' .
    ' WHERE id_document = ' . $Reference .
    ' AND d.Id_Type_Document = t.Id_Type_Document limit 1';

$x = Lit_Env();                    // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// 2 solutions en cas d'absence :
// - l'utilisateur a saisi un code absent dans l'URL ; le code ne doit pas être saisi dans l'URL, donc tant pis pour lui...
// - on revient de la mpage de modification et on a demandé la suppression ; donc on renvoye sur la page précédente, à priori la liste
if ((!$enreg_sel) or ($Reference == 0)) Retour_Ar();

$enreg = $enreg_sel;

$compl = Ajoute_Page_Info(600, 150);
if ($est_gestionnaire) {
    $compl .= Affiche_Icone_Lien('href="' . $root . '/edition_document.php?Reference=' . $Reference . '"', 'fiche_edition', my_html($LG_Menu_Title['Document_Edit'])) . '&nbsp;';
}

if ($enreg_sel) {
    $nomFic = $enreg['Nom_Fichier'];

    Insere_Haut(my_html($titre), $compl, 'Fiche_Document', '');

    if (($enreg['Diff_Internet'] == 'O') or ($est_gestionnaire)) {

        echo '<br />';
        echo '<table width="70%" class="table_form">' . "\n";
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Docs_Title) . '</td><td class="value">';
        echo my_html($enreg['Titre']) . '</td></tr>' . "\n";
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Docs_Nature) . '</td><td class="value">';
        echo $Natures_Docs[$enreg['Nature_Document']] . '</td></tr>' . "\n";
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Docs_File) . '</td><td class="value">';
        echo $nomFic . '</td></tr>' . "\n";
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_Docs_Doc_Type) . '</td><td class="value">';
        echo '<a href="' . $root . '/fiche_type_document.php?code=' . $enreg['Id_Type_Document'] . '">'
            . my_html($enreg['Libelle_Type'])
            . '</a></td></tr>' . "\n";
        echo '<tr><td class="label" width="25%">' . ucfirst($LG_show_on_internet) . '</td><td class="value">';
        $diff = $enreg['Diff_Internet'];
        if ($diff == 'O') echo $LG_Yes;
        else              echo $LG_No;
        echo '</td></tr>' . "\n";
        echo '</table>';

        $le_type = Get_Type_Mime($enreg['Nature_Document']);
        $chemin_docu = get_chemin_docu($enreg['Nature_Document']);

        if ($enreg['Nature_Document'] == 'IMG') {
            echo '<br />';
            Aff_Img_Redim_Lien($chemin_docu . $nomFic, 150, 150);
        } else
            echo '<br />' . my_html($LG_Docs_Doc_Show) . ' :&nbsp;' . Affiche_Icone_Lien('href="' . $chemin_docu . $enreg['Nom_Fichier'] . '" type="' . $le_type . '"', 'oeil', $LG_Docs_Doc_Show, 'n');

        // Lien vers les utilisations du document s'il en existe
        if ($est_gestionnaire) {
            $req = 'SELECT 1 FROM ' . nom_table('concerne_doc') . ' WHERE id_document = ' . $Reference . ' limit 1';
            $result = lect_sql($req);
            if ($result->rowCount() > 0) {
                echo '<br /><a href="' . $root . '/utilisations_document.php?Doc=' . $Reference . '">' . $LG_Menu_Title['Document_Utils'] . '</a>';
            }
        }
    } else {
        echo '<center><font color="red"><br><br><br><h2>' . $LG_Data_noavailable_profile . '</h2></font></center>';
    }

    echo '<br />' . "\n";
    Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
} ?>
</body>

</html>