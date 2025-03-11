<?php
//=====================================================================
// Code commun aux arbres (ascendant, noyau)
//=====================================================================

$n_personnes = nom_table('personnes');
$n_filiations = nom_table('filiations');

$Larg_Cellule = 180;
$Haut_Cellule = 65;
$Demie_Hauteur = round($Haut_Cellule / 2);
$Larg_Trait_Hor = 20;
$wp_cell = 'width:' . $Larg_Cellule . 'px; height:' . $Haut_Cellule . 'px; ';

// Dessine la case d'une personne
// - $LaRef : référence de la personne
// - $before : trait avant la cellule
// - $after : trait après la cellule
function case_pers($LaRef, $before, $after, $trait = 'solid')
{
    global $chemin_images_util, $top, $left, $wp_cell, $est_privilegie, $n_personnes, $img_asc, $img_desc, $img_image, $evenement, $existe_images, $Haut_Cellule, $Larg_Cellule, $Larg_Trait_Hor, $Demie_Hauteur, $Ok_Protection, $LG_Data_noavailable_profile;
    $Ok_Protection = false;
    // Accès aux données de la personne
    $sql = 'select Nom, Prenoms, Ne_Le, Decede_Le, Diff_Internet, Sexe from ' . $n_personnes . ' where reference = ' . $LaRef . ' limit 1';
    if ($res = lect_sql($sql)) {
        if ($infos = $res->fetch(PDO::FETCH_NUM)) {
            // Protection des données sur Internet
            if (($est_privilegie) or ($infos[4] == 'O')) {
                $Ok_Protection = true;
                // couleur de la case en fonction de la personne
                switch ($infos[5]) {
                    case 'm':
                        $classe = "case_arbre_asc_hom";
                        break;
                    case 'f':
                        $classe = "case_arbre_asc_fem";
                        break;
                    default:
                        $classe = "case_arbre_asc_def";
                }
                echo '<div class="' . $classe . '" style="top:' . $top . 'px; left:' . $left . 'px;' . $wp_cell . '">' . "\n";
                if ($LaRef) {
                    $P_N = ret_Nom_prenom($infos[0], $infos[1]);
                    echo '<table width="100%"><tr align="center">';
                    echo '<td>' . '<a ' . Ins_Ref_Pers($LaRef);
                    // Présence d'une image ? Si oui, celle-ci sera affichée au survol de la case
                    $image = Rech_Image_Defaut($LaRef, 'P');
                    // Si l'image n'existe pas, on la zappe...
                    if ($image != '') {
                        if (!file_exists($chemin_images_util . $image)) $image = '';
                    }
                    if ($image != '') {
                        $existe_images = true;
                    }
                    echo '>' . $P_N . '</a><br />' . "\n";
                    $Ne = affiche_date($infos[2]);
                    if ($Ne != '?') $Ne = '&deg; ' . $Ne;
                    else            $Ne = '';
                    $Decede = affiche_date($infos[3]);
                    if ($Decede != '?') $Decede = '+ ' . $Decede;
                    else                $Decede = '';
                    $Dates = $Ne . ' ' . $Decede;
                    echo $Dates . '<br />' . "\n";
                    echo '<a ' . Ins_Ref_Arbre($LaRef) . '>' . $img_asc . '</a>';
                    echo '<a ' . Ins_Ref_Arbre_Desc($LaRef) . '>' . $img_desc . '</a>';
                    if ($image != '') {
                        $image = $chemin_images_util . $image;
                        $hauteur = 150;
                        $largeur = 150;
                        redimage2($image, $hauteur, $largeur);
                        $txt_img = 'Image de ' . $infos[1];
                        echo $img_image . 'title="' . $txt_img . '" alt="' . $txt_img . '" ';
                        echo $evenement . '="visib_image(\'' . $image . '\',' . $hauteur . ',' . $largeur . ');"/>' . "\n";
                    }
                    echo '</td></tr>' . "\n";
                    echo '</table>' . "\n";
                }
                echo '</div>' . "\n";

                //$top2 = round($top+($Haut_Cellule / 2));
                $top2 = $top + $Demie_Hauteur;
                // Trait horizontal avant la cellule
                if ($before) {
                    $left2 = $left - $Larg_Trait_Hor;
                    trait_hor($left2, $top2, $Larg_Trait_Hor, $trait);
                }
                // Trait horizontal après la cellule
                if ($after) {
                    $left2 = $left + $Larg_Cellule;
                    trait_hor($left2, $top2, $Larg_Trait_Hor, $trait);
                }
            } else {
                echo '<div class="case_arbre_asc_def" style="top:' . $top . 'px; left:' . $left . 'px;' . $wp_cell . '">' . "\n";
                echo '<table><tr align="center"><td>' . my_html($LG_Data_noavailable_profile) . '</td></tr></table>';
                echo '</div>';
            }
        }
    }
}

// Dessinne un trait horizontal ou vertical
function trait_noir($left, $top, $largeur, $hauteur, $le_style = 'solid')
{
    echo '<div style="background:#FFFFFF;position:absolute; top:' . $top . 'px; ' .
        'font-size:0px; line-height:0px; ' .     // car IE utilise la taille de la fonte pour la hauteur mini
        'width:' . $largeur . 'px; height:' . $hauteur . 'px; ' .
        'left:' . $left . 'px;border:' . $le_style . ' 1px black;"></div>' . "\n";
}

function trait_hor($left, $top, $largeur, $le_style = 'solid')
{
    trait_noir($left, $top, $largeur, 0, $le_style);
}
function trait_vert($left, $top, $hauteur, $le_style = 'solid')
{
    trait_noir($left, $top, 0, $hauteur, $le_style);
}

// Renvoye le Nom et le premier prénom formatté
function ret_Nom_prenom($Nom, $Prenoms)
{
    $Prenoms = trim($Prenoms);
    // On ne retient que le premier prénom
    if ($Prenoms != '') $Prenoms = UnPrenom($Prenoms);
    $P_N = $Prenoms . ' ' . trim($Nom);
    $max_car = 20;
    if (strlen($P_N) > $max_car) $P_N = substr($P_N, 0, $max_car) . '+';
    return my_html($P_N);
}
