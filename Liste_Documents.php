<?php
//====================================================================
//  Affichage de la liste des documents classement par type
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                          // Type d'acc�s de la page : (M)ise � jour, (L)ecture
$titre = $LG_Menu_Title['Documents_List'];        // Titre pour META
$x = Lit_Env();
$niv_requis = 'I';                       // Page r�serv�e � partir du profil invit�
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Verrouillage de la gestion des documents sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

$compl = Ajoute_Page_Info(600, 150);

Insere_Haut(my_html($titre), $compl, 'Liste_Documents', '');

// Lien direct sur le dernier document saisi
if ($_SESSION['estGestionnaire']) {
    require(__DIR__ . '/app/ressources/fonctions_maj.php');
    $MaxRef = Nouvel_Identifiant('id_document', 'documents') - 1;
}

// R�cup�ration du type s�lectionn� sur l'affichage pr�c�dent
$defaut = '^^^^';
$TypeEv = '';
if (isset($_POST['TypeEv'])) $TypeEv = $_POST['TypeEv'];
if ($TypeEv == $defaut) $TypeEv = '';

$sql = 'select distinct d.id_type_document, Libelle_Type ' .
    'from ' . nom_table('documents') . ' d , ' . nom_table('types_doc') . ' t ' .
    'where d.id_type_document = t.id_type_document ' .
    'order by Libelle_Type';
//  
echo '<form action="' . my_self() . '" method="post">' . "\n";
echo '<table width="50%" align="center">' . "\n";
echo '<tr align="center" >';
echo '<td width="50%" class="rupt_table">' . LG_DOC_LIST_TYPE . ' ' . "\n";
echo '<select name="TypeEv">' . "\n";
echo '<option value="' . $defaut . '">' . my_html($LG_All) . '</option>' . "\n";
if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        echo '<option value="' . $row[0] . '"';
        if ($TypeEv == $row[0]) {
            echo ' selected="selected"';
        }
        echo '>' . my_html($row[1]) . '</option>' . "\n";
    }
}
$res->closeCursor();
echo '</select>' . "\n";
echo '</td>' . "\n";
echo '<td width="50%" class="rupt_table"><input type="submit" value="' . $LG_modify_list . '"/></td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";
echo '</form>' . "\n";

$crit_type = '';
if ($TypeEv != '') $crit_type = ' and d.id_type_document = "' . $TypeEv . '"';

// Constitution de la requ�te d'extraction
$requete = 'select id_Document,Titre,Nature_Document,Diff_Internet,Nom_Fichier, Libelle_Type ' .
    'FROM ' . nom_table('documents') . ' d,' . nom_table('types_doc') . ' t ' .
    'WHERE d.id_type_document = t.id_type_document ' .
    $crit_type .
    ' ORDER BY Titre';
$result = lect_sql($requete);

// Lien direct sur le dernier evenement saisi
if ($_SESSION['estGestionnaire']) {
    if ($MaxRef > 0) {
        echo '<a href="' . $root . '/edition_document.php?Reference=' . $MaxRef .
            '">' . my_html(LG_DOC_LIST_LAST) . '</a><br />';
    }
    // Possibilit� d'ins�rer un document
    echo my_html(LG_DOC_LIST_ADD_1) . ' ' . Affiche_Icone_Lien('href="' . $root . '/edition_document.php?Reference=-1"', 'ajouter', $LG_add) . '&nbsp;;&nbsp;';
    echo my_html(LG_DOC_LIST_ADD_MANY) . ' ' . Affiche_Icone_Lien('href="' . $root . '/create_multiple_docs.php"', 'ajout_multiple', LG_DOC_LIST_ADD_MANY_TIP) . '<br /><br />' . "\n";
}
//
//  Affichage des documents
if ($result->rowCount() > 0) {
    // Optimisation : pr�paration echo des images
    $echo_modif = '<img src="' . $chemin_images_icones . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '"  title="' . $LG_modify . '"/></a>';

    $premier = 1;
    while ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
        $refDoc = $enreg['id_Document'];
        $x_Titre = $enreg['Titre'];
        $typologie = $enreg['Libelle_Type'];
        $affiche = false;
        if (($enreg['Diff_Internet'] == 'O') or ($_SESSION['estGestionnaire'])) {
            $affiche = true;
        }
        if ($affiche) {
            echo '<a href="' . $root . '/fiche_document.php?Reference=' . $refDoc . '">' . $x_Titre . '</a>';
        } else {
            echo $x_Titre;
        }
        echo ' (' . LG_DOC_LIST_TYPE . ' ' . $typologie . ', ' . $Natures_Docs[$enreg['Nature_Document']] . ")\n";
        if ($affiche) {
            echo '&nbsp;<a href="' . $root . '/edition_document.php?Reference=' . $refDoc . '">' . $echo_modif;
            $nature = Get_Type_Mime($enreg['Nature_Document']);
            $chemin_docu = get_chemin_docu($enreg['Nature_Document']);
            echo '&nbsp;&nbsp;' . Affiche_Icone_Lien('href="' . $chemin_docu . $enreg['Nom_Fichier'] . '" type="' . $nature . '"', 'oeil', LG_DOC_LIST_DISPLAY, 'n');
        }
        echo '<br />' . "\n";
    }
}
Insere_Bas($compl);
?>
</body>

</html>