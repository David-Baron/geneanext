<?php

//=====================================================================
// Edition d'une image
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'nom_du_fichier',
    'Remplacer',
    'Horigine'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok             = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler        = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$Remplacer      = Secur_Variable_Post($Remplacer, 1, 'S');
//$nom_du_fichier = Secur_Variable_Post($nom_du_fichier,80,'S');
$Horigine       = Secur_Variable_Post($Horigine, 100, 'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Import_Docs'];                // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$n_images = nom_table('images');
$n_documents = nom_table('documents');

$compl = Ajoute_Page_Info(600, 220);
Insere_Haut($titre, $compl, 'Import_Docs', '');

//Demande de mise à jour
if ($bt_OK) {

    Ecrit_Entete_Page($titre, $contenu, $mots);
    include(__DIR__ . '/assets/js/monSSG.js');

    $nb_docs = count($_FILES["nom_du_fichier"]["name"]);

    for ($nb = 0; $nb < $nb_docs; $nb++) {
        $nom_fic = $_FILES["nom_du_fichier"]["name"][$nb];

        if ($nom_fic != '') {
            // Détermination du type de fichier
            $type_doc = get_file_type($nom_fic, $_FILES['nom_du_fichier']['type'][$nb]);

            $msg_import = LG_IMP_DOCS_IMPORT . $nom_fic . ', ';
            switch ($type_doc) {
                case 'IMG':
                    $msg_import .= LG_IMP_DOCS_IMG;
                    break;
                case 'HTM':
                    $msg_import .= LG_IMP_DOCS_HTML;
                    break;
                case 'PDF':
                    $msg_import .= LG_IMP_DOCS_PDF;
                    break;
            }
            echo my_html($msg_import . ', ' . $_FILES["nom_du_fichier"]["size"][$nb] . LG_IMP_DOCS_SIZE) . LG_SEMIC;

            // Traitement du fichier

            // Nettoyage du nom de fichier
            $nom_fic = nettoye_nom_fic($nom_fic);

            // Cas des images
            if ($type_doc == 'IMG') {

                $erreur = '';

                // Distinguer Image vs. Document de type Image	

                // Dans un premier temps, on contrôle que le nom du fichier est bien attendu
                $sql = 'select 1 from ' . $n_images . ' where nom = "' . $nom_fic . '" limit 1';
                $res = lect_sql($sql);
                $utilise_img = ($res->rowCount());

                if (!$utilise_img) {
                    $sql = 'SELECT 1 FROM ' . $n_documents . ' d,' . nom_table('concerne_doc') . ' c' .
                        ' WHERE d.id_document = c.id_document ' .
                        ' AND d.Nom_Fichier = "' . $nom_fic . '" limit 1';
                    $res = lect_sql($sql);
                    $utilise_doc = ($res->rowCount());
                }

                if (!$utilise_img and !$utilise_doc) {
                    $erreur = LG_IMP_DOCS_DOC_NOT_FORESSEN1 . $nom_fic . LG_IMP_DOCS_DOC_NOT_FORESSEN2;
                }

                // On controle le remplacement
                // Si le fichier existe déjà et que l'utilisateur n'a pas demandé le remplacement, on génère un warning
                if ($erreur == '') {
                    if ($utilise_img) $chemin = $chemin_images_util;
                    else              $chemin = $chemin_docs_IMG;
                    if ($Remplacer == 'N') {
                        if (file_exists($chemin . $nom_fic))
                            $erreur = LG_IMP_DOCS_FILE_EXISTS1 . $nom_fic . LG_IMP_DOCS_FILE_EXISTS2;
                    }
                }

                // Contrôle des caractéristiques de l'image
                if ($erreur == '') {
                    $erreur = Controle_Charg_Image($nb);
                }

                // Erreur constatée sur le chargement
                if ($erreur != '') {
                    echo '&nbsp;&nbsp;';
                    Affiche_Warning($erreur);
                }
                // Sinon on peut télécharger
                else {
                    // Téléchargement du fichier après contrôle
                    if (!ctrl_fichier_ko($nb)) {
                        move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'][$nb], $chemin . $nom_fic);
                        // la zone nom_du_fichier n'est pas alimentée...
                        $nom_du_fichier = $nom_fic;
                        // On chmod le fichier si on n'est pas sous Windows
                        if (substr(php_uname(), 0, 7) != 'Windows') chmod($chemin . $nom_fic, 0644);
                        echo 'ok<br>';
                    } else $erreur = '-';
                }
            }

            // Cas des documents HTML ou des PDF
            $type_doc_htm = false;
            $type_doc_pdf = false;
            if ($type_doc == 'HTM') $type_doc_htm = true;
            if ($type_doc == 'PDF') $type_doc_pdf = true;

            if ($type_doc_htm or $type_doc_pdf) {

                if ($type_doc_htm) $chemin_interne = $chemin_docs_HTM;
                if ($type_doc_pdf) $chemin_interne = $chemin_docs_PDF;

                $erreur = '';

                // Dans un premier temps, on contrôle que le nom du fichier est bien attendu
                $sql = 'SELECT 1 FROM ' . $n_documents . ' d,' . nom_table('concerne_doc') . ' c' .
                    ' WHERE d.id_document = c.id_document ' .
                    ' AND d.Nom_Fichier = "' . $nom_fic . '" limit 1';
                $res = lect_sql($sql);
                $utilise = ($res->rowCount());

                if (!$utilise) {
                    $erreur = LG_IMP_DOCS_DOC_NOT_FORESSEN1 . $nom_fic . LG_IMP_DOCS_DOC_NOT_FORESSEN2;
                }

                // On controle le remplacement
                // Si le fichier existe déjà et que l'utilisateur n'a pas demandé le remplacement, on génère un warning
                if ($Remplacer == 'N') {
                    if (file_exists($chemin_interne . $nom_fic))
                        $erreur = LG_IMP_DOCS_FILE_EXISTS1 . $nom_fic . LG_IMP_DOCS_FILE_EXISTS2;
                }

                // Contrôle des caractéristiques du fichier
                if (($erreur == '') and ($type_doc_htm)) {
                    $erreur = Controle_Charg_Doc($nb);
                }

                // Erreur constatée sur le chargement
                if ($erreur != '') {
                    echo '&nbsp;&nbsp;';
                    Affiche_Warning($erreur);
                }
                // Sinon on peut télécharger
                else {
                    // Téléchargement du fichier après contrôle
                    move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'][$nb], $chemin_interne . $nom_fic);
                    // la zone nom_du_fichier n'est pas alimentée...
                    $nom_du_fichier = $nom_fic;
                    // On chmod le fichier si on n'est pas sous Windows
                    if (substr(php_uname(), 0, 7) != 'Windows') chmod($chemin_interne . $nom_fic, 0644);
                    echo 'ok<br>';
                }
            }
        }
    }
    echo '<hr/>';
}

