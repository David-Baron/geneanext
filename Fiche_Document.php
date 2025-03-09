<?php
//=====================================================================
// FIche d'un document
//=====================================================================

// Gestion standard des pages
session_start();
include('fonctions.php');
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
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

    // 2 solutions en cas d'absence :
    // - l'utilisateur a saisi un code absent dans l'URL ; le code ne doit pas être saisi dans l'URL, donc tant pis pour lui...
    // - on revient de la mpage de modification et on a demandé la suppression ; donc on renvoye sur la page précédente, à priori la liste
    if ((!$enreg_sel) or ($Reference == 0)) Retour_Ar();

    $enreg = $enreg_sel;
    $enreg2 = $enreg;

    $compl = Ajoute_Page_Info(600, 150);
    if ($est_gestionnaire) {
        $compl .= Affiche_Icone_Lien('href="Edition_Document.php?Reference=' . $Reference . '"', 'fiche_edition', my_html($LG_Menu_Title['Document_Edit'])) . '&nbsp;';
    }

    //
    //  ========== Programme principal ==========
    //
    if ($enreg_sel) {
        $nomFic = $enreg['Nom_Fichier'];

        Insere_Haut(my_html($titre), $compl, 'Fiche_Document', '');

        if (($enreg['Diff_Internet'] == 'O') or ($est_gestionnaire)) {

            echo '<br />';
            $larg_titre = 25;
            echo '<table width="70%" class="table_form">' . "\n";

            colonne_titre_tab($LG_Docs_Title);
            echo my_html($enreg['Titre']) . '</td></tr>' . "\n";

            colonne_titre_tab($LG_Docs_Nature);
            $natureDoc  = $enreg['Nature_Document'];
            echo $Natures_Docs[$natureDoc] . '</td></tr>' . "\n";

            colonne_titre_tab($LG_Docs_File);
            echo $nomFic . '</td></tr>' . "\n";

            colonne_titre_tab($LG_Docs_Doc_Type);
            echo '<a href="Fiche_Type_Document.php?code=' . $enreg['Id_Type_Document'] . '">'
                . my_html($enreg['Libelle_Type'])
                . '</a></td></tr>' . "\n";

            colonne_titre_tab($LG_show_on_internet);
            $diff = $enreg['Diff_Internet'];
            if ($diff == 'O') echo my_html($LG_Yes);
            else              echo my_html($LG_No);
            echo '</td></tr>' . "\n";

            echo '</table>';

            $le_type = Get_Type_Mime($natureDoc);
            $chemin_docu = get_chemin_docu($enreg['Nature_Document']);

            if ($natureDoc == 'IMG') {
                echo '<br />';
                Aff_Img_Redim_Lien($chemin_docu . $nomFic, 150, 150);
            } else
                echo '<br />' . my_html($LG_Docs_Doc_Show) . ' :&nbsp;' . Affiche_Icone_Lien('href="' . $chemin_docu . $enreg['Nom_Fichier'] . '" type="' . $le_type . '"', 'oeil', $LG_Docs_Doc_Show, 'n');

            // Lien vers les utilisations du document s'il en existe
            if ($est_gestionnaire) {
                $req = 'SELECT 1 FROM ' . nom_table('concerne_doc') . ' WHERE id_document = ' . $Reference . ' limit 1';
                $result = lect_sql($req);
                if ($result->rowCount() > 0) {
                    echo '<br /><a href="Utilisations_Document.php?Doc=' . $Reference . '">' . my_html($LG_Menu_Title['Document_Utils']) . '</a>';
                }
            }
        } else aff_erreur($LG_Data_noavailable_profile);

        echo '<br />' . "\n";

        // Formulaire pour le bouton retour
        Bouton_Retour($lib_Retour, '?' . $_SERVER['QUERY_STRING']);
        Insere_Bas($compl);
    }
}
?>
</body>

</html>