<?php
// UTF-8

// Déclaration des dégradés de couleurs

function degrade($color1,$color2,$nb_cols) {
	$colors = [];
	$colors[0] = '#'.sprintf('%02X',$color1[0]).sprintf('%02X',$color1[1]).sprintf('%02X',$color1[2]);
	$diffs = array( (($color2[0]-$color1[0])/$nb_cols),
					(($color2[1]-$color1[1])/$nb_cols),
					(($color2[2]-$color1[2])/$nb_cols)
					);
	for($i=0;$i<$nb_cols;$i++) {
		$r = $color1[0]+($diffs[0]*$i);
		$g = $color1[1]+($diffs[1]*$i);
		$b = $color1[2]+($diffs[2]*$i);
		$colors[$i] = '#'.sprintf('%02X',$r).sprintf('%02X',$g).sprintf('%02X',$b);
	}
	$colors[$nb_cols-1] = '#'.sprintf('%02X',$color2[0]).sprintf('%02X',$color2[1]).sprintf('%02X',$color2[2]);
	return $colors;
}

// Conversion hexadecimale vers RGB
function HexaToRGB($color){
	// Init par défaut
	if (is_null($color))
		$color = '';
	$RGB = array("r" => 255, "g" => 255, "b" => 255);
	if(strlen($color) == 6) {
		$RGB['r'] = hexdec(substr($color,0,2));
		$RGB['g'] = hexdec(substr($color,2,2));
		$RGB['b'] = hexdec(substr($color,4,2));
	}
	return $RGB;
}

function Chapeau_Degrade($couleur) {
	global $coul_fin, $nb_cols;
	$col1 = HexaToRGB($couleur);
	if (! is_array($coul_fin)) {
		$coul_fin = HexaToRGB($coul_fin);
	}
	$colors = degrade(array($col1['r'],$col1['g'],$col1['b']),array($coul_fin['r'],$coul_fin['g'],$coul_fin['b']),$nb_cols);
	return $colors;
}

function Charge_Couleur($Degrade) {
	global $Rouge, $Vert, $Bleu, $Jaune, $Marron, $Violet, $Gris, $Orange, $Rose, $Lavande;
	switch ($Degrade) {
		case 'R' : $la_couleur = $Rouge; break;
		case 'V' : $la_couleur = $Vert; break;
		case 'B' : $la_couleur = $Bleu; break;
		case 'J' : $la_couleur = $Jaune; break;
		case 'M' : $la_couleur = $Marron; break;
		case 'v' : $la_couleur = $Violet; break;
		case 'G' : $la_couleur = $Gris; break;
		case 'O' : $la_couleur = $Orange; break;
		case 'r' : $la_couleur = $Rose; break;
		case 'L' : $la_couleur = $Lavande; break;
		default : $la_couleur = $Rouge;
	}
	return $la_couleur;
}

// Nombre de nuances
$nb_cols = 25;								
//$coul_fin = HexaToRGB('000000');
$coul_fin = HexaToRGB('FFFFFF');
$Rouge  = Chapeau_Degrade('FF0000');
$Bleu   = Chapeau_Degrade('0000FF');
//$all = true;
if (isset($all)) {
	$Vert    = Chapeau_Degrade('00FF00');
	$Jaune   = Chapeau_Degrade('FFFF00');
	$Marron  = Chapeau_Degrade('A25830');
	$Violet  = Chapeau_Degrade('7F007F');
	$Gris    = Chapeau_Degrade('333333');
	$Orange  = Chapeau_Degrade('ED7F10');
	$Rose    = Chapeau_Degrade('FFAEC9');
	$Lavande = Chapeau_Degrade('9D89E2');
} else {
	$Vert    = '';
	$Jaune   = '';
	$Marron  = '';
	$Violet  = '';
	$Gris    = '';
	$Orange  = '';
	$Rose    = '';
	$Lavande = '';
}
//for ($nb=0; $nb<$nb_cols;$nb++) echo $nb.' : <FONT COLOR="'.$Bleu[$nb].'"> '.$Bleu[$nb].'</FONT>, ';
//echo '<br />';

?>