echo '<table width="80%" border="0" class="classic"align="center">';
echo '<tr><th width="50%">' . my_html(LG_IMP_DOCS_MISS_IMG) . '</th><th>' . my_html(LG_IMP_DOCS_MISS_DOC) . '</th></tr>' . "\n";
echo '<tr class="liste"><td>';
// Recherche des images en base
$sql = 'SELECT DISTINCT (nom) FROM ' . $n_images . ' ORDER BY ident_image';
$abs_img = 0;
if ($res = lect_sql($sql)) {
    while ($enr = $res->fetch(PDO::FETCH_NUM)) {
        if (!file_exists($chemin_images_util . $enr[0])) {
            $abs_img++;
            echo $enr[0] . '<br>';
        }
    }
}
echo '</td><td>';
// Recherche des documents en base
$sql = 'SELECT DISTINCT (d.Nom_Fichier), d.Nature_Document FROM ' . $n_documents . ' d,' . nom_table('concerne_doc') . ' c' .
    ' WHERE d.id_document = c.id_document ' .
    ' ORDER BY d.Id_Document';
$abs_doc = 0;
if ($res = lect_sql($sql)) {
    while ($enr = $res->fetch(PDO::FETCH_NUM)) {
        switch ($enr[1]) {
            case 'IMG':
                $chemin_interne = $chemin_docs_IMG;
                break;
            case 'HTM':
                $chemin_interne = $chemin_docs_HTM;
                break;
            case 'PDF':
                $chemin_interne = $chemin_docs_PDF;
                break;
                if (!file_exists($chemin_interne . $enr[0])) {
                    $abs_doc++;
                    echo $enr[0];
                }
        }
    }
}
echo '</td></tr>' . "\n";
echo '<tr class="liste2">';
if ($abs_img > 1) $plu = 's';
else $plu = '';
echo '<td align="center">==> ' . $abs_img . '</td>' . "\n";
if ($abs_doc > 1) $plu = 's';
else $plu = '';
echo '<td align="center">==> ' . $abs_doc . '</td>' . "\n";
echo '</tr></table>';

$abs = $abs_img + $abs_doc;

if ($abs) {

    echo '<hr/>';

    // Pour les sites gratuits non Premium, on a droit à 5 documents au lieu de 10
    $nb_docs = 10;
    if (($SiteGratuit) and (!$Premium)) $nb_docs = 5;

    echo '<form id="saisie" method="post" enctype="multipart/form-data" action="' . my_self() . '">' . "\n";

    echo 'Remplacer : ';
    echo '<input type="radio" name="Remplacer" value="O"/>' . my_html(ucfirst($LG_Yes)) . '&nbsp;';
    echo '<input type="radio" name="Remplacer" value="N" checked="checked"/>' . my_html(ucfirst($LG_No));

    echo '<table width="60%" class="table_form">' . "\n";
    $larg_titre = "20";

    ligne_vide_tab_form(1);

    for ($nb = 1; $nb <= $nb_docs; $nb++) {
        colonne_titre_tab(LG_IMP_DOCS_MISS_IMG_DOC);
        echo '<input type="file" name="nom_du_fichier[]" size="50"/></td></tr>' . "\n";
    }

    ligne_vide_tab_form(1);
    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '');

    echo '</table>' . "\n";
    echo "</form>";
}

Insere_Bas($compl);
?>
</body>

</html>