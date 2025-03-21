<?php

//=====================================================================
// Arbre agnatique (par les hommes) / cognatique (par les femmes)
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

// Recup de la variable passée dans l'URL : type d'arbre : "A"gnatique / "C"ognatique
$Type_Arbre = Recup_Variable('Type', 'C', 'AC');

$agnatique = ($Type_Arbre == 'A' ? true : false);
$cognatique = ($Type_Arbre == 'C' ? true : false);

if ($agnatique) $titre = $LG_Tree_Men_Asc;
if ($cognatique) $titre = $LG_Tree_Women_Asc;

$x = Lit_Env();
$index_follow = 'IN';                    // NOFOLLOW demandé pour les moteurs
require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Recup de la variable passée dans l'URL : texte ou non
$texte = Dem_Texte();

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

require(__DIR__ . '/../app/ressources/commun_arbre.php');


$imp_mar = 0;
$ref_top = 0;
$ref_left = 400;
$dim_image = 120;
$left_image_gauche = $ref_left - ($dim_image * 2);
//$left_image_droite = 600;

$top = $ref_top;

// Top pour savoir s'il existe des images à afficher
$existe_image = false;

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

//$LG_Tree_Men_Asc = 'Arbre agnatique';
//$LG_Tree_Women_Asc  = 'Arbre cognatique';


if (!$texte) {
    if ($agnatique) {
        $Autre = 'C';
        $lib = $LG_Tree_Women_Asc;
    } else {
        $Autre = 'A';
        $lib = $LG_Tree_Men_Asc;
    }
    $compl = '<a href="' . $root . '/arbre_agnatique_cognatique?Refer=' . $Refer .  '&amp;Type=' . $Autre . '">' . $lib . '</a>&nbsp;';
} else $compl = '';
$compl .= Ajoute_Page_Info(600, 150) .
    Affiche_Icone_Lien('href="' . $root . '/arbre_agnatique_cognatique?Refer=' . $Refer . '&amp;Type=' . $Type_Arbre . '&amp;texte=O"', 'text', $LG_printable_format) .
    '&nbsp;';

if (! $texte) {
    Insere_Haut($titre, $compl, 'Arbre_Agnatique_Cognatique', $Refer);
} else {
    echo '</head>' . "\n";
    echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td align="center"><b>' . StripSlashes($titre) . '</b></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
}

// On restreint le nombre de générations car la mémoire est limitée...
if ($Environnement == 'L') $max_gen_AD = $max_gen_AD_loc;
else $max_gen_AD = $max_gen_AD_int;
// On autorise 2 fois plus de génération que pour la liste par générations
$max_gen_AD *= 2;

$img_asc = '<img src="' . $root . '/assets/img/' . $Icones['arbre_asc'] . '" border="0" title="' . $LG_assc_tree . '" alt="' . $LG_assc_tree . '"/>';
$img_desc = '<img src="' . $root . '/assets/img/' . $Icones['arbre_desc'] . '" border="0" title="' . $LG_desc_tree . '" alt="' . $LG_desc_tree . '"/>';
$img_image = '&nbsp;&nbsp;&nbsp;<img src="' . $root . '/assets/img/' . $Icones['images'] . '" border="0" ';

$nb_gen = 0;
$ref = $Refer;
$num_pers = 0;
$Personnes[] = $ref;
do {
    $ok_parents = Get_Parents($ref, $Num_Pere, $Num_Mere, $Rang);
    if ($ok_parents) {
        $Personnes[] = $Num_Pere;
        $Personnes[] = $Num_Mere;
        if ($agnatique) $ref = $Num_Pere;
        else            $ref = $Num_Mere;
        if ($ref == 0) $ok_parents = false;
    }
    $nb_gen++;
} while (($ok_parents) and ($nb_gen <= $max_gen_AD));

$glob_existe_image = false;

