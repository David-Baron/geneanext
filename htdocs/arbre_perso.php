<?php

//=====================================================================
// Affichage d'un arbre personnalisé
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Pers_Tree'];          // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');
//
//	Lecture des paramètres passés
//	Si Refer = 0 => appel direct de la page, sans avoir beson de la calculer
$nomArbre = Recup_Variable('nomArbre', 'S');
$reference = Recup_Variable('Refer', 'S');
//
if ($nomArbre != '') {
    $nomArbre = rawurldecode($nomArbre);
}
//
// Vérification s'il n'y a pas de personnes non diffusables sur Internet
$sql = 'SELECT COUNT(*) AS nbPers FROM ' . nom_table('arbrepers') . ' AS ap, ' .
    nom_table('personnes') . ' AS p, ' .
    nom_table('arbre') . ' AS a' .
    ' WHERE ap.reference = p.reference AND ap.idArbre = a.idArbre AND a.nomFichier = \'' . $nomArbre . '\'' .
    ' AND p.Diff_Internet  <> \'O\'';
$res = lect_sql($sql);
$row = $res->fetch(PDO::FETCH_NUM);
$nbPers = $row[0];
if ($nbPers > 0 and !$_SESSION['estPrivilegie']) {
    echo '<center><font color="red"><br><br><br><h2>' . LG_PERS_TREE_ERROR_NO_SHOW . '</h2></font></center><br />' .
        '<a href="' . $root . '/">' . my_html($LG_back_to_home) . '</a><br />';
    return 0;
}

//
Insere_Haut($titre, '', 'Arbre_Perso', '');
//
$style_fond = 'style="background-image:url(\'' . $chemin_images . 'bar_off.gif\');background-repeat:repeat-x;"';
//
if ($reference == 0) {
    $nomFichierHtml = $nomArbre;
    $position = strpos($nomArbre, "_");
    if ($position) {
        $nomArbre = substr($nomArbre, 0, $position);
    }
}
//
// Lecture du nom du répertoire
$sql = 'SELECT valeur FROM ' . nom_table('arbreparam')
    . ' WHERE ident1 = \'repertoire\' AND ident2 = \'genImg\'';
$res = lect_sql($sql);
if ($row = $res->fetch(PDO::FETCH_NUM)) {
    $rep = $row[0];
} else {
    echo my_html(LG_PERS_TREE_MISSING_DIR) . '<br />';
    exit;
}
//	Lecture des caractéristiques de l'arbre
$sql = 'SELECT * FROM ' . nom_table('arbre')
    . ' WHERE nomFichier = \'' . $nomArbre . '\'';
$res = lect_sql($sql);
$idArbre = 0;
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    $largPage = $row['largeurPage'];
    $hautPage = $row['hauteurPage'];
    $idArbre = $row['idArbre'];
    $descArbre = $row['descArbre'];
    $nbPagesHor = $row['nbPagesHor'];
    $nbPagesVer = $row['nbPagesVer'];
} else {
    echo my_html(LG_PERS_TREE_MISSING_ROW) . $nomArbre . '<br />';
    exit;
}
//	Lecture de la position de la personne
if ($reference > 0) {
    $sql = 'SELECT * FROM ' . nom_table('arbrepers') . ' WHERE idArbre = \'' . $idArbre . '\' AND reference = ' . $reference;
    $posX = 0;        //	Abscisse de la personne
    $posY = 0;        //	Ordonnée de la personne
    $numPageH = 0;    //	Numéro de page horizontale	
    $numPageV = 0;    //	Numéro de page verticale	
    $res = lect_sql($sql);
    if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $posX = $row['posX'] * 0.75;
        $posY = $row['posY'] * 0.75;

        $numPageH = intval($posX / $largPage);
        $numPageV = intval($posY / $hautPage);
    }
    $nomFichierHtml = $rep . '/' . $nomArbre . '_' . $numPageV .  '_' . $numPageH . '.html';
} else {
    $nomFichierHtml = $rep . '/' . $nomFichierHtml;
}
//	Affichage du nom du fichier
echo '<h2>&nbsp;&nbsp;&nbsp;' . $descArbre . '</h2>' . "\n";
//	Liste de choix de la page
if ($nbPagesHor * $nbPagesVer > 1) {
    $numPage = 1;
    echo '<form method="get" action="">' . "\n";
    echo '<select onchange="document.location = this.options[this.selectedIndex].value;">' . "\n";
    echo '<option>' . my_html(LG_PERS_TREE_CHOOSE_OTHER) . '</option>' . "\n";
    for ($indV = 0; $indV < $nbPagesVer; $indV++) {
        for ($indH = 0; $indH < $nbPagesHor; $indH++) {
            $Url = rawurlencode($nomArbre . '_' . $indV .  '_' . $indH);
            echo '<option value="Arbre_Perso.php?nomArbre=' . $Url . '.html&Refer=0">Page ' . $numPage . '</option>' . "\n";
            $numPage++;
        }
    }
    echo "</form></select><p>\n";
}
//
if (file_exists($nomFichierHtml)) {
    if ($wFic = fopen($nomFichierHtml, 'r')) {
        while (!feof($wFic)) {
            $ligne = fgets($wFic);
            echo trim($ligne) . "\n";
        }
    } else {
        echo my_html(LG_PERS_TREE_OPEN_ERROR) . $nomFichierHtml . "<br />";
    }
} else {
    echo my_html(LG_PERS_TREE_NOT_FOUND) . LG_SEMIC . $nomFichierHtml . "<br />";
}

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>