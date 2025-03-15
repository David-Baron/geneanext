<?php
//=====================================================================
// Arbre ascendant d'une personne
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                        // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_assc_tree;
$x = Lit_Env();
$index_follow = 'IN';                // NOFOLLOW demandé pour les moteurs
require(__DIR__ . '/app/ressources/gestion_pages.php');        // Appel de la gestion standard des pages

function Retourne_Pers($Num)
{
    global $Ensemble;
    $Ligne = $Num;
    if (is_array($Ensemble)) $Ensemble[] = $Ligne;
    else                     $Ensemble[0] = $Ligne;
}

// Ajoute une entrée vide dans l'ensemble de sortie
function Add_Vide()
{
    global $Ensemble;
    $Ensemble[] = '0';
}

// Recherche le couple de parents
function Charge_Parents($Personne)
{
    if ($Personne != 0) {
        $x = get_Parents($Personne, $Num_Pere, $Num_Mere, $Rang);
        if ($Num_Pere != 0) Retourne_Pers($Num_Pere);
        else                Add_Vide();
        if ($Num_Mere != 0) Retourne_Pers($Num_Mere);
        else                Add_Vide();
    } else {
        Add_Vide();
        Add_Vide();
    }
}

$imp_mar = 0;

require(__DIR__ . '/app/ressources/commun_arbre.php');

// Coordonnée gauche par génération
$Coord_X = array(40, 260, 480, 700);
// Coordonnées verticales
$Coord_Y = array(
    255,
    115,
    395,
    45,
    185,
    325,
    465,
    10,
    80,
    150,
    220,
    290,
    360,
    430,
    500
);

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

$compl = Ajoute_Page_Info(600, 150);
if (!$is_bot) {
    $compl .=
        Affiche_Icone_Lien('href="' . $root . '/appelle_image_arbre_asc.php?Refer=' . $Refer . '"', 'text', $LG_printable_format) . '&nbsp;' .
        Affiche_Icone_Lien('href="' . $root . '/arbre_asc_pdf.php?Refer=' . $Refer . '"', 'PDF', $LG_Tree_Pdf_7Gen) . '&nbsp;';
}
Insere_Haut('Arbre ascendant', $compl, 'nouv_arbre_asc', $Refer);

echo '<div style="position:absolute; top:50px; left:10px;">' . "\n";

$nb_Rangs = 4;
$Ref = $Refer;

Retourne_Pers($Ref);

if ($nb_Rangs > 1) {
    Charge_Parents($Ref);
}

if ($nb_Rangs > 2) {
    for ($nb = 3; $nb <= $nb_Rangs; $nb++) {
        $Rang_Min = pow(2, $nb - 2);
        $Rang_Max = pow(2, $nb - 1) - 1;
        for ($nb2 = $Rang_Min; $nb2 <= $Rang_Max; $nb2++) {
            $Ref = $Ensemble[$nb2 - 1];
            Charge_Parents($Ref);
        }
    }
}

$num_gen = 1;
$der_num = 0;
$Mariage = '';
// Top pour savoir s'il existe des images à afficher
$existe_images = false;
$c_ens = count($Ensemble);

$img_asc = '<img src="' . $chemin_images_icones . $Icones['arbre_asc'] . '" border="0" title="' . $LG_assc_tree . '" alt="' . $LG_assc_tree . '"/>';
$img_desc = '<img src="' . $chemin_images_icones . $Icones['arbre_desc'] . '" border="0" title="' . $LG_desc_tree . '" alt="' . $LG_desc_tree . '"/>';
$img_image = '&nbsp;&nbsp;&nbsp;<img src="' . $chemin_images_icones . $Icones['images'] . '" border="0" ';

if ($Comportement == 'C') $evenement = 'onclick';
else $evenement = 'onmouseover';