$c_personnes = count($Personnes);
$deb_sql = 'select Nom, Prenoms, Ne_le, Decede_Le, Diff_Internet, Sexe from ' . nom_table('personnes') . ' where Reference = ';
for ($nb = $c_personnes - 1; $nb >= 0; $nb--) {
    $Refer = $Personnes[$nb];
    if (pair($nb)) {
        $top += ($Haut_Cellule * 2);
        $left = $ref_left + ($Larg_Cellule * 2);
        $left_image = $left + $Larg_Cellule + $dim_image;
    } else {
        $left = $ref_left;
        $left_image = $left_image_gauche;
    }
    if ($nb == 0) {
        $left = $ref_left + ($Larg_Cellule * 1);
        switch ($Type_Arbre) {
            case 'A':
                $left = $ref_left;
                break;
            case 'C':
                $left = $ref_left + ($Larg_Cellule * 2);
                break;
            default:
                break;
        }
    }

    $existe_image = false;

    if ($Refer != 0) {

        $sql = $deb_sql . $Refer . ' limit 1';

        if ($res = lect_sql($sql)) {
            if ($infos = $res->fetch(PDO::FETCH_NUM)) {
                // Protection des données sur Internet
                if (IS_GRANTED('P') or ($infos[4] == 'O')) {
                    $P_N = ret_Nom_prenom($infos[0], $infos[1]);
                    // couleur de la case en fonction de la personne
                    $classe = "case_arbre_asc_def";
                    if (!$texte) {
                        switch ($infos[5]) {
                            case 'm':
                                $classe = "case_arbre_asc_hom";
                                break;
                            case 'f':
                                $classe = "case_arbre_asc_fem";
                                break;
                        }
                    }
                    $cont_cell = '<table width="100%"><tr align="center"><td>';
                    if (!$texte) {
                        $cont_cell .= '<a href="' . $root . '/fiche_fam_pers?Refer=' . $Refer . '"';
                        // Présence d'une image ? Si oui, celle-ci sera affichée au survol de la case
                        $image = Rech_Image_Defaut($Refer, 'P');
                        if ($image != '') {
                            $existe_image = true;
                            $glob_existe_image = true;
                        }
                        $cont_cell .= '>' . $P_N . '</a><br />' . "\n";
                    } else {
                        $cont_cell .= $P_N . '<br />';
                    }
                    $Ne = affiche_date($infos[2]);
                    if ($Ne != '?') $Ne = '&deg; ' . $Ne;
                    else            $Ne = '';
                    $Decede = affiche_date($infos[3]);
                    if ($Decede != '?') $Decede = '+ ' . $Decede;
                    else                $Decede = '';
                    $Dates = $Ne . ' ' . $Decede;
                    $cont_cell .= $Dates . '<br />' . "\n";
                    if (!$texte) {
                        $cont_cell .= '<a href="' . $root . '/arbre_asc_pers?Refer=' . $Refer . '">' . $img_asc . '</a>';
                        $cont_cell .= '<a href="' . $root . '/arbre_desc_pers?Refer=' . $Refer . '">' . $img_desc . '</a>';
                        if ($existe_image) {
                            $txt_img = 'Image de ' . $infos[1];
                            $cont_cell .= $img_image . 'title="' . $txt_img . '" alt="' . $txt_img . '" ' . Survole_Clic_Div('div_' . $Refer) . '/>';
                        }
                    }
                    $cont_cell .= '</td></tr>' . "\n";
                    $cont_cell .= '</table>' . "\n";
                }
            }
        }
    } else {
        // mbuono - definition de classe si pas de Refer
        if ($agnatique) $classe = (pair($nb) ? "case_arbre_asc_fem" : "case_arbre_asc_hom");
        else $classe = (pair($nb) ? "case_arbre_asc_hom" : "case_arbre_asc_fem");
    }


    // Affichage de la cellule
    //case_pers($Refer,false,false);
    // Affichage de la cellule
    echo '<div class="' . $classe . '" style="top:' . $top . 'px; ' .
        'width:' . $Larg_Cellule . 'px; height:' . $Haut_Cellule . 'px; ' .
        'left:' . $left . 'px;">' . "\n";
    echo $cont_cell;
    echo '</div>' . "\n";

    // Affichage de l'image
    if ($existe_image) {
        echo '<div id="div_' . $Refer . '" style="display:none; visibility:hidden; position:absolute; top:' . $top . 'px; left:' . $left_image . 'px;border:solid 1px black;">' . "\n";
        $image = $chemin_images_util . $image;
        Aff_Img_Redim_Lien($image, $dim_image, $dim_image, "id_" . $Refer);
        echo '</div>';
    }

    if (!pair($nb)) {
        // Trait horizontal après la cellule
        $top2 = round($top + ($Haut_Cellule / 2));
        $left2 = $left + $Larg_Cellule;
        trait_hor($left2, $top2, $Larg_Cellule);
    }
    // Trait vertical après la cellule
    if ($nb != 0) {
        $top2 = $top + $Haut_Cellule;
        if (
            (($agnatique) and (!pair($nb)))
            or
            (($cognatique) and (pair($nb)))
        ) {
            $left2 = $left + ($Larg_Cellule / 2);
            // Dessin du trait
            trait_vert($left2, $top2, $Haut_Cellule);
        }
    }
}

if ($nb_gen > $max_gen_AD) {
    echo '<br /><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . my_html($LG_tip) . '" title="' . my_html($LG_tip) . '">' .
        $LG_Tree_Lim_1 . $max_gen_AD . $LG_Tree_Lim_2 . '<a href="' . $root . '/vue_personnalisee">' . $LG_Tree_Lim_3 . '</a>' . $LG_Tree_Lim_4 . '.' . "\n";
}

if (!$texte) {
    for ($nb == 0; $nb < $c_personnes; $nb++)
        echo '<br /><br /><br /><br />';
    echo '<table cellpadding="0" width="100%" border="0"><tr><td align="right">';
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    if ($compl != '') {
        echo $compl;
    }
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
    echo '</td></tr></table>';
}

if ($glob_existe_image) {
    echo '<table>';
    echo '<tr><td>';
    echo '<div id="image">';
    $action = $LG_Tree_Icon_Hover;
    if ($Comportement == 'C') $action = $LG_Tree_Icon_Click;
    echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . my_html($LG_tip) . '" title="' . my_html($LG_tip) . '"> '
        . $action
        . '<img src="' . $root . '/assets/img/' . $Icones['images'] . '" alt="Images" title="Images"> '
        . $LG_Tree_Show_Image;
    echo '</div>';
    echo '</table>';
    echo '<script type="text/javascript">';
    echo '<!--';
    //echo 'if(document.all)';
    echo 'var defaut = image.innerHTML;';
    echo '//-->';
    echo '</script>';
}
?>

</body>

</html>