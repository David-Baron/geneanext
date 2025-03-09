<?php
// UTF-8
session_start();

$imp_mar = 1;
$Larg_Cellule = 140;

// Coordonnées pour un arbre horizontal
$HOR_Coord_X = Array(117,
                 117,117,
                 343,343,343,343,
                 570,570,570,570,570,570,570,570);
$HOR_Coord_Y = Array(458,
                 211,702,
                 98,324,589,816,
                 41,154,268,381,532,645,758,872);

// Coordonnées pour un arbre vertical
$VER_Coord_X = Array(300,
                 110,490,
                 20,210,400,590,
                 20,20,210,210,400,400,590,590);
$VER_Coord_Y = Array(930,
                 702,702,
                 475,475,475,475,
                 40,230,40,230,40,230,40,230);

header ("Content-type: image/png");

include_once('fonctions.php');

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer','N');

function imagestringcutted($img,$font,$y,$x1,$x2,$text,$color,$align='center') {
	$fontwidth = imagefontwidth($font);
	$fullwidth = strlen($text) * $fontwidth;
	$maxwidth = $x2-$x1;
	$targetwidth = $fullwidth-(4*$fontwidth);
	if($fullwidth > $maxwidth) {
		for($i = 0; $i < strlen($text) AND ((strlen($text)-($i-4))*$fontwidth) > $targetwidth ;$i++) { }
		$text = substr($text,0,(strlen($text)-$i)-4)."...";
	}
	$x3 = ($x2-$x1)/ 2 - strlen($text) * $fontwidth / 2;
	if($align == 'left') imagestring($img,$font,$x1,$y,$text,$color);
	elseif($align == 'right') imagestring($img,$font,$x2 - ((strlen($text) * $fontwidth)),$y,$text,$color);
	else imagestring($img,$font,$x1 + ($x2-$x1)/ 2 - strlen($text) * $fontwidth / 2,$y,$text,$color);
}

function larg_texte($font,$taille,$text) {
	// 0 : Coin inférieur gauche, abscisse
	// 2 : Coin inférieur droit, abscisse
	// ==> on peut calculer la largeur occupée par le texte : droit - gauche
	$coords = imagettfbbox($taille, 0, $font, $text);
	return abs($coords[2]-$coords[0]);
}