for ($nb_enr = 0; $nb_enr < $c_ens; ++$nb_enr) {

    if ($nb_enr == ($der_num * 2 + 1)) {
        ++$num_gen;
        $der_num = $nb_enr;
    }

    $LaRef = $Ensemble[$nb_enr];

    // Initialisation mari et femme
    if (!pair($nb_enr)) {
        $mari = 0;
        $femme = 0;
        $Mariage = '';
    }

    $top  = $Coord_Y[$nb_enr];
    $left = $Coord_X[$num_gen - 1];
    $before = false;
    $after = false;
    if ($num_gen > 1)
        $before = true;
    if ($num_gen < $nb_Rangs)
        $after = true;
    case_pers($LaRef, $before, $after);

    // Affichage des enfants du couple
    if (($LaRef) and ($Ok_Protection)) {
        if ($num_gen < $nb_Rangs) {

            $pere = $nb_enr * 2 + 1;
            $mere = $pere + 1;

            if (($Ensemble[$pere] != 0) or ($Ensemble[$mere] != 0)) {
                //echo '<!-- père : '.$Ensemble[$pere].' mère '.$Ensemble[$mere].' -->'."\n";
                $top_e = round($top + ($Haut_Cellule / 2)) + 3;
                $left_e = $left + $Larg_Cellule + 3;
                echo '<div id="d_img_' . $LaRef . '" style="position:absolute; top:' . $top_e . 'px; left:' . $left_e . 'px;">';
                echo '<img id="img_' . $LaRef . '" src="' . $chemin_images_icones . $Icones['groupe'] . '" alt="' . my_html($LG_Tree_Show_Hide_Child) . '" ' .
                    Survole_Clic_Div('enf_' . $LaRef) . '/>' . "\n";
                echo '</div>';
                $top_e += 17;
                echo '<div id="enf_' . $LaRef . '" style="background:#FFFFFF;border:dotted 1px black;position:absolute; top:' . $top_e . 'px; left:' . $left_e . 'px; z-index:10; width:' . $Larg_Cellule . 'px; visibility: hidden;">';
                $sql_FS = 'select Enfant, Nom, Prenoms, Diff_Internet from ' . $n_filiations . ' f, ' . $n_personnes . ' p ' .
                    ' where Pere = ' . $Ensemble[$pere] . ' and Mere = ' . $Ensemble[$mere] .
                    ' and Enfant = Reference ' .
                    ' order by Rang';
                $res_FS = lect_sql($sql_FS);
                if ($res_FS->RowCount() > 0) {
                    while ($row = $res_FS->fetch(PDO::FETCH_NUM)) {
                        if (($est_privilegie) or ($row[3] == 'O')) {
                            $Enfant = $row[0];
                            $P_N = ret_Nom_prenom($row[1], $row[2]);
                            echo '<a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Enfant . '">' . $P_N . '</a>' . "\n";
                            echo '<a href="' . $root . '/arbre_asc_pers.php?Refer=' . $Enfant . '">' . $img_asc . '</a>';
                            echo '<a href="' . $root . '/arbre_desc_pers.php?Refer=' . $Enfant . '">' . $img_desc . '</a>';
                        } else {
                            echo my_html($LG_Data_noavailable_profile);
                        }
                        echo '<br />';
                    }
                }
                echo '</div>';
            }
        }
    }
    if ($num_gen < $nb_Rangs) {
        // Trait horizontal après la cellule
        $top2 = round($top + ($Haut_Cellule / 2));
        $left2 = $left + $Larg_Cellule;
        // Trait vertical après la cellule
        // Calcul top père et mère
        $topP  = $Coord_Y[($nb_enr * 2) + 1];
        $topP2 = round($topP + ($Haut_Cellule / 2));
        $topM  = $Coord_Y[($nb_enr * 2) + 2];
        $topM2 = round($topM + ($Haut_Cellule / 2));
        $hauteur = $topM2 - $topP2;
        // Décalage sur la gauche
        $left2 = $left2 + $Larg_Trait_Hor;
        // Dessin du trait
        trait_vert($left2, $topP2, $hauteur);
    }
}

echo '<table cellpadding="0" width="100%"><tr><td>';
$left += $Larg_Cellule;
echo '<div style="position: absolute;  top: 570px; left:' . $left . '; width:200px;">' . "\n";
echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/' . $Icones['home'] . '" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
echo '</div>';
echo '</td></tr></table>';

echo '</div>';

if ($existe_images) {
    $action = $LG_Tree_Icon_Hover;
    if ($Comportement == 'C') $action = $LG_Tree_Icon_Click;
    $ch_def_image = '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="' . my_html($LG_tip) . '" title="' . my_html($LG_tip) . '"> '
        . $action
        . '<img src="' . $root . '/assets/img/' . $Icones['images'] . '" alt="Images" title="Images"> '
        . $LG_Tree_Show_Image;
    echo '<table>';
    echo '<tr><td>' . $ch_def_image . '</td></tr>' . "\n";
    echo '<tr><td>';
    echo '<div id="image" style="display:none; visibility:hidden;">&nbsp;</div>';
    echo '</td></tr>';
    echo '</table>';
}

?>

</body>

<script type="text/javascript">
    function visib_image(contenu, haut, larg) {
        if (typeof(precedant) == 'undefined') precedant = '&nbsp;';
        cont_demande = '<img src="' + contenu + '" width=' + larg + ' height=' + haut + ' border="0"/>';
        var image2 = document.getElementById("image");
        if (precedant != contenu) {
            image2.innerHTML = ' <a href="' + contenu + '" TARGET="_blank">' + cont_demande + '</a>';
            montre_div('image');
            precedant = contenu;
        } else {
            image2.innerHTML = '&nbsp;';
            cache_div('image');
            precedant = '&nbsp;';
        }
    }
</script>

</html>