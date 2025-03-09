<?php

//=====================================================================
// Liste des documents pour une branche
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'num_ref',
    'type_doc'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Afficher), 'S');
$annuler   = Secur_Variable_Post($annuler, 30, 'S');        // 2 libellés possibles
$num_ref   = Secur_Variable_Post($num_ref, 1, 'N');
$type_doc  = Secur_Variable_Post($type_doc, 1, 'N');

// On retravaille le libellé du bouton pour être standard...
if ($ok == $lib_Afficher) $ok = 'OK';
// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$acces = 'L';
$titre = $LG_Menu_Title['Galery_Branch'];        // Titre pour META
$x = Lit_Env();

require(__DIR__ . '/Gestion_Pages.php');

// Ecran interdit sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) $bt_An = true;

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {
    $compl = Ajoute_Page_Info(600, 150);

    if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);
    Insere_Haut($titre, $compl, 'Liste_Docs_Branche', '');

    if (!$bt_OK) {

        // var_dump($_SESSION['estPrivilegie']);

        // Affichage du formulaire de choix sur le 1er affichage
        include(__DIR__ . '/assets/js/edition_geneamania.js');
        include(__DIR__ . '/assets/js/Liste_Pers.js');

        echo '<form id="saisie" method="post" action="' . my_self() . '">' . "\n";

        echo '<input type="text" id="num_ref" name ="num_ref" value = "0" />';
        echo '<input type="' . $hidden . '" id="page" value = "' . $_SERVER['SCRIPT_NAME'] . '" />';

        $larg_titre = '30';
        echo '<table width="80%" border="0" class="table_form">' . "\n";

        ligne_vide_tab_form(1);

        colonne_titre_tab(LG_DOC_BRANCH_ORIGINE);
        $sql_noms = 'SELECT DISTINCT idNomFam, Nom FROM ' . nom_table('personnes') . ' WHERE Reference <> 0';
        if (!$est_privilegie) {
            $sql_noms .= " AND Diff_Internet = 'O'";
        }
        $sql_noms .= ' ORDER by Nom';
        $res = lect_sql($sql_noms);
        echo '<select name="nom" id="noms" onchange="updatePersonnes(this.value)">';
        echo '<option value="-1">' . my_html(LG_DOC_BRANCH_SEL_NAME) . '</option>';
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            echo '<option value="' . $enreg[0] . '">' . my_html($enreg[1]) . '</option>';
        }
        echo '</select>' . "\n";
        echo '<select name="personnes" id="personnes" onchange="updateLiensIcones(this.value)"></select>';
        echo '</td></tr>' . "\n";

        colonne_titre_tab(LG_DOC_BRANCH_DOC_TYPE);
        echo '<select name="type_doc" size="1">';
        echo '<option>' . my_html(LG_DOC_BRANCH_SEL_TYPE) . '</option>' . "\n";
        $sql = 'select distinct t.Id_Type_Document, Libelle_Type ' .
            'from ' . nom_table('documents') . ' d, ' . nom_table('types_doc') . ' t ' .
            'where d.Id_Type_Document = t.Id_Type_Document ' .
            ' and Nature_Document = "IMG" ' .
            'order by Libelle_Type';
        if ($res = lect_sql($sql)) {
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                echo '<option value="' . $row[0] . '"';
                if ($type_doc == $row[0]) echo ' selected="selected"';
                echo '>' . my_html($row[1]) . "</option>\n";
            }
        }
        $res->closeCursor();
        echo "</select>\n";
        echo '</td>' . "\n";

        echo '</tr>';

        ligne_vide_tab_form(1);
        bt_ok_an_sup($lib_Afficher, $lib_Annuler, '', '');

        echo '</table>';
        echo '</form>';
    }

    if (($num_ref != 0) and ($bt_OK)) {

        if (Get_Nom_Prenoms($num_ref, $Nom, $Prenoms)) {
            echo '<h3 align="center">' . LG_DOC_BRANCH_ORIGINE . ' : ' . $Prenoms . ' ' . $Nom . '</h3><br />' . "\n";
        }

        $base = $num_ref;
        $num_gen = 1;
        $courant = -1;
        $min = 0;
        $max = 0;

        do {
            $ajoutes = false;
            if ($num_gen == 1) {
                // 0..0
                $x = Get_Parents($base, $Num_Pere, $Num_Mere, $Rang);
                ajoute($base);
                ajoute($Num_Pere);
                ajoute($Num_Mere);
            } else {
                // 1..2
                // 3..6
                $min = $max + 1;
                $max = $courant;
                if ($debug) {
                    aff_var('min');
                    aff_var('max');
                }
                for ($nb = $min; $nb <= $max; $nb++) {
                    $x = Get_Parents($pers[$nb], $Num_Pere, $Num_Mere, $Rang);
                    ajoute($Num_Pere);
                    ajoute($Num_Mere);
                }
            }
            $num_gen++;
            if ($debug) var_dump($pers);
        } while ($ajoutes > 0);

        $rep = $chemin_docs_IMG;
        $cpt = 0;        // Compteur d'image par ligne
        $cptotal = 0;    // Nombre total d'images
        $num_image = 0;
        $nb_img_ligne = 5;

        $crit_diff_doc = '';
        $crit_diff_pers = '';
        if (!$est_privilegie) {
            $crit_diff_doc = ' and d.Diff_Internet = "O"';
            $crit_diff_pers = ' and p.Diff_Internet = "O"';
        }
        echo '<br />';
        echo '<table border="0" width="100%" cellspacing="2"  align="center">' . "\n";

        $sql = 'select '
            . '  d.Id_Document, d.Titre, d.Nom_Fichier, d.Date_Creation, d.Date_Modification'
            . '  ,c.Reference_Objet, c.Type_Objet'
            . '  ,p.Nom, p.Prenoms'
            . ' from ' . nom_table('documents') . ' d,'
            . nom_table('concerne_doc') . ' c,'
            . nom_table('personnes') . ' p'
            . '  where d.Id_Document = c.Id_Document'
            . '    and d.Nature_Document = "IMG"'
            . '   and d.Id_Type_Document = ' . $type_doc
            . '   and c.Type_Objet = "P"'
            . '   and p.Reference = c.Reference_Objet'
            . $crit_diff_doc
            . $crit_diff_pers
            . ' order by d.Id_Document';

        $resGI = lect_sql($sql);

        while ($row = $resGI->fetch(PDO::FETCH_ASSOC)) {
            $ref_pers = $row['Reference_Objet'];
            if ($debug) echo '<br />' . $row['Type_Objet'] . '/' . $ref_objet . '<br />';
            // La personne est-elle dans la branche ?
            if (array_search($ref_pers, $pers)) {
                if (!$num_image) {
                    echo '<tr align="center">';
                }
                $num_image++;
                echo '<td>';
                Aff_Img_Redim_Lien($rep . $row['Nom_Fichier'], 200, 200);
                echo '<br /><a ' . Ins_Ref_Pers($ref_pers) . '>' . $row['Prenoms'] . ' ' . $row['Nom'] . '</a>';
                echo '</td>' . "\n";
                if ($num_image == $nb_img_ligne) {
                    $num_image = 0;
                    echo '</tr>';
                }
            }
        }
        // Fin de la ligne
        if ($num_image)
            for ($nb = $num_image; $nb < $nb_img_ligne; $nb++) echo '<td>&nbsp;</td>';
        echo '</tr>';
        echo '</table>' . "\n";
        Bouton_Retour($lib_Retour, '');
    }
}

function ajoute($personne)
{
    global $pers, $ajoutes, $courant, $debug;
    if ($personne) {
        $pers[] = $personne;
        if ($debug) {
            if (Get_Nom_Prenoms($personne, $Nom, $Prenoms)) {
                echo $personne . ' : ' . $Prenoms . ' ' . $Nom . '<br />';
            }
        }
        $ajoutes = true;
        $courant++;
    }
}

function aff_var($nom)
{
    global $$nom;
    echo '$' . $nom . ' = ' . $$nom . '<br />';
}

function dd($date)
{
    return date("d/m/Y H:i:s", $date);
}

?>
</body>

</html>