function justifie($img,$font,$taille,$x,$y,$largeur,$text,$color,$align='centre') {
	global $debug, $f_log;
	$largeur_texte = larg_texte($font,$taille,$text);
	// **************************************
	// Prévoir le cas où la largeur > largeur max
	// **************************************
	$anc_x = $x;
	switch ($align) {
		case 'gauche' : break; // pas de changement d'abscisse
		case 'droite' : $x = $x + $largeur - $largeur_texte; break;
		case 'centre' : $x = ($x+($largeur/2)) - ($largeur_texte/2); break;
	}
	$x = round($x);
	$y = round($y);
	if ($debug) {
		ecrire($f_log,$taille);
	}
	//ImageTTFText($img, $taille, 0, $x, $y, $color, $font, $text.'anc x : '.$anc_x.' x : '.$x.' largeur : '.$largeur.' larg texte : '.$largeur_texte);
	ImageTTFText($img, $taille, 0, $x, $y, $color, $font, $text);
}

  // Affiche une personne
  function Insere_Chaine_Personne($Prenom,$Nom,$Dates,$Mariage) {
    global $image,$noir,$Coord_X,$Coord_Y,$nb_enr,$Larg_Cellule,$Type_Arbre_Asc,$font;
    //$font = 'c:/windows/Fonts/Arial.ttf';
    $c_x = $Coord_X[$nb_enr];
    $c_y = $Coord_Y[$nb_enr];

    $taille_font = 10;

    justifie($image,$font,$taille_font,$c_x,$c_y-20,$Larg_Cellule,$Prenom,$noir,'centre');
    justifie($image,$font,$taille_font,$c_x,$c_y,$Larg_Cellule,$Nom,$noir,'centre');
    justifie($image,$font,$taille_font,$c_x,$c_y+20,$Larg_Cellule,$Dates,$noir,'centre');

    // Affichage de l'année de mariage
    if ($Mariage != '') {
      switch ($Type_Arbre_Asc) {
        // cas d'un arbre horizontal
        case 'HOR' : $c_x2 = $c_x - 100;
                     $c_y2 = $Coord_Y[$nb_enr-1];
                     if ($nb_enr == 2) $c_x2 = $c_x2-60;
                     if (pair($nb_enr))
                       justifie($image,$font,$taille_font,$c_x2,($c_y+$c_y2)/2,$Larg_Cellule,
                                'x '.$Mariage,$noir,'centre');
                     break;
        // cas d'un arbre vertical
        case 'VER' : if ($nb_enr > 7) {
                       $c_x2 = $c_x + 10 + ($Larg_Cellule / 2);
                       $c_y2 = $c_y - 102;
                       $al = 'gauche';
                     }
                     else {
                       $c_x2 = $Coord_X[($nb_enr/2)-1];
                       $c_y2 = $c_y + 88;
                       $al = 'centre';
                     }
                     if (pair($nb_enr))
                       justifie($image,$font,$taille_font,$c_x2,$c_y2,$Larg_Cellule,'x '.$Mariage,$noir,$al);
                       break;
      }
    }

    //imagestringcutted($image,2,$c_y-20,$c_x,$c_x+$Larg_Cellule,$Prenom,$noir,'center');
    //imagestringcutted($image,2,$c_y,$c_x,$c_x+$Larg_Cellule,$Nom,$noir,'center');
    //imagestringcutted($image,2,$c_y+20,$c_x,$c_x+$Larg_Cellule,$Dates,$noir,'center');
  }

function Retourne_Pers($Num) {
  global $Ensemble;
  $Ligne = $Num;
  if (is_array($Ensemble)) $Ensemble[] = $Ligne;
  else                     $Ensemble[0] = $Ligne;
  return 1;
}

// Ajoute une entrée vide dans l'ensemble de sortie
function Add_Vide() {
  global $Ensemble;
  $Ensemble[] = '0';
  return 0;
}

// Recherche le couple de parents
function Charge_Parents($Personne) {
  global $Pere_GP,$Mere_GP,$Rang_GP;
  if ($Personne != 0) {
    $x = get_Parents($Personne,$Num_Pere,$Num_Mere,$Rang);
    if ($Num_Pere != 0) {
      $x = Retourne_Pers($Num_Pere);
    }
    else
      $x = Add_Vide();
    if ($Num_Mere != 0) {
      $x = Retourne_Pers($Num_Mere);
    }
    else
      $x = Add_Vide();
  }
  else {
    $x = Add_Vide();
    $x = Add_Vide();
  }
  return 0;
}

// $debug = false;

$x = Lit_Env();

$sql='select Image_Arbre_Asc, Affiche_Mar_Arbre_Asc  from '.nom_table('general');
$res = lect_sql($sql);
$enreg = $res->fetch(PDO::FETCH_ASSOC);

// Appel de l'image de fond
if ($debug) {
	$f_log = open_log();
	ecrire($f_log,'$chemin_images_a_asc : '.$chemin_images_a_asc);
}
//if ($debug) ecrire($f_log,$chemin_images_a_asc.$enreg['Image_Arbre_Asc']);
$imagesource = $chemin_images_a_asc.$enreg['Image_Arbre_Asc'];
$image = @ImageCreateFromPng($imagesource);
//$image = @imagecreate (100, 50) or die ("Impossible d'initialiser la bibliothèque GD");
$noir = ImageColorAllocate($image, 0, 0, 0);

if ($debug) ecrire($f_log,$image);
 
