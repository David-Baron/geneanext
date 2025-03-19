<?php

// Génération de l'image pour la carte Appelé par appelle_image_france_dep.php

require(__DIR__ . '/../app/ressources/fonctions.php');

function calc_indice($pour)
{
    if ($pour == 0) return 0;
    else return floor(($pour - 1) / 4);
}

function colorie_depart($x, $y, $num, $couleur = '#000000')
{
    global $image, $noir;
    @ImageFillToBorder($image, $x, $y, $noir, $couleur);
    imagestring($image, 1, $x, $y, $num, $noir);
}

// Colorie un département dont le code est fourni
function colorie_depart_code2($code, $couleur, $indice)
{
    global $coord;
    $cdx = $coord[$code];
    $x = substr($cdx, 0, 3);
    $y = substr($cdx, 4, 3);
    colorie_depart($x, $y, $indice, $couleur);
}

$all = true;
require(__DIR__ . '/../app/ressources/degrades_inc.php');
require(__DIR__ . '/../app/ressources/france_dep_inc.php');

$x = Lit_Env();                        // Lecture de l'indicateur d'environnement

// Recup de la variable passée dans l'URL : type de liste
$Type_Liste = Recup_Variable('Type_Liste', 'C', 'NMD');

header("Content-type: image/png");
$imagesource = "Images/cartes/france_depart.png";
$image = @ImageCreateFromPng($imagesource);

$la_couleur = Charge_Couleur($Degrade);

// On alloue les couleurs
// D'abord par rapport au dégradés
$nb_col = count($la_couleur);
for ($r = $nb_col - 1; $r >= 0; $r--) {
    $rvb = strtoupper($la_couleur[$r]);
    $couleurs[] = ImageColorAllocate($image, hexdec(substr($rvb, 1, 2)), hexdec(substr($rvb, 3, 2)), hexdec(substr($rvb, 5, 2)));
}
// Puis le noir
$noir = ImageColorAllocate($image, 0, 0, 0);

$n_personnes = nom_table('personnes');
$n_departements = nom_table('departements');
$n_villes = nom_table('villes');

switch ($Type_Liste) {
    case 'N':
        $sql = 'SELECT count(*) , d.Departement ' .
            'FROM ' . $n_personnes . ' p, ' . $n_departements . ' d, ' . $n_villes . ' v ' .
            'WHERE p.Ville_Naissance = v.Identifiant_zone and p.Reference <> 0 ' .
            'AND p.Ville_Naissance <> 0 ' .
            'AND v.Zone_Mere = d.Identifiant_zone ';
        if (!$_SESSION['estPrivilegie']) $sql = $sql . " and Diff_Internet = 'O' ";
        $sql .= 'GROUP BY d.Departement having d.Departement > 0;';
        break;
    case 'D':
        $sql = 'SELECT count(*) , d.Departement ' .
            'FROM ' . $n_personnes . ' p, ' . $n_departements . ' d, ' . $n_villes . ' v ' .
            'WHERE p.Ville_Deces = v.Identifiant_zone and p.Reference <> 0 ' .
            'AND p.Ville_Deces <> 0 ' .
            'AND v.Zone_Mere = d.Identifiant_zone ';
        if (!$_SESSION['estPrivilegie']) $sql = $sql . " and Diff_Internet = 'O' ";
        $sql .= 'GROUP BY d.Departement having d.Departement > 0;';
        break;
    case 'M':
        $sql = 'select count(*), d.Departement ' .
            'from ' . nom_table('personnes') . ' m, ' . nom_table('personnes') . ' f, ' .
            nom_table('unions') . ' u, ' . $n_departements . ' d, ' . $n_villes . ' v ' .
            'WHERE ';
        if (!$_SESSION['estPrivilegie']) {
            $sql = $sql . " m.Diff_Internet = 'O' AND ";
            $sql = $sql . " f.Diff_Internet = 'O' AND ";
        }
        $sql .= 'u.Ville_Mariage <> 0 ' .
            'AND u.Ville_Mariage = v.Identifiant_zone ' .
            'AND u.Conjoint_1 = m.Reference and u.Conjoint_2 = f.Reference ' .
            'AND v.Zone_Mere = d.Identifiant_zone ' .
            'GROUP BY d.Departement having d.Departement > 0;';
        break;
    default:
        break;
}

$res = lect_sql($sql);

// Calcul du nombre total de personnes
$nb_tot = 0;
if ($res->rowCount() > 0) {
    $res->closeCursor();
    $res = lect_sql($sql);
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $nb_tot += $row[0];
    }
}

// Coloriage des départements
if ($res->rowCount() > 0) {
    $res->closeCursor();
    $res = lect_sql($sql);
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $indice = calc_indice(floor($row[0] / $nb_tot * 100));
        if (array_key_exists($row[1], $coord)) {
            colorie_depart_code2($row[1], $couleurs[$indice], $row[0]);
        }
    }
}
$res->closeCursor();

@ImagePng($image);
// Sauvegarde pour utilisation externe
sauve_img_gd($image);
@ImageDestroy($image);
