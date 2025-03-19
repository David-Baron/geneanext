<?php

//=====================================================================
// Galerie d'image d'un type donné : signatures, portraits...
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                    // Type d'accès de la page : (M)ise à jour, (L)ecture

$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// Nécessaire en cas de sélection de type, sinon le bouton retour ne fonctionne pas
if (strpos(AddSlashes(getenv("HTTP_REFERER")), my_self()) !== false) {
    $xx = array_pop($_SESSION['pages']);
}

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$titre = $LG_Menu_Title['Galery'];     // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

    $type_doc = Recup_Variable('type_doc', 'N', 1);

    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut($titre, $compl, 'Galerie_Images', '');

    $rep = $chemin_docs_IMG;

    $cpt = 0; // Compteur d'images par ligne
    $cptotal = 0; // Nombre total d'images

    $num_image = 0;
    $nb_img_ligne = 5;

    $sql = 'select distinct t.Id_Type_Document, Libelle_Type ' .
        'from ' . nom_table('documents') . ' d, ' . nom_table('types_doc') . ' t ' .
        'where d.Id_Type_Document = t.Id_Type_Document ' .
        ' and Nature_Document = "IMG" ' .
        'order by Libelle_Type';

    echo '<form id="saisie" action="' . my_self() . '" method="post">' . "\n";
    echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
    echo '<table border="0" width="50%" align="center">' . "\n";
    echo '<tr align="center">';

    echo '<td class="rupt_table">' . my_html(LG_IMAGES_GAL_CHOOSE_TYPE) . LG_SEMIC . "\n";
    echo '<select name="type_doc" size="1" 
		onchange="var dest=this.options[this.selectedIndex].value;
					document.location = \'' . my_self() . '?type_doc=\'+dest;">' . "\n";
    echo '<option>Type ?</option>' . "\n";
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
    echo '</tr></table>';

    if ($type_doc) {

        $crit_diff = '';
        if (!$est_privilegie) $crit_diff = ' and d.Diff_Internet = "O"';

        echo '<br />';
        echo '<table border="0" width="100%" cellspacing="2"  align="center">' . "\n";

        $sql = 'select ' .
            '  d.Id_Document, d.Titre, d.Nom_Fichier, d.Diff_Internet, d.Date_Creation, d.Date_Modification,' .
            '  c.Reference_Objet, c.Type_Objet' .
            ' from ' . nom_table('documents') . ' d left join ' . nom_table('concerne_doc') . ' c' .
            '  on d.Id_Document = c.Id_Document' .
            ' where d.Nature_Document = "IMG" and d.Id_Type_Document = ' . $type_doc .
            $crit_diff .
            ' order by d.Id_Document';

        $resGI = lect_sql($sql);

        while ($row = $resGI->fetch(PDO::FETCH_ASSOC)) {
            $ref_objet = $row['Reference_Objet'];
            if ($debug) echo '<br />' . $row['Type_Objet'] . '/' . $ref_objet . '<br />';
            if (!$num_image) {
                echo '<tr align="center">';
            }
            $num_image++;
            echo '<td>';
            Aff_Img_Redim_Lien($rep . $row['Nom_Fichier'], 200, 200);
            if ($debug) echo '<br />' . $row['Type_Objet'] . '/' . $ref_objet;
            $n_personnes = nom_table('personnes');
            switch ($row['Type_Objet']) {
                // Personne
                case 'P':
                    $x = Get_Nom_Prenoms($ref_objet, $Nom, $Prenoms);
                    if ($debug) echo '$x ' . $x . ', est_privilegie' . $est_privilegie . ', $Diff_Internet_P' . $Diff_Internet_P . '<br />';
                    if (($x) and (($est_privilegie) or ($Diff_Internet_P == 'O'))) {
                        echo '<br /><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_objet . '">' . $Prenoms . ' ' . $Nom . '</a>';
                    }
                    break;
                // Union
                case 'U':
                    $sqlU = 'SELECT c1.Reference AS c1Ref,c1.Prenoms AS c1Prenoms,c1.Nom AS c1Nom,c1.Diff_Internet AS c1Diff,' .
                        'c2.Reference AS c2Ref,c2.Prenoms AS c2Prenoms,c2.Nom AS c2Nom,c2.Diff_Internet AS c2Diff' .
                        ' FROM ' . nom_table('unions') . ' u,'
                        . $n_personnes . ' c1,'
                        . $n_personnes . ' c2 ' .
                        ' WHERE u.Reference = ' . $ref_objet .
                        ' AND c1.Reference = u.Conjoint_1 AND c2.Reference = u.Conjoint_2 ';
                    $resU = lect_sql($sqlU);
                    if ($rowU = $resU->fetch(PDO::FETCH_ASSOC)) {
                        echo '<br />' . my_html(LG_IMAGES_GAL_UNION) . ' ';
                        // Conjoint 1
                        if (($est_privilegie) or ($rowU['c1Diff'] != 'N')) {
                            echo '<br /><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $rowU['c1Ref'] . '">' . my_html($rowU['c1Prenoms'] . ' ' . $rowU['c1Nom']) . '</a>';
                        } else echo 'x';
                        // Conjoint 2
                        echo ' ' . my_html(LG_IMAGES_GAL_UNION_AND) . ' ';
                        if (($est_privilegie) or ($rowU['c2Diff'] != 'N')) {
                            echo '<br /><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $rowU['c2Ref'] . '">' . my_html($rowU['c2Prenoms'] . ' ' . $rowU['c2Nom']) . '</a>';
                        } else echo 'x';
                    }
                    break;
                // Filiation
                case 'F':
                    $sqlF = 'SELECT enf.Sexe AS eSexe,enf.Prenoms AS ePrenoms,enf.Nom AS eNom,enf.Diff_Internet AS eDiff,' .
                        'pere.Reference AS pRef,pere.Prenoms AS pPrenoms,pere.Nom AS pNom,pere.Diff_Internet AS pDiff,' .
                        'mere.Reference AS mRef,mere.Prenoms AS mPrenoms,mere.Nom AS mNom,mere.Diff_Internet AS mDiff' .
                        ' FROM ' . nom_table('filiations') . ' fi,'
                        . $n_personnes . ' enf,'
                        . $n_personnes . ' pere,'
                        . $n_personnes . ' mere ' .
                        ' WHERE fi.Enfant = ' . $ref_objet .
                        ' AND fi.Enfant = enf.Reference AND fi.Pere = pere.Reference AND fi.Mere = mere.Reference ';
                    $resF = lect_sql($sqlF);
                    if ($rowF = $resF->fetch(PDO::FETCH_ASSOC)) {
                        // Enfant
                        if (($est_privilegie) or ($rowF['eDiff'] != 'N')) {
                            switch ($rowF['eSexe']) {
                                case 'm':
                                    $texte = LG_IMAGES_GAL_SON;
                                    break;
                                case 'f':
                                    $texte = LG_IMAGES_GAL_DAUGHTER;
                                    break;
                                default:
                                    $texte = LG_IMAGES_GAL_CHILD;
                            }
                            echo '<br />' . my_html(LG_IMAGES_GAL_FILIATION) . ' <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_objet . '">' . my_html($rowF['ePrenoms'] . ' ' . $rowF['eNom']) . '</a>';
                            echo '&nbsp;' . my_html($texte) . ' ';
                        } else echo 'x';
                        // Père
                        if (($est_privilegie) or ($rowF['pDiff'] != 'N')) {
                            echo '<br /><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $rowF['pRef'] . '">' . my_html($rowF['pPrenoms'] . ' ' . $rowF['pNom']) . '</a>';
                        } else echo 'x';
                        // Mère
                        echo ' et de ';
                        if (($est_privilegie) or ($rowF['eDiff'] != 'N')) {
                            echo '<br /><a href="' . $root . '/fiche_fam_pers.php?Refer=' . $rowF['mRef'] . '">' . my_html($rowF['mPrenoms'] . ' ' . $rowF['mNom']) . '</a>';
                        } else echo 'x';
                    }
                    break;
                // Evènement
                case 'E':
                    $sqlE = 'SELECT Reference, Titre FROM ' . nom_table('evenements') . ' WHERE reference = ' . $ref_objet . ' limit 1';
                    $resE = lect_sql($sqlE);
                    if ($rowE = $resE->fetch(PDO::FETCH_NUM)) {
                        echo '<br /><a href="' . $root . '/fiche_evenement.php?refPar=' . $rowE[0] . '">' . my_html($rowE[1]) . '</a>';
                    }
                    break;
                // Ville
                case 'V':
                    echo '<br /><a href="' . $root . '/fiche_ville.php?Ident=' . $ref_objet . '">' . lib_ville($ref_objet) . '</a>';
                    break;
            }

            echo '</td>' . "\n";
            if ($num_image == $nb_img_ligne) {
                $num_image = 0;
                echo '</tr>';
            }
        }
        // Fin de la ligne
        if ($num_image)
            for ($nb = $num_image; $nb < $nb_img_ligne; $nb++) echo '<td>&nbsp;</td>';
        echo '</tr>';
        echo '</table>' . "\n";
    }
    // Formulaire pour le bouton retour
    bt_ok_an_sup('', $lib_Retour, '', '', false);
    echo '</form>';

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