// Récupération des coordonnées en fonction du type d'arbre
// Le type d'arbre est la 3ème composante du nom de l'arbre
$composantes = explode('_', $enreg['Image_Arbre_Asc']);
$Type_Arbre_Asc = strtoupper($composantes[2]);
$Coord_X = ${$Type_Arbre_Asc.'_Coord_X'};
$Coord_Y = ${$Type_Arbre_Asc.'_Coord_Y'};


$Affiche_Mar_Arbre_Asc = $enreg['Affiche_Mar_Arbre_Asc'];

$nb_Rangs = 4;
$Ref = $Refer;

$font = Get_Font();

if ($debug) ecrire($f_log,'av Retourne_Pers pour '.$Ref);
$x = Retourne_Pers($Ref);
if ($debug) ecrire($f_log,'ap Retourne_Pers pour '.$Ref);

if ($nb_Rangs > 1) {
	if ($debug) ecrire($f_log,'av Charge_Parents pour '.$Ref);
	$x = Charge_Parents($Ref);
	if ($debug) ecrire($f_log,'ap Charge_Parents pour '.$Ref);
}
if ($nb_Rangs > 2) {
	for ($nb = 3; $nb <= $nb_Rangs; $nb++) {
		$Rang_Min = pow(2,$nb-2);
		$Rang_Max = pow(2,$nb-1)-1;
		for ($nb2 = $Rang_Min; $nb2 <= $Rang_Max; $nb2++) {
			$nb3 = $nb2 - 1;
			$Ref = $Ensemble[$nb2-1];
			if ($debug) ecrire($f_log,'av Charge_Parents pour '.$Ref);
			$x = Charge_Parents($Ref);
			if ($debug) ecrire($f_log,'ap Charge_Parents pour '.$Ref);
		}
	}
}

$num_gen = 1;
$der_num = 0;
$Mariage = '';
for ($nb_enr = 0; $nb_enr < count($Ensemble) ; ++$nb_enr) {
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
	if ($LaRef != '0') {
		// Accès aux données de la personne
		$sql='select Nom, Prenoms, Ne_Le, Decede_Le, Diff_Internet from '.nom_table('personnes').' where reference = '.$LaRef;
		if ($res = lect_sql($sql)) {
			if ($infos = $res->fetch(PDO::FETCH_NUM)) {
				// Protection des données sur Internet
				if (($_SESSION['estPrivilegie']) or ($infos[4] == 'O')) {
					$Nom    = trim($infos[0]);
					$Prenom = trim($infos[1]);
					// On ne retient que le premier prénom
					if ($Prenom != '') {
					  $BPos = strpos($Prenom,' ');
					  if ($BPos > 1) $Prenom = substr($Prenom,0,$BPos);
					}
					$Ne     = affiche_date($infos[2]);
					if ($Ne != '?') $Ne = '° '.$Ne;
					else            $Ne = '';
					$Decede = affiche_date($infos[3]);
					if ($Decede != '?') $Decede = '+ '.$Decede;
					else                $Decede = '';
					$Dates = $Ne.' '.$Decede;
					// Si femme, on va chercher la date du mariage
					if ($Affiche_Mar_Arbre_Asc == 'O') {
						if (($nb_enr > 0) and ($imp_mar == 1)){
							if (!pair($nb_enr)) $mari = $LaRef;
							else {
								$femme = $LaRef;
								$sql='select Maries_Le from '.nom_table('unions').' where Conjoint_1='.$mari.' and Conjoint_2='.$femme;
								if ($res = lect_sql($sql)) {
									if ($infosUn = $res->fetch(PDO::FETCH_NUM)) {
									  $Mariage = affiche_date($infosUn[0]);
									}
								}
							}
						}
					}
				}
				else {
					$Prenom = '';
					$Nom = 'Non disponible';
					$Dates = '';
				}
			}
			Insere_Chaine_Personne($Prenom,$Nom,$Dates,$Mariage);
		}
	}
}

if ($debug) ecrire($f_log,'Fin accès');

@ImagePng($image);

// Sauvegarde pour utilisation externe
//sauve_img_gd($image);
//@ImageDestroy($image);

if ($debug) fclose($f_log);
?>