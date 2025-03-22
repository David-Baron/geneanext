<?php
//=====================================================================
// Affichage d'un camembert de statistiques
// Paramètres :
// - $data : liste des données séparées par des *
// - $labels : liste des libellés séparées par des *
// - $couleur : couleur du dégradé Rouge, Vert, Bleu...
// - larg_image : largeur de l'image
// - haut_image : hauteur de l'image
// - suffixe : pour le nom du canvas
// - $larg_legende : largeur en pixels pris par la légende à droite
// - $texte_ajout : texte constant à afficher sur les lignes de légendes
//=====================================================================

function aff_degrade($datas, $labels, $couleur, $larg_image, $haut_image, $suffixe, $larg_legende, $texte_ajout)
{
    global $nb_cols, $Rouge, $Vert, $Bleu, $Jaune, $Marron, $Violet, $Gris, $Orange, $Rose;

    // Si on veut avoir toutes les couleurs de dégradés, décommenter la ligne suivante
    //$all = true;
    // Par défaut, on n'a que rouge et bleu pour limiter les calculs

    // Allocation des couleurs en fonction du dégradé du site
    include_once(__DIR__ . '/degrades_inc.php');

    $datas = str_replace('*', ',', $datas);
    $labels = str_replace('*', '","', $labels);

    //$larg_image = 400;
    //$haut_image = 200;

    $la_couleur = '';
    $la_couleur = Charge_Couleur($couleur);

    // On prend grosso modo une couleur sur 2 ; une décennie par couleur
    $couleurs = '';
    for ($r = 11; $r > 0; $r--) {
        $x = $r * 2;
        //$x = $r;
        $rvb = strtoupper($la_couleur[$x]);
        if ($couleurs != '') $couleurs .= ',';
        $couleurs .= '"' . $rvb . '"';
    }
    /*
	// Inversion...
	$couleurs = explode(',',$couleurs);
	$couleurs = array_reverse($couleurs);
	$couleurs = implode(',',$couleurs);
	*/

    echo '<table>';
    echo '<tr>';
    echo '<td><canvas id="canvas' . $suffixe . '" width="' . $larg_image . '" height="' . $haut_image . '" /></td>';
    echo '<td><canvas id="canvas_leg' . $suffixe . '" width="' . $larg_legende . '" height="' . $haut_image . '" /></td>';
    echo '</tr>';
    echo '</table>';

    echo '<script type="text/javascript" src="jscripts/moncanvas.js"></script>';
    echo '<script type="text/javascript">';
    echo '	var mesDonnees = [' . $datas . '];';
    echo '	var mesLabels = ["' . $labels . '"];';
    echo '	var pieColor = [' . $couleurs . '];';
    echo '	pie(' . $suffixe . ',pieColor, mesDonnees);';
    echo '	legende(' . $suffixe . ',"' . $texte_ajout . '", mesDonnees, mesLabels, true);';
    echo '</script>';
}
