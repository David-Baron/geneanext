<?php
//=====================================================================
// Liste des noms de famille
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/Phonetique.php');

$acces = 'L';                        // Type d'accès de la page : (M)ise à jour, (L)ecture

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();

$titre = $LG_Menu_Title['Names_List'];  // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

$codePho = new Phonetique();

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Liste_NomFam', '');

$n_noms_famille = nom_table('noms_famille');
$h_mod = my_html($LG_modify);

if (! $texte) {

    $icone_modifier = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($h_mod) . '" title="' . my_html($h_mod) . '">';

    // Lien direct sur le dernier nom de personne saisi et possibilité d'insérer un nom de famille
    if ($est_gestionnaire) {
        $MaxRef = 0;
        $sql = 'SELECT idNomFam, nomFamille FROM ' . $n_noms_famille . ' a ' .
            'WHERE a.idNomFam = ( SELECT max( idNomFam ) FROM ' . $n_noms_famille . ')';
        if ($resmax = lect_sql($sql)) {
            if ($enrmax = $resmax->fetch(PDO::FETCH_NUM))
                $MaxRef = $enrmax[0];
        }
        // Lien direct sur le dernier nom de personne saisi
        if ($MaxRef > 0) {
            echo my_html(LG_NAMES_LIST_LAST) . ' <a href="' . $root . '/fiche_nomfam.php?idNom=' . $MaxRef . '">' . my_html($enrmax[1]) . '</a>&nbsp;';
            echo Affiche_Icone_Lien('href="' . $root . '/edition_nomfam.php?idNom=' . $MaxRef . '"', 'fiche_edition', $h_mod) . '<br />';
        }
        $resmax->closeCursor();
        echo my_html(LG_NAMES_LIST_ADD) . LG_SEMIC . Affiche_Icone_Lien('href="' . $root . '/edition_nomfam.php?idNom=-1"', 'ajouter', my_html($LG_add)) . '<br /><br />' . "\n";
    }
    $echo_haut = Affiche_Icone_Lien('href="#top"', 'page_haut', my_html($LG_top)) . '<br />';

    // Affichage de la liste des initiales
    //$sql = 'SELECT DISTINCT UPPER(SUBSTRING(nomFamille,1,1)) AS initiale FROM ' . $n_noms_famille . ' ORDER BY initiale';
    $sql = 'SELECT idNomFam, nomFamille, codePhonetique, hex(nomFamille) FROM ' . nom_table('noms_famille') .
        ' ORDER BY 4';
    // Partie initiales
    $res = lect_sql($sql);
    $nb_lignes = $res->rowCount();
    if ($nb_lignes > 0) {
        echo '<table width="100%" border="0" cellspacing="1">' . "\n";
        echo '<tr align="center">' . "\n";
        $Anc_Lettre = '';
        $premier = true;
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $Nom = $row[1];
            if ($Nom == '') $Nom = '?';
            $Nouv_Lettre = $Nom[0];
            if ($Nouv_Lettre != $Anc_Lettre) {
                echo '<td class="rupt_table"><a ';
                if ($premier) {
                    echo 'id="top" ';
                    $premier = false;
                }
                echo 'href="#' . $Nouv_Lettre . '">' . $Nouv_Lettre . '</a></td>';
                $Anc_Lettre = $Nouv_Lettre;
            }
        }
        echo '</tr>' . "\n";
        echo '</table>' . "\n";
        echo '<br />' . "\n";
    }

    // Affichage principal
    //	$sql = 'SELECT idNomFam, nomFamille, codePhonetique, UPPER(SUBSTRING(nomFamille,1,1)) AS initiale FROM ' . nom_table('noms_famille') .
    //		' ORDER BY initiale, nomFamille';

    //	$res = lect_sql($sql);
    $res->closeCursor();
    if ($nb_lignes > 0) {
        $res = lect_sql($sql);
        $Anc_Lettre = '';
        $premier = true;
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $NomA = my_html($row[1]);
            if ($NomA == '') $NomA = '?';
            $Nouv_Lettre = $row[1][0];
            // Traitement en rupture sur Initiale
            if ($Nouv_Lettre != $Anc_Lettre) {
                if (!$premier) echo "<br />\n";
                $premier = false;
                echo '<a name="' . $Nouv_Lettre . '">' . $Nouv_Lettre . '</a>&nbsp;' . $echo_haut;
                $Anc_Lettre = $Nouv_Lettre;
            }

            echo '<a href="' . $root . '/fiche_nomfam.php?idNom=' . $row[0] . '">' . $NomA . '</a> (' . $codePho->codeVersPhon($row[2]) . ")\n";
            if (($est_gestionnaire) and (! $texte)) {
                echo '&nbsp;' . Affiche_Icone_Lien('href="' . $root . '/edition_nomfam.php?idNom=' . $row[0] . '"', 'fiche_edition', $h_mod);
            }
            echo "<br />\n";
        }
    }
    $res->closeCursor();
}

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
</body>

</html>